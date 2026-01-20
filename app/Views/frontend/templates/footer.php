<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    
    <script src="<?= base_url('assets/js/front.js') ?>"></script>

    <script>
        
        // PUENTE DE DATOS: PHP (Base de Datos) -> JavaScript (Frontend)
        const baseUrl = '<?= base_url() ?>';
        const rawData = <?= json_encode($peliculas) ?>;

        // Adaptamos los datos para que tu JS los entienda
        const moviesFormatted = rawData.map(item => ({
            id: item.id,
            title: item.titulo,
            // Si la imagen empieza por http es url, si no es local en assets
            img: item.imagen.startsWith('http') ? item.imagen : '<?= base_url("assets/img/") ?>' + item.imagen,
            bg: item.imagen_bg,
            premium: item.nivel_acceso == '2', // Si es 2 es premium
            link: '<?= base_url("ver/") ?>' + item.id
        }));

        // Cuando la página esté lista, renderizamos el grid
        $(document).ready(function() {
            renderGrid(moviesFormatted);
        });
    </script>
</body>
</html>