<?php
defined( 'ABSPATH' ) || exit;

class Garaz_Configurator {

	private bool $shortcode_rendered = false;

	public function __construct() {
		add_shortcode( 'garaz_konfigurator', [ $this, 'render_shortcode' ] );
		add_action( 'wp_footer', [ $this, 'maybe_enqueue_assets' ] );
	}

	public function render_shortcode( array $atts ): string {
		$this->shortcode_rendered = true;
		ob_start();
		include GARAZ_PLUGIN_DIR . 'templates/configurator-template.php';
		return ob_get_clean();
	}

	public function maybe_enqueue_assets(): void {
		if ( ! $this->shortcode_rendered ) {
			return;
		}

		wp_enqueue_script(
			'garaz-jspdf',
			'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js',
			[],
			'2.5.1',
			true
		);

		wp_enqueue_script(
			'garaz-configurator',
			GARAZ_PLUGIN_URL . 'assets/js/configurator.js',
			[ 'garaz-jspdf' ],
			GARAZ_VERSION,
			true
		);

		wp_localize_script( 'garaz-configurator', 'GarazConfig', [
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'garaz_send_email' ),
			'currency' => 'PLN',
			'pricing'  => [
				'basePerSqm'      => 450,
				'gableRoofPct'    => 0.15,
				'gateSegmentowe'  => 800,
				'gateRolowane'    => 1200,
				'gateWideExtra'   => 500,
				'windowUnit'      => 350,
				'sideDoor'        => 600,
				'gutters'         => 400,
				'ventilation'     => 300,
				// Domek narzędziowy
				'domekBasePerSqm' => 600,
				'domekWindow'     => 400,
				'domekWoodAccents'=> 800,
				'domekGutters'    => 300,
				// Wiata śmietnikowa
				'wiataPerBin'     => [ 0, 1500, 2200, 3000, 3800 ],
				'wiataClosedFront'=> 500,
			],
		] );

		wp_enqueue_style(
			'garaz-configurator',
			GARAZ_PLUGIN_URL . 'assets/css/configurator.css',
			[],
			GARAZ_VERSION
		);
	}
}
