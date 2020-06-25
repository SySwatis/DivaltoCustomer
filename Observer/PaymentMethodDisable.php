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
 
namespace Divalto\Customer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class PaymentMethodDisable implements ObserverInterface {
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_customerSession;

	/** 
	 * @var \Divalto\Customer\Helper\Data
	 */
	protected $helperData;

	/**
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Divalto\Customer\Helper\Data $helperData
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
		if(!$this->_helperData->isEnabled()) {
            return;
        }

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