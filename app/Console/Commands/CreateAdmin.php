<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create
        {name : The maintainer\'s name}
        {email : The email address used to sign in}';

    protected $description = 'Create a maintainer account for the reports admin page';

    public function handle(): int
    {
        $name = trim((string) $this->argument('name'));
        $email = strtolower(trim((string) $this->argument('email')));

        $validator = Validator::make(
            ['name' => $name, 'email' => $email],
            [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        // The password is asked for here and never passed as an argument,
        // so it does not end up in shell history.
        $password = (string) $this->secret('Password (at least 12 characters)');

        if (mb_strlen($password) < 12) {
            $this->error('The password must be at least 12 characters.');

            return self::FAILURE;
        }

        if ($password !== (string) $this->secret('Confirm password')) {
            $this->error('The passwords do not match.');

            return self::FAILURE;
        }

        $user = new User;
        $user->forceFill([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ])->save();

        $this->info("Maintainer {$email} created. They can sign in at ".route('login'));

        return self::SUCCESS;
    }
}
