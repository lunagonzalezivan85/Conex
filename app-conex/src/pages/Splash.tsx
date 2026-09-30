import React, { useEffect, useState } from 'react';
import { IonContent, IonPage, IonButton, useIonRouter } from '@ionic/react';
import { useAuth } from '../context/AuthContext';
import '../theme/splash.css';

const MIN_ANIM_MS = 2000;   // animacion minima visible
const MAX_WAIT_MS = 8000;   // fallback: mostrar boton para continuar

/**
 * Splash animado. El fondo sigue el tema del dispositivo
 * (claro/oscuro) via --ion-background-color.
 * Espera la revalidacion de sesion de AuthContext (loading)
 * y navega: user -> dashboard, sin user -> login.
 * Si tarda demasiado muestra boton Continuar.
 */
const Splash: React.FC = () => {
  const router = useIonRouter();
  const { user, loading } = useAuth();
  const [error, setError] = useState(false);
  const [navDone, setNavDone] = useState(false);

  // Red de seguridad: a los 8s mostrar boton manual (fetch tarda max 15s)
  useEffect(() => {
    const fallback = setTimeout(() => setError(true), MAX_WAIT_MS);
    return () => clearTimeout(fallback);
  }, []);

  useEffect(() => {
    if (navDone || loading) return;
    let cancelled = false;
    // esperar la animacion minima antes de navegar
    const t = setTimeout(() => {
      if (!cancelled) {
        setNavDone(true);
        router.push(user ? '/app/dashboard' : '/login', 'root', 'replace');
      }
    }, MIN_ANIM_MS);
    return () => { cancelled = true; clearTimeout(t); };
  }, [loading, user, router, navDone]);

  return (
    <IonPage>
      <IonContent scrollY={false}>
        <div className="splash-wrap">
          <div className="splash-glow" />
          <div className="splash-logo">C<span>x</span></div>
          <div className="splash-word" aria-label="CONEX">
            {'CONEX'.split('').map((l, i) => (
              <span key={i}>{l}</span>
            ))}
          </div>
          <div className="splash-tag">Conecta talento con oportunidades</div>
          {error ? (
            <IonButton
              className="splash-retry"
              fill="outline"
              onClick={() => router.push('/login', 'root', 'replace')}
            >
              Continuar
            </IonButton>
          ) : (
            <div className="splash-bar">
              <div className="splash-bar-fill" />
            </div>
          )}
        </div>
      </IonContent>
    </IonPage>
  );
};

export default Splash;
