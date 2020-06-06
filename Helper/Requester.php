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
        $this->_helperData = $helperData;
        $this->curl = $curl;
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

        if(isset($requestParams)) {
        
            $postData = array(
                'APIKey'=>'1234',
                'Numero_Dossier'=>'',// Config
                'Email_Client'=>$requestParams['email'],
                'Raison_Sociale'=>$requestParams['siret'],
                'Titre'=>'',
                'Telephone'=>'',
                'Numero_Siret'=>$requestParams['siret'],
                'Code_APE'=>$requestParams['ape'],
                'Numero_TVA'=>$this->_helperData->siretToVat($requestParams['siret']),
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

        }


        $url = self::ERP_API_BASE_URL_TEST;

        $this->curl->post($url, $postData);
        
        if ($this->curl->getStatus() !== 200) {
            // throw new LocalizedException(__('ERP query fail!'));
            $this->_logger->debug('ERP query fail ! Status 200 false, true : '.$this->curl->getStatus());
            return false;
        }

         if ($this->curl->getStatus() === 200) {
            $this->_logger->debug('ERP query fail 200');
        }
        
        $data = json_decode($this->curl->getBody(), true);

        if (!is_array($data)) {
            // throw new LocalizedException(__('ERP resutl fail!'));
            $this->_logger->debug('ERP query fail ! No array found.');
            return false;
        } else {
            // Filter result by post email
            $divaltoCustomerData = $this->filterByValue($data['liste_contact'], $requestParams['email']);
            $index = array_keys($divaltoCustomerData);
            if( is_array($divaltoCustomerData) && isset($index[0]) && isset($divaltoCustomerData[$index[0]]) ) {
                return array(
                    'groupe_name'           =>      $divaltoCustomerData[$index[0]]['code_Client'],
                    'outstanding_status'    =>      $divaltoCustomerData[$index[0]]['autorisation_Paiement']
                );
            }
        }

        return false;

    }

}