# SFAPI `<!-- omit in toc -->`

Cette API est conçu pour centraliser le données de différentes application et par example simplifier les identifiants de compte en les rendants accessible à toutes les applications (dans la limite des permissions données).

## Sommaire `<!-- omit in toc -->`

- [Clé d&#39;API](#clé-dapi)
  - [Version 0 (v0)](#version-0-v0)
    - [Permissions](#permissions)
- [Utilisation disponible](#utilisation-disponible)
  - [Compte](#compte)
    - [Connexion](#connexion)
      - [Connexion - Arguments](#connexion---arguments)
      - [Connexion - Exemple de requêtes](#connexion---exemple-de-requêtes)
    - [Enregistrer](#enregistrer)
      - [Enregistrer - Arguments](#enregistrer---arguments)
      - [Enregistrer - Hash du mot de passe](#enregistrer---hash-du-mot-de-passe)
      - [Enregistrer - Exemple de requêtes](#enregistrer---exemple-de-requêtes)
  - [Projet](#projet)
    - [Lecture](#lecture)
      - [Arguments](#arguments)
      - [Exemple de requête](#exemple-de-requête)
- [Codes d&#39;erreurs](#codes-derreurs)
  - [Code 10](#code-10)
  - [Code 11](#code-11)
  - [Code 12](#code-12)
  - [Code 13](#code-13)
  - [Code 30](#code-30)
  - [Code 90](#code-90)
  - [Code 91](#code-91)
- [Versions](#versions)

## Clé d'API

> [!IMPORTANT]
> Les clés d'API peuvent être distinctes pour chaque version majeure de l'API et possèdent des permissions spécifiques définies en fonction des besoins. Chaque version majeure de l'API a son propre préfixe de clé, permettant ainsi une gestion plus précise et sécurisée des accès.

### Version 0 (v0)

- Préfixe de clé d'API : apiv0_
- Format complet des clés d'API pour la v0 : apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
  - Exemple : apiv0_123e4567-e89b-12d3-a456-426614174000
- Description : Les clés d'API de la version 0 commencent par le préfixe apiv0_. Chaque clé est suivie d'un UUID v4 unique, garantissant ainsi l'unicité et la sécurité des accès.

#### Permissions

> [!IMPORTANT]
> Il y a toute une liste de permissions pour l'API, si jamais une clé ne possède pas la permission, alors l'API ne voudra pas répondre à la demande. Vous pouvez vous référez au fichier [config.php](../src/config.php) pour la définition de toutes les permissions et de leur code.

- __[Projet](#projet)__ :| Fonction                            | Description                                                       | Code |
  | ----------------------------------- | ----------------------------------------------------------------- | ---- |
  | `REGISTER_USER`                   | Vous permez d'enregistrer de nouveaux utilisateurs sur le réseau | 0    |
  | `LOGIN_USER`                      | Vous permez de connecter les utilisateurs                         | 1    |
  | `PERMISSION_SEND_MAIL`            | Vous permez d'envoyer un mail avec l'adresse mail de l'API        | 2    |
  | `CREATE_PROJECTS`                 | Permet de créer de nouveaux projets.                             | 3    |
  | `READ_PROJECTS`                   | Permet de lire les informations des projets.                      | 4    |
  | `UPDATE_PROJECTS`                 | Permet de mettre à jour les informations des projets existants.  | 5    |
  | `DELETE_PROJECTS`                 | Permet de supprimer des projets existants.                        | 6    |
  | `PERMISSION_OTHER_USERS_PROJECTS` | Vous permez d'accéder aux projets des autres utilisateurs        | 7    |

## Utilisation disponible

> [!IMPORTANT]
> Pour tout les exemples nous allons utiliser l'API comme en développemen local `http://localhost/api`.

> [!WARNING]
> Pour toute interaction avec l'API, vous devrez mettre votre clé d'API !

| Category          | Action           | Method           |
| ----------------- | ---------------- | ---------------- |
| __Account__ | `login.php`    | __POST__   |
| __Account__ | `register.php` | __POST__   |
| __Mailer__  | `send.php`     | __PUT__    |
| __Project__ | `create.php`   | __POST__   |
| __Project__ | `read.php`     | __GET__    |
| __Project__ | `update.php`   | __PUT__    |
| __Project__ | `delete.php`   | __DELETE__ |

### Compte

> [!NOTE]
> Tout les comptes enregistrer dans l'API seront disponible sur toutes les applications utilisant l'API et si elles ont les perissions correspondante.
> Pour toutes les interactions avec l'API de compte, vous avez besoin de faire vos requête à l'url `http://localhost/api/account/`.

#### Connexion

> [!IMPORTANT]
> Pour connecter un utilisateur, vous avez besoin de faire une requête à l'url : `http://localhost/api/account/login.php?key=apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`.
>
> L'API de connexion prends uniquement les requêtes __POST__.
>
> L'API de connexion accepte uniquement les données json brut.

##### Connexion - Arguments

| Nom          | Description                   | Type   |
| ------------ | ----------------------------- | ------ |
| `username` | Nom de l'utilisateur          | string |
| `email`    | Adresse mail de l'utilisateur | string |
| `password` | Mot de passe de l'utilisateur | string |

> [!NOTE]
> Vous pouvez authentifier l'utilisateur avec son nom d'utilisateur ou son adresse mail ou les deux.

> [!WARNING]
> Vous avez besoin d'au moins un identifiant entre `username` et `email` pour authentifier l'utilisateur. Le champ `username` est sensible à la casse.

##### Connexion - Exemple de requêtes

- [Recommandé]:

  ```json
  {
    "email": "email@example.com",
    "password": "password"
  }
  ```
- [Deprécié]:

  ```json
  {
    "username": "John Doe",
    "password": "password"
  }
  ```
- [Les deux]:

  ```json
  {
    "username": "John Doe",
    "email": "email@example.com",
    "password": "password"
  }
  ```

#### Enregistrer

> [!IMPORTANT]
> Pour enregister un utilisateur, vous avez besoin de faire une requête à l'url : `http://localhost/api/account/register.php?key=apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`.
>
> L'API de création de compte prends uniquement les requêtes __POST__.
>
> L'API de création de compte accepte uniquement les données json brut.

##### Enregistrer - Arguments

| Nom                      | Description                                   | Type                 |
| ------------------------ | --------------------------------------------- | -------------------- |
| `username`             | Nom de l'utilisateur                          | string (VARCHAR(35)) |
| `email`                | Adresse mail de l'utilisateur                 | string (VARCHAR(55)) |
| `password`             | Mot de passe de l'utilisateur                 | string (TEXT)        |
| `confirmationPassword` | Confirmation du mot de passe de l'utilisateur | string (TEXT)        |
| `rank`                 | Rang de l'utilisateur                         | string (TEXT)        |

##### Enregistrer - Hash du mot de passe

> [!IMPORTANT]
> L'API utilise l'algorythme __Argon2id__, c'est un algorythme avec une excellente performance de sécurité contre le brute force.

> [!WARNING]
> Si vous essayez d'utiliser un autre algorythme, l'API refusera l'enregistrement du compte.
>
> Si vous essayez d'utiliser __Argon2id__ mais avec les mauvais paramètres, l'API refusera l'enregistrement.

| Nom du paramètre            | Valeur utilisé par l'API |
| ---------------------------- | ------------------------- |
| __Parallelism Factor__ | 1                         |
| __Memory Cost__        | 65536                     |
| __Iterations__         | 4                         |
| __Hashe length__       | 19                        |

> [!NOTE]
> L'API ne définie pas automatique le sel de l'algorythme pour le laisser en génération aléatoire pour une meilleur sécurité.
>
> Pour voir si votre algorythme est correctement configuré, vous avez juste besoin de vérifier si le hash commence exactement par `$argon2id$v=19$m=65536,t=4,p=1`, si c'est le cas, alors votre algorythme est correctement configuré.

##### Enregistrer - Exemple de requêtes

- Enregistrer un utilisateur sans le rang :

  ```json
  {
    "username": "John Doe",
    "email": "email@example.com",
    "password": "password",
    "confirmationPassword": "password"
  }
  ```
- Enregistrer un utilisateur avec le rang :

  ```json
  {
    "username": "John Doe",
    "email": "email@example.com",
    "password": "password",
    "confirmationPassword": "password",
    "rank": "admin"
  }
  ```

### Projet

> [!NOTE]
> Actuellement vous pouvez seulement lire les projets.
> Pour accéder à la partie projet de l'API vous avez besoin de faire des requêtes à `project.php`.

#### Lecture

Pour la lecture des projets, la base de votre requête sera la suivante : `http://localhost/api/project.php?key=apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx&action=read`.

Puis vous pourrez écrire les arguments que vous souhaitez pris en charge par la lecture de projet.

##### Arguments

| Name           | Description                                        | Type          | Default value    |
| -------------- | -------------------------------------------------- | ------------- | ---------------- |
| `lang`       | Prend la traduction de la description d'un projet. | string (en)   | en               |
| `sort`       | Tri les résultats par date de création.          | latest/oldest | latest           |
| `filtertype` | Permet de définir le filtre que vous souhaitez.   | string        | _null_         |
| `filter`     | Permet de filtrer par l'id ou le nom.              | string        | _null_         |
| `limit`      | Limite le nombre de résultat rendu par l'API.     | int           | -1 (all project) |

> [!NOTE]
> Pour le moment, __`lang`__ supporte uniquement l'Anglais (en) et le Français (fr).
> __`sort`__ accepte uniquement deux valeurs: `latest` ou `oldest`.
> __`filtertype`__ accepte uniquement deux valeurs: `id` ou `name`.

> [!WARNING]
> Si __`filter`__ est défini et que `filtertype` n'est pas définie, une erreur peut apparaître.

##### Exemple de requête

- Prenons les projets en français et trier du plus récent au plus ancien:

  ```http
  GET http://localhost/api/project.php?lang=fr&sort=latest
  ```

  ```json
  [
    {
      "id": 1,
      "name": "Project 1",
      "description": "Description du Projet 1",
      "technologies": "React, PHP, MySQL",
      "creation": "15/03/2022"
    },
    {
      "id": 2,
      "name": "Project 2",
      "description": "Description du Projet 2",
      "technologies": "Node.js, Express, MongoDB",
      "creation": "22/11/2021"
    }
  ]
  ```
- Prenons les 5 projets les plus anciens :

  ```http
  GET http://localhost/api/project.php?sort=oldest&limit=5
  ```

  ```json
  [
    {
      "id": 4,
      "name": "Project 4",
      "description": "Description of Project 4",
      "technologies": "GDScript",
      "creation": "16/08/2019"
    },
    {
      "id": 2,
      "name": "Project 2",
      "description": "Description of Project 2",
      "technologies": "Node.js, Express, MongoDB",
      "creation": "22/11/2021"
    },
    {
      "id": 1,
      "name": "Project 1",
      "description": "Description of Project 1",
      "technologies": "React, PHP, MySQL",
      "creation": "15/03/2022"
    },
    {
      "id": 3,
      "name": "Project 3",
      "description": "Description of Project 3",
      "technologies": "Python",
      "creation": "10/05/2024"
    }
  ]
  ```

> [!NOTE]
> S'il n'y a pas suffisamment de données pour atteindre la limite alors l'API va retourner le maximum possible.

- Filtrer les projets par leur id :

  ```http
  GET http://localhost/api/project.php?filtertype=id&filter=1
  ```

  ```json
  [
    {
      "id": 1,
      "name": "Project 1",
      "description": "Description of Project 1",
      "technologies": "React, PHP, MySQL",
      "creation": "15/03/2022"
    }
  ]
  ```
- Filtrer les projets par leur nom:

  ```http
  GET http://localhost/api/project.php?filtertype=name&filter=Project%202
  ```

  ```json
  [
    {
      "id": 2,
      "name": "Project 2",
      "description": "Description of Project 2",
      "technologies": "Node.js, Express, MongoDB",
      "creation": "22/11/2021"
    }
  ]
  ```

## Codes d'erreurs

> [!IMPORTANT]
> Puisque l'API n'a pas été conçu pour être utilisé par d'autre utilisateurs, les codes d'erreurs ont été personnalisé.

| Code | Description                                  | Variation  |
| ---- | -------------------------------------------- | ---------- |
| 10   | Clé d'API vide                              | _none_   |
| 11   | Clé d'API incompatible avec la version      | _none_   |
| 12   | La clé d'API n'a pas la permission demandé | _none_   |
| 13   | La clé d'API est incorrecte                 | _none_   |
| 30   | Argument invalide pour les projets           | A,B,C,D,E  |
| 90   | Erreur dans une requête SQL                 | _aucune_ |
| 91   | Le résultat de la requête SQL est vide     | _none_   |

### Code 10

Ce code indique que la clé d'API n'a pas été définie dans la requête.

### Code 11

Ce code indique que la clé d'API saisi n'est pas compatible avec la version actuel de l'API vous référez au [clé d&#39;API](#clé-dapi).

### Code 12

Ce code indique que la clé n'a pas les permissions nécessaire pour interargir avec la fonction demandé.
Essayez d'utiliser une autre clé d'API ou de contacter l'administrateur de l'API.

### Code 13

Ce code indique que la clé d'API est incorrecte, elle ne permet pas d'utiliser l'API ou du moins, cette version.
Si vous êtes sûr que la clé doit fonctionner, contacter l'administrateur de l'API.

### Code 30

Ce code indique un argument invalide pour la partie projet de l'API.

- __30A__ : Argument invalide pour __`sort`__ (par exemple, une valeur autre que `latest` ou `oldest` a été définie).
- __30B__ : Argument invalide pour __`lang`__ (par exemple, un langage autre que `en` ou `fr` a été définie).
- __30C__ : Type de filtre invalide pour __`filtertype`__ (par exemple, un filtre autre que `id` ou `name` a été définie).
- __30D__ : Le __`filter`__ est vide alors que le `filtertype` est définie (par exemple, l'argument `filter` est absent ou il est vide alors que `filtertype` est spécifié).
- __30E__ : __`filtertype`__ est définie sur `id` alors que le `filter` n'est pas une ID valide (par exemple, `filter` est une chaîne non numérique alors que `filtertype` est définie sur `id`).

### Code 90

Ce code indique une erreur générique lors de l'exécution de la requête SQL. Vous devez vérifier vos logs pour trouver le problème.

### Code 91

Ce code indique que la requête SQL a été exécuter avec succès mais qu'il n'y a aucun résultat.

## Versions

> [!NOTE]
> Liste de toutes les versions et de si elles sont toujours active ou non.

> [!IMPORTANT]
> Pour l'instant, aucune version n'est répertoriée, car l'API n'est pas à un stade de développement suffisamment avancé.
