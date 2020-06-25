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
	protected $_log;

	protected $_helperData;

	protected $_helperRequester;

	protected $_comment;

	/**
	* @param \Psr\Log\LoggerInterface $_log
    */

	public function __construct(
        \Psr\Log\LoggerInterface $_log,
        \Divalto\Customer\Helper\Data $_helperData,
        \Divalto\Customer\Helper\Requester $_helperRequester,
        \Divalto\Customer\Model\Comment $_comment
    ){
        $this->_log = $_log;
        $this->_helperData = $_helperData;
        $this->_helperRequester = $_helperRequester;
        $this->_comment = $_comment;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$order = $observer->getEvent()->getOrder();
		$payment = $order->getPayment();
		$method = $payment->getMethodInstance();
		$methodTitle = $method->getTitle();
		$methodCode = $method->getCode();
		
		// Allowed (inline)

		$methodCodeAllowed = array('purshaseorder');
		$orderStatusAllowed = array('pending','holded');

		if( in_array($methodCode, $methodCodeAllowed) || in_array($order->getStatus(), $orderStatusAllowed) ) {
			
			// requester

			// send response requester to dataSessionDivalto or register a comment

			$this->_log->debug('set order id : '.$order->getId().' status : '.$order->getStatus().' / method : '.$methodCode );
		}
	}
}