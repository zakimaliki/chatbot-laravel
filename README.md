# Chatbot Laravel

Chatbot Laravel is an AI-powered chatbot built with Laravel. This project provides an interactive interface where users can communicate with the chatbot and receive automated responses.

## 📌 Features

- **AI-Powered Responses** – Leverages NLP for context-aware replies.
- **User Authentication** – Secure login & registration system.
- **Database-Backed Conversations** – Stores chat history in a database.
- **RESTful API** – Provides an API for external integrations.
- **Admin Dashboard** – Manage chatbot responses and user interactions.
- **Real-Time Messaging** – Uses WebSockets for instant responses (if enabled).

---

## ⚙️ Technologies Used

- **Backend**: Laravel 12, PHP 8+
- **Frontend**: Blade, TailwindCSS, Livewire (if used)
- **Database**: MySQL / PostgreSQL / SQLite (configurable)
- **WebSockets**: Laravel Echo & Pusher (if real-time is enabled)
- **Authentication**: Laravel Sanctum / JWT (configurable)
- **AI Processing**: OpenAI API / Custom NLP Model (configurable)

---

## 📋 Requirements

Before installing, ensure your system has:

- PHP 8.0 or higher
- Composer
- MySQL / PostgreSQL / SQLite
- Node.js & npm
- Laravel CLI (`composer global require laravel/installer`)

---

## 🚀 Installation

Follow these steps to set up the project:

1️⃣ Clone the Repository
git clone https://github.com/zakimaliki/chatbot-laravel.git
cd chatbot-laravel

2️⃣ Install Dependencies

composer install
npm install

3️⃣ Configure Environment
	•	Copy the .env.example file and rename it to .env
	•	Update database credentials, mail settings, and AI API keys (if required).

4️⃣ Generate Application Key

php artisan key:generate

5️⃣ Run Database Migrations

php artisan migrate --seed

6️⃣ Build Frontend Assets

npm run build

7️⃣ Start Development Server

php artisan serve

Access the application at http://localhost:8000.

---

## 🔑 API Keys

If the chatbot uses OpenAI, add your API key in .env:

OPENAI_API_KEY=your_api_key_here
JWT_SECRET=your_jwt_here

---

## 🛠️ API Endpoints

Method	Endpoint	Description
GET	/api/chat	Fetch chat history
POST	/api/chat/send	Send a message
POST	/api/auth/login	Authenticate user

Use Authorization: Bearer <token> for protected routes.

⸻

## 🧪 Running Tests

To run tests:

php artisan test

Or with coverage:

vendor/bin/phpunit --coverage-html=coverage



---

## 🤝 Contributing
	1.	Fork the repository.
	2.	Create a feature branch (git checkout -b feature-branch).
	3.	Commit your changes (git commit -m "Added new feature").
	4.	Push to the branch (git push origin feature-branch).
	5.	Create a Pull Request.

---

## 📜 License

This project is licensed under the MIT License.

---
