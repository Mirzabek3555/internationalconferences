# Google Scholar Indexing - Technical Specification

## International Scientific Online Conference (ISOC) Platform

**Version:** 2.0  
**Date:** February 2026  
**Status:** Production-Ready

---

## 1. Executive Summary

This document provides complete technical specifications for making the ISOC conference platform compliant with Google Scholar indexing requirements. Following these guidelines ensures that all published articles are discoverable, properly attributed, and academically citable.

---

## 2. URL Structure Requirements

### 2.1 Permanent Article URLs

Each article MUST have a unique, permanent, SEO-friendly URL:

```
https://artiqle.uz/article/{article-slug}
```

**Examples:**
- `https://artiqle.uz/article/innovative-approaches-to-renewable-energy`
- `https://artiqle.uz/article/machine-learning-in-healthcare-diagnostics`

**Alternative Structure (Country-Based):**
```
https://artiqle.uz/{country-code}-{year}/article/{slug}
```

**Examples:**
- `https://artiqle.uz/uzbekistan-2026/article/innovative-approaches`
- `https://artiqle.uz/germany-2026/article/machine-learning-healthcare`

### 2.2 URL Requirements Checklist

- [x] Permanent (no session IDs, no query parameters)
- [x] Descriptive (contains article title keywords)
- [x] Lowercase with hyphens
- [x] HTTPS enabled
- [x] No login required to access
- [x] No CAPTCHA blocking

---

## 3. Google Scholar Meta Tags (MANDATORY)

### 3.1 Required Meta Tags

Each article page MUST include these meta tags in the `<head>` section:

```html
<!-- Google Scholar Meta Tags -->
<meta name="citation_title" content="Article Title Here">
<meta name="citation_author" content="Author Full Name">
<meta name="citation_author_institution" content="University or Institution Name">
<meta name="citation_publication_date" content="2026/02/03">
<meta name="citation_conference_title" content="International Scientific Online Conference">
<meta name="citation_abstract" content="Full abstract text here...">
<meta name="citation_keywords" content="keyword1; keyword2; keyword3">
<meta name="citation_pdf_url" content="https://artiqle.uz/storage/articles/article-slug.pdf">
<meta name="citation_language" content="en">
<meta name="citation_firstpage" content="1">
<meta name="citation_lastpage" content="10">
```

### 3.2 Multiple Authors

For articles with multiple authors, use separate tags:

```html
<meta name="citation_author" content="John Smith">
<meta name="citation_author_institution" content="Harvard University">
<meta name="citation_author_email" content="john.smith@harvard.edu">

<meta name="citation_author" content="Jane Doe">
<meta name="citation_author_institution" content="MIT">
<meta name="citation_author_email" content="jane.doe@mit.edu">
```

### 3.3 Conference-Specific Tags

```html
<meta name="citation_conference_title" content="Innovative Developments in Science - Uzbekistan 2026">
<meta name="citation_publisher" content="International Scientific Online Conference (ISOC)">
<meta name="citation_public_url" content="https://artiqle.uz/article/slug">
```

### 3.4 Optional but Recommended Tags

```html
<!-- DOI (if available) -->
<meta name="citation_doi" content="10.1234/isoc.2026.001">

<!-- ISSN/ISBN -->
<meta name="citation_issn" content="1234-5678">

<!-- Volume/Issue -->
<meta name="citation_volume" content="1">
<meta name="citation_issue" content="2">
```

---

## 4. Article Page HTML Structure

### 4.1 Required Visible Elements

Each article page MUST display these elements clearly (not hidden):

| Element | HTML Tag | Visibility |
|---------|----------|------------|
| Article Title | `<h1>` | Prominent, at top |
| Author Name(s) | `<span>` or `<p>` | Clearly visible |
| Author Affiliation | `<p>` | Below author name |
| Author Email | `<a>` or `<span>` | Visible or in metadata |
| Abstract | `<div>` or `<section>` | Full text, not image |
| Keywords | `<span>` or `<ul>` | Plain text |
| Publication Date | `<time>` | Visible |
| Conference Name | `<p>` or `<span>` | Visible |
| Page Numbers | `<span>` | Visible |
| PDF Download Link | `<a href="...">` | Prominent button |

### 4.2 Sample Article Page Structure

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Article Title - ISOC 2026</title>
    
    <!-- Standard SEO Meta -->
    <meta name="description" content="Abstract excerpt...">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://artiqle.uz/article/slug">
    
    <!-- Google Scholar Meta Tags -->
    <meta name="citation_title" content="Article Title">
    <meta name="citation_author" content="Author Name">
    <!-- ... more citation tags ... -->
    
    <!-- Open Graph for Social Sharing -->
    <meta property="og:title" content="Article Title">
    <meta property="og:description" content="Abstract...">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://artiqle.uz/article/slug">
    
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ScholarlyArticle",
        "name": "Article Title",
        "author": {
            "@type": "Person",
            "name": "Author Name",
            "affiliation": "University Name"
        },
        "datePublished": "2026-02-03",
        "abstract": "Abstract text...",
        "publisher": {
            "@type": "Organization",
            "name": "ISOC"
        }
    }
    </script>
