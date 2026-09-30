<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VacVacanteGeo extends Migration
{
    public function up()
    {
        $fields = [
            'latitud'  => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true, 'after' => 'region'],
            'longitud' => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true, 'after' => 'latitud'],
        ];
        $this->forge->addColumn('vac_vacante', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('vac_vacante', ['latitud', 'longitud']);
    }
}
