<?php
require_once 'db_config.php';
require_once 'auth.php';

class BookAPI {
    // Fetch all books (accessible to guest, user, and admin)
    public function getBooks($userRole) {
        global $conn;
        try {
            $sql = "SELECT books.*, GROUP_CONCAT(genres.name) AS genre_names
                    FROM books
                    JOIN book_genres ON books.id = book_genres.book_id
                    JOIN genres ON book_genres.genre_id = genres.id
                    GROUP BY books.id";
            $result = $conn->query($sql);

            if (!$result) {
                throw new Exception("Database query failed: " . $conn->error);
            }

            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }

            echo json_encode($books);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching books", "error" => $e->getMessage()]);
        }
    }

    // Fetch books by genre (accessible to guest, user, and admin)
    public function getBooksByGenre($genre_ids, $userRole) {
        global $conn;
        try {
            if (is_string($genre_ids)) {
                $genre_ids = explode(',', $genre_ids);
            }

            $placeholders = implode(',', array_fill(0, count($genre_ids), '?'));
            $sql = "SELECT books.*, GROUP_CONCAT(genres.name) AS genre_names
                    FROM books
                    JOIN book_genres ON books.id = book_genres.book_id
                    JOIN genres ON book_genres.genre_id = genres.id
                    WHERE book_genres.genre_id IN ($placeholders)
                    GROUP BY books.id";

            $stmt = $conn->prepare($sql);
            $types = str_repeat('i', count($genre_ids));
            $stmt->bind_param($types, ...$genre_ids);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $books = [];
                while ($row = $result->fetch_assoc()) {
                    $books[] = $row;
                }
                echo json_encode($books);
            } else {
                echo json_encode(["message" => "No books found for the selected genres"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching books by genre", "error" => $e->getMessage()]);
        }
    }

    // Search books by title or author (accessible to guest, user, and admin)
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
            if ($result->num_rows > 0) {
                $books = [];
                while ($row = $result->fetch_assoc()) {
                    $books[] = $row;
                }
                echo json_encode($books);
            } else {
                echo json_encode(["message" => "No books found for the search query"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error searching books", "error" => $e->getMessage()]);
        }
    }

    // Add a new book (restricted to admin only)
    public function addBook($userRole) {
        if ($userRole !== 'admin') {
            echo json_encode(["message" => "Only admins can add books."]);
            return;
        }

        global $conn;
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['title']) || empty($data['author']) || empty($data['published_date']) || empty($data['description']) || empty($data['genre_ids'])) {
            echo json_encode(["message" => "Error adding book", "error" => "Missing required fields"]);
            return;
        }

        $title = $data['title'];
        $author = $data['author'];
        $published_date = $data['published_date'];
        $description = $data['description'];
        $genre_ids = $data['genre_ids'];

        $sql = "SELECT id FROM books WHERE title = ? AND author = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $title, $author);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["message" => "This book already exists."]);
            return;
        }

        $sql = "INSERT INTO books (title, author, published_date, description) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $title, $author, $published_date, $description);
        if (!$stmt->execute()) {
            echo json_encode(["message" => "Error adding book", "error" => $stmt->error]);
            return;
        }

        $book_id = $stmt->insert_id;

        foreach ($genre_ids as $genre_id) {
            $sql = "INSERT INTO book_genres (book_id, genre_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $book_id, $genre_id);
            if (!$stmt->execute()) {
                echo json_encode(["message" => "Error adding book genre", "error" => $stmt->error]);
                return;
            }
        }

        echo json_encode(["message" => "Book added successfully"]);
    }

    // Update a book (restricted to admin only)
    public function updateBook($id, $userRole) {
        if ($userRole !== 'admin') {
            echo json_encode(["message" => "Only admins can update books."]);
            return;
        }

        global $conn;
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['title']) || empty($data['author']) || empty($data['published_date']) || empty($data['description']) || empty($data['genre_ids'])) {
                throw new Exception("Missing required fields");
            }

            $title = $data['title'];
            $author = $data['author'];
            $published_date = $data['published_date'];
            $description = $data['description'];
            $genre_ids = $data['genre_ids'];

            $sql = "UPDATE books SET title = ?, author = ?, published_date = ?, description = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $title, $author, $published_date, $description, $id);
            $stmt->execute();

            $sql = "DELETE FROM book_genres WHERE book_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            foreach ($genre_ids as $genre_id) {
                $sql = "INSERT INTO book_genres (book_id, genre_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $id, $genre_id);
                $stmt->execute();
            }

            echo json_encode(["message" => "Book updated successfully"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error updating book", "error" => $e->getMessage()]);
        }
    }

    // Delete a book (restricted to admin only)
    public function deleteBook($id, $userRole) {
        if ($userRole !== 'admin') {
            echo json_encode(["message" => "Only admins can delete books."]);
            return;
        }

        global $conn;
        try {
            $sql = "DELETE FROM books WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode(["message" => "Book deleted successfully"]);
            } else {
                echo json_encode(["message" => "Book not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error deleting book", "error" => $e->getMessage()]);
        }
    }
}
?>
