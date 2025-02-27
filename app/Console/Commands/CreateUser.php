<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CreateUser extends Command
{
    protected $signature = 'user:create';
    protected $description = 'Crea un nuovo utente';

    public function handle()
    {
        $name = $this->ask('Inserisci il nome');
        $email = $this->ask('Inserisci l\'email');
        $password = $this->secret('Inserisci la password');

        // Validazione
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $this->error('Errore: ' . implode(' ', $validator->errors()->all()));
            return;
        }

        // Creazione utente con email verificata
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => Carbon::now(), // ✅ Imposta la verifica automatica
        ]);

        $this->info("✅ Utente creato con successo! ID: {$user->id}");
    }
}
