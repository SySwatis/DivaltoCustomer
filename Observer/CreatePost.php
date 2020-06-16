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

class CreatePost implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_log;

    /**
     * @var \Divalto\Customer\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Divalto\Customer\Helper\Requester
     */
    protected $_helperRequester;

    /**
     * @var \Magento\Customer\Model\Vat
     */
    protected $_vatCustomer;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Divalto\Customer\Helper\Data $helperData
     * @param \Divalto\Customer\Helper\Requester $helperRequester
     * @param \Magento\Customer\Model\Vat $vatCustomer
     */
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

        if(!$this->_helperData->isEnabled()) {
            return;
        }

        try {

            // Get all post parameters

            $requestParams = $this->_request->getParams();

            // Get group name (code_Client Divalto "users")

            $divaltoCustomerData = $this->_helperRequester->getDivaltoCustomerData($requestParams);

            $groupName = $divaltoCustomerData['group_name'];

            // Add group if response and create groupe if no exist

            if( isset($groupName) && $groupName ) { 
                $this->_helperData->groupCreate($groupName);
                $this->_helperData->setSessionDivaltoData($divaltoCustomerData); // For update account before register
            } else {
                $groupName = 'Not found';
            }

            // Add comment to log file

            $this->_log->debug('Observer CreatePost group name : '.$groupName);

        } catch (StateException $e) {
            $this->_log->critical($e->getMessage());
            $this->_messageManager->addExceptionMessage($e, __('We can\'t save the customer code.'));
        }

        return $observer;
    }  

}