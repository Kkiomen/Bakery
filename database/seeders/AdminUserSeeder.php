<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Sprawdź czy admin już istnieje
        if (User::where('email', 'admin@piekarnia.pl')->exists()) {
            $this->command->info('Administrator już istnieje.');
            return;
        }

        // Utwórz konto administratora
        User::create([
            'name' => 'Administrator Piekarni',
            'email' => 'admin@piekarnia.pl',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Konto administratora zostało utworzone!');
        $this->command->info('📧 Email: admin@piekarnia.pl');
        $this->command->info('🔑 Hasło: admin123');
    }
}