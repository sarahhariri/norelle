<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Create a NORELLE administrator';

    public function handle(): int
    {
        $name = trim($this->ask('Name'));
        $email = strtolower(trim($this->ask('Email')));

        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Enter a valid name and email.');
            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('This email already has an account.');
            return self::FAILURE;
        }

        $password = $this->secret('Password (at least 12 characters)');
        $confirmation = $this->secret('Confirm password');

        if (!$password || strlen($password) < 12 || $password !== $confirmation) {
            $this->error('Password is too short or does not match.');
            return self::FAILURE;
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->is_admin = true;
        $user->save();

        $this->info('Admin account created.');
        return self::SUCCESS;
    }
}