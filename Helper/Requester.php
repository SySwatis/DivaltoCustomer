<?php
/**
 * @category   Divalto
 * @package    Divalto_Customer
 * @subpackage Helper
 */

namespace Divalto\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

class Requester extends AbstractHelper
{
    protected $curl;

    protected $_logger;

    const ERP_API_BASE_URL = 'https://127.0.0.1/erp/api/';

    const ERP_API_BASE_URL_TEST = 'http://www.myerp.lan/';

    public function __construct(
        LoggerInterface $logger,
        Curl $curl,
        Context $context
    ) {
        $this->curl = $curl;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    public function filterByValue ($array, $value)
    {
        $like = $value;
        return $result = array_filter($array, function ($item) use ($like) {
        if (stripos($item['email'], $like) !== false) {
            return true;
        }
            return false;
        });
    }

    public function getCustomerErpId($requestParams)
    {

        $requestParamEmail = $requestParams['email'];
        // $requestParamCountry = $requestParams['taxvat'];
        // $requestParamApe = $requestParams['ape'];
        // $requestParamApe = $requestParams['siret'];

        $url = self::ERP_API_BASE_URL_TEST;
        
        if ($this->curl->getStatus() !== 200) {
            // throw new LocalizedException(__('ERP query fail!'));
            $this->_logger->debug('ERP query fail ! Status 200 false');
            return false;
        }

        $this->curl->post($url, $requestParamEmail);
        
        $data = json_decode($this->curl->getBody(), true);

        if (!is_array($data)) {
            // throw new LocalizedException(__('ERP resutl fail!'));
            $this->_logger->debug('ERP query fail ! No array found.');
            return false;
        } else {
            $customerErpId = $this->filterByValue($data, $requestParamEmail);
            $index = array_keys($customerErpId);
            if( is_array($customerErpId) && isset($index[0]) && isset($customerErpId[$index[0]]) ) {
                return $customerErpId[$index[0]]['customer_erp_id'];
            }
        }

        return false;

    }

    public function getGroupName($requestParams) 
    {
        return $this->getCustomerErpId($requestParams);
    }

}