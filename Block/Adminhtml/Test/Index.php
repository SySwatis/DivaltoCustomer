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

        $sslVerifypeer =  $this->_helperData->getGeneralConfig('ssl_verifypeer') == 1 ? 'Yes' : 'No';

        $html ='<header class="page-header row"><p><b>'.__('SSL Cert. Verfify Peer:').'</b> '.__($sslVerifypeer).'</p>';
        $html .='<p><b>'.__('Divlato Store Id:').'</b> '.$this->_helperData->getGeneralConfig('divalto_store_id').'</p>';
        $html .='<p><b>'.__('Email test:').'</b> '.$this->_helperData->getGeneralConfig('email_test').'</p>';
        $html .='<p><b>'.__('Code test:').'</b> '.$this->_helperData->getGeneralConfig('code_test').'</p>';
        $html .='<p><b>'.__('Api Url Test:').'</b> '.$this->_helperData->getGeneralConfig('api_url_test').'</p>';
        $html .='<p><b>'.__('Data Customer Test:').'</b></p>';
        $html .= '<p><pre>'.json_encode( ( $this->_helperData->dataCustomerTest() ) ).'</pre></p>';
        $html .='<p><b>'.__('Data Order Test:').'</b></p>';
        $html .= '<p><pre>'.json_encode( ( $this->_helperData->dataOrderTest() ) ).'</pre></p>';
        $html .= '<p><b>'.__('Message:').'</b> '.$this->_helperData->outstandingMessage().'</p></header>';
        $html .='<div class="page-main-actions"><p><a class="action-primary" href="'.$this->getUrl('customer/test/ping').'">Ping</a></p>'; 
        $html .='<p><a class="action-primary" href="'.$this->getUrl('customer/test/CreateCustomer').'">'.__('Create Customer').'</a></p>'; 
        $html .='<p><a class="action-primary" href="'.$this->getUrl('customer/test/CreateOrder').'">'.__('Create Order').'</a></p></div>';
        $html .='<div class="footer-legal-system"><small><b>Author:</b> SySwatis Copyright© '.date("Y").' | <a href="https://github.com/SySwatis/DivaltoCustomer">github.com DivaltoCustomer</a> - ver. 0.1.0 (dev-master)</small></div>';

        return $html;
    }
    
}