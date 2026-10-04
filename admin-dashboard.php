<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - CINETRACK</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- External CSS files -->
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body>
  <div class="dashboard">
    <!-- Header -->
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
          <span>ADMINISTRATION PANEL</span>
        </div>
      </div>

      <div class="admin-header-actions">
        <span class="admin-badge-header">ADMIN</span>
        <div class="menu-container">
          <button class="menu-btn" id="menuToggleBtn">
            Account
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>
          <ul class="menu-dropdown" id="menuDropdown">
            <li>
              <button class="menu-item logout" id="logoutBtn">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                  <polyline points="16 17 21 12 16 7"/>
                  <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Logout
              </button>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="admin-section">
      <!-- Stats -->
      <div class="admin-grid">
        <div class="stat-card">
          <h3>TOTAL MOVIES</h3>
          <div class="value" id="statMovies">10</div>
        </div>
        <div class="stat-card">
          <h3>REGISTERED USERS</h3>
          <div class="value">128</div>
        </div>
        <div class="stat-card">
          <h3>SYSTEM STATUS</h3>
          <div class="value status-active">● Active</div>
        </div>
      </div>

      <!-- Catalogue Table -->
      <div class="data-table-container">
        <div class="table-header-action">
          <h2>Manage Catalogue</h2>
          <button class="primary-btn" id="openAddModalBtn">+ Add New Film</button>
        </div>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>TITLE</th>
              <th>DIRECTOR</th>
              <th>YEAR</th>
              <th>GENRE</th>
              <th>ACTION</th>
            </tr>
          </thead>
          <tbody id="movieTableBody">
            <!-- Dynamically populated -->
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Add Film Modal -->
  <div class="modal-overlay" id="addFilmModal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Add New Film</h3>
        <button class="close-btn" id="closeAddModalBtn">&times;</button>
      </div>
      <form id="addFilmForm">
        <div class="form-group">
          <label for="filmTitle">MOVIE TITLE</label>
          <input type="text" id="filmTitle" placeholder="e.g. Dune: Part Two" required />
        </div>

        <div class="form-group">
          <label for="filmDirector">DIRECTOR</label>
          <input type="text" id="filmDirector" placeholder="e.g. Denis Villeneuve" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="filmYear">RELEASE YEAR</label>
            <input type="number" id="filmYear" placeholder="2024" required min="1888" max="2100" />
          </div>
          <div class="form-group">
            <label for="filmGenre">GENRE</label>
            <input type="text" id="filmGenre" placeholder="e.g. SCI-FI" required />
          </div>
        </div>

        <div class="form-group">
          <label for="filmNote">NOTES / DESCRIPTION</label>
          <textarea id="filmNote" rows="3" placeholder="Brief summary or commentary..."></textarea>
        </div>

        <div class="modal-actions">
          <button type="button" class="danger-btn" id="cancelModalBtn">Cancel</button>
          <button type="submit" class="primary-btn">Save Film</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Route Security Guard
    if (sessionStorage.getItem("userRole") !== "admin") {
      window.location.href = "login.html";
    }

    const films = [
      { id: 1, title:"Inception", year:2010, director:"Christopher Nolan", genre:"SCI-FI" },
      { id: 2, title:"The Godfather", year:1972, director:"Francis Ford Coppola", genre:"CRIME" },
      { id: 3, title:"Parasite", year:2019, director:"Bong Joon-ho", genre:"THRILLER" },
      { id: 4, title:"Blade Runner 2049", year:2017, director:"Denis Villeneuve", genre:"SCI-FI" },
      { id: 5, title:"Mulholland Drive", year:2001, director:"David Lynch", genre:"MYSTERY" },
      { id: 6, title:"There Will Be Blood", year:2007, director:"Paul Thomas Anderson", genre:"DRAMA" },
      { id: 7, title:"The Shining", year:1980, director:"Stanley Kubrick", genre:"HORROR" },
      { id: 8, title:"Melancholia", year:2011, director:"Lars von Trier", genre:"DRAMA" },
      { id: 9, title:"Nosferatu", year:2024, director:"Robert Eggers", genre:"HORROR" },
      { id: 10, title:"The Witch", year:2015, director:"Robert Eggers", genre:"HORROR" }
    ];

    function renderTable() {
      const tbody = document.getElementById("movieTableBody");
      tbody.innerHTML = "";
      document.getElementById("statMovies").textContent = films.length;

      films.forEach(film => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>#${film.id}</td>
          <td><strong>${film.title}</strong></td>
          <td>${film.director}</td>
          <td>${film.year}</td>
          <td>${film.genre}</td>
          <td><button class="danger-btn" onclick="deleteFilm(${film.id})">Remove</button></td>
        `;
        tbody.appendChild(tr);
      });
    }

    function deleteFilm(id) {
      const index = films.findIndex(f => f.id === id);
      if (index !== -1) {
        films.splice(index, 1);
        renderTable();
      }
    }

    // Modal controls
    const modal = document.getElementById("addFilmModal");
    const openModalBtn = document.getElementById("openAddModalBtn");
    const closeModalBtn = document.getElementById("closeAddModalBtn");
    const cancelModalBtn = document.getElementById("cancelModalBtn");
    const addFilmForm = document.getElementById("addFilmForm");

    function openModal() { modal.classList.add("active"); }
    function closeModal() { 
      modal.classList.remove("active");
      addFilmForm.reset();
    }

    openModalBtn.addEventListener("click", openModal);
    closeModalBtn.addEventListener("click", closeModal);
    cancelModalBtn.addEventListener("click", closeModal);

    // Form submission handler
    addFilmForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const newFilm = {
        id: films.length ? Math.max(...films.map(f => f.id)) + 1 : 1,
        title: document.getElementById("filmTitle").value.trim(),
        director: document.getElementById("filmDirector").value.trim(),
        year: parseInt(document.getElementById("filmYear").value, 10),
        genre: document.getElementById("filmGenre").value.trim().toUpperCase(),
        note: document.getElementById("filmNote").value.trim()
      };

      films.unshift(newFilm);
      renderTable();
      closeModal();
    });

    // Dropdown toggle logic
    const menuToggleBtn = document.getElementById("menuToggleBtn");
    const menuDropdown = document.getElementById("menuDropdown");
    const logoutBtn = document.getElementById("logoutBtn");

    menuToggleBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      menuDropdown.classList.toggle("show");
    });

    document.addEventListener("click", () => menuDropdown.classList.remove("show"));

    logoutBtn.addEventListener("click", () => {
      sessionStorage.clear();
      window.location.href = "login.html";
    });

    renderTable();
  </script>
</body>
</html>
