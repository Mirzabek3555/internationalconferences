<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Sertifikat yaratish
     */
    public function generate(Article $article)
    {
        try {
            $certificate = $this->certificateService->generate($article);

            return redirect()->route('admin.articles.show', $article)
                ->with('success', 'Sertifikat muvaffaqiyatli yaratildi: ' . $certificate->certificate_number);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Sertifikat yaratishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Sertifikatni qayta yaratish
     */
    public function regenerate(Article $article)
    {
        try {
            $certificate = $this->certificateService->regenerate($article);

            return redirect()->route('admin.articles.show', $article)
                ->with('success', 'Sertifikat qayta yaratildi.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Sertifikat yaratishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Sertifikatni yuklab olish
     */
    public function download(Article $article)
    {
        $certificate = $article->certificate;

        if (!$certificate || !$certificate->pdf_path) {
            return redirect()->back()
                ->with('error', 'Sertifikat topilmadi.');
        }

        return Storage::disk('public')->download($certificate->pdf_path);
    }
}
