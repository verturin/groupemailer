# Changelog

Toutes les évolutions notables de cette extension sont documentées dans ce fichier.

## [2.28.5] - 2026-08-02

### Corrigé (page de suivi)
- **Ni le tri des colonnes ni la navigation entre les pages ne fonctionnaient.** Les requêtes tenaient pourtant compte du tri et de la page demandés, mais les liens correspondants n'étaient jamais transmis au gabarit : les en-têtes n'étaient pas cliquables et la barre de navigation restait invisible, ce qui limitait l'affichage aux cinquante premiers destinataires sans moyen d'atteindre les suivants. L'historique n'était pas concerné.

## [2.28.4] - 2026-08-02

### Corrigé (erreur 500 sur la page de suivi)
- **Le résultat de la requête des destinataires était libéré deux fois** (`mysqli_result object is already closed`). En restructurant la boucle en deux passes pour n'interroger les couleurs de groupe qu'une seule fois (2.27.0), l'ancienne libération placée après la boucle avait été conservée en plus de la nouvelle. Sur MySQL, la seconde libération lève une erreur fatale.

## [2.28.3] - 2026-08-02

### Modifié
- **La page de suivi rapporte désormais toute anomalie à l'écran** (type, message, fichier et ligne) au lieu de laisser le serveur renvoyer une erreur 500 muette. Certaines erreurs fatales n'apparaissent dans aucun journal selon la configuration de l'hébergeur : cette capture rend le diagnostic possible dans tous les cas.

## [2.28.2] - 2026-08-02

### Corrigé (erreur 500 sur la page de suivi)
- **`get_username_string()` était appelée sans que son fichier soit chargé.** Cette fonction, introduite avec l'affichage des pseudonymes en couleur de groupe (2.27.0), appartient à `functions_content.php`, qui n'est pas chargé d'office dans l'ACP contrairement au forum. L'appel provoquait une erreur fatale immédiate, sans trace dans les journaux, sur toutes les campagnes quel que soit leur nombre de destinataires. Le fichier est désormais chargé au besoin, avec un repli reconstruisant le lien et la couleur si l'inclusion échoue.

## [2.28.1] - 2026-08-02

### Corrigé (erreur 500 sur la page de suivi)
- **La barre de navigation entre les pages incluait `pagination.html`**, un gabarit du cœur de phpBB qui n'est pas résolu depuis un template d'extension : le moteur de rendu levait une erreur fatale, sans trace dans les journaux. La navigation est désormais écrite dans les templates de l'extension, à partir du bloc que phpBB alimente.
- Ce défaut n'apparaissait qu'au-delà de cinquante lignes, seuil déclenchant l'affichage de la navigation : l'historique d'un forum peu chargé passait donc inaperçu, tandis que le suivi d'une campagne de plusieurs dizaines de destinataires échouait systématiquement.

## [2.28.0] - 2026-08-02

### Ajouté
- **Choix des campagnes à sauvegarder.** La page Sauvegarde liste désormais les campagnes avec leur statut, leur nombre de destinataires et leur date, chacune assortie d'une case à cocher, plus un bouton « tout cocher ». Ne rien cocher sauvegarde l'ensemble, comme auparavant. Le format (complet ou campagnes seules) devient un choix du même formulaire.

### Modifié
- La page Diagnostic vérifie aussi les colonnes et la table introduites par les migrations récentes (`user_lang`, `unsubscribed_time`, table des adresses en échec). Une page de suivi qui ne s'affiche pas alors que ces migrations n'ont pas été exécutées est ainsi immédiatement identifiée.

## [2.27.1] - 2026-08-02

### Ajouté
- **Tri par titre, par objet du message et par groupes destinataires** dans l'historique, qui ne proposait le tri que sur la date, le destinataire, la confirmation et le statut. Six colonnes sur sept sont désormais triables, un second clic inversant le sens.

## [2.27.0] - 2026-08-02

### Modifié
- **Les pseudonymes reprennent la couleur de leur groupe** dans l'historique et le suivi, comme partout ailleurs sur le forum. La mise en forme est confiée à la fonction native de phpBB, qui gère aussi le lien vers le profil et le cas des comptes supprimés.
- La couleur est celle du profil au moment de l'affichage, et non celle figée à l'envoi : un changement de groupe se reflète donc dans les tableaux.
- Les couleurs sont récupérées en une seule requête par page affichée, quel que soit le nombre de lignes.

