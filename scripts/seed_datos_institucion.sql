-- ================================================
-- Script: Seed de Datos para Formulario Institucional
-- Base de Datos: melisalacolina
-- Fecha: 2025-12-29
-- ================================================

-- Variables
SET @org_id = 1;  -- Melisa La Colina
SET @state_active = 1;  -- ACTIVE

-- ================================================
-- 1. POSITION (Cargos Institucionales)
-- ================================================
INSERT INTO position (organization_id, state_id, name, code, snomed_code, description, created_at, updated_at) VALUES
(@org_id, @state_active, 'Médico General', 'MED_GEN', '309343006', 'Médico de atención general', NOW(), NOW()),
(@org_id, @state_active, 'Médico Especialista', 'MED_ESP', '309395003', 'Médico con especialización', NOW(), NOW()),
(@org_id, @state_active, 'Enfermero/a', 'ENF', '106292003', 'Profesional de enfermería', NOW(), NOW()),
(@org_id, @state_active, 'Técnico Paramédico', 'TEC_PARA', '223366009', 'Técnico en enfermería', NOW(), NOW()),
(@org_id, @state_active, 'Administrativo', 'ADMIN', '308050009', 'Personal administrativo', NOW(), NOW()),
(@org_id, @state_active, 'Recepcionista', 'RECEP', '159034007', 'Recepcionista de atención', NOW(), NOW()),
(@org_id, @state_active, 'Secretaria', 'SECRET', '159037000', 'Secretaria médica', NOW(), NOW()),
(@org_id, @state_active, 'Coordinador/a', 'COORD', '224608005', 'Coordinador de servicios', NOW(), NOW()),
(@org_id, @state_active, 'Psicólogo/a', 'PSI', '59944000', 'Profesional de psicología', NOW(), NOW()),
(@org_id, @state_active, 'Nutricionista', 'NUTRI', '159033001', 'Profesional de nutrición', NOW(), NOW()),
(@org_id, @state_active, 'Kinesiólogo/a', 'KINE', '36682004', 'Profesional de kinesiología', NOW(), NOW()),
(@org_id, @state_active, 'Matrón/Matrona', 'MATRON', '106293008', 'Profesional de obstetricia', NOW(), NOW()),
(@org_id, @state_active, 'Tecnólogo Médico', 'TEC_MED', '159035008', 'Tecnólogo médico', NOW(), NOW()),
(@org_id, @state_active, 'Químico Farmacéutico', 'QUIM_FARM', '46255001', 'Profesional farmacéutico', NOW(), NOW()),
(@org_id, @state_active, 'Asistente Social', 'ASIS_SOC', '106328005', 'Trabajador social', NOW(), NOW());

-- ================================================
-- 2. PROFESSIONAL_TYPE (Tipo Profesional)
-- ================================================
INSERT INTO professional_type (organization_id, state_id, name, code, description, created_at, updated_at) VALUES
(@org_id, @state_active, 'Interno', 'INTERNO', 'Profesional de planta de la organización', NOW(), NOW()),
(@org_id, @state_active, 'Externo', 'EXTERNO', 'Profesional externo o de prestación de servicios', NOW(), NOW()),
(@org_id, @state_active, 'Mixto', 'MIXTO', 'Profesional con relación mixta (planta y externo)', NOW(), NOW());

-- ================================================
-- 3. BRANCH (Sucursales)
-- ================================================
INSERT INTO branch (organization_id, state_id, name, code, address, phone, email, created_at, updated_at) VALUES
(@org_id, @state_active, 'Casa Matriz', 'MAT', 'Av. La Colina 1234, La Florida, Santiago', '+56 2 2345 6789', 'matriz@melisalacolina.cl', NOW(), NOW()),
(@org_id, @state_active, 'Sucursal Centro', 'CEN', 'Av. Libertador Bernardo O''Higgins 2345, Santiago Centro', '+56 2 2456 7890', 'centro@melisalacolina.cl', NOW(), NOW()),
(@org_id, @state_active, 'Sucursal Providencia', 'PRO', 'Av. Providencia 3456, Providencia', '+56 2 2567 8901', 'providencia@melisalacolina.cl', NOW(), NOW()),
(@org_id, @state_active, 'Sucursal Las Condes', 'LCO', 'Av. Apoquindo 4567, Las Condes', '+56 2 2678 9012', 'lascondes@melisalacolina.cl', NOW(), NOW()),
(@org_id, @state_active, 'Sucursal Maipú', 'MAI', 'Av. Pajaritos 5678, Maipú', '+56 2 2789 0123', 'maipu@melisalacolina.cl', NOW(), NOW());

