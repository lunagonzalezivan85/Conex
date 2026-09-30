<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CfgConfiguracion extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'clave'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'valor'      => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('clave');
        $this->forge->createTable('cfg_configuracion', true);

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nombre'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'proveedor'    => ['type' => 'VARCHAR', 'constraint' => 50],
            'url'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'modelo'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'api_key'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'proposito'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'activo'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cfg_ia_modelo', true);
    }

    public function down()
    {
        $this->forge->dropTable('cfg_ia_modelo', true);
        $this->forge->dropTable('cfg_configuracion', true);
    }
}
