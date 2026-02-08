/* - Código Corregido (Con Buscador Externo + Diseño Unificado) */

let paginaActual = 1;
let cargando = false;
let finDeContenido = false;
let generoActualId = null;
const OMDb_API_KEY = '78a51c36'; // Tu API Key
let modoGridActivo = false;

$(document).ready(function () {
    console.log("🚀 Frontend Iniciado.");

    // =========================================================
    // 1. INICIO & SPLASH SCREEN
    // =========================================================
    function iniciarWeb() {
        $('#view-splash').fadeOut(600, function () {
            $(this).removeClass('active');
            if ($('#view-profiles').length > 0) {
                $('#view-profiles').addClass('active').css('display', 'flex');
            } else {
                const viewHome = $('#view-home');
                viewHome.addClass('active').css('opacity', 0).show();
                viewHome.animate({ opacity: 1 }, 400, function () {
                    inicializarCarruseles();
                });
            }
        });
    }

    if (typeof SHOW_SPLASH !== 'undefined' && SHOW_SPLASH === true) {
        $('#view-splash').addClass('active').css('display', 'flex');
        setTimeout(() => { $('.loader-line').css('width', '100%'); }, 100);
        setTimeout(() => { iniciarWeb(); }, 1500);
    } else {
        if ($('#view-peliculas-full').length === 0) {
            $('#view-splash').hide().removeClass('active');
            $('#view-home').show().addClass('active');
            inicializarCarruseles();
        } else {
            $('#view-splash').hide();
        }
    }

    // =========================================================
    // 2. ENRUTADOR DE VISTAS (Peliculas/Series/Género)
    // =========================================================
    if ($('#view-peliculas-full').length > 0) {

        const urlParams = new URLSearchParams(window.location.search);
        const generoUrl = urlParams.get('genero');

        // A. SI ENTRAMOS CON URL DE GÉNERO
        if (generoUrl) {
            modoGridActivo = true;
            generoActualId = generoUrl;
            cargarVistaGenero(generoUrl);
        }
        // B. SI ENTRAMOS A LA PORTADA NORMAL
        else {
            modoGridActivo = false;
            cargarPortadaNormal();
        }

        // C. INTERCEPTOR DEL MENÚ (Click en un género)
        $(document).on('click', '.trigger-filtro', function (e) {
            e.preventDefault();
            const genero = $(this).data('genero');

            // Actualizamos URL sin recargar
            const newUrl = BASE_URL + "peliculas?genero=" + encodeURIComponent(genero);
            window.history.pushState({ path: newUrl }, '', newUrl);

            // Cargamos la nueva vista
            modoGridActivo = true;
            generoActualId = genero;
            cargarVistaGenero(genero);
        });
    }

    // =========================================================
    // 3. SCROLL INFINITO (Solo si estamos en modo Grid)
    // =========================================================
    $('.view-section').on('scroll', function () {
        if ($('#grid-container').is(':visible') && !finDeContenido && !cargando) {
            if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 300) {
                cargarGridPeliculasAPI(null, true);
            }
        }
    });

    // =========================================================
    // 4. BUSCADOR & UI (Aquí está la parte externa RESTAURADA)
    // =========================================================
    $(document).on('mouseenter', '.movie-poster', function () {
        const bgUrl = $(this).data('bg');
        if (bgUrl) $('#dynamic-bg').css('background-image', `url(${bgUrl})`).css('opacity', '0.5');
    });
    $(document).on('mouseleave', '.movie-poster', function () { $('#dynamic-bg').css('opacity', '0.2'); });

    $("#global-search").autocomplete({
        minLength: 2, // Esperar a 2 letras para no saturar
        source: function (request, response) {
            var csrfName = $('.txt_csrftoken').attr('name');
            var csrfHash = $('.txt_csrftoken').val();

            $.ajax({
                // Asegúrate de que esta URL apunta a tu función autocompletar() de Home.php
                url: BASE_URL + "autocompletar",
                type: "post",
                dataType: "json",
                data: {
                    search: request.term,
                    [csrfName]: csrfHash
                },
                success: function (resp) {
                    // Actualizar token CSRF para la siguiente petición
                    if (resp.token) {
                        $('.txt_csrftoken').val(resp.token);
                    }

                    // La magia: PHP ya nos da la lista mezclada y limpia
                    response(resp.data);
                },
                error: function () {
                    response([]);
                }
            });
        },
        create: function () {
            $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                // Lógica visual: Si viene de 'local' (tu BD) o 'tmdb' (API)
                let badge = '';

                if (item.type === 'local') {
                    badge = '<span style="color:#46d369; font-size:0.6rem; border:1px solid #46d369; padding:1px 4px; border-radius:3px; float:right; margin-left:10px;">EN CATÁLOGO</span>';
                } else {
                    badge = '<span style="color:#00d2ff; font-size:0.6rem; border:1px solid #00d2ff; padding:1px 4px; border-radius:3px; float:right; margin-left:10px;">GLOBAL</span>';
                }

                return $("<li>")
                    .append(`
                    <div class="ui-menu-item-wrapper" style="display:flex; gap:10px; align-items:center; padding:5px;">
                        <img src="${item.img}" style="width:35px; height:52px; object-fit:cover; border-radius:4px; background:#333;">
                        <div style="flex:1; overflow:hidden;">
                            <div style="font-weight:600; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                ${item.label.split('(')[0]} 
                            </div>
                            <div style="font-size:0.75rem; color:#aaa; display:flex; justify-content:space-between; align-items:center;">
                                <span>${item.label.match(/\((.*?)\)/)?.[0] || ''}</span>
                                ${badge}
                            </div>
                        </div>
                    </div>
                `)
                    .appendTo(ul);
            };
        },
        select: function (event, ui) {
            // Redirección limpia usando el ID numérico
            // Esto llevará a /detalle/12345 y tu controlador sabrá qué hacer
            window.location.href = BASE_URL + "detalle/" + ui.item.value;
            return false;
        }
    });
}); // FIN DEL DOCUMENT READY


