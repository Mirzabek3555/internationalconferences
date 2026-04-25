/**
 * DOCX → HTML konverter (mammoth.js yordamida)
 * 
 * Ishlatish: node scripts/convert-docx.js <docx_fayl_yo'li>
 * Chiqish: JSON { html, messages }
 * 
 * Embedded va Linked rasmlarni ham to'g'ri ko'rsatadi.
 */
const mammoth = require('mammoth');
const fs = require('fs');
const path = require('path');

const docxPath = process.argv[2];

if (!docxPath || !fs.existsSync(docxPath)) {
    console.error(JSON.stringify({ error: 'DOCX fayl topilmadi: ' + (docxPath || 'yo\'l ko\'rsatilmagan') }));
    process.exit(1);
}

/**
 * Rasm fayl yo'lini normallashtirish
 * file:///C:/... → C:\...
 * C:/... → C:\...
 */
function normalizeImagePath(src) {
    if (!src) return null;
    // file:/// protokolini olib tashlash
    let filePath = src.replace(/^file:\/\/\//i, '');
    // URL decode
    try { filePath = decodeURIComponent(filePath); } catch(e) {}
    // Windows yo'lida / → \
    filePath = filePath.replace(/\//g, path.sep);
    return filePath;
}

/**
 * Rasm fayl kengaytmasiga qarab MIME type aniqlash
 */
function getMimeType(filePath) {
    const ext = path.extname(filePath).toLowerCase().replace('.', '');
    const mimeMap = {
        'jpg': 'image/jpeg', 'jpeg': 'image/jpeg',
        'png': 'image/png', 'gif': 'image/gif',
        'bmp': 'image/bmp', 'webp': 'image/webp',
        'svg': 'image/svg+xml', 'tiff': 'image/tiff', 'tif': 'image/tiff',
    };
    return mimeMap[ext] || 'image/jpeg';
}

// Mammoth options — Word formatlarini HTML teglariga moslashtirish
const options = {
    styleMap: [
        // Sarlavhalar
        "p[style-name='Heading 1'] => h2:fresh",
        "p[style-name='Heading 2'] => h3:fresh",
        "p[style-name='Heading 3'] => h4:fresh",
        // Maxsus stillar
        "p[style-name='Title'] => h1:fresh",
        "p[style-name='Subtitle'] => h2.subtitle:fresh",
        // Bold/Italic
        "b => strong",
        "i => em",
        // Subscript/Superscript
        "verticalAlignment[value='subscript'] => sub",
        "verticalAlignment[value='superscript'] => sup",
    ],

    /**
     * Embedded rasmlarni base64 ga o'girish
     * (Mammoth DOCX ichidagi media/image*.xxx fayllarni o'qiydi)
     */
    convertImage: mammoth.images.inline(function(element) {
        return element.read("base64").then(function(imageBuffer) {
            return {
                src: "data:" + element.contentType + ";base64," + imageBuffer
            };
        });
    }),
};

mammoth.convertToHtml({ path: docxPath }, options)
    .then(function(result) {
        let html = result.value;

        // --- POST-PROCESSING: Linked rasmlarni base64 ga o'girish ---
        // Mammoth linked rasmlarni src="fayl_yo'li" ko'rinishida chiqaradi
        // Biz ularni base64 ga o'giramiz
        html = html.replace(/<img([^>]*)src="([^"]+)"([^>]*)>/gi, function(match, before, src, after) {
            // Agar allaqachon base64 yoki http bo'lsa — o'zgartirmaylik
            if (src.startsWith('data:') || src.startsWith('http://') || src.startsWith('https://')) {
                return match;
            }

            // Fayl yo'lini normallashtirish
            const filePath = normalizeImagePath(src);
            
            if (!filePath || !fs.existsSync(filePath)) {
                // Rasm topilmadi — transparent 1x1 placeholder
                process.stderr.write('Linked rasm topilmadi: ' + (filePath || src) + '\n');
                // Rasim o'rniga placeholder sifatida xabar chiqaramiz
                return `<figure style="border:1px dashed #ccc;padding:8px;text-align:center;color:#999;font-size:10pt;min-height:40px;">
                    [Rasm: ${path.basename(filePath || src)}]
                </figure>`;
            }

            try {
                const imgData = fs.readFileSync(filePath);
                const base64 = imgData.toString('base64');
                const mime = getMimeType(filePath);
                process.stderr.write('Linked rasm base64 ga aylantildi: ' + path.basename(filePath) + '\n');
                return `<img${before}src="data:${mime};base64,${base64}"${after}>`;
            } catch(e) {
                process.stderr.write('Rasm o\'qishda xato: ' + e.message + '\n');
                return match;
            }
        });

        // Konvertatsiya xabarlari (ogohlantirishlar)
        const messages = result.messages.map(m => ({
            type: m.type,
            message: m.message
        }));

        console.log(JSON.stringify({
            html: html,
            messages: messages
        }));
    })
    .catch(function(err) {
        console.error(JSON.stringify({ error: err.message }));
        process.exit(1);
    });
