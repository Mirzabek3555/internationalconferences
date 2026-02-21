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
        // Har bir davlatdagi nashr qilingan maqolalar sonini hisoblash
        $countries = Country::active()
            ->withCount([
                'articles' => function ($query) {
                    $query->where('articles.status', 'published');
                }
            ])
            ->orderBy('name')
            ->take(10)
            ->get();

        // Statistika
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
                    $query->where('articles.status', 'published');
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
        // Shu davlatdagi barcha konferensiyalarning nashr qilingan maqolalari
        $articles = Article::whereHas('conference', function ($query) use ($country) {
            $query->where('country_id', $country->id);
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
}
