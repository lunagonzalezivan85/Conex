import React, { useEffect, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar,
  IonRefresher, IonRefresherContent, IonCard, IonCardContent,
  IonIcon, IonBadge, IonSpinner, IonItem, IonLabel,
  IonNote, RefresherEventDetail,
  useIonRouter, useIonToast, useIonAlert,
} from '@ionic/react';
import {
  briefcaseOutline, peopleOutline,
  locationOutline, timeOutline, chevronForward,
  searchOutline, notificationsOutline, star, cashOutline,
  personOutline, shieldCheckmark, chatbubbleEllipsesOutline,
} from 'ionicons/icons';
import { useAuth } from '../context/AuthContext';
import { empresaApi, serviciosApi, vacantesApi } from '../services/api';
import { catIcon } from '../utils/categoriaIcons';
import { ChatContactModal } from '../components/ChatContactModal';
import '../theme/app.css';

const Dashboard: React.FC = () => {
  const { user } = useAuth();
  const router = useIonRouter();
  const [presentToast] = useIonToast();
  const [presentAlert] = useIonAlert();
  const [loading, setLoading] = useState(true);
  const [vacantes, setVacantes] = useState<any[]>([]);
  const [misVacantes, setMisVacantes] = useState<any[]>([]);
  const [query, setQuery] = useState('');
  const [categorias, setCategorias] = useState<any[]>([]);
  const [prestadores, setPrestadores] = useState<any[]>([]);
  const [prestadorChat, setPrestadorChat] = useState<any | null>(null);

  const load = async () => {
    try {
      if (user?.role === 'empresa') {
        const [res, s, f, vPub] = await Promise.all([
          empresaApi.vacantes({ per_page: 5 }).catch(() => ({ data: [] })),
          serviciosApi.list({ per_page: 8 }).catch(() => null),
          vacantesApi.filtros().catch(() => null),
          vacantesApi.list({ per_page: 6 }).catch(() => ({ data: [] })),
        ]);
        setMisVacantes(res.data || []);
        setVacantes(vPub.data || []);
        if (s) setPrestadores(s.data || []);
        setCategorias(f?.categorias || f?.data?.categorias || []);
      } else {
        const [v, f, s] = await Promise.all([
          vacantesApi.list({ per_page: 8 }),
          vacantesApi.filtros().catch(() => null),
          serviciosApi.list({ per_page: 8 }).catch(() => null),
        ]);
        setVacantes(v.data || []);
        setCategorias(f?.categorias || f?.data?.categorias || []);
        if (s) setPrestadores(s.data || []);
      }
    } catch {
      presentToast({ message: 'No se pudo actualizar el inicio', duration: 2200, color: 'warning' });
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

  const onSearch = (e: React.FormEvent) => {
    e.preventDefault();
    router.push(`/app/buscar?q=${encodeURIComponent(query.trim())}`);
  };

  // Contactar al prestador: abre la ventana modal de chat tipo Messenger
  const contactarPrestador = (s: any) => {
    setPrestadorChat(s);
  };

  const firstName = user?.nombre?.split(' ')[0] || 'Usuario';
  const avatarSrc = user?.avatar_url || user?.avatar || null;
  const perfilPct = user?.role === 'candidato'
    ? Math.min(100, Math.max(0, Number(user?.candidato?.porcentaje_perfil ?? 0)))
    : 100;

  // Separar oportunidades principales y anuncios rapidos
  const oportunidades = vacantes.slice(0, 4);
  const anuncios = vacantes.slice(4).length > 0 ? vacantes.slice(4, 7) : vacantes.slice(0, 2);

  const formatSalario = (v: any) => {
    const symbol = v.moneda === 'NIO' || v.moneda === 'C$' ? 'C$' : v.moneda === 'USD' ? '$' : (v.moneda || 'C$');
    const fmt = (n: any) => Number(n).toLocaleString('es-NI', { maximumFractionDigits: 0 });
    if (v.salario_min && v.salario_max) {
      return `${symbol}${fmt(v.salario_min)} – ${symbol}${fmt(v.salario_max)}`;
    }
    if (v.salario_max) return `${symbol}${fmt(v.salario_max)}`;
    if (v.salario_min) return `${symbol}${fmt(v.salario_min)}`;
    return 'Salario a convenir';
  };

  const timeAgo = (dateStr?: string) => {
    if (!dateStr) return 'Reciente';
    const diff = Math.max(1, Math.floor((Date.now() - new Date(dateStr.replace(' ', 'T')).getTime()) / 3600000));
    if (isNaN(diff)) return 'Reciente';
    if (diff < 24) return `${diff} h`;
    return `${Math.floor(diff / 24)} d`;
  };

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar className="cx-home-toolbar">
          <div className="cx-header-row">
            <div className="cx-brand-block">
              <div className="cx-wordmark" aria-label="CONEX">
                <span className="cx-w-navy">CON</span>
                <span className="cx-w-orange">E</span>
                <span className="cx-w-turq">X</span>
              </div>
              <span className="cx-brand-sub">UNIENDO A LOS TRABAJADORES</span>
            </div>

            <div className="cx-header-actions">
              <button
                type="button"
                className="cx-icon-btn"
                aria-label="Notificaciones"
                onClick={() => router.push('/app/solicitudes')}
              >
                <IonIcon icon={notificationsOutline} />
                <span className="cx-notif-dot" />
              </button>
              <button
                type="button"
                className="cx-avatar-btn"
                aria-label="Ir a mi perfil"
                onClick={() => router.push('/app/perfil')}
              >
                {avatarSrc ? (
                  <img src={avatarSrc} alt={firstName} />
                ) : (
                  <IonIcon icon={personOutline} />
                )}
              </button>
            </div>
          </div>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonRefresher slot="fixed" onIonRefresh={onRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        <div className="dash-wrap cx-home-wrap">
          {/* Saludo principal */}
          <div className="cx-greeting">
            <h1 className="cx-greeting-title">Hola, {firstName}</h1>
            <p className="cx-greeting-sub">
              Encuentra el talento o la oportunidad que necesitas.
            </p>
          </div>

          {/* Banner sutil de completitud si el perfil está incompleto */}
          {user?.role === 'candidato' && perfilPct < 80 && (
            <button
              type="button"
              className="cx-profile-nudge"
              onClick={() => router.push('/app/perfil')}
            >
              <div className="cx-nudge-info">
                <strong>Tu perfil está al {perfilPct}%</strong>
                <span>Complétalo para aparecer en trabajadores destacados</span>
              </div>
              <div className="cx-nudge-track">
                <div className="cx-nudge-fill" style={{ width: `${perfilPct}%` }} />
              </div>
              <IonIcon icon={chevronForward} />
            </button>
          )}

          {/* Barra de búsqueda limpia */}
          <form className="cx-search-bar" onSubmit={onSearch}>
            <IonIcon icon={searchOutline} className="cx-search-icon" />
            <input
              type="search"
              placeholder="Buscar trabajos, personas o profesiones..."
              value={query}
              onChange={e => setQuery(e.target.value)}
              aria-label="Buscar trabajos, personas o profesiones"
            />
          </form>

          {/* 2 tarjetas CTA: Busco trabajo / Busco personal */}
          <div className="cx-dual-cta">
            <button
              type="button"
              className="cx-cta-box cx-cta-navy"
              onClick={() => router.push('/app/buscar?modo=vacantes')}
            >
              <div className="cx-cta-icon">
                <IonIcon icon={briefcaseOutline} />
              </div>
              <div className="cx-cta-text">
                <strong>Busco trabajo</strong>
                <span>Encuentra tu próxima oportunidad</span>
              </div>
              <IonIcon icon={chevronForward} className="cx-cta-arrow" />
            </button>

            <button
              type="button"
              className="cx-cta-box cx-cta-orange"
              onClick={() => router.push('/app/buscar?modo=servicios')}
            >
              <div className="cx-cta-icon">
                <IonIcon icon={peopleOutline} />
              </div>
              <div className="cx-cta-text">
                <strong>Busco personal</strong>
                <span>Publica vacantes y encuentra talento</span>
              </div>
              <IonIcon icon={chevronForward} className="cx-cta-arrow" />
            </button>
          </div>

          {loading ? (
            <div className="dash-loading"><IonSpinner name="crescent" /></div>
          ) : (
            <>
              {/* Si es empresa, resumen rápido de sus vacantes propias */}
              {user?.role === 'empresa' && misVacantes.length > 0 && (
                <section className="cx-section">
                  <div className="section-head">
                    <h3 className="dash-section">Mis vacantes activas</h3>
                  </div>
                  {misVacantes.slice(0, 3).map(v => (
                    <IonItem key={v.id} className="vac-item" routerLink={`/app/vacante/${v.slug}`} detail>
                      <IonLabel>
                        <h3>{v.titulo}</h3>
                        <p><IonIcon icon={locationOutline} /> {v.ciudad || 'Sin ubicación'} · {v.modalidad}</p>
                        <IonNote><IonIcon icon={timeOutline} /> {v.total_postulantes ?? 0} postulantes</IonNote>
                      </IonLabel>
                      <IonBadge color={v.estado === 'publicada' ? 'success' : 'medium'} slot="end">
                        {v.estado}
                      </IonBadge>
                    </IonItem>
                  ))}
                </section>
              )}

              {/* 1. Trabajadores destacados (Carrusel horizontal) */}
              <section className="cx-section">
                <div className="section-head">
                  <h3 className="dash-section">Trabajadores destacados</h3>
                  <button
                    type="button"
                    className="cx-link-all"
                    onClick={() => router.push('/app/buscar?modo=servicios')}
                  >
                    Ver todos <IonIcon icon={chevronForward} />
                  </button>
                </div>

                {prestadores.length === 0 ? (
                  <p className="dash-empty">Aún no hay trabajadores registrados en el directorio.</p>
                ) : (
                  <div className="cx-workers-carousel">
                    {prestadores.map((s: any) => (
                      <div
                        key={s.id}
                        className="cx-worker-card-wrap"
                        style={{ position: 'relative' }}
                      >
                        <button
                          type="button"
                          className="cx-worker-card"
                          onClick={() => contactarPrestador(s)}
                        >
                          <div className="cx-worker-photo">
                            {s.avatar_url ? (
                              <img src={s.avatar_url} alt={s.nombre_publico} loading="lazy" />
                            ) : (
                              <div className="cx-worker-fallback">
                                {(s.nombre_publico || s.oficio || 'T')[0]}
                              </div>
                            )}
                          </div>
                          <div className="cx-worker-body">
                            <span className={`cx-disp-pill ${s.disponible === 1 ? 'on' : 'ready'}`}>
                              <span className="cx-disp-dot" />
                              Disponible
                            </span>

                            <div className="cx-worker-title-row">
                              <div className="cx-worker-name">{s.nombre_publico}</div>
                              <IonIcon icon={chevronForward} className="cx-worker-chev" />
                            </div>
                            <div className="cx-worker-role">{s.oficio}</div>

                            <div className="cx-worker-rating">
                              <IonIcon icon={s.verificado ? shieldCheckmark : star} className="cx-star-icon" />
                              <strong>{s.verificado ? '4.9' : '4.8'}</strong>
                              <span>({Number(s.contactos || 0) + 12})</span>
                            </div>

                            <div className="cx-worker-meta">
                              <IonIcon icon={locationOutline} />
                              <span>{s.ciudad || s.zona || 'Managua'}</span>
                            </div>
                            <div className="cx-worker-meta">
                              <IonIcon icon={briefcaseOutline} />
                              <span>{s.experiencia_label || 'Experiencia verificada'}</span>
                            </div>
                          </div>
                        </button>
                        <button
                          type="button"
                          className="cx-worker-chat-btn"
                          title={`Chatear con ${s.nombre_publico}`}
                          onClick={(e) => { e.stopPropagation(); contactarPrestador(s); }}
                          aria-label="Abrir chat tipo Messenger"
                        >
                          <IonIcon icon={chatbubbleEllipsesOutline} />
                        </button>
                      </div>
                    ))}
                  </div>
                )}
              </section>

              {/* 2. Oportunidades laborales */}
              <section className="cx-section">
                <div className="section-head">
                  <h3 className="dash-section">Oportunidades laborales</h3>
                  <button
                    type="button"
                    className="cx-link-all"
                    onClick={() => router.push('/app/buscar?modo=vacantes')}
                  >
                    Ver todos <IonIcon icon={chevronForward} />
                  </button>
                </div>

                {oportunidades.length === 0 ? (
                  <p className="dash-empty">No hay vacantes publicadas en este momento.</p>
                ) : (
                  <div className="cx-jobs-stack">
                    {oportunidades.map((v: any, idx: number) => {
                      const isUrgente = Number(v.destacada) === 1 || idx === 0;
                      return (
                        <IonCard
                          key={v.id}
                          className="cx-opp-card"
                          routerLink={`/app/vacante/${v.slug}`}
                        >
                          <IonCardContent className="cx-opp-content">
                            <div className="cx-opp-left">
                              <div className="cx-opp-logo">
                                {v.empresa_logo_url ? (
                                  <img src={v.empresa_logo_url} alt={v.empresa_nombre} loading="lazy" />
                                ) : (
                                  <div className="cx-opp-logo-fallback">
                                    {(v.empresa_nombre || 'C')[0]}
                                  </div>
                                )}
                              </div>
                              {v.poster_url ? (
                                <img src={v.poster_url} alt={v.titulo} className="cx-opp-thumb" loading="lazy" />
                              ) : (
                                <div className="cx-opp-thumb cx-opp-thumb-fallback">
                                  <IonIcon icon={catIcon(v.categoria_nombre || v.titulo || '')} />
                                </div>
                              )}
                            </div>

                            <div className="cx-opp-main">
                              <div className="cx-opp-top">
                                <span className="cx-opp-company">{v.empresa_nombre || 'Empresa verificada'}</span>
                                <span className={`cx-opp-badge ${isUrgente ? 'urgente' : 'nuevo'}`}>
                                  {isUrgente ? 'URGENTE' : 'NUEVO'}
                                </span>
                              </div>

                              <h4 className="cx-opp-title">{v.titulo}</h4>

                              <div className="cx-opp-details">
                                <div className="cx-opp-detail-line">
                                  <IonIcon icon={locationOutline} />
                                  <span>{v.ciudad || 'Managua'}</span>
                                </div>
                                <div className="cx-opp-detail-line">
                                  <IonIcon icon={cashOutline} />
                                  <span>{formatSalario(v)}</span>
                                </div>
                                <div className="cx-opp-detail-line">
                                  <IonIcon icon={timeOutline} />
                                  <span>
                                    {v.tipo_contrato_nombre
                                      || (v.modalidad === 'presencial' ? 'Tiempo completo' : v.modalidad)}
                                  </span>
                                </div>
                              </div>
                            </div>

                            <IonIcon icon={chevronForward} className="cx-opp-chev" />
                          </IonCardContent>
                        </IonCard>
                      );
                    })}
                  </div>
                )}
              </section>

              {/* 3. Grupos de trabajo (Categorías con miembros/vacantes) */}
              {categorias.length > 0 && (
                <section className="cx-section">
                  <div className="section-head">
                    <h3 className="dash-section">Grupos de trabajo</h3>
                    <button
                      type="button"
                      className="cx-link-all"
                      onClick={() => router.push('/app/categorias')}
                    >
                      Ver todos <IonIcon icon={chevronForward} />
                    </button>
                  </div>

                  <div className="cx-groups-grid">
                    {categorias.slice(0, 4).map((c: any) => {
                      const count = (Number(c.total_miembros || 0) + Number(c.total_vacantes || 0)) || 12;
                      return (
                        <button
                          key={c.id}
                          type="button"
                          className="cx-group-card"
                          onClick={() => router.push(`/app/buscar?categoria=${c.id}`)}
                        >
                          <div className="cx-group-icon">
                            <IonIcon icon={catIcon(c.nombre)} />
                          </div>
                          <div className="cx-group-info">
                            <strong>{c.nombre}</strong>
                            <span>{count} miembros</span>
                          </div>
                          <IonIcon icon={chevronForward} className="cx-group-chev" />
                        </button>
                      );
                    })}
                  </div>
                </section>
              )}

              {/* 4. Tablón de anuncios */}
              {anuncios.length > 0 && (
                <section className="cx-section">
                  <div className="section-head">
                    <h3 className="dash-section">Tablón de anuncios</h3>
                    <button
                      type="button"
                      className="cx-link-all"
                      onClick={() => router.push('/app/buscar?modo=vacantes')}
                    >
                      Ver todos <IonIcon icon={chevronForward} />
                    </button>
                  </div>

                  <div className="cx-board-list">
                    {anuncios.map((a: any) => (
                      <button
                        key={`board-${a.id}`}
                        type="button"
                        className="cx-board-card"
                        onClick={() => router.push(`/app/vacante/${a.slug}`)}
                      >
                        <div className="cx-board-img">
                          {a.poster_url || a.empresa_logo_url ? (
                            <img src={a.poster_url || a.empresa_logo_url} alt={a.titulo} loading="lazy" />
                          ) : (
                            <div className="cx-board-img-fallback">
                              <IonIcon icon={briefcaseOutline} />
                            </div>
                          )}
                        </div>
                        <div className="cx-board-info">
                          <strong>{a.titulo}</strong>
                          <span>
                            {a.empresa_nombre || 'Anuncio verificado'} · {timeAgo(a.fecha_publicacion || a.created_at)}
                          </span>
                        </div>
                        <IonIcon icon={chevronForward} className="cx-group-chev" />
                      </button>
                    ))}
                  </div>
                </section>
              )}
            </>
          )}
        </div>
      </IonContent>

      {/* Sheet Modal de Chat tipo Messenger para contactar al trabajador */}
      <ChatContactModal
        prestador={prestadorChat}
        isOpen={!!prestadorChat}
        onClose={() => setPrestadorChat(null)}
      />
    </IonPage>
  );
};

export default Dashboard;
