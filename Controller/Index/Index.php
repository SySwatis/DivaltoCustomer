<?php
/**
 * Divalto_Customer
 */
namespace Divalto\Customer\Controller\Index;

use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action
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
		return $this->_pageFactory->create();
	}

}
