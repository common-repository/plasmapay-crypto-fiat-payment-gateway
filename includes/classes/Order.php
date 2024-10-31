<?php
namespace Plasmapay;

class Order {
    protected $order_id = 0;
    protected $currency = 'EURp';
    protected $total = 0;
    protected $user_id;
    protected $description;

    public function setId($order_id) {
        $this->order_id = $order_id;
    }

    public function setCurrency($currency) {
        $this->currency = $currency;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setDescription($desc) {
        $this->description = $desc;
    }

    public function getId() {
        return $this->order_id;
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getTotal() {
        return $this->total;
    }
    public function getUserId() {
        return $this->user_id;
    }

    public function getDescription() {
        return $this->description;
    }
}