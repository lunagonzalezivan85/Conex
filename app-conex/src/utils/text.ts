// Normalización de texto libre escrito por usuarios.
// Regla: solo se transforma cuando el texto está TODO en minúsculas o TODO
// en mayúsculas; el mixed-case se respeta (marcas, siglas, "UX Empresa SA").

const ES_CONNECTORS = new Set([
  'de', 'del', 'la', 'las', 'el', 'los', 'y', 'e', 'o', 'u',
  'en', 'con', 'a', 'al', 'para', 'por',
]);

const tidy = (s: string) => s.trim().replace(/\s+/g, ' ');

const needsNormalize = (t: string) =>
  t.length > 0 && (t === t.toLowerCase() || t === t.toUpperCase());

/** "maestro de obra" | "MAESTRO DE OBRA" → "Maestro de obra" */
export function toSentenceCase(str?: string | null): string {
  if (!str) return '';
  const t = tidy(str);
  if (!needsNormalize(t)) return t;
  const lower = t.toLowerCase();
  return lower.charAt(0).toUpperCase() + lower.slice(1);
}

/** Nombre de persona o empresa: siempre TODO en mayúsculas ("JOSÉ PÉREZ", "UX EMPRESA SA") */
export function toUpperName(str?: string | null): string {
  if (!str) return '';
  return tidy(str).toUpperCase();
}

/** "juan carlos pérez" → "Juan Carlos Pérez" (conectores en minúscula) */
export function toNameCase(str?: string | null): string {
  if (!str) return '';
  const t = tidy(str);
  if (!needsNormalize(t)) return t;
  return t
    .split(' ')
    .map((w, i) =>
      i > 0 && ES_CONNECTORS.has(w.toLowerCase())
        ? w.toLowerCase()
        : w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()
    )
    .join(' ');
}
