<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * Persona
 *
 * @ORM\Table(name="persona", indexes={@ORM\Index(name="IDX_PERSONA_IDENTIFICACIONEXTRANJERO", columns={"IDENTIFICACION_EXTRANJERO"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PersonaRepository")
 */
class Persona
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
     * @ORM\Column(name="RUT_PERSONA", type="integer", nullable=false)
     */
    private $rutPersona;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DIGITO_VERIFICADOR", type="string", length=1, nullable=true)
     */
    private $digitoVerificador;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_MOVIL", type="string", length=20, nullable=true)
     */
    private $telefonoMovil;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_TRABAJO", type="string", length=20, nullable=true)
     */
    private $telefonoTrabajo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TELEFONO_FIJO", type="string", length=20, nullable=true)
     */
    private $telefonoFijo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CORREO_ELECTRONICO", type="string", length=100, nullable=true)
     */
    private $correoElectronico;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CORREO_ELECTRONICO2", type="string", length=100, nullable=true)
     */
    private $correoElectronico2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="FAX", type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @var string|null
     *
     * @ORM\Column(name="IDENTIFICACION_EXTRANJERO", type="string", length=100, nullable=true)
     */
    private $identificacionExtranjero;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MEDIO_CONTACTO", type="string", length=50, nullable=true)
     */
    private $medioContacto;

    /**
     * @var \TipoIdentificacionExtranjero
     *
     * @ORM\ManyToOne(targetEntity="TipoIdentificacionExtranjero")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_IDENTIFICACION_EXTRANJERO", referencedColumnName="ID")
     * })
     */
    private $idTipoIdentificacionExtranjero;

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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rutPersona.
     *
     * @param int $rutPersona
     *
     * @return Persona
     */
    public function setRutPersona($rutPersona)
    {
        $this->rutPersona = $rutPersona;

        return $this;
    }

    /**
     * Get rutPersona.
     *
     * @return int
     */
    public function getRutPersona()
    {
        return $this->rutPersona;
    }

    /**
     * Set digitoVerificador.
     *
     * @param string|null $digitoVerificador
     *
     * @return Persona
     */
    public function setDigitoVerificador($digitoVerificador = null)
    {
        $this->digitoVerificador = $digitoVerificador;

        return $this;
    }

    /**
     * Get digitoVerificador.
     *
     * @return string|null
     */
    public function getDigitoVerificador()
    {
        return $this->digitoVerificador;
    }

    /**
     * Set telefonoMovil.
     *
     * @param string|null $telefonoMovil
     *
     * @return Persona
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
     * Set telefonoTrabajo.
     *
     * @param string|null $telefonoTrabajo
     *
     * @return Persona
     */
    public function setTelefonoTrabajo($telefonoTrabajo = null)
    {
        $this->telefonoTrabajo = $telefonoTrabajo;

        return $this;
    }

    /**
     * Get telefonoTrabajo.
     *
     * @return string|null
     */
    public function getTelefonoTrabajo()
    {
        return $this->telefonoTrabajo;
    }

    /**
     * Set telefonoFijo.
     *
     * @param string|null $telefonoFijo
     *
     * @return Persona
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
     * Set correoElectronico.
     *
     * @param string|null $correoElectronico
     *
     * @return Persona
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
     * Set correoElectronico2.
     *
     * @param string|null $correoElectronico2
     *
     * @return Persona
     */
    public function setCorreoElectronico2($correoElectronico2 = null)
    {
        $this->correoElectronico2 = $correoElectronico2;

        return $this;
    }

    /**
     * Get correoElectronico2.
     *
     * @return string|null
     */
    public function getCorreoElectronico2()
    {
        return $this->correoElectronico2;
    }

    /**
     * Set fax.
     *
     * @param string|null $fax
     *
     * @return Persona
     */
    public function setFax($fax = null)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax.
     *
     * @return string|null
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set identificacionExtranjero.
     *
     * @param string|null $identificacionExtranjero
     *
     * @return Persona
     */
    public function setIdentificacionExtranjero($identificacionExtranjero = null)
    {
        $this->identificacionExtranjero = $identificacionExtranjero;

        return $this;
    }

    /**
     * Get identificacionExtranjero.
     *
     * @return string|null
     */
    public function getIdentificacionExtranjero()
    {
        return $this->identificacionExtranjero;
    }

    /**
     * Set medioContacto.
     *
     * @param string|null $medioContacto
     *
     * @return Persona
     */
    public function setMedioContacto($medioContacto = null)
    {
        $this->medioContacto = $medioContacto;

        return $this;
    }

    /**
     * Get medioContacto.
     *
     * @return string|null
     */
    public function getMedioContacto()
    {
        return $this->medioContacto;
    }

    /**
     * Set idEmpresa.
     *
     * @param \App\Entity\Legacy\Legacy\Empresa $idEmpresa
     *
     * @return Persona
     */
    public function setIdEmpresa(\App\Entity\Legacy\Legacy\Empresa $idEmpresa)
    {
        $this->idEmpresa = $idEmpresa;

        return $this;
    }

    /**
     * Get idEmpresa.
     *
     * @return \App\Entity\Legacy\Legacy\Empresa
     */
    public function getIdEmpresa()
    {
        return $this->idEmpresa;
    }

    /**
     * Set idTipoIdentificacionExtranjero.
     *
     * @param \App\Entity\Legacy\Legacy\TipoIdentificacionExtranjero|null $idTipoIdentificacionExtranjero
     *
     * @return Persona
     */
    public function setIdTipoIdentificacionExtranjero(\App\Entity\Legacy\Legacy\TipoIdentificacionExtranjero $idTipoIdentificacionExtranjero = null)
    {
        $this->idTipoIdentificacionExtranjero = $idTipoIdentificacionExtranjero;

        return $this;
    }

    /**
     * Get idTipoIdentificacionExtranjero.
     *
     * @return \App\Entity\Legacy\Legacy\TipoIdentificacionExtranjero|null
     */
    public function getIdTipoIdentificacionExtranjero()
    {
        return $this->idTipoIdentificacionExtranjero;
    }

    /**
     * obtener objeto Pnatural relacionado a esta persona
     *
     * @return \App\Entity\Legacy\Legacy\Legacy\Pnatural
     */
    public function getIdPnatural($iNumeroHermanoGemelo = 0)
    {
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }

        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        return $em->getRepository("RebsolHermesBundle:Pnatural")->findOneBy(
            array(
                'numeroHermanoGemelo' => $iNumeroHermanoGemelo,
                'idPersona'           => $this->id
                )
            );
    }
}
