# Análisis de 20260310_crvReporte.xls e importación

Archivo: **[docs/20260310_crvReporte.xls](20260310_crvReporte.xls)**  
Formato: Excel 97-2003 (.xls). Una hoja: **Sheet1**.  
Contenido: **Activos por propietario (Persona)** — activos y quien tiene el activo.  
Filas totales: **2383**. Filas que son activos (con placa y producto): **~1424**.

---

## 1. Estructura del reporte

El libro es un reporte jerárquico con bloques repetidos:

1. **Título** (filas 8-9): "INSTITUTO COLOMBIANO DE BIENESTAR FAMILIAR", "ACTIVOS POR TERCERO".
2. **Encabezados** (fila 12): nombres de columnas de los activos (con celdas vacías entre medias).
3. **Bloques de datos**, que se repiten:
   - **Regional:** una fila con código regional y nombre (ej. `9500`, `REGIONAL GUAVIARE`).
   - **Bodega:** una fila con código de bodega y nombre (ej. `4692`, `95 - BODEGA DE BIENES...`).
   - **Propietario (Persona):** una fila con documento_identidad (col 2) y nombre (col 5) (ej. `18223666`, `ELIBERTO CAMACHO PINEDA` o `900545238`, `CORFUTURO`).
   - **Filas de activos:** varias filas con Placa, Producto, Descripción, etc.
   - **Subtotales:** filas "Subtotal Tercero:", "Subtotal Bodega:" (se pueden ignorar para la importación).

Al leer de arriba a abajo hay que **mantener en memoria** el último regional, bodega y propietario (Persona) y asignarlos a cada fila de activo.

---

## 2. Encabezados (fila 12) e índices de columna

Los encabezados están en la **fila 12**. Las celdas útiles son (índice de columna 0-based):

| Col | Nombre en Excel | Uso en fila de activo |
|-----|-----------------|------------------------|
| 1   | Placa           | Placa del activo (string) |
| 6   | Producto        | Código de producto (string) |
| 9   | Descripcion     | Descripción del bien |
| 12  | Adquisicion     | Fecha de adquisición (número de serie de Excel) |
| 15  | Serie           | Número de serie del equipo |
| 18  | Marca           | Marca |
| 22  | Modelo          | Modelo |
| 26  | Costo Historico | Valor en pesos (float) — en datos suele aparecer en col 25 |
| 30  | Depreciacion    | Valor depreciación (float) |

**Jerarquía (filas que no son activos):**

| Col 2      | Col 5        | Significado        |
|------------|--------------|--------------------|
| "9500"     | REGIONAL X    | Código y nombre regional |
| 4692.0     | 95 - BODEGA… | Código y nombre bodega |
| "18223666" | ELIBERTO…    | ID tercero y **nombre del propietario** |
| "900545238"| CORFUTURO    | NIT/ID y nombre (persona jurídica) |

**Fila de activo:** Col 1 tiene valor tipo placa (numérico o string numérico) y Col 6 tiene código de producto. Si ambos están rellenados, la fila es un activo y debe usarse el regional, bodega y tercero actuales como propietario/ubicación.

---

## 3. Objetos y atributos para importar

A partir de este archivo se pueden definir o enlazar:

### 3.1. Activo (reporte CRV)

Cada fila de activo del .xls se puede mapear a un registro con:

| Atributo          | Columna | Tipo      | Notas |
|-------------------|---------|-----------|--------|
| placa             | 1       | string    | Identificador placa |
| producto          | 6       | string    | Código producto |
| descripcion       | 9       | string    | Descripción |
| fecha_adquisicion | 12      | date      | Convertir número Excel con `xlrd.xldate.xldate_as_datetime(val, 0)` |
| serie             | 15      | string    | Número de serie |
| marca             | 18      | string    | Marca |
| modelo            | 22      | string    | Modelo |
| costo_historico   | 25/26   | decimal   | Valor en pesos |
| depreciacion      | 30      | decimal   | Depreciación |

### 3.2. Propietario (tercero)

Cada fila “tercero” del reporte aporta **quién es el propietario** del bloque de activos siguiente:

| Atributo   | Columna | Tipo   | Notas |
|------------|---------|--------|--------|
| documento_identidad   | 2       | string | **Llave natural.** Cédula o NIT (ej. 18223666, 900545238). |
| nombre                | 5       | string | Nombre completo o razón social (ELIBERTO CAMACHO PINEDA, CORFUTURO). |

**Propietario = Persona (una persona, muchos activos):** Es la misma entidad **Persona** (directorio activo + propietario). Una sola tabla `personas`, llave **documento_identidad**. Una persona tiene muchos activos asignados (hasMany ActivoCrv, hasMany CPU). Al importar CRV: crear/actualizar Persona por documento_identidad; ActivoCrv tiene **persona_id**.

### 3.3. Contexto regional y bodega

| Entidad   | Col 2   | Col 5   | Uso |
|-----------|---------|---------|-----|
| Regional  | código  | nombre  | regional_id / regional_nombre en el registro de activo |
| Bodega    | código  | nombre  | bodega_id / bodega_nombre en el registro de activo |

