# DESIGN.md

# InterSys — Admin Dashboard Design System

## 1. Objetivo

Modernizar completamente la interfaz de InterSys manteniendo la funcionalidad actual de Laravel + Livewire, pero reemplazando la apariencia visual por una interfaz administrativa moderna, profesional y consistente.

La aplicación debe transmitir:

- Control
- Estabilidad
- Información clara
- Gestión de infraestructura
- Profesionalismo
- Velocidad
- Simplicidad

La interfaz debe sentirse como una plataforma SaaS profesional de administración de clientes y red.

---

# 2. Dirección visual

## Concepto

> Modern Network Management Dashboard

La estética combina:

- SaaS administrativo
- Network management
- Dashboard técnico
- Minimalismo
- Interfaces enterprise

Evitar:

- Gradientes excesivos
- Sombras grandes
- Bordes demasiado redondeados
- Colores saturados
- Interfaces "gaming"
- Exceso de iconos
- Cards innecesarias
- Animaciones exageradas

La interfaz debe sentirse sólida y funcional.

---

# 3. Tipografía

## Font principal

Utilizar:

```css
font-family: 'JetBrains Mono', monospace;
```

Importación:

<link
    rel="preconnect"
    href="https://fonts.googleapis.com"
>


<link
    rel="preconnect"
    href="https://fonts.gstatic.com"
    crossorigin
>


<link
    href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap"
    rel="stylesheet"
>
Pesos
Regular
400

Uso:

contenido
tablas
descripciones
formularios
Medium
500

Uso:

navegación
labels
botones secundarios
Semi Bold
600

Uso:

títulos
valores importantes
estadísticas
Bold
700

Uso limitado:

títulos principales
métricas destacadas
4. Layout principal

La aplicación abandona el navbar horizontal tradicional de Laravel.

Utilizar:

┌──────────────────────────────────────────────────────────────┐
│ Sidebar        │ Topbar                                      │
│                ├──────────────────────────────────────────────┤
│                │                                              │
│ Navigation     │ Page Header                                  │
│                │                                              │
│ Dashboard      │                                              │
│ Clients        │ Content                                      │
│ Contracts      │                                              │
│ Access Points  │                                              │
│ Quota          │                                              │
│ Debtors        │                                              │
│ Banned         │                                              │
│ Statistics     │                                              │
│                │                                              │
│                │                                              │
│                │                                              │
│                │                                              │
│ Admin          │                                              │
└────────────────┴──────────────────────────────────────────────┘
5. Sidebar

La sidebar debe permanecer visible en desktop.

Ancho:

260px

En mobile:

280px

La sidebar debe ser colapsable en desktop.

Estados:

Expanded
Collapsed
Mobile drawer
6. Sidebar structure
INTERSYS
Network Management


MAIN


▣ Dashboard
◎ Clients
◇ Contracts
⌁ Access Points
▤ Quota


MONITORING


⚠ Debtors
⊘ Banned
◈ Statistics


SYSTEM


● Notifications
⚙ Admin

Los iconos deben utilizar una librería consistente.

Recomendación:

Lucide Icons

No utilizar emojis.

7. Logo

Parte superior:

INTERSYS
NETWORK MANAGEMENT

Ejemplo:

┌─────────────────────────┐
│ INTERSYS                │
│ NETWORK MANAGEMENT      │
└─────────────────────────┘

El logo debe ser tipográfico.

No utilizar un logo gigante.

8. Sidebar active state

La página activa debe tener:

fondo ligeramente elevado
borde lateral
icono destacado
texto con mayor contraste

Ejemplo:

│
│  ▣ Dashboard
│
│  ┌──────────────────────┐
│  │ ▣ Clients            │
│  └──────────────────────┘
│
│  ◇ Contracts
│  ⌁ Access Points

El estado activo debe ser inmediatamente reconocible.

9. Topbar

La topbar reemplaza el navbar actual.

Altura:

64px

Contenido:

┌──────────────────────────────────────────────────────────────┐
│ ☰    Search...                         🔔    ADMIN ▼         │
└──────────────────────────────────────────────────────────────┘

Elementos:

Left
sidebar toggle
breadcrumb opcional
búsqueda global
Right
notifications
user menu
10. User menu

Al hacer click:

