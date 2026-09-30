import React, { createContext, useCallback, useContext, useState } from 'react';
import { api, ApiUser, authApi, clearToken, saveToken } from '../services/api';

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
      const res = await authApi.me();
      setUser(res.user);
      return res.user;
    } catch {
      setUser(null);
      await clearToken();
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  const login = useCallback(async (usuario: string, password: string) => {
    const res = await authApi.login(usuario, password);
    await saveToken(res.token);
    const me = await authApi.me();
    setUser(me.user);
    return me.user;
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
