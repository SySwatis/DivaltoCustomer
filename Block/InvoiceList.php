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

class InvoiceList extends \Magento\Framework\View\Element\Template
{

    /**
     * Framework driverFile
     *
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_driverFile;

    /**
     * Store storeManager
     *
     * @var  \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer helperData
     *
     * @var  \Divalto\Customer\Helper\Data
     */
    protected $_helperData;

    public function __construct(
        Context $context,
        File $driverFile,
        StoreManagerInterface $storeManager,
        \Divalto\Customer\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
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

    public function getInvoiceList() 
    {
        $divaltoCustomerDir = $this->_helperData->getDivaltoInvoiceDirSession();
        if( $this->_driverFile->isExists($divaltoCustomerDir) && $this->_driverFile->isReadable($divaltoCustomerDir) ) {
            return  $this->_driverFile->readDirectory($divaltoCustomerDir);
        }
        return;
    }
}
