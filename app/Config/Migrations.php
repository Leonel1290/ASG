<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddValveFieldsToDispositivos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('dispositivos', [
            'estado_valvula' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'ubicacion'
            ],
            'ip_local' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
                'after' => 'estado_valvula'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('dispositivos', 'estado_valvula');
        $this->forge->dropColumn('dispositivos', 'ip_local');
    }
}