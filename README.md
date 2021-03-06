# Divalto Customer

- Module for magento 2
- Manage customer WS communication
- Version 0.1.0
- @author SySwatis (Stéphane JIMENEZ)
- @copyright Copyright (c) 2020 SySwatis (http://www.syswatis.com)

## Installation

composer require divalto/customer:dev-master

## Description

Ce module assure le flux d'échange de données entre Magento 2 (CMS e-commerce) et Divalto (ERP solution).<br>
Clients & commandes sont poussés simultanément vers l'ERP selon un montage spécifique et adapté pour le compte de <b>Stokhall</b> (ex Food Center).

## Web Service Divalto (Serveur)

### Gestions des données

Une <b>clé API</b>* sécurise la transmission des données :

1. Gestion du client : *ajout uniquement*
2. Gestion de la commande : *ajout uniquement*

<b>Attention</b>, l'essentiel de la gestion se fait sur l'ajout, <b>pas de suppression, ni d'édition</b> de données dans cette première version.

**Administrable dans la partie configuration du module Magento*

### WorkFlow

#### Résumés des étapes Client/Server

##### 1) Session Client Magento :

Connexion ou création de compte

##### 2) Retour réponses :

Api Key check<br>
Messages d'erreurs<br>
Status serveur<br>
Code Sociéte<br>
Autorisation de paiements<br>
N° de commande

##### 3) Mise à jour des données Magento :
Messages d'erreurs -> Session client (front) + Divalto Response (customer attr.)<br>
Status serveur -> Divalto Response (customer attr)<br>
Code Sociéte -> Groupe Client + Divalto Account Id (customer attr.)<br>
Autorisation de paiements -> Divalto Outstanding Status (customer attr.)<br>
N° de commande -> Historique de commentaires

#### La demande du CDC

Ce descriptif (rédigé en collaboration entre agences & client) est à titre explicatif de la gestion globale. Il peut permettre une meilleure compréhension des fonctionnalités développées sur ce module.

###### PRICE_APPLIED

> L'intégration des clients par Divalto créera les groupes clients nécessaires aux group price.
L'intégration des group Price se fera par Magento pour l'initialisation (import) puis par Divalto en webservices pour les mises à jour.
Ainsi, un client créé dans Magento aura accès à ses group price, le cas échéant.

###### OUTSTANDING_STATUS

> A la creation d'un client, Magento attend la valeur du outstanding_status
Sans reponse de Divalto, la valeur par défaut est "CB uniquement"
L'attribut customer outstanding_status conditionnera les méthodes de paiement proposées au client.
La portée de cet attribut est dans le client (contact) et non dans le groupe (société).

###### ORDER_PLACED

> Les webservices de commandes sont appelés à la mise à jour d'un statut (pending pour les bons de commande / processing pour CB).
Toutes les informations de l'entête de commande (client, adresses, totaux ..etc) + les lignes de commandes (articles, quantité, prix ...etc) sont envoyés à Divalto
La reponse attendue correspond au numero(s) de commande(s) Divalto (qui sera intégré sous forme de commentaires de commandes).

#### Les actions du modules

##### Création de Clients

Cf. Création de compte

##### Création de commandes

La création des commandes en appel serveur se fait <b>uniquement</b> sur le status magento "processing".
Ce paramètre est figé en constante "DIVALTO_STATE_PROCESSING".
La configuration du module permet l'appel du serveur selon mode de paiment ou le status d'une commande.
Ceci permet de gérer l'évènement d'un changement de status.
Dans le cas précis du Cdc, on autorisera le module "Purshase Order" (ou bon commande).

08/02/2021

Modification du module autorisé "Checkmo" au lieu de "Purshase Order". 

#### Les attributs

Les attributs "customer" sont ajoutés à l'installation du module.
Attention, un attribut supplémentaire nécessite un upgrade de version.

##### Clients

	divalto_account_id,
	divalto_outstanding_status
	ape
	siret
	legal_form
	company_name
	divalto_response
	divalto_extrafield_1
	divalto_extrafield_2

##### Commandes

Aucuns

### Le MVC

	- Block
	- Controller => Urls : admin, customer, validation
	- etc => Config : menu, url, module, admin config, events
	- Helper => Fonctionnalités principales
	- i18n => Traductions (csv)
	- Logger => Générateur des logs
	- Model => Fonctionnalités spécifiques
	- Observer => Fonctionnalités 
	- Setup => Installateur (attributs, upgrade)
	- view => Frontend (user & admin) : pHtml, js, layout

## Administration Magento

### Le menu "Divalto"

#### Mode test

Ce mode permet de vérifier la communication avec l'url* "Api Url Test" du serveur distant et de valider les actions sur la base de données statiques avec les boutons "Ping","Créer un Client","Créer une Commande" (cf. configuration).

**Ping (test)**

Retourne le status 200 si succès.
Une <b>latence trop importante</b> et/ou une erreur timeout (curl) est probablement due à un <b>problème d'IP(s) non autorisées</b> sur le serveur distant.

**Créer un Client (test)**

Retourne le code client (test) si succès avec "Numero_Dossier"* & "Contact.Email"*.<br>
Tableau source Json dataCustomerTest() : "Divalto/Customer/Helper/Data.php".


	$postData = '{"Numero_Dossier":"'.$divaltoStoreId.'","Email_Client":"'.$emailTest.'","Raison_Sociale":"","Titre":"","Telephone":"","Numero_Siret":"","Code_APE":"","Numero_TVA":"FR999999999","Adresse_Facturation":{"Rue":"","Ville":"","Code_Postal":"","Pays":""},"Adresse_Livraison":{"Rue":"","Ville":"","Code_Postal":"","Pays":""},"Contact":{"Nom":"","Prenom":"","Telephone":"","Email":"'.$emailTest.'","Fonction":""}}';


**Administrable dans la partie configuration du module Magento*

**Créer une Commande (test)**

Retourne le n° de commande Divalto si succès avec "Email_Client_Cde"* & Code_Client_Divalto"*.<br>
Tableau source Json dataOrderTest() : "Divalto/Customer/Helper/Data.php".


	$postData = '{"Numero_Dossier":"'.$divaltoStoreId.'","Numero_Commande_Magento":"000001","Email_Client_Cde":"'.$emailTest.'","Code_Client_Divalto":"'.$codeTest.'","Code_Adresse_Livraison":"","Adresse_Livraison_Manuelle":{"Rue":"37 RUE MARYSE BASTIE","Ville":"LYON","Code_Postal":"69008","Pays":"FR"},"Code_Adresse_Facturation":"","Paiement":"processing","liste_detail_ligne":[{"SKU":"00001AIBN","Quantite_Commandee":"10","Prix_Unitaire_TTC":"","Prix_Unitaire_HT":"100","Montant_Ligne":"1000"}],"Client_Particulier":{"Email_Client":"","Raison_Sociale":"POLAT","Titre":"SAS","Telephone":"0610158941","Contact":{"Nom":"","Prenom":"","Telephone":"","Email":"'.$emailTest.'","Fonction":""}}}';

**Administrable dans la partie configuration du module Magento*

#### Configuration

Raccourci vers la section "Divalo > Client" contenant tous les réglages du module (cf. "Configuration" ci-dessous).

**Administrable dans la partie configuration du module Magento*


## Compte client (utilisateur fontend)

### Création de compte

L'accès à la création de compte est ouvert à tous les visiteurs. Son inscription est soumise à la validation du serveur Divalto. Si ce dernier n'a pas été reconnu (adresse email, tva intr.). Le compte est toutefois enregistré sur Magento mais l'utilisateur ne peut pas valider de commande (outstanding "0"). Son groupe client est créé si nécessaire mais il est assigné temporairement au "Géneral" (tarification de base).
Il est cependant averti par un message d'alerte : "Compte client non validé, merci de nous contacter" et sur le tableau de bord de son compte client.

#### Siret

Désactivé sur le formulaire au profit du VAT. Le champ pays est obligatoire pour établir la validation européenne en lien avec la TVA (cf. VAT).

#### VAT

Validation effectuée à la création de compte par le format & à la validation du VAT  (http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl).

**Administrable dans la partie configuration du module Magento*

#### Ape

Validation effectuée à la création de compte par le format.

### Gestion de compte

#### Gestion des adresses (Facturation & livraison)

Uniquement en lecture, l'utilisateur n'a pas accès à l'édition de ces données.
Un message et un accès au formulaire de contact permet de demander une mise à jour des données.
Pas d'interactions, ni d'interfacages de mises à jour Serveur/Client. Cf. Gestions des données.

#### Factures

Le fonctionnement de base de magento de cette partie a été supprimé.
Elles sont donc déposées (en externe) et stockées au format PDF 
dans le répertoire identifié sur avec le code société Divalto.
L'utilisateur a accès à lecture de la liste des fichiers.
Leurs noms, hiérarchisations, limitations de dépots sont définis hors cadre module & CMS Magento.

#### Autorisation de paiements
L'utilisateur peut voir ses autorisations de paiement dans la section "En cours"

## Commandes

### Mapping

Extrait source code : "Divalto/Customer/Model/OrderMap.php"

	$orderData = [
            'Numero_Dossier'=>$divaltoStoreId,
            'Numero_Commande_Magento'=>$order->getIncrementId(),
            'Email_Client_Cde'=>$order->getCustomerEmail(),
            'Code_Client_Divalto'=>$groupCode,
            'Code_Adresse_Livraison'=>'',
            'Adresse_Livraison_Manuelle'=>$shippingAddressData,
            'Adresse_Facturation_Manuelle'=>$billingAddressData,
            'Code_Adresse_Facturation'=>'',
            'Paiement'=>'processing',
            'liste_detail_ligne'=>$orderDataItems,
            'MontantLivraison'=>$this->getShipingChargeOrder($order),
            'Client_Particulier'=>array(
                'Numero_TVA'=>$customerOrder->getTaxvat(),
                'Code_Ape'=>$this->getCustomerAttributeValue($customerOrder,'ape'),
                'Email_Client'=>'',
                'Raison_Sociale'=>$this->getCustomerAttributeValue($customerOrder,'company_name'),
                'Titre'=>$this->getCustomerAttributeValue($customerOrder,'legal_form'),
                'Telephone'=>$shippingAddress->getTelephone(),
                'Contact'=>array(
                    'Nom'=>$order->getCustomerLastname(),
                    'Prenom'=>$order->getCustomerFirstname(),
                    'Telephone'=>$billingAddress->getTelephone(),
                    'Email'=>$order->getCustomerEmail(),
                    'Fonction'=>$order->getCustomerPrefix()
                )
            )
        ];

## Logs

	-/var/log/divalto/customer/debug.log
	-/var/log/debug.log
	-/var/log/system.log

### Configuration

##### Activer le module (Oui/Non)
Active ou désactive les évènements de l'observer en relation avec Divalto (ne désactive pas le mode test).
##### Api Url
Addresse Url du Serveur Divalto de production.
##### Api Url Test
Addresse Url du Serveur Divalto de pre-production.
##### SSL Peer’s Certificate
Sécurité d'échange de données (curl option). Mettre oui, si installé sur le serveur.<br>
Pris en compte également dans le mode test.
##### Clé Api
Clé de validation d'échange avec Divato (serveur).
##### Dossier magasin
Format dig. 1, 2, 3, ... Identifiant du magasin associé au flux.
##### Validation commandes (Taxe)
Règle de validation des totaux des lignes de commandes selon la règle (HT/TTC).
##### Email Test
Email utilisé pour les modes test "Créer Client" & "Créer Commande"
##### Code Test
Code Société Divalto utilisé pour le mode test "Créer Commande".
##### Data Order Test
Laisser vide pour utiliser les données test du module ou personnaliser le tableau et ses données ici.
##### Data Customer Test
Laisser vide pour utiliser les données test du module ou personnaliser le tableau et ses données ici.
##### Statut de la commande
Status autorisés à l'appel du serveur Divalto (Créer Commande).
##### Mode de paiement
Paiements autorisés à l'appel du serveur Divalto (Créer Commande).
##### Forme juridique
Liste des formes juridiques entreprise (Titre - cf. Mapping).
##### Validation VAT
Activer la validation à distance du numéro de TVA intracommunautaire sur le formulaire de création de compte.

## Contribution


## Todo

### Test création d'un nouveau client au passage de la commande

    Action "Créer Client" au login, valider l'utilisation
    Action "Créer Client" au passage de la commande, point de conception à revoir 

### Réponse Divalto du "Code Société" (ou Groupe Client magento)

    a) Email inconnu et Société connue
    b) Email inconnu et Société inconnue

