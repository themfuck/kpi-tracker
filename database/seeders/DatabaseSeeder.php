<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@kpi.com',
            'password' => bcrypt('password'),
        ]);

        // Create KPI Target
        \App\Models\KpiTarget::create([
            'gmv_per_hour' => 2700000,
            'conversion_rate' => 0.03,
            'aov' => 180000,
            'likes_per_minute' => 300,
        ]);

        // Create Hosts
        $hosts = [
            ['name' => 'Siti Nurhaliza', 'role' => 'Host', 'is_active' => true],
            ['name' => 'Rina Wijaya', 'role' => 'Host', 'is_active' => true],
            ['name' => 'Dewi Lestari', 'role' => 'Host', 'is_active' => true],
            ['name' => 'Maya Sari', 'role' => 'Host', 'is_active' => true],
            ['name' => 'Ayu Ting Ting', 'role' => 'Host', 'is_active' => true],
            ['name' => 'Putri Andini', 'role' => 'Host', 'is_active' => true],
            ['name' => 'Lia Amelia', 'role' => 'Host', 'is_active' => true],
            ['name' => 'Nina Kusuma', 'role' => 'Host', 'is_active' => true],
            ['name' => 'Operator 1', 'role' => 'Operator', 'is_active' => true],
            ['name' => 'Operator 2', 'role' => 'Operator', 'is_active' => true],
        ];

        foreach ($hosts as $hostData) {
            \App\Models\Host::create($hostData);
        }

        // Create sample live sessions for current month
        $hosts = \App\Models\Host::where('role', 'Host')->get();
        
        foreach ($hosts as $host) {
            // Create 5-10 sessions for each host in current month
            $sessionCount = rand(5, 10);
            
            for ($i = 0; $i < $sessionCount; $i++) {
                \App\Models\LiveSession::create([
                    'host_id' => $host->id,
                    'date' => now()->subDays(rand(1, 20)),
                    'hours_live' => rand(2, 8) + (rand(0, 99) / 100),
                    'gmv' => rand(5000000, 25000000),
                    'orders' => rand(50, 300),
                    'viewers' => rand(1000, 10000),
                    'likes' => rand(5000, 50000),
                    'errors' => rand(0, 5),
                ]);
            }
        }
    }
}