-- ================================================
-- 4. DEPARTMENT (Unidades por Sucursal)
-- ================================================
-- Casa Matriz
INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Consultas Externas', 'CE', 'Atención ambulatoria de consultas médicas', NOW(), NOW()
FROM branch WHERE code = 'MAT';

INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Urgencias', 'URG', 'Servicio de atención de urgencias', NOW(), NOW()
FROM branch WHERE code = 'MAT';

INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Hospitalización', 'HOSP', 'Servicio de hospitalización', NOW(), NOW()
FROM branch WHERE code = 'MAT';

INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Pabellón', 'PAB', 'Pabellones quirúrgicos', NOW(), NOW()
FROM branch WHERE code = 'MAT';

INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Imagenología', 'IMG', 'Servicio de diagnóstico por imágenes', NOW(), NOW()
FROM branch WHERE code = 'MAT';

INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Laboratorio', 'LAB', 'Laboratorio clínico', NOW(), NOW()
FROM branch WHERE code = 'MAT';

-- Sucursal Centro
INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Consultas Externas', 'CE', 'Atención ambulatoria de consultas médicas', NOW(), NOW()
FROM branch WHERE code = 'CEN';

INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Procedimientos', 'PROC', 'Sala de procedimientos ambulatorios', NOW(), NOW()
FROM branch WHERE code = 'CEN';

INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Imagenología', 'IMG', 'Servicio de diagnóstico por imágenes', NOW(), NOW()
FROM branch WHERE code = 'CEN';

-- Sucursal Providencia
INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Consultas Externas', 'CE', 'Atención ambulatoria de consultas médicas', NOW(), NOW()
FROM branch WHERE code = 'PRO';

INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Medicina Preventiva', 'PREV', 'Medicina preventiva y check-ups', NOW(), NOW()
FROM branch WHERE code = 'PRO';

-- Sucursal Las Condes
INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Consultas Externas', 'CE', 'Atención ambulatoria de consultas médicas', NOW(), NOW()
FROM branch WHERE code = 'LCO';

INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Centro Médico Empresarial', 'CME', 'Atención corporativa y empresas', NOW(), NOW()
FROM branch WHERE code = 'LCO';

-- Sucursal Maipú
INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Consultas Externas', 'CE', 'Atención ambulatoria de consultas médicas', NOW(), NOW()
FROM branch WHERE code = 'MAI';

INSERT INTO department (branch_id, state_id, name, code, description, created_at, updated_at) 
SELECT id, @state_active, 'Atención Primaria', 'APS', 'Atención primaria de salud', NOW(), NOW()
FROM branch WHERE code = 'MAI';

-- ================================================
-- 5. MEDICAL_SERVICE (Servicios por Unidad)
-- ================================================
-- Consultas Externas - Casa Matriz
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Medicina Interna', 'MED_INT', 'Consultas de medicina interna', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Cardiología', 'CARDIO', 'Consultas de cardiología', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Traumatología', 'TRAUMA', 'Consultas de traumatología', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Pediatría', 'PEDIA', 'Consultas de pediatría', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Ginecología', 'GINE', 'Consultas de ginecología', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Neurología', 'NEURO', 'Consultas de neurología', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Dermatología', 'DERMA', 'Consultas de dermatología', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Psiquiatría', 'PSI', 'Consultas de psiquiatría', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'MAT';

-- Urgencias - Casa Matriz
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Box Médico', 'BOX_MED', 'Atención de urgencia médica', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'URG' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Reanimación', 'REANIM', 'Sala de reanimación', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'URG' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Observación', 'OBS', 'Sala de observación', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'URG' AND b.code = 'MAT';

