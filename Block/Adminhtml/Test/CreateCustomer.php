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
 * @subpackage Backend\Block
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */
namespace Divalto\Customer\Block\Adminhtml\Test;

use Magento\Backend\Block\Template\Context;

class CreateCustomer extends \Magento\Backend\Block\Template
{
	protected $_helperData;

    protected $_helperRequester;

	 public function __construct(
        \Divalto\Customer\Helper\Data $helperData,
        \Divalto\Customer\Helper\Requester $helperRequester,
        Context $context
    )
    {
        $this->_helperData = $helperData;
        $this->_helperRequester = $helperRequester;
        parent::__construct($context);
    }

    function responseUrlTest() 
    {
        $postData = $this->_helperData->dataCustomerTest();
        $emailTest = 'Email_Client : '.$postData['Email_Client'];
        $emailTest .= ' | Contact Email : '.$postData['Contact']['Email'];
        return array('Emails test'=>$emailTest,'Url Test'=>$this->_helperData->getGeneralConfig('api_url_test'),'Response Api'=>$this->_helperRequester->getDivaltoCustomerData($postData, $this->_helperRequester::ACTION_CREATE_CUSTOMER));
    }
}