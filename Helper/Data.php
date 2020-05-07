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
use \Magento\Customer\Model\GroupFactory;
use \Magento\Framework\App\RequestInterface;
use \Magento\Framework\Session\SessionManagerInterface;
use \Psr\Log\LoggerInterface;

/**
 * Class Data
 * @package Divalto\Customer\Helper
 */
class Data extends AbstractHelper
{

    const TAX_CLASS_DEFAULT_ID = 3;

    const XML_PATH_DIVALTO_CUSTOMER = 'divalto_customer/';
	
	protected $_groupFactory;

    protected $_request;

    protected $_coreSession;

    protected $_log;

	public function __construct (
        GroupFactory $groupFactory,
        RequestInterface $request,
        LoggerInterface $logger,
        SessionManagerInterface $coreSession,
		Context $context
	)
    {
		$this->_groupFactory = $groupFactory;
        $this->_request = $request;
        $this->_log = $logger;
        $this->_coreSession = $coreSession;
		parent::__construct($context);
    }


    public function setSessionGroupName($value){
        $this->_coreSession->start();
        $this->_coreSession->setGroupName($value);
     }

    public function getSessionGroupName(){
        $this->_coreSession->start();
        return $this->_coreSession->getGroupName();
    }

    public function unSessionGroupName(){
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
    public function getGeneralConfig($code, $storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_EXTRAPRICE .'general/'. $code, $storeId);
    }

    public function getCustomerGroupIdByName($groupName)
    {
       return $this->_groupFactory->create()->getCollection()
       ->addFieldToFilter("customer_group_code", array("eq" => $groupName))
       ->getFirstItem()
       ->getId();
    }
    
    public function groupCreate($groupName) 
    {

        $this->setSessionGroupName($groupName); // For update account before register

        try{
            if(!$this->getCustomerGroupIdByName($groupName)) { 
                $group = $this->_groupFactory->create();
                $group->setCode($groupName)
                ->setTaxClassId(self::TAX_CLASS_DEFAULT_ID) // magic numbers OK, core installers do it?!
                ->save();
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

    public function checkVat() {
        return;
    }
}