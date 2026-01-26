let paginaActual = 1;
let cargando = false;
let finDeContenido = false;
// API Key para el buscador híbrido (Asegúrate de que es válida)
const OMDb_API_KEY = '78a51c36'; 

$(document).ready(function () {
    
    // =========================================================
    // 1. INICIO & SPLASH SCREEN (LÓGICA DE CARGA)
    // =========================================================
    function iniciarWeb() {
        $('#view-splash').fadeOut(600, function () {
            $(this).removeClass('active');
            
            if ($('#view-profiles').length > 0) {
                $('#view-profiles').addClass('active').css('display', 'flex');
            } else {
                const viewHome = $('#view-home');
                viewHome.addClass('active').css('opacity', 0).show();
                
                // Animación suave + Inicialización de Carruseles cuando es visible
                viewHome.animate({ opacity: 1 }, 400, function() {
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
        $('#view-splash').hide().removeClass('active');
        $('#view-home').show().addClass('active');
        inicializarCarruseles();
    }

    // =========================================================
    // 2. SCROLL INFINITO (Solo para Grid)
    // =========================================================
    $('.view-section').on('scroll', function () {
        if ($('#grid-container').length > 0 && !finDeContenido && !cargando) {
            if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 300) {
                cargarMasPeliculas();
            }
        }
    });

    // =========================================================
    // 3. EFECTOS VISUALES
    // =========================================================
    $(document).on('mouseenter', '.movie-poster', function () {
        const bgUrl = $(this).data('bg');
        if (bgUrl) $('#dynamic-bg').css('background-image', `url(${bgUrl})`).css('opacity', '0.5');
    });

    $(document).on('mouseleave', '.movie-poster', function () {
        $('#dynamic-bg').css('opacity', '0.2');
    });

    // =========================================================
    // 4. BUSCADOR HÍBRIDO (LOCAL + GLOBAL) - ¡CORREGIDO!
    // =========================================================
    $("#global-search").autocomplete({
        minLength: 1, // Busca desde la primera letra
        source: function (request, response) {
            var csrfName = $('.txt_csrftoken').attr('name');
            var csrfHash = $('.txt_csrftoken').val();
            
            let combinedResults = [];

            // A. BUSCAR EN TU API LOCAL
            $.ajax({
                url: BASE_URL + "api/buscador/autocompletar",
                type: "post", dataType: "json",
                data: { search: request.term, [csrfName]: csrfHash },
                success: function (localData) {
                    if (localData.token) $('.txt_csrftoken').val(localData.token);
                    
                    // Añadir resultados locales
                    if (localData.data) {
                        const locals = localData.data.map(i => ({ ...i, source: 'local' }));
                        combinedResults = combinedResults.concat(locals);
                    }

                    // B. BUSCAR EN API GLOBAL (OMDb) SIMULTÁNEAMENTE
                    fetch(`https://www.omdbapi.com/?apikey=${OMDb_API_KEY}&s=${request.term}`)
                        .then(r => r.json())
                        .then(extData => {
                            if (extData.Response === "True") {
                                // Filtramos para no repetir lo que ya tenemos en local (opcional)
                                const externals = extData.Search.slice(0, 3).map(m => ({
                                    label: m.Title,
                                    value: m.imdbID, // ID tipo "tt12345"
                                    img: (m.Poster !== "N/A" ? m.Poster : BASE_URL + 'assets/img/no-poster.jpg'),
                                    year: m.Year,
                                    source: 'external'
                                }));
                                combinedResults = combinedResults.concat(externals);
                            }
                            // ENVIAR TODOS LOS RESULTADOS AL DESPLEGABLE
                            response(combinedResults);
                        });
                }
            });
        },
        // PERSONALIZACIÓN VISUAL DEL DESPLEGABLE
        create: function () {
            $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
                // Etiqueta diferente según origen
                const badge = item.source === 'local' 
                    ? '<span style="color:#46d369; font-size:0.6rem; border:1px solid #46d369; padding:1px 3px; border-radius:3px; float:right;">EN CATÁLOGO</span>' 
                    : '<span style="color:#00d2ff; font-size:0.6rem; border:1px solid #00d2ff; padding:1px 3px; border-radius:3px; float:right;">GLOBAL</span>';

                return $("<li>")
                    .append(`
                        <div class="ui-menu-item-wrapper" style="display:flex; align-items:center; gap:10px; border-bottom:1px solid rgba(255,255,255,0.1);">
                            <img src="${item.img}" style="width:35px; height:50px; object-fit:cover; border-radius:3px;">
                            <div style="flex:1;">
                                <div style="font-weight:bold; font-size:0.9rem; color:white;">${item.label}</div>
                                <div style="font-size:0.75rem; color:#aaa;">${item.year || ''} ${badge}</div>
                            </div>
                        </div>`)
                    .appendTo(ul);
            };
        },
        // AL SELECCIONAR -> IR A DETALLE (Local o Externo)
        select: function (event, ui) {
            $('#global-search').val(ui.item.label);
            // Gracias a la ruta 'detalle/(:segment)', esto funciona para ID numérico y texto
            window.location.href = BASE_URL + "detalle/" + ui.item.value;
            return false;
        }
    });
});

// =========================================================
// 5. INICIALIZACIÓN DE CARRUSELES (FIX VISUAL)
// =========================================================
function inicializarCarruseles() {
    // Hero
    if ($('.hero-carousel').length > 0 && !$('.hero-carousel').hasClass('slick-initialized')) {
        $('.hero-carousel').slick({
            dots: true, infinite: true, speed: 800, fade: true, cssEase: 'linear',
            autoplay: true, autoplaySpeed: 4000, arrows: false, pauseOnHover: false
        });
    }
    // Filas Netflix
    if ($('.slick-row').length > 0 && !$('.slick-row').hasClass('slick-initialized')) {
        $('.slick-row').slick({
            dots: false, infinite: true, speed: 500, slidesToShow: 6, slidesToScroll: 3,
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
    // Forzar recálculo
    $('.hero-carousel, .slick-row').slick('setPosition');
}

// =========================================================
// 6. CARGA DE DATOS (CONECTADO A NUEVA API)
// =========================================================
function cargarMasPeliculas() {
    if (cargando) return;
    cargando = true;
    paginaActual++;

    const urlParams = new URLSearchParams(window.location.search);
    const genero = urlParams.get('genero');
    
    // Llamada a la API RESTful
    let urlApi = BASE_URL + "api/catalogo?page=" + paginaActual;
    if (genero) urlApi += "&genero=" + genero;

    $.ajax({
        url: urlApi,
        type: "get", dataType: "json",
        success: function (response) {
            // La API devuelve { data: [...] }
            const listaPeliculas = response.data;

            if (!listaPeliculas || listaPeliculas.length === 0) {
                finDeContenido = true;
                cargando = false;
                return;
            }

            const moviesFormatted = listaPeliculas.map(item => ({
                id: item.id,
                title: item.titulo,
                img: item.imagen.startsWith('http') ? item.imagen : BASE_URL + 'assets/img/' + item.imagen,
                bg: item.imagen_bg.startsWith('http') ? item.imagen_bg : BASE_URL + 'assets/img/' + item.imagen_bg,
                premium: item.nivel_acceso == '2',
                age: item.edad_recomendada, 
                desc: item.descripcion,
                in_list: item.en_mi_lista || false,
                link_detalle: BASE_URL + 'detalle/' + item.id,
                link_ver: BASE_URL + 'ver/' + item.id
            }));

            appendMoviesToGrid(moviesFormatted);
            cargando = false;
        },
        error: function () { 
            console.error("Error API"); 
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
// 7. UTILIDADES (LISTA, PLAY, LOGIN)
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

    // Usamos la nueva ruta API para toggle
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

window.logout = function () { window.location.href = BASE_URL + 'auth/logout'; }

function attemptLogin(id, username, planId) {
    if (planId == 3) {
        realizarCambioPerfil(id, ''); 
    } else {
        $('#selectedUserId').val(id);
        $('#modalUser').text(username);
        $('#passwordInput').val('');
        $('#errorMsg').hide();
        $('#modalAuth').fadeIn().css('display', 'flex');
        setTimeout(() => $('#passwordInput').focus(), 100); 
    }
}

window.closeModal = function() { $('#modalAuth').fadeOut(); };
window.submitSwitchProfile = function() { realizarCambioPerfil($('#selectedUserId').val(), $('#passwordInput').val()); };
$(document).on('keypress', '#passwordInput', function (e) { if(e.which === 13) submitSwitchProfile(); });

function realizarCambioPerfil(id, password) {
    let csrfName = $('.txt_csrftoken').attr('name');
    let csrfHash = $('.txt_csrftoken').val();

    $.ajax({
        url: BASE_URL + "auth/login",
        type: "post", dataType: "json",
        data: { id: id, password: password, [csrfName]: csrfHash },
        success: function(response) {
            if (response.token) $('.txt_csrftoken').val(response.token);
            if (response.status === 'success') {
                window.location.reload(); 
            } else {
                $('#errorMsg').text(response.msg).show();
                if(typeof $.fn.effect === 'function') $('.modal-content').effect('shake', {times:3}, 300);
            }
        },
        error: function() { alert('Error de conexión.'); }
    });
}