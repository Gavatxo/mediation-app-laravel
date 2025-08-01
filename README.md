# 🏢 Application de Médiation - Laravel 12 + React

Application moderne de gestion de médiation développée avec Laravel 12, React 19, et une architecture Docker complète.

## 🚀 Installation Rapide avec Docker

### Prérequis
- **Docker** & **Docker Compose** installés
- **Git** installé
- **MySQL Workbench** (recommandé pour la gestion BDD)

---

## 📦 Installation Complète

### 1. Cloner le Repository
```bash
git clone https://github.com/Gavatxo/mediation-app-laravel.git
cd mediation-app-laravel
git checkout develop
```

### 2. Configuration Environnement
```bash
# Copier le fichier d'exemple
cp .env.example .env

# Générer la clé d'application Laravel
php artisan key:generate
```

### 3. Lancement Docker
```bash
# Build et démarrage de tous les services
docker-compose -f docker/docker-compose.yml up -d --build
```

### 4. Installation des Dépendances
```bash
# Dépendances PHP (Composer)
docker-compose -f docker/docker-compose.yml exec app composer install

# Dépendances JavaScript (NPM)
docker-compose -f docker/docker-compose.yml exec app npm install
```

### 5. Base de Données
```bash
# Lancer les migrations
docker-compose -f docker/docker-compose.yml exec app php artisan migrate

# (Optionnel) Données de test
docker-compose -f docker/docker-compose.yml exec app php artisan db:seed
```

### 6. Build Frontend
```bash
# Build production
docker-compose -f docker/docker-compose.yml exec app npm run build

# OU mode développement (watch)
docker-compose -f docker/docker-compose.yml exec app npm run dev
```

---

## 🌐 Accès à l'Application

### URLs Locales
- **Application principale** : http://localhost:8000
- **Interface MailHog** (tests emails) : http://localhost:8025
- **Base de données** : localhost:3306

### Connexion MySQL Workbench
```
Nom : Médiation DEV Local
Host : localhost
Port : 3306
Username : dev_user
Password : dev_password_secure_2024
Database : mediation_dev
```

---

## 🛠️ Scripts de Développement

### Utilisation des Scripts
```bash
# Rendre le script exécutable
chmod +x scripts/docker-dev.sh

# Démarrer l'environnement
./scripts/docker-dev.sh start

# Arrêter l'environnement
./scripts/docker-dev.sh stop

# Build React en mode watch
./scripts/docker-dev.sh build

# Lancer les migrations
./scripts/docker-dev.sh migrate

# Lancer les tests
./scripts/docker-dev.sh test
```

### Commandes Docker Utiles
```bash
# Voir les conteneurs en cours
docker-compose -f docker/docker-compose.yml ps

# Voir les logs
docker-compose -f docker/docker-compose.yml logs

# Accéder au shell Laravel
docker-compose -f docker/docker-compose.yml exec app bash

# Redémarrer un service
docker-compose -f docker/docker-compose.yml restart app
```

---

## 📊 Stack Technique

### Backend
- **Laravel 12** avec PHP 8.4
- **MySQL 8.0** pour la base de données
- **Redis 7** pour le cache et les sessions

### Frontend
- **React 19** avec Inertia.js
- **Tailwind CSS 4** pour le styling
- **Vite** pour le build system

### Outils de Développement
- **Docker Compose** pour l'environnement
- **Nginx** comme serveur web
- **MailHog** pour les tests d'emails

---

## 🌿 Workflow Git

### Structure des Branches
```
main                    → Production (VPS Docker)
develop                 → Recette/Staging (VPS Docker)
feature/nom-feature     → Développement (Docker local)
```

### Créer une Nouvelle Fonctionnalité
```bash
# Partir de develop
git checkout develop
git pull origin develop

# Créer une branche feature
git checkout -b feature/ma-nouvelle-fonctionnalite

# Développer avec Docker local
./scripts/docker-dev.sh start

# ... développement ...

# Tests
./scripts/docker-dev.sh test

# Commit et push
git add .
git commit -m "feat: add ma nouvelle fonctionnalité"
git push origin feature/ma-nouvelle-fonctionnalite

# Créer Pull Request vers develop sur GitHub
```

