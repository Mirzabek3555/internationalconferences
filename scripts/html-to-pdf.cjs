const puppeteer = require('puppeteer');

const outputPath = process.argv[2];
const topM = process.argv[3] || '0';
const bottomM = process.argv[4] || '0';
const leftM = process.argv[5] || '0';
const rightM = process.argv[6] || '0';

if (!outputPath) {
    console.error(JSON.stringify({ error: 'Output yo\'li berilmadi' }));
    process.exit(1);
}

let fullHtml = '';
process.stdin.setEncoding('utf8');
process.stdin.on('data', (chunk) => { fullHtml += chunk; });
process.stdin.on('end', async () => {
    try {
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

        await page.pdf({
            path: outputPath,
            format: 'A4',
            printBackground: true,
            margin: {
                top: topM,
                bottom: bottomM,
                left: leftM,
                right: rightM
            }
        });

        await browser.close();
        console.log(JSON.stringify({ success: true, path: outputPath }));
    } catch (e) {
        const fs = require('fs');
        fs.appendFileSync('C:/Users/Mirzabek/puppeteer_error.log', e.message + "\n");
        console.error(JSON.stringify({ error: e.message }));
        process.exit(1);
    }
});
