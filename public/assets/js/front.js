let paginaActual = 1;
let cargando = false;
let finDeContenido = false;
const OMDb_API_KEY = '78a51c36';



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
    // 2. LÓGICA PELÍCULAS (SPA) - NUEVA ESTRUCTURA PRIME VIDEO
    // =========================================================
    if ($('#view-peliculas-full').length > 0) {
        
        const urlParams = new URLSearchParams(window.location.search);
        const generoUrl = urlParams.get('genero');

        // A. SI ENTRAMOS CON URL DE GÉNERO
        if (generoUrl) {
            generoActualId = generoUrl;
            cargarVistaGenero(generoUrl);
        } 
        // B. SI ENTRAMOS A LA PORTADA NORMAL
        else {
            cargarPortadaNormal();
        }

        // C. INTERCEPTOR DEL MENÚ (Click en un género)
        $(document).on('click', '.trigger-filtro', function(e) {
            e.preventDefault(); 
            const genero = $(this).data('genero');
            
            // Actualizamos URL sin recargar
            const newUrl = BASE_URL + "peliculas?genero=" + encodeURIComponent(genero);
            window.history.pushState({path: newUrl}, '', newUrl);
            
            // Cargamos la nueva vista
            generoActualId = genero;
            cargarVistaGenero(genero);
        });
    }
    // =========================================================
    // 3. SCROLL INFINITO
    // =========================================================
    $('.view-section').on('scroll', function () {
        if ($('#grid-container').length > 0 && !finDeContenido && !cargando) {
            if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 300) {
                cargarGridPeliculasAPI(null, true);
            }
        }
    });

    // =========================================================
    // 4. BUSCADOR & UI
    // =========================================================
    $(document).on('mouseenter', '.movie-poster', function () {
        const bgUrl = $(this).data('bg');
        if (bgUrl) $('#dynamic-bg').css('background-image', `url(${bgUrl})`).css('opacity', '0.5');
    });
    $(document).on('mouseleave', '.movie-poster', function () { $('#dynamic-bg').css('opacity', '0.2'); });

    $("#global-search").autocomplete({
        minLength: 1,
        source: function (request, response) {
            var csrfName = $('.txt_csrftoken').attr('name');
            var csrfHash = $('.txt_csrftoken').val();
            let combinedResults = [];

            $.ajax({
                url: BASE_URL + "api/buscador/autocompletar",
                type: "post", dataType: "json",
                data: { search: request.term, [csrfName]: csrfHash },
                success: function (localData) {
                    if (localData.token) $('.txt_csrftoken').val(localData.token);
                    if (localData.data) {
                        combinedResults = combinedResults.concat(localData.data.map(i => ({ ...i, source: 'local' })));
                    }
                    fetch(`https://www.omdbapi.com/?apikey=${OMDb_API_KEY}&s=${request.term}`)
                        .then(r => r.json())
                        .then(extData => {
                            if (extData.Response === "True") {
                                combinedResults = combinedResults.concat(extData.Search.slice(0, 3).map(m => ({
                                    label: m.Title, value: m.imdbID,
                                    img: (m.Poster !== "N/A" ? m.Poster : BASE_URL + 'assets/img/no-poster.jpg'),
                                    year: m.Year, source: 'external'
                                })));
                            }
                            response(combinedResults);
                        });
                }
            });
        },
        create: function () {
            $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                const badge = item.source === 'local' ? '<span style="color:#46d369; font-size:0.6rem; border:1px solid #46d369; float:right;">EN CATÁLOGO</span>' : '<span style="color:#00d2ff; font-size:0.6rem; border:1px solid #00d2ff; float:right;">GLOBAL</span>';
                return $("<li>").append(`<div class="ui-menu-item-wrapper" style="display:flex; gap:10px;"><img src="${item.img}" style="width:35px; height:50px; object-fit:cover;"><div style="flex:1;"><div>${item.label}</div><div style="font-size:0.75rem; color:#aaa;">${item.year || ''} ${badge}</div></div></div>`).appendTo(ul);
            };
        },
        select: function (event, ui) {
            window.location.href = BASE_URL + "detalle/" + ui.item.value;
            return false;
        }
    });

}); 

