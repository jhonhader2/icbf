# Objetos y atributos — Parque computacional ICBF

Fuente: columnas de [Toma_Parque_2026.xlsx](Toma_Parque_2026.xlsx) (Data Equipos fila 2, primera hoja fila 1) y [All Users.xlsx](All%20Users.xlsx) (hoja All Users fila 7). Nombres en **snake_case** para BD y modelo.

**Normalización relacional (1FN, 2FN, 3FN):** Las tablas se diseñan cumpliendo primera, segunda y tercera forma normal: valores atómicos y PK definida (1FN); atributos que dependen de la clave completa (2FN); sin dependencias transitivas — p. ej. tablas **regionales**, **bodegas**, **departments** con código/nombre, y en tablas hijas solo FKs; no duplicar nombres (3FN). **Department** en All Users.xlsx tiene **duplicidad** (varios usuarios comparten el mismo departamento); en BD usar tabla **departments** y en `personas` solo **department_id** (FK).

---

## 1. CPU (equipo principal)

| Atributo | Origen (columna) | Tipo sugerido |
|----------|------------------|---------------|
| nombre_maquina | D — NOMBRE DE MAQUINA | string |
| serial | V — Serial | string |
| placa | W — PLACA CPU / PORTATIL | string |
| estado | X — ESTADO CPU/PORTATIL | string |
| tipo_equipo | J — TIPO EQUIPO | string |
| referencia_equipo | K — REFERENCIA EQUIPO | string |
| memoria_ram | M — MEMORIA RAM | string |
| so | N — SO | string |
| procesador | O — PROCESADOR | string |
| tipo_so | P — TIPO SO | string |
| bits | Q — BITS | string |
| n_discos_fisicos | R — NºDISCOS FISICOS | string/integer |
| capacidad_disco | S — CAPACIDAD DISCO DURO | string |
| direccion_ip | T — DIRECCION IP | string |
| mac_address | U — MAC ADDRESS | string |
| office_version | AO — OFFICE VERSION | string |
| tarjeta_red_inalambrica | AP — EQUIPO CUENTA CON TARJETA DE RED INALÁMBRICA | string/boolean |
| fecha_adquisicion | L — FECHA ADQUISICIÓN DE EQUIPO | date |
| año_adquisicion | Z — AÑO DE ADQUISICIÓN | string/integer |
| en_garantia | Y — ELEMENTO SE ENCUENTRA EN GARANTÍA | string/boolean |
| fecha_inventario | AQ — FECHA INVENTARIO | date |
| observaciones | AR — OBSERVACIONES | text |
| regional_id / regional | B — REGIONAL | string (o FK) |
| dependencia_id / dependencias | C — DEPENDENCIAS | string (o FK) |
| nombre_ingeniero_diligencio | A — NOMBRE INGENIERO REGIONAL QUE DILIGENCIO | string |
| usuario_red_id / usuario_red | E — USUARIO DE RED (vínculo a Persona) | string (o FK) |

**Clave natural sugerida:** `serial` + `nombre_maquina` + `regional` (o equivalente).

**Relaciones:** 1 CPU → 1 Monitor (opcional), 1 CPU → 1 Teclado (opcional), 1 CPU → 1 Mouse (opcional); N CPU → 1 Persona (una persona tiene muchos equipos asignados).

---

## 2. Monitor

| Atributo | Origen (columna) | Tipo sugerido |
|----------|------------------|---------------|
| marca | AA — MARCA MONITOR | string |
| modelo | AB — MODELO MONITOR | string |
| serial | AC — SERIAL MONITOR | string |
| placa | AD — PLACA MONITOR | string |
| estado | AE — ESTADO MONITOR | string |
| cpu_id | (relación) | FK → cpus.id |

**Clave natural sugerida:** `serial` (o serial + placa).

---

## 3. Teclado

| Atributo | Origen (columna) | Tipo sugerido |
|----------|------------------|---------------|
| marca | AH — MARCA TECLADO | string |
| modelo | AJ — MODELO TECLADO | string |
| serial | AI — SERIAL TECLADO | string |
| placa | AK — PLACA TECLADO | string |
| estado | AL — ESTADO TECLADO | string |
| cpu_id | (relación) | FK → cpus.id |

**Clave natural sugerida:** `serial` (o serial + placa).

---

## 4. Mouse

| Atributo | Origen | Tipo sugerido |
|----------|--------|---------------|
| marca | (por definir si existe columna/hoja) | string |
| modelo | (por definir) | string |
| serial | (por definir) | string |
| estado | (por definir) | string |
| cpu_id | (relación) | FK → cpus.id |

**Nota:** Dejar modelo y migración preparados para cuando exista columna o hoja en el reporte.

---

## 5. Persona (una persona, muchos activos asignados)

