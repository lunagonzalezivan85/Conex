<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VacVacante extends Migration
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
            'empresa_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'categoria_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'tipo_contrato_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'nivel_experiencia_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 180,
                'null'       => false,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'funciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ubicacion' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
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
            'modalidad' => [
                'type'       => 'ENUM',
                'constraint' => ['presencial', 'remoto', 'hibrido'],
                'default'    => 'presencial',
                'null'       => false,
            ],
            'salario_min' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'salario_max' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'moneda' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'default'    => 'USD',
                'null'       => false,
            ],
            'anios_experiencia' => [
                'type'       => 'INT',
                'constraint'  => 3,
                'default'    => 0,
                'null'       => false,
            ],
            'vacantes_disponibles' => [
                'type'       => 'INT',
                'constraint'  => 5,
                'default'    => 1,
                'null'       => false,
            ],
            'max_postulantes' => [
                'type'       => 'INT',
                'constraint'  => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['borrador', 'publicada', 'cerrada', 'suspendida'],
                'default'    => 'borrador',
                'null'       => false,
            ],
            'destacada' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
            ],
            'fecha_publicacion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'fecha_cierre' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('empresa_id', 'emp_empresa', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('categoria_id', 'cat_categoria', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('tipo_contrato_id', 'cat_tipo_contrato', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('nivel_experiencia_id', 'cat_nivel_experiencia', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('vac_vacante');
    }

    public function down()
    {
        $this->forge->dropTable('vac_vacante');
    }
}
