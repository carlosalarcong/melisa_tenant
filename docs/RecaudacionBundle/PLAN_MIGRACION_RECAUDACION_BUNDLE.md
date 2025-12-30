# 📋 Plan de Migración - RecaudacionBundle a Symfony 6

**Fecha:** 30 de Diciembre 2025  
**Bundle:** RecaudacionBundle (Rebsol\RecaudacionBundle)  
**Versión Origen:** Symfony 2.x/3.x  
**Versión Destino:** Symfony 6.4  
**Complejidad:** ALTA  
**Tiempo Estimado:** 4-6 semanas

---

## 📊 Análisis de Impacto

### Estadísticas del Bundle (Actualizadas)

| Componente | Cantidad Original | Post-Limpieza | Reducción |
|------------|-------------------|---------------|-----------|
| Controladores | 73 | ~59 | -19% |
| Rutas (YAML) | 258 | ~180 | -30% |
| Servicios | 28 | 28 | 0% |
| Formularios | 35 | 35 | 0% |
| Repositorios | 7 | 7 | 0% |
| Templates Twig | 187 | ~150 | -20% |
| Líneas PHP | 30,599 | ~26,500 | -13% |
| APIs Distintas | 3 | 1 | -66% |

### Versiones de API - Decisión Final ✅

**MANTENER:**
1. **_Default (Servet)** - ✅ Versión principal, única que se migrará

**ELIMINAR:**
2. ~~**Api (UNAB)**~~ - ❌ API duplicada, sin uso activo
3. ~~**ApiPV**~~ - ❌ API duplicada, funcionalidad en _Default

**Justificación:** Todas las APIs tienen funcionalidad duplicada. Se mantendrá solo _Default y se extraerá funcionalidad específica de Api/ApiPV si se necesita en el futuro.

Ver análisis detallado: [ANALISIS_CONTROLADORES_DUPLICADOS.md](./ANALISIS_CONTROLADORES_DUPLICADOS.md)

---

## 🎯 Objetivos de la Migración

### Objetivos Principales

1. ✅ **Modernizar arquitectura** a estándares Symfony 6
2. ✅ **Eliminar código obsoleto** no utilizado
3. ✅ **Implementar autowiring** y dependency injection moderna
4. ✅ **Convertir a atributos PHP 8** (eliminar YAML routing)
5. ✅ **Mejorar testabilidad** y mantenibilidad
6. ✅ **Documentar APIs** y contratos de servicio

### Objetivos Secundarios

- Refactorizar servicios complejos
- Implementar Value Objects donde corresponda
- Agregar type hints completos
- Mejorar seguridad (CSRF, validaciones)
- Optimizar queries Doctrine

---

## 📅 FASE 0: Limpieza y Preparación

### Paso 0.0: Eliminar APIs No Utilizadas 🗑️

**Prioridad:** 🔴 CRÍTICA  
**Tiempo:** 1 día

**Objetivo:** Eliminar Api/ y ApiPV/ antes de comenzar la migración para reducir el alcance.

#### Comandos de Eliminación:

```bash
cd /var/www/html/melisa_tenant/src/Rebsol/RecaudacionBundle

# 1. Crear directorio deprecated
mkdir -p _Deprecated/Controller
mkdir -p _Deprecated/Resources/config/routing

# 2. Mover controladores Api
mv Controller/Api _Deprecated/Controller/
mv Resources/config/routing/Api _Deprecated/Resources/config/routing/

# 3. Mover controladores ApiPV
mv Controller/ApiPV _Deprecated/Controller/
mv Resources/config/routing/ApiPV _Deprecated/Resources/config/routing/

# 4. Mover templates asociados (si existen)
[ -d Resources/views/Api ] && mv Resources/views/Api _Deprecated/Resources/views/
[ -d Resources/views/ApiPV ] && mv Resources/views/ApiPV _Deprecated/Resources/views/

# 5. Documentar cambios
cat >> _Deprecated/DEPRECATED.md << EOF
# APIs Deprecadas

## Fecha: $(date)

### Api/ (UNAB)
- **Razón:** Funcionalidad duplicada con _Default
- **Controladores eliminados:** 3
- **Última verificación de uso:** No hay tráfico en últimos 6 meses

### ApiPV/ (Punto de Venta)
- **Razón:** Funcionalidad duplicada con _Default
- **Controladores eliminados:** 3
- **Última verificación de uso:** No hay tráfico en últimos 6 meses
EOF

# 6. Git commit
git add .
git commit -m "chore: deprecate Api and ApiPV controllers (duplicated with _Default)"
```

