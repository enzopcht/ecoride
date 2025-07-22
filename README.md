# 🌱 EcoRide – Plateforme de covoiturage écologique

**EcoRide** est une application web visant à encourager le covoiturage écoresponsable.  
Elle permet aux conducteurs de proposer des trajets et aux passagers de réserver et noter leurs expériences.

---

## 🛠️ Stack & Objectifs

- **Symfony 7.2** (MVC)  
- **Twig** (templating)  
- **Doctrine ORM** (MySQL)  
- **MongoDB** (préférences utilisateurs)  
- **Docker & Apache** (environnement)  
- **phpMyAdmin** (interface MySQL)

### Objectifs clés
1. Réduire l’empreinte carbone  
2. Simplifier la gestion des trajets  
3. Offrir une expérience sécurisée et fluide  

---

## 🚀 Installation & Configuration

```bash
# 1. Cloner le projet
git clone https://github.com/enzopcht/ecoride.git
cd ecoride

# 2. Préparer .env.local (à la racine) :
#    DATABASE_URL="mysql://user:pass@host:port/dbname"
#    MAILER_DSN="smtp://user:pass@smtp.server:port?encryption=tls&auth_mode=login"
#    MONGODB_URL="mongodb://host:port/dbname"
#    ORS_API_KEY="VOTRE_CLE"
#    APP_SECRET="VOTRE_CLE"

# 3. Lancer Docker
docker compose up --build -d

# 4. Entrer dans le conteneur Apache pour installer les dépendances
docker compose exec apache bash
composer install --no-dev --optimize-autoloader

# 5. Créer et migrer la base de données
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

---

## 🔗 Services disponibles

- **Application Symfony** : http://localhost:8080  
- **phpMyAdmin** : http://localhost:8081  

---

## ⚡ Commandes courantes

```bash
# Entrer dans le conteneur Apache
docker compose exec apache bash

# Créer/Modifier une entité
php bin/console make:entity

# Générer une migration
php bin/console make:migration

# Appliquer les migrations
php bin/console doctrine:migrations:migrate

# Vider le cache
php bin/console cache:clear
```

---

## 📦 Structure SQL d’exemple

Le fichier `database/ecoride_database.sql` contient un aperçu de la structure et des requêtes d’exemple.

---

## 🔐 Sécurité & Accès

- Routes protégées par rôles (ROLE_PASSENGER, ROLE_DRIVER, ROLE_ADMIN…)  
- Tokens CSRF sur tous les formulaires critiques  
- Variables d’environnement pour les clés sensibles  
- phpMyAdmin désactivé en production  

---

## 👤 Auteur

**Enzo Pauchet**  
Développeur Web & passionné de tech  