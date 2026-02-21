# Google Scholar Ready Checklist

## Pre-Launch Verification

Use this checklist before submitting your site to Google Scholar.

### ✅ Article Page Requirements

- [ ] Each article has a unique, permanent URL
- [ ] Article title is in `<h1>` tag and clearly visible
- [ ] Author name(s) displayed on page
- [ ] Author affiliation visible (if available)
- [ ] Author email visible or in metadata
- [ ] Abstract is plain text (NOT an image)
- [ ] Keywords are displayed
- [ ] Publication date is visible
- [ ] Conference name is shown
- [ ] Page numbers are displayed
- [ ] PDF download link is prominent

### ✅ Meta Tags (check page source)

- [ ] `citation_title` present with correct value
- [ ] `citation_author` present for each author
- [ ] `citation_publication_date` in YYYY/MM/DD format
- [ ] `citation_conference_title` present
- [ ] `citation_pdf_url` with public, accessible URL
- [ ] `citation_abstract` (optional but recommended)
- [ ] `citation_keywords` (optional but recommended)

### ✅ PDF Requirements

- [ ] PDF is text-selectable (not scanned image)
- [ ] PDF first page has title, authors, abstract
- [ ] PDF is publicly accessible without login
- [ ] PDF URL responds with `Content-Type: application/pdf`

### ✅ Technical Requirements

- [ ] HTTPS enabled site-wide
- [ ] robots.txt allows crawling: `/robots.txt`
- [ ] XML sitemap exists: `/sitemap.xml`
- [ ] Pages load without JavaScript requirement
- [ ] No CAPTCHA or login walls on articles

### ✅ Testing Commands

```bash
# Check if indexed (after 2-8 weeks)
site:yourdomain.com "Article Title"

# Validate robots.txt
curl https://yourdomain.com/robots.txt

# Check PDF accessibility
curl -I https://yourdomain.com/storage/articles/example.pdf
```

### ✅ Submission

1. Go to: https://scholar.google.com/intl/en/scholar/inclusion.html
2. Read the inclusion guidelines
3. Submit your site URL
4. Wait 2-8 weeks for review
5. Test with site: search

---

**Status:** Ready for Review  
**Last Updated:** {{ date('Y-m-d') }}
