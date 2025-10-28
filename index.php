<?php
// index.php - entry point for your Twig SPA
session_start();

// Serve static files when using PHP built-in server
if (php_sapi_name() === 'cli-server') {
    $url  = parse_url($_SERVER["REQUEST_URI"]);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false; // serve the requested file directly
    }
}

// Autoload Composer dependencies
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/JsonDataManager.php';

// Twig setup
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

// Initialize data manager
$dataManager = new JsonDataManager(__DIR__ . '/data/users.json');

// Determine route
$route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Redirect /app to /app/dashboard
if ($route === '/app') {
    header("Location: /app/dashboard");
    exit;
}

// Helper function to check auth
function isAuthenticated(): bool {
    return isset($_SESSION['user_email']);
}

// Handle POST data
$postData = $_POST ?? [];

// Basic routing
switch ($route) {
    // Public pages
    case '/':
        echo $twig->render('landingpage.html.twig', ['current_route' => $route]);
        break;

    case '/login':
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $postData['email'] ?? '';
            $password = $postData['password'] ?? '';

            $user = $dataManager->validateLogin($email, $password);
            if ($user) {
                $_SESSION['user_email'] = $user['email'];
                echo $twig->render('login.html.twig', [
                    'current_route' => $route,
                    'success' => true
                ]);
                echo "<script>setTimeout(()=>{ window.location.href='/app/dashboard'; }, 1500);</script>";
                exit;
            } else {
                $error = 'Invalid email or password';
            }
        }

        echo $twig->render('login.html.twig', [
            'current_route' => $route,
            'error' => $error
        ]);
        break;

    case '/signup':
        $error = null;
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $postData['name'] ?? '';
            $email = $postData['email'] ?? '';
            $password = $postData['password'] ?? '';
            $confirmPassword = $postData['confirmPassword'] ?? '';

            if ($password !== $confirmPassword) {
                $error = 'Passwords do not match';
            } else {
                $added = $dataManager->addUser($name, $email, $password);
                if ($added) {
                    $_SESSION['user_email'] = $email;
                    $success = true;
                    echo $twig->render('signup.html.twig', [
                        'current_route' => $route,
                        'success' => $success
                    ]);
                    echo "<script>setTimeout(()=>{ window.location.href='/app/dashboard'; }, 1500);</script>";
                    exit;
                } else {
                    $error = 'Email already in use';
                }
            }
        }

        echo $twig->render('signup.html.twig', [
            'current_route' => $route,
            'error' => $error,
            'success' => $success
        ]);
        break;

    case '/pagenotfound':
        echo $twig->render('pagenotfound.html.twig', ['current_route' => $route]);
        break;

    // Authenticated / app pages
case '/app/dashboard':
    if (!isAuthenticated()) {
        header("Location: /login");
        exit;
    }

    $user = $dataManager->getUserByEmail($_SESSION['user_email']);
    $tickets = $user['tickets'] ?? [];

    // Count tickets by status
    $totalTickets = count($tickets);
    $openTickets = count(array_filter($tickets, fn($t) => $t['status'] === 'open'));
    $inProgressTickets = count(array_filter($tickets, fn($t) => $t['status'] === 'in_progress'));
    $closedTickets = count(array_filter($tickets, fn($t) => $t['status'] === 'closed'));

    echo $twig->render('components/dashboard.twig', [
        'user' => $user,
        'totalTickets' => $totalTickets,
        'openTickets' => $openTickets,
        'inProgressTickets' => $inProgressTickets,
        'closedTickets' => $closedTickets,
        'current_route' => $route
    ]);
    break;


    case '/app/tickets':
        if (!isAuthenticated()) {
            header("Location: /login");
            exit;
        }

        $user = $dataManager->getUserByEmail($_SESSION['user_email']);
        $tickets = $user['tickets'] ?? [];

        // Handle POST actions (create, edit, or delete)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // DELETE TICKET
            if (isset($_POST['delete_id'])) {
                $deleteId = $_POST['delete_id'];
                $tickets = array_filter($tickets, fn($t) => $t['id'] !== $deleteId);
                $tickets = array_values($tickets);

                // Save updated tickets to user
                $dataManager->setUserTickets($_SESSION['user_email'], $tickets);

                echo $twig->render('ticketmanagerpage.html.twig', [
                    'tickets' => $tickets,
                    'successDelete' => true,
                    'current_route' => $route
                ]);
                break;
            }

            // CREATE OR EDIT TICKET
            $title = $postData['title'] ?? '';
            $description = $postData['description'] ?? '';
            $status = $postData['status'] ?? 'open';
            $priority = $postData['priority'] ?? 'high';
            $editId = $postData['edit_id'] ?? null;

            if ($editId) {
                // Edit existing ticket
                foreach ($tickets as &$ticket) {
                    if ($ticket['id'] === $editId) {
                        $ticket['title'] = $title;
                        $ticket['description'] = $description;
                        $ticket['status'] = $status;
                        $ticket['priority'] = $priority;
                        $ticket['date'] = date('Y-m-d');
                        break;
                    }
                }
                unset($ticket);
                $successFlag = 'successUpdate';
            } else {
                // Create new ticket
                $newTicket = [
                    'id' => 't' . (count($tickets) + 1),
                    'title' => $title,
                    'description' => $description,
                    'status' => $status,
                    'priority' => $priority,
                    'date' => date('Y-m-d')
                ];
                $tickets[] = $newTicket;
                $successFlag = 'success';
            }

            // Save updated tickets to user
            $dataManager->setUserTickets($_SESSION['user_email'], $tickets);

            echo $twig->render('ticketmanagerpage.html.twig', [
                'tickets' => $tickets,
                $successFlag => true,
                'current_route' => $route
            ]);
            break;
        }

        
    // GET request — check if user is editing a ticket
    $editTicket = null;
    if (isset($_GET['edit_id'])) {
        $editId = $_GET['edit_id'];
        foreach ($tickets as $ticket) {
            if ($ticket['id'] === $editId) {
                $editTicket = $ticket;
                break;
            }
        }
    }

    // Always render main page, but include editTicket
    echo $twig->render('ticketmanagerpage.html.twig', [
        'tickets' => $tickets,
        'editTicket' => $editTicket,
        'current_route' => $route
    ]);
    break;

    // Logout
    case '/logout':
        session_destroy();
        header("Location: /");
        exit;

    // 404 fallback
    default:
        echo $twig->render('pagenotfound.html.twig', ['current_route' => $route]);
        break;
}
