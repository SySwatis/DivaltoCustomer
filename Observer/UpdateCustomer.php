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
 * @subpackage Observer
 */

namespace Divalto\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Message\ManagerInterface;

class UpdateCustomer implements ObserverInterface
{
	const CUSTOMER_GROUP_DEFAULT_ID = 1;

    protected $_customerRepositoryInterface;

    protected $_groupFactory;

    protected $_customerFactory;

    protected $_log;

    protected $_messageManager;

    protected $_helperData;


    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        GroupFactory $groupFactory,
        CustomerFactory $customerFactory,
        ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \Divalto\Customer\Helper\Data $helperData
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_groupFactory = $groupFactory;
        $this->_customerFactory = $customerFactory;
        $this->_log = $logger;
        $this->_messageManager = $messageManager;
        $this->_helperData = $helperData;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if(!$this->_helperData->isEnabled()) {
            return;
        }

        // Get session Divalto Data (response)

        $sessionDivaltoData = $this->_helperData->getSessionDivaltoData();
        $outStandingStatus = $sessionDivaltoData['outstanding_status'];
        $response = $sessionDivaltoData['divalto_response'];
        $extrafield_1 = $sessionDivaltoData['divalto_extrafield_1'];
        $extrafield_2 = $sessionDivaltoData['divalto_extrafield_2'];

        if( isset($sessionDivaltoData['group_name']) && $sessionDivaltoData['group_name'] ) {

            $groupName = $sessionDivaltoData['group_name'];
            $groupId = $this->_helperData->getCustomerGroupIdByName($groupName);

            $customer = $observer->getEvent()->getCustomer();
            if($customer){

                if($groupId) {
                    if ($customer->getGroupId() == self::CUSTOMER_GROUP_DEFAULT_ID) {
                        $customer->setGroupId($groupId);
                        $this->_log->debug('Observer UpdateCustomer Group Id : '.$groupId);
                    }
                }

                $customer->setCustomAttribute('divalto_outstanding_status',$outStandingStatus);
                $customer->setCustomAttribute('divalto_account_id',$groupName);
                $customer->setCustomAttribute('divalto_response',$response);
                $customer->setCustomAttribute('divalto_extrafield_1',$extrafield_1);
                $customer->setCustomAttribute('divalto_extrafield_2',$extrafield_2);
                $this->_customerRepositoryInterface->save($customer);

            }

        } else {

            // Add Warning message
            $this->_messageManager->addWarning( __('Account creation not valid, please contact us') );
        }

        $this->_helperData->unsSessionDivaltoData();
        
    }

}