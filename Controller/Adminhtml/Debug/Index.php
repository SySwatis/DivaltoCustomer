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

namespace Divalto\Customer\Controller\Adminhtml\Debug;

use Magento\Backend\App\Action;

class Index extends Action
{

    protected $_helperData;

    protected $_helperRequester;

    protected $_orderMap;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Divalto\Customer\Helper\Data $helperData,
        \Divalto\Customer\Helper\Requester $helperRequester,
        \Divalto\Customer\Model\OrderMap $orderMap
    )
    {
        parent::__construct($context);
        $this->_helperData = $helperData;
        $this->_helperRequester = $helperRequester;
        $this->_orderMap = $orderMap;
    }

    public function execute()
    {
       
        if( $this->_helperData->getDebugConfig()==1 ) {
            
            $action = 'No action';
            $allActions = array('CreerClient','CreerCommande');
            $postData = array('ini'=>'Post Data Empty');
            $response = array('ini'=>'Response Empty');

            $emailTest = "contact@pachadistribution.com";

            $sslVerifypeer =  $this->_helperData->getGeneralConfig('ssl_verifypeer') == 1 ? _('Yes') : _('No');


            $html =     '<div style="font-family:Gill Sans, sans-serif;padding:30px;"/>';
            $html .=    '<h1>Divalto Customer : Debug</h1>';
            $html .=    '<p><b>SSL Cert. Verfify Peer:</b> '.$sslVerifypeer.'</p>';
            $html .=    '<p><b>Email test: </b> '.$emailTest.'</p>';
            $html .=    '<p><b>Api Url Test (Debug):</b> '.$this->_helperData->getGeneralConfig('api_url_test').'</p>';
            $html .=    '<ul style="list-style:none;margin:0;padding:0;">';
            $html .=    '<li><a href="?action=CreerClient">CreerClient (Email test)</a></li>';
            $html .=    '<li><a href="?action=CreerCommande&OrderId=demo">CreerCommande Demo</a></li>';
            $html .=    '</ul>';

            if( isset($_GET['action']) && in_array($_GET['action'],$allActions) ) {

                $action = $_GET['action'];
               
                if($action === 'CreerClient') {
                
                    $postData = [
                        "Numero_Dossier"=>"2",
                        "Email_Client"=>"",
                        "Raison_Sociale"=>"",
                        "Titre"=>"",
                        "Telephone"=>"",
                        "Numero_Siret"=>"",
                        "Code_APE"=>"",
                        "Numero_TVA"=>"",
                        "Adresse_Facturation"=>array("Rue"=>"","Ville"=>"","Code_Postal"=>"","Pays"=>""),
                        "Adresse_Livraison"=>array("Rue"=>"","Ville"=>"","Code_Postal"=>"","Pays"=>""),
                        "Contact"=>array("Nom"=>"","Prenom"=>"","Telephone"=>"","Email"=>$emailTest,"Fonction"=>"")
                    ];

                }

                if($action === 'CreerCommande') {

                    if( isset($_GET['OrderId']) && $_GET['OrderId']=='demo' )  {
                        $postData = [
                            'Numero_Dossier'=>'1',
                            'Numero_Commande_Magento'=>'000001',
                            'Email_Client_Cde'=>'contact@pachadistribution.com',
                            'Code_Client_Divalto'=>'C0000001',
                            'Code_Adresse_Livraison'=>'',
                            'Adresse_Livraison_Manuelle'=>array('Rue'=>'37 RUE MARYSE BASTIE','Ville'=>'LYON','Code_Postal'=>'69008','Pays'=>'FR'),
                            'Code_Adresse_Facturation'=>'',
                            'Paiement'=>'processing',
                            'liste_detail_ligne'=>array(array('SKU'=>'00001AIBN','Quantite_Commandee'=>'10','Prix_Unitaire_TTC'=>'','Prix_Unitaire_HT'=>'100','Montant_Ligne'=>'1000')),
                            'Client_Particulier'=>array(
                                'Email_Client'=>'','Raison_Sociale'=>'POLAT','Titre'=>'SAS','Telephone'=>'0610158941',
                                'Contact'=>array('Nom'=>'','Prenom'=>'','Telephone'=>'','Email'=>'muratk21@hotmail.com','Fonction'=>'')
                            )
                        ];
                    }

                    if( isset($_GET['OrderId']) && is_numeric($_GET['OrderId']) ) {
                        $postData = $this->_orderMap->create($_GET['OrderId']);
                    }

                }

                // Param 3, debug url

                $response = $this->_helperRequester->getDivaltoCustomerData($postData, $action, true);

            }

            $html .= '<p>Action => '.$action.' <p>';
            
            if( isset($_GET['printJson'])) {
                echo $html;
                echo '<h2>Post Data (Json) :</h2>';
                echo '<pre style="overflow:scroll">';
                echo json_encode($postData);
                echo '</pre>';
                die('<b>Die : printJson</b>');
            }

            if( !is_array($response) || !$response || count($response)==0 ) {
                
                echo $html;
                $html .='<p>error response</p>';
                echo '<pre>';
                echo '<h2>Post Data :</h2>';
                print_r($postData);
                echo '</pre>';
              
            } else {
               
                $html .='<h2>Response :</h2>';
                echo $html;
                echo '<pre>';
                print_r($response);
                echo '</pre>';
                echo '<h2>Post Data :</h2>';
                echo '<pre style="overflow:scroll">';
                print_r($postData);
                echo '</pre>';
            }

            echo '</div>';

        } else {
            echo "debug disable";
        }
        
    }
}