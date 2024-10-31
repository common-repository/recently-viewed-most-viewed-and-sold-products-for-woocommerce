<?php
/**
 * All common functions to load in both admin and front
 */
namespace Codexpert\Recently_Viewed_Products_For_Woocommerce\App;
use Codexpert\Plugin\Base;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Common
 * @author Codexpert <hi@codexpert.io>
 */
class Common extends Base {

	public $plugin;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->server	= $this->plugin['server'];
		$this->version	= $this->plugin['Version'];
	}

	/**
	 * Registers a new post type
	 * @uses $wp_post_types Inserts new post type object into the list
	 *
	 * @param string  Post type key, must not exceed 20 characters
	 * @param array|string  See optional args description above.
	 * @return object|WP_Error the registered post type object, or an error object
	 */
	function register_cpts() {
	
		$labels = array(
			'name'               => __( 'Shortcodes Generator', 'recently-viewed-products-for-woocommerce' ),
			'Shortcode_name'     => __( 'Shortcodes Generator', 'recently-viewed-products-for-woocommerce' ),
			'add_new'            => _x( 'Add New Shortcode', 'recently-viewed-products-for-woocommerce', 'recently-viewed-products-for-woocommerce' ),
			'add_new_item'       => __( 'Add New Shortcode', 'recently-viewed-products-for-woocommerce' ),
			'edit_item'          => __( 'Edit Shortcode', 'recently-viewed-products-for-woocommerce' ),
			'new_item'           => __( 'New Shortcode', 'recently-viewed-products-for-woocommerce' ),
			'view_item'          => __( 'View Shortcode', 'recently-viewed-products-for-woocommerce' ),
			'search_items'       => __( 'Search Shortcodes', 'recently-viewed-products-for-woocommerce' ),
			'not_found'          => __( 'No Shortcodes found', 'recently-viewed-products-for-woocommerce' ),
			'not_found_in_trash' => __( 'No Shortcodes found in Trash', 'recently-viewed-products-for-woocommerce' ),
			'parent_item_colon'  => __( 'Parent Shortcode:', 'recently-viewed-products-for-woocommerce' ),
			'menu_name'          => __( 'Recently', 'recently-viewed-products-for-woocommerce' ),
        	'all_items'          => __( 'All Shortcodes', 'text_domain' ),
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			// 'show_in_menu'        => 'recently',
			// 'show_in_menu'          => 'admin.php?page=recently-viewed-products-for-woocommerce',
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => null,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
			),
		);
	
		register_post_type( 'shortcode', $args );
	}
}