<?php
//auth.php
// Start the session
session_start();

// Hard-coded user credentials (admin, user, and guest roles)
$users = [
    'admin' => ['password' => 'adminpass', 'role' => 'admin'],
    'user1' => ['password' => 'userpass', 'role' => 'user'],
    'guest' => ['password' => '', 'role' => 'guest'], // No password for guest
];

// Function to authenticate users
function authenticate($username, $password) {
    global $users;

    // Hard-coded check for the 'admin' role
    if (isset($users[$username])) {
        if ($username === 'admin' && $password === 'adminpass') {
            $_SESSION['role'] = 'admin';
            return true;
        } elseif ($users[$username]['password'] === $password || $password === '') {
            $_SESSION['role'] = $users[$username]['role'];
            return true;
        }
    }
    return false;
}

// Check if user is logged in and has the required role
function isAuthorized($requiredRole) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === $requiredRole) {
        return true;
    }
    return false;
}

// Get the current user role
function getUserRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : 'guest'; // Default to 'guest' if not logged in
}

// Logout function
function logout() {
    session_destroy();
    echo json_encode(["message" => "Logged out successfully"]);
}
?>
