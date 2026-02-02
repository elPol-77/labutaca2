<section id="view-series-full" class="view-section active">
    
    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div id="loading-initial" style="height: 80vh; display:flex; justify-content:center; align-items:center; flex-direction:column;">
        <div class="loader-line" style="width: 50px;"></div>
        <p style="color:#666; margin-top:15px; font-size: 0.9rem;">Cargando catálogo de series...</p>
    </div>

    <main class="content" style="display:none;">
        
        <div id="hero-wrapper"></div>
        <div id="rows-container" class="netflix-container" style="padding-bottom: 50px;"></div>
        
        <div id="genre-landing-container" style="display:none; padding-top: 100px;"></div>
        <div id="grid-expandido" style="display:none;"></div> 

    </main>
</section>