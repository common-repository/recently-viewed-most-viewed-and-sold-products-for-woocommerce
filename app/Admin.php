<?php
/**
 * All admin facing functions
 */
namespace Codexpert\Recently_Viewed_Products_For_Woocommerce\App;
use Codexpert\Plugin\Base;
use Codexpert\Plugin\Metabox;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Admin
 * @author Codexpert <hi@codexpert.io>
 */
class Admin extends Base {

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
	 * Internationalization
	 */
	public function i18n() {
		load_plugin_textdomain( 'recently-viewed-products-for-woocommerce', false, RVPFW_DIR . '/languages/' );
	}

	/**
	 * Installer. Runs once when the plugin in activated.
	 *
	 * @since 1.0
	 */
	public function install() {

		if( ! get_option( 'recently-viewed-products-for-woocommerce_version' ) ){
			update_option( 'recently-viewed-products-for-woocommerce_version', $this->version );
		}
		
		if( ! get_option( 'recently-viewed-products-for-woocommerce_install_time' ) ){
			update_option( 'recently-viewed-products-for-woocommerce_install_time', time() );
		}
	}

	/**
	 * Adds a sample meta box
	 */
	public function add_meta_boxes() {
		$metabox = [
			'id'            => $this->slug,
			'label'         => $this->name,
			'post_type'  	=> 'shortcode',
			// 'context'    => 'normal',
			// 'box_priority'	=> 'high',
			'topnav'	=> true,
			'sections'      => [
				'recently-viewed-products'	=> [
					'id'        => 'recently-viewed-products',
					'label'     => __( 'RECENTLY SHOWING PRODUCTS', 'recently-viewed-products-for-woocommerce' ),
					'icon'      => 'dashicons-admin-tools',
					'color'		=> '#4c3f93',
					'sticky'	=> true,
					'fields'    => [
						'section_title' => [
							'id'        => 'section_title',
							'label'     => __( 'Section Title', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'text',
							// 'class'     => '',
							'default'   => __( 'Recently Viewed Products', 'recently-viewed-products-for-woocommerce' ),
							'readonly'  => false, // true|false
							'disabled'  => false, // true|false
						],
						'product_type' => [
							'id'      => 'product_type',
							'label'     => __( 'Product Type', 'recently-viewed-products-for-woocommerce' ),
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
							'label'     => __( 'Pagination Show/Hide', 'recently-viewed-products-for-woocommerce' ),
							'type'      => 'switch',
							// 'class'     => '',
							'disabled'  => false, // true|false
							'default'   => 'off'
						],
					]
				],
			]
		];

		new Metabox( $metabox );
	}
	
	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'RVPFW_DEBUG' ) && RVPFW_DEBUG ? '' : '.min';
		
		wp_enqueue_style( $this->slug, plugins_url( "/assets/css/admin{$min}.css", RVPFW ), '', $this->version, 'all' );

		wp_enqueue_script( $this->slug, plugins_url( "/assets/js/admin{$min}.js", RVPFW ), [ 'jquery' ], $this->version, true );
	}

	public function action_links( $links ) {
		$this->admin_url = admin_url( 'admin.php' );

		$new_links = [
			'settings'	=> sprintf( '<a href="%1$s">' . __( 'Settings', 'recently-viewed-products-for-woocommerce' ) . '</a>', add_query_arg( 'page', $this->slug, $this->admin_url ) )
		];
		
		return array_merge( $new_links, $links );
	}

	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		
		if ( $this->plugin['basename'] === $plugin_file ) {
			$plugin_meta['help'] = '<a href="https://help.wpplugines.com/" target="_blank" class="cx-help">' . __( 'Help', 'recently-viewed-products-for-woocommerce' ) . '</a>';
		}

		return $plugin_meta;
	}

	public function update_cache( $post_id, $post, $update ) {
		wp_cache_delete( "rvpfw_{$post->post_type}", 'rvpfw' );
	}

	public function footer_text( $text ) {
		if( get_current_screen()->parent_base != $this->slug ) return $text;

		return sprintf( __( 'If you like <strong>%1$s</strong>, please <a href="%2$s" target="_blank">leave us a %3$s rating</a> on WordPress.org! It\'d motivate and inspire us to make the plugin even better!', 'recently-viewed-products-for-woocommerce' ), $this->name, "https://wordpress.org/support/plugin/{$this->slug}/reviews/?filter=5#new-post", '⭐⭐⭐⭐⭐' );
	}

	public function modal() {
		echo '
		<div id="recently-viewed-products-for-woocommerce-modal" style="display: none">
			<img id="recently-viewed-products-for-woocommerce-modal-loader" src="' . esc_attr( RVPFW_ASSET . '/img/loader.gif' ) . '" />
		</div>';
	}

	public function add_menu() {
		add_menu_page( __( 'Recently', 'coschool' ), __( 'Recently', 'coschool' ), 'manage_options', 'recently', '', '', 10 );
	}

	public function set_shortcuts( $post_id, $post, $update )	{
		if ( 'shortcode' !== $post->post_type ) {
			return;
		}

		if ( isset( $_POST['recently-viewed-products'] ) ) {
			$data 		= $this->sanitize( $_POST['recently-viewed-products'], 'array' );

			$shortcode 	= sprintf( 'rvpm-products title="%s" type="%s" num_posts=%d num_columns=%d order="%s" orderby="%s" pagination="%s"', 
				$data['section_title'],
				$data['product_type'],
				$data['how_many_products_show'],
				$data['how_many_products_show_per_row'],
				$data['order'],
				$data['orderby'],
				$data['pagination'],
			);
			update_post_meta( $post_id, 'shortcode', $shortcode );
		}
	}

	public function add_custom_meta_boxes()	{
		add_meta_box( 'rlms-shortcode', 'Shortcode', [ $this, 'shortcode_cb' ], 'shortcode', 'side' );
	}

	public function shortcode_cb( $post ) {
		$shortcode = get_post_meta( $post->ID, 'shortcode', true );
		?>
		<textarea name="" id="" cols="24" rows="6" readonly><?php echo esc_attr( $shortcode ) ?></textarea>
		<?php
	}

	public function product_columns( $columns )	{

		// $display = Helper::get_option( 'product-view-count_basic', 'display_view_count' );

		// if ( ! in_array( 'admin', $display ) ) {
		// 	return $columns;
		// }

		//add column
	    $columns['shortcode'] = __( 'Shortcode', 'woocommerce' );		  

	    return $columns;
	}

	public function view_count_columns( $column, $product_id ) {
		if ( $column == 'shortcode' ) {
	        $view_count = get_post_meta( $product_id, 'shortcode', true );
	        echo "<code>{$view_count}</code>";
	    }
	}

	public function sortable_columns( $columns ) {
		$columns['shortcode'] = 'shortcode';
  		return $columns;
	}
}