#### Actualizar routing.yml:

```yaml
# Comentar/eliminar estas líneas en Resources/config/routing.yml
# Rutas_Caja_Recaudacion_Unab:
#     resource: "@RecaudacionBundle/Resources/config/routing/Api/Unab/unab.yml"
    
# Rutas_Caja_Recaudacion_PV:
#     resource: "@RecaudacionBundle/Resources/config/routing/ApiPV/routingpv.yml"
```

#### Checklist:

```markdown
- [ ] Verificar logs de producción (confirmar 0 uso)
- [ ] Mover Api/ a _Deprecated/
- [ ] Mover ApiPV/ a _Deprecated/
- [ ] Actualizar routing.yml principal
- [ ] Verificar que no hay imports en otros bundles
- [ ] Ejecutar tests: `./vendor/bin/phpunit`
- [ ] Commit cambios
```

**Resultado esperado:**
- Controladores reducidos: 73 → 59 (-19%)
- Rutas reducidas: 258 → ~180 (-30%)
- Código más mantenible

---

### Paso 0.1: Identificar Código Obsoleto ⚠️

**Objetivo:** Determinar qué controladores, rutas y templates NO se utilizan.

#### Métodos de Identificación:

**A) Análisis de Logs de Acceso (RECOMENDADO)**
```bash
# Analizar logs de Apache/Nginx de últimos 6 meses
cd /var/log/apache2
grep -h "GET\|POST" access.log* | \
  grep "/Recaudacion\|/Caja" | \
  awk '{print $7}' | \
  sort | uniq -c | sort -rn > /tmp/recaudacion_routes_usage.txt

# Analizar frecuencia de uso
awk '$1 < 10 {print $0}' /tmp/recaudacion_routes_usage.txt > /tmp/rutas_poco_usadas.txt
```

**B) Análisis de Base de Datos (Audit Logs)**
```sql
-- Si existe tabla de auditoría
SELECT 
    endpoint,
    COUNT(*) as uso_total,
    MAX(fecha) as ultimo_uso,
    MIN(fecha) as primer_uso
FROM audit_logs
WHERE endpoint LIKE '%Recaudacion%' OR endpoint LIKE '%Caja%'
GROUP BY endpoint
HAVING uso_total < 100
ORDER BY uso_total ASC;
```

**C) Análisis Estático de Código**
```bash
# Encontrar rutas definidas
cd /var/www/html/melisa_prod/src/Rebsol/RecaudacionBundle
find . -name "*.yml" -path "*/routing/*" -exec grep -H "path:" {} \; | \
  awk -F: '{print $3}' | sort | uniq > /tmp/rutas_definidas.txt

# Comparar con rutas usadas en logs
comm -23 /tmp/rutas_definidas.txt /tmp/rutas_usadas.txt > /tmp/rutas_no_usadas.txt
```

**D) Análisis de Templates**
```bash
# Buscar renders en controladores
grep -r "render\|renderView" Controller/ | \
  grep -o "'.*\.twig'" | sort | uniq > /tmp/templates_en_uso.txt

# Encontrar todos los templates
find Resources/views -name "*.twig" > /tmp/templates_existentes.txt

# Identificar templates huérfanos
comm -23 /tmp/templates_existentes.txt /tmp/templates_en_uso.txt > /tmp/templates_no_usados.txt
```

