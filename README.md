# 🌱 EcoRide – Plateforme de covoiturage écologique

**EcoRide** est une application web développée dans le cadre du titre professionnel Développeur Web et Web Mobile.  
Elle vise à encourager le covoiturage en mettant en avant des trajets écoresponsables, principalement réalisés en voiture électrique.

---

## 🚀 Objectifs

- Réduire l'empreinte carbone des trajets en voiture
- Proposer une plateforme simple et intuitive pour les passagers et les chauffeurs
- Fournir des fonctionnalités de gestion de trajets, profils, avis, crédits, filtres…

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
docker compose up -d
```

🔗 L’application Symfony sera disponible sur :  
[http://localhost:8080](http://localhost:8080)  

🔗 phpMyAdmin est disponible sur :  
[http://localhost:8081](http://localhost:8081)  
→ Identifiants : `root` / `root` ou `user` / `userpass`

---

## 📁 Arborescence simplifiée

```
ecoride/
├── app/                # Projet Symfony
├── apache/             # Configuration Apache
│   └── vhost.conf
├── docker-compose.yml  # Stack de services (Apache, MySQL, MongoDB)
├── .gitignore
└── README.md
```

---

## 🧪 Commandes utiles (dans le container Apache)

```bash
# Entrer dans le container
docker exec -it ecoride_apache bash

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

## 📌 Liens à venir

- [Maquette Figma](#)
- [Tableau Trello](#)
- [Documentation technique](#)

---

## 👤 Auteur

**Enzo Pauchet**  
Développeur Web & ancien joueur pro – passionné par la tech & le sport ⚽💻
