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

    public function download(Article $article)
    {
        $certificate = $article->certificate;

        if (!$certificate || !$certificate->pdf_path) {
            return redirect()->back()
                ->with('error', 'Sertifikat topilmadi.');
        }

        $path     = Storage::disk('public')->path($certificate->pdf_path);
        
        // Muallif ismi bilan nomlash
        $authorName = preg_replace('/[\/\\:\*\?"<>\|]/', '', $article->author_display_name);
        if (empty($authorName)) {
            $authorName = 'Sertifikat_' . ($certificate->certificate_number ?? $article->id);
        }
        
        $extension = pathinfo($certificate->pdf_path, PATHINFO_EXTENSION);
        if (!$extension) {
            $extension = str_ends_with($certificate->pdf_path, '.zip') ? 'zip' : (str_ends_with($certificate->pdf_path, '.jpg') ? 'jpg' : 'pdf');
        }
        
        $fileName = trim($authorName) . ' - sertifikat' . '.' . $extension;

        $mime = $extension === 'zip' ? 'application/zip' : ($extension === 'jpg' ? 'image/jpeg' : 'application/pdf');

        return response()->file($path, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
