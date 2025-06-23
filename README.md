# 🚀 Deployment instructions

## ⚙️ Installation Steps

Follow the steps below in the exact order to install and run the application locally.

---

### 1. Install Required Dependencies

- **Node.js** : Version 23.11.0
- **NPM** : Version 10.9.2
- **Git** : Version 2.48.1
- **PHP** : Version 8.3.14
- **Composer** : Version 2.8.5
- **MySQL** : Version 8.0.40

* **Web server** : Apache ou Nginx

- **Symfony CLI** : Version 5.2.7
- **Stripe Account**: Required to configure the payment system and retrieve the development mode _public key_.

> [!TIP]
> You can also use tools like:
> XAMP or WAMP to install the necessary dependencies on the server.

> [!IMPORTANT]
> If you are in Windows environment, you will have to install **Git Bash**.

### 2. Clone the repository:

- Open your terminal and run the following commands:

  ```bash
  git clone https://github.com/masterxamss/knowledge-learning.git
  cd knowledge-learning
  ```

### 3. Setting environment variables

- Create a .env.local file in the root directory of the project and add the following environment variables:
- Ensure that you change the values with your credentials.

  ```bash
  cp .env .env.local
  ```

  ```env.local
  STRIPE_PUBLIC_KEY=your_stripe_public_key
  DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/knowledge_db?serverVersion=8.0.32&charset=utf8mb4"
  MAILER_FROM=your_email@example.com
  MAILER_DSN=smtp://user:password@smtp.exemple.com:587
  ```

- Create .env.test.local (For Test Environment)

  ```bash
  cp .env.test .env.test.local
  ```

  ```env.test.local
  MAILER_DSN=sendmail://default
  DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/knowledge_db?serverVersion=8.0.32&charset=utf8mb4"
  MAILER_FROM=test@example.com
  ```

### 4. Install dependencies:

```bash
composer install
```

### 5. Start the server for the first time:

```bash
composer start:dev
```

> [!NOTE]
>
> This will:
>
> - Automatically create the database (if it doesn't exist)
> - Run all migrations
> - Load sample data using fixtures
> - Execute the test suite
>
> **Default Users Created** > **Admin User**
>
> - Email: john.doe@example.com
> - Password: #JohnDoe_123
>
> **Standard User**
>
> - Email: jane.doe@example.com
> - Password: #JaneDoe_123

The results of the tests will be displayed in the terminal.

After this step, the application will be accessible at:
🔗 http://localhost:8000

### 6. Manage the Server

To stop the server:

```bash
symfony serve:stop
```

To start it again (without repeating the install process):

```bash
 symfony serve:start
```