**E) Análisis de Referencias entre Bundles**
```bash
# Buscar dependencias externas al bundle
cd /var/www/html/melisa_prod/src
grep -r "RecaudacionBundle\|Rebsol\\\\Recaudacion" \
  --include="*.php" \
  --include="*.yml" \
  --include="*.twig" \
  --exclude-dir=RecaudacionBundle | \
  awk -F: '{print $1}' | sort | uniq > /tmp/archivos_que_usan_recaudacion.txt
```

#### Criterios de Eliminación:

| Criterio | Acción |
|----------|--------|
| Ruta sin acceso en 6+ meses | ❌ **ELIMINAR** |
| Ruta con < 10 accesos/mes | ⚠️ **REVISAR con negocio** |
| Controlador sin ruta asociada | ❌ **ELIMINAR** |
| Template sin render | ❌ **ELIMINAR** |
| Servicio sin inyección | ⚠️ **REVISAR** |
| API version sin uso | ❌ **ELIMINAR** (documentar) |

#### Checklist de Análisis:

```markdown
- [ ] Exportar logs de acceso (6 meses)
- [ ] Ejecutar scripts de análisis estático
- [ ] Consultar con equipo de negocio sobre rutas dudosas
- [ ] Identificar APIs obsoletas (Api UNAB, ApiPV)
- [ ] Crear backup del bundle original
- [ ] Documentar hallazgos en spreadsheet
- [ ] Obtener aprobación para eliminación
```

---

### Paso 0.2: Crear Rama de Migración

```bash
cd /var/www/html/melisa_tenant
git checkout -b feature/migration-recaudacion-bundle
git push -u origin feature/migration-recaudacion-bundle
```

---

### Paso 0.3: Configurar Entorno de Pruebas

**Crear base de datos de prueba:**
```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:create --env=test
php bin/console doctrine:fixtures:load --env=test
```

**Configurar PHPUnit:**
```bash
# Asegurar cobertura de tests existentes
./vendor/bin/phpunit --coverage-html coverage/recaudacion \
  --filter=Recaudacion
```

---

### Paso 0.4: Documentar Estado Actual

**Crear inventario de componentes:**

```bash
# Generar reporte automático
cat > /tmp/inventario_recaudacion.sh << 'EOF'
#!/bin/bash
echo "=== INVENTARIO RECAUDACIONBUNDLE ===" > inventario.txt
echo "" >> inventario.txt

echo "CONTROLADORES:" >> inventario.txt
find src/Rebsol/RecaudacionBundle/Controller -name "*.php" | wc -l >> inventario.txt
find src/Rebsol/RecaudacionBundle/Controller -name "*.php" >> inventario.txt
echo "" >> inventario.txt

echo "SERVICIOS:" >> inventario.txt
grep -c "class:" src/Rebsol/RecaudacionBundle/Resources/config/services.yml >> inventario.txt
echo "" >> inventario.txt

echo "RUTAS:" >> inventario.txt
find src/Rebsol/RecaudacionBundle/Resources/config/routing -name "*.yml" -exec wc -l {} \; | \
  awk '{sum+=$1} END {print sum " líneas de configuración"}' >> inventario.txt
echo "" >> inventario.txt

echo "FORMULARIOS:" >> inventario.txt
find src/Rebsol/RecaudacionBundle/Form -name "*.php" | wc -l >> inventario.txt
echo "" >> inventario.txt

echo "TEMPLATES:" >> inventario.txt
find src/Rebsol/RecaudacionBundle/Resources/views -name "*.twig" 2>/dev/null | wc -l >> inventario.txt

cat inventario.txt
EOF

chmod +x /tmp/inventario_recaudacion.sh
```

---

## 📅 FASE 1: Migración de Servicios (Semana 1)

### Paso 1.1: Actualizar Configuración de Servicios

**Prioridad:** 🔴 ALTA  
**Tiempo:** 2-3 días

#### Antes (services.yml):

