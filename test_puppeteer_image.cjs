const puppeteer = require('puppeteer');
const fs = require('fs');

async function run() {
    const html = fs.readFileSync('test_image.html', 'utf8');
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();
    
    // Log console messages from the page
    page.on('console', msg => console.log('PAGE LOG:', msg.text()));
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));
    page.on('requestfailed', request => console.log('REQUEST FAILED:', request.url(), request.failure().errorText));

    await page.setContent(html, { waitUntil: 'networkidle0' });
    await page.pdf({ path: 'test_image_direct.pdf', format: 'A4' });
    await browser.close();
    console.log('Done. Check test_image_direct.pdf size.');
}
run();
