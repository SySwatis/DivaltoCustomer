<?php
namespace Divalto\Customer\Block;

class Invoice extends \Magento\Framework\View\Element\Template
{

    /**
     * Framework driverFile
     *
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_driverFile;
    
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Store storeManager
     *
     * @var  \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer groupRepository
     *
     * @var  \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $_groupRepository;

    protected $dir ='pub/media/wysiwyg/divalto/invoice';

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_groupRepository = $groupRepository;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_driverFile = $driverFile;
    }

    /**
     * Get current url for store
     *
     * @param bool|string $fromStore Include/Exclude from_store parameter from URL
     * @return string     
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getBaseMediaDir() 
    {
        return $this->_storeManager->getStore()->getBaseMediaDir();
    }

    public function getGroupId() 
    {
        return $this->_customerSession->getCustomer()->getGroupId();
    }

    public function getGroupCode() 
    {
        return  $this->_groupRepository->getById($this->_customerSession->getCustomer()->getGroupId())->getCode();
    }

    public function getInvoiceDir() 
    {
        return $this->getBaseMediaDir().$this->dir.$this->getGroupId();
    }

    public function getInvoiceList() 
    {
        $divaltoCustomerDir = $this->dir.'/'.$this->getGroupCode();
        if( $this->_driverFile->isExists($divaltoCustomerDir) && $this->_driverFile->isReadable($divaltoCustomerDir) ) {
            return  $this->_driverFile->readDirectory($divaltoCustomerDir);
        }
        return;
    }
}
