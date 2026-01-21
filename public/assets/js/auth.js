let currentUserId = null;

// Abrir el modal
function openLogin(id, name) {
    currentUserId = id;
    $('#modalUser').text(name);
    $('#passwordInput').val('');
    $('#errorMsg').hide();

    // Animación de entrada
    $('#modalAuth').css('display', 'flex').hide().fadeIn(300);
    $('#passwordInput').focus();
}

// Cerrar el modal
function closeModal() {
    $('#modalAuth').fadeOut(300);
}

// Detectar tecla ENTER
$(document).ready(function () {
    $('#passwordInput').on('keydown', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault(); 
            doLogin();
        }
    });
});

// Proceso de Login con AJAX y CSRF
function doLogin() {
    const pass = $('#passwordInput').val();

    // 1. Obtenemos los tokens del HTML (Que coinciden con header.php)
    const csrfName = $('.txt_csrftoken').attr('name');
    const csrfHash = $('.txt_csrftoken').val();

    $.ajax({
        url: BASE_URL + 'auth/login', 
        type: "POST",
        dataType: "json",
        data: {
            id: currentUserId,
            password: pass,
            [csrfName]: csrfHash // Enviamos el token actual
        },
        success: function (response) {
            // 2. IMPORTANTE: Actualizamos el token siempre, sea éxito o error.
            // Si no hacemos esto, el segundo intento fallará con 403 Forbidden.
            if(response.token) {
                $('.txt_csrftoken').val(response.token);
            }

            if (response.status === 'success') {
                window.location.href = BASE_URL;
            } else {
                // Si hay error, mostrar mensaje y vibrar
                $('#errorMsg').show();
                $('.modal-content')
                    .animate({ marginLeft: "-10px" }, 50)
                    .animate({ marginLeft: "10px" }, 50)
                    .animate({ marginLeft: "0px" }, 50);

                // Limpiar input
                $('#passwordInput').val('');
            }
        },
        error: function (xhr) {
            console.error("Error Login:", xhr.status);
            alert("Error de conexión. Recarga la página.");
        }
    });
}