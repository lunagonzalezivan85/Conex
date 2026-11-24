import React from 'react';
import { Redirect, Route, useLocation } from 'react-router-dom';
import {
  IonApp, IonIcon, IonLabel, IonRouterOutlet,
  IonTabBar, IonTabButton, IonTabs, setupIonicReact,
} from '@ionic/react';
import { IonReactRouter } from '@ionic/react-router';
import {
  briefcaseOutline, businessOutline, chatbubbleOutline, homeOutline, peopleOutline, personOutline,
} from 'ionicons/icons';

import { AuthProvider, useAuth } from './context/AuthContext';
import Splash from './pages/Splash';
import Login from './pages/Login';
import Registro from './pages/Registro';
import RegistroEmpresa from './pages/RegistroEmpresa';
import Dashboard from './pages/Dashboard';
import MisVacantes from './pages/MisVacantes';
import Perfil from './pages/Perfil';
import Buscar from './pages/Buscar';
import Categorias from './pages/Categorias';
import VacanteDetalle from './pages/VacanteDetalle';
import Planes from './pages/Planes';
import Solicitudes from './pages/Solicitudes';
import ChatMessenger from './pages/ChatMessenger';
import Notificaciones from './pages/Notificaciones';

import '@ionic/react/css/core.css';
import '@ionic/react/css/normalize.css';
import '@ionic/react/css/structure.css';
import '@ionic/react/css/typography.css';
import '@ionic/react/css/padding.css';
import '@ionic/react/css/flex-utils.css';
import '@ionic/react/css/display.css';
import './theme/variables.css';

setupIonicReact();

/**
 * Tabs principales de la app (adaptativos según rol Empresa o Candidato).
 */
const AppTabs: React.FC = () => {
  const { user } = useAuth();
  const location = useLocation();
  const isChat = location.pathname.startsWith('/app/chat');
  const role = (user?.role || '').toLowerCase().trim();
  const isEmpresa = role === 'empresa' || (role !== 'candidato' && Boolean(user?.empresa));

  return (
    <IonTabs>
      <IonRouterOutlet>
        <Route exact path="/app/dashboard" component={Dashboard} />
        <Route exact path="/app/mis-vacantes" component={MisVacantes} />
        <Route exact path="/app/buscar" component={Buscar} />
        <Route exact path="/app/contactos" component={Buscar} />
        <Route exact path="/app/talentos" component={Buscar} />
        <Route exact path="/app/solicitudes" component={Solicitudes} />
        <Route exact path="/app/mensajes" component={Solicitudes} />
        <Route exact path="/app/chat" component={ChatMessenger} />
        <Route exact path="/app/notificaciones" component={Notificaciones} />
        <Route exact path="/app/categorias" component={Categorias} />
        <Route exact path="/app/vacante/:slug" component={VacanteDetalle} />
        <Route exact path="/app/perfil" component={Perfil} />
        <Route exact path="/app/planes" component={Planes} />
        <Route exact path="/app" render={() => <Redirect to="/app/dashboard" />} />
      </IonRouterOutlet>

      {isEmpresa ? (
        <IonTabBar
          slot="bottom"
          className={isChat ? 'cx-tab-bar-hidden' : ''}
          style={{ display: isChat ? 'none' : 'flex' }}
        >
          <IonTabButton tab="dashboard" href="/app/dashboard">
            <IonIcon icon={homeOutline} />
            <IonLabel>Inicio</IonLabel>
          </IonTabButton>

          <IonTabButton tab="mis-vacantes" href="/app/mis-vacantes">
            <IonIcon icon={briefcaseOutline} />
            <IonLabel>Mis Vacantes</IonLabel>
          </IonTabButton>

          <IonTabButton tab="talentos" href="/app/talentos">
            <IonIcon icon={peopleOutline} />
            <IonLabel>Talentos</IonLabel>
          </IonTabButton>

          <IonTabButton tab="solicitudes" href="/app/solicitudes">
            <IonIcon icon={chatbubbleOutline} />
            <IonLabel>Mensajes</IonLabel>
          </IonTabButton>

          <IonTabButton tab="perfil" href="/app/perfil">
            <IonIcon icon={businessOutline} />
            <IonLabel>Empresa</IonLabel>
          </IonTabButton>
        </IonTabBar>
      ) : (
        <IonTabBar
          slot="bottom"
          className={isChat ? 'cx-tab-bar-hidden' : ''}
          style={{ display: isChat ? 'none' : 'flex' }}
        >
          <IonTabButton tab="dashboard" href="/app/dashboard">
            <IonIcon icon={homeOutline} />
            <IonLabel>Inicio</IonLabel>
          </IonTabButton>

          <IonTabButton tab="buscar" href="/app/buscar">
            <IonIcon icon={briefcaseOutline} />
            <IonLabel>Empleos</IonLabel>
          </IonTabButton>

          <IonTabButton tab="contactos" href="/app/contactos">
            <IonIcon icon={peopleOutline} />
            <IonLabel>Contactos</IonLabel>
          </IonTabButton>

          <IonTabButton tab="solicitudes" href="/app/solicitudes">
            <IonIcon icon={chatbubbleOutline} />
            <IonLabel>Mensajes</IonLabel>
          </IonTabButton>

          <IonTabButton tab="perfil" href="/app/perfil">
            <IonIcon icon={personOutline} />
            <IonLabel>Perfil</IonLabel>
          </IonTabButton>
        </IonTabBar>
      )}
    </IonTabs>
  );
};

/**
 * Guard inline: solo deja entrar a /app si hay sesion valida.
 * Debe ser un Route directo del IonRouterOutlet (el outlet solo
 * registra sus hijos Route, no componentes envoltura).
 */
const AppRoutes: React.FC = () => {
  const { user, loading } = useAuth();
  return (
    <IonReactRouter>
      <IonRouterOutlet>
        <Route exact path="/" component={Splash} />
        <Route
          exact
          path="/login"
          render={() => (user ? <Redirect to="/app/dashboard" /> : <Login />)}
        />
        <Route exact path="/registro" component={Registro} />
        <Route exact path="/registro-empresa" component={RegistroEmpresa} />
        <Route
          path="/app"
          render={() => {
            if (loading) return null;
            return user ? <AppTabs /> : <Redirect to="/login" />;
          }}
        />
      </IonRouterOutlet>
    </IonReactRouter>
  );
};

const App: React.FC = () => (
  <IonApp>
    <AuthProvider>
      <AppRoutes />
    </AuthProvider>
  </IonApp>
);

export default App;
