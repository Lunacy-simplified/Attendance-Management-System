Attendance Management System (AMS)
A Laravel-based attendance system running on Docker via Laravel Sail.

🛠 Prerequisites
Windows 10/11 with WSL2 enabled.

Docker Desktop (configured to use the WSL2 backend).

VS Code with the WSL Extension installed.

⚠️ Important: All commands below must be run inside your Ubuntu/WSL terminal, not PowerShell or Git Bash.

🚀 Setup (First Time)

1. Clone the Repository
   Bash

git clone https://github.com/Lunacy-simplified/Attendance-Management-System
cd Attendance-Management-System 2. Install Dependencies (The "Magic" Step)
Since you don't have PHP installed locally, use this Docker command to install the project's PHP libraries (including Sail):

Bash

docker run --rm \
 -u "$(id -u):$(id -g)" \
 -v "$(pwd):/var/www/html" \
 -w /var/www/html \
 laravelsail/php83-composer:latest \
 composer install --ignore-platform-reqs 3. Configure Environment
Create your .env file:

Bash

cp .env.example .env 4. Start the Application
Now that dependencies are installed, start the Docker containers:

Bash

./vendor/bin/sail up -d 5. Finalize Setup
Generate the app key and set up the database:

Bash

./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
✅ Done! Access the site at: http://localhost

💻 Daily Usage
Start & Stop
Start server: ./vendor/bin/sail up -d

Stop server: ./vendor/bin/sail down

Running Commands
Always prefix commands with ./vendor/bin/sail so they run inside Docker:

./vendor/bin/sail artisan migrate

./vendor/bin/sail composer require [package]

Pro Tip: Add an alias to your ~/.bashrc file so you can just type sail instead of the full path: alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

🐙 Git Workflow
Pushing Changes
Make edits in VS Code (connected to WSL).

Stage and commit:

Bash

git add .
git commit -m "Description of changes"
Push to GitHub:

Bash

git push origin main
Pulling Changes
Download latest code:

Bash

git pull origin main
Crucial: If the update changed dependencies, rebuild your environment:

Bash

./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
./vendor/bin/sail artisan migrate
