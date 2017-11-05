# Liste des balises XML TEI disponibles
## Éléments de structure logique du texte :
- head : titre (zéro ou un, au début du testament)
- dateline : mention de date (0 à n, au début et/ou à la fin du testament)
- p : paragraphe (1 à n)
- list : liste (0 à n)
- signed : signature (1 à n)
- postscript : post-scriptum (0 à n)

## Éléments de structure de 2e niveau :
- item : composant de liste
- seg : segment textuel
- address : adresse (existe dans le schéma mais pas encore redéfini, ainsi que son sous-élément addrLine)
- note : note (il peut y avoir des notes marginales de la main du testateur)

## Éléments marqueurs de la structure physique du testament :
- lb : changement de ligne
- pb : changement de page

## Éléments marqueurs de la mise en page du testament, ou de la mise en valeur de parties du texte
- space : espace laissé blanc
- metamark : métamarque (pour la lecture)
- marqueur de 2e niveau :
- hi (mise en évidence)

## Éléments permettant de consigner des micro-phénomènes de l'écriture :
- abbr : forme abrégée d'un mot (dans choice, accompagné de expan : forme développée du mot)
- sic : faute de français (dans choice, accompagné de corr : forme correcte)
- orig : graphie ancienne (dans choice, accompagné de reg : graphie normalisée)
- surplus : texte superflu (dans sic)
- del : suppression
- add : ajout
- subst : substitution (regroupe, le cas échéant, del et add)

## Éléments permettant de consigner des difficultés de lecture :
- damage : dommage matériel subi par le support (englobe souvent unclear et/ou supplied et/ou gap)
- supplied : texte restitué par l'éditeur
- unclear : transcription incertaine
- gap : partie de texte non transcrite

## Éléments de balisage sémantique :
- persName : nom de personne
- placeName : nom de lieu
- orgName : nom d'organisme
- date : date
- term : terme

## Éléments pour les notes d'apparat critique ou les notes sur le contenu du testament :
- ref : référence (pointeur vers une note sur le contenu si utilisé dans le corps de la transcription, ou vers une ressource externe si utilisé dans les notes éditoriales)
- note : note (cf. aussi plus haut)
- app : entrée d'apparat critique (contient lem et note)
- lem : lemme (au sens de leçon textuelle)
- bibl : référence bibliographique (dans les notes ; existe dans le schéma mais pas encore redéfini, ainsi que son sous-élément title)