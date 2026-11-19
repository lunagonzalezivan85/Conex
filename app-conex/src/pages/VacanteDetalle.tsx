import React, { useEffect, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar,
  IonButtons, IonBackButton, IonButton, IonIcon, IonSpinner,
  useIonAlert, useIonToast, useIonRouter,
  IonModal, IonTextarea,
} from '@ionic/react';
import { useParams } from 'react-router-dom';
import {
  locationOutline, cashOutline, briefcaseOutline, timeOutline,
  checkmarkCircle, businessOutline, globeOutline, sendOutline,
  peopleOutline, closeOutline, documentTextOutline, checkmarkDoneOutline,
  chatbubbleEllipsesOutline, logoWhatsapp, callOutline, helpCircleOutline,
} from 'ionicons/icons';
import { useAuth } from '../context/AuthContext';
import { candidatoApi, vacantesApi } from '../services/api';
import '../theme/app.css';
import '../theme/buscar.css';

const cleanPhone = (tel?: string | null) => {
  if (!tel) return '';
  const digits = tel.replace(/\D/g, '');
  return digits.length === 8 ? `505${digits}` : digits;
};

const PREGUNTAS_RAPIDAS = [
  '👋 Hola, ¿esta vacante sigue disponible?',
  '🕒 ¿Cuáles son los horarios laborales del puesto?',
  '📍 ¿El puesto es presencial, remoto o híbrido?',
  '💼 ¿Aceptan postulantes sin experiencia previa?',
];

/**
 * Detalle de vacante con diseno de marca: hero, meta-cards,
 * secciones con acento, CTA con gradiente y modal de postulacion.
 */
