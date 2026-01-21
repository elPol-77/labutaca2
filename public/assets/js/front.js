let paginaActual = 1;
let cargando = false;
let finDeContenido = false;

$(document).ready(function () {
    // ----------------------------------------------------
    // 1. LÓGICA DE INICIO (SPLASH SCREEN INTELIGENTE)
    // ----------------------------------------------------
    function iniciarWeb() {
        $('#view-splash').fadeOut(600, function () {
            $(this).removeClass('active');
            if ($('#view-profiles').length > 0) {
                $('#view-profiles').addClass('active').css('display', 'flex');
            } else {
                $('#view-home').addClass('active').fadeIn(400);
            }
        });
    }

    if (typeof SHOW_SPLASH !== 'undefined' && SHOW_SPLASH === true) {
        $('#view-splash').addClass('active').css('display', 'flex');
        setTimeout(() => { $('.loader-line').css('width', '100%'); }, 100);
        setTimeout(() => { iniciarWeb(); }, 2000);
    } else {
        $('#view-splash').hide().removeClass('active');
        if ($('#view-profiles').length > 0) {
            $('#view-profiles').addClass('active').css('display', 'flex');
        } else {
            $('#view-home').addClass('active').show();
        }
    }

    // ----------------------------------------------------
    // 2. INICIALIZAR CARRUSEL HERO (SLICK)
    // ----------------------------------------------------
    if ($('.hero-carousel').length > 0) {
        $('.hero-carousel').slick({
            dots: true,
            infinite: true,
            speed: 500,
            fade: true,
            cssEase: 'linear',
            autoplay: true,
            autoplaySpeed: 4000,
            arrows: false
        });
    }

    // ----------------------------------------------------
    // 3. DETECTOR DE SCROLL INFINITO
    // ----------------------------------------------------
    // IMPORTANTE: Escuchamos el scroll en la sección activa que tiene el scrollbar
    $('.view-section').on('scroll', function() {
        if (finDeContenido || cargando) return;

        // Si el usuario llega al 80% del scroll del contenedor
        if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 300) {
            cargarMasPeliculas();
        }
    });

    // ----------------------------------------------------
    // 4. EFECTOS & AUTOCOMPLETE
    // ----------------------------------------------------
    $(document).on('mouseenter', '.movie-poster', function () {
        const bgUrl = $(this).data('bg');
        if (bgUrl) $('#dynamic-bg').css('background-image', `url(${bgUrl})`).css('opacity', '0.5');
    });

    $(document).on('mouseleave', '.movie-poster', function () { 
        $('#dynamic-bg').css('opacity', '0.2'); 
    });

    $("#global-search").autocomplete({
        source: function (request, response) {
            var csrfName = $('.txt_csrftoken').attr('name');
            var csrfHash = $('.txt_csrftoken').val();
            $.ajax({
                url: BASE_URL + "api/buscador/autocompletar",
                type: "post",
                dataType: "json",
                data: { search: request.term, [csrfName]: csrfHash },
                success: function (data) {
                    if (data.token) $('.txt_csrftoken').val(data.token);
                    response(data.data);
                }
            });
        },
        select: function (event, ui) {
            $('#global-search').val(ui.item.label);
            window.location.href = BASE_URL + "detalle/" + ui.item.value;
            return false;
        }
    });
});

// ----------------------------------------------------
// 5. FUNCIONES DE CARGA Y RENDERIZADO
// ----------------------------------------------------
function cargarMasPeliculas() {
    if (cargando) return;
    cargando = true;
    paginaActual++;

    $.ajax({
        url: BASE_URL + "home/index/" + paginaActual,
        type: "get",
        dataType: "json",
        success: function(data) {
            if (!data || data.length === 0) {
                finDeContenido = true;
                return;
            }
            
            // Adaptamos los datos para el renderizado
            const moviesFormatted = data.map(item => ({
                id: item.id,
                title: item.titulo,
                img: item.imagen.startsWith('http') ? item.imagen : BASE_URL + 'assets/img/' + item.imagen,
                bg: item.imagen_bg.startsWith('http') ? item.imagen_bg : BASE_URL + 'assets/img/' + item.imagen_bg,
                premium: item.nivel_acceso == '2',
                link: BASE_URL + 'detalle/' + item.id
            }));

            // Usamos append en lugar de empty
            appendMoviesToGrid(moviesFormatted);
            cargando = false;
        },
        error: function() {
            cargando = false;
        }
    });
}

window.renderGrid = function (movies) {
    const grid = $('#grid-container');
    grid.empty();
    appendMoviesToGrid(movies);
};

function appendMoviesToGrid(movies) {
    const grid = $('#grid-container');
    if (!movies || movies.length === 0) {
        if (paginaActual === 1) grid.html('<p style="color:#a0a0a0; text-align:center; grid-column:1/-1;">No hay contenido disponible.</p>');
        return;
    }
    movies.forEach(m => {
        const badge = m.premium ? '<span class="badge badge-premium" style="position:absolute; top:10px; right:10px; z-index:5;">PRO</span>' : '';
        const card = `
            <a href="${m.link}" class="movie-poster" data-bg="${m.bg}" style="text-decoration:none;">
                ${badge}
                <img src="${m.img}" alt="${m.title}">
                <div class="poster-info"><h4>${m.title}</h4></div>
            </a>`;
        grid.append(card);
    });
}

// ----------------------------------------------------
// 6. OTRAS UTILIDADES
// ----------------------------------------------------
window.playCinematic = function(urlDestino) {
    $('#view-splash').addClass('active').css('display', 'flex').hide().fadeIn(300);
    $('.loader-line').css('width', '0%');
    setTimeout(() => { $('.loader-line').css('width', '100%'); }, 100);
    setTimeout(() => { window.location.href = urlDestino; }, 2000);
};

window.toggleMiLista = function (idContenido) {
    var csrfName = $('.txt_csrftoken').attr('name');
    var csrfHash = $('.txt_csrftoken').val();
    $.ajax({
        url: BASE_URL + "api/usuario/toggle-lista",
        type: "post", dataType: "json",
        data: { id: idContenido, [csrfName]: csrfHash },
        success: function (response) {
            if (response.token) $('.txt_csrftoken').val(response.token);
            location.reload(); // Recargamos para actualizar estado visual
        }
    });
};

window.logout = function () { window.location.href = BASE_URL + 'logout'; }