# Attribuer des droits à un utilisateur

L'attribution de droits à un utilisateur se fait en se connectant à l'API en ssh, puis via le bundle FOS User de Symfony 3.

- Se connecter en SSH au serveur
- Naviguer (via la commande `cd`) vers `www/web_main/api`
- Exécuter la commande `php bin/console fos:user:promote mail@user.fr [role à donner]`. Les rôles sont principalement ROLE_USER (utilisateur classique), ROLE_MODO (sans droits spécifiques sur TdP) et ROLE_ADMIN.
- Pour supprimer les droits d'un utilisateur, il suffit d'applique la commandate soeur `fos:user:demote`

Plus d'information sur la gestion des commandes en Symfony : 
- Fonctionnement général : https://symfony.com/doc/3.3/console/usage.html
- Sur FOS User: https://symfony.com/doc/current/bundles/FOSUserBundle/command_line_tools.html
