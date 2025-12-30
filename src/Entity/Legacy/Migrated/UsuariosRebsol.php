<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * UsuariosRebsol
 *
 * @ORM\Table(name="usuarios_rebsol")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\UsuariosRebsolRepository")
 */
class UsuariosRebsol
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE_USUARIO", type="string", length=50, nullable=false)
     */
    private $nombreUsuario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CONTRASENA_MD5", type="text", length=0, nullable=true)
     */
    private $contrasenaMd5;

    /**
     * @var string|null
     *
     * @ORM\Column(name="RCM", type="string", length=45, nullable=true)
     */
    private $rcm;

    /**
     * @var string|null
     *
     * @ORM\Column(name="REGISTRO_SUPERINTENDENCIA", type="string", length=45, nullable=true)
     */
    private $registroSuperintendencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="AVATAR", type="string", length=100, nullable=true)
     */
    private $avatar;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_TERMINO", type="datetime", nullable=true)
     */
    private $fechaTermino;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="AUDITORIA", type="datetime", nullable=false)
     */
    private $auditoria;

    /**
     * @var int
     *
     * @ORM\Column(name="INTENTOS_FALLIDOS", type="integer", nullable=false)
     */
    private $intentosFallidos;

    /**
     * @var int|null
     *
     * @ORM\Column(name="COUNT_LOGIN", type="integer", nullable=true)
     */
    private $countLogin;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_SISTEMA", type="boolean", nullable=true)
     */
    private $esSistema;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_SALA", type="boolean", nullable=true)
     */
    private $esSala;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CREACION_SISTEMA", type="integer", nullable=true)
     */
    private $creacionSistema;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_PROFESIONAL_URGENCIA", type="boolean", nullable=true)
     */
    private $esProfesionalUrgencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ZOOM_USER", type="string", length=100, nullable=true)
     */
    private $zoomUser;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ES_PROFESIONAL_INTEGRACION", type="boolean", nullable=true)
     */
    private $esProfesionalIntegracion;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="SOLO_MODULO_PACIENTES", type="boolean", nullable=true)
     */
    private $soloModuloPacientes;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="SOLO_PACIENTES_ASIGNADOS", type="boolean", nullable=true)
     */
    private $soloPacientesAsignados;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="VER_CAJA", type="boolean", nullable=false, options={"default"="0"}))
     */
    private $verCaja;

    /**
     * @var string|null
     *
     * @ORM\Column(name="API_KEY", type="string", unique=true, nullable=true)
     */
    private $apiKey;

    /**
     * @var \EstadoUsuarios
     *
     * @ORM\ManyToOne(targetEntity="EstadoUsuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_USUARIO", referencedColumnName="ID")
     * })
     */
    private $idEstadoUsuario;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PERSONA", referencedColumnName="ID")
     * })
     */
    private $idPersona;


    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="RelUsuarioPerfil", mappedBy="idUsuario")
     */
    private $perfilesIndividuales;

    /**
     * @var \Doctrine\Common\Collections\Collection 
     * @ORM\OneToMany(targetEntity="RelUsuarioGrupo", mappedBy="idUsuario")
     */
    private $perfilesGrupo;

    /**
     * @Assert\Image(maxSize = "10000k")
     */
    protected $foto;

    protected $container;

    /**
     * Constructor
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->perfilesIndividuales = new \Doctrine\Common\Collections\ArrayCollection();
        $this->perfilesGrupo = new \Doctrine\Common\Collections\ArrayCollection();
        $this->container = $container;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombreUsuario.
     *
     * @param string $nombreUsuario
     *
     * @return UsuariosRebsol
     */
    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;

        return $this;
    }

    /**
     * Get nombreUsuario.
     *
     * @return string
     */
    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    /**
     * Set contrasenaMd5.
     *
     * @param string|null $contrasenaMd5
     *
     * @return UsuariosRebsol
     */
    public function setContrasenaMd5($contrasenaMd5 = null)
    {
        $this->contrasenaMd5 = $contrasenaMd5;

        return $this;
    }

    /**
     * Get contrasenaMd5.
     *
     * @return string|null
     */
    public function getContrasenaMd5()
    {
        return $this->contrasenaMd5;
    }

    /**
     * Set rcm.
     *
     * @param string|null $rcm
     *
     * @return UsuariosRebsol
     */
    public function setRcm($rcm = null)
    {
        $this->rcm = $rcm;

        return $this;
    }

    /**
     * Get rcm.
     *
     * @return string|null
     */
    public function getRcm()
    {
        return $this->rcm;
    }

    /**
     * Set registroSuperintendencia.
     *
     * @param string|null $registroSuperintendencia
     *
     * @return UsuariosRebsol
     */
    public function setRegistroSuperintendencia($registroSuperintendencia = null)
    {
        $this->registroSuperintendencia = $registroSuperintendencia;

        return $this;
    }

    /**
     * Get registroSuperintendencia.
     *
     * @return string|null
     */
    public function getRegistroSuperintendencia()
    {
        return $this->registroSuperintendencia;
    }

    /**
     * Set avatar.
     *
     * @param string|null $avatar
     *
     * @return UsuariosRebsol
     */
    public function setAvatar($avatar = null)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar.
     *
     * @return string|null
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return UsuariosRebsol
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaTermino.
     *
     * @param \DateTime|null $fechaTermino
     *
     * @return UsuariosRebsol
     */
    public function setFechaTermino($fechaTermino = null)
    {
        $this->fechaTermino = $fechaTermino;

        return $this;
    }

    /**
     * Get fechaTermino.
     *
     * @return \DateTime|null
     */
    public function getFechaTermino()
    {
        return $this->fechaTermino;
    }

    /**
     * Set auditoria.
     *
     * @param \DateTime $auditoria
     *
     * @return UsuariosRebsol
     */
    public function setAuditoria($auditoria)
    {
        $this->auditoria = $auditoria;

        return $this;
    }

    /**
     * Get auditoria.
     *
     * @return \DateTime
     */
    public function getAuditoria()
    {
        return $this->auditoria;
    }

    /**
     * Set intentosFallidos.
     *
     * @param int $intentosFallidos
     *
     * @return UsuariosRebsol
     */
    public function setIntentosFallidos($intentosFallidos)
    {
        $this->intentosFallidos = $intentosFallidos;

        return $this;
    }

    /**
     * Get intentosFallidos.
     *
     * @return int
     */
    public function getIntentosFallidos()
    {
        return $this->intentosFallidos;
    }

    /**
     * Set countLogin.
     *
     * @param int|null $countLogin
     *
     * @return UsuariosRebsol
     */
    public function setCountLogin($countLogin = null)
    {
        $this->countLogin = $countLogin;

        return $this;
    }

    /**
     * Get countLogin.
     *
     * @return int|null
     */
    public function getCountLogin()
    {
        return $this->countLogin;
    }

    /**
     * Set esSistema.
     *
     * @param bool|null $esSistema
     *
     * @return UsuariosRebsol
     */
    public function setEsSistema($esSistema = null)
    {
        $this->esSistema = $esSistema;

        return $this;
    }

    /**
     * Get esSistema.
     *
     * @return bool|null
     */
    public function getEsSistema()
    {
        return $this->esSistema;
    }

    /**
     * Set esSala.
     *
     * @param bool|null $esSala
     *
     * @return UsuariosRebsol
     */
    public function setEsSala($esSala = null)
    {
        $this->esSala = $esSala;

        return $this;
    }

    /**
     * Get esSala.
     *
     * @return bool|null
     */
    public function getEsSala()
    {
        return $this->esSala;
    }

    /**
     * Set creacionSistema.
     *
     * @param int|null $creacionSistema
     *
     * @return UsuariosRebsol
     */
    public function setCreacionSistema($creacionSistema = null)
    {
        $this->creacionSistema = $creacionSistema;

        return $this;
    }

    /**
     * Get creacionSistema.
     *
     * @return int|null
     */
    public function getCreacionSistema()
    {
        return $this->creacionSistema;
    }

    /**
     * Set esProfesionalUrgencia.
     *
     * @param bool|null $esProfesionalUrgencia
     *
     * @return UsuariosRebsol
     */
    public function setEsProfesionalUrgencia($esProfesionalUrgencia = null)
    {
        $this->esProfesionalUrgencia = $esProfesionalUrgencia;

        return $this;
    }

    /**
     * Get esProfesionalUrgencia.
     *
     * @return bool|null
     */
    public function getEsProfesionalUrgencia()
    {
        return $this->esProfesionalUrgencia;
    }

    /**
     * Set zoomUser.
     *
     * @param string|null $zoomUser
     *
     * @return UsuariosRebsol
     */
    public function setZoomUser($zoomUser = null)
    {
        $this->zoomUser = $zoomUser;

        return $this;
    }

    /**
     * Get zoomUser.
     *
     * @return string|null
     */
    public function getZoomUser()
    {
        return $this->zoomUser;
    }

    /**
     * Set esProfesionalIntegracion.
     *
     * @param bool|null $esProfesionalIntegracion
     *
     * @return UsuariosRebsol
     */
    public function setEsProfesionalIntegracion($esProfesionalIntegracion = null)
    {
        $this->esProfesionalIntegracion = $esProfesionalIntegracion;

        return $this;
    }

    /**
     * Get esProfesionalIntegracion.
     *
     * @return bool|null
     */
    public function getEsProfesionalIntegracion()
    {
        return $this->esProfesionalIntegracion;
    }

    /**
     * Set soloModuloPacientes.
     *
     * @param bool|null $soloModuloPacientes
     *
     * @return UsuariosRebsol
     */
    public function setSoloModuloPacientes($soloModuloPacientes = null)
    {
        $this->soloModuloPacientes = $soloModuloPacientes;

        return $this;
    }

    /**
     * Get soloModuloPacientes.
     *
     * @return bool|null
     */
    public function getSoloModuloPacientes()
    {
        return $this->soloModuloPacientes;
    }

    /**
     * Set soloPacientesAsignados.
     *
     * @param bool|null $soloPacientesAsignados
     *
     * @return UsuariosRebsol
     */
    public function setSoloPacientesAsignados($soloPacientesAsignados = null)
    {
        $this->soloPacientesAsignados = $soloPacientesAsignados;

        return $this;
    }

    /**
     * Get soloPacientesAsignados.
     *
     * @return bool|null
     */
    public function getSoloPacientesAsignados()
    {
        return $this->soloPacientesAsignados;
    }

    /**
     * @return bool|null
     */
    public function getVerCaja()
    {
        return $this->verCaja;
    }

    /**
     * @param bool|null $verCaja
     */
    public function setVerCaja($verCaja)
    {
        $this->verCaja = $verCaja;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Add perfilesIndividuale.
     *
     * @param \Rebsol\HermesBundle\Entity\RelUsuarioPerfil $perfilesIndividuale
     *
     * @return UsuariosRebsol
     */
    public function addPerfilesIndividuale(\Rebsol\HermesBundle\Entity\RelUsuarioPerfil $perfilesIndividuale)
    {
        $this->perfilesIndividuales[] = $perfilesIndividuale;

        return $this;
    }

    /**
     * Remove perfilesIndividuale.
     *
     * @param \Rebsol\HermesBundle\Entity\RelUsuarioPerfil $perfilesIndividuale
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePerfilesIndividuale(\Rebsol\HermesBundle\Entity\RelUsuarioPerfil $perfilesIndividuale)
    {
        return $this->perfilesIndividuales->removeElement($perfilesIndividuale);
    }

    /**
     * Get perfilesIndividuales.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPerfilesIndividuales()
    {
        return $this->perfilesIndividuales;
    }

    /**
     * Add perfilesGrupo.
     *
     * @param \Rebsol\HermesBundle\Entity\RelUsuarioGrupo $perfilesGrupo
     *
     * @return UsuariosRebsol
     */
    public function addPerfilesGrupo(\Rebsol\HermesBundle\Entity\RelUsuarioGrupo $perfilesGrupo)
    {
        $this->perfilesGrupo[] = $perfilesGrupo;

        return $this;
    }

    /**
     * Remove perfilesGrupo.
     *
     * @param \Rebsol\HermesBundle\Entity\RelUsuarioGrupo $perfilesGrupo
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePerfilesGrupo(\Rebsol\HermesBundle\Entity\RelUsuarioGrupo $perfilesGrupo)
    {
        return $this->perfilesGrupo->removeElement($perfilesGrupo);
    }

    /**
     * Get perfilesGrupo.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPerfilesGrupo()
    {
        return $this->perfilesGrupo;
    }

    /**
     * Set idEstadoUsuario.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoUsuarios $idEstadoUsuario
     *
     * @return UsuariosRebsol
     */
    public function setIdEstadoUsuario(\Rebsol\HermesBundle\Entity\EstadoUsuarios $idEstadoUsuario)
    {
        $this->idEstadoUsuario = $idEstadoUsuario;

        return $this;
    }

    /**
     * Get idEstadoUsuario.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoUsuarios
     */
    public function getIdEstadoUsuario()
    {
        return $this->idEstadoUsuario;
    }

    /**
     * Set idPersona.
     *
     * @param \Rebsol\HermesBundle\Entity\Persona $idPersona
     *
     * @return UsuariosRebsol
     */
    public function setIdPersona(\Rebsol\HermesBundle\Entity\Persona $idPersona)
    {
        $this->idPersona = $idPersona;

        return $this;
    }

    /**
     * Get idPersona.
     *
     * @return \Rebsol\HermesBundle\Entity\Persona
     */
    public function getIdPersona()
    {
        return $this->idPersona;
    }

    /**
    * @param UploadedFile $foto
    */
    public function setFoto(UploadedFile $foto = null)
    {
        $this->foto = $foto;
    }

    /**
    * @return UploadedFile
    */
    public function getFoto()
    {
        return $this->foto;
    }

    public function eraseCredentials() {

    }

    public function getPassword() {
        return $this->contrasenaMd5;
    }

    private $perfilesCalculados = null;

    public function getRoles(): array {
        // $em = $this->getDoctrine();
        if(is_array($this->perfilesCalculados)){
            return $this->perfilesCalculados;
        }

        $this->perfilesCalculados = array();
        $perfilesExcluidos = array(); // Perfiles explícitamente desactivados

        // Primero, recolectar perfiles individuales ACTIVOS e INACTIVOS
        foreach($this->getPerfilesIndividuales() as $perfil){
            if ($perfil->getIdEstado()->getId() == 1) {
                // Perfil individual ACTIVO
                $this->perfilesCalculados[] = 'ROLE_'.$perfil->getIdPerfil()->getNombre();
            } else {
                // Perfil individual INACTIVO - marcarlo como excluido
                $perfilesExcluidos[] = 'ROLE_'.$perfil->getIdPerfil()->getNombre();
            }
        }

        // Luego, agregar perfiles de grupos SOLO si NO están excluidos
        foreach($this->getPerfilesGrupo() as $grupo){
            if ($grupo->getIdEstado()->getId() == 1) {
                $perfiles = $grupo->getIdGrupo()->getPerfiles();
                foreach($perfiles as $perfil){
                    if ($perfil->getIdEstado()->getId() == 1) {
                        $nombrePerfil = 'ROLE_'.$perfil->getIdPerfil()->getNombre();
                        // Solo agregar si NO está en la lista de excluidos
                        if (!in_array($nombrePerfil, $perfilesExcluidos)) {
                            $this->perfilesCalculados[] = $nombrePerfil;
                        }
                    }
                }
            }
        }

        $this->perfilesCalculados = array_unique($this->perfilesCalculados);

        return $this->perfilesCalculados;
    }

    public function getSalt() {
        return null;
    }

    public function getUsername() {
        return $this->nombreUsuario;
    }

    public function setPerfilesIndividuales($perfilesIndividuales) {
        $this->perfilesIndividuales = $perfilesIndividuales;
    }

    public function setPerfilesGrupo($perfilesGrupo){
        $this->perfilesGrupo = $perfilesGrupo;
    }

    public function __sleep(){
        return array('id');
    }

    public function deleteImage($user, $directorio, $nombreArchivoFoto, $oUsuariosRebsol) {
        $nombreArchivo = $oUsuariosRebsol->getavatar();
        if($nombreArchivo){
            unlink($directorio . $nombreArchivoFoto);
            return true;
        }

    }

    public function subirFoto($directorioDestino, $user, $oUsuariosRebsol)
    {
        if (null === $this->getFoto()) {
            return;
        }

        $nombreArchivoFoto = uniqid('user_'.$user.'_').'.'.$this->getFoto()->guessExtension();
        //$this->deleteImage($user, $directorioDestino, $nombreArchivoFoto, $oUsuariosRebsol);
        $this->getFoto()->move($directorioDestino, $nombreArchivoFoto);
        $this->setAvatar($nombreArchivoFoto);
    }

    private function obtenerRolesUsuarioLogin() {
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        } else {
            // var_dump('ERROR USUARIOSREBSOL.PHP LINEA 546');exit();
        }

        $arrPerfiles = $kernel->getContainer()->get('security.token_storage')->getToken()->getRoles();

        $arrReturn = array();

        foreach ($arrPerfiles as $value) {
            $arrReturn[] = $value->getRole();
        }


        // echo('<pre>');var_dump();exit();

        return $arrReturn;
    }

    private function obtenerParametro($nombreParametro) {
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }

        return $kernel->getContainer()->getParameter($nombreParametro);
    }

    public function tienePerfil($perfil) {
        if ( $this->obtenerParametro('tienePerfil') === false ) {
            return true;
        }

        $perfil        = $perfil;
        $arrPerfiles   = $this->getRoles();

        $resultadoPreg = preg_grep("/$perfil/", $arrPerfiles);

        if (COUNT($resultadoPreg) == 0) {
            return false;
        }
        return true;
    }

    public function __toString()
    {
        return (string)$this->nombreUsuario;
    }
}
