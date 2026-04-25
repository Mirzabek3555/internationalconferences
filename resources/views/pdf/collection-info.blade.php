<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $conference->title }} - Info</title>
    <style>
        @page {
            margin: 25mm 20mm 25mm 35mm;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
            line-height: 1.5;
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .main-title {
            font-size: 16px;
            font-weight: bold;
            color: #17428f;
            text-transform: uppercase;
        }

        .sub-title {
            font-size: 16px;
            font-weight: bold;
            color: #17428f;
            margin-bottom: 5px;
        }

        .separator {
            border-top: 1.5px solid #17428f;
            margin-bottom: 5px;
        }

        .date-text {
            color: #cc0000;
            text-align: center;
            font-size: 15px;
            margin-bottom: 5px;
        }

        .paragraph {
            text-align: justify;
            text-indent: 1.25cm;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .paragraph-bold {
            font-weight: bold;
        }

        .editor-section {
            text-align: center;
            margin-bottom: 20px;
            font-size: 15px;
            line-height: 1.3;
        }

        .editor-title {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .editor-name {
            font-weight: bold;
        }

        .email-link {
            color: #0000EE;
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        $confMainTitle = strtoupper($country->conference_name ?? 'THE LATEST PEDAGOGICAL AND PSYCHOLOGICAL INNOVATIONS IN EDUCATION');
        $confSubTitle = 'International online conference.';
        
        $dateObj = \Carbon\Carbon::parse($conference->conference_date);
        $dateFormatted = $dateObj->format('jS F-Y');
        $dateFormattedNumeric = $dateObj->format('d.m.Y');
        
        $quoteTitle = ucfirst(strtolower($confMainTitle));

        $alpha3map = [
            'UZB' => 'UZ', 'GBR' => 'GB', 'USA' => 'US', 'DEU' => 'DE',
            'FRA' => 'FR', 'ITA' => 'IT', 'ESP' => 'ES', 'RUS' => 'RU',
            'JPN' => 'JP', 'CHN' => 'CN', 'KOR' => 'KR', 'TUR' => 'TR',
            'POL' => 'PL', 'KAZ' => 'KZ', 'IND' => 'IN', 'BRA' => 'BR',
            'CAN' => 'CA', 'TKM' => 'TM', 'AZE' => 'AZ', 'TJK' => 'TJ', 'KGZ' => 'KG',
            'DNK' => 'DK', 'SWE' => 'SE', 'NOR' => 'NO', 'FIN' => 'FI',
            'NLD' => 'NL', 'BEL' => 'BE', 'CHE' => 'CH', 'AUT' => 'AT',
            'PRT' => 'PT', 'GRC' => 'GR', 'SAU' => 'SA', 'ARE' => 'AE',
        ];

        $editorDataList = [
            'UZ' => ['name' => 'Prof. Sherzod Yusupov', 'uni' => 'National University of Uzbekistan', 'city' => 'Tashkent, UZB', 'email' => 'sherzod.yusupov@nuu.uz'],
            'GB' => ['name' => 'Prof. Jonathan Hartley', 'uni' => 'University College London', 'city' => 'London, Great Britain', 'email' => 'j.hartley@ucl.ac.uk'],
            'DE' => ['name' => 'Prof. Klaus Hoffmann', 'uni' => 'Technical University of Munich', 'city' => 'Munich, Germany', 'email' => 'klaus.hoffmann@tum.de'],
            'RU' => ['name' => 'Prof. Alexander Petrov', 'uni' => 'Moscow State University', 'city' => 'Moscow, Russia', 'email' => 'a.petrov@msu.ru'],
            'FR' => ['name' => 'Prof. Jean-Michel Beaumont', 'uni' => 'Sorbonne University', 'city' => 'Paris, France', 'email' => 'jm.beaumont@sorbonne.fr'],
            'TR' => ['name' => 'Prof. Mehmet Yilmaz', 'uni' => 'Istanbul University', 'city' => 'Istanbul, Turkey', 'email' => 'mehmet.yilmaz@istanbul.edu.tr'],
            'JP' => ['name' => 'Prof. Hiroshi Tanaka', 'uni' => 'University of Tokyo', 'city' => 'Tokyo, Japan', 'email' => 'h.tanaka@u-tokyo.ac.jp'],
            'CN' => ['name' => 'Prof. Zhang Wei', 'uni' => 'Tsinghua University', 'city' => 'Beijing, China', 'email' => 'zhang.wei@tsinghua.edu.cn'],
            'US' => ['name' => 'Prof. Robert Williams', 'uni' => 'Harvard University', 'city' => 'Cambridge, USA', 'email' => 'rwilliams@harvard.edu'],
            'KZ' => ['name' => 'Prof. Nursultan Akhmetov', 'uni' => 'Al-Farabi Kazakh National University', 'city' => 'Almaty, Kazakhstan', 'email' => 'n.akhmetov@kaznu.kz'],
            'KR' => ['name' => 'Prof. Kim Junho', 'uni' => 'Seoul National University', 'city' => 'Seoul, South Korea', 'email' => 'junho.kim@snu.ac.kr'],
            'IN' => ['name' => 'Prof. Priya Ramesh', 'uni' => 'Indian Institute of Technology Delhi', 'city' => 'New Delhi, India', 'email' => 'pramesh@iitd.ac.in'],
            'IT' => ['name' => 'Prof. Giovanni Esposito', 'uni' => 'Sapienza University of Rome', 'city' => 'Rome, Italy', 'email' => 'g.esposito@uniroma1.it'],
            'ES' => ['name' => 'Prof. Carlos Fernandez', 'uni' => 'University of Barcelona', 'city' => 'Barcelona, Spain', 'email' => 'carlos.fernandez@ub.edu'],
            'PL' => ['name' => 'Prof. Marek Kowalski', 'uni' => 'University of Warsaw', 'city' => 'Warsaw, Poland', 'email' => 'm.kowalski@uw.edu.pl'],
            'BR' => ['name' => 'Prof. Carlos Oliveira', 'uni' => 'University of São Paulo', 'city' => 'São Paulo, Brazil', 'email' => 'carlos.oliveira@usp.br'],
            'CA' => ['name' => 'Prof. Michael Patterson', 'uni' => 'University of Toronto', 'city' => 'Toronto, Canada', 'email' => 'm.patterson@utoronto.ca'],
            'TM' => ['name' => 'Prof. Berdymurat Atayev', 'uni' => 'Turkmen State University', 'city' => 'Ashgabat, Turkmenistan', 'email' => 'b.atayev@tsu.tm'],
            'AZ' => ['name' => 'Prof. Elchin Mammadov', 'uni' => 'Baku State University', 'city' => 'Baku, Azerbaijan', 'email' => 'e.mammadov@bsu.edu.az'],
            'TJ' => ['name' => 'Prof. Rustam Nazarov', 'uni' => 'Tajik National University', 'city' => 'Dushanbe, Tajikistan', 'email' => 'r.nazarov@tnu.tj'],
            'KG' => ['name' => 'Prof. Bakyt Mamytbekov', 'uni' => 'Kyrgyz National University', 'city' => 'Bishkek, Kyrgyzstan', 'email' => 'b.mamytbekov@knu.kg'],
            'DK' => ['name' => 'Prof. Anders Christensen', 'uni' => 'University of Copenhagen', 'city' => 'Copenhagen, Denmark', 'email' => 'anders.c@ku.dk'],
            'SE' => ['name' => 'Prof. Erik Lindqvist', 'uni' => 'Stockholm University', 'city' => 'Stockholm, Sweden', 'email' => 'erik.lindqvist@su.se'],
            'NO' => ['name' => 'Prof. Lars Andersen', 'uni' => 'University of Oslo', 'city' => 'Oslo, Norway', 'email' => 'lars.andersen@uio.no'],
            'FI' => ['name' => 'Prof. Mikko Korhonen', 'uni' => 'University of Helsinki', 'city' => 'Helsinki, Finland', 'email' => 'mikko.korhonen@helsinki.fi'],
            'NL' => ['name' => 'Prof. Jan van der Berg', 'uni' => 'University of Amsterdam', 'city' => 'Amsterdam, Netherlands', 'email' => 'j.vanderberg@uva.nl'],
            'BE' => ['name' => 'Prof. Pierre Dubois', 'uni' => 'KU Leuven', 'city' => 'Leuven, Belgium', 'email' => 'pierre.dubois@kuleuven.be'],
            'CH' => ['name' => 'Prof. Thomas Müller', 'uni' => 'ETH Zurich', 'city' => 'Zurich, Switzerland', 'email' => 'thomas.muller@ethz.ch'],
            'AT' => ['name' => 'Prof. Wolfgang Bauer', 'uni' => 'University of Vienna', 'city' => 'Vienna, Austria', 'email' => 'wolfgang.bauer@univie.ac.at'],
            'PT' => ['name' => 'Prof. João Ferreira', 'uni' => 'University of Lisbon', 'city' => 'Lisbon, Portugal', 'email' => 'joao.ferreira@ulisboa.pt'],
            'GR' => ['name' => 'Prof. Nikos Papadopoulos', 'uni' => 'National and Kapodistrian University of Athens', 'city' => 'Athens, Greece', 'email' => 'npapadopoulos@uoa.gr'],
            'SA' => ['name' => 'Prof. Abdullah Al-Rashid', 'uni' => 'King Saud University', 'city' => 'Riyadh, Saudi Arabia', 'email' => 'alrashid@ksu.edu.sa'],
            'AE' => ['name' => 'Prof. Mohammed Al-Mansoori', 'uni' => 'United Arab Emirates University', 'city' => 'Al Ain, UAE', 'email' => 'm.almansoori@uaeu.ac.ae'],
        ];

        $countryRawCode = strtoupper($country->code ?? 'GB');
        $editorCode = strlen($countryRawCode) === 3
            ? ($alpha3map[$countryRawCode] ?? strtoupper(substr($countryRawCode, 0, 2)))
            : $countryRawCode;
            
        $editor = $editorDataList[$editorCode] ?? ['name' => 'Prof. Jonathan Hartley', 'uni' => 'University College London', 'city' => 'London, Great Britain', 'email' => 'j.hartley@ucl.ac.uk'];
    @endphp

    <div class="text-center main-title">{{ $confMainTitle }}.</div>
    <div class="text-center sub-title">{{ $confSubTitle }}</div>
    
    <div class="separator"></div>

    <div class="date-text">Date: {{ $dateFormatted }}</div>

    <div class="paragraph">
        “{{ $quoteTitle }}”. Collection of scientific papers on materials of the international scientific-practical conference {{ $dateFormattedNumeric }}, Pub. "ISC", {{ $editor['city'] }}, {{ $totalPages ?? 163 }} p.
    </div>

    <div class="editor-section">
        <div class="editor-title">Editor:</div>
        <div class="editor-name">{{ $editor['name'] }}</div>
        <div>Current ISC Editors</div>
        <div>{{ $editor['uni'] }}</div>
        <div>{{ $editor['city'] }}</div>
        <div style="margin-top: 5px;">
            <span class="editor-name">Email: </span><a href="mailto:{{ $editor['email'] }}" class="email-link">{{ $editor['email'] }}</a>
        </div>
    </div>

    <div class="paragraph">
        The collection of published scientific papers is a scientific and practical publication, which includes scientific articles from students, teachers, candidates of sciences, doctoral students, and independent researchers. The articles contain a study that reflects the processes and changes in the structure of modern science. The collection of scientific articles is intended for students, doctoral students, teachers, researchers, practitioners, and those interested in the development trends of modern science.
    </div>

    <div class="paragraph paragraph-bold">
        All materials contained in the book, published in the author's version. The editors do not make adjustments in scientific articles. Responsibility for the information published in the materials on display, are the authors.
    </div>

    <div class="paragraph">
        The electronic version of the collection is available online scientific publishing center «ISC» Site center: internationalscientificconferences.org
    </div>
</body>
</html>
