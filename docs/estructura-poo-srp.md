# Estructura POO y Single Responsibility Principle (SRP)

Documento de referencia para la implementación del proyecto activos ICBF: una sola responsabilidad por clase y diseño orientado a objetos.

---

## 1. Principio de responsabilidad única (SRP)

**Una clase debe tener una sola razón para cambiar.** Cada componente del proyecto debe tener una responsabilidad bien delimitada y no mezclar orígenes de datos, formatos o flujos distintos en la misma clase.

---

## 2. Capas y responsabilidades

### 2.1. Models (app/Models/)

**Responsabilidad:** Representar una entidad de dominio y su persistencia (Eloquent). Relaciones, atributos, casts, accesors/mutators simples. **No** contienen lógica de negocio compleja ni reglas de importación/exportación.

| Clase | Responsabilidad única |
|-------|------------------------|
| `User` | Usuario de la aplicación (auth). |
| `Cpu` | Entidad CPU (equipo principal); relaciones con Monitor, Teclado, Mouse, Persona. |
| `Monitor` | Entidad Monitor; pertenece a una CPU. |
| `Teclado` | Entidad Teclado; pertenece a una CPU. |
| `Mouse` | Entidad Mouse; pertenece a una CPU. |
| `Department` | Entidad departamento (nombre único); 3FN: **Department** en All Users.xlsx tiene duplicidad, por tanto tabla propia; Persona pertenece a un Department (department_id). |
| `Persona` | Una sola entidad: directorio activo (All Users) + propietario (CRV). Llave: documento_identidad; **department_id** (FK a Department). Se importan **todas** las personas; **account_status** importante. **Una persona tiene muchos activos asignados** (hasMany CPU, hasMany ActivoCrv). |
| `Regional` | Entidad regional (codigo PK, nombre); usada por ActivoCrv (3FN: no duplicar nombre en activos_crv). |
| `Bodega` | Entidad bodega (codigo PK, nombre); usada por ActivoCrv (3FN). |
| `ActivoCrv` | Activo del reporte CRV (placa, producto, descripcion, etc.); pertenece a **Persona** (persona_id), **Regional** (regional_codigo), **Bodega** (bodega_codigo). |

Cada modelo solo conoce su tabla, sus relaciones y sus reglas de atributos (fillable, casts). La **unicidad** (documento_identidad, placa, etc.) se declara en migraciones e índices; la validación en creación/actualización va en FormRequest o en reglas del import.

---

### 2.2. Controllers (app/Http/Controllers/)

**Responsabilidad:** Orquestar la petición HTTP (entrada/salida) y delegar en servicios, modelos o acciones. Mantener controladores **finos**: no lógica de negocio pesada, no lectura directa de Excel.

| Controlador | Responsabilidad única |
|-------------|------------------------|
| `CpuController` | CRUD HTTP para CPU (index, create, store, edit, update, destroy). Delega validación a FormRequest y persistencia al modelo. |
| `MonitorController` | CRUD HTTP para Monitor. |
| `TecladoController` | CRUD HTTP para Teclado. |
| `MouseController` | CRUD HTTP para Mouse. |
| `PersonaController` | CRUD HTTP para Persona (directorio activo y propietario de activos; una persona, muchos activos). |
| `ActivoCrvController` | CRUD HTTP para Activo CRV (pertenece a Persona). |
| `AllUsersImportController` | Mostrar formulario de importación All Users; recibir archivo y ejecutar **solo** el import de All Users (delega a AllUsersImport o servicio). |
| `CrvReporteImportController` | Mostrar formulario de importación CRV; recibir archivo y ejecutar **solo** el import CRV (delega a CrvReporteImport o servicio). |
| `ParqueExportController` | Generar y descargar el reporte final en formato Toma_Parque (delega a ParqueExport o servicio). |

**No** un único `ImportExportController` que mezcle All Users, CRV y export: son tres razones de cambio distintas (tres orígenes/formato). **SRP:** un controlador por flujo (All Users import, CRV import, Parque export).

---

### 2.3. Imports (app/Imports/)

**Responsabilidad:** Leer un **único** formato de archivo y mapear filas a modelos. **Antes de persistir en la base de datos** se debe aplicar normalización a todos los valores de texto (trim, colapsar espacios, eliminar caracteres de control) mediante una clase reutilizable (StringNormalizer) usada por ambos imports.

| Clase | Responsabilidad única |
|-------|------------------------|
| `AllUsersImport` | Leer All Users.xlsx (hoja "All Users", fila 7 = encabezados); importar **todas** las personas; crear/obtener `Department` por nombre (col F — 3FN: Department tiene duplicidad en Excel); mapear a `Persona` (department_id, account_status, etc.); validar y persistir. No conoce CRV ni export. |
| `CrvReporteImport` | Leer 20260310_crvReporte.xls; interpretar jerarquía (regional, bodega, propietario = Persona por documento_identidad, activos); mapear a `Persona` (crear/actualizar por documento_identidad) y `ActivoCrv` (persona_id). No conoce All Users ni export. |

Cada import tiene **una** fuente de datos y **un** formato; si en el futuro hubiera otro Excel de personas, sería otro Import (o otra hoja manejada por el mismo AllUsersImport si la responsabilidad sigue siendo "importar personas desde All Users").

---

### 2.4. Exports (app/Exports/)

**Responsabilidad:** Generar **un** tipo de archivo de salida a partir de los modelos.

