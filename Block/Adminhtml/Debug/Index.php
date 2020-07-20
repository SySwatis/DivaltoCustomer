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
namespace Divalto\Customer\Block\Adminhtml\Debug;

use Magento\Backend\Block\Template\Context;

class Index extends \Magento\Backend\Block\Template
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

    function getContent()
    {
        if( $this->_helperData->getDebugConfig()==1 ) {

            $sslVerifypeer =  $this->_helperData->getGeneralConfig('ssl_verifypeer') == 1 ? 'Yes' : 'No';



            $html =     '<p><b>'.__('SSL Cert. Verfify Peer:').'</b> '.__($sslVerifypeer).'</p>';
            $html .=    '<p><b>'.__('Email test:').'</b> '.$this->_helperData->getGeneralConfig('email_test').'</p>';
            $html .=    '<p><b>'.__('Api Url Test (Debug):').'</b> '.$this->_helperData->getGeneralConfig('api_url_test').'</p>';
            

        } else {
            $html = "Debug disable";
        }
        $html .=    '<p><b>Author:</b> SySwatis Copyright© 2020 | <a href="https://github.com/SySwatis/DivaltoCustomer">github.com DivaltoCustomer</a> - ver. 0.1.0 (dev-master)</p>';
        return $html;
    }
}