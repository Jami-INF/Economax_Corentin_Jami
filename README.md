# Dealabs

## Description

L'objectif de ce projet est de reproduire le site Dealabs en Symfony 6. Nous souhaitons mettre en avant des deals aux visiteurs du site et de permettre aux utilisateurs authentifiés de valoriser des deals via un système de vote (hot si supérieur à 100
degrés).

## Mise en place du projet


### Docker
*  Copier l'ensemble des fichiers relatifs à Docker à la racine de votre projet Symfony

*  Builder l'image : `docker-compose build`
*  Lancer les conteneurs : `make start_dev`
*  Arrêter les conteneurs : `make stop_dev`
*  Accéder au container PHP en tant que root : `docker exec -it -u root lpa_sf6_php bash`

*  Apache : `http://localhost:8081`
*  PhpMyAdmin : `http://localhost:8090`
*  Mailcatcher - Interface web : `http://localhost:1081`, Port SMTP : `1026`
*  MySQL : Port `3310`

### Symfony
* Aller dans le dossier du projet : `cd Economax`
* Installer les dépendances : `composer install`
* Créer la base de données : `php bin/console doctrine:database:create`
* Mettre a jour le schéma de la base : `php bin/console doctrine:schema:update --force --complete`
* Charger les fixtures : `php bin/console doctrine:fixtures:load`

### Node
* Installer les dépendances : `npm install`
* Compiler les assets : `npm run dev`

* Lancer le serveur php : `php -S localhost:8080 -t public/`

### Les commandes
* Créer un administrateur : `php bin/console app:create-admin`
* Envoyer les emails contenant les alertes : `php bin/console app:send-email-alert`