const VacanteDetalle: React.FC = () => {
  const router = useIonRouter();
  const { slug } = useParams<{ slug: string }>();
  const { user } = useAuth();
  const [vacante, setVacante] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [modalPostular, setModalPostular] = useState(false);
  const [modalConsulta, setModalConsulta] = useState(false);
  const [mensajeConsulta, setMensajeConsulta] = useState('');
  const [cvs, setCvs] = useState<any[]>([]);
  const [cvSel, setCvSel] = useState<number | null>(null);
  const [mensaje, setMensaje] = useState('');
  const [enviando, setEnviando] = useState(false);
  const [chateando, setChateando] = useState(false);
  const [yaPostulado, setYaPostulado] = useState(false);
  const [presentAlert] = useIonAlert();
  const [presentToast] = useIonToast();

  useEffect(() => {
    (async () => {
      try {
        const res = await vacantesApi.show(slug);
        setVacante(res.data);
        if (user?.role === 'candidato') {
          const p = await candidatoApi.postulaciones().catch(() => ({ data: [] }));
          const ya = p.data.some((x: any) => String(x.vacante_id) === String(res.data.id));
          setYaPostulado(ya);
        }
      } catch {
        presentToast({ message: 'No se pudo cargar la vacante', duration: 2500, color: 'danger' });
      } finally {
        setLoading(false);
      }
    })();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [slug]);

  const abrirPostular = async () => {
    if (!user) {
      presentToast({
        message: 'Inicia sesión para postularte a esta vacante',
        duration: 2500,
        color: 'warning',
      });
      router.push('/auth/login');
      return;
    }
    try {
      const res = await candidatoApi.cvs();
      setCvs(res.data || []);
      const principal = (res.data || []).find((c: any) => Number(c.es_principal) === 1);
      setCvSel(principal?.id ?? res.data?.[0]?.id ?? null);
    } catch {
      setCvs([]);
      setCvSel(null);
    } finally {
      setModalPostular(true);
    }
  };

  const postular = async () => {
    if (enviando) return;
    setEnviando(true);
    try {
      await candidatoApi.postularse(vacante.id, cvSel ?? undefined, mensaje || undefined);
      setYaPostulado(true);
      setModalPostular(false);
      presentToast({ message: '¡Postulación enviada con éxito!', duration: 2500, color: 'success' });
    } catch (e: any) {
      if (e.status === 409) {
        setYaPostulado(true);
        setModalPostular(false);
        presentToast({ message: 'Ya te postulaste a esta vacante', duration: 2500, color: 'warning' });
      } else {
        presentToast({ message: e.error || 'Error al postularse', duration: 2500, color: 'danger' });
      }
    } finally {
      setEnviando(false);
    }
  };

  const abrirConsulta = () => {
    if (!user) {
      presentToast({
        message: 'Inicia sesión para consultar o chatear con la empresa',
        duration: 2500,
        color: 'warning',
      });
      router.push('/auth/login');
      return;
    }
    setModalConsulta(true);
  };

  const enviarConsultaYChat = async (textoMsg?: string) => {
    if (!vacante?.id) return;
    setChateando(true);
    const msgToSend = (textoMsg ?? mensajeConsulta).trim();
    try {
      const res = await candidatoApi.iniciarChatEmpresa({
        vacante_id: vacante.id,
        mensaje: msgToSend || undefined,
      });
      setModalConsulta(false);
      setMensajeConsulta('');
      router.push(
        `/app/chat?contacto_id=${res.contacto_id}&nombre=${encodeURIComponent(res.nombre)}&oficio=${encodeURIComponent(res.oficio || vacante.titulo || '')}&avatar=${encodeURIComponent(res.avatar_url || '')}&telefono=${encodeURIComponent(res.telefono || '')}`
      );
    } catch (e: any) {
      const errMsg = e?.error || e?.message || 'No se pudo abrir el chat con la empresa';
      presentToast({ message: errMsg, duration: 3500, color: 'warning' });
    } finally {
      setChateando(false);
    }
  };

  const fmtFecha = (f?: string) => (f ? new Date(f).toLocaleDateString('es', { day: 'numeric', month: 'short' }) : null);

  const metaCards = [
    (vacante?.ciudad || vacante?.region) && {
      icon: locationOutline, label: 'Ubicación',
      value: [vacante.ciudad, vacante.region].filter(Boolean).join(', '),
    },
    (vacante?.salario_min || vacante?.salario_max) && {
      icon: cashOutline, label: 'Salario',
      value: `$${vacante.salario_min} - $${vacante.salario_max}`,
    },
    vacante?.anios_experiencia !== undefined && vacante?.anios_experiencia !== null && {
      icon: briefcaseOutline, label: 'Experiencia',
      value: Number(vacante.anios_experiencia) === 0 ? 'No requerida' : `${vacante.anios_experiencia} ${Number(vacante.anios_experiencia) === 1 ? 'año' : 'años'}`,
    },
    vacante?.fecha_cierre && {
      icon: timeOutline, label: 'Cierre',
      value: fmtFecha(vacante.fecha_cierre),
    },
    vacante?.vacantes_disponibles && {
      icon: peopleOutline, label: 'Plazas',
      value: `${vacante.vacantes_disponibles} disponible${Number(vacante.vacantes_disponibles) === 1 ? '' : 's'}`,
    },
  ].filter(Boolean) as { icon: string; label: string; value: string }[];

  if (loading) {
    return (
      <IonPage>
        <IonHeader><IonToolbar><IonButtons slot="start"><IonBackButton defaultHref="/app/buscar" /></IonButtons></IonToolbar></IonHeader>
        <IonContent><div className="dash-loading"><IonSpinner name="crescent" /></div></IonContent>
      </IonPage>
    );
  }

  if (!vacante) {
    return (
      <IonPage>
        <IonHeader><IonToolbar><IonButtons slot="start"><IonBackButton defaultHref="/app/buscar" /></IonButtons></IonToolbar></IonHeader>
        <IonContent><div className="buscar-empty"><p>Vacante no encontrada</p></div></IonContent>
      </IonPage>
    );
  }

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar className="vd-toolbar">
          <IonButtons slot="start">
            <IonBackButton defaultHref="/app/buscar" text="" />
          </IonButtons>
          {user?.role === 'candidato' && (
            <IonButtons slot="end">
              <button
                className={`vd-header-btn ${yaPostulado ? 'done' : ''}`}
                aria-label="Postularme"
                onClick={yaPostulado
                  ? () => presentAlert({ header: 'Ya postulado', message: 'Ya te postulaste a esta vacante', buttons: ['OK'] })
                  : abrirPostular}
              >
                <IonIcon icon={yaPostulado ? checkmarkCircle : sendOutline} />
              </button>
            </IonButtons>
          )}
        </IonToolbar>
      </IonHeader>

      <IonContent>
        {/* Hero */}
        <div className="vd-hero">
          <div className="vd-logo-ring">
            {vacante.empresa_logo_url
              ? <img src={vacante.empresa_logo_url} alt={vacante.empresa_nombre} />
              : <span className="vd-logo-letter">{(vacante.empresa_nombre || 'E')[0].toUpperCase()}</span>}
          </div>
          <h1 className="vd-titulo">{vacante.titulo}</h1>
          <div className="vd-empresa">
            <IonIcon icon={businessOutline} />
            <span>{vacante.empresa_nombre}</span>
            {Number(vacante.empresa_verificada) === 1 && (
              <IonIcon icon={checkmarkCircle} className="vd-verified" />
            )}
          </div>
          <div className="vd-badges">
            <span className="vd-badge vd-badge-primary">{vacante.modalidad}</span>
            {vacante.categoria_nombre && <span className="vd-badge">{vacante.categoria_nombre}</span>}
            {vacante.tipo_contrato_nombre && <span className="vd-badge">{vacante.tipo_contrato_nombre}</span>}
            {Number(vacante.destacada) === 1 && <span className="vd-badge vd-badge-star">Destacada</span>}
          </div>
        </div>

        {/* Meta cards grid */}
        {metaCards.length > 0 && (
          <div className="vd-grid">
            {metaCards.map((m, i) => (
              <div className="vd-meta-card" key={i}>
                <IonIcon icon={m.icon} />
                <span className="vd-meta-label">{m.label}</span>
                <span className="vd-meta-value">{m.value}</span>
              </div>
            ))}
          </div>
        )}

        {/* Descripción */}
        <section className="vd-seccion">
          <h3 className="vd-seccion-title">Descripción</h3>
          <p className="vd-texto">{vacante.descripcion}</p>
        </section>

        {vacante.funciones && (
          <section className="vd-seccion">
            <h3 className="vd-seccion-title">Funciones</h3>
            <p className="vd-texto">{vacante.funciones}</p>
          </section>
        )}

        {vacante.requisitos?.length > 0 && (
          <section className="vd-seccion">
            <h3 className="vd-seccion-title">Requisitos</h3>
            <ul className="vd-reqs">
              {vacante.requisitos.map((r: any, i: number) => (
                <li key={i}>
                  <IonIcon icon={checkmarkDoneOutline} />
                  {r.nombre}
                </li>
              ))}
            </ul>
          </section>
        )}

        {vacante.habilidades?.length > 0 && (
          <section className="vd-seccion">
            <h3 className="vd-seccion-title">Habilidades</h3>
            <div className="vd-tags">
              {vacante.habilidades.map((h: any, i: number) => (
                <span key={i} className="vd-skill">{h.nombre}</span>
              ))}
            </div>
          </section>
        )}

        {/* Acerca de la empresa */}
        {(vacante.empresa_sitio_web || vacante.empresa_nombre) && (
          <section className="vd-seccion">
            <h3 className="vd-seccion-title">Acerca de la empresa</h3>
            <div className="vd-empresa-card">
              <IonIcon icon={businessOutline} />
              <div className="vd-empresa-card-text">
                <strong>{vacante.empresa_nombre}</strong>
                {vacante.empresa_sitio_web && (
                  <a href={vacante.empresa_sitio_web} target="_blank" rel="noreferrer">
                    <IonIcon icon={globeOutline} /> {vacante.empresa_sitio_web}
                  </a>
                )}
              </div>
            </div>
          </section>
        )}

        {/* CTA postularse inline */}
        {(!user || user?.role === 'candidato' || user?.role === 'empresa' || user?.role === 'admin') && (
          <div className="vd-cta-inline" style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
            {yaPostulado ? (
              <>
                <button
                  type="button"
                  className="vd-apply done"
                  style={{ background: '#10B981', color: '#ffffff' }}
                  onClick={abrirConsulta}
                >
                  <IonIcon icon={checkmarkCircle} />
                  <span>Postulación enviada · Consultar a la Empresa</span>
                </button>
              </>
            ) : (
              <>
                <button
                  type="button"
                  className="vd-apply"
                  onClick={abrirPostular}
                >
                  <IonIcon icon={sendOutline} />
                  <span>Postularme ahora</span>
                </button>
                <button
                  type="button"
                  onClick={abrirConsulta}
                  disabled={chateando}
                  style={{
                    background: '#F1F5F9',
                    color: '#1B2A4A',
                    border: '1px solid #CBD5E1',
                    borderRadius: 14,
                    padding: '12px 16px',
                    fontWeight: 700,
                    fontSize: 14,
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    gap: 8,
                    cursor: 'pointer',
                  }}
                >
                  {chateando ? <IonSpinner name="crescent" style={{ width: 20, height: 20 }} /> : <IonIcon icon={chatbubbleEllipsesOutline} />}
                  <span>Consultar o enviar mensaje a la empresa</span>
                </button>
              </>
            )}
          </div>
        )}

        <div style={{ height: 40 }} />
      </IonContent>

      {/* Modal Consulta a la Empresa */}
      <IonModal
        isOpen={modalConsulta}
        onDidDismiss={() => setModalConsulta(false)}
        initialBreakpoint={0.78}
        breakpoints={[0, 0.78, 0.95]}
        className="filtros-modal"
      >
        <IonContent>
          <div className="fm-head">
            <button className="fm-close" onClick={() => setModalConsulta(false)} aria-label="Cerrar">
              <IonIcon icon={closeOutline} />
            </button>
            <span className="fm-title">Consultar a la Empresa</span>
            <span style={{ width: 52 }} />
          </div>

          <div style={{ padding: '16px 20px' }}>
            {/* Tarjeta de Empresa y Vacante */}
            <div
              style={{
                background: '#F8FAFC',
                border: '1px solid #E2E8F0',
                borderRadius: 14,
                padding: '12px 14px',
                display: 'flex',
                alignItems: 'center',
                gap: 12,
                marginBottom: 14,
              }}
            >
              <div
                style={{
                  width: 44,
                  height: 44,
                  borderRadius: 12,
                  background: '#1B2A4A',
                  color: '#fff',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  fontWeight: 800,
                  fontSize: 16,
                  overflow: 'hidden',
                  flexShrink: 0,
                }}
              >
                {vacante?.empresa_logo_url ? (
                  <img src={vacante.empresa_logo_url} alt="" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                ) : (
                  (vacante?.empresa_nombre || 'E').slice(0, 2).toUpperCase()
                )}
              </div>
              <div style={{ minWidth: 0, flex: 1 }}>
                <div style={{ fontWeight: 800, fontSize: 14, color: '#1B2A4A', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                  {vacante?.empresa_nombre || 'Empresa Reclutadora'}
                </div>
                <div style={{ fontSize: 12, color: '#64748B', whiteSpace: 'nowrap', overflow: 'hidden', textOverflow: 'ellipsis' }}>
                  Vacante: {vacante?.titulo}
                </div>
              </div>
            </div>

            {/* Aviso si la vacante es externa */}
            {(!vacante?.empresa_id || vacante?.empresa_externa) && (
              <div style={{ background: '#FEF3C7', border: '1px solid #FDE68A', borderRadius: 12, padding: 12, marginBottom: 14 }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 8, color: '#92400E', fontWeight: 700, fontSize: 13 }}>
                  <IonIcon icon={helpCircleOutline} />
                  <span>Publicación de Reclutador Externo</span>
                </div>
                <p style={{ margin: '6px 0 0', fontSize: 12, color: '#78350F', lineHeight: 1.4 }}>
                  Esta vacante fue provista por <strong>{vacante?.empresa_externa || 'fuente externa'}</strong>.
                  {vacante?.contacto_externo || vacante?.empresa_telefono ? (
                    <> Puedes contactar directamente al: <strong>{vacante?.contacto_externo || vacante?.empresa_telefono}</strong></>
                  ) : (
                    <> Para más información consulta la descripción de la vacante.</>
                  )}
                </p>
              </div>
            )}

            {/* Preguntas rápidas sugeridas */}
            <div style={{ marginBottom: 14 }}>
              <div style={{ fontSize: 11, fontWeight: 700, color: '#475569', marginBottom: 8, textTransform: 'uppercase', letterSpacing: '0.04em' }}>
                Preguntas sugeridas (toca para seleccionar)
              </div>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                {PREGUNTAS_RAPIDAS.map((p, i) => (
                  <button
                    key={i}
                    type="button"
                    onClick={() => setMensajeConsulta(p)}
                    style={{
                      background: mensajeConsulta === p ? '#EEF2FF' : '#FFFFFF',
                      border: mensajeConsulta === p ? '1.5px solid #4F46E5' : '1px solid #E2E8F0',
                      borderRadius: 10,
                      padding: '8px 12px',
                      fontSize: 13,
                      color: mensajeConsulta === p ? '#4338CA' : '#334155',
                      textAlign: 'left',
                      fontWeight: mensajeConsulta === p ? 600 : 500,
                      cursor: 'pointer',
                      transition: 'all 0.15s ease',
                    }}
                  >
                    {p}
                  </button>
                ))}
              </div>
            </div>

            {/* Mensaje personalizado */}
            <div style={{ marginBottom: 16 }}>
              <label style={{ display: 'block', fontSize: 11, fontWeight: 700, color: '#475569', marginBottom: 6, textTransform: 'uppercase', letterSpacing: '0.04em' }}>
                Tu mensaje o consulta
              </label>
              <textarea
                placeholder="Escribe tu consulta sobre la vacante, turnos, requisitos..."
                rows={3}
                value={mensajeConsulta}
                onChange={(e) => setMensajeConsulta(e.target.value)}
                style={{
                  width: '100%',
                  borderRadius: 12,
                  border: '1px solid #CBD5E1',
                  padding: '10px 12px',
                  fontSize: 14,
                  fontFamily: 'inherit',
                  outline: 'none',
                  resize: 'none',
                  boxSizing: 'border-box',
                }}
              />
            </div>

            {/* Botón principal enviar consulta y abrir chat */}
            <button
              type="button"
              className="fm-apply"
              onClick={() => enviarConsultaYChat()}
              disabled={chateando}
              style={{
                width: '100%',
                background: 'linear-gradient(135deg, #1B2A4A 0%, #2563EB 100%)',
                color: '#ffffff',
                border: 'none',
                borderRadius: 14,
                padding: '14px',
                fontWeight: 700,
                fontSize: 15,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                gap: 8,
                cursor: 'pointer',
                boxShadow: '0 4px 12px rgba(37, 99, 235, 0.25)',
              }}
            >
              {chateando ? (
                <IonSpinner name="crescent" style={{ width: 20, height: 20 }} />
              ) : (
                <>
                  <IonIcon icon={chatbubbleEllipsesOutline} style={{ fontSize: 18 }} />
                  <span>{mensajeConsulta.trim() ? 'Enviar Consulta y Abrir Chat' : 'Abrir Chat con la Empresa'}</span>
                </>
              )}
            </button>

            {/* Alternativas de contacto directo si hay teléfono */}
            {vacante?.empresa_telefono && (
              <div style={{ marginTop: 16, paddingTop: 14, borderTop: '1px solid #E2E8F0' }}>
                <div style={{ fontSize: 11, fontWeight: 700, color: '#64748B', textTransform: 'uppercase', marginBottom: 8 }}>
                  Contacto directo con reclutamiento
                </div>
                <div style={{ display: 'flex', gap: 10 }}>
                  <a
                    href={`https://wa.me/${cleanPhone(vacante.empresa_telefono)}?text=${encodeURIComponent(`Hola, vi su vacante de ${vacante.titulo} en CONEX y me gustaría consultarles:`)}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    style={{
                      flex: 1,
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      gap: 6,
                      background: '#25D366',
                      color: '#ffffff',
                      borderRadius: 12,
                      padding: '10px 12px',
                      fontWeight: 700,
                      fontSize: 13,
                      textDecoration: 'none',
                    }}
                  >
                    <IonIcon icon={logoWhatsapp} style={{ fontSize: 18 }} />
                    <span>WhatsApp</span>
                  </a>
                  <a
                    href={`tel:${vacante.empresa_telefono}`}
                    style={{
                      flex: 1,
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      gap: 6,
                      background: '#F1F5F9',
                      color: '#1B2A4A',
                      border: '1px solid #CBD5E1',
                      borderRadius: 12,
                      padding: '10px 12px',
                      fontWeight: 700,
                      fontSize: 13,
                      textDecoration: 'none',
                    }}
                  >
                    <IonIcon icon={callOutline} style={{ fontSize: 18 }} />
                    <span>Llamar</span>
                  </a>
                </div>
              </div>
            )}
          </div>
          <div style={{ height: 40 }} />
        </IonContent>
      </IonModal>

      {/* Modal postularse */}
      <IonModal isOpen={modalPostular} onDidDismiss={() => setModalPostular(false)} initialBreakpoint={0.7} breakpoints={[0, 0.7, 0.95]} className="filtros-modal">
        <IonContent>
          <div className="fm-head">
            <button className="fm-close" onClick={() => setModalPostular(false)} aria-label="Cerrar">
              <IonIcon icon={closeOutline} />
            </button>
            <span className="fm-title">Postularme</span>
            <span style={{ width: 52 }} />
          </div>

          <section className="fm-section">
            <h4>Currículum Vitae (CV)</h4>
            {cvs.length === 0 ? (
              <div className="vd-empty-cv" style={{ background: '#F0FDF4', border: '1px solid #BBF7D0', padding: 14, borderRadius: 12 }}>
                <IonIcon icon={checkmarkCircle} style={{ color: '#16A34A', fontSize: 30 }} />
                <p style={{ margin: '6px 0 0', color: '#166534', fontSize: 13, lineHeight: 1.4 }}>
                  <strong>¡Te postularás con tu Perfil Digital CONEX!</strong><br />
                  No tienes un archivo PDF subido, pero la empresa recibirá tu perfil completo: datos de contacto, profesión, experiencia y tu carta de presentación.
                </p>
              </div>
            ) : (
              <div className="vd-cv-list">
                {cvs.map(c => (
                  <button
                    key={c.id}
                    className={`vd-cv-item ${cvSel === c.id ? 'active' : ''}`}
                    onClick={() => setCvSel(c.id)}
                  >
                    <IonIcon icon={documentTextOutline} />
                    <span className="vd-cv-name">
                      {c.archivo_nombre}
                      {Number(c.es_principal) === 1 && <small>Principal</small>}
                    </span>
                    <span className={`fm-geo-dot ${cvSel === c.id ? 'on' : ''}`} />
                  </button>
                ))}
              </div>
            )}
          </section>

          <section className="fm-section">
            <h4>Mensaje o Carta de Presentación (opcional)</h4>
            <div className="fm-field vd-msg-field">
              <textarea
                placeholder="Preséntate brevemente a la empresa o destaca tu experiencia..."
                rows={3}
                value={mensaje}
                onChange={e => setMensaje(e.target.value)}
              />
            </div>
          </section>

          <div style={{ height: 100 }} />
        </IonContent>

        <div className="fm-footer">
          <button className="fm-apply" onClick={postular} disabled={enviando}>
            {enviando ? <IonSpinner name="crescent" /> : cvs.length > 0 ? 'Enviar postulación con CV' : 'Postularme con mi Perfil CONEX'}
          </button>
        </div>
      </IonModal>
    </IonPage>
  );
};

export default VacanteDetalle;
