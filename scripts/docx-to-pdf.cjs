/**
 * DOCX HTML → PDF konverter (Puppeteer yordamida)
 * 
 * Ishlatish: node scripts/docx-to-pdf.cjs <output_pdf_path>
 * Kirish: stdin orqali HTML string (DocxProcessorService dan kelgan)
 * Chiqish: PDF fayl yaratiladi
 * 
 * KaTeX formulalar, jadvallar, rasmlar to'liq render qilinadi
 */
const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const outputPath = process.argv[2];
const topMarginFirstPage = process.argv[3] || '20mm';
const topMarginRest = process.argv[4] || '20mm';
const leftMargin = process.argv[5] || '20mm';
const rightMargin = process.argv[6] || '15mm';
const bottomMargin = process.argv[7] || '20mm';

if (!outputPath) {
    console.error(JSON.stringify({ error: 'Output PDF yo\'li ko\'rsatilmagan' }));
    process.exit(1);
}

// KaTeX CSS ni o'qish
let katexCss = '';
const katexCssPath = path.join(__dirname, '..', 'node_modules', 'katex', 'dist', 'katex.min.css');
if (fs.existsSync(katexCssPath)) {
    katexCss = fs.readFileSync(katexCssPath, 'utf8');
}

// stdin yoki fayldan o'qish
const htmlFilePath = process.argv[8]; // Opsyonal: input.html yo'li

async function generatePdf(inputHtml) {
    try {
        // To'liq HTML sahifa yaratish
        const fullHtml = `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        ${katexCss}
        
        @page {
            margin: ${topMarginRest} ${rightMargin} ${bottomMargin} ${leftMargin};
            size: A4 portrait;
        }
        @page :first {
            margin-top: ${topMarginFirstPage};
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', 'DejaVu Serif', Georgia, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000000;
            background: #ffffff;
        }
        
        p {
            margin-bottom: 6pt;
            text-align: justify;
            text-indent: 1.25cm;
        }
        
        h1, h2, h3, h4 {
            font-weight: 700;
            color: #000000;
            margin-top: 12pt;
            margin-bottom: 6pt;
            text-indent: 0;
            page-break-after: avoid;
        }
        
        h1 { font-size: 14pt; text-align: center; text-transform: uppercase; }
        h2 { font-size: 13pt; }
        h3 { font-size: 12pt; }
        h4 { font-size: 11pt; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8pt 0;
            font-size: 10pt;
        }
        
        table td, table th {
            border: 0.5pt solid #333;
            padding: 4pt 6pt;
            text-align: left;
        }
        
        table th {
            background: #f0f0f0;
            font-weight: 700;
        }
        
        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 8pt auto;
        }
        
        ol, ul {
            padding-left: 20pt;
            margin-bottom: 6pt;
        }
        
        li {
            margin-bottom: 3pt;
        }
        
        sub { font-size: 0.8em; }
        sup { font-size: 0.8em; }
        
        .math-formula {
            display: inline-block;
            vertical-align: middle;
        }
        
        .katex {
            font-size: 1em !important;
        }
        
        .katex-display {
            margin: 8pt 0;
            text-align: center;
        }

        .references-section {
            margin-top: 20pt;
            border-top: 0.5pt solid #ddd;
            padding-top: 10pt;
        }
        .references-title {
            text-align: center;
            font-size: 12pt;
            font-style: italic;
            margin-bottom: 10pt;
            text-transform: none;
        }
        .reference-item {
            text-align: left !important;
            text-indent: 0 !important;
            font-size: 10pt;
            line-height: 1.4;
            margin-bottom: 4pt;
        }
    </style>
</head>
<body>
    ${inputHtml}
</body>
</html>`;

        const browser = await puppeteer.launch({
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-gpu',
                '--disable-dev-shm-usage',
            ]
        });

        const page = await browser.newPage();
        await page.setContent(fullHtml, { waitUntil: 'networkidle0', timeout: 30000 });

        // PDF yaratish
        await page.pdf({
            path: outputPath,
            format: 'A4',
            printBackground: true,
            preferCSSPageSize: true,
        });

        await browser.close();

        console.log(JSON.stringify({ success: true, path: outputPath }));
    } catch (e) {
        console.error(JSON.stringify({ error: e.message }));
        process.exit(1);
    }
}

if (htmlFilePath && fs.existsSync(htmlFilePath)) {
    // Katta hajmdagi HTML (masalan, base64 rasmlar) bo'lsa, fayldan o'qiymiz
    const inputHtml = fs.readFileSync(htmlFilePath, 'utf8');
    generatePdf(inputHtml);
} else {
    // Fallback: stdin dan o'qish
    let inputHtml = '';
    process.stdin.setEncoding('utf8');
    process.stdin.on('data', (chunk) => { inputHtml += chunk; });
    process.stdin.on('end', () => generatePdf(inputHtml));
}
