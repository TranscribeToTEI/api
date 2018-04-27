# Structuration de l'API :

Est détaillé ici la structure de l'API, et les fichiers importants qui y sont présents

- *api*
    - *app* : configuration de l'application
        - *config*
            - **parameters.yml** : fichier de paramètres de l'API
    - *bin* : gère notamment la console de commande, ne pas y toucher
    - *doc* : documentation de l'API (le présent fichier s'y trouve)
    - *src* : contenu de l'API
        - *AppBundle* : principal bundle de l'application
        - *CommentBundle* : bundle dédié à la gestion spécifique des commentaires liés à FOSCommentBundle
        - *DownloadBundle* : bundle dédié au téléchargement de fichiers depuis l'API
        - *UserBundle* : bundle lié à FOSUserBundle
    - *var* : Contient les caches et les logs
        - *cache* : Dossier qui peut être supprimé si `php bin/console cache:clear` ne fonctionne pas
        - *logs* : Dossier qui contient les logs de l'API, à explorer via la commande shell `tail -n 100 var/logs/prod.log`
    - *vendor* : Contient les bundle (librairies) externes utilisées par l'API
    - *web* : Contient les fichiers accessibles aux utilisateurs
        - *XMLModel* : Contient le modèle TEI
            - **model.xml** : Fichier ODD du modèle TEI
        - *download* : Contient les fichiers XML générés par l'API à l'exportation
        - *uploads* : Contient les images utilisées par l'API. C'est ici que doivent être déposées les images utilisées par l'API ou le modèle de données.