┌─────────────────────────────┐
│ ADMIN                       │
│ admin@example.com           │
├─────────────────────────────┤
│ Profile                     │
│ Settings                    │
│                             │
│ Logout                      │
└─────────────────────────────┘

No utilizar un dropdown enorme.

11. Notifications

El icono de notificaciones debe mostrar:

🔔
●

donde el punto indica notificaciones pendientes.

Al abrir:

┌────────────────────────────────┐
│ Notifications                  │
├────────────────────────────────┤
│ ● Payment pending              │
│   Client #103                  │
│                                │
│ ● Client banned                │
│   Client #121                  │
│                                │
│ ○ New client                   │
│   Client #140                  │
├────────────────────────────────┤
│ View all notifications         │
└────────────────────────────────┘
12. Dashboard

El Dashboard debe ser la pantalla principal.

Layout:

Dashboard
Network overview


┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│ CLIENTS      │ │ CONTRACTS    │ │ BANDWIDTH    │ │ DEBTORS      │
│              │ │              │ │              │ │              │
│ 139          │ │ 7            │ │ 1.2 TB       │ │ 18           │
│ +4 this month│ │ active       │ │ sold         │ │ attention     │
└──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘




┌─────────────────────────────────────┐ ┌─────────────────────────┐
│ Client activity                     │ │ Network status          │
│                                     │ │                         │
│             CHART                   │ │ ● Access Point 01      │
│                                     │ │ ● Access Point 02      │
│                                     │ │ ● Access Point 03      │
└─────────────────────────────────────┘ └─────────────────────────┘




┌──────────────────────────────────────────────────────────────────┐
│ Recent clients                                                   │
│                                                                  │
│ Name              IP Address        Contract        Status       │
│ Juan Perez        192.168.1.10      50 MB           Active       │
│ ...                                                              │
└──────────────────────────────────────────────────────────────────┘
13. Dashboard metrics

Las métricas principales:

Total Clients
Active Clients
Banned Clients
Debtors
Contracts
Bandwidth Sold
Access Points
Monthly Revenue

No mostrar todas simultáneamente.

Priorizar:

Clients
Bandwidth Sold
Debtors
Revenue
14. Metric cards

Las cards deben ser simples.

┌──────────────────────────────┐
│ TOTAL CLIENTS                │
│                              │
│ 139                          │
│                              │
│ +4.2% this month             │
└──────────────────────────────┘

Características:

border: 1px solid
border-radius: 10px
padding: 20px

No usar sombras fuertes.

15. Status indicators

Estados:

ACTIVE
BANNED
DEBTOR
INACTIVE
ONLINE
OFFLINE

Utilizar badges.

Ejemplo:

[ ACTIVE ]
[ BANNED ]
[ DEBTOR ]

Los badges deben ser pequeños y discretos.

16. Clients

Header:

Clients


Manage registered clients

Acciones:

+ New Client
Export

Layout:

┌──────────────────────────────────────────────────────────────┐
│ Search clients...                         Contract ▼         │
└──────────────────────────────────────────────────────────────┘




┌──────────────────────────────────────────────────────────────┐
│ Name       IP Address       Contract       Status      Actions│
├──────────────────────────────────────────────────────────────┤
│ Juan Perez 192.168.1.10     50 MB          ACTIVE       ...   │
│ Maria Lopez192.168.1.11     100 MB         ACTIVE       ...   │
└──────────────────────────────────────────────────────────────┘
17. Tables

Las tablas son un componente central de la aplicación.

Características:

font-size: 13px
border-collapse: separate
border-spacing: 0

Header:

uppercase
font-size: 11px
letter-spacing: 0.08em

Filas:

height: 56px

Hover:

background: subtle

Evitar líneas demasiado fuertes.

18. Table actions

Cada fila puede tener:

View
Edit
Delete
Ban
Unban

No mostrar cinco botones gigantes.

Utilizar:

icon buttons

Ejemplo:

[eye] [edit] [ban] [...]

Con tooltip.

19. Pagination

La paginación debe estar integrada debajo de las tablas.

Showing 1–10 of 139


< Previous    1  2  3  4  5    Next >

Estados:

disabled
active
hover

Debe funcionar correctamente con Livewire.

No recargar toda la página.

20. Search

Componente global:

