import React from 'react';
import { Redirect, Route } from 'react-router-dom';
import {
  IonApp, IonIcon, IonLabel, IonRouterOutlet,
  IonTabBar, IonTabButton, IonTabs, setupIonicReact,
} from '@ionic/react';
import { IonReactRouter } from '@ionic/react-router';
import { homeOutline, personOutline, searchOutline } from 'ionicons/icons';

import { AuthProvider, useAuth } from './context/AuthContext';
import Splash from './pages/Splash';
import Login from './pages/Login';
import Dashboard from './pages/Dashboard';
import Perfil from './pages/Perfil';
import Buscar from './pages/Buscar';
import VacanteDetalle from './pages/VacanteDetalle';

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
 * Tabs principales de la app (Inicio + Perfil).
 */
const AppTabs: React.FC = () => (
  <IonTabs>
    <IonRouterOutlet>
      <Route exact path="/app/dashboard" component={Dashboard} />
      <Route exact path="/app/buscar" component={Buscar} />
      <Route exact path="/app/vacante/:slug" component={VacanteDetalle} />
      <Route exact path="/app/perfil" component={Perfil} />
      <Route exact path="/app" render={() => <Redirect to="/app/dashboard" />} />
    </IonRouterOutlet>

    <IonTabBar slot="bottom">
      <IonTabButton tab="dashboard" href="/app/dashboard">
        <IonIcon icon={homeOutline} />
        <IonLabel>Inicio</IonLabel>
      </IonTabButton>
      <IonTabButton tab="buscar" href="/app/buscar">
        <IonIcon icon={searchOutline} />
        <IonLabel>Buscar</IonLabel>
      </IonTabButton>
      <IonTabButton tab="perfil" href="/app/perfil">
        <IonIcon icon={personOutline} />
        <IonLabel>Perfil</IonLabel>
      </IonTabButton>
    </IonTabBar>
  </IonTabs>
);

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
        <Route exact path="/login" component={Login} />
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
