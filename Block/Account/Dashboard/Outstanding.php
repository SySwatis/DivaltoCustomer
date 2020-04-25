<?php
namespace Divalto\Customer\Block\Account\Dashboard;
class Outstanding extends \Magento\Framework\View\Element\Template
{
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
    
        array $data = []
    )
    {        
        parent::__construct($context, $data);
    }

    public function getOutstanding() {
        return '000.00 #';
    }
}