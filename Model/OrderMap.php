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

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Exception;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\Order;

class OrderMap
{
	
    const INCL_TAX_RULE_ORDER = 'INCL_TAX';

	const EXCL_TAX_RULE_ORDER = 'EXCL_TAX';

    const DIVALTO_STATE_PROCESSING = Order::STATE_PROCESSING;
   
	/** @var LoggerInterface */
    private $_logger;

    /** @var */
    private $_orderRepository;

    /** @var */
    private $_helperData;

    public function __construct(
    	OrderRepository $orderRepository,
    	PsrLoggerInterface $logger,
    	\Divalto\Customer\Helper\data $helperData
    ) {
    	$this->_logger = $logger;
    	$this->_orderRepository = $orderRepository;
    	$this->_helperData = $helperData;
    }

    function getCustomerAttributeValue($customerOrder, $attibuteCode)
    {
        if($customerOrder->getCustomAttribute($attibuteCode)) {
            return $customerOrder->getCustomAttribute($attibuteCode)->getValue();
        }
    }

    public function getShipingChargeOrder($order)
    {
        $shippingAmount = null;
        try {
            $shippingAmount = (float)$order->getShippingAmount();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $shippingAmount;
    }

    function create($orderIn,$orderStatus=self::DIVALTO_STATE_PROCESSING)
    {

        // Config

        $divaltoStoreId = $this->_helperData->getGeneralConfig('divalto_store_id');
		$divaltoTaxRuleOrder = $this->_helperData->getGeneralConfig('divalto_tax_rule_order');
        $priceTaxConfig = $divaltoTaxRuleOrder ?? self::EXCL_TAX_RULE_ORDER;

        // Get Order

		$order = $this->_orderRepository->get($orderIn->getId());

		// Get Customer Order

       // if(!$order->getCustomerId()) return;

        $customerOrder = $this->_helperData->getCustomerById($order->getCustomerId());

        // Get Order Items

        $orderItems = $order->getAllItems();

        // Get Billig Address

        $billingAddress = $order->getBillingAddress();

        $billingAddress = $order->getbillingAddress();
        $billingAddressStreets = $billingAddress->getStreet();
        $billingStreet = '';
        $billingStreetSeparator = '';
        
        foreach ($billingAddressStreets as $street) {
            $billingStreet .= $billingStreetSeparator.$street;
            $billingStreetSeparator = ', ';
        }

        $billingAddressData = array(
                'Rue'=>$billingStreet,
                'Ville'=>$billingAddress->getCity(),
                'Code_Postal'=>$billingAddress->getPostcode(),
                'Pays'=>$billingAddress->getCountryId());

        // Get Shippigng Address

        $shippingAddress = $order->getShippingAddress();
        $shippingAddressStreets = $shippingAddress->getStreet();
        $shippingStreet = '';
        $shippingStreetSeparator = '';
        
        foreach ($shippingAddressStreets as $street) {
            $shippingStreet .= $shippingStreetSeparator.$street;
            $shippingStreetSeparator = ', ';
        }

        $shippingAddressData = array(
            	'Rue'=>$shippingStreet,
            	'Ville'=>$shippingAddress->getCity(),
            	'Code_Postal'=>$shippingAddress->getPostcode(),
            	'Pays'=>$shippingAddress->getCountryId());

        // Order Items

        $orderDataItems = array();

        $i=0;

        foreach ($orderItems as $item) {

            if($item->getProductType()==="simple") {

                $itemPrice = $item->getPrice();
                $itemPriceTaxInc =  $priceTaxConfig == self::INCL_TAX_RULE_ORDER ? $item->getPrice() : '';
                $itemPriceTaxExc =  $priceTaxConfig == self::EXCL_TAX_RULE_ORDER ? $item->getPrice() : '';
                
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
            'Adresse_Facturation_Manuelle'=>$billingAddressData,
            'Code_Adresse_Facturation'=>'',
            'Paiement'=>'processing',
            'liste_detail_ligne'=>$orderDataItems,
            'MontantLivraison'=>$this->getShipingChargeOrder($order),
            'Client_Particulier'=>array(
                'Numero_TVA'=>$customerOrder->getTaxvat(),
                'Code_Ape'=>$this->getCustomerAttributeValue($customerOrder,'ape'),
                'Email_Client'=>'',
                'Raison_Sociale'=>$this->getCustomerAttributeValue($customerOrder,'company_name'),
                'Titre'=>$this->getCustomerAttributeValue($customerOrder,'legal_form'),
                'Telephone'=>$shippingAddress->getTelephone(),
                'Contact'=>array(
                    'Nom'=>$order->getCustomerLastname(),
                    'Prenom'=>$order->getCustomerFirstname(),
                    'Telephone'=>$billingAddress->getTelephone(),
                    'Email'=>$order->getCustomerEmail(),
                    'Fonction'=>$order->getCustomerPrefix()
                )
            )
        ];
        
        return $orderData;
    }
}