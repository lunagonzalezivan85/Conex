<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            [
                'nombre'      => 'Programacion y Tecnologia',
                'slug'        => 'programacion-tecnologia',
                'descripcion' => 'Desarrollo de software, analisis de datos, infraestructura y mas',
                'icono'       => '&#128187;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Hosteleria y Turismo',
                'slug'        => 'hosteleria-turismo',
                'descripcion' => 'Hoteles, restaurantes, cocina, atencion al cliente',
                'icono'       => '&#127968;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Seguridad',
                'slug'        => 'seguridad',
                'descripcion' => 'Vigilancia, seguridad privada, control de acceso',
                'icono'       => '&#128737;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Administracion y Finanzas',
                'slug'        => 'administracion-finanzas',
                'descripcion' => 'Contabilidad, auditoria, banca, administracion',
                'icono'       => '&#128202;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Ventas y Marketing',
                'slug'        => 'ventas-marketing',
                'descripcion' => 'Ventas, marketing digital, publicidad, comercio',
                'icono'       => '&#128200;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Salud',
                'slug'        => 'salud',
                'descripcion' => 'Medicina, enfermeria, farmacia, laboratorio',
                'icono'       => '&#128138;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Educacion',
                'slug'        => 'educacion',
                'descripcion' => 'Docencia, tutorias, capacitacion, pedagogia',
                'icono'       => '&#128218;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Construccion y Obra',
                'slug'        => 'construccion-obra',
                'descripcion' => 'Albanileria, plomeria, electricidad, obra civil',
                'icono'       => '&#128736;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Transporte y Logistica',
                'slug'        => 'transporte-logistica',
                'descripcion' => 'Conduccion, distribucion, almacen, cadena de suministro',
                'icono'       => '&#128666;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Diseno y Creatividad',
                'slug'        => 'diseno-creatividad',
                'descripcion' => 'Diseno grafico, UX/UI, multimedia, audiovisuales',
                'icono'       => '&#127912;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Recursos Humanos',
                'slug'        => 'recursos-humanos',
                'descripcion' => 'Gestion de talento, reclutamiento, nomina',
                'icono'       => '&#129489;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Agricultura y Ganaderia',
                'slug'        => 'agricultura-ganaderia',
                'descripcion' => 'Agricultura, ganaderia, pesca, agroindustria',
                'icono'       => '&#127806;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Legal y Juridico',
                'slug'        => 'legal-juridico',
                'descripcion' => 'Abogacia, notaria, asesoramiento legal',
                'icono'       => '&#9878;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Ingenieria',
                'slug'        => 'ingenieria',
                'descripcion' => 'Ingenieria civil, industrial, mecanica, electrica',
                'icono'       => '&#9881;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nombre'      => 'Atencion al Cliente',
                'slug'        => 'atencion-cliente',
                'descripcion' => 'Call center, soporte, servicio al cliente',
                'icono'       => '&#128222;',
                'estado'      => 'activo',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('cat_categoria')->insertBatch($categorias);
    }
}
