import React, { useEffect, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonTitle,
  IonList, IonItem, IonLabel, IonIcon, IonButton, IonSpinner,
  IonProgressBar, useIonAlert, useIonRouter,
} from '@ionic/react';
import {
  personOutline, mailOutline, callOutline, briefcaseOutline,
  locationOutline, linkOutline, logOutOutline, businessOutline,
  documentTextOutline,
} from 'ionicons/icons';
import { useAuth } from '../context/AuthContext';
import '../theme/app.css';

/**
 * Perfil del usuario (candidato o empresa) con datos del API /me.
 */
const Perfil: React.FC = () => {
  const { user, logout, refresh } = useAuth();
  const router = useIonRouter();
  const [presentAlert] = useIonAlert();
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    refresh();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const doLogout = () => {
    presentAlert({
      header: 'Cerrar sesion',
      message: 'Quieres cerrar tu sesion?',
      buttons: [
        { text: 'Cancelar', role: 'cancel' },
        {
          text: 'Cerrar sesion',
          role: 'destructive',
          handler: async () => {
            setLoading(true);
            await logout();
            router.push('/login', 'root');
          },
        },
      ],
    });
  };

  if (!user) {
    return (
      <IonPage>
        <IonContent>
          <div className="dash-loading"><IonSpinner name="crescent" /></div>
        </IonContent>
      </IonPage>
    );
  }

  const initials = `${(user.nombre || '')[0] || ''}${(user.apellido || '')[0] || ''}`.toUpperCase();
  const perfil = user.role === 'candidato' ? user.candidato : user.empresa;
  const porcentaje = user.role === 'candidato' ? Number(user.candidato?.porcentaje_perfil ?? 0) : null;

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar>
          <IonTitle>Mi perfil</IonTitle>
        </IonToolbar>
      </IonHeader>
      <IonContent>
        <div className="perfil-head">
          <div className="perfil-avatar">
            {user.avatar ? <img src={user.avatar} alt={user.nombre} /> : initials}
          </div>
          <h2 className="perfil-name">{user.nombre} {user.apellido}</h2>
          <span className="perfil-role">{user.role}</span>
          {perfil?.razon_social && (
            <p className="dash-sub" style={{ marginTop: 6 }}>{perfil.razon_social}</p>
          )}
        </div>

        {porcentaje !== null && (
          <div className="perfil-progress">
            <IonLabel>
              <small>Perfil completado: {porcentaje}%</small>
            </IonLabel>
            <IonProgressBar value={porcentaje / 100} color={porcentaje >= 100 ? 'success' : 'primary'} />
          </div>
        )}

        <IonList className="perfil-list" inset>
          <IonItem>
            <IonIcon icon={mailOutline} slot="start" color="medium" />
            <IonLabel>
              <h3>Email</h3><p>{user.email}</p>
            </IonLabel>
          </IonItem>
          <IonItem>
            <IonIcon icon={personOutline} slot="start" color="medium" />
            <IonLabel>
              <h3>Usuario</h3><p>@{user.usuario}</p>
            </IonLabel>
          </IonItem>
          {user.telefono && (
            <IonItem>
              <IonIcon icon={callOutline} slot="start" color="medium" />
              <IonLabel>
                <h3>Telefono</h3><p>{user.telefono}</p>
              </IonLabel>
            </IonItem>
          )}

          {user.role === 'candidato' && perfil && (
            <>
              {perfil.profesion && (
                <IonItem>
                  <IonIcon icon={briefcaseOutline} slot="start" color="medium" />
                  <IonLabel><h3>Profesion</h3><p>{perfil.profesion}</p></IonLabel>
                </IonItem>
              )}
              {(perfil.ciudad || perfil.region) && (
                <IonItem>
                  <IonIcon icon={locationOutline} slot="start" color="medium" />
                  <IonLabel>
                    <h3>Ubicacion</h3>
                    <p>{[perfil.ciudad, perfil.region, perfil.pais].filter(Boolean).join(', ')}</p>
                  </IonLabel>
                </IonItem>
              )}
              {perfil.linkedin_url && (
                <IonItem href={perfil.linkedin_url} target="_blank" detail>
                  <IonIcon icon={linkOutline} slot="start" color="medium" />
                  <IonLabel><h3>LinkedIn</h3><p>Ver perfil</p></IonLabel>
                </IonItem>
              )}
            </>
          )}

          {user.role === 'empresa' && perfil && (
            <>
              {perfil.rubro && (
                <IonItem>
                  <IonIcon icon={businessOutline} slot="start" color="medium" />
                  <IonLabel><h3>Rubro</h3><p>{perfil.rubro}</p></IonLabel>
                </IonItem>
              )}
              {(perfil.ciudad || perfil.region) && (
                <IonItem>
                  <IonIcon icon={locationOutline} slot="start" color="medium" />
                  <IonLabel>
                    <h3>Ubicacion</h3>
                    <p>{[perfil.ciudad, perfil.region, perfil.pais].filter(Boolean).join(', ')}</p>
                  </IonLabel>
                </IonItem>
              )}
              {perfil.sitio_web && (
                <IonItem href={perfil.sitio_web} target="_blank" detail>
                  <IonIcon icon={linkOutline} slot="start" color="medium" />
                  <IonLabel><h3>Sitio web</h3><p>{perfil.sitio_web}</p></IonLabel>
                </IonItem>
              )}
            </>
          )}
        </IonList>

        {user.role === 'candidato' && (
          <div style={{ padding: '0 16px' }}>
            <IonButton expand="block" fill="outline" routerLink="/app/perfil">
              <IonIcon icon={documentTextOutline} slot="start" />
              Mis CVs
            </IonButton>
          </div>
        )}

        <div style={{ padding: '8px 16px 32px' }}>
          <IonButton expand="block" color="danger" fill="outline" onClick={doLogout} disabled={loading}>
            <IonIcon icon={logOutOutline} slot="start" />
            {loading ? 'Cerrando...' : 'Cerrar sesion'}
          </IonButton>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default Perfil;
