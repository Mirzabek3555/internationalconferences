<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Certificate;
use App\Models\Conference;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Sertifikat PDF yaratish - professional dizayn, davlat ranglari bilan
     * A4 landscape, vektor elementlar, QR kod, optimallashtirilgan hajm (< 1MB)
     */
    public function generateCertificate(Article $article, Certificate $certificate): string
    {
        $article->load(['conference.country']);
        $country = $article->conference->country;

        // Davlat ranglarini olish
        $colors = $this->getCountryColors($country->code ?? 'GB');

        $pdf = Pdf::loadView('pdf.certificate-professional', [
            'article' => $article,
            'conference' => $article->conference,
            'country' => $country,
            'certificate' => $certificate,
            'colors' => $colors,
        ]);

        // A4 landscape
        $pdf->setPaper('A4', 'landscape');

        // PDF ishlab chiqarish sozlamalari
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('dpi', 150); // Optimallashtirilgan DPI (sifat va hajm balansda)
        $pdf->setOption('isFontSubsettingEnabled', true); // Font subsetting - hajmni kamaytirish
        $pdf->setOption('isPhpEnabled', true);

        $filename = 'certificate_' . $certificate->certificate_number . '.pdf';
        $path = 'certificates/' . $filename;

        // Papkani yaratish
        $directory = Storage::disk('public')->path('certificates');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Davlat ranglari - bayroq asosida
     * primary:   asosiy dominant rang
     * secondary: ikkinchi rang
     * accent:    aksent rang (oltin/kumush/kontrast)
     *
     * ISO 3166-1 alpha-2 va alpha-3 kodlar qo'llab-quvvatlanadi
     */
    private function getCountryColors(string $code): array
    {
        // 3 harfli kodlarni 2 harfliga o'girish
        $alpha3to2 = [
            'UZB' => 'UZ',
            'GBR' => 'GB',
            'USA' => 'US',
            'DEU' => 'DE',
            'FRA' => 'FR',
            'ITA' => 'IT',
            'ESP' => 'ES',
            'RUS' => 'RU',
            'JPN' => 'JP',
            'CHN' => 'CN',
            'KOR' => 'KR',
            'TUR' => 'TR',
            'POL' => 'PL',
            'KAZ' => 'KZ',
            'IND' => 'IN',
            'BRA' => 'BR',
            'CAN' => 'CA',
            'TKM' => 'TM',
            'AUS' => 'AU',
            'NLD' => 'NL',
            'SWE' => 'SE',
            'CHE' => 'CH',
            'AUT' => 'AT',
            'BEL' => 'BE',
            'PRT' => 'PT',
            'GRC' => 'GR',
            'SAU' => 'SA',
            'ARE' => 'AE',
            'MYS' => 'MY',
            'SGP' => 'SG',
            'THA' => 'TH',
            'VNM' => 'VN',
            'IDN' => 'ID',
            'PHL' => 'PH',
            'PAK' => 'PK',
            'BGD' => 'BD',
            'EGY' => 'EG',
            'NGA' => 'NG',
            'ZAF' => 'ZA',
            'MEX' => 'MX',
            'ARG' => 'AR',
            'COL' => 'CO',
            'CHL' => 'CL',
            'PER' => 'PE',
            'NZL' => 'NZ',
            'IRL' => 'IE',
            'ISR' => 'IL',
            'AZE' => 'AZ',
            'TJK' => 'TJ',
            'KGZ' => 'KG',
            'AFG' => 'AF',
            'IRN' => 'IR',
        ];

        $normalizedCode = strlen($code) === 3
            ? ($alpha3to2[strtoupper($code)] ?? strtoupper(substr($code, 0, 2)))
            : strtoupper($code);

        $colors = [
            // O'zbekiston — moviy-oq-yashil, oltin aksent
            'UZ' => ['primary' => '#0099b5', 'secondary' => '#1eb53a', 'accent' => '#c9a227'],

            // Turkiya — qizil-oq, oltin aksent
            'TR' => ['primary' => '#e30a17', 'secondary' => '#1a1a2e', 'accent' => '#c9a227'],

            // Qozog'iston — ko'k va oltin
            'KZ' => ['primary' => '#00afca', 'secondary' => '#ffc61e', 'accent' => '#006994'],

            // AQSh — ko'k, qizil, oq
            'US' => ['primary' => '#3c3b6e', 'secondary' => '#b22234', 'accent' => '#c9a227'],

            // Buyuk Britaniya
            'GB' => ['primary' => '#012169', 'secondary' => '#c8102e', 'accent' => '#c9a227'],

            // Germaniya
            'DE' => ['primary' => '#000000', 'secondary' => '#dd0000', 'accent' => '#ffcc00'],

            // Fransiya
            'FR' => ['primary' => '#0055a4', 'secondary' => '#ef4135', 'accent' => '#c9a227'],

            // Italiya
            'IT' => ['primary' => '#009246', 'secondary' => '#cd212a', 'accent' => '#c9a227'],

            // Ispaniya
            'ES' => ['primary' => '#c60b1e', 'secondary' => '#ffc400', 'accent' => '#8b4513'],

            // Rossiya
            'RU' => ['primary' => '#0039a6', 'secondary' => '#d52b1e', 'accent' => '#c9a227'],

            // Yaponiya
            'JP' => ['primary' => '#bc002d', 'secondary' => '#1a1a2e', 'accent' => '#c9a227'],

            // Xitoy
            'CN' => ['primary' => '#de2910', 'secondary' => '#ffde00', 'accent' => '#8b0000'],

            // Janubiy Koreya
            'KR' => ['primary' => '#0047a0', 'secondary' => '#cd2e3a', 'accent' => '#1a1a1a'],

            // Polsha
            'PL' => ['primary' => '#dc143c', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Hindiston
            'IN' => ['primary' => '#ff9933', 'secondary' => '#138808', 'accent' => '#000080'],

            // Braziliya
            'BR' => ['primary' => '#009c3b', 'secondary' => '#ffdf00', 'accent' => '#002776'],

            // Kanada
            'CA' => ['primary' => '#ff0000', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Turkmaniston
            'TM' => ['primary' => '#00843d', 'secondary' => '#d22630', 'accent' => '#c9a227'],

            // Avstraliya
            'AU' => ['primary' => '#00008b', 'secondary' => '#ff0000', 'accent' => '#c9a227'],

            // Niderlandiya
            'NL' => ['primary' => '#ae1c28', 'secondary' => '#21468b', 'accent' => '#f47920'],

            // Shvetsiya
            'SE' => ['primary' => '#006aa7', 'secondary' => '#fecc00', 'accent' => '#1a1a2e'],

            // Shveytsariya
            'CH' => ['primary' => '#ff0000', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Avstriya
            'AT' => ['primary' => '#ed2939', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Belgiya
            'BE' => ['primary' => '#000000', 'secondary' => '#fdda24', 'accent' => '#ef3340'],

            // Portugaliya
            'PT' => ['primary' => '#006600', 'secondary' => '#ff0000', 'accent' => '#ffcc00'],

            // Gretsiya
            'GR' => ['primary' => '#0d5eaf', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // Saudiya Arabistoni
            'SA' => ['primary' => '#006c35', 'secondary' => '#ffffff', 'accent' => '#c9a227'],

            // BAA
            'AE' => ['primary' => '#00732f', 'secondary' => '#ff0000', 'accent' => '#000000'],

            // Ozarbayjon
            'AZ' => ['primary' => '#0092bc', 'secondary' => '#e4002b', 'accent' => '#00af66'],

            // Tojikiston
            'TJ' => ['primary' => '#cc0000', 'secondary' => '#006600', 'accent' => '#ffffff'],

            // Qirg'iziston
            'KG' => ['primary' => '#e8112d', 'secondary' => '#ffcc00', 'accent' => '#e8112d'],
        ];

        return $colors[$normalizedCode] ?? ['primary' => '#1a5276', 'secondary' => '#2980b9', 'accent' => '#c9a227'];
    }

    /**
     * Oylik to'plam PDF yaratish (mundarija uchun)
     */
    public function generateMonthlyCollection(Conference $conference): string
    {
        $conference->load(['country', 'articles.author']);

        $pdf = Pdf::loadView('pdf.collection', [
            'conference' => $conference,
            'country' => $conference->country,
            'articles' => $conference->articles()->published()->get(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'collection_' . $conference->country->code . '_' . $conference->month_year . '.pdf';
        $path = 'collections/' . $filename;

        // Papkani yaratish
        $directory = Storage::disk('public')->path('collections');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
