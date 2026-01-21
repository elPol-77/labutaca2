<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    
    <script>
        const BASE_URL = "<?= base_url() ?>/";
        const SHOW_SPLASH = <?= json_encode($splash ?? false) ?>;
    </script>
    
    <script src="<?= base_url('assets/js/front.js') ?>"></script>

    <script>
        const rawData = <?= isset($peliculas) ? json_encode($peliculas) : '[]' ?>;

        if (rawData.length > 0) {
            const moviesFormatted = rawData.map(item => ({
                id: item.id,
                title: item.titulo,
                img: item.imagen.startsWith('http') ? item.imagen : '<?= base_url("assets/img/") ?>' + item.imagen,
                bg: item.imagen_bg.startsWith('http') ? item.imagen_bg : '<?= base_url("assets/img/") ?>' + item.imagen_bg,
                premium: item.nivel_acceso == '2',
                link: '<?= base_url("detalle/") ?>' + item.id
            }));

            $(document).ready(function() {
                // Solo renderizamos si la función existe en front.js
                if(typeof renderGrid === 'function') {
                    renderGrid(moviesFormatted);
                }
            });
        }
    </script>
</body>
</html>