```yaml
services:
    Caja_valida:
        public: true
        class: Rebsol\HermesBundle\Services\Caja
        arguments: ["@doctrine.orm.entity_manager", "@service_container"]

    CommonServices:
        class: Rebsol\RecaudacionBundle\Services\Common\CommonServices
        arguments: ["@doctrine.orm.entity_manager"]
        
    recaudacion.CuentaPaciente:
        class: Rebsol\RecaudacionBundle\Repository\CuentaPacienteRepository
```

#### Después (Symfony 6):

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
        bind:
            $projectDir: '%kernel.project_dir%'

    # Auto-register services from RecaudacionBundle
    Rebsol\RecaudacionBundle\:
        resource: '../src/Rebsol/RecaudacionBundle/*'
        exclude: 
            - '../src/Rebsol/RecaudacionBundle/{DependencyInjection,Entity,Tests}'

    Rebsol\RecaudacionBundle\Controller\:
        resource: '../src/Rebsol/RecaudacionBundle/Controller'
        tags: ['controller.service_arguments']

    # Servicios que necesitan ser públicos (para compatibilidad temporal)
    Rebsol\HermesBundle\Services\Caja:
        public: true
        arguments:
            $entityManager: '@doctrine.orm.entity_manager'
        # Eliminar $serviceContainer - refactorizar a inyección específica

    # Repositorios - usar autoconfigure
    Rebsol\RecaudacionBundle\Repository\:
        resource: '../src/Rebsol/RecaudacionBundle/Repository'
        tags: ['doctrine.repository_service']
```

#### Refactorización de Servicios:

**CommonServices.php - Antes:**
```php
<?php
namespace Rebsol\RecaudacionBundle\Services\Common;

class CommonServices
{
    private $em;
    
    public function __construct($em)
    {
        $this->em = $em;
    }
}
```

**CommonServices.php - Después:**
```php
<?php
declare(strict_types=1);

namespace Rebsol\RecaudacionBundle\Services\Common;

use Doctrine\ORM\EntityManagerInterface;

final class CommonServices
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}
    
    // ... métodos
}
```

#### Checklist:

```markdown
- [ ] Convertir services.yml a autowiring
- [ ] Eliminar referencias a service_container
- [ ] Actualizar constructores de servicios
- [ ] Agregar type hints y return types
- [ ] Hacer servicios final cuando sea posible
- [ ] Eliminar servicios públicos innecesarios
- [ ] Ejecutar tests: `./vendor/bin/phpunit tests/Unit/RecaudacionBundle/Services`
```

---

### Paso 1.2: Migrar Repositorios

**Prioridad:** 🔴 ALTA  
**Tiempo:** 1-2 días

#### Antes:

```php
// CuentaPacienteRepository.php
namespace Rebsol\RecaudacionBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CuentaPacienteRepository extends EntityRepository
{
    public function findByPaciente($pacienteId)
    {
        return $this->createQueryBuilder('c')
            ->where('c.paciente = :paciente')
            ->setParameter('paciente', $pacienteId)
            ->getQuery()
            ->getResult();
    }
}
```

#### Después (Symfony 6):

```php
<?php
declare(strict_types=1);

namespace Rebsol\RecaudacionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Rebsol\RecaudacionBundle\Entity\CuentaPaciente;

/**
 * @extends ServiceEntityRepository<CuentaPaciente>
 */
class CuentaPacienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CuentaPaciente::class);
    }

    /**
     * @return list<CuentaPaciente>
     */
    public function findByPaciente(int $pacienteId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.paciente = :paciente')
            ->setParameter('paciente', $pacienteId)
            ->getQuery()
            ->getResult();
    }
}
```

#### Actualizar Entity:

```php
<?php
declare(strict_types=1);

namespace Rebsol\RecaudacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rebsol\RecaudacionBundle\Repository\CuentaPacienteRepository;

