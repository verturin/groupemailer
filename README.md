# Group Mailer
[![Version](https://img.shields.io/badge/version-2.29.1-blue.svg)](https://github.com/verturin/groupemailer)
[![phpBB](https://img.shields.io/badge/phpBB-3.3.14+-orange.svg)](https://www.phpbb.com/)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)](license.txt)

Extension phpBB 3.3 — envoi de campagnes email à un ou plusieurs groupes de membres, avec file d'attente pour respecter les limites d'envoi de votre hébergeur.

## Fonctionnalités

- **Campagnes** : titre interne, objet, contenu avec `{USERNAME}` dynamique
- **Ciblage par groupe(s)** : un ou plusieurs groupes au choix, y compris tous les membres via "Utilisateurs enregistrés"
- **Cadence configurable** par campagne (mails par lot / intervalle en minutes)
- **Filtres de destinataires** : comptes désactivés par un administrateur, comptes dormants depuis X jours, refus des emails de masse, adresses en échec répété
- **File d'attente** avec envoi progressif via le cron natif phpBB, reprise automatique après interruption
- **Pause / reprise / arrêt définitif** d'une campagne en cours d'envoi
- **Trois modes d'envoi** : notification seule avec suivi (message lu en ligne, suivi fiable), message complet avec suivi, ou message complet sans suivi
- **Suivi de lecture** : lien personnel affichant le message en ligne, la consultation valant confirmation (sans connexion au forum)
- **Liens cliquables** dans le message affiché en ligne
- **Désabonnement en un clic** depuis l'email ou la page de lecture, sans connexion, avec réabonnement possible et suivi des désabonnements
- **Relance des non-lecteurs** : nouvelle campagne ciblant automatiquement ceux qui n'ont pas confirmé
- **Page de suivi par campagne** : qui a reçu, qui a confirmé, avec renvoi à tous / aux non-confirmés / à un seul membre
- **Envoi immédiat** d'un lot depuis l'ACP, sans attendre le cron
- **Envoi de test** à sa propre adresse avant de démarrer une campagne
- **Duplication** d'une campagne et **démarrage programmé** à une date choisie
- **Tableau de bord** : vue d'ensemble et comparatif des taux de lecture
- **Taux de lecture** par campagne
- **Relance en un clic** des envois en erreur
- **Historique détaillé** : destinataire, campagne, objet, groupe(s), nombre de destinataires, date, statut
- **Tableaux triables et paginés**, pseudonymes affichés avec la couleur de leur groupe
- **Expéditeur, en-tête et pied de page** personnalisables depuis l'ACP
- **Sauvegarde et restauration** des campagnes dans un fichier JSON
- **Page de diagnostic** : état réel du schéma, de la tâche cron et de la configuration email, pour identifier tout blocage
- Interface entièrement en français et en anglais, 100% ACP (aucune interface UCP nécessaire)

## Compatibilité

| Logiciel | Version minimale |
|----------|-----------------|
| phpBB | 3.3.14 |
| PHP | 7.2.0 |

## Installation

1. Télécharger et décompresser l'archive
2. Copier le dossier dans `phpBB/ext/verturin/groupemailer/`
3. Purger le cache : ACP → Système → Purger le cache
4. Activer : ACP → Personnaliser → Gérer les extensions
5. Configurer : ACP → Group Mailer → Réglages

Le module apparaît dans l'ACP sous la catégorie **Group Mailer** : Campagnes, Historique, Réglages, Diagnostic.

> **Arborescence requise :** `phpBB/ext/verturin/groupemailer/composer.json`

## Mise à jour

1. Sauvegarder vos campagnes depuis **ACP → Group Mailer → Sauvegarde / Restauration**
2. Remplacer l'intégralité du dossier `ext/verturin/groupemailer/`
3. Purger le cache : ACP → Système → Purger le cache
4. Désactiver puis réactiver l'extension, afin que les migrations s'exécutent

> **Ne pas cliquer sur « Supprimer les données »** lors d'une mise à jour : cette action efface les campagnes, la file d'attente et tout l'historique.

## Configuration ACP

| Paramètre | Description |
|-----------|-------------|
| Nombre de mails par défaut | Cadence proposée à la création d'une nouvelle campagne (défaut : 10) |
| Intervalle par défaut | Minutes entre chaque lot d'envoi (défaut : 10) |
| Nom de l'expéditeur | Vide = utilise le nom du site configuré dans le forum |
| Adresse email de l'expéditeur | Vide = utilise l'adresse de contact configurée dans le forum |
| En-tête | Texte optionnel ajouté avant chaque message envoyé |
| Pied de page | Texte optionnel ajouté après chaque message envoyé |
| Mention de désabonnement | Ajoute automatiquement en fin d'email la marche à suivre pour ne plus recevoir ces messages (activé par défaut) |
| Seuil d'inactivité par défaut | Nombre de jours proposé pour l'exclusion des comptes inactifs (défaut : 180) |

### Options par campagne

| Option | Description |
|--------|-------------|
| Contenu de l'email et suivi | Un choix unique à trois options : notification seule avec suivi, message complet avec suivi, message complet sans suivi |
| Comptes désactivés | Écarte les comptes marqués « Inactif » dans phpBB (activé par défaut) |
| Comptes inactifs | Écarte les membres sans connexion depuis plus de X jours |
| Emails de masse | Respecte le refus exprimé par le membre dans son profil (activé par défaut) |
| Cadence | Propre à la campagne, remplace la valeur par défaut |

> **Important — adresse d'expéditeur :** elle doit être une boîte **réellement créée sur le domaine du forum** (ex. `contact@votreforum.com`), et renseignée dans les réglages de l'extension. Une adresse d'un autre domaine (`@gmail.com`, `@outlook.com`…) ou l'adresse par défaut du serveur mutualisé sera acceptée par votre hébergeur puis classée comme indésirable, voire écartée en silence par les grandes messageries — le journal de remise de l'hébergeur affichant pourtant « accepté ». Aucun réglage de l'extension ne peut contourner cette protection, qui s'applique côté destinataire.

> **Important — déclenchement de l'envoi :** l'extension utilise le cron natif de phpBB. L'envoi progresse à chaque visite d'une page du **forum public** (l'ACP ne le déclenche pas). Sur un forum peu fréquenté la nuit, l'envoi peut marquer une pause et reprendre au matin.

## Désinstallation

1. Désactiver l'extension dans l'ACP
2. Cliquer sur **Supprimer les données** (rollback complet, aucun résidu en base)
3. Supprimer le dossier `phpBB/ext/verturin/groupemailer/`
4. Purger le cache phpBB

## Tests

L'extension est livrée avec des tests unitaires exécutables sans installation de phpBB :

```
vendor/bin/phpunit -c tests/phpunit.xml
```

Voir [tests/README.md](tests/README.md).

## Licence

GPL-2.0-only — voir [license.txt](LICENSE)

---
**Made with ❤️ for the phpBB community** · [verturin](https://github.com/verturin)
