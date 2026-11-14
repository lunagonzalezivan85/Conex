import React, { useState, useEffect } from 'react';
import {
  IonModal,
  IonButton,
  IonIcon,
  IonSpinner,
  useIonToast,
  useIonRouter,
} from '@ionic/react';
import {
  closeOutline,
  sendOutline,
  cashOutline,
  shieldCheckmarkOutline,
  checkmarkCircle,
  chatbubblesOutline,
  personOutline,
  callOutline,
  navigateOutline,
} from 'ionicons/icons';
import { useAuth } from '../context/AuthContext';
import { serviciosApi } from '../services/api';

export interface PrestadorChatInfo {
  id: number;
  nombre_publico: string;
  oficio: string;
  avatar_url?: string | null;
  precio_desde?: number | string | null;
  precio_tipo?: string | null;
  zona?: string | null;
  ciudad?: string | null;
  verificado?: boolean | number;
  disponible?: boolean | number;
}

interface ChatContactModalProps {
  prestador: PrestadorChatInfo | null;
  isOpen: boolean;
  onClose: () => void;
  onSuccess?: (solicitudId?: number) => void;
}

export const ChatContactModal: React.FC<ChatContactModalProps> = ({
  prestador,
  isOpen,
  onClose,
  onSuccess,
}) => {
  const { user } = useAuth();
  const router = useIonRouter();
  const [presentToast] = useIonToast();

  const [nombre, setNombre] = useState('');
  const [telefono, setTelefono] = useState('');
  const [mensaje, setMensaje] = useState('');
  const [precioPropuesto, setPrecioPropuesto] = useState('');
  const [sending, setSending] = useState(false);
  const [sent, setSent] = useState(false);
  const [sentMessage, setSentMessage] = useState<string | null>(null);
  const [sentPrice, setSentPrice] = useState<string | null>(null);

  // Pre-llenar datos del usuario
  useEffect(() => {
    if (isOpen) {
      const defaultName = `${user?.nombre ?? ''} ${user?.apellido ?? ''}`.trim();
      setNombre(defaultName);
      setTelefono(user?.telefono ?? '');
      setMensaje('');
      setPrecioPropuesto('');
      setSent(false);
      setSentMessage(null);
      setSentPrice(null);
    }
  }, [isOpen, user]);

  if (!prestador) return null;

  const getInitials = (name?: string) => {
    if (!name) return 'P';
    const parts = name.trim().split(/\s+/);
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return (parts[0][0] + parts[1][0]).toUpperCase();
  };

  const handleSend = async (e?: React.FormEvent) => {
    if (e) e.preventDefault();

    const cleanNombre = nombre.trim();
    const cleanTelefono = telefono.trim();
    const cleanMensaje = mensaje.trim();

    if (cleanNombre.length < 2) {
      presentToast({
        message: 'Por favor ingresa tu nombre',
        duration: 2200,
        color: 'warning',
      });
      return;
    }

    if (cleanTelefono.length < 7) {
      presentToast({
        message: 'Por favor ingresa un número de teléfono válido',
        duration: 2200,
        color: 'warning',
      });
      return;
    }

    if (cleanMensaje.length < 4) {
      presentToast({
        message: 'Escribe un breve mensaje describiendo lo que necesitas',
        duration: 2200,
        color: 'warning',
      });
      return;
    }

    setSending(true);
    try {
      const parsedPrice = precioPropuesto.trim() ? parseFloat(precioPropuesto) : undefined;
      const res = await serviciosApi.contactar(prestador.id, {
        nombre: cleanNombre,
        telefono: cleanTelefono,
        mensaje: cleanMensaje,
        precio_propuesto: parsedPrice,
      });

      setSent(true);
      setSentMessage(cleanMensaje);
      setSentPrice(precioPropuesto.trim() ? precioPropuesto.trim() : null);

      presentToast({
        message: res.message || '¡Mensaje enviado con éxito!',
        duration: 2600,
        color: 'success',
      });

      if (onSuccess) {
        onSuccess(res.contacto_id);
      }
    } catch (err: any) {
      // eslint-disable-next-line no-console
      console.warn('Error al contactar prestador:', err);
      presentToast({
        message: err?.error || 'No se pudo enviar el mensaje. Intenta de nuevo.',
        duration: 2800,
        color: 'danger',
      });
    } finally {
      setSending(false);
    }
  };

  const irASolicitudes = () => {
    onClose();
    router.push('/app/solicitudes');
  };

  return (
    <IonModal
      isOpen={isOpen}
      onDidDismiss={onClose}
      initialBreakpoint={0.88}
      breakpoints={[0, 0.65, 0.88, 1]}
      handle={true}
      className="cx-chat-sheet-modal"
    >
      <div className="cx-chat-sheet-content">
        {/* Encabezado estilo Messenger */}
        <div className="cx-chat-sheet-header">
          <div className="cx-chat-prestador-info">
            <div className="cx-chat-avatar-wrap">
              {prestador.avatar_url ? (
                <img
                  src={prestador.avatar_url}
                  alt={prestador.nombre_publico}
                  className="cx-chat-avatar-img"
                />
              ) : (
                <div className="cx-chat-avatar-fallback">
                  {getInitials(prestador.nombre_publico || prestador.oficio)}
                </div>
              )}
              <span className="cx-chat-online-dot" />
            </div>

            <div className="cx-chat-prestador-meta">
              <div className="cx-chat-prestador-name">
                {prestador.nombre_publico}
                {prestador.verificado ? (
                  <IonIcon
                    icon={shieldCheckmarkOutline}
                    className="cx-chat-verified-icon"
                    title="Prestador verificado"
                  />
                ) : null}
              </div>
              <div className="cx-chat-prestador-sub">
                <span className="cx-chat-trade">{prestador.oficio}</span>
                <span className="cx-chat-sep">·</span>
                <span className="cx-chat-location">
                  {prestador.zona || prestador.ciudad || 'Nicaragua'}
                </span>
              </div>
              {prestador.precio_desde ? (
                <div className="cx-chat-price-badge">
                  Tarifa: Desde C$ {Number(prestador.precio_desde).toLocaleString()}
                  {prestador.precio_tipo === 'hora' ? '/hora' : ''}
                </div>
              ) : null}
            </div>
          </div>

          <button
            type="button"
            className="cx-chat-close-btn"
            onClick={onClose}
            aria-label="Cerrar"
          >
            <IonIcon icon={closeOutline} />
          </button>
        </div>

        {/* Cuerpo del Chat / Conversación */}
        <div className="cx-chat-sheet-body">
          {/* Mensaje de bienvenida del prestador */}
          <div className="msg-bubble-row incoming">
            <span className="msg-bubble-author">{prestador.nombre_publico}</span>
            <div className="msg-bubble incoming">
              ¡Hola! 👋 Trabajo en servicios de <strong>{prestador.oficio}</strong>.
              Cuéntame qué necesitas o qué problema deseas resolver y con gusto te respondo.
              <span className="msg-bubble-time">En línea</span>
            </div>
          </div>

          {/* Si ya se envió el mensaje, mostrar la burbuja saliente del cliente */}
          {sent && sentMessage && (
            <div className="msg-bubble-row outgoing" style={{ marginTop: '12px' }}>
              <span className="msg-bubble-author">Tú (Solicitud enviada)</span>
              <div className="msg-bubble outgoing">
                "{sentMessage}"
                <span className="msg-bubble-time">Enviado hace un momento</span>
              </div>
              {sentPrice && (
                <div className="cx-deal-card outgoing">
                  <div className="cx-deal-header">
                    <div className="cx-deal-title-box">
                      <span className="cx-deal-icon">💰</span>
                      <span>Tu Presupuesto Estimado</span>
                    </div>
                    <span className="cx-deal-status pendiente">Pendiente</span>
                  </div>
                  <div className="cx-deal-amount-box">
                    <span className="cx-deal-currency">C$</span>
                    <span className="cx-deal-amount">{Number(sentPrice).toLocaleString('es-NI', { minimumFractionDigits: 2 })}</span>
                  </div>
                  <div className="cx-deal-footer">
                    Propuesta inicial enviada al prestador para cotizar el trabajo.
                  </div>
                </div>
              )}
            </div>
          )}

          {/* Confirmación y llamado a la acción una vez enviado */}
          {sent ? (
            <div className="cx-chat-sent-success">
              <div className="cx-chat-sent-icon">
                <IonIcon icon={checkmarkCircle} />
              </div>
              <h4>¡Mensaje entregado!</h4>
              <p>
                {prestador.nombre_publico} recibirá una notificación y podrá responderte
                con su presupuesto o disponibilidad directamente en tu bandeja de mensajes.
              </p>
              <div className="cx-chat-sent-actions">
                <IonButton
                  expand="block"
                  className="msg-send-btn"
                  onClick={irASolicitudes}
                >
                  <IonIcon icon={chatbubblesOutline} slot="start" />
                  Ver en Mis Mensajes / Negociación
                </IonButton>
                <IonButton
                  expand="block"
                  fill="clear"
                  color="medium"
                  onClick={onClose}
                >
                  Continuar explorando
                </IonButton>
              </div>
            </div>
          ) : (
            /* Formulario de envío estilo Messenger */
            <form onSubmit={handleSend} className="cx-chat-form-box">
              {/* Campos de contacto si no están presentes */}
              {(!user || !user.telefono) && (
                <div className="cx-chat-contact-fields">
                  <div className="cx-chat-input-wrap">
                    <IonIcon icon={personOutline} />
                    <input
                      type="text"
                      className="cx-chat-compact-input"
                      placeholder="Tu nombre"
                      value={nombre}
                      onChange={(e) => setNombre(e.target.value)}
                      required
                    />
                  </div>
                  <div className="cx-chat-input-wrap">
                    <IonIcon icon={callOutline} />
                    <input
                      type="tel"
                      className="cx-chat-compact-input"
                      placeholder="Tu teléfono (ej: 8888-8888)"
                      value={telefono}
                      onChange={(e) => setTelefono(e.target.value)}
                      required
                    />
                  </div>
                </div>
              )}

              {/* Textarea para el mensaje del usuario */}
              <div className="cx-chat-textarea-wrap">
                <textarea
                  className="cx-chat-textarea"
                  placeholder={`Escribe un mensaje para ${prestador.nombre_publico}... (ej: Necesito reparar una fuga de agua en Managua este fin de semana)`}
                  rows={3}
                  value={mensaje}
                  onChange={(e) => setMensaje(e.target.value)}
                  disabled={sending}
                  required
                />
              </div>

              {/* Fila opcional de presupuesto */}
              <div className="cx-chat-bottom-row">
                <div className="cx-chat-budget-box">
                  <IonIcon icon={cashOutline} className="cx-chat-budget-icon" />
                  <span className="cx-chat-cur-prefix">C$</span>
                  <input
                    type="number"
                    min="1"
                    step="any"
                    className="cx-chat-budget-input"
                    placeholder="Presupuesto sugerido (opcional)"
                    value={precioPropuesto}
                    onChange={(e) => setPrecioPropuesto(e.target.value)}
                    disabled={sending}
                  />
                </div>

                <button
                  type="submit"
                  className="cx-chat-submit-btn"
                  disabled={sending || !mensaje.trim()}
                  title="Enviar mensaje"
                >
                  {sending ? (
                    <IonSpinner name="dots" />
                  ) : (
                    <>
                      <span>Enviar</span>
                      <IonIcon icon={sendOutline} />
                    </>
                  )}
                </button>
              </div>

              <div className="cx-chat-privacy-hint">
                <IonIcon icon={shieldCheckmarkOutline} />
                <span>
                  Tu número de teléfono solo se compartirá si {prestador.nombre_publico} acepta la negociación.
                </span>
              </div>
            </form>
          )}
        </div>
      </div>
    </IonModal>
  );
};
export default ChatContactModal;