┌──────────────────────────────────────┐
│ ⌕ Search clients...                  │
└──────────────────────────────────────┘

Características:

debounce
Livewire
clear button
resultados instantáneos

Para Livewire:

wire:model.live.debounce.300ms="search"
21. Contracts

Página:

Contracts


Manage available plans

Tabla:

Contract
Bandwidth
Price
Clients
Status
Actions

Ejemplo:

50 MB
100 MB
200 MB
500 MB

Mostrar también:

Total clients
Bandwidth sold
Revenue
22. Access Points

La página debe sentirse más técnica.

Cada Access Point:

┌─────────────────────────────────────┐
│ AP-01                               │
│                                     │
│ SSID                                │
│ Frequency                           │
│ IP                                  │
│ Location                            │
│                                     │
│ 32 CLIENTS                          │
│                                     │
│ [ View clients ]                    │
└─────────────────────────────────────┘

Opcionalmente combinar:

Cards
+
Table
23. Access Point detail

Header:

Access Point
AP-01


192.168.10.1

Metrics:

Clients
Bandwidth
Status

Debajo:

Connected Clients

utilizando el componente Livewire existente:

@livewire('access-clients-table', [
    'access_point_id' => $access_point->id
])
24. Quota

La página de Quota debe enfocarse en administración de cuotas.

Mostrar:

Total quotas
Paid
Pending
Overdue

Tabla:

Quota
Due date
Amount
Status
Clients
Actions
25. Debtors

La página debe tener una identidad visual de alerta, pero sin utilizar rojo en toda la pantalla.

Header:

Debtors


Clients with outstanding payments

Filtros:

Year ▼
Month ▼
Search

Acciones:

Export PDF

Tabla:

Client
IP
Contract
Debt
Last payment
Actions
26. Banned

Header:

Banned Clients


Restricted client accounts

Mostrar:

Client
IP
Contract
Reason
Banned at
Actions

Acción principal:

Unban
27. Statistics

Statistics debe ser una página orientada a análisis.

Layout:

Statistics


Overview


┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│ Revenue      │ │ Clients      │ │ Bandwidth    │
└──────────────┘ └──────────────┘ └──────────────┘




┌─────────────────────────────────────────────────┐
│ Revenue over time                               │
│                                                 │
│                    CHART                        │
│                                                 │
└─────────────────────────────────────────────────┘




┌────────────────────────────┐
│ Contracts distribution     │
│                            │
│          CHART             │
└────────────────────────────┘
28. Forms

Todos los formularios deben utilizar un diseño consistente.

Ejemplo:

Create Client


Personal information


First name
┌────────────────────────────────────┐
│                                    │
└────────────────────────────────────┘


Last name
┌────────────────────────────────────┐
│                                    │
└────────────────────────────────────┘




Network


IP Address
┌────────────────────────────────────┐
│                                    │
└────────────────────────────────────┘


Access Point
┌────────────────────────────────────┐
│ Select access point            ▼   │
└────────────────────────────────────┘




[ Cancel ]                         [ Create Client ]
29. Form sections

Los formularios grandes deben dividirse:

Personal Information


Network Information


Contract Information


Additional Information

Cada sección:

title
description
fields
30. Input design

Inputs:

height: 42px
border-radius: 7px
border: 1px solid

Estados:

default
hover
focus
error
disabled

Focus:

outline: none
border-color: accent
31. Validation errors

Los errores deben aparecer debajo del campo.

Ejemplo:

IP Address


┌──────────────────────────────────┐
│ 192.168.1.500                    │
└──────────────────────────────────┘


Invalid IP address.

El campo debe conservar el valor ingresado cuando sea posible.

No mostrar errores únicamente mediante alertas.

32. SweetAlert2

Actualmente se utiliza:

SweetAlert2 v11

vía CDN.

Mantenerlo inicialmente para:

Delete confirmation
Ban confirmation
Unban confirmation
Success messages
Critical errors

Pero adaptar el diseño visual para que coincida con InterSys.

Ejemplo conceptual:

┌─────────────────────────────────┐
│                                 │
│       Delete client?            │
│                                 │
│ This action cannot be undone.   │
│                                 │
│ Cancel              Delete      │
│                                 │
└─────────────────────────────────┘

No utilizar estilos predeterminados de SweetAlert2 sin personalización.

