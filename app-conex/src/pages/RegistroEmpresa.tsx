import React, { useState } from 'react';
import {
  IonContent, IonPage, IonInput, IonButton, IonIcon, IonText,
  IonSpinner, IonItem, IonToggle, IonSelect, IonSelectOption,
  IonInputPasswordToggle, useIonRouter,
} from '@ionic/react';
import {
  businessOutline, personOutline, mailOutline, callOutline,
  lockClosedOutline, chevronBackOutline, checkmarkCircle,
  sparklesOutline, cardOutline, briefcaseOutline, searchOutline,
  imageOutline, arrowForwardOutline,
} from 'ionicons/icons';
import { authApi, saveToken, ApiError } from '../services/api';
import { useAuth } from '../context/AuthContext';
import '../theme/auth.css';

const RUBROS = [
  'Comercio y Ventas',
  'Tecnología y Software',
  'Servicios Profesionales',
  'Hostelería y Turismo',
  'Construcción e Inmobiliaria',
  'Industria y Manufactura',
  'Salud y Farmacia',
  'Transporte y Logística',
  'Educación y Capacitación',
  'Finanzas y Seguros',
  'Otro',
];

const CIUDADES = [
  'Managua',
  'León',
  'Granada',
  'Masaya',
  'Chinandega',
  'Matagalpa',
  'Estelí',
  'Rivas',
  'Carazo',
  'Chontales',
  'Boaco',
  'Jinotega',
  'Madriz',
  'Nueva Segovia',
  'Río San Juan',
  'Costa Caribe Norte',
  'Costa Caribe Sur',
];

