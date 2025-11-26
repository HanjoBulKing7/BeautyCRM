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
use Illuminate\Support\Facades\Auth;


// Rutas Públicas (Cliente)
Route::get('/home', [HomeController::class, 'index'])->name('cliente.home');

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

// Registro de clientes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

//Redirection URI for Google Client Route 

Route::get('/auth/google/callback', [GoogleCalendarController::class, 'callback']);
// Rutas de Administración
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/home', function () {
        if (!Auth::check() || Auth::user()->role_id != 3) {
            return redirect('/login')->with('error', 'No tienes permisos para acceder a esta sección.');
        }
        return view('admin.dashboard');
    })->name('dashboard');

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
        Route::get('/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('disconnect');
        Route::get('/status', [GoogleCalendarController::class, 'status'])->name('status');
    });
    // SERVICIOS - CORREGIDO
    Route::resource('servicios', ServicioController::class);

    // Rutas de Google Calendar
    Route::prefix('google')->name('google.')->group(function () {
    Route::get('/auth', [GoogleCalendarController::class, 'connect'])->name('auth');
    Route::get('/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('disconnect');
    Route::get('/status', [GoogleCalendarController::class, 'status'])->name('status');
});
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