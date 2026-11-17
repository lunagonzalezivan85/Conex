import React, { useState, useEffect } from 'react';
import {
  IonContent, IonPage, IonInput, IonButton, IonIcon,
  IonText, IonSpinner, IonItem, IonInputPasswordToggle, useIonRouter,
} from '@ionic/react';
import {
  personOutline, lockClosedOutline, briefcaseOutline,
  businessOutline, logInOutline, chevronForwardOutline,
  chevronBackOutline,
} from 'ionicons/icons';
import { useLocation } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { ApiError } from '../services/api';
import '../theme/auth.css';

const Login: React.FC = () => {
  const router = useIonRouter();
  const location = useLocation();
  const { login } = useAuth();

  const [vista, setVista] = useState<'opciones' | 'login'>('opciones');
  const [usuario, setUsuario] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  // Si llega con ?modo=login, abrir directo el formulario
  useEffect(() => {
    const params = new URLSearchParams(location.search);
    if (params.get('modo') === 'login') {
      setVista('login');
    }
  }, [location.search]);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (busy) return;
    setError('');

    if (!usuario.trim() || !password) {
      setError('Ingresa tu usuario, cédula o email y tu contraseña');
      return;
    }

    setBusy(true);
    try {
      await login(usuario.trim(), password);
      router.push('/app/dashboard', 'root', 'replace');
    } catch (err) {
      const e = err as ApiError;
      if (e.status === 429) {
        setError('Demasiados intentos. Espera un momento.');
      } else if (e.status === 422 && e.errors) {
        setError(Object.values(e.errors)[0]);
      } else {
        setError(e.error || 'Credenciales inválidas. Verifica tu usuario y contraseña.');
      }
    } finally {
      setBusy(false);
    }
  };

  return (
    <IonPage>
      <IonContent scrollY={false}>
        <div className="auth-wrap">
          <div className="auth-card">
            {/* Logo Cx */}
            <div className="auth-logo">
              C<span>x</span>
            </div>

            {vista === 'opciones' ? (
              <>
                <h1 className="auth-title">Bienvenido a CONEX</h1>
                <p className="auth-sub" style={{ marginBottom: 20 }}>
                  Conectando talento con oportunidades
                </p>

                {/* Los 3 botones principales requeridos */}
                <div className="portal-options">
                  {/* 1. Busco Empleo */}
                  <button
                    type="button"
                    className="portal-btn portal-btn-empleo"
                    onClick={() => router.push('/registro')}
                  >
                    <div className="portal-btn-icon">
                      <IonIcon icon={briefcaseOutline} />
                    </div>
                    <div className="portal-btn-content">
                      <span className="portal-btn-title">Busco Empleo</span>
                      <span className="portal-btn-sub">
                        Crea tu perfil con tu cédula o QR en 30 segundos
                      </span>
                    </div>
                    <IonIcon icon={chevronForwardOutline} className="portal-btn-arrow" />
                  </button>

                  {/* 2. Busco Talento */}
                  <button
                    type="button"
                    className="portal-btn portal-btn-talento"
                    onClick={() => router.push('/registro-empresa')}
                  >
                    <div className="portal-btn-icon">
                      <IonIcon icon={businessOutline} />
                    </div>
                    <div className="portal-btn-content">
                      <span className="portal-btn-title">Busco Talento</span>
                      <span className="portal-btn-sub">
                        1 mes de prueba gratis • 5 vacantes • 8 candidatos (USD)
                      </span>
                    </div>
                    <IonIcon icon={chevronForwardOutline} className="portal-btn-arrow" />
                  </button>

                  {/* 3. Ya tengo una cuenta */}
                  <button
                    type="button"
                    className="portal-btn portal-btn-cuenta"
                    onClick={() => {
                      setError('');
                      setVista('login');
                    }}
                  >
                    <div className="portal-btn-icon">
                      <IonIcon icon={logInOutline} />
                    </div>
                    <div className="portal-btn-content">
                      <span className="portal-btn-title">Ya tengo una cuenta</span>
                      <span className="portal-btn-sub">
                        Inicia sesión con tu usuario, cédula o email
                      </span>
                    </div>
                    <IonIcon icon={chevronForwardOutline} className="portal-btn-arrow" />
                  </button>
                </div>
              </>
            ) : (
              <>
                <div style={{ display: 'flex', alignItems: 'center', marginBottom: 10 }}>
                  <button
                    type="button"
                    onClick={() => {
                      setError('');
                      setVista('opciones');
                    }}
                    style={{
                      background: 'none',
                      border: 'none',
                      display: 'flex',
                      alignItems: 'center',
                      gap: 4,
                      fontSize: 14,
                      color: '#1B2A4A',
                      fontWeight: 600,
                      cursor: 'pointer',
                      padding: 4,
                    }}
                  >
                    <IonIcon icon={chevronBackOutline} style={{ fontSize: 18 }} />
                    Volver a opciones
                  </button>
                </div>

                <h1 className="auth-title">Iniciar sesión</h1>
                <p className="auth-sub" style={{ marginBottom: 20 }}>
                  Ingresa tu usuario, cédula o email
                </p>

                <form onSubmit={submit} className="auth-form">
                  <IonItem className="auth-item" lines="none">
                    <IonIcon icon={personOutline} slot="start" />
                    <IonInput
                      placeholder="Usuario, cédula o email"
                      value={usuario}
                      onIonInput={e => setUsuario(e.detail.value ?? '')}
                      autocomplete="username"
                      required
                    />
                  </IonItem>

                  <IonItem className="auth-item" lines="none">
                    <IonIcon icon={lockClosedOutline} slot="start" />
                    <IonInput
                      type="password"
                      placeholder="Contraseña o PIN"
                      value={password}
                      onIonInput={e => setPassword(e.detail.value ?? '')}
                      autocomplete="current-password"
                      required
                    >
                      <IonInputPasswordToggle slot="end" />
                    </IonInput>
                  </IonItem>

                  {error && (
                    <IonText color="danger" className="auth-error">
                      <small>{error}</small>
                    </IonText>
                  )}

                  <IonButton expand="block" type="submit" disabled={busy} className="auth-btn">
                    {busy ? <IonSpinner name="crescent" /> : 'Iniciar sesión'}
                  </IonButton>

                  <div style={{ textAlign: 'center', marginTop: 14 }}>
                    <IonButton
                      fill="clear"
                      size="small"
                      onClick={() => {
                        setError('');
                        setVista('opciones');
                      }}
                      className="login-registro-link"
                      style={{ fontSize: 13, textTransform: 'none' }}
                    >
                      ¿No tienes cuenta aún? <strong>Crear cuenta</strong>
                    </IonButton>
                  </div>
                </form>
              </>
            )}
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default Login;
