<?php
use Plasmapay\Order;
use Plasmapay\Payment;

class WC_Plasmapay_Gateway extends WC_Payment_Gateway
{
    private $api_key;
    private $sandbox;
    private $currency;
    private $locale;
    private $show_logo;

    public function __construct()
    {
        loadPlasmaPayLibrary();

        $this->id = 'plasmapay_gateway';
        $this->icon = apply_filters('woocommerce_plasmapay_icon', '');
        $this->has_fields = true;
        $this->method_title = _x('PlasmaPay Payment', 'woocommerce');
        $this->method_description = __('PlasmaPay Checkout redirects customers to the PlasmaPay to enter payment details and pay with Visa/MC credit card, cryptocurrency and digital cash. <a href="https://plasmapay.com/">https://plasmapay.com</a>', 'woocommerce');

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->sandbox = $this->get_option('sandbox');
        $this->api_key = $this->get_option('api_key');
        $this->currency = $this->get_option('currency');
        $this->locale = $this->get_option('locale');
        $this->show_logo = $this->get_option('show_logo');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_api_plasmapay_gateway', array($this, 'callback_success'));
    }

    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __( 'Enable/Disable', 'woocommerce' ),
                'type' => 'checkbox',
                'label' => __( 'Enable PlasmaPay Payment', 'woocommerce' ),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __( 'Title', 'woocommerce' ),
                'type' => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                'default' => __( 'PlasmaPay Payment', 'woocommerce' ),
                //'desc_tip'      => true,
            ),
            'api_key' => array(
                'title' => __( 'API key', 'woocommerce' ),
                'type' => 'text',
                'description' => __( 'Get your key by opening a business account   <a href="https://plasmapay.com">Plasmapay</a>', 'woocommerce' ),
                'default' => '',
            ),
        );
    }

    public function payment_fields() {
            ?>
        <!--<img src="<?php echo PLASMAPAY_PATH . 'assets/images/visa-master_card.png'; ?>" alt="" style=""/>-->
            <?php
    }

    public function get_icon() {
        $image_url =  PLASMAPAY_PATH . 'assets/images/visa-master_card.png';
        $icon_html = '<img src="' . $image_url . '" alt="PlasmaPay mark" width="51" height="32" />';
        return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
    }

    public function process_payment( $order_id ) {

        $order = new WC_Order( $order_id );

        $plasmaPayOrder = new Order();
        $plasmaPayOrder->setId($order->get_id());
        $plasmaPayOrder->setCurrency($order->get_currency() . "p");
        $plasmaPayOrder->setTotal($order->get_total());
        $plasmaPayOrder->setUserId($order->get_user_id());
        $plasmaPayOrder->setDescription("Payment");

        $payment = new Payment($this->getAPIKey());
        $payment->setOrder($plasmaPayOrder);

        $response = $payment->submit();

        if ($response->isSuccess()) {
            global $woocommerce;
            $woocommerce->cart->empty_cart();

            if($order->get_status() != 'pending') {
                $order->update_status('pending');
            }
            return array(
                'result' => 'success',
                'redirect' => $response->getRedirectUrl()
            );
        } else if ($response->isError()){
            $order->update_status('failed');
            wc_add_notice(  'Payment failed', 'error' );
            return false;
        } else {
            wc_add_notice(  'Connection error. ('. $response->getMessage() . ')', 'error' );
            return false;
        }
    }

    public function callback_success() {

        $callback_json = @file_get_contents('php://input');
        //file_put_contents('callback_body.log', "\r\n" .$callback_json . "\r\n", FILE_APPEND);

        $callback = json_decode($callback_json, true);

        $response = new \Plasmapay\Response($callback['invoice']);
        $order_id = (int)$response->getOrderId();
        $order = new WC_Order( $order_id );

        if($response->isComplete()) {
            $order->payment_complete($response->getInvoiceId());
        }
    }

    private function getAPIKey() {
        return $this->api_key;
    }

}
