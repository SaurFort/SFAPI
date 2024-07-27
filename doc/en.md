# SFAPI <!-- omit in toc -->

This is the API used and created by SaurFort.

## Summary <!-- omit in toc -->

- [API key](#api-key)
  - [Version 0 (v0)](#version-0-v0)
    - [Permissions](#permissions)
- [Usage available](#usage-available)
  - [Project](#project)
    - [Read](#read)
      - [Arguments](#arguments)
      - [Example Requests](#example-requests)
- [Error Codes](#error-codes)
  - [Code 10](#code-10)
  - [Code 11](#code-11)
  - [Code 12](#code-12)
  - [Code 13](#code-13)
  - [Code 30](#code-30)
  - [Code 90](#code-90)
  - [Code 91](#code-91)
- [Versions](#versions)

## API key

> [!IMPORTANT]\
> API keys are distinct for each major API version and have specific permissions defined according to requirements. Each major API version has its own key prefix, enabling more precise and secure access management.

### Version 0 (v0)

- API key prefix: apiv0_
- Complete API key format for v0: apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
  - Example: apiv0_123e4567-e89b-12d3-a456-426614174000
- Description: Version 0 API keys begin with the prefix apiv0_. Each key is followed by a unique v4 UUID, guaranteeing unique and secure access.

#### Permissions

> [!IMPORTANT]\
> There's a whole list of permissions for the API, so if a key doesn't have the permission, the API won't respond to the request.

- __[Project](#project)__ :
  | Function | Description | Method | Code |
  | --- | --- | --- | --- |
  | `CREATE_PROJECTS` | Create new projects | __POST__ | 0 |
  | `UPDATE_PROJECTS` | Allows you to update information on existing projects. | __PUT__ | 1 |
  | `DELETE_PROJECTS` | Delete existing projects. | __DELETE__ | 2 |
  | `READ_PROJECTS`| Allows you to read project information. | __GET__ | 3 |

## Usage available

> [!IMPORTANT]\
> For all examples, we're gonna use the API like in local development `http://localhost/api`.

> [!WARNING]\
> For any interaction with the API, you'll need to enter your API key!

### Project

> [!NOTE]\
> Actually you can only get projects of SaurFort with some arguments.
> To access the project's API you need to request `project.php`.

#### Read

For reading projects, the basis of your query will be: `http://localhost/api/project.php?key=apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx&action=read`.

Then you can write the arguments you want the project to read.

##### Arguments

| Name | Description | Type | Default value |
| --- | --- | --- | --- |
| `lang` | Take translation of the project description. | string (en) | en |
| `sort` | Sort results by creation date. | latest/oldest | latest |
| `filtertype` | Permit to define the filter you want to use. | string | _null_ |
| `filter` | Permit to define the value of filter. | string | _null_ |
| `limit` | Limit the number of projects sorted. | int | -1 (all project) |

> [!NOTE]\
> For the moment, `lang` supports only English (en) and French (fr).
> `sort` accepts only two values: `latest` or `oldest`.
> `filtertype` accepts only two values: `id` or `name`.

> [!WARNING]\
> If `filter` is defined and `filtertype` is not provided, an error may occur.

##### Example Requests

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
| 10 | Empty API key | _none_ |
| 11 | API key incompatible with version | _none_ |
| 12 | API key does not have requested permission | _none_ |
| 13 | Incorrect API key | _none_ |
| 30 | Invalid argument for project | A,B,C,D,E |
| 90 | SQL query error | _none_ |
| 91 | SQL query result is empty | _none_ |

### Code 10

This code indicates that the API key has not been defined in the request.

### Code 11

This code indicates that the API key entered is not compatible with the current version of the API. Please refer to [API key](#api-key).

### Code 12

This code indicates that the key does not have the necessary permissions to interface with the requested function.
Try using another API key or contact the API administrator.

### Code 13

This code indicates that the API key is incorrect, and will not allow you to use the API, or at least this version of it.
If you are sure the key should work, contact the API administrator.

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

> [!NOTE]\
> List of all versions and whether they are still active or not.

> [!IMPORTANT]\
> At present, no versions are listed, as the API is not at a sufficiently advanced stage of development.
