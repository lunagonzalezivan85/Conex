import React, { useEffect, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonTitle,
  IonRefresher, IonRefresherContent, IonCard, IonCardContent,
  IonIcon, IonBadge, IonSpinner, IonButton, IonSegment,
  IonSegmentButton, IonLabel, RefresherEventDetail,
  useIonToast, useIonRouter,
} from '@ionic/react';
import {
  chatbubbleEllipsesOutline, callOutline, checkmarkCircleOutline,
  closeCircleOutline, briefcaseOutline, locationOutline, timeOutline,
  chevronForward, logoWhatsapp,
} from 'ionicons/icons';
import { useAuth } from '../context/AuthContext';
import { candidatoApi } from '../services/api';
import '../theme/app.css';

const Solicitudes: React.FC = () => {
  const { user } = useAuth();
  const router = useIonRouter();
  const [presentToast] = useIonToast();
  const [tab, setTab] = useState<'recibidas' | 'enviadas' | 'postulaciones'>('recibidas');
  const [loading, setLoading] = useState(true);
  const [recibidas, setRecibidas] = useState<any[]>([]);
  const [enviadas, setEnviadas] = useState<any[]>([]);
  const [postulaciones, setPostulaciones] = useState<any[]>([]);
  const [actingId, setActingId] = useState<number | null>(null);

  const load = async () => {
    try {
      const [solRes, postRes] = await Promise.all([
        candidatoApi.solicitudes().catch(() => null),
        user?.role === 'candidato' ? candidatoApi.postulaciones().catch(() => null) : Promise.resolve(null),
      ]);
      if (solRes?.data) {
        setRecibidas(solRes.data.recibidas || []);
        setEnviadas(solRes.data.enviadas || []);
      }
      if (postRes?.data) {
        setPostulaciones(postRes.data || []);
      }
    } catch {
      presentToast({ message: 'No se pudieron cargar tus mensajes', duration: 2500, color: 'danger' });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user?.id]);

  const onRefresh = async (e: CustomEvent<RefresherEventDetail>) => {
    await load();
    e.detail.complete();
  };

  const responder = async (id: number, accion: 'aceptar' | 'rechazar') => {
    setActingId(id);
    try {
      const res = await candidatoApi.responderSolicitud(id, accion);
      presentToast({ message: res.message, duration: 2500, color: accion === 'aceptar' ? 'success' : 'medium' });
      await load();
    } catch (err: any) {
      presentToast({ message: err?.error || 'Error al responder solicitud', duration: 2500, color: 'danger' });
    } finally {
      setActingId(null);
    }
  };

  const cleanPhone = (tel?: string | null) => {
    if (!tel) return '';
    const digits = tel.replace(/\D/g, '');
    return digits.length === 8 ? `505${digits}` : digits;
  };

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar>
          <IonTitle>Mensajes y Solicitudes</IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonRefresher slot="fixed" onIonRefresh={onRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        <div className="dash-wrap">
          <IonSegment
            value={tab}
            onIonChange={e => setTab(e.detail.value as any)}
            className="dash-segment"
          >
            <IonSegmentButton value="recibidas">
              <IonLabel>Recibidas ({recibidas.length})</IonLabel>
            </IonSegmentButton>
            <IonSegmentButton value="enviadas">
              <IonLabel>Enviadas ({enviadas.length})</IonLabel>
            </IonSegmentButton>
            {user?.role === 'candidato' && (
              <IonSegmentButton value="postulaciones">
                <IonLabel>Empleos ({postulaciones.length})</IonLabel>
              </IonSegmentButton>
            )}
          </IonSegment>

          {loading ? (
            <div className="dash-loading"><IonSpinner name="crescent" /></div>
          ) : tab === 'recibidas' ? (
            recibidas.length === 0 ? (
              <div className="empty-box">
                <IonIcon icon={chatbubbleEllipsesOutline} />
                <h4>Sin solicitudes recibidas</h4>
                <p>Cuando un cliente solicite tus servicios en el directorio, aparecerá aquí.</p>
              </div>
            ) : (
              <div className="sol-list">
                {recibidas.map(s => (
                  <IonCard key={s.id} className="sol-card">
                    <IonCardContent>
                      <div className="sol-top">
                        <div>
                          <div className="sol-name">{s.nombre}</div>
                          <div className="sol-oficio">Servicio: <strong>{s.oficio}</strong></div>
                        </div>
                        <IonBadge
                          color={s.estado === 'aceptado' ? 'success' : s.estado === 'rechazado' ? 'danger' : 'warning'}
                        >
                          {s.estado}
                        </IonBadge>
                      </div>

                      {s.mensaje && <p className="sol-msg">"{s.mensaje}"</p>}

                      <div className="sol-date">
                        <IonIcon icon={timeOutline} /> {s.created_at?.slice(0, 16)}
                      </div>

                      {s.estado === 'pendiente' && (
                        <div className="sol-actions">
                          <IonButton
                            size="small"
                            className="sol-btn-accept"
                            disabled={actingId === s.id}
                            onClick={() => responder(s.id, 'aceptar')}
                          >
                            <IonIcon icon={checkmarkCircleOutline} slot="start" />
                            Aceptar y ver teléfono
                          </IonButton>
                          <IonButton
                            size="small"
                            fill="outline"
                            color="medium"
                            disabled={actingId === s.id}
                            onClick={() => responder(s.id, 'rechazar')}
                          >
                            <IonIcon icon={closeCircleOutline} slot="start" />
                            Rechazar
                          </IonButton>
                        </div>
                      )}

                      {s.estado === 'aceptado' && s.telefono && (
                        <div className="sol-contact-box">
                          <span>Teléfono: <strong>{s.telefono}</strong></span>
                          <div className="sol-contact-btns">
                            <a href={`tel:${s.telefono}`} className="sol-chip-btn navy">
                              <IonIcon icon={callOutline} /> Llamar
                            </a>
                            <a
                              href={`https://wa.me/${cleanPhone(s.telefono)}`}
                              target="_blank"
                              rel="noreferrer"
                              className="sol-chip-btn whatsapp"
                            >
                              <IonIcon icon={logoWhatsapp} /> WhatsApp
                            </a>
                          </div>
                        </div>
                      )}
                    </IonCardContent>
                  </IonCard>
                ))}
              </div>
            )
          ) : tab === 'enviadas' ? (
            enviadas.length === 0 ? (
              <div className="empty-box">
                <IonIcon icon={chatbubbleEllipsesOutline} />
                <h4>No has enviado solicitudes</h4>
                <p>Busca trabajadores en la pestaña Contactos y envíales una solicitud.</p>
              </div>
            ) : (
              <div className="sol-list">
                {enviadas.map(s => (
                  <IonCard key={s.id} className="sol-card">
                    <IonCardContent>
                      <div className="sol-top">
                        <div>
                          <div className="sol-name">{s.oficio}</div>
                          <div className="sol-oficio">{s.prestador_nombre} · {s.zona || s.ciudad || 'Nicaragua'}</div>
                        </div>
                        <IonBadge
                          color={s.estado === 'aceptado' ? 'success' : s.estado === 'rechazado' ? 'danger' : 'warning'}
                        >
                          {s.estado}
                        </IonBadge>
                      </div>
                      {s.mensaje && <p className="sol-msg">"{s.mensaje}"</p>}
                      {s.estado === 'aceptado' && s.prestador_telefono && (
                        <div className="sol-contact-box">
                          <span>Contacto directo: <strong>{s.prestador_telefono}</strong></span>
                          <div className="sol-contact-btns">
                            <a href={`tel:${s.prestador_telefono}`} className="sol-chip-btn navy">
                              <IonIcon icon={callOutline} /> Llamar
                            </a>
                            <a
                              href={`https://wa.me/${cleanPhone(s.prestador_telefono)}`}
                              target="_blank"
                              rel="noreferrer"
                              className="sol-chip-btn whatsapp"
                            >
                              <IonIcon icon={logoWhatsapp} /> WhatsApp
                            </a>
                          </div>
                        </div>
                      )}
                    </IonCardContent>
                  </IonCard>
                ))}
              </div>
            )
          ) : (
            postulaciones.length === 0 ? (
              <div className="empty-box">
                <IonIcon icon={briefcaseOutline} />
                <h4>Aún no te has postulado</h4>
                <p>Explora las oportunidades laborales y postúlate en un toque.</p>
                <IonButton size="small" className="plan-cta" onClick={() => router.push('/app/buscar?modo=vacantes')}>
                  Ver vacantes
                </IonButton>
              </div>
            ) : (
              <div className="sol-list">
                {postulaciones.map(p => (
                  <IonCard key={p.id} className="job-card" routerLink={p.slug ? `/app/vacante/${p.slug}` : undefined}>
                    <IonCardContent>
                      <div className="job-row">
                        <div className="job-info">
                          <div className="job-title">{p.titulo}</div>
                          <div className="job-company">{p.empresa_nombre || 'Empresa'}</div>
                          <div className="job-meta">
                            <IonIcon icon={locationOutline} /> {p.ciudad || 'Nicaragua'} · {p.modalidad}
                          </div>
                        </div>
                        <IonBadge
                          color={
                            p.estado === 'aceptada' || p.estado === 'contratado'
                              ? 'success'
                              : p.estado === 'rechazada'
                              ? 'danger'
                              : 'primary'
                          }
                        >
                          {p.estado}
                        </IonBadge>
                        <IonIcon icon={chevronForward} className="job-chevron" />
                      </div>
                    </IonCardContent>
                  </IonCard>
                ))}
              </div>
            )
          )}
        </div>
      </IonContent>
    </IonPage>
  );
};

export default Solicitudes;

