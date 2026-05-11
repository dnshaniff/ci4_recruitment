<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@mail.com',
            'password' => password_hash('P@ssw0rd', PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
