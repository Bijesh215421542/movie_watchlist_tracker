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
    <section>
      <ul id="movieList"></ul>
    </section>

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
            <button class="menu-item logout" id="logoutBtn">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="circle" stroke-linejoin="circle">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
              Logout<br>
              Sign out 
            </button> 
          </li>
        </ul>
      </div>
    </header>
    <div class="controls">
      <div class="tabs" id="tabs">
        <button class="tab active" data-filter="all">All Films <span class="count">10</span></button>
        <button class="tab" data-filter="watchlist">Watchlist <span class="count">3</span></button>
        <button class="tab" data-filter="watched">Watched <span class="count">4</span></button>
        <button class="tab" data-filter="favorite">Favorites <span class="count">3</span></button>
      </div>
      <div class="search">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
          <path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="circle" stroke-linejoin="circle"/>
        </svg>
        <input id="searchInput" type="text" placeholder="Search title, director, genre..." />
      </div>
    </div>

    <main class="grid" id="grid"></main>
  </div>

  <!-- Back to Top Button -->
  <button id="backToTopBtn" class="back-to-top" title="Back to Top">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="circle" stroke-linejoin="circle">
      <polyline points="18 15 12 9 6 15"></polyline>
    </svg>
  </button>

  <script>
    const films = [
      { id: 1, title:"Inception", year:2010, director:"Christopher Nolan", genre:"SCI-FI", note:"Mind-bending architecture of dreams. The spinning top never lies.", status:"watched", art:"p1" },
      { id: 2, title:"The Godfather", year:1972, director:"Francis Ford Coppola", genre:"CRIME", note:"An offer I could not refuse. Perfect in every scene.", status:"favorite", art:"p2" },
      { id: 3, title:"Parasite", year:2019, director:"Bong Joon-ho", genre:"THRILLER", note:"Layers within layers. The stone metaphor.", status:"watched", art:"p3" },
      { id: 4, title:"Blade Runner 2049", year:2017, director:"Denis Villeneuve", genre:"SCI-FI", note:"Deakins at his absolute peak. The bee sequence.", status:"favorite", art:"p4" },
      { id: 5, title:"Mulholland Drive", year:2001, director:"David Lynch", genre:"MYSTERY", note:"Lynch at his most opaque. Need a clear evening.", status:"watchlist", art:"p5" },
      { id: 6, title:"There Will Be Blood", year:2007, director:"Paul Thomas Anderson", genre:"DRAMA", note:"Daniel Day-Lewis. 158 minutes of American myth.", status:"watchlist", art:"p6" },
      { id: 7, title:"The Shining", year:1980, director:"Stanley Kubrick", genre:"HORROR", note:"The Overlook never lets go. Rewatched for the score.", status:"watched", art:"p7" },
      { id: 8, title:"Melancholia", year:2011, director:"Lars von Trier", genre:"DRAMA", note:"The end of the world, slow and beautiful.", status:"favorite", art:"p8" },
      { id: 9, title:"Nosferatu", year:2024, director:"Robert Eggers", genre:"HORROR", note:"A shadow that grows heavier with every frame.", status:"watched", art:"p9" },
      { id: 10, title:"The Witch", year:2015, director:"Robert Eggers", genre:"HORROR", note:"Waiting for a quiet night in the woods.", status:"watchlist", art:"p10" },
    ];

    const badgeIcons = {
      watched: `<svg viewBox="0 0 24 24" fill="none"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>`,
      favorite: `<svg viewBox="0 0 24 24" fill="none"><path d="M12 21s-7.5-4.6-10-9.3C0.3 8.2 2 4.5 5.8 4c2.3-0.3 4.3 1 6.2 3.2C14 5 16 3.7 18.2 4c3.8 0.5 5.5 4.2 3.8 7.7C19.5 16.4 12 21 12 21z" stroke="currentColor" stroke-width="1.8"/></svg>`,
      watchlist: `<svg viewBox="0 0 24 24" fill="none"><path d="M6 3h12v18l-6-4-6 4V3z" stroke="currentColor" stroke-width="1.8"/></svg>`
    };

    const badgeLabel = {
      watched: "WATCHED",
      favorite: "FAVORITE",
      watchlist: "WATCHLIST"
    };

    const grid = document.getElementById("grid");
    const searchInput = document.getElementById("searchInput");
    const tabs = document.getElementById("tabs");
    const randomizeBtn = document.getElementById("randomizeBtn");
    const logoutBtn = document.getElementById("logoutBtn");
    const menuToggleBtn = document.getElementById("menuToggleBtn");
    const menuDropdown = document.getElementById("menuDropdown");
    const backToTopBtn = document.getElementById("backToTopBtn");

    const state = {
      filter: "all",
      query: ""
    };

    function normalize(value) {
      return value.toLowerCase().trim();
    }

    function getFilteredFilms() {
      const q = normalize(state.query);
      return films.filter(f => {
        const matchesFilter = state.filter === "all" || f.status === state.filter;
        const matchesSearch = !q || [f.title, f.director, f.genre].join(" ").toLowerCase().includes(q);
        return matchesFilter && matchesSearch;
      });
    }

    function updateStats() {
      const watched = films.filter(f => f.status === "watched").length;
      const watchlist = films.filter(f => f.status === "watchlist").length;
      const favorite = films.filter(f => f.status === "favorite").length;

      document.querySelectorAll(".tab .count")[0].textContent = films.length;
      document.querySelectorAll(".tab .count")[1].textContent = watchlist;
      document.querySelectorAll(".tab .count")[2].textContent = watched;
      document.querySelectorAll(".tab .count")[3].textContent = favorite;
    }

