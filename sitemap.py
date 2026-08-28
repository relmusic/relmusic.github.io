#!/usr/bin/env python3

import json
from pathlib import Path
from html import escape
from urllib.parse import urlparse


BASE_DIR = Path(__file__).resolve().parent
JSON_FILE = BASE_DIR / "index.json"
OUTPUT_FILE = BASE_DIR / "sitemap.html"


GROUPS = {
    "3d": "3D",
    "ai": "AI",
    "audio": "Audio",
    "blog": "Blog",
    "calendar": "Calendar",
    "fb": "Social & Facebook",
    "ip": "Internet & IP",
    "keyboard-jawa": "Keyboard Jawa",
    "live": "Live",
    "macapat": "Macapat",
    "maps": "Maps",
    "mp3": "MP3",
    "music": "Music",
    "quran": "Quran",
    "quranclip": "Quran Clip",
    "search": "Search",
    "social": "Social",
    "tiktok": "TikTok",
    "tts": "Text to Speech",
    "vidio": "Vidio",
    "visualizer": "Visualizer",
    "voice": "Voice",
    "wa": "WhatsApp",
    "wayang": "Wayang",
    "youtube": "YouTube",
}


def load_urls():
    """Membaca URL dari index.json."""

    if not JSON_FILE.exists():
        raise FileNotFoundError(
            f"File tidak ditemukan: {JSON_FILE}"
        )

    with JSON_FILE.open(
        "r",
        encoding="utf-8"
    ) as file:
        data = json.load(file)

    pages = data.get("pages", [])

    if not isinstance(pages, list):
        raise ValueError(
            "Field 'pages' harus berupa array/list."
        )

    # Hapus duplikat sambil mempertahankan urutan
    return list(dict.fromkeys(
        url for url in pages
        if isinstance(url, str)
    ))


def get_group(url):
    """Menentukan kategori berdasarkan path URL."""

    path = urlparse(url).path.strip("/")
    parts = path.split("/")

    if not path:
        return "Home & Legal"

    first = parts[0].lower()

    return GROUPS.get(
        first,
        first.replace("-", " ").title()
    )


def get_path(url):
    """Mengambil path URL untuk tampilan."""

    path = urlparse(url).path

    return path or "/"


def build_groups(urls):
    """Mengelompokkan URL berdasarkan kategori."""

    groups = {}

    for url in urls:
        group = get_group(url)

        groups.setdefault(
            group,
            []
        ).append(url)

    # Home & Legal selalu pertama
    ordered = {}

    if "Home & Legal" in groups:
        ordered["Home & Legal"] = groups.pop(
            "Home & Legal"
        )

    for name in sorted(groups):
        ordered[name] = groups[name]

    return ordered


