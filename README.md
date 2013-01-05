My-M-GaTE
=========

# Installation d�taill�

1. Installer WAMPSERVEUR Apache 2.2.22 et PHP 5.4.3, attention prendre la version 32bits !!!
Changer le port 80 en port 81 : Clique gauche sur wamp > Apache > httpd.conf > remplacer "Listen 80" en "Listen 81"
Activer le module ssl : Clique gauche sur wamp > Apache > Apache Module > ssl_module
Clique gauche sur wamp > PHP > PHP Extensions > php_openssl
Clique gauche sur wamp > PHP > PHP Extensions > php_intl
Clique gauche sur wamp > PHP > Version > s�l�ctionnez une version >= � 5.3.8

2. Installer https://help.github.com/articles/set-up-git#platform-windows

3. Cloner My-M-GaTE avec l'interface graphique.
Se cr�er un compte github, et me communiquer votre pseudo
Clique droit sur My-M-GaTE dans le logiciel de github puis "clone to" choisir le dossier dans C:/wamp/www
Il faut faire en sorte que http://127.0.0.1:81/My-M-GaTE/ pointe sur le repository
(c'est aussi possible avec un alias: Clique gauche sur wamp > Apache > Apache directories)

4. Configurer Symfony2:
Cr�er le fichier app/config/parameters.yml
Contenant ceci :
parameters:
    database_driver:   pdo_mysql
    database_host:     localhost
    database_port:     ~
    database_name:     symfony
    database_user:     root
    database_password: ~

    mailer_transport:  smtp
    mailer_host:       localhost
    mailer_user:       ~
    mailer_password:   ~

    locale:            fr
    secret:            ThisTokenIsNotSoSecretChangeIt

5. Ajouter PHP � ses variables d'environnements :
Clique droit sur Ordinateur > Propri�t�s > Modifier les param�tres > Param�tres Syst�me avanc�s > Variables d'environnement
Partie "variable syst�me", modifier l'entr�e "Path" et rajouter ;c:\wamp\bin\php\php5.4.3  (en v�rifiant que ce dossier existe bien, sinon adapter)

6. T�l�chargement/Installation des vendors:
- Sur le logiciel de github, clique droit sur My-M-GaTE "open a shell here"
- Taper: curl -s https://getcomposer.org/installer | php
- Taper: php composer.phar update
- Faire autre chose et attendre

7. Installer la base de donn�e
php app/console doctrine:database:create
php app/console doctrine:schema:update --force

8. Tester la configuration sur http://127.0.0.1:81/My-M-GaTE/web/config.php
Cr�er les dossiers app/cache et app/logs

9. Tester My-M-GaTE
http://127.0.0.1/My-M-GaTE/web/app_dev.php/suivi

# Remarque
En cas de probl�me d'acces pour phpmyadmin:
Clique gauche sur wamp > Apache > Alias directories > http://localhost/phpmyadmin > Edit alias, mettre :
<Directory "c:/wamp/apps/phpmyadmin3.5.1/"> # adapter la version
   Options Indexes FollowSymLinks MultiViews
    AllowOverride all
        Order Deny,Allow
        Allow from all
</Directory>
Autre probl�me courant : il faut pr�f�rer utiliser 127.0.0.1:81 au lieu de localhost:81