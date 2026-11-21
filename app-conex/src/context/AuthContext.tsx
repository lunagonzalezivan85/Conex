import React, { createContext, useCallback, useContext, useEffect, useState } from 'react';
import { api, ApiUser, authApi, clearToken, getToken, saveToken } from '../services/api';

interface AuthState {
  user: ApiUser | null;
  loading: boolean;
  login: (usuario: string, password: string) => Promise<ApiUser>;
  logout: () => Promise<void>;
  refresh: () => Promise<ApiUser | null>;
}

const AuthContext = createContext<AuthState>({
  user: null,
  loading: true,
  login: async () => { throw new Error('no init'); },
  logout: async () => {},
  refresh: async () => null,
});

export const useAuth = () => useContext(AuthContext);

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [user, setUser] = useState<ApiUser | null>(null);
  const [loading, setLoading] = useState(true);

  const refresh = useCallback(async () => {
    try {
      const token = await getToken();
      if (!token) { setUser(null); return null; }
      const res = await authApi.me();
      setUser(res.user);
      return res.user;
    } catch (e: any) {
      setUser(null);
      // solo revocar la sesion local si el server dice que el token murio
      if (e?.status === 401) await clearToken();
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  // Sesion persistida: revalidar al abrir la app
  useEffect(() => { refresh(); }, [refresh]);

  const login = useCallback(async (usuario: string, password: string) => {
    const res = await authApi.login(usuario, password);
    await saveToken(res.token);
    let currentUser = res.user;
    try {
      const me = await authApi.me();
      if (me?.user) currentUser = me.user;
    } catch {
      // Usar usuario retornado en res.user como fallback
    }
    setUser(currentUser);
    setLoading(false);
    return currentUser;
  }, []);

  const logout = useCallback(async () => {
    try {
      await authApi.logout();
    } catch {
      // si falla el revoke igual limpiamos local
    }
    await clearToken();
    setUser(null);
  }, []);

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, refresh }}>
      {children}
    </AuthContext.Provider>
  );
};
