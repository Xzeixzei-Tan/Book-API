<?php
// BookAPI.php
require_once 'db_config.php';
require_once 'auth.php';

class BookAPI {
    // Fetch all books with pagination
    public function getBooks($userRole) {
        global $conn;
        
        // Default pagination values
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default to 10 items per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default to page 1
        $offset = ($page - 1) * $limit; // Calculate the offset for the SQL query

        try {
            // Query to get the total number of books
            $totalQuery = "SELECT COUNT(*) AS total_books FROM books";
            $totalResult = $conn->query($totalQuery);
            $totalBooks = $totalResult->fetch_assoc()['total_books'];
            
            // Query to fetch books with pagination
            $sql = "SELECT books.*, GROUP_CONCAT(genres.name) AS genre_names
                    FROM books
                    JOIN book_genres ON books.id = book_genres.book_id
                    JOIN genres ON book_genres.genre_id = genres.id
                    GROUP BY books.id
                    LIMIT $limit OFFSET $offset";
            $result = $conn->query($sql);

            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }

            // Calculate total pages
            $totalPages = ceil($totalBooks / $limit);

            // Response with pagination details
            echo json_encode([
                'books' => $books,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_books' => $totalBooks,
                    'limit' => $limit
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching books", "error" => $e->getMessage()]);
        }
    }

    // Fetch a book by ID
    public function getBookById($id, $userRole) {
        global $conn;
        try {
            $sql = "SELECT books.*, GROUP_CONCAT(genres.name) AS genre_names
                    FROM books
                    JOIN book_genres ON books.id = book_genres.book_id
                    JOIN genres ON book_genres.genre_id = genres.id
                    WHERE books.id = ?
                    GROUP BY books.id";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $book = $result->fetch_assoc();

            if ($book) echo json_encode($book);
            else {
                http_response_code(404);
                echo json_encode(["message" => "Book not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching book", "error" => $e->getMessage()]);
        }
    }

    // Fetch books by genre ID
    public function getBooksByGenre($genreId, $userRole) {
        global $conn;
        try {
            // Log the genre ID for debugging
            file_put_contents('php://stderr', "Fetching books for Genre ID: $genreId\n");

            // SQL query to fetch books by genre ID
            $sql = "SELECT books.*, GROUP_CONCAT(genres.name) AS genre_names
                    FROM books
                    JOIN book_genres ON books.id = book_genres.book_id
                    JOIN genres ON book_genres.genre_id = genres.id
                    WHERE genres.id = ?
                    GROUP BY books.id";

            // Prepare the statement and bind the genre ID parameter
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $genreId); // 'i' for integer type

            // Execute the query
            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch books and store them in an array
            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }

            // Return response
            if (empty($books)) {
                echo json_encode(['message' => 'No books found for this genre']);
            } else {
                echo json_encode(['books' => $books]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching books by genre ID", "error" => $e->getMessage()]);
        }
    }



    // Search books
    public function searchBooks($query, $userRole) {
        global $conn;
        try {
            $sql = "SELECT books.*, GROUP_CONCAT(genres.name) AS genre_names
                    FROM books
                    JOIN book_genres ON books.id = book_genres.book_id
                    JOIN genres ON book_genres.genre_id = genres.id
                    WHERE books.title LIKE ? OR books.author LIKE ?
                    GROUP BY books.id";
            
            $stmt = $conn->prepare($sql);
            $searchQuery = "%" . $query . "%";
            $stmt->bind_param("ss", $searchQuery, $searchQuery);
            $stmt->execute();
            $result = $stmt->get_result();

            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }

            if (count($books) > 0) {
                echo json_encode(['books' => $books]);
            } else {
                echo json_encode(["message" => "No books found matching your search query."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error searching books", "error" => $e->getMessage()]);
        }
    }

    // Add a new book
    public function addBook($input) {
        global $conn;

        // Check if the book already exists
        $sql = "SELECT * FROM books WHERE title = ? AND author = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $input['title'], $input['author']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If the book already exists
            echo json_encode(["message" => "This book already exists."]);
            return;
        }

        // Insert the new book into the books table
        $sql = "INSERT INTO books (title, author, published_date, description) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $input['title'], $input['author'], $input['published_date'], $input['description']);
        $stmt->execute();

        // Get the inserted book's ID
        $bookId = $stmt->insert_id;

        // Insert genres into the book_genres table
        foreach ($input['genre'] as $genre) {
            // Check if the genre exists in the genres table
            $genreSql = "SELECT id FROM genres WHERE name = ?";
            $genreStmt = $conn->prepare($genreSql);
            $genreStmt->bind_param("s", $genre);
            $genreStmt->execute();
            $genreResult = $genreStmt->get_result();
            
            if ($genreResult->num_rows > 0) {
                $genreRow = $genreResult->fetch_assoc();
                $genreId = $genreRow['id'];
            } else {
                // If genre doesn't exist, you may insert it (optional)
                $genreSql = "INSERT INTO genres (name) VALUES (?)";
                $genreStmt = $conn->prepare($genreSql);
                $genreStmt->bind_param("s", $genre);
                $genreStmt->execute();
                $genreId = $genreStmt->insert_id;
            }

            // Insert into book_genres table to link book and genre
            $bookGenreSql = "INSERT INTO book_genres (book_id, genre_id) VALUES (?, ?)";
            $bookGenreStmt = $conn->prepare($bookGenreSql);
            $bookGenreStmt->bind_param("ii", $bookId, $genreId);
            $bookGenreStmt->execute();
        }

        echo json_encode(["message" => "Book added successfully."]);
    }

    // Update a book by ID
    public function updateBook($id, $input) {
        global $conn;

        // Check if the book exists
        $sql = "SELECT * FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(["message" => "Book not found."]);
            return;
        }

        // Update the book information
        $sql = "UPDATE books SET title = ?, author = ?, published_date = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $input['title'], $input['author'], $input['published_date'], $input['description'], $id);
        $stmt->execute();

        // Now, update the genres if provided
        // First, clear existing genres
        $sql = "DELETE FROM book_genres WHERE book_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Insert the new genres
        foreach ($input['genre'] as $genre) {
            // Check if the genre exists
            $genreSql = "SELECT id FROM genres WHERE name = ?";
            $genreStmt = $conn->prepare($genreSql);
            $genreStmt->bind_param("s", $genre);
            $genreStmt->execute();
            $genreResult = $genreStmt->get_result();

            if ($genreResult->num_rows > 0) {
                $genreRow = $genreResult->fetch_assoc();
                $genreId = $genreRow['id'];
            } else {
                // If genre doesn't exist, insert it
                $genreSql = "INSERT INTO genres (name) VALUES (?)";
                $genreStmt = $conn->prepare($genreSql);
                $genreStmt->bind_param("s", $genre);
                $genreStmt->execute();
                $genreId = $genreStmt->insert_id;
            }

            // Link the book to the genre
            $bookGenreSql = "INSERT INTO book_genres (book_id, genre_id) VALUES (?, ?)";
            $bookGenreStmt = $conn->prepare($bookGenreSql);
            $bookGenreStmt->bind_param("ii", $id, $genreId);
            $bookGenreStmt->execute();
        }

        echo json_encode(["message" => "Book updated successfully."]);
    }

    public function deleteBook($id) {
        global $conn;
    
        // Check if the book exists
        $sql = "SELECT * FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 0) {
            echo json_encode(["message" => "Book not found."]);
            return;
        }
    
        // Delete the book
        $sql = "DELETE FROM books WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        // Optionally, delete related genres from book_genres
        $sql = "DELETE FROM book_genres WHERE book_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        echo json_encode(["message" => "Book deleted successfully."]);
    }
    




}
?>
