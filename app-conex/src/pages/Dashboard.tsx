import React, { useEffect, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonTitle,
  IonRefresher, IonRefresherContent, IonCard, IonCardContent,
  IonIcon, IonBadge, IonSpinner, IonAvatar, IonItem, IonLabel,
  IonNote, RefresherEventDetail,
} from '@ionic/react';
import {
  briefcaseOutline, documentTextOutline, peopleOutline,
  locationOutline, timeOutline, chevronForward,
} from 'ionicons/icons';
import { useAuth } from '../context/AuthContext';
import { candidatoApi, empresaApi, vacantesApi } from '../services/api';
import '../theme/app.css';

/**
 * Dashboard por rol:
 * - candidato: stats (postulaciones, CVs) + vacantes recientes
 * - empresa: stats (vacantes activas, postulantes) + ultimas postulaciones
 */
const Dashboard: React.FC = () => {
  const { user } = useAuth();
  const [loading, setLoading] = useState(true);
  const [vacantes, setVacantes] = useState<any[]>([]);
  const [postulaciones, setPostulaciones] = useState<any[]>([]);
  const [cvs, setCvs] = useState<any[]>([]);
  const [misVacantes, setMisVacantes] = useState<any[]>([]);

  const load = async () => {
    try {
      if (user?.role === 'candidato') {
        const [v, p, c] = await Promise.all([
          vacantesApi.list({ per_page: 5 }),
          candidatoApi.postulaciones(),
          candidatoApi.cvs(),
        ]);
        setVacantes(v.data);
        setPostulaciones(p.data);
        setCvs(c.data);
      } else if (user?.role === 'empresa') {
        const res = await empresaApi.vacantes({ per_page: 5 });
        setMisVacantes(res.data);
      }
    } catch {
      // silencioso: el dashboard muestra lo que haya
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (user) load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user]);

  const onRefresh = async (e: CustomEvent<RefresherEventDetail>) => {
    await load();
    e.detail.complete();
  };

  const firstName = user?.nombre?.split(' ')[0] || 'Hola';

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar>
          <IonTitle>CONEX</IonTitle>
        </IonToolbar>
      </IonHeader>
      <IonContent>
        <IonRefresher slot="fixed" onIonRefresh={onRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        <div className="dash-wrap">
          <h2 className="dash-hello">Hola, {firstName}</h2>
          <p className="dash-sub">
            {user?.role === 'empresa'
              ? 'Gestiona tus vacantes y postulantes'
              : 'Encuentra tu proxima oportunidad'}
          </p>

          {loading ? (
            <div className="dash-loading"><IonSpinner name="crescent" /></div>
          ) : user?.role === 'empresa' ? (
            <EmpresaDash vacantes={misVacantes} />
          ) : (
            <CandidatoDash vacantes={vacantes} postulaciones={postulaciones} cvs={cvs} />
          )}
        </div>
      </IonContent>
    </IonPage>
  );
};

/* ---------- Candidato ---------- */
const CandidatoDash: React.FC<{ vacantes: any[]; postulaciones: any[]; cvs: any[] }> = ({
  vacantes, postulaciones, cvs,
}) => (
  <>
    <div className="stat-grid">
      <IonCard className="stat-card">
        <IonCardContent>
          <IonIcon icon={briefcaseOutline} className="stat-icon" />
          <div className="stat-num">{postulaciones.length}</div>
          <div className="stat-label">Postulaciones</div>
        </IonCardContent>
      </IonCard>
      <IonCard className="stat-card">
        <IonCardContent>
          <IonIcon icon={documentTextOutline} className="stat-icon" />
          <div className="stat-num">{cvs.length}</div>
          <div className="stat-label">CVs subidos</div>
        </IonCardContent>
      </IonCard>
    </div>

    <h3 className="dash-section">Vacantes recientes</h3>
    {vacantes.length === 0 && <p className="dash-empty">No hay vacantes disponibles.</p>}
    {vacantes.map(v => (
      <IonCard key={v.id} className="job-card" routerLink={`/app/vacante/${v.slug}`}>
        <IonCardContent>
          <div className="job-row">
            <IonAvatar className="job-logo">
              {v.empresa_logo_url
                ? <img src={v.empresa_logo_url} alt={v.empresa_nombre} />
                : <div className="job-logo-fallback">{(v.empresa_nombre || 'E')[0]}</div>}
            </IonAvatar>
            <div className="job-info">
              <div className="job-title">{v.titulo}</div>
              <div className="job-company">{v.empresa_nombre}</div>
              <div className="job-meta">
                <IonIcon icon={locationOutline} /> {v.ciudad || 'Remoto'}
                <IonBadge className="job-badge">{v.modalidad}</IonBadge>
              </div>
            </div>
            <IonIcon icon={chevronForward} className="job-chevron" />
          </div>
        </IonCardContent>
      </IonCard>
    ))}
  </>
);

/* ---------- Empresa ---------- */
const EmpresaDash: React.FC<{ vacantes: any[] }> = ({ vacantes }) => {
  const activas = vacantes.filter(v => v.estado === 'publicada').length;
  const totalPost = vacantes.reduce((acc, v) => acc + (v.total_postulantes || 0), 0);

  return (
    <>
      <div className="stat-grid">
        <IonCard className="stat-card">
          <IonCardContent>
            <IonIcon icon={briefcaseOutline} className="stat-icon" />
            <div className="stat-num">{activas}</div>
            <div className="stat-label">Vacantes activas</div>
          </IonCardContent>
        </IonCard>
        <IonCard className="stat-card">
          <IonCardContent>
            <IonIcon icon={peopleOutline} className="stat-icon" />
            <div className="stat-num">{totalPost}</div>
            <div className="stat-label">Postulantes</div>
          </IonCardContent>
        </IonCard>
      </div>

      <h3 className="dash-section">Mis vacantes</h3>
      {vacantes.length === 0 && <p className="dash-empty">Aun no tienes vacantes publicadas.</p>}
      {vacantes.map(v => (
        <IonItem key={v.id} className="vac-item" detail>
          <IonLabel>
            <h3>{v.titulo}</h3>
            <p>
              <IonIcon icon={locationOutline} /> {v.ciudad || 'Remoto'} · {v.modalidad}
            </p>
            <IonNote>
              <IonIcon icon={timeOutline} /> {v.total_postulantes ?? 0} postulantes
            </IonNote>
          </IonLabel>
          <IonBadge color={v.estado === 'publicada' ? 'success' : 'medium'} slot="end">
            {v.estado}
          </IonBadge>
        </IonItem>
      ))}
    </>
  );
};

export default Dashboard;
