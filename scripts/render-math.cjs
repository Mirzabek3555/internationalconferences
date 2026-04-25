/**
 * Matematik formulalarni KaTeX yordamida render qilish
 * 
 * Ishlatish: node scripts/render-math.js
 * Kirish: stdin orqali HTML string
 * Chiqish: stdout orqali rendered HTML
 * 
 * Qo'llab-quvvatlaydi:
 * - MathML (<math>...</math>) → KaTeX HTML
 * - LaTeX inline ($...$) → KaTeX HTML
 * - LaTeX display ($$...$$) → KaTeX HTML
 * - Kimyoviy formulalar (H2O, CO2) → subscript HTML
 */
const katex = require('katex');

// stdin ni o'qish
let inputHtml = '';
process.stdin.setEncoding('utf8');
process.stdin.on('data', (chunk) => { inputHtml += chunk; });
process.stdin.on('end', () => {
    try {
        let html = inputHtml;

        // 1. LaTeX display formulalar: $$...$$
        html = html.replace(/\$\$([\s\S]*?)\$\$/g, (match, latex) => {
            try {
                return katex.renderToString(latex.trim(), {
                    displayMode: true,
                    throwOnError: false,
                    output: 'html',
                    trust: true
                });
            } catch (e) {
                return `<span class="math-error" title="${e.message}">${match}</span>`;
            }
        });

        // 2. LaTeX inline formulalar: $...$
        html = html.replace(/\$([^\$\n]+?)\$/g, (match, latex) => {
            try {
                return katex.renderToString(latex.trim(), {
                    displayMode: false,
                    throwOnError: false,
                    output: 'html',
                    trust: true
                });
            } catch (e) {
                return `<span class="math-error" title="${e.message}">${match}</span>`;
            }
        });

        // 3. MathML → KaTeX (agar mavjud bo'lsa)
        html = html.replace(/<math[^>]*>([\s\S]*?)<\/math>/gi, (match) => {
            try {
                return katex.renderToString(match, {
                    displayMode: false,
                    throwOnError: false,
                    output: 'html',
                    trust: true
                });
            } catch (e) {
                // Agar KaTeX MathML ni parse qila olmasa, asl holida qoldirish
                return match;
            }
        });

        // 4. Kimyoviy formulalar: H2O, CO2, NaCl, C6H12O6
        // Element + raqam formatini topish va subscript qilish
        html = html.replace(/\b([A-Z][a-z]?)(\d+)(?=[A-Z\s,\.\)\(<]|$)/g, (match, element, num) => {
            return `${element}<sub>${num}</sub>`;
        });

        // 5. Daraja belgilari: x^2, E^n kabi
        html = html.replace(/(\w)\^(\d+)/g, (match, base, exp) => {
            return `${base}<sup>${exp}</sup>`;
        });

        // 6. Subscript belgilari: x_1, a_n kabi
        html = html.replace(/(\w)_(\d+)/g, (match, base, sub) => {
            return `${base}<sub>${sub}</sub>`;
        });

        console.log(html);
    } catch (e) {
        console.error(JSON.stringify({ error: e.message }));
        process.exit(1);
    }
});
