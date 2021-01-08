<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use App\Models\OperationalType;
use Illuminate\Support\Str;

class OperationalTypeSeeder extends Seeder
{
    private function operationalTypes() {
        return collect([
            'Teknisi',
            'Cleaning Service',
            'Security'
        ]);
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->operationalTypes()->each(function($value) {
            OperationalType::create([
                'name' => $value,
                'slug' => Str::slug($value),
            ]);
        });
    }
}
