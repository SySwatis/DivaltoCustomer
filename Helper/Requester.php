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
        
        $url = $this->_helperData->getGeneralConfig('test_mode') == 1 ? $this->_helperData->getGeneralConfig('api_url_test') : $this->_helperData->getGeneralConfig('api_url');

        if(!$apiKey) {
            return false;
        }

        if(isset($requestParams)) {

            
            // Address (Billing, Delivery)
            
            $address = array();

            if( isset($requestParams['street_1']) ) {
                $street3 = '';
                if( isset($requestParams['street_2']) ) {
                    $street2 = ', '.$requestParams['street_2'];
                }
                $street3 = '';
                if( isset($requestParams['street_3']) ) {
                    $street3 = ', '.$requestParams['street_3'];
                }
                $address = array (
                    'Rue'=>$requestParams['street_1'].$stree2.$street3,
                    'Ville'=>$requestParams['city'], 
                    'Code_Postal'=>$requestParams['zip'],
                    'Pays'=>$requestParams['country']
                );
            }

            $postData = array(
                'APIKey'=>$apiKey,
                'Numero_Dossier'=>$divaltoStoreId,
                'Email_Client'=>$requestParams['email'],
                // 'Raison_Sociale'=>$requestParams['siret'],
                // 'Titre'=>$requestParams['legal_form'],
                // 'Telephone'=>$requestParams['telephone'],
                // 'Numero_Siret'=>$requestParams['siret'],
                // 'Code_APE'=>$requestParams['ape'],
                // 'Numero_TVA'=>$divaltoTvaIdDefault,
                // 'Adresse_Facturation'=>$address,
                // 'Adresse_Livraison'=>$address,
                // 'Contact'=>array(
                //     'Nom'=>$requestParams['lastname'], 
                //     'Prenom'=>$requestParams['firstname'],
                //     'Telephone'=>$requestParams['telephone'],
                //     'Email'=>$requestParams['email'],
                //     'Fonction'=>''
                // )
            );

        } else {

            return false;
        }

        $this->_curl->post($url, $postData);

        $statusCode = $this->_curl->getStatus();

        $data = json_decode($this->_curl->getBody(), true);

        if ($statusCode !== 200) {

            $logText = '';

            // switch ($statusCode) {
            //     case 400 : 
            //         $logText = 'Incorrect Data'; break;
            //     case 401 : 
            //         $logText = 'Unauthenticated User'; break;
            //     case 404 : 
            //         $logText = 'Page Not Found'; break;
            //     default:
            //         $logText = 'Unknown http status code'; break;
            // }
            
            if ( is_array($data) && isset($data['message']) ) {
                $logText = $data['message'].' '.$statusCode;
            }
            $this->_logger->debug('Observer Requester, ERP query fail : '.$logText);
            return false;
        }

        if ( is_array($data) && isset($data['liste_contact'])) {
            // Filter result by post email
            $divaltoCustomerData = $this->filterByValue($data['liste_contact'], $requestParams['email']);
            $index = array_keys($divaltoCustomerData);
            if( is_array($divaltoCustomerData) && isset($index[0]) && isset($divaltoCustomerData[$index[0]]) ) {
                return array(
                    'group_name'         => $divaltoCustomerData[$index[0]]['code_Client'],
                    'outstanding_status' => $divaltoCustomerData[$index[0]]['autorisation_Paiement']
                );
            }

        } else {
             // throw new LocalizedException(__('ERP resutl fail!'));
            $this->_logger->debug('ERP resutl fail !');
            return false;
        }

        return false;

    }

}