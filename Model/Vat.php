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
 * @subpackage Model
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */

/**
 * Notices :
 * Report Bug 2.3.4
 * https://support.magento.com/hc/en-us/articles/360043471592-Unable-to-validate-VAT-number-Magento-Commerce-Cloud
 * vendor/magento/module-customer/Model/Vat.php
 * https://devblog.lexik.fr/symfony/un-validator-tva-bien-pratique-1123
 * https://fr.wikipedia.org/wiki/Code_Insee
 * https://en.wikipedia.org/wiki/VAT_identification_number
 */

namespace Divalto\Customer\Model;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Exception;
use Magento\Framework\DataObject;
use \Magento\Framework\Webapi\Soap\ClientFactory;

/**
 * Class Vat
 * @package Divalto\Customer\Model
 */
class Vat
{

    /**
     * WSDL of VAT validation service
     *
     */
    const VAT_VALIDATION_WSDL_URL = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * @var \Magento\Framework\Webapi\Soap\ClientFactory; 
     */
     protected $_soapClientFactory;

    /**
     * @var PsrLogger
     */
    protected $_logger;

    public function __construct (
        ClientFactory $soapClientFactory,
        PsrLoggerInterface $log
    )
    {
        $this->_soapClientFactory = $soapClientFactory;
        $this->_logger = $log;
    }

    public function siretToVatNumber($siretIn,$country='FR')
    {
        $siret = str_replace(array(' ', '.', '-', ',', ', '), '', $siretIn);
        if(!is_numeric($siret)) return;
        $siren = substr($siret, 0, 9);
        $key = (12 + 3 * ($siren%97))%97;
        $keyFormat = sprintf("%02d", $key);
        return $country.$keyFormat.$siren;
    }

    public function checkVatNumber($vatNumberIn, $countryCodeIn=false)
    {

        // Default response

        $response = new DataObject([ 'is_valid' => false, 'message'=>__('Invalid VAT Number, error unknown') ]);
            
        if (!extension_loaded('soap')) {
            $this->_logger->critical(new LocalizedException(__('PHP SOAP extension is required.')));
            return $response;
        }

        // Serializing
        
        $vat_number   = str_replace(array(' ', '.', '-', ',', ', '), '', $vatNumberIn);

        // Get Country Code
        
        $countryCode  = substr($vat_number, 0, 2);
        
        // Get Vat Number
        
        $vatNumber    = substr($vat_number, 2);

        // Check Country Code Format

        if (strlen($vatNumber) < 5 || strlen($countryCode) != 2 || is_numeric(substr($countryCode, 0, 1)) || is_numeric(substr($countryCode, 1, 2))) {
            $response->setIsValid(false);
            $response->setMessage(__('Your VAT Number syntax is not correct'));
            return $response;
        }

        // Check with Country Code in

        if ( $countryCodeIn && $countryCodeIn != $countryCode ) {
            $response->setIsValid(false);
            $response->setMessage(__('Your VAT Number is not valid for the selected country'));
            return $response;
        }

        // Check Vat Number WS Europa VAT (Soap)

        try {

            $params = array('countryCode' => $countryCode, 'vatNumber' => $vatNumber);

            $wsdl = self::VAT_VALIDATION_WSDL_URL;

            $soapClient = $this->_soapClientFactory->create($wsdl);
            $result = $soapClient->checkVat($params);
            
            if ( !$result->valid ) {
                $response->setIsValid(false);
                $response->setMessage(__('Invalid VAT Number (Europa VAT)'));
            } else {
                $response->setIsValid(true);
                $response->setMessage(__('This account was validate with this VAT Number:'));
            }

        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        return  $response;

        
    }
}