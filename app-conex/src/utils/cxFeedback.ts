import { useIonToast, useIonAlert } from '@ionic/react';
import type { ToastOptions, AlertOptions } from '@ionic/react';
import {
  alertCircleOutline,
  checkmarkCircleOutline,
  informationCircleOutline,
  warningOutline,
} from 'ionicons/icons';

/**
 * Feedback de marca CONEX.
 * Reemplaza useIonToast/useIonAlert: misma firma de `present`,
 * pero inyecta cssClass de marca (bento + colores CONEX).
 * Nunca usar toasts/alerts por defecto ni window.alert.
 */

const TOAST_META: Record<string, { icon: string; cls: string }> = {
  success: { icon: checkmarkCircleOutline, cls: 'cx-toast-success' },
  danger: { icon: alertCircleOutline, cls: 'cx-toast-danger' },
  warning: { icon: warningOutline, cls: 'cx-toast-warning' },
  medium: { icon: informationCircleOutline, cls: 'cx-toast-info' },
  info: { icon: informationCircleOutline, cls: 'cx-toast-info' },
};

export const useCxToast = () => {
  const [present, dismiss] = useIonToast();
  const presentCx = (options: ToastOptions) => {
    const meta = TOAST_META[options.color ?? 'info'] ?? TOAST_META.info;
    return present({
      position: 'bottom',
      duration: 2400,
      ...options,
      color: undefined, // el acento lo pone la clase de marca, no el color Ionic
      icon: options.icon ?? meta.icon,
      cssClass: ['cx-toast', meta.cls, options.cssClass ?? ''].join(' ').trim(),
    });
  };
  return [presentCx, dismiss] as const;
};

export const useCxAlert = () => {
  const [present] = useIonAlert();
  const presentCx = (options: AlertOptions) =>
    present({
      ...options,
      cssClass: ['cx-alert', options.cssClass ?? ''].join(' ').trim(),
    });
  return [presentCx] as const;
};
