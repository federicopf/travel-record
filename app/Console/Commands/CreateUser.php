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
        $username = $this->ask('Inserisci l\'username');
        $password = $this->secret('Inserisci la password');

        // Validazione
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $this->error('Errore: ' . implode(' ', $validator->errors()->all()));
            return;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'theme_id' => 1,
            'email_verified_at' => Carbon::now(), 
        ]);

        $this->info("✅ Utente creato con successo! ID: {$user->id}");
    }
}