HTML_TEMPLATE = """<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>RelMusic Sitemap</title>

<meta
    name="description"
    content="Sitemap lengkap RelMusic."
>

<meta
    name="robots"
    content="index, follow"
>

<style>

:root {{
    --bg: #f5f7fb;
    --card: #ffffff;
    --text: #172033;
    --muted: #667085;
    --primary: #4f46e5;
    --primary-dark: #3730a3;
    --border: #e5e7eb;
    --hover: #eef2ff;
}}

* {{
    box-sizing: border-box;
}}

html {{
    scroll-behavior: smooth;
}}

body {{
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
}}

a {{
    color: var(--primary);
    text-decoration: none;
}}

a:hover {{
    color: var(--primary-dark);
    text-decoration: underline;
}}

.container {{
    width: min(
        1200px,
        calc(100% - 32px)
    );

    margin: auto;
}}

header {{
    padding: 60px 0 50px;

    color: white;

    background:
        linear-gradient(
            135deg,
            #312e81,
            #4f46e5,
            #7c3aed
        );
}}

header h1 {{
    margin: 0 0 8px;

    font-size:
        clamp(2rem, 6vw, 3.5rem);

    line-height: 1.1;
}}

header p {{
    margin: 0;

    max-width: 760px;

    color:
        rgba(255,255,255,.85);
}}

.toolbar {{
    position: relative;

    z-index: 5;

    margin-top: -25px;
}}

.toolbar-box {{
    display: flex;

    gap: 12px;

    align-items: center;

    flex-wrap: wrap;

    padding: 16px;

    background: var(--card);

    border:
        1px solid var(--border);

    border-radius: 16px;

    box-shadow:
        0 8px 30px
        rgba(0,0,0,.07);
}}

#search {{
    flex: 1 1 300px;

    min-width: 0;

    padding: 14px 16px;

    border:
        1px solid var(--border);

    border-radius: 10px;

    outline: none;

    font-size: 15px;
}}

#search:focus {{
    border-color: var(--primary);

    box-shadow:
        0 0 0 3px
        rgba(79,70,229,.12);
}}

#counter {{
    color: var(--muted);

    font-size: 14px;

    white-space: nowrap;
}}

main {{
    padding: 40px 0 70px;
}}

.section {{
    margin-bottom: 22px;

    padding: 24px;

    background: var(--card);

    border:
        1px solid var(--border);

    border-radius: 16px;

    box-shadow:
        0 8px 30px
        rgba(0,0,0,.05);
}}

.section h2 {{
    display: flex;

    align-items: center;

    gap: 10px;

    margin: 0 0 20px;

    font-size: 1.35rem;
}}

.section h2::before {{
    content: "";

    width: 5px;
    height: 25px;

    border-radius: 5px;

    background: var(--primary);
}}

.links {{
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
}}

.links a {{
    display: block;

    padding: 10px 12px;

    border-radius: 9px;

    overflow-wrap: anywhere;
}}

.links a:hover {{
    background: var(--hover);

    text-decoration: none;
}}

.links a::before {{
    content: "→ ";

    opacity: .5;
}}

.empty {{
    display: none;

    padding: 45px 20px;

    color: var(--muted);

    text-align: center;

    background: var(--card);

    border:
        1px solid var(--border);

    border-radius: 16px;
}}

footer {{
    padding: 30px 0;

    border-top:
        1px solid var(--border);

    color: var(--muted);

    text-align: center;

    font-size: 14px;
}}

.top {{
    position: fixed;

    right: 18px;
    bottom: 18px;

    display: grid;

    width: 45px;
    height: 45px;

    place-items: center;

    color: white;

    background: var(--primary);

    border-radius: 50%;

    box-shadow:
        0 5px 20px
        rgba(79,70,229,.3);
}}

.top:hover {{
    color: white;

    background: var(--primary-dark);

    text-decoration: none;
}}

@media (max-width: 700px) {{

    .container {{
        width:
            calc(100% - 20px);
    }}

    header {{
        padding: 42px 0 38px;
    }}

    .toolbar-box {{
        padding: 12px;
    }}

    #counter {{
        width: 100%;
    }}

    main {{
        padding-top: 25px;
    }}

    .section {{
        padding: 18px;
    }}

    .links {{
        grid-template-columns: 1fr;
    }}

}}

</style>

</head>

<body id="top">

<header>

<div class="container">

<h1>RelMusic Sitemap</h1>

<p>
Daftar lengkap halaman, aplikasi, tools,
blog, musik, Quran, sosial,
dan layanan RelMusic.
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
{total} halaman
</span>

</div>

</div>

</div>

<main>

<div class="container">

<div id="sitemap">

{sections}

</div>

<div
    class="empty"
    id="noResult"
>
Tidak ada halaman yang cocok
dengan pencarian.
</div>

</div>

</main>

<footer>

<div class="container">

© {year} RelMusic

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
    document.getElementById("search");

const sections =
    document.querySelectorAll(".section");

const counter =
    document.getElementById("counter");

const noResult =
    document.getElementById("noResult");

function filterSitemap() {{

    const query =
        search.value
            .toLowerCase()
            .trim();

    let visible = 0;

    sections.forEach(section => {{

        const items =
            section.querySelectorAll("li");

        let sectionVisible = 0;

        items.forEach(item => {{

            const text =
                item.textContent
                    .toLowerCase();

            const link =
                item.querySelector("a");

            const url =
                link
                    ? link.href.toLowerCase()
                    : "";

            const match =
                !query ||
                text.includes(query) ||
                url.includes(query);

            item.style.display =
                match ? "" : "none";

            if (match) {{
                sectionVisible++;
                visible++;
            }}

        }});

        section.style.display =
            sectionVisible
                ? ""
                : "none";

    }});

    counter.textContent =
        visible + " halaman";

    noResult.style.display =
        visible
            ? "none"
            : "block";
}}

search.addEventListener(
    "input",
    filterSitemap
);

</script>

</body>
</html>
"""


def make_sections(groups):
    """Membuat HTML setiap kategori."""

    sections = []

    for group, urls in groups.items():

        items = []

        for url in urls:

            safe_url = escape(
                url,
                quote=True
            )

            path = escape(
                get_path(url),
                quote=True
            )

            items.append(
                f"""
                <li>
                    <a
                        href="{safe_url}"
                        title="{safe_url}"
                    >
                        {path}
                    </a>
                </li>
                """
            )

        sections.append(
            f"""
            <section class="section">

                <h2>
                    {escape(group)}
                </h2>

                <ul class="links">
                    {''.join(items)}
                </ul>

            </section>
            """
        )

    return "\n".join(sections)


def generate():
    """Generate sitemap.html."""

    urls = load_urls()

    groups = build_groups(urls)

    sections = make_sections(groups)

    html = HTML_TEMPLATE.format(
        total=len(urls),
        sections=sections,
        year=2026
    )

    OUTPUT_FILE.write_text(
        html,
        encoding="utf-8"
    )

    print(
        f"✓ Sitemap berhasil dibuat: "
        f"{OUTPUT_FILE}"
    )

    print(
        f"✓ Total URL: {len(urls)}"
    )

    print(
        f"✓ Total kategori: {len(groups)}"
    )


if __name__ == "__main__":
    generate()
