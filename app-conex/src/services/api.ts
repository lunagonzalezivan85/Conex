import { Preferences } from '@capacitor/preferences';
import { API_URL } from '../config';

const TOKEN_KEY = 'conex_token';

export interface ApiUser {
  id: number;
  nombre: string;
  apellido: string;
  usuario: string;
  email: string;
  telefono?: string | null;
  avatar?: string | null;
  avatar_url?: string | null;
  role: 'candidato' | 'empresa' | 'admin' | 'asesor' | string;
  perfil_completo: boolean;
  candidato?: any;
  empresa?: any;
}

export interface ApiError {
  error: string;
  errors?: Record<string, string>;
  status: number;
}

export async function getToken(): Promise<string | null> {
  const { value } = await Preferences.get({ key: TOKEN_KEY });
  return value;
}

export async function saveToken(token: string): Promise<void> {
  await Preferences.set({ key: TOKEN_KEY, value: token });
}

export async function clearToken(): Promise<void> {
  await Preferences.remove({ key: TOKEN_KEY });
}

/**
 * Wrapper fetch con Bearer token + manejo de errores consistente.
 */
export async function api<T = any>(
  path: string,
  options: {
    method?: 'GET' | 'POST' | 'PUT' | 'DELETE';
    body?: Record<string, any> | FormData;
    auth?: boolean;
  } = {}
): Promise<T> {
  const { method = 'GET', body, auth = true } = options;

  const headers: Record<string, string> = { Accept: 'application/json' };

  if (auth) {
    const token = await getToken();
    if (token) headers['Authorization'] = `Bearer ${token}`;
  }

  let payload: BodyInit | undefined;
  if (body instanceof FormData) {
    payload = body; // el navegador pone el boundary multipart
  } else if (body) {
    headers['Content-Type'] = 'application/json';
    payload = JSON.stringify(body);
  }

  // Timeout: ninguna request puede quedar colgada (ej. splash)
  const ctrl = new AbortController();
  const timer = setTimeout(() => ctrl.abort(), 15000);

  let res: Response;
  try {
    res = await fetch(`${API_URL}${path}`, {
      method, headers, body: payload, signal: ctrl.signal,
    });
  } catch (e: any) {
    clearTimeout(timer);
    throw {
      error: e?.name === 'AbortError' ? 'Tiempo de espera agotado' : 'Error de conexion',
      status: 0,
    } as ApiError;
  } finally {
    clearTimeout(timer);
  }

  let json: any = null;
  try {
    json = await res.json();
  } catch {
    // respuesta sin JSON
  }

  if (!res.ok) {
    const err: ApiError = {
      error: json?.error || `Error ${res.status}`,
      errors: json?.errors,
      status: res.status,
    };
    throw err;
  }

  return json as T;
}

/* ---------- Endpoints concretos ---------- */

export interface RegistroAppPayload {
  cedula: string;
  nombre: string;
  apellido: string;
  telefono: string;
  pin: string;
  email?: string;
  terminos: 1;
}

export const authApi = {
  login: (usuario: string, password: string) =>
    api<{ token: string; expires_at: string; user: ApiUser }>('/login', {
      method: 'POST',
      auth: false,
      body: { usuario, password, device_name: 'conex-app' },
    }),
  registerApp: (payload: RegistroAppPayload) =>
    api<{ token: string; expires_at: string; user: ApiUser }>('/register', {
      method: 'POST',
      auth: false,
      body: { ...payload, device_name: 'conex-app' },
    }),
  logout: () => api('/logout', { method: 'POST' }),
  me: () => api<{ user: ApiUser }>('/me'),
};

export const vacantesApi = {
  list: (params: Record<string, string | number | undefined> = {}) => {
    const qs = new URLSearchParams();
    Object.entries(params).forEach(([k, v]) => {
      if (v !== undefined && v !== '') qs.append(k, String(v));
    });
    return api<{ data: any[]; meta: any }>(`/vacantes?${qs.toString()}`);
  },
  show: (slug: string) => api<{ data: any }>(`/vacantes/${slug}`),
  filtros: () => api('/vacantes/meta/filtros'),
};

export const serviciosApi = {
  list: (params: { q?: string; page?: number; per_page?: number } = {}) => {
    const qs = new URLSearchParams();
    Object.entries(params).forEach(([k, v]) => {
      if (v !== undefined && v !== '') qs.append(k, String(v));
    });
    return api<{ data: any[]; meta: any }>(`/servicios?${qs.toString()}`);
  },
  // Solicitud de contacto: el prestador ve tus datos solo si acepta
  contactar: (id: number, data: { nombre: string; telefono: string; mensaje?: string; precio_propuesto?: number }) =>
    api<{ message: string; contacto_id?: number }>(`/servicios/${id}/contacto`, { method: 'POST', body: data }),
};

export interface CandidatoDashboard {
  postulaciones: number;
  servicios_brindados: number;
  vistas_perfil: number;
  ofrece_servicios: number;
  disponible_ahora: number;
  servicios: any[];
}

export interface CandidatoPerfilFull {
  user: ApiUser;
  candidato: any;
  experiencias: any[];
  educaciones: any[];
  habilidades: any[];
  idiomas: any[];
  cvs: any[];
  servicios: any[];
  catalogos: {
    niveles_experiencia: { id: number; nombre: string; anios_minimos: number; anios_maximos?: number | null }[];
    niveles_educacion: { id: number; nombre: string }[];
    idiomas: { id: number; nombre: string }[];
  };
}

