<?php
namespace Plasmapay;

class Payment {
    protected $api_key;
    protected $cancelUrl;
    protected $notificationUrl;
    protected $order;
    protected $returnUrl;

    const URL_INVOISES = "https://app.plasmapay.com/business/api/v1/public/invoices";

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    protected function _apiRequest() {

        $headers = array(
            'Content-type'  => 'application/json',
            'Authorization' => 'Bearer ' . $this->api_key
        );

        if (!empty($this->order)) {
            $order = $this->getOrder();
            $postfields = array (
                "merchantId" => (string)$order->getId(),
                "currencyCode" => $order->getCurrency(),
                "amount" => $order->getTotal(),
                "description" => $order->getDescription(),
                "accountId" => (string)$order->getUserId()
            );
            $body = apply_filters('convertkit-call-args', $postfields);
        } else {
            throw new \Exception("Request error: order not exisrs");
        }

        $args = array(
            'method'      => 'POST',
            'body'        => json_encode($body),
            'headers'     => $headers,
        );

        $request = wp_remote_request(self::URL_INVOISES, $args);

        if ($request === false) {
            throw new \Exception("Request error");
        }

        $response = json_decode($request['body']);

        if ($response->code == "400") {
            throw new \Exception($response->message);
        }

        return $response;
    }

    public function submit() {
        try {
            $response = $this->_apiRequest();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $response = array("errors" => $msg, "message" => $msg);
        }
        return new Response($response);
    }

    public function setCancelUrl($url) {
        $this->cancelUrl = $url;
        return $this;
    }

    public function setNotificationUrl($url) {
        $this->notificationUrl = $url;
        return $this;
    }

    public function setOrder(Order $order) {
        $this->order = $order;
        return $this;
    }

    public function setReturnUrl($url) {
        return $this->returnUrl;
    }

    public function getCancelUrl() {
        return $this->cancelUrl;
    }

    public function getNotificationUrl() {
        return $this->notificationUrl;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getReturnUrl() {
        return $this->returnUrl;
    }
}