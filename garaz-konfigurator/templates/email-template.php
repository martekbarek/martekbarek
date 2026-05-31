<?php defined( 'ABSPATH' ) || exit;
/**
 * Variables available: $data (array), $price (float)
 * Use inline styles only — Gmail strips <style> blocks.
 */
$roof_labels = [ 'jednospadowy' => 'Jednospadowy', 'dwuspadowy' => 'Dwuspadowy' ];
$gate_labels = [ 'uchylne' => 'Uchylne', 'segmentowe' => 'Segmentowe', 'rolowane' => 'Rolowane' ];
$ral_labels  = [
	'RAL 7016' => 'RAL 7016 – Antracyt',
	'RAL 8017' => 'RAL 8017 – Brązowy',
	'RAL 3009' => 'RAL 3009 – Czerwony',
	'RAL 6005' => 'RAL 6005 – Zielony',
	'RAL 5010' => 'RAL 5010 – Niebieski',
	'RAL 9010' => 'RAL 9010 – Biały',
	'RAL 1015' => 'RAL 1015 – Beżowy',
];
$ral_hex = [
	'RAL 7016' => '#4a4f54', 'RAL 8017' => '#44322d', 'RAL 3009' => '#8e402a',
	'RAL 6005' => '#114232', 'RAL 5010' => '#0d4a8b', 'RAL 9010' => '#f0f0f0',
	'RAL 1015' => '#e6d6a8',
];

