# Group Mailer
[![Version](https://img.shields.io/badge/version-2.7.1-blue.svg)](https://github.com/verturin/groupemailer)
[![phpBB](https://img.shields.io/badge/phpBB-3.3.14+-orange.svg)](https://www.phpbb.com/)
[![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)](license.txt)

Extension phpBB 3.3 — envoi de campagnes email à un ou plusieurs groupes de membres, avec file d'attente pour respecter les limites d'envoi de votre hébergeur.

## Fonctionnalités

- **Campagnes** : titre interne, objet, contenu avec `{USERNAME}` dynamique
- **Ciblage par groupe(s)** : un ou plusieurs groupes au choix, y compris tous les membres via "Utilisateurs enregistrés"
- **Cadence configurable** par campagne (mails par lot / intervalle en minutes)
- **File d'attente** avec envoi progressif via le cron natif phpBB, reprise automatique après interruption
- **Pause / reprise** d'une campagne en cours d'envoi
- **Relance en un clic** des envois en erreur
- **Historique détaillé** : destinataire, campagne, objet, groupe(s), nombre de destinataires, date, statut
- **Expéditeur, en-tête et pied de page** personnalisables depuis l'ACP
- Entièrement 100% ACP (aucune interface UCP nécessaire)

## Compatibilité

| Logiciel | Version minimale |
|----------|-----------------|
| phpBB | 3.3.14 |
| PHP | 7.1.0 |

## Installation

1. Télécharger et décompresser l'archive
2. Copier le dossier dans `phpBB/ext/verturin/groupemailer/`
3. Purger le cache : ACP → Système → Purger le cache
4. Activer : ACP → Personnaliser → Gérer les extensions
5. Configurer : ACP → Group Mailer → Réglages

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

> **Important :** l'adresse d'expéditeur doit être sur le même domaine que votre forum (ex. `contact@votreforum.com`). Une adresse `@gmail.com`, `@live.fr` ou équivalente sera très probablement classée comme indésirable par les clients mail (protection anti-usurpation SPF/DKIM).

## Désinstallation

1. Désactiver l'extension dans l'ACP
2. Cliquer sur **Supprimer les données** (rollback complet, aucun résidu en base)
3. Supprimer le dossier `phpBB/ext/verturin/groupemailer/`
4. Purger le cache phpBB

## Licence

GPL-2.0-only — voir [license.txt](LICENSE)

---
**Made with ❤️ for the phpBB community** · [verturin](https://github.com/verturin)
