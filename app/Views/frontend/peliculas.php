<section id="view-peliculas-full" class="view-section active">
    
    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div id="loading-initial" style="height: 80vh; display:flex; justify-content:center; align-items:center; flex-direction:column;">
        <div class="loader-line" style="width: 50px;"></div>
        <p style="color:#666; margin-top:15px; font-size: 0.9rem;">Cargando cartelera...</p>
    </div>

    <main class="content" style="display:none;">
        
        <div id="hero-wrapper"></div>
        <div id="rows-container" class="netflix-container" style="padding-bottom: 50px; margin-top: -30px; position: relative; z-index: 10;"></div>
        
        <div id="grid-container" style="display:none;"></div>

    </main>
</section>