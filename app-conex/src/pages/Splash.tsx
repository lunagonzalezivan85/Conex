import React, { useEffect } from 'react';
import { IonContent, IonPage } from '@ionic/react';
import { useHistory } from 'react-router-dom';
import { Preferences } from '@capacitor/preferences';
import { authApi } from '../services/api';
import '../theme/splash.css';

/**
 * Splash animado. El fondo sigue el tema del dispositivo
 * (claro/oscuro) via --ion-background-color.
 */
const Splash: React.FC = () => {
  const history = useHistory();

  useEffect(() => {
    let cancelled = false;

    const boot = async () => {
      // Tiempo minimo de animacion
      const minWait = new Promise(r => setTimeout(r, 2000));

      // Revalidar token si existe
      const auth = (async () => {
        const { value: token } = await Preferences.get({ key: 'conex_token' });
        if (!token) return false;
        try {
          await authApi.me();
          return true;
        } catch {
          await Preferences.remove({ key: 'conex_token' });
          return false;
        }
      })();

      const [, logged] = await Promise.all([minWait, auth]);
      if (cancelled) return;
      history.replace(logged ? '/app/dashboard' : '/login');
    };

    boot();
    return () => { cancelled = true; };
  }, [history]);

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
          <div className="splash-bar">
            <div className="splash-bar-fill" />
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default Splash;
