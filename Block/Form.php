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
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */


namespace Divalto\Customer\Block;

class Form  extends \Magento\Framework\View\Element\Template
{

    /**
     * Customer helperData
     *
     * @var  \Divalto\Customer\Helper\Data
     */
    protected $_helperData;

    /**
     * Customer serialize
     *
     * @var  \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_serialize;

    public function __construct(
        \Divalto\Customer\Helper\Data $helperData,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        \Magento\Backend\Block\Template\Context $context,     
        array $data = []
    )
    {        
        parent::__construct($context, $data);
        $this->_helperData = $helperData;
        $this->_serialize = $serialize;
    }

    public function getLegalFormHtmlOptions()
    {  
        $options = $this->_helperData->getGeneralConfig('legal_form');
      
        
        if($options == '' || $options == null)
        return;
        
        $unserializedata = $this->_serialize->unserialize($options);
        $html = '';
        foreach($unserializedata as $key => $row)
        {
            $html .= '<option value="'.$row['legal_form_value'].'">'.$row['legal_form_label'].'</option>';
        }
        return $html;
    }
    
}