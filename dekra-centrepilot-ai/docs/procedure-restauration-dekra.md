# Procédure de restauration — DEKRA CentrePilot AI

Ce document explique comment restaurer les données de la plateforme en cas de perte, de corruption de la base de données, ou d'erreur grave.

## 1. Où se trouvent les sauvegardes

Chaque sauvegarde automatique est stockée dans un dossier daté, sous :

```
storage/app/backups/AAAA-MM-JJ_HH-MM-SS/
```

Chaque dossier contient :
- `database.sql` — un export complet de la base de données MySQL au moment de la sauvegarde.
- `documents/` — une copie de tous les fichiers archivés (factures PDF, images scannées, titres de propriété, etc.).

Les sauvegardes sont générées automatiquement chaque jour à 2h du matin (commande `backup:run`, planifiée dans `routes/console.php`). Seules les **10 dernières** sauvegardes sont conservées ; les plus anciennes sont supprimées automatiquement.

La liste des sauvegardes disponibles, avec leur taille et leur statut, est consultable dans l'application via le menu **Sauvegardes**.

## 2. Quand restaurer

Cette procédure doit être utilisée dans les cas suivants :
- La base de données est corrompue ou inaccessible.
- Des données ont été supprimées par erreur et ne sont plus récupérables via la **Corbeille** de l'application.
- Le serveur a été réinstallé et la base doit être reconstituée à partir d'une sauvegarde.

⚠️ Avant toute restauration, vérifier d'abord si les données recherchées ne sont pas simplement dans la **Corbeille** (menu de l'application) — une restauration depuis la corbeille est plus rapide et ne nécessite pas d'intervention technique.

## 3. Restaurer la base de données

### Étape 1 — Identifier la bonne sauvegarde

Aller dans `storage/app/backups/`, choisir le dossier daté le plus pertinent (généralement le plus récent avant l'incident).

### Étape 2 — Vider la base actuelle (si nécessaire)

⚠️ Cette étape écrase les données actuelles de la base. À faire uniquement si la base est corrompue ou doit être reconstruite entièrement.

Depuis phpMyAdmin (fourni avec XAMPP) ou en ligne de commande :

```bash
mysql -h 127.0.0.1 -P 3307 -u root -e "DROP DATABASE dekra_centrepilot_ai; CREATE DATABASE dekra_centrepilot_ai;"
```

### Étape 3 — Réimporter la sauvegarde

```bash
mysql -h 127.0.0.1 -P 3307 -u root dekra_centrepilot_ai < storage/app/backups/2026-07-12_15-50-21/database.sql
```

(remplacer le chemin par le dossier de sauvegarde choisi à l'étape 1)

Sous Windows, avec XAMPP, remplacer `mysql` par le chemin complet si nécessaire :
```
C:\xampp\mysql\bin\mysql.exe -h 127.0.0.1 -P 3307 -u root dekra_centrepilot_ai < storage/app/backups/2026-07-12_15-50-21/database.sql
```

### Étape 4 — Vérifier

Se connecter à l'application (`/dashboard`) et vérifier que les factures, rappels, documents, etc. sont bien revenus.

## 4. Restaurer les documents archivés

### Étape 1 — Localiser le dossier de documents à restaurer

Dans `storage/app/backups/AAAA-MM-JJ_HH-MM-SS/documents/`.

### Étape 2 — Copier ce dossier vers l'emplacement actif

Copier tout le contenu de ce dossier `documents/` vers :
```
storage/app/public/
```

en écrasant les fichiers existants si nécessaire (ou en renommant l'ancien dossier `public` en `public_old` par précaution avant de copier, pour pouvoir comparer ensuite).

### Étape 3 — Vérifier le lien symbolique de stockage

Si les fichiers ne s'affichent pas dans l'application après restauration, relancer :
```bash
php artisan storage:link
```

## 5. Restauration partielle (un seul élément)

Si un seul enregistrement doit être récupéré (une facture, un document précis) plutôt que toute la base :

1. Ouvrir le fichier `database.sql` de la sauvegarde concernée avec un éditeur de texte.
2. Rechercher la ligne `INSERT INTO` correspondant à la table concernée (`invoices`, `documents`, `rappels`, etc.).
3. Identifier la ligne correspondant à l'enregistrement recherché.
4. Réinsérer manuellement cette ligne dans la base actuelle via phpMyAdmin ou une requête SQL ciblée, en adaptant si besoin les identifiants pour éviter les doublons.

Cette méthode demande de la prudence et doit être réservée à un utilisateur à l'aise avec SQL.

## 6. Test régulier de la procédure

Il est recommandé de tester cette procédure de restauration **au moins une fois par trimestre**, sur un environnement de test (jamais directement sur la base de production), afin de s'assurer que :
- Les sauvegardes sont bien exploitables (pas de fichier `.sql` corrompu ou vide).
- La procédure décrite ici reste à jour avec la version actuelle de l'application.

## 7. Contact en cas de difficulté

En cas de doute ou de difficulté lors d'une restauration, contacter le développeur du projet ou l'encadrant technique avant toute manipulation destructive (suppression de base, écrasement de fichiers).
