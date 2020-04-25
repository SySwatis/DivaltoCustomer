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
 */
namespace Divalto\Customer\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Divalto\Customer\Helper
 */
class Validator extends AbstractHelper
{

    public function __construct(
        $vat_number = 'vat_number', 
        $country = 'country', 
        $options = array(), 
        $messages = array()
    )
    {
        $this->addOption('vat_number', $vat_number);
        $this->addOption('country', $country);
        $this->addOption('throw_global_error', false);
        $this->messages = array_merge(
        $this->messages,
            array(
              'invalid_syntax'=>'Your VAT Number syntax is not correct. You should have something like this: BE805670816B01',
              'invalid_country'=>'Your VAT Number is not valid for the selected country.',
              'invalid'=>sprintf('Invalid VAT Number. Check the validity on the customer VAT Number via Europa VAT Number validation webservice', 'http://ec.europa.eu/taxation_customs/vies/lang.do?fromWhichPage=vieshome'),
            )
        );
        parent::__construct(null, $options, $messages);
    }

    public function doClean($values)
    {
        if (null === $values)
        {
          $values = array();
        }

        if (!is_array($values))
        {
          throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
        }

        $vatnumber  = isset($values[$this->getOption('vat_number')]) ? $values[$this->getOption('vat_number')] : null;
        $country = isset($values[$this->getOption('country')]) ? $values[$this->getOption('country')] : null;

        //on récupère la validité du numéro de TVA via le webService
        $valid = $this->haleValidateVAT(array('vatnumber' => $vatnumber, 'country' => $country));

        //si le résultat n'est pas valide, on throw l'erreur correspondante
        if (!$valid['result'])
        {
          throw new sfValidatorError($this, $valid['error'], array('value' => $vatnumber));

          if ($this->getOption('throw_global_error'))
          {
            throw $error;
          }

          //l'erreur s'applique sur le champ vat_number
          throw new sfValidatorErrorSchema($this, array($this->getOption('vat_number') => $error));
        }

        //si valide, on retourne les valeurs
        return $values;
    }

    /**
    * vérifie la validité du numéro de TVA en prenant en compte le pays donné
    *
    * @param array $args
    * @return array
    */
    protected function haleValidateVAT($args = array()) {
        if ( '' != $args['vatnumber'] )
        {
            // on sérialize le numéro TVA
            $vat_number   = str_replace(array(' ', '.', '-', ',', ', '), '', $args['vatnumber']);
            // on récupère le code pays
            $countryCode  = substr($vat_number, 0, 2);
            //on récupère le numéro TVA
            $vatNumber    = substr($vat_number, 2);

            //on vérifie la syntaxe du numéro
            if (strlen($countryCode) != 2 || is_numeric(substr($countryCode, 0, 1)) || is_numeric(substr($countryCode, 1, 2)))
            {
                $error = array('result' => false, 'error'=>'invalid_syntax');
                return $error;
            }

            //on vérifie que le pays correspond bien au pays indiqué dans le numéro de TVA
            if ( $args['country'] != $countryCode )
            {
                $error = array('result' => false, 'error'=>'invalid_country');
                return $error;
            }

            //appelle le webservice
            $client = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
            $params = array('countryCode' => $countryCode, 'vatNumber' => $vatNumber);
            $result = $client->checkVat($params);

            //vérifie la validité et renvoie l'erreyr correspondante
            if ( !$result->valid )
            {
                $error = array('result' => false, 'error'=>'invalid');
                return $error;
            }else{
                return array('result' => true);
            }
        }
        return array('result' => false, 'error'=>'required');
    }
}