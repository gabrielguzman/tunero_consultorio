<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\HealthInsurance;
use App\Models\AppointmentType;
use App\Models\Patient;
use App\Models\WorkSchedule;
use App\Models\Appointment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Obras Sociales
        $osde = HealthInsurance::create(['name' => 'OSDE', 'code' => '210']);
        $swiss = HealthInsurance::create(['name' => 'Swiss Medical', 'code' => 'SM']);
        $part = HealthInsurance::create(['name' => 'Particular', 'code' => 'PART']);

        // 2. Crear Tipos de Turno
        $control = AppointmentType::create([
            'name' => 'Control Niño Sano',
            'duration_minutes' => 30,
            'price' => 5000,
            'color' => '#10B981', // Verde
        ]);

        $consulta = AppointmentType::create([
            'name' => 'Consulta Enfermedad',
            'duration_minutes' => 20,
            'price' => 4000,
            'color' => '#EF4444', // Rojo
        ]);

        // 3. Crear Usuario ADMIN (Tu hermana)
        $admin = User::create([
            'name' => 'Dra. Ana (Admin)',
            'email' => 'admin@turnero.com',
            'phone' => '1122334455',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 4. Crear Usuario PACIENTE (Una mamá de ejemplo)
        $madre = User::create([
            'name' => 'María Pérez',
            'email' => 'madre@gmail.com',
            'phone' => '1199887766',
            'password' => Hash::make('password'),
            'role' => 'patient',
        ]);

        // 5. Crear los Hijos (Pacientes reales)
        $pedrito = Patient::create([
            'user_id' => $madre->id,
            'health_insurance_id' => $osde->id,
            'affiliate_number' => '123456789',
            'name' => 'Pedrito Lopez',
            'birth_date' => Carbon::now()->subYears(5), // 5 años
            'gender' => 'M',
        ]);

        $sofia = Patient::create([
            'user_id' => $madre->id,
            'health_insurance_id' => $swiss->id,
            'affiliate_number' => '987654321',
            'name' => 'Sofía Lopez',
            'birth_date' => Carbon::now()->subMonths(6), // 6 meses
            'gender' => 'F',
            'medical_alerts' => 'Nació prematura',
        ]);

        // 6. Configurar Horario Laboral (Lunes a Viernes, 9 a 17)
        // El loop va del 1 (Lunes) al 5 (Viernes)
        for ($i = 1; $i <= 5; $i++) {
            WorkSchedule::create([
                'day_of_week' => $i,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
            ]);
        }

        // 7. Crear un Turno de prueba para MAÑANA a las 10:00 AM
        Appointment::create([
            'user_id' => $madre->id,
            'patient_id' => $pedrito->id,
            'appointment_type_id' => $control->id,
            'start_time' => Carbon::tomorrow()->setHour(10)->setMinute(0),
            'end_time' => Carbon::tomorrow()->setHour(10)->setMinute(30),
            'status' => 'scheduled',
            'patient_notes' => 'Control anual',
            'created_by' => $madre->id,
        ]);

        // 8. Crear un Turno pasado (Ayer) para ver historial
        Appointment::create([
            'user_id' => $madre->id,
            'patient_id' => $sofia->id,
            'appointment_type_id' => $consulta->id,
            'start_time' => Carbon::yesterday()->setHour(14)->setMinute(0),
            'end_time' => Carbon::yesterday()->setHour(14)->setMinute(20),
            'status' => 'completed',
            'doctor_notes' => 'Tenía fiebre leve. Paracetamol.',
            'created_by' => $admin->id,
        ]);
    }
}