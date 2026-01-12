<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            1 => 'CLIENTE',
            2 => 'EMPLEADO',
            3 => 'ADMINISTRADOR',
        ];

        foreach ($roles as $id => $nombre) {
            Role::updateOrCreate(
                ['id' => $id],
                ['nombre_rol' => $nombre]
            );
        }
    }
}
