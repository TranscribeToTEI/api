# Mettre à jour le modèle TEI

L'interface de transcription utilise une version simplifiée du modèle TEI pour générer un certain nombre d'éléments : documentation, hiérarchie des boutons, etc.

Pour mettre à jour ce modèle, il faut :
- Modifier le fichier XML correspondant, dans le repository https://github.com/TranscribeToTEI/teiModel
- Modifier le fichier **web/XMLModel/model.xml** et y placer le contenu du modèle
- Mettre à jour le serveur d'API
- Accéder à `{URL de l'API}/model?elements=true&info=full` (il se peut que l'accès soit difficile et long, si ça plante, relancer le chargement)
- Copier le contenu obtenu dans `webapp\web\teiInfo.json`
- Mettre à jour **teiInfo.json** sur le serveur
