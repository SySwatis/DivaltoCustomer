<?php
namespace Divalto\Customer\Model\Config\Source;

class ListPaymentMethod implements \Magento\Framework\Option\ArrayInterface
{
 	protected $paymentHelper;
 
    protected $paymentConfig;
 
    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Config $paymentConfig
    ) {
        $this->paymentHelper = $paymentHelper;
        $this->paymentConfig = $paymentConfig;
    }
 
    public function getAllPaymentMethods() {
        return $this->paymentHelper->getPaymentMethods();
    }
 
    public function getActivePaymentMethods() {
        return $this->paymentConfig->getActiveMethods();
    }


 	public function toOptionArray()
 	{
 		$methodListKeys = array_keys($this->getActivePaymentMethods());
 		$methodList = $this->getAllPaymentMethods();
 		$methodListArr= array();
 		foreach( $methodListKeys as $key ) {
   			$methodListArr[]=['value' => $key, 'label' =>$methodList[$key]['title']];
		}
 		return $methodListArr;
 	}
}