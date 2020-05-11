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
 * @subpackage Block
 */

namespace Divalto\Customer\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

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

    /**
     * Customer helperData
     *
     * @var  \Divalto\Customer\Helper\Data
     */
    protected $_helperData;

    public function __construct(
        Context $context,
        File $driverFile,
        Session $customerSession,
        GroupRepositoryInterface $groupRepository,
        StoreManagerInterface $storeManager,
        \Divalto\Customer\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->_groupRepository = $groupRepository;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_helperData = $helperData;
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

    public function getInvoiceList() 
    {
        $divaltoCustomerDir = $this->_helperData->getDivaltoInvoiceDir().$this->getGroupCode();
        if( $this->_driverFile->isExists($divaltoCustomerDir) && $this->_driverFile->isReadable($divaltoCustomerDir) ) {
            return  $this->_driverFile->readDirectory($divaltoCustomerDir);
        }
        return;
    }
}
