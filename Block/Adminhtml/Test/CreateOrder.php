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
 * @subpackage Backend\Block
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */
namespace Divalto\Customer\Block\Adminhtml\Test;

use Magento\Backend\Block\Template\Context;

class CreateOrder extends \Magento\Backend\Block\Template
{
	protected $_helperData;

    protected $_helperRequester;

	 public function __construct(
        \Divalto\Customer\Helper\Data $helperData,
        \Divalto\Customer\Helper\Requester $helperRequester,
        Context $context
    )
    {
        $this->_helperData = $helperData;
        $this->_helperRequester = $helperRequester;
        parent::__construct($context);
    }

    function responseUrlTest() {
        $emailTest = $this->_helperData->getGeneralConfig('email_test');
        $codeTest = $this->_helperData->getGeneralConfig('code_test');
        $postData = [
            'Numero_Dossier'=>'1',
            'Numero_Commande_Magento'=>'000001',
            'Email_Client_Cde'=>$emailTest,
            'Code_Client_Divalto'=>$codeTest,
            'Code_Adresse_Livraison'=>'',
            'Adresse_Livraison_Manuelle'=>array('Rue'=>'37 RUE MARYSE BASTIE','Ville'=>'LYON','Code_Postal'=>'69008','Pays'=>'FR'),
            'Code_Adresse_Facturation'=>'',
            'Paiement'=>'processing',
            'liste_detail_ligne'=>array(array('SKU'=>'00001AIBN','Quantite_Commandee'=>'10','Prix_Unitaire_TTC'=>'','Prix_Unitaire_HT'=>'100','Montant_Ligne'=>'1000')),
                'Client_Particulier'=>array(
                    'Email_Client'=>'','Raison_Sociale'=>'POLAT','Titre'=>'SAS','Telephone'=>'0610158941',
                    'Contact'=>array('Nom'=>'','Prenom'=>'','Telephone'=>'','Email'=>'muratk21@hotmail.com','Fonction'=>'')
                )
        ];
        // if( isset($_GET['OrderId']) && is_numeric($_GET['OrderId']) ) {
        //     $postData = $this->_orderMap->create($_GET['OrderId']);
        // }
        return array('Url Test'=>$this->_helperData->getGeneralConfig('api_url_test'),'Response Api'=>$this->_helperRequester->getDivaltoCustomerData($postData, $this->_helperRequester::ACTION_CREATE_ORDER, true));
    }
    
    function responseUrlProd() {
        // $postData = array();
        // return array('Url Prod'=>$this->_helperData->getGeneralConfig('api_url'),'Response Api'=>$this->_helperRequester->getDivaltoCustomerData($postData, 'CreerCommande'));
    }
}