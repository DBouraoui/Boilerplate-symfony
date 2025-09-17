## 🧩 Bundles Disponibles

- [`lexik/jwt-authentication-bundle`](https://github.com/lexik/LexikJWTAuthenticationBundle)
- `symfony/messenger`
- `symfony/maker-bundle` (dev only)
- `doctrine/orm`
- `symfony/serializer`
- `symfony/validator`
- `symfony/uid`
- `symfony/rate-limiter`
- `symfony/mailer` (via Gmail — ne pas utiliser en prod)
- `phpstan/phpstan`
- `league/flysystem-bundle`
- `symfony/twig-bundle`
- **Monlogs**
- **PHPUnit** (via `symfony/test-pack`)

### ✅ Tests

- 📁 Les tests se trouvent dans le dossier `tests/`
- 🛠 Utilisez la commande `bin/console make:test` pour générer des fichiers de test
- ▶️ Lancez les tests avec :

  ```bash
  php bin/phpunit
  ```

---

## 🚀 Étapes d'installation

1. Générer les clés JWT :

   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```

2. Copier le fichier `.env` vers `.env.local` :

   ```bash
   cp .env .env.local
   ```

3. Compléter les informations nécessaires dans `.env.test.local`

4. Générer les clés JWT pour les tests :

   ```bash
    openssl genrsa -out config/jwt/private-test.pem -aes256 4096
    openssl rsa -pubout -in config/jwt/private-test.pem -out config/jwt/public-test.pem
   ```
   
    /!\Ne pas oublier de copier la passphrase dans .env.test.local

5. Effectuer les migrations:

   ```bash
   docker compose exec php bin/console make:migration
   docker compose exec php bin/console d:m:m
   ```

---

## 🛠️ Makefile Commands

| Commande    | Description                                                        |
|-------------|--------------------------------------------------------------------|
| `make dev`  | Lance le mode développement (Adminer, serveur, PostgreSQL)         |
| `make prod` | Lance la production (Adminer, serveur, PostgreSQL, Grafana)        |
| `make down` | Stoppe Docker et nettoie les logs                                  |
| `make check`| Exécute PHPStan pour vérifier le code                              |
| `make test` | Lance PHPUnit avec option `--testdox`                              |
| `make consume` | Lance Symfony Messenger en tâche de fond pour les jobs asynchrones |

---

## 🔐 HTTPS Local

Pour activer le HTTPS en local :
- Supprimer les variables d’environnement du service `php` dans le `compose.yml`
- L’application s'exécutera sur `https://localhost` au lieu de `http://localhost:8000`

---

## ✨ Fonctionnalités

- ✅ Inscription utilisateur avec email et mot de passe + envoi d'un email de confirmation
- 🔐 Connexion utilisateur via email/mot de passe
- 📝 Journalisation des connexions (`IP`, `User Agent`, `Date`) dans la table `session`
- 📩 Confirmation d'email à l'inscription
- 🔄 Mot de passe oublié → envoi d'un email avec un token de réinitialisation

---

## 🧠 Fonctionnalités Avancées

- ⏱️ Rate Limiter sur inscription et connexion
- 🧾 Logs utilisateurs pour les actions de base
- ⚙️ Utilisation d’`Event` et `EventSubscriber` pour les emails et le ratelimit
- ✉️ Emails envoyés de manière asynchrone via Symfony Messenger (`make consume`)
- 📦 Utilisation de DTOs pour mapper les requêtes et valider les données via `assertions`

---

## 🧪 Intégration avec Bruno

> Tous les appels API sont configurés pour [Bruno](https://www.usebruno.com/) dans le dossier :

```
Boilerplate-sf-bruno/
├── Auth/
├── bruno.json
└── environments/
```

---

## 📁 Structure du Projet (niveau 2)

```
.
├── Boilerplate-sf-bruno/       # Config pour Bruno (API REST client)
├── bin/                        # Console Symfony & PHPUnit
├── config/                     # Configuration Symfony
├── migrations/                 # Fichiers de migration Doctrine
├── public/                     # Point d’entrée public (index.php)
├── src/                        # Code source PHP (Controller, Entity, Service, etc.)
├── templates/                  # Templates Twig (emails, etc.)
├── tests/                      # Fichiers de test
├── var/                        # Cache, logs, stockage local
├── vendor/                     # Dépendances Composer
├── .env, .env.local, .env.test
├── compose.yml, compose.override.yml
├── Dockerfile
├── Makefile
├── phpunit.dist.xml
├── phpstan.neon
└── Readme
```

---

## 👤 Auteur

> 🧑‍💻 Ce boilerplate a été conçu avec soin pour accélérer la création d’API Symfony modernes, sécurisées et testables.

---

## 📝 Licence

Ce projet est sous licence **MIT**.
Vous pouvez l’utiliser, le modifier, et le redistribuer librement.

---

## 📬 Contact

Des questions, suggestions ou bugs ?
→ Ouvrez une **Issue** ou contactez le mainteneur via l’adresse indiquée dans le repo.

---
