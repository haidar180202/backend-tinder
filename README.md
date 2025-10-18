# Tinder Clone - Backend Service

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

This repository contains the backend service for a Tinder-clone application. Built with the Laravel framework, it provides a robust, scalable, and secure RESTful API to handle all core functionalities, including user management, authentication, profile matching, and real-time interactions.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture & Documentation](#architecture--documentation)
- [Prerequisites](#prerequisites)
- [Installation and Setup](#installation-and-setup)
- [Running the Application](#running-the-application)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)

---

## Features

- **Secure User Authentication**: JWT-based authentication using Laravel Sanctum for secure user registration and login.
- **Advanced User Recommendations**: A sophisticated recommendation engine to suggest potential matches based on user data and preferences.
- **Swiping Mechanics**: Core like/dislike functionality to enable user interaction.
- **Profile Management**: Endpoints for users to create, update, and manage their profiles.
- **Cloud-Based Image Uploads**: Seamlessly handles profile picture uploads to Google Cloud Storage, ensuring scalability and performance.
- **Database Seeding**: Includes comprehensive seeders to populate the database with dummy users and profile pictures for easy testing and development.
- **Structured API**: A clean, well-documented, and RESTful API design.

## Tech Stack

This project leverages a modern tech stack to ensure efficiency and scalability:

- **Backend Framework**: [Laravel 11](https://laravel.com/)
- **Language**: PHP 8.2
- **Database**: SQLite for local development. Easily configurable for MySQL or PostgreSQL.
- **Authentication**: [Laravel Sanctum](https://laravel.com/docs/sanctum) (Token-based)
- **API Documentation**: [Scribe](https://scribe.knuckles.wtf/laravel) (auto-generated)
- **File Storage**: [Google Cloud Storage (GCS)](https://cloud.google.com/storage)
- **HTTP Client**: [Guzzle](https://github.com/guzzle/guzzle)
- **Development Environment**: [Laravel Sail](https://laravel.com/docs/sail) (Docker-based)

## Architecture & Documentation

This project follows standard Laravel conventions and best practices. For a deeper understanding of the internal workflows, database schema, and core logic, please refer to our technical documentation.

- **[Technical Workflow Documentation](./TECHNICAL.md)**

For detailed information about all available API endpoints, request parameters, and example responses, please consult our API documentation.

- **[API Documentation](./DOCUMENTATION.md)**

## Prerequisites

- PHP >= 8.1
- [Composer](https://getcomposer.org/)
- A database server (e.g., MySQL, PostgreSQL) or SQLite
- A Google Cloud Platform account with a configured project and bucket.

## Installation and Setup

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/haidar180202/backend-tinder.git
    cd backend-tinder
    ```

2.  **Install Dependencies:**
    ```bash
    composer install
    ```

3.  **Environment Configuration:**
    -   Create your `.env` file: `cp .env.example .env`
    -   Generate a new application key: `php artisan key:generate`

4.  **Database Setup:**
    -   Configure your `DB_*` variables in the `.env` file.
    -   Run migrations and seeders to set up the database schema and populate it with initial data:
        ```bash
        php artisan migrate --seed
        ```

5.  **Google Cloud Storage Setup:**
    -   Place your Google Cloud service account JSON key file in a secure location.
    -   Update the `.env` file with your GCS credentials:
        ```env
        FILESYSTEM_DISK=gcs
        GOOGLE_CLOUD_PROJECT_ID=your-gcp-project-id
        GOOGLE_CLOUD_KEY_FILE=/path/to/your/credentials.json
        GOOGLE_CLOUD_STORAGE_BUCKET=your-gcs-bucket-name
        ```

## Running the Application

-   **Start the development server:**
    ```bash
    php artisan serve
    ```
-   The API will be accessible at `http://127.0.0.1:8000`.

## API Endpoints

A summary of the primary endpoints is listed below. For full details, see the [API Documentation](./DOCUMENTATION.md).

| Method | URI                     | Action                               |
|--------|-------------------------|--------------------------------------|
| POST   | `/api/register`         | Register a new user.                 |
| POST   | `/api/login`            | Log in a user and receive a token.   |
| GET    | `/api/users/recommended`| Get a list of recommended users.     |
| GET    | `/api/users/{id}/action`| Like or dislike a user.              |

## Contributing

Contributions are highly encouraged. Please fork the repository and submit a pull request with your changes.