// =========================================================
// 5. FUNCIONES DE CARGA DE DATOS (API)
// =========================================================

// --- A. CARGA DEL GRID SIMPLE (Paginado) ---
function cargarGridPeliculasAPI(genero = null, esScroll = false) {
    if (cargando) return;
    cargando = true;

    if (!esScroll) {
        paginaActual = 1;
        $('#grid-container').empty();
        finDeContenido = false;
    } else {
        paginaActual++;
    }

    if (!genero) {
        const urlParams = new URLSearchParams(window.location.search);
        genero = urlParams.get('genero');
    }

    let baseUrlClean = BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';
    let urlApi = baseUrlClean + "index.php/api/catalogo?page=" + paginaActual;

    if (genero) urlApi += "&genero=" + encodeURIComponent(genero);

    $.ajax({
        url: urlApi,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#loading-initial').hide();
            $('.content').fadeIn();

            if (modoGridActivo) {
                $('#grid-container').addClass('activo-visible');
            } else {
                $('#grid-container').removeClass('activo-visible');
            }

            cargando = false;

            if (response.data && response.data.length > 0) {
                let htmlAcumulado = '';
                response.data.forEach(peli => {
                    // Usamos tu función maestra para generar el HTML
                    htmlAcumulado += generarHtmlTarjeta(peli);
                });
                $('#grid-container').append(htmlAcumulado);
            } else {
                if (paginaActual === 1) {
                    $('#grid-container').html('<h3 style="color:white; text-align:center; grid-column: 1/-1; padding: 50px;">No hay contenido disponible.</h3>');
                }
                finDeContenido = true;
            }
        },
        error: function () {
            cargando = false;
            $('#loading-initial').hide();
        }
    });
}

// --- B. CARGA DE VISTA DE GÉNERO (Filas + Ver Más) ---
function cargarVistaGenero(generoId) {
    $('#loading-initial').show();
    $('#hero-wrapper').hide();
    $('#rows-container').hide();
    $('#grid-expandido').hide().empty();
    $('#genre-landing-container').hide().empty();

    $('.nav-link').removeClass('active');

    let urlApi = BASE_URL + "index.php/api/catalogo?genero=" + generoId;

    $.ajax({
        url: urlApi,
        dataType: 'json',
        success: function (response) {
            $('#loading-initial').hide();
            $('.content').fadeIn();

            if (response.modo === 'landing_genero') {
                renderGenreRows(response.secciones, response.titulo);
                $('#genre-landing-container').fadeIn();
            }
        }
    });
}

