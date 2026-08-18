# InterSys — Implementación del Rediseño (DESIGN.md)

> Plan de implementación basado en `docs/DESIGN.md`
> Fecha: Agosto 2026

---

## 📋 Resumen del Diseño

**Objetivo**: Transformar la UI de InterSys de Laravel Breeze default a un **Network Management Dashboard** dark, técnico, minimal y profesional.

**Dirección visual**:
- Dark mode con paleta `#0B0F14` / `#11161D` / `#58A6FF`
- Tipografía `JetBrains Mono` (monospace)
- Layout: Sidebar (260px) + Topbar (64px) + Content
- Alta densidad de información + legibilidad
- Sin gradientes excesivos, sin sombras grandes, sin bordes demasiado redondeados

**Stack frontend** (sin cambios):
- Laravel + Blade + Livewire 3 + Tailwind CSS + Alpine.js
- SweetAlert2 (con estilo personalizado para confirmaciones)
- Lucide Icons

---

## 🔄 Estado Actual vs. Diseño

| Aspecto | Estado Actual | Diseño Objetivo |
|---------|---------------|-----------------|
| Layout | Navbar horizontal Jetstream | Sidebar + Topbar |
| Theme | Light mode blanco/gris | Dark mode `#0B0F14` |
| Font | Figtree (sans-serif) | JetBrains Mono |
| Componentes UI | Creados en P0-2 (Alpine.js) | Adaptar a dark mode + diseño |
| Iconos | Mix inconsistente | Lucide Icons unificado |
| Tablas | Tailwind default | 13px, 56px rows, uppercase headers |
| Cards | Genéricas | Stat cards con borde, sin sombra |
| Notificaciones | Alpine toast (P0-2) | Alpine toast adaptado + SweetAlert2 |

---

## 🏗️ Estrategia de Implementación

Fases progresivas siguiendo la regla de oro: **NO cambiar lógica de negocio, solo presentación**.

### Fase 1 — Fundación del Layout
**Objetivo**: Sidebar + Topbar + estructura base funcionando.

| # | Tarea | Archivos |
|---|-------|----------|
| 1.1 | Crear `layouts/app.blade.php` con sidebar + topbar + content | `resources/views/layouts/app.blade.php` |
| 1.2 | Crear componente Sidebar con navegación por secciones | `resources/views/components/sidebar.blade.php` |
| 1.3 | Crear componente Topbar con búsqueda + notificaciones + user menu | `resources/views/components/topbar.blade.php` |
| 1.4 | Agregar JetBrains Mono + paleta dark en Tailwind config | `tailwind.config.js` |
| 1.5 | Agregar estilos globales dark (body, scrollbar, transitions) | `resources/css/app.css` |

### Fase 2 — Componentes Base
**Objetivo**: Componentes reutilizables con diseño InterSys.

| # | Tarea | Archivo |
|---|-------|---------|
| 2.1 | Stat card (métrica con borde, valor, subtítulo) | `components/stat-card.blade.php` |
| 2.2 | Page header (título + descripción + acciones) | `components/page-header.blade.php` |
| 2.3 | Search input (con debounce, botón limpiar) | `components/search-input.blade.php` |
| 2.4 | Status badge (ACTIVE, BANNED, DEBTOR, etc.) | `components/status-badge.blade.php` |
| 2.5 | Empty state (icono + mensaje + acción opcional) | `components/empty-state.blade.php` |
| 2.6 | Skeleton loader (líneas animadas) | `components/skeleton.blade.php` |
| 2.7 | Loading overlay (para tablas) | `components/loading-overlay.blade.php` |
| 2.8 | Data table wrapper (header uppercase, filas 56px, hover) | `components/data-table.blade.php` |

### Fase 3 — Dashboard
**Objetivo**: Dashboard moderno con métricas + charts + actividad reciente.

| # | Tarea | Archivo |
|---|-------|---------|
| 3.1 | Rediseñar dashboard con grid de stat cards | `resources/views/dashboard.blade.php` |
| 3.2 | Integrar Chart.js con paleta dark (payments per month, statuses) | `livewire/dashboard-statistics.blade.php` |
| 3.3 | Agregar sección "Recent clients" con data table | `dashboard.blade.php` |
| 3.4 | Agregar sección "Network status" con APs online | `dashboard.blade.php` |

### Fase 4 — Páginas de Datos (Tablas)
**Objetivo**: Todas las páginas con tablas modernas.

