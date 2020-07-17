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
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */
 
namespace Divalto\Customer\Observer;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Exception;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
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
        PsrLoggerInterface $logger,
        \Divalto\Customer\Helper\Data $helperData
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_groupFactory = $groupFactory;
        $this->_customerFactory = $customerFactory;
        $this->_log = $logger;
        $this->_messageManager = $messageManager;
        $this->_helperData = $helperData;
    }

    public function execute(Observer $observer)
    {

        if(!$this->_helperData->isEnabled()) {
            return;
        }

        // Default 

        $outStandingStatus = 1; // CB Only - Divalto/Customer/Model/Config/Source/OutstandingStatus.php
        $groupName = '';
        $response = '';
        $extrafield_1 = '';
        $extrafield_2 = '';

        // Get Customer

        $customer = $observer->getEvent()->getCustomer();

        if($customer){

            // Get session Divalto Data (response)

            $sessionDivaltoData = $this->_helperData->getSessionDivaltoData();

            if( $sessionDivaltoData && isset($sessionDivaltoData['group_name']) && $sessionDivaltoData['group_name'] ) {
                $groupName = $sessionDivaltoData['group_name'];
                $outStandingStatus = $sessionDivaltoData['outstanding_status'];
                $response = $sessionDivaltoData['divalto_response'];
                $extrafield_1 = $sessionDivaltoData['divalto_extrafield_1'];
                $extrafield_2 = $sessionDivaltoData['divalto_extrafield_2'];
            }

            // GroupId

            if($groupName!='') {

                $groupId = $this->_helperData->getCustomerGroupIdByName($groupName);

                if($groupId) {
                    if ($customer->getGroupId() == self::CUSTOMER_GROUP_DEFAULT_ID) {
                        $customer->setGroupId($groupId);
                        $this->_log->debug('Observer UpdateCustomer Group Id : '.$groupId);
                    }
                }
            }

            // Set Atttributes

            $customer->setCustomAttribute('divalto_outstanding_status',$outStandingStatus);
            $customer->setCustomAttribute('divalto_account_id',$groupName);
            $customer->setCustomAttribute('divalto_response',$response);
            $customer->setCustomAttribute('divalto_extrafield_1',$extrafield_1);
            $customer->setCustomAttribute('divalto_extrafield_2',$extrafield_2);

            $this->_customerRepositoryInterface->save($customer);

            //

            $this->_helperData->unsSessionDivaltoData();

        } else {
            // Add Warning message
            $this->_messageManager->addWarning( __('Account creation not valid, please contact us') );
        }    
    }


}