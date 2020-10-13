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


use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Api\GroupRepositoryInterface;

/**
 * Class Data
 * @package Divalto\Customer\Helper
 */
class Data extends AbstractHelper
{

    const TAX_CLASS_DEFAULT_ID = 3;

    const XML_PATH_DIVALTO_CUSTOMER = 'divalto_customer/';

    const DIVALTO_DIR = 'pub/media/wysiwyg/divalto/';
	
    /**
     * @var
     */
    protected $_driverFile;

    /**
     * @var
     */
    protected $_groupFactory;

    /**
     * @var
     */
    protected $_vatModel;

    /**
     * @var
     */
    protected $_customerSession;

    /**
     * @var
     */
    protected $_customerRepository;

    /**
     * @var
     */
    protected $_logger;

    /**
     * @var  \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var
     */
    protected $_coreSession;


	public function __construct (
        File $driverFile,
        StoreManagerInterface $storeManager,
        GroupFactory $groupFactory,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        PsrLoggerInterface $logger,
        GroupRepositoryInterface $groupRepository,
        SessionManagerInterface $coreSession,
		Context $context
	)
    {
        $this->_driverFile = $driverFile;
        $this->_storeManager = $storeManager;
		$this->_groupFactory = $groupFactory;
        $this->_customerSession = $customerSession;
        $this->_customerRepository = $customerRepository;
        $this->_logger = $logger;
        $this->_groupRepository = $groupRepository;
        $this->_coreSession = $coreSession;
		parent::__construct($context);
    }


    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    public function getGroupId() 
    {
        return $this->_customerSession->getCustomer()->getGroupId();
    }

    public function getGroupCode() 
    {
        return  $this->_groupRepository->getById($this->_customerSession->getCustomer()->getGroupId())->getCode();
    }
    public function getGroupById($groupId) 
    {
        return  $this->_groupRepository->getById($groupId)->getCode();
    }

    public function getDivaltoInvoiceDir($groupName) 
    {
        return self::DIVALTO_DIR . $groupName . '/invoice';
    }

    public function getDivaltoInvoiceDirSession() 
    {
        return $this->getDivaltoInvoiceDir($this->getGroupCode());
    }

    public function setSessionDivaltoData($value)
    {
        $this->_coreSession->start();
        $this->_coreSession->setSessionDivaltoData($value);
     }

    public function getSessionDivaltoData()
    {
        $this->_coreSession->start();
        return $this->_coreSession->getSessionDivaltoData();
    }

    public function unsSessionDivaltoData()
    {
        $this->_coreSession->start();
        return $this->_coreSession->unsSessionDivaltoData();
    }

    /**
     * Returns config value
     *
     * @return string
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Returns general config value
     *
     * @return string
     */
    public function getGeneralConfig($field, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DIVALTO_CUSTOMER .'general/'. $field, $storeId);
    }

    public function getCustomerGroupIdByName($groupName)
    {
       return $this->_groupFactory->create()->getCollection()
       ->addFieldToFilter("customer_group_code", array("eq" => $groupName))
       ->getFirstItem()
       ->getId();
    }

    public function getCustomerById($customerId)
    {
       return $this->_customerRepository->getById($customerId);
    }

    public function createDirectoryGroupName($groupName) 
    {
        $divaltoCustomerDir = $this->getDivaltoInvoiceDir($groupName);
        if( !$this->_driverFile->isExists($divaltoCustomerDir) ) {
            $this->_driverFile->createDirectory($divaltoCustomerDir);
        }
    }
    
    public function groupCreate($groupName, $taxClassId=null) 
    {
        $taxClassId ?? self::TAX_CLASS_DEFAULT_ID;
        try{
            if(!$this->getCustomerGroupIdByName($groupName)) { 
                $group = $this->_groupFactory->create();
                $group->setCode($groupName)
                ->setTaxClassId($taxClassId) // Core installers (Db) : 3 
                ->save();
            }
            $this->createDirectoryGroupName($groupName); // Create path users to invoice dir
        } catch (Exception $e) {
            $this->_logger->critical($e->getGroupName());
        }
    }

    public function getOutstandingValue()
    {
        return $this->_customerSession->getCustomer()->getData('divalto_outstanding_status');
    }

    public function outstandingMessage()
    {
        $supportEmail = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
        $contactText = $this->scopeConfig->getValue('general/store_information/phone',ScopeInterface::SCOPE_STORE);
        return __('Your account is not activated for the order payment, please contact us: <b>%1</b> or <a href="mailto:%2">%2</a>',$contactText, $supportEmail);
    }

    public function isEnabled()
    {
        return $this->getGeneralConfig('enabled')==1 ? true : false;
    }

    public function dataOrderTest() 
    {

        if($postData = $this->getGeneralConfig('data_order_test')) {
            return json_decode($postData, true);
        }

        $emailTest = $this->getGeneralConfig('email_test');
        $codeTest = $this->getGeneralConfig('code_test');
        $divaltoStoreId = $this->getGeneralConfig('divalto_store_id');

        $postData = '{"Numero_Dossier":"'.$divaltoStoreId.'","Numero_Commande_Magento":"000001","Email_Client_Cde":"'.$emailTest.'","Code_Client_Divalto":"'.$codeTest.'","Code_Adresse_Livraison":"","Adresse_Livraison_Manuelle":{"Rue":"37 RUE MARYSE BASTIE","Ville":"LYON","Code_Postal":"69008","Pays":"FR"},"Code_Adresse_Facturation":"","Paiement":"processing","liste_detail_ligne":[{"SKU":"00001AIBN","Quantite_Commandee":"10","Prix_Unitaire_TTC":"","Prix_Unitaire_HT":"100","Montant_Ligne":"1000"}],"Client_Particulier":{"Email_Client":"","Raison_Sociale":"POLAT","Titre":"SAS","Telephone":"0610158941","Contact":{"Nom":"","Prenom":"","Telephone":"","Email":"'.$emailTest.'","Fonction":""}}}';

        return json_decode($postData, true);
    }

    public function dataCustomerTest()
    {
        if($postData = $this->getGeneralConfig('data_customer_test')) {
            return json_decode($postData, true);
        }

        $emailTest = $this->getGeneralConfig('email_test');
        $divaltoStoreId = $this->getGeneralConfig('divalto_store_id');
        
        $postData = '{"Numero_Dossier":"'.$divaltoStoreId.'","Email_Client":"'.$emailTest.'","Raison_Sociale":"","Titre":"","Telephone":"","Numero_Siret":"","Code_APE":"","Numero_TVA":"FR999999999","Adresse_Facturation":{"Rue":"","Ville":"","Code_Postal":"","Pays":""},"Adresse_Livraison":{"Rue":"","Ville":"","Code_Postal":"","Pays":""},"Contact":{"Nom":"","Prenom":"","Telephone":"","Email":"'.$emailTest.'","Fonction":""}}';

        return json_decode($postData, true);
    }
    
}