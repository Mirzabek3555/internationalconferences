<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use App\Models\Country;
use App\Services\ArticlePdfService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConferenceController extends Controller
{
    protected PdfService $pdfService;
    protected ArticlePdfService $articlePdfService;

    public function __construct(PdfService $pdfService, ArticlePdfService $articlePdfService)
    {
        $this->pdfService = $pdfService;
        $this->articlePdfService = $articlePdfService;
    }

    /**
     * Konferensiyalar ro'yxati
     */
    public function index(Request $request)
    {
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $selectedMonth = $request->get('month');

        if ($selectedMonth) {
            $query = Conference::with('country')
                ->withCount('articles')
                ->latest();
                
            if ($selectedMonth !== 'all') {
                $query->where('month_year', $selectedMonth);
            }

            $conferences = $query->paginate(20)->withQueryString();

            return view('admin.conferences.index', compact('conferences', 'countries', 'selectedMonth'));
        }

        $monthlyStats = Conference::select('month_year')
            ->selectRaw('count(*) as conferences_count')
            ->whereNotNull('month_year')
            ->groupBy('month_year')
            ->orderBy('month_year', 'desc')
            ->get();

        foreach ($monthlyStats as $stat) {
            $stat->articles_count = \App\Models\Article::whereHas('conference', function($q) use ($stat) {
                $q->where('month_year', $stat->month_year);
            })->count();
        }

        return view('admin.conferences.index', compact('monthlyStats', 'countries'));
    }

    public function destroyMonth($month)
    {
        $conferences = Conference::where('month_year', $month)->get();
        $count = $conferences->count();

        foreach ($conferences as $conference) {
            if ($conference->collection_pdf_path) {
                Storage::disk('public')->delete($conference->collection_pdf_path);
            }
            if ($conference->pdf_collection_path) {
                Storage::disk('public')->delete($conference->pdf_collection_path);
            }
            $conference->delete();
        }

        return redirect()->route('admin.conferences.index')->with('success', "{$month} oyiga tegishli jami {$count} ta konferensiya muvaffaqiyatli o'chirildi.");
    }

    /**
     * Yangi konferensiya qo'shish formasi
     */
    public function create()
    {
        $countries = Country::active()->orderBy('name')->get();
        return view('admin.conferences.create', compact('countries'));
    }

    /**
     * Yangi konferensiya saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'conference_date' => 'required|date',
            'status' => 'required|in:draft,active,completed',
        ]);

        $validated['month_year'] = date('Y-m', strtotime($validated['conference_date']));

        Conference::create($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Konferensiya muvaffaqiyatli yaratildi.');
    }

    /**
     * Oylik konferensiyalarni avtomatik generatsiya qilish
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'target_month' => 'required|date_format:Y-m',
        ]);

        $targetMonth = $validated['target_month'];
        list($year, $month) = explode('-', $targetMonth);

        $countries = Country::where('is_active', true)
            ->orderBy('schedule_order')
            ->orderBy('id')
            ->get();

        if ($countries->isEmpty()) {
            return redirect()->back()->with('error', 'Konferensiya yaratish uchun faol davlatlar topilmadi.');
        }

        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $totalCountries = $countries->count();
        $daysPerCountry = floor($totalDays / $totalCountries);
        if ($daysPerCountry < 1) $daysPerCountry = 1;

        $currentDay = 1;
        $createdCount = 0;

        foreach ($countries as $index => $country) {
            if ($currentDay > $totalDays) break;

            $startDay = $currentDay;
            $endDay = $startDay + 2;

            if ($endDay > $totalDays) {
                $endDay = $totalDays;
            }

            $startDate = sprintf('%04d-%02d-%02d', $year, $month, $startDay);
            $endDate = sprintf('%04d-%02d-%02d', $year, $month, $endDay);

            Conference::create([
                'country_id' => $country->id,
                'title' => $country->conference_name ?: 'Bu yerda konferensiya nomi yoziladi',
                'description' => $country->conference_description,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'conference_date' => $endDate, // Asosiy yakunlash sanasi (3-kun) orqali maqola va sertifikat chiqariladi.
                'month_year' => $targetMonth,
                'status' => 'active',
            ]);

            $createdCount++;
            $currentDay = $endDay + 1;
        }

        return redirect()->route('admin.conferences.index')
            ->with('success', "{$targetMonth} oyi uchun {$createdCount} ta konferensiya muvaffaqiyatli taqsimlanib yaratildi.");
    }

    /**
     * Konferensiyani ko'rish
     */
    public function show(Conference $conference)
    {
        $conference->load(['country', 'articles.author', 'articles.certificate']);
        return view('admin.conferences.show', compact('conference'));
    }

    /**
     * Konferensiyani tahrirlash formasi
     */
    public function edit(Conference $conference)
    {
        $countries = Country::active()->orderBy('name')->get();
        return view('admin.conferences.edit', compact('conference', 'countries'));
    }

    /**
     * Konferensiyani yangilash
     */
    public function update(Request $request, Conference $conference)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'conference_date' => 'required|date',
            'status' => 'required|in:draft,active,completed',
        ]);

        $validated['month_year'] = date('Y-m', strtotime($validated['conference_date']));

        $conference->update($validated);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Konferensiya muvaffaqiyatli yangilandi.');
    }

    /**
     * Konferensiyani o'chirish
     */
    public function destroy(Conference $conference)
    {
        // Kolleksiya PDF ni o'chirish
        if ($conference->collection_pdf_path) {
            Storage::disk('public')->delete($conference->collection_pdf_path);
        }
        if ($conference->pdf_collection_path) {
            Storage::disk('public')->delete($conference->pdf_collection_path);
        }

        $conference->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Konferensiya muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Oylik to'plam PDF yaratish (oddiy versiya)
     */
    public function generateCollection(Conference $conference)
    {
        try {
            $path = $this->pdfService->generateMonthlyCollection($conference);
            $conference->update(['collection_pdf_path' => $path]);

            return redirect()->route('admin.conferences.show', $conference)
                ->with('success', 'Oylik to\'plam PDF muvaffaqiyatli yaratildi.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'PDF yaratishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Konferensiya yakunida barcha maqolalar to'plamini yaratish
     * Har bir maqola formatlangan holda birlashtiriladi
     */
    public function generateFullCollection(Conference $conference)
    {
        try {
            // ArticlePdfService orqali to'liq to'plam yaratish
            $path = $this->articlePdfService->generateCollection($conference);
            $conference->update([
                'pdf_collection_path' => $path,
                'is_completed' => true,
            ]);

            return redirect()->route('admin.conferences.show', $conference)
                ->with('success', 'Konferensiya to\'plami muvaffaqiyatli yaratildi! Barcha maqolalar birlashtirildi.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'To\'plam yaratishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * To'plamni yuklab olish
     */
    public function downloadCollection(Conference $conference)
    {
        // Konferensiya nomi bilan fayl nomini shakllantirish
        $conferenceTitle = preg_replace('/[\/\\:\*\?"<>\|]/', '', $conference->title);
        if (empty($conferenceTitle)) {
            $conferenceTitle = 'Collection_' . $conference->country->code . '_' . $conference->month_year;
        }
        
        $fileName = trim($conferenceTitle) . '.pdf';

        // Avval to'liq to'plamni tekshirish
        if ($conference->pdf_collection_path && Storage::disk('public')->exists($conference->pdf_collection_path)) {
            return Storage::disk('public')->download(
                $conference->pdf_collection_path,
                $fileName
            );
        }

        // Keyin oddiy to'plamni tekshirish
        if ($conference->collection_pdf_path && Storage::disk('public')->exists($conference->collection_pdf_path)) {
            return Storage::disk('public')->download(
                $conference->collection_pdf_path,
                $fileName
            );
        }

        return redirect()->back()->with('error', 'To\'plam fayli topilmadi. Avval to\'plam yarating.');
    }

    /**
     * Konferensiyani yakunlash
     */
    public function complete(Conference $conference)
    {
        // Avval to'plamni yaratish
        try {
            $path = $this->articlePdfService->generateCollection($conference);

            $conference->update([
                'pdf_collection_path' => $path,
                'is_completed' => true,
                'status' => 'completed',
            ]);

            // Server hajmini tejash uchun ortiqcha fayllarni o'chirish (Archive qilingandan so'ng)
            // 1. Har bir maqolaning alohida formatlangan PDF'i endi kerak emas, chunki ular to'plamga birlashtirildi
            // (Agar kimdir alohida yuklab olsa, u on-the-fly qayta yaratiladi)
            foreach ($conference->articles as $article) {
                if ($article->formatted_pdf_path && Storage::disk('public')->exists($article->formatted_pdf_path)) {
                    Storage::disk('public')->delete($article->formatted_pdf_path);
                    $article->update(['formatted_pdf_path' => null]);
                }
            }

            // 2. Vaqtinchalik DOCX fayllarini tozalash (1 kundan eskilari)
            $docxDir = Storage::disk('public')->path('articles/docx');
            if (is_dir($docxDir)) {
                $files = glob($docxDir . '/*');
                $now = time();
                foreach ($files as $file) {
                    if (is_file($file)) {
                        if ($now - filemtime($file) >= 60 * 60 * 24) { // 1 kundan eski
                            @unlink($file);
                        }
                    }
                }
            }

            return redirect()->route('admin.conferences.show', $conference)
                ->with('success', 'Konferensiya muvaffaqiyatli yakunlandi! To\'plam PDF yaratildi va server hajmi tejaldi.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Konferensiyani yakunlashda xatolik: ' . $e->getMessage());
        }
    }
}
