<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Conference;
use App\Models\Country;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Bosh sahifa - davlatlar va ularning konferensiyalari
     */
    public function home()
    {
        // Har bir davlatdagi nashr qilingan maqolalar sonini hisoblash (Faqat active konferensiyalar uchun)
        $countries = Country::active()
            ->withCount([
                'articles' => function ($query) {
                    $query->where('articles.status', 'published')
                          ->whereHas('conference', function($q) {
                              $q->where('status', 'active');
                          });
                }
            ])
            ->orderBy('name')
            ->take(10)
            ->get();

        // Statistika (Barcha vaqtlardagi nashr qilingan maqolalar, shu jumladan arxivdagi)
        $totalArticles = Article::published()->count();

        return view('public.home', compact('countries', 'totalArticles'));
    }

    /**
     * Davlatlar (Konferensiyalar) ro'yxati
     */
    public function countries()
    {
        $countries = Country::active()
            ->withCount([
                'articles' => function ($query) {
                    $query->where('articles.status', 'published')
                          ->whereHas('conference', function($q) {
                              $q->where('status', 'active');
                          });
                }
            ])
            ->orderBy('name')
            ->get();

        return view('public.countries.index', compact('countries'));
    }

    /**
     * Davlat sahifasi - shu davlatdagi barcha maqolalar
     */
    public function country(Country $country)
    {
        // Shu davlatdagi barcha ACTIVE konferensiyalarning nashr qilingan maqolalari
        $articles = Article::whereHas('conference', function ($query) use ($country) {
            $query->where('country_id', $country->id)
                  ->where('status', 'active');
        })
            ->published()
            ->with(['conference', 'author'])
            ->orderByDesc('published_at')
            ->paginate(20);

        return view('public.countries.show', compact('country', 'articles'));
    }

    /**
     * Konferensiya sahifasi (Legacy - eski konferensiyalar uchun)
     */
    public function conference(Conference $conference)
    {
        $conference->load([
            'country',
            'articles' => function ($query) {
                $query->published()->with('author')->orderBy('order_number');
            }
        ]);

        return view('public.conferences.show', compact('conference'));
    }

    /**
     * Maqola sahifasi
     */
    public function article(Article $article)
    {
        if ($article->status !== 'published') {
            abort(404);
        }

        $article->load(['conference.country', 'author', 'certificate']);

        return view('public.articles.show', compact('article'));
    }

    /**
     * Arxiv - Yakunlangan konferensiyalar ro'yxati
     */
    public function archive()
    {
        $conferences = Conference::with('country')
            ->where('status', 'completed')
            ->orderByDesc('month_year')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('public.archive.index', compact('conferences'));
    }
}
