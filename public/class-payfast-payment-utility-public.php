<?php

class Payfast_Payment_Utility_Public {

    private $plugin_name;
    private $version;
    private $merchantOptions;
    private $_payfastTokenUrl = 'https://ipg1.apps.net.pk/Ecommerce/api/Transaction/GetAccessToken?MERCHANT_ID=%s&SECURED_KEY=%s';
    private $_payfastWebcheckout = 'https://ipg1.apps.net.pk/Ecommerce/api/Transaction/PostTransaction';
    private $_payfastAccessToken;
    private $_basketId;
    private $_paymentResponse;

    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/payfast-payment-utility-public.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/payfast-payment-utility-public.js', array('jquery'), $this->version, false);
    }

    public function register_shortcodes() {
        add_shortcode('payfast_payment_widget', array($this, 'display_payment_page'));
    }

    public function display_payment_page($atts = array(), $contents = '', $tag = '') {
        
        $this->process_payment_response();
        $this->merchantOptions = get_option('payfast_payment_merchant_options');
        $merchantId = $this->merchantOptions['merchant_id'];
        $securedKey = $this->merchantOptions['secured_key'];
        $this->_payfastAccessToken = $this->getPayFastAccessToken($merchantId, $securedKey);

        include "partials/payfast-payment-utility-public-display.php";
    }

    private function getPayFastAccessToken($merchant_id, $secured_key) {

        $this->_payfastTokenUrl = sprintf($this->_payfastTokenUrl, $merchant_id, $secured_key);

        $response = $this->curl_request($this->_payfastTokenUrl);
        $response_decode = json_decode($response);

        if (isset($response_decode->ACCESS_TOKEN)) {
            return $response_decode->ACCESS_TOKEN;
        }
        return;
    }

    private function curl_request($tokenUrl) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'application/json; charset=utf-8    '
        ));
		curl_setopt($ch,CURLOPT_USERAGENT,'Wordpress Payment Utility APPS PayFast Plugin');
        $certificate = plugin_dir_path(__FILE__) . '/partials/cacert.pem';
        if (file_exists($certificate)) {
            curl_setopt($ch, CURLOPT_CAINFO, $certificate);
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function generateBasketId() {

        $sitename = str_replace(" ", "", get_bloginfo());
        $sitename = str_replace("-", "", $sitename);
        
        $sitename = str_replace("&",'', $sitename);
        $sitename = str_replace('amp;','', $sitename);
        $sitename = str_replace('\"','', $sitename);
        $sitename = str_replace("\'",'', $sitename);
        $sitename = substr($sitename, 0, 5);
        $sitename .= date('YmdHi');
        
        return $sitename;
    }

    public function register_payment_response() {
        add_filter('query_vars', array($this, 'add_payfast_response_vars'));
    }

    public function add_payfast_response_vars($vars) {
        /**
         * Returned params from PayFast response
         */
        $vars[] = "transaction_id";
        $vars[] = "basket_id";
        $vars[] = "err_msg";
        $vars[] = "err_code";
        $vars[] = "Rdv_Message_Key";
        $vars[] = "responseKey";
        $vars[] = "txnamt";
        return $vars;
    }

    public function process_payment_response() {



        $transactionid = get_query_var('transaction_id');
        $basketid = urldecode(get_query_var('basket_id'));
        $errmsg = get_query_var('err_msg');
        $errcode = get_query_var('err_code');
        $rdv = get_query_var('Rdv_Message_Key');
        $response = get_query_var('responseKey');
        $txnamt = get_query_var('txnamt');

        if ($basketid != '' && $errcode != '') {
            $this->_paymentResponse = [
                'response' => true,
                'basket_id' => $basketid,
            ];
            
            
            if ($errcode == '00' || $errcode == '000') {
                
                $message = "Dear Administrator

A payment transaction was done via PayFast. Transaction was successful.

Total Amount: %s /-
Payment Reference (Basket ID): %s
PayFast Transaction ID: %s
PayFast RDV Message Key: %s

Thank You";
                $this->merchantOptions = get_option('payfast_payment_merchant_options');
                $message = sprintf($message, $txnamt, $basketid, $transactionid, $rdv);
                
                $this->_paymentResponse['success'] = true;
                if (isset($this->merchantOptions['payfast_success_email'])) {
                    if ($this->merchantOptions['payfast_success_email'] != '') {
                        wp_mail($this->merchantOptions['payfast_success_email'], 'Payment Received via PayFast', $message, $headers = '');
                    }
                }
            } else {
                $this->_paymentResponse['success'] = false;
            }
        }
    }

}
