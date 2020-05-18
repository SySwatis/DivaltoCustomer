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

namespace Divalto\Customer\Block\Account\Dashboard;
class Address extends \Magento\Framework\View\Element\Template
{
	protected $_customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customer,
        \Magento\Backend\Block\Template\Context $context,        
    
        array $data = []
    )
    {        
        $this->_customerSession = $customer;
        parent::__construct($context, $data);
    }

    public function getCustomerDelivery() {
        $customer = $this->_customerSession->getCustomer();
        if ($customer) {
            $shippingAddress = $customer->getDefaultShippingAddress();
            if ($shippingAddress) {
                return $shippingAddress;
            }
        }
        return null;
    }
    public function getCustomerBilling() {
        $customer = $this->_customerSession->getCustomer();
        if ($customer) {
            $billingAddress = $customer->getDefaultBillingAddress();
            if ($billingAddress) {
                return $billingAddress;
            }
        }
        return null;
    }
    public function getApe() {
        $customer = $this->_customerSession->getCustomer();
        if ($customer) {
            $ape = $customer->getApe();
            if ($ape) {
                return $ape;
            }
        }
        return null;
    }
    public function getSiret() {
        $customer = $this->_customerSession->getCustomer();
        if ($customer) {
            $siret = $customer->getSiret();
            if ($siret) {
                return $siret;
            }
        }
        return null;
    }
}