<?php
/**
 * All settings related functions
 */
namespace Codexpert\Recently_Viewed_Products_For_Woocommerce\App;
use Codexpert\Recently_Viewed_Products_For_Woocommerce\Helper;
use Codexpert\Plugin\Base;
use Codexpert\Plugin\Settings as Settings_API;

/**
 * @package Plugin
 * @subpackage Settings
 * @author Codexpert <hi@codexpert.io>
 */
class Settings extends Base {

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
	
	public function init_menu() {

		$settings = [
			'id'            => $this->slug,
			'label'         => __( 'Settings', 'recently-viewed-products-for-woocommerce' ),
			'title'         => "{$this->name} v{$this->version}",
			'header'        => $this->name,
			'parent'     	=> 'edit.php?post_type=shortcode',
			// 'priority'   => 10,
			// 'capability' => 'manage_options',
			// 'icon'       => 'dashicons-wordpress',
			// 'position'   => 25,
			// 'topnav'	=> true,
			'sections'      => [
				'rvpfw_general'	=> [
					'id'        => 'rvpfw_general',
					'label'     => __( 'General', 'recently-viewed-products-for-woocommerce' ),
					'icon'      => 'dashicons-admin-tools',
					// 'color'		=> '#4c3f93',
					'sticky'	=> false,
					'fields'    => [
						'cookie_time' => [
							'id'        => 'cookie_time',
							'label'     => __( 'Set cookie time', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'number',
							'desc'      => __( 'Set the duration (days) of the cookie time that tracks customer viewed products.', 'recently-viewed-products-for-woocommerce' ),
							// 'class'     => '',
							'default'   => 30,
							'readonly'  => false, // true|false
							'disabled'  => false, // true|false
						],
						'product_type' => [
							'id'      => 'product_type',
							'label'     => __( 'Select which product type to display', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'radio',
							// 'class'     => '',
							'options'   => [
								'recently-viewed'  	=> __( 'Recently Viewed Products', 'recently-viewed-products-for-woocommerce' ),
								'most-viewed'  		=> __( 'Most Viewed Products', 'recently-viewed-products-for-woocommerce' ),
								'sold-products'  	=> __( 'Sold Products', 'recently-viewed-products-for-woocommerce' ),
							],
							'default'   => 'recently-viewed',
							'disabled'  => false, // true|false
						],
						'how_many_products_show' => [
							'id'      => 'how_many_products_show',
							'label'     => __( 'How many products to show?', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'number',
							// 'class'     => '',
							'default'   => 8,
							'readonly'  => false, // true|false
							'disabled'  => false, // true|false
						],
						'how_many_products_show_per_row' => [
							'id'      => 'how_many_products_show_per_row',
							'label'     => __( 'How many products to show per row', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'number',
							// 'class'     => '',
							'default'   => 4,
							'readonly'  => false, // true|false
							'disabled'  => false, // true|false
						],
						'order' => [
							'id'      => 'order',
							'label'     => __( 'Order', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'select',
							// 'class'     => '',
							'options'   => [
								'ASC'  	=> __( 'ASC', 'recently-viewed-products-for-woocommerce' ),
								'DESC'  => __( 'DESC', 'recently-viewed-products-for-woocommerce' ),
							],
							'default'   => 'option_2',
							'disabled'  => false, // true|false
							'multiple'  => false, // true|false
						],
						'orderby' => [
							'id'      => 'orderby',
							'label'     => __( 'Order By', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'select',
							// 'class'     => '',
							'options'   => [
								'none'      			=> __( 'None', 'recently-viewed-products-for-woocommerce' ),
						        'ID'        			=> __( 'ID', 'recently-viewed-products-for-woocommerce' ),
						        'title'     			=> __( 'Title', 'recently-viewed-products-for-woocommerce' ),
						        'name'      			=> __( 'Name', 'recently-viewed-products-for-woocommerce' ),
						        'date'      			=> __( 'Date', 'recently-viewed-products-for-woocommerce' ),
						        'rand'      			=> __( 'Random', 'recently-viewed-products-for-woocommerce' ),
						        'menu_order'      		=> __( 'Menu Order', 'recently-viewed-products-for-woocommerce' ),
						        '_price' 				=> __( 'Product Price', 'recently-viewed-products-for-woocommerce' ),
						        'total_sales' 			=> __( 'Top Seller', 'recently-viewed-products-for-woocommerce' ),
						        'comment_count' 		=> __( 'Most Reviewed', 'recently-viewed-products-for-woocommerce' ),
						        '_wc_average_rating'	=> __( 'Top Rated', 'recently-viewed-products-for-woocommerce' ),
							],
							'default'   => 'option_2',
							'disabled'  => false, // true|false
							'multiple'  => false, // true|false
						],
						'pagination' => [
							'id'      => 'pagination',
							'label'     => __( 'Pagination Show', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'switch',
							// 'class'     => '',
							'disabled'  => false, // true|false
							'default'   => 'off'
						],
					]
				],
				'rvpfw_display'	=> [
					'id'        => 'rvpfw_display',
					'label'     => __( 'Display', 'recently-viewed-products-for-woocommerce' ),
					'icon'      => 'dashicons-admin-tools',
					// 'color'		=> '#4c3f93',
					'sticky'	=> false,
					'fields'    => [
						'hide_products' => [
							'id'      => 'hide_products',
							'label'     => __( 'Set which product type to hide', 'cx-plugin' ),
							'type'      => 'checkbox',
							// 'class'     => '',
							'options'   => [
								'stock_products'  	=> __( 'Hide out of stock products', 'cx-plugin' ),
								'free_products'  	=> __( 'Hide free products', 'cx-plugin' ),
							],
							'default'   => [ 'stock_products', 'free_products' ],
							'disabled'  => false, // true|false
							'multiple'  => true, // true|false
						],
						'hide_category' => [
							'id'      	=> 'hide_category',
							'label'     => __( 'Category Hide', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'switch',
							// 'class'     => '',
							'disabled'  => false, // true|false
							'default'   => 'off'
						],
						'hide_ratting' => [
							'id'      	=> 'hide_ratting',
							'label'     => __( 'Ratting Hide', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'switch',
							// 'class'     => '',
							'disabled'  => false, // true|false
							'default'   => 'off'
						],
						'hide_sale' => [
							'id'      	=> 'hide_sale',
							'label'     => __( 'Sale Ribbon Hide', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'switch',
							// 'class'     => '',
							'disabled'  => false, // true|false
							'default'   => 'off'
						],
					]
				],
			],
		];

		new Settings_API( $settings );
	}
}