33. Toast notifications

Para operaciones exitosas utilizar Toast.

Ejemplo:

                         ┌──────────────────────────┐
                         │ ✓ Client created          │
                         │   Client #140 created.    │
                         └──────────────────────────┘

Posición:

top-right

Duración:

3000–4000ms

No interrumpir el flujo del usuario.

34. Loading states

Livewire debe mostrar estados de carga.

Ejemplo:

wire:loading

Para tablas:

┌──────────────────────────────────────────────┐
│                                              │
│              Loading clients...              │
│                                              │
└──────────────────────────────────────────────┘

Preferir skeletons sobre spinners cuando se cargan tablas.

35. Skeleton

Ejemplo:

┌──────────────────────────────────────────────┐
│ ███████████                                  │
│                                              │
│ ███████████████████                          │
│                                              │
│ █████████████████████████                    │
└──────────────────────────────────────────────┘

Utilizar skeleton para:

Dashboard
Tables
Statistics
Access Points
36. Modal

Los modales deben utilizarse para:

Confirmaciones
Ediciones pequeñas
Información rápida

No utilizar modales para formularios extremadamente grandes.

Modal:

max-width: 640px
border-radius: 10px

Overlay:

background: rgba(...)
backdrop-filter: blur(...)
37. Responsive design

La aplicación debe funcionar en:

Desktop
Tablet
Mobile

Breakpoints:

640px
768px
1024px
1280px
1536px
38. Mobile navigation

En mobile:

┌──────────────────────────────┐
│ ☰     INTERSYS       🔔      │
└──────────────────────────────┘

Sidebar:

drawer

Al abrir:

┌───────────────────────┐
│ INTERSYS              │
│                       │
│ Dashboard             │
│ Clients               │
│ Contracts             │
│ Access Points         │
│ Quota                 │
│ Debtors               │
│ Banned                │
│ Statistics            │
│                       │
│ Notifications         │
│ Admin                 │
└───────────────────────┘
39. Colors

Utilizar una paleta neutra.

Background
#0B0F14
Surface
#11161D
Surface elevated
#171D25
Border
#252D36
Primary text
#E6EDF3
Secondary text
#8B949E
Muted
#5D6670
Accent
#58A6FF
Success
#3FB950
Warning
#D29922
Danger
#F85149
40. Light mode

La aplicación debe estar preparada para soportar Light Mode.

No implementar inmediatamente si no es necesario.

La arquitectura visual debe permitir:

Dark
Light

sin modificar componentes individualmente.

41. Borders

Preferir:

1px solid

sobre sombras.

Ejemplo:

border: 1px solid #252D36;

Las cards no deben parecer flotantes.

42. Border radius

Utilizar valores pequeños:

4px
6px
8px
10px

Evitar:

rounded-full
rounded-3xl

excepto para:

avatars
status indicators
pequeños badges
43. Shadows

Las sombras deben ser mínimas.

Evitar:

shadow-2xl

Preferir:

shadow-sm

o ninguna sombra.

La jerarquía debe provenir principalmente de:

background
border
spacing
typography
44. Spacing

Sistema base:

4px
8px
12px
16px
20px
24px
32px
40px
48px

Contenido principal:

padding: 24px

Desktop grande:

padding: 32px
45. Page header

Todas las páginas deben utilizar:

Page title
Description
Actions

Ejemplo:

Clients
Manage and monitor registered clients


                         + New Client

Esto reemplaza títulos aislados.

46. Breadcrumbs

Utilizar breadcrumbs solamente cuando exista navegación jerárquica.

Ejemplo:

Access Points / AP-01

No utilizar breadcrumbs en:

Dashboard
Clients
Contracts
Statistics
47. Buttons
Primary
+ New Client

Uso:

crear
guardar
confirmar
Secondary
Export
Filter
Cancel
Danger
Delete
Ban
Ghost

Para acciones secundarias:

View
More
48. Icons

Utilizar una única librería.

Recomendación:

Lucide Icons

Tamaños:

16px
18px
20px
24px

No mezclar:

FontAwesome
Heroicons
Lucide
Bootstrap Icons

dentro de la misma interfaz.

49. Accessibility

Todos los componentes interactivos deben:

