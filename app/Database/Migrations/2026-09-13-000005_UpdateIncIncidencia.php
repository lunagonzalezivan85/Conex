<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateIncIncidencia extends Migration
{
    public function up()
    {
        // SLA segun plan de la empresa
        $this->db->query("ALTER TABLE inc_incidencia ADD COLUMN sla_horas INT NULL AFTER prioridad");
        $this->db->query("ALTER TABLE inc_incidencia ADD COLUMN fecha_limite_resolucion DATETIME NULL AFTER sla_horas");
        $this->db->query("ALTER TABLE inc_incidencia ADD COLUMN tiempo_resolucion_horas DECIMAL(10,2) NULL AFTER fecha_limite_resolucion");

        // Expandir enum de prioridad
        $this->db->query("ALTER TABLE inc_incidencia MODIFY COLUMN prioridad ENUM('baja','media','alta','critica') NOT NULL DEFAULT 'media'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE inc_incidencia MODIFY COLUMN prioridad ENUM('baja','media','alta') NOT NULL DEFAULT 'media'");
        $this->db->query("ALTER TABLE inc_incidencia DROP COLUMN tiempo_resolucion_horas");
        $this->db->query("ALTER TABLE inc_incidencia DROP COLUMN fecha_limite_resolucion");
        $this->db->query("ALTER TABLE inc_incidencia DROP COLUMN sla_horas");
    }
}
