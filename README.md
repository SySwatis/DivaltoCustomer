# Divalto_Customer
- Module for magento 2
- Manage customer WS communication
- Version 0.1.0

## Installation

composer require divalto/customer:dev-master

## Description

Ce module assure le flux d'échange de Magento 2 (CMS e-commerce) vers Divalto (ERP solution).<br>
Clients & commandes sont poussés simultanément vers l'ERP selon un montage spécifique et adapté pour le compte de "Stokhall" (ex Food Center).

## Web Service Divalto (Serveur)

### Gestions des données

Une clé API * sécurise la transmission des données.

- 1) Gestion du client : ajout uniquement
- 2) Gestion de la commande : ajout uniquement

*Administrable dans la partie configuration du module Magento 2

### WorkFlow

#### Résumés des étapes Client/Server

- ##### 1) Session Client Magento
- ##### 2) Retour réponses : 
	- Api Key check
	- Messages d'erreurs
	- Status serveur
	- Code Sociéte
	- Autorisation de paiements
	- N° de commande
- ##### 3) Mise à jour des données Magento :
	- Messages d'erreurs -> Session client (front) + Divalto Response (customer attr.)
	- Status serveur -> Divalto Response (customer attr)
	- Code Sociéte -> Groupe Client + Divalto Account Id (customer attr.)
	- Autorisation de paiements -> Divalto Outstanding Status (customer attr.)
	- N° de commande -> Historique de commentaires


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

    Les webservices de commandes sont appelés à la mise à jour d'un statut (pending pour les bons de commande / processing pour CB)
    Toutes les informations de l'entête de commande (client, adresses, totaux ..etc) + les lignes de commandes (articles, quantité, prix ...etc) sont envoyés à Divalto
    La reponse attendue correspond au numero(s) de commande(s) Divalto (qui sera intégré sous forme de commentaires de commandes)

#### Les actions du modules

- ##### Création de Clients
- ##### Création de commandes

La création des commandes en direction de l'ERP s'effectue uniquement sur le status "processing".

#### Les attributs

- ##### Clients
- ##### Commandes
- ##### Le Numéro de TVA

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

### Le menu "Divalto

Le menu "Test" permet de vérifier la communication avec le serveur distant et de valider les actions sur la base de données statiques :
- Ping : Une latence trop importante et/ou une erreur timeout (curl) est probablement due à un problème d'IP(s) non reconnus sur le serveur distant.

### Configuration

- Activer le module (Oui/Non) | Active ou désactive les évènements de l'observer en relation avec Divalto (ne désactive pas le mode test)
- Clé Api | Clé de validation d'échange avec Divato (serveur)
- Dossier magasin | dig. 1, 2, 3, ... Identifiant du magasin associé au flux
- Validation commandes (Taxe) (HT/TTC) | Règle de validation des totaux des lignes de commandes

## Contribute