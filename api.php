<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Security check: Only admins can perform modify actions
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit;
}

$action = $_GET['action'] ?? '';
$input  = json_decode(file_get_contents('php://input'), true);

// ADD FILM
if ($action === 'add_film') {
    $title    = trim($input['title'] ?? '');
    $director = trim($input['director'] ?? '');
    $year     = (int)($input['year'] ?? 0);
    $genre    = trim($input['genre'] ?? '');
    $note     = trim($input['note'] ?? '');

    if (!$title || !$director || !$year || !$genre) {
        echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO movies (title, director, year, genre, note) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $director, $year, $genre, $note]);
        $newId = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'movie'   => [
                'id'       => (int)$newId,
                'title'    => $title,
                'director' => $director,
                'year'     => $year,
                'genre'    => $genre,
                'note'     => $note
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// DELETE FILM
if ($action === 'delete_film') {
    $movieId = (int)($input['movie_id'] ?? 0);

    if (!$movieId) {
        echo json_encode(['success' => false, 'error' => 'Invalid movie ID provided.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->execute([$movieId]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// RESET FILMS
if ($action === 'reset_films') {
    try {
        $pdo->exec("TRUNCATE TABLE movies");
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