$price_fmt = number_format( $price, 0, ',', ' ' );
$date      = date_i18n( 'd.m.Y' );
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Wycena garażu blaszanego</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background:#f4f4f4;padding:20px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" border="0"
       style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);">

  <!-- HEADER -->
  <tr>
    <td bgcolor="#2c3e50" style="background:#2c3e50;padding:32px 40px;">
      <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">
        Wycena Garażu Blaszanego
      </h1>
      <p style="margin:6px 0 0;color:#bdc3c7;font-size:14px;">
        Przygotowano: <?php echo esc_html( $date ); ?> &nbsp;|&nbsp;
        Dla: <?php echo esc_html( $data['customer_name'] ); ?>
      </p>
    </td>
  </tr>

  <!-- PRICE HERO -->
  <tr>
    <td bgcolor="#e74c3c" style="background:#e74c3c;padding:24px 40px;text-align:center;">
      <p style="margin:0;color:#ffffff;font-size:13px;letter-spacing:1px;text-transform:uppercase;">
        Szacunkowa wycena
      </p>
      <p style="margin:8px 0 0;color:#ffffff;font-size:40px;font-weight:700;line-height:1;">
        <?php echo esc_html( $price_fmt ); ?> PLN
      </p>
      <p style="margin:6px 0 0;color:rgba(255,255,255,0.8);font-size:12px;">
        * cena orientacyjna, bez transportu i montażu
      </p>
    </td>
  </tr>

  <!-- BODY -->
  <tr>
    <td style="padding:32px 40px;">

      <!-- WYMIARY -->
      <h2 style="margin:0 0 12px;font-size:14px;text-transform:uppercase;letter-spacing:1px;
                 color:#7f8c8d;border-bottom:1px solid #ecf0f1;padding-bottom:6px;">
        Wymiary
      </h2>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
        <?php
        $rows = [
          [ 'Szerokość',      $data['width']      . ' m' ],
          [ 'Długość',        $data['length']     . ' m' ],
          [ 'Wysokość ścian', $data['wall_height'] . ' m' ],
        ];
        foreach ( $rows as $i => $row ) :
          $bg = $i % 2 === 0 ? '#f8f9fa' : '#ffffff';
        ?>
        <tr bgcolor="<?php echo $bg; ?>">
          <td style="padding:8px 12px;font-size:14px;color:#555;width:55%;">
            <?php echo esc_html( $row[0] ); ?>
          </td>
          <td style="padding:8px 12px;font-size:14px;color:#2c3e50;font-weight:600;text-align:right;">
            <?php echo esc_html( $row[1] ); ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>

      <!-- DACH I WROTA -->
      <h2 style="margin:0 0 12px;font-size:14px;text-transform:uppercase;letter-spacing:1px;
                 color:#7f8c8d;border-bottom:1px solid #ecf0f1;padding-bottom:6px;">
        Dach i Wrota
      </h2>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
        <?php
        $rows = [
          [ 'Typ dachu',        $roof_labels[ $data['roof_type'] ] ?? $data['roof_type'] ],
          [ 'Typ wrót',         $gate_labels[ $data['gate_type'] ] ?? $data['gate_type'] ],
          [ 'Szerokość wrót',   $data['gate_width'] . ' m' ],
        ];
        foreach ( $rows as $i => $row ) :
          $bg = $i % 2 === 0 ? '#f8f9fa' : '#ffffff';
        ?>
        <tr bgcolor="<?php echo $bg; ?>">
          <td style="padding:8px 12px;font-size:14px;color:#555;width:55%;">
            <?php echo esc_html( $row[0] ); ?>
          </td>
          <td style="padding:8px 12px;font-size:14px;color:#2c3e50;font-weight:600;text-align:right;">
            <?php echo esc_html( $row[1] ); ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>

      <!-- KOLORY -->
      <h2 style="margin:0 0 12px;font-size:14px;text-transform:uppercase;letter-spacing:1px;
                 color:#7f8c8d;border-bottom:1px solid #ecf0f1;padding-bottom:6px;">
        Kolory
      </h2>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
        <?php
        $color_rows = [
          [ 'Kolor ścian', $data['wall_color'] ],
          [ 'Kolor dachu', $data['roof_color'] ],
        ];
        foreach ( $color_rows as $i => $row ) :
          $bg  = $i % 2 === 0 ? '#f8f9fa' : '#ffffff';
          $hex = $ral_hex[ $row[1] ] ?? '#ccc';
          $lbl = $ral_labels[ $row[1] ] ?? $row[1];
        ?>
        <tr bgcolor="<?php echo $bg; ?>">
          <td style="padding:8px 12px;font-size:14px;color:#555;width:55%;">
            <?php echo esc_html( $row[0] ); ?>
          </td>
          <td style="padding:8px 12px;font-size:14px;color:#2c3e50;font-weight:600;text-align:right;">
            <span style="display:inline-block;width:14px;height:14px;border-radius:2px;
                         background:<?php echo esc_attr( $hex ); ?>;
                         border:1px solid rgba(0,0,0,0.2);vertical-align:middle;
                         margin-right:6px;"></span>
            <?php echo esc_html( $lbl ); ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>

      <!-- DODATKI -->
      <h2 style="margin:0 0 12px;font-size:14px;text-transform:uppercase;letter-spacing:1px;
                 color:#7f8c8d;border-bottom:1px solid #ecf0f1;padding-bottom:6px;">
        Dodatki
      </h2>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
        <?php
        $extras = [
          [ 'Okna',         $data['windows'] > 0 ? $data['windows'] . ' szt.' : 'Brak' ],
          [ 'Drzwi boczne', $data['side_door']    ? 'Tak' : 'Nie' ],
          [ 'Rynny',        $data['gutters']       ? 'Tak' : 'Nie' ],
          [ 'Wentylacja',   $data['ventilation']   ? 'Tak' : 'Nie' ],
        ];
        foreach ( $extras as $i => $row ) :
          $bg = $i % 2 === 0 ? '#f8f9fa' : '#ffffff';
        ?>
        <tr bgcolor="<?php echo $bg; ?>">
          <td style="padding:8px 12px;font-size:14px;color:#555;width:55%;">
            <?php echo esc_html( $row[0] ); ?>
          </td>
          <td style="padding:8px 12px;font-size:14px;color:#2c3e50;font-weight:600;text-align:right;">
            <?php echo esc_html( $row[1] ); ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>

      <!-- DISCLAIMER -->
      <p style="margin:0;padding:16px;background:#fef9e7;border-left:4px solid #f39c12;
                font-size:12px;color:#7f8c8d;line-height:1.6;border-radius:0 4px 4px 0;">
        Wycena ma charakter orientacyjny i nie stanowi oferty w rozumieniu przepisów Kodeksu
        Cywilnego. Ostateczna cena jest ustalana indywidualnie po dokładnej analizie wymagań.
        Nie uwzględnia kosztów transportu, fundamentu ani montażu.
      </p>

    </td>
  </tr>

  <!-- FOOTER -->
  <tr>
    <td bgcolor="#2c3e50" style="background:#2c3e50;padding:24px 40px;text-align:center;">
      <p style="margin:0;color:#bdc3c7;font-size:12px;">
        <?php echo esc_html( get_bloginfo( 'name' ) ); ?> &bull;
        <?php echo esc_html( home_url() ); ?>
      </p>
      <p style="margin:8px 0 0;color:#7f8c8d;font-size:11px;">
        Wiadomość wygenerowana automatycznie przez konfigurator garażu.
      </p>
    </td>
  </tr>

</table>
</td></tr>
</table>

</body>
</html>
