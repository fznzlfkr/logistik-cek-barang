<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogAktivitas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_log' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'id_user' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true
            ],
            'id_admin' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['User', 'Admin', 'Super Admin', 'tidak terdaftar']
            ],
            'aktivitas' => [
                'type' => 'TEXT'
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45'
            ],
            'user_agent' => [
                'type' => 'TEXT'
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'default' => 'current_timestamp()'
            ]
        ]);

        $this->forge->addKey('id_log', true);

        // Optional FK (hapus bila tidak digunakan)
        $this->forge->addForeignKey('id_user', 'user', 'id_user', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('id_admin', 'admin', 'id_admin', 'SET NULL', 'SET NULL');

        $this->forge->createTable('log_aktivitas');
    }

    public function down()
    {
        $this->forge->dropTable('log_aktivitas');
    }
}
