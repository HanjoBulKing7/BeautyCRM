<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\RutaController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
|--------------------------------------------------------------------------
*/
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (REQUEREN AUTENTICACIÓN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // ==================== DASHBOARD ====================
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    // ==================== 🚚 RUTAS (DISTRIBUCIÓN) ====================
    Route::get('/rutas', [RutaController::class, 'index'])->name('rutas.index');
    Route::get('/rutas/create', [RutaController::class, 'create'])->name('rutas.create');
    Route::post('/rutas', [RutaController::class, 'store'])->name('rutas.store');
    Route::get('/rutas/{ruta}', [RutaController::class, 'show'])->name('rutas.show'); // ✅ FALTABA ESTA RUTA
    Route::get('/rutas/{ruta}/edit', [RutaController::class, 'edit'])->name('rutas.edit');
    Route::put('/rutas/{ruta}', [RutaController::class, 'update'])->name('rutas.update');
    Route::delete('/rutas/{ruta}', [RutaController::class, 'destroy'])->name('rutas.destroy');

    // Productos dentro de rutas
    Route::post('/rutas/{ruta}/add-producto', [RutaController::class, 'addProducto'])->name('rutas.addProducto');
    Route::put('/rutas-detalle/{detalle}', [RutaController::class, 'updateDetalle'])->name('rutas.updateDetalle');
    Route::delete('/rutas-detalle/{detalle}', [RutaController::class, 'deleteDetalle'])->name('rutas.deleteDetalle');


    // ==================== 🧾 VENTAS ====================
    Route::prefix('ventas')->group(function () {
        Route::get('/', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/crear', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('/', [VentaController::class, 'store'])->name('ventas.store');
        Route::get('/{venta}', [VentaController::class, 'show'])->name('ventas.show');
        Route::get('/{venta}/editar', [VentaController::class, 'edit'])->name('ventas.edit');
        Route::put('/{venta}', [VentaController::class, 'update'])->name('ventas.update');
        Route::delete('/{venta}', [VentaController::class, 'destroy'])->name('ventas.destroy');
        Route::get('/{venta}/ticket', [VentaController::class, 'ticket'])->name('ventas.ticket');
    });


    // ==================== 💸 GASTOS ====================
    Route::prefix('gastos')->group(function () {
        Route::get('/', [GastoController::class, 'index'])->name('gastos.index');
        Route::get('/crear', [GastoController::class, 'create'])->name('gastos.create');
        Route::post('/', [GastoController::class, 'store'])->name('gastos.store');
        Route::get('/{gasto}', [GastoController::class, 'show'])->name('gastos.show');
        Route::get('/{gasto}/editar', [GastoController::class, 'edit'])->name('gastos.edit');
        Route::put('/{gasto}', [GastoController::class, 'update'])->name('gastos.update');
        Route::delete('/{gasto}', [GastoController::class, 'destroy'])->name('gastos.destroy');
        Route::get('/{gasto}/descargar-comprobante', [GastoController::class, 'downloadComprobante'])
            ->name('gastos.download.comprobante');
    });


    // ==================== 👷 EMPLEADOS ====================
    Route::prefix('empleados')->group(function () {
        Route::get('/', [EmpleadoController::class, 'index'])->name('empleados.index');
        Route::get('/create', [EmpleadoController::class, 'create'])->name('empleados.create');
        Route::post('/', [EmpleadoController::class, 'store'])->name('empleados.store');
        Route::get('/{empleado}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit');
        Route::put('/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update');
        Route::delete('/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
    });


    // ==================== 📦 PRODUCTOS ====================
    Route::prefix('productos')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/crear', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    });


    // ==================== 🧭 INVENTARIO ====================
    Route::prefix('inventario')->group(function () {
        Route::get('/', [InventarioController::class, 'index'])->name('inventario.index');
        Route::get('/{existencia}', [InventarioController::class, 'show'])->name('inventario.show');
        Route::get('/{existencia}/edit', [InventarioController::class, 'edit'])->name('inventario.edit');
        Route::put('/{existencia}', [InventarioController::class, 'update'])->name('inventario.update');
        Route::get('/movimientos/{producto}', [InventarioController::class, 'movimientos'])->name('inventario.movimientos');
    });


    // ==================== 📈 REPORTES ====================
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
});

/*
|--------------------------------------------------------------------------
| ⚠️ REDIRECCIÓN PARA RUTAS NO EXISTENTES
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return redirect()->route('login');
});