</head>
<body>
    <article itemscope itemtype="http://schema.org/ScholarlyArticle">
        <!-- Article Header -->
        <header>
            <h1 itemprop="name">Article Title</h1>
            
            <div class="article-meta">
                <span class="author" itemprop="author">Author Full Name</span>
                <span class="affiliation">University, Country</span>
                <span class="email">author@university.edu</span>
            </div>
            
            <div class="publication-info">
                <time datetime="2026-02-03" itemprop="datePublished">February 3, 2026</time>
                <span class="conference">ISOC Uzbekistan 2026</span>
                <span class="pages">Pages: 1-10</span>
            </div>
        </header>
        
        <!-- Abstract Section -->
        <section class="abstract">
            <h2>Abstract</h2>
            <p itemprop="abstract">
                Full abstract text here. This must be plain text,
                not an image. The abstract should be complete and
                readable without requiring any user interaction.
            </p>
        </section>
        
        <!-- Keywords -->
        <section class="keywords">
            <h3>Keywords</h3>
            <ul itemprop="keywords">
                <li>keyword1</li>
                <li>keyword2</li>
                <li>keyword3</li>
            </ul>
        </section>
        
        <!-- PDF Download -->
        <section class="download">
            <a href="https://artiqle.uz/storage/articles/article.pdf" 
               class="btn-download"
               itemprop="url">
                Download PDF
            </a>
        </section>
        
        <!-- Full Article Content (optional on web) -->
        <section class="content" itemprop="articleBody">
            <!-- Article text if displayed on web -->
        </section>
    </article>
</body>
</html>
```

---

## 5. PDF Requirements

### 5.1 Mandatory PDF Specifications

| Requirement | Specification |
|-------------|---------------|
| Format | PDF/A (archival) preferred, standard PDF acceptable |
| Text | Selectable, searchable (NOT scanned images) |
| First Page | Must include title, authors, abstract |
| Metadata | Embedded PDF metadata with title, author |
| Accessibility | Publicly accessible without login |
| File Size | Reasonable (<20MB recommended) |
| Naming | Consistent: `article-slug.pdf` or `article-id.pdf` |

### 5.2 PDF First Page Requirements

The first page of every PDF MUST contain:

1. **Article Title** - Prominently displayed at top
2. **Author Name(s)** - Full names with affiliations
3. **Abstract** - Complete abstract text
4. **Conference Name** - "International Scientific Online Conference"
5. **Publication Date** - Conference date
6. **Page Numbers** - Starting from page 1

### 5.3 PDF URL Requirements

```
https://artiqle.uz/storage/articles/{filename}.pdf
```

- Must be HTTPS
- Must be publicly accessible (no auth required)
- Must return proper `Content-Type: application/pdf` header
- Should have consistent naming convention

---

## 6. robots.txt Configuration

```txt
# robots.txt for artiqle.uz

User-agent: *
Allow: /

# Allow Google Scholar bot specifically
User-agent: Googlebot-Scholar
Allow: /

