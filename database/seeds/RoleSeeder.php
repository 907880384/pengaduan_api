<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    private function roles() {
        return [
            [
                "name" => "Administrator", 
                "slug" => "admin",
                "alias" => "administrator"
            ],
            [
                "name" => "Pegawai (ASN)", 
                "slug" => "customer",
                "alias" => "customer"
            ],
            [
                "name" => "Teknisi", 
                "slug" => "teknisi",
                "alias" => "pegawai"
            ],
            [
                "name" => "Cleaning Service", 
                "slug" => "cleaning-service",
                "alias" => "pegawai"
            ],
            [
                "name" => "Security", 
                "slug" => "security",
                "alias" => "pegawai"
            ],
            [
                "name" => "Pest Control", 
                "slug" => "pest-control",
                "alias" => "pegawai"
            ],
            [
                "name" => "Gardener", 
                "slug" => "gardener",
                "alias" => "pegawai"
            ],
            [
                "name" => "Receptionis", 
                "slug" => "receptionis",
                "alias" => "pegawai"
            ],
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
