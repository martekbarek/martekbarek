<?php
defined( 'ABSPATH' ) || exit;

class Garaz_Email_Handler {

	private const ALLOWED_WIDTHS      = [ 3, 4, 5, 6, 7, 8, 9, 10 ];
	private const ALLOWED_LENGTHS     = [ 5, 6, 7, 8, 9, 10, 12, 15 ];
	private const ALLOWED_HEIGHTS     = [ 2.0, 2.5, 3.0, 3.5, 4.0 ];
	private const ALLOWED_ROOF_TYPES  = [ 'jednospadowy', 'dwuspadowy' ];
	private const ALLOWED_GATE_TYPES  = [ 'uchylne', 'segmentowe', 'rolowane' ];
	private const ALLOWED_GATE_WIDTHS = [ 2.4, 2.8, 3.0, 4.0, 5.0 ];
	private const ALLOWED_COLORS      = [
		'RAL 7016', 'RAL 8017', 'RAL 3009',
		'RAL 6005', 'RAL 5010', 'RAL 9010', 'RAL 1015',
	];

	private const PRICING = [
		'basePerSqm'     => 450,
		'gableRoofPct'   => 0.15,
		'gateSegmentowe' => 800,
		'gateRolowane'   => 1200,
		'gateWideExtra'  => 500,
		'windowUnit'     => 350,
		'sideDoor'       => 600,
		'gutters'        => 400,
		'ventilation'    => 300,
	];

	public function __construct() {
		add_action( 'wp_ajax_garaz_send_email',        [ $this, 'handle_send_email' ] );
		add_action( 'wp_ajax_nopriv_garaz_send_email', [ $this, 'handle_send_email' ] );
	}

	public function handle_send_email(): void {
		if ( ! check_ajax_referer( 'garaz_send_email', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => 'Błąd bezpieczeństwa.' ], 403 );
		}

		$this->check_rate_limit();

		$data = $this->sanitize_and_validate();
		if ( is_wp_error( $data ) ) {
			wp_send_json_error( [ 'message' => $data->get_error_message() ], 422 );
		}

		$price = $this->calculate_price( $data );

		ob_start();
		include GARAZ_PLUGIN_DIR . 'templates/email-template.php';
		$body = ob_get_clean();

		$subject = sprintf(
			'Wycena garażu blaszanego – %s PLN',
			number_format( $price, 0, ',', ' ' )
		);

		$headers = [
			'Content-Type: text/html; charset=UTF-8',
			'From: Garaż Konfigurator <noreply@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>',
		];

		$sent = wp_mail( $data['customer_email'], $subject, $body, $headers );

		$admin = get_option( 'admin_email' );
		if ( $admin && $admin !== $data['customer_email'] ) {
			wp_mail( $admin, '[KOPIA] ' . $subject, $body, $headers );
		}

