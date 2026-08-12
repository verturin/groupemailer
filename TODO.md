# TODO — Group Mailer

Liste des évolutions envisagées. Les éléments cochés sont livrés.

## Corrections de fond

- [x] **1. En-tête `List-Unsubscribe`** — exigé par Gmail et Yahoo depuis 2024 pour les expéditeurs en nombre ; son absence pénalise le placement en boîte de réception. Pointe vers le lien de désabonnement personnel et activé par défaut. *(v2.21.0, v2.24.0, v2.26.0)*
- [x] **2. Langue du destinataire** — l'habillage des notifications utilisait la langue du visiteur ayant déclenché le cron, et le gabarit celle du forum. La langue de chaque destinataire est désormais figée à la constitution de la file et utilisée à l'envoi. *(v2.21.0)*
- [x] **3. Pagination** — l'historique s'arrêtait à 500 lignes et le suivi à 1000, sans navigation. *(v2.21.0)*
- [x] **Tri des colonnes** — historique et suivi triables par date, destinataire, statut et confirmation. *(v2.21.0)*
- [x] **Sauvegarde et restauration des campagnes** — export JSON complet ou modèles seuls, restauration sans écrasement. *(v2.23.0)*
- [x] **Lien vers le profil** — le pseudo renvoie à la fiche du membre. *(v2.21.0)*
- [x] **Pseudonymes en couleur de groupe** dans l'historique et le suivi. *(v2.27.0)*
- [x] **Sélection des campagnes à sauvegarder** — cases à cocher plutôt qu'un export global. *(v2.28.0)*

## Prérequis de mise en service

> **L'adresse d'expéditeur doit être une boîte réellement créée sur le domaine du forum.**
> Une adresse d'un autre domaine, ou l'adresse par défaut du serveur mutualisé, est acceptée
> par l'hébergeur puis écartée en silence par les grandes messageries. Le journal de remise
> de l'hébergeur affiche « accepté » dans les deux cas : il ne permet donc pas de détecter
> le problème, seule la réception effective le confirme.

## Délivrabilité

- [x] **4. Désabonnement en un clic** depuis l'email et la page de lecture, sans connexion préalable, avec réabonnement possible et mention dans le suivi. *(v2.24.0)*
- [x] **5. Détection des adresses en échec répété** — compteur d'échecs consécutifs par membre, exclusion automatique au-delà d'un seuil réglable, affichage dans le suivi et réintégration manuelle. *(v2.25.0)*

## Volume et usage courant

- [ ] **6. Recherche et filtres** dans l'historique et le suivi (par membre, campagne, statut).
- [ ] **7. Export CSV** du suivi d'une campagne.
- [ ] **8. Purge automatique** des campagnes de plus de X mois.

## Confort de rédaction

- [x] **9. Dupliquer une campagne** — message et réglages repris dans un nouveau brouillon. *(v2.29.0)*
- [ ] **10. Aperçu de la page de lecture** depuis l'ACP, sans envoyer d'email.
- [ ] **11. BBCode dans le message**, rendu sur la page de lecture (gras, listes, citations).
- [x] **12. Démarrage programmé** à une date et une heure, avec annulation possible. *(v2.29.0)*

## Suivi

- [x] **13. Tableau de bord** — vue d'ensemble et comparatif des campagnes. *(v2.29.0)*
- [ ] **14. Notification à l'administrateur** à la fin d'une campagne.
- [ ] **15. « Ne plus relancer »** par membre, ou relance automatique plafonnée à N rappels.

## Robustesse

- [x] **16. Tests automatisés PHPUnit** — 17 tests couvrant l'envoi et la structure du module. *(v2.29.0)*
