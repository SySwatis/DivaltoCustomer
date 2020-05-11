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
class Address extends \Magento\Framework\View\Element\Template
{
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
    
        array $data = []
    )
    {        
        parent::__construct($context, $data);
    }

    public function getAddress() 
    {
        return 'First address';
    }
}