function renderGenreRows(secciones, nombreGenero) {
    let html = `<div style="padding: 20px 4% 0;"><h1 style="color:white; margin: 0 0 10px 0; font-size: 2rem;">Explorando: <span style="color:var(--accent)">${nombreGenero}</span></h1></div>`;

    secciones.forEach((sec, idx) => {
        if (sec.data && sec.data.length > 0) {
            const movies = formatData(sec.data);

            html += `
            <div class="category-row" style="margin-bottom: 50px; padding-left: 4%;">
                <div class="header-seccion-genero" style="display:flex; align-items:center; margin-bottom:15px;">
                    <h3 class="row-title" style="color:white; font-size:1.4rem; margin:0;">${sec.titulo}</h3>
                    <button class="btn-ver-mas-row" onclick="abrirGridExpandido(${sec.tipo}, '${sec.titulo}')" 
                        style="background:transparent; border:1px solid var(--accent); color:var(--accent); margin-left:20px; padding:5px 15px; border-radius:4px; cursor:pointer;">
                        Ver todo <i class="fa fa-th"></i>
                    </button>
                </div>
                <div class="slick-row" id="row-genre-${idx}">
                    ${movies.map(m => `
                        <div class="slick-slide-item" style="padding: 0 5px;">
                            ${generarHtmlTarjeta(m)} 
                        </div>`).join('')}
                </div>
            </div>`;
        }
    });

    $('#genre-landing-container').html(html);

    setTimeout(() => {
        $('.slick-row').not('.slick-initialized').slick({
            dots: false, infinite: false, speed: 500, slidesToShow: 6, slidesToScroll: 3,
            prevArrow: '<button type="button" class=" left-arrow custom-arrow"><i class="fa fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class=" right-arrow custom-arrow"><i class="fa fa-chevron-right"></i></button>',
            responsive: [
                { breakpoint: 1400, settings: { slidesToShow: 5 } },
                { breakpoint: 1100, settings: { slidesToShow: 4 } },
                { breakpoint: 500, settings: { slidesToShow: 2 } }
            ]
        });
    }, 100);
}

// --- C. CARGA DE PORTADA NORMAL ---
function cargarPortadaNormal() {
    $('#hero-wrapper').show();
    $('#rows-container').show();
    $('#genre-landing-container').hide();
    $('#grid-expandido').hide();

    let cleanBase = BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';
    fetch(cleanBase + 'api/peliculas-landing')
        .then(r => r.json())
        .then(data => {
            $('#loading-initial').hide();
            $('.content').fadeIn();
            if (data.carrusel) renderHeroCarousel(data.carrusel);
            if (data.secciones) renderNetflixRows(data.secciones);
            inicializarCarruseles();
        });
}

function renderNetflixRows(secciones) {
    let html = '';
    secciones.forEach((sec, idx) => {
        if (sec.data && sec.data.length > 0) {
            const movies = formatData(sec.data);
            html += `
            <div class="category-row" style="margin-bottom: 40px; padding-left: 4%;">
                <h3 class="row-title" style="color:white; font-size:1.4rem; margin-bottom:15px; font-weight:600;">${sec.titulo}</h3>
                <div class="slick-row" id="row-spa-${idx}">
                    ${movies.map(m => `
                        <div class="slick-slide-item" style="padding: 0 5px;">
                             ${generarHtmlTarjeta(m)} 
                        </div>`).join('')}
                </div>
            </div>`;
        }
    });
    $('#rows-container').html(html);
}

// =========================================================
// 6. FUNCIONES DE UI Y UTILIDADES
// =========================================================

// --- A. GRID EXPANDIDO (VER TODO) ---
window.abrirGridExpandido = function (tipoId, tituloSeccion) {
    $('.nav-link').removeClass('active');
    if (tipoId == 1) $('a[href*="peliculas"]').addClass('active');
    else if (tipoId == 2) $('a[href*="series"]').addClass('active');

    $('#genre-landing-container').hide();
    $('#loading-initial').show();
    $('#grid-expandido').empty();

    let urlApi = BASE_URL + "index.php/api/catalogo?genero=" + generoActualId;

    $.ajax({
        url: urlApi,
        dataType: 'json',
        success: function (response) {
            $('#loading-initial').hide();

            let datosFiltrados = [];
            if (response.secciones) {
                response.secciones.forEach(sec => {
                    if (sec.tipo == tipoId) datosFiltrados = sec.data;
                });
            }

            let htmlGrid = `
                <div style="grid-column: 1/-1; margin-bottom: 30px; display:flex; align-items:center; gap:15px;">
                    <button onclick="volverALandingGenero()" style="background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;"><i class="fa fa-arrow-left"></i></button>
                    <h2 style="color:white; margin:0;">${tituloSeccion}</h2>
                </div>
            `;

            const movies = formatData(datosFiltrados);
            movies.forEach(m => {
                htmlGrid += generarHtmlTarjeta(m);
            });

            $('#grid-expandido').html(htmlGrid).css('display', 'grid').show();
        }
    });
};

