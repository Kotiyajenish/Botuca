<?php

/**
 * Volume discounts
 *
 * @package Merchant
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Bulk discounts module class.
 *
 */
class Merchant_Volume_Discounts extends Merchant_Add_Module {

	/**
	 * Module ID.
	 */
	const MODULE_ID = 'volume-discounts';

	/**
	 * Module path.
	 */
	const MODULE_DIR = MERCHANT_DIR . 'inc/modules/' . self::MODULE_ID;

	/**
	 * Module template path.
	 */
	const MODULE_TEMPLATES_PATH = 'modules/' . self::MODULE_ID;

	/**
	 * Is module preview.
	 *
	 */
	public static $is_module_preview = false;

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		// Module id.
		$this->module_id = self::MODULE_ID;

		// WooCommerce only.
		$this->wc_only = true;

		// Parent construct.
		parent::__construct();

		// Module section.
		$this->module_section = 'boost-revenue';

		// Module default settings.
		$this->module_default_settings = array(
			'title'                    => __( 'Discount', 'merchant' ),
			'single_product_placement' => 'before-cart-form',
			'table_title'              => __( 'Buy more, save more!', 'merchant' ),
			'save_label'               => esc_html__( 'Save {amount}', 'merchant' ),
			'buy_text'                 => esc_html__( 'Buy {quantity}, get {discount} off each', 'merchant' ),
			'item_text'                => esc_html__( 'Per item:', 'merchant' ),
			'total_text'               => esc_html__( 'Total price:', 'merchant' ),
			'cart_title_text'          => esc_html__( 'Discount', 'merchant' ),
			'cart_description_text'    => esc_html__( 'A discount of {amount} has been applied.', 'merchant' ),
		);

		// Module data.
		$this->module_data = Merchant_Admin_Modules::$modules_data[ self::MODULE_ID ];

