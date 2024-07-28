# SFAPI <!-- omit in toc -->

This is the API used and created by SaurFort.

## Summary <!-- omit in toc -->

- [API key](#api-key)
  - [Version 0 (v0)](#version-0-v0)
    - [Permissions](#permissions)
- [Usage available](#usage-available)
  - [Account](#account)
    - [Login](#login)
      - [Login - Arguments](#login---arguments)
      - [Login - Example Requests](#login---example-requests)
    - [Register](#register)
      - [Register - Arguments](#register---arguments)
      - [Register - Password Hash](#register---password-hash)
      - [Register - Example Requests](#register---example-requests)
  - [Project](#project)
    - [Create](#create)
      - [Create - Arguments](#create---arguments)
      - [Create - Example Requests](#create---example-requests)
    - [Update](#update)
      - [Update - Arguments](#update---arguments)
      - [Update - Example Request](#update---example-request)
    - [Delete](#delete)
      - [Delete - Arguments](#delete---arguments)
      - [Delete - Example Requests](#delete---example-requests)
    - [Read](#read)
      - [Read - Arguments](#read---arguments)
      - [Read - Example Requests](#read---example-requests)
- [API Codes](#api-codes)
  - [Code 01](#code-01)
  - [Code 02](#code-02)
  - [Code 10](#code-10)
  - [Code 11](#code-11)
  - [Code 12](#code-12)
  - [Code 13](#code-13)
  - [Code 14](#code-14)
  - [Code 15](#code-15)
  - [Code 16](#code-16)
  - [Code 17](#code-17)
  - [Code 18](#code-18)
  - [Code 30](#code-30)
  - [Code 31](#code-31)
  - [Code 32](#code-32)
  - [Code 33](#code-33)
  - [Code 34](#code-34)
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
> You can refer to the [config file](../src/config.php) for all permissions and permissions code definitions.

- __[Project](#project)__ :
  | Function | Description | Method | Code |
  | --- | --- | --- | --- |
  | `REGISTER_USER` | Allows you to register new users on the network | __POST__ | 0 |
  | `LOGIN_USER` | Allow you to login user | __POST__ | 1 |
  | `CREATE_PROJECTS` | Create new projects | __POST__ | 2 |
  | `UPDATE_PROJECTS` | Allows you to update information on existing projects. | __PUT__ | 3 |
  | `DELETE_PROJECTS` | Delete existing projects. | __DELETE__ | 4 |
  | `READ_PROJECTS`| Allows you to read project information. | __GET__ | 5 |

## Usage available

> [!IMPORTANT]\
> For all examples, we're gonna use the API like in local development `http://localhost/api`.
> [!WARNING]\
> For any interaction with the API, you'll need to enter your API key!

### Account

> [!NOTE]\
> All accounts registered in the API will be available on application using the API and if there have the corresponding permissions.
> For all interaction with the Account API, you need to make your query to `http://localhost/api/account/`.

#### Login

> [!IMPORTANT]\
> For login an user, you need to make your query at: `http://localhost/api/account/login.php?key=apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`.
> Login API only takes __POST__ query.
> Login API accept only raw json data.

##### Login - Arguments

| Name | Description | Type |
| --- | --- | --- |
| `username` | Username of the user | string |
| `email` | Email address of the user | string |
| `password` | Password of the user | string |

> [!NOTE]\
> You can authenticate the user with his username or email address or the both.
> [!WARNING]\
> You need at least one identifier between `username` and `email` to authenticate the user.
> The `username` field is case sensitive.
> The `password` field is necessary, the API won't accept an empty password.

##### Login - Example Requests

- Recommended login query body:

  ```json
  {
    "email": "email@example.com",
    "password": "password"
  }
  ```

  - Depreciated login query body:

  ```json
  {
    "username": "John Doe",
    "password": "password"
  }
  ```

  - Best login query body:

  ```json
  {
    "username": "John Doe",
    "email": "email@example.com",
    "password": "password"
  }
  ```

#### Register

> [!IMPORTANT]\
> For register an user, you need to make your query at: `http://localhost/api/account/register.php?key=apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx`.
> Register API only takes __POST__ query.
> Register API accept only raw json data.

##### Register - Arguments

| Name | Description | Type |
| --- | --- | --- |
| `username` | Username of the user | string (VARCHAR(35)) |
| `email` | Email address of the user | string (VARCHAR(55)) |
| `password` | Password of the user | string (TEXT) |
| `confirmationPassword` | Password confirmation of the user | string (TEXT) |
| `rank` | Rank of the user | string (TEXT) |

> [!IMPORTANT]\
> `password` field store the password destinated to be hashed so you can hash it before giving to the API but it's very important to respect exactly the [algorithm and parameters](#register---password-hash) used by the API.
> [!WARNING]\
> `username`, `email`, `password` and `confirmationPassword` fields are necessary in the query body.
> [!NOTE]\
> `rank` field is not required because if it's not defined the user will obtain the rank __user__. Rank don't have determined permissions by the API because it's at application using the API to define their permissions.

##### Register - Password Hash

> [!IMPORTANT]\
> The API used the algorithm __Argon2id__, it's a recent algorithm with excellent security performance againt brute-force.
> [!WARNING]\
> If you trying to used an other algorithm, the API will refuse your registration.
> If you trying to used __Argon2id__ but not with the same parameters of the API, the API will refuse your registration.

| Parameter name | API used value |
| --- | --- |
| __Parallelism Factor__ | `1` |
| __Memory Cost__ | `65536` |
| __Iterations__ | `4` |
| __Hash Length__ | `19` |

> [!NOTE]\
> The API don't set manually the salt of the algorithm because it's generated randomly for a better security.
> To see if your algorithm is correctly configured you just need to check the start of your hashed password and if it's exactly the same at `$argon2id$v=19$m=65536,t=4,p=1` then your algorithm is correctly configured.

##### Register - Example Requests

- Register a user without rank:

  ```json
  {
    "username": "John Doe",
    "email": "email@example.com",
    "password": "password",
    "confirmationPassword": "password"
  }
  ```

  - Register a user with rank:

  ```json
  {
    "username": "John Doe",
    "email": "email@example.com",
    "password": "password",
    "confirmationPassword": "password",
    "rank": "admin"
  }
  ```

### Project

> [!NOTE]\
> Actually you can only get projects of SaurFort with some arguments.
> To access the project's API you need to request `project.php`.

#### Create

> [!IMPORTANT]\
> For create projects, the basis of your query will be: `http://localhost/api/project.php?key=apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx&action=create`.
> Don't forgot to make your query with the method __POST__.
> [!WARNING]\
> API only accept raw json for create query.

##### Create - Arguments

| Name | Description | Type |
| --- | --- | --- |
| __`name`__ | Set the project's name | string _(VARCHAR(30)_) |
| __`technologies`__ | Set technologies used for project | string _(TEXT)_ |
| `creation` | Set the creation date of the project | string (DATE) |
| __`description-en`__ | Set the English description of the project | string _(TEXT)_ |
| `description-fr` | Set the French description of the project | string _(TEXT)_ |

> [!IMPORTANT]\
> Bold arguments are necessary for any create query.
> `creation` field is under the format: `Y-m-d`, if you don't set a value for it, the date of day will be taken.

##### Create - Example Requests

```json
{
  "name": "Project Alpha",
  "technologies": "PHP, MySQL, JavaScript",
  "description-en": "This is a project description in English.",
}
```

```json
{
  "name": "Project Alpha",
  "technologies": "PHP, MySQL, JavaScript",
  "creation": "2024-07-27",
  "description-en": "This is a project description in English.",
  "description-fr": "Ceci est une description du projet en français."
}
```

#### Update

> [!IMPORTANT]\
> For update projects, the basis of your query will be: `http://localhost/api/project.php?key=apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx&action=update`.
> Don't forgot to make your query with the method __PUT__.
> [!WARNING]\
> API only accept raw json for update query.

##### Update - Arguments

| Name | Description | Type |
| --- | --- | --- |
| __`id`__ | Find the corresponding project and translations | int (INT) |
| `name` | Set the project's name | string _(VARCHAR(30)_) |
| `technologies` | Set technologies used for project | string _(TEXT)_ |
| `creation` | Set the creation date of the project | string (DATE) |
| `description-en` | Set the English description of the project | string _(TEXT)_ |
| `description-fr` | Set the French description of the project | string _(TEXT)_ |

> [!IMPORTANT]\
> Bold argument is necessary for any update query.
> You're not forced to put all data just the data you want to be updated.

##### Update - Example Request

```json
{
  "id": 1,
  "description-fr": "Description du projet mise à jour en français."
}
```

```json
{
  "id": 1,
  "name": "Updated Project Alpha",
  "technologies": "PHP, MySQL, JavaScript, Node.js",
  "description-en": "Updated project description in English.",
  "description-fr": "Description du projet mise à jour en français."
}
```

#### Delete

> [!IMPORTANT]\
> For delete projects, the basis of your query will be: `http://localhost/api/project.php?key=apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx&action=delete`.
> Don't forgot to make your query with the method __DELETE__.
> [!WARNING]\
> API only accept raw json for delete query.

##### Delete - Arguments

| Name | Description | Type |
| --- | --- | --- |
| __`id`__ | Find the corresponding project and translations | int (INT) |

> [!WARNING]\
> If the `id` is not defined the API can't delete anything
> [!IMPORTANT]\
> When you delete a project, everything is deleted, project and project's translations.

##### Delete - Example Requests

```json
{
  "id": 1
}
```

#### Read

> [!IMPORTANT]\
> For reading projects, the basis of your query will be: `http://localhost/api/project.php?key=apiv0_xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx&action=read`.
> Don't forgot to make your query with the method __GET__.
> [!NOTE]\
> Reading a project doesn't require any arguments to be accessible.

##### Read - Arguments

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

##### Read - Example Requests

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

## API Codes

> [!IMPORTANT]\
> All of this code has been maded to be more informative.

| Code | Description | Variation |
| --- | --- | --- |
| 01 | Query has been complete successfully | _none_ |
| 02 | Invalid method | _none_ |
| 10 | Empty API key | _none_ |
| 11 | API key incompatible with version | _none_ |
| 12 | API key does not have requested permission | _none_ |
| 13 | Incorrect API key | _none_ |
| 14 | Invalid argument for register | A,B,C,D |
| 15 | Invalid password for register | A,B,C,D |
| 16 | Account already registered | _none_ |
| 17 | Invalid argument for login | A,B |
| 18 | Login account failed | _none_ |
| 30 | Invalid action for project | _none_ |
| 31 | Invalid argument for create project action | _none_ |
| 32 | Invalid argument for update project action | _none_ |
| 33 | Invalid argument for delete project action | _none_ |
| 34 | Invalid argument for read project action | A,B,C,D,E |
| 90 | SQL query error | _none_ |
| 91 | SQL query result is empty | _none_ |
| 92 | Error when preparing SQL query | _none_ |

### Code 01

This code indicates that the query has been completed successfully by the API.

### Code 02

This codes indicates an invalid method when calling the API. To be sure you can [refer to permissions](#permissions) where all methods are refered, else you can check the corresponding section.

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

### Code 14

This code indicates an invalid argument for registration.

- __14A__: Argument `username` is not defined.
- __14B__: Argument `email` is not defined.
- __14C__: Argument `password` is not defined.
- __14D__: Argument `confirmationPassword` is not defined.

### Code 15

This code indicates an invalid password for registration.

- __15A__: Argument `password` is already hashed but the algorithm is not supported by the API. (For example, you have used Bcrypt or Argon2i or any other algorithm).
- __15B__: Argument `password` used the correct algorithm but not the right parameters. (For example, you have make an error when you've done the parameters of algorithm).
- __15C__: Arguments `password` and `confirmationPassword` are not the same. (For example, you've used an algorithm that haven't been referenced in not supported algorithms).
- __15D__: Argument `password` was not hashed correctly by the API. Just retry and if the problem persists contact you API administrator.
  
### Code 16

This code indicates that the username or email address is already used on an other account.

### Code 17

This code indicates an invalid argument for login.

- __17A__: Arguments `username` and `email` haven't been defined but you need at least one of both, [refer to login example](#login---example-requests).
- __17B__: Argument `password` is not defined.

### Code 18

This code is a generic code to say that the credentials are not valid but for security the API will never return what in credentials is not valid.

### Code 30

This code indicates that the action you have attempted is not a valid action for the project.

### Code 31

This code indicates an invalid argument for the create project's action.

### Code 32

This code indicates an invalid argument for the update project's action.

### Code 33

This code indicates an invalid argument for the delete project's action.

### Code 34

This code indicates an invalid argument for the read project's action.

- __34A__: Invalid argument for __`sort`__ (for example, a value other than `latest` or `oldest` is supplied).
- __34B__: Invalid argument for __`lang`__ (for example, a language other than `en` or `fr` is supplied).
- __34C__: Invalid filter type for __`filtertype`__ (for example, a value other than `id` or `name` is supplied).
- __34D__: The __`filter`__ is empty when `filtertype` is defined (for example, the `filter` parameter is absent or is an empty string when `filtertype` is specified).
- __34E__: __`filtertype`__ is set to `id` but the `filter` is not a valid ID (for example, `filter` is a non-numeric string whereas `filtertype` is `id`).

### Code 90

This code indicates a generic error in the execution of the SQL query. You need to check your logs to find the problem.

### Code 91

This code indicates that the SQL query executed successfully but returned no rows.

## Versions

> [!NOTE]\
> List of all versions and whether they are still active or not.
> Early development versions are listed but are not hosted on a public server.

| Version | Version Type | Status |
| --- | --- | --- |
| v0.6.0 | `early development` | __private__ |