# Allow PDF access
Allow: /storage/articles/*.pdf

# Disallow admin areas
Disallow: /admin/
Disallow: /user/

# Sitemap location
Sitemap: https://artiqle.uz/sitemap.xml
```

---

## 7. Sitemap Requirements

### 7.1 XML Sitemap Structure

Create `sitemap.xml` with all article URLs:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Homepage -->
    <url>
        <loc>https://artiqle.uz/</loc>
        <lastmod>2026-02-03</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Article Pages -->
    <url>
        <loc>https://artiqle.uz/article/innovative-approaches</loc>
        <lastmod>2026-02-01</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <!-- More articles... -->
</urlset>
```

### 7.2 HTML Sitemap

Include a human-readable sitemap page:
- `https://artiqle.uz/sitemap`
- Lists all countries, conferences, and articles
- Organized hierarchically

---

## 8. Technical Requirements

### 8.1 Server-Side Rendering (SSR)

**CRITICAL:** Article content MUST be rendered server-side.

```php
// Laravel Controller Example
public function show(Article $article)
{
    $article->load(['conference.country', 'author']);
    
    return view('public.articles.show', [
        'article' => $article,
        // All data available for SSR
    ]);
}
```

**DO NOT:**
- Load article content via JavaScript/AJAX
- Use client-side routing that hides content from crawlers
- Require user interaction to reveal abstract/content

### 8.2 Page Load Performance

| Metric | Target |
|--------|--------|
| First Contentful Paint | < 1.5s |
| Largest Contentful Paint | < 2.5s |
| Time to Interactive | < 3.0s |
| Total Page Size | < 2MB |

### 8.3 HTTPS Requirement

All pages and assets MUST be served over HTTPS:

```nginx
# Nginx SSL Configuration
server {
    listen 443 ssl http2;
    server_name artiqle.uz;
    
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    # Redirect HTTP to HTTPS
    if ($scheme = http) {
        return 301 https://$server_name$request_uri;
    }
}
```

---

## 9. Schema.org Structured Data

### 9.1 ScholarlyArticle Schema

```json
{
    "@context": "https://schema.org",
    "@type": "ScholarlyArticle",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "https://artiqle.uz/article/slug"
    },
    "headline": "Article Title",
    "alternativeHeadline": "Short Title",
    "abstract": "Full abstract text...",
    "author": [
        {
            "@type": "Person",
            "name": "Author One",
            "affiliation": {
                "@type": "Organization",
                "name": "University Name"
            },
            "email": "author@university.edu"
        }
    ],
    "datePublished": "2026-02-03",
    "dateModified": "2026-02-03",
    "publisher": {
        "@type": "Organization",
        "name": "International Scientific Online Conference",
        "logo": {
            "@type": "ImageObject",
            "url": "https://artiqle.uz/images/logo.png"
        }
    },
    "keywords": ["keyword1", "keyword2", "keyword3"],
    "inLanguage": "en",
    "isAccessibleForFree": true,
    "license": "https://creativecommons.org/licenses/by/4.0/",
    "pagination": "1-10",
    "isPartOf": {
        "@type": "Periodical",
        "name": "ISOC Conference Proceedings",
        "issn": "1234-5678"
    }
}
```

---

## 10. Common Rejection Reasons to Avoid

### 10.1 Critical Issues (Immediate Rejection)

| Issue | Solution |
|-------|----------|
| Articles only inside PDF | Create HTML article pages |
| Missing author names | Display author clearly on page |
| Abstract as image | Use plain text for abstract |
| Login wall | Make articles publicly accessible |
| Dynamic/session-based URLs | Use permanent, clean URLs |
| Multiple articles on one page | One article per page |
| No PDF link | Provide direct PDF download |

### 10.2 Quality Issues (May Delay Indexing)

| Issue | Solution |
|-------|----------|
| Missing meta tags | Add all citation_* meta tags |
| Inconsistent formatting | Use consistent templates |
| Poor content quality | Peer review process |
| Missing publication date | Always include date |
| No conference attribution | Include conference name |

---

## 11. Verification & Testing

### 11.1 Pre-Launch Checklist

- [ ] All articles have unique URLs
- [ ] Meta tags present on all article pages
- [ ] PDFs are text-selectable
- [ ] PDFs are publicly accessible
- [ ] robots.txt allows crawling
- [ ] Sitemap includes all articles
- [ ] HTTPS enabled site-wide
- [ ] Page loads under 3 seconds
- [ ] Articles render without JavaScript

### 11.2 Testing Commands

**Check if article is indexed:**
```
site:artiqle.uz "Article Title"
```

**Check robots.txt:**
```
https://artiqle.uz/robots.txt
```

**Validate structured data:**
```
https://search.google.com/test/rich-results
```

### 11.3 Google Scholar Submission

1. Go to: https://scholar.google.com/intl/en/scholar/inclusion.html
2. Submit your site for consideration
3. Wait 2-8 weeks for review
4. Check indexing with site: search

---

## 12. Recommended Additional Features

### 12.1 DOI Assignment

- Register with Crossref as a publisher
- Assign DOIs to each article
- Format: `10.{prefix}/{suffix}`
- Example: `10.55555/isoc.2026.001`

### 12.2 Citation Export

Provide export formats:
- BibTeX
- EndNote
- RIS
- APA/MLA formatted citation

### 12.3 Editorial Pages

Create these supporting pages:
- `/about` - About the conference
- `/editorial-board` - List of reviewers/editors
- `/peer-review-policy` - Review process description
- `/ethics` - Publication ethics statement
- `/contact` - Publisher contact information

### 12.4 ORCID Integration

- Allow authors to link ORCID IDs
- Display ORCID on article pages
- Include in meta tags

---

## 13. Implementation Priority

### Phase 1 (Critical - Week 1)
1. Add Google Scholar meta tags to article pages
2. Ensure PDFs are publicly accessible
3. Verify robots.txt configuration
4. Create XML sitemap

### Phase 2 (Important - Week 2)
5. Add Schema.org structured data
6. Create HTML sitemap
7. Add citation export functionality
8. Verify all URLs are HTTPS

### Phase 3 (Enhancement - Week 3-4)
9. Create editorial board page
10. Add peer review policy page
11. Implement DOI assignment
12. Add ORCID support

---

## 14. Contact & Support

**Technical Questions:**
- Google Scholar Inclusion: https://scholar.google.com/intl/en/scholar/inclusion.html

**Platform Support:**
- Email: support@artiqle.uz
- Documentation: https://artiqle.uz/docs

---

*Document Version: 2.0 | Last Updated: February 2026*
