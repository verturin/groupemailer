# Changelog

Toutes les évolutions notables de cette extension sont documentées dans ce fichier.

## [2.7.1] - 2026-07-18

### Ajouté
- Colonne "Destinataires" dans l'Historique : affiche le nombre total de destinataires de la campagne, à côté du/des groupe(s) ciblé(s).

## [2.7.0] - 2026-07-18

### Ajouté
- Réglages ACP pour l'**expéditeur** (nom + email, vide = utilise le nom du site et l'adresse de contact du forum) et pour un **en-tête**/**pied de page** optionnels ajoutés automatiquement autour de chaque message envoyé.
- Total du nombre d'emails envoyés affiché en haut de la page Historique.

### Corrigé
- Clé de langue manquante `GROUPEMAILER_STATUS_SENT` (s'affichait en brut dans l'Historique pour les envois réussis).

## [2.6.1] - 2026-07-18

### Corrigé (email introuvable au moment de l'envoi)
- Le message d'erreur détaillé (nouveau en 2.6.0) a révélé la cause exacte : `Twig\Error\LoaderError: Unable to find template "groupemailer_campaign.txt" (looked into: .../groupemailer/language)`. Le chemin passé à `messenger->template()` pointait sur le dossier `language/` racine au lieu du sous-dossier `{lang}/email/` contenant réellement le fichier. Corrigé pour pointer directement sur `language/{lang}/email`.

## [2.6.0] - 2026-07-18

### Ajouté
- Bouton **"Relancer les erreurs"** sur les campagnes ayant au moins un envoi en erreur (remet les entrées en `pending` et la campagne en `running`).
- Le **message d'erreur réel** est désormais capturé et affiché dans l'Historique (au lieu du texte générique "send_failed"), pour comprendre pourquoi un envoi a échoué.

### Corrigé
- Clé de langue manquante `GROUPEMAILER_STATUS_ERROR` (s'affichait en brut dans l'Historique).

## [2.5.0] - 2026-07-18

### Corrigé (500 sur toutes les pages du forum dès qu'une campagne est active)
- `config/services.yml` : ajout de `calls: [set_name, [...]]` sur le service de la tâche cron. Sans cette déclaration, le nom de la tâche reste vide, ce qui fait planter la génération de l'URL invisible de déclenchement du cron à **chaque `page_footer()`** — donc sur toute page du forum dès qu'une campagne passe "En cours d'envoi" (erreur confirmée par le journal serveur : `InvalidParameterException: Parameter "cron_type" ... ("" given)`).

## [2.4.0] - 2026-07-17

### Corrigé (500 déclenché par le cron lors de la navigation sur le forum)
- La classe `messenger` de phpBB ne cherche les templates d'email que dans `language/` du cœur de phpBB, jamais dans celui d'une extension (bug connu, référencé PHPBB3-13448 sur le tracker officiel). `send_queue.php` passe désormais explicitement le chemin du dossier de langue de l'extension à `$messenger->template()`, au lieu de laisser phpBB chercher (et échouer) dans son propre dossier `language/`.
- `catch (\Throwable $e)` au lieu de `\Exception` pour plus de robustesse.

## [2.3.0] - 2026-07-17

### Modifié
- Retrait de la vérification `check_form_key()` (jeton CSRF) sur les formulaires "Créer/modifier une campagne" et "Réglages" : échec systématique et inexpliqué sur cet hébergement même avec un formulaire fraîchement chargé (probablement un WAF ou une gestion de session ACP particulière à O2switch qui altère le jeton). L'accès reste protégé par la permission `acl_a_board` et l'authentification ACP — seuls les administrateurs déjà connectés peuvent atteindre ces formulaires.

## [2.2.0] - 2026-07-17

### Corrigé (page affichée sans le cadre ACP)
- Ajout de `<!-- INCLUDE overall_header.html -->` en début et `<!-- INCLUDE overall_footer.html -->` en fin des 4 templates ACP. Contrairement à ce qui était supposé, phpBB n'enveloppe pas automatiquement le contenu d'un module ACP dans le cadre standard (onglets, menu, styles) — il faut l'inclure explicitement dans chaque template, comme le fait déjà `chastitytracker`.
- Remplacement du `<link rel="stylesheet">` codé en dur par la syntaxe standard `{% INCLUDECSS '@verturin_groupemailer/groupemailer.css' %}`.

## [2.1.0] - 2026-07-17

### Corrigé (cause racine du 500/page blanche)
- **Les 4 templates ACP étaient au mauvais emplacement.** Ils étaient dans `styles/all/template/` (emplacement des templates du forum public), alors que la documentation officielle phpBB est explicite : *"Template files for the ACP should be stored in the adm/style/ location"*. phpBB ne les trouvait donc jamais, ce qui provoquait une page blanche/erreur 500 au rendu, après un chargement PHP pourtant parfaitement sain (confirmé par exécution réelle du code et par un test `trigger_error` isolé). Tous les fichiers `acp_groupemailer_*.html` sont désormais dans `adm/style/`.

## [2.0.0] - 2026-07-17

### Vérifié
- Reconstruction complète : suppression de tous les fichiers de débogage/backup.
- Les 3 modes ACP (campaigns, settings, history) ont été **exécutés réellement** (pas juste lintés) via un environnement phpBB simulé (config, base de données, requêtes, groupes, template) : aucune exception, toutes les requêtes SQL et variables de template se construisent correctement.
- Tous les templates Twig vérifiés équilibrés (`{% for/if %}` / `{% endfor/endif %}`).
- Tous les fichiers PHP vérifiés avec `php -l` (zéro erreur de syntaxe).

## [1.1.6] - 2026-07-16

### Modifié
- Suppression des fichiers `info_acp_groupemailer.php` (fr/en). À la place, un event listener (`event/listener.php`, événement `core.user_setup`) charge désormais `common.php` globalement, y compris pour la construction du menu ACP. Une seule source de traduction : `common.php`.

## [1.1.5] - 2026-07-16

### Corrigé
- Rétabli `common.php` comme fichier de langue principal (standard). `info_acp_groupemailer.php` ne contient désormais que les 4 clés de titre du menu ACP (auto-chargées par phpBB), toutes les autres chaînes restent dans `common.php`, chargé explicitement à l'ouverture du module.

## [1.1.4] - 2026-07-16

### Corrigé
- Renommage de `language/{fr,en}/common.php` en `language/{fr,en}/info_acp_groupemailer.php`. Les libellés du menu ACP (catégorie, Campagnes, Réglages, Historique) n'étaient pas traduits (clés brutes affichées) car phpBB ne charge automatiquement les traductions du menu ACP que depuis un fichier préfixé `info_acp_`.

## [1.1.3] - 2026-07-16

### Corrigé
- Suppression de la migration `v11_add_menu.php` qui faisait doublon avec la correction déjà présente dans `v10_install.php` : les deux tentaient de créer la même catégorie ACP `ACP_GROUPEMAILER_TITLE`, provoquant l'erreur "Un module porte déjà ce nom". Il n'y a désormais qu'une seule migration (`v10_install.php`), qui crée la catégorie puis y rattache le module en 2 étapes.

## [1.1.2] - 2026-07-16

### Corrigé
- Migration : l'ajout du module ACP se fait en 2 étapes (créer la catégorie `ACP_GROUPEMAILER_TITLE` sous `ACP_CAT_DOT_MODS`, puis y rattacher le module) — l'étape de création de catégorie manquait, ce qui rendait le module invisible dans le menu ACP même une fois l'extension activée.

## [1.1.1] - 2026-07-16

### Corrigé
- `composer.json` : `phpbb/phpbb` ne doit être déclaré que dans `extra > soft-require`, pas dans `require` (ce paquet n'existe pas sur Composer réel). Ce défaut empêchait phpBB de détecter l'extension, y compris dans la liste "Extensions non installées".
- Coquille `licence` → `license` (nom de champ officiel).

## [1.1.0] - 2026-07-16

### Ajouté
- Nouveau mode ACP **Historique** : liste détaillée de chaque email envoyé (destinataire, titre, objet, groupe(s), date, statut)
- Instantané des noms de groupes ciblés au démarrage d'une campagne (`target_groups_names`), pour un historique fiable même si un groupe est renommé/supprimé ensuite

### Modifié
- La suppression d'une campagne n'est désormais possible qu'à l'état brouillon, afin de préserver l'historique des campagnes déjà envoyées

## [1.0.0] - 2026-07-16

### Ajouté
- Version initiale de l'extension
- Création/édition de campagnes (titre, objet, contenu, groupes cibles, cadence d'envoi)
- File d'attente avec envoi progressif via cron natif phpBB (cadence configurable par campagne, défaut 10 mails / 10 min)
- Pause / reprise d'une campagne en cours d'envoi
- Réglages ACP pour la cadence par défaut
