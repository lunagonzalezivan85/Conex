<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCrmPlan extends Migration
{
    public function up()
    {
        // Multi-usuario por empresa
        $this->db->query("ALTER TABLE crm_plan ADD COLUMN max_usuarios INT NOT NULL DEFAULT 1 AFTER max_postulaciones");

        // Analisis de CV con IA (cantidad por mes, 0 = sin acceso, 999 = ilimitado)
        $this->db->query("ALTER TABLE crm_plan ADD COLUMN analisis_cv_ia INT NOT NULL DEFAULT 0 AFTER acceso_cvs");

        // Verificacion express (prioridad en verificacion)
        $this->db->query("ALTER TABLE crm_plan ADD COLUMN verificacion_express TINYINT(1) NOT NULL DEFAULT 0 AFTER analisis_cv_ia");

        // Candidatos destacados (acceso a candidatos con mayor puntaje)
        $this->db->query("ALTER TABLE crm_plan ADD COLUMN candidatos_destacados TINYINT(1) NOT NULL DEFAULT 0 AFTER verificacion_express");

        // SLA de soporte en horas
        $this->db->query("ALTER TABLE crm_plan ADD COLUMN sla_soporte_horas INT NOT NULL DEFAULT 72 AFTER candidatos_destacados");

        // Actualizar planes existentes
        // Gratis
        $this->db->query("UPDATE crm_plan SET max_usuarios = 1, analisis_cv_ia = 0, verificacion_express = 0, candidatos_destacados = 0, sla_soporte_horas = 72 WHERE slug = 'gratis'");
        // Basico
        $this->db->query("UPDATE crm_plan SET max_usuarios = 3, analisis_cv_ia = 5, verificacion_express = 0, candidatos_destacados = 0, sla_soporte_horas = 48 WHERE slug = 'basico'");
        // Premium
        $this->db->query("UPDATE crm_plan SET max_usuarios = 10, analisis_cv_ia = 999, verificacion_express = 1, candidatos_destacados = 1, sla_soporte_horas = 12 WHERE slug = 'premium'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE crm_plan DROP COLUMN sla_soporte_horas");
        $this->db->query("ALTER TABLE crm_plan DROP COLUMN candidatos_destacados");
        $this->db->query("ALTER TABLE crm_plan DROP COLUMN verificacion_express");
        $this->db->query("ALTER TABLE crm_plan DROP COLUMN analisis_cv_ia");
        $this->db->query("ALTER TABLE crm_plan DROP COLUMN max_usuarios");
    }
}
