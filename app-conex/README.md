# app-conex — App movil CONEX (Ionic + React)

App movil para **candidato** y **empresa**. Consume la API REST del backend CodeIgniter 4.

## API

Base URL: `https://conex.softlutionic.com/api` (local: `http://localhost/CONEX/api`)

### Auth

Todos los endpoints (excepto login/register) requieren header:
```
Authorization: Bearer {token}
```

| Metodo | Ruta | Descripcion |
|---|---|---|
| POST | `/login` | `{usuario, password, device_name?}` → `{token, user}` |
| POST | `/register` | `{tipo_cuenta, nombre, apellido, usuario, email, telefono, password, password_confirm, razon_social?}` → `{token, user}` |
| POST | `/logout` | Revoca el token actual |
| GET  | `/me` | Usuario actual + perfil segun rol |

### Vacantes (ambos roles)

| Metodo | Ruta | Params |
|---|---|---|
| GET | `/vacantes` | `q, categoria, modalidad, ciudad, lat, lng, radio, orden, page, per_page` |
| GET | `/vacantes/{slug}` | Detalle + requisitos + habilidades |
| GET | `/vacantes/meta/filtros` | Categorias, modalidades, ordenes |
| GET | `/cv/{id}` | Descarga CV (dueno o empresa con postulacion) |

### Candidato

| Metodo | Ruta | Descripcion |
|---|---|---|
| GET  | `/candidato/perfil` | Perfil completo |
| POST | `/candidato/perfil` | Actualizar perfil |
| GET  | `/candidato/cvs` | Lista de CVs |
| POST | `/candidato/cv` | Subir CV (multipart, campo `cv`, max 5MB) |
| DELETE | `/candidato/cv/{id}` | Eliminar CV |
| GET  | `/candidato/postulaciones` | Mis postulaciones |
| POST | `/candidato/postularse/{vacante_id}` | `{cv_id?, mensaje?}` |

### Empresa

| Metodo | Ruta | Descripcion |
|---|---|---|
| GET  | `/empresa/perfil` | Perfil empresa |
| POST | `/empresa/perfil` | Actualizar perfil |
| GET  | `/empresa/vacantes` | `?estado=&page=` |
| POST | `/empresa/vacantes` | Crear vacante |
| PUT  | `/empresa/vacantes/{id}` | Actualizar |
| GET  | `/empresa/vacantes/{id}/postulaciones` | Candidatos postulados |
| POST | `/empresa/postulaciones/{id}/estado` | `{estado: enviada\|en_revision\|entrevista\|oferta\|contratado\|rechazada}` |

## Seguridad

- **Tokens**: SHA-256 hash en BD (`api_token`), expiran a 30 dias, revocables
- **Rate limit**: 5 req/min en login/register (anti fuerza bruta), 120 req/min el resto por IP+endpoint → `429` + `Retry-After`
- **CORS**: solo orígenes Ionic (`capacitor://`, `ionic://`, `localhost`)
- **Validacion**: todos los inputs validados server-side; errores devuelven `422` con `{error, errors{}}`
- **Roles**: endpoints separados por rol — candidato no puede acceder a rutas de empresa y viceversa (`403`)

## Errores

```json
{ "error": "mensaje", "errors": { "campo": "detalle" } }
```

Codigos: `400` mal request, `401` sin/invalido token, `403` sin permiso/rol, `404` no existe, `409` conflicto (ej: ya postulado), `422` validacion, `429` rate limit, `500` error servidor.

## Scaffolding (cuando se inicie la app)

```bash
npm install -g @ionic/cli
cd app-conex
ionic start app tabs --type=react --capacitor
```
