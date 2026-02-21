<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Conference;
use App\Models\Country;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate XML Sitemap for Google Scholar and SEO
     */
    public function xml()
    {
        $articles = Article::where('status', 'published')
            ->with(['conference.country'])
            ->orderBy('published_at', 'desc')
            ->get();

        $conferences = Conference::with('country')
            ->where('status', '!=', 'draft')
            ->orderBy('conference_date', 'desc')
            ->get();

        $countries = Country::whereHas('conferences')
            ->orderBy('name')
            ->get();

        $content = view('sitemap.xml', [
            'articles' => $articles,
            'conferences' => $conferences,
            'countries' => $countries,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * HTML Sitemap for users
     */
    public function html()
    {
        $countries = Country::with([
            'conferences' => function ($q) {
                $q->with([
                    'articles' => function ($q2) {
                        $q2->where('status', 'published')->orderBy('order_number');
                    }
                ])->orderBy('conference_date', 'desc');
            }
        ])
            ->whereHas('conferences')
            ->orderBy('name')
            ->get();

        return view('sitemap.html', [
            'countries' => $countries,
        ]);
    }

    /**
     * Articles-only sitemap for Google Scholar
     */
    public function articles()
    {
        $articles = Article::where('status', 'published')
            ->with(['conference.country'])
            ->orderBy('published_at', 'desc')
            ->get();

        $content = view('sitemap.articles', [
            'articles' => $articles,
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
