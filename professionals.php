<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Professionals - The Serene Finder</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .explore-layout { display: flex; gap: 30px; max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .filter-sidebar { flex: 0 0 280px; background: white; padding: 25px; border-radius: 12px; border: 1px solid #e1e8eb; height: fit-content; }
        .filter-group { margin-bottom: 20px; }
        .filter-group label { display: block; font-weight: 600; margin-bottom: 8px; color: var(--bg-dark); }
        .filter-group input[type="text"], .filter-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; }
        .filter-group input[type="range"] { width: 100%; margin-top: 10px; }
        .pro-grid { flex: 1; display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; align-content: start; }
        .pro-card { background: white; border: 1px solid #e1e8eb; border-radius: 12px; padding: 20px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
        .pro-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: var(--primary-teal); }
    </style>
</head>
<body class="light-theme">

    <header>
        <div class="logo" onclick="window.location.href='index.html'" style="cursor: pointer;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="#2c636b"><path d="M12 2L2 22h20L12 2z"/></svg>
            The Serene Finder
        </div>
        <nav>
            <ul>
                <li><a href="index.html" style="text-decoration: none; color: inherit;">Home</a></li>
                <li style="font-weight: 700;">Explore</li>
            </ul>
        </nav>
        <div class="nav-actions">
            <button onclick="window.location.href='login.html'" class="btn-primary" style="background: transparent; color: var(--bg-dark); border: 1px solid #ccc;">Sign In</button>
        </div>
    </header>

    <div class="explore-layout">
        
        <aside class="filter-sidebar">
            <h3 style="margin-top: 0; margin-bottom: 20px;">Filters</h3>
            
            <div class="filter-group">
                <label>Search Keyword</label>
                <div style="display: flex; gap: 5px;">
                    <input type="text" id="searchInput" placeholder="Name or skill...">
                </div>
            </div>

            <div class="filter-group">
                <label>Category</label>
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="HVAC">HVAC & Plumbing</option>
                    <option value="Electri">Electrical</option>
                    <option value="Clean">Cleaning & Care</option>
                    <option value="Repair">General Repair</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Max Hourly Rate: <span id="priceLabel" style="color: var(--primary-teal);">$100</span></label>
                <input type="range" id="priceFilter" min="10" max="200" step="5" value="100">
            </div>

            <button id="searchBtn" class="btn-block btn-book" style="margin-top: 10px;">Apply Filters</button>
        </aside>

        <main class="pro-grid" id="providersGrid">
            </main>

    </div>

    <script>
        // --- GLOBAL AUTH CHECKER ---
        async function runAuthCheck() {
            try {
                const response = await fetch('api/check_auth.php');
                const auth = await response.json();
                const navActions = document.querySelector('.nav-actions');
                
                if (auth.loggedIn && navActions) {
                    navActions.innerHTML = `
                        <button onclick="window.location.href='dashboard.html'" class="btn-primary" style="background: var(--primary-teal); color: white; border: none; margin-right: 10px;">Dashboard</button>
                        <button id="globalLogoutBtn" class="btn-primary" style="background: transparent; color: var(--bg-dark); border: 1px solid #ccc;">Log Out</button>
                    `;
                    document.getElementById('globalLogoutBtn').addEventListener('click', async () => {
                        await fetch('api/logout.php');
                        window.location.reload(); 
                    });
                }
            } catch (error) { console.error("Auth check failed", error); }
        }
        window.addEventListener("pageshow", runAuthCheck);
        document.addEventListener("DOMContentLoaded", runAuthCheck);

        // --- SEARCH ENGINE LOGIC ---
        async function loadProfessionals() {
            const grid = document.getElementById('providersGrid');
            grid.innerHTML = '<p style="color: var(--text-muted); grid-column: 1/-1; text-align: center;">Searching...</p>';

            // Grab the values from the sidebar
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            const maxPrice = document.getElementById('priceFilter').value;

            // Update the visual price label
            document.getElementById('priceLabel').innerText = `$${maxPrice}`;

            // Build the dynamic URL
            const url = `api/search_providers.php?search=${encodeURIComponent(search)}&category=${encodeURIComponent(category)}&max_price=${maxPrice}`;

            try {
                const response = await fetch(url);
                const data = await response.json();

                if (data.success && data.providers.length > 0) {
                    let html = '';
                    data.providers.forEach(p => {
                        // Math for Stars
                        const rating = parseFloat(p.rating || 0);
                        const stars = '★'.repeat(Math.round(rating)) + '☆'.repeat(5 - Math.round(rating));
                        const initial = p.full_name.charAt(0).toUpperCase();

                        html += `
                        <div class="pro-card" onclick="window.location.href='profile.html?id=${p.id}'">
                            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                                <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--primary-teal); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold;">
                                    ${initial}
                                </div>
                                <div>
                                    <h3 style="margin: 0; font-size: 1.1rem; color: var(--bg-dark);">${p.full_name}</h3>
                                    <div style="color: var(--text-muted); font-size: 0.85rem;">${p.specialty || 'Service Provider'}</div>
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-size: 0.85rem;">
                                <div style="color: #ffd700; letter-spacing: 2px;">${stars} <span style="color: var(--text-muted); letter-spacing: normal;">(${p.reviews_count || 0})</span></div>
                                <div style="color: var(--text-muted);">📍 ${p.location || 'Remote'}</div>
                            </div>
                            
                            <div style="font-weight: 600; color: var(--primary-teal); font-size: 1.1rem;">
                                $${p.hourly_rate || '0.00'} <span style="font-size: 0.8rem; font-weight: normal; color: var(--text-muted);">/ hr</span>
                            </div>
                        </div>
                        `;
                    });
                    grid.innerHTML = html;
                } else {
                    grid.innerHTML = `<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted);">No professionals found matching your filters. Try adjusting your search!</div>`;
                }
            } catch (error) {
                console.error("Search failed", error);
                grid.innerHTML = `<p style="color: red; grid-column: 1/-1;">Error connecting to database.</p>`;
            }
        }

        // --- EVENT LISTENERS ---
        // Run search immediately when the page loads
        document.addEventListener('DOMContentLoaded', loadProfessionals);
        
        // Run search when the big button is clicked
        document.getElementById('searchBtn').addEventListener('click', loadProfessionals);
        
        // Run search when user presses 'Enter' in the text box
        document.getElementById('searchInput').addEventListener('keyup', (e) => {
            if (e.key === 'Enter') loadProfessionals();
        });

        // Run search instantly when the slider is dragged
        document.getElementById('priceFilter').addEventListener('input', loadProfessionals);
        
        // Run search instantly when a category is picked
        document.getElementById('categoryFilter').addEventListener('change', loadProfessionals);

    </script>
</body>
</html>