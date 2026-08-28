<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');

$baseUrl = 'https://relmusic.github.io';
$jsonFile = __DIR__ . '/index.json';

$pages = [];

if (is_file($jsonFile)) {
    $json = file_get_contents($jsonFile);
    $data = json_decode($json, true);

    if (
        is_array($data) &&
        isset($data['pages']) &&
        is_array($data['pages'])
    ) {
        foreach ($data['pages'] as $url) {
            if (is_string($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                $pages[] = $url;
            }
        }
    }
}

/*
 * Hilangkan duplikat URL
 */
$pages = array_values(array_unique($pages));

/*
 * Kelompokkan berdasarkan folder/path
 */
$groups = [];

foreach ($pages as $url) {
    $path = parse_url($url, PHP_URL_PATH) ?: '/';

    $segments = array_values(
        array_filter(explode('/', trim($path, '/')))
    );

    if (empty($segments)) {
        $group = 'Home & Legal';
    } else {
        $first = strtolower($segments[0]);

        $groupNames = [
            '3d'            => '3D',
            'ai'            => 'AI',
            'audio'         => 'Audio',
            'blog'          => 'Blog',
            'calendar'      => 'Calendar',
            'fb'            => 'Social & Facebook',
            'ip'            => 'Internet & IP',
            'keyboard-jawa' => 'Keyboard Jawa',
            'live'          => 'Live',
            'macapat'       => 'Macapat',
            'maps'          => 'Maps',
            'mp3'            => 'MP3',
            'music'          => 'Music',
            'quran'          => 'Quran',
            'quranclip'      => 'Quran Clip',
            'search'        => 'Search',
            'social'        => 'Social',
            'tiktok'        => 'TikTok',
            'tts'           => 'Text to Speech',
            'vidio'         => 'Vidio',
            'visualizer'    => 'Visualizer',
            'voice'         => 'Voice',
            'wa'            => 'WhatsApp',
            'wayang'        => 'Wayang',
            'youtube'       => 'YouTube'
        ];

        $group = $groupNames[$first] ?? ucfirst($first);
    }

    $groups[$group][] = $url;
}

/*
 * Urutan kategori
 */
ksort($groups);

/*
 * Home & Legal selalu di atas
 */
if (isset($groups['Home & Legal'])) {
    $home = $groups['Home & Legal'];
    unset($groups['Home & Legal']);

    $groups = [
        'Home & Legal' => $home
    ] + $groups;
}

$total = count($pages);
?>
<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Sitemap - RelMusic</title>

    <meta
        name="description"
        content="Sitemap lengkap RelMusic berisi seluruh halaman, tools, aplikasi, blog, musik, Quran, sosial, dan layanan."
    >

    <meta
        name="robots"
        content="index, follow"
    >

    <style>

        :root {
            --bg: #f5f7fb;
            --card: #ffffff;
            --text: #172033;
            --muted: #667085;
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --border: #e5e7eb;
            --hover: #eef2ff;
            --shadow:
                0 8px 30px rgba(0, 0, 0, .06);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family:
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Roboto,
                Arial,
                sans-serif;
            line-height: 1.6;
        }

        a {
            color: var(--primary);
            text-decoration: none;
        }

        a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .container {
            width: min(
                1200px,
                calc(100% - 32px)
            );
            margin: 0 auto;
        }

        header {
            padding: 60px 0 50px;
            color: #fff;

            background:
                linear-gradient(
                    135deg,
                    #312e81,
                    #4f46e5,
                    #7c3aed
                );
        }

        header h1 {
            margin: 0 0 8px;

            font-size:
                clamp(2rem, 6vw, 3.5rem);

            line-height: 1.1;
        }

        header p {
            max-width: 750px;
            margin: 0;

            color:
                rgba(255, 255, 255, .86);
        }

        .toolbar {
            position: relative;
            z-index: 10;
            margin-top: -25px;
        }

        .toolbar-box {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;

            padding: 16px;

            background: var(--card);

            border:
                1px solid var(--border);

            border-radius: 16px;

            box-shadow: var(--shadow);
        }

        #search {
            flex: 1 1 300px;

            width: 100%;

            padding: 14px 16px;

            border:
                1px solid var(--border);

            border-radius: 10px;

            outline: none;

            font-size: 15px;
        }

        #search:focus {
            border-color: var(--primary);

            box-shadow:
                0 0 0 3px
                rgba(79, 70, 229, .12);
        }

        #counter {
            padding: 0 8px;

            color: var(--muted);

            font-size: 14px;

            white-space: nowrap;
        }

        main {
            padding: 40px 0 70px;
        }

        .section {
            margin-bottom: 22px;

            padding: 24px;

            background: var(--card);

            border:
                1px solid var(--border);

            border-radius: 16px;

            box-shadow: var(--shadow);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;

            margin: 0 0 20px;

            font-size: 1.35rem;
        }

        .section-title::before {
            content: "";

            width: 5px;
            height: 25px;

            border-radius: 5px;

            background: var(--primary);
        }

        .links {
            display: grid;

            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(280px, 1fr)
                );

            gap: 8px 20px;

            padding: 0;
            margin: 0;

            list-style: none;
        }

        .links li {
            min-width: 0;
        }

        .links a {
            display: block;

            padding: 10px 12px;

            border-radius: 9px;

            overflow-wrap: anywhere;

            transition:
                background .15s ease;
        }

        .links a:hover {
            background: var(--hover);

            text-decoration: none;
        }

        .links a::before {
            content: "→ ";

            opacity: .5;
        }

        .empty {
            display: none;

            padding: 45px 20px;

            color: var(--muted);

            text-align: center;

            background: var(--card);

            border:
                1px solid var(--border);

            border-radius: 16px;
        }

        footer {
            padding: 30px 0;

            border-top:
                1px solid var(--border);

            color: var(--muted);

            text-align: center;

            font-size: 14px;
        }

        .top {
            position: fixed;

            right: 18px;
            bottom: 18px;

            display: grid;

            width: 45px;
            height: 45px;

            place-items: center;

            color: #fff;

            background: var(--primary);

            border-radius: 50%;

            box-shadow:
                0 5px 20px
                rgba(79, 70, 229, .3);
        }

        .top:hover {
            color: #fff;

            background:
                var(--primary-dark);

            text-decoration: none;
        }

        code {
            padding: 2px 5px;

            background: #f2f4f7;

            border-radius: 5px;
        }

        @media (max-width: 700px) {

            .container {
                width:
                    calc(100% - 20px);
            }

            header {
                padding:
                    42px 0 38px;
            }

            .toolbar-box {
                padding: 12px;
            }

            main {
                padding-top: 25px;
            }

            .section {
                padding: 18px;
            }

            .links {
                grid-template-columns: 1fr;
            }

            #counter {
                width: 100%;
            }
        }

    </style>

