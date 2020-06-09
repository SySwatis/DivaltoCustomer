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
 * @subpackage Model
 */

namespace Divalto\Customer\Model\Config\Source;
 
class LegalForm extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
                ['value' => '',  'label' => __('Please Select')],
                ['value' => '0', 'label' => __('EURL')],
                ['value' => '1', 'label' => __('SA')],
                ['value' => '2', 'label' => __('SARL')],
                ['value' => '3', 'label' => __('SAS')],
                ['value' => '4', 'label' => __('SASU')],
                ['value' => '5', 'label' => __('SNC')],
                ['value' => '6', 'label' => __('Other')]
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