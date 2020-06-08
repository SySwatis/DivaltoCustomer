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
    protected $_curl;

    protected $_logger;

    protected $_helperData;

    public function __construct(
        \Divalto\Customer\Helper\Data $helperData,
        LoggerInterface $logger,
        Curl $curl,
        Context $context
    ) {
        $this->_helperData = $helperData;
        $this->_curl = $curl;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    public function filterByValue ($array, $value)
    {
        $like = $value;
        return $result = array_filter($array, function ($item) use ($like) {
            if (stripos($item['email_Contact'], $like) !== false) {
                return true;
            }
            return false;
        });
    }

    public function getDivaltoCustomerData($requestParams)
    {
        
        // Config

        $apiKey = $this->_helperData->getGeneralConfig('api_key');
        $divaltoStoreId = $this->_helperData->getGeneralConfig('divalto_store_id');
        $divaltoTvaIdDefault = $this->_helperData->getGeneralConfig('divalto_tva_id_default');

        // Config Api Url
        
        $url = $this->_helperData->getGeneralConfig('test_mode') === 1 ? $this->_helperData->getGeneralConfig('api_url_test') : $this->_helperData->getGeneralConfig('api_url');

        if(!$apiKey) {
            return false;
        }

        if(isset($requestParams)) {
        
            $postData = array(
                'APIKey'=>$apiKey,
                'Numero_Dossier'=>$divaltoStoreId,
                'Email_Client'=>$requestParams['email'],
                'Raison_Sociale'=>$requestParams['siret'],
                'Titre'=>'',
                'Telephone'=>'',
                'Numero_Siret'=>$requestParams['siret'],
                'Code_APE'=>$requestParams['ape'],
                'Numero_TVA'=>$divaltoTvaIdDefault,
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
        } else {
            return false;
        }

        $this->_curl->post($url, $postData);

        if ($this->_curl->getStatus() !== 200) {

            $statusCode = $this->_curl->getStatus();
            switch ($statusCode) {
                case 400 : 
                    $logText = 'Incorrect Data'; break;
                case 401 : 
                    $logText = 'Unauthenticated User'; break;
                case 404 : 
                    $logText = 'Page Not Found'; break;
                default:
                    $logText = 'Unknown http status code'; break;
            }
            $this->_logger->debug('ERP query fail '.$logText.' : '.htmlentities($statusCode));

            return false;
        }
        
        $data = json_decode($this->_curl->getBody(), true);

        if (!is_array($data)) {
            // throw new LocalizedException(__('ERP resutl fail!'));
            $this->_logger->debug('ERP resutl fail !');
            return false;

        } else {
            // Filter result by post email
            $divaltoCustomerData = $this->filterByValue($data['liste_contact'], $requestParams['email']);
            $index = array_keys($divaltoCustomerData);
            if( is_array($divaltoCustomerData) && isset($index[0]) && isset($divaltoCustomerData[$index[0]]) ) {
                return array(
                    'group_name'         => $divaltoCustomerData[$index[0]]['code_Client'],
                    'outstanding_status' => $divaltoCustomerData[$index[0]]['autorisation_Paiement']
                );
            }
        }

        return false;

    }

}