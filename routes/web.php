<?php

use App\Http\Controllers\CitaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\ServiciosPublicController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\AgendarCitaPublicController;
use App\Http\Controllers\CategoriaServicioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProductosPublicController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardCitasController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use App\Http\Controllers\MisReservasController;
use App\Http\Controllers\CuponController;
use App\Http\Controllers\PublicMediaController;

// ✅ ROOT: siempre manda a Home pública
Route::get('/', function () {
    return redirect()->route('cliente.home'); // /home
})->name('root');

// =============================
// Rutas Públicas (Cliente)
// =============================
Route::get('/home', [HomeController::class, 'index'])
    ->name('cliente.home');

Route::get('/media/public/{path}', [PublicMediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.public');

Route::view('/galeria', 'galeria')->name('galeria');
Route::view('/nosotros', 'nosotros')->name('nosotros');

Route::get('/servicio', [ServiciosPublicController::class, 'index'])
    ->name('servicio.public');

// Productos públicos
Route::get('/productos', [ProductosPublicController::class, 'index'])
    ->name('productos.public');

// =============================
// Agendar cita (vista pública)
// =============================
Route::get('/agendar-cita', [AgendarCitaPublicController::class, 'create'])
    ->name('agendarcita.create');

Route::post('/agendar-cita', [AgendarCitaPublicController::class, 'store'])
    ->middleware('auth')
    ->name('agendarcita.store');

Route::get('/agendar-cita/horas-disponibles', [AgendarCitaPublicController::class, 'horasDisponibles'])
    ->name('agendarcita.horasDisponibles');

Route::get('/agendar-cita/availability-month', [AgendarCitaPublicController::class, 'availabilityMonth'])
    ->name('agendarcita.availabilityMonth');

// ===========================================================
// Webhook Stripe (producción & más seguro)
// ===========================================================
Route::post('/stripe/webhook', [PagoController::class, 'webhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([ValidateCsrfToken::class]);

// =============================
// Vistas cliente
// =============================
Route::get('/anticipo', function () {
    return view('cliente.anticipo');
})->name('cliente.anticipo');

Route::get('/reserva', function () {
    return view('cliente.reserva');
})->name('cliente.reserva');

Route::get('/sucursal', function () {
    return view('cliente.sucursal');
})->name('cliente.sucursal');

// =============================
// Autenticación
// =============================
Route::get('/login', function () {
    return view('login');
})->name('login.form');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Register (público, clientes)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Google Login (clientes / general)
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])
    ->name('google.redirect')
    ->middleware('guest');

Route::get('/auth/google/login/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('google.callback')
    ->middleware('guest');

// ✅ Invitación empleado (link firmado expirable)  (DEJAR SOLO UNA VEZ)
Route::get('/invitation/employee/{user}', [AuthController::class, 'acceptEmployeeInvitation'])
    ->name('invitation.employee')
    ->middleware('signed');

// ✅ Google OAuth para empleados (incluye Calendar + offline)
Route::get('/auth/google/employee', [AuthController::class, 'redirectEmployeeToGoogle'])
    ->name('google.employee.redirect')
    ->middleware('guest');

Route::get('/auth/google/employee/callback', [AuthController::class, 'handleEmployeeGoogleCallback'])
    ->name('google.employee.callback')
    ->middleware('guest');

// (Opcional) mantiene tu flujo existente de GoogleCalendarController si lo sigues usando
Route::get('/auth/google/callback', [GoogleCalendarController::class, 'callback'])
    ->name('google.calendar.callback');

// =============================
// Rutas de Administración
// =============================
Route::prefix('admin')->name('admin.')->group(function () {
    // Ventas de productos
    Route::prefix('productoventa')->name('productoventa.')->group(function () {
        Route::get('/', [App\Http\Controllers\VentasProductoController::class, 'index'])
            ->name('index')
            ->middleware('auth');
        Route::get('/create', [App\Http\Controllers\VentasProductoController::class, 'create'])
            ->name('create')
            ->middleware('auth');
        Route::post('/', [App\Http\Controllers\VentasProductoController::class, 'store'])
            ->name('store')
            ->middleware('auth');
        Route::get('/{venta}', [App\Http\Controllers\VentasProductoController::class, 'show'])
            ->name('show')
            ->middleware('auth');
        Route::get('/{venta}/edit', [App\Http\Controllers\VentasProductoController::class, 'edit'])
            ->name('edit')
            ->middleware('auth');
        Route::put('/{venta}', [App\Http\Controllers\VentasProductoController::class, 'update'])
            ->name('update')
            ->middleware('auth');
        Route::delete('/{venta}', [App\Http\Controllers\VentasProductoController::class, 'destroy'])
            ->name('destroy')
            ->middleware('auth');
    });
    // AJAX para crear categoría desde el formulario de servicios
    Route::post('/categoriaservicios/ajax', [CategoriaServicioController::class, 'storeAjax'])
        ->middleware('auth')
        ->name('categoriaservicios.ajax');

    Route::get('/home', [DashboardCitasController::class, 'index'])
        ->name('dashboard')
        ->middleware('auth');

    // Empleados
    Route::prefix('empleados')->name('empleados.')->group(function () {
        Route::get('/', [EmpleadoController::class, 'index'])->name('index');
        Route::get('/create', [EmpleadoController::class, 'create'])->name('create');
        Route::post('/', [EmpleadoController::class, 'store'])->name('store');
        Route::get('/{empleado}', [EmpleadoController::class, 'show'])->name('show');
        Route::get('/{empleado}/edit', [EmpleadoController::class, 'edit'])->name('edit');
        Route::put('/{empleado}', [EmpleadoController::class, 'update'])->name('update');
        Route::delete('/{empleado}', [EmpleadoController::class, 'destroy'])->name('destroy');
    });

    // Citas
    Route::prefix('citas')->name('citas.')->group(function () {

        // ✅ AJAX: empleados por servicio (ponla ARRIBA)
        Route::get('/empleados-por-servicio', [CitaController::class, 'empleadosPorServicio'])
            ->name('empleadosPorServicio');

        // AJAX horas disponibles
        Route::get('/horas-disponibles', [CitaController::class, 'horasDisponibles'])
            ->name('horasDisponibles');

        Route::get('/', [CitaController::class, 'index'])->name('index');
        Route::get('/create', [CitaController::class, 'create'])->name('create');
        Route::post('/', [CitaController::class, 'store'])->name('store');
        Route::get('/{cita}', [CitaController::class, 'show'])->name('show');
        Route::get('/{cita}/edit', [CitaController::class, 'edit'])->name('edit');
        Route::put('/{cita}', [CitaController::class, 'update'])->name('update');
        Route::delete('/{cita}', [CitaController::class, 'destroy'])->name('destroy');

        // Sincronización con Google Calendar
        Route::post('/{cita}/sync', [CitaController::class, 'syncWithGoogle'])->name('sync');
        Route::post('/sync-all', [CitaController::class, 'syncAllWithGoogle'])->name('sync-all');
    });

    // Clientes
    Route::prefix('clientes')->name('clientes.')->middleware(['auth'])->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('index');
        Route::get('/create', [ClienteController::class, 'create'])->name('create');
        Route::post('/', [ClienteController::class, 'store'])->name('store');
        Route::get('/{cliente}', [ClienteController::class, 'show'])->name('show');
        Route::get('/{cliente}/edit', [ClienteController::class, 'edit'])->name('edit');
        Route::put('/{cliente}', [ClienteController::class, 'update'])->name('update');
        Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('destroy');
    });

    // ✅ Google Calendar (DEJAR SOLO UN BLOQUE)
    Route::prefix('google')->name('google.')->group(function () {
        Route::get('/connect', [GoogleCalendarController::class, 'connect'])->name('connect');

        // si /auth era alias de connect, lo mantenemos
        Route::get('/auth', [GoogleCalendarController::class, 'connect'])->name('auth');

        // ✅ mantener POST para desconectar (evita GET action insegura)
        Route::post('/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('disconnect');

        Route::get('/status', [GoogleCalendarController::class, 'status'])->name('status');
    });

    // Categorías Servicios
    Route::prefix('categoriaservicios')->name('categoriaservicios.')->middleware('auth')->group(function () {
        Route::get('/', [CategoriaServicioController::class, 'index'])->name('index');
        Route::get('/create', [CategoriaServicioController::class, 'create'])->name('create');
        Route::post('/', [CategoriaServicioController::class, 'store'])->name('store');
        // Evita colisiones con rutas literales como /ajax
        Route::get('/{categoria}', [CategoriaServicioController::class, 'show'])->whereNumber('categoria')->name('show');
        Route::get('/{categoria}/edit', [CategoriaServicioController::class, 'edit'])->whereNumber('categoria')->name('edit');
        Route::put('/{categoria}', [CategoriaServicioController::class, 'update'])->whereNumber('categoria')->name('update');
        Route::delete('/{categoria}', [CategoriaServicioController::class, 'destroy'])->whereNumber('categoria')->name('destroy');
    });

    Route::resource('productos', ProductoController::class)->middleware('auth');

    // Servicios
    Route::resource('servicios', ServicioController::class);

    // Ventas (solo lectura)
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');

    // Reportes
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/exportar/{tipo}', [ReporteController::class, 'exportarReporte'])->name('reportes.exportar');

    // Ruta adicional para reporte
    Route::get('/ventas/reporte', [VentaController::class, 'reporte'])
        ->name('ventas.reporte');

    // Completar cita con pago
    Route::post('/citas/{id}/completar', [CitaController::class, 'completarConPago'])
        ->name('citas.completar.con-pago');

    // Cupones y Promociones
    Route::prefix('cupones')->name('cupones.')->middleware('auth')->group(function () {
        Route::get('/', [CuponController::class, 'index'])->name('index');
        Route::get('/create', [CuponController::class, 'create'])->name('create');
        Route::post('/', [CuponController::class, 'store'])->name('store');
        Route::get('/{cupon}', [CuponController::class, 'show'])->name('show');
        Route::get('/{cupon}/edit', [CuponController::class, 'edit'])->name('edit');
        Route::put('/{cupon}', [CuponController::class, 'update'])->name('update');
        Route::delete('/{cupon}', [CuponController::class, 'destroy'])->name('destroy');

        // Rutas AJAX para validación
        Route::post('/validar', [CuponController::class, 'validar'])->name('validar');
        Route::post('/descuento-cumpleaños', [CuponController::class, 'descuentoCumpleaños'])->name('descuentoCumpleaños');
    });
});

// =============================
// Checkout
// =============================
Route::get('/checkout', [PagoController::class, 'checkout'])->name('checkout');
Route::get('/success', [PagoController::class, 'success'])->name('success');
Route::get('/cancel', [PagoController::class, 'cancel'])->name('cancel');

// =============================
// Mis reservas
// =============================
Route::middleware(['auth'])->group(function () {
    Route::get('/mis-reservas', [MisReservasController::class, 'index'])->name('misreservas');

    // ✅ cancelar reserva
    Route::post('/mis-reservas/{cita}/cancelar', [MisReservasController::class, 'cancel'])
        ->name('misreservas.cancel');
});

// =============================
// Ruta de diagnóstico
// =============================
Route::get('/debug-auth', function () {
    return response()->json([
        'auth_check' => Auth::check(),
        'auth_user' => Auth::user(),
        'session_id' => session()->getId(),
        'all_session' => session()->all()
    ]);
});