# SaurFort's API <!-- omit in toc -->

Cette API est utilisé et a été créer par SaurFort

## Sommaire <!-- omit in toc -->

- [Clé d'API](#clé-dapi)
  - [Version 0 (v0)](#version-0-v0)
- [Utilisation disponible](#utilisation-disponible)
  - [Projet](#projet)
    - [Arguments](#arguments)
    - [Exemple de requête](#exemple-de-requête)
- [Codes d'erreurs](#codes-derreurs)
  - [Code 30](#code-30)
  - [Code 90](#code-90)
  - [Code 91](#code-91)
- [Versions](#versions)

## Clé d'API

> [!IMPORTANT]
> Les clés d'API sont distinctes pour chaque version majeure de l'API et possèdent des permissions spécifiques définies en fonction des besoins. Chaque version majeure de l'API a son propre préfixe de clé, permettant ainsi une gestion plus précise et sécurisée des accès.

### Version 0 (v0)

- Préfixe de clé d'API : apiv0_
- Format complet des clés d'API pour la v0 : apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
  - Exemple : apiv0_123e4567-e89b-12d3-a456-426614174000
- Description : Les clés d'API de la version 0 commencent par le préfixe apiv0_. Chaque clé est suivie d'un UUID v4 unique, garantissant ainsi l'unicité et la sécurité des accès.

## Utilisation disponible

> [!IMPORTANT]\
> Pour tout les exemples nous allons utiliser l'API comme en développemen local `http://localhost/api`.

### Projet

> [!NOTE]\
> Actuellement vous pouvez seulement récupérer les projets de SaurFort avec quelques arguments
> Pour accéder à la partie projet de l'API vous avez besoin de faire des requêtes à `project.php`.

#### Arguments

| Name | Description | Type | Default value |
| --- | --- | --- | --- |
| `lang` | Prend la traduction de la description d'un projet. | string (en) | en |
| `sort` | Tri les résultats par date de création. | latest/oldest | latest |
| `filtertype` | Permet de définir le filtre que vous souhaitez. | string | _null_ |
| `filter` | Permet de filtrer par l'id ou le nom. | string | _null_ |
| `limit` | Limite le nombre de résultat rendu par l'API. | int | -1 (all project) |

> [!NOTE]\
> > Pour le moment, __`lang`__ supporte uniquement l'Anglais (en) et le Français (fr).
> __`sort`__ accepte uniquement deux valeurs: `latest` ou `oldest`.
> __`filtertype`__ accepte uniquement deux valeurs: `id` ou `name`.

> [!WARNING]\
> Si __`filter`__ est défini et que `filtertype` n'est pas définie, une erreur peut apparaître.

#### Exemple de requête

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

> [!NOTE]\
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

> [!IMPORTANT]\
> Puisque l'API n'a pas été conçu pour être utilisé par d'autre utilisateurs, les codes d'erreurs ont été personnalisé.

| Code | Description | Variation |
| --- | --- | --- |
| 30 | Argument invalide pour les projets | A,B,C,D,E |
| 90 | Erreur dans une requête SQL | _aucune_ |
| 91 | Le résultat de la requête SQL est vide  | _none_ |

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

> [!NOTE]\
> Liste de toutes les versions et de si elles sont toujours active ou non.

> [!IMPORTANT]\
> Pour l'instant, aucune version n'est répertoriée, car l'API n'est pas à un stade de développement suffisamment avancé.
