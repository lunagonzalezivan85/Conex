import React, { useEffect, useState } from 'react';
import { IonContent, IonPage, IonButton, useIonRouter } from '@ionic/react';
import { Preferences } from '@capacitor/preferences';
import { authApi } from '../services/api';
import '../theme/splash.css';

const MIN_ANIM_MS = 2000;   // animacion minima visible
const MAX_WAIT_MS = 8000;   // nunca quedarse colgado: fallback a login

/**
 * Splash animado. El fondo sigue el tema del dispositivo
 * (claro/oscuro) via --ion-background-color.
 * Siempre navega: con token valido -> dashboard, si no -> login.
 * Si algo falla, muestra boton para continuar manualmente.
 */
const Splash: React.FC = () => {
  const router = useIonRouter();
  const [error, setError] = useState(false);

  useEffect(() => {
    let cancelled = false;

    const nav = (to: string) => {
      if (!cancelled) router.push(to, 'root', 'replace');
    };

    // Red de seguridad: a los 8s forzar login pase lo que pase
    const fallback = setTimeout(() => {
      if (!cancelled) setError(true);
    }, MAX_WAIT_MS);

    const boot = async () => {
      const minWait = new Promise(r => setTimeout(r, MIN_ANIM_MS));
      let logged = false;

      try {
        const { value: token } = await Preferences.get({ key: 'conex_token' });
        if (token) {
          try {
            await authApi.me();
            logged = true;
          } catch {
            await Preferences.remove({ key: 'conex_token' }).catch(() => {});
          }
        }
      } catch {
        // Preferences no disponible o fallo -> ir a login
      }

      await minWait;
      if (cancelled) return;
      clearTimeout(fallback);
      nav(logged ? '/app/dashboard' : '/login');
    };

    boot().catch(() => {
      if (!cancelled) { clearTimeout(fallback); nav('/login'); }
    });

    return () => { cancelled = true; clearTimeout(fallback); };
  }, [router]);

  return (
    <IonPage>
      <IonContent scrollY={false}>
        <div className="splash-wrap">
          <div className="splash-glow" />
          <div className="splash-logo">Cx</div>
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
