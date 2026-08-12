# Tests

Tests unitaires de l'extension, exécutables sans installation de phpBB.

Ils couvrent la logique propre à l'extension : résolution de la langue du
destinataire, construction du corps des emails, filtrage des destinataires,
comptage des échecs, mise en forme des statuts et conversion des dates de
programmation. Les dépendances à phpBB sont remplacées par des doublures
minimales, définies dans `bootstrap.php`.

## Exécution

```
composer require --dev phpunit/phpunit
vendor/bin/phpunit -c tests/phpunit.xml
```

Ou, si PHPUnit est installé globalement :

```
phpunit -c tests/phpunit.xml
```
