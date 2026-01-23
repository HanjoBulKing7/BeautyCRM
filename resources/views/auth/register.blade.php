<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Bonita - Registro</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: url("{{ asset('img/fondo-galletas.png') }}") no-repeat center center fixed;
            background-size: cover; /* ✅ se ajusta a la pantalla completa */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-box {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(8px);
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            max-width: 450px;
            width: 100%;
            animation: fadeIn 0.8s ease-in-out;
        }

        .login-logo {
            width: 160px;
            margin-bottom: 1.5rem;
            animation: zoomIn 1s ease;
        }

        h2 {
            font-weight: 700;
            color: #999189; /* Color principal de Beauty Bonita */
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #999189; /* Color principal de Beauty Bonita */
            box-shadow: 0 0 0 0.25rem rgba(153, 145, 137, 0.2); /* Color con transparencia */
        }

        .btn-login {
            background: linear-gradient(135deg, #999189, #c8c2bc); /* Colores de Beauty Bonita */
            border: none;
            padding: 14px;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #c8c2bc, #999189); /* Invertido para hover */
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(153, 145, 137, 0.3); /* Color con transparencia */
        }

        .btn-outline-secondary {
            border-radius: 12px;
            padding: 12px;
        }

        .text-beauty {
            color: #999189; /* Color principal para textos */
        }

        .text-beauty-light {
            color: #c8c2bc; /* Color secundario */
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.7); }
            to { opacity: 1; transform: scale(1); }
        }

        /* Estilos para el selector de rol */
        .role-option {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .role-option:hover {
            border-color: #999189;
            background-color: rgba(153, 145, 137, 0.05);
        }

        .role-option.selected {
            border-color: #999189;
            background-color: rgba(153, 145, 137, 0.1);
        }

        .role-radio {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-box text-center">
        <!-- Logo -->
        <img src="{{ asset('iconos/logo blanco.png') }}" alt="Logo Beauty Bonita" class="login-logo">

        <h2>Registro de Usuario</h2>
        <p class="text-muted">Crea tu cuenta para acceder al sistema</p>

        <!-- Mensaje de error -->
        @if ($errors->any())
            <div class="alert alert-danger text-start">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Errores en el formulario</strong>
                <ul class="mb-0 mt-2 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Mensajes de sesión -->
        @if(session('status'))
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('status') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success mb-4">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="d-grid gap-2 mb-3">
            <a href="{{ route('google.employee.redirect') }}" class="btn btn-outline-secondary">
                <i class="fab fa-google me-2"></i> Continuar con Google
            </a>
        </div>

        <div class="text-muted small mb-2">— o —</div>

        <!-- Formulario -->
        <form id="registerForm" method="POST" action="{{ route('register') }}" autocomplete="off" class="mt-3">
            @csrf

            <div class="mb-3 text-start">
                <label for="name" class="form-label">
                    <i class="fas fa-user me-2"></i>Nombre completo
                </label>
                <input type="text" id="name" name="name" class="form-control" 
                       value="{{ old('name') }}" required placeholder="Ingrese su nombre completo" autocomplete="name">
            </div>

            <div class="mb-3 text-start">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Correo electrónico
                </label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="{{ old('email') }}" required placeholder="Ingrese su correo electrónico" autocomplete="email">
            </div>

            <div class="mb-3 text-start">
                <label for="telefono" class="form-label">Teléfono</label>
                <input
                    type="text"
                    class="form-control"
                    id="telefono"
                    name="telefono"
                    value="{{ old('telefono') }}"
                    required
                    placeholder="Ej. 55 1234 5678"
                >
            </div>

            <div class="mb-3 text-start">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Contraseña
                </label>
                <div class="input-group">
                    <input type="password" id="input_password" name="password" class="form-control" 
                           required placeholder="Ingrese su contraseña" autocomplete="new-password">
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" 
                            aria-label="Mostrar contraseña" title="Mostrar/ocultar contraseña">
                        <i class="bi bi-eye-slash" id="passwordIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3 text-start">
                <label for="password_confirmation" class="form-label">
                    <i class="fas fa-lock me-2"></i>Confirmar Contraseña
                </label>
                <div class="input-group">
                    <input type="password" id="input_password_confirmation" name="password_confirmation" class="form-control" 
                           required placeholder="Confirme su contraseña" autocomplete="new-password">
                    <button type="button" class="btn btn-outline-secondary" id="togglePasswordConfirmation" 
                            aria-label="Mostrar contraseña" title="Mostrar/ocultar contraseña">
                        <i class="bi bi-eye-slash" id="passwordConfirmationIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-login text-white">
                    <i class="fas fa-user-plus me-2"></i> Registrarse
                </button>
            </div>

            <div class="text-center">
                <p class="small text-muted mb-0">
                    ¿Ya tienes cuenta?
                    <a href="{{ route('login.form') }}" class="text-beauty text-decoration-none">Inicia sesión</a>
                </p>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle para contraseña
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('input_password');
            const passwordIcon = document.getElementById('passwordIcon');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                passwordIcon.classList.toggle('bi-eye');
                passwordIcon.classList.toggle('bi-eye-slash');
            });

            // Toggle para confirmar contraseña
            const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
            const passwordConfirmation = document.getElementById('input_password_confirmation');
            const passwordConfirmationIcon = document.getElementById('passwordConfirmationIcon');

            togglePasswordConfirmation.addEventListener('click', function() {
                const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmation.setAttribute('type', type);
                passwordConfirmationIcon.classList.toggle('bi-eye');
                passwordConfirmationIcon.classList.toggle('bi-eye-slash');
            });

            // Efectos para los roles
            const roleOptions = document.querySelectorAll('.role-option');
            
            roleOptions.forEach(option => {
                const radio = option.querySelector('.role-radio');
                
                // Marcar como seleccionado al cargar la página
                if (radio.checked) {
                    option.classList.add('selected');
                }
                
                // Marcar como seleccionado al hacer clic
                option.addEventListener('click', function() {
                    roleOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    radio.checked = true;
                });
            });
        });
    </script>
</body>
</html>