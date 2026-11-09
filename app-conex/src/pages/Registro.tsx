import React, { useState } from 'react';
import {
  IonContent, IonPage, IonInput, IonButton, IonIcon, IonText,
  IonSpinner, IonItem, IonToggle, IonModal, IonChip, useIonRouter,
} from '@ionic/react';
import {
  scanOutline, chevronBackOutline, checkmarkCircle,
  flashOutline, personOutline,
} from 'ionicons/icons';
import { authApi, candidatoApi, vacantesApi, saveToken, ApiError } from '../services/api';
import { escanearCedula, formatearCedula, normalizarCedula } from '../services/cedulaScan';
import { useAuth } from '../context/AuthContext';
import '../theme/registro.css';

type Paso = 'cedula' | 'contacto' | 'modo' | 'preguntas' | 'servicio' | 'listo';

interface Habilidad {
  id: number;
  nombre: string;
  categoria_id: number | null;
}

interface Categoria {
  id: number;
  nombre: string;
}

const DISPONIBILIDAD = [
  { v: 'inmediata', t: 'Ahorita' },
  { v: '15_dias', t: 'En 15 dias' },
  { v: '30_dias', t: 'En 30 dias' },
  { v: 'a_convenir', t: 'Cuando sea' },
];

const MODALIDAD = [
  { v: 'presencial', t: 'Presencial' },
  { v: 'remoto', t: 'Remoto' },
  { v: 'hibrido', t: 'Hibrido' },
  { v: 'indiferente', t: 'Cualquiera' },
];

const NIVELES = [
  { v: 'basico', t: 'Basico' },
  { v: 'intermedio', t: 'Intermedio' },
  { v: 'avanzado', t: 'Avanzado' },
];

/**
 * Wizard de registro: el gesto del pitch es escanear la cedula.
 * La cuenta se crea en el paso de contacto; el resto corre autenticado
 * para que un abandono no pierda la cuenta.
 */
