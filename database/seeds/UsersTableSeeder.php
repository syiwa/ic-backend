<?php

use Illuminate\Database\Seeder;
use App\User;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'name' => "User",
            'email' => 'user@gmail.com',
            'phone' => '081234567890',
            'address' => '1',
            'password' => bcrypt('password'),
        ])->assignRole('user');

        User::create([
            'name' => "Admin",
            'email' => 'admin@gmail.com',
            'phone' => '081234567891',
            'address' => '2',
            'password' => bcrypt('password'),
        ])->assignRole('admin');

      	$faker = Faker::create();

      	foreach(range(1, 40) as $index){
      		User::create([
	            'name' => $faker->name,
	            'email' => $faker->email,
	            'phone' => $faker->e164PhoneNumber,
	            'address' => $faker->address,
	            'password' => bcrypt('password'),
	        ])->assignRole('user');
      	}  
    }
}