// =========================================================
// 5. FUNCIONES GLOBALES (FUERA DEL READY)
// =========================================================

// --- A. CARGA Y PINTADO DEL GRID (VERSIÓN CORREGIDA Y UNIFICADA) ---
function cargarGridPeliculasAPI(genero = null, esScroll = false) {
    if (cargando) return;
    cargando = true;

    // 1. Gestión estado
    if (!esScroll) {
        paginaActual = 1;
        $('#grid-container').empty();
        finDeContenido = false;
    } else {
        paginaActual++;
    }

    // 2. Obtener género
    if (!genero) {
        const urlParams = new URLSearchParams(window.location.search);
        genero = urlParams.get('genero');
    }

    // 3. Construir URL
    let baseUrlClean = BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';
    let urlApi = baseUrlClean + "index.php/api/catalogo?page=" + paginaActual;

    if (genero) {
        urlApi += "&genero=" + encodeURIComponent(genero);
    }

    console.log("🎨 Pidiendo:", urlApi);

    // 4. AJAX
    $.ajax({
        url: urlApi,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#loading-initial').hide();
            $('.content').fadeIn();

            // Gestionar visibilidad del contenedor Grid
            if (modoGridActivo) {
                $('#grid-container').addClass('activo-visible');
            } else {
                $('#grid-container').removeClass('activo-visible');
            }

            cargando = false;

            if (response.data && response.data.length > 0) {
                let htmlAcumulado = '';

                // --- BUCLE CORREGIDO ---
                response.data.forEach(peli => {
                    // Usamos la misma función que el resto de la web para uniformidad
                    // Nota: generarHtmlTarjeta ya gestiona si viene 'imagen' o 'img'
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
        error: function (xhr, status, error) {
            console.error("❌ Error JS:", error);
            cargando = false;
            if (paginaActual === 1) {
                $('#loading-initial').hide();
            }
        }
    });
}

// --- B. RENDERIZADO VISUAL PORTADA ---
function renderHeroCarousel(movies) {
    let html = '<div class="hero-carousel">';
    movies.forEach(c => {
        let bg = c.imagen_bg.startsWith('http') ? c.imagen_bg : BASE_URL + 'assets/img/' + c.imagen_bg;
        html += `<div class="hero-item" style="background-image: url('${bg}');"><div class="hero-content-wrapper"><div class="hero-info"><h1>${c.titulo}</h1><div class="hero-badges"><span class="badge badge-hd">4K</span><span class="badge badge-age">+${c.edad_recomendada}</span></div><p style="color:#ddd; margin-bottom:20px; font-size:1.1rem; line-height:1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">${c.descripcion}</p><div style="display:flex; gap:15px;"><button class="btn-primary" onclick="playCinematic('${BASE_URL}ver/${c.id}')"><i class="fa fa-play"></i> Ver</button><button class="btn-secondary btn-lista-${c.id}" onclick="toggleMiLista(${c.id})" style="background:rgba(255,255,255,0.2); border:none; color:white; padding:12px 25px; border-radius:50px; cursor:pointer; font-weight:bold; display:flex; align-items:center; gap:8px;"><i class="fa ${c.en_mi_lista ? 'fa-check' : 'fa-plus'}"></i> Mi Lista</button></div></div></div></div>`;
    });
    html += '</div>';
    $('#hero-wrapper').html(html);
}

function renderNetflixRows(secciones) {
    let html = '';
    secciones.forEach((sec, idx) => {
        if (sec.data && sec.data.length > 0) {
            const movies = formatData(sec.data);
            html += `<div class="category-row" style="margin-bottom: 40px; padding-left: 4%;"><h3 class="row-title" style="color:white; font-size:1.4rem; margin-bottom:15px; font-weight:600;">${sec.titulo}</h3><div class="slick-row" id="row-spa-${idx}">${movies.map(m => `<div class="slick-slide-item" style="padding: 0 5px;"><div class="movie-card"><div class="poster-visible"><img src="${m.img}" alt="${m.title}"></div><div class="hover-details-card"><div class="hover-backdrop" style="background-image: url('${m.bg}'); cursor: pointer;" onclick="window.location.href='${m.link_detalle}'"></div><div class="hover-info"><div class="hover-buttons"><button class="btn-mini-play" onclick="playCinematic('${m.link_ver}')"><i class="fa fa-play"></i></button><button class="btn-mini-icon btn-lista-${m.id}" onclick="toggleMiLista(${m.id})"><i class="fa ${m.in_list ? 'fa-check' : 'fa-heart'}"></i></button></div><h4 style="cursor:pointer;" onclick="window.location.href='${m.link_detalle}'">${m.title}</h4><div class="hover-meta"><span style="color:#46d369; font-weight:bold;">98% para ti</span><span class="badge badge-hd" style="font-size:0.6rem;">+${m.age}</span></div><p style="font-size:0.75rem; color:#ccc; margin:0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${m.desc}</p></div></div></div></div>`).join('')}</div></div>`;
        }
    });
    $('#rows-container').html(html);
}

// --- C. HELPERS & UTILIDADES ---
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

// =========================================================
// 5. INICIALIZACIÓN DE CARRUSELES (CORREGIDO DESCUADRE)
// =========================================================
function inicializarCarruseles() {
    // 1. Inicializar Hero (Principal)
    if ($('.hero-carousel').length > 0) {
        if (!$('.hero-carousel').hasClass('slick-initialized')) {
            $('.hero-carousel').slick({
                dots: true, infinite: true, speed: 800, fade: true,
                cssEase: 'linear', autoplay: true, autoplaySpeed: 4000,
                arrows: false, pauseOnHover: false
            });
        }
    }

    // 2. Inicializar Filas (Netflix)
    if ($('.slick-row').length > 0) {
        if (!$('.slick-row').hasClass('slick-initialized')) {
            $('.slick-row').slick({
                dots: false, infinite: true, speed: 500,
                slidesToShow: 6, slidesToScroll: 3,
                responsive: [
                    { breakpoint: 1600, settings: { slidesToShow: 5, slidesToScroll: 2 } },
                    { breakpoint: 1100, settings: { slidesToShow: 4, slidesToScroll: 2 } },
                    { breakpoint: 800, settings: { slidesToShow: 3, slidesToScroll: 1 } },
                    { breakpoint: 500, settings: { slidesToShow: 2, slidesToScroll: 1 } }
                ],
                prevArrow: '<button type="button" class="slick-prev custom-arrow"><i class="fa fa-chevron-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next custom-arrow"><i class="fa fa-chevron-right"></i></button>'
            });
        }
    }

    // Esto arregla el descuadre cuando vienes de login/splash
    setTimeout(function () {
        $('.hero-carousel, .slick-row').slick('setPosition');
        // A veces se necesita un trigger de resize manual
        $(window).trigger('resize');
    }, 200);
}

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

// Login Functions
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
// FUNCIONES NUEVAS (ESTILO PRIME VIDEO / NETFLIX)
// =========================================================

let generoActualId = null; // Variable global para saber dónde estamos

// 1. CARGA LA VISTA DE FILAS (2 Pelis + 2 Series)
function cargarVistaGenero(generoId) {
    $('#loading-initial').show();
    
    // 1. Ocultar todo lo demás
    $('#hero-wrapper').hide();
    $('#rows-container').hide();
    $('#grid-expandido').hide().empty();
    $('#genre-landing-container').hide().empty(); 

    // 2. GESTIÓN DEL MENÚ ACTIVE (NUEVO)
    // Como estamos viendo una mezcla, quitamos el active de Pelis y Series
    $('.nav-link').removeClass('active'); 
    // O si prefieres mantener 'Inicio' activo, usa selectores específicos:
    // $('a[href*="peliculas"], a[href*="series"]').removeClass('active');

    // 3. Pedir datos a la API
    let urlApi = BASE_URL + "index.php/api/catalogo?genero=" + generoId;

    $.ajax({
        url: urlApi,
        dataType: 'json',
        success: function(response) {
            $('#loading-initial').hide();
            $('.content').fadeIn();

            if (response.modo === 'landing_genero') {
                renderGenreRows(response.secciones, response.titulo);
                $('#genre-landing-container').fadeIn();
            }
        },
        error: function() {
            $('#loading-initial').hide();
            console.error("Error cargando género");
        }
    });
}

// 2. PINTA LAS FILAS Y EL BOTÓN "VER MÁS"
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
                    ${movies.map(m => `<div class="slick-slide-item" style="padding: 0 5px;">
                    ${generarHtmlTarjeta(m)}  </div>`).join('')}
                    </div>
                </div>
            </div>`;
        }
    });

    $('#genre-landing-container').html(html);

    // Inicializamos los carruseles de estas nuevas filas
    setTimeout(() => {
        $('.slick-row').slick({
            dots: false, infinite: false, speed: 500, slidesToShow: 6, slidesToScroll: 3,
            prevArrow: '<button type="button" class="slick-prev custom-arrow"><i class="fa fa-chevron-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next custom-arrow"><i class="fa fa-chevron-right"></i></button>',
            responsive: [ 
                { breakpoint: 1400, settings: { slidesToShow: 5 } },
                { breakpoint: 1100, settings: { slidesToShow: 4 } }, 
                { breakpoint: 500, settings: { slidesToShow: 2 } } 
            ]
        });
    }, 100);
}

// 3. ABRE EL GRID DE 6 COLUMNAS (VER TODO)
window.abrirGridExpandido = function(tipoId, tituloSeccion) {
    // 1. GESTIÓN DEL MENÚ ACTIVE (NUEVO)
    // Limpiamos primero
    $('.nav-link').removeClass('active');

    // Activamos según el tipo que hemos pulsado
    if (tipoId == 1) {
        // Buscamos el link que contenga "peliculas" en su href
        $('a[href*="peliculas"]').addClass('active');
    } else if (tipoId == 2) {
        // Buscamos el link que contenga "series" en su href
        $('a[href*="series"]').addClass('active');
    }

    // 2. Ocultamos las filas y mostramos el grid
    $('#genre-landing-container').hide();
    $('#loading-initial').show();
    $('#grid-expandido').empty();

    let urlApi = BASE_URL + "index.php/api/catalogo?genero=" + generoActualId; 

    $.ajax({
        url: urlApi,
        dataType: 'json',
        success: function(response) {
            $('#loading-initial').hide();
            
            let datosFiltrados = [];
            if(response.secciones) {
                response.secciones.forEach(sec => {
                    if (sec.tipo == tipoId) {
                        datosFiltrados = sec.data;
                    }
                });
            }

            // Pintamos el Header con botón de volver
            let htmlGrid = `
                <div style="grid-column: 1/-1; margin-bottom: 30px; display:flex; align-items:center; gap:15px;">
                    <button onclick="volverALandingGenero()" style="background:none; border:none; color:white; font-size:1.5rem; cursor:pointer;"><i class="fa fa-arrow-left"></i></button>
                    <h2 style="color:white; margin:0;">${tituloSeccion}</h2>
                </div>
            `;

            // Pintamos las tarjetas
            const movies = formatData(datosFiltrados);
            movies.forEach(m => {
            htmlGrid += generarHtmlTarjeta(m); // <--- USAMOS LA NUEVA FUNCIÓN
        });

            // Mostramos el contenedor con estilo Grid
            $('#grid-expandido').html(htmlGrid).css('display', 'grid').show();
        }
    });
};

// 4. VOLVER ATRÁS (Del Grid a las Filas)
window.volverALandingGenero = function() {
    // 1. GESTIÓN MENÚ (NUEVO)
    // Al volver a la vista mixta, quitamos el active de nuevo
    $('.nav-link').removeClass('active');
    
    // 2. Lógica visual
    $('#grid-expandido').hide();
    $('#genre-landing-container').fadeIn();
};

// 5. CARGAR PORTADA (Cuando no hay filtro)
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
// =========================================================
// HELPER: Generador de HTML de Tarjeta (DISEÑO UNIFICADO)
// =========================================================
// =========================================================
// HELPER: Generador de HTML de Tarjeta (DISEÑO NETFLIX PREVIEW)
// =========================================================
// =========================================================
// HELPER: Generador de Tarjetas (ESTILO PRIME/NETFLIX DEFINITIVO)
// =========================================================
function generarHtmlTarjeta(item) {
    // 1. URLs de Imágenes
    let cleanBase = BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';
    
    // Poster (Vertical)
    let imgPoster = item.img || item.imagen;
    if (imgPoster && !imgPoster.startsWith('http')) {
        imgPoster = cleanBase + 'assets/img/' + imgPoster;
    }

    // Fondo (Horizontal) - Fallback al poster si no hay fondo
    let imgBg = item.bg || item.imagen_bg;
    if (!imgBg) {
        imgBg = imgPoster;
    } else if (!imgBg.startsWith('http')) {
        imgBg = cleanBase + 'assets/img/' + imgBg;
    }

    // 2. Datos y Enlaces
    let titulo = item.title || item.titulo;
    let linkDetalle = item.link_detalle || (cleanBase + 'detalle/' + item.id);
    let linkVer = item.link_ver || (cleanBase + 'ver/' + item.id);
    let edad = item.age || item.edad_recomendada || "12";
    let desc = item.desc || item.descripcion || "Sin descripción disponible.";
    

    // Estado de "Mi Lista" (si lo tenemos disponible)
    let iconLista = item.in_list ? 'fa-check' : 'fa-plus';

    // 3. RETORNAR HTML (Estructura exacta para tu CSS)
    return `
    <div class="movie-card" onclick="window.location.href='${linkDetalle}'">
        
        <div class="poster-visible">
            <img src="${imgPoster}" alt="${titulo}" loading="lazy">
        </div>

        <div class="hover-details-card">
            
            <div class="hover-backdrop" style="background-image: url('${imgBg}');"></div>
            
            <div class="hover-info">
                
                <div class="hover-buttons">
                    <button class="btn-mini-play" onclick="event.stopPropagation(); playCinematic('${linkVer}')">
                        <i class="fa fa-play"></i>
                    </button>
                    <button class="btn-mini-icon btn-lista-${item.id}" onclick="event.stopPropagation(); toggleMiLista(${item.id})">
                        <i class="fa ${iconLista}"></i>
                    </button>
                    <button class="btn-mini-icon" onclick="event.stopPropagation(); window.location.href='${linkDetalle}'">
                        <i class="fa fa-chevron-down"></i>
                    </button>
                </div>

                <h4>${titulo}</h4>

                <div class="hover-meta">
                    <span style="color:#46d369; font-weight:bold;">${matchScore}% para ti</span>
                    <span class="badge badge-hd" style="border:1px solid #aaa; padding:0 4px; border-radius:2px; color:#ddd; font-size:0.65rem;">+${edad}</span>
                    <span>HD</span>
                </div>

                <p style="font-size:0.75rem; color:#ccc; margin:0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    ${desc}
                </p>

            </div>
        </div>
    </div>`;
}