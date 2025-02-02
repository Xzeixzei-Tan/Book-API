<?php
//BookAPI.php
require_once 'db_config.php';
require_once 'auth.php';

class BookAPI {
    // Fetch all books with pagination
    public function getBooks($userRole, $page = 1, $perPage = 10) {
        global $conn;
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT books.*, GROUP_CONCAT(genres.name) AS genre_names
                    FROM books
                    JOIN book_genres ON books.id = book_genres.book_id
                    JOIN genres ON book_genres.genre_id = genres.id
                    GROUP BY books.id
                    LIMIT ?, ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $offset, $perPage);
            $stmt->execute();

            $result = $stmt->get_result();
            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }

            // Total books count for pagination
            $countResult = $conn->query("SELECT COUNT(*) AS total FROM books");
            $countRow = $countResult->fetch_assoc();
            $total = $countRow['total'];

            echo json_encode([
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage),
                'books' => $books
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching books", "error" => $e->getMessage()]);
        }
    }

    // Search books by title or author with pagination
    public function searchBooks($query, $userRole, $page = 1, $perPage = 10) {
        global $conn;
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT books.*, GROUP_CONCAT(genres.name) AS genre_names
                    FROM books
                    JOIN book_genres ON books.id = book_genres.book_id
                    JOIN genres ON book_genres.genre_id = genres.id
                    WHERE books.title LIKE ? OR books.author LIKE ?
                    GROUP BY books.id
                    LIMIT ?, ?";
            
            $stmt = $conn->prepare($sql);
            $searchQuery = "%" . $query . "%";
            $stmt->bind_param("ssii", $searchQuery, $searchQuery, $offset, $perPage);
            $stmt->execute();

            $result = $stmt->get_result();
            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }

            // Total books count for pagination
            $countResult = $conn->query("SELECT COUNT(*) AS total FROM books WHERE title LIKE '%$query%' OR author LIKE '%$query%'");
            $countRow = $countResult->fetch_assoc();
            $total = $countRow['total'];

            echo json_encode([
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage),
                'books' => $books
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error searching books", "error" => $e->getMessage()]);
        }
    }

    // Fetch a single book by its ID
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
            $stmt->bind_param("i", $id);  // Bind the book ID
            $stmt->execute();

            $result = $stmt->get_result();
            $book = $result->fetch_assoc();
            
            if ($book) {
                echo json_encode($book);
            } else {
                // Return 404 if no book is found
                http_response_code(404);
                echo json_encode(["message" => "Book not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching book", "error" => $e->getMessage()]);
        }
    }
}
?>
