import React, { useEffect, useRef, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonSearchbar,
  IonButton, IonIcon, IonChip, IonLabel, IonModal, IonList,
  IonItem, IonSelect, IonSelectOption, IonRange, IonToggle, IonInput,
  IonCard, IonCardContent, IonAvatar, IonBadge, IonSpinner,
  IonInfiniteScroll, IonInfiniteScrollContent, IonRefresher,
  IonRefresherContent, useIonToast, RefresherEventDetail,
} from '@ionic/react';
import {
  optionsOutline, locationOutline, navigateOutline,
  chevronForward, businessOutline, cashOutline,
} from 'ionicons/icons';
import { Geolocation } from '@capacitor/geolocation';
import { vacantesApi } from '../services/api';
import '../theme/app.css';
import '../theme/buscar.css';

interface Filtros {
  q: string;
  categoria: string;
  modalidad: string;
  ciudad: string;
  orden: string;
  geo: boolean;
  radio: number;
}

const FILTROS_INIT: Filtros = {
  q: '', categoria: '', modalidad: '', ciudad: '',
  orden: 'reciente', geo: false, radio: 50,
};

const ORDENES: Record<string, string> = {
  reciente: 'Mas recientes',
  antigua: 'Mas antiguas',
  salario: 'Mayor salario',
  distancia: 'Mas cercanas',
};

