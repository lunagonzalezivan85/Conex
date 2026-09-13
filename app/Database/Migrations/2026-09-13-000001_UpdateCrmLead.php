<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCrmLead extends Migration
{
    public function up()
    {
        // Cambiar enum de estado a 5 etapas + perdido
        $this->db->query("ALTER TABLE crm_lead MODIFY COLUMN estado ENUM('prospecto','contacto','union','propuesta','cerrado','perdido') NOT NULL DEFAULT 'prospecto'");

        // Cambiar enum de origen con mas opciones
        $this->db->query("ALTER TABLE crm_lead MODIFY COLUMN origen ENUM('web','feria_laboral','referido','campana','redes','directo','otro') NOT NULL DEFAULT 'web'");

        // Agregar tipo de lead (nuevo vs upsell)
        $this->db->query("ALTER TABLE crm_lead ADD COLUMN tipo ENUM('nuevo','upsell') NOT NULL DEFAULT 'nuevo' AFTER empresa_id");

        // Agregar rubro de la empresa prospecto
        $this->db->query("ALTER TABLE crm_lead ADD COLUMN rubro VARCHAR(100) NULL AFTER empresa_nombre");

        // Agregar tamaño de empresa
        $this->db->query("ALTER TABLE crm_lead ADD COLUMN tamano_empresa ENUM('1-10','11-50','51-200','201-500','500+') NULL AFTER rubro");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE crm_lead DROP COLUMN tamano_empresa");
        $this->db->query("ALTER TABLE crm_lead DROP COLUMN rubro");
        $this->db->query("ALTER TABLE crm_lead DROP COLUMN tipo");
        $this->db->query("ALTER TABLE crm_lead MODIFY COLUMN origen ENUM('web','referido','campana','directo','otro') NOT NULL DEFAULT 'web'");
        $this->db->query("ALTER TABLE crm_lead MODIFY COLUMN estado ENUM('nuevo','contactado','calificado','propuesta','negociacion','ganado','perdido') NOT NULL DEFAULT 'nuevo'");
    }
}
