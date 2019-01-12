<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Permission::create(['name' => 'add users']);
    	Permission::create(['name' => 'edit users']);
    	Permission::create(['name' => 'delete users']);
    	Permission::create(['name' => 'detail users']);
    	Permission::create(['name' => 'list users']);

    	$roleAdmin = Role::create([
        	'name' => 'admin'
        ])->givePermissionTo(Permission::all());

        $roleUser = Role::create([
        	'name' => 'user'
        ])->givePermissionTo('edit users','detail users');
    }
}
