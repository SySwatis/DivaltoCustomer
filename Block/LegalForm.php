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
class LegalForm extends \Magento\Framework\View\Element\Template
{

    /**
     * Customer helperData
     *
     * @var  \Divalto\Customer\Helper\Data
     */
    protected $_helperData;

    public function __construct(
        \Divalto\Customer\Helper\Data $helperData,
        \Magento\Backend\Block\Template\Context $context,        
        array $data = []
    )
    {        
        parent::__construct($context, $data);
        $this->_helperData = $helperData;
    }

    public function getLegalFormHtmlOptions()
    {  
        $options = $this->_helperData->getGeneralConfig('legal_form');
        $html = '';
        foreach ($options as $key => $value) {
            # code...
            $html .= $key.$value;
        }

        return '<option>getLegalFormHtmlOptions'. $html .'</option>';
    }
    
}