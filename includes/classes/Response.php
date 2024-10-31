<?php
namespace Plasmapay;

class Response {
    protected $id;
    protected $amount;
    protected $currencyCode;
    protected $status;
    protected $extId;
    protected $createdAt;
    protected $link;
    protected $metadata;
    protected $merchantId;
    protected $description;
    protected $accountId;
    protected $errors;
    protected $message;

    public function __construct($data)
    {
        $this->setFields($data);
    }

    public function getInvoiceId() {
        return $this->id;
    }

    public function getOrderId() {
        return $this->merchantId;
    }

    public function getRedirectUrl() {
        return $this->link;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getMessage() {
        return $this->message;
    }

    public function isSuccess () {

        return in_array($this->status, ["new", "success", "pending"]) ? true : false;
    }

    public function isComplete () {

        return $this->status == "success" ? true : false;
    }

    public function isError () {

        return in_array($this->status, ["failed", "canceled", "deleted"]) ? true : false;
    }

    public function setFields($data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}