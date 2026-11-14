import React, { useEffect, useState, useRef } from 'react';
import { useLocation } from 'react-router-dom';
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonContent,
  IonFooter,
  IonIcon,
  IonSpinner,
  useIonToast,
  useIonRouter,
} from '@ionic/react';
import {
  arrowBackOutline,
  send,
  checkmarkCircle,
  locationOutline,
  cashOutline,
} from 'ionicons/icons';
import { Geolocation } from '@capacitor/geolocation';
import { useAuth } from '../context/AuthContext';
import { candidatoApi, serviciosApi } from '../services/api';
import '../theme/app.css';

interface MessageItem {
  id: string | number;
  emisor_tipo: 'cliente' | 'prestador';
  emisor_nombre?: string;
  mensaje: string;
  precio_propuesto?: number | null;
  created_at?: string;
}

export const ChatMessenger: React.FC = () => {
  const { user } = useAuth();
  const router = useIonRouter();
  const location = useLocation();
  const [presentToast] = useIonToast();

  const queryParams = new URLSearchParams(location.search);
  const paramContactoId = queryParams.get('contacto_id');
  const paramServicioId = queryParams.get('servicio_id');
  const paramNombre = queryParams.get('nombre') || 'Contacto';
  const paramOficio = queryParams.get('oficio') || 'Servicio';
  const paramAvatar = queryParams.get('avatar') || '';
  const paramPrecio = queryParams.get('precio') || '';

  const [contactoId, setContactoId] = useState<number | null>(
    paramContactoId ? parseInt(paramContactoId, 10) : null
  );
  const [loading, setLoading] = useState(true);
  const [contactoData, setContactoData] = useState<any>(null);
  const [messages, setMessages] = useState<MessageItem[]>([]);
  const [inputText, setInputText] = useState('');
  const [sending, setSending] = useState(false);

  const messagesEndRef = useRef<HTMLDivElement>(null);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  const loadConversation = async () => {
    try {
      if (contactoId) {
        const res = await candidatoApi.solicitudDetalle(contactoId).catch(() => null);
        if (res?.data) {
          setContactoData(res.data);
          const rawMsgs: MessageItem[] = [];

          if (Array.isArray(res.data.mensajes) && res.data.mensajes.length > 0) {
            res.data.mensajes.forEach((m: any, idx: number) => {
              rawMsgs.push({
                id: m.id || `m-${idx}`,
                emisor_tipo: m.emisor_tipo === 'prestador' ? 'prestador' : 'cliente',
                emisor_nombre: m.emisor_nombre,
                mensaje: m.mensaje,
                precio_propuesto: m.precio_propuesto ? Number(m.precio_propuesto) : null,
                created_at: m.created_at,
              });
            });

            // Verificar si el mensaje original inicial no está en la lista
            const initialText = (res.data.mensaje || '').trim();
            if (
              initialText &&
              !rawMsgs.some(
                (m) =>
                  m.emisor_tipo === 'cliente' &&
                  m.mensaje.toLowerCase() === initialText.toLowerCase()
              )
            ) {
              rawMsgs.unshift({
                id: `init-${res.data.id}`,
                emisor_tipo: 'cliente',
                emisor_nombre: res.data.nombre || 'Cliente',
                mensaje: initialText,
                precio_propuesto: res.data.precio_propuesto ? Number(res.data.precio_propuesto) : null,
                created_at: res.data.created_at,
              });
            }
          } else {
            // Conversación legacy
            if (res.data.mensaje) {
              rawMsgs.push({
                id: `init-${res.data.id}`,
                emisor_tipo: 'cliente',
                emisor_nombre: res.data.nombre || 'Cliente',
                mensaje: res.data.mensaje,
                precio_propuesto: res.data.precio_propuesto ? Number(res.data.precio_propuesto) : null,
                created_at: res.data.created_at,
              });
            }
            if (res.data.respuesta) {
              rawMsgs.push({
                id: `resp-${res.data.id}`,
                emisor_tipo: 'prestador',
                emisor_nombre: res.data.prestador_nombre || 'Prestador',
                mensaje: res.data.respuesta,
                precio_propuesto: res.data.precio_propuesto ? Number(res.data.precio_propuesto) : null,
                created_at: res.data.fecha_respuesta || res.data.updated_at,
              });
            }
          }

          // Deduplicación defensiva
          const deduped: MessageItem[] = [];
          rawMsgs.forEach((m) => {
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

          setMessages(deduped);
        }
      } else {
        // Conversación nueva iniciada desde el directorio
        setMessages([
          {
            id: 'welcome',
            emisor_tipo: 'prestador',
            emisor_nombre: paramNombre,
            mensaje: `¡Hola! 👋 Trabajo en servicios de ${paramOficio}. Cuéntame qué necesitas para coordinar el trabajo o brindarte un presupuesto.`,
            precio_propuesto: paramPrecio ? parseFloat(paramPrecio) : null,
            created_at: new Date().toISOString(),
          },
        ]);
      }
    } catch (e) {
      // eslint-disable-next-line no-console
      console.warn('Error cargando chat:', e);
    } finally {
      setLoading(false);
      setTimeout(scrollToBottom, 200);
    }
  };

  useEffect(() => {
    loadConversation();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [contactoId, paramServicioId]);

  // Compartir ubicación actual vía GPS
  const compartirUbicacionActual = async () => {
    try {
      presentToast({ message: 'Obteniendo tu ubicación actual...', duration: 1500 });
      const pos = await Geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 10000 });
      const lat = pos.coords.latitude.toFixed(6);
      const lng = pos.coords.longitude.toFixed(6);
      const mapsUrl = `https://maps.google.com/?q=${lat},${lng}`;
      const locText = `📍 Mi ubicación actual: ${mapsUrl}`;
      setInputText(locText);
      presentToast({ message: 'Ubicación agregada al mensaje', duration: 1800, color: 'success' });
    } catch (err) {
      presentToast({
        message: 'No se pudo obtener la ubicación GPS. Verifica que el GPS y los permisos estén activados.',
        duration: 2500,
        color: 'warning',
      });
    }
  };

  // Enviar mensaje
  const handleSendMessage = async (e?: React.FormEvent) => {
    if (e) e.preventDefault();
    const cleanMsg = inputText.trim();

    if (!cleanMsg) {
      return;
    }

    setSending(true);
    try {
      if (contactoId) {
        const res = await candidatoApi.responderSolicitud(contactoId, {
          accion: 'mensaje',
          mensaje: cleanMsg,
        });

        presentToast({
          message: res.message || 'Mensaje enviado',
          duration: 1800,
          color: 'success',
        });

        setInputText('');
        await loadConversation();
      } else if (paramServicioId) {
        const defaultName = `${user?.nombre ?? ''} ${user?.apellido ?? ''}`.trim() || 'Cliente';
        const defaultTel = user?.telefono || '8888-8888';

        const res = await serviciosApi.contactar(parseInt(paramServicioId, 10), {
          nombre: defaultName,
          telefono: defaultTel,
          mensaje: cleanMsg,
        });

        presentToast({
          message: res.message || 'Mensaje enviado con éxito',
          duration: 2200,
          color: 'success',
        });

        if (res.contacto_id) {
          setContactoId(res.contacto_id);
        }

        setInputText('');
        await loadConversation();
      }
    } catch (err: any) {
      presentToast({
        message: err?.error || 'No se pudo enviar el mensaje',
        duration: 2600,
        color: 'danger',
      });
    } finally {
      setSending(false);
      setTimeout(scrollToBottom, 150);
    }
  };

  const getTargetName = () => {
    if (contactoData) {
      return contactoData.es_cliente
        ? contactoData.prestador_nombre || paramNombre
        : contactoData.nombre || 'Cliente';
    }
    return paramNombre;
  };

  const getTargetOficio = () => {
    if (contactoData) {
      return contactoData.oficio || paramOficio;
    }
    return paramOficio;
  };

  const formatPrice = (val?: number | string | null) => {
    if (val === null || val === undefined || val === '') return null;
    const num = Number(val);
    if (isNaN(num)) return null;
    return num.toLocaleString('es-NI', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  const initials = (name?: string) => {
    if (!name) return 'CX';
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  };

  const targetName = getTargetName();
  const targetOficio = getTargetOficio();

  return (
    <IonPage className="cx-messenger-screen">
      {/* 1. Header Azul Marino sólido (sin iconos de llamar, videollamada ni info) */}
      <IonHeader className="cx-msgr-header">
        <IonToolbar className="cx-msgr-toolbar">
          <div className="cx-msgr-nav-row">
            {/* Botón Volver ← */}
            <button
              type="button"
              className="cx-msgr-back-btn"
              onClick={() => router.goBack()}
              aria-label="Volver"
            >
              <IonIcon icon={arrowBackOutline} />
            </button>

            {/* Avatar circular con foto o iniciales */}
            <div className="cx-msgr-avatar-wrap">
              {paramAvatar ? (
                <img src={paramAvatar} alt={targetName} className="cx-msgr-avatar-img" />
              ) : (
                <div className="cx-msgr-avatar-fallback">{initials(targetName)}</div>
              )}
              <span className="cx-msgr-online-dot" />
            </div>

            {/* Nombre del contacto (sin subtítulo de servicio) */}
            <div className="cx-msgr-user-info">
              <span className="cx-msgr-name">{targetName}</span>
            </div>
          </div>
        </IonToolbar>
      </IonHeader>

      {/* 2. Cuerpo de Conversación */}
      <IonContent className="cx-msgr-content" scrollEvents={true}>
        {loading ? (
          <div className="cx-msgr-loading">
            <IonSpinner name="crescent" color="primary" />
          </div>
        ) : (
          <div className="cx-msgr-flow">
            {/* Servicio centrado en la parte superior del contenedor del chat */}
            <div className="cx-msgr-service-pill-center">
              <span className="cx-msgr-service-pill-label">SERVICIO</span>
              <strong className="cx-msgr-service-pill-title">{targetOficio}</strong>
            </div>

            {/* Fecha centrada tipo Messenger */}
            <div className="cx-msgr-date-divider">
              <span>HOY A LAS {new Date().toLocaleTimeString('es-NI', { hour: '2-digit', minute: '2-digit' })}</span>
            </div>

            {messages.map((m) => {
              const isMe = m.emisor_tipo === 'cliente';

              return (
                <React.Fragment key={m.id}>
                  {/* Fila de Burbuja de Mensaje (Azul Marino para salientes) */}
                  {m.mensaje && (
                    <div className={`cx-msgr-bubble-row ${isMe ? 'outgoing' : 'incoming'}`}>
                      {!isMe && (
                        <div className="cx-msgr-bubble-avatar">
                          {paramAvatar ? (
                            <img src={paramAvatar} alt={targetName} />
                          ) : (
                            <span>{initials(targetName)}</span>
                          )}
                        </div>
                      )}

                      <div className={`cx-msgr-bubble ${isMe ? 'outgoing' : 'incoming'}`}>
                        {m.mensaje}
                      </div>

                      {isMe && (
                        <div className="cx-msgr-check-icon" title="Entregado">
                          <IonIcon icon={checkmarkCircle} />
                        </div>
                      )}
                    </div>
                  )}

                  {/* Tarjeta de Negociación si tiene precio propuesto */}
                  {m.precio_propuesto && (
                    <div className="cx-msgr-plan-wrap">
                      <div className="cx-msgr-plan-card">
                        <div className="cx-msgr-plan-header">
                          <span className="cx-msgr-plan-badge-dot">💰</span>
                          <span className="cx-msgr-plan-title">
                            {isMe ? 'Propuesta económica del cliente' : 'Cotización del prestador'}: C$ {formatPrice(m.precio_propuesto)}
                          </span>
                        </div>

                        <p className="cx-msgr-plan-desc">
                          {isMe
                            ? `Tú propusiste un presupuesto de C$ ${formatPrice(m.precio_propuesto)} para coordinar el trabajo de ${targetOficio}.`
                            : `${targetName} te ha cotizado C$ ${formatPrice(m.precio_propuesto)} por la mano de obra del servicio.`}
                        </p>
                      </div>

                      <div className="cx-msgr-plan-sub-text">
                        <span>{isMe ? 'Propuesta económica registrada.' : `${targetName} ha enviado una cotización.`}</span>
                        <IonIcon icon={checkmarkCircle} className="cx-msgr-plan-check" />
                      </div>
                    </div>
                  )}
                </React.Fragment>
              );
            })}

            <div ref={messagesEndRef} />
          </div>
        )}
      </IonContent>

      {/* 3. Footer / Input Bar sin choque con la navegación */}
      <IonFooter className="cx-msgr-footer">
        <form onSubmit={handleSendMessage} className="cx-msgr-input-bar">
          {/* Opción de agregar: Compartir ubicación actual */}
          <button
            type="button"
            className="cx-msgr-loc-btn"
            title="Compartir ubicación actual"
            onClick={compartirUbicacionActual}
          >
            <IonIcon icon={locationOutline} />
          </button>

          {/* Opción de escribir el mensaje */}
          <div className="cx-msgr-pill-input">
            <input
              type="text"
              placeholder="Escribe un mensaje..."
              value={inputText}
              onChange={(e) => setInputText(e.target.value)}
              disabled={sending}
            />
          </div>

          {/* Botón de Enviar */}
          <button
            type="submit"
            className="cx-msgr-send-btn"
            disabled={sending || !inputText.trim()}
            title="Enviar mensaje"
          >
            <IonIcon icon={send} />
          </button>
        </form>
      </IonFooter>
    </IonPage>
  );
};

export default ChatMessenger;
