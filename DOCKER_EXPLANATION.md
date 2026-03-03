# Comprendre Docker dans ce projet

Ce document explique les concepts fondamentaux de Docker (conteneurs, réseaux, volumes) en s'appuyant sur la configuration de ce projet.

## 1. Les Conteneurs (Containers)

Un conteneur est une unité logicielle standard qui emballe le code et toutes ses dépendances afin que l'application s'exécute rapidement et de manière fiable d'un environnement informatique à un autre.

Dans ce projet, nous avons deux conteneurs :
- **`expenses_php_container` (Service `app`)** : Contient l'application PHP et le serveur web Apache. Il est construit à partir du `Dockerfile`.
- **`expenses_db_container` (Service `db`)** : Contient la base de données MariaDB.

### Le Dockerfile
C'est la "recette" pour construire l'image du conteneur `app`. Il définit :
- L'image de base (`php:8.2-apache`).
- Les extensions nécessaires (`pdo_mysql`).
- La configuration du serveur (activation de `mod_rewrite`).
- La copie du code source dans le conteneur.

## 2. Le Réseau (Networking)

Docker permet aux conteneurs de communiquer entre eux de manière isolée.

### Le Réseau Bridge (`expenses_network`)
Dans le fichier `docker-compose.yml`, nous avons défini un réseau appelé `expenses_network`.
- **Isolation** : Seuls les conteneurs connectés à ce réseau peuvent se voir.
- **Résolution DNS interne** : C'est l'aspect le plus important. Docker fournit un serveur DNS interne qui permet aux conteneurs de s'appeler par leur **nom de service**.

**Exemple concret :**
Dans la configuration PHP (`app`), nous utilisons `DB_HOST: db`. L'application ne cherche pas une adresse IP variable, mais utilise le nom du service défini dans Docker Compose. Docker traduit automatiquement `db` vers l'IP interne du conteneur MariaDB.

## 3. Les Volumes (Persistence)

Par défaut, les données à l'intérieur d'un conteneur sont éphémères : si le conteneur est supprimé, les données sont perdues. Les volumes permettent de persister les données sur la machine hôte.

Nous utilisons deux types de montages :
- **Volume nommé (`db_data`)** : Utilisé pour `/var/lib/mysql`. Docker gère un espace sur votre disque dur où les données de la base de données sont stockées de manière permanente, même si vous recréez le conteneur.
- **Bind Mount (`./init.sql`)** : On lie directement un fichier de notre dossier local vers le conteneur pour initialiser la base de données au démarrage.

## 4. Exposition des Ports

Le conteneur Apache écoute sur le port `80` (standard HTTP). Cependant, pour y accéder depuis votre navigateur sur Windows, nous faisons un mappage :
- `8080:80` signifie : "Transfère tout le trafic arrivant sur le port **8080** de mon ordinateur vers le port **80** du conteneur".
- URL d'accès : `http://localhost:8080`

## Résumé du flux
1. L'utilisateur accède à `localhost:8080`.
2. Le trafic est envoyé au conteneur `app` sur le port `80`.
3. Le code PHP s'exécute et tente de se connecter à la base de données.
4. PHP utilise le nom d'hôte `db`.
5. Le réseau Docker redirige la requête vers le conteneur `db`.
6. Le conteneur `db` lit/écrit les données dans le volume `db_data`.
