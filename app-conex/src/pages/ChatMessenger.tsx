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
  IonModal,
  useIonToast,
  useIonRouter,
} from '@ionic/react';
import {
  arrowBackOutline,
  call,
  videocam,
  informationCircleOutline,
  addCircle,
  cameraOutline,
  imagesOutline,
  micOutline,
  happyOutline,
  send,
  thumbsUp,
  cashOutline,
  calendarOutline,
  checkmarkCircle,
  closeOutline,
  shieldCheckmarkOutline,
  logoWhatsapp,
  checkmark,
  locationOutline,
  personOutline,
  callOutline,
} from 'ionicons/icons';
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
  const paramTelefono = queryParams.get('telefono') || '';
  const paramPrecio = queryParams.get('precio') || '';

  const [contactoId, setContactoId] = useState<number | null>(
    paramContactoId ? parseInt(paramContactoId, 10) : null
  );
  const [loading, setLoading] = useState(true);
  const [contactoData, setContactoData] = useState<any>(null);
  const [messages, setMessages] = useState<MessageItem[]>([]);
  const [inputText, setInputText] = useState('');
  const [inputPrice, setInputPrice] = useState('');
  const [showPriceBox, setShowPriceBox] = useState(false);
  const [showInfoModal, setShowInfoModal] = useState(false);
  const [sending, setSending] = useState(false);

  // Datos para clientes no registrados o primer contacto
  const [clientNombre, setClientNombre] = useState(
    `${user?.nombre ?? ''} ${user?.apellido ?? ''}`.trim()
  );
  const [clientTelefono, setClientTelefono] = useState(user?.telefono ?? '');

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
        // Conversación nueva por iniciar desde Buscar / Dashboard
        setMessages([
          {
            id: 'welcome',
            emisor_tipo: 'prestador',
            emisor_nombre: paramNombre,
            mensaje: `¡Hola! 👋 Trabajo en servicios de ${paramOficio}. Cuéntame qué trabajo necesitas para coordinar o darte un presupuesto.`,
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

  // Enviar mensaje o contraoferta
  const handleSendMessage = async (e?: React.FormEvent) => {
    if (e) e.preventDefault();
    const cleanMsg = inputText.trim();
    const cleanPrice = inputPrice.trim() ? parseFloat(inputPrice) : undefined;

    if (!cleanMsg && cleanPrice === undefined) {
      return;
    }

    setSending(true);
    try {
      if (contactoId) {
        // Responder en solicitud existente
        const res = await candidatoApi.responderSolicitud(contactoId, {
          accion: 'mensaje',
          mensaje: cleanMsg || undefined,
          precio_propuesto: cleanPrice,
        });

        presentToast({
          message: res.message || 'Mensaje enviado',
          duration: 2000,
          color: 'success',
        });

        setInputText('');
        setInputPrice('');
        setShowPriceBox(false);
        await loadConversation();
      } else if (paramServicioId) {
        // Primer mensaje de contacto
        if (clientNombre.trim().length < 2 || clientTelefono.trim().length < 7) {
          presentToast({
            message: 'Por favor completa tu nombre y teléfono para enviar la solicitud',
            duration: 2400,
            color: 'warning',
          });
          setShowInfoModal(true);
          setSending(false);
          return;
        }

        const res = await serviciosApi.contactar(parseInt(paramServicioId, 10), {
          nombre: clientNombre.trim(),
          telefono: clientTelefono.trim(),
          mensaje: cleanMsg || 'Solicito cotización de servicio',
          precio_propuesto: cleanPrice,
        });

        presentToast({
          message: res.message || '¡Solicitud y mensaje enviados con éxito!',
          duration: 2600,
          color: 'success',
        });

        if (res.contacto_id) {
          setContactoId(res.contacto_id);
        }

        setInputText('');
        setInputPrice('');
        setShowPriceBox(false);
        await loadConversation();
      }
    } catch (err: any) {
      presentToast({
        message: err?.error || 'No se pudo enviar el mensaje',
        duration: 2800,
        color: 'danger',
      });
    } finally {
      setSending(false);
      setTimeout(scrollToBottom, 150);
    }
  };

  const enviarMeGusta = () => {
    setInputText('👍');
    setTimeout(() => {
      handleSendMessage();
    }, 50);
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

  const getTargetPhone = () => {
    if (contactoData) {
      return contactoData.es_cliente
        ? contactoData.prestador_telefono
        : contactoData.cliente_telefono_revelado;
    }
    return paramTelefono;
  };

  const cleanPhone = (tel?: string | null) => {
    if (!tel) return '';
    const digits = tel.replace(/\D/g, '');
    return digits.length === 8 ? `505${digits}` : digits;
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
  const phoneDirect = getTargetPhone();

  return (
    <IonPage className="cx-messenger-screen">
      {/* 1. Header Idéntico a Facebook Messenger */}
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

            {/* Título y estado */}
            <div className="cx-msgr-user-info" onClick={() => setShowInfoModal(true)}>
              <span className="cx-msgr-name">{targetName}</span>
              <span className="cx-msgr-sub">{targetOficio} · En línea</span>
            </div>

            {/* Acciones del Header: 📞 📹 ℹ️ (apuntado con la flecha roja en la imagen) */}
            <div className="cx-msgr-head-actions">
              {phoneDirect ? (
                <a href={`tel:${phoneDirect}`} className="cx-msgr-head-icon" title="Llamar">
                  <IonIcon icon={call} />
                </a>
              ) : (
                <button
                  type="button"
                  className="cx-msgr-head-icon"
                  title="Llamada telefónica disponible al aceptar la solicitud"
                  onClick={() =>
                    presentToast({
                      message: 'El número telefónico se habilita al aceptar la negociación.',
                      duration: 2500,
                      color: 'primary',
                    })
                  }
                >
                  <IonIcon icon={call} />
                </button>
              )}

              {phoneDirect ? (
                <a
                  href={`https://wa.me/${cleanPhone(phoneDirect)}?text=${encodeURIComponent(
                    `¡Hola ${targetName}! Te contacto desde CONEX sobre el trabajo de ${targetOficio}.`
                  )}`}
                  target="_blank"
                  rel="noreferrer"
                  className="cx-msgr-head-icon"
                  title="WhatsApp / Video"
                >
                  <IonIcon icon={videocam} />
                </a>
              ) : (
                <button
                  type="button"
                  className="cx-msgr-head-icon"
                  title="WhatsApp"
                  onClick={() =>
                    presentToast({
                      message: 'WhatsApp disponible cuando se acepte el servicio.',
                      duration: 2500,
                      color: 'primary',
                    })
                  }
                >
                  <IonIcon icon={videocam} />
                </button>
              )}

              {/* Botón de Información con círculo ℹ️ (Flecha Roja en captura) */}
              <button
                type="button"
                className="cx-msgr-head-icon info"
                title="Información y detalles del trato"
                onClick={() => setShowInfoModal(true)}
              >
                <IonIcon icon={informationCircleOutline} />
              </button>
            </div>
          </div>

          {/* Sub-barra de evento / cita / plan acordado */}
          <div className="cx-msgr-event-banner">
            <div className="cx-msgr-banner-left">
              <IonIcon icon={calendarOutline} className="cx-msgr-banner-icon" />
              <span>Servicio de {targetOficio} · Negociación activa</span>
            </div>
            <span className="cx-msgr-banner-right">CONEX</span>
          </div>
        </IonToolbar>
      </IonHeader>

      {/* 2. Cuerpo de Conversación idéntico a Messenger */}
      <IonContent className="cx-msgr-content" scrollEvents={true}>
        {loading ? (
          <div className="cx-msgr-loading">
            <IonSpinner name="crescent" color="primary" />
          </div>
        ) : (
          <div className="cx-msgr-flow">
            {/* Fecha centrada tipo Messenger */}
            <div className="cx-msgr-date-divider">
              <span>HOY A LAS {new Date().toLocaleTimeString('es-NI', { hour: '2-digit', minute: '2-digit' })}</span>
            </div>

            {messages.map((m) => {
              const isMe = m.emisor_tipo === 'cliente';

              return (
                <React.Fragment key={m.id}>
                  {/* Fila de Burbuja de Mensaje */}
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

                  {/* 3. Tarjeta de Negociación / Acuerdo Integrada en el Chat (Idéntica a la tarjeta de la foto) */}
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

                        <button
                          type="button"
                          className="cx-msgr-plan-btn"
                          onClick={() => {
                            setShowPriceBox(true);
                            setInputPrice(String(m.precio_propuesto));
                          }}
                        >
                          MODIFICAR O RENOVAR COTIZACIÓN
                        </button>
                      </div>

                      <div className="cx-msgr-plan-sub-text">
                        <span>{isMe ? 'Has propuesto una oferta económica.' : `${targetName} ha enviado una cotización.`}</span>
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

      {/* 4. Barra Inferior de Entrada (Messenger Input Bar) */}
      <IonFooter className="cx-msgr-footer">
        {/* Desplegable para Contraoferta / Cotización C$ */}
        {showPriceBox && (
          <div className="cx-msgr-price-drawer">
            <div className="cx-msgr-drawer-head">
              <span>💰 Proponer Cotización / Contraoferta (C$)</span>
              <button type="button" onClick={() => setShowPriceBox(false)}>
                <IonIcon icon={closeOutline} />
              </button>
            </div>
            <div className="cx-msgr-drawer-body">
              <span className="cx-msgr-curr">C$</span>
              <input
                type="number"
                step="any"
                min="1"
                placeholder="Monto propuesto (ej: 1800)"
                value={inputPrice}
                onChange={(e) => setInputPrice(e.target.value)}
              />
            </div>
          </div>
        )}

        <form onSubmit={handleSendMessage} className="cx-msgr-input-bar">
          {/* Botón azul + */}
          <button
            type="button"
            className="cx-msgr-plus-btn"
            title="Proponer cotización o plan"
            onClick={() => setShowPriceBox(!showPriceBox)}
          >
            <IonIcon icon={addCircle} />
          </button>

          {/* Iconos de Messenger: cámara, galería, micro */}
          <button
            type="button"
            className="cx-msgr-tool-btn"
            title="Cámara"
            onClick={() => presentToast({ message: 'Adjuntar fotos de la avería estará disponible en breve', duration: 1800 })}
          >
            <IonIcon icon={cameraOutline} />
          </button>
          <button
            type="button"
            className="cx-msgr-tool-btn"
            title="Galería"
            onClick={() => presentToast({ message: 'Selecciona una foto de la galería', duration: 1800 })}
          >
            <IonIcon icon={imagesOutline} />
          </button>
          <button
            type="button"
            className="cx-msgr-tool-btn"
            title="Nota de voz"
            onClick={() => presentToast({ message: 'Mensajes de voz en preparación', duration: 1800 })}
          >
            <IonIcon icon={micOutline} />
          </button>

          {/* Campo Píldora Aa */}
          <div className="cx-msgr-pill-input">
            <input
              type="text"
              placeholder="Aa"
              value={inputText}
              onChange={(e) => setInputText(e.target.value)}
              disabled={sending}
            />
            <button
              type="button"
              className="cx-msgr-emoji-btn"
              onClick={() => setInputText((prev) => `${prev} 😊`)}
            >
              <IonIcon icon={happyOutline} />
            </button>
          </div>

          {/* Botón de Enviar o Me gusta 👍 */}
          {inputText.trim() || inputPrice.trim() ? (
            <button
              type="submit"
              className="cx-msgr-send-btn"
              disabled={sending}
              title="Enviar mensaje"
            >
              <IonIcon icon={send} />
            </button>
          ) : (
            <button
              type="button"
              className="cx-msgr-thumb-btn"
              title="Enviar me gusta"
              onClick={enviarMeGusta}
            >
              <IonIcon icon={thumbsUp} />
            </button>
          )}
        </form>
      </IonFooter>

      {/* Modal de Información ℹ️ del Contacto */}
      <IonModal
        isOpen={showInfoModal}
        onDidDismiss={() => setShowInfoModal(false)}
        initialBreakpoint={0.7}
        breakpoints={[0, 0.7, 1]}
        className="cx-msgr-info-modal"
      >
        <div className="cx-msgr-info-body">
          <div className="cx-msgr-info-head">
            <div className="cx-msgr-info-avatar">{initials(targetName)}</div>
            <h3>{targetName}</h3>
            <p>{targetOficio} · Verificado</p>
          </div>

          <div className="cx-msgr-info-meta-list">
            <div className="cx-msgr-info-item">
              <IonIcon icon={shieldCheckmarkOutline} />
              <div>
                <strong>Seguridad CONEX</strong>
                <span>Tus datos telefónicos se protegen mediante el sistema de aceptación.</span>
              </div>
            </div>

            {phoneDirect ? (
              <div className="cx-msgr-info-item">
                <IonIcon icon={callOutline} />
                <div>
                  <strong>Teléfono directo</strong>
                  <span>{phoneDirect}</span>
                </div>
              </div>
            ) : null}

            <div className="cx-msgr-info-item">
              <IonIcon icon={cashOutline} />
              <div>
                <strong>Tarifa de referencia</strong>
                <span>{paramPrecio ? `Desde C$ ${paramPrecio}` : 'A convenir por cotización'}</span>
              </div>
            </div>
          </div>

          {/* Datos del cliente para primer mensaje si no está registrado */}
          {!contactoId && (!user || !user.telefono) && (
            <div className="cx-msgr-guest-box">
              <h4>Tus datos para este contacto:</h4>
              <input
                type="text"
                placeholder="Tu nombre"
                value={clientNombre}
                onChange={(e) => setClientNombre(e.target.value)}
              />
              <input
                type="tel"
                placeholder="Tu teléfono (ej: 8888-8888)"
                value={clientTelefono}
                onChange={(e) => setClientTelefono(e.target.value)}
              />
            </div>
          )}

          <button
            type="button"
            className="cx-msgr-close-info-btn"
            onClick={() => setShowInfoModal(false)}
          >
            Volver a la conversación
          </button>
        </div>
      </IonModal>
    </IonPage>
  );
};

export default ChatMessenger;

