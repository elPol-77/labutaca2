<section class="view-section active" style="padding-top: 100px; min-height: 100vh;">
    
    <input type="hidden" class="txt_csrftoken" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

    <div class="container-fluid" style="padding: 0 4%;">
        
        <div style="display:flex; align-items:center; gap:15px; margin-bottom: 30px;">
            <h1 style="color: white; margin:0;">Mi Lista</h1>
            <span style="color: #666; font-size: 1.2rem;">(<?= count($peliculas) ?> títulos)</span>
        </div>

        <?php if (empty($peliculas)): ?>
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height: 50vh; color: #666;">
                <i class="fa fa-folder-open" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.5;"></i>
                <h3 style="color:#ddd;">Tu lista está vacía</h3>
                <p>Agrega películas y series para verlas más tarde.</p>
                <a href="<?= base_url('/') ?>" class="btn-primary" style="margin-top: 20px; padding: 10px 25px; border-radius: 4px; text-decoration: none;">
                    Explorar catálogo
                </a>
            </div>
        <?php else: ?>
            
            <div id="grid-mi-lista" style="
                display: grid; 
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
                gap: 1.5vw; 
                padding-bottom: 50px;">
                </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // 1. Recibimos los datos de PHP en formato JSON
                    const misPelis = <?= json_encode($peliculas) ?>;
                    const container = document.getElementById('grid-mi-lista');
                    
                    let html = '';
                    
                    // 2. Usamos tu función maestra 'generarHtmlTarjeta' (definida en front.js)
                    // para que el diseño sea consistente en toda la web
                    misPelis.forEach(item => {
                        html += generarHtmlTarjeta(item);
                    });
                    
                    // 3. Insertamos el HTML
                    container.innerHTML = html;
                });
            </script>

        <?php endif; ?>
    </div>
</section>