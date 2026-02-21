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
        $query = Article::with(['conference.country', 'author']);

        if ($request->filled('conference_id')) {
            $query->where('conference_id', $request->conference_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $articles = $query->latest()->paginate(15);
        $conferences = Conference::with('country')->get();

        return view('admin.articles.index', compact('articles', 'conferences'));
    }

    /**
     * Yangi maqola qo'shish formasi
     */
    public function create()
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $authors = User::where('role', 'author')->orderBy('name')->get();
        return view('admin.articles.create', compact('countries', 'authors'));
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
            'country_id' => 'required|exists:countries,id',
            'author_name' => 'required|string|max:255',
            'author_affiliation' => 'nullable|string|max:500',
            'co_authors' => 'nullable|string',
            'title' => 'required|string|max:500',
            'abstract' => 'nullable|string',
            'keywords' => 'nullable|string|max:500',
            'references' => 'nullable|string',
            'order_number' => 'required|integer|min:1',
            'start_page' => 'required|integer|min:1',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480', // 20MB
        ];

        // Agar PDF fayl bo'lmasa, content majburiy
        if (!$request->hasFile('pdf_file')) {
            $rules['content'] = 'required|string|min:100';
        } else {
            $rules['content'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // Davlat uchun konferensiyani topish yoki yaratish
        $country = Country::findOrFail($validated['country_id']);
        $conference = Conference::firstOrCreate(
            [
                'country_id' => $country->id,
                'month_year' => now()->format('Y-m'),
            ],
            [
                'title' => $country->conference_name ?? $country->name . ' Scientific Conference',
                'description' => $country->conference_description,
                'conference_date' => now(),
                'status' => 'active',
            ]
        );

        // Konferensiya sanasini har doim bugungi kunga yangilash
        // firstOrCreate mavjud yozuvni yangilamaydi, shuning uchun alohida update
        $conference->update(['conference_date' => now()]);

        $content = $validated['content'] ?? '';
        $basePdfPath = null;
        $pageCount = 1;

        // =====================================================
        // 1-VARIANT: PDF YUKLANGAN (ENG YAXSHI - FORMULALAR SAQLANADI)
        // =====================================================
        if ($request->hasFile('pdf_file')) {
            try {
                // Asl PDF ni saqlash
                $basePdfPath = $request->file('pdf_file')->store('articles/base', 'public');
                $fullBasePath = Storage::disk('public')->path($basePdfPath);

                // Sahifalar sonini aniqlash
                // Sahifalar sonini aniqlash - Memory friendly
                $pdfParser = new \Smalot\PdfParser\Parser();
                try {
                    $pdf = $pdfParser->parseFile($fullBasePath);
                    $pageCount = count($pdf->getPages());

                    // Matnni olish (abstract uchun, agar bo'sh bo'lsa)
                    if (empty($validated['abstract'])) {
                        $extractedText = $pdf->getText();

                        // UTF-8 ga o'tkazish va tozalash
                        if (!mb_check_encoding($extractedText, 'UTF-8')) {
                            $extractedText = mb_convert_encoding($extractedText, 'UTF-8', 'UTF-8');
                        }
                        // Nazorat belgilarini olib tashlash (yangi qator va tab lardan tashqari)
                        $extractedText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $extractedText);

                        // Birinchi 500 belgini abstract sifatida olish
                        if (mb_strlen($extractedText) > 100) {
                            $content = mb_substr($extractedText, 0, 500) . '...';
                        }
                    }
                } catch (\Exception $e) {
                    // PDF parse xatolik - default qiymatlar
                    $pageCount = 1;
                    \Log::warning('PDF parsing failed: ' . $e->getMessage());
                }

            } catch (\Exception $e) {
                return back()->withErrors(['pdf_file' => 'PDF faylni yuklashda xatolik: ' . $e->getMessage()])->withInput();
            }
        }

        // Sahifalar diapazonini hisoblash
        if (!$basePdfPath && !empty($content)) {
            // Matndan sahifa soni
            $plainText = strip_tags($content);
            $contentLength = mb_strlen($plainText);
            $pageCount = max(1, ceil($contentLength / 3000));
        }

        $startPage = $validated['start_page'];
        $endPage = $startPage + $pageCount - 1;
        $pageRange = $startPage == $endPage ? (string) $startPage : "{$startPage}-{$endPage}";

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
            'content' => $content,
            'page_count' => $pageCount,
            'page_range' => $pageRange,
            'order_number' => $validated['order_number'],
            'status' => 'pending',
            'pdf_path' => $basePdfPath, // Base PDF (agar yuklangan bo'lsa)
        ]);

        // =====================================================
        // PDF YARATISH / OVERLAY QO'SHISH
        // =====================================================
        try {
            if ($basePdfPath) {
                // PDF yuklangan - faqat overlay qo'shish
                \Log::info('Starting PDF overlay application', ['article_id' => $article->id]);
                $startTime = microtime(true);

                $formattedPath = $this->articlePdfService->applyOverlayToPdf($article, $country);

                $duration = round(microtime(true) - $startTime, 2);
                \Log::info('PDF overlay completed', ['article_id' => $article->id, 'duration' => $duration . 's']);

                $article->update([
                    'formatted_pdf_path' => $formattedPath,
                ]);
            } else {
                // Matndan PDF yaratish (INCOP uslubida)
                \Log::info('Starting INCOP style PDF generation', ['article_id' => $article->id]);
                $startTime = microtime(true);

                $pdfPath = $this->articlePdfService->generateIncopStyle($article, $country);

                $duration = round(microtime(true) - $startTime, 2);
                \Log::info('INCOP PDF generation completed', ['article_id' => $article->id, 'duration' => $duration . 's']);

                $article->update([
                    'pdf_path' => $pdfPath,
                    'formatted_pdf_path' => $pdfPath,
                ]);
            }
        } catch (\Exception $e) {
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
            'country_id' => 'required|exists:countries,id',
            'author_id' => 'nullable|exists:users,id',
            'author_name' => 'required|string|max:255',
            'author_affiliation' => 'nullable|string|max:500',
            'title' => 'required|string|max:255',
            'abstract' => 'nullable|string',
            'content' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:10240',
            'page_count' => 'required|integer|min:1|max:100',
            'page_range' => 'required|string|max:20',
            'order_number' => 'required|integer|min:1',
        ]);

        // Davlat uchun konferensiyani topish yoki yaratish
        $country = Country::findOrFail($validated['country_id']);
        $conference = Conference::firstOrCreate(
            [
                'country_id' => $country->id,
                'month_year' => $article->conference->month_year ?? now()->format('Y-m'),
            ],
            [
                'title' => $country->conference_name ?? $country->name . ' Scientific Conference',
                'description' => $country->conference_description,
                'conference_date' => now(),
                'status' => 'active',
            ]
        );

        // Konferensiya sanasini har doim bugungi kunga yangilash
        $conference->update(['conference_date' => now()]);

        // Agar yangi PDF yuklansa
        if ($request->hasFile('pdf')) {
            // Eski PDF ni o'chirish
            if ($article->pdf_path) {
                Storage::disk('public')->delete($article->pdf_path);
            }
            if ($article->formatted_pdf_path) {
                Storage::disk('public')->delete($article->formatted_pdf_path);
            }

            $validated['pdf_path'] = $request->file('pdf')->store('articles', 'public');

            // Yangi formatlangan PDF yaratish (INCOP Style)
            try {
                $formattedPath = $this->articlePdfService->applyOverlayToPdf($article, $country);
                $validated['formatted_pdf_path'] = $formattedPath;
            } catch (\Exception $e) {
                \Log::error('Formatted PDF update failed: ' . $e->getMessage());
            }
        }

        // country_id ni olib tashlaymiz, conference_id qo'shamiz
        unset($validated['country_id']);
        $validated['conference_id'] = $conference->id;

        $article->update($validated);

        // Agar PDF yuklanmagan bo'lsa va matn bo'lsa, PDF ni yangi INCOP dizaynida qayta generatsiya qilish
        if (!$request->hasFile('pdf') && $request->filled('content')) {
            try {
                $pdfPath = $this->articlePdfService->generateIncopStyle($article, $country);
                $article->update([
                    'pdf_path' => $pdfPath,
                    'formatted_pdf_path' => $pdfPath,
                ]);
            } catch (\Exception $e) {
                \Log::error('PDF regeneration failed: ' . $e->getMessage());
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
        $article->update([
            'status' => 'published',
            'published_at' => now(),
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
        if ($article->formatted_pdf_path && Storage::disk('public')->exists($article->formatted_pdf_path)) {
            return Storage::disk('public')->download($article->formatted_pdf_path);
        }

        // Agar mavjud bo'lmasa, yaratish
        try {
            $formattedPath = $this->articlePdfService->formatArticle($article);
            $article->update(['formatted_pdf_path' => $formattedPath]);
            return Storage::disk('public')->download($formattedPath);
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }
}
