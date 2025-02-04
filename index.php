<?php
// index.php

require_once 'auth.php';  // Include authentication file
require_once 'BookAPI.php'; // Include BookAPI file
require_once 'vendor/autoload.php'; // Include Composer autoload to access Firebase JWT

header("Content-Type: application/json");

// Get the current user role from JWT
$userRole = 'guest';  // Default to 'guest'
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $jwt = trim(str_replace('Bearer', '', $_SERVER['HTTP_AUTHORIZATION']));
    $decodedUser = decodeJWT($jwt);
    if ($decodedUser && isset($decodedUser['role'])) {
        $userRole = $decodedUser['role'];  // Extract role from decoded token
    }
}

// Initialize the BookAPI class
$bookAPI = new BookAPI();

// Helper function for error handling
function handleError($message, $statusCode = 500) {
    http_response_code($statusCode);
    echo json_encode(["message" => $message]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = rtrim($_SERVER['REQUEST_URI'], '/');  // Remove trailing slash

// Handle login
if (strpos($endpoint, '/login') !== false && $method === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    if (empty($input['username']) || empty($input['password'])) {
        handleError("Username and password are required", 400);
    }

    $username = $input['username'];
    $password = $input['password'];
    
    $authResult = authenticate($username, $password);
    if ($authResult) {
        $token = generateJWT($authResult);
        echo json_encode(["message" => "Login successful", "role" => $authResult['role'], "token" => $token]);
    } else {
        handleError("Invalid credentials", 401);
    }
    exit();
} else 

// Handle logout
if (strpos($endpoint, '/logout') !== false && $method === 'POST') {
    // If implementing blacklisting, you'd mark the token as invalid here
    echo json_encode(["message" => "Logout successful. Token invalidated (client-side)."]);
    exit();
}

// Handle API requests for books
if (strpos($endpoint, '/books') !== false) {
    if (preg_match('/\/books\/(\d+)$/', $endpoint, $matches) && $method === 'GET') {
        // Fetch a single book by ID
        $bookAPI->getBookById($matches[1], $userRole);
    } elseif ($method === 'GET') {
        if (isset($_GET['genre_id'])) {
            $bookAPI->getBooksByGenre($_GET['genre_id'], $userRole);
        } elseif (isset($_GET['search'])) {
            $searchQuery = $_GET['search'];
            if (empty($searchQuery)) handleError("Search query is required", 400);
            $bookAPI->searchBooks($searchQuery, $userRole);
        } else {
            $bookAPI->getBooks($userRole);
        }
    } elseif ($method === 'POST') {
        // Add a new book
        $input = json_decode(file_get_contents("php://input"), true);
        
        // Validate the required fields
        if (empty($input['title']) || empty($input['author']) || empty($input['published_date']) || empty($input['description']) || empty($input['genre'])) {
            handleError("Title, author, published_date, description, and genre are required", 400);
        }

        // Add the book
        $bookAPI->addBook($input);
        exit();
    } elseif ($method === 'PUT') {
        // Update a book by ID
        if (preg_match('/\/books\/(\d+)$/', $endpoint, $matches)) {
            $input = json_decode(file_get_contents("php://input"), true);

            // Validate the required fields for the update
            if (empty($input['title']) || empty($input['author']) || empty($input['published_date']) || empty($input['description']) || empty($input['genre'])) {
                handleError("Title, author, published_date, description, and genre are required", 400);
            }

            // Update the book
            $bookAPI->updateBook($matches[1], $input);
            exit();
        }
    } elseif ($method === 'DELETE') {
        // Delete a book by ID
        if (preg_match('/\/books\/(\d+)$/', $endpoint, $matches)) {
            $bookAPI->deleteBook($matches[1]);
            exit();
        }
    }
} else {
    handleError("Invalid API endpoint", 404);
}

?>
