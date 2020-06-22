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
use Magento\Framework\Exception\LocalizedException;

class SetOrder implements ObserverInterface
{
	protected $_logger;

	/**
	* @param \Psr\Log\LoggerInterface $_logger
    */

	public function __construct(
        \Psr\Log\LoggerInterface $_logger
    ){
        $this->_logger = $_logger;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();
		$payment = $order->getPayment();
		$method = $payment->getMethodInstance();
		$methodTitle = $method->getTitle();
		$methodCode = $method->getCode();

		// Requester Divalto

		// send Session ?

		$this->_logger->debug('set order id : '.$order->getId().' status : '.$order->getStatus().' / method : '.$methodCode );
		//var_dump($order->getData());
		//exit;
	}
}