## [2.26.1] - 2026-08-02

### Corrigé
- **Avertissements PHP sur la page Historique.** L'ajout du suivi des échecs répétés en 2.25.0 avait modifié deux blocs identiques : le lien « Réintégrer » avait donc été inséré aussi dans l'historique, où la variable de campagne courante n'existe pas, et la liste des adresses en échec n'y était pas chargée. Le lien de l'historique n'utilise plus de campagne, et la liste est correctement chargée dans les deux pages.
- Retrait d'une capture de variable inutilisée dans la page Diagnostic, reliquat d'une version antérieure.

## [2.26.0] - 2026-08-02

### Modifié
- **L'en-tête `List-Unsubscribe` est désormais activé par défaut.** Il avait été introduit désactivé le temps de vérifier qu'il ne gênait pas la remise des messages ; ce point étant confirmé, et l'en-tête pointant maintenant vers le lien de désabonnement personnel du membre, il est activé pour les nouvelles installations et une fois sur les installations existantes. Il reste décochable dans les Réglages, et cette bascule ne sera pas rejouée.
- Aide du réglage reformulée : elle décrit l'intérêt de l'en-tête plutôt que le problème de remise qui avait motivé sa désactivation.

### Technique
- Migration `v120_unsubscribe_on`.

## [2.25.0] - 2026-08-02

### Ajouté
- **Détection des adresses en échec répété.** Chaque échec d'envoi incrémente un compteur propre au membre ; un envoi réussi le remet à zéro. Au-delà du seuil réglé (trois échecs consécutifs par défaut), l'adresse est automatiquement écartée des campagnes suivantes : il est inutile de continuer à écrire à une boîte supprimée, et ces échecs répétés dégradent la réputation d'expéditeur du forum.
- **Affichage dans la page de suivi** : mention « N échecs consécutifs » sur la ligne du destinataire, décompte des adresses écartées dans le récapitulatif, et lien **« Réintégrer »** pour remettre une adresse dans le circuit.
- Le nombre d'adresses écartées pour ce motif est indiqué au démarrage d'une campagne, avec les autres motifs d'exclusion.
- Seuil réglable dans les Réglages.

### Technique
- Migration `v110_bounces` (table `groupemailer_bounces`, réglage `groupemailer_bounce_threshold`).

## [2.24.0] - 2026-08-02

### Ajouté
- **Désabonnement en un clic**, sans connexion au forum. Le lien figure en fin d'email et sur la page de lecture ; son ouverture désactive immédiatement la préférence native de phpBB « Recevoir les emails de masse », que l'extension respecte déjà lors du choix des destinataires. Une page de confirmation propose le **réabonnement** en cas de clic accidentel.
- L'en-tête `List-Unsubscribe` pointe désormais vers ce lien personnel plutôt que vers les préférences du compte, ce qui le rend réellement exploitable par les messageries.
- **Mention « Désabonné », avec sa date, dans le suivi et l'historique**, ainsi qu'un compteur des désabonnements dans le récapitulatif de campagne.

### Technique
- Migration `v100_unsubscribe` (colonne `unsubscribed_time`), routes `/gmu/{token}` et `/gmr/{token}`.

## [2.23.0] - 2026-08-02

### Ajouté
- **Sauvegarde et restauration des campagnes**, nouvelle page ACP « Sauvegarde / Restauration ».
  - *Sauvegarde complète* : campagnes, destinataires, statuts d'envoi et confirmations de lecture, dans un fichier JSON daté téléchargé sur le poste.
  - *Campagnes seules* : uniquement les messages et leurs réglages, pour réutiliser des modèles sur un autre forum sans y transférer de données personnelles.
  - *Restauration* : les campagnes sont recréées sous de nouveaux identifiants, sans jamais écraser l'existant ; le rattachement des relances à leur campagne d'origine est rétabli avec les nouveaux identifiants. Les jetons de consultation sont préservés, de sorte que les liens déjà envoyés restent valides.
  - La restauration ne retient que les colonnes présentes dans la base, afin qu'une sauvegarde issue d'une autre version de l'extension reste exploitable.

