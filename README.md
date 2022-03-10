# sgdf_materiel
Scout et Guide de France

Vous trouverez ci joint un site en PHP/SQLi permettant de gérer le matériel d'un groupe scout.
 - On peut créer tout type de matériel (tente, marabout, latrine, trépied...)
 - On peut faire des réservations
 - Gerer l'état du matériel, le rendre indisponible temporairement, ajouter des commenteraires...
 - Génération de listes permettant de faire un état rapide du matériel
 - Génération d'un QRCode renvoyant sur un formulaire afin que sur les camps les chef puisse remonter les problèmes sur le matériel
 - Installation automatique de la base de donnée
 - Création de plusieurs comptes utilisateur
 
 Il faut juste mettre le code présent dans le repertoire src à la racine du site (ou dans un autre répertoire) un interface d'installation s'affichera et il ne vous restera plus qu'à renseigner le nom de la base de donnée, mot de passe.
 
 Ce site fonctionne sur PHP 5.x à 8.x : donc tout serveur web avec PHP et SQLi ira très bien.
