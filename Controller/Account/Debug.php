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
 * @subpackage Controller
 */

namespace Divalto\Customer\Controller\Account;

use Magento\Framework\App\RequestInterface;

class Debug extends \Magento\Framework\App\Action\Action
{
	
	/**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

	/**
     * Framework pageFactory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
	protected $_pageFactory;

    protected $_helperRequester;

    protected $_helperData;


	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Divalto\Customer\Helper\Requester $helperRequester,
        \Divalto\Customer\Helper\data $helperData
	) {
		parent::__construct($context);
		$this->_customerSession = $customerSession;
		$this->_pageFactory = $pageFactory;
        $this->_helperRequester = $helperRequester;
        $this->_helperData = $helperData;
	}

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_customerSession->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }
    
    public function execute()
    {
        
        if( $this->_helperData->getDebugConfig()==1 ) {
            // $this->_log->debug('Divalto Customer Observer Debug Enabled');
            // $this->_helperData->createDirectoryGroupName('test0000001');
            $requestParams=array(
                'email'=>'contact@pachadistribution.com',
                // 'siret'=>'','ape'=>'',
                // 'lastname'=>'','firstname'=>'',
                // 'legal_form'=>'','telephone'=>''
            );

            
            $data = $this->_helperRequester->getDivaltoCustomerData($requestParams);
            echo '<pre>';
            print_r($data);
            echo '</pre>'.'<br>';
            // $this->_helperData->setSessionDivaltoData($data);
            // $dataGet = $this->_helperData->getSessionDivaltoData();
            // echo isset($dataGet['group_name']) ? $dataGet['group_name'] : 'no group name';
            // exit;
             die('debug');
        }

       
    }

}