<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use App\Models\TypeComplaint;
use App\Models\Role;

class TypeComplaintSeeder extends Seeder
{
    private function typeComplaints() {
        return collect([
            ["title" => "Closet Rusak", "slug" => "cleaning-service"],
            ["title" => "Keran Tidak Menyala", "slug" => "cleaning-service"],
            ["title" => "Tisu Toilet Habis", "slug" => "cleaning-service"],
            ["title" => "Rusak Pintu Kamar Mandi", "slug" => "cleaning-service"],

            ["title" => "Kerusakan AC", "slug" => "teknisi"],
            ["title" => "Kerusakan TV", "slug" => "teknisi"],
            ["title" => "Kerusakan Shower", "slug" => "teknisi"],

            ["title" => "Keamanan Parkiran", "slug" => "security"],
            ["title" => "Jaga Malam", "slug" => "security"],
            ["title" => "Perlindungan Tamu", "slug" => "security"],
            
        ]);
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->typeComplaints()->each(function($item) {
            TypeComplaint::create([
                "title" => $item['title'],
                "role_id" => Role::where('slug', '=', $item['slug'])->first()->id
            ]);
        });
        //factory(App\Models\TypeComplaint::class, 100)->create();



    }
}