| # | Tarea | Archivo |
|---|-------|---------|
| 4.1 | Clients: header + search + filter + tabla + acciones icono | `livewire/clients-table.blade.php` |
| 4.2 | Contracts: tabla + métricas (total clients, revenue) | `livewire/contracts-table.blade.php` |
| 4.3 | Access Points: cards + tabla híbrida + detail page | `livewire/access-table.blade.php` + `access-point/show.blade.php` |
| 4.4 | Quota: tabla + métricas (paid, pending, overdue) | `livewire/quota-table.blade.php` |
| 4.5 | Debtors: tabla + filtros year/month + export PDF | `livewire/debtors-table.blade.php` |
| 4.6 | Banned: tabla + acción unban | `clients/banned.blade.php` |
| 4.7 | Statistics: métricas + charts detallados | `livewire/statistics.blade.php` |

### Fase 5 — Formularios + UX
**Objetivo**: Formularios modernizados + estados de loading/empty/error.

| # | Tarea | Archivo |
|---|-------|---------|
| 5.1 | Input component (42px, border 1px, focus accent) | `components/ui/input.blade.php` |
| 5.2 | Select component (mismo estilo) | `components/ui/select.blade.php` |
| 5.3 | Button component (primary, secondary, danger, ghost) | `components/ui/button.blade.php` |
| 5.4 | Form section (título + descripción + fields) | `components/form-section.blade.php` |
| 5.5 | Rediseñar formulario crear cliente | `clients/create.blade.php` |
| 5.6 | Rediseñar formulario editar cliente | `clients/edit.blade.php` |
| 5.7 | Rediseñar formularios contracts + access points | `contracts/create.blade.php`, `access-point/create.blade.php` |
| 5.8 | Rediseñar formulario pago | `payments/edit.blade.php` |
| 5.9 | SweetAlert2 custom theme (paleta dark InterSys) | `resources/js/sweetalert-config.js` |
| 5.10 | Skeleton states para dashboard + tablas | Integrar `skeleton.blade.php` en vistas |

### Fase 6 — Notificaciones + Admin
**Objetivo**: Página de notifications moderna + página admin.

| # | Tarea | Archivo |
|---|-------|---------|
| 6.1 | Página notifications (All/Unread/Read tabs) | `notifications/index.blade.php` |
| 6.2 | Bell icon en topbar con unread badge | `topbar.blade.php` |
| 6.3 | Página Admin (settings placeholder) | `resources/views/admin/index.blade.php` |

---

## 🎨 Design Tokens

### Paleta de colores (Dark)

```css
:root {
  --bg-primary: #0B0F14;      /* Background principal */
  --bg-surface: #11161D;       /* Cards, sidebar */
  --bg-surface-elevated: #171D25; /* Hover, modals */
  --border: #252D36;           /* Bordes */
  --text-primary: #E6EDF3;     /* Texto principal */
  --text-secondary: #8B949E;   /* Texto secundario */
  --text-muted: #5D6670;       /* Texto deshabilitado */
  --accent: #58A6FF;           /* Accent (links, focus) */
  --success: #3FB950;          /* Verde */
  --warning: #D29922;          /* Amarillo */
  --danger: #F85149;           /* Rojo */
}
```

### Tipografía

```css
font-family: 'JetBrains Mono', monospace;
/* 400: contenido, tablas, formularios */
/* 500: navegación, labels, botones secundarios */
/* 600: títulos, valores, estadísticas */
/* 700: títulos principales (uso limitado) */
```

### Spacing Scale

```
4px 8px 12px 16px 20px 24px 32px 40px 48px
Contenido principal: padding 24px
Desktop grande: padding 32px
```

### Border Radius

```
4px 6px 8px 10px  (evitar rounded-full excepto avatars/badges)
```

### Animations

```css
transition: background-color 150ms ease,
            border-color 150ms ease,
            color 150ms ease,
            opacity 150ms ease;
/* Solo: hover, sidebar, dropdown, modal, toast, loading */
```

---

## 📐 Layout Structure

```
┌──────────────────────────────────────────────────────────────┐
│ 260px Sidebar        │ 64px Topbar                            │
│                      ├────────────────────────────────────────┤
│ INTERSYS             │ Page Header            [+ New Client]  │
│ Network Management   │                                            │
│                      │ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐    │
│ MAIN                 │ │CLIENT│ │CONTR.│ │BANDW.│ │DEBTOR│    │
│ ▣ Dashboard          │ │ 139  │ │  7   │ │1.2TB │ │  18  │    │
│ ◎ Clients            │ └──────┘ └──────┘ └──────┘ └──────┘    │
│ ◇ Contracts          │                                            │
│ ⌁ Access Points      │ ┌─────────────────┐ ┌──────────────┐    │
│ ▤ Quota              │ │ Client activity │ │ Network stat │    │
│                      │ │    CHART        │ │ ● AP-01     │    │
│ MONITORING           │ │                 │ │ ● AP-02     │    │
│ ⚠ Debtors            │ └─────────────────┘ └──────────────┘    │
│ ⊘ Banned             │                                            │
│ ◈ Statistics         │ ┌──────────────────────────────────┐    │
│                      │ │ Recent clients          TABLE    │    │
│ SYSTEM               │ └──────────────────────────────────┘    │
│ ● Notifications      │                                            │
│ ⚙ Admin              │                                            │
│                      │                                            │
└──────────────────────┴────────────────────────────────────────────┘
```

