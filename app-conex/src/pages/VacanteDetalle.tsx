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
  chatbubbleEllipsesOutline,
} from 'ionicons/icons';
import { useAuth } from '../context/AuthContext';
import { candidatoApi, vacantesApi } from '../services/api';
import '../theme/app.css';
import '../theme/buscar.css';

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
    try {
      const res = await candidatoApi.cvs();
      setCvs(res.data || []);
      const principal = (res.data || []).find((c: any) => Number(c.es_principal) === 1);
      setCvSel(principal?.id ?? res.data?.[0]?.id ?? null);
      setModalPostular(true);
    } catch {
      presentToast({ message: 'No se pudieron cargar tus CVs', duration: 2500, color: 'warning' });
    }
  };

  const postular = async () => {
    if (enviando) return;
    setEnviando(true);
    try {
      await candidatoApi.postularse(vacante.id, cvSel ?? undefined, mensaje || undefined);
      setYaPostulado(true);
      setModalPostular(false);
      presentToast({ message: 'Postulacion enviada!', duration: 2500, color: 'success' });
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

  const abrirChatEmpresa = async () => {
    if (!vacante?.id) return;
    setChateando(true);
    try {
      const res = await candidatoApi.iniciarChatEmpresa({ vacante_id: vacante.id });
      router.push(
        `/app/chat?contacto_id=${res.contacto_id}&nombre=${encodeURIComponent(res.nombre)}&oficio=${encodeURIComponent(res.oficio || vacante.titulo || '')}&avatar=${encodeURIComponent(res.avatar_url || '')}&telefono=${encodeURIComponent(res.telefono || '')}`
      );
    } catch {
      presentToast({ message: 'No se pudo abrir el chat con la empresa', duration: 2500, color: 'danger' });
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

        {/* CTA postularse inline - solo candidato */}
        {user?.role === 'candidato' && (
          <div className="vd-cta-inline" style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
            {yaPostulado ? (
              <>
                <button
                  type="button"
                  className="vd-apply done"
                  style={{ background: '#10B981', color: '#ffffff' }}
                  onClick={abrirChatEmpresa}
                >
                  <IonIcon icon={checkmarkCircle} />
                  <span>Postulación enviada · Abrir Chat con la Empresa</span>
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
                  onClick={abrirChatEmpresa}
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
