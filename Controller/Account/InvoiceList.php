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

class InvoiceList extends \Magento\Framework\App\Action\Action
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


	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\View\Result\PageFactory $pageFactory
	) {
		parent::__construct($context);
		$this->_customerSession = $customerSession;
		$this->_pageFactory = $pageFactory;
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
		// return $this->_pageFactory->create();
		$resultPage = $this->_pageFactory->create();
       	$resultPage->addHandle('divalto_customer_invoicelist'); //loads the layout of module_custom_customlayout.xml file with its name
       	return $resultPage;
	}

}