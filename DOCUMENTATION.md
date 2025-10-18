# API Documentation

This document provides detailed information about the API endpoints for the Tinder Clone backend. For auto-generated, interactive documentation, you can run `php artisan scribe:generate`.

---

## Base URL

`http://127.0.0.1:8000/api`

## Authentication

All protected endpoints require a `Bearer` token to be included in the `Authorization` header.

`Authorization: Bearer <YOUR_AUTH_TOKEN>`

---

## Endpoints

### 1. Authentication

#### **POST** `/register`

Registers a new user.

-   **Request Body**:
    -   `name` (string, required)
    -   `email` (string, required, unique)
    -   `password` (string, required, min: 8)
    -   `password_confirmation` (string, required)

-   **Success Response (201)**:
    ```json
    {
        "message": "User registered successfully"
    }
    ```

#### **POST** `/login`

Logs in a user and returns an API token.

-   **Request Body**:
    -   `email` (string, required)
    -   `password` (string, required)

-   **Success Response (200)**:
    ```json
    {
        "token": "<your-api-token>"
    }
    ```

### 2. User Actions

#### **GET** `/users/recommended`

_Authentication required._

Fetches a list of recommended users for the authenticated user.

-   **Success Response (200)**:
    ```json
    [
        {
            "id": 2,
            "name": "Jane Doe",
            "pictures": [
                {
                    "url": "http://..."
                }
            ]
        }
    ]
    ```

#### **GET** `/users/{id}/action`

_Authentication required._

Performs a `like` or `dislike` action on a user.

-   **URL Parameters**:
    -   `id` (integer, required): The ID of the user to action.
-   **Query Parameters**:
    -   `action` (string, required): Must be either `like` or `dislike`.

-   **Success Response (200)**:
    ```json
    {
        "message": "Action successful"
    }
    ```

---

*This documentation should be kept in sync with any API changes.*