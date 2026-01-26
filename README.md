# Attendance Management System (AMS) — Setup & Workflow

This document explains how to set up, run, and contribute to the Attendance Management System (AMS) on Windows using WSL2, Docker, and Laravel Sail.

---

## 1️⃣ Prerequisites

Make sure you have the following installed:

- [Docker Desktop](https://www.docker.com/products/docker-desktop) (with WSL2 enabled)
- [Windows Subsystem for Linux (WSL2)](https://learn.microsoft.com/en-us/windows/wsl/install)
- [VS Code](https://code.visualstudio.com/) with **Remote - WSL** extension
- [Git](https://git-scm.com/) installed in WSL
- Optional: [Node.js & npm](https://nodejs.org/) in WSL if not using Sail’s Node container

---

## 2️⃣ First-Time Setup

### 2.1 Clone the Repository

Open WSL terminal:

```bash
cd ~
mkdir -p code
cd code
git clone https://github.com/Lunacy-simplified/Attendance-Management-System
cd Attendance-Management-System

2.2 Set permissions

sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

This ensures Laravel can write logs and cache files.

2.3 Start Sail & Install Dependencies

Start Docker containers:

./vendor/bin/sail up -d


Install PHP dependencies:

./vendor/bin/sail composer install


Install Node dependencies (inside Sail container):

./vendor/bin/sail npm install

2.4 Environment Setup

Copy the example environment file:

cp .env.example .env


Generate the application key:

./vendor/bin/sail artisan key:generate

2.5 Database Setup

Run migrations to create tables:

./vendor/bin/sail artisan migrate


If you need seed data:

./vendor/bin/sail artisan db:seed


2.6 Build Frontend Assets

For development:

./vendor/bin/sail npm run dev


For production:

./vendor/bin/sail npm run build

3️⃣ Running the Application

Once Sail is up, open your browser:

http://localhost


No need to run php artisan serve; Sail’s Nginx serves the app.

To stop containers:

./vendor/bin/sail down

4️⃣ Git Workflow
4.1 Pushing Changes

Make your changes in VS Code (connected to WSL project)

Stage & commit:

git add .
git commit -m "Describe your changes"


Push to GitHub:

git push origin main

4.2 Pulling Changes
git pull origin main


If using Sail, make sure to rebuild containers if dependencies changed:

./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

4.3 Rebuilding Containers

If you need to rebuild Docker containers (e.g., after changing PHP version or docker-compose.yml):

./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d

5️⃣ Troubleshooting

Permission errors on storage/logs or bootstrap/cache:

sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache


Database table missing errors:

./vendor/bin/sail artisan migrate


Assets not showing or broken styles:

./vendor/bin/sail npm run build

6️⃣ Notes

Windows folder vs WSL folder:

You can edit files in Windows (GitHub Desktop) or WSL (VS Code Remote).

Sail always uses the WSL path for Docker mounting.

Always use Sail for running PHP commands (composer, artisan) to avoid local PHP issues.
