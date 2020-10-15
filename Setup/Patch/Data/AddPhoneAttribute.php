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

namespace Divalto\Customer\Setup\Patch\Data;

use Magento\Catalog\Ui\DataProvider\Product\ProductCollectionFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Customer\Api\CustomerMetadataInterface;
use Psr\Log\LoggerInterface;

/**
* Class AddPhoneAttribute
* @package Divalto\Customer\Setup\Patch\Data
*/
class AddPhoneAttribute implements DataPatchInterface, PatchRevertableInterface
{
   /**
    * @var ModuleDataSetupInterface
    */
   private $moduleDataSetup;
   /**
    * @var EavSetupFactory
    */
   private $eavSetupFactory;
   /**
    * @var ProductCollectionFactory
    */
   private $productCollectionFactory;
   /**
    * @var LoggerInterface
    */
   private $logger;
   /**
    * @var Config
    */
   private $eavConfig;
   /**
    * @var \Magento\Customer\Model\ResourceModel\Attribute
    */
   private $attributeResource;

   /**
    * AddPhoneAttribute constructor.
    * @param EavSetupFactory $eavSetupFactory
    * @param Config $eavConfig
    * @param LoggerInterface $logger
    * @param \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
    */
   public function __construct(
       EavSetupFactory $eavSetupFactory,
       Config $eavConfig,
       LoggerInterface $logger,
       \Magento\Customer\Model\ResourceModel\Attribute $attributeResource,
       \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
   ) {
       $this->eavSetupFactory = $eavSetupFactory;
       $this->eavConfig = $eavConfig;
       $this->logger = $logger;
       $this->attributeResource = $attributeResource;
       $this->moduleDataSetup = $moduleDataSetup;
   }

   /**
    * {@inheritdoc}
    */
   public function apply()
   {
       $this->moduleDataSetup->getConnection()->startSetup();
       $this->addPhoneAttribute();
       $this->moduleDataSetup->getConnection()->endSetup();
   }

   /**
    * @throws \Magento\Framework\Exception\AlreadyExistsException
    * @throws \Magento\Framework\Exception\LocalizedException
    * @throws \Zend_Validate_Exception
    */
   public function addPhoneAttribute()
   {
        $eavSetup = $this->eavSetupFactory->create();


        $eavSetup->addAttribute(
           \Magento\Customer\Model\Customer::ENTITY,
           'phone_number',
           [
               'type' => 'varchar',
               'label' => 'Phone Number',
               'input' => 'text',
               'required' => 0,
               'visible' => 1,
               'user_defined' => 1,
               'sort_order' => 999,
               'position' => 999,
               'system' => 0
           ]
       );

       $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
       $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'phone_number');
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);

        $eavSetup->addAttributeToSet(
            CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
            CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            null,
            'phone_number');

        $attribute->setData('used_in_forms', [
           'adminhtml_customer', 'customer_account_create'
        ]);

       $this->attributeResource->save($attribute);
   }

   /**
    * {@inheritdoc}
    */
   public static function getDependencies()
   {
       return [];
   }

   /**
    *
    */
   public function revert()
   {
   }

   /**
    * {@inheritdoc}
    */
   public function getAliases()
   {
       return [];
   }
}