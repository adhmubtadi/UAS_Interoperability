<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barber;

class BarberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barbers = [
            [
                'name' => 'Budi Santoso',
                'status' => 'available',
                'photo_url' => 'https://i.pravatar.cc/150?img=12'
            ],
            [
                'name' => 'Ahmad Rizki',
                'status' => 'available',
                'photo_url' => 'https://i.pravatar.cc/150?img=13'
            ],
            [
                'name' => 'Denny Pratama',
                'status' => 'busy',
                'photo_url' => 'https://i.pravatar.cc/150?img=14'
            ],
            [
                'name' => 'Eko Wijaya',
                'status' => 'available',
                'photo_url' => 'https://i.pravatar.cc/150?img=15'
            ],
            [
                'name' => 'Fajar Ramadhan',
                'status' => 'off',
                'photo_url' => 'https://i.pravatar.cc/150?img=33'
            ],
            [
                'name' => 'Gilang Prakoso',
                'status' => 'available',
                'photo_url' => 'https://i.pravatar.cc/150?img=51'
            ],
        ];

        foreach ($barbers as $barber) {
            Barber::create($barber);
        }
    }
}
