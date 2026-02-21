<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    
    {{-- Homepage --}}
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    {{-- Countries Page --}}
    <url>
        <loc>{{ route('countries') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    
    {{-- Individual Country Pages --}}
@foreach($countries as $country)
    <url>
        <loc>{{ route('country.show', $country) }}</loc>
        <lastmod>{{ $country->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
@endforeach
    
    {{-- Conference Pages --}}
@foreach($conferences as $conference)
    <url>
        <loc>{{ route('conference.show', $conference) }}</loc>
        <lastmod>{{ $conference->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
@endforeach
    
    {{-- Article Pages (Most important for Google Scholar) --}}
@foreach($articles as $article)
    <url>
        <loc>{{ route('article.show', $article) }}</loc>
        <lastmod>{{ $article->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
@endforeach
    
    {{-- Static Pages --}}
    <url>
        <loc>{{ url('/sitemap') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
</urlset>
