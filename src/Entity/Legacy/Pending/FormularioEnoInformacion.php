<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormularioEnoInformacion
 *
 * @ORM\Table(name="formulario_eno_informacion")
 * @ORM\Entity
 */
class FormularioEnoInformacion
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=true)
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_PRIMER_SINTOMA", type="datetime", nullable=true)
     */
    private $fechaPrimerSintoma;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TEXTO_ID_PAIS_CONTAGIO", type="text", length=0, nullable=true)
     */
    private $textoIdPaisContagio;

    /**
     * @var string|null
     *
     * @ORM\Column(name="VALOR_TBC", type="text", length=0, nullable=true)
     */
    private $valorTBC;

    /**
     * @var string|null
     *
     * @ORM\Column(name="VALOR_RECAIDA", type="text", length=0, nullable=true)
     */
    private $valorRecaida;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TEXTO_DIAGNOSTICO", type="text", length=0, nullable=true)
     */
    private $textoDiagnostico;

    /**
     * @var string|null
     *
     * @ORM\Column(name="TEXTO_SEGUNDO_DIAGNOSTICO", type="text", length=0, nullable=true)
     */
    private $textoSegundoDiagnostico;

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
     * Set fechaCreacion.
     *
     * @param \DateTime|null $fechaCreacion
     *
     * @return FormularioEnoInformacion
     */
    public function setFechaCreacion($fechaCreacion = null)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime|null
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaPrimerSintoma.
     *
     * @param \DateTime|null $fechaPrimerSintoma
     *
     * @return FormularioEnoInformacion
     */
    public function setFechaPrimerSintoma($fechaPrimerSintoma = null)
    {
        $this->fechaPrimerSintoma = $fechaPrimerSintoma;

        return $this;
    }

    /**
     * Get fechaPrimerSintoma.
     *
     * @return \DateTime|null
     */
    public function getFechaPrimerSintoma()
    {
        return $this->fechaPrimerSintoma;
    }

    /**
     * Set textoIdPaisContagio.
     *
     * @param string|null $textoIdPaisContagio
     *
     * @return FormularioEnoInformacion
     */
    public function setTextoIdPaisContagio($textoIdPaisContagio = null)
    {
        $this->textoIdPaisContagio = $textoIdPaisContagio;

        return $this;
    }

    /**
     * Get textoIdPaisContagio.
     *
     * @return string|null
     */
    public function getTextoIdPaisContagio()
    {
        return $this->textoIdPaisContagio;
    }

    /**
     * Set valorTBC.
     *
     * @param string|null $valorTBC
     *
     * @return FormularioEnoInformacion
     */
    public function setValorTBC($valorTBC = null)
    {
        $this->valorTBC = $valorTBC;

        return $this;
    }

    /**
     * Get valorTBC.
     *
     * @return string|null
     */
    public function getValorTBC()
    {
        return $this->valorTBC;
    }

    /**
     * Set valorRecaida.
     *
     * @param string|null $valorRecaida
     *
     * @return FormularioEnoInformacion
     */
    public function setValorRecaida($valorRecaida = null)
    {
        $this->valorRecaida = $valorRecaida;

        return $this;
    }

    /**
     * Get valorRecaida.
     *
     * @return string|null
     */
    public function getValorRecaida()
    {
        return $this->valorRecaida;
    }

    /**
     * Set textoDiagnostico.
     *
     * @param string|null $textoDiagnostico
     *
     * @return FormularioEnoInformacion
     */
    public function setTextoDiagnostico($textoDiagnostico = null)
    {
        $this->textoDiagnostico = $textoDiagnostico;

        return $this;
    }

    /**
     * Get textoDiagnostico.
     *
     * @return string|null
     */
    public function getTextoDiagnostico()
    {
        return $this->textoDiagnostico;
    }

    /**
     * Set textoSegundoDiagnostico.
     *
     * @param string|null $textoSegundoDiagnostico
     *
     * @return FormularioEnoInformacion
     */
    public function setTextoSegundoDiagnostico($textoSegundoDiagnostico = null)
    {
        $this->textoSegundoDiagnostico = $textoSegundoDiagnostico;

        return $this;
    }

    /**
     * Get textoSegundoDiagnostico.
     *
     * @return string|null
     */
    public function getTextoSegundoDiagnostico()
    {
        return $this->textoSegundoDiagnostico;
    }
}
