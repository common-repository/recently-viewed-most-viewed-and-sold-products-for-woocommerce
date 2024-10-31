<?php
use Codexpert\Recently_Viewed_Products_For_Woocommerce\Helper;

if( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/**
 * Gets the site's base URL
 * 
 * @uses get_bloginfo()
 * 
 * @return string $url the site URL
 */
if( ! function_exists( 'rvpfw_site_url' ) ) :
function rvpfw_site_url() {
	$url = get_bloginfo( 'url' );

	return $url;
}
endif;

if( ! function_exists( 'rvpm_pri' ) ) :
function rvpm_pri( $data, $admin_only = true, $hide_adminbar = true ) {

    if( $admin_only && ! current_user_can( 'manage_options' ) ) return;

    echo '<pre>';
    if( is_object( $data ) || is_array( $data ) ) {
        print_r( $data );
    }
    else {
        var_dump( $data );
    }
    echo '</pre>';

    if( is_admin() && $hide_adminbar ) {
        echo '<style>#adminmenumain{display:none;}</style>';
    }
}
endif;

add_action('template_redirect', function () {
    ob_start();
});

if( !function_exists( 'rvpw_product_query' ) ) :
function rvpw_product_query( $args ) {

    extract( $args );

    $hide_products  = Helper::get_option( 'rvpfw_display', 'hide_products' );

    if ( empty( $type ) ) {
        $type       = Helper::get_option( 'rvpfw_general', 'product_type' );
    }

    if ( empty( $order ) ) {
        $order      = Helper::get_option( 'rvpfw_general', 'order' );
    }

    if ( empty( $orderby ) ) {
        $orderby    = Helper::get_option( 'rvpfw_general', 'orderby' );
    }

    if ( empty( $num_posts ) ) {
        $num_posts  = Helper::get_option( 'rvpfw_general', 'how_many_products_show' );
    }

    $paged          = get_query_var( 'paged') ? get_query_var( 'paged') : 1;

    $args = array(
        'post_type'         => 'product',
        'post_status '      => 'publish',
        'posts_per_page'    => $num_posts,
        'order'             => $order,
        'orderby'           => $orderby,
        'paged'             => $paged,
    );

    if ( $type == 'recently-viewed' ) {
        if ( is_user_logged_in() ) {
            $user_id            = get_current_user_id();
            $product_ids        = get_user_meta( $user_id, 'recently_viewed_products', true );
            $viewed_products    = $product_ids;
        }
        else {
            // $product_ids        = isset( $_COOKIE['recently_viewed_products'] ) ? unserialize( stripslashes( sanitize_text_field( $_COOKIE['recently_viewed_products'] ) ) ) : [];

            $_product_ids   = isset( $_COOKIE['recently_viewed_products'] ) ? sanitize_text_field( $_COOKIE['recently_viewed_products'] ) : '';
            $product_ids    = unserialize( stripslashes( $_product_ids ) );

            $viewed_products   = $product_ids;
        }
    }
    elseif ( $type == 'most-viewed' ) {
        $args['orderby']    = 'meta_value_num';
        $args['order']      = 'DESC';
        $args['meta_key']   = 'product_view_count';
    }
    elseif ( $type == 'sold-products' ) {
        $viewed_products = rvpm_get_recently_sold_products();
    }

    if( ! empty( $viewed_products ) ) {
        $args['post__in']   = $viewed_products;
    }

    if ( ! empty( $hide_products ) ) {
        foreach ( $hide_products as $_hide_products ) {
            if ( 'stock_products' == $_hide_products ) {
                $args['meta_query'][] = [
                    'key'       => '_stock_status',
                    'value'     => 'outofstock',
                    'compare'   => 'NOT IN'
                ];
            }

            if ( 'free_products' == $_hide_products ) {
                $args['meta_query'][] = [
                    'key'       => '_price',
                    'value'     => '',
                    'compare'   => 'NOT IN'
                ];
            }
        }
    }
    
    $products = new \WP_Query( apply_filters( 'rvpw_product_query_params', $args ) );
    
    return apply_filters( 'rvpw_queried_products', $products );
}
endif;

if( ! function_exists( 'rvpm_get_recently_sold_items' ) ) :
function rvpm_get_recently_sold_items() {
    global $wpdb;

    $limit = 11;

    // get the last 30 days of orders
    $results = $wpdb->get_results( "
        SELECT order_items.order_item_name, order_items.order_item_id, SUM( order_item_meta.meta_value ) AS qty
        FROM {$wpdb->prefix}woocommerce_order_items as order_items
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->prefix}posts as posts ON order_items.order_id = posts.ID
        WHERE order_item_meta.meta_key = '_qty'
        AND posts.post_date >= date_sub( now(), INTERVAL 60 DAY )
        AND posts.post_type = 'shop_order'
        AND posts.post_status IN ( 'wc-completed', 'wc-processing', 'wc-on-hold' )
        GROUP BY order_items.order_item_name, order_items.order_item_id
        LIMIT $limit
        " );

    return $results;
}
endif;

if( ! function_exists( 'rvpm_get_recently_sold_products' ) ) :
function rvpm_get_recently_sold_products() {
    $items      = rvpm_get_recently_sold_items();

    $products   = [];
    foreach ( $items as $_item ) {
        $item       = new \WC_Order_Item_Product( $_item->order_item_id );
        $products[] = $item->get_product_id(); 
    }

    return $products;
}
endif;

/**
 * Shop page - Parent Category
 */
if ( ! function_exists( 'rvpm_woo_shop_parent_category' ) ) :
    /**
     * Add and/or Remove Categories from shop archive page.
     *
     * @hooked woocommerce_after_shop_loop_item - 9
     *
     * @since 1.1.0
     */
    function rvpm_woo_shop_parent_category() { ?>
        <span class="rvpm-woo-product-category">
            <?php
            global $product;
            $product_categories = function_exists( 'wc_get_product_category_list' ) ? wc_get_product_category_list( get_the_ID(), ';', '', '' ) : $product->get_categories( ';', '', '' );

            $product_categories = htmlspecialchars_decode( wp_strip_all_tags( $product_categories ) );
            if ( $product_categories ) {
                list( $parent_cat ) = explode( ';', $product_categories );
                echo apply_filters( 'rvpm_woo_shop_product_categories', esc_html( $parent_cat ), get_the_ID() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }

            ?>
        </span>
        <?php
    }
endif;

if ( ! function_exists( 'rvpm_woocommerce_template_loop_product_title' ) ) {

    /**
     * Show the product title in the product loop. By default this is an H2.
     */
    function rvpm_woocommerce_template_loop_product_title() {

        echo '<a href="' . esc_url( get_the_permalink() ) . '" class="rvpm-loop-product__link">';
            woocommerce_template_loop_product_title();
        echo '</a>';
    }
}

if( ! function_exists( 'rvpm_rating_html' ) ) :
function rvpm_rating_html( $rating ) {

    $half_rating = $rating - floor($rating);
    $rating_html = '';
    
    for ( $i = 0; $i < (int)$rating; $i++ ) { 
        $rating_html .= "<span class='dashicons dashicons-star-filled'></span>";
    }
    
    if ( $half_rating > 0 ) {
        $rating += 1;
        $rating_html .= "<span class='dashicons dashicons-star-half'></span>";
    }

    for ( $i = 0; $i < 5 - (int)$rating; $i++ ) { 
        $rating_html .= "<span class='dashicons dashicons-star-empty'></span>";
    }

    return $rating_html;
}
endif;

if( ! function_exists( 'rvpm_pagination' ) ) :
function rvpm_pagination( $products ) {

    $total_pages    = $products->max_num_pages;
    $big            = 999999999;

    if ( $total_pages > 1 ) {

        $paged = get_query_var( 'paged' );
        
        $current_page = max( 1, $paged );

        echo paginate_links( array(
            'base'      => str_replace( $big, '%#%', get_pagenum_link( $big, false ) ),
            'format'    => '?paged=%#%',
            'current'   => $current_page,
            'total'     => $total_pages,
            'prev_text' => '<span class="dashicons dashicons-arrow-left-alt2"></span>',
            'next_text' => '<span class="dashicons dashicons-arrow-right-alt2"></span>',
        ) );
    }
}
endif;