import React, { useEffect, useRef, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonSearchbar,
  IonButton, IonIcon, IonModal, IonRange,
  IonCard, IonCardContent, IonAvatar, IonBadge, IonSpinner,
  IonInfiniteScroll, IonInfiniteScrollContent, IonRefresher,
  IonRefresherContent, useIonToast, RefresherEventDetail,
} from '@ionic/react';
import {
  optionsOutline, locationOutline, navigateOutline,
  chevronForward, businessOutline, cashOutline, closeOutline,
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
  const numFiltros = filtrosActivos.length;

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar className="buscar-toolbar">
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
            className="buscar-filter-btn"
            onClick={() => setModalOpen(true)}
            aria-label="Filtros"
          >
            <IonIcon icon={optionsOutline} slot="icon-only" />
            {numFiltros > 0 && <span className="filter-badge">{numFiltros}</span>}
          </IonButton>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonRefresher slot="fixed" onIonRefresh={onRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        {/* Fila de filtros rapidos */}
        <div className="buscar-chips">
          <button
            className={`filter-pill ${filtros.geo ? 'active' : ''}`}
            onClick={filtros.geo ? quitarGeo : activarGeo}
          >
            <IonIcon icon={navigateOutline} />
            {geoLoading ? 'Ubicando...' : filtros.geo ? `Cerca de ti · ${filtros.radio} km` : 'Cerca de ti'}
          </button>
          {filtrosActivos.filter((f): f is string => !!f && f !== `A ${filtros.radio} km`).map((f, i) => (
            <button key={i} className="filter-pill active" onClick={() => setModalOpen(true)}>
              {f}
            </button>
          ))}
          <span className="filter-count">{meta.total} resultados</span>
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
      <IonModal isOpen={modalOpen} onDidDismiss={() => setModalOpen(false)} initialBreakpoint={0.85} breakpoints={[0, 0.85, 1]} className="filtros-modal">
        <IonContent>
          <div className="fm-head">
            <button className="fm-close" onClick={() => setModalOpen(false)} aria-label="Cerrar">
              <IonIcon icon={closeOutline} />
            </button>
            <span className="fm-title">Filtros</span>
            <button className="fm-clear" onClick={() => { setFiltros(f => ({ ...FILTROS_INIT, q: f.q })); quitarGeo(); }}>
              Limpiar
            </button>
          </div>

          {/* Ordenar */}
          <section className="fm-section">
            <h4>Ordenar por</h4>
            <div className="fm-pills">
              {Object.entries(ORDENES).map(([k, v]) => (
                <button
                  key={k}
                  className={`fm-pill ${filtros.orden === k ? 'active' : ''}`}
                  onClick={() => setFiltros(f => ({ ...f, orden: k }))}
                >
                  {v}
                </button>
              ))}
            </div>
          </section>

          {/* Ubicacion */}
          <section className="fm-section">
            <h4>Ubicacion</h4>
            <button
              className={`fm-geo ${filtros.geo ? 'active' : ''}`}
              onClick={filtros.geo ? quitarGeo : activarGeo}
            >
              <IonIcon icon={navigateOutline} />
              <span className="fm-geo-text">
                <strong>{geoLoading ? 'Obteniendo ubicacion...' : filtros.geo ? 'Usando mi ubicacion' : 'Cerca de mi'}</strong>
                <small>{filtros.geo ? `${coords?.lat.toFixed(3)}, ${coords?.lng.toFixed(3)}` : 'GPS del dispositivo'}</small>
              </span>
              <span className={`fm-geo-dot ${filtros.geo ? 'on' : ''}`} />
            </button>
            {filtros.geo && (
              <div className="fm-radio">
                <span className="fm-radio-label">Radio: <strong>{filtros.radio} km</strong></span>
                <IonRange
                  min={5} max={500} step={5}
                  value={filtros.radio}
                  onIonChange={e => setFiltros(f => ({ ...f, radio: e.detail.value as number }))}
                  className="fm-range"
                />
              </div>
            )}
            <div className="fm-field">
              <IonIcon icon={locationOutline} />
              <input
                type="text"
                placeholder="Ciudad (ej: Managua)"
                value={filtros.ciudad}
                onChange={e => setFiltros(f => ({ ...f, ciudad: e.target.value }))}
              />
            </div>
          </section>

          {/* Categoria */}
          <section className="fm-section">
            <h4>Categoria</h4>
            <div className="fm-pills">
              <button
                className={`fm-pill ${filtros.categoria === '' ? 'active' : ''}`}
                onClick={() => setFiltros(f => ({ ...f, categoria: '' }))}
              >
                Todas
              </button>
              {categorias.map((c: any) => (
                <button
                  key={c.id}
                  className={`fm-pill ${String(filtros.categoria) === String(c.id) ? 'active' : ''}`}
                  onClick={() => setFiltros(f => ({ ...f, categoria: String(c.id) }))}
                >
                  {c.nombre}
                </button>
              ))}
            </div>
          </section>

          {/* Modalidad */}
          <section className="fm-section">
            <h4>Modalidad</h4>
            <div className="fm-pills">
              {[['', 'Todas'], ['presencial', 'Presencial'], ['remoto', 'Remoto'], ['hibrido', 'Hibrido']].map(([v, l]) => (
                <button
                  key={v}
                  className={`fm-pill ${filtros.modalidad === v ? 'active' : ''}`}
                  onClick={() => setFiltros(f => ({ ...f, modalidad: v }))}
                >
                  {l}
                </button>
              ))}
            </div>
          </section>

          <div style={{ height: 90 }} />
        </IonContent>

        {/* Footer CTA */}
        <div className="fm-footer">
          <button className="fm-apply" onClick={() => setModalOpen(false)}>
            Ver {meta.total} resultado{meta.total === 1 ? '' : 's'}
          </button>
        </div>
      </IonModal>
    </IonPage>
  );
};

export default Buscar;
