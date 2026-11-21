<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega el estado 'finalizado' a srv_contacto.
 * Permite a la empresa cerrar una conversacion de contacto:
 * ninguna de las partes puede volver a escribir.
 */
class SrvContactoFinalizado extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE srv_contacto
            MODIFY COLUMN estado ENUM('pendiente','aceptado','rechazado','en_revision','finalizado') NOT NULL DEFAULT 'pendiente'");
        $this->db->query("ALTER TABLE srv_contacto_mensaje
            MODIFY COLUMN emisor_tipo ENUM('cliente','prestador','sistema') NOT NULL DEFAULT 'prestador'");
    }

    public function down()
    {
        $this->db->query("UPDATE srv_contacto SET estado = 'rechazado' WHERE estado = 'finalizado'");
        $this->db->query("ALTER TABLE srv_contacto
            MODIFY COLUMN estado ENUM('pendiente','aceptado','rechazado','en_revision') NOT NULL DEFAULT 'pendiente'");
        $this->db->query("UPDATE srv_contacto_mensaje SET emisor_tipo = 'prestador' WHERE emisor_tipo = 'sistema'");
        $this->db->query("ALTER TABLE srv_contacto_mensaje
            MODIFY COLUMN emisor_tipo ENUM('cliente','prestador') NOT NULL DEFAULT 'prestador'");
    }
}
