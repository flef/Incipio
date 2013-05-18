My-M-GaTE
=========

# Installation détaillée

1. Installer WAMPSERVEUR Apache 2.2.22 et PHP 5.4.3, attention prendre la version 32bits !!!
Changer le port 80 en port 81 : Clique gauche sur wamp > Apache > httpd.conf > remplacer "Listen 80" en "Listen 81"
Activer le module ssl : Clique gauche sur wamp > Apache > Apache Module > ssl_module
Clique gauche sur wamp > PHP > PHP Extensions > php_openssl
Clique gauche sur wamp > PHP > PHP Extensions > php_intl
Clique gauche sur wamp > PHP > Version > sélectionnez une version >=  5.3.8

2. Installer https://help.github.com/articles/set-up-git#platform-windows

3. Cloner My-M-GaTE avec l'interface graphique.
Se créer un compte github, et me communiquer votre pseudo
Clique droit sur My-M-GaTE dans le logiciel de github puis "clone to" choisir le dossier dans C:/wamp/www
Il faut faire en sorte que http://127.0.0.1:81/My-M-GaTE/ pointe sur le repository
(c'est aussi possible avec un alias: Clique gauche sur wamp > Apache > Apache directories)

4. Configurer Symfony2:
Créer le fichier app/config/parameters.yml
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

5. Ajouter PHP ses variables d'environnements :
Clique droit sur Ordinateur > Propriétés > Modifier les paramètres > Paramétres Système avancé > Variables d'environnement
Partie "variable système", modifier l'entrée "Path" et rajouter ;c:\wamp\bin\php\php5.4.3  (en vérifiant que ce dossier existe bien, sinon adapter)

6. Téléchargement/Installation des vendors:
- Sur le logiciel de github, clique droit sur My-M-GaTE "open a shell here"
- Taper: curl -s https://getcomposer.org/installer | php
- Taper: php composer.phar update
- Faire autre chose et attendre

7. Installer la base de données
php app/console doctrine:database:create
php app/console doctrine:schema:update --force

8. Tester la configuration sur http://127.0.0.1:81/My-M-GaTE/web/config.php
Créer les dossiers app/cache et app/logs

9. Tester My-M-GaTE
http://127.0.0.1/My-M-GaTE/web/app_dev.php/suivi

# Remarque
En cas de problème d'accès pour phpmyadmin:
Clique gauche sur wamp > Apache > Alias directories > http://localhost/phpmyadmin > Edit alias, mettre :
<Directory "c:/wamp/apps/phpmyadmin3.5.1/"> # adapter la version
   Options Indexes FollowSymLinks MultiViews
    AllowOverride all
        Order Deny,Allow
        Allow from all
</Directory>
Autre problème courant : il faut préférer utiliser 127.0.0.1:81 au lieu de localhost:81

# Erreurs possibles

In this entry, you embed only one collection, but you are not limited to this. You can also embed nested collection as many level down as you like. But if you use Xdebug in your development setup, you may receive a Maximum function nesting level of '100' reached, aborting! error. This is due to the xdebug.max_nesting_level PHP setting, which defaults to 100.

This directive limits recursion to 100 calls which may not be enough for rendering the form in the template if you render the whole form at once (e.g form_widget(form)). To fix this you can set this directive to a higher value (either via a PHP ini file or via ini_set, for example in app/autoload.php) or render each form field by hand using form_row.
