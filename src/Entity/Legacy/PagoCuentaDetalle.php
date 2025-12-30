<?php

namespace App\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * PagoCuentaDetalle
 *
 * @ORM\Table(name="pago_cuenta_detalle")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\PagoCuentaDetalleRepository")
 */
class PagoCuentaDetalle
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
     * @var string|null
     *
     * @ORM\Column(name="URL_DTE", type="text", length=0, nullable=true)
     */
    private $urlDte;

    /**
     * @var string|null
     *
     * @ORM\Column(name="URL_PROD_DTE", type="text", length=0, nullable=true)
     */
    private $urlProdDte;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ENVIADO_DTE", type="boolean", nullable=true)
     */
    private $enviadoDte;

    /**
     * @var array|null
     *
     * @ORM\Column(name="DETALLE_DTE", type="json_array", length=0, nullable=true)
     */
    private $detalleDte;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DATA_PENDIENTE", type="text", length=0, nullable=true)
     */
    private $dataPendiente;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CONSULTA_URL_PROD", type="boolean", length=0, nullable=true)
     */
    private $consultaUrlProd;

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
     * Set urlDte.
     *
     * @param string|null $urlDte
     *
     * @return PagoCuentaDetalle
     */
    public function setUrlDte($urlDte = null)
    {
        $this->urlDte = $urlDte;

        return $this;
    }

    /**
     * Get urlDte.
     *
     * @return string|null
     */
    public function getUrlDte()
    {
        return $this->urlDte;
    }

    /**
     * @return string|null
     */
    public function getUrlProdDte()
    {
        return $this->urlProdDte;
    }

    /**
     * @param string|null $urlProdDte
     */
    public function setUrlProdDte($urlProdDte)
    {
        $this->urlProdDte = $urlProdDte;
    }



    /**
     * Set enviadoDte.
     *
     * @param bool|null $enviadoDte
     *
     * @return PagoCuentaDetalle
     */
    public function setEnviadoDte($enviadoDte = null)
    {
        $this->enviadoDte = $enviadoDte;

        return $this;
    }

    /**
     * Get enviadoDte.
     *
     * @return bool|null
     */
    public function getEnviadoDte()
    {
        return $this->enviadoDte;
    }

    /**
     * Set detalleDte.
     *
     * @param array|null $detalleDte
     *
     * @return PagoCuentaDetalle
     */
    public function setDetalleDte($detalleDte = null)
    {
        $this->detalleDte = $detalleDte;

        return $this;
    }

    /**
     * Get detalleDte.
     *
     * @return array|null
     */
    public function getDetalleDte()
    {
        return $this->detalleDte;
    }

    /**
     * Set dataPendiente.
     *
     * @param string|null $dataPendiente
     *
     * @return PagoCuentaDetalle
     */
    public function setDataPendiente($dataPendiente = null)
    {
        $this->dataPendiente = $dataPendiente;

        return $this;
    }

    /**
     * Get dataPendiente.
     *
     * @return string|null
     */
    public function getDataPendiente()
    {
        return $this->dataPendiente;
    }

    /**
     * @return string|null
     */
    public function getConsultaUrlProd()
    {
        return $this->consultaUrlProd;
    }

    /**
     * @param string|null $consultaUrlProd
     */
    public function setConsultaUrlProd($consultaUrlProd)
    {
        $this->consultaUrlProd = $consultaUrlProd;
    }



    /**
     * Set idPagoCuenta.
     *
     * @param \App\Entity\Legacy\Legacy\PagoCuenta|null $idPagoCuenta
     *
     * @return PagoCuentaDetalle
     */
    public function setIdPagoCuenta(\App\Entity\Legacy\Legacy\PagoCuenta $idPagoCuenta = null)
    {
        $this->idPagoCuenta = $idPagoCuenta;

        return $this;
    }

    /**
     * Get idPagoCuenta.
     *
     * @return \App\Entity\Legacy\Legacy\PagoCuenta|null
     */
    public function getIdPagoCuenta()
    {
        return $this->idPagoCuenta;
    }

}