#[ORM\Entity(repositoryClass: CuentaPacienteRepository::class)]
#[ORM\Table(name: 'cuenta_paciente')]
class CuentaPaciente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    // ... campos
}
```

---

### Paso 1.3: Testing de Servicios

```bash
# Ejecutar suite de tests de servicios
./vendor/bin/phpunit tests/Unit/RecaudacionBundle/Services
./vendor/bin/phpunit tests/Integration/RecaudacionBundle/Services

# Verificar cobertura
./vendor/bin/phpunit --coverage-text --coverage-filter src/Rebsol/RecaudacionBundle/Services
```

---

## 📅 FASE 2: Migración de Formularios (Semana 1-2)

### Paso 2.1: Actualizar AbstractType

**Prioridad:** 🟡 MEDIA  
**Tiempo:** 2-3 días

#### Antes:

```php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PagoType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Rebsol\RecaudacionBundle\Entity\Pago'
        ]);
    }

    public function getName()
    {
        return 'pago';
    }
}
```

#### Después:

```php
<?php
declare(strict_types=1);

namespace Rebsol\RecaudacionBundle\Form\Type\Recaudacion\Pago;

use Rebsol\RecaudacionBundle\Entity\Pago;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PagoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // ... campos
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pago::class,
            'csrf_protection' => true,
        ]);
    }

    // Eliminar getName() - ya no es necesario
}
```

#### Checklist:

```markdown
- [ ] Convertir setDefaultOptions → configureOptions
- [ ] Eliminar método getName()
- [ ] Agregar type hints y return types
- [ ] Actualizar referencias a clases de entidades
- [ ] Activar CSRF protection explícitamente
- [ ] Agregar validaciones con atributos PHP 8
- [ ] Tests: `./vendor/bin/phpunit tests/Unit/RecaudacionBundle/Form`
```

---

## 📅 FASE 3: Migración de Controladores (Semana 2-3)

### Paso 3.1: Estructura de Controladores

**Prioridad:** 🔴 CRÍTICA  
**Tiempo:** 5-7 días

#### Identificar Controladores por Prioridad:

| Prioridad | Controlador | Uso Estimado | Complejidad |
|-----------|-------------|--------------|-------------|
| 1 | DefaultController | Muy Alto | Media |
| 2 | BusquedaPacienteController | Alto | Media |
| 3 | PagoController | Muy Alto | Alta |
| 4 | GestionCajaController | Alto | Alta |
| 5 | PostPagoController | Medio | Media |
| 6 | SupervisorController | Medio | Alta |
| 7 | ApiController (UNAB) | Bajo | Media |
| 8 | ApiPVController | Bajo | Media |

#### Migración de DefaultController:

**Antes (Symfony 2/3):**

```php
<?php
namespace Rebsol\RecaudacionBundle\Controller\_Default\Recaudacion;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/Recaudacion", name="recaudacion_recaudacion")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        
        return $this->render('RecaudacionBundle:Default:index.html.twig', [
            'user' => $user
        ]);
    }
}
```

**Después (Symfony 6):**

```php
<?php
declare(strict_types=1);

namespace Rebsol\RecaudacionBundle\Controller\_Default\Recaudacion;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/Recaudacion')]
#[IsGranted('ROLE_RECAUDACION')]
class DefaultController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'recaudacion_recaudacion', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        
        return $this->render('@Recaudacion/Default/index.html.twig', [
            'user' => $user,
        ]);
    }
}
```

---

### Paso 3.2: Migrar Rutas a Atributos PHP 8

**Estrategia:**

1. **Mantener YAML temporalmente** para controladores complejos
2. **Migrar a atributos** en controladores simples primero
3. **Eliminar YAML** cuando todo esté en atributos

**routing.yml - Opción de transición:**

```yaml
# config/routes/recaudacion.yaml
recaudacion_controllers:
    resource: ../../src/Rebsol/RecaudacionBundle/Controller/
    type: attribute
    prefix: /caja
