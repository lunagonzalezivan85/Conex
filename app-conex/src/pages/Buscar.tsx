import React, { useEffect, useRef, useState } from 'react';
import { useLocation } from 'react-router-dom';
import {
  IonContent, IonPage, IonHeader, IonToolbar,
  IonButton, IonIcon, IonModal, IonRange,
  IonCard, IonCardContent, IonAvatar, IonBadge, IonSpinner,
  IonSegment, IonSegmentButton, IonLabel,
  IonInfiniteScroll, IonInfiniteScrollContent, IonRefresher,
  IonRefresherContent, useIonToast, useIonAlert, useIonRouter, RefresherEventDetail,
} from '@ionic/react';
import {
  filterOutline, locationOutline, navigateOutline,
  chevronForward, businessOutline, cashOutline, closeOutline,
  mapOutline, listOutline, appsOutline, searchOutline, constructOutline,
  chatbubbleOutline, chatbubbleEllipsesOutline,
} from 'ionicons/icons';
import { Geolocation } from '@capacitor/geolocation';
import { CAT_STYLES, catIcon } from '../utils/categoriaIcons';
import * as maplibregl from 'maplibre-gl';
import 'maplibre-gl/dist/maplibre-gl.css';
import { vacantesApi, serviciosApi } from '../services/api';
import { useAuth } from '../context/AuthContext';
import { ChatContactModal } from '../components/ChatContactModal';
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
  const [vista, setVista] = useState<'lista' | 'mapa'>('lista');
  const [mapError, setMapError] = useState<string | null>(null);
  const [modo, setModo] = useState<'vacantes' | 'servicios'>('vacantes');
  const [servicios, setServicios] = useState<any[]>([]);
  const [servMeta, setServMeta] = useState<any>({ current_page: 1, last_page: 1, total: 0 });
  const [selectedItem, setSelectedItem] = useState<{
    tipo: 'vacante' | 'servicio';
    data: any;
  } | null>(null);
  const [prestadorChat, setPrestadorChat] = useState<any | null>(null);
  const [presentToast] = useIonToast();
  const [presentAlert] = useIonAlert();
  const { user } = useAuth();
  const router = useIonRouter();
  const debounceRef = useRef<ReturnType<typeof setTimeout>>();
  const location = useLocation();
  const mapDivRef = useRef<HTMLDivElement>(null);
  const mapRef = useRef<maplibregl.Map | null>(null);
  const markersRef = useRef<maplibregl.Marker[]>([]);

  useEffect(() => {
    vacantesApi.filtros()
      .then(r => setCategorias(r.categorias || r.data?.categorias || []))
      .catch(() => {});
  }, []);

  // Deep-link desde el inicio: ?q=, ?categoria=, ?geo=1, ?modo=vacantes|servicios
  useEffect(() => {
    const p = new URLSearchParams(location.search);
    const q = p.get('q');
    const cat = p.get('categoria');
    const m = p.get('modo');
    if (m === 'servicios' || m === 'vacantes') {
      setModo(m);
    }
    if (q !== null || cat !== null) {
      setFiltros(f => ({
        ...f,
        q: q !== null ? q : f.q,
        categoria: cat !== null ? cat : f.categoria,
      }));
    }
    if (p.get('geo') === '1' && !filtros.geo) {
      activarGeo();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [location.search]);

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

  // Directorio de prestadores: q busca en oficio (ej: electricista)
  const cargarServicios = async (page = 1, replace = false) => {
    if (page === 1) setLoading(true);
    try {
      const res = await serviciosApi.list({ q: filtros.q, page, per_page: 15 });
      setServicios(prev => (replace || page === 1 ? res.data : [...prev, ...res.data]));
      setServMeta(res.meta);
    } catch {
      presentToast({ message: 'Error cargando servicios', duration: 2500, color: 'danger' });
    } finally {
      setLoading(false);
    }
  };

  // Recarga al cambiar filtros (debounce para el texto)
  useEffect(() => {
    if (debounceRef.current) clearTimeout(debounceRef.current);
    debounceRef.current = setTimeout(() => {
      if (modo === 'servicios') cargarServicios(1, true);
      else cargar(1, true);
    }, 400);
    return () => { if (debounceRef.current) clearTimeout(debounceRef.current); };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [filtros, coords, modo]);

  const activarGeo = () => {
    presentAlert({
      header: 'Ubicación en CONEX',
      message: 'CONEX utiliza tu ubicación para calcular la distancia a las oportunidades laborales y mostrarte ofertas cercanas en el mapa.',
      buttons: [
        { text: 'Ahora no', role: 'cancel' },
        {
          text: 'Continuar',
          handler: async () => {
            setGeoLoading(true);
            try {
              const pos = await Geolocation.getCurrentPosition({ enableHighAccuracy: true });
              setCoords({ lat: pos.coords.latitude, lng: pos.coords.longitude });
              setFiltros(f => ({ ...f, geo: true, orden: f.orden === 'reciente' ? 'distancia' : f.orden }));
            } catch {
              presentToast({ message: 'No se pudo obtener tu ubicación', duration: 2500, color: 'warning' });
              setFiltros(f => ({ ...f, geo: false }));
            } finally {
              setGeoLoading(false);
            }
          },
        },
      ],
    });
  };

  const quitarGeo = () => {
    setCoords(null);
    setFiltros(f => ({ ...f, geo: false, orden: f.orden === 'distancia' ? 'reciente' : f.orden }));
  };

  const onRefresh = async (e: CustomEvent<RefresherEventDetail>) => {
    if (modo === 'servicios') await cargarServicios(1, true);
    else await cargar(1, true);
    e.detail.complete();
  };

  const cargarMas = async (e: CustomEvent<void>) => {
    const m = modo === 'servicios' ? servMeta : meta;
    if (m.current_page < m.last_page) {
      if (modo === 'servicios') await cargarServicios(m.current_page + 1);
      else await cargar(m.current_page + 1);
    }
    (e.target as HTMLIonInfiniteScrollElement).complete();
  };

  // Contacto al prestador: abre la ventana de chat estilo Messenger
  const contactar = (s: any) => {
    const params = new URLSearchParams({
      servicio_id: String(s.id),
      nombre: s.nombre_publico || '',
      oficio: s.oficio || '',
      precio: s.precio_desde ? String(s.precio_desde) : '',
      avatar: s.avatar_url || '',
      telefono: s.telefono || '',
    });
    router.push(`/app/chat?${params.toString()}`);
  };

  // ---------- Mapa (Leaflet + OSM) ----------
  const ciudadFallback = (ciudad: string): [number, number] => {
    const c = ciudad.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    const map: Record<string, [number, number]> = {
      managua: [12.1149, -86.2362], leon: [12.4315, -86.878], granada: [11.9299, -85.956],
      masaya: [11.9744, -86.0942], chinandega: [12.6296, -87.1311], matagalpa: [12.9276, -85.9157],
      esteli: [13.0918, -86.3539], jinotega: [13.0893, -86.0031], ocotal: [13.632, -86.4751],
      somoto: [13.4769, -86.5814], rivas: [11.4379, -85.8266], 'san juan del sur': [11.2525, -85.8706],
      juigalpa: [12.1011, -85.3678], boaco: [12.4735, -85.6587], bluefields: [12.0137, -83.7635],
      bilwi: [14.0355, -83.3805], tipitapa: [12.1987, -86.0964], nindiri: [12.0033, -86.1209],
      matagalpa2: [12.9276, -85.9157], diriamba: [11.8589, -86.2397], jinotepe: [11.8488, -86.1991],
    };
    return map[c] ?? [12.8654, -85.2072]; // centro de Nicaragua
  };

  // Posicion [lng, lat] (orden MapLibre): coords reales o fallback por ciudad
  const vGeo = (v: any): [number, number] | null => {
    const la = parseFloat(v.latitud); const lo = parseFloat(v.longitud);
    if (!isNaN(la) && !isNaN(lo)) return [lo, la];
    if (v.ciudad) {
      const [la, lo] = ciudadFallback(v.ciudad);
      return [lo, la];
    }
    return null;
  };

  useEffect(() => {
    if (vista !== 'mapa' || !mapDivRef.current) return;
    if (!mapRef.current) {
      try {
        const m = new maplibregl.Map({
          container: mapDivRef.current,
          style: 'https://tiles.openfreemap.org/styles/liberty', // mismo estilo que la web
          center: coords ? [coords.lng, coords.lat] : [-85.2072, 12.8654],
          zoom: coords ? 11 : 7,
          attributionControl: { compact: true },
          // cooperativeGestures NO: en movil su capa captura los toques y los pines no responden
        });
        m.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'bottom-right');
        m.on('error', ev => {
          // eslint-disable-next-line no-console
          console.warn('mapa error:', ev?.error);
          setMapError('No se pudo cargar el mapa');
        });
        m.on('load', () => setMapError(null));
        mapRef.current = m;
      } catch (err) {
        // eslint-disable-next-line no-console
        console.warn('mapa init error:', err);
        setMapError('No se pudo cargar el mapa');
        return;
      }
    }
    const m = mapRef.current;
    if (!m) return;
    setTimeout(() => m.resize(), 60);
    markersRef.current.forEach(mk => mk.remove());
    markersRef.current = [];

    const onMapClick = (e: any) => {
      const orig = e.originalEvent;
      if (orig && (orig.target as HTMLElement)?.closest?.('.cx-marker-wrap')) {
        return;
      }
      setSelectedItem(null);
    };
    m.off('click', onMapClick);
    m.on('click', onMapClick);

    const pinEl = (cls: string, onSelect?: () => void) => {
      const wrap = document.createElement('div');
      wrap.className = 'cx-marker-wrap';
      const pin = document.createElement('div');
      pin.className = cls;
      wrap.appendChild(pin);

      if (onSelect) {
        const trigger = (e: Event) => {
          e.stopPropagation();
          onSelect();
        };
        wrap.addEventListener('click', trigger);
        wrap.addEventListener('touchend', trigger);
      }
      return wrap;
    };

    // Separar pines que caen en la misma posicion (misma ciudad / mismas coords)
    const seen: Record<string, number> = {};
    const jitter = (pos: [number, number]): [number, number] => {
      const key = pos.map(n => n.toFixed(4)).join(',');
      const n = seen[key] ?? 0;
      seen[key] = n + 1;
      if (n === 0) return pos;
      const ang = n * 137.5 * (Math.PI / 180); // espiral dorada
      const r = 0.004 * Math.ceil(n / 6);
      return [pos[0] + r * Math.cos(ang), pos[1] + r * Math.sin(ang)];
    };

    if (modo === 'servicios') {
      // Pines turquesa: solo prestadores 'disponibles ahora' con coords reales
      servicios.forEach(s => {
        if (s.disponible !== 1 || s.lat == null || s.lng == null) return;
        const pos = jitter([Number(s.lng), Number(s.lat)]);
        const popup = new maplibregl.Popup({ offset: 22, closeButton: true, closeOnClick: true })
          .setHTML(
            `<div class="cx-pop"><strong>${s.oficio}</strong>` +
            `<span>${s.nombre_publico} · ${s.zona || s.ciudad || ''}</span>` +
            `<button class="cx-pop-btn" data-sid="${s.id}">Contactar</button></div>`
          );
        popup.on('open', () => {
          popup.getElement().querySelector('.cx-pop-btn')
            ?.addEventListener('click', () => contactar(s), { once: true });
        });
        const onSelect = () => {
          setSelectedItem({ tipo: 'servicio', data: s });
          m.flyTo({ center: pos, zoom: Math.max(m.getZoom(), 12), offset: [0, -70] });
        };
        const el = pinEl('cx-map-pin cx-map-pin-srv', onSelect);
        const mk = new maplibregl.Marker({ element: el })
          .setLngLat(pos).setPopup(popup).addTo(m);
        markersRef.current.push(mk);
      });
      // Centrar en el primer prestador disponible si no hay ubicacion propia
      const first = servicios.find(s => s.disponible === 1 && s.lat != null && s.lng != null);
      if (first && !coords) {
        m.flyTo({ center: [Number(first.lng), Number(first.lat)], zoom: 11 });
      }
    } else {
      vacantes.forEach(v => {
        const pos0 = vGeo(v);
        if (!pos0) return;
        const pos = jitter(pos0);
        const popup = new maplibregl.Popup({ offset: 22, closeButton: true, closeOnClick: true })
          .setHTML(
            `<div class="cx-pop"><strong>${v.titulo}</strong><span>${v.empresa_nombre || ''}</span>` +
            `<button class="cx-pop-btn" data-slug="${v.slug}">Ver vacante</button></div>`
          );
        popup.on('open', () => {
          popup.getElement().querySelector('.cx-pop-btn')
            ?.addEventListener('click', () => router.push(`/app/vacante/${v.slug}`), { once: true });
        });
        const onSelect = () => {
          setSelectedItem({ tipo: 'vacante', data: v });
          m.flyTo({ center: pos, zoom: Math.max(m.getZoom(), 12), offset: [0, -70] });
        };
        const el = pinEl('cx-map-pin', onSelect);
        const mk = new maplibregl.Marker({ element: el })
          .setLngLat(pos).setPopup(popup).addTo(m);
        markersRef.current.push(mk);
      });
    }
    if (coords) {
      const el = pinEl('cx-map-pin cx-map-pin-me');
      const mk = new maplibregl.Marker({ element: el })
        .setLngLat([coords.lng, coords.lat])
        .setPopup(new maplibregl.Popup({ offset: 22, closeButton: true }).setHTML('<div class="cx-pop"><strong>Tu ubicación</strong></div>'))
        .addTo(m);
      markersRef.current.push(mk);
      if (m.getZoom() < 10) m.flyTo({ center: [coords.lng, coords.lat], zoom: 11 });
    }
  }, [vista, vacantes, servicios, coords, modo, router]);

  useEffect(() => {
    setSelectedItem(null);
  }, [modo, vista, filtros]);

  useEffect(() => () => {
    if (mapRef.current) { mapRef.current.remove(); mapRef.current = null; }
  }, []);

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
          {/* Caja de busqueda propia: sin shadow DOM de Ionic, una sola caja */}
          <div className="buscar-searchbox">
            <IonIcon icon={searchOutline} />
            <input
              type="text"
              inputMode="search"
              placeholder={modo === 'servicios' ? 'Buscar servicio: electricista, plomero...' : 'Buscar empleo, empresa...'}
              value={filtros.q}
              onChange={e => setFiltros(f => ({ ...f, q: e.target.value }))}
            />
          </div>
          {modo === 'vacantes' && (
            <div slot="end" className="buscar-filter-wrap">
              <IonButton
                className="buscar-filter-btn"
                onClick={() => setModalOpen(true)}
                aria-label="Filtros"
              >
                <IonIcon icon={filterOutline} slot="icon-only" />
              </IonButton>
              {numFiltros > 0 && <span className="filter-badge">{numFiltros}</span>}
            </div>
          )}
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonRefresher slot="fixed" onIonRefresh={onRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        {/* Toggle Vacantes / Servicios (directorio de prestadores) */}
        <div className="buscar-modo-wrap">
          <IonSegment
            className="dash-segment"
            value={modo}
            onIonChange={e => setModo(e.detail.value as 'vacantes' | 'servicios')}
          >
            <IonSegmentButton value="vacantes">
              <IonLabel>Vacantes</IonLabel>
            </IonSegmentButton>
            <IonSegmentButton value="servicios">
              <IonLabel>Servicios</IonLabel>
            </IonSegmentButton>
          </IonSegment>
        </div>

        {/* Fila de filtros rapidos + toggle lista/mapa (solo vacantes) */}
        <div className="buscar-chips">
          <button
            className={`filter-pill vista-toggle ${vista === 'mapa' ? 'active' : ''}`}
            onClick={() => setVista(v => (v === 'mapa' ? 'lista' : 'mapa'))}
          >
            <IonIcon icon={vista === 'mapa' ? listOutline : mapOutline} />
            {vista === 'mapa' ? 'Lista' : 'Mapa'}
          </button>
          {modo === 'vacantes' && (
            <button
              className={`filter-pill ${filtros.geo ? 'active' : ''}`}
              onClick={filtros.geo ? quitarGeo : activarGeo}
            >
              <IonIcon icon={navigateOutline} />
              {geoLoading ? 'Ubicando...' : filtros.geo ? `Cerca de ti · ${filtros.radio} km` : 'Cerca de ti'}
            </button>
          )}
          {modo === 'vacantes' && filtrosActivos.filter((f): f is string => !!f && f !== `A ${filtros.radio} km`).map((f, i) => (
            <button key={i} className="filter-pill active" onClick={() => setModalOpen(true)}>
              {f}
            </button>
          ))}
          <span className="filter-count">{modo === 'servicios' ? servMeta.total : meta.total} resultados</span>
        </div>

        {/* Vista mapa (vacantes o prestadores disponibles) */}
        <div className="buscar-map" style={{ display: vista === 'mapa' ? 'block' : 'none' }}>
          <div ref={mapDivRef} style={{ width: '100%', height: '100%' }} />
          {modo === 'servicios' && vista === 'mapa' && !mapError && (
            <div className="buscar-map-hint">
              {servicios.some(s => s.disponible === 1)
                ? 'Pines turquesa = prestadores disponibles ahora'
                : 'Nadie esta disponible en el mapa ahora'}
            </div>
          )}
          {mapError && (
            <div className="buscar-map-error">
              <IonIcon icon={mapOutline} />
              <p>{mapError}</p>
              <small>Revisa tu conexion a internet</small>
            </div>
          )}

          {/* Card flotante al seleccionar un marker en el mapa */}
          {selectedItem && (
            <div className="cx-map-card-container">
              {selectedItem.tipo === 'vacante' ? (
                <div className="cx-map-card">
                  <button
                    className="cx-map-card-close"
                    onClick={() => setSelectedItem(null)}
                    aria-label="Cerrar"
                  >
                    <IonIcon icon={closeOutline} />
                  </button>
                  <div className="cx-map-card-tags">
                    {selectedItem.data.destacada && <span className="cx-card-badge badge-urgente">URGENTE</span>}
                    {selectedItem.data.tipo_contrato_nombre && (
                      <span className="cx-card-badge">{selectedItem.data.tipo_contrato_nombre}</span>
                    )}
                    <span className="cx-card-badge">{selectedItem.data.ciudad || 'Nicaragua'}</span>
                  </div>
                  <h4 className="cx-map-card-title">{selectedItem.data.titulo}</h4>
                  <p className="cx-map-card-empresa">{selectedItem.data.empresa_nombre || 'Empresa confidencial'}</p>
                  <div className="cx-map-card-footer">
                    <span className="cx-map-card-salary">
                      {selectedItem.data.salario_formateado || 'Salario a convenir'}
                    </span>
                    <button
                      className="cx-map-card-btn"
                      onClick={() => router.push(`/app/vacante/${selectedItem.data.slug}`)}
                    >
                      Ver vacante
                      <IonIcon icon={chevronForward} />
                    </button>
                  </div>
                </div>
              ) : (
                <div className="cx-map-card">
                  <button
                    className="cx-map-card-close"
                    onClick={() => setSelectedItem(null)}
                    aria-label="Cerrar"
                  >
                    <IonIcon icon={closeOutline} />
                  </button>
                  <div className="cx-map-card-tags">
                    <span className="cx-card-badge badge-disp">● Disponible ahora</span>
                    <span className="cx-card-badge">{selectedItem.data.zona || selectedItem.data.ciudad || 'Managua'}</span>
                  </div>
                  <h4 className="cx-map-card-title">{selectedItem.data.oficio}</h4>
                  <p className="cx-map-card-empresa">
                    {selectedItem.data.nombre_publico}
                    {selectedItem.data.experiencia_label ? ` · ${selectedItem.data.experiencia_label}` : ''}
                  </p>
                  <div className="cx-map-card-footer">
                    <span className="cx-map-card-salary">
                      {selectedItem.data.precio_desde
                        ? `Desde C$${Number(selectedItem.data.precio_desde).toLocaleString()}`
                        : 'Precio a convenir'}
                    </span>
                    <button
                      className="cx-chat-icon-btn"
                      title={`Chatear con ${selectedItem.data.nombre_publico}`}
                      onClick={() => contactar(selectedItem.data)}
                      aria-label="Abrir chat tipo Messenger"
                    >
                      <IonIcon icon={chatbubbleEllipsesOutline} />
                    </button>
                  </div>
                </div>
              )}
            </div>
          )}
        </div>

        {/* Modo servicios: directorio de prestadores por oficio */}
        {modo === 'servicios' && vista === 'lista' && (
          loading ? (
            <div className="dash-loading"><IonSpinner name="crescent" /></div>
          ) : servicios.length === 0 ? (
            <div className="buscar-empty">
              <IonIcon icon={constructOutline} />
              <p>No se encontraron prestadores con esa busqueda</p>
            </div>
          ) : (
            <div className="buscar-list">
              {servicios.map(s => (
                <IonCard key={s.id} className="job-card">
                  <IonCardContent>
                    <div className="job-row">
                      <IonAvatar className="job-logo">
                        {s.avatar_url
                          ? <img src={s.avatar_url} alt={s.nombre_publico} />
                          : <div className="job-logo-fallback">{(s.nombre_publico || s.oficio || 'S')[0]}</div>}
                      </IonAvatar>
                      <div className="job-info">
                        <div className="job-title">{s.oficio}</div>
                        <div className="job-company">{s.nombre_publico}</div>
                        <div className="job-meta">
                          <IonIcon icon={locationOutline} />
                          {s.zona || s.ciudad || 'Tu zona'}
                          {s.precio_desde && (
                            <IonBadge color="primary" className="job-badge">
                              Desde ${s.precio_desde}{s.precio_tipo === 'hora' ? '/h' : ''}
                            </IonBadge>
                          )}
                        </div>
                        {s.disponible === 1 && (
                          <div className="job-meta">
                            <IonBadge className="job-badge-disp">
                              <IonIcon icon={navigateOutline} /> Disponible ahora
                            </IonBadge>
                          </div>
                        )}
                      </div>
                      <button
                        type="button"
                        className="cx-chat-bubble-btn"
                        title={`Chatear con ${s.nombre_publico}`}
                        onClick={e => { e.stopPropagation(); contactar(s); }}
                        aria-label="Chatear con prestador"
                      >
                        <IonIcon icon={chatbubbleEllipsesOutline} />
                      </button>
                    </div>
                  </IonCardContent>
                </IonCard>
              ))}
            </div>
          )
        )}

        {modo === 'vacantes' && vista === 'lista' && (
          loading ? (
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
            <div className="buscar-list">
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
              ))}
            </div>
          )
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

          {/* Categoria: carrusel de iconos como el inicio */}
          <section className="fm-section">
            <h4>Categoria</h4>
            <div className="cat-grid fm-cats">
              <button
                type="button"
                className={`cat-btn ${filtros.categoria === '' ? 'sel' : ''}`}
                onClick={() => setFiltros(f => ({ ...f, categoria: '' }))}
              >
                <span className="cat-icon cat-c6">
                  <IonIcon icon={appsOutline} />
                </span>
                <span className="cat-label">Todas</span>
              </button>
              {categorias.map((c: any, i: number) => (
                <button
                  key={c.id}
                  type="button"
                  className={`cat-btn ${String(filtros.categoria) === String(c.id) ? 'sel' : ''}`}
                  onClick={() => setFiltros(f => ({ ...f, categoria: String(c.id) }))}
                >
                  <span className={`cat-icon ${CAT_STYLES[i % CAT_STYLES.length]}`}>
                    <IonIcon icon={catIcon(c.nombre)} />
                  </span>
                  <span className="cat-label">{c.nombre}</span>
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

      {/* Sheet Modal de Chat tipo Messenger para contactar al prestador */}
      <ChatContactModal
        prestador={prestadorChat}
        isOpen={!!prestadorChat}
        onClose={() => setPrestadorChat(null)}
      />
    </IonPage>
  );
};

export default Buscar;
