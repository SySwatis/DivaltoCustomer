<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Divalto\Customer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class PaymentMethodDisable implements ObserverInterface {
	/**
	 * @var
	 */
	protected $_customerSession;
	protected $helperData;

	/**
	 * @param \Magento\Customer\Model\Session $customerSession [description]
	 */
	public function __construct(
		\Magento\Customer\Model\Session $customerSession,
		\Boeki\DisablePaymentMethod\Helper\Data $helperData
	) {
		$this->_customerSession = $customerSession;
		$this->_helperData = $helperData;
	}

	/**
	 * @param Observer $observer
	 *
	 * @return void
	 */
	public function execute(Observer $observer) 
	{

		$payment_method_code = $observer->getEvent()->getMethodInstance()->getCode();

		if (!$this->getOutstanding()&&$payment_method_code !== 'purchaseorder') {
			$result = $observer->getEvent()->getResult();
			$result->setData('is_available', false);
		}
	}

	public function getOutstanding()
    {
        return $this->_customerSession->getCustomer()->getData('divalto_outstanding_status')==1 ? true : false;
    }
}