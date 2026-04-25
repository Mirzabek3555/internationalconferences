<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Foydalanuvchi dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();

        $stats = [
            'total_articles' => $user->articles()->count(),
            'published_articles' => $user->articles()->published()->count(),
            'pending_articles' => $user->articles()->pending()->count(),
        ];

        $recentArticles = $user->articles()
            ->with(['conference.country', 'certificate'])
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('stats', 'recentArticles'));
    }

    /**
     * Mening maqolalarim
     */
    public function myArticles()
    {
        $articles = auth()->user()
            ->articles()
            ->with(['conference.country', 'certificate'])
            ->latest()
            ->paginate(10);

        return view('user.articles', compact('articles'));
    }

    /**
     * Mening sertifikatlarim
     */
    public function myCertificates()
    {
        $articles = auth()->user()
            ->articles()
            ->whereHas('certificate')
            ->with(['conference.country', 'certificate'])
            ->latest()
            ->paginate(10);

        return view('user.certificates', compact('articles'));
    }

    /**
     * Maqola PDF yuklab olish
     */
    public function downloadArticle(Article $article)
    {
        // Faqat o'z maqolasini yuklab olish
        if ($article->author_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return Storage::disk('public')->download($article->pdf_path);
    }

    /**
     * Sertifikat PDF yuklab olish
     */
    public function downloadCertificate(Article $article)
    {
        // Faqat o'z sertifikatini yuklab olish
        if ($article->author_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $certificate = $article->certificate;

        if (!$certificate || !$certificate->pdf_path) {
            return redirect()->back()
                ->with('error', 'Sertifikat topilmadi.');
        }

        $extension = pathinfo($certificate->pdf_path, PATHINFO_EXTENSION);
        if (!$extension) {
            $extension = str_ends_with($certificate->pdf_path, '.zip') ? 'zip' : 'jpg';
        }

        $contentType = $extension === 'zip' ? 'application/zip' : ($extension === 'pdf' ? 'application/pdf' : 'image/jpeg');

        return response()->file(
            Storage::disk('public')->path($certificate->pdf_path),
            [
                'Content-Type'        => $contentType,
                'Content-Disposition' => 'attachment; filename="certificate_' . $certificate->certificate_number . '.' . $extension . '"',
            ]
        );
    }
}
