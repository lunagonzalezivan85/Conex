<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CandCandidato extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'apellidos' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'fecha_nacimiento' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'profesion' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nivel_experiencia_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'sobre_mi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'direccion' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'ciudad' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'region' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'pais' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'latitud' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
            ],
            'longitud' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'disponibilidad' => [
                'type'       => 'ENUM',
                'constraint' => ['inmediata', '15_dias', '30_dias', 'a_convenir'],
                'default'    => 'a_convenir',
                'null'       => false,
            ],
            'modalidad_preferida' => [
                'type'       => 'ENUM',
                'constraint' => ['presencial', 'remoto', 'hibrido', 'indiferente'],
                'default'    => 'indiferente',
                'null'       => false,
            ],
            'salario_esperado_usd' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'portafolio_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'linkedin_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'porcentaje_perfil' => [
                'type'       => 'INT',
                'constraint'  => 3,
                'default'    => 0,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'auth_user', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('nivel_experiencia_id', 'cat_nivel_experiencia', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('cand_candidato');
    }

    public function down()
    {
        $this->forge->dropTable('cand_candidato');
    }
}
