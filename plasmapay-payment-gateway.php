<?php

/**
 * Plugin Name: PlasmaPay.com Card Checkout and Crypto Payment Gateway for WooCommerce
 * Plugin URI:
 * Version: 1.0
 * Description: PlasmaPay Checkout redirects customers to the PlasmaPay to enter payment details and pay with Visa/MC credit card, cryptocurrency and digital cash. <a href="https://plasmapay.com/">https://plasmapay.com</a>
 */

define('PLASMAPAY_DIR', plugin_dir_path(__FILE__));
define('PLASMAPAY_PATH', plugin_dir_url(__FILE__));

add_action( 'plugins_loaded', 'init_plasmapay_gateway_class', 11 );
add_filter( 'woocommerce_payment_gateways', 'add_plasmapay_gateway_class' );

function init_plasmapay_gateway_class() {
    require_once PLASMAPAY_DIR . 'includes/class-wc-plasmapay-gateway.php';
}

function add_plasmapay_gateway_class( $methods ) {
    $methods[] = 'WC_Plasmapay_Gateway';
    return $methods;
}

function loadPlasmaPayLibrary() {
    require_once PLASMAPAY_DIR . 'includes/classes/Payment.php';
    require_once PLASMAPAY_DIR . 'includes/classes/Order.php';
    require_once PLASMAPAY_DIR . 'includes/classes/Response.php';
}
