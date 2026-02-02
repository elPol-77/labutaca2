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
    // 2. LÓGICA PELÍCULAS (SPA) - CORREGIDA
    // =========================================================
    if ($('#view-peliculas-full').length > 0) {
        
        const urlParams = new URLSearchParams(window.location.search);
        const generoUrl = urlParams.get('genero');

        // A. CASO: HAY FILTRO (Mostrar Grid, Ocultar Portada)
        if (generoUrl) {
            console.log("🔍 Modo Filtro Activado:", generoUrl);
            
            modoGridActivo = true; // Activamos el scroll infinito
            
            // 1. Ocultamos lo que no queremos ver
            $('#hero-wrapper').hide().empty();
            $('#rows-container').hide().empty();
            
            // 2. Mostramos el Grid
            $('#grid-container').show();
            
            // 3. Cargamos datos
            cargarGridPeliculasAPI(generoUrl);
        } 
        
        // B. CASO: PORTADA GENERAL (Mostrar Portada, Ocultar Grid)
        else {
            console.log("🎬 Modo Portada (Netflix Style)");
            
            modoGridActivo = false; // Desactivamos scroll infinito del grid
            
            // 1. Ocultamos y LIMPIAMOS el Grid para que no salga abajo
            $('#grid-container').hide().empty(); 
            
            // 2. Mostramos contenedores de portada
            $('#hero-wrapper').show();
            $('#rows-container').show();
            
            // 3. Llamada a la API de Portada
            let cleanBase = BASE_URL.endsWith('/') ? BASE_URL : BASE_URL + '/';
            
            fetch(cleanBase + 'api/peliculas-landing')
                .then(r => r.json())
                .then(data => {
                    $('#loading-initial').hide();
                    $('.content').fadeIn();
                    
                    if (data.carrusel) renderHeroCarousel(data.carrusel);
                    if (data.secciones) renderNetflixRows(data.secciones);
                    
                    inicializarCarruseles();
                })
                .catch(e => {
                    console.error("Error Landing:", e);
                    $('#loading-initial').hide();
                });
        }

        // C. INTERCEPTOR DE CLICS (Para cambiar entre modos sin recargar)
        $(document).on('click', '.trigger-filtro', function(e) {
            e.preventDefault(); 
            const genero = $(this).data('genero');
            
            // Cambiar URL
            const newUrl = BASE_URL + "peliculas?genero=" + encodeURIComponent(genero);
            window.history.pushState({path: newUrl}, '', newUrl);

            // Cambiar a MODO GRID manualmente
            modoGridActivo = true;
            $('#hero-wrapper').hide();
            $('#rows-container').hide();
            $('#grid-container').empty().show(); // Vaciamos y mostramos
            $('#loading-initial').show();
            
            cargarGridPeliculasAPI(genero);
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

}); // <--- FIN DOCUMENT READY

// =========================================================
// 5. FUNCIONES GLOBALES (FUERA DEL READY)
// =========================================================

// --- A. CARGA Y PINTADO DEL GRID (VERSIÓN MANUAL DEFINITIVA) ---
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

    // 3. Construir URL (Asegurando barra e index.php)
    let baseUrlClean = BASE_URL;
    if (!baseUrlClean.endsWith('/')) {
        baseUrlClean += '/';
    }
    // Añadimos index.php por si acaso el servidor no tiene rewrite rules
    let urlApi = baseUrlClean + "index.php/api/catalogo?page=" + paginaActual;

    if (genero) {
        urlApi += "&genero=" + encodeURIComponent(genero);
    }

    console.log("🎨 Pidiendo:", urlApi);

    // 4. AJAX y PINTADO MANUAL
    $.ajax({
        url: urlApi,
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log("📦 Respuesta:", response); // Debug

            $('#loading-initial').hide();
            $('.content').fadeIn(); // Aseguramos que el contenedor principal sea visible

            // Forzamos visibilidad y estilo Grid
            $('#grid-container').css({
                'display': 'grid',
                'grid-template-columns': 'repeat(auto-fill, minmax(180px, 1fr))',
                'gap': '20px',
                'padding': '120px 4% 40px 4%'
            }).show();

            cargando = false;

            if (response.data && response.data.length > 0) {
                let htmlAcumulado = '';

                response.data.forEach(peli => {
                    // Ajuste ruta imagen
                    let imgPoster = peli.imagen;
                    if (!imgPoster.startsWith('http')) {
                        imgPoster = baseUrlClean + 'assets/img/' + imgPoster;
                    }

                    // HTML Tarjeta
                    htmlAcumulado += `
                        <div class="movie-card-grid" style="position:relative; transition: transform 0.3s; cursor:pointer;" onclick="window.location.href='${baseUrlClean}detalle/${peli.id}'">
                            <div class="poster-wrapper" style="border-radius: 8px; overflow: hidden; height: 280px; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
                                <img src="${imgPoster}" alt="${peli.titulo}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="movie-info" style="margin-top: 10px;">
                                <h4 style="color: white; font-size: 0.9rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${peli.titulo}</h4>
                                <span style="color: #aaa; font-size: 0.8rem;">${peli.anio || ''}</span>
                            </div>
                        </div>
                    `;
                });

                $('#grid-container').append(htmlAcumulado);

            } else {
                if (paginaActual === 1) {
                    $('#grid-container').html('<h3 style="color:white; text-align:center; grid-column: 1/-1; padding: 50px;">No hay contenido disponible en esta categoría.</h3>');
                }
                finDeContenido = true;
            }
        },
        error: function (xhr, status, error) {
            console.error("❌ Error JS:", error);
            cargando = false;
            // Si falla, mostramos mensaje amigable
            if (paginaActual === 1) {
                $('#grid-container').html('<div style="color:red; text-align:center; grid-column:1/-1">Error cargando películas.<br>Verifica la consola.</div>').show();
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
    setTimeout(function() {
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