<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Collection;
use App\Models\StatusProcess;
use Illuminate\Support\Str;

class StatusProcessSeeder extends Seeder
{
    private function statuses() {
        return collect([
            'mulai',
            'dikerjakan',
            'tunda',
            'Kendala',
            'batal',
            'selesai',
        ]);
    } 

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->statuses()->each(function($item) {
            StatusProcess::create([
                'name' => $item,
                'slug' => Str::slug($item)
            ]);
        });
    }
}