const Buscar: React.FC = () => {
  const [filtros, setFiltros] = useState<Filtros>(FILTROS_INIT);
  const [coords, setCoords] = useState<{ lat: number; lng: number } | null>(null);
  const [vacantes, setVacantes] = useState<any[]>([]);
  const [meta, setMeta] = useState<any>({ current_page: 1, last_page: 1, total: 0 });
  const [loading, setLoading] = useState(true);
  const [modalOpen, setModalOpen] = useState(false);
  const [categorias, setCategorias] = useState<any[]>([]);
  const [geoLoading, setGeoLoading] = useState(false);
  const [presentToast] = useIonToast();
  const searchRef = useRef<HTMLIonSearchbarElement>(null);
  const debounceRef = useRef<ReturnType<typeof setTimeout>>();

  useEffect(() => {
    vacantesApi.filtros()
      .then(r => setCategorias(r.categorias || r.data?.categorias || []))
      .catch(() => {});
  }, []);

  const cargar = async (page = 1, replace = false) => {
    if (page === 1) setLoading(true);
    try {
      const params: Record<string, any> = {
        q: filtros.q,
        categoria: filtros.categoria,
        modalidad: filtros.modalidad,
        ciudad: filtros.ciudad,
        orden: filtros.orden,
        page,
        per_page: 15,
      };
      if (filtros.geo && coords) {
        params.lat = coords.lat;
        params.lng = coords.lng;
        params.radio = filtros.radio;
      }
      const res = await vacantesApi.list(params);
      setVacantes(prev => (replace || page === 1 ? res.data : [...prev, ...res.data]));
      setMeta(res.meta);
    } catch {
      presentToast({ message: 'Error cargando vacantes', duration: 2500, color: 'danger' });
    } finally {
      setLoading(false);
    }
  };

  // Recarga al cambiar filtros (debounce para el texto)
  useEffect(() => {
    if (debounceRef.current) clearTimeout(debounceRef.current);
    debounceRef.current = setTimeout(() => cargar(1, true), 400);
    return () => { if (debounceRef.current) clearTimeout(debounceRef.current); };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [filtros, coords]);

  const activarGeo = async () => {
    setGeoLoading(true);
    try {
      const pos = await Geolocation.getCurrentPosition({ enableHighAccuracy: true });
      setCoords({ lat: pos.coords.latitude, lng: pos.coords.longitude });
      setFiltros(f => ({ ...f, geo: true, orden: f.orden === 'reciente' ? 'distancia' : f.orden }));
    } catch {
      presentToast({ message: 'No se pudo obtener tu ubicacion', duration: 2500, color: 'warning' });
      setFiltros(f => ({ ...f, geo: false }));
    } finally {
      setGeoLoading(false);
    }
  };

  const quitarGeo = () => {
    setCoords(null);
    setFiltros(f => ({ ...f, geo: false, orden: f.orden === 'distancia' ? 'reciente' : f.orden }));
  };

  const onRefresh = async (e: CustomEvent<RefresherEventDetail>) => {
    await cargar(1, true);
    e.detail.complete();
  };

  const cargarMas = async (e: CustomEvent<void>) => {
    if (meta.current_page < meta.last_page) {
      await cargar(meta.current_page + 1);
    }
    (e.target as HTMLIonInfiniteScrollElement).complete();
  };

  const filtrosActivos = [
    filtros.categoria && categorias.find(c => String(c.id) === String(filtros.categoria))?.nombre,
    filtros.modalidad,
    filtros.ciudad,
    filtros.geo ? `A ${filtros.radio} km` : null,
    filtros.orden !== 'reciente' ? ORDENES[filtros.orden] : null,
  ].filter(Boolean);

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar>
          <IonSearchbar
            ref={searchRef}
            placeholder="Buscar empleo, empresa..."
            debounce={0}
            value={filtros.q}
            onIonInput={e => setFiltros(f => ({ ...f, q: e.detail.value ?? '' }))}
            className="buscar-searchbar"
          />
          <IonButton
            slot="end"
            fill="clear"
            onClick={() => setModalOpen(true)}
            aria-label="Filtros"
          >
            <IonIcon icon={optionsOutline} slot="icon-only" />
          </IonButton>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonRefresher slot="fixed" onIonRefresh={onRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        {/* Chips de filtros activos */}
        <div className="buscar-chips">
          <IonChip
            outline={!filtros.geo}
            color={filtros.geo ? 'primary' : 'medium'}
            onClick={filtros.geo ? quitarGeo : activarGeo}
          >
            <IonIcon icon={navigateOutline} />
            <IonLabel>
              {geoLoading ? 'Ubicando...' : filtros.geo ? `Cerca de ti (${filtros.radio}km)` : 'Cerca de ti'}
            </IonLabel>
          </IonChip>
          {filtrosActivos.filter((f): f is string => !!f && f !== `A ${filtros.radio} km`).map((f, i) => (
            <IonChip key={i} color="primary" outline>{f}</IonChip>
          ))}
          <IonChip color="medium" outline onClick={() => setModalOpen(true)}>
            <IonIcon icon={optionsOutline} />
            <IonLabel>{meta.total} resultados</IonLabel>
          </IonChip>
        </div>

        {loading ? (
          <div className="dash-loading"><IonSpinner name="crescent" /></div>
        ) : vacantes.length === 0 ? (
          <div className="buscar-empty">
            <IonIcon icon={businessOutline} />
            <p>No se encontraron vacantes con esos filtros</p>
            <IonButton fill="clear" size="small" onClick={() => { setFiltros(FILTROS_INIT); setCoords(null); }}>
              Limpiar filtros
            </IonButton>
          </div>
        ) : (
          vacantes.map(v => (
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
                      <IonIcon icon={locationOutline} />
                      {v.ciudad || 'Sin ubicacion'} · {v.modalidad}
                      {v.distancia_km !== undefined && (
                        <IonBadge color="primary" className="job-badge">
                          {Math.round(v.distancia_km)} km
                        </IonBadge>
                      )}
                    </div>
                    {(v.salario_min || v.salario_max) && (
                      <div className="job-meta">
                        <IonIcon icon={cashOutline} />
                        {v.salario_min} - {v.salario_max} {v.moneda}
                      </div>
                    )}
                  </div>
                  <IonIcon icon={chevronForward} className="job-chevron" />
                </div>
              </IonCardContent>
            </IonCard>
          ))
        )}

        <IonInfiniteScroll onIonInfinite={cargarMas} disabled={meta.current_page >= meta.last_page}>
          <IonInfiniteScrollContent loadingSpinner="crescent" loadingText="Cargando..." />
        </IonInfiniteScroll>
      </IonContent>

      {/* Modal de filtros */}
      <IonModal isOpen={modalOpen} onDidDismiss={() => setModalOpen(false)} initialBreakpoint={0.75} breakpoints={[0, 0.75, 1]}>
        <IonHeader>
          <IonToolbar>
            <IonButton slot="start" fill="clear" onClick={() => { setFiltros(f => ({ ...FILTROS_INIT, q: f.q })); quitarGeo(); }}>
              Limpiar
            </IonButton>
            <IonButton slot="end" fill="clear" strong onClick={() => setModalOpen(false)}>
              Aplicar
            </IonButton>
          </IonToolbar>
        </IonHeader>
        <IonContent className="ion-padding">
          <IonList inset>
            <IonItem>
              <IonSelect
                label="Categoria"
                labelPlacement="stacked"
                value={filtros.categoria}
                onIonChange={e => setFiltros(f => ({ ...f, categoria: e.detail.value }))}
              >
                <IonSelectOption value="">Todas</IonSelectOption>
                {categorias.map((c: any) => (
                  <IonSelectOption key={c.id} value={c.id}>{c.nombre}</IonSelectOption>
                ))}
              </IonSelect>
            </IonItem>

            <IonItem>
              <IonSelect
                label="Modalidad"
                labelPlacement="stacked"
                value={filtros.modalidad}
                onIonChange={e => setFiltros(f => ({ ...f, modalidad: e.detail.value }))}
              >
                <IonSelectOption value="">Todas</IonSelectOption>
                <IonSelectOption value="presencial">Presencial</IonSelectOption>
                <IonSelectOption value="remoto">Remoto</IonSelectOption>
                <IonSelectOption value="hibrido">Hibrido</IonSelectOption>
              </IonSelect>
            </IonItem>

            <IonItem>
              <IonSelect
                label="Ordenar por"
                labelPlacement="stacked"
                value={filtros.orden}
                onIonChange={e => setFiltros(f => ({ ...f, orden: e.detail.value }))}
              >
                {Object.entries(ORDENES).map(([k, v]) => (
                  <IonSelectOption key={k} value={k}>{v}</IonSelectOption>
                ))}
              </IonSelect>
            </IonItem>

            <IonItem>
              <IonInput
                label="Ciudad"
                labelPlacement="stacked"
                placeholder="Ej: Managua"
                value={filtros.ciudad}
                onIonInput={e => setFiltros(f => ({ ...f, ciudad: e.detail.value ?? '' }))}
              />
            </IonItem>

            <IonItem lines="none">
              <IonToggle
                checked={filtros.geo}
                onIonChange={e => (e.detail.checked ? activarGeo() : quitarGeo())}
              >
                <IonLabel>
                  <h3>Cerca de mi ubicacion</h3>
                  <p>{coords ? 'Ubicacion obtenida' : 'Usa tu GPS'}</p>
                </IonLabel>
              </IonToggle>
            </IonItem>

            {filtros.geo && (
              <IonItem>
                <IonRange
                  label={`Radio: ${filtros.radio} km`}
                  labelPlacement="stacked"
                  min={5} max={500} step={5}
                  value={filtros.radio}
                  onIonChange={e => setFiltros(f => ({ ...f, radio: e.detail.value as number }))}
                />
              </IonItem>
            )}
          </IonList>
        </IonContent>
      </IonModal>
    </IonPage>
  );
};

export default Buscar;
