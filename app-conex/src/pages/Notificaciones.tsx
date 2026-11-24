import React, { useEffect, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonTitle, IonIcon,
  IonButton, IonSpinner, IonRefresher, IonRefresherContent,
  RefresherEventDetail, useIonRouter,
} from '@ionic/react';
import {
  notificationsOutline, chatbubbleEllipsesOutline, briefcaseOutline,
  checkmarkDoneOutline, chevronForward, arrowBackOutline,
} from 'ionicons/icons';
import { notificacionesApi } from '../services/api';
import '../theme/app.css';
import { useCxToast } from '../utils/cxFeedback';

interface Notificacion {
  id: number;
  tipo: string;
  titulo: string;
  mensaje: string;
  url?: string;
  leida: number;
  created_at: string;
}

const TIPO_ICON: Record<string, string> = {
  mensaje: chatbubbleEllipsesOutline,
  postulacion: briefcaseOutline,
};

const TIPO_COLOR: Record<string, string> = {
  mensaje: '#0FC6C2',
  postulacion: '#F97316',
};

/** Mapea la url web de la notificacion a la ruta equivalente de la app. */
const mapUrl = (url?: string): string | null => {
  if (!url) return null;
  if (url.startsWith('chat/')) {
    const id = url.split('/')[1];
    return `/app/chat?contacto_id=${id}`;
  }
  if (url.startsWith('candidato/postulaciones')) return '/app/solicitudes';
  if (url.startsWith('empresa/postulantes')) return '/app/mis-vacantes';
  if (url.startsWith('candidato/solicitudes')) return '/app/solicitudes';
  return null;
};

const Notificaciones: React.FC = () => {
  const router = useIonRouter();
  const [presentToast] = useCxToast();
  const [loading, setLoading] = useState(true);
  const [notifs, setNotifs] = useState<Notificacion[]>([]);
  const [unread, setUnread] = useState(0);
  const [marking, setMarking] = useState(false);

  const load = async () => {
    try {
      const res = await notificacionesApi.resumen();
      setNotifs(res?.notificaciones || []);
      setUnread(res?.unread_count ?? 0);
    } catch {
      setNotifs([]);
      setUnread(0);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { load(); }, []);

  const doRefresh = async (e: CustomEvent<RefresherEventDetail>) => {
    await load();
    e.detail.complete();
  };

  const marcarLeidas = async () => {
    if (marking || unread === 0) return;
    setMarking(true);
    try {
      await notificacionesApi.marcarLeidas();
      setUnread(0);
      setNotifs(prev => prev.map(n => ({ ...n, leida: 1 })));
    } catch {
      presentToast({ message: 'No se pudieron marcar como leídas', duration: 2500, color: 'danger' });
    } finally {
      setMarking(false);
    }
  };

  const abrir = (n: Notificacion) => {
    const destino = mapUrl(n.url);
    if (destino) {
      router.push(destino);
    }
  };

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar>
          <IonButton slot="start" fill="clear" onClick={() => router.push('/app/dashboard')} aria-label="Volver">
            <IonIcon icon={arrowBackOutline} />
          </IonButton>
          <IonTitle>Notificaciones</IonTitle>
          {unread > 0 && (
            <IonButton slot="end" fill="clear" onClick={marcarLeidas} disabled={marking} aria-label="Marcar todas como leídas">
              {marking ? <IonSpinner name="crescent" style={{ width: 18, height: 18 }} /> : <IonIcon icon={checkmarkDoneOutline} />}
            </IonButton>
          )}
        </IonToolbar>
      </IonHeader>

      <IonContent className="ion-padding">
        <IonRefresher slot="fixed" onIonRefresh={doRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        {loading ? (
          <div style={{ display: 'flex', justifyContent: 'center', paddingTop: 80 }}>
            <IonSpinner name="crescent" />
          </div>
        ) : notifs.length === 0 ? (
          <div style={{ textAlign: 'center', paddingTop: 80, color: 'var(--cx-muted, #6B7280)' }}>
            <IonIcon icon={notificationsOutline} style={{ fontSize: 44, opacity: 0.35 }} />
            <p style={{ marginTop: 12, fontSize: 14 }}>No tienes notificaciones todavía.</p>
          </div>
        ) : (
          <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
            {notifs.map(n => {
              const noLeida = Number(n.leida) === 0;
              const destino = mapUrl(n.url);
              const color = TIPO_COLOR[n.tipo] || '#1B2A4A';
              return (
                <button
                  key={n.id}
                  type="button"
                  onClick={() => abrir(n)}
                  disabled={!destino}
                  style={{
                    display: 'flex', gap: 12, alignItems: 'flex-start', textAlign: 'left',
                    width: '100%', padding: '14px 16px', borderRadius: 16,
                    border: '1px solid var(--cx-border, #E5E9F0)',
                    background: noLeida ? 'rgba(15,198,194,.07)' : 'var(--cx-surface, #fff)',
                    cursor: destino ? 'pointer' : 'default',
                  }}
                >
                  <span style={{
                    flexShrink: 0, width: 38, height: 38, borderRadius: 12,
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    background: `${color}1a`, color,
                  }}>
                    <IonIcon icon={TIPO_ICON[n.tipo] || notificationsOutline} style={{ fontSize: 19 }} />
                  </span>
                  <span style={{ flex: 1, minWidth: 0 }}>
                    <span style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
                      <span style={{
                        fontSize: 14, fontWeight: noLeida ? 700 : 600,
                        color: 'var(--cx-navy, #1B2A4A)', flex: 1,
                      }}>
                        {n.titulo}
                      </span>
                      {noLeida && (
                        <span style={{ width: 8, height: 8, borderRadius: '50%', background: '#0FC6C2', flexShrink: 0 }} />
                      )}
                    </span>
                    <span style={{ display: 'block', fontSize: 13, color: '#4B5563', marginTop: 2, lineHeight: 1.45 }}>
                      {n.mensaje}
                    </span>
                    <span style={{ display: 'block', fontSize: 12, color: '#9CA3AF', marginTop: 4 }}>
                      {n.created_at ? n.created_at.replace('T', ' ').slice(0, 16) : ''}
                    </span>
                  </span>
                  {destino && (
                    <IonIcon icon={chevronForward} style={{ color: '#9CA3AF', alignSelf: 'center', flexShrink: 0 }} />
                  )}
                </button>
              );
            })}
          </div>
        )}
      </IonContent>
    </IonPage>
  );
};

export default Notificaciones;
