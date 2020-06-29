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
 
namespace Divalto\Customer\Model;

use Magento\Sales\Model\OrderRepository;
use \Magento\Tax\Model\Config;
use Psr\Log\LoggerInterface;

class OrderMap
{
	

	const TAX_RULE_ORDER = 'HT';

	/** @var LoggerInterface */
    private $_log;

    /** @var */
    private $_orderRepository;

    /** @var */
    private $_helperData;

    public function __construct(
    	OrderRepository $orderRepository,
    	Config $configTax,
    	LoggerInterface $log,
    	\Divalto\Customer\Helper\data $helperData
    ) {
    	$this->_log = $log;
    	$this->_orderRepository = $orderRepository;
    	$this->_configTax = $configTax;
    	$this->_helperData = $helperData;
    }

    function getCustomerAttributeValue($customerOrder, $attibuteCode)
    {
        if($customerOrder->getCustomAttribute($attibuteCode)) {
            return $customerOrder->getCustomAttribute($attibuteCode)->getValue();
        }
    }

    function create($orderId) {

        // Config

        $divaltoStoreId = $this->_helperData->getGeneralConfig('divalto_store_id');
		$divaltoTaxRuleOrder = $this->_helperData->getGeneralConfig('divalto_tax_rule_order');
        $priceTaxConfig = $divaltoTaxRuleOrder ?? self::TAX_RULE_ORDER;

        // Get Order

		$order = $this->_orderRepository->get($orderId);

		// Get Customer Order

        $customerOrder = $this->_helperData->getCustomerById($order->getCustomerId());

        // Get Order Items

        $orderItems = $order->getAllItems();

        // Get Shippigng Address

        $shippingaddress = $order->getShippingAddress();
        $shippingaddressStreets = $shippingaddress->getStreet();
        $shippingStreet = '';
        $shippingStreetSeparator = '';
        
        foreach ($shippingaddressStreets as $street) {
            $shippingStreet .= $shippingStreetSeparator.$street;
            $shippingStreetSeparator = ', ';
        }

        $shippingAddressData = array(
            	'Rue'=>$shippingStreet,
            	'Ville'=>$shippingaddress->getCity(),
            	'Code_Postal'=>$shippingaddress->getPostcode(),
            	'Pays'=>$shippingaddress->getCountryId());

        // Order Items

        $orderDataItems = array();

        $i=0;

        foreach ($orderItems as $item) {

            if($item->getProductType()==="simple") {

                $itemPrice = $item->getPrice();
                $itemPriceTaxInc =  $priceTaxConfig == 'HT' ? $item->getPrice() : '';
                $itemPriceTaxExc =  $priceTaxConfig == 'TTC'? $item->getPrice() : '';
                
                $orderDataItems[$i]['SKU']=$item->getSku();
                $orderDataItems[$i]['Quantite_Commandee']=$item->getQtyOrdered();
                $orderDataItems[$i]['Prix_Unitaire_TTC']=$itemPriceTaxInc;
                $orderDataItems[$i]['Prix_Unitaire_HT']=$itemPriceTaxExc;
                $orderDataItems[$i]['Montant_Ligne']=$itemPrice*$item->getQtyOrdered();

                $i++;
            }
        }

        // Group Code (Code Client Divalto)

        $groupCode = $this->_helperData->getGroupById($customerOrder->getGroupId());

        // Order Data (Divalto Mapping)

        $orderData = [
            'Numero_Dossier'=>$divaltoStoreId,
            'Numero_Commande_Magento'=>$order->getIncrementId(),
            'Email_Client_Cde'=>$order->getCustomerEmail(),
            'Code_Client_Divalto'=>$groupCode,
            'Code_Adresse_Livraison'=>'',
            'Adresse_Livraison_Manuelle'=>$shippingAddressData,
            'Code_Adresse_Facturation'=>'',
            'Paiement'=>'processing',
            'liste_detail_ligne'=>$orderDataItems,
            'Client_Particulier'=>array(
                'Email_Client'=>'',
                'Raison_Sociale'=>$this->getCustomerAttributeValue($customerOrder,'company_name'),
                'Titre'=>$this->getCustomerAttributeValue($customerOrder,'legal_form'),
                'Telephone'=>$shippingaddress->getTelephone(),
                'Contact'=>array(
                    'Nom'=>'',
                    'Prenom'=>'',
                    'Telephone'=>'',
                    'Email'=>'',
                    'Fonction'=>''
                )
            )
        ];
        
        return $orderData;
    }
}