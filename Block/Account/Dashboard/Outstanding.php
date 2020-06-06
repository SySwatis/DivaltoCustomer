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

namespace Divalto\Customer\Block\Account\Dashboard;
class Outstanding extends \Magento\Framework\View\Element\Template
{
	protected $_customerSession;

    protected $_helperData;

    public function __construct(
        \Divalto\Customer\Helper\Data $helperData,
        \Magento\Customer\Model\Session $customer,
        \Magento\Backend\Block\Template\Context $context,        
    
        array $data = []
    )
    {        
        $this->_helperData = $helperData;
        $this->_customerSession = $customer;
        parent::__construct($context, $data);
    }

    public function getOutstanding()
    {
        return $this->_helperData->getOutstanding();
    }
}