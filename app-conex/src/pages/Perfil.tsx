import React, { useEffect, useRef, useState } from 'react';
import {
  IonContent, IonPage, IonHeader, IonToolbar, IonTitle,
  IonList, IonItem, IonLabel, IonIcon, IonButton, IonSpinner,
  IonToggle, IonModal, IonBadge, IonRefresher, IonRefresherContent,
  useIonAlert, useIonToast, useIonRouter, RefresherEventDetail,
} from '@ionic/react';
import {
  personOutline, mailOutline, callOutline, briefcaseOutline,
  locationOutline, linkOutline, logOutOutline, businessOutline,
  documentTextOutline, constructOutline, starOutline, navigateOutline,
  cameraOutline, createOutline, addOutline, trashOutline, closeOutline,
  schoolOutline, sparklesOutline, languageOutline, chevronForward,
  cloudUploadOutline, downloadOutline, shieldCheckmarkOutline, trashBinOutline,
  openOutline,
} from 'ionicons/icons';
import { Geolocation } from '@capacitor/geolocation';
import { useAuth } from '../context/AuthContext';
import { candidatoApi, empresaApi, vacantesApi, usuarioApi, CandidatoPerfilFull } from '../services/api';
import '../theme/app.css';

type EditModalType =
  | null
  | 'personal'
  | 'profesional'
  | 'servicio'
  | 'experiencia'
  | 'educacion'
  | 'habilidades'
  | 'idiomas'
  | 'cvs'
  | 'empresa';

