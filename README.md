# Tickit: Simplify Your Ticket Management 🚀

Tickit is a robust and intuitive web application designed for efficient personal ticket management. Built with a minimalist PHP backend and the Twig templating engine, it allows users to effortlessly create, track, update, and delete support tickets, providing a clear overview of their tasks through a dynamic dashboard.

## Installation 🛠️

To get Tickit up and running on your local machine, follow these simple steps:

### Clone the Repository

Start by cloning the project repository to your desired directory:

```bash
git clone https://github.com/your-username/tickit-twig.git # Replace with your actual repo URL
cd tickit-twig
```

### Install Dependencies

This project uses Composer to manage its PHP dependencies.

```bash
composer install
```

This command will install Twig and any other required libraries.

### Start the PHP Development Server

You can use PHP's built-in web server for local development:

```bash
php -S localhost:8000
```

This will start the server on `http://localhost:8000`.

### Configure Data Storage

Tickit uses a local JSON file for data persistence.

- Ensure the `data/` directory exists at the root of your project:
  ```bash
  mkdir -p data
  ```
- The `JsonDataManager` will automatically create `data/users.json` if it doesn't exist when the application runs.

## Usage 🧑‍💻

Once the server is running, navigate to `http://localhost:8000` in your web browser.

1.  **Landing Page**: You'll be greeted by the landing page with options to Login or Get Started (Sign Up).
2.  **Sign Up**: Create a new account by providing your full name, email, and password. Upon successful registration, you'll be redirected to your dashboard.
3.  **Login**: Access your account with your registered email and password.
4.  **Dashboard**: View a summary of your tickets, including total, open, in-progress, and closed tickets. Quick actions allow you to create or view tickets.
5.  **Ticket Management**: Navigate to the "Tickets" section to:
    - **Create New Ticket**: Fill out the form with a title, description, status, and priority.
    - **View Tickets**: See a list of all your tickets.
    - **Edit Ticket**: Click the "Edit" button on any ticket to pre-fill the form and update its details.
    - **Delete Ticket**: Click the "Delete" button and confirm to remove a ticket.
6.  **Logout**: Securely end your session from the navigation bar.

## Features ✨

- **User Authentication**: Secure user registration, login, and logout.
- **Session Management**: Maintains user sessions for authenticated access.
- **Ticket Creation**: Easily add new tickets with title, description, status, and priority.
- **Ticket Viewing**: Displays a comprehensive list of all created tickets.
- **Ticket Editing**: Update existing tickets with new information and status changes.
- **Ticket Deletion**: Remove tickets that are no longer needed.
- **Dashboard Overview**: Provides at-a-glance statistics on ticket status (open, in-progress, closed).
- **Local Data Storage**: Persists user and ticket data in a local JSON file.

## Technologies Used 💻

