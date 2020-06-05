<?php

// http://blog.chapagain.com.np/magento-2-create-customer-attribute-programmatically-also-update-delete-customer-attribute/

namespace Divalto\Customer\Model\Config\Source;
 
class OutstandingStatus extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => '', 'label' => __('Please Select')],
                ['value' => '0', 'label' => __('Not Allowed')],
                ['value' => '1', 'label' => __('CB Only')],
                ['value' => '2', 'label' => __('CB Or Purshase Order')],
                ['value' => '3', 'label' => __('Custom Rules')]
            ];
        }
        return $this->_options;
    }
 
    /**
     * Get text of the option value
     * 
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionValue($value) 
    { 
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}