# Technical Workflow Documentation

This document provides a detailed explanation of the internal architecture, core workflows, and key logic of the Tinder Clone backend application.

## 1. Project Structure

-   **`app/Http/Controllers`**: Contains all the controllers that handle incoming HTTP requests.
-   **`app/Models`**: Defines the Eloquent models for interacting with the database.
-   **`routes/api.php`**: The entry point for all API routes.
-   **`database/migrations`**: Contains all database schema definitions.
-   **`database/seeders`**: Holds the seeders for populating the database.
-   **`config/filesystems.php`**: Configuration for file storage, including the Google Cloud Storage disk.

## 2. Core Workflows

### 2.1. User Authentication

-   **Registration**: A new user is created via the `/api/register` endpoint. The password is encrypted using bcrypt.
-   **Login**: A user logs in via `/api/login`. Upon successful authentication, a Sanctum API token is generated and returned to the client.
-   **Authorization**: All protected routes are guarded by the `auth:sanctum` middleware, which validates the `Bearer` token from the `Authorization` header.

### 2.2. User Recommendation Logic

-   The recommendation engine, found in the `UserController`, fetches users who the current authenticated user has not yet interacted with (liked or disliked).
-   *(You can add more details here about your specific recommendation algorithm, e.g., filtering by location, age, etc.)*

### 2.3. Swiping Mechanism (Like/Dislike)

-   When a user performs a `like` or `dislike` action via `/api/users/{id}/action`, a new record is created in the `likes` or `dislikes` table, respectively.
-   This action prevents the swiped user from appearing in the authenticated user's recommendations again.

### 2.4. Image Upload to Google Cloud Storage

-   The `UserPictureSeeder` demonstrates how to upload files to GCS.
-   It uses `Storage::disk('gcs')->put($path, $contents)` to store the file.
-   The public URL is then retrieved using `Storage::disk('gcs')->url($path)` and saved to the `user_pictures` table.

## 3. Database Schema

-   **`users`**: Stores user information (name, email, password).
-   **`profiles`**: Contains additional user profile details.
-   **`user_pictures`**: Stores URLs to user images on GCS, linked to the `users` table.
-   **`likes`**: Records a `like` action from one user to another.
-   **`dislikes`**: Records a `dislike` action.
-   **`personal_access_tokens`**: Managed by Laravel Sanctum for API tokens.

---

*This document should be expanded as the project evolves.*