---

## 🔧 Componentes a Crear/Adaptar

### Desde P0-2 (ya existen, adaptar a dark)
- `ui/toast` ✅ → Adaptar colores dark
- `ui/confirm` ✅ → Adaptar colores dark + integrar con SweetAlert2
- `ui/modal` ✅ → Adaptar colores dark
- `ui/loading` ✅ → Adaptar colores dark
- `ui/badge` ✅ → Renombrar a status-badge, adaptar variantes

### Nuevos (diseño específico)
| Componente | Propósito |
|------------|-----------|
| `sidebar.blade.php` | Navegación lateral con secciones |
| `topbar.blade.php` | Barra superior con búsqueda + user |
| `stat-card.blade.php` | Métrica con valor + subtítulo |
| `page-header.blade.php` | Título de página + acciones |
| `search-input.blade.php` | Búsqueda con debounce + clear |
| `status-badge.blade.php` | Estados ACTIVE/BANNED/DEBTOR |
| `skeleton.blade.php` | Líneas animadas de carga |
| `loading-overlay.blade.php` | Overlay para tablas |
| `data-table.blade.php` | Wrapper de tabla estilizado |
| `ui/input.blade.php` | Input 42px dark |
| `ui/select.blade.php` | Select 42px dark |
| `ui/button.blade.php` | Botones primary/secondary/danger/ghost |
| `form-section.blade.php` | Sección de formulario |

---

## ⚠️ Decisiones Pendientes

### 1. SweetAlert2 vs Alpine confirm
El diseño menciona SweetAlert2 para confirmaciones. En P0-2 creamos `ui/confirm` con Alpine.

**Opción A**: Mantener SweetAlert2 con custom theme (como dice DESIGN.md)
**Opción B**: Usar nuestro Alpine confirm component (ya funciona, zero deps)

**Recomendación**: **Opción B** — ya lo tenemos, es consistente con el stack Alpine, zero dependencias externas. Para confirmaciones de delete/ban usar el `x-confirm` que ya existe.

### 2. Lucide Icons
El diseño recomienda Lucide Icons. Actualmente usamos SVGs inline.

**Opción A**: Instalar `@ lucide-icons` paquete npm
**Opción B**: Crear componentes Blade con SVGs Lucide (sin dependencia npm)

**Recomendación**: **Opción B** — crear un componente `components/icon.blade.php` que reciba el nombre y renderice el SVG correspondiente. Zero deps, full control.

### 3. Tailwind Config Extensión
La paleta dark requiere extender `tailwind.config.js` con los colores custom.

---

## 📊 Definition of Done

- [ ] Sidebar funcional con navegación + active state
- [ ] Topbar con búsqueda + notificaciones + user menu
- [ ] Dark mode completo aplicado a todas las páginas
- [ ] JetBrains Mono aplicada globalmente
- [ ] Dashboard con stat cards + charts + recent clients
- [ ] Tablas con estilo InterSys (13px, 56px rows, uppercase headers)
- [ ] Formularios con inputs 42px + secciones
- [ ] Empty states + skeleton loaders + loading overlays
- [ ] Status badges (ACTIVE, BANNED, DEBTOR, etc.)
- [ ] Confirmaciones de delete/ban con Alpine confirm
- [ ] Toast notifications top-right
- [ ] Responsive: mobile drawer + tablet + desktop
- [ ] SweetAlert2 custom theme (si se decide mantener)
- [ ] Lucide Icons integrados

---

## 🎬 Orden Recomendado de Implementación

```
Fase 1: Layout (sidebar + topbar + dark theme)
    ↓
Fase 2: Componentes base (stat-card, badge, skeleton, etc.)
    ↓
Fase 3: Dashboard (métricas + charts + tablas)
    ↓
Fase 4: Páginas de datos (Clients, Contracts, AP, Quota, Debtors, Banned, Statistics)
    ↓
Fase 5: Formularios + UX (inputs, buttons, SweetAlert2, skeletons)
    ↓
Fase 6: Notifications + Admin
```

---

**Document version**: 1.0
**Source**: `docs/DESIGN.md` (1751 líneas, 76 secciones)
**Creado**: Agosto 2026
