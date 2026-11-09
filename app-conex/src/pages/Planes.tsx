import React from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonTitle, IonButtons,
  IonBackButton, IonIcon, IonButton, useIonToast,
} from '@ionic/react';
import {
  starOutline, rocketOutline, checkmarkCircle, personOutline,
  sparklesOutline,
} from 'ionicons/icons';
import '../theme/app.css';

/**
 * Mockup de planes para destacar servicios en el directorio.
 * Sin pagos aun: solo UI — el boton muestra 'Proximamente'.
 */
const PLANES = [
  {
    id: 'basico',
    nombre: 'Básico',
    precio: 'Gratis',
    tagline: 'Tu perfil actual',
    icon: personOutline,
    items: [
      'Visible en el directorio de servicios',
      'Aparecés en búsquedas de tu oficio',
      'Zona y datos de contacto públicos',
    ],
    actual: true,
  },
  {
    id: 'pro',
    nombre: 'Pro',
    precio: '$3.99',
    periodo: '/mes',
    tagline: 'Destacá entre los prestadores',
    icon: starOutline,
    items: [
      'Insignia de prestador verificado',
      'Arriba en búsquedas de tu oficio',
      'Estadísticas de contactos',
      'Pin destacado en el mapa',
    ],
    destacado: true,
  },
  {
    id: 'premium',
    nombre: 'Premium',
    precio: '$7.99',
    periodo: '/mes',
    tagline: 'Máxima visibilidad',
    icon: rocketOutline,
    items: [
      'Todo lo del plan Pro',
      'Banner destacado en el inicio',
      'Botón de WhatsApp directo',
      'Soporte prioritario',
    ],
  },
];

const Planes: React.FC = () => {
  const [presentToast] = useIonToast();

  const proximamente = () =>
    presentToast({ message: 'Disponible pronto — esta vista es un adelanto', duration: 2200, color: 'warning' });

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar>
          <IonButtons slot="start">
            <IonBackButton defaultHref="/app/perfil" text="" />
          </IonButtons>
          <IonTitle>Destacar mi servicio</IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        {/* Hero navy solido, sin degradado */}
        <div className="planes-hero">
          <div className="planes-hero-icon">
            <IonIcon icon={sparklesOutline} />
          </div>
          <h1>Más clientes te encuentran</h1>
          <p>Destacá tu servicio en el directorio, el buscador y el mapa de CONEX</p>
        </div>

        <div className="planes-list">
          {PLANES.map(p => (
            <div key={p.id} className={`plan-card ${p.destacado ? 'destacado' : ''}`}>
              {p.destacado && <span className="plan-flag">Recomendado</span>}
              {p.actual && <span className="plan-flag actual">Plan actual</span>}

              <div className="plan-head">
                <div className={`plan-icon ${p.destacado ? 'on' : ''}`}>
                  <IonIcon icon={p.icon} />
                </div>
                <div>
                  <h2 className="plan-nombre">{p.nombre}</h2>
                  <p className="plan-tagline">{p.tagline}</p>
                </div>
              </div>

              <div className="plan-precio">
                <strong>{p.precio}</strong>
                {p.periodo && <span>{p.periodo}</span>}
              </div>

              <ul className="plan-items">
                {p.items.map((item, i) => (
                  <li key={i}>
                    <IonIcon icon={checkmarkCircle} />
                    {item}
                  </li>
                ))}
              </ul>

              {p.actual ? (
                <IonButton expand="block" fill="outline" disabled>
                  Incluido
                </IonButton>
              ) : (
                <IonButton
                  expand="block"
                  className="plan-cta"
                  fill={p.destacado ? 'solid' : 'outline'}
                  onClick={proximamente}
                >
                  Elegir {p.nombre}
                </IonButton>
              )}
            </div>
          ))}
        </div>

        <p className="planes-nota">
          Los planes destacados todavía no están habilitados — esta es una vista previa.
        </p>
      </IonContent>
    </IonPage>
  );
};

export default Planes;
