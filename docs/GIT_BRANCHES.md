# OpenToWork - Estrategia de Ramas Git

## Convencion de Ramas

Cada IA trabaja en su propia rama por fase. Al finalizar la fase, se hace merge a `main`.

### Formato de nombre de rama

```
{ia}-{fase}
```

### Ejemplos

```
main                # Rama principal (produccion)
iluna-fase-1        # Iluna trabajando en Fase 1
iluna-fase-2        # Iluna trabajando en Fase 2
dsiezar-fase-2      # Dsiezar trabajando en Fase 2
dsiezar-fase-3      # Dsiezar trabajando en Fase 3
```

---

## Flujo de Ramas

```
main (produccion)
  |
  +-- iluna-fase-1 ----[Fase 1 completada]----> MERGE a main
  |                                              |
  |                                              v
  |                                         main (Fase 1 lista)
  |                                              |
  |                                              +-- iluna-fase-2 ----[completada]----> MERGE a main
  |                                              |                         |
  |                                              |                         v
  |                                              |                    main (Fase 2 lista)
  |                                              |                         |
  |                                              +-- dsiezar-fase-2 ----[completada]----> MERGE a main
  |                                                                        |
  |                                                                        v
  |                                                                   main (Fase 2 lista)
  |
  +-- dsiezar-fase-3 ----[Fase 3 completada]----> MERGE a main
                                                  |
                                                  v
                                             main (Fase 3 lista)
```

---

## Reglas

1. **Nadie commitea directamente a `main`.** Todo cambio va por rama de fase.
2. **Una rama por IA por fase.** Si ambas IAs trabajan en la misma fase, cada una tiene su rama.
3. **Merge solo cuando la fase este completada** (Etapa 7 del WORKFLOW: PM + QA + SEC aprobados).
4. **Merge con `--no-ff`** para preservar el historial de la rama:
   ```
   git merge --no-ff iluna-fase-1 -m "Merge: Fase 1 completada por Iluna"
   ```
5. **Despues del merge**, la rama de fase se puede eliminar:
   ```
   git branch -d iluna-fase-1
   ```
6. **Commits descriptivos** con el formato:
   ```
   [Fase N] Etapa X: descripcion del cambio
   ```
7. **La bitacora** (`docs/iluna/fase-N.md` o `docs/dsiezar/fase-N.md`) debe actualizarse antes del merge.

---

## Comandos Comunes

```bash
# Crear rama de fase
git checkout -b iluna-fase-2

# Hacer commit
git add -A
git commit -m "[Fase 2] Etapa 3: Implementar entidad PT_Vacancies"

# Subir rama al remoto
git push -u origin iluna-fase-2

# Merge a main (al finalizar la fase)
git checkout main
git merge --no-ff iluna-fase-2 -m "Merge: Fase 2 completada por Iluna"

# Eliminar rama despues del merge
git branch -d iluna-fase-2

# Ver todas las ramas
git branch -a
```

---

## Estado Actual de Ramas

| Rama | IA | Fase | Estado |
|---|---|---|---|
| `main` | - | - | Fase 1 mergeada |
| `iluna-fase-1` | Iluna | Fase 1 | Completada, lista para merge |
| `iluna-fase-2` | Iluna | Fase 2 | Pendiente de crear |
| `dsiezar-fase-2` | Dsiezar | Fase 2 | Pendiente de crear |
| `dsiezar-fase-3` | Dsiezar | Fase 3 | Pendiente de crear |
