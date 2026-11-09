import React, { useEffect, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonTitle,
  IonButtons, IonBackButton, IonIcon, IonSpinner, useIonRouter,
} from '@ionic/react';
import { vacantesApi } from '../services/api';
import { catIcon, CAT_STYLES } from '../utils/categoriaIcons';
import '../theme/app.css';

/**
 * Vista "Ver todas" del inicio: grid completo de categorias
 * (cat_categoria). Tocar una filtra las vacantes en /app/buscar.
 */
const Categorias: React.FC = () => {
  const router = useIonRouter();
  const [categorias, setCategorias] = useState<any[] | null>(null);

  useEffect(() => {
    vacantesApi.filtros()
      .then(r => setCategorias(r.categorias || r.data?.categorias || []))
      .catch(() => setCategorias([]));
  }, []);

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar>
          <IonButtons slot="start">
            <IonBackButton defaultHref="/app/dashboard" text="" />
          </IonButtons>
          <IonTitle>Categorías</IonTitle>
        </IonToolbar>
      </IonHeader>
      <IonContent>
        <div className="dash-wrap">
          {categorias === null ? (
            <div className="dash-loading"><IonSpinner name="crescent" /></div>
          ) : categorias.length === 0 ? (
            <p className="dash-empty">No hay categorias disponibles.</p>
          ) : (
            <div className="cat-page-grid">
              {categorias.map((c: any, i: number) => (
                <button
                  key={c.id}
                  type="button"
                  className="cat-btn"
                  onClick={() => router.push(`/app/buscar?categoria=${c.id}`)}
                >
                  <span className={`cat-icon ${CAT_STYLES[i % CAT_STYLES.length]}`}>
                    <IonIcon icon={catIcon(c.nombre)} />
                  </span>
                  <span className="cat-label">{c.nombre}</span>
                </button>
              ))}
            </div>
          )}
        </div>
      </IonContent>
    </IonPage>
  );
};

export default Categorias;
