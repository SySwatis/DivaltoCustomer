<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category   Divalto
 * @package    Divalto_Customer
 * @subpackage Helper
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */

namespace Divalto\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;

class Requester extends AbstractHelper
{
    
    const CUSTOMER_GROUP_DEFAULT_NAME = 'General';

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

    // public function filterByValue ($array, $value)
    // {
    //     $like = $value;
    //     return $result = array_filter($array, function ($item) use ($like) {
    //         if (stripos($item['email_Contact'], $like) !== false) {
    //             return true;
    //         }
    //         return false;
    //     });
    // }

    public function getDivaltoCustomerData($postData, $action='ping', $debug = false)
    {

        $apiKey = $this->_helperData->getGeneralConfig('api_key');

        // Config Api Url
        
        $url = $this->_helperData->getGeneralConfig('test_mode') == 1 && $debug == true ? $this->_helperData->getGeneralConfig('api_url_test') : $this->_helperData->getGeneralConfig('api_url');
       
        // Check End Slash

        if(substr($url , -1)!='/'){
            $url .= '/';
        }
        // Add Action to url

        $url .= $action;
        
        // Check Api Key

        if(!$apiKey) {
            return false;
        }

        // Check Data and Data key Email

        if( !isset($postData) && !isset($postData['Contact']['Email']) && $action === 'CreerClient' ) {
            return false;
        }

        if( !isset($postData) && !isset($postData['Client_Particulier']['Contact']['Email']) && $action === 'CreerCommande' ) {
            return false;
        }

        // Content type Json
        
        $dataJson = json_encode($postData); 
        $contentLength = strlen($dataJson);

        // Header

        $headers = ["APIKey" => $apiKey,"Content-Type"=>"application/json","Content-Length"=> $contentLength,"Accept-Encoding"=>"gzip,deflate"];

        // Curl Options

        $sslVerifypeer =  $this->_helperData->getGeneralConfig('ssl_verifypeer') == 1 ? true : false;

        $options = [
            CURLOPT_CUSTOMREQUEST=>"POST",
            CURLOPT_POSTFIELDS=>$dataJson,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_SSL_VERIFYPEER=>$sslVerifypeer
        ];

        $this->_curl->setHeaders($headers);
        $this->_curl->setOptions($options);
        $this->_curl->post($url, $dataJson);

        $statusCode = $this->_curl->getStatus();
        $data = json_decode($this->_curl->getBody(), true);

        $responseText = 'Status: '.$statusCode;

        if ($statusCode !== 200) {
            $responseText .= ' => ';
            switch ($statusCode) {
                case 400 : 
                    $responseText .= 'Incorrect Data'; break;
                case 401 : 
                    $responseText .= 'Unauthenticated User'; break;
                case 404 : 
                    $responseText .= 'Page Not Found'; break;
                default:
                    $responseText .= 'Unknown http status code'; break;
            }
            
            $this->_logger->debug('Observer Requester, ERP query fail : '.$responseText);
        }

        if ( is_array($data) ) {

            // Ping

            if($action == 'ping') {
                return $data;
            }

            // CreerClient

            if($action == 'CreerClient') {

                if( isset($data['liste_contact']) ) {
                    return array(
                        'group_name'         => $data['liste_contact'][0]['code_Client'],
                        'outstanding_status' => 1,  // CB only Divalto/Customer/Model/Config/Source/OutstandingStatus.php
                        'divalto_response' => $responseText,
                        'divalto_extrafield_1'=>'',
                        'divalto_extrafield_2'=>''
                    );
                }

                if( isset($data['liste_contact']) ) {
                    return array(
                        'group_name'         => $data['liste_contact'][0]['code_Client'],
                        'outstanding_status' => 1,  // CB only Divalto/Customer/Model/Config/Source/OutstandingStatus.php
                        'divalto_response' => $responseText,
                        'divalto_extrafield_1'=>'',
                        'divalto_extrafield_2'=>''
                    );
                }

                if( isset($data['message']) ) {
                    return array(
                        'group_name'         => self::CUSTOMER_GROUP_DEFAULT_NAME,
                        'outstanding_status' => 0,  // CB only Divalto/Customer/Model/Config/Source/OutstandingStatus.php
                        'divalto_response' => $responseText.' | '.$data['message'],
                        'divalto_extrafield_1'=>'',
                        'divalto_extrafield_2'=>''
                    );
                }

            }

            // CreerCommande

            if($action == 'CreerCommande') {

                if( isset($data['numero_Commande_Divalto']) ) {
                     return array('comment'=>$data['numero_Commande_Divalto']);
                }

                if( isset($data['message']) ) {
                     return array('comment'=>$responseText.' | '.$data['message']);
                }

            }

        } else {
             // throw new LocalizedException(__('ERP resutl fail!'));
            $this->_logger->debug('ERP result fail !');
            return false;
        }

        return false;

    }

}