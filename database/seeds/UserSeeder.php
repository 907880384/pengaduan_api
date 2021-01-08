<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use App\User;
use App\Models\Role;

class UserSeeder extends Seeder
{

    private function users() {
        return collect([
            [
                'name' => 'Administrator', 
                'username' => 'admin', 
                'password' => bcrypt('12345678'),
                'role' => 'admin'
            ],

            //Employees
            [
                'name' => 'Razor Colombias', 
                'username' => 'razor', 
                'password' => bcrypt('12345678'),
                'role' => 'pegawai'
            ],
            [
                'name' => 'John Doe', 
                'username' => 'johndoe', 
                'password' => bcrypt('12345678'),
                'role' => 'pegawai'
            ],

            //Technicians
            [
                'name' => 'Anton', 
                'username' => 'anton', 
                'password' => bcrypt('12345678'),
                'role' => 'teknisi'
            ],
            [
                'name' => 'Ardi Wijaya', 
                'username' => 'ardi', 
                'password' => bcrypt('12345678'),
                'role' => 'teknisi'
            ],

            //Cleaning Services
            [
                'name' => 'Moria Anita', 
                'username' => 'moria', 
                'password' => bcrypt('12345678'),
                'role' => 'cleaning-service'
            ],
            [
                'name' => 'Rona Sena', 
                'username' => 'rona', 
                'password' => bcrypt('12345678'),
                'role' => 'cleaning-service'
            ],


            //Other Support
            [
                'name' => 'Afdan Roy', 
                'username' => 'afdan', 
                'password' => bcrypt('12345678'),
                'role' => 'support-center'
            ],
            [
                'name' => 'Sumarwan', 
                'username' => 'sumarwan', 
                'password' => bcrypt('12345678'),
                'role' => 'support-center'
            ],

        ]);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->users()->each(function($value) {
            $user = User::create([
                'name' => $value['name'], 
                'username' => $value['username'], 
                'password' => $value['password'],
            ]);
            $user->roles()->attach(Role::where('slug',$value['role'])->first());
        });
    }
}
