<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class EnsureAdminUser extends Command
{
    protected $signature = 'user:ensure-admin {email} {password} {--first_name=} {--last_name=}';

    protected $description = 'Create or promote a user to admin (by email)';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $password = (string) $this->argument('password');
        $firstName = (string) ($this->option('first_name') ?? 'Kevin');
        $lastName = (string) ($this->option('last_name') ?? 'Aquino');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address.');
            return self::FAILURE;
        }

        if ($password === '') {
            $this->error('Password cannot be empty.');
            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => trim($firstName.' '.$lastName),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'password' => Hash::make($password),
                'is_admin' => true,
            ]
        );

        if (!$user->is_admin) {
            $user->is_admin = true;
            $user->save();
        }

        $this->info('Admin user ensured: '.$user->email.' (id='.$user->id.', is_admin='.(int) $user->is_admin.')');
        return self::SUCCESS;
    }
}