const RegistroEmpresa: React.FC = () => {
  const router = useIonRouter();
  const { refresh } = useAuth();

  const [razonSocial, setRazonSocial] = useState('');
  const [nombre, setNombre] = useState('');
  const [apellido, setApellido] = useState('');
  const [cedula, setCedula] = useState('');
  const [ruc, setRuc] = useState('');
  const [telefono, setTelefono] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [rubro, setRubro] = useState('');
  const [ciudad, setCiudad] = useState('');
  const [terminos, setTerminos] = useState(false);

  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const [exito, setExito] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (busy) return;
    setError('');

    if (!razonSocial.trim()) {
      setError('Ingresa el nombre o razón social de tu empresa');
      return;
    }
    if (!nombre.trim() || !apellido.trim()) {
      setError('Ingresa el nombre y apellido del contacto');
      return;
    }
    if (!telefono.trim() || telefono.replace(/\D/g, '').length < 8) {
      setError('Ingresa un teléfono o WhatsApp corporativo válido (mínimo 8 dígitos)');
      return;
    }
    if (!email.trim() || !email.includes('@')) {
      setError('Ingresa un correo electrónico corporativo válido');
      return;
    }
    if (!password || password.length < 6) {
      setError('La contraseña debe tener al menos 6 caracteres');
      return;
    }
    if (!terminos) {
      setError('Debes aceptar los términos y condiciones de la prueba gratuita');
      return;
    }

    setBusy(true);
    try {
      const res = await authApi.registerEmpresa({
        tipo_cuenta: 'empresa',
        razon_social: razonSocial.trim(),
        nombre: nombre.trim(),
        apellido: apellido.trim(),
        email: email.trim().toLowerCase(),
        telefono: telefono.trim(),
        password,
        ruc: ruc.trim() || undefined,
        rubro: rubro || undefined,
        ciudad: ciudad || undefined,
      });

      await saveToken(res.token);
      await refresh();
      setExito(true);
    } catch (err) {
      const e = err as ApiError;
      if (e.status === 422 && e.errors) {
        setError(Object.values(e.errors)[0]);
      } else {
        setError(e.error || 'No se pudo registrar la empresa');
      }
    } finally {
      setBusy(false);
    }
  };

  if (exito) {
    return (
      <IonPage>
        <IonContent className="ion-padding">
          <div className="auth-wrap" style={{ minHeight: '100%', alignItems: 'center' }}>
            <div className="auth-card" style={{ maxWidth: 440, padding: '24px 16px' }}>
              <div
                style={{
                  width: 72,
                  height: 72,
                  borderRadius: '50%',
                  background: 'linear-gradient(135deg, #10B981, #059669)',
                  color: '#fff',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  fontSize: 38,
                  margin: '0 auto 16px',
                  boxShadow: '0 8px 24px rgba(16, 185, 129, 0.35)',
                }}
              >
                <IonIcon icon={checkmarkCircle} />
              </div>

              <h1 className="auth-title" style={{ fontSize: 24, marginBottom: 6 }}>
                ¡Bienvenido a CONEX!
              </h1>
              <p className="auth-sub" style={{ marginBottom: 16 }}>
                Tu cuenta de empresa para <strong>{razonSocial}</strong> está activa con{' '}
                <strong>1 mes de prueba gratis</strong> en moneda <strong>USD</strong>.
              </p>

              <div
                style={{
                  background: 'rgba(15, 198, 194, 0.08)',
                  border: '1px solid rgba(15, 198, 194, 0.25)',
                  borderRadius: 14,
                  padding: '12px 16px',
                  marginBottom: 24,
                  textAlign: 'left',
                }}
              >
                <div style={{ fontWeight: 700, color: '#0F766E', fontSize: 13, marginBottom: 4 }}>
                  🎁 BENEFICIOS DE TU PRUEBA GRATUITA:
                </div>
                <ul style={{ margin: 0, paddingLeft: 18, fontSize: 13, color: '#334155', lineHeight: '1.6' }}>
                  <li>5 vacantes activas simultáneas</li>
                  <li>Acceso a 8 candidatos completos</li>
                  <li>30 días sin costo (Planes en USD)</li>
                </ul>
              </div>

              <h2 style={{ fontSize: 16, fontWeight: 700, margin: '0 0 14px', color: '#1B2A4A' }}>
                ¿Cómo quieres armar tu perfil o razón social?
              </h2>

              <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                <button
                  type="button"
                  className="portal-btn"
                  style={{
                    background: '#1B2A4A',
                    color: '#fff',
                    textAlign: 'left',
                    padding: '14px 16px',
                    borderRadius: 14,
                    display: 'flex',
                    alignItems: 'center',
                    gap: 12,
                    border: 'none',
                    cursor: 'pointer',
                  }}
                  onClick={() => router.push('/app/perfil', 'root', 'replace')}
                >
                  <div
                    style={{
                      width: 40,
                      height: 40,
                      borderRadius: 10,
                      background: 'rgba(255,255,255,0.15)',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontSize: 20,
                      flexShrink: 0,
                    }}
                  >
                    <IonIcon icon={imageOutline} />
                  </div>
                  <div style={{ flex: 1 }}>
                    <div style={{ fontWeight: 700, fontSize: 14 }}>Subir logo y armar perfil</div>
                    <div style={{ fontSize: 12, opacity: 0.8 }}>Agrega tu logo, descripción y redes</div>
                  </div>
                  <IonIcon icon={arrowForwardOutline} />
                </button>

                <button
                  type="button"
                  className="portal-btn"
                  style={{
                    background: '#F97316',
                    color: '#fff',
                    textAlign: 'left',
                    padding: '14px 16px',
                    borderRadius: 14,
                    display: 'flex',
                    alignItems: 'center',
                    gap: 12,
                    border: 'none',
                    cursor: 'pointer',
                  }}
                  onClick={() => router.push('/app/dashboard', 'root', 'replace')}
                >
                  <div
                    style={{
                      width: 40,
                      height: 40,
                      borderRadius: 10,
                      background: 'rgba(255,255,255,0.2)',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontSize: 20,
                      flexShrink: 0,
                    }}
                  >
                    <IonIcon icon={briefcaseOutline} />
                  </div>
                  <div style={{ flex: 1 }}>
                    <div style={{ fontWeight: 700, fontSize: 14 }}>Publicar mi primera vacante</div>
                    <div style={{ fontSize: 12, opacity: 0.9 }}>Usa 1 de tus 5 vacantes gratis</div>
                  </div>
                  <IonIcon icon={arrowForwardOutline} />
                </button>

                <button
                  type="button"
                  className="portal-btn"
                  style={{
                    background: '#F1F5F9',
                    color: '#1B2A4A',
                    textAlign: 'left',
                    padding: '14px 16px',
                    borderRadius: 14,
                    display: 'flex',
                    alignItems: 'center',
                    gap: 12,
                    border: '1px solid #CBD5E1',
                    cursor: 'pointer',
                  }}
                  onClick={() => router.push('/app/buscar', 'root', 'replace')}
                >
                  <div
                    style={{
                      width: 40,
                      height: 40,
                      borderRadius: 10,
                      background: '#E2E8F0',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontSize: 20,
                      color: '#0F766E',
                      flexShrink: 0,
                    }}
                  >
                    <IonIcon icon={searchOutline} />
                  </div>
                  <div style={{ flex: 1 }}>
                    <div style={{ fontWeight: 700, fontSize: 14 }}>Explorar candidatos disponibles</div>
                    <div style={{ fontSize: 12, color: '#64748B' }}>Consulta hasta 8 perfiles completos</div>
                  </div>
                  <IonIcon icon={arrowForwardOutline} />
                </button>
              </div>
            </div>
          </div>
        </IonContent>
      </IonPage>
    );
  }

  return (
    <IonPage>
      <IonContent className="ion-padding">
        <div className="auth-wrap" style={{ minHeight: '100%', alignItems: 'flex-start', paddingTop: 20 }}>
          <div className="auth-card" style={{ maxWidth: 440 }}>
            {/* Header con botón atrás */}
            <div style={{ display: 'flex', alignItems: 'center', marginBottom: 12 }}>
              <button
                type="button"
                onClick={() => router.push('/login')}
                style={{
                  background: 'none',
                  border: 'none',
                  display: 'flex',
                  alignItems: 'center',
                  gap: 4,
                  fontSize: 14,
                  color: '#1B2A4A',
                  fontWeight: 600,
                  cursor: 'pointer',
                  padding: 4,
                }}
              >
                <IonIcon icon={chevronBackOutline} style={{ fontSize: 18 }} />
                Volver
              </button>
            </div>

            <div className="auth-logo" style={{ margin: '0 auto 12px' }}>
              C<span>x</span>
            </div>
            <h1 className="auth-title">Busco Talento</h1>
            <p className="auth-sub" style={{ marginBottom: 16 }}>
              Registra tu empresa y obtén <strong>1 mes de prueba gratis</strong>
            </p>

            {/* Badge de prueba gratuita */}
            <div
              style={{
                display: 'flex',
                alignItems: 'center',
                gap: 8,
                background: 'linear-gradient(135deg, #1B2A4A, #283e6b)',
                color: '#fff',
                padding: '10px 14px',
                borderRadius: 12,
                fontSize: 13,
                marginBottom: 20,
                textAlign: 'left',
              }}
            >
              <IonIcon icon={sparklesOutline} style={{ fontSize: 22, color: '#0FC6C2', flexShrink: 0 }} />
              <div>
                <span style={{ fontWeight: 700 }}>Beneficio de bienvenida:</span> 5 vacantes, 8 candidatos,
                30 días gratis. Moneda de planes: <strong>USD</strong>.
              </div>
            </div>

            <form onSubmit={handleSubmit} className="auth-form">
              {/* Razón Social */}
              <label style={{ fontSize: 13, fontWeight: 700, color: '#334155', display: 'block', marginBottom: 4 }}>
                Nombre de la Empresa o Razón Social *
              </label>
              <IonItem className="auth-item" lines="none">
                <IonIcon icon={businessOutline} slot="start" />
                <IonInput
                  placeholder="Ej. Distribuidora del Norte S.A."
                  value={razonSocial}
                  onIonInput={e => setRazonSocial(e.detail.value ?? '')}
                  required
                />
              </IonItem>

              {/* RUC (Opcional) */}
              <label style={{ fontSize: 13, fontWeight: 600, color: '#64748B', display: 'block', marginBottom: 4 }}>
                Número RUC <small style={{ color: '#94A3B8' }}>(Opcional)</small>
              </label>
              <IonItem className="auth-item" lines="none">
                <IonIcon icon={cardOutline} slot="start" />
                <IonInput
                  placeholder="Ej. J0310000123456 (No obligatorio)"
                  value={ruc}
                  onIonInput={e => setRuc(e.detail.value ?? '')}
                />
              </IonItem>

              {/* Nombre y Apellido del Contacto */}
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                <div>
                  <label style={{ fontSize: 13, fontWeight: 700, color: '#334155', display: 'block', marginBottom: 4 }}>
                    Nombre Contacto *
                  </label>
                  <IonItem className="auth-item" lines="none">
                    <IonIcon icon={personOutline} slot="start" />
                    <IonInput
                      placeholder="Ej. Roberto"
                      value={nombre}
                      onIonInput={e => setNombre(e.detail.value ?? '')}
                      required
                    />
                  </IonItem>
                </div>
                <div>
                  <label style={{ fontSize: 13, fontWeight: 700, color: '#334155', display: 'block', marginBottom: 4 }}>
                    Apellido Contacto *
                  </label>
                  <IonItem className="auth-item" lines="none">
                    <IonInput
                      placeholder="Ej. Pineda"
                      value={apellido}
                      onIonInput={e => setApellido(e.detail.value ?? '')}
                      required
                    />
                  </IonItem>
                </div>
              </div>

              {/* Cédula del Contacto (Opcional, acepta letras) */}
              <label style={{ fontSize: 13, fontWeight: 600, color: '#64748B', display: 'block', marginBottom: 4 }}>
                Cédula del Representante <small style={{ color: '#94A3B8' }}>(Opcional — acepta letras)</small>
              </label>
              <IonItem className="auth-item" lines="none">
                <IonIcon icon={cardOutline} slot="start" />
                <IonInput
                  placeholder="Ej. 001-101191-0019M"
                  value={cedula}
                  onIonInput={e => setCedula(e.detail.value ?? '')}
                />
              </IonItem>

              {/* Teléfono / WhatsApp */}
              <label style={{ fontSize: 13, fontWeight: 700, color: '#334155', display: 'block', marginBottom: 4 }}>
                Teléfono o WhatsApp corporativo *
              </label>
              <IonItem className="auth-item" lines="none">
                <IonIcon icon={callOutline} slot="start" />
                <IonInput
                  type="tel"
                  placeholder="Ej. +505 8888 1234"
                  value={telefono}
                  onIonInput={e => setTelefono(e.detail.value ?? '')}
                  required
                />
              </IonItem>

              {/* Correo Electrónico */}
              <label style={{ fontSize: 13, fontWeight: 700, color: '#334155', display: 'block', marginBottom: 4 }}>
                Correo electrónico corporativo *
              </label>
              <IonItem className="auth-item" lines="none">
                <IonIcon icon={mailOutline} slot="start" />
                <IonInput
                  type="email"
                  placeholder="empresa@correo.com"
                  value={email}
                  onIonInput={e => setEmail(e.detail.value ?? '')}
                  required
                />
              </IonItem>

              {/* Contraseña */}
              <label style={{ fontSize: 13, fontWeight: 700, color: '#334155', display: 'block', marginBottom: 4 }}>
                Contraseña de acceso *
              </label>
              <IonItem className="auth-item" lines="none">
                <IonIcon icon={lockClosedOutline} slot="start" />
                <IonInput
                  type="password"
                  placeholder="Mínimo 6 caracteres"
                  value={password}
                  onIonInput={e => setPassword(e.detail.value ?? '')}
                  required
                >
                  <IonInputPasswordToggle slot="end" />
                </IonInput>
              </IonItem>

              {/* Rubro y Ciudad */}
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10, marginTop: 4 }}>
                <div>
                  <label style={{ fontSize: 13, fontWeight: 600, color: '#64748B', display: 'block', marginBottom: 4 }}>
                    Rubro / Sector
                  </label>
                  <IonItem className="auth-item" lines="none">
                    <IonSelect
                      placeholder="Seleccionar"
                      value={rubro}
                      onIonChange={e => setRubro(e.detail.value)}
                    >
                      {RUBROS.map(r => (
                        <IonSelectOption key={r} value={r}>
                          {r}
                        </IonSelectOption>
                      ))}
                    </IonSelect>
                  </IonItem>
                </div>
                <div>
                  <label style={{ fontSize: 13, fontWeight: 600, color: '#64748B', display: 'block', marginBottom: 4 }}>
                    Departamento / Ciudad
                  </label>
                  <IonItem className="auth-item" lines="none">
                    <IonSelect
                      placeholder="Seleccionar"
                      value={ciudad}
                      onIonChange={e => setCiudad(e.detail.value)}
                    >
                      {CIUDADES.map(c => (
                        <IonSelectOption key={c} value={c}>
                          {c}
                        </IonSelectOption>
                      ))}
                    </IonSelect>
                  </IonItem>
                </div>
              </div>

              {/* Términos y condiciones */}
              <div style={{ display: 'flex', alignItems: 'center', gap: 10, margin: '14px 0 10px' }}>
                <IonToggle
                  checked={terminos}
                  onIonChange={e => setTerminos(e.detail.checked)}
                />
                <span style={{ fontSize: 12, color: '#475569' }}>
                  Acepto los términos y la activación del <strong>mes de prueba gratis</strong> en CONEX.
                </span>
              </div>

              {error && (
                <IonText color="danger" className="auth-error">
                  <small>{error}</small>
                </IonText>
              )}

              <IonButton
                expand="block"
                type="submit"
                disabled={busy}
                className="auth-btn"
                style={{ marginTop: 16 }}
              >
                {busy ? <IonSpinner name="crescent" /> : 'Crear Cuenta de Empresa'}
              </IonButton>

              <div style={{ textAlign: 'center', marginTop: 16, marginBottom: 24 }}>
                <IonButton
                  fill="clear"
                  size="small"
                  onClick={() => router.push('/login?modo=login')}
                  style={{ fontSize: 13, textTransform: 'none', color: '#1B2A4A' }}
                >
                  ¿Ya tienes una cuenta de empresa? <strong>Iniciar sesión</strong>
                </IonButton>
              </div>
            </form>
          </div>
        </div>
      </IonContent>
    </IonPage>
  );
};

export default RegistroEmpresa;
