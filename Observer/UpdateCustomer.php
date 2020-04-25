<?php

namespace Divalto\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Customer\Model\CustomerFactory;

class UpdateCustomer implements ObserverInterface
{
	const CUSTOMER_GROUP_DEFAULT_ID = 1;

    const TAX_CLASS_DEFAULT_ID = 3;

    protected $_customerRepositoryInterface;

    protected $_groupFactory;

    protected $_customerFactory;

    protected $_log;


    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        GroupFactory $groupFactory,
        CustomerFactory $customerFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_groupFactory = $groupFactory;
        $this->_customerFactory = $customerFactory;
        $this->_log = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $test = 'Test034'; // Divalto request
        $groupId = $this->getCustomerGroupIdByName($test);

        // if(!$groupId) {
        //     $this->groupCreate($test);
        // }

        $customer = $observer->getEvent()->getCustomer();

        if($customer){
            if ($customer->getGroupId() == self::CUSTOMER_GROUP_DEFAULT_ID) {
                $customer->setGroupId($groupId);
                $this->_customerRepositoryInterface->save($customer);
                $this->_log->debug('Observer UpdateCustomer executing assin group id: '.$groupId );
            }
        }
        
    }

    public function getCustomerGroupIdByName($groupCode)
    {
       return $this->_groupFactory->create()->getCollection()
       ->addFieldToFilter("customer_group_code", array("eq" => $groupCode))
       ->getFirstItem()
       ->getId();
    }

    public function groupCreate($groupCode) 
    {
        if(!$this->getCustomerGroupIdByName($groupCode)) { 
            $group = $this->_groupFactory->create();
            $group->setCode($groupCode)
            ->setTaxClassId(self::TAX_CLASS_DEFAULT_ID) // magic numbers OK, core installers do it?!
            ->save();
        }
    }
}