export const candidatoApi = {
  perfil: () => api<{ data: CandidatoPerfilFull }>('/candidato/perfil'),
  dashboard: () => api<{ data: CandidatoDashboard }>('/candidato/dashboard'),
  // Pin 'disponible ahora' en el mapa de prestadores (estilo inDrive)
  disponibilidad: (data: { disponible: 0 | 1; lat?: number; lng?: number }) =>
    api<{ message: string; disponible_ahora: number }>('/candidato/disponibilidad', { method: 'POST', body: data }),
  actualizarPerfil: (data: Record<string, any>) =>
    api<{ message: string; porcentaje_perfil?: number; candidato: any }>('/candidato/perfil', { method: 'POST', body: data }),
  subirAvatar: (file: File) => {
    const fd = new FormData();
    fd.append('avatar', file);
    return api<{ message: string; avatar: string; avatar_url: string }>('/candidato/avatar', { method: 'POST', body: fd });
  },
  cvs: () => api<{ data: any[] }>('/candidato/cvs'),
  subirCv: (file: File) => {
    const fd = new FormData();
    fd.append('cv', file);
    return api('/candidato/cv', { method: 'POST', body: fd });
  },
  eliminarCv: (id: number) => api(`/candidato/cv/${id}`, { method: 'DELETE' }),
  guardarHabilidades: (habilidades: { habilidad_id: number; nivel?: string }[]) =>
    api<{ message: string; total: number; porcentaje_perfil?: number }>('/candidato/habilidades', { method: 'POST', body: { habilidades } }),
  guardarExperiencia: (data: {
    id?: number;
    empresa: string;
    cargo: string;
    descripcion?: string;
    fecha_inicio: string;
    fecha_fin?: string;
    actual?: number | boolean;
  }) => api<{ message: string; experiencia: any; porcentaje_perfil?: number }>('/candidato/experiencia', { method: 'POST', body: data }),
  eliminarExperiencia: (id: number) =>
    api<{ message: string; porcentaje_perfil?: number }>(`/candidato/experiencia/${id}`, { method: 'DELETE' }),
  guardarEducacion: (data: {
    id?: number;
    nivel_educacion_id?: number;
    institucion: string;
    titulo: string;
    fecha_inicio?: string;
    fecha_fin?: string;
    en_curso?: number | boolean;
  }) => api<{ message: string; educacion: any; porcentaje_perfil?: number }>('/candidato/educacion', { method: 'POST', body: data }),
  eliminarEducacion: (id: number) =>
    api<{ message: string; porcentaje_perfil?: number }>(`/candidato/educacion/${id}`, { method: 'DELETE' }),
  guardarIdiomas: (idiomas: { idioma_id?: number; nombre?: string; nivel: string }[]) =>
    api<{ message: string; total: number; porcentaje_perfil?: number }>('/candidato/idiomas', { method: 'POST', body: { idiomas } }),
  guardarServicio: (data: {
    id?: number;
    oficio: string;
    descripcion?: string;
    precio_desde?: number | string | null;
    precio_tipo?: 'por_hora' | 'por_servicio' | 'a_convenir';
    ciudad?: string;
    zona?: string;
    estado?: 'activo' | 'pausado';
  }) => api<{ message: string; servicio: any }>('/candidato/servicios', { method: 'POST', body: data }),
  eliminarServicio: (id: number) =>
    api<{ message: string }>(`/candidato/servicios/${id}`, { method: 'DELETE' }),
  solicitudes: () =>
    api<{ data: { recibidas: any[]; enviadas: any[] } }>('/candidato/solicitudes'),
  responderSolicitud: (
    id: number,
    payloadOrAccion: 'aceptar' | 'rechazar' | 'negociar' | 'mensaje' | { accion?: string; mensaje?: string; precio_propuesto?: number | string }
  ) => {
    const body = typeof payloadOrAccion === 'string' ? { accion: payloadOrAccion } : payloadOrAccion;
    return api<{
      message: string;
      estado: string;
      telefono?: string | null;
      precio_propuesto?: number | null;
      respuesta?: string | null;
      mensajes?: any[];
    }>(`/candidato/solicitudes/${id}`, {
      method: 'POST',
      body,
    });
  },
  postulaciones: () => api<{ data: any[] }>('/candidato/postulaciones'),
  postularse: (vacanteId: number, cvId?: number, mensaje?: string) =>
    api(`/candidato/postularse/${vacanteId}`, {
      method: 'POST',
      body: { cv_id: cvId, mensaje },
    }),
};

export const usuarioApi = {
  eliminarCuenta: () => api<{ message: string }>('/usuario/eliminar-cuenta', { method: 'POST' }),
};

export const empresaApi = {
  perfil: () => api('/empresa/perfil'),
  actualizarPerfil: (data: Record<string, any>) =>
    api<{ message: string; empresa: any }>('/empresa/perfil', { method: 'POST', body: data }),
  vacantes: (params: Record<string, string | number> = {}) => {
    const qs = new URLSearchParams();
    Object.entries(params).forEach(([k, v]) => qs.append(k, String(v)));
    return api<{ data: any[]; meta: any }>(`/empresa/vacantes?${qs.toString()}`);
  },
  postulaciones: (vacanteId: number) =>
    api<{ data: any[] }>(`/empresa/vacantes/${vacanteId}/postulaciones`),
  cambiarEstadoPostulacion: (id: number, estado: string) =>
    api(`/empresa/postulaciones/${id}/estado`, { method: 'POST', body: { estado } }),
};
