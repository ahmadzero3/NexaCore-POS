# Using the Makefile for NexaCore-POS

This document explains how to use the `Makefile` to manage your NexaCore-POS Dockerized application. The `Makefile` provides convenient shortcuts for common development and deployment tasks.

## Prerequisites

Before you begin, ensure you have `make` installed on your system. It is usually pre-installed on macOS and Linux distributions. If you are on Windows, you might need to install it via a package manager like Chocolatey or use Git Bash which often includes `make`.

## Available Commands

Navigate to the root directory of your NexaCore-POS project where the `Makefile` is located.

### 1. Initialize the Application

To perform a full initialization of the application, including building Docker images, starting containers, installing PHP dependencies, generating the Laravel application key, and running database migrations:

```bash
make init
```

This command is ideal for setting up the project for the first time or after a clean clone. After execution, the application should be accessible in your web browser at `http://localhost`.

### 2. Development Mode Setup

To set up and run the application specifically for development (similar to `init` but can be used for ongoing development):

```bash
make dev
```

This command combines several steps into one for a quick development environment setup. After execution, the application should be accessible in your web browser at `http://localhost`.

### 4. Production Mode Setup

For a production-like setup, which builds Docker images and starts the containers. This target assumes that dependencies are already installed and migrations are handled as part of your CI/CD pipeline or a separate deployment process.

```bash
make prod
```

**Note**: This `prod` target is a basic example. A robust production deployment would typically involve a more complex setup, potentially using a separate `docker-compose.prod.yml` for optimized images, persistent volumes, and specific production environment variables.

### 5. Building Docker Images

To explicitly build or rebuild the Docker images for your application services (app and nginx):

```bash
make build
```

### 6. Starting Docker Containers

To start the Docker containers in detached mode (runs in the background) without rebuilding images:

```bash
make up
```

### 7. Stopping Docker Containers

To stop the running Docker containers:

```bash
make down
```

This command will stop and remove the containers, networks, and volumes created by `docker-compose up`.

### 8. Installing Composer Dependencies

To install or update PHP dependencies using Composer inside the `app` service container:

```bash
make install
```

### 9. Generating Application Key

To generate the Laravel application key, which is crucial for security:

```bash
make key
```

### 10. Running Database Migrations

To apply pending database migrations and set up your database schema:

```bash
make migrate
```

### 11. Running Any Artisan Command

You can run any Laravel Artisan command directly through the `Makefile`:

```bash
make artisan <your-artisan-command>
```

**Examples:**

*   Clear application cache: `make artisan cache:clear`
*   Run a seeder: `make artisan db:seed`
*   View routes: `make artisan route:list`

### 12. Cleaning Up Docker Environment

To stop and remove all Docker containers, networks, and volumes, and also remove all images associated with your services:

```bash
make clean
```

Use this command with caution as it will remove all data in your Docker volumes for this project.