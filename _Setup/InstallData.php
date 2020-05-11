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
 */

namespace Divalto\Customer\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;

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

        
        // customerAttribute1

        $eavSetup->removeAttribute(Customer::ENTITY, 'divalto_customer_user_id');

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'divalto_customer_user_id',
            [
                'type'         => 'varchar',
                'label'        => 'Divalto Customer User Id',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 333,
                'system'       => 0,
            ]
        );
        $customerAttribute1 = $this->eavConfig->getAttribute(Customer::ENTITY, 'divalto_customer_user_id');

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute1->setData(
            'used_in_forms',
            ['adminhtml_customer']

        );
        $customerAttribute1->save();



        // customerAttribute2

        $eavSetup->removeAttribute(Customer::ENTITY, 'divalto_customer_outstanding_status');

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'divalto_customer_outstanding_status',
            [
                'type'         => 'decimal',
                'label'        => 'Divalto Customer Outstanding Status',
                'input'        => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'default' => 0,
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 334,
                'system'       => 0,
            ]
        );
        $customerAttribute2 = $this->eavConfig->getAttribute(Customer::ENTITY, 'divalto_customer_outstanding_status');

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute2->setData(
            'used_in_forms',
            ['adminhtml_customer']

        );
        $customerAttribute2->save();

        

        // customerAttribute3

        $eavSetup->removeAttribute(Customer::ENTITY, 'ape');

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'ape',
            [
                'type'         => 'varchar',
                'label'        => 'Divalto Customer User Ape',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 335,
                'system'       => 0,
            ]
        );
        $customerAttribute3 = $this->eavConfig->getAttribute(Customer::ENTITY, 'ape');

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute3->setData(
            'used_in_forms',
            ['adminhtml_customer']

        );
        $customerAttribute3->save();

        

        // customerAttribute4

        $eavSetup->removeAttribute(Customer::ENTITY, 'siret');

        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'siret',
            [
                'type'         => 'varchar',
                'label'        => 'Divalto Customer User Siret',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 336,
                'system'       => 0,
            ]
        );
        $customerAttribute4 = $this->eavConfig->getAttribute(Customer::ENTITY, 'siret');

        // more used_in_forms ['adminhtml_checkout','adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address']
        $customerAttribute4->setData(
            'used_in_forms',
            ['adminhtml_customer']

        );
        $customerAttribute4->save();
    }
}
?>