# Liste des formulaires spécifiques d’API

*Obtenir un token de session utilisateur :*

    {
        "grant_type": "password",
        "client_id": "",
        "client_secret": "",
        "username": "",
        "password": ""
    }

*Rafraichir un token de session utilisateur :*

    {
        "grant_type": "refresh_token",
        "client_id": "",
        "client_secret": "",
        "refresh_token": "
    }

*Enregistrer un nouvel utilisateur :*
    
    {"fos_user_registration_form":
        {
            "email": "",
            "plainPassword": {
                "first":"",
                "second": ""
            },
            "name": ""
        }
    }

*Créer un nouveau fil de commentaires :*
    
    {"fos_comment_thread":
        {
            "id": "test",
            "permalink": "http://test.com/test"
        }
    }

*Poster un nouveau commentaire :*

    {"fos_comment_comment":
        {
            "body": "premier commentaire"
        }
    }

*Réinitialiser un mot de passe :*
    
    {"fos_user_resetting_form":
        {
            "plainPassword": {
                "first": "",
                "second": ""
            }
        }
    }

*Soumettre un changement de mot de passe :*
    
    {"fos_user_change_password_form":
        {
            "current_password": "ancien",
            "plainPassword": {
                "first": "nouveau"
                "second": "nouveau"
            }
        }
    }