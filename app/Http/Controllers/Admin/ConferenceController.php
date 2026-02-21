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
    public function index()
    {
        $conferences = Conference::with('country')
            ->withCount('articles')
            ->latest()
            ->paginate(15);

        // Davlatlar ro'yxati (konferensiya nomlarini o'zgartirish uchun)
        $countries = Country::where('is_active', true)->orderBy('name')->get();

        return view('admin.conferences.index', compact('conferences', 'countries'));
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
            'conference_date' => 'required|date',
            'status' => 'required|in:draft,active,completed',
        ]);

        $validated['month_year'] = date('Y-m', strtotime($validated['conference_date']));

        Conference::create($validated);

        return redirect()->route('admin.conferences.index')
            ->with('success', 'Konferensiya muvaffaqiyatli yaratildi.');
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
            'conference_date' => 'required|date',
            'status' => 'required|in:draft,active,completed',
        ]);

        $validated['month_year'] = date('Y-m', strtotime($validated['conference_date']));

        $conference->update($validated);

        return redirect()->route('admin.conferences.index')
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

        return redirect()->route('admin.conferences.index')
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
        // Avval to'liq to'plamni tekshirish
        if ($conference->pdf_collection_path && Storage::disk('public')->exists($conference->pdf_collection_path)) {
            return Storage::disk('public')->download(
                $conference->pdf_collection_path,
                'Collection_' . $conference->country->code . '_' . $conference->month_year . '.pdf'
            );
        }

        // Keyin oddiy to'plamni tekshirish
        if ($conference->collection_pdf_path && Storage::disk('public')->exists($conference->collection_pdf_path)) {
            return Storage::disk('public')->download(
                $conference->collection_pdf_path,
                'Collection_' . $conference->country->code . '_' . $conference->month_year . '.pdf'
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

            return redirect()->route('admin.conferences.show', $conference)
                ->with('success', 'Konferensiya muvaffaqiyatli yakunlandi! To\'plam PDF yaratildi.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Konferensiyani yakunlashda xatolik: ' . $e->getMessage());
        }
    }
}
