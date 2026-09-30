<?php
$db = new PDO('mysql:host=mysql5044.site4now.net;dbname=db_aa03a4_conex', 'aa03a4_conex', 'easy2024', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$db->exec("CREATE TABLE IF NOT EXISTS cfg_configuracion (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    clave VARCHAR(100) NOT NULL,
    valor TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
echo "cfg_configuracion ok\n";

$db->exec("CREATE TABLE IF NOT EXISTS cfg_ia_modelo (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    proveedor VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    api_key VARCHAR(255) NULL,
    proposito VARCHAR(50) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
echo "cfg_ia_modelo ok\n";

$keys = [
    'empresa_nombre', 'empresa_ruc', 'empresa_telefono', 'empresa_email',
    'empresa_direccion', 'empresa_ciudad', 'empresa_region', 'empresa_sitio_web',
    'empresa_facebook', 'empresa_instagram', 'empresa_linkedin',
];
$now = date('Y-m-d H:i:s');
$stmt = $db->prepare("INSERT IGNORE INTO cfg_configuracion (clave, valor, created_at, updated_at) VALUES (?, '', ?, ?)");
foreach ($keys as $k) { $stmt->execute([$k, $now, $now]); }
$db->exec("UPDATE cfg_configuracion SET valor = 'CONEX' WHERE clave = 'empresa_nombre' AND valor = ''");
echo "defaults ok\n";

$exists = $db->query("SELECT id FROM migrations WHERE version = '2026-09-29-000002'")->rowCount() > 0;
if (!$exists) {
    $batch = (int)$db->query("SELECT MAX(batch) FROM migrations")->fetchColumn() + 1;
    $db->exec("INSERT INTO migrations (version, class, `group`, namespace, time, batch) VALUES ('2026-09-29-000002', 'App\\Database\\Migrations\\CfgConfiguracion', 'default', 'App', " . time() . ", $batch)");
    echo "migracion registrada\n";
}
echo "Done\n";
