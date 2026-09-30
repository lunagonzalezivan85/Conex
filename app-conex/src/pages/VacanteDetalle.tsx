import React, { useEffect, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonTitle,
  IonButtons, IonBackButton, IonButton, IonIcon, IonSpinner,
  IonBadge, IonAvatar, useIonAlert, useIonToast,
  IonModal, IonList, IonItem, IonRadioGroup, IonRadio, IonTextarea,
} from '@ionic/react';
import { useParams } from 'react-router-dom';
import {
  locationOutline, cashOutline, briefcaseOutline, timeOutline,
  checkmarkCircle, businessOutline, globeOutline, sendOutline,
} from 'ionicons/icons';
import { useAuth } from '../context/AuthContext';
import { candidatoApi, vacantesApi } from '../services/api';
import '../theme/app.css';

/**
 * Detalle de vacante: info completa + requisitos + habilidades.
 * Candidato puede postularse eligiendo CV + mensaje.
 */
const VacanteDetalle: React.FC = () => {
  const { slug } = useParams<{ slug: string }>();
  const { user } = useAuth();
  const [vacante, setVacante] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [modalPostular, setModalPostular] = useState(false);
  const [cvs, setCvs] = useState<any[]>([]);
  const [cvSel, setCvSel] = useState<number | null>(null);
  const [mensaje, setMensaje] = useState('');
  const [enviando, setEnviando] = useState(false);
  const [yaPostulado, setYaPostulado] = useState(false);
  const [presentAlert] = useIonAlert();
  const [presentToast] = useIonToast();

  useEffect(() => {
    (async () => {
      try {
        const res = await vacantesApi.show(slug);
        setVacante(res.data);
        // saber si ya postulo (candidato)
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
      const principal = (res.data || []).find((c: any) => c.es_principal === '1' || c.es_principal === 1);
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
        <IonHeader><IonToolbar><IonButtons slot="start"><IonBackButton defaultHref="/app/buscar" /></IonButtons><IonTitle>Vacante</IonTitle></IonToolbar></IonHeader>
        <IonContent><div className="buscar-empty"><p>Vacante no encontrada</p></div></IonContent>
      </IonPage>
    );
  }

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar>
          <IonButtons slot="start">
            <IonBackButton defaultHref="/app/buscar" text="" />
          </IonButtons>
          <IonTitle>{vacante.titulo}</IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        {/* Empresa */}
        <div className="vd-head">
          <IonAvatar className="vd-logo">
            {vacante.empresa_logo_url
              ? <img src={vacante.empresa_logo_url} alt={vacante.empresa_nombre} />
              : <div className="job-logo-fallback">{(vacante.empresa_nombre || 'E')[0]}</div>}
          </IonAvatar>
          <h1 className="vd-titulo">{vacante.titulo}</h1>
          <div className="vd-empresa">
            <IonIcon icon={businessOutline} /> {vacante.empresa_nombre}
            {vacante.empresa_verificada === '1' && <IonIcon icon={checkmarkCircle} color="success" />}
          </div>
          <div className="vd-badges">
            <IonBadge color="primary">{vacante.modalidad}</IonBadge>
            {vacante.categoria_nombre && <IonBadge color="medium">{vacante.categoria_nombre}</IonBadge>}
          </div>
        </div>

        {/* Meta rapida */}
        <IonList inset className="vd-meta">
          {(vacante.ciudad || vacante.region) && (
            <IonItem lines="none">
              <IonIcon icon={locationOutline} slot="start" color="medium" />
              <span>{[vacante.ciudad, vacante.region].filter(Boolean).join(', ')}</span>
            </IonItem>
          )}
          {(vacante.salario_min || vacante.salario_max) && (
            <IonItem lines="none">
              <IonIcon icon={cashOutline} slot="start" color="medium" />
              <span>{vacante.salario_min} - {vacante.salario_max} {vacante.moneda}</span>
            </IonItem>
          )}
          {vacante.anios_experiencia && (
            <IonItem lines="none">
              <IonIcon icon={briefcaseOutline} slot="start" color="medium" />
              <span>{vacante.anios_experiencia} anos de experiencia</span>
            </IonItem>
          )}
          {vacante.fecha_cierre && (
            <IonItem lines="none">
              <IonIcon icon={timeOutline} slot="start" color="medium" />
              <span>Cierra: {new Date(vacante.fecha_cierre).toLocaleDateString()}</span>
            </IonItem>
          )}
          {vacante.empresa_sitio_web && (
            <IonItem href={vacante.empresa_sitio_web} target="_blank" detail lines="none">
              <IonIcon icon={globeOutline} slot="start" color="medium" />
              <span>{vacante.empresa_sitio_web}</span>
            </IonItem>
          )}
        </IonList>

        {/* Descripcion */}
        <div className="vd-seccion">
          <h3>Descripcion</h3>
          <p className="vd-texto">{vacante.descripcion}</p>
        </div>

        {vacante.funciones && (
          <div className="vd-seccion">
            <h3>Funciones</h3>
            <p className="vd-texto">{vacante.funciones}</p>
          </div>
        )}

        {vacante.requisitos?.length > 0 && (
          <div className="vd-seccion">
            <h3>Requisitos</h3>
            <div className="vd-tags">
              {vacante.requisitos.map((r: any, i: number) => (
                <IonBadge key={i} color="light" className="vd-tag">{r.nombre}</IonBadge>
              ))}
            </div>
          </div>
        )}

        {vacante.habilidades?.length > 0 && (
          <div className="vd-seccion">
            <h3>Habilidades</h3>
            <div className="vd-tags">
              {vacante.habilidades.map((h: any, i: number) => (
                <IonBadge key={i} color="tertiary" className="vd-tag">{h.nombre}</IonBadge>
              ))}
            </div>
          </div>
        )}

        <div style={{ height: 90 }} />
      </IonContent>

      {/* CTA postularse - solo candidato */}
      {user?.role === 'candidato' && (
        <div className="vd-cta">
          <IonButton
            expand="block"
            disabled={yaPostulado}
            onClick={yaPostulado
              ? () => presentAlert({ header: 'Ya postulado', message: 'Ya te postulaste a esta vacante', buttons: ['OK'] })
              : abrirPostular}
          >
            <IonIcon icon={sendOutline} slot="start" />
            {yaPostulado ? 'Ya te postulaste' : 'Postularme'}
          </IonButton>
        </div>
      )}

      {/* Modal postularse */}
      <IonModal isOpen={modalPostular} onDidDismiss={() => setModalPostular(false)} initialBreakpoint={0.6} breakpoints={[0, 0.6, 0.9]}>
        <IonHeader>
          <IonToolbar>
            <IonTitle>Postularme</IonTitle>
            <IonButtons slot="end">
              <IonButton onClick={() => setModalPostular(false)}>Cancelar</IonButton>
            </IonButtons>
          </IonToolbar>
        </IonHeader>
        <IonContent className="ion-padding">
          <h4 style={{ marginTop: 0 }}>Elige tu CV</h4>
          {cvs.length === 0 ? (
            <p style={{ color: 'var(--ion-color-medium)' }}>
              No tienes CVs subidos. Ve a Perfil para subir uno primero.
            </p>
          ) : (
            <IonRadioGroup value={cvSel} onIonChange={e => setCvSel(e.detail.value)}>
              <IonList>
                {cvs.map(c => (
                  <IonItem key={c.id} lines="full">
                    <IonRadio value={c.id} slot="start" />
                    <span>{c.archivo_nombre}{Number(c.es_principal) === 1 ? ' (principal)' : ''}</span>
                  </IonItem>
                ))}
              </IonList>
            </IonRadioGroup>
          )}

          <IonTextarea
            label="Mensaje (opcional)"
            labelPlacement="stacked"
            placeholder="Presentate brevemente..."
            rows={3}
            value={mensaje}
            onIonInput={e => setMensaje(e.detail.value ?? '')}
            className="vd-mensaje"
          />

          <IonButton expand="block" onClick={postular} disabled={enviando || cvs.length === 0}>
            {enviando ? <IonSpinner name="crescent" /> : 'Enviar postulacion'}
          </IonButton>
        </IonContent>
      </IonModal>
    </IonPage>
  );
};

export default VacanteDetalle;