| Technology     | Description                                  | Version | Link                                                                    |
| :------------- | :------------------------------------------- | :------ | :---------------------------------------------------------------------- |
| **PHP**        | Backend scripting language                   | 8.x     | [php.net](https://www.php.net/)                                         |
| **Twig**       | Flexible, fast, and secure templating engine | 3.x     | [twig.symfony.com](https://twig.symfony.com/)                           |
| **Composer**   | Dependency Manager for PHP                   | 2.x     | [getcomposer.org](https://getcomposer.org/)                             |
| **HTML5**      | Structure of web pages                       | N/A     | [W3C HTML5](https://www.w3.org/TR/html5/)                               |
| **CSS3**       | Styling of web pages                         | N/A     | [W3C CSS3](https://www.w3.org/Style/CSS/specs.html)                     |
| **JavaScript** | Client-side scripting for interactivity      | ES6+    | [MDN Web Docs](https://developer.mozilla.org/en-US/docs/Web/JavaScript) |

## API Documentation

### Overview

The Tickit application operates as a server-rendered PHP application, where routes handle traditional form submissions and serve HTML responses. Data persistence is managed via a `JsonDataManager` storing user and ticket information in local JSON files.

### Base URL

The application is accessed from the root path `/` of the web server. All authenticated routes are prefixed with `/app`.

### Environment Variables

The application currently does not utilize external environment variables (e.g., via `.env` files). Key configurations, such as the data file path (`data/users.json`), are managed internally within the `src/JsonDataManager.php` class. For production deployment, it is recommended to externalize such paths.

### Endpoints

#### POST /login

Authenticates a user based on provided credentials.

**Request**:

```
Content-Type: application/x-www-form-urlencoded

email=user%40example.com&password=securePassword123
```

- `email` (string, required): User's email address.
- `password` (string, required): User's password.

**Response**:

- **Success (HTML Redirect)**: On successful login, the user is redirected to `/app/dashboard`.
  ```html
  <!DOCTYPE html>
  <html lang="en">
  	...
  	<body>
  		...
  		<script>
  			setTimeout(() => {
  				window.location.href = "/app/dashboard";
  			}, 1500);
  		</script>
  	</body>
  </html>
  ```
- **Error (HTML Render)**: If login fails, the `login.html.twig` template is re-rendered with an `error` message.
  ```html
  <span class="error">Invalid email or password</span>
  ```

**Errors**:

- `400 Bad Request`: Missing `email` or `password`.
- `200 OK` (with error message): Invalid credentials.

#### POST /signup

Registers a new user account.

**Request**:

```
Content-Type: application/x-www-form-urlencoded

name=John%20Doe&email=john.doe%40example.com&password=StrongPassword123&confirmPassword=StrongPassword123
```

- `name` (string, required): Full name of the user.
- `email` (string, required): User's email address (must be unique).
- `password` (string, required): User's chosen password.
- `confirmPassword` (string, required): Confirmation of the password.

**Response**:

- **Success (HTML Redirect)**: On successful registration, the user is redirected to `/app/dashboard`.
  ```html
  <!DOCTYPE html>
  <html lang="en">
  	...
  	<body>
  		...
  		<script>
  			setTimeout(() => {
  				window.location.href = "/app/dashboard";
  			}, 1500);
  		</script>
  	</body>
  </html>
  ```
- **Error (HTML Render)**: If registration fails (e.g., email already exists, passwords don't match), the `signup.html.twig` template is re-rendered with an `error` message.
  ```html
  <span class="error">Passwords do not match</span>
  <!-- or -->
  <span class="error">Email already in use</span>
  ```

**Errors**:

- `400 Bad Request`: Missing required fields.
- `200 OK` (with error message): Passwords do not match or email already in use.

#### GET /app/dashboard

Displays the authenticated user's dashboard with ticket statistics.

**Request**:
No specific request payload. Requires an active session.

**Response**:

- **Success (HTML Render)**: Renders `components/dashboard.twig` with user details and ticket statistics.
  ```html
  <!-- Example partial HTML for dashboard -->
  <section class="dashboardSection">
  	<div class="container">
  		<h1 class="title">Welcome back, John Doe</h1>
  		<div class="majorStat">...</div>
  		<div class="minorStats">...</div>
  	</div>
  </section>
  ```
- **Error (HTML Redirect)**: If not authenticated, redirects to `/login`.

**Errors**:

- `302 Found`: Unauthenticated access (redirects to `/login`).

#### GET /app/tickets

Displays the authenticated user's tickets and the ticket management form. Can be used to pre-fill the form for editing.

**Request**:
No specific request payload for viewing all tickets.

- Optional: `edit_id` (string): The ID of the ticket to be edited.
  ```
  /app/tickets?edit_id=t1
  ```

**Response**:

- **Success (HTML Render)**: Renders `ticketmanagerpage.html.twig` with the user's tickets. If `edit_id` is provided, the `editTicket` variable will contain the ticket data, pre-filling the form.
  ```html
  <!-- Example partial HTML for ticket list and form -->
  <form class="form" method="POST" action="/app/tickets">
  	<h2>Create New Ticket</h2>
  	<input type="hidden" name="edit_id" value="" />
  	<label for="title">Title</label>
  	<input id="title" type="text" name="title" value="Existing Ticket Title" />
  	...
  </form>
  <div id="ticketList">
  	<div class="card" data-ticket-id="t1">...</div>
  </div>
  ```
- **Error (HTML Redirect)**: If not authenticated, redirects to `/login`.

**Errors**:

- `302 Found`: Unauthenticated access (redirects to `/login`).

#### POST /app/tickets

Handles creating, updating, or deleting tickets.

**Request**:

```
Content-Type: application/x-www-form-urlencoded
```

- **Create Ticket**:
  `title=New%20Ticket&description=Details%20about%20the%20new%20ticket.&status=open&priority=high`
  - `title` (string, required): Title of the ticket.
  - `description` (string, optional): Detailed description.
  - `status` (enum, optional, default: 'open'): `open`, `in_progress`, `closed`.
  - `priority` (enum, optional, default: 'high'): `high`, `medium`, `low`.
- **Update Ticket**:
  `edit_id=t1&title=Updated%20Title&description=Updated%20Details.&status=in_progress&priority=medium`
  - `edit_id` (string, required): The ID of the ticket to update.
  - `title`, `description`, `status`, `priority` (as above).
- **Delete Ticket**:
  `delete_id=t1`
  - `delete_id` (string, required): The ID of the ticket to delete.

**Response**:

- **Success (HTML Render)**: Renders `ticketmanagerpage.html.twig` with the updated list of tickets and a success message.
  ```html
  <!-- Example partial HTML showing success message -->
  <p class="successMessage">Ticket saved successfully!</p>
  <!-- or -->
  <p class="successMessage">Ticket updated successfully!</p>
  <!-- or -->
  <p class="successMessage">Ticket deleted successfully!</p>
  ```
- **Error (HTML Redirect)**: If not authenticated, redirects to `/login`.
- **Error (HTML Render)**: If form data is invalid or an operation fails, the page might re-render with the form and appropriate error indication (though current implementation focuses on success messages and relies on HTML `required` for basic validation).

**Errors**:

- `302 Found`: Unauthenticated access (redirects to `/login`).
- `400 Bad Request`: Missing required fields for create/update/delete operations.

#### GET /logout

Logs out the current user and destroys the session.

**Request**:
No specific request payload. Requires an active session.

**Response**:

- **Success (HTML Redirect)**: Redirects to the landing page `/`.

**Errors**:
None, always redirects.

## Contributing 🤝

We welcome contributions to Tickit! If you have suggestions for improvements or find any issues, please follow these guidelines:

- Fork the repository.
- Create a new branch for your feature or bug fix: `git checkout -b feature/your-feature-name`
- Make your changes and ensure the code adheres to the existing style.
- Commit your changes with a clear and concise message: `git commit -m "feat: Add new feature for X"`
- Push your branch: `git push origin feature/your-feature-name`
- Open a pull request describing your changes.

## License 📜

No explicit license has been provided for this project. All rights are reserved by the author.

## Author Info 👤

Developed by a passionate software engineer dedicated to building efficient web solutions.

- **LinkedIn**: [Ehizojie Azamegbe](https://www.linkedin.com/in/ehizojie-azamegbe-082ba52b9/)
- **Twitter**: [@ehiz_dev](https://x.com/ehiz_dev)

---

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![Twig](https://img.shields.io/badge/Twig-3.x-2C2D32?style=flat&logo=twig&logoColor=white)](https://twig.symfony.com/)
[![Composer](https://img.shields.io/badge/Composer-2.x-885630?style=flat&logo=composer&logoColor=white)](https://getcomposer.org/)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white)](https://www.w3.org/TR/html5/)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white)](https://www.w3.org/Style/CSS/specs.html)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![JsonDataManager](https://img.shields.io/badge/Data%20Storage-JSON-000000?style=flat&logo=json&logoColor=white)](https://www.json.org/json-en.html)

---

[![Readme was generated by Dokugen](https://img.shields.io/badge/Readme%20was%20generated%20by-Dokugen-brightgreen)](https://www.npmjs.com/package/dokugen)
