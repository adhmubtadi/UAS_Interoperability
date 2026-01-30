<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Gentleman Cut',
                'price' => 50000,
                'duration_minutes' => 30,
                'description' => 'Potongan rambut klasik pria dengan teknik gunting dan sisir. Termasuk keramas dan styling.'
            ],
            [
                'name' => 'Premium Cut & Wash',
                'price' => 75000,
                'duration_minutes' => 45,
                'description' => 'Potongan rambut premium dengan konsultasi style, keramas menggunakan produk premium, dan styling.'
            ],
            [
                'name' => 'Hair Coloring',
                'price' => 150000,
                'duration_minutes' => 90,
                'description' => 'Pewarnaan rambut dengan produk berkualitas. Termasuk bleaching jika diperlukan.'
            ],
            [
                'name' => 'Beard Trim & Shave',
                'price' => 35000,
                'duration_minutes' => 20,
                'description' => 'Perawatan jenggot dan kumis. Termasuk cukur bersih dengan pisau cukur tradisional.'
            ],
            [
                'name' => 'Hair Spa Treatment',
                'price' => 100000,
                'duration_minutes' => 60,
                'description' => 'Perawatan rambut lengkap dengan creambath, masker rambut, dan pijat kepala.'
            ],
            [
                'name' => 'Kids Haircut',
                'price' => 40000,
                'duration_minutes' => 25,
                'description' => 'Potongan rambut khusus untuk anak-anak dengan suasana nyaman dan ramah anak.'
            ],
            [
                'name' => 'Buzz Cut',
                'price' => 30000,
                'duration_minutes' => 15,
                'description' => 'Potongan rambut cepat dengan mesin clipper. Praktis dan rapi.'
            ],
            [
                'name' => 'Hair Smoothing',
                'price' => 200000,
                'duration_minutes' => 120,
                'description' => 'Pelurusan rambut permanen untuk tampilan rambut yang lurus dan berkilau.'
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
