<?php

/**
 * The plugin bootstrap file
 *
 * 
 *
 * @link              https://apps.net.pk
 * @since             1.0.0
 * @package           Payfast_Payment_Utility
 *
 * @wordpress-plugin
 * Plugin Name:       PayFast Payment Utility
 * Plugin URI:        https://apps.net.pk
 * Description:       This plugin will let WordPress enable to accept PayFast Payment Gateway Payments
 * Version:           1.0.0
 * Author:            APPS-PayFast Dev Team
 * Author URI:        https://apps.net.pk
 * License:           FreeWare
 * License URI:       
 * Text Domain:       payfast-payment-utility
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function activate_payfast_payment_utility() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-payfast-payment-utility-activator.php';
	Payfast_Payment_Utility_Activator::activate();
}


function deactivate_payfast_payment_utility() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-payfast-payment-utility-deactivator.php';
	Payfast_Payment_Utility_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_payfast_payment_utility' );
register_deactivation_hook( __FILE__, 'deactivate_payfast_payment_utility' );


require plugin_dir_path( __FILE__ ) . 'includes/class-payfast-payment-utility.php';

function run_payfast_payment_utility() {

	$plugin = new Payfast_Payment_Utility();
	$plugin->run();

}
add_action( 'admin_menu', 'payfast_menu_item' );

function payfast_menu_item(){

  $page_title = 'PayFast Payment Utility';
  $menu_title = 'PayFast Payment Utility';
  $capability = 'manage_options';
  $menu_slug  = 'payfast_payment_options';  
  $position   = 11;

  add_menu_page( $page_title,
                 $menu_title, 
                 $capability, 
                 $menu_slug, 
                 '', 
                 '', 
                 $position );
}
run_payfast_payment_utility();
