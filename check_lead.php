<?php
$db = new PDO('mysql:host=localhost;dbname=conex', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$rows = $db->query("SELECT id, tipo, estado, empresa_nombre, nombre_contacto FROM crm_lead ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
