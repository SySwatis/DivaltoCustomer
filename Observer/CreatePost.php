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

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Exception;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

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
     * @var PsrLoggerInterface
     */
    private $_logger;

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
     */
    public function __construct(
        RequestInterface $request,
        ManagerInterface $messageManager,
        PsrLoggerInterface $logger,
        \Divalto\Customer\Helper\Data $helperData,
        \Divalto\Customer\Helper\Requester $helperRequester
    ) {
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_logger = $logger;
        $this->_helperData = $helperData;
        $this->_helperRequester = $helperRequester;
    }

    public function execute(Observer $observer)
    {

        if(!$this->_helperData->isEnabled()) {
            return;
        }

        // Divalo Store Id (Numero_Dossier)

        $divaltoStoreId = $this->_helperData->getGeneralConfig('divalto_store_id');
        
        // Get all post parameters

        $requestParams = $this->_request->getParams();

        // Default

        // $groupName = '';

        if( !isset($requestParams['email']) || !isset($requestParams['taxvat']) ) return;

        $postData = [
            "Numero_Dossier"=>$divaltoStoreId,
            "Email_Client"=>$requestParams['email'],
            "Raison_Sociale"=>$requestParams['company_name'],
            "Titre"=>$requestParams['legal_form'],
            "Telephone"=>$requestParams['phone_number'],
            "Numero_Siret"=>"",
            "Code_APE"=>"",
            "Numero_TVA"=>$requestParams['taxvat'],
            "Adresse_Facturation"=>array("Rue"=>"","Ville"=>"","Code_Postal"=>"","Pays"=>""),
            "Adresse_Livraison"=>array("Rue"=>"","Ville"=>"","Code_Postal"=>"","Pays"=>""),
            "Contact"=>array("Nom"=>$requestParams['lastname'],"Prenom"=>$requestParams['firstname'],"Telephone"=>"","Email"=>$requestParams['email'],"Fonction"=>"")
        ];

        try {

            // Get group name (code_Client Divalto "users")

            $divaltoCustomerData = $this->_helperRequester->getDivaltoCustomerData($postData,$this->_helperRequester::ACTION_CREATE_CUSTOMER);

            // Add group if response and create groupe if no exist

            if( isset($divaltoCustomerData['group_name']) && $divaltoCustomerData['group_name'] ) {
                $groupName = $divaltoCustomerData['group_name'];
                $this->_helperData->groupCreate($groupName);
                $this->_helperData->setSessionDivaltoData($divaltoCustomerData); // For update account before register
            }

            // Add a warning messages

            if( isset($divaltoCustomerData['message']) && $divaltoCustomerData['message'] ) {
                $outStandingMessage = $this->_helperData->outStandingMessage();
                $this->_messageManager->addWarning( 'Account creation not valid, please contact us' );
            }

            if( isset($divaltoCustomerData['outstanding_status']) && $divaltoCustomerData['outstanding_status']==0 ) {
                $outStandingMessage = $this->_helperData->outStandingMessage();
                $this->_messageManager->addWarning( $outStandingMessage );
            }

            // Add comment to log file

            $this->_logger->info('Observer CreatePost group name : '.$groupName ?? 'Not found');

        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
            $this->_messageManager->addExceptionMessage($e, __('We can\'t save the customer code.'));
        }
       
        return $observer;
    }  

}