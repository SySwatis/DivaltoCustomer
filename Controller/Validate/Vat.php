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
 * @subpackage Controller
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */

namespace Divalto\Customer\Controller\Validate;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Vat extends Action
{
     /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
     protected $_resultJsonFactory;

     /**
     * @var \Divalto\Customer\Helper\Vat 
     */
     protected $_vatCustomer;

     /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
     public function __construct(
          Context $context,
          JsonFactory $resultJsonFactory,
          \Divalto\Customer\Model\Vat $vatCustomer
     ) {
          $this->_resultJsonFactory = $resultJsonFactory;
          $this->_vatCustomer =  $vatCustomer;
          parent::__construct($context);
     }

     public function execute()
     {
          
          $resultJson = $this->_resultJsonFactory->create();
          $vatNumber = $this->getRequest()->getParam('taxvat');
          $checkVatNumber = $this->_vatCustomer->checkVatNumber($vatNumber);
          
          if( !$checkVatNumber['is_valid']) {  
               $resultJson->setData($checkVatNumber['message']); 
          } else {
               $resultJson->setData('true');
          }
         
          return $resultJson;
     }

}