<?php

class Payfast_Payment_Utility_Admin_Settings {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function setup_plugin_options_menu() {

        add_plugins_page(
                'PayFast Payment Utility', 'PayFast Payment Utility', 'manage_options', 'payfast_payment_options', array($this, 'render_settings_page_content')
        );
    }

    public function default_merchant_options() {

        $defaults = array(
            'merchant_id' => '',
            'secured_key' => '',
            'merchant_name' => '',
        );

        return $defaults;
    }

    public function render_settings_page_content($active_tab = '') {
        ?>

        <div class="wrap">

            <h2><?php _e('PayFast Payment Utility', 'payfast-payment-utility'); ?></h2>
            <?php settings_errors(); ?>

            <?php
            if (isset($_GET['tab'])) {
                $active_tab = $_GET['tab'];
            } else if ($active_tab == 'social_options') {
                $active_tab = 'merchant_options';
            }
            ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=payfast_payment_options&tab=merchant_options" class="nav-tab <?php echo $active_tab == 'merchant_options' ? 'nav-tab-active' : ''; ?>"><?php _e('Display Options', 'payfast-payment-utility'); ?></a>

            </h2>

            <form method="post" action="options.php">
                <?php
                settings_fields('payfast_payment_merchant_options');
                do_settings_sections('payfast_payment_merchant_options');

                submit_button();
                ?>
            </form>

        </div>
        <?php
    }

    public function initialize_merchant_options() {

        if (false == get_option('payfast_payment_merchant_options')) {
            $default_array = $this->default_merchant_options();
            add_option('payfast_payment_merchant_options', $default_array);
        }

        add_settings_section(
                'general_settings_section', 
                __('Display Options', 'payfast-payment-utility'), 
                array($this, 'general_options_callback'), 
                'payfast_payment_merchant_options'
        );

        add_settings_field(
                'merchant_id', __('Merchant ID', 'payfast-payment-utility'), array($this, 'merchant_id_callback'), 'payfast_payment_merchant_options', 'general_settings_section', array(
            __('', 'payfast-payment-utility'),
                )
        );

        add_settings_field(
                'secured_key', __('Secured Key', 'payfast-payment-utility'), array($this, 'secured_key_callback'), 'payfast_payment_merchant_options', 'general_settings_section', array(
            __('', 'payfast-payment-utility'),
                )
        );

        add_settings_field(
                'merchant_name', __('Merchant Name', 'payfast-payment-utility'), array($this, 'merchant_name_callback'), 'payfast_payment_merchant_options', 'general_settings_section', array(
            __('This name will be displayed on PayFast Web Checkout', 'payfast-payment-utility'),
                )
        );
        
         add_settings_field(
                'secret_word', __('Secret Word For Reponse', 'payfast-payment-utility'), array($this, 'secret_word_callback'), 'payfast_payment_merchant_options', 'general_settings_section', array(
            __('(optional)', 'payfast-payment-utility'),
                )
        );
        
        add_settings_field(
                'page_description', __('Page Description', 'payfast-payment-utility'), 
                array($this, 'page_description_callback'), 
                'payfast_payment_merchant_options', 
                'general_settings_section', array(
            __('This text will be displayed on payment page', 'payfast-payment-utility'),
                )
        );
        
        add_settings_field(
                'payfast_success_email', __('Send Payment Notification Email', 'payfast-payment-utility'), array($this, 'payfast_success_email_callback'), 'payfast_payment_merchant_options', 'general_settings_section', array(
            __('(optional)', 'payfast-payment-utility'),
                )
        );
        
        add_settings_field(
                'payfast_plugin_help', __('Usage', 'payfast-payment-utility'), array($this, 'payfast_plugin_help_email_callback'), 'payfast_payment_merchant_options', 'general_settings_section', array(
            __('', 'payfast-payment-utility'),
                )
        );
        
        register_setting(
                'payfast_payment_merchant_options', 'payfast_payment_merchant_options'
        );
    }

    public function merchant_id_callback($args) {
        $options = get_option('payfast_payment_merchant_options');

        $html = '<input type="text" id="merchant_id" name="payfast_payment_merchant_options[merchant_id]" value="' . (isset($options['merchant_id']) ? $options['merchant_id'] : '' ) . '"/>';
        $html .= '<label for="merchant_id">&nbsp;' . $args[0] . '</label>';

        echo $html;
    }

    public function secured_key_callback($args) {

        $options = get_option('payfast_payment_merchant_options');

        $html = '<input type="text" id="secured_key" name="payfast_payment_merchant_options[secured_key]" value="' . (isset($options['secured_key']) ? $options['secured_key'] : '' ) . '"/>';
        $html .= '<label for="secured_key">&nbsp;' . $args[0] . '</label>';

        echo $html;
    }
    
    public function page_description_callback($args){
        
        $options = get_option('payfast_payment_merchant_options');

        $html = '<textarea cols="40" rows="5" id="description" name="payfast_payment_merchant_options[description]"/>'
                . (isset($options['description']) ? $options['description'] : '' )
                . '</textarea>';
        $html .= '<label for="description">&nbsp;' . $args[0] . '</label>';

        echo $html;
        
    }

    public function merchant_name_callback($args) {

        $options = get_option('payfast_payment_merchant_options');

        $html = '<input type="text" id="merchant_name" name="payfast_payment_merchant_options[merchant_name]" value="' . (isset($options['merchant_name']) ? $options['merchant_name'] : 0 ) . '"/>';
        $html .= '<label for="merchant_name">&nbsp;' . $args[0] . '</label>';

        echo $html;
    }
    
     public function secret_word_callback($args) {

        $options = get_option('payfast_payment_merchant_options');

        $html = '<input type="text" id="secret_word" name="payfast_payment_merchant_options[secret_word]" value="' . (isset($options['secret_word']) ? $options['secret_word'] : '' ) . '"/>';
        $html .= '<label for="secret_word">&nbsp;' . $args[0] . '</label>';

        echo $html;
    }
    
    public function payfast_success_email_callback($args) {

        $options = get_option('payfast_payment_merchant_options');

        $html = '<input type="email" placeholder="email@example.com" id="payfast_success_email" name="payfast_payment_merchant_options[payfast_success_email]" value="' . (isset($options['payfast_success_email']) ? $options['payfast_success_email'] : '' ) . '"/>';
        $html .= '<label for="secret_word">&nbsp;' . $args[0] . '</label>';

        echo $html;
    }
    
       public function payfast_plugin_help_email_callback($args) {

        $options = get_option('payfast_payment_merchant_options');

        $html = 'Insert shortcode "<strong>[payfast_payment_widget]</strong>" in any page or post to enable the payment widget. ';
        $html .= '<label for="secret_word">&nbsp;' . $args[0] . '</label>';

        echo $html;
    }
    
    public function general_options_callback($args){
        echo "Merchant Settings";
    }
    
    

}
