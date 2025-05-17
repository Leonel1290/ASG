<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TUsuarios extends Migration
{
	public function up()
        {
                $this->forge->addField([
                        'id_usuario'          => [
                                'type'           => 'INT',
                                'constraint'     => 5,
                                'unsigned'       => true,
                                'auto_increment' => true,
                        ],
                        'usuario'       => [
                                'type'           => 'VARCHAR',
                                'constraint'     => '255',
                        ],
                        'password'       => [
                                'type'           => 'VARCHAR',
                                'constraint'     => '255',
                        ],
                        'type'       => [
                                'type'           => 'VARCHAR',
                                'constraint'     => '255',
                        ],
                ]);
                $this->forge->addKey('id_usuario', true);
                $this->forge->createTable('t_usuario');

                // NOTA IMPORTANTE: La estructura de esta migración (tabla 't_usuario' con campos 'id_usuario', 'usuario', 'password', 'type')
                // NO COINCIDE con la estructura de tabla ('usuarios' con campos 'id', 'nombre', 'apellido', 'email', 'is_active', etc.)
                // que tus controladores (Home, registerController, PerfilController) y el modelo UserModel parecen estar utilizando.
                // Debes asegurarte de que la base de datos en Clever Cloud tenga la estructura de tabla correcta que tu aplicación espera.
                // Si tus controladores y UserModel usan la tabla 'usuarios', esta migración puede ser antigua o incorrecta y deberías eliminarla
                // o crear una nueva migración que defina la tabla 'usuarios' con la estructura correcta.
        }

        public function down()
        {
                $this->forge->dropTable('t_usuario');
        }
}