### Notes

    29/09/2020 
    ----------

    les données "codes" sont absentes en réponse "Creer Commande".
    Les emails "Contact.Email" & "Email_Client_Cde" sont indentiques à l'envoie de la commande.
    Le retour réponse serait un "code contact" dans les cas a) ou b) ?

    13/10/2020
    ----------

    Le process de création client a été réactivé avec une nouvelle conception :
    L'envoie côté client contient : "Contact.Email" & "Email_Client_Cde" + "Numero_TVA"
    La réponse du serveur Divalto "liste_contact" contient :
    - le status d'autorisation de paiement (outstanding) "autorisation_Paiement"
    - les ids codes "code_Client" & code_Contact

    03/11/2020

    Mise à jour des datas orders avec les champs, adresse facturation, montant de la livraison

    Data Order test :

    {
        "Numero_Dossier": "1",
        "Numero_Commande_Magento": "000001",
        "Email_Client_Cde": "muratk21@hotmail.com",
        "Code_Client_Divalto": "C0000043",
        "Code_Adresse_Livraison": "",
        "Adresse_Livraison_Manuelle": {
            "Rue": "37 RUE MARYSE BASTIE",
            "Ville": "LYON",
            "Code_Postal": "69008",
            "Pays": "FR"
        },
        "Adresse_Facturation_Manuelle": {
            "Rue": "1 IMPASSE SOUS LA GRIMAUDIERE",
            "Ville": "DIEMOZ",
            "Code_Postal": "38790",
            "Pays": "FR"
        },
        "Code_Adresse_Facturation": "",
        "Paiement": "processing",
        "Montant_Livraison": "10",
        "liste_detail_ligne": [{
            "SKU": "00001AIBN",
            "Quantite_Commandee": "10",
            "Prix_Unitaire_TTC": "",
            "Prix_Unitaire_HT": "100",
            "Montant_Ligne": "1000"
        }],
        "Client_Particulier": {
            "Email_Client": "",
            "Raison_Sociale": "POLAT",
            "Titre": "SAS",
            "Telephone": "0610158941",
            "Contact": {
                "Nom": "",
                "Prenom": "",
                "Telephone": "",
                "Email": "muratk21@hotmail.com",
                "Fonction": ""
            }
        }
    }

    ou (MontantLivraison)

    {
      "Numero_Dossier": "1",
      "Numero_Commande_Magento": "123456879",
      "Email_Client_Cde": "zeggriim@sgagence.com",
      "Code_Client_Divalto": "C0000795",
      "Code_Adresse_Livraison": "",
      "Adresse_Livraison_Manuelle": {
        "Rue": "37 RUE MARYSE BASTIE",
        "Ville": "LYON",
        "Code_Postal": "69008",
        "Pays": "FR"
      },
      "Adresse_Facturation_Manuelle": {
        "Rue": "1 IMPASSE SOUS LA GRIMAUDIERE",
        "Ville": "DIEMOZ",
        "Code_Postal": "38790",
        "Pays": "FR"
      },
      "Code_Adresse_Facturation": "",
      "Paiement": "Processing",
      "MontantLivraison": "",
      "liste_detail_ligne": [
        {
          "SKU": "00001AIBN",
          "Quantite_Commandee": "10",
          "Prix_Unitaire_TTC": "",
          "Prix_Unitaire_HT": "100.000",
          "Montant_Ligne": "1000"
        }
      ],
      "Client_Particulier": {
        "Email_Client": "",
        "Raison_Sociale": "",
        "Titre": "",
        "Telephone": "",
        "Contact": {
          "Nom": "",
          "Prenom": "",
          "Telephone": "",
          "Email": "",
          "Fonction": ""
        }
      }
    }


