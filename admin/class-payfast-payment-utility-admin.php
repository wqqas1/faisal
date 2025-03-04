<?php

class Payfast_Payment_Utility_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->load_dependencies();
    }

    private function load_dependencies() {

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-payfast-payment-utility-settings.php';
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wppb-payfast-payment-utility.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/payfast-payment-utility-admin.js', array('jquery'), $this->version, false);
    }

}
