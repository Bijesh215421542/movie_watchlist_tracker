<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php_input'), true) ?? $_POST;

try {
    switch ($action) {
        case 'update_status':
            $movieId = intval($input['movie_id'] ?? 0);
            $status = $input['status'] ?? 'none';
            $userId = $_SESSION['user_id'];

            if (!$movieId) {
                throw new Exception("Invalid Movie ID");
            }

            $stmt = $pdo->prepare("
                INSERT INTO user_movie_status (user_id, movie_id, status) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE status = VALUES(status)
            ");
            $stmt->execute([$userId, $movieId, $status]);

            echo json_encode(['success' => true, 'status' => $status]);
            break;

        case 'add_film':
            if ($_SESSION['role'] !== 'admin') {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Forbidden']);
                exit;
            }

            $title = trim($input['title'] ?? '');
            $director = trim($input['director'] ?? '');
            $year = intval($input['year'] ?? 0);
            $genre = strtoupper(trim($input['genre'] ?? ''));
            $note = trim($input['note'] ?? 'No description provided.');
            $art = 'p' . rand(1, 10);

            if (!$title || !$director || !$year || !$genre) {
                throw new Exception("Missing required movie fields");
            }

            $stmt = $pdo->prepare("INSERT INTO movies (title, director, year, genre, note, art) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $director, $year, $genre, $note, $art]);
            $newId = $pdo->lastInsertId();

            echo json_encode([
                'success' => true, 
                'movie' => [
                    'id' => $newId, 
                    'title' => $title, 
                    'director' => $director, 
                    'year' => $year, 
                    'genre' => $genre, 
                    'note' => $note, 
                    'art' => $art,
                    'status' => 'none'
                ]
            ]);
            break;

        case 'delete_film':
            if ($_SESSION['role'] !== 'admin') {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Forbidden']);
                exit;
            }

            $movieId = intval($input['movie_id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
            $stmt->execute([$movieId]);

            echo json_encode(['success' => true]);
            break;

        case 'reset_films':
            if ($_SESSION['role'] !== 'admin') {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Forbidden']);
                exit;
            }

            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE movies; SET FOREIGN_KEY_CHECKS = 1;");
            $stmt = $pdo->prepare("INSERT INTO movies (id, title, director, year, genre, note, art) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $defaults = [
                [1, "Inception", "Christopher Nolan", 2010, "SCI-FI", "Mind-bending architecture of dreams.", "p1"],
                [2, "The Godfather", "Francis Ford Coppola", 1972, "CRIME", "An offer I could not refuse.", "p2"],
                [3, "Parasite", "Bong Joon-ho", 2019, "THRILLER", "Layers within layers.", "p3"],
                [4, "Blade Runner 2049", "Denis Villeneuve", 2017, "SCI-FI", "Deakins at his peak.", "p4"],
                [5, "Mulholland Drive", "David Lynch", 2001, "MYSTERY", "Lynch at his most opaque.", "p5"]
            ];

            foreach ($defaults as $row) {
                $stmt->execute($row);
            }

            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
