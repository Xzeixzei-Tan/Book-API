<?php
//auth.php
session_start();
require_once 'vendor/autoload.php'; // Include Composer autoload to access Firebase JWT

use \Firebase\JWT\JWT;

// JWT secret key for signing tokens
define('SECRET_KEY', 'c67f9a20c99a321a8b2a2a0c4f0289b58ad4f3c7ac0042a8b4d0b23c9e59e07a');  // Replace with a more secure secret

// Hard-coded user credentials (admin, user, and guest roles) with hashed passwords
$users = [
    'admin' => ['password' => password_hash('adminpass', PASSWORD_BCRYPT), 'role' => 'admin'],
    'user1' => ['password' => password_hash('userpass', PASSWORD_BCRYPT), 'role' => 'user'],
    'guest' => ['password' => '', 'role' => 'guest'], // No password for guest
];

// Function to authenticate users
function authenticate($username, $password) {
    global $users;

    // Check if user exists
    if (isset($users[$username])) {
        if ($username === 'admin' && password_verify($password, $users['admin']['password'])) {
            // Admin login
            return ['role' => 'admin'];
        } elseif ($username !== 'admin' && password_verify($password, $users[$username]['password'])) {
            // For other users
            return ['role' => $users[$username]['role']];
        }
    }
    return false;
}

// Function to generate a JWT token
function generateJWT($user) {
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;  // jwt valid for 1 hour from the issued time
    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'user' => $user
    ];

    // Pass the algorithm explicitly as the third argument
    return JWT::encode($payload, SECRET_KEY, 'HS256');
}

// Function to decode JWT token and verify it
function decodeJWT($jwt) {
    try {
        $decoded = JWT::decode($jwt, SECRET_KEY, ['HS256']);
        return (array) $decoded->user;
    } catch (Exception $e) {
        return null;
    }
}

// Check if user has the required role from JWT
function isAuthorized($requiredRole, $jwt) {
    $user = decodeJWT($jwt);
    return $user && $user['role'] === $requiredRole;
}

// Logout function
function logout() {
    // Invalidate the session or token by not passing it in further requests
    echo json_encode(["message" => "Logged out successfully"]);
}

?>