| Clase | Responsabilidad única |
|-------|------------------------|
| `ParqueExport` | Construir el .xlsx del reporte final (formato Toma_Parque_2026.xlsx) a partir de CPU, Monitor, Teclado, etc. No importa; no genera otros formatos. |

Si más adelante se pidiera otro reporte (por ejemplo PDF o otro Excel), sería una nueva clase de export (ej. `OtroReporteExport`).

---

### 2.5. Form Requests (app/Http/Requests/)

**Responsabilidad:** Validar **una** petición (un formulario o un tipo de import). Una clase por contexto de validación.

| Clase | Responsabilidad única |
|-------|------------------------|
| `StoreCpuRequest`, `UpdateCpuRequest` | Reglas de validación para crear/actualizar CPU. |
| `StoreMonitorRequest`, `UpdateMonitorRequest` | Idem para Monitor. |
| (Análogo para Teclado, Mouse, Persona, ActivoCrv.) | |
| `ImportAllUsersRequest` | Validar archivo subido para import All Users (tipo, tamaño, extensión). |
| `ImportCrvReporteRequest` | Validar archivo subido para import CRV (.xls). |

Cada Request solo conoce las reglas de **esa** acción; no mezcla validación de CPU con validación de import.

---

### 2.6. Servicios opcionales (app/Services/)

Si la lógica de import/export crece (transacciones, resúmenes, notificaciones), se puede extraer a servicios con **una** responsabilidad cada uno:

| Clase | Responsabilidad única |
|-------|------------------------|
| `AllUsersImportService` | Orquestar: recibir archivo, ejecutar AllUsersImport, transacción, normalización previa, resumen (importados/rechazados/errores). |
| `CrvReporteImportService` | Orquestar: recibir archivo, ejecutar CrvReporteImport, transacción, resumen. |
| `ParqueExportService` | Orquestar: aplicar filtros, ejecutar ParqueExport, devolver descarga. |

Los controladores de import/export delegarían en estos servicios en lugar de llamar directamente al Import/Export si se quiere mantener el controlador aún más fino. Si el flujo es simple (controlador → Import/Export → respuesta), los servicios son opcionales.

---

### 2.7. Utilidades (app/Support o app/Services)

**Responsabilidad única por clase:**

| Clase | Responsabilidad única |
|-------|------------------------|
| `StringNormalizer` (o `NormalizadorCadena`) | Normalizar cadenas para importación: trim, colapsar espacios, eliminar caracteres de control. **Obligatorio** usarlo en AllUsersImport y CrvReporteImport antes de escribir en BD; no conoce modelos ni Excel. |

Así la “normalización” tiene una sola razón para cambiar (criterios de limpieza) y se aplica de forma consistente antes de persistir y se reutiliza en todos los imports.

---

## 3. Resumen SRP por tipo de componente

- **Model:** una entidad de dominio + persistencia.
- **Controller:** un flujo HTTP (un recurso CRUD o un flujo de import/export).
- **Import:** un formato de archivo de entrada → un conjunto de modelos.
- **Export:** un formato de archivo de salida ← modelos.
- **FormRequest:** validación de un tipo de petición.
- **Service (opcional):** orquestación de un caso de uso (un import o un export).
- **Normalizer:** reglas de normalización de cadenas.

Ninguna clase debe: importar dos formatos distintos, exportar dos formatos distintos, o mezclar validación de recursos no relacionados.

---

## 4. Estructura de carpetas sugerida (resumen)

```
app/
├── Models/
│   ├── User.php
│   ├── Cpu.php, Monitor.php, Teclado.php, Mouse.php
│   ├── Department.php   # 3FN: Department en All Users con duplicidad (nombre único)
│   ├── Persona.php   # directorio activo + propietario CRV; department_id FK; hasMany activos
│   ├── Regional.php, Bodega.php   # tablas auxiliares 3FN (codigo, nombre)
│   ├── ActivoCrv.php   # persona_id, regional_codigo FK, bodega_codigo FK
├── Http/
│   ├── Controllers/
│   │   ├── CpuController.php, MonitorController.php, TecladoController.php, MouseController.php
│   │   ├── PersonaController.php, ActivoCrvController.php   # Persona = propietario; una persona, muchos activos
│   │   ├── AllUsersImportController.php   # solo import All Users
│   │   ├── CrvReporteImportController.php # solo import CRV
│   │   └── ParqueExportController.php     # solo export reporte final
│   ├── Requests/
│   │   ├── StoreCpuRequest.php, UpdateCpuRequest.php, ...
│   │   ├── ImportAllUsersRequest.php, ImportCrvReporteRequest.php
├── Imports/
│   ├── AllUsersImport.php    # un origen, un formato
│   └── CrvReporteImport.php  # un origen, un formato
├── Exports/
│   └── ParqueExport.php     # un formato de salida
├── Services/                 # opcional
│   ├── AllUsersImportService.php, CrvReporteImportService.php, ParqueExportService.php
└── Support/                 # o Services
    └── StringNormalizer.php  # una responsabilidad: normalizar cadenas
```

---

## 5. Relación con el plan

Este documento complementa el plan de la app Laravel activos ICBF: la estructura de la aplicación en el plan debe reflejar **controladores separados** (AllUsersImportController, CrvReporteImportController, ParqueExportController) en lugar de un único ImportExportController, y el uso de Imports/Exports con responsabilidad única por formato. El diseño de la base de datos cumple **1FN, 2FN y 3FN** (plan §4.1); tablas auxiliares **Department** (duplicidad en All Users), **Regional** y **Bodega** para evitar redundancia.
