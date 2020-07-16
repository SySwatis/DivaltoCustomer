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
 * @subpackage Observer
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */

namespace Divalto\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class CreatePost implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Divalto\Customer\Logger\Logger
     */
    protected $_logger;

    /**
     * @var \Divalto\Customer\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Divalto\Customer\Helper\Requester
     */
    protected $_helperRequester;

    /**
     * @var \Magento\Customer\Model\Vat
     */
    protected $_vatCustomer;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Divalto\Customer\Helper\Data $helperData
     * @param \Divalto\Customer\Helper\Requester $helperRequester
     * @param \Magento\Customer\Model\Vat $vatCustomer
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Divalto\Customer\Logger\Logger $logger,
        \Divalto\Customer\Helper\Data $helperData,
        \Divalto\Customer\Helper\Requester $helperRequester,
        \Magento\Customer\Model\Vat $vatCustomer
    ) {
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_logger = $logger;
        $this->_helperData = $helperData;
        $this->_helperRequester = $helperRequester;
        $this->_vatCustomer = $vatCustomer;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        return;


        if(!$this->_helperData->isEnabled()) {
            return;
        }

        // Divalo Store Id (Numero_Dossier)

        $divaltoStoreId = $this->_helperData->getGeneralConfig('divalto_store_id');
        
        // Get all post parameters

        $requestParams = $this->_request->getParams();

        if(!isset($requestParams['email'])) return;

        $postData = [
            "Numero_Dossier"=>$divaltoStoreId,
            "Email_Client"=>"",
            "Raison_Sociale"=>"",
            "Titre"=>"",
            "Telephone"=>"",
            "Numero_Siret"=>"",
            "Code_APE"=>"",
            "Numero_TVA"=>"",
            "Adresse_Facturation"=>array("Rue"=>"","Ville"=>"","Code_Postal"=>"","Pays"=>""),
            "Adresse_Livraison"=>array("Rue"=>"","Ville"=>"","Code_Postal"=>"","Pays"=>""),
            "Contact"=>array("Nom"=>"","Prenom"=>"","Telephone"=>"","Email"=>$requestParams['email'],"Fonction"=>"")
        ];

        try {

            // Get group name (code_Client Divalto "users")

            $divaltoCustomerData = $this->_helperRequester->getDivaltoCustomerData($postData,$this->_helperRequester::ACTION_CREATE_CUSTOMER);

            // Default (log)

            $groupName = 'Not found';

            // Add group if response and create groupe if no exist

            if( isset($divaltoCustomerData['group_name']) && $divaltoCustomerData['group_name'] ) {
                $groupName = $divaltoCustomerData['group_name'];
                $this->_helperData->groupCreate($groupName);
                $this->_helperData->setSessionDivaltoData($divaltoCustomerData); // For update account before register
            }

            // Add comment to log file

            $this->_logger->info('Observer CreatePost group name : '.$groupName);

        } catch (StateException $e) {
            $this->_logger->info($e->getMessage());
            $this->_messageManager->addExceptionMessage($e, __('We can\'t save the customer code.'));
        }

        return $observer;
    }  

}