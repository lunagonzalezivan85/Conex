import React, { useState } from 'react';
import {
  IonContent, IonPage, IonInput, IonButton, IonIcon,
  IonText, IonSpinner, IonItem, IonInputPasswordToggle,
} from '@ionic/react';
import { useHistory } from 'react-router-dom';
import { personOutline, lockClosedOutline } from 'ionicons/icons';
import { useAuth } from '../context/AuthContext';
import { ApiError } from '../services/api';
import '../theme/auth.css';

const Login: React.FC = () => {
  const history = useHistory();
  const { login } = useAuth();
  const [usuario, setUsuario] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  const submit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (busy) return;
    setError('');

    if (!usuario.trim() || !password) {
      setError('Ingresa tu usuario y contrasena');
      return;
    }

    setBusy(true);
    try {
      await login(usuario.trim(), password);
      history.replace('/app/dashboard');
    } catch (err) {
      const e = err as ApiError;
      if (e.status === 429) {
        setError('Demasiados intentos. Espera un momento.');
      } else if (e.status === 422 && e.errors) {
        setError(Object.values(e.errors)[0]);
      } else {
        setError(e.error || 'No se pudo iniciar sesion');
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
            <div className="auth-logo">C</div>
            <h1 className="auth-title">Bienvenido a CONEX</h1>
            <p className="auth-sub">Inicia sesion para continuar</p>

            <form onSubmit={submit} className="auth-form">
              <IonItem className="auth-item" lines="full">
                <IonIcon icon={personOutline} slot="start" />
                <IonInput
                  placeholder="Usuario o email"
                  value={usuario}
                  onIonInput={e => setUsuario(e.detail.value ?? '')}
                  autocomplete="username"
                  required
                />
              </IonItem>

              <IonItem className="auth-item" lines="full">
                <IonIcon icon={lockClosedOutline} slot="start" />
                <IonInput
                  type="password"
                  placeholder="Contrasena"
                  value={password}
                  onIonInput={e => setPassword(e.detail.value ?? '')}
                  autocomplete="current-password"
                  required
                />
                <IonInputPasswordToggle slot="end" />
              </IonItem>

              {error && (
                <IonText color="danger" className="auth-error">
                  <small>{error}</small>
                </IonText>
              )}

              <IonButton expand="block" type="submit" disabled={busy} className="auth-btn">
                {busy ? <IonSpinner name="crescent" /> : 'Iniciar sesion'}
              </IonButton>
            </form>
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default Login;
