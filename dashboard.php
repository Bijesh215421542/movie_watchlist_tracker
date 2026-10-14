<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

// Handle status updates via URL parameters (No JavaScript required)
if (isset($_GET['movie_id']) && isset($_GET['status'])) {
    $movieId = (int) $_GET['movie_id'];
    $newStatus = trim($_GET['status']);
    $allowedStatuses = ['none', 'watchlist', 'watched', 'favorite'];

    if ($movieId > 0 && in_array($newStatus, $allowedStatuses, true)) {
        $checkStmt = $pdo->prepare("SELECT status FROM user_movie_status WHERE user_id = ? AND movie_id = ?");
        $checkStmt->execute([$userId, $movieId]);
        $currentRecord = $checkStmt->fetch();
        $currentStatus = $currentRecord ? $currentRecord['status'] : 'none';
        $targetStatus = ($currentStatus === $newStatus) ? 'none' : $newStatus;

        if ($targetStatus === 'none') {
            $stmt = $pdo->prepare("DELETE FROM user_movie_status WHERE user_id = ? AND movie_id = ?");
            $stmt->execute([$userId, $movieId]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO user_movie_status (user_id, movie_id, status)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE status = ?
            ");
            $stmt->execute([$userId, $movieId, $targetStatus, $targetStatus]);
        }
    }

    header("Location: dashboard.php");
    exit;
}
// Fetch movies joined with logged-in user's personal movie status
try {
    $stmt = $pdo->prepare("
        SELECT m.*, COALESCE(ums.status, 'none') AS status
        FROM movies m
        LEFT JOIN user_movie_status ums ON m.id = ums.movie_id AND ums.user_id = ?
        ORDER BY m.id DESC
    ");
    $stmt->execute([$userId]);
    $dbFilms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Automatically fetch real posters for films missing a poster URL
    foreach ($dbFilms as &$film) {
        if (empty($film['poster_url'])) {
            $poster = fetchMoviePoster($film['title'], $film['year'], $pdo, $film['id']);
            if ($poster) {
                $film['poster_url'] = $poster;
            }
        }
    }
    unset($film);
} catch (Exception $e) {
    $dbFilms = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Movie Watchlist Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="dashboard.css" />
</head>
<body>
  <div class="dashboard">
    <header class="topbar">
      <div class="brand">
        <div class="brand-mark">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/>
            <path d="M8 4v16M16 4v16M3 9h5M16 9h5M3 15h5M16 15h5" stroke="currentColor" stroke-width="1.6"/>
          </svg>
        </div>
        <div class="brand-text">
          <h1>🎬 Cinetrack</h1>
          <span>YOUR FILM JOURNAL</span>
        </div>
      </div>
      <div class="menu-container">
        <button class="menu-btn" id="menuToggleBtn">
          Menu
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <ul class="menu-dropdown" id="menuDropdown">
          <li>
            <button class="menu-item" id="randomizeBtn">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/>
              </svg>
              Pick Random 🎲
            </button>
          </li>
          <li class="menu-divider"></li>
          <li>
            <a href="logout.php" class="menu-item logout" style="text-decoration:none;">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
              Logout
            </a>
          </li>
        </ul>
      </div>
    </header>

    <div class="controls">
      <div class="tabs" id="tabs">
        <button class="tab active" data-filter="all">All Films <span class="count">0</span></button>
        <button class="tab" data-filter="watchlist">Watchlist <span class="count">0</span></button>
        <button class="tab" data-filter="watched">Watched <span class="count">0</span></button>
        <button class="tab" data-filter="favorite">Favorites <span class="count">0</span></button>
      </div>
      <div class="search">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
          <path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="1.8"/>
        </svg>
        <input id="searchInput" type="text" placeholder="Search title, director, genre..." />
      </div>
    </div>

    <main class="grid" id="grid"></main>
  </div>

  <button id="backToTopBtn" class="back-to-top" title="Back to Top">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2">
      <polyline points="18 15 12 9 6 15"></polyline>
    </svg>
  </button>

  <script>
    let films = <?php echo json_encode($dbFilms ?: []); ?>;

    const badgeIcons = {
      watched: `<svg viewBox="0 0 24 24" fill="none"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>`,
      favorite: `<svg viewBox="0 0 24 24" fill="none"><path d="M12 21s-7.5-4.6-10-9.3C0.3 8.2 2 4.5 5.8 4c2.3-0.3 4.3 1 6.2 3.2C14 5 16 3.7 18.2 4c3.8 0.5 5.5 4.2 3.8 7.7C19.5 16.4 12 21 12 21z" stroke="currentColor" stroke-width="1.8"/></svg>`,
      watchlist: `<svg viewBox="0 0 24 24" fill="none"><path d="M6 3h12v18l-6-4-6 4V3z" stroke="currentColor" stroke-width="1.8"/></svg>`
    };

    const badgeLabel = { watched: "WATCHED", favorite: "FAVORITE", watchlist: "WATCHLIST" };

    const grid = document.getElementById("grid");
    const searchInput = document.getElementById("searchInput");
    const tabs = document.getElementById("tabs");
    const randomizeBtn = document.getElementById("randomizeBtn");
    const menuToggleBtn = document.getElementById("menuToggleBtn");
    const menuDropdown = document.getElementById("menuDropdown");
    const backToTopBtn = document.getElementById("backToTopBtn");

    const state = { filter: "all", query: "" };

    function getFilteredFilms() {
      const q = state.query.toLowerCase().trim();
      return films.filter(f => {
        const matchesFilter = state.filter === "all" || f.status === state.filter;
        const matchesSearch = !q || [f.title, f.director, f.genre].join(" ").toLowerCase().includes(q);
        return matchesFilter && matchesSearch;
      });
    }

    function updateStats() {
      const counts = document.querySelectorAll(".tab .count");
      counts[0].textContent = films.length;
      counts[1].textContent = films.filter(f => f.status === "watchlist").length;
      counts[2].textContent = films.filter(f => f.status === "watched").length;
      counts[3].textContent = films.filter(f => f.status === "favorite").length;
    }

    function createCard(film) {
      const card = document.createElement("article");
      card.className = "card";
      card.dataset.id = String(film.id);

      const badgeHtml = film.status && film.status !== 'none'
        ? `<div class="badge ${film.status}">
             ${badgeIcons[film.status] || ''}
             ${badgeLabel[film.status] || ''}
           </div>`
        : '';

      const posterStyle = film.poster_url ? `style="background-image: url('${film.poster_url}');"` : '';
      const posterClass = film.poster_url ? 'poster' : `poster ${film.art || 'p1'}`;

      card.innerHTML = `
        <div class="${posterClass}" ${posterStyle}>
          ${badgeHtml}
          <div class="poster-actions">
            <a href="dashboard.php?movie_id=${film.id}&status=watchlist" class="action-btn">Watchlist</a>
            <a href="dashboard.php?movie_id=${film.id}&status=watched" class="action-btn">Watched</a>
            <a href="dashboard.php?movie_id=${film.id}&status=favorite" class="action-btn">Favorite</a>
          </div>
        </div>
        <div class="meta">
          <div class="title-row">
            <h2>${film.title}</h2>
            <span class="year">${film.year}</span>
          </div>
          <div class="director">${film.director}</div>
          <div class="divider"></div>
          <div class="genre">${film.genre}</div>
          <div class="note">${film.note || ''}</div>
        </div>
      `;

      return card;
    }

    function render() {
      updateStats();
      grid.innerHTML = "";
      const filtered = getFilteredFilms();

      if (!filtered.length) {
        grid.innerHTML = `<p class="empty-state">No films match your search.</p>`;
        return;
      }

      filtered.forEach(film => grid.appendChild(createCard(film)));
    }

    tabs.addEventListener("click", (e) => {
      const btn = e.target.closest(".tab");
      if (!btn) return;
      state.filter = btn.dataset.filter;
      document.querySelectorAll(".tab").forEach(t => t.classList.remove("active"));
      btn.classList.add("active");
      render();
    });

    searchInput.addEventListener("input", (e) => {
      state.query = e.target.value;
      render();
    });

    menuToggleBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      menuDropdown.classList.toggle("show");
    });

    randomizeBtn.addEventListener("click", () => {
      const availableFilms = getFilteredFilms();
      if (!availableFilms.length) return;

      document.querySelectorAll(".card.highlighted").forEach(c => c.classList.remove("highlighted"));

      const chosenFilm = availableFilms[Math.floor(Math.random() * availableFilms.length)];
      const cardEl = document.querySelector(`.card[data-id="${chosenFilm.id}"]`);

      if (cardEl) {
        cardEl.classList.add("highlighted");
        cardEl.scrollIntoView({ behavior: "smooth", block: "center" });
      }

      menuDropdown.classList.remove("show");
    });

    window.addEventListener("scroll", () => {
      if (window.scrollY > 300) {
        backToTopBtn.classList.add("visible");
      } else {
        backToTopBtn.classList.remove("visible");
      }
    });

    backToTopBtn.addEventListener("click", () => {
      window.scrollTo({ top: 0, behavior: "smooth" });
    });

    document.addEventListener("click", (e) => {
      if (!e.target.closest(".menu-container")) menuDropdown.classList.remove("show");
    });

    render();
  </script>
</body>
</html>