window.volverALandingGenero = function () {
    $('.nav-link').removeClass('active');
    $('#grid-expandido').hide();
    $('#genre-landing-container').fadeIn();
};

// --- B. RENDERIZADO HERO ---
function renderHeroCarousel(movies) {
    let html = '<div class="hero-carousel">';
    movies.forEach(c => {
        let bg = c.imagen_bg.startsWith('http') ? c.imagen_bg : BASE_URL + 'assets/img/' + c.imagen_bg;
        // El Hero tiene un diseño único, así que este sí lo mantenemos a mano
        html += `<div class="hero-item" style="background-image: url('${bg}');"><div class="hero-content-wrapper"><div class="hero-info"><h1>${c.titulo}</h1><div class="hero-badges"><span class="badge badge-hd">4K</span><span class="badge badge-age">+${c.edad_recomendada}</span></div><p style="color:#ddd; margin-bottom:20px; font-size:1.1rem; line-height:1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">${c.descripcion}</p><div style="display:flex; gap:15px;"><button class="btn-primary" onclick="playCinematic('${BASE_URL}ver/${c.id}')"><i class="fa fa-play"></i> Ver</button><button class="btn-secondary btn-lista-${c.id}" onclick="toggleMiLista(${c.id})" style="background:rgba(255,255,255,0.2); border:none; color:white; padding:12px 25px; border-radius:50px; cursor:pointer; font-weight:bold; display:flex; align-items:center; gap:8px;"><i class="fa ${c.en_mi_lista ? 'fa-check' : 'fa-plus'}"></i> Mi Lista</button></div></div></div></div>`;
    });
    html += '</div>';
    $('#hero-wrapper').html(html);
}

// --- C. HELPERS DE DATOS ---
function formatData(lista) {
    let cleanBase = BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';
    return lista.map(item => ({
        id: item.id, title: item.titulo,
        img: item.imagen.startsWith('http') ? item.imagen : cleanBase + 'assets/img/' + item.imagen,
        bg: item.imagen_bg.startsWith('http') ? item.imagen_bg : cleanBase + 'assets/img/' + item.imagen_bg,
        premium: item.nivel_acceso == '2', age: item.edad_recomendada, desc: item.descripcion,
        in_list: item.en_mi_lista || false, link_detalle: cleanBase + 'detalle/' + item.id, link_ver: cleanBase + 'ver/' + item.id
    }));
}

function inicializarCarruseles() {
    if ($('.hero-carousel').length > 0 && !$('.hero-carousel').hasClass('slick-initialized')) {
        $('.hero-carousel').slick({
            dots: true, infinite: true, speed: 800, fade: true,
            cssEase: 'linear', autoplay: true, autoplaySpeed: 4000, arrows: false, pauseOnHover: false
        });
    }

    if ($('.slick-row').length > 0 && !$('.slick-row').hasClass('slick-initialized')) {
        $('.slick-row').slick({
            dots: false, infinite: true, speed: 500, slidesToShow: 6, slidesToScroll: 3,
            responsive: [
                { breakpoint: 1600, settings: { slidesToShow: 5, slidesToScroll: 2 } },
                { breakpoint: 1100, settings: { slidesToShow: 4, slidesToScroll: 2 } },
                { breakpoint: 800, settings: { slidesToShow: 3, slidesToScroll: 1 } },
                { breakpoint: 500, settings: { slidesToShow: 2, slidesToScroll: 1 } }
            ],
            prevArrow: '<button type="button" class=" left-arrow custom-arrow"><i class="fa fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class=" right-arrow custom-arrow"><i class="fa fa-chevron-right"></i></button>'
        });
    }

    setTimeout(function () {
        $('.hero-carousel, .slick-row').slick('setPosition');
        $(window).trigger('resize');
    }, 200);
}

// --- D. INTERACCIONES (Play, Lista, Login) ---
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
                    btn.css({ 'border-color': 'var(--accent)', 'color': 'var(--accent)' });
                } else {
                    icon.removeClass('fa-check').addClass('fa-heart');
                    btn.css({ 'border-color': '', 'color': '' });
                }
            }
        }
    });
};

window.logout = function () { window.location.href = BASE_URL + 'auth/logout'; };

