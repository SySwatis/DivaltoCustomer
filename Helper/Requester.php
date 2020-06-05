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

     protected $_helperData;

    const ERP_API_BASE_URL = 'https://127.0.0.1/erp/api/';

    const ERP_API_BASE_URL_TEST = 'http://www.myerp.lan/';

    public function __construct(
        \Divalto\Customer\Helper\Data $helperData,
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
        
        $postData = array(
            'Numero_Dossier'=>'',
            'Email_Client'=>$requestParams['email'],
            'Raison_Sociale'=>$requestParams['siret'],
            'Titre'=>'',
            'Telephone'=>'',
            'Numero_Siret'=>$requestParams['siret'],
            'Code_APE'=>$requestParams['ape'],
            'Numero_TVA'=>'',
            'Adresse_Facturation'=>array(
                'Rue'=>'',
                'Ville'=>'', 
                'Code_Postal'=>''
            ),
            'Adresse_Livraison'=>array(
                'Rue'=>'',
                'Ville'=>'',
                'Code_Postal'=>''
            ),
            'Contact'=>array(
                'Nom'=>$requestParams['lastname'], 
                'Prenom'=>$requestParams['firstname'], 
                'Email'=>$requestParams['email']
            )
        );

        $url = self::ERP_API_BASE_URL_TEST;
        
        if ($this->curl->getStatus() !== 200) {
            // throw new LocalizedException(__('ERP query fail!'));
            $this->_logger->debug('ERP query fail ! Status 200 false');
            return false;
        }

        $this->curl->post($url, $postData);
        
        $data = json_decode($this->curl->getBody(), true);

        if (!is_array($data)) {
            // throw new LocalizedException(__('ERP resutl fail!'));
            $this->_logger->debug('ERP query fail ! No array found.');
            return false;
        } else {
            $customerErpId = $this->filterByValue($data, $requestParamEmail);
            $index = array_keys($customerErpId);
            if( is_array($customerErpId) && isset($index[0]) && isset($customerErpId[$index[0]]) ) {
                return $customerErpId[$index[0]]['code_Client'];
            }
        }

        return false;

    }

    public function getGroupName($requestParams) 
    {
        return $this->getCustomerErpId($requestParams);
    }

}