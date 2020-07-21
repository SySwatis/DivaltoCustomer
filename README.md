# Divalto_Customer
- Module for magento 2
- Manage customer WS communication
- Version 0.1.0

## Installation

composer require divalto/customer -dev-master

## Contribute
## Description
Ce module assure le flux d'échange de Magento 2 (CMS e-commerce) vers Divalto (ERP solution).
Clients & commandes sont poussés selon un montage spécifique et adpaté pour le compte de "Food Center Group".

Il agit en deux temps :

- 1) Gestion du client
- 2) Gestion de la commande

## WS Divalto
Une clé API sécurise la transmission des données. Elle est administrable dans la partie configuration du module Magento 2

## Description MVC
En cours d'élaboration, cette description sera prochainement complétée et approfondie.

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




## Admin
Le menu "Divalto" dans l'admin permet de vérifier la communication avec le serveur distant et de valider les actions sur la base de données statiques.

## Création de Clients
Les attributs
Le Numéro de TVA

## Création de commandes
La création des commandes en direction de l'ERP s'effectue uniqument via le status "processing".

## Configuration (Admin)

- Activer le module (Oui/Non) | Active ou desactive les evenements de l'observer en relation avec Divalto
- Activer le dbug (Oui/Non) | Active ou desactive les evenements de l'observer en relation avec Divalto
- Clé Api | Clé de validation d'échange avec Divato (serveur)
- Dossier magasin | dig. 1, 2, 3, ... Identifiant du magasin associé au flux
- Validation commandes (Taxe) 
