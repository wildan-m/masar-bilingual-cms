<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * One account per role for local development. The passwords are documented
 * in the README and must never be used outside a local environment.
 */
class UserSeeder extends Seeder
{
    public const ACCOUNTS = [
        ['name' => 'Layla Haddad', 'email' => 'admin@masar.test', 'role' => Role::Admin, 'password' => 'Admin#Local2026'],
        ['name' => 'Yousef Karim', 'email' => 'editor@masar.test', 'role' => Role::Editor, 'password' => 'Editor#Local2026'],
        ['name' => 'Mariam Saleh', 'email' => 'intake@masar.test', 'role' => Role::Intake, 'password' => 'Intake#Local2026'],
    ];

    public function run(): void
    {
        foreach (self::ACCOUNTS as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'password' => $account['password'],
                    'is_active' => true,
                ],
            );
        }
    }
}
