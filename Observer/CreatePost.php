<?php

namespace Divalto\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class CreatePost implements ObserverInterface
{
    protected $messageManager;

    protected $_log;

    protected $_helperGroup;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \Divalto\Customer\Helper\Data $helperGroup,
        \Divalto\Customer\Helper\Requester $helperRequester
    ) {
        $this->messageManager = $messageManager;
        $this->_log = $logger;
        $this->_helperGroup = $helperGroup;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $condition = 'Your condition';
        $this->_log->debug('Observer CreatePost executing create group code:');

        $this->_helperGroup->groupCreate('Test034'); // Divalto request

        if($condition){
            $this->messageManager->addSuccess(
                __("Custom message here!")
            );
        }

        return $observer;
    }  
}