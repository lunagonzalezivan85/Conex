import React, { useEffect, useId, useMemo, useRef, useState } from 'react';
import { IonIcon } from '@ionic/react';
import { chevronDownOutline, searchOutline } from 'ionicons/icons';

export interface CxAutocompleteOption {
  value: string | number;
  label: string;
}

interface CxAutocompleteProps {
  value: string | number;
  onChange: (value: string | number) => void;
  options: Array<string | CxAutocompleteOption>;
  placeholder?: string;
  icon?: string;
  /** true = solo permite valores de la lista; false = texto libre con sugerencias */
  strict?: boolean;
}

/** Quita acentos y normaliza para comparar */
const norm = (s: string) =>
  s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();

/**
 * Input pill con autocompletado (estilo One UI/bento).
 * - Filtra mientras escribe; soporta opciones con {value, label} distintos.
 * - strict: el valor solo cambia al elegir una opción (o match exacto al salir).
 * - Aria combobox/listbox + navegación con flechas/Enter/Escape.
 */
const CxAutocomplete: React.FC<CxAutocompleteProps> = ({
  value,
  onChange,
  options,
  placeholder = 'Escribe para buscar',
  icon = searchOutline,
  strict = false,
}) => {
  const listId = useId();
  const wrapRef = useRef<HTMLDivElement>(null);
  const [open, setOpen] = useState(false);
  const [hl, setHl] = useState(-1);
  const [text, setText] = useState('');

  const opts = useMemo<CxAutocompleteOption[]>(
    () => options.map(o => (typeof o === 'string' ? { value: o, label: o } : o)),
    [options],
  );

  const labelOf = (v: string | number) =>
    opts.find(o => o.value === v)?.label ?? (strict ? '' : String(v ?? ''));

  // sincroniza el texto cuando cambia el valor u opciones
  useEffect(() => {
    setText(labelOf(value));
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [value, opts]);

  const filtered = useMemo(() => {
    const q = norm(text);
    if (!q) return opts;
    return opts.filter(o => norm(o.label).includes(q));
  }, [text, opts]);

  const pick = (opt: CxAutocompleteOption) => {
    setText(opt.label);
    onChange(opt.value);
    setOpen(false);
    setHl(-1);
  };

  const onKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      setOpen(true);
      setHl(i => Math.min(i + 1, filtered.length - 1));
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      setHl(i => Math.max(i - 1, 0));
    } else if (e.key === 'Enter') {
      if (open && hl >= 0 && filtered[hl]) {
        e.preventDefault();
        pick(filtered[hl]);
      }
    } else if (e.key === 'Escape') {
      setOpen(false);
      setHl(-1);
      setText(labelOf(value));
    }
  };

  const onBlur = () => {
    // delay para permitir el click en una opción
    setTimeout(() => {
      if (wrapRef.current?.contains(document.activeElement)) return;
      setOpen(false);
      setHl(-1);
      if (strict) {
        const exact = opts.find(o => norm(o.label) === norm(text));
        if (exact) pick(exact);
        else setText(labelOf(value));
      } else {
        onChange(text);
      }
    }, 120);
  };

  return (
    <div className="cx-ac-wrap" ref={wrapRef}>
      <IonIcon icon={icon} className="cx-ac-icon" />
      <input
        type="text"
        className="cx-ac-input"
        role="combobox"
        aria-expanded={open}
        aria-controls={listId}
        aria-autocomplete="list"
        aria-activedescendant={hl >= 0 ? `${listId}-${hl}` : undefined}
        placeholder={placeholder}
        value={text}
        onFocus={() => setOpen(true)}
        onChange={e => {
          setText(e.target.value);
          if (!strict) onChange(e.target.value);
          setOpen(true);
          setHl(-1);
        }}
        onKeyDown={onKeyDown}
        onBlur={onBlur}
      />
      <button
        type="button"
        className="cx-ac-toggle"
        aria-label={open ? 'Cerrar opciones' : 'Ver opciones'}
        tabIndex={-1}
        onClick={() => setOpen(o => !o)}
      >
        <IonIcon icon={chevronDownOutline} />
      </button>

      {open && filtered.length > 0 && (
        <ul className="cx-ac-list" id={listId} role="listbox">
          {filtered.map((opt, i) => (
            <li
              key={String(opt.value)}
              id={`${listId}-${i}`}
              role="option"
              aria-selected={opt.value === value}
              className={`cx-ac-option ${i === hl ? 'hl' : ''} ${opt.value === value ? 'sel' : ''}`}
              onMouseDown={e => {
                e.preventDefault(); // evita blur antes del pick
                pick(opt);
              }}
              onMouseEnter={() => setHl(i)}
            >
              {opt.label}
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default CxAutocomplete;