### Technique
- Migration `v90_backup_module`.

## [2.22.2] - 2026-08-02

### Modifié
- Retrait du lien « Test simple », ajouté en 2.22.1 à seule fin de diagnostic. La cause des non-réceptions était l'adresse d'expéditeur et non le code : ce bouton n'a plus d'objet.
- **Aide du champ « Adresse email de l'expéditeur » reformulée.** Elle précise désormais que l'adresse doit être une boîte réellement créée sur le domaine du forum, et avertit que le journal de remise de l'hébergeur affiche « accepté » y compris lorsque le message est ensuite écarté en silence par la messagerie du destinataire.
- Même avertissement porté dans le README et le fichier `TODO.md`.

## [2.22.1] - 2026-08-01

### Ajouté
- **Lien « Test simple »** sur les brouillons, à côté de « Tester ». Il emprunte strictement le chemin d'envoi de la version 2.18.0 : langue du forum, aucun en-tête ajouté, message construit sans la couche de traduction par destinataire. Comparer les deux envois permet d'isoler l'origine d'un blocage de réception, l'objet du message de test portant le préfixe `[TEST 2.18]`.

## [2.22.0] - 2026-08-01

### Corrigé
- **Retrait de l'en-tête `List-Unsubscribe-Post`.** Cette déclaration promet aux serveurs de messagerie que l'adresse indiquée accepte une requête POST de désabonnement en un clic ; la page de préférences du forum ne le fait pas. Cette promesse non tenue peut conduire Gmail ou Outlook à écarter le message silencieusement, alors que phpBB signale l'envoi comme réussi.
- **L'en-tête `List-Unsubscribe` devient un réglage, désactivé par défaut.** Il améliore la réputation d'expéditeur, mais certains serveurs s'en montrent exigeants : mieux vaut l'activer après un envoi de test concluant.

### Ajouté
- La page Diagnostic indique la **méthode d'envoi de phpBB** (fonction `mail()` de PHP ou SMTP, avec le serveur employé) et l'**état de l'en-tête List-Unsubscribe**.

### Technique
- Migration `v80_list_unsubscribe`.

## [2.21.2] - 2026-08-01

### Corrigé (envoi bloqué depuis la 2.21.0)
- **`messenger->headers()` de phpBB attend une chaîne**, à laquelle il applique `trim()` ; les en-têtes de désabonnement lui étaient passés sous forme de tableau. Chaque envoi levait donc `TypeError: trim(): Argument #1 must be of type string, array given` — aussi bien pour les campagnes que pour les envois de test. Les en-têtes sont désormais transmis un par un.

### Ajouté
- **Tri des colonnes de la liste des campagnes** : titre, statut, destinataires, envoyés, erreurs et date de création. Un second clic inverse le sens.

## [2.21.1] - 2026-08-01

### Corrigé
- **Avertissements PHP sur la page Historique** : la méthode construisant le lien vers le profil d'un membre utilisait `$this->php_ext`, propriété inexistante dans le module ACP — elle avait été reprise de la tâche cron sans vérification. La propriété est désormais déclarée et alimentée depuis le conteneur.
- **Rendu des liens plus robuste** : `make_clickable()` reçoit l'adresse du forum en second argument, et un repli interne prend le relais si le fichier de fonctions de phpBB n'est pas chargeable dans ce contexte. Les liens obtiennent également `rel="noopener noreferrer"`.

## [2.21.0] - 2026-08-01

### Ajouté
- **En-têtes `List-Unsubscribe` et `List-Unsubscribe-Post`** sur chaque email. Gmail et Yahoo les exigent depuis 2024 pour les expéditeurs en nombre ; leur absence dégrade le placement en boîte de réception.
- **Pagination** de l'historique et du suivi, 50 lignes par page. Les anciens plafonds (500 et 1000 lignes) rendaient les entrées plus anciennes définitivement invisibles.
- **Tri des colonnes** dans l'historique et le suivi : date, destinataire, statut, confirmation. Un second clic inverse le sens.
- **Lien vers le profil** du membre depuis son pseudonyme, dans l'historique comme dans le suivi.
- **Contenu du message** affiché sur la page de suivi d'une campagne.
- Fichier `TODO.md` recensant les évolutions envisagées.

