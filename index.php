<?php
//index.php
require_once 'auth.php';  // Include authentication file
require_once 'BookAPI.php'; // Include BookAPI file
require_once 'vendor/autoload.php'; // Include Composer autoload to access Firebase JWT


// Get the current user role from JWT
$userRole = 'guest';  // Default to 'guest'
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $jwt = trim(str_replace('Bearer', '', $_SERVER['HTTP_AUTHORIZATION']));
    $userRole = getUserRole($jwt);
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
$endpoint = $_SERVER['REQUEST_URI'];

// Handle login and logout
if (strpos($endpoint, '/login') !== false && $method === 'POST') {
    // If login request
    if (empty($_POST['username']) || empty($_POST['password'])) {
        // Return 400 Bad Request if username or password is missing
        handleError("Username and password are required", 400);
    }

    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        if (authenticate($username, $password)) {
            $user = ['username' => $username, 'role' => $_SESSION['role']];
            $token = generateJWT($user);  // Generate JWT token for the logged-in user
            echo json_encode(["message" => "Login successful", "role" => $_SESSION['role'], "token" => $token]);
            exit();  
        } else {
            // Return 401 Unauthorized for invalid credentials
            handleError("Invalid credentials", 401);
        }
    } catch (Exception $e) {
        // Return 500 Internal Server Error for any exception
        handleError("Internal server error: " . $e->getMessage());
    }
} elseif (strpos($endpoint, '/logout') !== false && $method === 'POST') {
    // If logout request
    try {
        logout();
        echo json_encode(["message" => "Logout successful"]);
        exit();
    } catch (Exception $e) {
        // Return 500 Internal Server Error if something goes wrong during logout
        handleError("Internal server error during logout: " . $e->getMessage());
    }
}

// Handle API requests for book viewing, searching, etc.
if (strpos($endpoint, '/books') !== false) {
    // Check if the request is for a single book
    if (preg_match('/\/books\/(\d+)$/', $endpoint, $matches) && $method === 'GET') {
        // Fetch a book by ID
        $bookId = $matches[1];
        try {
            $bookAPI->getBookById($bookId, $userRole);
        } catch (Exception $e) {
            handleError("Error fetching book: " . $e->getMessage());
        }
    } elseif ($method === 'GET') {
        // Handle book listing or search
        if (isset($_GET['genre'])) {
            // Fetch books by genre(s)
            $bookAPI->getBooksByGenre($_GET['genre'], $userRole);
        } elseif (isset($_GET['search'])) {
            // Fetch books based on search query (title or author)
            $searchQuery = $_GET['search'];

            // Check if the search query is empty
            if (empty($searchQuery)) {
                // Return 400 error if search query is empty
                handleError("Search query is required", 400);
            }

            $books = $bookAPI->searchBooks($searchQuery, $userRole);

            // If no books are found for the search query
            if (empty($books)) {
                handleError("No books found for the search query", 404);
            }

            // Return the books if found
            echo json_encode($books);
            exit(); // Make sure to exit after sending the response
        } else {
            // Fetch all books
            $bookAPI->getBooks($userRole);
        }
    }
} else {
    // Return an error for invalid endpoints
    handleError("Invalid API endpoint", 404);
}
?>
