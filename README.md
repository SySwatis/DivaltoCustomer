# Divalto_Customer
- Module for magento 2
- Manage customer WS communication
- Version 0.1.0

## Installation

composer require divalto/customer:dev-master

## Contribute
## Description
Ce module assure le flux d'échange de Magento 2 (CMS e-commerce) vers Divalto (ERP solution).
Clients & commandes sont poussés selon un montage spécifique et adapté pour le compte de "Stokhall" (ex Food Center).

## Web Service Divalto (Serveur)

### Gestions des données

- 1) Gestion du client : ajout uniquement
- 2) Gestion de la commande : ajout uniquement

Une clé API sécurise la transmission des données. Elle est administrable dans la partie configuration du module Magento 2.

## Description du module Magento Divalto

En cours d'élaboration, cette description sera prochainement complétée et approfondie.

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

## Admin

### Le menu "Divalto

Le mode "test" permet de vérifier la communication avec le serveur distant et de valider les actions sur la base de données statiques :
- Ping : Une latence trop importante et/ou une erreur timeout (curl) est probablement due à un problème d'IP(s) non reconnus sur le serveur distant.

## Création de Clients
### Les attributs
### Le Numéro de TVA

## Création de commandes

La création des commandes en direction de l'ERP s'effectue uniquement via le status "processing".

## Configuration (Admin)

- Activer le module (Oui/Non) | Active ou désactive les évènements de l'observer en relation avec Divalto (ne désactive pas le mode test)
- Clé Api | Clé de validation d'échange avec Divato (serveur)
- Dossier magasin | dig. 1, 2, 3, ... Identifiant du magasin associé au flux
- Validation commandes (Taxe) (HT/TTC) | Règle de validation des totaux des lignes de commandes