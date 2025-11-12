-- =====================================================
-- SCRIPT DE MIGRACIÓN: tenant → tenant_db
-- =====================================================
-- 
-- OBJETIVO: Adaptar la tabla 'tenant' de melisa_central para que sea
--           compatible con hakam/multi-tenancy-bundle
--
-- IMPORTANTE: Este script debe ejecutarse en melisa_central (sistema principal)
--
-- PASOS:
-- 1. Renombrar tabla: tenant → tenant_db
-- 2. Agregar columna: database_status
-- 3. Verificar estructura final
-- =====================================================

USE melisa_central;

-- =====================================================
-- PASO 1: RENOMBRAR TABLA
-- =====================================================
-- La tabla 'tenant' debe llamarse 'tenant_db' para que el bundle la reconozca

RENAME TABLE tenant TO tenant_db;

-- =====================================================
-- PASO 2: AGREGAR COLUMNA database_status
-- =====================================================
-- El bundle necesita esta columna para saber el estado de cada tenant
-- Valores posibles: 'migrated', 'pending', 'failed'

ALTER TABLE tenant_db 
ADD COLUMN database_status VARCHAR(50) NOT NULL DEFAULT 'migrated' 
AFTER database_name;

-- =====================================================
-- PASO 3: ACTUALIZAR REGISTROS EXISTENTES
-- =====================================================
-- Marcar todos los tenants existentes como 'migrated'

UPDATE tenant_db 
SET database_status = 'migrated' 
WHERE database_status IS NULL OR database_status = '';

-- =====================================================
-- VERIFICACIÓN FINAL
-- =====================================================
-- Ejecuta estos comandos para verificar que todo quedó bien:

-- Ver estructura de la tabla
DESC tenant_db;

-- Ver datos de ejemplo
SELECT id, subdomain, database_name, database_status 
FROM tenant_db 
LIMIT 5;

-- =====================================================
-- ESTRUCTURA FINAL ESPERADA
-- =====================================================
/*
+------------------+--------------+------+-----+-----------+----------------+
| Field            | Type         | Null | Key | Default   | Extra          |
+------------------+--------------+------+-----+-----------+----------------+
| id               | int          | NO   | PRI | NULL      | auto_increment |
| name             | varchar(255) | NO   |     | NULL      |                |
| subdomain        | varchar(255) | NO   |     | NULL      |                |
| domain           | varchar(255) | YES  |     | NULL      |                |
| database_name    | varchar(255) | NO   |     | NULL      |                |
| database_status  | varchar(50)  | NO   |     | migrated  |                | ← NUEVA
| rut_empresa      | varchar(255) | YES  |     | NULL      |                |
| host             | varchar(255) | YES  |     | NULL      |                |
| host_port        | int          | YES  |     | NULL      |                |
| db_user          | varchar(255) | YES  |     | NULL      |                |
| db_password      | varchar(255) | YES  |     | NULL      |                |
| driver           | varchar(255) | YES  |     | NULL      |                |
| version          | varchar(255) | YES  |     | NULL      |                |
| language         | varchar(255) | YES  |     | NULL      |                |
| is_active        | tinyint(1)   | NO   |     | NULL      |                |
| tenant_path      | varchar(500) | YES  |     | NULL      |                |
+------------------+--------------+------+-----+-----------+----------------+
*/

-- =====================================================
-- VALORES POSIBLES PARA database_status
-- =====================================================
/*
'migrated'  - La BD del tenant está creada y migrada (ESTADO NORMAL)
'pending'   - La BD del tenant está pendiente de crear/migrar
'failed'    - Hubo un error creando/migrando la BD del tenant
*/

-- =====================================================
-- ROLLBACK (Si algo sale mal)
-- =====================================================
-- Si necesitas revertir los cambios:
/*
USE melisa_central;
ALTER TABLE tenant_db DROP COLUMN database_status;
RENAME TABLE tenant_db TO tenant;
*/