**Una sola entidad Persona:** Es la misma que el propietario del CRV. Se alimenta de **All Users.xlsx** (directorio activo) y del **CRV** (col 2 + nombre). **Llave única:** **documento_identidad**. Origen del valor: en import **All Users** = **Employee ID** (col B); en import **CRV** = cédula/NIT (col 2). **Relación:** una persona tiene muchos activos asignados (hasMany CPUs, ActivoCrv).

Datos desde **All Users.xlsx**, hoja **All Users**, fila 7 = encabezados. **Se importan todas las personas del directorio activo** (sin excluir por estado). Cruce con parque por USUARIO DE RED / Logon Name.

### 5.1 Desde All Users.xlsx (columnas A→AF, fila 7)

**Importación:** incluir **todas** las filas de la hoja (todas las personas del directorio). El campo **Account Status** (col D) indica el estado de la cuenta del usuario y es **importante**: debe persistirse en Persona y tenerse en cuenta en listados y filtros. **Department** (col F) tiene **duplicidad** en el Excel — al importar, crear o obtener el registro en la tabla **departments** por nombre y asignar **department_id** en Persona (3FN). **Normalización:** antes de guardar en la base de datos, aplicar normalización a todos los textos (trim, colapsar espacios, eliminar caracteres de control); usar una utilidad reutilizable (ej. StringNormalizer) en All Users y CRV.

| Atributo | Origen (columna) | Tipo sugerido |
|----------|------------------|---------------|
| s_no | A — S.No. | integer |
| employee_id | B — Employee ID | string |
| account_expiry_time | C — Account Expiry Time | datetime |
| account_status | D — Account Status | string (importante: estado de la cuenta) |
| full_name | E — Full Name | string |
| department_id | F — Department | FK → departments.id (3FN: Department en Excel se repite; tabla departments(nombre único) y personas.department_id) |
| display_name | G — Display Name | string |
| email_address | H — Email Address | string |
| office | I — Office | string |
| title | J — Title | string |
| city | K — City | string |
| common_name | L — Common Name | string |
| company | M — Company | string |
| country | N — Country | string |
| days_since_password_last_set | O — Days Since Password Last Set | integer |
| password_expires_in | P — Password Expires In | string |
| description | Q — Description | text |
| logon_name | R — Logon Name | string |
| logon_to | S — Logon To | string |
| manager | T — Manager | string |
| ou_name | U — OU Name | string |
| password_expiry_date | V — Password Expiry Date | date |
| password_last_set | W — Password Last Set | datetime |
| password_status | X — Password Status | string |
| state_province | Y — State/Province | string |
| when_changed | Z — When Changed | datetime |
| when_created | AA — When Created | datetime |
| employe_type | AB — EmployeType | string |
| pwd_never_expires_flag | AC — Pwd Never Expires Flag | string/boolean |
| employee_number | AD — Employee Number | string |
| sam_account_name | AE — SAM Account Name | string |
| distinguished_name | AF — Distinguished Name | string |

### 5.2 Cruce con parque (Data Equipos)

| Atributo | Origen (columna Data Equipos) | Tipo sugerido |
|----------|-------------------------------|---------------|
| usuario_red | E — USUARIO DE RED | string (vínculo con logon_name) |
| nombre_completo | F — NOMBRE COMPLETO DEL USUARIO | string |
| cedula | G — CEDULA | string |
| tipo_vinculacion_usuario | H — TIPO DE VINCULACIÓN USUARIO | string |
| cargo | I — CARGO | string |

**Clave natural:** `documento_identidad` (único). Desde All Users se toma **Employee ID** (col B) como documento_identidad; desde CRV, la col 2 (cédula/NIT). Cruce parque/CRV por este mismo campo.

**Relación (una persona, muchos activos):** 1 Persona → N CPU (equipos parque asignados), 1 Persona → N ActivoCrv (activos por tercero en CRV). La persona es el propietario o responsable; se alimenta de All Users y del CRV (mismo documento_identidad).

---

## Resumen de relaciones

- **Persona:** una sola entidad (directorio activo + propietario CRV); **documento_identidad** como llave; **department_id** (FK a departments; 3FN: Department en All Users tiene duplicidad). **Tiene muchos activos** (hasMany): CPUs, Activos CRV.
- **Departments:** tabla auxiliar (id, nombre único) para eliminar duplicidad de Department del Excel; 3FN.
- **CPU** tiene (opcional): 1 Monitor, 1 Teclado, 1 Mouse; pertenece a 1 Persona (usuario asignado).
- **ActivoCrv** pertenece a 1 Persona (propietario); **regional_codigo** (FK a regionales), **bodega_codigo** (FK a bodegas) — 3FN: no almacenar nombre regional/bodega en activos_crv.
- **Regionales, Bodegas:** tablas auxiliares (codigo PK, nombre) para cumplir 3FN.
- **Monitor, Teclado, Mouse** pertenecen a 1 CPU.
