// Variable global para el CSRF
let csrfName = $('.txt_csrftoken').attr('name');
let csrfHash = $('.txt_csrftoken').val();

function attemptLogin(id, username, planId) {
    // 1. SI ES NIÑO (PLAN 3) -> LOGIN DIRECTO
    if (planId == 3) {
        realizarLoginAjax(id, ''); // Enviamos contraseña vacía
    } 
    // 2. SI ES ADULTO -> PEDIR PIN
    else {
        $('#selectedUserId').val(id);
        $('#modalUser').text(username);
        $('#passwordInput').val('');
        $('#errorMsg').hide();
        $('#modalAuth').fadeIn().css('display', 'flex');
        $('#passwordInput').focus();
    }
}

function closeModal() {
    $('#modalAuth').fadeOut();
}

function submitLogin() {
    const id = $('#selectedUserId').val();
    const pass = $('#passwordInput').val();
    realizarLoginAjax(id, pass);
}

// Función común para hacer la llamada al servidor
function realizarLoginAjax(id, password) {
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
            // Actualizamos token CSRF siempre
            if (response.token) {
                $('.txt_csrftoken').val(response.token);
                csrfHash = response.token;
            }

            if (response.status === 'success') {
                // Éxito: Recargar la página para ir al Home
                window.location.href = BASE_URL;
            } else {
                // Error: Mostrar mensaje en el modal
                $('#errorMsg').text(response.msg).show();
                // Si estábamos intentando entrar como niño y falló (raro), no hacemos nada visual extra
            }
        },
        error: function() {
            alert('Error de conexión con el servidor');
        }
    });
}

// Permitir intro en el input
$('#passwordInput').on('keypress', function (e) {
    if(e.which === 13) submitLogin();
});