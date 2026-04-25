<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Conference;
use App\Models\Country;
use App\Models\User;
use App\Services\ArticlePdfService;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    protected CertificateService $certificateService;
    protected ArticlePdfService $articlePdfService;

    public function __construct(CertificateService $certificateService, ArticlePdfService $articlePdfService)
    {
        $this->certificateService = $certificateService;
        $this->articlePdfService = $articlePdfService;
    }

    /**
     * Maqolalar ro'yxati
     */
    public function index(Request $request)
    {
        // Oylar bo'yicha konferensiyalar va maqolalar
        $conferencesQuery = Conference::with(['country', 'articles.author'])
            ->orderBy('month_year', 'desc');

        // Filter: davlat bo'yicha
        if ($request->filled('country_id')) {
            $conferencesQuery->where('country_id', $request->country_id);
        }

        // Filter: oy bo'yicha
        if ($request->filled('month_year')) {
            $conferencesQuery->where('month_year', $request->month_year);
        }

        $conferences = $conferencesQuery->get();

        // Barcha davlatlar (filter uchun)
        $countries = \App\Models\Country::where('is_active', true)->orderBy('name')->get();

        // Oy ro'yxati (filter uchun) - mavjud konferensiyalardan
        $availableMonths = Conference::select('month_year')
            ->distinct()
            ->orderBy('month_year', 'desc')
            ->pluck('month_year');

        return view('admin.articles.index', compact('conferences', 'countries', 'availableMonths'));
    }

    /**
     * Yangi maqola qo'shish formasi
     */
    public function create(Request $request)
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $authors = User::where('role', 'author')->orderBy('name')->get();

        // Agar conference_id berilgan bo'lsa, oy va davlatni oldindan belgilaymiz
        $preselectedConference = null;
        if ($request->filled('conference_id')) {
            $preselectedConference = Conference::with('country')->find($request->conference_id);
        }

        return view('admin.articles.create', compact('countries', 'authors', 'preselectedConference'));
    }

    /**
     * Davlat bo'yicha mavjud oylik konferensiyalarni qaytarish (AJAX)
     */
    public function conferencesByCountry(Request $request)
    {
        $countryId = $request->get('country_id');
        $conferences = Conference::where('country_id', $countryId)
            ->orderBy('month_year', 'desc')
            ->get(['id', 'month_year', 'title', 'status'])
            ->map(function ($c) {
            $monthNames = [
                '01' => 'Yanvar', '02' => 'Fevral', '03' => 'Mart',
                '04' => 'Aprel', '05' => 'May', '06' => 'Iyun',
                '07' => 'Iyul', '08' => 'Avgust', '09' => 'Sentabr',
                '10' => 'Oktabr', '11' => 'Noyabr', '12' => 'Dekabr',
            ];
            [$year, $month] = explode('-', $c->month_year);
            $c->label = ($monthNames[$month] ?? $month) . ' ' . $year;
            $c->status_label = [
                'draft' => 'Loyiha',
                'active' => 'Faol',
                'completed' => 'Yakunlangan',
            ][$c->status] ?? $c->status;
            return $c;
        });

        return response()->json($conferences);
    }

    /**
     * Yangi maqola saqlash - PDF overlay, Word konvertatsiya, yoki matndan yaratish
     * 
     * Strategiya:
     * 1. PDF yuklansa -> Overlay qo'shish (formulalar 100% saqlanadi)
     * 2. Word yuklansa -> LibreOffice orqali PDF ga, keyin overlay
     * 3. Faqat matn -> HTML dan PDF yaratish (formulalar oddiy matn)
     */
    public function store(Request $request)
    {
        // Maximum resources for very large PDFs - UNLIMITED
        set_time_limit(0);
        ini_set('memory_limit', '-1'); // Unlimited memory
        ini_set('max_execution_time', '0');

        $rules = [
            'country_id'      => 'required|exists:countries,id',
            'month_year'      => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'conference_date' => 'required|date',
            'author_name'     => 'required|string|max:255',
            'author_affiliation' => 'nullable|string|max:500',
            'co_authors'      => 'nullable|string',
            'title'           => 'required|string|max:500',
            'abstract'        => 'nullable|string',
            'keywords'        => 'nullable|string|max:500',
            'references'      => 'nullable|string',
            'docx_file'       => 'required|file|mimes:docx,doc|max:20480', // 20MB DOCX
        ];

        $validated = $request->validate($rules);

        // Davlat uchun konferensiyani topish yoki yaratish
        $country = Country::findOrFail($validated['country_id']);

        // month_year va conference_date admin tomonidan qo'lda kiritilgan
        $monthYear      = $validated['month_year'];
        $conferenceDate = $validated['conference_date']; // Masalan: 2026-03-12

        $conference = Conference::firstOrCreate(
        [
            'country_id' => $country->id,
            'month_year' => $monthYear,
        ],
        [
            'title'           => $country->conference_name ?? $country->name . ' Scientific Conference',
            'description'     => $country->conference_description,
            'conference_date' => $conferenceDate,
            'status'          => 'active',
        ]
        );

        // Agar konferensiya allaqachon mavjud bo'lsa, sanasini yangilaymiz
        if ($conference->wasRecentlyCreated === false) {
            $conference->update(['conference_date' => $conferenceDate]);
        }

        $content = '';
        $basePdfPath = null;
        $docxPath = null;
        $pageCount = 1;

        // =====================================================
        // 2-VARIANT: DOCX YUKLANGAN (FORMULALAR KONVERT QILINADI)
        // =====================================================
        if ($request->hasFile('docx_file')) {
            try {
                $docxPath = $request->file('docx_file')->store('articles/docx', 'public');
                $fullDocxPath = Storage::disk('public')->path($docxPath);

                // PHPWord bilan sahifa sonini taxminlash
                try {
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullDocxPath);
                    $totalText = '';
                    foreach ($phpWord->getSections() as $section) {
                        foreach ($section->getElements() as $element) {
                            if (method_exists($element, 'getText')) {
                                $totalText .= $element->getText() . ' ';
                            }
                        }
                    }
                    $pageCount = max(1, ceil(mb_strlen($totalText) / 3000));
                } catch (\Exception $e) {
                    $pageCount = 1;
                    \Log::warning('DOCX page count estimation failed: ' . $e->getMessage());
                }
            }
            catch (\Exception $e) {
                return back()->withErrors(['docx_file' => 'DOCX faylni yuklashda xatolik: ' . $e->getMessage()])->withInput();
            }
        }

        $startPage = 1;

        $maxOrder = Article::where('conference_id', $conference->id)->max('order_number');
        $nextOrder = $maxOrder ? $maxOrder + 1 : 1;

        // Maqolani yaratish
        $article = Article::create([
            'conference_id' => $conference->id,
            'author_name' => $validated['author_name'],
            'author_affiliation' => $validated['author_affiliation'],
            'co_authors' => $validated['co_authors'] ?? null,
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'keywords' => $validated['keywords'] ?? null,
            'references' => $validated['references'] ?? null,
            'content' => '',
            'page_count' => $pageCount,
            'page_range' => '1-' . $pageCount,
            'order_number' => $nextOrder,
            'status' => 'pending',
            'pdf_path' => null, 
        ]);

        // =====================================================
        // DOCX DAN PDF YARATISH (Puppeteer + mergeWithCoverPage)
        // =====================================================
        try {
            \Log::info('Starting DOCX to PDF generation (Puppeteer)', ['article_id' => $article->id]);
            $startTime = microtime(true);

            $fullDocxPath = Storage::disk('public')->path($docxPath);
            $formattedPath = $this->articlePdfService->generateFromDocx($fullDocxPath, $article, $country);

            $duration = round(microtime(true) - $startTime, 2);
            \Log::info('DOCX PDF generation completed', ['article_id' => $article->id, 'duration' => $duration . 's']);

            $article->update([
                'formatted_pdf_path' => $formattedPath,
            ]);
        }
        catch (\Exception $e) {
            \Log::error('PDF creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'PDF yaratishda xatolik: ' . $e->getMessage())
                ->withInput();
        }

        // Agar "Saqlash va nashr qilish" bosilgan bo'lsa
        if ($request->has('publish_now')) {
            return $this->publish($article);
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Maqola muvaffaqiyatli yaratildi va PDF tayyor!');
    }

    /**
     * Maqolani ko'rish
     */
    public function show(Article $article)
    {
        $article->load(['conference.country', 'author', 'certificate']);
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Maqolani tahrirlash formasi
     */
    public function edit(Article $article)
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $authors = User::where('role', 'author')->orderBy('name')->get();
        return view('admin.articles.edit', compact('article', 'countries', 'authors'));
    }

    /**
     * Maqolani yangilash
     */
    public function update(Request $request, Article $article)
    {
        // Increase execution time for PDF processing
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $validated = $request->validate([
            'country_id'      => 'required|exists:countries,id',
            'month_year'      => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'conference_date' => 'required|date',
            'author_id'       => 'nullable|exists:users,id',
            'author_name'     => 'required|string|max:255',
            'author_affiliation' => 'nullable|string|max:500',
            'title'           => 'required|string|max:255',
            'abstract'        => 'nullable|string',
            'docx_file'       => 'nullable|file|mimes:docx,doc|max:20480',
        ]);

        // Davlat uchun konferensiyani topish yoki yaratish
        $country = Country::findOrFail($validated['country_id']);

        // month_year va conference_date admin tomonidan qo'lda kiritilgan
        $monthYear      = $validated['month_year'];
        $conferenceDate = $validated['conference_date']; // Masalan: 2026-03-12

        $conference = Conference::firstOrCreate(
        [
            'country_id' => $country->id,
            'month_year' => $monthYear,
        ],
        [
            'title'           => $country->conference_name ?? $country->name . ' Scientific Conference',
            'description'     => $country->conference_description,
            'conference_date' => $conferenceDate,
            'status'          => 'active',
        ]
        );

        // Konferensiya sanasini yangilash (agar o'zgargan bo'lsa)
        $conference->update(['conference_date' => $conferenceDate]);

        // Agar yangi DOCX fayli yuklansa
        if ($request->hasFile('docx_file')) {
            // Eski fayllarni o'chirish (ixtiyoriy, agar saqlamoqchi bo'lsangiz komment qilish mumkin)
            if ($article->pdf_path) {
                Storage::disk('public')->delete($article->pdf_path);
            }
            if ($article->formatted_pdf_path) {
                Storage::disk('public')->delete($article->formatted_pdf_path);
            }

            $docxPath = $request->file('docx_file')->store('articles/docx', 'public');
            $fullDocxPath = Storage::disk('public')->path($docxPath);

            // Yangi sahifa soni taxmini (ixtiyoriy, ammo docx dan olish qiyinroq)
            $pageCount = $article->page_count; 
            try {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullDocxPath);
                $totalText = '';
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $totalText .= $element->getText() . ' ';
                        }
                    }
                }
                $pageCount = max(1, ceil(mb_strlen($totalText) / 3000));
            } catch (\Exception $e) {}

            $validated['page_count'] = $pageCount;
            $validated['page_range'] = '1-' . $pageCount;

            // DOCX -> PDF
            try {
                \Log::info('Starting DOCX to PDF generation on update', ['article_id' => $article->id]);
                $formattedPath = $this->articlePdfService->generateFromDocx($fullDocxPath, $article, $country);
                $validated['formatted_pdf_path'] = $formattedPath;
            }
            catch (\Exception $e) {
                \Log::error('Formatted PDF update failed: ' . $e->getMessage());
            }
        }

        // country_id ni olib tashlaymiz, conference_id qo'shamiz
        unset($validated['country_id']);
        $validated['conference_id'] = $conference->id;

        $article->update($validated);

        // PDF qayta generatsiya qilinishi kerakmi? (sarlavha, muallif, konf o'zgarsa)
        $needsRegeneration = $article->wasChanged(['title', 'abstract', 'author_name', 'author_affiliation', 'keywords', 'references', 'country_id', 'conference_date']);

        if (!$request->hasFile('docx_file') && $needsRegeneration && $article->pdf_path) {
            // Agar DOCX o'zgarmagan bo'lsa, lekin sarlavha, ism o'zgarsa — cover qismini qayta yasash kerak bo'lishi mumkin.
            // (generateFromDocx aslida to'liq faylni qayta yasash degani. Lekin bu yerda to'liq DOCX fayl saqlanib qolmagan bo'lishi mumkin.
            // Odatda Article da "source docx path" ustuni bo'lishi kerak. Biz hozir faqat ma'lumotlarni saqlaymiz.)
            
            // Asos cover page o'zgarganda faqat mergeWithCoverPage ni qayta ishlashi mumkin, agar base_pdf bo'lsa
            try {
                if (file_exists(Storage::disk('public')->path($article->pdf_path))) {
                    $formattedPath = $this->articlePdfService->mergeWithCoverPage($article, $country);
                    $article->update(['formatted_pdf_path' => $formattedPath]);
                }
            }
            catch (\Exception $e) {
                \Log::error('PDF cover page regeneration failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Maqola muvaffaqiyatli yangilandi.');
    }

    /**
     * Maqolani o'chirish
     */
    public function destroy(Article $article)
    {
        // PDF fayllarni o'chirish
        if ($article->pdf_path) {
            Storage::disk('public')->delete($article->pdf_path);
        }

        if ($article->formatted_pdf_path) {
            Storage::disk('public')->delete($article->formatted_pdf_path);
        }

        if ($article->certificate && $article->certificate->pdf_path) {
            Storage::disk('public')->delete($article->certificate->pdf_path);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Maqola muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Maqolani nashr qilish
     */
    public function publish(Article $article)
    {
        $publishedAt = $article->conference && $article->conference->conference_date
            ? \Carbon\Carbon::parse($article->conference->conference_date)->setTime(12, 0, 0)
            : now();

        $article->update([
            'status' => 'published',
            'published_at' => $publishedAt,
            'article_link' => url("/article/{$article->id}"),
        ]);

        // Sertifikat yaratish
        $this->certificateService->generate($article);

        return redirect()->route('admin.articles.show', $article)
            ->with('success', 'Maqola muvaffaqiyatli nashr qilindi va sertifikat yaratildi.');
    }

    /**
     * Maqolani formatlangan PDF ko'rinishda yuklash
     */
    public function downloadFormatted(Article $article)
    {
        // Mualliflar ro'yxatini shakllantiramiz
        $authorsList = [];
        $mainAuthor = mb_convert_case(trim($article->author_display_name), MB_CASE_TITLE, 'UTF-8');
        if (!empty($mainAuthor)) {
            $authorsList[] = $mainAuthor;
        }

        if (!empty($article->co_authors)) {
            $coAuthorsRaw = explode("\n", trim($article->co_authors));
            foreach ($coAuthorsRaw as $ca) {
                $nameParts = explode(',', $ca);
                $caName = mb_convert_case(trim($nameParts[0]), MB_CASE_TITLE, 'UTF-8');
                if (!empty($caName)) {
                    $authorsList[] = $caName;
                }
            }
        }
        
        $authorsString = implode(', ', $authorsList);
        if (empty($authorsString)) {
            $authorsString = 'Maqola_' . $article->id;
        }
        
        // Fayl nomi xavfsiz bo'lishi uchun xato belgilarni olib tashlaymiz
        $safeFileName = preg_replace('/[\/\\:\*\?"<>\|]/', '', $authorsString);
        // Juda uzun nom bo'lsa kesamiz
        $safeFileName = mb_strimwidth($safeFileName, 0, 120, "...");
        $downloadFileName = trim($safeFileName, ', ') . '.pdf';

        if ($article->formatted_pdf_path && Storage::disk('public')->exists($article->formatted_pdf_path)) {
            return Storage::disk('public')->download($article->formatted_pdf_path, $downloadFileName);
        }

        // Agar mavjud bo'lmasa, yaratish
        try {
            $formattedPath = $this->articlePdfService->formatArticle($article);
            $article->update(['formatted_pdf_path' => $formattedPath]);
            return Storage::disk('public')->download($formattedPath, $downloadFileName);
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Formatlangan PDF yaratishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Maqolani qayta formatlash
     */
    public function reformatPdf(Article $article)
    {
        try {
            // Eski formatlangan PDF ni o'chirish
            if ($article->formatted_pdf_path) {
                Storage::disk('public')->delete($article->formatted_pdf_path);
            }

            $formattedPath = $this->articlePdfService->formatArticle($article);
            $article->update(['formatted_pdf_path' => $formattedPath]);

            return redirect()->back()->with('success', 'Maqola qayta formatlandi.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }
}