---

## 🔧 Dépannage

### Problèmes Courants

#### Port déjà utilisé
```bash
# Changer les ports dans docker-compose.yml si nécessaire
# Par exemple : "8001:80" au lieu de "8000:80"
```

#### Conteneur qui ne démarre pas
```bash
# Voir les logs détaillés
docker-compose -f docker/docker-compose.yml logs [nom-du-service]

# Forcer la reconstruction
docker-compose -f docker/docker-compose.yml up -d --build --force-recreate
```

#### Permissions sur les fichiers
```bash
# Réparer les permissions (macOS/Linux)
sudo chown -R $(whoami) storage bootstrap/cache
chmod -R 755 storage bootstrap/cache
```

#### Base de données non accessible
```bash
# Vérifier que MySQL est démarré
docker-compose -f docker/docker-compose.yml exec mysql mysql -u dev_user -p

# Reset complet de la base
docker-compose -f docker/docker-compose.yml down -v
docker-compose -f docker/docker-compose.yml up -d --build
```

---

## 🧪 Tests

### Lancer les Tests
```bash
# Tests PHP (PHPUnit)
./scripts/docker-dev.sh test

# OU directement
docker-compose -f docker/docker-compose.yml exec app php artisan test

# Tests avec couverture
docker-compose -f docker/docker-compose.yml exec app php artisan test --coverage
```

### Types de Tests
- **Feature Tests** : Tests d'intégration complets
- **Unit Tests** : Tests unitaires des classes
- **Browser Tests** : Tests end-to-end (Laravel Dusk)

---

## 👥 Équipe & Collaboration

### Développeurs
- **Lead Developer** : [Votre nom]
- **Developer** : Christophe

### Communication
- **Réunions** : Vendredis 14h
- **Chat** : WhatsApp équipe
- **Issues** : GitHub Issues
- **Project Management** : ClickUp (configuration en cours)

---

## 📂 Structure du Projet

```
mediation-app/
├── docker/                 # Configuration Docker
│   ├── Dockerfile          # Image PHP customisée
│   ├── docker-compose.yml  # Services (app, mysql, redis, etc.)
│   └── nginx/              # Configuration Nginx
├── scripts/                # Scripts utilitaires
│   └── docker-dev.sh       # Script de développement
├── app/                    # Code Laravel
├── resources/              # Vues, CSS, JS
│   └── js/                 # Composants React
├── database/               # Migrations, seeders
└── public/                 # Assets publics
```

---

## 🚀 Environnements

### Développement (Local)
- **Docker** : Environnement local isolé
- **Base** : MySQL locale avec seeders
- **Debug** : Activé, logs détaillés
- **Hot Reload** : React en mode développement

### Recette (VPS - À venir)
- **Docker** : Même configuration, variables différentes
- **Base** : MySQL partagée, données anonymisées
- **Tests** : Validation avant production
- **Demo** : Présentation client

### Production (VPS - Final)
- **Docker** : Configuration optimisée
- **Base** : MySQL sécurisée, données réelles
- **Performance** : Cache activé, assets optimisés
- **Monitoring** : Logs et métriques

---

## 📝 Notes Importantes

### ⚠️ Sécurité
- Ne jamais commiter le fichier `.env` (contient mots de passe)
- Utiliser `.env.example` pour partager la structure
- Changer les mots de passe par défaut en production

### 🔄 Mises à Jour
```bash
# Mettre à jour les dépendances
docker-compose -f docker/docker-compose.yml exec app composer update
docker-compose -f docker/docker-compose.yml exec app npm update

# Rebuilder après changement Dockerfile
docker-compose -f docker/docker-compose.yml up -d --build
```

### 📊 Base de Données
- **Migrations** : Toujours via `php artisan migrate`
- **Seeders** : Pour les données de test uniquement
- **Workbench** : Outil recommandé pour l'administration
