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
 * @subpackage Helper
 */
namespace Divalto\Customer\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use \Magento\Customer\Model\GroupFactory;

/**
 * Class Data
 * @package Divalto\Customer\Helper
 */
class Data extends AbstractHelper
{

    const TAX_CLASS_DEFAULT_ID = 3;
	
	protected $_groupFactory;

	public function __construct (
		Context $context,
		GroupFactory $groupFactory
	)
    {
		$this->_groupFactory = $groupFactory;
		parent::__construct($context);
    }

    public function getCustomerGroupIdByName($groupCode)
    {
       return $this->_groupFactory->create()->getCollection()
       ->addFieldToFilter("customer_group_code", array("eq" => $groupCode))
       ->getFirstItem()
       ->getId();
    }

    public function groupCreate($groupCode) 
    {
        if(!$this->getCustomerGroupIdByName($groupCode)) { 
            $group = $this->_groupFactory->create();
            $group->setCode($groupCode)
            ->setTaxClassId(self::TAX_CLASS_DEFAULT_ID) // magic numbers OK, core installers do it?!
            ->save();
        }
    }
}