tener focus visible
tener labels
tener aria-label cuando sea necesario
poder utilizarse con teclado
tener contraste suficiente
no depender exclusivamente del color

Los botones de icono deben tener:

aria-label
50. Livewire

El rediseño no debe romper Livewire.

Mantener:

wire:model.live
wire:click
wire:loading
wire:target
wire:key

La navegación debe permanecer compatible con Livewire.

51. Livewire tables

Todas las tablas dinámicas deben mantener:

Search
Filters
Pagination
Loading
Empty state

Estados:

Loading
Loaded
Empty
Error
52. Empty states

Cuando no existen registros:

┌────────────────────────────────────────────┐
│                                            │
│              No clients found              │
│                                            │
│      Try changing your search filters      │
│                                            │
│             + Create Client                │
│                                            │
└────────────────────────────────────────────┘

No mostrar simplemente:

No data
53. Error states

Los errores generales deben ser claros.

Something went wrong


We couldn't complete this operation.


[ Try again ]

No mostrar stack traces al usuario.

54. Delete workflow

Eliminar un cliente:

User clicks Delete
        ↓
SweetAlert2 confirmation
        ↓
User confirms
        ↓
Livewire / Controller
        ↓
Soft delete
        ↓
Toast success
        ↓
Table refresh
55. Ban workflow
Ban
 ↓
Confirmation
 ↓
Update is_banned
 ↓
Notification
 ↓
Refresh table

Visual:

[ BAN CLIENT ]

Danger action.

Para desbanear:

[ UNBAN ]

Success/primary action.

56. Dashboard navigation

Sidebar:

MAIN


Dashboard
Clients
Contracts
Access Points
Quota


MONITORING


Debtors
Banned
Statistics


SYSTEM


Notifications
Admin

Mantener este orden.

57. Admin

Admin debe contener:

System Settings
User Management
Application Settings
Security

Separar visualmente:

Application
Security
Administration
58. Notifications page

La página completa de notifications debe permitir:

All
Unread
Read

Cada notification:

● Client payment pending
  Client: Juan Perez
  5 minutes ago

Acciones:

Mark as read
Delete
59. Animation

Las animaciones deben ser discretas.

Duración:

150ms
200ms

Utilizar únicamente para:

hover
sidebar
dropdown
modal
toast
loading

Evitar animaciones permanentes.

60. Global transition
transition:
    background-color 150ms ease,
    border-color 150ms ease,
    color 150ms ease,
    opacity 150ms ease;
61. Component architecture

Crear componentes reutilizables:

resources/views/components/


app-layout.blade.php
sidebar.blade.php
topbar.blade.php
page-header.blade.php
stat-card.blade.php
search-input.blade.php
data-table.blade.php
status-badge.blade.php
empty-state.blade.php
loading-state.blade.php
confirm-button.blade.php
toast.blade.php
modal.blade.php
62. Layout architecture

Utilizar:

resources/views/layouts/app.blade.php

como layout principal.

Conceptualmente:

<x-app-layout>


    <x-slot name="header">
        ...
    </x-slot>


    {{ $slot }}


</x-app-layout>

El layout debe contener:

Sidebar
Topbar
Main content
Toast container
Modal container
63. Main content

Desktop:

margin-left: 260px

Topbar:

position: sticky
top: 0

Main:

min-height: calc(100vh - 64px)

Contenido:

max-width: 1600px
margin: auto
padding: 24px 32px
64. Visual hierarchy

Prioridad:

1. Page title
2. Main metric / action
3. Primary content
4. Secondary information
5. Metadata

No hacer que todos los elementos tengan la misma importancia.

65. Dashboard density

La aplicación administra información técnica.

No utilizar demasiado espacio vacío.

Objetivo:

high information density
+
strong readability

La interfaz debe mostrar bastante información sin parecer saturada.

66. Tables vs Cards

Utilizar:

Tables

Para:

Clients
Debtors
Banned
Quota
Contracts
Cards

Para:

Dashboard metrics
Access Points
Statistics summaries
Charts

Para:

Statistics
Revenue
Clients growth
Bandwidth sold
67. Charts

Los gráficos deben seguir la identidad visual.

Evitar:

gráficos 3D
colores excesivos
gradients fuertes

Preferir:

line charts
bar charts
donut charts
area charts

Con pocos colores.

68. Responsive tables

