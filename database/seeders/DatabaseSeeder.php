<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador Demo',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '600000000',
            'address' => 'Calle Demo 1',
            'city' => 'Oviedo',
            'postal_code' => '33001',
        ]);

        User::create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'phone' => '600000001',
            'address' => 'Avenida Ejemplo 10',
            'city' => 'Gijón',
            'postal_code' => '33201',
        ]);

        $catalog = [
            'Informática' => [
                ['Portátil NovaBook 14', 749.90, 12, 'Portátil ligero de 14 pulgadas pensado para trabajo, estudio y movilidad.'],
                ['Teclado mecánico Pulse', 79.95, 25, 'Teclado mecánico compacto con iluminación y conexión USB-C.'],
                ['Ratón inalámbrico AirClick', 34.90, 40, 'Ratón ergonómico inalámbrico con autonomía prolongada.'],
            ],
            'Audio' => [
                ['Auriculares Wave Pro', 129.90, 18, 'Auriculares inalámbricos con cancelación de ruido y sonido equilibrado.'],
                ['Altavoz Pocket Beat', 59.90, 30, 'Altavoz Bluetooth compacto resistente a salpicaduras.'],
                ['Micrófono Stream One', 89.00, 14, 'Micrófono USB para videollamadas, podcast y streaming.'],
            ],
            'Accesorios' => [
                ['Hub USB-C Connect 7', 49.90, 22, 'Hub multipuerto con HDMI, USB, lector de tarjetas y carga Power Delivery.'],
                ['Soporte portátil Elevate', 39.50, 19, 'Soporte de aluminio regulable para mejorar la ergonomía del puesto de trabajo.'],
                ['Mochila Urban Tech', 54.90, 16, 'Mochila acolchada para portátil con compartimentos y diseño urbano.'],
            ],
        ];

        foreach ($catalog as $categoryName => $products) {
            $category = Category::create(['name' => $categoryName, 'slug' => Str::slug($categoryName)]);
            foreach ($products as [$name, $price, $stock, $description]) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'active' => true,
                ]);
            }
        }
    }
}
