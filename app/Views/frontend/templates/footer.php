<div class="password-modal" id="modalAuth" style="display:none;">
        <div class="modal-content">
            <h3 id="modalUser" style="margin-top:0; color: white;">Usuario</h3>
            <p style="color:#aaa; margin-bottom:20px;">Introduce tu PIN</p>
            
            <input type="hidden" id="selectedUserId">
            <input type="password" id="passwordInput" class="pin-input" placeholder="••••" autocomplete="off">
            <p class="error-msg" id="errorMsg" style="display:none; color: #ff4757; margin-top: 10px;">Contraseña incorrecta</p>
            
            <div style="display:flex; gap:10px; justify-content:center; margin-top:20px;">
                <button class="btn-cancel" onclick="closeModal()">Cancelar</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script>
        const BASE_URL = "<?= base_url() ?>";
        const SHOW_SPLASH = <?= json_encode($splash ?? false) ?>;
    </script>

    <script src="<?= base_url('assets/js/front.js') ?>"></script>

    <script>
        // Carga inicial del Grid (Datos desde PHP)
        const rawData = <?= isset($peliculas) ? json_encode($peliculas) : '[]' ?>;

        if (rawData && rawData.length > 0) {
            const moviesFormatted = rawData.map(item => ({
                id: item.id,
                title: item.titulo,
                img: item.imagen.startsWith('http') ? item.imagen : '<?= base_url("assets/img/") ?>' + item.imagen,
                bg: item.imagen_bg.startsWith('http') ? item.imagen_bg : '<?= base_url("assets/img/") ?>' + item.imagen_bg,
                premium: item.nivel_acceso == '2',
                age: item.edad_recomendada,
                desc: item.descripcion, 
                in_list: item.en_mi_lista ?? false,
                link_detalle: '<?= base_url("detalle/") ?>' + item.id,
                link_ver: '<?= base_url("ver/") ?>' + item.id
            }));

            $(document).ready(function() {
                if(typeof renderGrid === 'function') {
                    renderGrid(moviesFormatted);
                }
            });
        }
    </script>
</body>
</html>