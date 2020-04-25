<?php
namespace Divalto\Customer\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
class Requester extends AbstractHelper
{
    protected $curl;
    const ERP_API_BASE_URL = 'https://127.0.0.1/erp/api/';
    public function __construct(
        Context $context,
         \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        $this->curl = $curl;
        parent::__construct($context);
    }

    public function createCustomer(array $customer)
    {
        $url = self::ERP_API_BASE_URL . 'customer/create';
        $this->curl->post($url, $customer);
        if ($this->curl->getStatus() !== 200) {
            throw new LocalizedException(__('ERP query fail!'));
        }
        $response = json_decode($this->curl->getBody(), true);
        if ($response['success']) {
            return $response['customer_erp_id'];   
        } else {
            throw new LocalizedException(__($response['error_message']));
        }
    }
    
    public function updateCustomer(array $customer)
    {
        $url = self::ERP_API_BASE_URL . 'customer/update';
        if (!$customer['customer_erp_id']) {
            throw new LocalizedException(__('Invalid customer ERP ID!'));
        }
        $this->curl->post($url, $customer);
        if ($this->curl->getStatus() !== 200) {
            throw new LocalizedException(__('ERP query fail!'));
        }
        $response = json_decode($this->curl->getBody(), true);
        if ($response['success']) {
            return $response['customer_erp_id'];
        } else {
            throw new LocalizedException(__($response['error_message']));
        }
    }
}