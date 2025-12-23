<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminder;
use Carbon\Carbon;

class SendReminders extends Command
{
    // El nombre para llamar al comando
    protected $signature = 'app:send-reminders';

    protected $description = 'Envía recordatorios a los pacientes con turno mañana';

    public function handle()
    {
        $this->info('🔍 Buscando turnos para mañana...');

        // Buscamos turnos que sean MAÑANA
        $tomorrow = Carbon::tomorrow();
        
        $appointments = Appointment::with(['patient.user']) // Cargamos paciente y usuario (donde está el email)
            ->whereDate('start_time', $tomorrow)
            ->where('status', '!=', 'cancelled') // Ignoramos cancelados
            ->get();

        if ($appointments->count() === 0) {
            $this->info('✅ No hay turnos para mañana. Nada que enviar.');
            return;
        }

        $count = 0;
        foreach ($appointments as $app) {
            // Verificamos si el paciente tiene usuario y email válido
            if ($app->patient && $app->patient->user && $app->patient->user->email) {
                
                Mail::to($app->patient->user->email)->send(new AppointmentReminder($app));
                
                $this->info("📧 Enviado a: " . $app->patient->name);
                $count++;
            } else {
                $this->warn("⚠️ No se pudo enviar a: " . $app->patient->name . " (Falta email)");
            }
        }

        $this->info("🚀 Proceso terminado. $count correos enviados.");
    }
}