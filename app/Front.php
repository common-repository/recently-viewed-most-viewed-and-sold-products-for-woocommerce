<?php
/**
 * All public facing functions
 */
namespace Codexpert\Recently_Viewed_Products_For_Woocommerce\App;
use Codexpert\Plugin\Base;
use Codexpert\Recently_Viewed_Products_For_Woocommerce\Helper;
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Front
 * @author Codexpert <hi@codexpert.io>
 */
class Front extends Base {

	public $plugin;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->version	= $this->plugin['Version'];
	}

	public function add_admin_bar( $admin_bar ) {
		if( ! current_user_can( 'manage_options' ) ) return;

		$admin_bar->add_menu( [
			'id'    => $this->slug,
			'title' => $this->name,
			'href'  => add_query_arg( 'page', $this->slug, admin_url( 'admin.php' ) ),
			'meta'  => [
				'title' => $this->name,            
			],
		] );
	}

	public function head() {
	}

	public function remove_hooks() {
		$hide_sale 		= Helper::get_option( 'rvpfw_display', 'hide_sale' );
		$hide_ratting 	= Helper::get_option( 'rvpfw_display', 'hide_ratting' );

		if ( ! empty( $hide_sale ) ) {
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		}

		if ( ! empty( $hide_ratting ) ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		}
	}
	
	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'RVPFW_DEBUG' ) && RVPFW_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->slug, plugins_url( "/assets/css/front{$min}.css", RVPFW ), '', $this->version, 'all' );

		wp_enqueue_script( $this->slug, plugins_url( "/assets/js/front{$min}.js", RVPFW ), [ 'jquery' ], $this->version, true );
		
		$localized = [
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'_wpnonce'	=> wp_create_nonce(),
		];
		wp_localize_script( $this->slug, 'RVPFW', apply_filters( "{$this->slug}-localized", $localized ) );
	}

	public function modal() {
		echo '
		<div id="recently-viewed-products-for-woocommerce-modal" style="display: none">
			<img id="recently-viewed-products-for-woocommerce-modal-loader" src="' . esc_attr( RVPFW_ASSET . '/img/loader.gif' ) . '" />
		</div>';
	}

	public function recently_viewed() {
		if ( ! is_product() ) return;
        global $product;

		// Get the current product ID.
 		$product_id = $product->get_id();

 		if ( is_user_logged_in() ) {
 			// Logged-in Users
            $user_id        = get_current_user_id();
            $product_ids    = get_user_meta( $user_id, 'recently_viewed_products', true ) ? : [];

            $product_ids[]  = $product_id;
            update_user_meta( $user_id, 'recently_viewed_products', array_unique( $product_ids ) );
        }
        else {
        	// Guest Users
			$cookie_time  	= Helper::get_option( 'rvpfw_general', 'cookie_time' );

            $_product_ids   = isset( $_COOKIE['recently_viewed_products'] ) ? $this->sanitize( $_COOKIE['recently_viewed_products'] ) : '';
            $product_ids    = unserialize( stripslashes( $_product_ids ) );

            $product_ids[]  = $product_id;
            setcookie( 'recently_viewed_products', serialize( array_unique( $product_ids ) ), time() + $cookie_time, COOKIEPATH, COOKIE_DOMAIN );
        }

	}

	public function most_viewed() {
		if ( ! is_product() ) return;
        global $product;

        // Get the current product ID.
 		$product_id = $product->get_id();

        // Get the current view count for the product.
	    $view_count = get_post_meta( $product_id, 'product_view_count', true );

	    // If the view count is empty, set it to 0.
	    if ( empty( $view_count ) ) {
	        $view_count = 0;
	    }

	    // Increment the view count by 1.
	    $view_count++;

	    // Update the view count meta data.
	    update_post_meta( $product_id, 'product_view_count', $view_count );
	}

	/**
	 * Rating Markup
	 *
	 * @since 1.2.2
	 * @param  string $html  Rating Markup.
	 * @param  float  $rating Rating being shown.
	 * @param  int    $count  Total number of ratings.
	 * @return string
	 */
	public function rating_markup( $html, $rating, $count ) {
	    $html  = '<div class="rvpm-star-rating">';
	    $html .= rvpm_rating_html( $rating );
	    $html .= '</div>';
	    return $html;
	}
}