<?php defined( 'ABSPATH' ) || exit; ?>

<div id="garaz-konfigurator" class="gk" role="main" aria-label="Konfigurator garażu blaszanego">

  <div class="gk__layout">

    <!-- ===== PANEL STEROWANIA ===== -->
    <aside class="gk__controls" aria-label="Opcje konfiguracji">

      <details class="gk__section" open>
        <summary class="gk__section-title">📐 Wymiary</summary>
        <div class="gk__section-body">

          <div class="gk__field">
            <label for="gk-width">Szerokość</label>
            <select id="gk-width" data-config-key="width">
              <option value="3">3 m</option>
              <option value="4">4 m</option>
              <option value="5">5 m</option>
              <option value="6" selected>6 m</option>
              <option value="7">7 m</option>
              <option value="8">8 m</option>
              <option value="9">9 m</option>
              <option value="10">10 m</option>
            </select>
          </div>

          <div class="gk__field">
            <label for="gk-length">Długość</label>
            <select id="gk-length" data-config-key="length">
              <option value="5">5 m</option>
              <option value="6" selected>6 m</option>
              <option value="7">7 m</option>
              <option value="8">8 m</option>
              <option value="9">9 m</option>
              <option value="10">10 m</option>
              <option value="12">12 m</option>
              <option value="15">15 m</option>
            </select>
          </div>

          <div class="gk__field">
            <label for="gk-wall-height">Wysokość ścian</label>
            <select id="gk-wall-height" data-config-key="wall_height">
              <option value="2.0">2,0 m</option>
              <option value="2.5" selected>2,5 m</option>
              <option value="3.0">3,0 m</option>
              <option value="3.5">3,5 m</option>
              <option value="4.0">4,0 m</option>
            </select>
          </div>

        </div>
      </details>

      <details class="gk__section" open>
        <summary class="gk__section-title">🏠 Dach</summary>
        <div class="gk__section-body">

          <div class="gk__radio-group">
            <label class="gk__radio-label">
              <input type="radio" name="roof_type" value="jednospadowy"
                     data-config-key="roof_type" checked>
              <span class="gk__radio-box">
                <span class="gk__radio-icon">⬅</span>
                Jednospadowy
              </span>
            </label>
            <label class="gk__radio-label">
              <input type="radio" name="roof_type" value="dwuspadowy"
                     data-config-key="roof_type">
              <span class="gk__radio-box">
                <span class="gk__radio-icon">∧</span>
                Dwuspadowy
              </span>
            </label>
          </div>

        </div>
      </details>

      <details class="gk__section" open>
        <summary class="gk__section-title">🚪 Wrota</summary>
        <div class="gk__section-body">

          <div class="gk__field">
            <label>Typ wrót</label>
            <div class="gk__radio-group">
              <label class="gk__radio-label">
                <input type="radio" name="gate_type" value="uchylne"
                       data-config-key="gate_type" checked>
                <span class="gk__radio-box">Uchylne</span>
              </label>
              <label class="gk__radio-label">
                <input type="radio" name="gate_type" value="segmentowe"
                       data-config-key="gate_type">
                <span class="gk__radio-box">Segmentowe <span class="gk__price-tag">+800 PLN</span></span>
              </label>
              <label class="gk__radio-label">
                <input type="radio" name="gate_type" value="rolowane"
                       data-config-key="gate_type">
                <span class="gk__radio-box">Rolowane <span class="gk__price-tag">+1200 PLN</span></span>
              </label>
            </div>
          </div>

          <div class="gk__field">
            <label for="gk-gate-width">Szerokość wrót</label>
            <select id="gk-gate-width" data-config-key="gate_width">
              <option value="2.4">2,4 m</option>
              <option value="2.8" selected>2,8 m</option>
              <option value="3.0">3,0 m</option>
              <option value="4.0">4,0 m <span>+500 PLN</span></option>
              <option value="5.0">5,0 m +500 PLN</option>
            </select>
          </div>

        </div>
      </details>

      <details class="gk__section" open>
        <summary class="gk__section-title">🎨 Kolory</summary>
        <div class="gk__section-body">

          <div class="gk__field">
            <label>Kolor ścian</label>
            <div class="gk__swatches" data-config-key="wall_color">
              <button type="button" class="gk__swatch" aria-pressed="true"
                      data-color="RAL 7016" data-config-key="wall_color"
                      style="background:#4a4f54" title="RAL 7016 – Antracyt"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 8017" data-config-key="wall_color"
                      style="background:#44322d" title="RAL 8017 – Brązowy"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 3009" data-config-key="wall_color"
                      style="background:#8e402a" title="RAL 3009 – Czerwony"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 6005" data-config-key="wall_color"
                      style="background:#114232" title="RAL 6005 – Zielony"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 5010" data-config-key="wall_color"
                      style="background:#0d4a8b" title="RAL 5010 – Niebieski"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 9010" data-config-key="wall_color"
                      style="background:#f4f4f4;border:1px solid #ccc" title="RAL 9010 – Biały"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 1015" data-config-key="wall_color"
                      style="background:#e6d6a8" title="RAL 1015 – Beżowy"></button>
            </div>
            <p class="gk__swatch-label" id="gk-wall-color-label">RAL 7016 – Antracyt</p>
          </div>

          <div class="gk__field">
            <label>Kolor dachu</label>
            <div class="gk__swatches" data-config-key="roof_color">
              <button type="button" class="gk__swatch" aria-pressed="true"
                      data-color="RAL 7016" data-config-key="roof_color"
                      style="background:#4a4f54" title="RAL 7016 – Antracyt"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 8017" data-config-key="roof_color"
                      style="background:#44322d" title="RAL 8017 – Brązowy"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 3009" data-config-key="roof_color"
                      style="background:#8e402a" title="RAL 3009 – Czerwony"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 6005" data-config-key="roof_color"
                      style="background:#114232" title="RAL 6005 – Zielony"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 5010" data-config-key="roof_color"
                      style="background:#0d4a8b" title="RAL 5010 – Niebieski"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 9010" data-config-key="roof_color"
                      style="background:#f4f4f4;border:1px solid #ccc" title="RAL 9010 – Biały"></button>
              <button type="button" class="gk__swatch" aria-pressed="false"
                      data-color="RAL 1015" data-config-key="roof_color"
                      style="background:#e6d6a8" title="RAL 1015 – Beżowy"></button>
            </div>
            <p class="gk__swatch-label" id="gk-roof-color-label">RAL 7016 – Antracyt</p>
          </div>

        </div>
      </details>

      <details class="gk__section" open>
        <summary class="gk__section-title">➕ Dodatki</summary>
        <div class="gk__section-body">

          <div class="gk__field">
            <label for="gk-windows">Okna</label>
            <select id="gk-windows" data-config-key="windows">
              <option value="0" selected>Bez okien</option>
              <option value="1">1 okno (+350 PLN)</option>
              <option value="2">2 okna (+700 PLN)</option>
              <option value="3">3 okna (+1 050 PLN)</option>
              <option value="4">4 okna (+1 400 PLN)</option>
            </select>
          </div>

          <div class="gk__checkbox-group">
            <label class="gk__checkbox-label">
              <input type="checkbox" data-config-key="side_door">
              <span>Drzwi boczne <span class="gk__price-tag">+600 PLN</span></span>
            </label>
            <label class="gk__checkbox-label">
              <input type="checkbox" data-config-key="gutters">
              <span>Rynny <span class="gk__price-tag">+400 PLN</span></span>
            </label>
            <label class="gk__checkbox-label">
              <input type="checkbox" data-config-key="ventilation">
              <span>Wentylacja <span class="gk__price-tag">+300 PLN</span></span>
            </label>
          </div>

        </div>
      </details>

    </aside>

    <!-- ===== PANEL PODGLĄDU ===== -->
    <div class="gk__preview-panel">

      <!-- SVG Wizualizacja -->
      <div class="gk__svg-wrapper">
        <svg id="gk-svg" xmlns="http://www.w3.org/2000/svg"
             viewBox="0 0 700 420" aria-label="Izometryczna wizualizacja garażu">
          <defs>
            <linearGradient id="gk-grad-front" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="rgba(255,255,255,0.18)"/>
              <stop offset="100%" stop-color="rgba(0,0,0,0.12)"/>
            </linearGradient>
            <linearGradient id="gk-grad-side" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="rgba(0,0,0,0.08)"/>
              <stop offset="100%" stop-color="rgba(0,0,0,0.22)"/>
            </linearGradient>
            <linearGradient id="gk-grad-roof-l" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%" stop-color="rgba(255,255,255,0.2)"/>
              <stop offset="100%" stop-color="rgba(0,0,0,0.08)"/>
            </linearGradient>
            <linearGradient id="gk-grad-roof-r" x1="1" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="rgba(0,0,0,0.05)"/>
              <stop offset="100%" stop-color="rgba(0,0,0,0.18)"/>
            </linearGradient>
          </defs>
          <g id="gk-svg-scene"></g>
        </svg>
      </div>

      <!-- Podsumowanie wyceny -->
      <div class="gk__summary" id="gk-summary">
        <h3 class="gk__summary-title">Wycena</h3>
        <div class="gk__breakdown" id="gk-price-breakdown"></div>
        <div class="gk__summary-total">
          <span class="gk__summary-total-label">Szacunkowa wycena:</span>
          <span class="gk__summary-total-price" id="gk-total-price">– PLN</span>
        </div>
        <p class="gk__summary-note">* Cena orientacyjna, bez kosztów transportu i montażu.</p>
      </div>

      <!-- Przyciski akcji -->
      <div class="gk__actions">
        <button type="button" class="gk__btn gk__btn--secondary" id="gk-btn-pdf">
          ⬇ Pobierz PDF
        </button>
        <button type="button" class="gk__btn gk__btn--primary" id="gk-btn-email-open">
          ✉ Wyślij wycenę e-mailem
        </button>
      </div>

    </div><!-- /.gk__preview-panel -->
  </div><!-- /.gk__layout -->

  <!-- ===== MODAL EMAIL ===== -->
  <div class="gk__modal-overlay" id="gk-modal-overlay" hidden aria-hidden="true"></div>
  <div class="gk__modal" id="gk-modal" role="dialog"
       aria-modal="true" aria-labelledby="gk-modal-title" hidden>
    <div class="gk__modal-content">
      <button type="button" class="gk__modal-close" id="gk-modal-close"
              aria-label="Zamknij okno dialogowe">&times;</button>
      <h2 class="gk__modal-title" id="gk-modal-title">Wyślij wycenę e-mailem</h2>
      <p class="gk__modal-subtitle">Wyślemy Ci szczegółową wycenę skonfigurowanego garażu.</p>

      <form class="gk__form" id="gk-email-form" novalidate>
        <div class="gk__field">
          <label for="gk-name">Imię i nazwisko <span class="gk__required">*</span></label>
          <input type="text" id="gk-name" name="customer_name"
                 required maxlength="100" autocomplete="name">
        </div>
        <div class="gk__field">
          <label for="gk-email">Adres e-mail <span class="gk__required">*</span></label>
          <input type="email" id="gk-email" name="customer_email"
                 required autocomplete="email">
        </div>
        <div class="gk__field">
          <label for="gk-phone">Telefon <span class="gk__optional">(opcjonalnie)</span></label>
          <input type="tel" id="gk-phone" name="customer_phone"
                 autocomplete="tel">
        </div>
        <div class="gk__field gk__field--checkbox">
          <label class="gk__checkbox-label">
            <input type="checkbox" id="gk-consent" name="consent" required>
            <span>Wyrażam zgodę na przetwarzanie moich danych osobowych w celu otrzymania wyceny. <span class="gk__required">*</span></span>
          </label>
        </div>
        <div class="gk__form-status" id="gk-form-status" role="status" aria-live="polite"></div>
        <button type="submit" class="gk__btn gk__btn--primary gk__btn--full" id="gk-submit-btn">
          Wyślij wycenę
        </button>
      </form>
    </div>
  </div>

</div><!-- /#garaz-konfigurator -->
