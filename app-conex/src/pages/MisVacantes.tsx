import React, { useEffect, useState } from 'react';
import { useLocation } from 'react-router-dom';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonTitle,
  IonButton, IonIcon, IonBadge, IonSpinner, IonItem, IonLabel,
  IonNote, IonModal, IonInput, IonTextarea, IonSelect, IonSelectOption,
  IonRefresher, IonRefresherContent, RefresherEventDetail,
  useIonToast, useIonAlert, useIonRouter,
} from '@ionic/react';
import {
  briefcaseOutline, addOutline, locationOutline, timeOutline,
  peopleOutline, cashOutline, closeOutline, checkmarkCircleOutline,
  chatbubbleEllipsesOutline, alertCircleOutline, trashOutline,
} from 'ionicons/icons';
import { empresaApi, vacantesApi, ApiError } from '../services/api';
import '../theme/app.css';

const MODALIDADES = [
  { v: 'presencial', t: 'Presencial' },
  { v: 'remoto', t: 'Remoto' },
  { v: 'hibrido', t: 'Híbrido' },
];

const CIUDADES = [
  'Managua', 'León', 'Granada', 'Masaya', 'Chinandega',
  'Matagalpa', 'Estelí', 'Rivas', 'Carazo', 'Chontales',
];

const CATEGORIAS_FALLBACK = [
  { id: 8, nombre: 'Construcción y Obra' },
  { id: 2, nombre: 'Hostelería y Turismo' },
  { id: 9, nombre: 'Transporte y Logística' },
  { id: 5, nombre: 'Ventas y Marketing' },
  { id: 3, nombre: 'Seguridad' },
  { id: 12, nombre: 'Agricultura y Ganadería' },
  { id: 1, nombre: 'Programación y Tecnología' },
  { id: 4, nombre: 'Administración y Finanzas' },
  { id: 6, nombre: 'Salud' },
  { id: 14, nombre: 'Ingeniería' },
  { id: 15, nombre: 'Atención al Cliente' },
  { id: 7, nombre: 'Educación' },
  { id: 10, nombre: 'Diseño y Creatividad' },
  { id: 11, nombre: 'Recursos Humanos' },
  { id: 13, nombre: 'Legal y Jurídico' },
];