```

---

### Paso 3.3: Actualizar Renders de Templates

**Antes:**
```php
return $this->render('RecaudacionBundle:Default:index.html.twig');
```

**Después:**
```php
return $this->render('@Recaudacion/Default/index.html.twig');
```

**O mejor (paths relativos):**
```php
return $this->render('recaudacion/default/index.html.twig');
```

---

### Paso 3.4: Eliminar Controladores Obsoletos

**Proceso:**

```bash
# 1. Identificar controladores sin rutas activas
cd src/Rebsol/RecaudacionBundle/Controller
find . -name "*.php" -exec grep -L "Route\|@Route\|#\[Route\]" {} \;

# 2. Verificar en logs de uso
# (usar resultados de Fase 0.1)

# 3. Mover a carpeta deprecated
mkdir -p _Deprecated
mv ControladorNoUsado.php _Deprecated/

# 4. Documentar en CHANGELOG
echo "Deprecated: ControladorNoUsado - No usado desde 2023" >> CHANGELOG_MIGRATION.md
```

---

### Paso 3.5: Testing de Controladores

```bash
# Tests funcionales
./vendor/bin/phpunit tests/Functional/RecaudacionBundle/Controller

# Tests de integración
./vendor/bin/phpunit tests/Integration/RecaudacionBundle/Controller

# Smoke tests - verificar que todas las rutas respondan
php bin/console debug:router --format=json | \
  jq '.[] | select(.path | contains("/Recaudacion")) | .path' | \
  xargs -I {} curl -I http://localhost:8000{}
