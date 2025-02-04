<?php
// auth.php

require_once 'vendor/autoload.php'; // Include Composer autoload to access Firebase JWT

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// JWT secret key for signing tokens
define('SECRET_KEY', 'c67f9a20c99a321a8b2a2a0c4f0289b58ad4f3c7ac0042a8b4d0b23c9e59e07a');  // Replace with a more secure secret

// Hard-coded user credentials (admin, user, guest)
$users = [
    'admin' => ['password' => password_hash('adminpass', PASSWORD_BCRYPT), 'role' => 'admin'],
    'user1' => ['password' => password_hash('userpass', PASSWORD_BCRYPT), 'role' => 'user'],
    'guest' => ['password' => '', 'role' => 'guest'], // No password for guest
];

// Function to authenticate users
function authenticate($username, $password) {
    global $users;

    if (isset($users[$username]) && password_verify($password, $users[$username]['password'])) {
        return ['username' => $username, 'role' => $users[$username]['role']];
    }
    return false;
}

// Function to generate a JWT token
function generateJWT($user) {
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;  // JWT valid for 1 hour
    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'user' => [
            'username' => $user['username'], // Correct the username field
            'role' => $user['role']          // Correct the role field
        ]
    ];
    return JWT::encode($payload, SECRET_KEY, 'HS256');
}

// Function to decode the JWT token
function decodeJWT($jwt) {
    if (empty($jwt)) {
        file_put_contents('php://stderr', "Error: Empty JWT token\n");  // Log if token is empty
        return null;
    }

    try {
        file_put_contents('php://stderr', "JWT: $jwt\n");  // Log the JWT token for debugging
        $decoded = JWT::decode($jwt, new Key(SECRET_KEY, 'HS256'));

        file_put_contents('php://stderr', "Decoded JWT: " . print_r($decoded, true) . "\n");  // Log the decoded JWT
        return (array) $decoded->user;
    } catch (ExpiredException $e) {
        file_put_contents('php://stderr', "JWT expired: " . $e->getMessage() . "\n");
        return null;  // Could return a specific error message or code indicating the token has expired
    } catch (Exception $e) {
        file_put_contents('php://stderr', "JWT Decoding Error: " . $e->getMessage() . "\n");
        return null;  // Could return a specific error message for other exceptions
    }
}

// Function to check user authorization based on role
function isAuthorized($requiredRole, $jwt) {
    $user = decodeJWT($jwt);  // Decode the JWT and get user data

    if (!$user) {
        file_put_contents('php://stderr', "Unauthorized: No user found\n");  // Log if user not found
        return false;  // User not found or token invalid
    }

    file_put_contents('php://stderr', "Decoded Role: " . $user['role'] . "\n");  // Log the role of the user

    return $user['role'] === $requiredRole;  // Check if role matches the required role
}

// Function to retrieve token from the Authorization header
function getAuthToken() {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $authHeader = $headers['Authorization'];
        // Assuming the token is sent as 'Bearer <token>'
        if (preg_match('/Bearer (.+)/', $authHeader, $matches)) {
            return $matches[1]; // Return the token
        }
    }
    return null; // No token found
}
?>
