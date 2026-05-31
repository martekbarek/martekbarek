/* Garaż Konfigurator — vanilla JS, IIFE */
(function (window, document, Config) {
  'use strict';

  if (!Config) return;

  /* ------------------------------------------------------------------ */
  /* STATE                                                                */
  /* ------------------------------------------------------------------ */

  const COLOR_MAP = {
    'RAL 7016': '#4a4f54',
    'RAL 8017': '#44322d',
    'RAL 3009': '#8e402a',
    'RAL 6005': '#114232',
    'RAL 5010': '#0d4a8b',
    'RAL 9010': '#f0f0f0',
    'RAL 1015': '#e6d6a8',
  };

  const COLOR_LABELS = {
    '#4a4f54': 'RAL 7016 – Antracyt',
    '#44322d': 'RAL 8017 – Brązowy',
    '#8e402a': 'RAL 3009 – Czerwony',
    '#114232': 'RAL 6005 – Zielony',
    '#0d4a8b': 'RAL 5010 – Niebieski',
    '#f0f0f0': 'RAL 9010 – Biały',
    '#e6d6a8': 'RAL 1015 – Beżowy',
  };

  const state = {
    width:       6,
    length:      6,
    wallHeight:  2.5,
    roofType:    'jednospadowy',
    gateType:    'uchylne',
    gateWidth:   2.8,
    wallColor:   '#4a4f54',
    roofColor:   '#4a4f54',
    windows:     0,
    sideDoor:    false,
    gutters:     false,
    ventilation: false,
  };

  /* ------------------------------------------------------------------ */
  /* PRICING                                                              */
  /* ------------------------------------------------------------------ */

  function calcPrice() {
    const p = Config.pricing;
    let t = state.width * state.length * p.basePerSqm;
    if (state.roofType === 'dwuspadowy') t *= 1 + p.gableRoofPct;
    t += { uchylne: 0, segmentowe: p.gateSegmentowe, rolowane: p.gateRolowane }[state.gateType] || 0;
    if (state.gateWidth > 3.0) t += p.gateWideExtra;
    t += state.windows * p.windowUnit;
    if (state.sideDoor)    t += p.sideDoor;
    if (state.gutters)     t += p.gutters;
    if (state.ventilation) t += p.ventilation;
    return Math.round(t);
  }

  function fmt(n) {
    return new Intl.NumberFormat('pl-PL').format(n) + ' PLN';
  }

  function renderBreakdown() {
    const p  = Config.pricing;
    const wl = state.wallColor;
    const area = state.width * state.length;
    const base = area * p.basePerSqm;
    const items = [{ label: `Baza (${area} m²)`, val: base }];

    if (state.roofType === 'dwuspadowy') {
      items.push({ label: 'Dach dwuspadowy (+15%)', val: Math.round(base * p.gableRoofPct) });
    }
    if (state.gateType === 'segmentowe') items.push({ label: 'Wrota segmentowe', val: p.gateSegmentowe });
    if (state.gateType === 'rolowane')   items.push({ label: 'Wrota rolowane',   val: p.gateRolowane });
    if (state.gateWidth > 3.0)           items.push({ label: 'Szeroka brama (>3m)', val: p.gateWideExtra });
    if (state.windows > 0)               items.push({ label: `Okna (${state.windows} szt.)`, val: state.windows * p.windowUnit });
    if (state.sideDoor)                  items.push({ label: 'Drzwi boczne', val: p.sideDoor });
    if (state.gutters)                   items.push({ label: 'Rynny', val: p.gutters });
    if (state.ventilation)               items.push({ label: 'Wentylacja', val: p.ventilation });

    const breakdownEl = document.getElementById('gk-price-breakdown');
    if (breakdownEl) {
      breakdownEl.innerHTML = items.map(it =>
        `<div class="gk__breakdown-row">
           <span class="gk__breakdown-label">${it.label}</span>
           <span class="gk__breakdown-value">${fmt(it.val)}</span>
         </div>`
      ).join('');
    }

    const totalEl = document.getElementById('gk-total-price');
    if (totalEl) totalEl.textContent = fmt(calcPrice());
  }

  /* ------------------------------------------------------------------ */
  /* ISO SVG RENDERER                                                     */
  /* ------------------------------------------------------------------ */

  /* Isometric projection helpers */
  const COS30 = 0.866;
  const SIN30 = 0.5;

  function isoPoint(rx, rz, ry, scale, ox, oy) {
    return {
      x: ox + (rx - rz) * COS30 * scale,
      y: oy - ry * scale - (rx + rz) * SIN30 * scale,
    };
  }

  function pts(arr) {
    return arr.map(p => `${p.x.toFixed(1)},${p.y.toFixed(1)}`).join(' ');
  }

  function darken(hex, pct) {
    let r = parseInt(hex.slice(1, 3), 16);
    let g = parseInt(hex.slice(3, 5), 16);
    let b = parseInt(hex.slice(5, 7), 16);
    r = Math.max(0, Math.round(r * (1 - pct)));
    g = Math.max(0, Math.round(g * (1 - pct)));
    b = Math.max(0, Math.round(b * (1 - pct)));
    return `rgb(${r},${g},${b})`;
  }

  function renderSVG() {
    const scene = document.getElementById('gk-svg-scene');
    if (!scene) return;

    const W  = state.width;
    const L  = state.length;
    const H  = state.wallHeight;
    const maxDim = Math.max(W, L, H * 2.5);

    /* Normalise to fill ~220px of viewBox */
    const scale = 200 / maxDim;

    /* Isometric origin — bottom-front-left corner of garage */
    const ox = 350;
    const oy = 310;

    /* Helper: iso point from garage coords (x=width, z=depth, y=height) */
    const ip = (x, z, y) => isoPoint(x, z, y, scale, ox, oy);

    /* Corner points of the base box */
    const A  = ip(0, 0, 0);   // front-left  bottom
    const B  = ip(W, 0, 0);   // front-right bottom
    const C  = ip(W, L, 0);   // back-right  bottom
    const D  = ip(0, L, 0);   // back-left   bottom
    const A1 = ip(0, 0, H);   // front-left  top
    const B1 = ip(W, 0, H);   // front-right top
    const C1 = ip(W, L, H);   // back-right  top
    const D1 = ip(0, L, H);   // back-left   top

    const wc  = state.wallColor;
    const rc  = state.roofColor;
    const wcd = darken(wc, 0.25); // side wall is darker

    let html = '';

    /* ---- GROUND SHADOW ---- */
    const shadow = [A, B, C, D].map(p => ({ x: p.x, y: p.y + 4 }));
    html += `<ellipse cx="${ox.toFixed(1)}" cy="${(oy + 5).toFixed(1)}"
      rx="${(W * COS30 * scale * 0.8).toFixed(1)}"
      ry="${((W + L) * SIN30 * scale * 0.18).toFixed(1)}"
      fill="rgba(0,0,0,0.12)" />`;

    /* ---- RIGHT SIDE WALL (drawn first — behind) ---- */
    html += `<polygon points="${pts([B, C, C1, B1])}"
      fill="${wcd}" stroke="#222" stroke-width="1.2"/>`;
    html += `<polygon points="${pts([B, C, C1, B1])}"
      fill="url(#gk-grad-side)" stroke="none"/>`;

    /* ---- FRONT WALL ---- */
    html += `<polygon points="${pts([A, B, B1, A1])}"
      fill="${wc}" stroke="#222" stroke-width="1.2"/>`;
    html += `<polygon points="${pts([A, B, B1, A1])}"
      fill="url(#gk-grad-front)" stroke="none"/>`;

    /* ---- GATE (on front face) ---- */
    const gw    = Math.min(state.gateWidth, W * 0.85);
    const gx0   = (W - gw) / 2;
    const gx1   = gx0 + gw;
    const gh    = H * 0.85;
    const GA    = ip(gx0, 0, 0);
    const GB    = ip(gx1, 0, 0);
    const GA1   = ip(gx0, 0, gh);
    const GB1   = ip(gx1, 0, gh);
    const gFill = darken(wc, 0.08);

    html += `<polygon points="${pts([GA, GB, GB1, GA1])}"
      fill="${gFill}" stroke="#333" stroke-width="1"/>`;

    if (state.gateType === 'segmentowe') {
      /* Horizontal panels */
      const panels = 5;
      for (let i = 1; i < panels; i++) {
        const fy = gh * i / panels;
        const pA = ip(gx0, 0, fy);
        const pB = ip(gx1, 0, fy);
        html += `<line x1="${pA.x.toFixed(1)}" y1="${pA.y.toFixed(1)}"
          x2="${pB.x.toFixed(1)}" y2="${pB.y.toFixed(1)}"
          stroke="#555" stroke-width="0.8"/>`;
      }
    } else if (state.gateType === 'rolowane') {
      const stripes = 12;
      for (let i = 1; i < stripes; i++) {
        const fy = gh * i / stripes;
        const pA = ip(gx0, 0, fy);
        const pB = ip(gx1, 0, fy);
        html += `<line x1="${pA.x.toFixed(1)}" y1="${pA.y.toFixed(1)}"
          x2="${pB.x.toFixed(1)}" y2="${pB.y.toFixed(1)}"
          stroke="rgba(0,0,0,0.2)" stroke-width="0.5"/>`;
      }
    } else {
      /* uchylne — diagonal cross bar */
      html += `<line x1="${GA.x.toFixed(1)}" y1="${GA.y.toFixed(1)}"
        x2="${GB1.x.toFixed(1)}" y2="${GB1.y.toFixed(1)}"
        stroke="#555" stroke-width="0.9" opacity="0.6"/>`;
      html += `<line x1="${GB.x.toFixed(1)}" y1="${GB.y.toFixed(1)}"
        x2="${GA1.x.toFixed(1)}" y2="${GA1.y.toFixed(1)}"
        stroke="#555" stroke-width="0.9" opacity="0.6"/>`;
    }

    /* ---- WINDOWS (front wall) ---- */
    if (state.windows > 0) {
      const winW = 0.6;
      const winH = 0.55;
      const winY = H * 0.45;

      /* Spread windows across available space on left + right of gate */
      const positions = [];
      if (state.windows >= 1) positions.push(gx0 * 0.5 - winW / 2);
      if (state.windows >= 2) positions.push(gx1 + (W - gx1) * 0.5 - winW / 2);
      if (state.windows >= 3) positions.push(gx0 * 0.18 - winW / 2);
      if (state.windows >= 4) positions.push(gx1 + (W - gx1) * 0.78 - winW / 2);

      positions.forEach(wx => {
        if (wx < 0) wx = 0.1;
        if (wx + winW > W) wx = W - winW - 0.1;
        const WA  = ip(wx,       0, winY);
        const WB  = ip(wx + winW, 0, winY);
        const WA1 = ip(wx,       0, winY + winH);
        const WB1 = ip(wx + winW, 0, winY + winH);
        const WMx = ip(wx + winW / 2, 0, winY);
        const WMx1 = ip(wx + winW / 2, 0, winY + winH);
        const WMy  = ip(wx, 0, winY + winH / 2);
        const WMy1 = ip(wx + winW, 0, winY + winH / 2);
        html += `<polygon points="${pts([WA, WB, WB1, WA1])}"
          fill="#c8e4f0" stroke="#6a9fc4" stroke-width="0.8" opacity="0.85"/>`;
        html += `<line x1="${WMx.x.toFixed(1)}" y1="${WMx.y.toFixed(1)}"
          x2="${WMx1.x.toFixed(1)}" y2="${WMx1.y.toFixed(1)}"
          stroke="#6a9fc4" stroke-width="0.6"/>`;
        html += `<line x1="${WMy.x.toFixed(1)}" y1="${WMy.y.toFixed(1)}"
          x2="${WMy1.x.toFixed(1)}" y2="${WMy1.y.toFixed(1)}"
          stroke="#6a9fc4" stroke-width="0.6"/>`;
      });
    }

    /* ---- SIDE DOOR (right wall) ---- */
    if (state.sideDoor) {
      const dw  = 0.9;
      const dh  = H * 0.78;
      const dz0 = L * 0.25;
      const SA  = ip(W, dz0, 0);
      const SB  = ip(W, dz0 + dw, 0);
      const SA1 = ip(W, dz0, dh);
      const SB1 = ip(W, dz0 + dw, dh);
      html += `<polygon points="${pts([SA, SB, SB1, SA1])}"
        fill="${gFill}" stroke="#333" stroke-width="0.9"/>`;
      const SM = ip(W, dz0 + dw / 2, 0);
      const SM1 = ip(W, dz0 + dw / 2, dh);
      html += `<line x1="${SM.x.toFixed(1)}" y1="${SM.y.toFixed(1)}"
        x2="${SM1.x.toFixed(1)}" y2="${SM1.y.toFixed(1)}"
        stroke="#555" stroke-width="0.7"/>`;
    }

    /* ---- VENTILATION (right wall near top) ---- */
    if (state.ventilation) {
      const vw  = 0.5;
      const vh  = 0.25;
      const vz0 = L * 0.65;
      const vy  = H * 0.75;
      const VA  = ip(W, vz0,      vy);
      const VB  = ip(W, vz0 + vw, vy);
      const VA1 = ip(W, vz0,      vy + vh);
      const VB1 = ip(W, vz0 + vw, vy + vh);
      html += `<polygon points="${pts([VA, VB, VB1, VA1])}"
        fill="#888" stroke="#555" stroke-width="0.7"/>`;
      for (let i = 1; i < 4; i++) {
        const vy2 = vy + vh * i / 4;
        const La = ip(W, vz0, vy2);
        const Lb = ip(W, vz0 + vw, vy2);
        html += `<line x1="${La.x.toFixed(1)}" y1="${La.y.toFixed(1)}"
          x2="${Lb.x.toFixed(1)}" y2="${Lb.y.toFixed(1)}"
          stroke="#aaa" stroke-width="0.5"/>`;
      }
    }

    /* ---- ROOF ---- */
    const overhang = 0.35;

    if (state.roofType === 'jednospadowy') {
      const ridgeH = H + overhang * 0.6;
      const eaveH  = H - overhang * 0.4;

      /* Front edge high, back edge lower */
      const R_FL = ip(-overhang * 0.5, -overhang, ridgeH);
      const R_FR = ip(W + overhang * 0.5, -overhang, ridgeH);
      const R_BR = ip(W + overhang * 0.5,  L + overhang, eaveH);
      const R_BL = ip(-overhang * 0.5,     L + overhang, eaveH);

      /* Left panel (visible) */
      html += `<polygon points="${pts([R_FL, R_FR, R_BR, R_BL])}"
        fill="${rc}" stroke="#222" stroke-width="1.2"/>`;
      html += `<polygon points="${pts([R_FL, R_FR, R_BR, R_BL])}"
        fill="url(#gk-grad-roof-l)" stroke="none"/>`;

      /* Gutters along eave */
      if (state.gutters) {
        html += `<line x1="${R_BL.x.toFixed(1)}" y1="${(R_BL.y + 2).toFixed(1)}"
          x2="${R_BR.x.toFixed(1)}" y2="${(R_BR.y + 2).toFixed(1)}"
          stroke="#555" stroke-width="3" stroke-linecap="round"/>`;
        html += `<line x1="${R_FL.x.toFixed(1)}" y1="${(R_FL.y + 2).toFixed(1)}"
          x2="${R_FR.x.toFixed(1)}" y2="${(R_FR.y + 2).toFixed(1)}"
          stroke="#555" stroke-width="2" stroke-linecap="round" opacity="0.5"/>`;
      }

    } else {
      /* DWUSPADOWY */
      const ridgeH  = H + W * 0.22;
      const ridgeXL = ip(0,       -overhang, ridgeH);
      const ridgeXR = ip(W,       -overhang, ridgeH);
      const ridgeBL = ip(0,       L + overhang, ridgeH);
      const ridgeBR = ip(W,       L + overhang, ridgeH);
      const ridgeFM = ip(W / 2, -overhang, ridgeH);
      const ridgeBM = ip(W / 2,  L + overhang, ridgeH);

      /* Left roof panel */
      const RLL_F = ip(-overhang * 0.5, -overhang, H);
      const RLL_B = ip(-overhang * 0.5,  L + overhang, H);
      html += `<polygon points="${pts([RLL_F, ridgeFM, ridgeBM, RLL_B])}"
        fill="${rc}" stroke="#222" stroke-width="1.2"/>`;
      html += `<polygon points="${pts([RLL_F, ridgeFM, ridgeBM, RLL_B])}"
        fill="url(#gk-grad-roof-l)" stroke="none"/>`;

      /* Right roof panel */
      const RRL_F = ip(W + overhang * 0.5, -overhang, H);
      const RRL_B = ip(W + overhang * 0.5,  L + overhang, H);
      html += `<polygon points="${pts([ridgeFM, RRL_F, RRL_B, ridgeBM])}"
        fill="${darken(rc, 0.08)}" stroke="#222" stroke-width="1.2"/>`;
      html += `<polygon points="${pts([ridgeFM, RRL_F, RRL_B, ridgeBM])}"
        fill="url(#gk-grad-roof-r)" stroke="none"/>`;

      /* Front gable triangle */
      const gableFL = ip(-overhang * 0.5, -overhang, H);
      const gableFR = ip(W + overhang * 0.5, -overhang, H);
      const gableFM = ip(W / 2, -overhang, ridgeH);
      html += `<polygon points="${pts([gableFL, gableFR, gableFM])}"
        fill="${wc}" stroke="#222" stroke-width="1.2"/>`;

      /* Gutters */
      if (state.gutters) {
        const eL_F = ip(-overhang * 0.5, -overhang, H);
        const eL_B = ip(-overhang * 0.5, L + overhang, H);
        const eR_F = ip(W + overhang * 0.5, -overhang, H);
        const eR_B = ip(W + overhang * 0.5, L + overhang, H);
        html += `<line x1="${eL_F.x.toFixed(1)}" y1="${(eL_F.y+1).toFixed(1)}"
          x2="${eL_B.x.toFixed(1)}" y2="${(eL_B.y+1).toFixed(1)}"
          stroke="#555" stroke-width="3" stroke-linecap="round"/>`;
        html += `<line x1="${eR_F.x.toFixed(1)}" y1="${(eR_F.y+1).toFixed(1)}"
          x2="${eR_B.x.toFixed(1)}" y2="${(eR_B.y+1).toFixed(1)}"
          stroke="#555" stroke-width="3" stroke-linecap="round"/>`;
      }
    }

    /* ---- DIMENSION LABELS ---- */
    const lblColor = '#c0392b';
    const lblFontSize = 12;

    /* Width label (front bottom edge) */
    const dimWA = ip(0, 0, 0);
    const dimWB = ip(W, 0, 0);
    const dimWM = { x: (dimWA.x + dimWB.x) / 2, y: (dimWA.y + dimWB.y) / 2 + 18 };
    html += `<line x1="${dimWA.x.toFixed(1)}" y1="${(dimWA.y+10).toFixed(1)}"
      x2="${dimWB.x.toFixed(1)}" y2="${(dimWB.y+10).toFixed(1)}"
      stroke="${lblColor}" stroke-width="1" marker-end="url(#gk-arrow)"/>`;
    html += `<text x="${dimWM.x.toFixed(1)}" y="${(dimWM.y+4).toFixed(1)}"
      font-family="sans-serif" font-size="${lblFontSize}" fill="${lblColor}"
      text-anchor="middle" font-weight="600">${state.width} m</text>`;

    /* Depth label (right bottom edge) */
    const dimLA = ip(W, 0, 0);
    const dimLB = ip(W, L, 0);
    const dimLM = { x: (dimLA.x + dimLB.x) / 2 + 24, y: (dimLA.y + dimLB.y) / 2 };
    html += `<line x1="${(dimLA.x+10).toFixed(1)}" y1="${dimLA.y.toFixed(1)}"
      x2="${(dimLB.x+10).toFixed(1)}" y2="${dimLB.y.toFixed(1)}"
      stroke="${lblColor}" stroke-width="1"/>`;
    html += `<text x="${(dimLM.x).toFixed(1)}" y="${dimLM.y.toFixed(1)}"
      font-family="sans-serif" font-size="${lblFontSize}" fill="${lblColor}"
      text-anchor="start" dominant-baseline="middle" font-weight="600">${state.length} m</text>`;

    /* Height label (front-left vertical) */
    const dimHA  = ip(0, 0, 0);
    const dimHA1 = ip(0, 0, H);
    const dimHM  = { x: dimHA.x - 22, y: (dimHA.y + dimHA1.y) / 2 };
    html += `<line x1="${(dimHA.x-12).toFixed(1)}" y1="${dimHA.y.toFixed(1)}"
      x2="${(dimHA1.x-12).toFixed(1)}" y2="${dimHA1.y.toFixed(1)}"
      stroke="${lblColor}" stroke-width="1"/>`;
    html += `<text x="${(dimHM.x - 2).toFixed(1)}" y="${dimHM.y.toFixed(1)}"
      font-family="sans-serif" font-size="${lblFontSize}" fill="${lblColor}"
      text-anchor="middle" dominant-baseline="middle" font-weight="600"
      transform="rotate(-90 ${(dimHM.x - 2).toFixed(1)} ${dimHM.y.toFixed(1)})"
      >${state.wallHeight} m</text>`;

    scene.innerHTML = html;
  }

  /* ------------------------------------------------------------------ */
  /* MAIN UPDATE                                                          */
  /* ------------------------------------------------------------------ */

  function update() {
    renderSVG();
    renderBreakdown();
  }

  /* ------------------------------------------------------------------ */
  /* EVENT HANDLERS                                                       */
  /* ------------------------------------------------------------------ */

  function camel(str) {
    return str.replace(/_([a-z])/g, (_, c) => c.toUpperCase());
  }

  const root = document.getElementById('garaz-konfigurator');
  if (!root) return;

  /* Delegate change events (select, radio, checkbox inputs) */
  root.addEventListener('change', function (e) {
    const key = e.target.dataset.configKey;
    if (!key) return;

    const stateKey = camel(key);
    if (e.target.type === 'checkbox') {
      state[stateKey] = e.target.checked;
    } else if (e.target.type === 'radio') {
      state[stateKey] = e.target.value;
    } else {
      const v = e.target.value;
      state[stateKey] = isNaN(v) ? v : parseFloat(v);
    }
    update();
  });

  /* Color swatches */
  root.addEventListener('click', function (e) {
    const swatch = e.target.closest('[data-color]');
    if (!swatch) return;

    const key      = swatch.dataset.configKey;
    const ralLabel = swatch.dataset.color;
    const hex      = COLOR_MAP[ralLabel];
    if (!hex || !key) return;

    state[camel(key)] = hex;

    /* Update aria-pressed on sibling swatches */
    swatch.closest('.gk__swatches').querySelectorAll('.gk__swatch').forEach(s => {
      s.setAttribute('aria-pressed', s === swatch ? 'true' : 'false');
    });

    /* Update label text */
    const labelId = key === 'wall_color' ? 'gk-wall-color-label' : 'gk-roof-color-label';
    const labelEl = document.getElementById(labelId);
    if (labelEl) labelEl.textContent = `${ralLabel} – ${swatch.title.split('–')[1]?.trim() || ''}`;

    update();
  });

  /* ------------------------------------------------------------------ */
  /* EMAIL MODAL                                                          */
  /* ------------------------------------------------------------------ */

  const modal   = document.getElementById('gk-modal');
  const overlay = document.getElementById('gk-modal-overlay');

  function openModal() {
    if (!modal || !overlay) return;
    modal.hidden   = false;
    overlay.hidden = false;
    document.body.style.overflow = 'hidden';
    modal.querySelector('input')?.focus();
  }

  function closeModal() {
    if (!modal || !overlay) return;
    modal.hidden   = true;
    overlay.hidden = true;
    document.body.style.overflow = '';
  }

  document.getElementById('gk-btn-email-open')?.addEventListener('click', openModal);
  document.getElementById('gk-modal-close')?.addEventListener('click', closeModal);
  overlay?.addEventListener('click', closeModal);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal && !modal.hidden) closeModal();
  });

  /* ---- Form submission ---- */
  document.getElementById('gk-email-form')?.addEventListener('submit', function (e) {
    e.preventDefault();

    const statusEl  = document.getElementById('gk-form-status');
    const submitBtn = document.getElementById('gk-submit-btn');
    statusEl.textContent = '';
    statusEl.className   = 'gk__form-status';

    const name    = this.querySelector('#gk-name').value.trim();
    const email   = this.querySelector('#gk-email').value.trim();
    const phone   = this.querySelector('#gk-phone').value.trim();
    const consent = this.querySelector('#gk-consent').checked;

    if (!name)                  return showErr('Podaj imię i nazwisko.');
    if (!isValidEmail(email))   return showErr('Podaj prawidłowy adres e-mail.');
    if (!consent)               return showErr('Wymagana zgoda na przetwarzanie danych.');

    submitBtn.disabled    = true;
    submitBtn.textContent = 'Wysyłanie…';

    const fd = new FormData();
    fd.append('action',          'garaz_send_email');
    fd.append('nonce',           Config.nonce);
    fd.append('customer_name',   name);
    fd.append('customer_email',  email);
    fd.append('customer_phone',  phone);
    fd.append('width',           state.width);
    fd.append('length',          state.length);
    fd.append('wall_height',     state.wallHeight);
    fd.append('roof_type',       state.roofType);
    fd.append('gate_type',       state.gateType);
    fd.append('gate_width',      state.gateWidth);
    fd.append('wall_color',      ralLabelFromHex(state.wallColor));
    fd.append('roof_color',      ralLabelFromHex(state.roofColor));
    fd.append('windows',         state.windows);
    fd.append('side_door',       state.sideDoor ? '1' : '0');
    fd.append('gutters',         state.gutters   ? '1' : '0');
    fd.append('ventilation',     state.ventilation ? '1' : '0');

    fetch(Config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          statusEl.className   = 'gk__form-status gk__form-status--success';
          statusEl.textContent = data.data.message;
          this.reset();
          setTimeout(closeModal, 2200);
        } else {
          showErr(data.data?.message || 'Wystąpił błąd. Spróbuj ponownie.');
        }
      })
      .catch(() => showErr('Błąd połączenia. Sprawdź internet i spróbuj ponownie.'))
      .finally(() => {
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Wyślij wycenę';
      });

    function showErr(msg) {
      statusEl.className   = 'gk__form-status gk__form-status--error';
      statusEl.textContent = msg;
      submitBtn.disabled    = false;
      submitBtn.textContent = 'Wyślij wycenę';
    }
  });

  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function ralLabelFromHex(hex) {
    for (const [ral, h] of Object.entries(COLOR_MAP)) {
      if (h === hex) return ral;
    }
    return 'RAL 7016';
  }

  /* ------------------------------------------------------------------ */
  /* PDF EXPORT                                                           */
  /* ------------------------------------------------------------------ */

  document.getElementById('gk-btn-pdf')?.addEventListener('click', function () {
    if (!window.jspdf) {
      alert('Biblioteka PDF jest wciąż ładowana. Spróbuj za chwilę.');
      return;
    }
    const { jsPDF } = window.jspdf;
    const doc  = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
    const pW   = 210;
    const mg   = 20;
    const cW   = pW - mg * 2;

    /* Header */
    doc.setFillColor(44, 62, 80);
    doc.rect(0, 0, pW, 38, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(20);
    doc.setFont('helvetica', 'bold');
    doc.text('Wycena Garazu Blaszanego', mg, 16);
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text('Konfiguracja indywidualna', mg, 25);
    doc.text(new Date().toLocaleDateString('pl-PL'), pW - mg, 25, { align: 'right' });

    let y = 50;

    function pdfSection(title) {
      doc.setFillColor(236, 240, 241);
      doc.rect(mg, y, cW, 8, 'F');
      doc.setTextColor(44, 62, 80);
      doc.setFontSize(9);
      doc.setFont('helvetica', 'bold');
      doc.text(title, mg + 3, y + 5.5);
      y += 11;
    }

    function pdfRow(label, value, even, hexColor) {
      doc.setFillColor(even ? 248 : 255, even ? 249 : 255, even ? 250 : 255);
      doc.rect(mg, y, cW, 7, 'F');
      doc.setTextColor(85, 85, 85);
      doc.setFontSize(9);
      doc.setFont('helvetica', 'normal');
      doc.text(label, mg + 3, y + 5);
      doc.setTextColor(44, 62, 80);
      doc.setFont('helvetica', 'bold');
      if (hexColor) {
        /* Draw color swatch */
        const rgb = hexToRgb(hexColor);
        if (rgb) {
          doc.setFillColor(rgb.r, rgb.g, rgb.b);
          doc.rect(pW - mg - 55, y + 1.5, 4, 4, 'F');
          doc.setDrawColor(150, 150, 150);
          doc.rect(pW - mg - 55, y + 1.5, 4, 4, 'S');
        }
        doc.text(value, pW - mg - 49, y + 5, { align: 'left' });
      } else {
        doc.text(value, pW - mg - 3, y + 5, { align: 'right' });
      }
      y += 7;
    }

    pdfSection('WYMIARY');
    pdfRow('Szerokosc', state.width + ' m', true);
    pdfRow('Dlugosc',   state.length + ' m', false);
    pdfRow('Wysokosc scian', state.wallHeight + ' m', true);

    y += 4;
    pdfSection('DACH I WROTA');
    const rt = { jednospadowy: 'Jednospadowy', dwuspadowy: 'Dwuspadowy' };
    const gt = { uchylne: 'Uchylne', segmentowe: 'Segmentowe', rolowane: 'Rolowane' };
    pdfRow('Typ dachu',       rt[state.roofType] || state.roofType, true);
    pdfRow('Typ wrot',        gt[state.gateType] || state.gateType, false);
    pdfRow('Szerokosc wrot',  state.gateWidth + ' m', true);

    y += 4;
    pdfSection('KOLORY');
    const wl = COLOR_LABELS[state.wallColor] || state.wallColor;
    const rl = COLOR_LABELS[state.roofColor] || state.roofColor;
    pdfRow('Kolor scian', wl, true,  state.wallColor);
    pdfRow('Kolor dachu', rl, false, state.roofColor);

    y += 4;
    pdfSection('DODATKI');
    pdfRow('Okna',         state.windows > 0 ? state.windows + ' szt.' : 'Brak', true);
    pdfRow('Drzwi boczne', state.sideDoor    ? 'Tak' : 'Nie', false);
    pdfRow('Rynny',        state.gutters     ? 'Tak' : 'Nie', true);
    pdfRow('Wentylacja',   state.ventilation ? 'Tak' : 'Nie', false);

    /* Total price block */
    y += 10;
    doc.setFillColor(231, 76, 60);
    doc.rect(mg, y, cW, 14, 'F');
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(12);
    doc.setFont('helvetica', 'bold');
    doc.text('SZACUNKOWA WYCENA', mg + 4, y + 9);
    doc.setFontSize(14);
    doc.text(new Intl.NumberFormat('pl-PL').format(calcPrice()) + ' PLN', pW - mg - 4, y + 9, { align: 'right' });

    /* Disclaimer */
    y += 22;
    doc.setTextColor(127, 140, 141);
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    const disc = 'Wycena ma charakter orientacyjny. Nie uwzglednia kosztow transportu, fundamentu i montazu. Ostateczna cena ustalana indywidualnie.';
    const lines = doc.splitTextToSize(disc, cW);
    doc.text(lines, pW / 2, y, { align: 'center' });

    doc.save(`garaz-wycena-${Date.now()}.pdf`);
  });

  function hexToRgb(hex) {
    const m = hex.replace('#', '').match(/.{2}/g);
    if (!m) return null;
    return { r: parseInt(m[0], 16), g: parseInt(m[1], 16), b: parseInt(m[2], 16) };
  }

  /* ------------------------------------------------------------------ */
  /* INIT                                                                 */
  /* ------------------------------------------------------------------ */

  update();

})(window, document, window.GarazConfig);
