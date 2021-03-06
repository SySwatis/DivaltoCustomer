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
 * @subpackage Setup
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */
 
namespace Divalto\Customer\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;
use Magento\Customer\Api\CustomerMetadataInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig       = $eavConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {


        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        
        // Customer Attribute

        $attributeCode = 'divalto_account_id';
        $eavSetup->removeAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $attributeCode,
            [
                'type'         => 'varchar',
                'label'        => 'Divalto Account Id',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 333,
                'system'       => 0,
            ]
        );
        $customerAttribute_1 = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute_1->setData(
            'used_in_forms',
            ['adminhtml_customer']

        );
        $customerAttribute_1->save();



        // Customer Attribute 2

        $attributeCode = 'divalto_outstanding_status';
        $eavSetup->removeAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $attributeCode,
            [
                'type'         => 'varchar',
                'label'        => 'Divalto Outstanding Status',
                'input'        => 'select',
                'source'       => 'Divalto\Customer\Model\Config\Source\OutstandingStatus',
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'default'      => 0,
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 334,
                'system'       => 0
            ]
        );
        $customerAttribute_2 = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute_2->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );
        $customerAttribute_2->save();

        

        // Customer Attribute 3

        $attributeCode = 'ape';
        $eavSetup->removeAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $attributeCode,
            [
                'type'         => 'varchar',
                'label'        => 'Ape',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 335,
                'system'       => 0,
            ]
        );
        $customerAttribute_3 = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute_3->setData(
            'used_in_forms',
            ['adminhtml_customer','customer_account_create']
        );
        $customerAttribute_3->save();

        

        // Customer Attribute 4

        $attributeCode = 'siret';
        $eavSetup->removeAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $attributeCode,
            [
                'type'         => 'varchar',
                'label'        => 'Siret',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 336,
                'system'       => 0,
            ]
        );
        $customerAttribute_4 = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute_4->setData(
            'used_in_forms',
            ['adminhtml_customer','customer_account_create']
        );
        $customerAttribute_4->save();



        // Customer Attribute 5

        $attributeCode = 'legal_form';
        $eavSetup->removeAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $attributeCode,
            [
                'type'         => 'varchar',
                'label'        => 'Legal Form',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 337,
                'system'       => 0,
            ]
        );
        $customerAttribute_5 = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute_5->setData(
            'used_in_forms',
            ['adminhtml_customer','customer_account_create']
        );
        $customerAttribute_5->save();



        // Customer Attribute 6

        $attributeCode = 'company_name';
        $eavSetup->removeAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $attributeCode,
            [
                'type'         => 'varchar',
                'label'        => 'Company Name',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 338,
                'system'       => 0,
            ]
        );
        $customerAttribute_6 = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute_6->setData(
            'used_in_forms',
            ['adminhtml_customer','customer_account_create']
        );
        $customerAttribute_6->save();

        

        // Customer Attribute 7

        $attributeCode = 'divalto_response';
        $eavSetup->removeAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $attributeCode,
            [
                'type'         => 'varchar',
                'label'        => 'Divalto Response',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 338,
                'system'       => 0,
            ]
        );
        $customerAttribute_7 = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute_7->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );
        $customerAttribute_7->save();



        // Customer Attribute 8

        $attributeCode = 'divalto_extrafield_1';
        $eavSetup->removeAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $attributeCode,
            [
                'type'         => 'varchar',
                'label'        => 'Divalto Extra Field 1',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 338,
                'system'       => 0,
            ]
        );
        $customerAttribute_8 = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute_8->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );
        $customerAttribute_8->save();



        // Customer Attribute 9

        $attributeCode = 'divalto_extrafield_2';
        $eavSetup->removeAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            $attributeCode,
            [
                'type'         => 'varchar',
                'label'        => 'Divalto Extra Field 2',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 338,
                'system'       => 0,
            ]
        );
        $customerAttribute_9 = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            $attributeCode);

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute_9->setData(
            'used_in_forms',
            ['adminhtml_customer']
        );
        $customerAttribute_9->save();

    }
}
?>