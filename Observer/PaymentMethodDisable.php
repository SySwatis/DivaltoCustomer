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

	/**
	 * @var
	 */
	protected $helperData;

	/**
	 * @param \Magento\Customer\Model\Session $customerSession [description]
	 * @param \Divalto\Customer\Helper\Data $helperData [description]
	 */
	public function __construct(
		\Magento\Customer\Model\Session $customerSession,
		\Divalto\Customer\Helper\Data $helperData
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

		// 0 = Pas de paiement autorisé
		// 1 = CB uniquement
		// 2 = CB + Bon de commande

		if ($this->_helperData->getOutstanding()) {
			if($this->_helperData->getOutstandingValue()==1  && $payment_method_code === 'purchaseorder') {
				$result = $observer->getEvent()->getResult();
				$result->setData('is_available', false);
			}
		} else {
			$result = $observer->getEvent()->getResult();
			$result->setData('is_available', false);
		}
	}
	
}