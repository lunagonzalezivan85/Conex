import {
  briefcaseOutline, constructOutline, homeOutline, carOutline,
  laptopOutline, medkitOutline, schoolOutline, restaurantOutline,
  storefrontOutline, businessOutline, leafOutline,
  shieldCheckmarkOutline, cutOutline,
} from 'ionicons/icons';

/** Fondos pastel rotativos para los circulos de categoria. */
export const CAT_STYLES = ['cat-c1', 'cat-c2', 'cat-c3', 'cat-c4', 'cat-c5', 'cat-c6'];

/** Icono segun el nombre de la categoria (catalogo cat_categoria). */
export function catIcon(nombre: string): string {
  const n = (nombre || '').toLowerCase();
  if (/construc|alba|obra|reparaci/.test(n)) return constructOutline;
  if (/hogar|limpieza|domest/.test(n)) return homeOutline;
  if (/transport|conduc|taxi|moto|envio|logist/.test(n)) return carOutline;
  if (/tecnol|inform|sistem|compu|program/.test(n)) return laptopOutline;
  if (/salud|enferm|cuidado|medic|enfermera/.test(n)) return medkitOutline;
  if (/educ|docen|maestr|profesor|tutor/.test(n)) return schoolOutline;
  if (/restaur|cocina|comida|chef|bar|repart|hostel|turism/.test(n)) return restaurantOutline;
  if (/venta|comerc|tienda|atencion|market/.test(n)) return storefrontOutline;
  if (/admin|oficina|conta|finanz|secret|recursos|legal|jurid|ingenier/.test(n)) return businessOutline;
  if (/agric|campo|cultiv|ganado/.test(n)) return leafOutline;
  if (/seguridad|vigil/.test(n)) return shieldCheckmarkOutline;
  if (/belleza|peluqu|estetica|barber|salon|disen|creativ/.test(n)) return cutOutline;
  if (/servicio|oficio|tecnico/.test(n)) return constructOutline;
  return briefcaseOutline;
}
