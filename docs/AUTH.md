# AUTENTICACION - Guia de Implementacion

## CONEX - Sistema de Reclutamiento de Personal

**Version:** 1.0  
**Fecha:** 2026-09-12  
**Estado:** En progreso

---

## 1. Autenticacion Local (Prioridad)

### 1.1 Login con usuario/email + contrasena

**Flujo:**
1. Usuario ingresa `usuario` o `email` + `password` en `/login`
2. `AuthController::attemptLogin` valida credenciales contra `auth_user`
3. Si es correcto, crea sesion y redirige segun rol:
   - `admin` / `asesor` → `/admin`
   - `empresa` → `/empresa`
   - `candidato` → `/candidato`
4. Si falla, muestra error y mantiene input

**Seguridad:**
- Contrasena hasheada con `PASSWORD_BCRYPT`
- Proteccion CSRF en todos los formularios
- Campo `last_login` actualizado en cada login
- Estados de cuenta: `activo`, `inactivo`, `suspendido`

### 1.2 Registro

**Flujo:**
1. Usuario elige tipo de cuenta con toggle button: **Candidato** o **Empresa**
2. Campos comunes: nombre, apellido, usuario, email, telefono, password, password_confirm
3. Si es empresa: campo extra `razon_social`
4. Se crea registro en `auth_user` + `cand_candidato` o `emp_empresa`
5. Login automatico y redireccion al dashboard

**Validaciones:**
- `usuario`: unico, min 3 caracteres
- `email`: unico, formato valido
- `telefono`: min 8 caracteres
- `password`: min 8 caracteres, debe coincidir con confirmacion

---

## 2. Login con Google OAuth 2.0 (Pendiente)

### 2.1 Pasos para configurar

1. **Crear proyecto en Google Cloud Console**
   - Ir a https://console.cloud.google.com/
   - Crear nuevo proyecto o seleccionar uno existente

2. **Configurar OAuth Consent Screen**
   - Ir a APIs & Services → OAuth consent screen
   - Tipo: External
   - Completar: nombre de app (CONEX), email de soporte, dominio
   - Scopes necesarios:
     - `openid`
     - `email`
     - `profile`

3. **Crear credenciales OAuth 2.0**
   - Ir a APIs & Services → Credentials
   - Crear credenciales → OAuth client ID
   - Tipo: Web application
   - Authorized redirect URIs:
     - `http://localhost:8080/auth/google/callback` (desarrollo)
     - `https://dominio.com/auth/google/callback` (produccion)

4. **Instalar dependencia**
   ```bash
   composer require league/oauth2-google
   ```

5. **Configurar variables de entorno (.env)**
   ```env
   google.client_id = "tu-client-id.apps.googleusercontent.com"
   google.client_secret = "tu-client-secret"
   google.redirect_uri = "http://localhost:8080/auth/google/callback"
   ```

6. **Crear configuracion en app/Config/Google.php**
   ```php
   <?php
   namespace Config;
   use CodeIgniter\Config\BaseConfig;
   class Google extends BaseConfig {
       public string $clientId = '';
       public string $clientSecret = '';
       public string $redirectUri = '';
   }
   ```

7. **Implementar en AuthController**
   - `googleRedirect()`: redirige a Google con scopes
   - `googleCallback()`: recibe codigo, intercambia por token, obtiene datos del usuario
   - Si el email ya existe en `auth_user`: login directo
   - Si no existe: crear cuenta con datos de Google (rol: candidato por defecto)

8. **Agregar columna `google_id` a `auth_user`**
   ```sql
   ALTER TABLE auth_user ADD COLUMN google_id VARCHAR(255) NULL AFTER avatar;
   ALTER TABLE auth_user ADD UNIQUE KEY (google_id);
   ```

### 2.2 Flujo de Google Login

```
[Usuario hace clic en "Iniciar sesion con Google"]
    |
    v
[Redirect a Google OAuth consent screen]
    |
    v
[Usuario autoriza acceso]
    |
    v
[Google redirige a /auth/google/callback?code=xxx]
    |
    v
[AuthController::googleCallback()]
    |
    +---> Buscar usuario por google_id o email
    |         |
    |         +---> Existe: crear sesion, redirigir
    |         |
    |         +---> No existe: crear cuenta (candidato), login
    |
    v
[Redirigir al dashboard segun rol]
```

---

## 3. Verificacion de Cuenta (Pendiente)

### 3.1 Verificacion por Email

**Estado actual:** Las cuentas se crean activas (`estado = activo`).

**Para activar verificacion por email:**

1. **Configurar servicio de email**
   - Opcion A: SMTP con Gmail/Outlook
   - Opcion B: Servicio transaccional (SendGrid, Mailgun, Amazon SES)
   - Configurar en `app/Config/Email.php`

2. **Crear columna `verification_token` en `auth_user`**
   ```sql
   ALTER TABLE auth_user ADD COLUMN verification_token VARCHAR(64) NULL AFTER password;
   ALTER TABLE auth_user ADD COLUMN email_verified_at DATETIME NULL AFTER verification_token;
   ```

3. **Modificar flujo de registro**
   - Al registrar: `estado = pendiente`, generar token unico
   - Enviar email con link: `/verificar/{token}`
   - Al visitar el link: marcar `email_verified_at`, cambiar `estado = activo`

4. **Crear plantilla de email**
   - Asunto: "Verifica tu cuenta en CONEX"
   - Cuerpo: saludo + boton de verificacion + link alternativo

### 3.2 Verificacion por SMS

**Para activar verificacion por SMS:**

1. **Configurar servicio SMS**
   - Opcion: Twilio, MessageBird, o proveedor local
   - Instalar SDK correspondiente

2. **Crear columna `phone_verification_code` en `auth_user`**
   ```sql
   ALTER TABLE auth_user ADD COLUMN phone_verification_code VARCHAR(6) NULL AFTER verification_token;
   ALTER TABLE auth_user ADD COLUMN phone_verified_at DATETIME NULL AFTER phone_verification_code;
   ```

3. **Flujo:**
   - Al registrar: generar codigo de 6 digitos
   - Enviar SMS al telefono registrado
   - Mostrar pantalla de verificacion: ingresar codigo
   - Al validar: marcar `phone_verified_at`

### 3.3 Recuperacion de Contrasena

**Pendiente de implementar cuando el servicio de email este activo.**

Flujo:
1. Usuario solicita recuperacion en `/recuperar-password`
2. Sistema genera token unico con expiracion (1 hora)
3. Se envia email con link: `/reset-password/{token}`
4. Usuario ingresa nueva contrasena
5. Sistema actualiza y invalida el token

---

## 4. Gestion de Sesion

### 4.1 Datos en sesion
```php
$_SESSION = [
    'user_id' => 1,
    'nombre' => 'Juan',
    'apellido' => 'Perez',
    'email' => 'juan@example.com',
    'role_id' => 4,
    'isLoggedIn' => true,
];
```

### 4.2 Filtros de autenticacion (pendiente)

Crear `app/Filters/AuthFilter.php` para proteger rutas privadas:
- Verificar `isLoggedIn` en sesion
- Redirigir a `/login` si no esta autenticado
- Verificar rol segun prefijo de ruta (`/admin`, `/empresa`, `/candidato`)

---

## 5. Pendientes

- [x] Login local (usuario/email + password)
- [x] Registro con toggle Candidato/Empresa
- [x] Logout
- [ ] Login con Google OAuth 2.0
- [ ] Verificacion por email
- [ ] Verificacion por SMS
- [ ] Recuperacion de contrasena
- [ ] Filtros de autenticacion por rol
- [ ] Rate limiting en intentos de login
