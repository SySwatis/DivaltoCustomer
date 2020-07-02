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

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class UpdateOrder implements ObserverInterface
{
	
	const HEADING_COMMENT = '#Divalto | ';

	protected $_log;

	protected $_helperData;

	protected $_helperRequester;

	protected $_orderMap;

	protected $_comment;

	/**
	* @param \Psr\Log\LoggerInterface $_log
    */

	public function __construct(
        \Psr\Log\LoggerInterface $_log,
        \Divalto\Customer\Helper\Data $_helperData,
        \Divalto\Customer\Helper\Requester $_helperRequester,
        \Divalto\Customer\Model\OrderMap $_orderMap,
        \Divalto\Customer\Model\Comment $_comment
    ){
        $this->_log = $_log;
        $this->_helperData = $_helperData;
        $this->_helperRequester = $_helperRequester;
        $this->_orderMap = $_orderMap;
        $this->_comment = $_comment;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		
		// Order

		$order = $observer->getEvent()->getOrder();
		$payment = $order->getPayment();
		$method = $payment->getMethodInstance();
		$methodTitle = $method->getTitle();
		$methodCode = $method->getCode();
		$response = '';


		// Skip  Event frontend

		if( $this->_helperData->getCustomer()->getId() && $observer->getEvent()->getName()=='sales_order_save_after' ) {
			return;
		}

		// Divalo Store Id (Numero_Dossier)

        $divaltoStoreId = $this->_helperData->getGeneralConfig('divalto_store_id');

        // Status Allowed (Default: processing)
		
		$orderStatusAllowed = explode(",",$this->_helperData->getGeneralConfig('order_status'));

		// Payment method Allowed (Default: purchaseorder)

		$orderPaymentMethodAllowed = explode(",",$this->_helperData->getGeneralConfig('payment_method'));

		// Allows Checking

		if( in_array($methodCode, $orderPaymentMethodAllowed) || in_array($order->getStatus(), $orderStatusAllowed) ) {

			// Send Data Order

			$postData = array();

			try {
				
				// Order Mapping
				
				$postData = $this->_orderMap->create($order);

				// Get response from api Divalto
				$response = $this->_helperRequester->getDivaltoCustomerData($postData, $this->_helperRequester::ACTION_CREATE_ORDER, true);

			} catch (StateException $e) {
				
				// Comment (Fail)
			
				$response = $order->getStatus().' - ERP query fail';

            	$this->_log->critical($e->getMessage());
            	$this->_messageManager->addExceptionMessage($e, __('We can\'t save the order.'));
        	}

        	// Add comment to order (Order Id(s) dvialto)

			if($response!='')
				
			$this->_comment->addCommentToOrder($order->getId(),self::HEADING_COMMENT.$response['comment']);

			// Add event to log

			$this->_log->debug('Oberser Event Update Order order id : '.$order->getId().' Status : '.$order->getStatus().' | Method : '.$methodCode );
		}
	}
}