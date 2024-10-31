<?php
/**
 * All Shortcode related functions
 */
namespace Codexpert\Recently_Viewed_Products_For_Woocommerce\App;
use Codexpert\Recently_Viewed_Products_For_Woocommerce\Helper;
use Codexpert\Plugin\Base;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @package Plugin
 * @subpackage Shortcode
 * @author Codexpert <hi@codexpert.io>
 */
class Shortcode extends Base {

    public $plugin;

    /**
     * Constructor function
     */
    public function __construct( $plugin ) {
        $this->plugin   = $plugin;
        $this->slug     = $this->plugin['TextDomain'];
        $this->name     = $this->plugin['Name'];
        $this->version  = $this->plugin['Version'];
    }

    public function rvpm_products( $atts ) {
        return Helper::get_template( 'rvpm-products', 'views', $atts );
    }
}