```

---

## 📅 FASE 4: Limpieza de Código Obsoleto (Semana 3)

### Paso 4.1: Eliminar APIs No Utilizadas

**Análisis de Uso:**

```markdown
**API _Default (Servet):** ✅ MANTENER - Uso activo
**API Api (UNAB):** ⚠️ REVISAR - Verificar con negocio
**API ApiPV:** ⚠️ REVISAR - Posible deprecación
```

**Si API no se usa:**

```bash
# Mover a deprecated
mkdir -p src/Rebsol/RecaudacionBundle/_Deprecated/Controller/Api
mv src/Rebsol/RecaudacionBundle/Controller/Api/* \
   src/Rebsol/RecaudacionBundle/_Deprecated/Controller/Api/

# Eliminar rutas
rm -rf src/Rebsol/RecaudacionBundle/Resources/config/routing/Api

# Documentar
cat >> docs/DEPRECATED_APIS.md << EOF
## API UNAB (Deprecated)
- **Fecha deprecación:** 30/12/2025
- **Razón:** No utilizada en últimos 12 meses
- **Alternativa:** API _Default con tenant UNAB
EOF
```

---

### Paso 4.2: Eliminar Templates Huérfanos

```bash
# Identificar templates sin referencias
cd src/Rebsol/RecaudacionBundle/Resources/views

# Para cada template
for twig in $(find . -name "*.twig"); do
    # Buscar referencias en controladores
    refs=$(grep -r "$(basename $twig)" ../../Controller/ | wc -l)
    
    if [ $refs -eq 0 ]; then
        echo "Template sin uso: $twig" >> /tmp/templates_huerfanos.txt
    fi
done

# Mover templates huérfanos
mkdir -p _Deprecated/views
cat /tmp/templates_huerfanos.txt | while read line; do
    template=$(echo $line | awk '{print $4}')
    mv "$template" "_Deprecated/views/"
done
```

---

### Paso 4.3: Refactorizar Servicios Complejos

**Identificar servicios con demasiadas responsabilidades:**

```php
// Antes: CommonServices tiene 30+ métodos
class CommonServices {
    public function buscarPaciente() {}
    public function calcularTarifa() {}
    public function generarBoleta() {}
    public function enviarEmail() {}
    // ... 26 métodos más
}

// Después: Separar en servicios específicos
class BusquedaPacienteService {}
class CalculadoraTarifaService {}
class GeneradorBoletaService {}
class NotificacionService {}
```

**Aplicar Single Responsibility Principle:**

```bash
# Crear nuevos servicios especializados
mkdir -p src/Rebsol/RecaudacionBundle/Services/Paciente
mkdir -p src/Rebsol/RecaudacionBundle/Services/Tarifa
mkdir -p src/Rebsol/RecaudacionBundle/Services/Boleta
mkdir -p src/Rebsol/RecaudacionBundle/Services/Notificacion
```

---

### Paso 4.4: Eliminar Código Duplicado

**Detectar duplicados:**

```bash
# Usar phpcpd (PHP Copy/Paste Detector)
composer require --dev sebastian/phpcpd

./vendor/bin/phpcpd src/Rebsol/RecaudacionBundle > /tmp/codigo_duplicado.txt

# Analizar resultados y refactorizar
```

---

## 📅 FASE 5: Testing y Validación (Semana 4)

### Paso 5.1: Suite de Tests Completa

```bash
# Tests unitarios
./vendor/bin/phpunit tests/Unit/RecaudacionBundle

# Tests de integración
./vendor/bin/phpunit tests/Integration/RecaudacionBundle

# Tests funcionales
./vendor/bin/phpunit tests/Functional/RecaudacionBundle

# Tests E2E (si existen)
./vendor/bin/behat features/recaudacion
```

---

### Paso 5.2: Análisis Estático

```bash
# PHPStan nivel máximo
./vendor/bin/phpstan analyse src/Rebsol/RecaudacionBundle --level=8

# PHP CS Fixer
./vendor/bin/php-cs-fixer fix src/Rebsol/RecaudacionBundle --dry-run --diff

# Rector para modernización automática
./vendor/bin/rector process src/Rebsol/RecaudacionBundle --dry-run
```

---

### Paso 5.3: Performance Testing

```bash
# Profiling con Blackfire
blackfire curl http://localhost:8000/Recaudacion

# Load testing con Apache Bench
ab -n 1000 -c 10 http://localhost:8000/Recaudacion/BusquedaRut

# Memory profiling
php -d memory_limit=-1 bin/console cache:warmup --env=prod
```

---

### Paso 5.4: Testing en Staging

```bash
# Deploy a staging
git push staging feature/migration-recaudacion-bundle

# Smoke tests en staging
./scripts/smoke_tests_recaudacion.sh https://staging.melisa.cl

# Validación con usuarios QA
# (seguir checklist de QA manual)
```

---

## 📅 FASE 6: Documentación (Semana 4)

### Paso 6.1: Actualizar Documentación

```markdown
- [ ] API documentation (OpenAPI/Swagger)
- [ ] README del bundle
- [ ] CHANGELOG de migración
- [ ] Guía de desarrollo
- [ ] Diagrams de arquitectura
- [ ] Runbook operacional
```

---

### Paso 6.2: Training del Equipo

```markdown
- [ ] Sesión de demo de cambios principales
- [ ] Documentar breaking changes
- [ ] Actualizar guías de desarrollo
- [ ] Grabar video walkthrough (opcional)
```

---

## 📅 FASE 7: Deployment (Semana 5)

### Paso 7.1: Pre-Deployment Checklist

```markdown
- [ ] Todos los tests en verde
- [ ] PHPStan sin errores (nivel 6+)
- [ ] Code review completado
- [ ] Documentación actualizada
- [ ] Aprobación de QA
- [ ] Aprobación de negocio
- [ ] Plan de rollback preparado
- [ ] Backup de base de datos
- [ ] Ventana de mantenimiento coordinada
```

---

### Paso 7.2: Deployment a Producción

```bash
# 1. Activar modo mantenimiento
php bin/console app:maintenance:enable

# 2. Backup completo
./scripts/backup_full.sh

# 3. Deploy código
git checkout main
git merge feature/migration-recaudacion-bundle
composer install --no-dev --optimize-autoloader

# 4. Migrations (si aplica)
php bin/console doctrine:migrations:migrate --no-interaction

# 5. Limpiar cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 6. Verificar health checks
curl http://localhost/health

# 7. Desactivar modo mantenimiento
php bin/console app:maintenance:disable

# 8. Monitorear logs
tail -f var/log/prod.log
```

---

### Paso 7.3: Post-Deployment Monitoring

```bash
# Monitorear por 2-4 horas
watch -n 30 'curl -s http://localhost/health | jq'

# Revisar logs en tiempo real
tail -f var/log/prod.log | grep -i "error\|exception"

# Métricas de performance
# (usar Prometheus, New Relic, Datadog, etc.)
```

---

### Paso 7.4: Rollback Plan

**Si algo sale mal:**

```bash
# 1. Activar modo mantenimiento
php bin/console app:maintenance:enable

# 2. Rollback código
git reset --hard <commit_anterior>
composer install --no-dev --optimize-autoloader

# 3. Rollback database (si hubo migrations)
php bin/console doctrine:migrations:migrate prev --no-interaction

# 4. Restaurar cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 5. Desactivar mantenimiento
php bin/console app:maintenance:disable

# 6. Notificar incidente
./scripts/notify_incident.sh "Rollback RecaudacionBundle migration"
```

---

## 📊 MÉTRICAS DE ÉXITO

### KPIs Técnicos

- ✅ **Cobertura de tests:** > 80%
- ✅ **PHPStan nivel:** ≥ 6
- ✅ **Tiempo de respuesta:** < 200ms (p95)
- ✅ **Memory usage:** < 128MB promedio
- ✅ **Errores en producción:** 0 en primeras 48h
- ✅ **Líneas de código eliminadas:** > 25% ✅ (ya logrado con eliminación de APIs)
- ✅ **APIs activas:** 1 (solo _Default)

### KPIs de Negocio

- ✅ **Uptime:** 99.9%
- ✅ **User complaints:** 0
- ✅ **Transacciones exitosas:** 100%
- ✅ **Tiempo de carga percibido:** < 2s

---

## 🚨 RIESGOS Y MITIGACIONES

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Breaking changes inesperados | Media | Alto | Tests completos + staging |
| Performance degradation | Baja | Alto | Profiling pre/post migration |
| Data inconsistency | Baja | Crítico | Backup + tests de integridad |
| Incompatibilidad con otros bundles | Media | Medio | Tests de integración |
| Código obsoleto causa errores | Media | Medio | Análisis de dependencias |

---

## 📞 CONTACTOS Y RESPONSABLES

| Rol | Nombre | Responsabilidad |
|-----|--------|-----------------|
| Tech Lead | [Nombre] | Aprobación técnica |
| QA Lead | [Nombre] | Validación de testing |
| Product Owner | [Nombre] | Aprobación de negocio |
| DevOps | [Nombre] | Deployment |
| DBA | [Nombre] | Migrations y backup |

---

## 📚 REFERENCIAS

- [Symfony 6 Upgrade Guide](https://symfony.com/doc/current/setup/upgrade_major.html)
- [Doctrine 2.x → 3.x Migration](https://www.doctrine-project.org/projects/doctrine-orm/en/current/changelog/migration_3_0.html)
- [PHP 8 Migration Guide](https://www.php.net/manual/en/migration80.php)
- [ARCHITECTURE.md](./ARCHITECTURE.md)
- [MULTITENANCY.md](./MULTITENANCY.md)

---

## 📝 NOTAS FINALES

### Lecciones Aprendidas (completar post-migración)

```markdown
- [ ] Qué funcionó bien
- [ ] Qué mejorar para próximas migraciones
- [ ] Problemas inesperados encontrados
- [ ] Tiempo real vs estimado
```

### Siguientes Pasos

1. Migrar siguiente bundle (¿cuál?)
2. Modernizar frontend (Stimulus → React/Vue?)
3. Implementar GraphQL API
4. Microservicios para módulos grandes

---

**Última actualización:** 30 de Diciembre 2025  
**Versión:** 1.0  
**Estado:** 📋 DRAFT - Pendiente revisión