const Registro: React.FC = () => {
  const router = useIonRouter();
  const { refresh } = useAuth();

  const [paso, setPaso] = useState<Paso>('cedula');
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const [verTerminos, setVerTerminos] = useState(false);

  // Datos
  const [cedula, setCedula] = useState('');
  const [nombre, setNombre] = useState('');
  const [apellido, setApellido] = useState('');
  const [telefono, setTelefono] = useState('');
  const [email, setEmail] = useState('');
  const [pin, setPin] = useState('');
  const [pin2, setPin2] = useState('');
  const [terminos, setTerminos] = useState(false);
  const [escaneando, setEscaneando] = useState(false);

  const [profesion, setProfesion] = useState('');
  const [ciudad, setCiudad] = useState('');
  const [disponibilidad, setDisponibilidad] = useState('a_convenir');
  const [modalidad, setModalidad] = useState('indiferente');
  const [salario, setSalario] = useState('');

  const [ofrece, setOfrece] = useState(false);
  const [categorias, setCategorias] = useState<Categoria[]>([]);
  const [habilidades, setHabilidades] = useState<Habilidad[]>([]);
  const [categoriaSel, setCategoriaSel] = useState<number | null>(null);
  const [habSel, setHabSel] = useState<Record<number, string>>({});
  const [nivelGeneral, setNivelGeneral] = useState('intermedio');
  const [catalogosListos, setCatalogosListos] = useState(false);

  /* ---------- Paso 1: cedula ---------- */
  const scan = async () => {
    setEscaneando(true);
    setError('');
    try {
      const data = await escanearCedula();
      if (!data) {
        setError('No se pudo escanear. Escribi el numero a mano.');
      } else {
        if (data.cedula) setCedula(formatearCedula(data.cedula));
        if (data.nombres) setNombre(data.nombres);
        if (data.apellidos) setApellido(data.apellidos);
        if (data.municipio) setCiudad(data.municipio);
      }
    } finally {
      setEscaneando(false);
    }
  };

  const validarCedula = () => {
    if (normalizarCedula(cedula).length < 6) {
      setError('Ingresa un numero de cedula valido');
      return;
    }
    if (!nombre.trim() || !apellido.trim()) {
      setError('Escribi tu nombre y apellido');
      return;
    }
    setError('');
    setPaso('contacto');
  };

  /* ---------- Paso 2: contacto + crear cuenta ---------- */
  const crearCuenta = async () => {
    if (telefono.replace(/\D/g, '').length < 8) {
      setError('Ingresa un telefono valido');
      return;
    }
    if (!/^\d{4}$/.test(pin)) {
      setError('El PIN debe ser de 4 numeros');
      return;
    }
    if (pin !== pin2) {
      setError('Los PIN no coinciden');
      return;
    }
    if (!terminos) {
      setError('Debes aceptar los terminos y condiciones');
      return;
    }

    setBusy(true);
    setError('');
    try {
      const res = await authApi.registerApp({
        cedula: normalizarCedula(cedula),
        nombre: nombre.trim(),
        apellido: apellido.trim(),
        telefono: telefono.trim(),
        pin,
        email: email.trim() || undefined,
        terminos: 1,
      });
      await saveToken(res.token);
      await refresh();
      setPaso('modo');
    } catch (err) {
      const e = err as ApiError;
      if (e.status === 422 && e.errors?.cedula) {
        setError('Esa cedula ya tiene cuenta. Inicia sesion.');
      } else if (e.status === 422 && e.errors) {
        setError(Object.values(e.errors)[0]);
      } else {
        setError(e.error || 'No se pudo crear la cuenta');
      }
    } finally {
      setBusy(false);
    }
  };

  /* ---------- Paso 4: preguntas (modo rapido) ---------- */
  const guardarPreguntas = async () => {
    setBusy(true);
    setError('');
    try {
      const body: Record<string, any> = {};
      if (profesion.trim()) body.profesion = profesion.trim();
      if (ciudad.trim()) body.ciudad = ciudad.trim();
      body.disponibilidad = disponibilidad;
      body.modalidad_preferida = modalidad;
      if (salario && !isNaN(Number(salario))) {
        body.salario_esperado_usd = Number(salario);
      }
      await candidatoApi.actualizarPerfil(body);
      await cargarCatalogos();
      setPaso('servicio');
    } catch (err) {
      const e = err as ApiError;
      setError(e.error || 'No se pudo guardar. Intenta de nuevo.');
    } finally {
      setBusy(false);
    }
  };

  /* ---------- Paso 5: servicio opt-in ---------- */
  const cargarCatalogos = async () => {
    if (catalogosListos) return;
    try {
      const res = await vacantesApi.filtros();
      setCategorias(res.categorias ?? []);
      setHabilidades(res.habilidades ?? []);
      setCatalogosListos(true);
    } catch {
      // si falla, el paso se muestra solo con el toggle
    }
  };

  const guardarServicio = async () => {
    setBusy(true);
    setError('');
    try {
      if (ofrece) {
        await candidatoApi.actualizarPerfil({ ofrece_servicios: 1 });
        const lista = Object.keys(habSel).map(id => ({
          habilidad_id: Number(id),
          nivel: habSel[Number(id)] || nivelGeneral,
        }));
        if (lista.length > 0) {
          await candidatoApi.guardarHabilidades(lista);
        }
      }
      setPaso('listo');
    } catch (err) {
      const e = err as ApiError;
      setError(e.error || 'No se pudo guardar');
    } finally {
      setBusy(false);
    }
  };

  const toggleHab = (id: number) => {
    setHabSel(prev => {
      const next = { ...prev };
      if (next[id]) delete next[id];
      else next[id] = nivelGeneral;
      return next;
    });
  };

  const volverLogin = () => router.push('/login', 'back');
  const irDashboard = () => router.push('/app/dashboard', 'root', 'replace');
  const irPerfil = () => router.push('/app/perfil', 'root', 'replace');

  return (
    <IonPage>
      <IonContent>
        <div className="reg-wrap">
          <div className="reg-card">

            {paso === 'cedula' && (
              <>
                <div className="auth-logo">C<span>x</span></div>
                <h1 className="reg-title">Tu cedula, tu perfil</h1>
                <p className="reg-sub">Apunta la camara a tu cedula y arrancamos</p>

                <IonButton
                  expand="block"
                  onClick={scan}
                  disabled={escaneando}
                  className="reg-scan-btn"
                >
                  {escaneando
                    ? <IonSpinner name="crescent" />
                    : <><IonIcon icon={scanOutline} slot="start" /> Escanear cedula</>}
                </IonButton>

                <div className="reg-divider"><span>o escribilo a mano</span></div>

                <IonItem className="auth-item" lines="full">
                  <IonInput
                    label="Numero de cedula"
                    labelPlacement="floating"
                    inputmode="numeric"
                    value={cedula}
                    onIonInput={e => setCedula(e.detail.value ?? '')}
                  />
                </IonItem>
                <IonItem className="auth-item" lines="full">
                  <IonInput
                    label="Nombres"
                    labelPlacement="floating"
                    value={nombre}
                    onIonInput={e => setNombre(e.detail.value ?? '')}
                  />
                </IonItem>
                <IonItem className="auth-item" lines="full">
                  <IonInput
                    label="Apellidos"
                    labelPlacement="floating"
                    value={apellido}
                    onIonInput={e => setApellido(e.detail.value ?? '')}
                  />
                </IonItem>

                {error && <IonText color="danger" className="auth-error"><small>{error}</small></IonText>}

                <IonButton expand="block" onClick={validarCedula} className="auth-btn">
                  Continuar
                </IonButton>
                <IonButton fill="clear" size="small" onClick={volverLogin}>
                  Ya tengo cuenta
                </IonButton>
              </>
            )}

            {paso === 'contacto' && (
              <>
                <h2 className="reg-title">Tu contacto</h2>
                <p className="reg-sub">Con tu telefono y un PIN entras siempre</p>

                <IonItem className="auth-item" lines="full">
                  <IonInput
                    label="Telefono (WhatsApp)"
                    labelPlacement="floating"
                    inputmode="tel"
                    value={telefono}
                    onIonInput={e => setTelefono(e.detail.value ?? '')}
                  />
                </IonItem>
                <IonItem className="auth-item" lines="full">
                  <IonInput
                    label="Correo (opcional)"
                    labelPlacement="floating"
                    type="email"
                    value={email}
                    onIonInput={e => setEmail(e.detail.value ?? '')}
                  />
                </IonItem>
                <IonItem className="auth-item" lines="full">
                  <IonInput
                    label="PIN de 4 numeros"
                    labelPlacement="floating"
                    type="password"
                    inputmode="numeric"
                    maxlength={4}
                    value={pin}
                    onIonInput={e => setPin(e.detail.value ?? '')}
                  />
                </IonItem>
                <IonItem className="auth-item" lines="full">
                  <IonInput
                    label="Repeti tu PIN"
                    labelPlacement="floating"
                    type="password"
                    inputmode="numeric"
                    maxlength={4}
                    value={pin2}
                    onIonInput={e => setPin2(e.detail.value ?? '')}
                  />
                </IonItem>

                <div className="reg-terminos">
                  <IonToggle
                    checked={terminos}
                    onIonChange={e => setTerminos(e.detail.checked)}
                    labelPlacement="end"
                  >
                    Acepto los{' '}
                    <span className="reg-link" onClick={() => setVerTerminos(true)}>
                      términos y política de privacidad
                    </span>
                  </IonToggle>
                </div>

                {error && <IonText color="danger" className="auth-error"><small>{error}</small></IonText>}

                <IonButton expand="block" onClick={crearCuenta} disabled={busy} className="auth-btn">
                  {busy ? <IonSpinner name="crescent" /> : 'Crear mi cuenta'}
                </IonButton>
                <IonButton fill="clear" size="small" onClick={() => setPaso('cedula')}>
                  <IonIcon icon={chevronBackOutline} slot="start" /> Atras
                </IonButton>
              </>
            )}

            {paso === 'modo' && (
              <>
                <IonIcon icon={checkmarkCircle} className="reg-ok-icon" />
                <h2 className="reg-title">Cuenta creada</h2>
                <p className="reg-sub">¿Como queres armar tu perfil?</p>

                <div className="reg-modo-card" onClick={() => cargarCatalogos().then(() => setPaso('preguntas'))}>
                  <IonIcon icon={flashOutline} />
                  <div>
                    <strong>Rapido</strong>
                    <p>4 preguntas y listo. Te las hago yo.</p>
                  </div>
                </div>

                <div className="reg-modo-card" onClick={irPerfil}>
                  <IonIcon icon={personOutline} />
                  <div>
                    <strong>Profesional</strong>
                    <p>Lo lleno yo mismo, completo.</p>
                  </div>
                </div>

                <IonButton fill="clear" size="small" onClick={irDashboard}>
                  Lo hago despues
                </IonButton>
              </>
            )}

            {paso === 'preguntas' && (
              <>
                <h2 className="reg-title">Contanos de vos</h2>
                <p className="reg-sub">Con esto ya te podemos buscar trabajo</p>

                <IonItem className="auth-item" lines="full">
                  <IonInput
                    label="¿A que te dedicas?"
                    labelPlacement="floating"
                    placeholder="Ej: albanil, cocinera, electricista"
                    value={profesion}
                    onIonInput={e => setProfesion(e.detail.value ?? '')}
                  />
                </IonItem>
                <IonItem className="auth-item" lines="full">
                  <IonInput
                    label="¿En que ciudad?"
                    labelPlacement="floating"
                    value={ciudad}
                    onIonInput={e => setCiudad(e.detail.value ?? '')}
                  />
                </IonItem>
                <IonItem className="auth-item" lines="full">
                  <IonInput
                    label="¿Cuanto esperas ganar? ($/mes)"
                    labelPlacement="floating"
                    inputmode="numeric"
                    value={salario}
                    onIonInput={e => setSalario(e.detail.value ?? '')}
                  />
                </IonItem>

                <p className="reg-label">¿Cuando podes empezar?</p>
                <div className="reg-chips">
                  {DISPONIBILIDAD.map(d => (
                    <IonChip
                      key={d.v}
                      color={disponibilidad === d.v ? 'primary' : 'medium'}
                      outline={disponibilidad !== d.v}
                      onClick={() => setDisponibilidad(d.v)}
                    >
                      {d.t}
                    </IonChip>
                  ))}
                </div>

                <p className="reg-label">¿Como preferis trabajar?</p>
                <div className="reg-chips">
                  {MODALIDAD.map(m => (
                    <IonChip
                      key={m.v}
                      color={modalidad === m.v ? 'primary' : 'medium'}
                      outline={modalidad !== m.v}
                      onClick={() => setModalidad(m.v)}
                    >
                      {m.t}
                    </IonChip>
                  ))}
                </div>

                {error && <IonText color="danger" className="auth-error"><small>{error}</small></IonText>}

                <IonButton expand="block" onClick={guardarPreguntas} disabled={busy} className="auth-btn">
                  {busy ? <IonSpinner name="crescent" /> : 'Guardar'}
                </IonButton>
                <IonButton fill="clear" size="small" onClick={() => setPaso('servicio')}>
                  Saltar
                </IonButton>
              </>
            )}

            {paso === 'servicio' && (
              <>
                <h2 className="reg-title">Una ultima</h2>
                <p className="reg-sub">¿Tambien ofreces algun servicio? (ej: electricista, peluquera)</p>

                <IonItem className="auth-item" lines="none">
                  <IonToggle
                    checked={ofrece}
                    onIonChange={e => setOfrece(e.detail.checked)}
                    labelPlacement="end"
                  >
                    Si, ofrezco servicios
                  </IonToggle>
                </IonItem>

                {ofrece && (
                  <>
                    <p className="reg-label">¿De que area?</p>
                    <div className="reg-chips">
                      {categorias.map(c => (
                        <IonChip
                          key={c.id}
                          color={categoriaSel === c.id ? 'primary' : 'medium'}
                          outline={categoriaSel !== c.id}
                          onClick={() => setCategoriaSel(c.id)}
                        >
                          {c.nombre}
                        </IonChip>
                      ))}
                    </div>

                    {categoriaSel && (
                      <>
                        <p className="reg-label">Marca lo que sabes hacer</p>
                        <div className="reg-chips">
                          {habilidades
                            .filter(h => h.categoria_id === categoriaSel)
                            .map(h => (
                              <IonChip
                                key={h.id}
                                color={habSel[h.id] ? 'success' : 'medium'}
                                outline={!habSel[h.id]}
                                onClick={() => toggleHab(h.id)}
                              >
                                {h.nombre}
                              </IonChip>
                            ))}
                        </div>

                        <p className="reg-label">Tu nivel general</p>
                        <div className="reg-chips">
                          {NIVELES.map(n => (
                            <IonChip
                              key={n.v}
                              color={nivelGeneral === n.v ? 'primary' : 'medium'}
                              outline={nivelGeneral !== n.v}
                              onClick={() => setNivelGeneral(n.v)}
                            >
                              {n.t}
                            </IonChip>
                          ))}
                        </div>
                      </>
                    )}
                  </>
                )}

                {error && <IonText color="danger" className="auth-error"><small>{error}</small></IonText>}

                <IonButton expand="block" onClick={guardarServicio} disabled={busy} className="auth-btn">
                  {busy ? <IonSpinner name="crescent" /> : 'Terminar'}
                </IonButton>
              </>
            )}

            {paso === 'listo' && (
              <>
                <IonIcon icon={checkmarkCircle} className="reg-ok-icon" />
                <h2 className="reg-title">Listo</h2>
                <p className="reg-sub">
                  Tu perfil ya esta buscandote trabajo.
                  Entras con tu cedula y tu PIN.
                </p>
                <IonButton expand="block" onClick={irDashboard} className="auth-btn">
                  Ver vacantes
                </IonButton>
                <IonButton fill="clear" size="small" onClick={irPerfil}>
                  Completar mi perfil
                </IonButton>
              </>
            )}

          </div>
        </div>

        <IonModal isOpen={verTerminos} onDidDismiss={() => setVerTerminos(false)}>
          <div className="reg-terminos-modal">
            <h2>Terminos y condiciones</h2>
            <div className="reg-terminos-texto">
              <p><strong>1. El servicio.</strong> CONEX es una bolsa de trabajo y directorio de servicios de Nicaragua. Conectamos personas que buscan empleo o que ofrecen servicios con empresas y clientes. No somos empleadores ni garantizamos contratacion.</p>
              <p><strong>2. Tus datos.</strong> Usamos tu cedula, nombre, telefono y los datos de tu perfil para crear tu cuenta, mostrarte a empresas y contactarte por WhatsApp. Tu telefono solo se revela a una empresa cuando vos aceptas una postulacion o solicitud.</p>
              <p><strong>3. Prestadores.</strong> Si ofreces servicios, tu perfil aparece en el directorio publico. La insignia de "verificado" se otorga despues de una verificacion por llamada; no garantiza la calidad del trabajo ni crea relacion laboral con CONEX.</p>
              <p><strong>4. Conducta.</strong> Es prohibido publicar informacion falsa, suplantar identidades o usar la plataforma para fraudes. Las cuentas que lo hagan seran suspendidas.</p>
              <p><strong>5. Privacidad.</strong> No vendemos tus datos. Puedes solicitar la eliminación definitiva de tu cuenta y datos en cualquier momento desde tu Perfil o en nuestro portal web.</p>
              <p>Consulta nuestra <a href="https://conex.softlutionic.com/politica-privacidad" target="_blank" rel="noopener noreferrer" style={{ color: '#F97316', fontWeight: 700 }}>Política de Privacidad completa aquí</a>.</p>
              <p><strong>6. Edad.</strong> Debes ser mayor de edad para registrarte.</p>
            </div>
            <IonButton expand="block" onClick={() => setVerTerminos(false)} className="auth-btn">
              Entendido
            </IonButton>
          </div>
        </IonModal>

      </IonContent>
    </IonPage>
  );
};

export default Registro;
