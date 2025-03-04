<?php

class Payfast_Payment_Utility {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {

        $this->plugin_name = 'payfast-payment-utility';
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-payfast-payment-utility-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-payfast-payment-utility-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-payfast-payment-utility-admin.php';
        //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-payfast-payment-utility-admin-settings.php';


        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-payfast-payment-utility-public.php';

        $this->loader = new Payfast_Payment_Utility_Loader();
    }

    private function set_locale() {

        $plugin_i18n = new Payfast_Payment_Utility_i18n();
        $plugin_i18n->set_domain('payfast-payment');

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    private function define_admin_hooks() {

        $plugin_admin = new Payfast_Payment_Utility_Admin($this->get_plugin_name(), $this->get_version());
        $plugin_settings = new Payfast_Payment_Utility_Admin_Settings($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $this->loader->add_action('admin_menu', $plugin_settings, 'setup_plugin_options_menu');
        $this->loader->add_action('admin_init', $plugin_settings, 'initialize_merchant_options');
    }

    private function define_public_hooks() {

        $plugin_public = new Payfast_Payment_Utility_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('init', $plugin_public, 'register_shortcodes');
        $this->loader->add_action('init', $plugin_public, 'register_payment_response');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }

}
