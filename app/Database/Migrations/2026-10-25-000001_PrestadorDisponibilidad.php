<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Disponibilidad "en vivo" del prestador (estilo inDrive):
 * cuando activa 'disponible_ahora' la app envia su GPS
 * (latitud/longitud ya existen en cand_candidato) y su
 * pin aparece en el mapa del buscador. Al apagarlo,
 * disponible_desde queda null y desaparece del mapa.
 */
class PrestadorDisponibilidad extends Migration
{
    public function up()
    {
        $this->forge->addColumn('cand_candidato', [
            'disponible_ahora' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'ofrece_servicios',
            ],
            'disponible_desde' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'disponible_ahora',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('cand_candidato', ['disponible_ahora', 'disponible_desde']);
    }
}
