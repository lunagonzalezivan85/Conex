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

async function getToken(): Promise<string | null> {
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

  const res = await fetch(`${API_URL}${path}`, { method, headers, body: payload });

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

export const authApi = {
  login: (usuario: string, password: string) =>
    api<{ token: string; expires_at: string; user: ApiUser }>('/login', {
      method: 'POST',
      auth: false,
      body: { usuario, password, device_name: 'conex-app' },
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

export const candidatoApi = {
  perfil: () => api('/candidato/perfil'),
  actualizarPerfil: (data: Record<string, any>) =>
    api('/candidato/perfil', { method: 'POST', body: data }),
  cvs: () => api<{ data: any[] }>('/candidato/cvs'),
  subirCv: (file: File) => {
    const fd = new FormData();
    fd.append('cv', file);
    return api('/candidato/cv', { method: 'POST', body: fd });
  },
  eliminarCv: (id: number) => api(`/candidato/cv/${id}`, { method: 'DELETE' }),
  postulaciones: () => api<{ data: any[] }>('/candidato/postulaciones'),
  postularse: (vacanteId: number, cvId?: number, mensaje?: string) =>
    api(`/candidato/postularse/${vacanteId}`, {
      method: 'POST',
      body: { cv_id: cvId, mensaje },
    }),
};

export const empresaApi = {
  perfil: () => api('/empresa/perfil'),
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
