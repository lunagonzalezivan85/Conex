# Despliegue — CONEX

## Requisitos

- **PHP `^8.2`** (composer.json) — CI4 instalado: v4.7.4.
- Extensiones: `intl`, `mbstring`, `json`, `mysqlnd`, `libcurl` (llamadas a
  Gemini), `fileinfo` (subida de CVs).
- MySQL/MariaDB, Composer (`composer.phar` incluido en la raíz), Apache
  `mod_rewrite`.
- Node/npm solo para la app móvil (`app-conex/`); JDK 21 para compilar el APK.

## Ejecución local (XAMPP)

```powershell
cd C:\xampp\htdocs\CONEX
C:\xampp\php\php.exe composer.phar install
copy env .env
# .env: app.baseURL='http://localhost/CONEX/', database.default.* (BD conex),
#       GEMINI_API_KEY=...
C:\xampp\php\php.exe spark migrate      # 44 migraciones
C:\xampp\php\php.exe spark serve        # opcional
```

- URL típica: `http://localhost/CONEX/` (el proyecto sirve desde la raíz vía
  `.htaccess`; `public/` existe también como DocumentRoot alternativo).
- `writable/` con permisos de escritura.

## Producción

- **Servidor:** IIS en `conex.softlutionic.com` (la app móvil apunta a
  `https://conex.softlutionic.com/api`); `web.config` incluido.
- **BD remota:** grupo de producción en `app/Config/Database.php`
  (SmarterASP — host `mysql5044.site4now.net`, BD `db_aa03a4_conex`).
- **Subida de archivos:** FTP — `.ftp-config` en la raíz (config del editor/deploy).
- `CI_ENVIRONMENT = production`, `app.baseURL` del dominio, `encryption.key`
  generado y `GEMINI_API_KEY` válida en el `.env` del servidor.
- Migrar la BD remota desde local con el grupo correspondiente
  (`php spark migrate --group <grupo-produccion>`) o replicando las
  migraciones en el servidor.
- HTTPS obligatorio (API consumida por la app Android).

## App móvil (`app-conex/`)

```powershell
cd app-conex
npm install
npm run build
npx cap sync android
cd android; .\gradlew.bat assembleDebug   # JAVA_HOME → JDK 21
```

APK debug: `app-conex/android/app/build/outputs/apk/debug/app-debug.apk`.
Git del proyecto: `master` local → `git push -f origin master:main` (según
`AGENT.md`).

## No versionar / no subir

`.env`, `writable/`, `vendor/`, `node_modules/` (web y app),
`.ftp-config` con credenciales reales.
