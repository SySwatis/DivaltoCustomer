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

class UpdateOrder implements ObserverInterface
{
	protected $_logger;

	protected $_comment;

	/**
	* @param \Psr\Log\LoggerInterface $_logger
    */

	public function __construct(
        \Psr\Log\LoggerInterface $_logger,
        \Divalto\Customer\Model\Comment $_comment
    ){
        $this->_logger = $_logger;
        $this->_comment = $_comment;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		
		if(!$this->_helperData->isEnabled()) {
            return;
        }

		$order = $observer->getEvent()->getOrder();

		        // $divaltoTvaIdDefault = $this->_helperData->getGeneralConfig('divalto_tva_id_default');

		// if ... no comment ?

		$this->_comment->addCommentToOrder($order->getId()); // + status
		
		$this->_logger->debug('update order id : '.$order->getId().' status : '.$order->getStatus());
		//var_dump($order->getData());
		//exit;
	}
}