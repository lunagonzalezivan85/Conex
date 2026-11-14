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
  chevronForward, logoWhatsapp, sendOutline, cashOutline,
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

  // Estado para responder / negociar
  const [replyingId, setReplyingId] = useState<number | null>(null);
  const [replyText, setReplyText] = useState('');
  const [replyPrice, setReplyPrice] = useState('');

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

  const responder = async (
    id: number,
    accion: 'aceptar' | 'rechazar' | 'negociar' | 'mensaje',
    msgOverride?: string,
    priceOverride?: string
  ) => {
    setActingId(id);
    try {
      const msg = msgOverride !== undefined ? msgOverride : replyText;
      const price = priceOverride !== undefined ? priceOverride : replyPrice;
      const res = await candidatoApi.responderSolicitud(id, {
        accion,
        mensaje: msg.trim() || undefined,
        precio_propuesto: price.trim() ? parseFloat(price) : undefined,
      });
      presentToast({ message: res.message, duration: 2500, color: accion === 'rechazar' ? 'medium' : 'success' });
      setReplyingId(null);
      setReplyText('');
      setReplyPrice('');
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

  const getInitials = (name?: string) => {
    if (!name) return 'CX';
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  };

  const formatPrice = (val?: number | string | null) => {
    if (val === null || val === undefined || val === '') return null;
    const num = Number(val);
    if (isNaN(num)) return null;
    return num.toLocaleString('es-NI', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  interface ThreadMessage {
    id: string | number;
    emisor_tipo: 'cliente' | 'prestador';
    emisor_nombre?: string;
    mensaje: string;
    precio_propuesto?: number | null;
    created_at?: string;
  }

  // Prepara y deduplica el historial de mensajes de la conversación
  const getThreadMessages = (s: any, rolContexto: 'recibidas' | 'enviadas'): ThreadMessage[] => {
    const raw: ThreadMessage[] = [];
    const initialText = (s.mensaje || '').trim();
    const initialPrice = s.precio_propuesto ? Number(s.precio_propuesto) : null;

    if (Array.isArray(s.mensajes) && s.mensajes.length > 0) {
      s.mensajes.forEach((m: any, idx: number) => {
        const msgText = (m.mensaje || '').trim();
        if (!msgText && !m.precio_propuesto) return;
        raw.push({
          id: m.id || `msg-${idx}`,
          emisor_tipo: m.emisor_tipo === 'prestador' ? 'prestador' : 'cliente',
          emisor_nombre: m.emisor_nombre || (m.emisor_tipo === 'cliente' ? (s.nombre || 'Cliente') : (s.prestador_nombre || 'Prestador')),
          mensaje: msgText,
          precio_propuesto: m.precio_propuesto ? Number(m.precio_propuesto) : null,
          created_at: m.created_at,
        });
      });

      // Si el texto inicial no está en el array devuelto por el backend, anteponerlo
      const yaEstaInicial = initialText && raw.some(
        m => m.emisor_tipo === 'cliente' && m.mensaje.toLowerCase() === initialText.toLowerCase()
      );
      if (!yaEstaInicial && initialText) {
        raw.unshift({
          id: `initial-${s.id}`,
          emisor_tipo: 'cliente',
          emisor_nombre: s.nombre || 'Cliente',
          mensaje: initialText,
          precio_propuesto: initialPrice,
          created_at: s.created_at,
        });
      }
    } else {
      if (initialText) {
        raw.push({
          id: `initial-${s.id}`,
          emisor_tipo: 'cliente',
          emisor_nombre: s.nombre || 'Cliente',
          mensaje: initialText,
          precio_propuesto: initialPrice,
          created_at: s.created_at,
        });
      }
      if (s.respuesta && s.respuesta.trim()) {
        raw.push({
          id: `resp-${s.id}`,
          emisor_tipo: 'prestador',
          emisor_nombre: s.prestador_nombre || 'Prestador',
          mensaje: s.respuesta.trim(),
          precio_propuesto: s.precio_propuesto ? Number(s.precio_propuesto) : null,
          created_at: s.fecha_respuesta || s.updated_at,
        });
      }
    }

    // Deduplicación defensiva estricta: omitir mensajes idénticos consecutivos del mismo emisor
    const deduped: ThreadMessage[] = [];
    raw.forEach((m) => {
      if (deduped.length === 0) {
        deduped.push(m);
      } else {
        const prev = deduped[deduped.length - 1];
        const sameSender = prev.emisor_tipo === m.emisor_tipo;
        const sameText = prev.mensaje.trim().toLowerCase() === m.mensaje.trim().toLowerCase();
        const samePrice = (prev.precio_propuesto || null) === (m.precio_propuesto || null);
        if (!(sameSender && sameText && samePrice)) {
          deduped.push(m);
        }
      }
    });

    return deduped;
  };

  // Componente de oferta y negociación dedicado (sin solapamiento visual)
  const renderDealCard = (
    precio: number | null | undefined,
    isMe: boolean,
    estado: string,
    tituloOverride?: string
  ) => {
    if (!precio) return null;
    return (
      <div className={`cx-deal-card ${isMe ? 'outgoing' : 'incoming'}`}>
        <div className="cx-deal-header">
          <div className="cx-deal-title-box">
            <span className="cx-deal-icon">💰</span>
            <span>{tituloOverride || (isMe ? 'Tu Contraoferta' : 'Cotización Propuesta')}</span>
          </div>
          <span className={`cx-deal-status ${estado}`}>
            {estado === 'aceptado' ? 'Aceptada' : estado === 'rechazado' ? 'Rechazada' : 'En Negociación'}
          </span>
        </div>
        <div className="cx-deal-amount-box">
          <span className="cx-deal-currency">C$</span>
          <span className="cx-deal-amount">{formatPrice(precio)}</span>
        </div>
        <div className="cx-deal-footer">
          {isMe
            ? 'Propuesta económica enviada en esta conversación.'
            : 'Monto presupuestado para acordar la realización del servicio.'}
        </div>
      </div>
    );
  };

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar>
          <IonTitle>Mensajes y Negociación</IonTitle>
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
            className="dash-segment msg-tabs-segment"
            scrollable={true}
            mode="md"
          >
            <IonSegmentButton value="recibidas">
              <IonLabel className="msg-tab-label">
                <span>Recibidas</span>
                <span className="msg-tab-count">{recibidas.length}</span>
              </IonLabel>
            </IonSegmentButton>
            <IonSegmentButton value="enviadas">
              <IonLabel className="msg-tab-label">
                <span>Enviadas</span>
                <span className="msg-tab-count">{enviadas.length}</span>
              </IonLabel>
            </IonSegmentButton>
            {user?.role === 'candidato' && (
              <IonSegmentButton value="postulaciones">
                <IonLabel className="msg-tab-label">
                  <span>Empleos</span>
                  <span className="msg-tab-count">{postulaciones.length}</span>
                </IonLabel>
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
                <p>Cuando un cliente solicite tus servicios en el directorio, aparecerá aquí en estilo chat.</p>
              </div>
            ) : (
              <div className="sol-list">
                {recibidas.map(s => {
                  const isReplying = replyingId === s.id;
                  const hasHistory = (Array.isArray(s.mensajes) && s.mensajes.length > 0) || !!s.respuesta;

                  return (
                    <IonCard key={s.id} className="sol-card msg-thread-card">
                      <IonCardContent>
                        {/* Encabezado Messenger con Avatar ARRIBA */}
                        <div className="msg-header">
                          <div className="msg-avatar-wrap">
                            <div className="msg-avatar client">
                              {getInitials(s.nombre || 'Cliente')}
                            </div>
                            <div className="msg-avatar-dot" />
                          </div>
                          <div className="msg-user-info">
                            <div className="msg-name">
                              {s.nombre || 'Cliente Interesado'}
                            </div>
                            <div className="msg-service-tag">
                              Servicio: {s.oficio}
                            </div>
                            <div className="msg-meta-row">
                              <IonIcon icon={timeOutline} />
                              <span>{s.created_at ? s.created_at.slice(0, 16) : 'Reciente'}</span>
                            </div>
                          </div>
                          <IonBadge
                            color={s.estado === 'aceptado' ? 'success' : s.estado === 'rechazado' ? 'danger' : 'warning'}
                            style={{ textTransform: 'capitalize', fontSize: '11px', padding: '4px 8px' }}
                          >
                            {s.estado}
                          </IonBadge>
                        </div>

                        {/* Conversación Messenger ABAJO (Sin Duplicados ni Solapamientos) */}
                        <div className="msg-chat-container">
                          {(() => {
                            const thread = getThreadMessages(s, 'recibidas');
                            if (thread.length === 0) {
                              return (
                                <div className="msg-bubble-row incoming">
                                  <span className="msg-bubble-author">{s.nombre || 'Cliente'}</span>
                                  <div className="msg-bubble incoming" style={{ fontStyle: 'italic', opacity: 0.85 }}>
                                    "Solicito contacto para coordinar trabajo de {s.oficio}."
                                  </div>
                                </div>
                              );
                            }
                            return thread.map((m) => {
                              const isMe = m.emisor_tipo === 'prestador';
                              return (
                                <div key={m.id} className={`msg-bubble-row ${isMe ? 'outgoing' : 'incoming'}`}>
                                  <span className="msg-bubble-author">
                                    {isMe ? 'Tú (Respuesta / Oferta)' : (m.emisor_nombre || s.nombre || 'Cliente')}
                                  </span>
                                  {m.mensaje && (
                                    <div className={`msg-bubble ${isMe ? 'outgoing' : 'incoming'}`}>
                                      "{m.mensaje}"
                                      {m.created_at && (
                                        <span className="msg-bubble-time">{m.created_at.slice(11, 16)}</span>
                                      )}
                                    </div>
                                  )}
                                  {renderDealCard(m.precio_propuesto, isMe, s.estado, isMe ? 'Tu Cotización' : 'Oferta del Cliente')}
                                </div>
                              );
                            });
                          })()}
                        </div>

                        {/* Panel de Respuesta y Negociación en la App */}
                        {isReplying ? (
                          <div className="msg-composer-box">
                            <div className="msg-composer-title">
                              <IonIcon icon={chatbubbleEllipsesOutline} /> Negociación en la App
                            </div>
                            <textarea
                              className="msg-composer-textarea"
                              placeholder="Escribe tu mensaje, disponibilidad o acuerdo de trabajo..."
                              value={replyText}
                              onChange={e => setReplyText(e.target.value)}
                              rows={2}
                            />
                            <div className="msg-composer-price-row">
                              <IonIcon icon={cashOutline} style={{ fontSize: '20px', color: '#F97316' }} />
                              <input
                                type="number"
                                className="msg-price-input"
                                placeholder="Cotización / Precio propuesto en C$ (Opcional)"
                                value={replyPrice}
                                onChange={e => setReplyPrice(e.target.value)}
                              />
                            </div>
                            <div className="msg-composer-actions">
                              <IonButton
                                size="small"
                                className="msg-send-btn"
                                disabled={actingId === s.id || (!replyText.trim() && !replyPrice.trim())}
                                onClick={() => responder(s.id, 'negociar')}
                              >
                                <IonIcon icon={sendOutline} slot="start" />
                                Enviar respuesta
                              </IonButton>
                              {s.estado === 'pendiente' && (
                                <IonButton
                                  size="small"
                                  className="msg-accept-btn"
                                  disabled={actingId === s.id}
                                  onClick={() => responder(s.id, 'aceptar')}
                                >
                                  <IonIcon icon={checkmarkCircleOutline} slot="start" />
                                  Aceptar trabajo
                                </IonButton>
                              )}
                              <IonButton
                                size="small"
                                fill="outline"
                                color="medium"
                                onClick={() => {
                                  setReplyingId(null);
                                  setReplyText('');
                                  setReplyPrice('');
                                }}
                              >
                                Cancelar
                              </IonButton>
                            </div>
                          </div>
                        ) : (
                          <div className="sol-actions" style={{ flexWrap: 'wrap', marginTop: '12px', width: '100%', gap: '8px' }}>
                            <IonButton
                              expand="block"
                              fill="solid"
                              className="cx-cta-chat-btn"
                              onClick={() => {
                                router.push(`/app/chat?contacto_id=${s.id}&nombre=${encodeURIComponent(s.nombre || 'Cliente')}&oficio=${encodeURIComponent(s.oficio)}`);
                              }}
                            >
                              <IonIcon icon={chatbubbleEllipsesOutline} slot="start" />
                              Abrir Chat tipo Messenger
                            </IonButton>

                            {s.estado === 'pendiente' && (
                              <div style={{ display: 'flex', gap: '8px', width: '100%', marginTop: '4px' }}>
                                <IonButton
                                  size="small"
                                  className="sol-btn-accept"
                                  style={{ flex: 1 }}
                                  disabled={actingId === s.id}
                                  onClick={() => responder(s.id, 'aceptar')}
                                >
                                  <IonIcon icon={checkmarkCircleOutline} slot="start" />
                                  Aceptar directo
                                </IonButton>
                                <IonButton
                                  size="small"
                                  fill="outline"
                                  color="danger"
                                  style={{ flex: 1 }}
                                  disabled={actingId === s.id}
                                  onClick={() => responder(s.id, 'rechazar')}
                                >
                                  <IonIcon icon={closeCircleOutline} slot="start" />
                                  Rechazar
                                </IonButton>
                              </div>
                            )}
                          </div>
                        )}

                        {/* Contacto Directo Telefónico / WhatsApp al ser aceptado */}
                        {s.estado === 'aceptado' && s.telefono && (
                          <div className="sol-contact-box">
                            <span style={{ fontWeight: 600 }}>Contacto acordado: <strong>{s.telefono}</strong></span>
                            <div className="sol-contact-btns">
                              <a href={`tel:${s.telefono}`} className="sol-chip-btn navy">
                                <IonIcon icon={callOutline} /> Llamar
                              </a>
                              <a
                                href={`https://wa.me/${cleanPhone(s.telefono)}?text=${encodeURIComponent(
                                  `¡Hola ${s.nombre}! Te escribo sobre el servicio de ${s.oficio} en CONEX.` +
                                    (s.precio_propuesto ? ` Mi cotización acordada es de C$ ${formatPrice(s.precio_propuesto)}.` : '')
                                )}`}
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
                  );
                })}
              </div>
            )
          ) : tab === 'enviadas' ? (
            enviadas.length === 0 ? (
              <div className="empty-box">
                <IonIcon icon={chatbubbleEllipsesOutline} />
                <h4>No has enviado solicitudes</h4>
                <p>Busca trabajadores en la pestaña Contactos y envíales una solicitud de servicio.</p>
              </div>
            ) : (
              <div className="sol-list">
                {enviadas.map(s => {
                  const isReplying = replyingId === s.id;
                  const hasHistory = (Array.isArray(s.mensajes) && s.mensajes.length > 0) || !!s.respuesta;

                  return (
                    <IonCard key={s.id} className="sol-card msg-thread-card">
                      <IonCardContent>
                        {/* Encabezado Messenger con Avatar ARRIBA */}
                        <div className="msg-header">
                          <div className="msg-avatar-wrap">
                            <div className="msg-avatar">
                              {getInitials(s.prestador_nombre || s.oficio)}
                            </div>
                            <div className="msg-avatar-dot" />
                          </div>
                          <div className="msg-user-info">
                            <div className="msg-name">
                              {s.prestador_nombre || 'Prestador del Servicio'}
                            </div>
                            <div className="msg-service-tag">
                              {s.oficio} · {s.zona || s.ciudad || 'Nicaragua'}
                            </div>
                            <div className="msg-meta-row">
                              <IonIcon icon={timeOutline} />
                              <span>{s.created_at ? s.created_at.slice(0, 16) : 'Reciente'}</span>
                            </div>
                          </div>
                          <IonBadge
                            color={s.estado === 'aceptado' ? 'success' : s.estado === 'rechazado' ? 'danger' : 'warning'}
                            style={{ textTransform: 'capitalize', fontSize: '11px', padding: '4px 8px' }}
                          >
                            {s.estado}
                          </IonBadge>
                        </div>

                        {/* Conversación Messenger ABAJO (Sin Duplicados ni Solapamientos) */}
                        <div className="msg-chat-container">
                          {(() => {
                            const thread = getThreadMessages(s, 'enviadas');
                            if (thread.length === 0) {
                              return (
                                <p style={{ fontStyle: 'italic', fontSize: '12.5px', color: 'var(--cx-muted)', margin: '6px 0' }}>
                                  Esperando respuesta del prestador...
                                </p>
                              );
                            }
                            return thread.map((m) => {
                              const isMe = m.emisor_tipo === 'cliente';
                              return (
                                <div key={m.id} className={`msg-bubble-row ${isMe ? 'outgoing' : 'incoming'}`}>
                                  <span className="msg-bubble-author">
                                    {isMe ? 'Tú (Tu solicitud)' : (m.emisor_nombre || s.prestador_nombre || 'Prestador')}
                                  </span>
                                  {m.mensaje && (
                                    <div className={`msg-bubble ${isMe ? 'outgoing' : 'incoming'}`}>
                                      "{m.mensaje}"
                                      {m.created_at && (
                                        <span className="msg-bubble-time">{m.created_at.slice(11, 16)}</span>
                                      )}
                                    </div>
                                  )}
                                  {renderDealCard(m.precio_propuesto, isMe, s.estado, isMe ? 'Tu Contraoferta' : 'Cotización del Prestador')}
                                </div>
                              );
                            });
                          })()}
                        </div>

                        {/* Responder / Contraofertar desde el cliente */}
                        {isReplying ? (
                          <div className="msg-composer-box">
                            <div className="msg-composer-title">
                              <IonIcon icon={chatbubbleEllipsesOutline} /> Responder en la Negociación
                            </div>
                            <textarea
                              className="msg-composer-textarea"
                              placeholder="Escribe tu respuesta o contraoferta al prestador..."
                              value={replyText}
                              onChange={e => setReplyText(e.target.value)}
                              rows={2}
                            />
                            <div className="msg-composer-price-row">
                              <IonIcon icon={cashOutline} style={{ fontSize: '20px', color: '#F97316' }} />
                              <input
                                type="number"
                                className="msg-price-input"
                                placeholder="Contraoferta C$ (Opcional)"
                                value={replyPrice}
                                onChange={e => setReplyPrice(e.target.value)}
                              />
                            </div>
                            <div className="msg-composer-actions">
                              <IonButton
                                size="small"
                                className="msg-send-btn"
                                disabled={actingId === s.id || (!replyText.trim() && !replyPrice.trim())}
                                onClick={() => responder(s.id, 'mensaje')}
                              >
                                <IonIcon icon={sendOutline} slot="start" />
                                Enviar mensaje
                              </IonButton>
                              <IonButton
                                size="small"
                                fill="outline"
                                color="medium"
                                onClick={() => {
                                  setReplyingId(null);
                                  setReplyText('');
                                  setReplyPrice('');
                                }}
                              >
                                Cancelar
                              </IonButton>
                            </div>
                          </div>
                        ) : (
                          <div className="sol-actions" style={{ flexWrap: 'wrap', marginTop: '12px', width: '100%' }}>
                            <IonButton
                              expand="block"
                              fill="solid"
                              className="cx-cta-chat-btn"
                              onClick={() => {
                                router.push(`/app/chat?contacto_id=${s.id}&nombre=${encodeURIComponent(s.prestador_nombre || 'Prestador')}&oficio=${encodeURIComponent(s.oficio || '')}`);
                              }}
                            >
                              <IonIcon icon={chatbubbleEllipsesOutline} slot="start" />
                              Abrir Chat tipo Messenger
                            </IonButton>
                          </div>
                        )}

                        {s.estado === 'aceptado' && s.prestador_telefono && (
                          <div className="sol-contact-box">
                            <span style={{ fontWeight: 600 }}>Contacto directo: <strong>{s.prestador_telefono}</strong></span>
                            <div className="sol-contact-btns">
                              <a href={`tel:${s.prestador_telefono}`} className="sol-chip-btn navy">
                                <IonIcon icon={callOutline} /> Llamar
                              </a>
                              <a
                                href={`https://wa.me/${cleanPhone(s.prestador_telefono)}?text=${encodeURIComponent(
                                  `¡Hola! Te contacto desde la app CONEX sobre tu servicio de ${s.oficio}.`
                                )}`}
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
                  );
                })}
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
