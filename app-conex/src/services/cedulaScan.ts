/**
 * Escaneo del QR de la cedula nicaraguense.
 * Usa el plugin nativo @capacitor/barcode-scanner cuando esta disponible
 * (APK real). En dev web devuelve null y el wizard usa entrada manual.
 */

import {
  CapacitorBarcodeScanner,
  CapacitorBarcodeScannerTypeHintALLOption,
  CapacitorBarcodeScannerCameraDirection,
  CapacitorBarcodeScannerScanOrientation,
  CapacitorBarcodeScannerAndroidScanningLibrary,
} from '@capacitor/barcode-scanner';

export interface CedulaData {
  cedula: string;
  nombres?: string;
  apellidos?: string;
  municipio?: string;
}

/**
 * Abre el escaner nativo. Devuelve null si se cancela,
 * si no hay camara o si ocurre cualquier error.
 */
export async function escanearCedula(): Promise<CedulaData | null> {
  try {
    const res = await CapacitorBarcodeScanner.scanBarcode({
      hint: CapacitorBarcodeScannerTypeHintALLOption.ALL,
      scanInstructions: 'Apunta el QR de tu cedula',
      scanButton: false,
      cameraDirection: CapacitorBarcodeScannerCameraDirection.BACK,
      scanOrientation: CapacitorBarcodeScannerScanOrientation.ADAPTIVE,
      android: {
        scanningLibrary: CapacitorBarcodeScannerAndroidScanningLibrary.MLKIT,
      },
    });

    const raw = res?.ScanResult;
    if (!raw) return null;
    return parsearCedula(raw);
  } catch {
    return null; // usuario cancelo, sin permiso de camara, o error
  }
}

/**
 * Formato real del QR de la cedula nicaraguense — campos separados por '<':
 *   [0] id interno          (10059267)
 *   [1] cedula sin guiones  (0011011910019M)
 *   [2] apellido 1, [3] apellido 2
 *   [4] nombre 1,   [5] nombre 2
 *   [6] municipio,  [7] fecha nac, [8] sexo,
 *   [9] emision,   [10] vencimiento, resto: minucias/estado
 */
const IDX_CEDULA = 1;
const IDX_AP1 = 2;
const IDX_AP2 = 3;
const IDX_NOM1 = 4;
const IDX_NOM2 = 5;
const IDX_MUNICIPIO = 6;

/**
 * Parsea el contenido del QR. Conoce el formato '<' de la cedula
 * y como fallback intenta JSON, patron de cedula y digitos sueltos.
 */
export function parsearCedula(raw: string): CedulaData {
  let cedula = '';
  let nombres: string | undefined;
  let apellidos: string | undefined;
  let municipio: string | undefined;

  // 0) Formato real: campos separados por '<'
  if (raw.includes('<')) {
    const f = raw.split('<');
    // el campo de cedula es el que trae 13+ caracteres alfanumericos;
    // si no, usamos el primer id
    const cand = [f[IDX_CEDULA], f[0]].find(
      x => /^[0-9]{10,}[A-Za-z0-9]$/.test((x || '').trim())
    );
    cedula = (cand || '').trim();
    apellidos = [f[IDX_AP1], f[IDX_AP2]]
      .map(s => (s || '').trim()).filter(Boolean).join(' ') || undefined;
    nombres = [f[IDX_NOM1], f[IDX_NOM2]]
      .map(s => (s || '').trim()).filter(Boolean).join(' ') || undefined;
    municipio = (f[IDX_MUNICIPIO] || '').trim() || undefined;
  }

  // 1) Si es JSON, buscar campos comunes
  try {
    const json = JSON.parse(raw);
    if (json && typeof json === 'object') {
      const lower: Record<string, string> = {};
      for (const [k, v] of Object.entries(json)) {
        lower[k.toLowerCase()] = String(v ?? '');
      }
      cedula = lower['cedula'] || lower['numero_cedula'] || lower['id'] || '';
      nombres = lower['nombres'] || lower['nombre'] || lower['name'];
      apellidos =
        lower['apellidos'] || lower['apellido'] || lower['surname'] ||
        [lower['primer_apellido'], lower['segundo_apellido']]
          .filter(Boolean).join(' ') || undefined;
    }
  } catch {
    // no es JSON
  }

  // 2) Patron de cedula nicaraguense en texto plano
  if (!cedula) {
    const match = raw.match(/\b\d{3}-?\d{6}-?\d{4}[A-Za-z0-9]?\b/);
    if (match) cedula = match[0];
  }

  // 3) Ultimo recurso: secuencia larga de digitos
  if (!cedula) {
    const digits = raw.replace(/\D/g, '');
    if (digits.length >= 6) cedula = digits.slice(0, 20);
  }

  return {
    cedula: cedula.replace(/[^0-9A-Za-z]/g, ''),
    nombres: nombres?.trim() || undefined,
    apellidos: apellidos?.trim() || undefined,
    municipio: municipio?.trim() || undefined,
  };
}

/**
 * Muestra la cedula con guiones: 001-101191-0019M.
 * Solo formatea; al enviar se quita todo lo no alfanumerico.
 */
export function formatearCedula(cedula: string): string {
  const limpia = cedula.replace(/[^0-9A-Za-z]/g, '');
  const m = limpia.match(/^(\d{3})(\d{6})(.+)$/);
  return m ? `${m[1]}-${m[2]}-${m[3]}` : limpia;
}

/** Normaliza para enviar al backend (mismo preg_replace del servidor). */
export function normalizarCedula(cedula: string): string {
  return cedula.replace(/[^0-9A-Za-z]/g, '');
}