Pueden ser tablas auxiliares (regional, bodega) o solo campos en el registro de activo, según si se van a reutilizar en otros módulos.

---

## 4. Cómo importar el archivo

### 4.1. Tecnología

- **Laravel:** usar **PhpSpreadsheet** (por ejemplo vía **maatwebsite/excel**) para leer `.xls`.
- PhpSpreadsheet soporta **Excel 5 (BIFF)** y puede leer `.xls`; la hoja se lee por nombre o índice.
- Alternativa: comando o job que use **Python + xlrd** para generar CSV/JSON y que Laravel importe ese resultado (menos integrado).

Recomendación: **PhpSpreadsheet** en un `Import` de Laravel (ej. `CrvReporteImport`) para mantener todo en PHP y reutilizar normalización y transacciones.

### 4.2. Algoritmo de lectura (por filas)

1. Abrir el archivo y seleccionar la primera hoja (Sheet1).
2. Ignorar filas 0–11 (o hasta localizar la fila de encabezados).
3. Inicializar: `regional = null`, `bodega = null`, `persona_actual = null` (propietario = Persona).
4. Desde la fila 14 hasta la última:
   - Leer valores de las columnas 1, 2, 5, 6, 9, 12, 15, 18, 22, 25, 26, 30 (y las que se definan).
   - **Si Col 2 tiene valor y Col 5 tiene valor:**
     - Si Col 5 empieza por "REGIONAL" → actualizar `regional` (código = Col 2, nombre = Col 5).
     - Si Col 5 contiene "BODEGA" o patrón de bodega → actualizar `bodega` (código = Col 2, nombre = Col 5).
     - Si no → **propietario = Persona**: crear o actualizar Persona por documento_identidad (Col 2) y nombre (Col 5); asignar a persona_actual.
   - **Si Col 1 tiene valor (placa) y Col 6 tiene valor (producto):** tratar la fila como **activo**:
     - Convertir Col 12 a fecha (si es número de serie Excel).
     - Crear ActivoCrv con placa, producto, descripcion, fecha_adquisicion, serie, marca, modelo, costo_historico, depreciacion, **persona_id** = persona_actual, **regional_codigo** (FK; crear/actualizar fila en tabla regionales), **bodega_codigo** (FK; crear/actualizar fila en tabla bodegas). 3FN: no guardar nombres en activos_crv.
   - Filas "Subtotal Tercero:", "Subtotal Bodega:" se pueden saltar.
5. Aplicar **normalización** (trim, colapsar espacios, eliminar caracteres de control) a todos los textos **antes de validar y de persistir en la base de datos**; usar la misma utilidad que en All Users (StringNormalizer).
6. **Unicidad:** Persona por documento_identidad; ActivoCrv por placa o placa + producto + persona_id; en modo mezclar, actualizar por esa clave “mezclar”.

### 4.3. Integración con el resto del sistema

- **Propietario = Persona:** una sola entidad **Persona** (directorio activo + propietario CRV); **una persona tiene muchos activos** asignados. Tabla `personas` con documento_identidad único; alimentada por All Users y por CRV.
- **Activo CRV:** tabla `activos_crv` con **persona_id** (FK a personas), **regional_codigo** (FK a regionales), **bodega_codigo** (FK a bodegas). Cumplir 3FN: tablas **regionales**(codigo, nombre) y **bodegas**(codigo, nombre); no duplicar nombres en activos_crv. Opcionalmente relación con CPU/equipo del parque si se cruza por placa/serie.
- **Import en la app:** ruta `GET /activos-crv/import` (o `/crv/import`) con formulario de subida del `.xls`; `POST` que ejecute el import en transacción, con opción reemplazar/mezclar y reporte de filas importadas y errores.

### 4.4. Resumen de pasos para implementar

1. Añadir dependencia que permita leer `.xls` (PhpSpreadsheet / maatwebsite excel) si no está.
2. Crear migraciones respetando 1FN, 2FN, 3FN: **personas**; **regionales**(codigo PK, nombre); **bodegas**(codigo PK, nombre); `activos_crv` (placa, producto, descripcion, fecha_adquisicion, serie, marca, modelo, costo_historico, depreciacion, **persona_id** [FK], **regional_codigo** [FK a regionales], **bodega_codigo** [FK a bodegas] — sin columnas nombre regional/bodega).
3. Crear `CrvReporteImport` que implemente el algoritmo anterior: crear/actualizar **Persona** por documento_identidad; crear o reutilizar registros en **regionales** y **bodegas** por código; crear ActivoCrv con persona_id, regional_codigo (FK), bodega_codigo (FK).
4. Añadir rutas y controlador para subir el archivo y ejecutar el import; **aplicar normalización a todos los valores antes de persistir en BD** y política de unicidad (reemplazar/mezclar).

Con esto se tiene analizado el archivo **20260310_crvReporte.xls**, la información de **activos** y **propietario (Persona)**. Una persona tiene muchos activos asignados; propietario = misma entidad Persona (documento_identidad).
