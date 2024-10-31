<?php 
use Codexpert\Recently_Viewed_Products_For_Woocommerce\Helper;

if ( ! empty( $args ) ) {
	extract( $args );
}
else {
	$args = [];
}

$title 			= isset( $args['title'] ) ? $args['title'] : '';
$num_columns 	= isset( $args['num_columns'] ) ? $args['num_columns'] : '';
$pagination 	= isset( $args['pagination'] ) ? $args['pagination'] : '';

$hide_products  = Helper::get_option( 'rvpfw_display', 'hide_products' );
$hide_category 	= Helper::get_option( 'rvpfw_display', 'hide_category' );

if ( empty( $num_columns ) ) {
    $num_columns = Helper::get_option( 'rvpfw_general', 'how_many_products_show_per_row' );
}

if ( empty( $pagination ) ) {
    $pagination = Helper::get_option( 'rvpfw_general', 'pagination' );
}

$products 		= rvpw_product_query( $args );
?>
<?php if ( $title ): 
	echo '<h2>'. esc_html( $title ) .'</h2>';
endif; ?>

<div class="rvpm-products" style="grid-template-columns: repeat(<?php echo esc_attr( $num_columns ); ?>, 1fr)">
	<?php
	if( $products->have_posts()) : 
        while( $products->have_posts()) : $products->the_post(); 
        	$product_id 	= get_the_ID();
            $product    	= wc_get_product( $product_id );
            $product_type   = $product->get_type();
        	?>

			<div id="rvpm-product-<?php echo esc_attr( $product_id ); ?>" class="rvpm-product product-type-<?php echo esc_attr( $product_type ); ?>">
				<?php
				echo '<div class="rvpm-shop-thumbnail-wrap">';

				/**
				 * Hook: woocommerce_before_shop_loop_item.
				 *
				 * @hooked woocommerce_template_loop_product_link_open - 10
				 */
				// do_action( 'woocommerce_before_shop_loop_item' );


				/**
				 * Hook: woocommerce_before_shop_loop_item_title.
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item_title' );

				echo '</div>';

				echo '<div class="rvpm-shop-summary-wrap">';

				if ( empty( $hide_category ) ) {
					rvpm_woo_shop_parent_category();
				}

				/**
				 * Hook: woocommerce_shop_loop_item_title.
				 *
				 * @hooked woocommerce_template_loop_product_title - 10
				 */
				// do_action( 'woocommerce_shop_loop_item_title' );

				rvpm_woocommerce_template_loop_product_title();

				/**
				 * Hook: woocommerce_after_shop_loop_item_title.
				 *
				 * @hooked woocommerce_template_loop_rating - 5
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );

				/**
				 * Hook: woocommerce_after_shop_loop_item.
				 *
				 * @hooked woocommerce_template_loop_product_link_close - 5
				 * @hooked woocommerce_template_loop_add_to_cart - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item' );

				echo '</div>';
				?>
			</div>

        <?php endwhile; wp_reset_query(); else: 

        echo "<p>" . __( 'No Product Found!', 'recently-viewed-products-for-woocommerce' ) . "</p>";

    endif; 
	?>
</div>

<?php

/**
* pagination
*/
if ( $pagination ) {
	echo '<div class="rvpm-pagination">';
	rvpm_pagination( $products );
	echo '</div>';
}
