<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Certificate;
use Carbon\Carbon;

class CertificateService
{
    protected PdfService $pdfService;

    public function __construct(PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Yangi sertifikat yaratish
     */
    public function generate(Article $article): Certificate
    {
        // Agar sertifikat mavjud bo'lsa, uni qaytarish
        if ($article->certificate) {
            return $article->certificate;
        }

        // Yangi sertifikat yaratish
        $certificate = Certificate::create([
            'article_id' => $article->id,
            'certificate_number' => Certificate::generateNumber(),
            'issue_date' => Carbon::now(),
        ]);

        // Article ni refresh qilish - yangi sertifikat bilan
        $article->refresh();

        // PDF generatsiya qilish
        $pdfPath = $this->pdfService->generateCertificate($article, $certificate);
        $certificate->update(['pdf_path' => $pdfPath]);

        return $certificate;
    }

    /**
     * Sertifikatni qayta generatsiya qilish
     */
    public function regenerate(Article $article): Certificate
    {
        $certificate = $article->certificate;

        if (!$certificate) {
            return $this->generate($article);
        }

        // PDF ni qayta generatsiya qilish
        $pdfPath = $this->pdfService->generateCertificate($article, $certificate);
        $certificate->update(['pdf_path' => $pdfPath]);

        return $certificate;
    }
}
