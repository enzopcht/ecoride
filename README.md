# 🌱 EcoRide – Plateforme de covoiturage écologique

**EcoRide** est une application web développée dans le cadre du titre professionnel Développeur Web et Web Mobile.  
Elle vise à encourager le covoiturage en mettant en avant des trajets écoresponsables, principalement réalisés en voiture électrique.

---

## 🚀 Objectifs

- Réduire l'empreinte carbone des trajets en voiture
- Proposer une plateforme simple et intuitive pour les passagers et les chauffeurs
- Fournir des fonctionnalités de gestion de trajets, profils, avis, crédits, filtres…
- Assurer une sécurité robuste et une expérience utilisateur fluide

---

## 🛠️ Stack technique

- **Symfony 7.2** (back-end MVC)
- **Twig** (templating)
- **Doctrine ORM** (base de données MySQL)
- **MongoDB** (stockage des préférences chauffeurs)
- **Apache + Docker** (environnement multi-plateforme)
- **phpMyAdmin** (interface visuelle MySQL)

---

## ⚙️ Installation (via Docker)

> Cloner le projet et exécuter :

```bash
git clone https://github.com/enzopcht/ecoride.git
cd ecoride

# Créez et configurez votre fichier .env.local avec : 
# MAILER_DSN="..."
# DATABASE_URL="..."
# MONGODB_URL="..."
# ORS_API_KEY=""
# APP_SECRET=VOTRE_CLE

docker compose up --build -d
docker compose exec apache bash
composer install
```

ℹ️ Pour une exécution plus rapide (sans reconstruction), une fois le projet buildé une première fois :  
`docker compose up -d`

🔗 L’application Symfony sera disponible sur :  
[http://localhost:8080](http://localhost:8080)  

🔗 phpMyAdmin est disponible sur :  
[http://localhost:8081](http://localhost:8081)  

---

## 🔄 Switcher entre environnements dev et prod

Pour basculer entre les environnements de développement et de production, procédez comme suit :

1. Modifiez manuellement la variable `APP_ENV` dans votre fichier `.env` ou `.env.local` :  
   - `APP_ENV=dev` et `APP_DEBUG=1` pour le développement  
   - `APP_ENV=prod` et `APP_DEBUG=0` pour la production

2. Arrêtez les conteneurs Docker en cours d'exécution :  
   ```bash
   docker compose down
   ```

3. Relancez les conteneurs avec la configuration mise à jour :  
   ```bash
   docker compose up --build -d
   ```

4. Entrez dans le conteneur Apache pour installer ou mettre à jour les dépendances si nécessaire :  
   ```bash
   docker compose exec apache bash
   composer install ou composer install --no-dev --optimized-autoloader
   ```

Cette méthode vous permet de gérer les environnements sans scripts additionnels, en respectant les bonnes pratiques Docker et Symfony.

---

## 🧪 Commandes utiles pour le développement

> ℹ️ Ces commandes Symfony sont à utiliser uniquement dans le cadre du développement local.  
> En production, la base de données est déjà créée manuellement et les fixtures ne sont pas utilisées.

```bash
# Entrer dans le container
docker exec -it apache bash

# Créer la base de données
php bin/console doctrine:database:create

# Créer une entité
php bin/console make:entity

# Créer une migration
php bin/console make:migration

# Appliquer la migration
php bin/console doctrine:migrations:migrate
```

---

## 📦 Fichier SQL

Le fichier `database/ecoride_database.sql` contient :

- la structure complète de la base de données relationnelle `ecoride`
- les clés primaires, étrangères, et contraintes d’intégrité
- des exemples de requêtes SQL permettant de démontrer la maîtrise de :
  - création de trajets (`INSERT INTO`)
  - réservation de trajets (`INSERT INTO participation`)
  - validation de réservations avec mise à jour (`UPDATE`, `JOIN`)
  - recherche de trajets (`SELECT`, `WHERE`, `JOIN`, `ORDER BY`, `LIMIT`)
  - gestion transactionnelle avec vérification des places disponibles

- 💡 Ce fichier SQL est fourni à titre de démonstration technique.  
- Il prouve la capacité à manipuler manuellement le langage SQL (création de tables, requêtes avancées, contraintes, etc.).  
- Il n’est **pas destiné à remplacer** le système officiel de migration utilisé par Symfony (Doctrine Migrations).  
Le projet Symfony s’appuie uniquement sur `php bin/console doctrine:migrations:migrate` pour créer et synchroniser la base.

---

## 🧱 Données & Fixtures

Le projet contient des fixtures permettant de générer :
- des utilisateurs de rôles variés (passager, conducteur, admin, employé)
- des trajets types, véhicules, avis et réservations
- des préférences MongoDB
- des transactions

⚠️ Ces fixtures **ne doivent pas être exécutées en production**.  
En prod, l’admin est créé manuellement via phpMyAdmin.

---

## 📌 Liens à venir

- [Maquette Figma](#)
- [Tableau Trello](#)
- [Documentation technique](#)

---

## 🔐 Accès & Sécurité

- Les routes sont protégées par rôles (`ROLE_PASSENGER`, `ROLE_DRIVER`, `ROLE_EMPLOYE`, `ROLE_ADMIN`)
- Les tokens CSRF sont utilisés pour sécuriser tous les formulaires sensibles (réservations, suppressions…)
- Les utilisateurs ne peuvent pas accéder à des sections qui ne correspondent pas à leur rôle
- L’inscription est sécurisée via contraintes serveur (`Regex`, `NotBlank`, validation Twig)
- Les identifiants sensibles (clé API ORS, connexions DB) sont stockés dans `.env` ou variables d’environnement au moment du déploiement
- Pour des raisons de sécurité, phpMyAdmin est **inaccessible en production**

---

## 👤 Auteur

**Enzo Pauchet**  
Développeur Web & ancien joueur pro – passionné par la tech & le sport ⚽💻

---

## 🧪 Phase de test

Le projet a été testé manuellement selon les cas suivants :
- Création de trajets
- Réservations avec validation et paiements simulés
- Cycle complet passager/driver (inscription, validation, litige)
- Restrictions d’accès par rôle
- Gestion de préférences utilisateur avec MongoDB
- Suppression protégée (voiture, trajets)
