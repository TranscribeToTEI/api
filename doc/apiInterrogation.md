# Interroger l'API

## Accéder à l'API
L'API de Testaments de Poilus est accessible à l'URL : https://testaments-de-poilus.huma-num.fr/api/web

Par exemple, en exécutant la requête :
> GET https://testaments-de-poilus.huma-num.fr/api/web/wills

Vous obtiendrez une réponse du type :

    [
        {
            "id": 1,
            "entity": {
                "id": 1,
                "willNumber": 1,
                "will": null,
                "resources": [],
                "isShown": true,
                "createUser": null,
                "createDate": "2017-10-10T16:04:06+02:00",
                "updateUser": null,
                "updateDate": "2017-11-17T18:30:45+01:00",
            },
            "callNumber": "MC/ET/XLVIII/1820",
            "minuteLink": null,
            "title": "Testament MC/ET/XLVIII/1820",
            "minuteDate": "20 janvier 1921",
            "minuteYear": "1921",
            "willWritingDate": "7 août 1914",
            "willWritingYear": "1914",
            "willWritingPlace": {
                "id": 2,
                "names": [
                    {
                        "id": 2,
                        "placeName": null,
                        "placeDepartement": null,
                        "placeRegion": null,
                        "placeCity": null,
                        "placeCountry": null,
                        "name": "Paris",
                        "date": null,
                        "year": null,
                        "placeType": null,
                        "createUser": null,
                        "createDate": "2017-10-10T15:56:40+02:00",
                        "updateUser": null,
                        "updateDate": "2017-10-13T10:21:31+02:00",
                        "updateComment": "entity creation",
                    }
                ],
                "frenchDepartements": [],
                "frenchRegions": [],
                "cities": [],
                "countries": [],
                "description": null,
                "geonamesId": "2988507",
                "geographicalCoordinates": "48.85341+2.3488",
                "isOfficialVersion": false,
            },
            "testator": {
                "id": 1,
                "name": "Joseph Daviet",
                "surname": "Daviet",
                "firstnames": "Joseph",
                "profession": "militaire ?",
                "addressNumber": null,
                "addressStreet": "Paris (1er), 2 quai de la Mégisserie",
                "addressDistrict": null,
                "addressCity": null,
                "dateOfBirth": "21 juillet 1878",
                "yearOfBirth": "1878",
                "placeOfBirth": {...},
                "dateOfDeath": "24 ou 25 février 1915",
                "yearOfDeath": "1915",
                "placeOfDeath": {...},
                "deathMention": "mort pour la France",
                "memoireDesHommes": [
                    "http://www.memoiredeshommes.sga.defense.gouv.fr/fr/ark:/40699/m005239e2584299e"
                ],
                "militaryUnit": {
                    "id": 1,
                    "name": "103e régiment d’infanterie",
                    "country": null,
                    "armyCorps": null,
                    "regimentNumber": null,
                    "description": null,
                    "isOfficialVersion": false,
                },
                "rank": "capitaine",
                "description": "\"* croix de guerre avec étoile de bronze* jugement déclaratif de décès du tribunal civil de la Seine du 9 juillet 1920, transcrit à la mairie du 1er arrondissement le 1er septembre 1920* testament adressé par lettre recommandée à Me Dufour le 23 janvier 1920\"",
                "picture": null,
                "isOfficialVersion": false,
            },
            "pagePhysDescSupport": null,
            "pagePhysDescHeight": null,
            "pagePhysDescWidth": null,
            "pagePhysDescHand": null,
            "envelopePhysDescSupport": null,
            "envelopePhysDescHeight": null,
            "envelopePhysDescWidth": null,
            "envelopePhysDescHand": null,
            "hostingOrganization": {
                "id": 1,
                "name": "Archives nationales",
                "code": "AN",
            },
            "identificationUser": "ML",
            "description": null,
            "isOfficialVersion": false,
        },
        {...}
    ]
    
## Obtenir la liste des requêtes possibles :
En vous rendant à https://testaments-de-poilus.huma-num.fr/api/web/doc, vous obtiendrez la liste complète et à jour des requêtes possibles auprès de l'API.

Certaines requêtes spécifiques (concernant les utilisateurs notamment), la [liste des requêtes spécifiques est disponible ici](queries.md)