		// Module preview URL
		$this->module_data['preview_url'] = $this->set_module_preview_url( array(
			'type'  => 'product',
			'query' => array(
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => '_merchant_volume_discounts',
						'value'   => '',
						'compare' => '!=',
					),
					array(
						'key'     => '_merchant_volume_discounts',
						'value'   => 'a:0:{}',
						'compare' => '!=',
					),
				),
			),
		) );

		// Module options path.
		$this->module_options_path = self::MODULE_DIR . '/admin/options.php';

		// Is module preview page.
		if ( is_admin() && parent::is_module_settings_page() ) {
			self::$is_module_preview = true;

			// Enqueue admin scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_css' ) );

			// Add preview box
			add_filter( 'merchant_module_preview', array( $this, 'render_admin_preview' ), 10, 2 );
		}

		if ( Merchant_Modules::is_module_active( self::MODULE_ID ) && is_admin() ) {
			// Init translations.
			$this->init_translations();
		}
	}

	/**
	 * Init translations.
	 *
	 * @return void
	 */
	public function init_translations() {
		$settings = $this->get_module_settings();
		$strings  = array(
			'table_title'           => 'Bulk discount: title',
			'save_label'            => 'Bulk discount: save label',
			'buy_text'              => 'Bulk discount: buy text',
			'item_text'             => 'Bulk discount: item text',
			'total_text'            => 'Bulk discount: total text',
			'cart_title_text'       => 'Bulk discount: cart item discount title',
			'cart_description_text' => 'Bulk discount: Cart item discount description',
		);
		if ( isset( $settings['offers'] ) && ! empty( $settings['offers'] ) ) {
			foreach ( $settings['offers'] as $offer ) {
				foreach ( $strings as $key => $string ) {
					if ( ! empty( $offer['product_single_page'][ $key ] ) ) {
						Merchant_Translator::register_string( $offer['product_single_page'][ $key ], $string . ' - product single page' );
					}
					if ( ! empty( $rule['cart_page'][ $key ] ) ) {
						Merchant_Translator::register_string( $rule['cart_page'][ $key ], $string . ' - cart page' );
					}
				}
			}
		}
	}

	/**
	 * Enqueue admin page content scripts.
	 *
	 * @return void
	 */
	public function admin_enqueue_css() {
		if ( $this->is_module_settings_page() ) {
			wp_enqueue_style( "merchant-admin-{$this->module_id}", MERCHANT_URI . "assets/css/modules/volume-discounts/admin/preview.min.css", array(), MERCHANT_VERSION );
			wp_enqueue_style( "merchant-{$this->module_id}", MERCHANT_URI . "assets/css/modules/{$this->module_id}/{$this->module_id}.min.css", array(), MERCHANT_VERSION );
			wp_enqueue_script( "merchant-{$this->module_id}", MERCHANT_URI . "assets/js/modules/{$this->module_id}/admin/preview.min.js", array( 'jquery' ), MERCHANT_VERSION,
				true );
		}
	}

	/**
	 * Render admin preview
	 *
	 * @param Merchant_Admin_Preview $preview
	 * @param string                 $module
	 *
	 * @return Merchant_Admin_Preview
	 */
	public function render_admin_preview( $preview, $module ) {
		if ( $module === self::MODULE_ID ) {
			ob_start();
			self::admin_preview_content();
			$content = ob_get_clean();

			// HTML
			$preview->set_html( $content );

			// Table Title
			$preview->set_text( 'table_title', '.merchant-volume-discounts-title' );

			// Save Label
			$preview->set_text( 'save_label', '.merchant-volume-discounts-save-label', array(
				array(
					'{amount}',
				),
				array(
					wc_price( 10 ),
				),
			) );

			// Buy Text
			$preview->set_text( 'buy_text', '.merchant-volume-discounts-buy-label', array(
				array(
					'{amount}',
					'{discount}',
				),
				array(
					'<strong>10</strong>',
					'<strong>' . wc_price( 2 ) . '</strong>',
				),
			) );
		}

		return $preview;
	}

	/**
	 * Admin preview content.
	 *
	 * @return void
	 */
	public function admin_preview_content() {
		$settings = $this->get_module_settings();
		?>
        <div class="merchant-single-product-preview">
            <div class="mrc-preview-single-product-elements">
                <div class="mrc-preview-left-column">
                    <div class="mrc-preview-product-image-wrapper">
                        <div class="mrc-preview-product-image"></div>
                        <div class="mrc-preview-product-image-thumbs">
                            <div class="mrc-preview-product-image-thumb"></div>
                            <div class="mrc-preview-product-image-thumb"></div>
                            <div class="mrc-preview-product-image-thumb"></div>
                        </div>
                    </div>
                </div>
                <div class="mrc-preview-right-column" data-currency="<?php
				echo esc_attr( get_woocommerce_currency_symbol() ); ?>">
					<?php
					merchant_get_template_part(
						Merchant_Volume_Discounts::MODULE_TEMPLATES_PATH,
						'single-product',
						array(
							'settings'       => $settings,
							'discount_tiers' => array(
								array(
									'quantity'            => 10,
									'discount'            => 5,
									'discount_type'       => 'percentage_discount',
									'product_single_page' => array(
										'save_label'  => esc_html__( 'Save {amount}', 'merchant' ),
										'item_text'   => esc_html__( 'Per item:', 'merchant' ),
										'total_text'  => esc_html__( 'Total price:', 'merchant' ),
										'buy_text'    => esc_html__( 'Buy {quantity}, get {discount} off each', 'merchant' ),
										'table_title' => esc_html__( 'Buy more, save more!', 'merchant' ),
									),
								),
							),
							'product_price'  => 20,
						)
					);
					?>
                </div>
            </div>
        </div>
		<?php
		$this->cart_item_preview();
	}

	/**
	 * Cart item admin preview.
	 *
	 * @return void
	 */
	public function cart_item_preview() {
		?>
        <div class="merchant-cart-preview">
            <div class="merchant-cart-offers-container">
                <div class="cart-item-offers">
                    <div class="cart-item-offer__container">
                        <div class="cart-item-offer">
                            <div class="item-row">
                                <div class="column_3">
                                    <div class="product-details">
                                        <div class="offer-description">Add 3 to get 20% Discount</div>
                                        <div class="product-name">
                                            <a href="#" title="Product Name">Product Name</a>
                                        </div>
                                        <div class="price-area">
                                            <span class="price">
                                                <?php
                                                echo wp_kses( wc_price( 15 ), merchant_kses_allowed_tags( array( 'bdi' ) ) ) ?>
                                            </span>
                                        </div>
                                        <div class="item-footer">
                                            <div class="product-variations-wrapper"></div>
                                            <div class="add-to-cart">
                                                <button class="add-to-cart-button alt" type="button">Add To Cart</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Add this function for backward compatibility purpose.
	 *
	 * @return void
	 */
	public function get_module_custom_css() {
		// No implementation needed.
		return;
	}
}


// Initialize the module.
add_action( 'init', function () {
	Merchant_Modules::create_module( new Merchant_Volume_Discounts() );
} );
