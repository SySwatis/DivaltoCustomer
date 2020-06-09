<?php
namespace Divalto\Customer\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Vendor\Module\Block\Adminhtml\Form\Field\TaxColumn;

/**
 * Class Ranges
 */
class LegalFormRanges extends AbstractFieldArray
{

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('legal_form_label', ['label' => __('Label'), 'class' => 'required-entry']);
        $this->addColumn('legal_form_value', ['value' => __('Value'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

}
