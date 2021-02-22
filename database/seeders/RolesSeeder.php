<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('Roles')->insert([
            'name' => 'Author',
            'slug' => 'author',
            'permissions' => json_encode([
               'create-post' => true,
             ]),
        ]);
        
        DB::table('Roles')->insert([
            'name' => 'Editor',
            'slug' => 'editor',
            'permissions' => json_encode([
                'update-post' => true,
                'publish-post' => true,
            ]),
        ]);
    }
}
// 'permissions' => json_encode([
//     'create-post' => true,
// ]),

// 'permissions' => json_encode([
//     'update-post' => true,
//     'publish-post' => true,
// ]),