const Perfil: React.FC = () => {
  const { user, logout, refresh } = useAuth();
  const router = useIonRouter();
  const [presentAlert] = useIonAlert();
  const [presentToast] = useIonToast();
  const avatarInputRef = useRef<HTMLInputElement>(null);
  const cvInputRef = useRef<HTMLInputElement>(null);

  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [activeModal, setActiveModal] = useState<EditModalType>(null);

  // Datos completos del candidato
  const [perfilData, setPerfilData] = useState<CandidatoPerfilFull | null>(null);
  const [categorias, setCategorias] = useState<any[]>([]);
  const [catalogoHabilidades, setCatalogoHabilidades] = useState<any[]>([]);

  // Toggles rápidos de prestador
  const [ofrece, setOfrece] = useState(false);
  const [srvSaving, setSrvSaving] = useState(false);
  const [disponible, setDisponible] = useState(false);
  const [dispSaving, setDispSaving] = useState(false);

  // Formularios modales
  const [personalForm, setPersonalForm] = useState({
    nombre: '',
    apellidos: '',
    telefono: '',
    fecha_nacimiento: '',
    ciudad: '',
    region: '',
    direccion: '',
  });

  const [profForm, setProfForm] = useState({
    profesion: '',
    nivel_experiencia_id: '',
    sobre_mi: '',
    disponibilidad: 'inmediata',
    modalidad_preferida: 'presencial',
    salario_esperado_usd: '',
    linkedin_url: '',
    portafolio_url: '',
  });

  const [srvForm, setSrvForm] = useState({
    id: undefined as number | undefined,
    oficio: '',
    descripcion: '',
    precio_desde: '',
    precio_tipo: 'a_convenir' as 'por_hora' | 'por_servicio' | 'a_convenir',
    ciudad: '',
    zona: '',
  });

  const [expForm, setExpForm] = useState({
    id: undefined as number | undefined,
    empresa: '',
    cargo: '',
    descripcion: '',
    fecha_inicio: '',
    fecha_fin: '',
    actual: false,
  });

  const [eduForm, setEduForm] = useState({
    id: undefined as number | undefined,
    nivel_educacion_id: 1,
    institucion: '',
    titulo: '',
    fecha_inicio: '',
    fecha_fin: '',
    en_curso: false,
  });

  const [selectedSkills, setSelectedSkills] = useState<{ habilidad_id: number; nivel: string }[]>([]);
  const [skillCatFilter, setSkillCatFilter] = useState<number | ''>('');
  const [selectedIdiomas, setSelectedIdiomas] = useState<{ idioma_id?: number; nombre: string; nivel: string }[]>([]);

  const [empForm, setEmpForm] = useState({
    razon_social: '',
    ruc: '',
    rubro: '',
    descripcion: '',
    telefono: '',
    ciudad: '',
    region: '',
    direccion: '',
    sitio_web: '',
  });

  const loadFullProfile = async () => {
    if (!user) return;
    setLoading(true);
    try {
      if (user.role === 'candidato') {
        const [pRes, fRes] = await Promise.all([
          candidatoApi.perfil(),
          vacantesApi.filtros().catch(() => null),
        ]);
        const d = pRes.data;
        setPerfilData(d);
        const c = d.candidato || {};
        const u = d.user || user;

        setOfrece(Number(c.ofrece_servicios) === 1);
        setDisponible(Number(c.disponible_ahora) === 1);

        setPersonalForm({
          nombre: u.nombre || '',
          apellidos: c.apellidos || u.apellido || '',
          telefono: u.telefono || '',
          fecha_nacimiento: c.fecha_nacimiento || '',
          ciudad: c.ciudad || '',
          region: c.region || '',
          direccion: c.direccion || '',
        });

        setProfForm({
          profesion: c.profesion || '',
          nivel_experiencia_id: c.nivel_experiencia_id ? String(c.nivel_experiencia_id) : '',
          sobre_mi: c.sobre_mi || '',
          disponibilidad: c.disponibilidad || 'inmediata',
          modalidad_preferida: c.modalidad_preferida || 'presencial',
          salario_esperado_usd: c.salario_esperado_usd ? String(c.salario_esperado_usd) : '',
          linkedin_url: c.linkedin_url || '',
          portafolio_url: c.portafolio_url || '',
        });

        const firstSrv = d.servicios?.[0];
        setSrvForm({
          id: firstSrv?.id,
          oficio: firstSrv?.oficio || c.profesion || '',
          descripcion: firstSrv?.descripcion || c.sobre_mi || '',
          precio_desde: firstSrv?.precio_desde ? String(firstSrv.precio_desde) : '',
          precio_tipo: firstSrv?.precio_tipo || 'a_convenir',
          ciudad: firstSrv?.ciudad || c.ciudad || '',
          zona: firstSrv?.zona || c.zona_servicio || '',
        });

        setSelectedSkills(
          (d.habilidades || []).map((h: any) => ({
            habilidad_id: Number(h.habilidad_id),
            nivel: h.nivel || 'intermedio',
          }))
        );

        setSelectedIdiomas(
          (d.idiomas || []).map((i: any) => ({
            idioma_id: Number(i.idioma_id),
            nombre: i.nombre,
            nivel: i.nivel || 'intermedio',
          }))
        );

        if (fRes) {
          setCategorias(fRes.categorias || fRes.data?.categorias || []);
          setCatalogoHabilidades(fRes.habilidades || fRes.data?.habilidades || []);
        }
      } else if (user.role === 'empresa') {
        const res = await empresaApi.perfil().catch(() => null);
        const emp = res?.data?.empresa || user.empresa || {};
        setEmpForm({
          razon_social: emp.razon_social || '',
          ruc: emp.ruc || '',
          rubro: emp.rubro || '',
          descripcion: emp.descripcion || '',
          telefono: emp.telefono || user.telefono || '',
          ciudad: emp.ciudad || '',
          region: emp.region || '',
          direccion: emp.direccion || '',
          sitio_web: emp.sitio_web || '',
        });
      }
    } catch {
      presentToast({ message: 'No se pudo cargar el perfil completo', duration: 2500, color: 'warning' });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadFullProfile();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user?.id]);

  const onRefresh = async (e: CustomEvent<RefresherEventDetail>) => {
    await Promise.all([refresh(), loadFullProfile()]);
    e.detail.complete();
  };

  /* ---------- Subir Avatar ---------- */
  const onAvatarPicked = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;
    setSaving(true);
    try {
      await candidatoApi.subirAvatar(file);
      presentToast({ message: 'Foto de perfil actualizada', duration: 2200, color: 'success' });
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'Error al subir imagen', duration: 2800, color: 'danger' });
    } finally {
      setSaving(false);
      e.target.value = '';
    }
  };

  /* ---------- Toggles de Servicios ---------- */
  const toggleServicios = async (on: boolean) => {
    setOfrece(on);
    setSrvSaving(true);
    try {
      const res: any = await candidatoApi.actualizarPerfil({ ofrece_servicios: on ? 1 : 0 });
      const saved = Number(res?.candidato?.ofrece_servicios ?? (on ? 1 : 0)) === 1;
      setOfrece(saved);
      await loadFullProfile();
      refresh();
    } catch (err: any) {
      setOfrece(!on);
      presentToast({ message: err?.error || 'No se pudo actualizar', duration: 2400, color: 'danger' });
    } finally {
      setSrvSaving(false);
    }
  };

  const toggleDisponible = async (on: boolean) => {
    if (on) {
      presentAlert({
        header: 'Ubicación de tu Servicio',
        message: 'Para aparecer como disponible ahora en el mapa, CONEX accederá a tu ubicación actual para que los clientes encuentren tu oficio en su zona.',
        buttons: [
          { text: 'Cancelar', role: 'cancel', handler: () => setDisponible(false) },
          {
            text: 'Activar',
            handler: async () => {
              setDispSaving(true);
              try {
                const pos = await Geolocation.getCurrentPosition({ enableHighAccuracy: true });
                const res = await candidatoApi.disponibilidad({ disponible: 1, lat: pos.coords.latitude, lng: pos.coords.longitude });
                setDisponible(Number(res.disponible_ahora) === 1);
                presentToast({ message: 'Ahora apareces disponible en el mapa', duration: 2400, color: 'success' });
              } catch {
                setDisponible(false);
                presentToast({ message: 'Activa el GPS para marcar tu ubicación disponible', duration: 2800, color: 'warning' });
              } finally {
                setDispSaving(false);
              }
            },
          },
        ],
      });
      return;
    }

    setDispSaving(true);
    try {
      const res = await candidatoApi.disponibilidad({ disponible: 0 });
      setDisponible(Number(res.disponible_ahora) === 1);
      presentToast({
        message: 'Disponibilidad desactivada',
        duration: 2400,
      });
    } catch (err: any) {
      setDisponible(true);
      presentToast({
        message: err?.error || 'Error de conexión',
        duration: 2800,
        color: 'warning',
      });
    } finally {
      setDispSaving(false);
    }
  };

  /* ---------- Guardar Datos Personales ---------- */
  const savePersonal = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    try {
      await candidatoApi.actualizarPerfil({
        nombre: personalForm.nombre.trim(),
        apellido: personalForm.apellidos.trim(),
        apellidos: personalForm.apellidos.trim(),
        telefono: personalForm.telefono.trim(),
        fecha_nacimiento: personalForm.fecha_nacimiento || null,
        ciudad: personalForm.ciudad.trim(),
        region: personalForm.region.trim(),
        direccion: personalForm.direccion.trim(),
      });
      presentToast({ message: 'Datos personales guardados', duration: 2200, color: 'success' });
      setActiveModal(null);
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'Revisa los datos ingresados', duration: 2800, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  /* ---------- Guardar Perfil Profesional ---------- */
  const saveProfesional = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    try {
      await candidatoApi.actualizarPerfil({
        profesion: profForm.profesion.trim(),
        nivel_experiencia_id: profForm.nivel_experiencia_id ? Number(profForm.nivel_experiencia_id) : null,
        sobre_mi: profForm.sobre_mi.trim(),
        disponibilidad: profForm.disponibilidad,
        modalidad_preferida: profForm.modalidad_preferida,
        salario_esperado_usd: profForm.salario_esperado_usd ? Number(profForm.salario_esperado_usd) : null,
        linkedin_url: profForm.linkedin_url.trim(),
        portafolio_url: profForm.portafolio_url.trim(),
      });
      presentToast({ message: 'Perfil profesional actualizado', duration: 2200, color: 'success' });
      setActiveModal(null);
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'Revisa los campos (URLs deben iniciar con https://)', duration: 2800, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  /* ---------- Guardar Ficha de Servicio ---------- */
  const saveServicio = async (e: React.FormEvent) => {
    e.preventDefault();
    if (srvForm.oficio.trim().length < 2) {
      presentToast({ message: 'Indica el oficio o servicio que ofreces', duration: 2200, color: 'warning' });
      return;
    }
    setSaving(true);
    try {
      await candidatoApi.guardarServicio({
        id: srvForm.id,
        oficio: srvForm.oficio.trim(),
        descripcion: srvForm.descripcion.trim(),
        precio_desde: srvForm.precio_desde ? Number(srvForm.precio_desde) : null,
        precio_tipo: srvForm.precio_tipo,
        ciudad: srvForm.ciudad.trim(),
        zona: srvForm.zona.trim(),
      });
      presentToast({ message: 'Ficha de servicio guardada', duration: 2200, color: 'success' });
      setActiveModal(null);
      await loadFullProfile();
    } catch (err: any) {
      presentToast({ message: err?.error || 'No se pudo guardar el servicio', duration: 2600, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  /* ---------- Experiencia Laboral ---------- */
  const saveExperiencia = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!expForm.empresa.trim() || !expForm.cargo.trim() || !expForm.fecha_inicio) {
      presentToast({ message: 'Completa empresa, cargo y fecha de inicio', duration: 2400, color: 'warning' });
      return;
    }
    setSaving(true);
    try {
      await candidatoApi.guardarExperiencia({
        id: expForm.id,
        empresa: expForm.empresa.trim(),
        cargo: expForm.cargo.trim(),
        descripcion: expForm.descripcion.trim(),
        fecha_inicio: expForm.fecha_inicio,
        fecha_fin: expForm.actual ? undefined : (expForm.fecha_fin || undefined),
        actual: expForm.actual ? 1 : 0,
      });
      presentToast({ message: 'Experiencia guardada', duration: 2000, color: 'success' });
      setExpForm({ id: undefined, empresa: '', cargo: '', descripcion: '', fecha_inicio: '', fecha_fin: '', actual: false });
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'Error guardando experiencia', duration: 2500, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  const deleteExperiencia = async (id: number) => {
    setSaving(true);
    try {
      await candidatoApi.eliminarExperiencia(id);
      presentToast({ message: 'Experiencia eliminada', duration: 2000, color: 'medium' });
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'No se pudo eliminar', duration: 2400, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  /* ---------- Educación ---------- */
  const saveEducacion = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!eduForm.institucion.trim() || !eduForm.titulo.trim()) {
      presentToast({ message: 'Completa institución y título obtenido', duration: 2400, color: 'warning' });
      return;
    }
    setSaving(true);
    try {
      await candidatoApi.guardarEducacion({
        id: eduForm.id,
        nivel_educacion_id: Number(eduForm.nivel_educacion_id) || 1,
        institucion: eduForm.institucion.trim(),
        titulo: eduForm.titulo.trim(),
        fecha_inicio: eduForm.fecha_inicio || undefined,
        fecha_fin: eduForm.en_curso ? undefined : (eduForm.fecha_fin || undefined),
        en_curso: eduForm.en_curso ? 1 : 0,
      });
      presentToast({ message: 'Estudio guardado', duration: 2000, color: 'success' });
      setEduForm({ id: undefined, nivel_educacion_id: 1, institucion: '', titulo: '', fecha_inicio: '', fecha_fin: '', en_curso: false });
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'Error guardando educación', duration: 2500, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  const deleteEducacion = async (id: number) => {
    setSaving(true);
    try {
      await candidatoApi.eliminarEducacion(id);
      presentToast({ message: 'Estudio eliminado', duration: 2000, color: 'medium' });
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'No se pudo eliminar', duration: 2400, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  /* ---------- Habilidades ---------- */
  const toggleSkill = (id: number) => {
    setSelectedSkills(prev => {
      const exists = prev.some(s => s.habilidad_id === id);
      if (exists) return prev.filter(s => s.habilidad_id !== id);
      return [...prev, { habilidad_id: id, nivel: 'intermedio' }];
    });
  };

  const saveSkills = async () => {
    setSaving(true);
    try {
      await candidatoApi.guardarHabilidades(selectedSkills);
      presentToast({ message: 'Habilidades actualizadas', duration: 2200, color: 'success' });
      setActiveModal(null);
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'Error guardando habilidades', duration: 2500, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  /* ---------- Idiomas ---------- */
  const toggleIdioma = (id: number, nombre: string) => {
    setSelectedIdiomas(prev => {
      const exists = prev.some(i => i.idioma_id === id || i.nombre === nombre);
      if (exists) return prev.filter(i => i.idioma_id !== id && i.nombre !== nombre);
      return [...prev, { idioma_id: id, nombre, nivel: 'intermedio' }];
    });
  };

  const changeIdiomaNivel = (nombre: string, nivel: string) => {
    setSelectedIdiomas(prev => prev.map(i => (i.nombre === nombre ? { ...i, nivel } : i)));
  };

  const saveIdiomas = async () => {
    setSaving(true);
    try {
      await candidatoApi.guardarIdiomas(selectedIdiomas);
      presentToast({ message: 'Idiomas actualizados', duration: 2200, color: 'success' });
      setActiveModal(null);
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'Error guardando idiomas', duration: 2500, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  /* ---------- Subir / Eliminar CV ---------- */
  const onCvPicked = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;
    setSaving(true);
    try {
      await candidatoApi.subirCv(file);
      presentToast({ message: 'CV subido correctamente', duration: 2400, color: 'success' });
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'Error al subir CV (solo PDF/DOCX máx 5MB)', duration: 2800, color: 'danger' });
    } finally {
      setSaving(false);
      e.target.value = '';
    }
  };

  const deleteCv = async (id: number) => {
    setSaving(true);
    try {
      await candidatoApi.eliminarCv(id);
      presentToast({ message: 'CV eliminado', duration: 2000, color: 'medium' });
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'No se pudo eliminar el CV', duration: 2400, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  /* ---------- Guardar Perfil Empresa ---------- */
  const saveEmpresa = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    try {
      await empresaApi.actualizarPerfil(empForm);
      presentToast({ message: 'Perfil de empresa actualizado', duration: 2200, color: 'success' });
      setActiveModal(null);
      await Promise.all([refresh(), loadFullProfile()]);
    } catch (err: any) {
      presentToast({ message: err?.error || 'Revisa los campos de la empresa', duration: 2800, color: 'danger' });
    } finally {
      setSaving(false);
    }
  };

  const doLogout = () => {
    presentAlert({
      header: 'Cerrar sesión',
      message: '¿Quieres cerrar tu sesión?',
      buttons: [
        { text: 'Cancelar', role: 'cancel' },
        {
          text: 'Cerrar sesión',
          role: 'destructive',
          handler: async () => {
            await logout();
          },
        },
      ],
    });
  };

  const doEliminarCuenta = () => {
    presentAlert({
      header: 'Eliminar mi cuenta',
      subHeader: 'Esta acción no se puede deshacer',
      message: 'Al eliminar tu cuenta se borrarán permanentemente tu perfil, currículums, solicitudes y datos asociados. ¿Deseas continuar?',
      buttons: [
        { text: 'Cancelar', role: 'cancel' },
        {
          text: 'Sí, eliminar definitivamente',
          role: 'destructive',
          handler: async () => {
            try {
              await usuarioApi.eliminarCuenta();
              presentToast({ message: 'Tu cuenta ha sido eliminada', duration: 3000, color: 'success' });
              await logout();
            } catch (err: any) {
              presentToast({ message: err?.error || 'No se pudo eliminar la cuenta', duration: 3000, color: 'danger' });
            }
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

  const cand = perfilData?.candidato || user.candidato || {};
  const uInfo = perfilData?.user || user;
  const avatarSrc = uInfo.avatar_url || uInfo.avatar || null;
  const initials = `${(uInfo.nombre || '')[0] || ''}${(uInfo.apellido || '')[0] || ''}`.toUpperCase();
  const porcentaje = user.role === 'candidato' ? Number(cand?.porcentaje_perfil ?? 0) : null;

  const filteredSkills = skillCatFilter
    ? catalogoHabilidades.filter(h => Number(h.categoria_id) === Number(skillCatFilter))
    : catalogoHabilidades;

  return (
    <IonPage>
      <IonHeader translucent>
        <IonToolbar>
          <IonTitle>Mi perfil</IonTitle>
        </IonToolbar>
      </IonHeader>

      <IonContent>
        <IonRefresher slot="fixed" onIonRefresh={onRefresh}>
          <IonRefresherContent />
        </IonRefresher>

        {/* Input oculto para subir foto de perfil */}
        <input
          ref={avatarInputRef}
          type="file"
          accept="image/jpeg,image/png,image/webp"
          style={{ display: 'none' }}
          onChange={onAvatarPicked}
        />

        {/* Input oculto para subir CV */}
        <input
          ref={cvInputRef}
          type="file"
          accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
          style={{ display: 'none' }}
          onChange={onCvPicked}
        />

        {/* Cabecera del perfil con botón de cámara */}
        <div className="perfil-head">
          <div className="perfil-avatar-wrap">
            <div className="perfil-avatar">
              {avatarSrc ? <img src={avatarSrc} alt={uInfo.nombre} /> : initials}
            </div>
            <button
              type="button"
              className="perfil-cam-btn"
              onClick={() => avatarInputRef.current?.click()}
              aria-label="Cambiar foto de perfil"
              disabled={saving}
            >
              <IonIcon icon={cameraOutline} />
            </button>
          </div>
          <h2 className="perfil-name">{uInfo.nombre} {cand.apellidos || uInfo.apellido}</h2>
          <span className="perfil-role">{cand.profesion || user.role}</span>
        </div>

        {/* Barra de completitud interactiva */}
        {porcentaje !== null && (
          <div className="perfil-progress-card">
            <div className="perfil-progress-top">
              <strong>Perfil completado al {porcentaje}%</strong>
              <span>{porcentaje >= 80 ? '¡Excelente perfil!' : 'Completa las secciones de abajo'}</span>
            </div>
            <div className="perfil-progress-track">
              <div
                className="perfil-progress-fill"
                style={{ width: `${Math.min(100, porcentaje)}%` }}
              />
            </div>
          </div>
        )}

        {loading ? (
          <div className="dash-loading"><IonSpinner name="crescent" /></div>
        ) : user.role === 'candidato' ? (
          <div className="perfil-sections-wrap">
            {/* 1. Datos Personales */}
            <div className="pf-section-card">
              <div className="pf-sec-head">
                <div className="pf-sec-title">
                  <IonIcon icon={personOutline} />
                  <h4>Datos personales</h4>
                </div>
                <button type="button" className="pf-edit-btn" onClick={() => setActiveModal('personal')}>
                  <IonIcon icon={createOutline} /> Editar
                </button>
              </div>
              <div className="pf-sec-body">
                <div className="pf-kv"><span>Teléfono:</span> <strong>{uInfo.telefono || 'Sin registrar'}</strong></div>
                <div className="pf-kv"><span>Email:</span> <strong>{uInfo.email}</strong></div>
                <div className="pf-kv">
                  <span>Ubicación:</span>
                  <strong>{[cand.ciudad, cand.region].filter(Boolean).join(', ') || 'Agregar ciudad'}</strong>
                </div>
                {cand.direccion && <div className="pf-kv"><span>Dirección:</span> <strong>{cand.direccion}</strong></div>}
              </div>
            </div>

            {/* 2. Perfil Profesional */}
            <div className="pf-section-card">
              <div className="pf-sec-head">
                <div className="pf-sec-title">
                  <IonIcon icon={briefcaseOutline} />
                  <h4>Perfil profesional</h4>
                </div>
                <button type="button" className="pf-edit-btn" onClick={() => setActiveModal('profesional')}>
                  <IonIcon icon={createOutline} /> {cand.profesion ? 'Editar' : 'Completar'}
                </button>
              </div>
              <div className="pf-sec-body">
                <div className="pf-kv"><span>Profesión / Oficio:</span> <strong>{cand.profesion || 'Sin definir'}</strong></div>
                <div className="pf-kv"><span>Disponibilidad:</span> <strong>{cand.disponibilidad || 'Inmediata'}</strong></div>
                <div className="pf-kv"><span>Modalidad:</span> <strong>{cand.modalidad_preferida || 'Presencial'}</strong></div>
                {cand.salario_esperado_usd && (
                  <div className="pf-kv"><span>Expectativa salarial:</span> <strong>${cand.salario_esperado_usd}</strong></div>
                )}
                {cand.sobre_mi ? (
                  <p className="pf-bio">{cand.sobre_mi}</p>
                ) : (
                  <p className="pf-hint">Agrega una breve descripción sobre tu experiencia y lo que buscas.</p>
                )}
              </div>
            </div>

            {/* 3. Mis Servicios (Directorio de Trabajadores) */}
            <div className="pf-section-card">
              <div className="pf-sec-head">
                <div className="pf-sec-title">
                  <IonIcon icon={constructOutline} />
                  <h4>Directorio de servicios</h4>
                </div>
                <button type="button" className="pf-edit-btn" onClick={() => setActiveModal('servicio')}>
                  <IonIcon icon={createOutline} /> Configurar oficio
                </button>
              </div>

              <IonList lines="none" className="pf-inline-list">
                <IonItem>
                  <IonLabel>
                    <h3>Ofrezco servicios independientes</h3>
                    <p className="srv-toggle-hint">
                      {ofrece ? 'Apareces en Trabajadores Destacados y Contactos' : 'Actívalo para recibir clientes'}
                    </p>
                  </IonLabel>
                  <IonToggle
                    slot="end"
                    className="srv-toggle"
                    checked={ofrece}
                    disabled={srvSaving}
                    onIonChange={e => toggleServicios(e.detail.checked)}
                  />
                </IonItem>

                {ofrece && (
                  <IonItem>
                    <IonIcon icon={navigateOutline} slot="start" color="primary" />
                    <IonLabel>
                      <h3>Disponible ahora en mapa</h3>
                      <p className="srv-toggle-hint">
                        {disponible ? 'Pin verde activo en el mapa' : 'Activa para mostrar tu pin en tiempo real'}
                      </p>
                    </IonLabel>
                    <IonToggle
                      slot="end"
                      className="srv-toggle"
                      checked={disponible}
                      disabled={dispSaving}
                      onIonChange={e => toggleDisponible(e.detail.checked)}
                    />
                  </IonItem>
                )}
              </IonList>

              {perfilData?.servicios && perfilData.servicios.length > 0 && (
                <div className="pf-srv-summary">
                  <strong>{perfilData.servicios[0].oficio}</strong>
                  <span>
                    {perfilData.servicios[0].zona || perfilData.servicios[0].ciudad || 'Managua'} ·{' '}
                    {perfilData.servicios[0].precio_desde
                      ? `Desde $${perfilData.servicios[0].precio_desde}`
                      : 'Precio a convenir'}
                  </span>
                </div>
              )}
            </div>

            {/* 4. Habilidades */}
            <div className="pf-section-card">
              <div className="pf-sec-head">
                <div className="pf-sec-title">
                  <IonIcon icon={sparklesOutline} />
                  <h4>Habilidades ({perfilData?.habilidades?.length || 0})</h4>
                </div>
                <button type="button" className="pf-edit-btn" onClick={() => setActiveModal('habilidades')}>
                  <IonIcon icon={addOutline} /> Elegir
                </button>
              </div>
              <div className="pf-sec-body">
                {perfilData?.habilidades && perfilData.habilidades.length > 0 ? (
                  <div className="pf-tags">
                    {perfilData.habilidades.map((h: any) => (
                      <span key={h.id} className="pf-tag">{h.nombre}</span>
                    ))}
                  </div>
                ) : (
                  <p className="pf-hint">Selecciona tus habilidades técnicas o de oficio (+15% en tu perfil).</p>
                )}
              </div>
            </div>

            {/* 5. Experiencia Laboral */}
            <div className="pf-section-card">
              <div className="pf-sec-head">
                <div className="pf-sec-title">
                  <IonIcon icon={businessOutline} />
                  <h4>Experiencia laboral ({perfilData?.experiencias?.length || 0})</h4>
                </div>
                <button
                  type="button"
                  className="pf-edit-btn"
                  onClick={() => {
                    setExpForm({ id: undefined, empresa: '', cargo: '', descripcion: '', fecha_inicio: '', fecha_fin: '', actual: false });
                    setActiveModal('experiencia');
                  }}
                >
                  <IonIcon icon={addOutline} /> Agregar
                </button>
              </div>
              <div className="pf-sec-body">
                {perfilData?.experiencias && perfilData.experiencias.length > 0 ? (
                  <div className="pf-timeline">
                    {perfilData.experiencias.map((exp: any) => (
                      <div key={exp.id} className="pf-tl-item">
                        <div className="pf-tl-info">
                          <strong>{exp.cargo}</strong>
                          <span>{exp.empresa}</span>
                          <small>
                            {exp.fecha_inicio} — {Number(exp.actual) === 1 ? 'Actualidad' : (exp.fecha_fin || '')}
                          </small>
                        </div>
                        <button
                          type="button"
                          className="pf-del-btn"
                          onClick={() => deleteExperiencia(exp.id)}
                          aria-label="Eliminar experiencia"
                        >
                          <IonIcon icon={trashOutline} />
                        </button>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="pf-hint">Agrega tus trabajos anteriores para mostrar tus años de experiencia (+20%).</p>
                )}
              </div>
            </div>

            {/* 6. Educación */}
            <div className="pf-section-card">
              <div className="pf-sec-head">
                <div className="pf-sec-title">
                  <IonIcon icon={schoolOutline} />
                  <h4>Educación y formación ({perfilData?.educaciones?.length || 0})</h4>
                </div>
                <button
                  type="button"
                  className="pf-edit-btn"
                  onClick={() => {
                    setEduForm({ id: undefined, nivel_educacion_id: 1, institucion: '', titulo: '', fecha_inicio: '', fecha_fin: '', en_curso: false });
                    setActiveModal('educacion');
                  }}
                >
                  <IonIcon icon={addOutline} /> Agregar
                </button>
              </div>
              <div className="pf-sec-body">
                {perfilData?.educaciones && perfilData.educaciones.length > 0 ? (
                  <div className="pf-timeline">
                    {perfilData.educaciones.map((edu: any) => (
                      <div key={edu.id} className="pf-tl-item">
                        <div className="pf-tl-info">
                          <strong>{edu.titulo}</strong>
                          <span>{edu.institucion}</span>
                          <small>{edu.nivel_nombre || 'Formación'}</small>
                        </div>
                        <button
                          type="button"
                          className="pf-del-btn"
                          onClick={() => deleteEducacion(edu.id)}
                          aria-label="Eliminar educación"
                        >
                          <IonIcon icon={trashOutline} />
                        </button>
                      </div>
                    ))}
                  </div>
                ) : (
                  <p className="pf-hint">Agrega tus estudios, cursos técnicos o certificaciones (+15%).</p>
                )}
              </div>
            </div>

            {/* 7. Idiomas y Currículum (CV) */}
            <div className="pf-section-card">
              <div className="pf-sec-head">
                <div className="pf-sec-title">
                  <IonIcon icon={documentTextOutline} />
                  <h4>Currículum (CV) e Idiomas</h4>
                </div>
                <button type="button" className="pf-edit-btn" onClick={() => setActiveModal('cvs')}>
                  <IonIcon icon={cloudUploadOutline} /> Gestionar CVs
                </button>
              </div>
              <div className="pf-sec-body">
                <div className="pf-kv">
                  <span>CVs cargados:</span>
                  <strong>{perfilData?.cvs?.length || 0} archivo(s)</strong>
                </div>
                <div className="pf-kv">
                  <span>Idiomas:</span>
                  <strong>
                    {perfilData?.idiomas && perfilData.idiomas.length > 0
                      ? perfilData.idiomas.map((i: any) => `${i.nombre} (${i.nivel})`).join(', ')
                      : 'Español'}
                  </strong>
                  <button
                    type="button"
                    className="pf-inline-link"
                    onClick={() => setActiveModal('idiomas')}
                  >
                    Editar idiomas
                  </button>
                </div>
              </div>
            </div>

            <div style={{ padding: '4px 0' }}>
              <IonButton expand="block" className="plan-cta" routerLink="/app/planes">
                <IonIcon icon={starOutline} slot="start" />
                Destacar mi perfil / servicio
              </IonButton>
            </div>
          </div>
        ) : (
          /* Vista Perfil Empresa */
          <div className="perfil-sections-wrap">
            <div className="pf-section-card">
              <div className="pf-sec-head">
                <div className="pf-sec-title">
                  <IonIcon icon={businessOutline} />
                  <h4>Datos de la empresa</h4>
                </div>
                <button type="button" className="pf-edit-btn" onClick={() => setActiveModal('empresa')}>
                  <IonIcon icon={createOutline} /> Editar
                </button>
              </div>
              <div className="pf-sec-body">
                <div className="pf-kv"><span>Razón Social:</span> <strong>{empForm.razon_social || 'Sin definir'}</strong></div>
                <div className="pf-kv"><span>RUC:</span> <strong>{empForm.ruc || 'No especificado'}</strong></div>
                <div className="pf-kv"><span>Rubro:</span> <strong>{empForm.rubro || 'No especificado'}</strong></div>
                <div className="pf-kv"><span>Teléfono:</span> <strong>{empForm.telefono || uInfo.telefono || 'No especificado'}</strong></div>
                <div className="pf-kv"><span>Ciudad:</span> <strong>{empForm.ciudad || 'Managua'}</strong></div>
                {empForm.sitio_web && <div className="pf-kv"><span>Sitio web:</span> <strong>{empForm.sitio_web}</strong></div>}
                {empForm.descripcion && <p className="pf-bio">{empForm.descripcion}</p>}
              </div>
            </div>
          </div>
        )}

        <div style={{ padding: '8px 16px 36px', display: 'flex', flexDirection: 'column', gap: '10px' }}>
          <IonButton
            expand="block"
            fill="clear"
            color="medium"
            href="https://conex.softlutionic.com/politica-privacidad"
            target="_blank"
            rel="noopener noreferrer"
            style={{ fontSize: 13 }}
          >
            <IonIcon icon={shieldCheckmarkOutline} slot="start" />
            Política de Privacidad
            <IonIcon icon={openOutline} slot="end" style={{ fontSize: 14 }} />
          </IonButton>

          <IonButton expand="block" color="primary" fill="outline" onClick={doLogout}>
            <IonIcon icon={logOutOutline} slot="start" />
            Cerrar sesión
          </IonButton>

          <IonButton expand="block" color="danger" fill="clear" onClick={doEliminarCuenta} style={{ fontSize: 13, marginTop: 4 }}>
            <IonIcon icon={trashBinOutline} slot="start" />
            Eliminar mi cuenta y datos
          </IonButton>
        </div>

        {/* ==================== MODALES DE EDICIÓN ==================== */}

        {/* Modal 1: Datos Personales */}
        <IonModal isOpen={activeModal === 'personal'} onDidDismiss={() => setActiveModal(null)} initialBreakpoint={0.85} breakpoints={[0, 0.85, 1]} className="filtros-modal">
          <IonContent>
            <div className="fm-head">
              <button className="fm-close" onClick={() => setActiveModal(null)}><IonIcon icon={closeOutline} /></button>
              <span className="fm-title">Datos personales</span>
              <span style={{ width: 36 }} />
            </div>
            <form className="pf-form" onSubmit={savePersonal}>
              <label className="pf-label">Nombre *
                <input type="text" required value={personalForm.nombre} onChange={e => setPersonalForm(f => ({ ...f, nombre: e.target.value }))} />
              </label>
              <label className="pf-label">Apellidos *
                <input type="text" required value={personalForm.apellidos} onChange={e => setPersonalForm(f => ({ ...f, apellidos: e.target.value }))} />
              </label>
              <label className="pf-label">Teléfono / WhatsApp *
                <input type="tel" required value={personalForm.telefono} onChange={e => setPersonalForm(f => ({ ...f, telefono: e.target.value }))} />
              </label>
              <label className="pf-label">Fecha de nacimiento
                <input type="date" value={personalForm.fecha_nacimiento} onChange={e => setPersonalForm(f => ({ ...f, fecha_nacimiento: e.target.value }))} />
              </label>
              <div className="pf-row-2">
                <label className="pf-label">Ciudad
                  <input type="text" placeholder="Ej: Managua" value={personalForm.ciudad} onChange={e => setPersonalForm(f => ({ ...f, ciudad: e.target.value }))} />
                </label>
                <label className="pf-label">Departamento
                  <input type="text" placeholder="Ej: Managua" value={personalForm.region} onChange={e => setPersonalForm(f => ({ ...f, region: e.target.value }))} />
                </label>
              </div>
              <label className="pf-label">Dirección / Barrio
                <input type="text" placeholder="Barrio, reparto o referencia" value={personalForm.direccion} onChange={e => setPersonalForm(f => ({ ...f, direccion: e.target.value }))} />
              </label>
              <button type="submit" className="fm-apply" disabled={saving}>
                {saving ? 'Guardando...' : 'Guardar datos personales'}
              </button>
            </form>
          </IonContent>
        </IonModal>

        {/* Modal 2: Perfil Profesional */}
        <IonModal isOpen={activeModal === 'profesional'} onDidDismiss={() => setActiveModal(null)} initialBreakpoint={0.9} breakpoints={[0, 0.9, 1]} className="filtros-modal">
          <IonContent>
            <div className="fm-head">
              <button className="fm-close" onClick={() => setActiveModal(null)}><IonIcon icon={closeOutline} /></button>
              <span className="fm-title">Perfil profesional</span>
              <span style={{ width: 36 }} />
            </div>
            <form className="pf-form" onSubmit={saveProfesional}>
              <label className="pf-label">Profesión u oficio principal *
                <input
                  type="text"
                  required
                  placeholder="Ej: Albañil, Electricista, Contador..."
                  value={profForm.profesion}
                  onChange={e => setProfForm(f => ({ ...f, profesion: e.target.value }))}
                />
              </label>

              <div className="pf-label">Nivel de experiencia
                <div className="fm-pills">
                  {(perfilData?.catalogos?.niveles_experiencia || []).map(n => (
                    <button
                      type="button"
                      key={n.id}
                      className={`fm-pill ${String(profForm.nivel_experiencia_id) === String(n.id) ? 'active' : ''}`}
                      onClick={() => setProfForm(f => ({ ...f, nivel_experiencia_id: String(n.id) }))}
                    >
                      {n.nombre}
                    </button>
                  ))}
                </div>
              </div>

              <label className="pf-label">Sobre mí (Resumen de experiencia)
                <textarea
                  rows={3}
                  placeholder="Describe tu experiencia, especialidades y herramientas que manejas..."
                  value={profForm.sobre_mi}
                  onChange={e => setProfForm(f => ({ ...f, sobre_mi: e.target.value }))}
                />
              </label>

              <div className="pf-label">Disponibilidad para trabajar
                <div className="fm-pills">
                  {[
                    ['inmediata', 'Inmediata'],
                    ['15_dias', 'En 15 días'],
                    ['30_dias', 'En 30 días'],
                    ['a_convenir', 'A convenir'],
                  ].map(([val, lbl]) => (
                    <button
                      type="button"
                      key={val}
                      className={`fm-pill ${profForm.disponibilidad === val ? 'active' : ''}`}
                      onClick={() => setProfForm(f => ({ ...f, disponibilidad: val }))}
                    >
                      {lbl}
                    </button>
                  ))}
                </div>
              </div>

              <div className="pf-label">Modalidad preferida
                <div className="fm-pills">
                  {[
                    ['presencial', 'Presencial'],
                    ['remoto', 'Remoto'],
                    ['hibrido', 'Híbrido'],
                    ['indiferente', 'Cualquiera'],
                  ].map(([val, lbl]) => (
                    <button
                      type="button"
                      key={val}
                      className={`fm-pill ${profForm.modalidad_preferida === val ? 'active' : ''}`}
                      onClick={() => setProfForm(f => ({ ...f, modalidad_preferida: val }))}
                    >
                      {lbl}
                    </button>
                  ))}
                </div>
              </div>

              <label className="pf-label">Salario mensual esperado (USD opcional)
                <input
                  type="number"
                  placeholder="Ej: 450"
                  value={profForm.salario_esperado_usd}
                  onChange={e => setProfForm(f => ({ ...f, salario_esperado_usd: e.target.value }))}
                />
              </label>

              <label className="pf-label">Enlace a LinkedIn o Portafolio (opcional)
                <input
                  type="url"
                  placeholder="https://..."
                  value={profForm.linkedin_url}
                  onChange={e => setProfForm(f => ({ ...f, linkedin_url: e.target.value }))}
                />
              </label>

              <button type="submit" className="fm-apply" disabled={saving}>
                {saving ? 'Guardando...' : 'Guardar perfil profesional'}
              </button>
            </form>
          </IonContent>
        </IonModal>

        {/* Modal 3: Configurar Servicio de Prestador */}
        <IonModal isOpen={activeModal === 'servicio'} onDidDismiss={() => setActiveModal(null)} initialBreakpoint={0.85} breakpoints={[0, 0.85, 1]} className="filtros-modal">
          <IonContent>
            <div className="fm-head">
              <button className="fm-close" onClick={() => setActiveModal(null)}><IonIcon icon={closeOutline} /></button>
              <span className="fm-title">Mi ficha de servicio</span>
              <span style={{ width: 36 }} />
            </div>
            <form className="pf-form" onSubmit={saveServicio}>
              <label className="pf-label">Oficio o servicio que ofreces *
                <input
                  type="text"
                  required
                  placeholder="Ej: Electricista residencial, Plomero, Limpieza..."
                  value={srvForm.oficio}
                  onChange={e => setSrvForm(f => ({ ...f, oficio: e.target.value }))}
                />
              </label>

              <div className="pf-label">Tipo de tarifa
                <div className="fm-pills">
                  {[
                    ['a_convenir', 'A convenir'],
                    ['por_servicio', 'Por trabajo'],
                    ['por_hora', 'Por hora'],
                  ].map(([val, lbl]) => (
                    <button
                      type="button"
                      key={val}
                      className={`fm-pill ${srvForm.precio_tipo === val ? 'active' : ''}`}
                      onClick={() => setSrvForm(f => ({ ...f, precio_tipo: val as any }))}
                    >
                      {lbl}
                    </button>
                  ))}
                </div>
              </div>

              {srvForm.precio_tipo !== 'a_convenir' && (
                <label className="pf-label">Precio desde ($)
                  <input
                    type="number"
                    placeholder="Ej: 15"
                    value={srvForm.precio_desde}
                    onChange={e => setSrvForm(f => ({ ...f, precio_desde: e.target.value }))}
                  />
                </label>
              )}

              <div className="pf-row-2">
                <label className="pf-label">Ciudad
                  <input
                    type="text"
                    placeholder="Ej: Managua"
                    value={srvForm.ciudad}
                    onChange={e => setSrvForm(f => ({ ...f, ciudad: e.target.value }))}
                  />
                </label>
                <label className="pf-label">Zona de cobertura
                  <input
                    type="text"
                    placeholder="Ej: Carretera Masaya, Bolonia"
                    value={srvForm.zona}
                    onChange={e => setSrvForm(f => ({ ...f, zona: e.target.value }))}
                  />
                </label>
              </div>

              <label className="pf-label">Descripción de tu servicio
                <textarea
                  rows={3}
                  placeholder="Detalla qué trabajos realizas, si cuentas con herramientas propias, garantía, etc."
                  value={srvForm.descripcion}
                  onChange={e => setSrvForm(f => ({ ...f, descripcion: e.target.value }))}
                />
              </label>

              <button type="submit" className="fm-apply" disabled={saving}>
                {saving ? 'Publicando...' : 'Guardar servicio'}
              </button>
            </form>
          </IonContent>
        </IonModal>

        {/* Modal 4: Habilidades */}
        <IonModal isOpen={activeModal === 'habilidades'} onDidDismiss={() => setActiveModal(null)} initialBreakpoint={0.9} breakpoints={[0, 0.9, 1]} className="filtros-modal">
          <IonContent>
            <div className="fm-head">
              <button className="fm-close" onClick={() => setActiveModal(null)}><IonIcon icon={closeOutline} /></button>
              <span className="fm-title">Mis habilidades ({selectedSkills.length})</span>
              <span style={{ width: 36 }} />
            </div>

            <div className="pf-form">
              <div className="pf-label">Filtrar por categoría
                <div className="fm-pills">
                  <button
                    type="button"
                    className={`fm-pill ${skillCatFilter === '' ? 'active' : ''}`}
                    onClick={() => setSkillCatFilter('')}
                  >
                    Todas
                  </button>
                  {categorias.map(c => (
                    <button
                      type="button"
                      key={c.id}
                      className={`fm-pill ${Number(skillCatFilter) === Number(c.id) ? 'active' : ''}`}
                      onClick={() => setSkillCatFilter(Number(c.id))}
                    >
                      {c.nombre}
                    </button>
                  ))}
                </div>
              </div>

              <div className="pf-label">Toca para seleccionar tus habilidades
                <div className="fm-pills">
                  {filteredSkills.map((h: any) => {
                    const active = selectedSkills.some(s => s.habilidad_id === Number(h.id));
                    return (
                      <button
                        type="button"
                        key={h.id}
                        className={`fm-pill ${active ? 'active' : ''}`}
                        onClick={() => toggleSkill(Number(h.id))}
                      >
                        {h.nombre}
                      </button>
                    );
                  })}
                </div>
              </div>

              <button type="button" className="fm-apply" onClick={saveSkills} disabled={saving}>
                {saving ? 'Guardando...' : `Guardar (${selectedSkills.length}) habilidades`}
              </button>
            </div>
          </IonContent>
        </IonModal>

        {/* Modal 5: Experiencia Laboral */}
        <IonModal isOpen={activeModal === 'experiencia'} onDidDismiss={() => setActiveModal(null)} initialBreakpoint={0.85} breakpoints={[0, 0.85, 1]} className="filtros-modal">
          <IonContent>
            <div className="fm-head">
              <button className="fm-close" onClick={() => setActiveModal(null)}><IonIcon icon={closeOutline} /></button>
              <span className="fm-title">Agregar experiencia</span>
              <span style={{ width: 36 }} />
            </div>
            <form className="pf-form" onSubmit={saveExperiencia}>
              <label className="pf-label">Cargo o puesto desempeñado *
                <input
                  type="text"
                  required
                  placeholder="Ej: Maestro de obra, Vendedor..."
                  value={expForm.cargo}
                  onChange={e => setExpForm(f => ({ ...f, cargo: e.target.value }))}
                />
              </label>
              <label className="pf-label">Empresa o proyecto *
                <input
                  type="text"
                  required
                  placeholder="Ej: Constructora Silva / Independiente"
                  value={expForm.empresa}
                  onChange={e => setExpForm(f => ({ ...f, empresa: e.target.value }))}
                />
              </label>
              <div className="pf-row-2">
                <label className="pf-label">Fecha de inicio *
                  <input
                    type="date"
                    required
                    value={expForm.fecha_inicio}
                    onChange={e => setExpForm(f => ({ ...f, fecha_inicio: e.target.value }))}
                  />
                </label>
                {!expForm.actual && (
                  <label className="pf-label">Fecha de fin
                    <input
                      type="date"
                      value={expForm.fecha_fin}
                      onChange={e => setExpForm(f => ({ ...f, fecha_fin: e.target.value }))}
                    />
                  </label>
                )}
              </div>

              <IonItem lines="none" style={{ '--padding-start': 0 }}>
                <IonLabel>Trabajo aquí actualmente</IonLabel>
                <IonToggle
                  checked={expForm.actual}
                  onIonChange={e => setExpForm(f => ({ ...f, actual: e.detail.checked }))}
                />
              </IonItem>

              <label className="pf-label">Funciones realizadas (opcional)
                <textarea
                  rows={3}
                  placeholder="Breve resumen de tus logros o tareas..."
                  value={expForm.descripcion}
                  onChange={e => setExpForm(f => ({ ...f, descripcion: e.target.value }))}
                />
              </label>

              <button type="submit" className="fm-apply" disabled={saving}>
                {saving ? 'Guardando...' : 'Guardar experiencia'}
              </button>
            </form>
          </IonContent>
        </IonModal>

        {/* Modal 6: Educación */}
        <IonModal isOpen={activeModal === 'educacion'} onDidDismiss={() => setActiveModal(null)} initialBreakpoint={0.85} breakpoints={[0, 0.85, 1]} className="filtros-modal">
          <IonContent>
            <div className="fm-head">
              <button className="fm-close" onClick={() => setActiveModal(null)}><IonIcon icon={closeOutline} /></button>
              <span className="fm-title">Agregar formación</span>
              <span style={{ width: 36 }} />
            </div>
            <form className="pf-form" onSubmit={saveEducacion}>
              <div className="pf-label">Nivel educativo
                <div className="fm-pills">
                  {(perfilData?.catalogos?.niveles_educacion || []).map(n => (
                    <button
                      type="button"
                      key={n.id}
                      className={`fm-pill ${Number(eduForm.nivel_educacion_id) === Number(n.id) ? 'active' : ''}`}
                      onClick={() => setEduForm(f => ({ ...f, nivel_educacion_id: Number(n.id) }))}
                    >
                      {n.nombre}
                    </button>
                  ))}
                </div>
              </div>

              <label className="pf-label">Título o certificación *
                <input
                  type="text"
                  required
                  placeholder="Ej: Técnico en Electricidad, Lic. en Contaduría..."
                  value={eduForm.titulo}
                  onChange={e => setEduForm(f => ({ ...f, titulo: e.target.value }))}
                />
              </label>
              <label className="pf-label">Institución o centro *
                <input
                  type="text"
                  required
                  placeholder="Ej: INATEC, UNI, UNAN..."
                  value={eduForm.institucion}
                  onChange={e => setEduForm(f => ({ ...f, institucion: e.target.value }))}
                />
              </label>

              <div className="pf-row-2">
                <label className="pf-label">Fecha de inicio
                  <input
                    type="date"
                    value={eduForm.fecha_inicio}
                    onChange={e => setEduForm(f => ({ ...f, fecha_inicio: e.target.value }))}
                  />
                </label>
                {!eduForm.en_curso && (
                  <label className="pf-label">Fecha de fin
                    <input
                      type="date"
                      value={eduForm.fecha_fin}
                      onChange={e => setEduForm(f => ({ ...f, fecha_fin: e.target.value }))}
                    />
                  </label>
                )}
              </div>

              <IonItem lines="none" style={{ '--padding-start': 0 }}>
                <IonLabel>Cursando actualmente</IonLabel>
                <IonToggle
                  checked={eduForm.en_curso}
                  onIonChange={e => setEduForm(f => ({ ...f, en_curso: e.detail.checked }))}
                />
              </IonItem>

              <button type="submit" className="fm-apply" disabled={saving}>
                {saving ? 'Guardando...' : 'Guardar estudio'}
              </button>
            </form>
          </IonContent>
        </IonModal>

        {/* Modal 7: Gestión de CVs */}
        <IonModal isOpen={activeModal === 'cvs'} onDidDismiss={() => setActiveModal(null)} initialBreakpoint={0.75} breakpoints={[0, 0.75, 1]} className="filtros-modal">
          <IonContent>
            <div className="fm-head">
              <button className="fm-close" onClick={() => setActiveModal(null)}><IonIcon icon={closeOutline} /></button>
              <span className="fm-title">Mis Currículums (CV)</span>
              <span style={{ width: 36 }} />
            </div>

            <div className="pf-form">
              <p className="pf-hint" style={{ marginTop: 0 }}>
                Sube tu CV en formato PDF o Word (máx. 5 MB) para adjuntarlo al postularte a vacantes.
              </p>

              <button
                type="button"
                className="fm-apply"
                onClick={() => cvInputRef.current?.click()}
                disabled={saving}
              >
                <IonIcon icon={cloudUploadOutline} style={{ verticalAlign: '-3px', marginRight: 6 }} />
                {saving ? 'Subiendo archivo...' : 'Subir nuevo CV (PDF / DOCX)'}
              </button>

              <div className="pf-timeline" style={{ marginTop: 18 }}>
                {perfilData?.cvs && perfilData.cvs.length > 0 ? (
                  perfilData.cvs.map((cv: any) => (
                    <div key={cv.id} className="pf-tl-item">
                      <div className="pf-tl-info">
                        <strong>{cv.archivo_nombre}</strong>
                        <small>Subido: {cv.created_at?.slice(0, 10)}</small>
                      </div>
                      <button
                        type="button"
                        className="pf-del-btn"
                        onClick={() => deleteCv(cv.id)}
                        aria-label="Eliminar CV"
                      >
                        <IonIcon icon={trashOutline} />
                      </button>
                    </div>
                  ))
                ) : (
                  <p className="pf-hint">Aún no has subido ningún archivo de CV.</p>
                )}
              </div>
            </div>
          </IonContent>
        </IonModal>

        {/* Modal 8: Idiomas */}
        <IonModal isOpen={activeModal === 'idiomas'} onDidDismiss={() => setActiveModal(null)} initialBreakpoint={0.75} breakpoints={[0, 0.75, 1]} className="filtros-modal">
          <IonContent>
            <div className="fm-head">
              <button className="fm-close" onClick={() => setActiveModal(null)}><IonIcon icon={closeOutline} /></button>
              <span className="fm-title">Idiomas</span>
              <span style={{ width: 36 }} />
            </div>
            <div className="pf-form">
              <div className="pf-label">Selecciona los idiomas que hablas
                <div className="fm-pills">
                  {(perfilData?.catalogos?.idiomas || [
                    { id: 1, nombre: 'Español' },
                    { id: 2, nombre: 'Inglés' },
                    { id: 3, nombre: 'Portugués' },
                    { id: 4, nombre: 'Francés' },
                  ]).map(lang => {
                    const active = selectedIdiomas.some(i => i.idioma_id === Number(lang.id) || i.nombre === lang.nombre);
                    return (
                      <button
                        type="button"
                        key={lang.id}
                        className={`fm-pill ${active ? 'active' : ''}`}
                        onClick={() => toggleIdioma(Number(lang.id), lang.nombre)}
                      >
                        {lang.nombre}
                      </button>
                    );
                  })}
                </div>
              </div>

              {selectedIdiomas.map(lang => (
                <div key={lang.nombre} className="pf-label">
                  Nivel de {lang.nombre}
                  <div className="fm-pills">
                    {['basico', 'intermedio', 'avanzado', 'nativo'].map(niv => (
                      <button
                        type="button"
                        key={niv}
                        className={`fm-pill ${lang.nivel === niv ? 'active' : ''}`}
                        onClick={() => changeIdiomaNivel(lang.nombre, niv)}
                      >
                        {niv}
                      </button>
                    ))}
                  </div>
                </div>
              ))}

              <button type="button" className="fm-apply" onClick={saveIdiomas} disabled={saving}>
                {saving ? 'Guardando...' : 'Guardar idiomas'}
              </button>
            </div>
          </IonContent>
        </IonModal>

        {/* Modal 9: Perfil Empresa */}
        <IonModal isOpen={activeModal === 'empresa'} onDidDismiss={() => setActiveModal(null)} initialBreakpoint={0.85} breakpoints={[0, 0.85, 1]} className="filtros-modal">
          <IonContent>
            <div className="fm-head">
              <button className="fm-close" onClick={() => setActiveModal(null)}><IonIcon icon={closeOutline} /></button>
              <span className="fm-title">Datos de la empresa</span>
              <span style={{ width: 36 }} />
            </div>
            <form className="pf-form" onSubmit={saveEmpresa}>
              <label className="pf-label">Razón Social *
                <input type="text" required value={empForm.razon_social} onChange={e => setEmpForm(f => ({ ...f, razon_social: e.target.value }))} />
              </label>
              <div className="pf-row-2">
                <label className="pf-label">RUC
                  <input type="text" value={empForm.ruc} onChange={e => setEmpForm(f => ({ ...f, ruc: e.target.value }))} />
                </label>
                <label className="pf-label">Rubro / Sector
                  <input type="text" value={empForm.rubro} onChange={e => setEmpForm(f => ({ ...f, rubro: e.target.value }))} />
                </label>
              </div>
              <div className="pf-row-2">
                <label className="pf-label">Teléfono
                  <input type="tel" value={empForm.telefono} onChange={e => setEmpForm(f => ({ ...f, telefono: e.target.value }))} />
                </label>
                <label className="pf-label">Ciudad
                  <input type="text" value={empForm.ciudad} onChange={e => setEmpForm(f => ({ ...f, ciudad: e.target.value }))} />
                </label>
              </div>
              <label className="pf-label">Dirección
                <input type="text" value={empForm.direccion} onChange={e => setEmpForm(f => ({ ...f, direccion: e.target.value }))} />
              </label>
              <label className="pf-label">Sitio Web (https://...)
                <input type="url" value={empForm.sitio_web} onChange={e => setEmpForm(f => ({ ...f, sitio_web: e.target.value }))} />
              </label>
              <label className="pf-label">Descripción de la empresa
                <textarea rows={3} value={empForm.descripcion} onChange={e => setEmpForm(f => ({ ...f, descripcion: e.target.value }))} />
              </label>
              <button type="submit" className="fm-apply" disabled={saving}>
                {saving ? 'Guardando...' : 'Guardar empresa'}
              </button>
            </form>
          </IonContent>
        </IonModal>
      </IonContent>
    </IonPage>
  );
};

export default Perfil;
