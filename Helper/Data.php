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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Data
 * @package Divalto\Customer\Helper
 */
class Data extends AbstractHelper
{

    const TAX_CLASS_DEFAULT_ID = 3;

    const XML_PATH_DIVALTO_CUSTOMER = 'divalto_customer/';

    const DIVALTO_INVOICE_DIR = 'pub/media/wysiwyg/divalto/invoice';
	
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
    protected $_customerSession;

    /**
     * @var
     */
    protected $_request;

    /**
     * @var
     */
    protected $_coreSession;

    /**
     * @var
     */
    protected $_log;


	public function __construct (
        File $driverFile,
        StoreManagerInterface $storeManager,
        GroupFactory $groupFactory,
        Session $customerSession,
        RequestInterface $request,
        LoggerInterface $logger,
        SessionManagerInterface $coreSession,
		Context $context
	)
    {
        $this->_driverFile = $driverFile;
        $this->_storeManager = $storeManager;
		$this->_groupFactory = $groupFactory;
        $this->_customerSession = $customerSession;
        $this->_request = $request;
        $this->_log = $logger;
        $this->_coreSession = $coreSession;
		parent::__construct($context);
    }

    public function getDivaltoInvoiceDir() 
    {
        return self::DIVALTO_INVOICE_DIR . '/';
    }

    public function setSessionGroupName($value)
    {
        $this->_coreSession->start();
        $this->_coreSession->setGroupName($value);
     }

    public function getSessionGroupName()
    {
        $this->_coreSession->start();
        return $this->_coreSession->getGroupName();
    }

    public function unSessionGroupName()
    {
        $this->_coreSession->start();
        return $this->_coreSession->unsGroupName();
    }

    /**
     * Returns config value
     *
     * @return string
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
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

    public function createDirectoryGroupName($groupName) 
    {
        $divaltoCustomerDir = $this->getDivaltoInvoiceDir().$groupName;
        if( !$this->_driverFile->isExists($divaltoCustomerDir) ) {
            $this->_driverFile->createDirectory($divaltoCustomerDir);
        }
    }
    
    public function groupCreate($groupName) 
    {

        $this->setSessionGroupName($groupName); // For update account before register

        try{
            if(!$this->getCustomerGroupIdByName($groupName)) { 
                $group = $this->_groupFactory->create();
                $group->setCode($groupName)
                ->setTaxClassId(self::TAX_CLASS_DEFAULT_ID) // Core installers (Db) : 3 
                ->save();
                $this->createDirectoryGroupName($groupName); // Create path users to invoice dir
            }
        } catch (\Exception $e) {
            $this->_log->critical($e->getGroupName());
        }

        // Add comment to log file
        
        $requestParams = $this->_request->getParams();
        $logtxt= '';
        foreach ($requestParams as $param => $value) {
            $logtxt .=  ' '.$param.' '.$value;
        }
        $this->_log->debug('Helper Data GroupCreate :'.$logtxt);

    }

    public function getOutstanding()
    {
        $outstanding = $this->_customerSession->getCustomer()->getData('divalto_outstanding_status');
        return $outstanding > 0  ? true : false;
    }

    public function getOutstandingValue()
    {
        return $this->_customerSession->getCustomer()->getData('divalto_outstanding_status');
    }

    // https://fr.wikipedia.org/wiki/Code_Insee

    public function siretToVat($siret,$country='FR')
    {
        $siren = substr($siret,-9);
        $key = 12+(3*($siren%97));
        return $country.$key.$siren;
    }

    public function checkVat()
    {
        return;
    }
    
}