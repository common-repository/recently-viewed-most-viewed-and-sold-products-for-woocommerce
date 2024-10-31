<?php
$base_url 	= rvpfw_site_url();
$buttons 	= [
	'changelog' => [
		'url' 	=> 'https://wordpress.org/plugins/recently-viewed-products-for-woocommerce/#developers',
		'label' => __( 'Changelog', 'rvpfw' ) 
	],
	'community' 	=> [
		'url' 	=> 'https://facebook.com/groups/codexpert.io',
		'label' => __( 'Community', 'rvpfw' ) 
	],
	'website' 	=> [
		'url' 	=> 'https://codexpert.io/',
		'label' => __( 'Official Website', 'rvpfw' ) 
	],
	'support' 	=> [
		'url' 	=> 'https://help.codexpert.io/',
		'label' => __( 'Ask Support', 'rvpfw' ) 
	],
];
$buttons 	= apply_filters( 'recently-viewed-products-for-woocommerce_help_btns', $buttons );
?>
<script type="text/javascript">
	jQuery(function($){ $.get( ajaxurl, { action : 'recently-viewed-products-for-woocommerce_fetch-docs' }); });
</script>
<div class="recently-viewed-products-for-woocommerce-help-tab">
	<div class="recently-viewed-products-for-woocommerce-documentation">
		 <div class='wrap'>
		 	<div id='recently-viewed-products-for-woocommerce-helps'>
		    <?php

		    $helps = get_option( 'recently-viewed-products-for-woocommerce_docs_json', [] );
			$utm = [ 'utm_source' => 'dashboard', 'utm_medium' => 'settings', 'utm_campaign' => 'faq' ];
		    if( is_array( $helps ) ) :
		    foreach ( $helps as $help ) {
		    	$help_link = add_query_arg( $utm, $help['link'] );
		        ?>
		        <div id='recently-viewed-products-for-woocommerce-help-<?php echo esc_attr( $help['id'] ); ?>' class='recently-viewed-products-for-woocommerce-help'>
		            <h2 class='recently-viewed-products-for-woocommerce-help-heading' data-target='#recently-viewed-products-for-woocommerce-help-text-<?php echo esc_attr( $help['id'] ); ?>'>
		                <a href='<?php echo esc_url( $help_link ); ?>' target='_blank'>
		                <span class='dashicons dashicons-admin-links'></span></a>
		                <span class="heading-text"><?php echo esc_html( $help['title']['rendered'] ); ?></span>
		            </h2>
		            <div id='recently-viewed-products-for-woocommerce-help-text-<?php echo esc_attr( $help['id'] ); ?>' class='recently-viewed-products-for-woocommerce-help-text' style='display:none'>
		                <?php echo wpautop( wp_trim_words( $help['content']['rendered'], 55, " <a class='sc-more' href='" . esc_url( $help_link ) . "' target='_blank'>[more..]</a>" ) ); ?>
		            </div>
		        </div>
		        <?php
		    }
		    else:
		        echo '<p>' . __( 'Something is wrong! No help found!', 'recently-viewed-products-for-woocommerce' ) . '</p>';
		    endif;
		    ?>
		    </div>
		</div>
	</div>
	<div class="recently-viewed-products-for-woocommerce-help-links">
		<?php 
		foreach ( $buttons as $key => $button ) {
			$button_url = add_query_arg( $utm, $button['url'] );
			echo "<a target='_blank' href='" . esc_url( $button_url ) . "' class='recently-viewed-products-for-woocommerce-help-link'>" . esc_html( $button['label'] ) . "</a>";
		}
		?>
	</div>
</div>

<?php do_action( 'recently-viewed-products-for-woocommerce_help_tab_content' ); ?>