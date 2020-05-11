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

class Debug implements ObserverInterface
{
    protected $_messageManager;

    protected $_log;

    protected $_helperData;

    protected $_helperRequester;

    protected $_vatCustomer;


    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \Divalto\Customer\Helper\Data $helperData,
        \Divalto\Customer\Helper\Requester $helperRequester,
        \Magento\Customer\Model\Vat $vatCustomer
    ) {
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_log = $logger;
        $this->_helperData = $helperData;
        $this->_helperRequester = $helperRequester;
        $this->_vatCustomer = $vatCustomer;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $this->_helperData->createDirectoryGroupName('test0000001');
    }  
}