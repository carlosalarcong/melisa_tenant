<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * InterfazImed
 *
 * @ORM\Table(name="interfaz_imed", indexes={@ORM\Index(name="RUT_BENEF_INDEX", columns={"RUT_BENEF"})})
 * @ORM\Entity
 */
class InterfazImed
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="COD_USUARIO", type="string", length=255, nullable=false)
     */
    private $codUsuario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="COD_CLAVE", type="string", length=255, nullable=true)
     */
    private $codClave;

    /**
     * @var string
     *
     * @ORM\Column(name="RUT_CONVENIO", type="string", length=255, nullable=false)
     */
    private $rutConvenio;

    /**
     * @var string
     *
     * @ORM\Column(name="RUT_TRATANTE", type="string", length=255, nullable=false)
     */
    private $rutTratante;

    /**
     * @var string
     *
     * @ORM\Column(name="RUT_SOLIC", type="string", length=255, nullable=false)
     */
    private $rutSolic;

    /**
     * @var string
     *
     * @ORM\Column(name="RUT_BENEF", type="string", length=255, nullable=false)
     */
    private $rutBenef;

    /**
     * @var string
     *
     * @ORM\Column(name="RUT_CAJERO", type="string", length=255, nullable=false)
     */
    private $rutCajero;

    /**
     * @var string
     *
     * @ORM\Column(name="INDURGENCIA", type="string", length=255, nullable=false)
     */
    private $indurgencia;

    /**
     * @var string
     *
     * @ORM\Column(name="URL_RET_EXITO", type="string", length=255, nullable=false)
     */
    private $urlRetExito;

    /**
     * @var string
     *
     * @ORM\Column(name="URL_RET_ERROR", type="string", length=255, nullable=false)
     */
    private $urlRetError;

    /**
     * @var int
     *
     * @ORM\Column(name="COD_FINANCIADOR", type="integer", nullable=false)
     */
    private $codFinanciador;

    /**
     * @var int
     *
     * @ORM\Column(name="COD_LUGAR", type="integer", nullable=false)
     */
    private $codLugar;

    /**
     * @var int
     *
     * @ORM\Column(name="COD_TIPO_TRATAMIENTO", type="integer", nullable=false)
     */
    private $codTipoTratamiento;

    /**
     * @var int
     *
     * @ORM\Column(name="CORR_CONVENIO", type="integer", nullable=false)
     */
    private $corrConvenio;

    /**
     * @var string
     *
     * @ORM\Column(name="NOM_SOLIC", type="string", length=255, nullable=false)
     */
    private $nomSolic;

    /**
     * @var int
     *
     * @ORM\Column(name="FEC_INI_TRATAMIENTO", type="integer", nullable=false)
     */
    private $fecIniTratamiento;

    /**
     * @var int
     *
     * @ORM\Column(name="FEC_TER_TRATAMIENTO", type="integer", nullable=false)
     */
    private $fecTerTratamiento;

    /**
     * @var int
     *
     * @ORM\Column(name="CANT_DIAS", type="integer", nullable=false)
     */
    private $cantDias;

    /**
     * @var int
     *
     * @ORM\Column(name="FOLIO_ANTECEDENTE", type="integer", nullable=false)
     */
    private $folioAntecedente;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_PACIENTE", type="integer", nullable=true)
     */
    private $idPaciente;

    /**
     * @var int
     *
     * @ORM\Column(name="ESTADO", type="integer", nullable=false)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="LIS_PRESTA_UT", type="text", length=0, nullable=false)
     */
    private $lisPrestaUt;

    /**
     * @var string
     *
     * @ORM\Column(name="LISTA_BONOS", type="text", length=0, nullable=false)
     */
    private $listaBonos;

    /**
     * @var string
     *
     * @ORM\Column(name="LISTA_FOR_PAG", type="text", length=0, nullable=false)
     */
    private $listaForPag;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_TRANS_IMED", type="datetime", nullable=false)
     */
    private $fechaTransImed;

    /**
     * @var string
     *
     * @ORM\Column(name="CODIGO_TRANSACCION", type="string", length=255, nullable=false)
     */
    private $codigoTransaccion;

    /**
     * @var string
     *
     * @ORM\Column(name="NUMERO_AUDITORIA", type="string", length=255, nullable=false)
     */
    private $numeroAuditoria;

    /**
     * @var string|null
     *
     * @ORM\Column(name="GLO_ERROR", type="string", length=255, nullable=true)
     */
    private $gloError;

    /**
     * @var \PagoCuenta
     *
     * @ORM\ManyToOne(targetEntity="PagoCuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_PAGO_CUENTA", referencedColumnName="ID")
     * })
     */
    private $idPagoCuenta;



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
     * Set codUsuario.
     *
     * @param string $codUsuario
     *
     * @return InterfazImed
     */
    public function setCodUsuario($codUsuario)
    {
        $this->codUsuario = $codUsuario;

        return $this;
    }

    /**
     * Get codUsuario.
     *
     * @return string
     */
    public function getCodUsuario()
    {
        return $this->codUsuario;
    }

    /**
     * Set codClave.
     *
     * @param string|null $codClave
     *
     * @return InterfazImed
     */
    public function setCodClave($codClave = null)
    {
        $this->codClave = $codClave;

        return $this;
    }

    /**
     * Get codClave.
     *
     * @return string|null
     */
    public function getCodClave()
    {
        return $this->codClave;
    }

    /**
     * Set rutConvenio.
     *
     * @param string $rutConvenio
     *
     * @return InterfazImed
     */
    public function setRutConvenio($rutConvenio)
    {
        $this->rutConvenio = $rutConvenio;

        return $this;
    }

    /**
     * Get rutConvenio.
     *
     * @return string
     */
    public function getRutConvenio()
    {
        return $this->rutConvenio;
    }

    /**
     * Set rutTratante.
     *
     * @param string $rutTratante
     *
     * @return InterfazImed
     */
    public function setRutTratante($rutTratante)
    {
        $this->rutTratante = $rutTratante;

        return $this;
    }

    /**
     * Get rutTratante.
     *
     * @return string
     */
    public function getRutTratante()
    {
        return $this->rutTratante;
    }

    /**
     * Set rutSolic.
     *
     * @param string $rutSolic
     *
     * @return InterfazImed
     */
    public function setRutSolic($rutSolic)
    {
        $this->rutSolic = $rutSolic;

        return $this;
    }

    /**
     * Get rutSolic.
     *
     * @return string
     */
    public function getRutSolic()
    {
        return $this->rutSolic;
    }

    /**
     * Set rutBenef.
     *
     * @param string $rutBenef
     *
     * @return InterfazImed
     */
    public function setRutBenef($rutBenef)
    {
        $this->rutBenef = $rutBenef;

        return $this;
    }

    /**
     * Get rutBenef.
     *
     * @return string
     */
    public function getRutBenef()
    {
        return $this->rutBenef;
    }

    /**
     * Set rutCajero.
     *
     * @param string $rutCajero
     *
     * @return InterfazImed
     */
    public function setRutCajero($rutCajero)
    {
        $this->rutCajero = $rutCajero;

        return $this;
    }

    /**
     * Get rutCajero.
     *
     * @return string
     */
    public function getRutCajero()
    {
        return $this->rutCajero;
    }

    /**
     * Set indurgencia.
     *
     * @param string $indurgencia
     *
     * @return InterfazImed
     */
    public function setIndurgencia($indurgencia)
    {
        $this->indurgencia = $indurgencia;

        return $this;
    }

    /**
     * Get indurgencia.
     *
     * @return string
     */
    public function getIndurgencia()
    {
        return $this->indurgencia;
    }

    /**
     * Set urlRetExito.
     *
     * @param string $urlRetExito
     *
     * @return InterfazImed
     */
    public function setUrlRetExito($urlRetExito)
    {
        $this->urlRetExito = $urlRetExito;

        return $this;
    }

    /**
     * Get urlRetExito.
     *
     * @return string
     */
    public function getUrlRetExito()
    {
        return $this->urlRetExito;
    }

    /**
     * Set urlRetError.
     *
     * @param string $urlRetError
     *
     * @return InterfazImed
     */
    public function setUrlRetError($urlRetError)
    {
        $this->urlRetError = $urlRetError;

        return $this;
    }

    /**
     * Get urlRetError.
     *
     * @return string
     */
    public function getUrlRetError()
    {
        return $this->urlRetError;
    }

    /**
     * Set codFinanciador.
     *
     * @param int $codFinanciador
     *
     * @return InterfazImed
     */
    public function setCodFinanciador($codFinanciador)
    {
        $this->codFinanciador = $codFinanciador;

        return $this;
    }

    /**
     * Get codFinanciador.
     *
     * @return int
     */
    public function getCodFinanciador()
    {
        return $this->codFinanciador;
    }

    /**
     * Set codLugar.
     *
     * @param int $codLugar
     *
     * @return InterfazImed
     */
    public function setCodLugar($codLugar)
    {
        $this->codLugar = $codLugar;

        return $this;
    }

    /**
     * Get codLugar.
     *
     * @return int
     */
    public function getCodLugar()
    {
        return $this->codLugar;
    }

    /**
     * Set codTipoTratamiento.
     *
     * @param int $codTipoTratamiento
     *
     * @return InterfazImed
     */
    public function setCodTipoTratamiento($codTipoTratamiento)
    {
        $this->codTipoTratamiento = $codTipoTratamiento;

        return $this;
    }

    /**
     * Get codTipoTratamiento.
     *
     * @return int
     */
    public function getCodTipoTratamiento()
    {
        return $this->codTipoTratamiento;
    }

    /**
     * Set corrConvenio.
     *
     * @param int $corrConvenio
     *
     * @return InterfazImed
     */
    public function setCorrConvenio($corrConvenio)
    {
        $this->corrConvenio = $corrConvenio;

        return $this;
    }

    /**
     * Get corrConvenio.
     *
     * @return int
     */
    public function getCorrConvenio()
    {
        return $this->corrConvenio;
    }

    /**
     * Set nomSolic.
     *
     * @param string $nomSolic
     *
     * @return InterfazImed
     */
    public function setNomSolic($nomSolic)
    {
        $this->nomSolic = $nomSolic;

        return $this;
    }

    /**
     * Get nomSolic.
     *
     * @return string
     */
    public function getNomSolic()
    {
        return $this->nomSolic;
    }

    /**
     * Set fecIniTratamiento.
     *
     * @param int $fecIniTratamiento
     *
     * @return InterfazImed
     */
    public function setFecIniTratamiento($fecIniTratamiento)
    {
        $this->fecIniTratamiento = $fecIniTratamiento;

        return $this;
    }

    /**
     * Get fecIniTratamiento.
     *
     * @return int
     */
    public function getFecIniTratamiento()
    {
        return $this->fecIniTratamiento;
    }

    /**
     * Set fecTerTratamiento.
     *
     * @param int $fecTerTratamiento
     *
     * @return InterfazImed
     */
    public function setFecTerTratamiento($fecTerTratamiento)
    {
        $this->fecTerTratamiento = $fecTerTratamiento;

        return $this;
    }

    /**
     * Get fecTerTratamiento.
     *
     * @return int
     */
    public function getFecTerTratamiento()
    {
        return $this->fecTerTratamiento;
    }

    /**
     * Set cantDias.
     *
     * @param int $cantDias
     *
     * @return InterfazImed
     */
    public function setCantDias($cantDias)
    {
        $this->cantDias = $cantDias;

        return $this;
    }

    /**
     * Get cantDias.
     *
     * @return int
     */
    public function getCantDias()
    {
        return $this->cantDias;
    }

    /**
     * Set folioAntecedente.
     *
     * @param int $folioAntecedente
     *
     * @return InterfazImed
     */
    public function setFolioAntecedente($folioAntecedente)
    {
        $this->folioAntecedente = $folioAntecedente;

        return $this;
    }

    /**
     * Get folioAntecedente.
     *
     * @return int
     */
    public function getFolioAntecedente()
    {
        return $this->folioAntecedente;
    }

    /**
     * Set idPaciente.
     *
     * @param int|null $idPaciente
     *
     * @return InterfazImed
     */
    public function setIdPaciente($idPaciente = null)
    {
        $this->idPaciente = $idPaciente;

        return $this;
    }

    /**
     * Get idPaciente.
     *
     * @return int|null
     */
    public function getIdPaciente()
    {
        return $this->idPaciente;
    }

    /**
     * Set estado.
     *
     * @param int $estado
     *
     * @return InterfazImed
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado.
     *
     * @return int
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set lisPrestaUt.
     *
     * @param string $lisPrestaUt
     *
     * @return InterfazImed
     */
    public function setLisPrestaUt($lisPrestaUt)
    {
        $this->lisPrestaUt = $lisPrestaUt;

        return $this;
    }

    /**
     * Get lisPrestaUt.
     *
     * @return string
     */
    public function getLisPrestaUt()
    {
        return $this->lisPrestaUt;
    }

    /**
     * Set listaBonos.
     *
     * @param string $listaBonos
     *
     * @return InterfazImed
     */
    public function setListaBonos($listaBonos)
    {
        $this->listaBonos = $listaBonos;

        return $this;
    }

    /**
     * Get listaBonos.
     *
     * @return string
     */
    public function getListaBonos()
    {
        return $this->listaBonos;
    }

    /**
     * Set listaForPag.
     *
     * @param string $listaForPag
     *
     * @return InterfazImed
     */
    public function setListaForPag($listaForPag)
    {
        $this->listaForPag = $listaForPag;

        return $this;
    }

    /**
     * Get listaForPag.
     *
     * @return string
     */
    public function getListaForPag()
    {
        return $this->listaForPag;
    }

    /**
     * Set fechaTransImed.
     *
     * @param \DateTime $fechaTransImed
     *
     * @return InterfazImed
     */
    public function setFechaTransImed($fechaTransImed)
    {
        $this->fechaTransImed = $fechaTransImed;

        return $this;
    }

    /**
     * Get fechaTransImed.
     *
     * @return \DateTime
     */
    public function getFechaTransImed()
    {
        return $this->fechaTransImed;
    }

    /**
     * Set codigoTransaccion.
     *
     * @param string $codigoTransaccion
     *
     * @return InterfazImed
     */
    public function setCodigoTransaccion($codigoTransaccion)
    {
        $this->codigoTransaccion = $codigoTransaccion;

        return $this;
    }

    /**
     * Get codigoTransaccion.
     *
     * @return string
     */
    public function getCodigoTransaccion()
    {
        return $this->codigoTransaccion;
    }

    /**
     * Set numeroAuditoria.
     *
     * @param string $numeroAuditoria
     *
     * @return InterfazImed
     */
    public function setNumeroAuditoria($numeroAuditoria)
    {
        $this->numeroAuditoria = $numeroAuditoria;

        return $this;
    }

    /**
     * Get numeroAuditoria.
     *
     * @return string
     */
    public function getNumeroAuditoria()
    {
        return $this->numeroAuditoria;
    }

    /**
     * Set gloError.
     *
     * @param string|null $gloError
     *
     * @return InterfazImed
     */
    public function setGloError($gloError = null)
    {
        $this->gloError = $gloError;

        return $this;
    }

    /**
     * Get gloError.
     *
     * @return string|null
     */
    public function getGloError()
    {
        return $this->gloError;
    }

    /**
     * @return \PagoCuenta
     */
    public function getIdPagoCuenta()
    {
        return $this->idPagoCuenta;
    }

    /**
     * @param \PagoCuenta $idPagoCuenta
     */
    public function setIdPagoCuenta($idPagoCuenta)
    {
        $this->idPagoCuenta = $idPagoCuenta;
    }

}