</head>

<body id="top">

<header>

    <div class="container">

        <h1>RelMusic Sitemap</h1>

        <p>
            Daftar lengkap halaman, aplikasi,
            tools, blog, musik, Quran,
            sosial, dan layanan RelMusic.
        </p>

    </div>

</header>

<div class="toolbar">

    <div class="container">

        <div class="toolbar-box">

            <input
                type="search"
                id="search"
                placeholder="Cari halaman atau URL..."
                aria-label="Cari sitemap"
                autocomplete="off"
            >

            <span id="counter">
                <?= $total ?> halaman
            </span>

        </div>

    </div>

</div>

<main>

    <div class="container">

        <?php if ($total > 0): ?>

            <div id="sitemap">

                <?php foreach ($groups as $group => $urls): ?>

                    <section
                        class="section"
                        data-section
                    >

                        <h2 class="section-title">
                            <?= htmlspecialchars(
                                $group,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </h2>

                        <ul class="links">

                            <?php foreach ($urls as $url): ?>

                                <?php
                                $safeUrl =
                                    htmlspecialchars(
                                        $url,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                $path =
                                    parse_url(
                                        $url,
                                        PHP_URL_PATH
                                    ) ?: '/';

                                $safePath =
                                    htmlspecialchars(
                                        $path,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                ?>

                                <li data-item>

                                    <a
                                        href="<?= $safeUrl ?>"
                                        title="<?= $safeUrl ?>"
                                    >
                                        <?= $safePath ?>
                                    </a>

                                </li>

                            <?php endforeach; ?>

                        </ul>

                    </section>

                <?php endforeach; ?>

            </div>

            <div
                class="empty"
                id="noResult"
            >
                Tidak ada halaman yang cocok
                dengan pencarian.
            </div>

        <?php else: ?>

            <div class="empty" style="display:block">

                <strong>
                    Sitemap belum tersedia.
                </strong>

                <p>
                    Pastikan file
                    <code>index.json</code>
                    tersedia di folder yang sama
                    dengan <code>sitemap.php</code>.
                </p>

            </div>

        <?php endif; ?>

    </div>

</main>

<footer>

    <div class="container">

        © <?= date('Y') ?> RelMusic

        ·

        <a href="<?= $baseUrl ?>/">
            Home
        </a>

        ·

        <a href="<?= $baseUrl ?>/sitemap.html">
            HTML Sitemap
        </a>

    </div>

</footer>

<a
    class="top"
    href="#top"
    aria-label="Kembali ke atas"
>
    ↑
</a>

<script>

const search =
    document.getElementById('search');

const sections =
    document.querySelectorAll('[data-section]');

const counter =
    document.getElementById('counter');

const noResult =
    document.getElementById('noResult');

function filterSitemap() {

    const query =
        search.value
            .toLowerCase()
            .trim();

    let visible = 0;

    sections.forEach(section => {

        const items =
            section.querySelectorAll('[data-item]');

        let sectionVisible = 0;

        items.forEach(item => {

            const text =
                item.textContent
                    .toLowerCase();

            const link =
                item.querySelector('a');

            const url =
                link
                    ? link.href.toLowerCase()
                    : '';

            const match =
                !query ||
                text.includes(query) ||
                url.includes(query);

            item.style.display =
                match ? '' : 'none';

            if (match) {
                sectionVisible++;
                visible++;
            }

        });

        section.style.display =
            sectionVisible > 0
                ? ''
                : 'none';

    });

    counter.textContent =
        `${visible} halaman`;

    if (noResult) {
        noResult.style.display =
            visible === 0
                ? 'block'
                : 'none';
    }

}

if (search) {
    search.addEventListener(
        'input',
        filterSitemap
    );
}

</script>

</body>
</html>
