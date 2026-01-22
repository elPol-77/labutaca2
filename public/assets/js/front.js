let paginaActual = 1;
let cargando = false;
let finDeContenido = false;

$(document).ready(function () {
    
    // =========================================================
    // 1. INICIO & SPLASH SCREEN (LÓGICA DE CARGA)
    // =========================================================
    function iniciarWeb() {
        // Desvanecemos el Splash
        $('#view-splash').fadeOut(600, function () {
            $(this).removeClass('active');
            
            // Si estamos en la pantalla de perfiles (Login)
            if ($('#view-profiles').length > 0) {
                $('#view-profiles').addClass('active').css('display', 'flex');
            } 
            // Si estamos en el Home (Catálogo)
            else {
                const viewHome = $('#view-home');
                // Preparamos el home (visible pero transparente para animar)
                viewHome.addClass('active').css('opacity', 0).show();
                
                // Animación suave de entrada
                viewHome.animate({ opacity: 1 }, 400, function() {
                    // [IMPORTANTE] Inicializamos los carruseles AQUÍ, 
                    // justo cuando el div ya es visible y tiene ancho real.
                    inicializarCarruseles();
                });
            }
        });
    }

    // Comprobar si hay que mostrar Splash (variable definida en footer.php)
    if (typeof SHOW_SPLASH !== 'undefined' && SHOW_SPLASH === true) {
        $('#view-splash').addClass('active').css('display', 'flex');
        // Animación de la barra de carga
        setTimeout(() => { $('.loader-line').css('width', '100%'); }, 100);
        // Quitar splash a los 2 segundos
        setTimeout(() => { iniciarWeb(); }, 1500);
    } else {
        // Si no hay splash (navegación interna), mostrar directo
        $('#view-splash').hide().removeClass('active');
        $('#view-home').show().addClass('active');
        
        // Inicializar carruseles inmediatamente
        inicializarCarruseles();
    }

    // =========================================================
    // 2. DETECTOR DE SCROLL INFINITO (Solo para Vistas Grid)
    // =========================================================
    $('.view-section').on('scroll', function () {
        // Solo activamos scroll infinito si existe el contenedor de grid (no en modo Netflix)
        if ($('#grid-container').length > 0 && !finDeContenido && !cargando) {
            if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 300) {
                cargarMasPeliculas();
            }
        }
    });

    // =========================================================
    // 3. EFECTOS & AUTOCOMPLETE
    // =========================================================
    // Efecto cambio de fondo al pasar por un póster
    $(document).on('mouseenter', '.movie-poster', function () {
        const bgUrl = $(this).data('bg');
        if (bgUrl) $('#dynamic-bg').css('background-image', `url(${bgUrl})`).css('opacity', '0.5');
    });

    $(document).on('mouseleave', '.movie-poster', function () {
        $('#dynamic-bg').css('opacity', '0.2');
    });

    // Buscador predictivo (jQuery UI)
    $("#global-search").autocomplete({
        source: function (request, response) {
            var csrfName = $('.txt_csrftoken').attr('name');
            var csrfHash = $('.txt_csrftoken').val();
            $.ajax({
                url: BASE_URL + "api/buscador/autocompletar",
                type: "post", dataType: "json",
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

// =========================================================
// 4. FUNCIÓN MAESTRA DE CARRUSELES (SOLUCIÓN BUG VISUAL)
// =========================================================
function inicializarCarruseles() {
    // A. Inicializar Hero Carousel (Portada)
    if ($('.hero-carousel').length > 0 && !$('.hero-carousel').hasClass('slick-initialized')) {
        $('.hero-carousel').slick({
            dots: true, infinite: true, speed: 800, fade: true, cssEase: 'linear',
            autoplay: true, autoplaySpeed: 4000, arrows: false, pauseOnHover: false
        });
    }

    // B. Inicializar Filas Netflix (.slick-row)
    if ($('.slick-row').length > 0 && !$('.slick-row').hasClass('slick-initialized')) {
        $('.slick-row').slick({
            dots: false, infinite: true, speed: 500,
            slidesToShow: 6, slidesToScroll: 3,
            responsive: [
                { breakpoint: 1600, settings: { slidesToShow: 5, slidesToScroll: 2 } },
                { breakpoint: 1100, settings: { slidesToShow: 4, slidesToScroll: 2 } },
                { breakpoint: 800, settings: { slidesToShow: 3, slidesToScroll: 1 } },
                { breakpoint: 500, settings: { slidesToShow: 2, slidesToScroll: 1 } }
            ],
            // Flechas personalizadas
            prevArrow: '<button type="button" class="slick-prev custom-arrow"><i class="fa fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next custom-arrow"><i class="fa fa-chevron-right"></i></button>'
        });
    }
    
    // [CRÍTICO] Forzar a Slick a recalcular posiciones
    // Esto arregla el bug de que no se vean hasta redimensionar/recargar
    $('.hero-carousel, .slick-row').slick('setPosition');
}

// =========================================================
// 5. FUNCIONES DE CARGA Y RENDERIZADO (GRID)
// =========================================================
function cargarMasPeliculas() {
    if (cargando) return;
    cargando = true;
    paginaActual++;

    // Obtener parámetros de la URL (si hay filtro)
    const urlParams = new URLSearchParams(window.location.search);
    const genero = urlParams.get('genero');
    
    let urlAjax = BASE_URL + "home/index/" + paginaActual;
    if(genero) urlAjax += "?genero=" + genero;

    $.ajax({
        url: urlAjax,
        type: "get", dataType: "json",
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function (data) {
            if (!data || data.length === 0) {
                finDeContenido = true;
                return;
            }
            // Formatear datos
            const moviesFormatted = data.map(item => ({
                id: item.id,
                title: item.titulo,
                img: item.imagen.startsWith('http') ? item.imagen : BASE_URL + 'assets/img/' + item.imagen,
                bg: item.imagen_bg.startsWith('http') ? item.imagen_bg : BASE_URL + 'assets/img/' + item.imagen_bg,
                premium: item.nivel_acceso == '2',
                age: item.edad_recomendada, 
                desc: item.descripcion,
                in_list: item.en_mi_lista,
                link_detalle: BASE_URL + 'detalle/' + item.id,
                link_ver: BASE_URL + 'ver/' + item.id
            }));

            appendMoviesToGrid(moviesFormatted);
            cargando = false;
        },
        error: function () { cargando = false; }
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
        let badgesHtml = '';
        let edadTexto = (m.age && m.age > 0) ? '+' + m.age : 'TP';
        badgesHtml += `<span class="badge badge-hd" style="font-size:0.6rem; padding:2px 6px;">${edadTexto}</span>`;

        let iconClass = m.in_list ? 'fa-check' : 'fa-heart';
        let btnStyle = m.in_list ? 'border-color: var(--accent); color: var(--accent);' : '';
        let btnTitle  = m.in_list ? 'Quitar de mi lista' : 'Añadir a mi lista';

        const card = `
            <div class="movie-card">
                <div class="poster-visible">
                    <img src="${m.img}" alt="${m.title}">
                </div>
                <div class="hover-details-card">
                    <div class="hover-backdrop" style="background-image: url('${m.bg}'); cursor: pointer;" onclick="window.location.href='${m.link_detalle}'"></div>
                    <div class="hover-info">
                        <div class="hover-buttons">
                            <button class="btn-mini-play" onclick="playCinematic('${m.link_ver}')" title="Reproducir"><i class="fa fa-play"></i></button>
                            <button class="btn-mini-icon btn-lista-${m.id}" onclick="toggleMiLista(${m.id})" title="${btnTitle}" style="${btnStyle}"><i class="fa ${iconClass}"></i></button>
                        </div>
                        <h4 style="cursor:pointer;" onclick="window.location.href='${m.link_detalle}'">${m.title}</h4>
                        <div class="hover-meta"><span style="color:#46d369; font-weight:bold;">98% para ti</span>${badgesHtml}</div>
                         <p style="font-size:0.75rem; color:#ccc; margin:0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">${m.desc}</p> 
                    </div>
                </div>
            </div>`;
        grid.append(card);
    });
}

// =========================================================
// 6. UTILIDADES VARIAS
// =========================================================
window.playCinematic = function (urlDestino) {
    $('#view-splash').addClass('active').css('display', 'flex').hide().fadeIn(300);
    $('.loader-line').css('width', '0%');
    setTimeout(() => { $('.loader-line').css('width', '100%'); }, 100);
    setTimeout(() => { window.location.href = urlDestino; }, 2000);
};

window.toggleMiLista = function (idContenido) {
    var csrfName = $('.txt_csrftoken').attr('name');
    var csrfHash = $('.txt_csrftoken').val();
    const btn = $(`.btn-lista-${idContenido}`);
    const icon = btn.find('i');

    $.ajax({
        url: BASE_URL + "api/usuario/toggle-lista",
        type: "post", dataType: "json",
        data: { id: idContenido, [csrfName]: csrfHash },
        success: function (response) {
            if (response.token) $('.txt_csrftoken').val(response.token);
            if (response.status === 'success') {
                if (response.action === 'added') {
                    icon.removeClass('fa-heart').addClass('fa-check');
                    btn.css({'border-color': 'var(--accent)', 'color': 'var(--accent)'});
                } else {
                    icon.removeClass('fa-check').addClass('fa-heart');
                    btn.css({'border-color': '', 'color': ''});
                }
            }
        },
        error: function() { console.log("Error al actualizar lista"); }
    });
};

window.logout = function () { window.location.href = BASE_URL + 'logout'; }

// =========================================================
// 7. LÓGICA DE CAMBIO DE PERFIL
// =========================================================

function attemptLogin(id, username, planId) {
    // A. NIÑO (Plan 3) -> Directo
    if (planId == 3) {
        realizarCambioPerfil(id, ''); 
    } 
    // B. ADULTO -> Modal
    else {
        $('#selectedUserId').val(id);
        $('#modalUser').text(username);
        $('#passwordInput').val('');
        $('#errorMsg').hide();
        $('#modalAuth').fadeIn().css('display', 'flex');
        setTimeout(() => $('#passwordInput').focus(), 100); 
    }
}

window.closeModal = function() {
    $('#modalAuth').fadeOut();
};

window.submitSwitchProfile = function() {
    const id = $('#selectedUserId').val();
    const pass = $('#passwordInput').val();
    realizarCambioPerfil(id, pass);
};

$(document).on('keypress', '#passwordInput', function (e) {
    if(e.which === 13) submitSwitchProfile();
});

function realizarCambioPerfil(id, password) {
    let csrfName = $('.txt_csrftoken').attr('name');
    let csrfHash = $('.txt_csrftoken').val();

    $.ajax({
        url: BASE_URL + "auth/login",
        type: "post",
        dataType: "json",
        data: {
            id: id,
            password: password,
            [csrfName]: csrfHash
        },
        success: function(response) {
            if (response.token) {
                $('.txt_csrftoken').val(response.token);
            }
            if (response.status === 'success') {
                // Al recargar, se disparará iniciarWeb() que cargará el Slick correctamente
                window.location.reload(); 
            } else {
                $('#errorMsg').text(response.msg).show();
                // Animación de error si está disponible
                if(typeof $.fn.effect === 'function') {
                    $('.modal-content').effect('shake', {times:3}, 300);
                }
            }
        },
        error: function() {
            alert('Error de conexión con el servidor.');
        }
    });
}