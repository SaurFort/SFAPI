# SFAPI <!-- omit in toc -->

This is the API used and created by SaurFort.

## Summary <!-- omit in toc -->

- [Usage available](#usage-available)
  - [Project](#project)
    - [Arguments](#arguments)
    - [Example Requests](#example-requests)
- [Error Codes](#error-codes)
  - [Code 30](#code-30)
  - [Code 90](#code-90)
  - [Code 91](#code-91)
- [Versions](#versions)

## Usage available

> [!IMPORTANT]\
> For all examples, we're gonna use the API like in local development `http://localhost/api`.

### Project

> [!NOTE]\
> Actually you can only get projects of SaurFort with some arguments.
> To access the project's API you need to request `project.php`.

#### Arguments

| Name | Description | Type | Default value |
| --- | --- | --- | --- |
| `lang` | Take translation of the project description. | string (en) | en |
| `sort` | Sort results by creation date. | latest/oldest | latest |
| `filtertype` | Permit to define the filter you want to use. | string | _null_ |
| `filter` | Permit to define the value of filter. | string | _null_ |
| `limit` | Limit the number of projects sorted. | int | -1 (all project) |

> [!INFO]\
> > For the moment, `lang` supports only English (en) and French (fr).
> `sort` accepts only two values: `latest` or `oldest`.
> `filtertype` accepts only two values: `id` or `name`.

> [!WARNING]\
> If `filter` is defined and `filtertype` is not provided, an error may occur.

#### Example Requests

- Gets French project, sorted by latest:

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

- Get the first 5 projects sorted by oldest:
  
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
> If there aren't enough rows for your limit, the API returns the max reachable.

- Filter by project's id:
  
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

- Filter by project's name:

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

## Error Codes

> [!IMPORTANT]\
> Since this API is not intended for use by other users, the error codes have been customized.

| Code | Description | Variation |
| --- | --- | --- |
| 30 | Invalid argument for project | A,B,C,D,E |
| 90 | SQL query error | _none_ |
| 91 | SQL query result is empty | _none_ |

### Code 30

This code indicates an invalid argument for the project part of the API.

- __30A__: Invalid argument for __`sort`__ (for example, a value other than `latest` or `oldest` is supplied).
- __30B__: Invalid argument for __`lang`__ (for example, a language other than `en` or `fr` is supplied).
- __30C__: Invalid filter type for __`filtertype`__ (for example, a value other than `id` or `name` is supplied).
- __30D__: The __`filter`__ is empty when `filtertype` is defined (for example, the `filter` parameter is absent or is an empty string when `filtertype` is specified).
- __30E__: __`filtertype`__ is set to `id` but the `filter` is not a valid ID (for example, `filter` is a non-numeric string whereas `filtertype` is `id`).

### Code 90

This code indicates a generic error in the execution of the SQL query. You need to check your logs to find the problem.

### Code 91

This code indicates that the SQL query executed successfully but returned no rows.

## Versions

> [!INFO]\
> List of all versions and whether they are still active or not.

> [!IMPORTANT]\
> At present, no versions are listed, as the API is not at a sufficiently advanced stage of development.