-- Hospitalización - Casa Matriz
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Medicina', 'HOSP_MED', 'Hospitalización medicina', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'HOSP' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Cirugía', 'HOSP_CIR', 'Hospitalización cirugía', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'HOSP' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'UCI', 'UCI', 'Unidad de cuidados intensivos', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'HOSP' AND b.code = 'MAT';

-- Pabellón - Casa Matriz
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Pabellón 1', 'PAB1', 'Pabellón quirúrgico 1', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'PAB' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Pabellón 2', 'PAB2', 'Pabellón quirúrgico 2', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'PAB' AND b.code = 'MAT';

-- Imagenología - Casa Matriz
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Radiología', 'RX', 'Radiología simple', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'IMG' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Ecografía', 'ECO', 'Ecografía y ultrasonido', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'IMG' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Tomografía', 'TAC', 'Tomografía computarizada', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'IMG' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Resonancia Magnética', 'RM', 'Resonancia magnética', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'IMG' AND b.code = 'MAT';

-- Laboratorio - Casa Matriz
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Laboratorio General', 'LAB_GEN', 'Exámenes de laboratorio general', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'LAB' AND b.code = 'MAT';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Microbiología', 'MICRO', 'Exámenes microbiológicos', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'LAB' AND b.code = 'MAT';

-- Consultas Externas - Sucursal Centro
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Medicina General', 'MED_GEN', 'Consultas de medicina general', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'CEN';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Nutrición', 'NUTRI', 'Consultas de nutrición', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'CEN';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Kinesiología', 'KINE', 'Consultas de kinesiología', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'CEN';

-- Procedimientos - Sucursal Centro
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Curaciones', 'CUR', 'Curaciones y procedimientos menores', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'PROC' AND b.code = 'CEN';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Inyectables', 'INY', 'Administración de medicamentos inyectables', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'PROC' AND b.code = 'CEN';

-- Imagenología - Sucursal Centro
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Radiología', 'RX', 'Radiología simple', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'IMG' AND b.code = 'CEN';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Ecografía', 'ECO', 'Ecografía y ultrasonido', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'IMG' AND b.code = 'CEN';

-- Consultas Externas - Sucursal Providencia
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Medicina General', 'MED_GEN', 'Consultas de medicina general', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'PRO';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Oftalmología', 'OFTAL', 'Consultas de oftalmología', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'PRO';

-- Medicina Preventiva - Sucursal Providencia
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Check-up Ejecutivo', 'CHECKUP', 'Exámenes preventivos ejecutivos', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'PREV' AND b.code = 'PRO';

-- Consultas Externas - Sucursal Las Condes
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Medicina General', 'MED_GEN', 'Consultas de medicina general', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'LCO';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Medicina del Trabajo', 'MED_TRAB', 'Consultas de medicina del trabajo', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'LCO';

-- Centro Médico Empresarial - Sucursal Las Condes
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Exámenes Ocupacionales', 'EXAM_OCUP', 'Exámenes de salud ocupacional', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CME' AND b.code = 'LCO';

-- Consultas Externas - Sucursal Maipú
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Medicina Familiar', 'MED_FAM', 'Consultas de medicina familiar', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'MAI';

INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Pediatría', 'PEDIA', 'Consultas de pediatría', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'CE' AND b.code = 'MAI';

-- Atención Primaria - Sucursal Maipú
INSERT INTO medical_service (department_id, state_id, name, code, description, created_at, updated_at) 
SELECT d.id, @state_active, 'Programas de Salud', 'PROG_SALUD', 'Programas de salud preventiva', NOW(), NOW()
FROM department d 
JOIN branch b ON d.branch_id = b.id 
WHERE d.code = 'APS' AND b.code = 'MAI';

-- ================================================
-- RESUMEN
-- ================================================
-- Position: 15 cargos
-- ProfessionalType: 3 tipos
-- Branch: 5 sucursales
-- Department: 15 unidades
-- MedicalService: 43 servicios médicos
-- ================================================
