# Group Mailer
[![Version](https://img.shields.io/badge/version-2.18.0-blue.svg)](https://github.com/verturin/groupemailer)
[![phpBB](https://img.shields.io/badge/phpBB-3.3.14+-orange.svg)](https://www.phpbb.com/)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)](license.txt)

Extension phpBB 3.3 — envoi de campagnes email à un ou plusieurs groupes de membres, avec file d'attente pour respecter les limites d'envoi de votre hébergeur.

## Fonctionnalités

- **Campagnes** : titre interne, objet, contenu avec `{USERNAME}` dynamique
- **Ciblage par groupe(s)** : un ou plusieurs groupes au choix, y compris tous les membres via "Utilisateurs enregistrés"
- **Cadence configurable** par campagne (mails par lot / intervalle en minutes)
- **Filtres de destinataires** : comptes désactivés par un administrateur, comptes dormants depuis X jours, refus des emails de masse
- **File d'attente** avec envoi progressif via le cron natif phpBB, reprise automatique après interruption
- **Pause / reprise / arrêt définitif** d'une campagne en cours d'envoi
- **Trois modes d'envoi** : notification seule avec suivi (message lu en ligne, suivi fiable), message complet avec suivi, ou message complet sans suivi
- **Suivi de lecture** : lien personnel affichant le message en ligne, la consultation valant confirmation (sans connexion au forum)
- **Mention de désabonnement** ajoutée automatiquement, avec lien vers les préférences du compte
- **Relance des non-lecteurs** : nouvelle campagne ciblant automatiquement ceux qui n'ont pas confirmé
- **Page de suivi par campagne** : qui a reçu, qui a confirmé, avec renvoi à tous / aux non-confirmés / à un seul membre
- **Envoi immédiat** d'un lot depuis l'ACP, sans attendre le cron
- **Envoi de test** à sa propre adresse avant de démarrer une campagne
- **Taux de lecture** par campagne
- **Relance en un clic** des envois en erreur
- **Historique détaillé** : destinataire, campagne, objet, groupe(s), nombre de destinataires, date, statut
- **Expéditeur, en-tête et pied de page** personnalisables depuis l'ACP
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

> **Important — adresse d'expéditeur :** elle doit être sur le même domaine que votre forum (ex. `contact@votreforum.com`). Une adresse `@gmail.com`, `@outlook.com` ou équivalente sera affichée « envoyé via … » et classée comme indésirable : les serveurs de ces fournisseurs n'autorisent pas votre hébergeur à écrire en leur nom (protection SPF/DKIM). Aucun réglage de l'extension ne peut contourner cela.

> **Important — déclenchement de l'envoi :** l'extension utilise le cron natif de phpBB. L'envoi progresse à chaque visite d'une page du **forum public** (l'ACP ne le déclenche pas). Sur un forum peu fréquenté la nuit, l'envoi peut marquer une pause et reprendre au matin.

## Désinstallation

1. Désactiver l'extension dans l'ACP
2. Cliquer sur **Supprimer les données** (rollback complet, aucun résidu en base)
3. Supprimer le dossier `phpBB/ext/verturin/groupemailer/`
4. Purger le cache phpBB

## Licence

GPL-2.0-only — voir [license.txt](LICENSE)

---
**Made with ❤️ for the phpBB community** · [verturin](https://github.com/verturin)
