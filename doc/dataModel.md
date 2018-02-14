# Comprendre le modèle de données

## Le modèle de données
Si vous souhaitez consulter l'intégralité du modèle de données, vous le retrouverez dans [web/XMLModel/model.xml](../web/XMLModel/model.xml)

## Les tags XML TEI utilisés
[Accéder à la liste des tags utilisés](teiTags.md)

Vous pouvez utiliser l'API pour obtenir des informations structurées en JSON à propos du modèle TEI. Pour cela, pour obtenir les informations sur l'élément `p`, exécutez la requête : 
>GET https://testaments-de-poilus.huma-num.fr/api/web/model?element=p&info=full 

## Export du modèle pour la webapp
Attention, il faut supprimer à la main les balises egXML dans le fichier JSON.