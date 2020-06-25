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
 * @subpackage Helper
 * @author SySwatis (Stéphane JIMENEZ)
 * @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)
 */
 
namespace Divalto\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;
   
class Order extends AbstractHelper
{ 
    protected $orderRepository;
  
    protected $searchCriteriaBuilder;
    
    public function __construct(
        Context $context, 
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);
    }
    
    public function getOrderById($id) {
        return $this->orderRepository->get($id);
    }
    
    public function getOrderByIncrementId($incrementId) {
        
        $this->searchCriteriaBuilder->addFilter('increment_id', $incrementId);
  
        $order = $this->orderRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems(); // FirstItem ???
  
        return $order;
    }

    public function getOrderFormatedData($order) {

//         {
//     'Numero_Dossier' : '2',
//     'Numero_Commande_Magento': '123456',
//     'Email_Client_Cde': 'contact@pachadistribution.com',
//     'Code_Client_Divalto': '',
//     'Code_Adresse_Livraison': '',
//     'Adresse_Livraison_Manuelle': {
//             'Rue': '37 RUE MARYSE BASTIE',
//             'Ville': 'LYON',
//             'Code_Postal': '69008'
//         },
//     'Code_Adresse_Facturation': '',
//     'Paiement': 'Proceccing',
//     'liste_detail_ligne': [
//             {
//             'SKU': '00001AIBN',
//             'Quantite_Commandee': '10',
//             'Prix_Unitaire_TTC': '',
//             'Prix_Unitaire_HT': '100',
//             'Montant_Ligne':'1000'
//             }
//         ],
//     'Client_Particulier':{
//                 'Email_Client':'lilian.doraizo@hotmail.fr',
//                 'Raison_Sociale':'DODO & CO',
//                 'Titre':'SAS',
//                 'Telephone':'0610158941',
//                 'Contact':{                             
//                             'Nom':'',
//                             'Prenom':'',
//                             'Telephone':'',
//                             'Email':'',
//                             'Fonction':''
//                         }
//                 }   
// }

        $data = array();
        $row = array();

        $data= array(
            'Numero_Dossier' => '2', // config
            'Numero_Commande_Magento'=> $order->getIncrementId(),
            'Email_Client_Cde'=> 'contact@pachadistribution.com', // ?
            'Code_Client_Divalto'=> '',// get customer group
            'Code_Adresse_Livraison'=> '',
            'Adresse_Livraison_Manuelle'=> array(
                'Rue'=> '37 RUE MARYSE BASTIE',
                'Ville'=> 'LYON',
                'Code_Postal'=> '69008'
            ),
            'Code_Adresse_Facturation'=> '',
            'Paiement'=> 'Proceccing',
            // 'Methode_Livraison'=>'' ??????????????????????????????? => shipping description
        );

        foreach ($order->getAllItems() as $item)
        {
           $row['SKU'] = $item->getId();
           // echo $item->getProductType();
           $row['Quantite_Commandee'] = $item->getQtyOrdered();
           $row['Prix_Unitaire_TTC'] = $item->getPrice(); // 
           $row['Montant_Ligne'] = ''; // TTC
        }

        $data['liste_detail_ligne'] = $row;

        $data['Client_Particulier'] = array('Email_Client' => $order->getCustomerEmail());

        return $data;
    }
}