// --- LANGUAGE.JS ---

const LANGS       = ['ka', 'en', 'ru'];
const LANG_LABELS = { ka: 'ქართული', en: 'English', ru: 'Русский' };

const GetLanguage = () => localStorage.getItem('ActiveLanguage') || 'ka';

const SetLanguageTo = (lang) => {
    if (!LANGS.includes(lang)) return;
    localStorage.setItem('ActiveLanguage', lang);
    closeLangDropdown();
    ChangeData();
    // Let pages that define their own updater hook in
    if (typeof onLangChange === 'function') onLangChange(lang);
};

// Legacy toggle — keep backward compat
const SetLanguage = () => {
    const cur = GetLanguage();
    const idx = LANGS.indexOf(cur);
    SetLanguageTo(LANGS[(idx + 1) % LANGS.length]);
};

// ── Dropdown ────────────────────────────────────────────────────
function showLangDropdown(btn) {
    const existing = document.getElementById('lang-dropdown-popup');
    if (existing) { existing.remove(); return; }

    const cur = GetLanguage();
    const popup = document.createElement('div');
    popup.id = 'lang-dropdown-popup';
    popup.innerHTML = LANGS.map(l =>
        `<div class="ldp-opt${l === cur ? ' ldp-active' : ''}"
              onclick="SetLanguageTo('${l}');event.stopPropagation()">
            ${LANG_LABELS[l]}
         </div>`
    ).join('');

    const rect = btn.getBoundingClientRect();
    const fromRight = window.innerWidth - rect.right;
    popup.style.cssText =
        `position:fixed;top:${Math.round(rect.bottom + 6)}px;right:${Math.round(fromRight)}px;` +
        `z-index:999999;background:rgba(8,16,10,0.97);` +
        `border:1px solid rgba(34,197,94,0.38);border-radius:10px;overflow:hidden;` +
        `box-shadow:0 10px 28px rgba(0,0,0,0.7);backdrop-filter:blur(14px);min-width:130px;`;

    document.body.appendChild(popup);
    setTimeout(() => document.addEventListener('click', closeLangDropdown, { once: true }), 0);
}

function closeLangDropdown() {
    const p = document.getElementById('lang-dropdown-popup');
    if (p) p.remove();
}

// Inject dropdown styles once
(function () {
    const s = document.createElement('style');
    s.textContent =
        `.ldp-opt{color:#c8c8c8;padding:11px 20px;cursor:pointer;font-size:.92rem;font-weight:600;` +
        `transition:background .15s,color .15s;white-space:nowrap;}` +
        `.ldp-opt:hover{background:rgba(34,197,94,.14);color:#fff;}` +
        `.ldp-active{color:#22c55e;background:rgba(34,197,94,.1);}`;
    document.head.appendChild(s);
})();

// ── Data loading ─────────────────────────────────────────────────
async function getLanguageData() {
    const lang = GetLanguage();
    try {
        const r = await fetch(`/langs/${lang}.json`);
        if (!r.ok) return {};
        return await r.json();
    } catch (e) {
        console.error('Language load failed:', e);
        return {};
    }
}

async function ChangeData() {
    const data = await getLanguageData();
    if (!Object.keys(data).length) return;
    for (const [key, value] of Object.entries(data)) {
        document.querySelectorAll(`[name="${key}"]`).forEach(el => {
            el.innerHTML = key === 'key_motivational' ? `<i>${value}</i>` : '';
            if (key !== 'key_motivational') el.textContent = value;
        });
    }
}

// ── Init ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    ChangeData();

    // Attach dropdown to all lang buttons (works on every page)
    document.querySelectorAll('.navbar-lang-btn, .mobile-lang-fixed, .nm-lang').forEach(btn => {
        btn.style.cursor = 'pointer';
        btn.onclick = (e) => { e.stopPropagation(); showLangDropdown(btn); };
    });
});
