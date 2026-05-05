<?php
/**
 * Front Controller — all web requests route through here.
 * On cPanel: set document root to this "public/" folder.
 */

define('APP_ROOT', realpath(dirname(__DIR__)));
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';

// ── Resolve URI ──────────────────────────────────────────────────────────────
// Use REQUEST_URI (the real URL the browser sent) — not SCRIPT_NAME,
// which may contain /public when accessed via root .htaccess forward.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip any sub-folder prefix (e.g. /BusTicketingSystem on local dev).
// We compute base from SCRIPT_NAME but strip the /public segment.
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
$base       = rtrim(dirname(preg_replace('#/public/index\.php$#i', '/index.php', $scriptName)), '/');
if ($base && $base !== '/' && strpos($uri, $base) === 0) {
    $uri = substr($uri, strlen($base));
}
$uri    = '/' . trim($uri, '/');
$method = $_SERVER['REQUEST_METHOD'];

// ── Helpers ──────────────────────────────────────────────────────────────────
function view(string $path): void {
    $full = APP_ROOT . '/app/views/' . $path;
    if (file_exists($full)) {
        require $full;
    } else {
        http_response_code(404);
        echo '<h1>404 — Page Not Found</h1>';
    }
}

function loadController(string $name): void {
    require_once APP_ROOT . '/app/controllers/' . $name . '.php';
}

// ── Router ───────────────────────────────────────────────────────────────────
switch ($uri) {

    // Home
    case '/':
        view('home.php');
        break;

    // ── Auth ─────────────────────────────────────────────────────────────────
    case '/login':
        if ($method === 'POST') {
            loadController('AuthController');
            AuthController::login();
        } else {
            view('auth/login.php');
        }
        break;

    case '/register':
        if ($method === 'POST') {
            loadController('AuthController');
            AuthController::register();
        } else {
            view('auth/register.php');
        }
        break;

    case '/verify-otp':
        if ($method === 'POST') {
            loadController('AuthController');
            AuthController::verifyOTP();
        } else {
            if (empty($_SESSION['temp_user_id'])) {
                header('Location: ' . BASE_URL . 'register');
                exit;
            }
            view('auth/verify_otp.php');
        }
        break;

    case '/resend-otp':
        loadController('AuthController');
        AuthController::resendOTP();
        break;

    case '/forgot-password':
        if ($method === 'POST') {
            loadController('AuthController');
            AuthController::forgotPassword();
        } else {
            view('auth/forgot_password.php');
        }
        break;

    case '/reset-password':
        if ($method === 'POST') {
            loadController('AuthController');
            AuthController::resetPassword();
        } else {
            view('auth/reset_password.php');
        }
        break;

    case '/logout':
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: ' . BASE_URL);
        exit;

    // ── Admin ────────────────────────────────────────────────────────────────
    case '/admin':
        header('Location: ' . BASE_URL . 'admin/dashboard');
        exit;
    case '/admin/dashboard':
        view('admin/dashboard.php');
        break;
    case '/admin/users':
        view('admin/manage_users.php');
        break;
    case '/admin/buses':
        view('admin/manage_buses.php');
        break;
    case '/admin/schedules':
        view('admin/manage_schedules.php');
        break;
    case '/admin/tickets':
        view('admin/manage_tickets.php');
        break;
    case '/admin/reports':
        view('admin/reports.php');
        break;
    case '/admin/add-bus':
        view('admin/add_bus.php');
        break;
    case '/admin/add-operator':
        view('admin/add_operator.php');
        break;

    // ── Operator ─────────────────────────────────────────────────────────────
    case '/operator':
        header('Location: ' . BASE_URL . 'operator/dashboard');
        exit;
    case '/operator/dashboard':
        view('operator/dashboard.php');
        break;
    case '/operator/add-schedule':
        view('operator/add_schedule.php');
        break;
    case '/operator/schedules':
        view('operator/manage_schedules.php');
        break;
    case '/operator/boarding':
        view('operator/boarding.php');
        break;
    case '/operator/on-spot-booking':
        view('operator/on_spot_booking.php');
        break;
    case '/operator/process-trip':
        view('operator/process_trip.php');
        break;
    case '/operator/get-passenger':
        view('operator/get_passenger_details.php');
        break;

    // ── Passenger ────────────────────────────────────────────────────────────
    case '/passenger':
        header('Location: ' . BASE_URL . 'passenger/dashboard');
        exit;
    case '/passenger/dashboard':
        view('passenger/dashboard.php');
        break;
    case '/passenger/book-ticket':
        view('passenger/book_ticket.php');
        break;
    case '/passenger/my-tickets':
        view('passenger/my_ticket.php');
        break;

    // ── Public pages ─────────────────────────────────────────────────────────
    case '/search':
        view('search.php');
        break;
    case '/results':
        view('results.php');
        break;
    case '/receipt':
        view('receipt.php');
        break;
    case '/download-ticket':
        view('download_ticket.php');
        break;
    case '/verify-ticket':
        view('verify_ticket.php');
        break;
    case '/booking':
        if ($method === 'POST') {
            view('booking_handler.php');
        } else {
            header('Location: ' . BASE_URL . 'search');
            exit;
        }
        break;

    // ── 404 ──────────────────────────────────────────────────────────────────
    default:
        http_response_code(404);
        echo '<!DOCTYPE html><html><body style="font-family:sans-serif;text-align:center;padding:60px">
              <h1>404 — Page Not Found</h1>
              <p><a href="' . BASE_URL . '">Go Home</a></p></body></html>';
        break;
}