function createCard(film) {
      const card = document.createElement("article");
      card.className = "card";
      card.dataset.id = film.id;

      card.innerHTML = `
        <div class="poster ${film.art}">
          <div class="badge ${film.status}">
            ${badgeIcons[film.status] || ''}
            ${badgeLabel[film.status] || ''}
          </div>
          <!-- New hover overlay for buttons -->
          <div class="poster-actions">
            <button class="action-btn" data-status="watchlist">Watchlist</button>
            <button class="action-btn" data-status="watched">Watched</button>
            <button class="action-btn" data-status="favorite">Favorite</button>
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
          <!-- Action buttons were removed from here -->
          <button class="toggle-desc-btn">Show Description</button>
          <div class="description-container">
            <div class="note">${film.note}</div>
          </div>
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

      const fragment = document.createDocumentFragment();
      filtered.forEach(film => fragment.appendChild(createCard(film)));
      grid.appendChild(fragment);
    }

    function setFilmStatus(id, status) {
      const film = films.find(f => f.id === id);
      if (!film) return;
      film.status = film.status === status ? "none" : status;
      render();
    }

    function toggleMenu() {
      menuDropdown.classList.toggle("show");
    }

    function closeMenu() {
      menuDropdown.classList.remove("show");
    }

    // Grid Event Delegation (Status update & Description toggle)
    grid.addEventListener("click", (e) => {
      const actionBtn = e.target.closest(".action-btn");
      if (actionBtn) {
        const card = actionBtn.closest(".card");
        const filmId = parseInt(card.dataset.id, 10);
        setFilmStatus(filmId, actionBtn.dataset.status);
        return;
      }

      const descBtn = e.target.closest(".toggle-desc-btn");
      if (descBtn) {
        const containerEl = descBtn.nextElementSibling;
        const isHidden = containerEl.classList.toggle("hidden");
        descBtn.textContent = isHidden ? "Show Description" : "Hide Description";
      }
    });

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
      toggleMenu();
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
      closeMenu();
    });

    logoutBtn.addEventListener("click", () => {
      window.location.href = "login.html";
    });

    // Back to Top functionality
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
      if (!e.target.closest(".menu-container")) closeMenu();
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") closeMenu();
    });

    render();
  </script>
</body>
</html>
