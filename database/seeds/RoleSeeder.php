<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    private function roles() {
        return [
            ["name" => "Administrator", "slug" => "admin"],
            ["name" => "Pegawai Negeri", "slug" => "pegawai"],
            ["name" => "Teknisi", "slug" => "teknisi"],
            ["name" => "Cleaning Service", "slug" => "cleaning-service"],
            ["name" => "Security", "slug" => "security"],
        ];
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->roles() as $value) {
            Role::create($value);
        }
    }
}
