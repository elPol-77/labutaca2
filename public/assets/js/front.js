$(document).ready(function() {
    // 1. Animación Splash
    $('#loader').css('width', '100%');
    setTimeout(() => {
        $('#view-splash').fadeOut(500, function(){
            $('#view-home').fadeIn().css('display', 'flex');
        });
    }, 1000);

    // 2. Lógica del Grid y Hover (Se ejecutará cuando recibamos los datos)
    window.renderGrid = function(movies) {
        const grid = $('#grid-container');
        movies.forEach(m => {
            const badge = m.premium ? '<span class="badge-premium"><i class="fa fa-crown"></i> PRO</span>' : '';
            // Ajustamos ruta de imagen si es local
            const card = `
                <a href="${m.link}" class="movie-poster" data-bg="${m.bg}" style="display:block;">
                    ${badge}
                    <img src="${m.img}" alt="${m.title}">
                    <div class="poster-info">
                        <h4>${m.title}</h4>
                    </div>
                </a>
            `;
            grid.append(card);
        });
    };

    // 3. Efecto Fondo
    $(document).on('mouseenter', '.movie-poster', function() {
        const bg = $(this).data('bg');
        if(bg) $('#dynamic-bg').css('background-image', `url(${bg})`);
    });

    // public/assets/js/front.js

$(document).ready(function() {
    // 1. ANIMACIÓN DE CARGA INICIAL
    setTimeout(() => {
        $('.loader-line').css('width', '100%');
    }, 100);

    setTimeout(() => {
        $('#view-splash').fadeOut(600, function() {
            $(this).removeClass('active');
            $('#view-profiles').addClass('active').hide().fadeIn(600).css('display', 'flex');
        });
    }, 2000);

    // 2. EFECTO INMERSIVO (Cambio de fondo al hacer hover)
    // Usamos delegación de eventos porque el grid se carga dinámicamente
    $(document).on('mouseenter', '.movie-poster', function() {
        const bgUrl = $(this).data('bg');
        if(bgUrl) {
            $('#dynamic-bg').css('background-image', `url(${bgUrl})`).css('opacity', '0.6');
        }
    });

    $(document).on('mouseleave', '.movie-poster', function() {
        $('#dynamic-bg').css('opacity', '0.4');
    });
});

// FUNCIÓN PARA RENDERIZAR EL GRID (Llamada desde el Footer)
window.renderGrid = function(movies) {
    const grid = $('#grid-container');
    
    // Autocomplete Setup
    const titles = movies.map(m => m.title);
    $("#global-search").autocomplete({
        source: titles,
        select: function(event, ui) {
            alert("Ir a película: " + ui.item.value);
        }
    });

    movies.forEach(m => {
        const badge = m.premium ? '<span class="badge badge-premium" style="position:absolute; top:10px; right:10px; font-size:0.6rem; z-index:5">PRO</span>' : '';
        
        // Usamos el link que viene preparado desde PHP
        const card = `
            <a href="${m.link}" class="movie-poster" data-bg="${m.bg}" style="display:block; text-decoration:none; color:white;">
                ${badge}
                <img src="${m.img}" alt="${m.title}">
                <div class="poster-info">
                    <h4 style="margin:0; font-size:1rem; text-shadow:0 2px 4px black;">${m.title}</h4>
                    <span style="font-size:0.8rem; color:var(--accent);">Ver detalles <i class="fa fa-arrow-right"></i></span>
                </div>
            </a>
        `;
        grid.append(card);
    });
};

// TRANSICIÓN ENTRE PERFILES
window.enterApp = function(role) {
    $('#view-profiles').fadeOut(400, function() {
        $(this).removeClass('active');
        $('#view-home').addClass('active').css('display', 'flex').hide().fadeIn(400);
    });
}

window.logout = function() {
     $('#view-home').fadeOut(400, function() {
        $(this).removeClass('active');
        $('#view-profiles').addClass('active').css('display', 'flex').hide().fadeIn(400);
    });
}
});