const MisVacantes: React.FC = () => {
  const router = useIonRouter();
  const location = useLocation();
  const [presentToast] = useIonToast();
  const [presentAlert] = useIonAlert();

  const [loading, setLoading] = useState(true);
  const [vacantes, setVacantes] = useState<any[]>([]);
  const [categorias, setCategorias] = useState<any[]>(CATEGORIAS_FALLBACK);
  const [deletingId, setDeletingId] = useState<number | null>(null);

  // Modal Crear Vacante
  const [showCrear, setShowCrear] = useState(false);
  const [busyCrear, setBusyCrear] = useState(false);
  const [errorCrear, setErrorCrear] = useState('');

  const [titulo, setTitulo] = useState('');
  const [categoriaId, setCategoriaId] = useState<number | string>('');
  const [modalidad, setModalidad] = useState('presencial');
  const [ciudad, setCiudad] = useState('Managua');
  const [salarioMin, setSalarioMin] = useState('');
  const [salarioMax, setSalarioMax] = useState('');
  const [aniosExp, setAniosExp] = useState('0');
  const [numVacantes, setNumVacantes] = useState('1');
  const [descripcion, setDescripcion] = useState('');
  const [funciones, setFunciones] = useState('');

  // Modal Ver Postulantes
  const [vacanteSel, setVacanteSel] = useState<any | null>(null);
  const [postulantes, setPostulantes] = useState<any[]>([]);
  const [loadingPost, setLoadingPost] = useState(false);

  const cargar = async () => {
    try {
      const [res, f] = await Promise.all([
        empresaApi.vacantes({ per_page: 20 }),
        vacantesApi.filtros().catch(() => null),
      ]);
      setVacantes(res?.data || []);
      const cats = f?.categorias || f?.data?.categorias || [];
      if (cats && cats.length > 0) {
        setCategorias(cats);
      }
    } catch {
      presentToast({ message: 'No se pudieron cargar tus vacantes', duration: 2000, color: 'warning' });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    cargar();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    const params = new URLSearchParams(location.search);
    if (params.get('crear') === '1') {
      setShowCrear(true);
    }
  }, [location.search]);

  const onRefresh = async (e: CustomEvent<RefresherEventDetail>) => {
    await cargar();
    e.detail.complete();
  };

  const handleCrearVacante = async (e?: React.FormEvent) => {
    if (e && e.preventDefault) e.preventDefault();
    if (busyCrear) return;
    setErrorCrear('');

    if (vacantes.length >= 5) {
      setErrorCrear('Has alcanzado el límite de 5 vacantes de tu mes de prueba gratuito. Consulta planes para ampliar.');
      return;
    }
    const t = titulo.trim();
    if (!t || t.length < 3) {
      setErrorCrear('Ingresa un título de al menos 3 caracteres');
      return;
    }
    if (!categoriaId) {
      setErrorCrear('Selecciona una categoría para la vacante');
      return;
    }
    const d = descripcion.trim();
    if (!d || d.length < 10) {
      setErrorCrear('Ingresa una descripción clara de al menos 10 caracteres');
      return;
    }

    const sMin = salarioMin.trim() !== '' ? Number(salarioMin) : null;
    const sMax = salarioMax.trim() !== '' ? Number(salarioMax) : null;

    if (sMin !== null && isNaN(sMin)) {
      setErrorCrear('El salario mínimo debe ser un número válido');
      return;
    }
    if (sMax !== null && isNaN(sMax)) {
      setErrorCrear('El salario máximo debe ser un número válido');
      return;
    }
    if (sMin !== null && sMax !== null && sMax < sMin) {
      setErrorCrear('El salario máximo no puede ser menor al salario mínimo');
      return;
    }

    setBusyCrear(true);
    try {
      await empresaApi.crearVacante({
        titulo: t,
        categoria_id: Number(categoriaId),
        modalidad,
        ciudad,
        salario_min: sMin,
        salario_max: sMax,
        anios_experiencia: Number(aniosExp) || 0,
        vacantes_disponibles: Number(numVacantes) || 1,
        descripcion: d,
        funciones: funciones.trim() || null,
        estado: 'publicada',
      });

      presentToast({ message: '¡Vacante publicada con éxito!', duration: 2500, color: 'success' });
      setShowCrear(false);
      // Reset form
      setTitulo('');
      setDescripcion('');
      setFunciones('');
      setSalarioMin('');
      setSalarioMax('');
      cargar();
    } catch (err) {
      const e = err as ApiError;
      let msg = e.error || 'No se pudo publicar la vacante';
      if (e.errors && typeof e.errors === 'object') {
        const errorList = Object.values(e.errors);
        if (errorList.length > 0) {
          msg = String(errorList[0]);
        }
      }
      setErrorCrear(msg);
      presentToast({ message: msg, duration: 4000, color: 'danger' });
    } finally {
      setBusyCrear(false);
    }
  };

  const confirmarEliminar = (v: any) => {
    presentAlert({
      header: 'Eliminar Vacante',
      subHeader: v.titulo,
      message: '¿Estás seguro de que deseas eliminar esta vacante? Se removerá de las búsquedas y liberará 1 cupo en tu plan.',
      buttons: [
        { text: 'Cancelar', role: 'cancel' },
        {
          text: 'Eliminar',
          role: 'destructive',
          handler: async () => {
            setDeletingId(v.id);
            try {
              await empresaApi.eliminarVacante(v.id);
              presentToast({ message: 'Vacante eliminada correctamente', duration: 2500, color: 'success' });
              await cargar();
            } catch (err: any) {
              presentToast({ message: err?.error || 'No se pudo eliminar la vacante', duration: 3000, color: 'danger' });
            } finally {
              setDeletingId(null);
            }
          },
        },
      ],
    });
  };

  const abrirPostulantes = async (v: any) => {
    setVacanteSel(v);
    setLoadingPost(true);
    try {
      const res = await empresaApi.postulaciones(v.id);
      setPostulantes(res?.data || []);
    } catch {
      // Evitar mensaje de error al cargar postulaciones
      setPostulantes([]);
    } finally {
      setLoadingPost(false);
    }
  };

  const cambiarEstado = async (postulacionId: number, nuevoEstado: string) => {
    try {
      await empresaApi.cambiarEstadoPostulacion(postulacionId, nuevoEstado);
      setPostulantes(prev =>
        prev.map(p => (p.id === postulacionId ? { ...p, estado: nuevoEstado } : p))
      );
      presentToast({ message: `Candidato marcado como "${nuevoEstado}"`, duration: 2000, color: 'success' });
    } catch {
      presentToast({ message: 'No se pudo cambiar el estado', duration: 2000, color: 'danger' });
    }
  };

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar className="cx-home-toolbar">
          <IonTitle style={{ fontWeight: 800, fontSize: 18, color: '#1B2A4A' }}>
            Mis Vacantes
          </IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent className="ion-padding">
        <IonRefresher slot="fixed" onIonRefresh={onRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        {/* Banner de cuota Free Trial */}
        <div
          style={{
            background: 'linear-gradient(135deg, #1B2A4A, #283e6b)',
            color: '#fff',
            padding: '14px 16px',
            borderRadius: 16,
            marginBottom: 18,
            boxShadow: '0 6px 18px rgba(27, 42, 74, 0.25)',
          }}
        >
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 6 }}>
            <span style={{ fontSize: 12, fontWeight: 700, color: '#0FC6C2', textTransform: 'uppercase' }}>
              Mes de Prueba Gratis (USD)
            </span>
            <IonBadge color={vacantes.length >= 5 ? 'warning' : 'success'}>
              {vacantes.length} / 5 vacantes usadas
            </IonBadge>
          </div>
          <div style={{ fontSize: 13, opacity: 0.9 }}>
            Tienes hasta <strong>5 vacantes simultáneas</strong> incluidas durante tus 30 días de prueba.
          </div>
        </div>

        {/* Botón Acción Crear */}
        <div style={{ marginBottom: 18 }}>
          <IonButton
            expand="block"
            className="auth-btn"
            disabled={vacantes.length >= 5}
            onClick={() => setShowCrear(true)}
          >
            <IonIcon icon={addOutline} slot="start" />
            {vacantes.length >= 5 ? 'Límite de prueba alcanzado (5/5)' : 'Publicar Nueva Vacante'}
          </IonButton>
        </div>

        {/* Listado de Vacantes */}
        {loading ? (
          <div style={{ textAlign: 'center', padding: 40 }}>
            <IonSpinner name="crescent" />
            <p style={{ color: '#64748B', fontSize: 13 }}>Cargando vacantes...</p>
          </div>
        ) : vacantes.length === 0 ? (
          <div
            style={{
              textAlign: 'center',
              padding: '40px 20px',
              background: '#F8FAFC',
              borderRadius: 16,
              border: '1.5px dashed #CBD5E1',
            }}
          >
            <IonIcon icon={briefcaseOutline} style={{ fontSize: 48, color: '#94A3B8', marginBottom: 10 }} />
            <h3 style={{ fontSize: 17, fontWeight: 700, color: '#1B2A4A', margin: '0 0 6px' }}>
              No has publicado vacantes aún
            </h3>
            <p style={{ fontSize: 13, color: '#64748B', margin: '0 0 16px' }}>
              Comienza publicando tu primera oferta de trabajo para recibir postulaciones de candidatos verificados.
            </p>
            <IonButton size="small" onClick={() => setShowCrear(true)}>
              Crear mi primera vacante gratis
            </IonButton>
          </div>
        ) : (
          <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
            {vacantes.map(v => (
              <div
                key={v.id}
                style={{
                  background: '#fff',
                  borderRadius: 16,
                  padding: 16,
                  border: '1px solid #E2E8F0',
                  boxShadow: '0 2px 8px rgba(0,0,0,0.04)',
                }}
              >
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 6 }}>
                  <h3 style={{ fontSize: 16, fontWeight: 700, color: '#1B2A4A', margin: 0 }}>
                    {v.titulo}
                  </h3>
                  <IonBadge color={v.estado === 'publicada' ? 'success' : 'medium'}>
                    {v.estado}
                  </IonBadge>
                </div>

                <div style={{ display: 'flex', flexWrap: 'wrap', gap: 12, fontSize: 12, color: '#64748B', marginBottom: 12 }}>
                  <span><IonIcon icon={locationOutline} /> {v.ciudad} ({v.modalidad})</span>
                  {v.salario_max && (
                    <span><IonIcon icon={cashOutline} /> ${v.salario_min || 0} - ${v.salario_max} USD</span>
                  )}
                  <span><IonIcon icon={timeOutline} /> {v.vacantes_disponibles} plaza(s)</span>
                </div>

                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', paddingTop: 10, borderTop: '1px solid #F1F5F9' }}>
                  <span style={{ fontSize: 13, fontWeight: 700, color: '#0F766E' }}>
                    <IonIcon icon={peopleOutline} style={{ verticalAlign: -2 }} /> {v.total_postulantes ?? 0} candidatos postulados
                  </span>
                  <div style={{ display: 'flex', gap: 6, alignItems: 'center' }}>
                    <IonButton
                      size="small"
                      fill="outline"
                      onClick={() => abrirPostulantes(v)}
                      style={{ textTransform: 'none', fontWeight: 600 }}
                    >
                      Ver Postulantes
                    </IonButton>
                    <IonButton
                      size="small"
                      fill="clear"
                      color="danger"
                      disabled={deletingId === v.id}
                      onClick={() => confirmarEliminar(v)}
                      title="Eliminar vacante"
                      style={{ textTransform: 'none', '--padding-start': '6px', '--padding-end': '6px' }}
                    >
                      {deletingId === v.id ? (
                        <IonSpinner name="crescent" style={{ width: 16, height: 16 }} />
                      ) : (
                        <IonIcon icon={trashOutline} slot="icon-only" />
                      )}
                    </IonButton>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}

        {/* Modal Crear Vacante */}
        <IonModal isOpen={showCrear} onDidDismiss={() => setShowCrear(false)}>
          <IonHeader>
            <IonToolbar>
              <IonTitle style={{ fontSize: 17, fontWeight: 700 }}>Nueva Vacante de Empleo</IonTitle>
              <IonButton slot="end" fill="clear" onClick={() => setShowCrear(false)}>
                <IonIcon icon={closeOutline} />
              </IonButton>
            </IonToolbar>
          </IonHeader>

          <IonContent className="ion-padding">
            <form onSubmit={handleCrearVacante}>
              <label style={{ fontSize: 13, fontWeight: 700, color: '#334155', display: 'block', marginBottom: 4 }}>
                Título del puesto o vacante *
              </label>
              <IonItem className="auth-item" lines="none">
                <IonInput
                  placeholder="Ej. Técnico de Refrigeración"
                  value={titulo}
                  onIonInput={e => setTitulo(e.detail.value ?? '')}
                  required
                />
              </IonItem>

              <label style={{ fontSize: 13, fontWeight: 700, color: '#334155', display: 'block', marginBottom: 4 }}>
                Categoría / Rubro *
              </label>
              <IonItem className="auth-item" lines="none">
                <IonSelect
                  placeholder="Selecciona categoría"
                  value={categoriaId}
                  onIonChange={e => setCategoriaId(e.detail.value)}
                >
                  {categorias.map(c => (
                    <IonSelectOption key={c.id} value={c.id}>
                      {c.nombre}
                    </IonSelectOption>
                  ))}
                </IonSelect>
              </IonItem>

              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                <div>
                  <label style={{ fontSize: 13, fontWeight: 700, color: '#334155', display: 'block', marginBottom: 4 }}>
                    Modalidad *
                  </label>
                  <IonItem className="auth-item" lines="none">
                    <IonSelect value={modalidad} onIonChange={e => setModalidad(e.detail.value)}>
                      {MODALIDADES.map(m => (
                        <IonSelectOption key={m.v} value={m.v}>
                          {m.t}
                        </IonSelectOption>
                      ))}
                    </IonSelect>
                  </IonItem>
                </div>
                <div>
                  <label style={{ fontSize: 13, fontWeight: 700, color: '#334155', display: 'block', marginBottom: 4 }}>
                    Ciudad / Ubicación *
                  </label>
                  <IonItem className="auth-item" lines="none">
                    <IonSelect value={ciudad} onIonChange={e => setCiudad(e.detail.value)}>
                      {CIUDADES.map(c => (
                        <IonSelectOption key={c} value={c}>
                          {c}
                        </IonSelectOption>
                      ))}
                    </IonSelect>
                  </IonItem>
                </div>
              </div>

              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10, marginTop: 4 }}>
                <div>
                  <label style={{ fontSize: 13, fontWeight: 600, color: '#64748B', display: 'block', marginBottom: 4 }}>
                    Salario Mín (USD)
                  </label>
                  <IonItem className="auth-item" lines="none">
                    <IonInput
                      type="number"
                      placeholder="Ej. 400"
                      value={salarioMin}
                      onIonInput={e => setSalarioMin(e.detail.value ?? '')}
                    />
                  </IonItem>
                </div>
                <div>
                  <label style={{ fontSize: 13, fontWeight: 600, color: '#64748B', display: 'block', marginBottom: 4 }}>
                    Salario Máx (USD)
                  </label>
                  <IonItem className="auth-item" lines="none">
                    <IonInput
                      type="number"
                      placeholder="Ej. 650"
                      value={salarioMax}
                      onIonInput={e => setSalarioMax(e.detail.value ?? '')}
                    />
                  </IonItem>
                </div>
              </div>
              <small style={{ color: '#94A3B8', fontSize: 11, display: 'block', marginTop: -4, marginBottom: 8 }}>
                Opcional. Valores expresados en dólares (USD).
              </small>

              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', margin: '8px 0 4px' }}>
                <label style={{ fontSize: 13, fontWeight: 700, color: '#334155' }}>
                  Descripción de la vacante y requisitos *
                </label>
                <small style={{ color: descripcion.trim().length >= 10 ? '#0F766E' : '#94A3B8', fontSize: 11 }}>
                  {descripcion.trim().length} / 10 mín.
                </small>
              </div>
              <IonItem className="auth-item" lines="none">
                <IonTextarea
                  rows={4}
                  placeholder="Describe las responsabilidades del puesto, horario y requisitos principales..."
                  value={descripcion}
                  onIonInput={e => setDescripcion(e.detail.value ?? '')}
                  required
                />
              </IonItem>

              {errorCrear && (
                <div
                  style={{
                    color: '#DC2626',
                    backgroundColor: '#FEF2F2',
                    border: '1px solid #FCA5A5',
                    borderRadius: 10,
                    padding: '10px 12px',
                    fontSize: 13,
                    margin: '12px 0 4px',
                    display: 'flex',
                    alignItems: 'center',
                    gap: 8,
                  }}
                >
                  <IonIcon icon={alertCircleOutline} style={{ fontSize: 18, flexShrink: 0 }} />
                  <span>{errorCrear}</span>
                </div>
              )}

              <IonButton
                expand="block"
                type="submit"
                disabled={busyCrear}
                className="auth-btn"
                style={{ marginTop: 16 }}
              >
                {busyCrear ? <IonSpinner name="crescent" /> : 'Publicar Vacante'}
              </IonButton>
            </form>
          </IonContent>
        </IonModal>

        {/* Modal Postulantes */}
        <IonModal isOpen={!!vacanteSel} onDidDismiss={() => setVacanteSel(null)}>
          <IonHeader>
            <IonToolbar>
              <IonTitle style={{ fontSize: 16, fontWeight: 700 }}>
                Postulantes: {vacanteSel?.titulo}
              </IonTitle>
              <IonButton slot="end" fill="clear" onClick={() => setVacanteSel(null)}>
                <IonIcon icon={closeOutline} />
              </IonButton>
            </IonToolbar>
          </IonHeader>

          <IonContent className="ion-padding">
            {loadingPost ? (
              <div style={{ textAlign: 'center', padding: 40 }}><IonSpinner name="crescent" /></div>
            ) : postulantes.length === 0 ? (
              <div style={{ textAlign: 'center', padding: 40, color: '#64748B' }}>
                <IonIcon icon={peopleOutline} style={{ fontSize: 44, color: '#94A3B8' }} />
                <p>Aún no hay candidatos postulados a esta vacante.</p>
              </div>
            ) : (
              <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                {postulantes.map(p => (
                  <div
                    key={p.id}
                    style={{
                      background: '#fff',
                      borderRadius: 14,
                      padding: 14,
                      border: '1px solid #E2E8F0',
                    }}
                  >
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 4 }}>
                      <strong style={{ fontSize: 15, color: '#1B2A4A' }}>
                        {p.nombre} {p.apellido}
                      </strong>
                      <IonBadge color={p.estado === 'contratado' ? 'success' : p.estado === 'rechazada' ? 'danger' : 'primary'}>
                        {p.estado}
                      </IonBadge>
                    </div>

                    <div style={{ fontSize: 13, color: '#64748B', marginBottom: 10 }}>
                      {p.profesion || 'Sin profesión indicada'} · {p.ciudad || 'Ubicación no especificada'}
                    </div>

                    {/* Acciones de reclutamiento */}
                    <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
                      <IonButton
                        size="small"
                        fill="outline"
                        color="success"
                        onClick={() => cambiarEstado(p.id, 'contratado')}
                      >
                        <IonIcon icon={checkmarkCircleOutline} slot="start" /> Marcar Contratado
                      </IonButton>
                      <IonButton
                        size="small"
                        fill="outline"
                        onClick={() => cambiarEstado(p.id, 'en_revision')}
                      >
                        En Revisión
                      </IonButton>
                      <IonButton
                        size="small"
                        fill="outline"
                        color="danger"
                        onClick={() => cambiarEstado(p.id, 'rechazada')}
                      >
                        Descartar
                      </IonButton>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </IonContent>
        </IonModal>
      </IonContent>
    </IonPage>
  );
};

export default MisVacantes;
