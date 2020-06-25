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
 */

namespace Divalto\Customer\Controller\Account;

use Magento\Framework\App\RequestInterface;

class Debug extends \Magento\Framework\App\Action\Action
{
	
	/**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

	/**
     * Framework pageFactory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
	protected $_pageFactory;

    protected $_helperRequester;

    protected $_helperData;


	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Divalto\Customer\Helper\Requester $helperRequester,
        \Divalto\Customer\Helper\data $helperData
	) {
		parent::__construct($context);
		$this->_customerSession = $customerSession;
		$this->_pageFactory = $pageFactory;
        $this->_helperRequester = $helperRequester;
        $this->_helperData = $helperData;
	}

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_customerSession->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }
    
    public function execute()
    {
        
        if( $this->_helperData->getDebugConfig()==1 ) {
            
            $action = 'ping';
            $postData = array();
            $allActions = array('ping','CreerClient','CreerCommande');

            $html =     '<div style="font-family:Gill Sans, sans-serif;padding:30px;"/>';
            $html .=    '<h1>Divalto Customer : Debug</h1>';
            $html .=    '<ul style="list-style:none;margin:0;padding:0;">';
            $html .=    '<li><a href="?action=ping">Ping</a></li>';
            $html .=    '<li><a href="?action=CreerClient">CreerClient</a></li>';
            $html .=    '<li><a href="?action=CreerCommande">CreerCommande</a></li>';
            $html .=    '</ul>';

            if( isset($_GET['action']) && in_array($_GET['action'],$allActions) ) {

                $action = $_GET['action'];
               
                if($action === 'CreerClient') {
                
                    $postData = [
                        "Numero_Dossier"=>"1",
                        "Email_Client"=>"",
                        "Raison_Sociale"=>"",
                        "Titre"=>"",
                        "Telephone"=>"",
                        "Numero_Siret"=>"",
                        "Code_APE"=>"",
                        "Numero_TVA"=>"",
                        "Adresse_Facturation"=>array("Rue"=>"","Ville"=>"","Code_Postal"=>"","Pays"=>""),
                        "Adresse_Livraison"=>array("Rue"=>"","Ville"=>"","Code_Postal"=>"","Pays"=>""),
                        "Contact"=>array("Nom"=>"","Prenom"=>"","Telephone"=>"","Email"=>"muratk21@hotmail.com","Fonction"=>"")
                    ];

                }

                if($action === 'CreerCommande') {

                     $postData = [
                        'Numero_Dossier'=>'1',
                        'Numero_Commande_Magento'=>'000001',
                        'Email_Client_Cde'=>'',
                        'Code_Client_Divalto'=>'C0000043',
                        'Code_Adresse_Livraison'=>'',
                        'Adresse_Livraison_Manuelle'=>array('Rue'=>'37 RUE MARYSE BASTIE','Ville'=>'LYON','Code_Postal'=>'69008'),
                        'Code_Adresse_Facturation'=>'',
                        'Paiement'=>'Processing',
                        'liste_detail_ligne'=>array(array('SKU'=>'00001AIBN','Quantite_Commandee'=>'10','Prix_Unitaire_TTC'=>'','Prix_Unitaire_HT'=>'100','Montant_Ligne'=>'1000')),
                        'Client_Particulier'=>array(
                            'Email_Client'=>'','Raison_Sociale'=>'POLAT','Titre'=>'SAS','Telephone'=>'0610158941',
                            'Contact'=>array('Nom'=>'','Prenom'=>'','Telephone'=>'','Email'=>'muratk21@hotmail.com','Fonction'=>'')
                        )
                    ];

                }

            }

            $html .= '<p>Action => '.$action.' <p>';
            
            if( isset($_GET['printJson'])) {
                echo $html;
                echo '<pre style="overflow:scroll">';
                echo json_encode($postData);
                echo '</pre>';
                die('<b>Die : printJson</b>');
            }

            $response = $this->_helperRequester->getDivaltoCustomerData($postData, $action);

            

            if(!$response) {
                
                echo $html;
                $html .='<p>error response</p>';
              
            } else {
               
                $html .='<h2>Response :</h2>';
                echo $html;
                echo '<pre"/>';
                print_r($response);
                echo '</pre>';
            }

            echo '</div>';

        }
        
    }

}