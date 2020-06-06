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

    protected $_request;

    protected $_messageManager;

    protected $_log;

    protected $_helperData;

    protected $_helperRequester;

    protected $_vatCustomer;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    private $responseFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /*
         * @param UrlFactory $urlFactory
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
    
        try {

            // Get all post parameters

            $requestParams = $this->_request->getParams();

            // Get group name (code id divalto "users") and send params to api (email, taxvat)

            $divaltoCustomerData = $this->_helperRequester->getDivaltoCustomerData($requestParams);
            $groupName = $divaltoCustomerData['groupe_name'];

            // Add group if reponse and create groupe if no exist

            if($groupName) { 
                $this->_helperData->groupCreate($groupName);
            } else {
                $groupName = 'Not found';
            }

            // // Check vatNumber
            //  $requestParamCountry = $requestParams['taxvat'];

            // // Check country

            // if(!isset($requestParams['country'])) {
            //     // Debug
            //     $requestTaxVat = 'FR';
            // } else {
            //     $requestTaxVat = $requestParams['country'];
            // }

            // if( isset($requestParamCountry) && isset($requestTaxVat) ){
            //     $this->_helperData->checkVat($requestParamCountry, $requestTaxVat);
            // }

            // Add comment to log file

            $this->_log->debug('Observer CreatePost group name : '.$groupName);

        } catch (StateException $e) {
            $this->_log->critical($e->getMessage());
            $this->_messageManager->addExceptionMessage($e, __('We can\'t save the customer code.'));
        }

        return $observer;
    }  

}