<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $newUser = new User;
        $newUser->name="Usuario";
        $newUser->email="correo@gmail.com";
        $newUser->password=bcrypt("1234");
        $newUser->save();

    }
}