// Funciones de Login
function attemptLogin(id, username, planId) {
    if (planId == 3) { realizarCambioPerfil(id, ''); }
    else {
        $('#selectedUserId').val(id);
        $('#modalUser').text(username);
        $('#passwordInput').val('');
        $('#errorMsg').hide();
        $('#modalAuth').fadeIn().css('display', 'flex');
        setTimeout(() => $('#passwordInput').focus(), 100);
    }
}
window.closeModal = function () { $('#modalAuth').fadeOut(); };
window.submitSwitchProfile = function () { realizarCambioPerfil($('#selectedUserId').val(), $('#passwordInput').val()); };
$(document).on('keypress', '#passwordInput', function (e) { if (e.which === 13) submitSwitchProfile(); });

function realizarCambioPerfil(id, password) {
    let csrfName = $('.txt_csrftoken').attr('name');
    let csrfHash = $('.txt_csrftoken').val();
    $.ajax({
        url: BASE_URL + "auth/login",
        type: "post", dataType: "json",
        data: { id: id, password: password, [csrfName]: csrfHash },
        success: function (response) {
            if (response.token) $('.txt_csrftoken').val(response.token);
            if (response.status === 'success') window.location.reload();
            else {
                $('#errorMsg').text(response.msg).show();
                if (typeof $.fn.effect === 'function') $('.modal-content').effect('shake', { times: 3 }, 300);
            }
        },
        error: function () { alert('Error de conexión.'); }
    });
}


// =========================================================
// 7. MOLDE MAESTRO: GENERADOR DE TARJETAS HTML
// =========================================================
// =========================================================
// 7. MOLDE MAESTRO: GENERADOR DE TARJETAS HTML (IDÉNTICO A PHP)
// =========================================================
function generarHtmlTarjeta(item) {
    let cleanBase = BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';

    // 1. Normalización de datos
    let imgPoster = item.img || item.imagen;
    if (imgPoster && !imgPoster.startsWith('http')) imgPoster = cleanBase + 'assets/img/' + imgPoster;

    let imgBg = item.bg || item.imagen_bg;
    if (!imgBg) imgBg = imgPoster;
    else if (!imgBg.startsWith('http')) imgBg = cleanBase + 'assets/img/' + imgBg;

    let titulo = item.title || item.titulo;
    let linkDetalle = item.link_detalle || (cleanBase + 'detalle/' + item.id);
    let linkVer = item.link_ver || (cleanBase + 'ver/' + item.id);
    let edad = item.age || item.edad_recomendada || "12";
    let desc = item.desc || item.descripcion || "Sin descripción disponible.";

    let enLista = item.in_list || item.en_mi_lista;

    let styleBtnLista = enLista ? 'border-color: var(--accent); color: var(--accent);' : '';
    let iconClass = enLista ? 'fa-check' : 'fa-heart';

    // Random Match para simular algoritmo (98% para ti)
    let matchScore = Math.floor(Math.random() * (99 - 80 + 1) + 80);

    // 3. ESTRUCTURA HTML (Copia exacta de tu PHP)
    return `
    <div class="slick-slide-item" style="padding: 0 5px;">
        <div class="movie-card">
            <div class="poster-visible">
                <img src="${imgPoster}" alt="${titulo}">
            </div>
            
            <div class="hover-details-card">
                <div class="hover-backdrop" style="background-image: url('${imgBg}'); cursor: pointer;" 
                     onclick="window.location.href='${linkDetalle}'">
                </div>
                <div class="hover-info">
                    <div class="hover-buttons">
                        <button class="btn-mini-play" onclick="playCinematic('${linkVer}')"><i class="fa fa-play"></i></button>
                        
                        <button class="btn-mini-icon btn-lista-${item.id}" 
                                onclick="toggleMiLista('${item.id}')" 
                                style="${styleBtnLista}">
                            <i class="fa ${iconClass}"></i>
                        </button>
                    </div>
                    
                    <h4 style="cursor:pointer;" onclick="window.location.href='${linkDetalle}'">${titulo}</h4>
                    
                    <div class="hover-meta">
                        <span style="color:#46d369; font-weight:bold;">${matchScore}% para ti</span>
                        <span class="badge badge-hd">${edad}</span>
                    </div>
                    
                    <p style="font-size:0.75rem; color:#ccc; margin:0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        ${desc}
                    </p>
                </div>
            </div>
        </div>
    </div>`;

}