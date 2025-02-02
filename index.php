<?php
//index.php
require_once 'auth.php';  // Include authentication file
require_once 'BookAPI.php'; // Include BookAPI file

// Get the current user role
$userRole = getUserRole(); // Returns 'guest', 'user', or 'admin'

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
            // Send the login success message and stop further execution
            echo json_encode(["message" => "Login successful", "role" => $_SESSION['role']]);
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
    // Book related API actions
    if ($method === 'GET') {
        try {
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
        } catch (Exception $e) {
            // Return 500 Internal Server Error if any exception occurs while fetching books
            handleError("Internal server error while fetching books: " . $e->getMessage());
        }
    }

    // Only allow adding, updating, or deleting books for admins
    if ($method === 'POST' && isAuthorized('admin')) {
        // Check if required book data is provided
        if (empty($_POST['title']) || empty($_POST['author']) || empty($_POST['genre'])) {
            // Return 400 Bad Request if required fields are missing
            handleError("Title, author, and genre are required", 400);
        }

        try {
            // Add the book
            $bookAPI->addBook($userRole);
        } catch (Exception $e) {
            // Return 500 Internal Server Error if something goes wrong while adding the book
            handleError("Internal server error while adding the book: " . $e->getMessage());
        }
    } elseif ($method === 'PUT' && isAuthorized('admin')) {
        // Check if the ID and required data are provided for updating
        if (empty($_GET['id']) || empty($_POST['title']) || empty($_POST['author']) || empty($_POST['genre'])) {
            // Return 400 Bad Request if any required field is missing
            handleError("Book ID, title, author, and genre are required for updating", 400);
        }

        try {
            // Update the book
            $bookAPI->updateBook($_GET['id'], $userRole);
        } catch (Exception $e) {
            // Return 500 Internal Server Error if something goes wrong while updating the book
            handleError("Internal server error while updating the book: " . $e->getMessage());
        }
    } elseif ($method === 'DELETE' && isAuthorized('admin')) {
        try {
            // Delete the book
            $bookAPI->deleteBook($_GET['id'], $userRole);
        } catch (Exception $e) {
            // Return 500 Internal Server Error if something goes wrong while deleting the book
            handleError("Internal server error while deleting the book: " . $e->getMessage());
        }
    }
} else {
    // Return an error for invalid endpoints
    handleError("Invalid API endpoint", 404);
}
