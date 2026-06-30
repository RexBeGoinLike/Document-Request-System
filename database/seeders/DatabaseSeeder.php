<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Yajra\Address\Seeders\AddressSeeder;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DocumentTypeSeeder::class);
        $this->call(AddressSeeder::class);
        $this->call(UserSeeder::class);
    }
}

