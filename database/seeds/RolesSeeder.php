<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            // Users Permissions
            array('name'=>'Super Admin', 'description'=>'Gateway Super Admin'),
            array('name'=>'Admin', 'description'=>'Gateway Admin'),
            array('name'=>'Editor', 'description'=>'Gateway Editor'),
            array('name'=>'Viewer', 'description'=>'Gateway Viewer'),
        );
        \App\Models\Role::insert($data);
    }
}