		if ( $sent ) {
			wp_send_json_success( [ 'message' => 'Wycena została wysłana na podany adres e-mail!' ] );
		} else {
			wp_send_json_error( [ 'message' => 'Błąd wysyłki. Sprawdź konfigurację SMTP i spróbuj ponownie.' ], 500 );
		}
	}

	private function check_rate_limit(): void {
		$key   = 'garaz_rate_' . md5( $_SERVER['REMOTE_ADDR'] ?? '' );
		$count = (int) get_transient( $key );
		if ( $count >= 5 ) {
			wp_send_json_error( [ 'message' => 'Zbyt wiele prób. Poczekaj godzinę i spróbuj ponownie.' ], 429 );
		}
		set_transient( $key, $count + 1, HOUR_IN_SECONDS );
	}

	private function sanitize_and_validate(): array|WP_Error {
		$name  = sanitize_text_field( $_POST['customer_name']  ?? '' );
		$email = sanitize_email( $_POST['customer_email'] ?? '' );
		$phone = sanitize_text_field( $_POST['customer_phone'] ?? '' );

		if ( empty( $name ) || mb_strlen( $name ) > 100 ) {
			return new WP_Error( 'invalid', 'Podaj prawidłowe imię i nazwisko.' );
		}
		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid', 'Podaj prawidłowy adres e-mail.' );
		}
		if ( $phone && ! preg_match( '/^\+?[\d\s\-]{7,20}$/', $phone ) ) {
			return new WP_Error( 'invalid', 'Podaj prawidłowy numer telefonu.' );
		}

		$width       = (int) ( $_POST['width']       ?? 0 );
		$length      = (int) ( $_POST['length']      ?? 0 );
		$wall_height = (float) ( $_POST['wall_height'] ?? 0 );
		$roof_type   = sanitize_key( $_POST['roof_type']  ?? '' );
		$gate_type   = sanitize_key( $_POST['gate_type']  ?? '' );
		$gate_width  = (float) ( $_POST['gate_width']  ?? 0 );
		$wall_color  = sanitize_text_field( $_POST['wall_color'] ?? '' );
		$roof_color  = sanitize_text_field( $_POST['roof_color'] ?? '' );
		$windows     = (int) ( $_POST['windows'] ?? 0 );
		$side_door   = ! empty( $_POST['side_door'] ) && $_POST['side_door'] !== '0';
		$gutters     = ! empty( $_POST['gutters'] )   && $_POST['gutters']   !== '0';
		$ventilation = ! empty( $_POST['ventilation'] ) && $_POST['ventilation'] !== '0';

		if ( ! in_array( $width,       self::ALLOWED_WIDTHS,      true ) ) return new WP_Error( 'invalid', 'Nieprawidłowa szerokość.' );
		if ( ! in_array( $length,      self::ALLOWED_LENGTHS,     true ) ) return new WP_Error( 'invalid', 'Nieprawidłowa długość.' );
		if ( ! in_array( $wall_height, self::ALLOWED_HEIGHTS,     true ) ) return new WP_Error( 'invalid', 'Nieprawidłowa wysokość.' );
		if ( ! in_array( $roof_type,   self::ALLOWED_ROOF_TYPES,  true ) ) return new WP_Error( 'invalid', 'Nieprawidłowy typ dachu.' );
		if ( ! in_array( $gate_type,   self::ALLOWED_GATE_TYPES,  true ) ) return new WP_Error( 'invalid', 'Nieprawidłowy typ wrót.' );
		if ( ! in_array( $gate_width,  self::ALLOWED_GATE_WIDTHS, true ) ) return new WP_Error( 'invalid', 'Nieprawidłowa szerokość wrót.' );
		if ( ! in_array( $wall_color,  self::ALLOWED_COLORS,      true ) ) return new WP_Error( 'invalid', 'Nieprawidłowy kolor ścian.' );
		if ( ! in_array( $roof_color,  self::ALLOWED_COLORS,      true ) ) return new WP_Error( 'invalid', 'Nieprawidłowy kolor dachu.' );
		if ( $windows < 0 || $windows > 4 )                               return new WP_Error( 'invalid', 'Nieprawidłowa liczba okien.' );

		return compact(
			'name', 'email', 'phone',
			'width', 'length', 'wall_height',
			'roof_type', 'gate_type', 'gate_width',
			'wall_color', 'roof_color',
			'windows', 'side_door', 'gutters', 'ventilation'
		) + [ 'customer_name' => $name, 'customer_email' => $email, 'customer_phone' => $phone ];
	}

	private function calculate_price( array $data ): float {
		$p     = self::PRICING;
		$total = $data['width'] * $data['length'] * $p['basePerSqm'];

		if ( $data['roof_type'] === 'dwuspadowy' ) {
			$total *= 1 + $p['gableRoofPct'];
		}

		$gate_extras = [
			'uchylne'    => 0,
			'segmentowe' => $p['gateSegmentowe'],
			'rolowane'   => $p['gateRolowane'],
		];
		$total += $gate_extras[ $data['gate_type'] ] ?? 0;

		if ( $data['gate_width'] > 3.0 ) {
			$total += $p['gateWideExtra'];
		}

		$total += $data['windows'] * $p['windowUnit'];
		if ( $data['side_door'] )   $total += $p['sideDoor'];
		if ( $data['gutters'] )     $total += $p['gutters'];
		if ( $data['ventilation'] ) $total += $p['ventilation'];

		return round( $total, 2 );
	}
}
