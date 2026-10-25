<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ServiciosPrestador extends Migration
{
    public function up()
    {
        // Flags en el candidato: opt-in al directorio + ubicacion aproximada publica
        $this->forge->addColumn('cand_candidato', [
            'ofrece_servicios' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'modalidad_preferida',
            ],
            'zona_servicio' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'ofrece_servicios',
            ],
            'prestador_verificado' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'zona_servicio',
            ],
        ]);

        // Servicios ofrecidos por un candidato (electricista, plomero, etc.)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'candidato_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'oficio' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'precio_desde' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'precio_tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['por_hora', 'por_servicio', 'a_convenir'],
                'default'    => 'a_convenir',
                'null'       => false,
            ],
            'ciudad' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'zona' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['activo', 'pausado'],
                'default'    => 'activo',
                'null'       => false,
            ],
            'destacado' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
            'contactos' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->addKey(['estado', 'ciudad']);
        $this->forge->addForeignKey('candidato_id', 'cand_candidato', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('srv_servicio');

        // Solicitudes de contacto: el dato de contacto del cliente se revela
        // solo cuando el prestador ACEPTA la solicitud
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'servicio_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'user_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => true,
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => false,
            ],
            'telefono' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
            ],
            'mensaje' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'ip_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['pendiente', 'aceptado', 'rechazado'],
                'default'    => 'pendiente',
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['servicio_id', 'estado']);
        $this->forge->addKey(['ip_hash', 'created_at']);
        $this->forge->addForeignKey('servicio_id', 'srv_servicio', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('user_id', 'auth_user', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->createTable('srv_contacto');

        // Reportes antifraude: 1 por IP+servicio, umbral de suspension en lectura
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'servicio_id' => [
                'type'      => 'INT',
                'constraint' => 11,
                'unsigned'  => true,
                'null'      => false,
            ],
            'motivo' => [
                'type'       => 'ENUM',
                'constraint' => ['fraude', 'datos_falsos', 'comportamiento', 'spam', 'otro'],
                'null'       => false,
            ],
            'detalle' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
            'ip_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['servicio_id', 'ip_hash']);
        $this->forge->addForeignKey('servicio_id', 'srv_servicio', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('srv_reporte');
    }

    public function down()
    {
        $this->forge->dropTable('srv_reporte', true);
        $this->forge->dropTable('srv_contacto', true);
        $this->forge->dropTable('srv_servicio', true);
        $this->forge->dropColumn('cand_candidato', ['ofrece_servicios', 'zona_servicio', 'prestador_verificado']);
    }
}