En mobile no comprimir 15 columnas.

Opciones:

horizontal scroll

o:

responsive card rows

Para información crítica:

Name
IP
Contract
Status
Actions

son las columnas prioritarias.

69. Performance

El diseño debe evitar:

imágenes innecesarias
librerías duplicadas
JS innecesario
animaciones pesadas

Priorizar:

Tailwind CSS
Livewire
Alpine.js
Lucide
SweetAlert2
70. Frontend stack

Mantener:

Laravel
Blade
Livewire
Tailwind CSS
Alpine.js
SweetAlert2 v11
Lucide Icons
JetBrains Mono

No introducir React/Vue para este rediseño.

La interfaz actual ya está basada en Laravel + Livewire, por lo que el objetivo es modernizar visualmente sin aumentar innecesariamente la complejidad.

71. Design principles
01 — Clarity

Cada pantalla debe dejar claro:

Where am I?
What am I seeing?
What can I do?
02 — Consistency

Los mismos componentes deben verse iguales en toda la aplicación.

03 — Density

Mostrar información suficiente para administración profesional.

04 — Feedback

Cada acción importante debe generar feedback.

05 — Safety

Acciones destructivas requieren confirmación.

06 — Speed

La interfaz debe sentirse inmediata.

72. Final visual target

El resultado final debe sentirse como:

Modern SaaS
        +
Network Management
        +
Enterprise Admin
        +
Developer Tooling

La aplicación NO debe parecer:

Laravel Breeze default
Bootstrap admin template
Generic dashboard
Gaming UI
Consumer application

Debe sentirse como una plataforma propia:

INTERSYS
NETWORK MANAGEMENT PLATFORM
73. Migration strategy

El rediseño debe realizarse progresivamente.

Phase 1

Layout:

Sidebar
Topbar
Main content
Phase 2

Componentes:

Buttons
Inputs
Tables
Badges
Cards
Pagination
Search
Phase 3

Dashboard:

Metrics
Charts
Recent activity
Network status
Phase 4

Pages:

Clients
Contracts
Access Points
Quota
Debtors
Banned
Statistics
Notifications
Admin
Phase 5

UX:

Loading
Skeletons
Toast
SweetAlert2
Empty states
Error states
Responsive
Phase 6

Polish:

Animations
Accessibility
Performance
Dark mode refinement
74. Important implementation rule

No cambiar la lógica de negocio durante el rediseño.

El objetivo inicial es:

CURRENT BACKEND
      │
      ▼
CURRENT LIVEWIRE
      │
      ▼
NEW UI

No modificar innecesariamente:

Controllers
Models
Database
Routes
Livewire logic
Validation
Authentication

Primero se moderniza la presentación.

Después se pueden realizar mejoras funcionales independientemente.

75. Definition of Done

El rediseño estará completo cuando:

 Sidebar moderna implementada
 Topbar implementada
 Responsive navigation
 Dashboard moderno
 Clients moderno
 Contracts moderno
 Access Points moderno
 Quota moderno
 Debtors moderno
 Banned moderno
 Statistics moderno
 Notifications moderno
 Admin moderno
 Formularios modernizados
 Search modernizado
 Pagination modernizada
 Loading states
 Skeleton states
 Empty states
 Error states
 SweetAlert2 personalizado
 Toast notifications
 Status badges
 Responsive tables
 Accessibility básica
 JetBrains Mono aplicada
 Componentes reutilizables
 Consistencia visual completa
76. Final direction

InterSys debe pasar de:

Laravel default admin

a:

INTERSYS
Network Management Platform

con una interfaz:

dark
technical
minimal
dense
professional
responsive
fast
consistent

La interfaz debe parecer construida específicamente para administrar una infraestructura real de clientes, contratos, cuotas y puntos de acceso.



### Mi recomendación para implementarlo


No intentaría rediseñar las 9 páginas de golpe. Haría primero **`app.blade.php + sidebar + topbar + sistema de botones/inputs/tables`**. Una vez que esa base esté bien, las páginas `Clients`, `Debtors` y `Banned` pueden reutilizar prácticamente todos los componentes y nos van a servir como referencia para el resto.


Y mantendría **Livewire + Tailwind + Alpine + SweetAlert2**, sin meter React ni otra capa innecesaria.