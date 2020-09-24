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

Une clé API* sécurise la transmission des données :

- 1) Gestion du client : ajout uniquement
- 2) Gestion de la commande : ajout uniquement

<b>Attention</b>, l'essentiel de la gestion se fait sur l'ajout, <b>pas de suppression, ni d'édition</b> de données dans cette première version.

###### *Administrable dans la partie configuration du module Magento

### WorkFlow

#### Résumés des étapes Client/Server

##### 1) Session Client Magento
Connexion ou création de compte
##### 2) Retour réponses : 
Api Key check<br>
Messages d'erreurs<br>
Status serveur<br>
Code Sociéte<br>
Autorisation de paiements<br>
N° de commande<br>
##### 3) Mise à jour des données Magento :
Messages d'erreurs -> Session client (front) + Divalto Response (customer attr.)<br>
Status serveur -> Divalto Response (customer attr)<br>
Code Sociéte -> Groupe Client + Divalto Account Id (customer attr.)<br>
Autorisation de paiements -> Divalto Outstanding Status (customer attr.)<br>
N° de commande -> Historique de commentaires<br>

#### La demande du CDC

Ce descriptif (rédigé en collaboration entre agences & client) est à titre explicatif de la gestion globale. Il peut permettre une meilleure compréhension des fonctionnalités développées sur ce module.

###### PRICE_APPLIED

L'intégration des clients par Divalto créera les groupes clients nécessaires aux group price.
L'intégration des group Price se fera par Magento pour l'initialisation (import) puis par Divalto en webservices pour les mises à jour.
Ainsi, un client créé dans Magento aura accès à ses group price, le cas échéant.

###### OUTSTANDING_STATUS

A la creation d'un client, Magento attend la valeur du outstanding_status
Sans reponse de Divalto, la valeur par défaut est "CB uniquement"
L'attribut customer outstanding_status conditionnera les méthodes de paiement proposées au client.
La portée de cet attribut est dans le client (contact) et non dans le groupe (société).

###### ORDER_PLACED

Les webservices de commandes sont appelés à la mise à jour d'un statut (pending pour les bons de commande / processing pour CB).
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
Dans le cas précis du Cdc, on autorisera le module "Purshase Order" (ou bon commande) & 

#### Les attributs

Les attributs "customer" sont ajouté à l'installation du module.
Pour ajouter un nouvel attribut, il faut upgrader la version de ce dernier

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
	- Helper => Fonctionnalités générales
	- i18n => Traductions (csv)
	- Logger => Générateur des logs
	- Model => Fonctionnalités spécifiques
	- Observer => Fonctionnalités 
	- Setup => Installateur (attributs, upgrade)
	- view => Frontend (user & admin) : pHtml, js, layout

### Fonctionnalités détaillées

	Helper
	- Data
	- Requester

## Administration Magento

### Le menu "Divalto"

#### Mode test

Permet de vérifier la communication avec l'url* "Api Url Test" du serveur distant et de valider les actions sur la base de données statiques avec les boutons :

##### Ping
Retourne le status 200 si succès.
Une <b>latence trop importante</b> et/ou une erreur timeout (curl) est probablement due à un <b>problème d'IP(s) non autorisées</b> sur le serveur distant.
##### Créer un Client
Retourne le code client si succès.
##### Créer une Commande
Retourne le n° de commande Divalto si succès.

#### Configuration

Raccourci vers la section "Divalo > Client" contenant tous les réglages du module (cf. ci-dessous).

![alt text]*Administrable dans la partie configuration du module Magento

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
##### Statut de la commande
Status autorisés à l'appel du serveur Divalto (Créer Commande).
##### Mode de paiement
Paiements autorisés à l'appel du serveur Divalto (Créer Commande).
##### Forme juridique
Liste des formes juridiques entreprise (Titre - cf. Mapping).

## Compte client (utilisateur fontend)

### Création de compte

L'accès à la création de compte est ouvert à tous les visiteurs. Son inscription est soumise à la validation du serveur Divalto. Si ce dernier n'a pas été reconnu (adresse email). Le compte est toutefois enregistré sur Magento, il cependant est averti par un message d'erreur "Compte client non validé, merci de nous contacter".

#### Siret

Désactivé sur le formulaire au profit du VAT. Le champ pays est obligatoire pour établir la validation européenne en lien avec la TVA (cf. VAT).

#### VAT

Validation effectuée à la création de compte par le format & à la validation du VAT  (http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl).

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
            'Code_Adresse_Facturation'=>'',
            'Paiement'=>'processing',
            'liste_detail_ligne'=>$orderDataItems,
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


## Contribute