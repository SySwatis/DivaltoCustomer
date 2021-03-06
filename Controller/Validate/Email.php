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
use Magento\Customer\Model\Customer;

class Email extends Action
{
     /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
     protected $_resultJsonFactory;

     /**
     * @var \Magento\Customer\Model\Customer 
     */
     protected $_customerModel;

     /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\Customer $customerModel
     */
     public function __construct(
          Context $context,
          JsonFactory $resultJsonFactory,
          Customer $customerModel
     ) {
          $this->_resultJsonFactory = $resultJsonFactory;
          $this->_customerModel = $customerModel;
          parent::__construct($context);
     }

     public function execute()
     {
          $resultJson = $this->_resultJsonFactory->create();
          $email = $this->getRequest()->getParam('email');
          $customerData = $this->_customerModel->getCollection()->addFieldToFilter('email', $email);
          if(!count($customerData)) {
            $resultJson->setData('true');
          } else {
            $resultJson->setData(__('That email is already taken, try another one'));
          }
          return $resultJson;
     }

}