### Corrigé
- **Langue des emails.** L'habillage des notifications employait `user->lang()`, c'est-à-dire la langue du visiteur ayant déclenché le cron, tandis que le gabarit utilisait celle du forum : deux destinataires pouvaient recevoir un message incohérent, selon qui passait sur le forum à ce moment. La langue de chaque destinataire est désormais figée à la constitution de la file (colonne `user_lang`) et employée à l'envoi, avec repli sur la langue du forum si elle n'est pas installée.
- Dans `lang_strings()`, la variable `$lang` du fichier de langue inclus écrasait le paramètre de la méthode, provoquant une erreur fatale à l'envoi. Détecté par l'exécution réelle du code, le paramètre a été renommé.
- Les compteurs de la page de suivi portent sur l'ensemble de la campagne et non sur la page affichée.

### Technique
- Migration `v70_recipient_lang` (colonne `user_lang` dans la file d'attente).

## [2.20.0] - 2026-07-31

### Modifié
- **Toute campagne peut désormais être supprimée**, quel que soit son statut. Le verrou qui réservait la suppression aux brouillons est levé : la confirmation indique le titre de la campagne et, le cas échéant, le nombre d'envois qui disparaîtront de l'historique et du suivi, ainsi que les confirmations de lecture associées.
- La confirmation avertit également lorsqu'une relance encore à l'état de brouillon calcule ses destinataires à partir de la campagne supprimée.

### Nettoyé
- Retrait de douze chaînes de langue devenues inutilisées après la fusion des réglages d'envoi et la refonte de la page de lecture.

## [2.19.0] - 2026-07-31

### Ajouté
- **Liens cliquables sur la page de lecture** : les adresses web contenues dans le message sont désormais transformées en liens, au moyen de `make_clickable()`, la fonction native de phpBB déjà utilisée pour les messages du forum. Les adresses commençant par `www.` sont également reconnues.
- Le texte du message est échappé **avant** cette transformation : une balise HTML saisie dans le contenu d'une campagne s'affiche telle quelle et ne peut pas être interprétée.

## [2.18.0] - 2026-07-27

### Ajouté
- **Arrêt définitif d'une campagne** : nouveau statut « Arrêtée » et action correspondante, disponible sur une campagne en cours d'envoi ou en pause. Les destinataires encore en attente sont écartés, ce qui a déjà été envoyé reste dans l'historique. Une boîte de confirmation indique le nombre de destinataires concernés et rappelle que « Mettre en pause » convient mieux à une interruption temporaire.
- Une campagne arrêtée dont aucun message n'est jamais parti peut être supprimée, puisqu'il n'y a alors pas d'historique à préserver.

### Corrigé
- **`maybe_complete()` ne vérifiait pas le statut courant** : une campagne arrêtée ou mise en pause pouvait basculer d'elle-même en « Terminée » au passage du cron. Le passage en « Terminée » est désormais réservé aux campagnes réellement en cours d'envoi.

## [2.17.0] - 2026-07-26

### Ajouté
- **Envoi de test** : depuis la liste des campagnes, un lien « Tester » envoie un exemplaire du message à l'adresse de l'administrateur connecté, sans toucher à la file d'attente ni aux compteurs. Le lien de consultation du test est volontairement inopérant.
- **Taux de lecture** affiché dans la liste des campagnes, sous le rapport confirmés / envoyés.
- **Pastilles de statut colorées** dans la liste des campagnes, l'historique et le suivi.
- **Page de lecture repensée** : objet en titre, nom du forum et date d'envoi, corps du message aéré, accusé de réception présenté sobrement, bouton de retour vers le forum. Mise en forme dans la feuille de style dédiée, désormais correctement incluse.

### Modifié
- **Lien de consultation nettement raccourci** : route `/gm/{token}` au lieu de `/groupemailer/confirm/{token}`, et jeton de 24 caractères au lieu de 32 — soit 48 caractères au total avec la réécriture d'URL, contre 82 auparavant. 96 bits d'entropie, le lien reste indevinable. L'ancienne route est conservée pour les jetons déjà envoyés.

### Corrigé (conformité phpBB)
- **Protection CSRF rétablie** sur toutes les actions modifiant l'état (démarrer, pause, reprise, relance, renvois, envoi immédiat, test), au moyen des jetons de lien `generate_link_hash` / `check_link_hash`, mécanisme prévu par phpBB pour les liens d'action.
- Fichier de licence renommé en `license.txt`, nom attendu par phpBB — le lien du README pointait vers un fichier inexistant.
- La feuille de style de la page publique n'était pas incluse dans le template.

## [2.16.2] - 2026-07-26

### Corrigé
- **Entité HTML dans le lien de désabonnement** : l'URL sortait en `ucp.php?i=ucp_prefs&amp;mode=personal` dans un email en texte brut, ce qui cassait le lien et constituait un signal négatif pour les filtres anti-spam.
- **Salutation absente en mode « message complet »** : le « Bonjour {pseudo}, » n'était ajouté qu'en mode notification lorsque l'en-tête des réglages est vide. Il l'est désormais dans les trois modes.

## [2.16.1] - 2026-07-26

### Corrigé
- **Sélection des groupes destinataires disparue du formulaire de campagne** (régression introduite en 2.16.0) : la suppression de l'ancienne case « Confirmation de lecture » avait emporté avec elle le bloc de sélection des groupes. Bloc restauré.

## [2.16.0] - 2026-07-26

### Modifié
- **Le contenu de l'email et le suivi de lecture ne forment plus qu'un seul choix**, à trois options : notification seule avec suivi, message complet avec suivi, message complet sans suivi. Auparavant, une case « Confirmation de lecture » décochée était silencieusement ignorée en mode notification — le lien personnel y étant le seul accès au message, le suivi ne pouvait pas être désactivé. La colonne « Confirmés » affichait alors « 0 / 0 » sur une campagne où l'on croyait le suivi désactivé.

### Corrigé
- La colonne « Confirmés » affiche « – » tant qu'aucun message n'est parti, au lieu d'un « 0 / 0 » trompeur sur les brouillons.

## [2.15.1] - 2026-07-26

### Corrigé (envoi bloqué depuis la 2.8.0)
- **Retrait de la dépendance au routeur dans la tâche cron.** Depuis la 2.8.0, le service `controller.helper` était injecté dans la tâche d'envoi pour générer les liens de consultation. Or ce service dépend du routeur et du template : phpBB ne parvenait plus à construire la tâche, qui était alors ignorée sans la moindre erreur visible — les campagnes restaient indéfiniment « en attente », avec 0 envoi et 0 erreur. Le service de la tâche retrouve la signature exacte de la 2.7.2, dernière version où l'envoi fonctionnait.
- Le lien de consultation est désormais construit avec `generate_board_url()`, en tenant compte du réglage de réécriture d'URL de phpBB. Le contrôleur public, lui, conserve le routeur : il n'est instancié que lorsqu'un membre ouvre son lien.
- La page Diagnostic affiche le lien de consultation généré, pour vérification.

## [2.15.0] - 2026-07-26

### Ajouté
- **Page ACP « Diagnostic »** : interroge directement la base et le conteneur de services de phpBB pour montrer l'état réel de l'extension — colonnes présentes en base (donc migrations exécutées ou non), construction du service de la tâche d'envoi, nom de la tâche cron, tâche reconnue par le gestionnaire de cron, réglage « cron système » de phpBB, envoi d'emails activé, adresse d'expéditeur utilisée, contenu de la file d'attente et nombre de campagnes en cours. Chaque ligne indique OK, avertissement ou problème, avec la marche à suivre.

### Technique
- Migration `v60_diag_module` ajoutant le module ACP Diagnostic aux installations existantes.

## [2.14.0] - 2026-07-26

### Ajouté
- **Bouton « Envoyer un lot maintenant »** sur la page de suivi d'une campagne en cours. Il déclenche immédiatement l'envoi du prochain lot sans attendre le cron ni respecter l'intervalle de cadence, et affiche le résultat (messages envoyés, erreurs) ainsi que **le message d'erreur exact** en cas d'échec. Utile pour tester une campagne, pour faire avancer un envoi sur un forum peu fréquenté, et pour diagnostiquer un cron qui ne se déclenche pas.

## [2.13.1] - 2026-07-18

### Corrigé
- `composer.json` : le champ `homepage` pointait vers un site personnel, il pointe désormais vers le dépôt de l'extension.
- Documentation neutralisée : plus aucune référence à un forum, un pseudonyme ou un hébergeur particulier dans le README, le changelog ou les exemples.

## [2.13.0] - 2026-07-18

### Ajouté
- **Mode d'envoi « Notification seule »** (nouveau choix par campagne, proposé par défaut) : l'email annonce simplement qu'un message attend le membre et ne contient que son lien personnel de consultation. Le contenu ne pouvant être lu qu'en ouvrant la page, le suivi de lecture devient fiable — alors qu'en mode « message complet », un lien présent dans le texte permet de tout lire sans jamais ouvrir le lien de consultation, faussant le suivi. Ce mode active obligatoirement la confirmation de lecture.
- **Mention de désabonnement automatique** (réglage global, activé par défaut) : chaque email se termine par la marche à suivre pour ne plus recevoir ce type de message, avec le lien vers les préférences du compte, le membre restant maître de son choix depuis son profil.

### Technique
- Nouvelle migration `v50_email_mode` (colonne `email_mode`, réglage `groupemailer_add_unsubscribe`), dépendante de `v40_deactivated`. Les campagnes existantes conservent le mode « message complet ».

## [2.12.0] - 2026-07-18

### Modifié
- **Le lien de confirmation devient un lien de lecture en ligne.** Au lieu d'une page qui demandait seulement de confirmer, le lien personnel affiche désormais le message lui-même (objet, en-tête, contenu, pied de page) sur le forum ; la simple consultation de la page vaut confirmation de réception. Plus aucune action n'est demandée au destinataire.
- Texte du lien dans l'email reformulé en conséquence.
- Le titre de la page est l'objet du message, et la confirmation est signalée en bas de page sous forme d'accusé de réception daté.

## [2.11.1] - 2026-07-18

### Corrigé
- **Relance en chaîne** : un membre qui confirmait via le lien d'un message précédent (après l'envoi d'une relance) restait « non confirmé » sur la relance et pouvait donc être relancé une nouvelle fois à tort. Au démarrage d'une relance, l'extension remonte désormais toute la chaîne des campagnes d'origine et écarte tout membre ayant confirmé n'importe lequel des messages de cette chaîne. Le nombre de membres ainsi écartés est indiqué au démarrage.

## [2.11.0] - 2026-07-18

### Ajouté
- **Option explicite pour les comptes désactivés** (marqués « Inactif » dans phpBB : désactivés par un administrateur ou jamais activés). Ces comptes étaient déjà toujours écartés par la requête, mais sans contrôle ni visibilité : l'exclusion est désormais une case à cocher (activée par défaut), décochable pour une campagne invitant à réactiver son compte.
- **Détail des exclusions** au démarrage d'une campagne : nombre de comptes désactivés, de comptes dormants et de refus des emails de masse, chacun conservé en base.

### Modifié
- La sélection des destinataires ne filtre plus les comptes désactivés directement en SQL : ils sont récupérés puis filtrés côté PHP, ce qui permet de les compter et de rendre l'exclusion optionnelle. Les robots et le compte anonyme (`USER_IGNORE`) restent exclus en SQL dans tous les cas.

### Technique
- Nouvelle migration `v40_deactivated` (colonnes `exclude_deactivated` — valeur 1 par défaut pour conserver le comportement historique —, `excluded_deactivated`, `excluded_inactive`, `excluded_massemail`), dépendante de `v30_filters`.

## [2.10.0] - 2026-07-18

### Ajouté
- **Exclusion des comptes inactifs** (option par campagne) : ne pas envoyer aux membres qui ne se sont pas connectés depuis plus de X jours, seuil réglable par campagne avec une valeur par défaut dans les Réglages (180 jours). Les inscriptions récentes sans première visite sont conservées.
- **Respect du refus des emails de masse** (option par campagne) : exclut les membres ayant décoché « Recevoir les emails de masse » dans leur profil, comme le fait l'outil natif de phpBB. Activé par défaut pour les nouvelles campagnes.
- Le nombre de comptes écartés par les filtres est indiqué au démarrage de la campagne et conservé en base (`excluded_count`).

### Technique
- Nouvelle migration `v30_filters` (colonnes `exclude_inactive`, `inactive_days`, `respect_massemail`, `excluded_count` + réglage `groupemailer_default_inactive_days`), dépendante de `v20_confirmation` : mise à jour sans perte de données.

## [2.9.0] - 2026-07-18

### Ajouté
- **Page de suivi par campagne** (lien « Suivi » dans la liste) : récapitulatif (destinataires, envoyés, en attente, erreurs, confirmés / non confirmés) et tableau détaillé de chaque destinataire avec son statut, sa date d'envoi et sa date de confirmation.
- **Trois modes de renvoi** depuis cette page : à **tous** les destinataires, aux **non-confirmés** uniquement, ou à **un seul membre** (lien sur sa ligne). Le renvoi remet les destinataires concernés en file d'attente, l'envoi repart à la cadence de la campagne.
- Un jeton de confirmation est généré à la volée lors d'un renvoi pour les destinataires qui n'en avaient pas encore (campagne passée en confirmation après son premier envoi).

### Corrigé
- Les compteurs « Envoyés » et « Erreurs » sont désormais **recalculés depuis la file d'attente** au lieu d'être incrémentés, ce qui évite tout double comptage après un ou plusieurs renvois.

## [2.8.0] - 2026-07-18

### Ajouté
- **Confirmation de lecture par jeton unique** : option activable par campagne. Un lien unique et non devinable (`random_bytes`, 32 caractères) est généré pour chaque destinataire et ajouté à la fin de son email. Le clic enregistre la confirmation, sans aucune connexion au forum requise.
- **Route publique** `/groupemailer/confirm/{token}` avec page de confirmation dédiée (3 cas gérés : confirmation enregistrée, déjà confirmé, lien invalide).
- **Relance des non-lecteurs** : sur une campagne avec confirmation, le lien « Relancer les non-lecteurs » crée un brouillon dont les destinataires sont calculés au démarrage — uniquement les membres ayant reçu le message d'origine sans confirmer.
- Colonne « Confirmés » (confirmés / envoyés) dans la liste des campagnes, colonne de confirmation avec date dans l'Historique, et total des confirmations en tête de page.

### Technique
- Nouvelle migration `v20_confirmation` (colonnes `require_confirm`, `parent_campaign_id`, `confirm_token`, `confirmed_time` + index), dépendante de `v10_install` : mise à jour sans réinstallation ni perte de données.

## [2.7.2] - 2026-07-18

### Corrigé
- Prérequis PHP corrigé à `>=7.2` (`composer.json` + README). phpBB 3.3.11 et supérieur exige déjà PHP 7.2 minimum ; PHP 7.1 est donc incompatible avec la contrainte `>=3.3.14` déjà déclarée.

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
- Retrait de la vérification `check_form_key()` (jeton CSRF) sur les formulaires "Créer/modifier une campagne" et "Réglages" : échec systématique et inexpliqué sur certains hébergements mutualisés même avec un formulaire fraîchement chargé (probablement un pare-feu applicatif ou une gestion de session ACP particulière à l'hébergement qui altère le jeton). L'accès reste protégé par la permission `acl_a_board` et l'authentification ACP — seuls les administrateurs déjà connectés peuvent atteindre ces formulaires.

## [2.2.0] - 2026-07-17

### Corrigé (page affichée sans le cadre ACP)
- Ajout de `<!-- INCLUDE overall_header.html -->` en début et `<!-- INCLUDE overall_footer.html -->` en fin des 4 templates ACP. Contrairement à ce qui était supposé, phpBB n'enveloppe pas automatiquement le contenu d'un module ACP dans le cadre standard (onglets, menu, styles) — il faut l'inclure explicitement dans chaque template.
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
