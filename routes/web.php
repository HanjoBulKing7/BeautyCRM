<?php
use App\Http\Controllers\CitaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardCitasController;


// Rutas Públicas (Cliente)
Route::get('/home', [HomeController::class, 'index'])->name('cliente.home');
Route::view('/galeria', 'galeria')->name('galeria');

Route::view('/servicio', 'servicio')->name('servicio');
Route::view('/agendarcita', 'agendarcita')->name('agendarcita');

Route::get('/anticipo', function () {
    return view('cliente.anticipo');
})->name('cliente.anticipo');

Route::get('/reserva', function () {
    return view('cliente.reserva');
})->name('cliente.reserva');

Route::get('/sucursal', function () {
    return view('cliente.sucursal');
})->name('cliente.sucursal');

// Rutas de Autenticación
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


// ✅ Invitación empleado (link firmado expirable)
Route::get('/invitation/employee/{user}', [AuthController::class, 'acceptEmployeeInvitation'])
    ->name('invitation.employee')
    ->middleware('signed'); // important: valida firma + expiración


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

// ✅ Link de invitación (firmado + expira)
Route::get('/invitation/employee/{user}', [AuthController::class, 'acceptEmployeeInvitation'])
    ->name('invitation.employee')
    ->middleware('signed');
// Rutas de Administración
Route::prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/home', [DashboardCitasController::class, 'index'])
        ->name('dashboard')
        ->middleware('auth');


    Route::prefix('empleados')->name('empleados.')->group(function () {
        Route::get('/', [EmpleadoController::class, 'index'])->name('index');
        Route::get('/create', [EmpleadoController::class, 'create'])->name('create');
        Route::post('/', [EmpleadoController::class, 'store'])->name('store');
        Route::get('/{empleado}', [EmpleadoController::class, 'show'])->name('show');
        Route::get('/{empleado}/edit', [EmpleadoController::class, 'edit'])->name('edit');
        Route::put('/{empleado}', [EmpleadoController::class, 'update'])->name('update');
        Route::delete('/{empleado}', [EmpleadoController::class, 'destroy'])->name('destroy');
    });
    
    // Rutas de Citas
    Route::prefix('citas')->name('citas.')->group(function () {
            // ✅ AJAX: empleados por servicio (ponla ARRIBA)
        Route::get('/empleados-por-servicio', [CitaController::class, 'empleadosPorServicio'])
            ->name('empleadosPorServicio');
        Route::get('/horas-disponibles', [CitaController::class, 'horasDisponibles'])
        ->name('horasDisponibles');
        Route::get('/', [CitaController::class, 'index'])->name('index');
        Route::get('/create', [CitaController::class, 'create'])->name('create');
        Route::post('/', [CitaController::class, 'store'])->name('store');
        Route::get('/{cita}', [CitaController::class, 'show'])->name('show');
        Route::get('/{cita}/edit', [CitaController::class, 'edit'])->name('edit');
        Route::put('/{cita}', [CitaController::class, 'update'])->name('update');
        Route::delete('/{cita}', [CitaController::class, 'destroy'])->name('destroy');
        
        // Rutas de sincronización con Google Calendar
        Route::post('/{cita}/sync', [CitaController::class, 'syncWithGoogle'])->name('sync');
        Route::post('/sync-all', [CitaController::class, 'syncAllWithGoogle'])->name('sync-all');
    });

    
    Route::prefix('clientes')->name('clientes.')->middleware(['auth'])->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('index');
        Route::get('/create', [ClienteController::class, 'create'])->name('create');
        Route::post('/', [ClienteController::class, 'store'])->name('store');
        Route::get('/{cliente}', [ClienteController::class, 'show'])->name('show');
        Route::get('/{cliente}/edit', [ClienteController::class, 'edit'])->name('edit');
        Route::put('/{cliente}', [ClienteController::class, 'update'])->name('update');
        Route::delete('/{cliente}', [ClienteController::class, 'destroy'])->name('destroy');
    });

    // Rutas de Google Calendar
    Route::prefix('google')->name('google.')->group(function () {
        Route::get('/connect', [GoogleCalendarController::class, 'connect'])->name('connect');
        Route::get('/auth', [GoogleCalendarController::class, 'connect'])->name('auth');   
        Route::post('/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('disconnect');
        Route::get('/status', [GoogleCalendarController::class, 'status'])->name('status');
    });
    // SERVICIOS - CORREGIDO
    Route::resource('servicios', ServicioController::class);

    // Rutas de Google Calendar (dentro de Route::prefix('admin')->name('admin.'))
    Route::prefix('google')->name('google.')->group(function () {
        Route::get('/auth', [GoogleCalendarController::class, 'connect'])->name('auth');
        Route::get('/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('disconnect');
        Route::get('/status', [GoogleCalendarController::class, 'status'])->name('status');
    });
    

    // Solo index y show - no hay create, store, edit, update, destroy
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');


    // Reportes
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/exportar/{tipo}', [ReporteController::class, 'exportarReporte'])->name('reportes.exportar');

    // Ruta adicional para reporte
    Route::get('/ventas/reporte', [\App\Http\Controllers\VentaController::class, 'reporte'])
        ->name('ventas.reporte');
    
    // Ruta para completar cita con pago
    Route::post('/citas/{id}/completar', [\App\Http\Controllers\CitaController::class, 'completarConPago'])
        ->name('citas.completar.con-pago');

});

// Rutas de Payment Stripe
Route::get('/pagar', function () {  
    return view('metodo_pago');
})->name('metodo.pago');

Route::get('/checkout', [PagoController::class, 'checkout'])->name('checkout');
Route::get('/success', [PagoController::class, 'success'])->name('success');
Route::get('/cancel', [PagoController::class, 'cancel'])->name('cancel');

// Ruta de diagnóstico
Route::get('/debug-auth', function () {
    return response()->json([
        'auth_check' => Auth::check(),
        'auth_user' => Auth::user(),
        'session_id' => session()->getId(),
        'all_session' => session()->all()
    ]);
});