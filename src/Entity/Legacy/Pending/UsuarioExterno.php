<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsuarioExterno
 *
 * @ORM\Table(name="usuario_externo")
 * @ORM\Entity
 */
class UsuarioExterno
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
     * @var int
     *
     * @ORM\Column(name="RUT", type="integer", nullable=false)
     */
    private $rut;

    /**
     * @var string
     *
     * @ORM\Column(name="DIGITO_VERIFICADOR", type="string", length=1, nullable=false)
     */
    private $digitoVerificador;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRES", type="string", length=60, nullable=false)
     */
    private $nombres;

    /**
     * @var string
     *
     * @ORM\Column(name="APELLIDO_PATERNO", type="string", length=45, nullable=false)
     */
    private $apellidoPaterno;

    /**
     * @var string
     *
     * @ORM\Column(name="APELLIDO_MATERNO", type="string", length=45, nullable=false)
     */
    private $apellidoMaterno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CORREO_ELECTRONICO", type="string", length=100, nullable=true)
     */
    private $correoElectronico;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_NACIMIENTO", type="datetime", nullable=false)
     */
    private $fechaNacimiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_REGISTRO", type="datetime", nullable=false)
     */
    private $fechaRegistro;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_FIJO", type="string", length=20, nullable=true)
     */
    private $telefonoFijo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_MOVIL", type="string", length=20, nullable=true)
     */
    private $telefonoMovil;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_CONTACTO", type="string", length=20, nullable=true)
     */
    private $telefonoContacto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DIRECCION", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PASSWORD", type="text", length=0, nullable=true)
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NUMERO", type="string", length=10, nullable=true)
     */
    private $numero;

    /**
     * @var \EstadoUsuarioExterno
     *
     * @ORM\ManyToOne(targetEntity="EstadoUsuarioExterno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO_USUARIO_EXTERNO", referencedColumnName="ID")
     * })
     */
    private $idEstadoUsuarioExterno;

    /**
     * @var \Prevision
     *
     * @ORM\ManyToOne(targetEntity="Prevision")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PREVISION", referencedColumnName="ID")
     * })
     */
    private $idPrevision;

    /**
     * @var \TipoLogueo
     *
     * @ORM\ManyToOne(targetEntity="TipoLogueo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_LOGUEO", referencedColumnName="ID")
     * })
     */
    private $idTipoLogueo;

    /**
     * @var \Sexo
     *
     * @ORM\ManyToOne(targetEntity="Sexo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SEXO", referencedColumnName="ID")
     * })
     */
    private $idSexo;

    /**
     * @var \Empresa
     *
     * @ORM\ManyToOne(targetEntity="Empresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idEmpresa;

    /**
     * @var \TipoCreacion
     *
     * @ORM\ManyToOne(targetEntity="TipoCreacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_CREACION", referencedColumnName="ID")
     * })
     */
    private $idTipoCreacion;

    /**
     * @var \Comuna
     *
     * @ORM\ManyToOne(targetEntity="Comuna")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_COMUNA", referencedColumnName="ID")
     * })
     */
    private $idComuna;



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
     * Set rut.
     *
     * @param int $rut
     *
     * @return UsuarioExterno
     */
    public function setRut($rut)
    {
        $this->rut = $rut;

        return $this;
    }

    /**
     * Get rut.
     *
     * @return int
     */
    public function getRut()
    {
        return $this->rut;
    }

    /**
     * Set digitoVerificador.
     *
     * @param string $digitoVerificador
     *
     * @return UsuarioExterno
     */
    public function setDigitoVerificador($digitoVerificador)
    {
        $this->digitoVerificador = $digitoVerificador;

        return $this;
    }

    /**
     * Get digitoVerificador.
     *
     * @return string
     */
    public function getDigitoVerificador()
    {
        return $this->digitoVerificador;
    }

    /**
     * Set nombres.
     *
     * @param string $nombres
     *
     * @return UsuarioExterno
     */
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;

        return $this;
    }

    /**
     * Get nombres.
     *
     * @return string
     */
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * Set apellidoPaterno.
     *
     * @param string $apellidoPaterno
     *
     * @return UsuarioExterno
     */
    public function setApellidoPaterno($apellidoPaterno)
    {
        $this->apellidoPaterno = $apellidoPaterno;

        return $this;
    }

    /**
     * Get apellidoPaterno.
     *
     * @return string
     */
    public function getApellidoPaterno()
    {
        return $this->apellidoPaterno;
    }

    /**
     * Set apellidoMaterno.
     *
     * @param string $apellidoMaterno
     *
     * @return UsuarioExterno
     */
    public function setApellidoMaterno($apellidoMaterno)
    {
        $this->apellidoMaterno = $apellidoMaterno;

        return $this;
    }

    /**
     * Get apellidoMaterno.
     *
     * @return string
     */
    public function getApellidoMaterno()
    {
        return $this->apellidoMaterno;
    }

    /**
     * Set correoElectronico.
     *
     * @param string|null $correoElectronico
     *
     * @return UsuarioExterno
     */
    public function setCorreoElectronico($correoElectronico = null)
    {
        $this->correoElectronico = $correoElectronico;

        return $this;
    }

    /**
     * Get correoElectronico.
     *
     * @return string|null
     */
    public function getCorreoElectronico()
    {
        return $this->correoElectronico;
    }

    /**
     * Set fechaNacimiento.
     *
     * @param \DateTime $fechaNacimiento
     *
     * @return UsuarioExterno
     */
    public function setFechaNacimiento($fechaNacimiento)
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    /**
     * Get fechaNacimiento.
     *
     * @return \DateTime
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * Set fechaRegistro.
     *
     * @param \DateTime $fechaRegistro
     *
     * @return UsuarioExterno
     */
    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    /**
     * Get fechaRegistro.
     *
     * @return \DateTime
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * Set telefonoFijo.
     *
     * @param string|null $telefonoFijo
     *
     * @return UsuarioExterno
     */
    public function setTelefonoFijo($telefonoFijo = null)
    {
        $this->telefonoFijo = $telefonoFijo;

        return $this;
    }

    /**
     * Get telefonoFijo.
     *
     * @return string|null
     */
    public function getTelefonoFijo()
    {
        return $this->telefonoFijo;
    }

    /**
     * Set telefonoMovil.
     *
     * @param string|null $telefonoMovil
     *
     * @return UsuarioExterno
     */
    public function setTelefonoMovil($telefonoMovil = null)
    {
        $this->telefonoMovil = $telefonoMovil;

        return $this;
    }

    /**
     * Get telefonoMovil.
     *
     * @return string|null
     */
    public function getTelefonoMovil()
    {
        return $this->telefonoMovil;
    }

    /**
     * Set telefonoContacto.
     *
     * @param string|null $telefonoContacto
     *
     * @return UsuarioExterno
     */
    public function setTelefonoContacto($telefonoContacto = null)
    {
        $this->telefonoContacto = $telefonoContacto;

        return $this;
    }

    /**
     * Get telefonoContacto.
     *
     * @return string|null
     */
    public function getTelefonoContacto()
    {
        return $this->telefonoContacto;
    }

    /**
     * Set direccion.
     *
     * @param string|null $direccion
     *
     * @return UsuarioExterno
     */
    public function setDireccion($direccion = null)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion.
     *
     * @return string|null
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set password.
     *
     * @param string|null $password
     *
     * @return UsuarioExterno
     */
    public function setPassword($password = null)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set numero.
     *
     * @param string|null $numero
     *
     * @return UsuarioExterno
     */
    public function setNumero($numero = null)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return string|null
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set idTipoCreacion.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoCreacion $idTipoCreacion
     *
     * @return UsuarioExterno
     */
    public function setIdTipoCreacion(\Rebsol\HermesBundle\Entity\TipoCreacion $idTipoCreacion)
    {
        $this->idTipoCreacion = $idTipoCreacion;

        return $this;
    }

    /**
     * Get idTipoCreacion.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoCreacion
     */
    public function getIdTipoCreacion()
    {
        return $this->idTipoCreacion;
    }

    /**
     * Set idTipoLogueo.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoLogueo $idTipoLogueo
     *
     * @return UsuarioExterno
     */
    public function setIdTipoLogueo(\Rebsol\HermesBundle\Entity\TipoLogueo $idTipoLogueo)
    {
        $this->idTipoLogueo = $idTipoLogueo;

        return $this;
    }

    /**
     * Get idTipoLogueo.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoLogueo
     */
    public function getIdTipoLogueo()
    {
        return $this->idTipoLogueo;
    }

    /**
     * Set idEstadoUsuarioExterno.
     *
     * @param \Rebsol\HermesBundle\Entity\EstadoUsuarioExterno $idEstadoUsuarioExterno
     *
     * @return UsuarioExterno
     */
    public function setIdEstadoUsuarioExterno(\Rebsol\HermesBundle\Entity\EstadoUsuarioExterno $idEstadoUsuarioExterno)
    {
        $this->idEstadoUsuarioExterno = $idEstadoUsuarioExterno;

        return $this;
    }

    /**
     * Get idEstadoUsuarioExterno.
     *
     * @return \Rebsol\HermesBundle\Entity\EstadoUsuarioExterno
     */
    public function getIdEstadoUsuarioExterno()
    {
        return $this->idEstadoUsuarioExterno;
    }

    /**
     * Set idPrevision.
     *
     * @param \Rebsol\HermesBundle\Entity\Prevision $idPrevision
     *
     * @return UsuarioExterno
     */
    public function setIdPrevision(\Rebsol\HermesBundle\Entity\Prevision $idPrevision)
    {
        $this->idPrevision = $idPrevision;

        return $this;
    }

    /**
     * Get idPrevision.
     *
     * @return \Rebsol\HermesBundle\Entity\Prevision
     */
    public function getIdPrevision()
    {
        return $this->idPrevision;
    }

    /**
     * Set idSexo.
     *
     * @param \Rebsol\HermesBundle\Entity\Sexo $idSexo
     *
     * @return UsuarioExterno
     */
    public function setIdSexo(\Rebsol\HermesBundle\Entity\Sexo $idSexo)
    {
        $this->idSexo = $idSexo;

        return $this;
    }

    /**
     * Get idSexo.
     *
     * @return \Rebsol\HermesBundle\Entity\Sexo
     */
    public function getIdSexo()
    {
        return $this->idSexo;
    }

    /**
     * Set idComuna.
     *
     * @param \Rebsol\HermesBundle\Entity\Comuna|null $idComuna
     *
     * @return UsuarioExterno
     */
    public function setIdComuna(\Rebsol\HermesBundle\Entity\Comuna $idComuna = null)
    {
        $this->idComuna = $idComuna;

        return $this;
    }

    /**
     * Get idComuna.
     *
     * @return \Rebsol\HermesBundle\Entity\Comuna|null
     */
    public function getIdComuna()
    {
        return $this->idComuna;
    }

    /**
     * Set idEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\Empresa $idEmpresa
     *
     * @return UsuarioExterno
     */
    public function setIdEmpresa(\Rebsol\HermesBundle\Entity\Empresa $idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\Empresa
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }
}
