<section id="view-splash" class="view-section active">
        <div class="splash-logo">LA BUTACA</div>
        <div style="width: 200px;">
            <div class="loader-line"></div>
        </div>
    </section>



    <section id="view-home" class="view-section">
        <nav class="sidebar">
            <div class="sidebar-logo">LB</div>
            <a href="#" class="menu-item active"><i class="fa fa-compass"></i> <span>Explorar</span></a>
            <a href="#" class="menu-item"><i class="fa fa-heart"></i> <span>Favoritos</span></a>
            <a href="#" class="menu-item"><i class="fa fa-ticket"></i> <span>Estrenos</span></a>
            <a href="#" class="menu-item"><i class="fa fa-clock"></i> <span>Historial</span></a>
            <div style="margin-top: auto; width: 100%;">
                 <a href="#" class="menu-item" onclick="logout()"><i class="fa fa-sign-out-alt"></i> <span>Salir</span></a>
            </div>
        </nav>

        <main class="content">
            <div class="top-bar">
                <div style="display: flex; gap: 20px;">
                    <span style="font-weight: 800; font-size: 1.2rem;">Catálogo</span>
                    <span style="color: var(--text-muted);">Series</span>
                </div>
                <div class="search-container">
                    <i class="fa fa-search search-icon"></i>
                    <input id="global-search" class="search-input" type="text" placeholder="Encuentra películas...">
                </div>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="text-align: right;">
                        <div style="font-size: 0.9rem; font-weight: bold;">Hola, Admin</div>
                        <div style="font-size: 0.7rem; color: var(--premium);">Plan Premium</div>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 50%; background-image: url('https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80'); background-size: cover;"></div>
                </div>
            </div>

            <div class="hero-card">
                <div class="hero-info">
                    <div class="hero-badges">
                        <span class="badge badge-premium"><i class="fa fa-crown"></i> Premium</span>
                        <span class="badge badge-hd">4K Ultra HD</span>
                    </div>
                    <h1>Dune: Parte Dos</h1>
                    <p style="max-width: 500px; color: #ddd; margin-bottom: 2rem;">Paul Atreides se une a Chani y a los Fremen mientras busca venganza.</p>
                    <button class="btn-action btn-primary"><i class="fa fa-play"></i> Ver Ahora</button>
                </div>
            </div>

            <h3 class="section-title">Tendencias Ahora</h3>
            <div class="movie-grid" id="grid-container">
                </div>
        </main>
    </section>