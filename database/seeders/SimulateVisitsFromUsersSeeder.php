<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User; // Asegúrate de que esta es la ruta correcta a tu modelo User
use Illuminate\Support\Facades\Log; // Importa la fachada Log

class SimulateVisitsFromUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void // Asegúrate de que es `void` o no tiene `return` si solo hace inserts.
    {
        Log::info('SimulateVisitsFromUsersSeeder: Iniciando ejecución del seeder.');

        try {
            // Opcional: Vaciar la tabla visits antes de insertar nuevos datos simulados
            DB::table('visits')->truncate();
            $this->command->info('Tabla visits truncada.');
            Log::info('SimulateVisitsFromUsersSeeder: Tabla visits truncada exitosamente.');

        } catch (\Throwable $e) {
            $this->command->error('Error al truncar la tabla visits: ' . $e->getMessage());
            Log::error('SimulateVisitsFromUsersSeeder: Error al truncar visits. ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return; // Detener la ejecución si no se puede truncar
        }

        try {
            $users = User::all(); // Obtener todos los usuarios de la tabla 'users'
            $this->command->info('Total de usuarios encontrados: ' . $users->count());
            Log::info('SimulateVisitsFromUsersSeeder: Total de usuarios encontrados: ' . $users->count());

            if ($users->isEmpty()) {
                $this->command->warn('No se encontraron usuarios en la tabla users. No se simularán visitas.');
                Log::warning('SimulateVisitsFromUsersSeeder: No hay usuarios para simular visitas.');
                return; // No hay usuarios, salir del seeder
            }

            $insertedCount = 0;
            foreach ($users as $user) {
                // Simular 1 a 3 visitas para cada usuario, cerca de su fecha de registro o último login.
                $baseDate = $user->created_at ?? Carbon::now()->subDays(rand(0, 30)); // Fecha base para la visita

                for ($i = 0; $i < rand(1, 3); $i++) {
                    try {
                        DB::table('visits')->insert([
                            'user_id'    => $user->id, // Coincide con 'user id' de tu tabla (¡con espacio!)
                            'page'       => '/simulated/page/' . rand(1, 10), // Ruta de página simulada
                            'ip'         => '192.168.' . rand(0, 255) . '.' . rand(0, 255), // Coincide con 'ip'
                            'created_at' => $baseDate->copy()->addMinutes(rand(0, 1440)), // Fecha y hora de la visita
                        ]);
                        $insertedCount++;
                    } catch (\Throwable $e) {
                        $this->command->error('Error al insertar visita para usuario ID ' . $user->id . ': ' . $e->getMessage());
                        Log::error('SimulateVisitsFromUsersSeeder: Error al insertar visita para usuario ID ' . $user->id . '. ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                    }
                }
            }

            $this->command->info('Simulación de visitas completada. Se insertaron ' . $insertedCount . ' registros en la tabla visits.');
            Log::info('SimulateVisitsFromUsersSeeder: Simulación completada. Registros insertados: ' . $insertedCount);

        } catch (\Throwable $e) {
            $this->command->error('Error inesperado durante la simulación de visitas: ' . $e->getMessage());
            Log::error('SimulateVisitsFromUsersSeeder: Error inesperado. ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }
}
