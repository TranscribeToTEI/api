# Gestion de l'API :

Pour exécuter des commandes sur l'API :
- Se connecter en SSH au serveur
- Naviguer (via la commande `cd`) vers `www/web_main/api`
- Exécuter `php bin/console` auquel vous ajoutez votre commande.

## Vider le cache de l'application :
> php bin/console cache:clear --env=prod --no-warmup

## Mettre à jour la base de données en fonction du modèle de données :
> php bin/console doctrine:schema:update --dump-sql --force

## Mettre à jour un utilisateur :
Changer les rôles d'un utilisateur :
> php bin/console fos:user:promote mail@user.fr [role à donner]
Les rôles sont principalement ROLE_USER (utilisateur classique), ROLE_MODO (sans droits spécifiques sur TdP) et ROLE_ADMIN.
Pour supprimer les droits d'un utilisateur, il suffit d'applique la commandate soeur `fos:user:demote`

Plus d'information sur la gestion des commandes en Symfony : 
- Fonctionnement général : https://symfony.com/doc/3.3/console/usage.html
- Sur FOS User: https://symfony.com/doc/current/bundles/FOSUserBundle/command_line_tools.html