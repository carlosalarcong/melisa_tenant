<?php

namespace Rebsol\HermesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Articulo
 *
 * @ORM\Table(name="articulo")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Rebsol\HermesBundle\Repository\ArticuloRepository")
 */
class Articulo
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
     * @ORM\Column(name="CODIGO", type="string", length=255, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="NOMBRE", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="DESCRIPCION", type="string", length=2000, nullable=true)
     */
    private $descripcion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_AGRUPACION_CUENTA", type="string", length=255, nullable=true)
     */
    private $codigoAgrupacionCuenta;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_CONSIGNACION", type="boolean", nullable=false)
     */
    private $esConsignacion = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_CONTROLADO", type="boolean", nullable=false)
     */
    private $esControlado = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_CREACION", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="FECHA_MODIFICACION", type="datetime", nullable=true)
     */
    private $fechaModificacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="TIENE_FECHA_VENCIMIENTO_LOTE", type="boolean", nullable=false)
     */
    private $tieneFechaVencimientoLote = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_FOTO", type="string", length=255, nullable=true)
     */
    private $nombreFoto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="STOCK_MINIMO", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $stockMinimo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="STOCK_CRITICO", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $stockCritico;

    /**
     * @var string|null
     *
     * @ORM\Column(name="STOCK_OPTIMO", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $stockOptimo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="STOCK_MAXIMO", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $stockMaximo;

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_CRITICO", type="boolean", nullable=false)
     */
    private $esCritico = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_GENERICO", type="boolean", nullable=false)
     */
    private $esGenerico = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_REESTERILIZABLE", type="boolean", nullable=false)
     */
    private $esReesterilizable = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_VENTA", type="boolean", nullable=false)
     */
    private $esVenta = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_FACTURABLE", type="boolean", nullable=false)
     */
    private $esFacturable = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="ES_REBAJA_BOTIQUIN", type="boolean", nullable=false)
     */
    private $esRebajaBotiquin = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_GENERICO", type="string", length=100, nullable=true)
     */
    private $nombreGenerico;

    /**
     * @var string|null
     *
     * @ORM\Column(name="NOMBRE_ABREVIADO", type="string", length=100, nullable=true)
     */
    private $nombreAbreviado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="MARGEN", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $margen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_ICON", type="string", length=255, nullable=true)
     */
    private $codigoIcon;

    /**
     * @var string|null
     *
     * @ORM\Column(name="CODIGO_CENABAST", type="string", length=255, nullable=true)
     */
    private $codigoCenabast;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_CREACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioCreacion;

    /**
     * @var \UsuariosRebsol
     *
     * @ORM\ManyToOne(targetEntity="UsuariosRebsol")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_USUARIO_MODIFICACION", referencedColumnName="ID")
     * })
     */
    private $idUsuarioModificacion;

    /**
     * @var \ItemPresupuestario
     *
     * @ORM\ManyToOne(targetEntity="ItemPresupuestario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ITEM_PRESUPUESTARIO", referencedColumnName="ID")
     * })
     */
    private $idItemPresupuestario;

    /**
     * @var \TipoArticulo
     *
     * @ORM\ManyToOne(targetEntity="TipoArticulo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_TIPO_ARTICULO", referencedColumnName="ID")
     * })
     */
    private $idTipoArticulo;

    /**
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_ESTADO", referencedColumnName="ID")
     * })
     */
    private $idEstado;

    /**
     * @var \UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_UNIDAD_MEDIDA", referencedColumnName="ID")
     * })
     */
    private $idUnidadMedida;

    /**
     * @var \SubEmpresa
     *
     * @ORM\ManyToOne(targetEntity="SubEmpresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUB_EMPRESA_FACTURADORA", referencedColumnName="ID")
     * })
     */
    private $idSubEmpresaFacturadora;

    /**
     * @var \SubEmpresa
     *
     * @ORM\ManyToOne(targetEntity="SubEmpresa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_SUB_EMPRESA", referencedColumnName="ID")
     * })
     */
    private $idSubEmpresa;



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
     * Set codigo.
     *
     * @param string $codigo
     *
     * @return Articulo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Articulo
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return Articulo
     */
    public function setDescripcion($descripcion = null)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string|null
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set codigoAgrupacionCuenta.
     *
     * @param string|null $codigoAgrupacionCuenta
     *
     * @return Articulo
     */
    public function setCodigoAgrupacionCuenta($codigoAgrupacionCuenta = null)
    {
        $this->codigoAgrupacionCuenta = $codigoAgrupacionCuenta;

        return $this;
    }

    /**
     * Get codigoAgrupacionCuenta.
     *
     * @return string|null
     */
    public function getCodigoAgrupacionCuenta()
    {
        return $this->codigoAgrupacionCuenta;
    }

    /**
     * Set esConsignacion.
     *
     * @param bool $esConsignacion
     *
     * @return Articulo
     */
    public function setEsConsignacion($esConsignacion)
    {
        $this->esConsignacion = $esConsignacion;

        return $this;
    }

    /**
     * Get esConsignacion.
     *
     * @return bool
     */
    public function getEsConsignacion()
    {
        return $this->esConsignacion;
    }

    /**
     * Set esControlado.
     *
     * @param bool $esControlado
     *
     * @return Articulo
     */
    public function setEsControlado($esControlado)
    {
        $this->esControlado = $esControlado;

        return $this;
    }

    /**
     * Get esControlado.
     *
     * @return bool
     */
    public function getEsControlado()
    {
        return $this->esControlado;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Articulo
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
     * Set fechaModificacion.
     *
     * @param \DateTime|null $fechaModificacion
     *
     * @return Articulo
     */
    public function setFechaModificacion($fechaModificacion = null)
    {
        $this->fechaModificacion = $fechaModificacion;

        return $this;
    }

    /**
     * Get fechaModificacion.
     *
     * @return \DateTime|null
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

    /**
     * Set tieneFechaVencimientoLote.
     *
     * @param bool $tieneFechaVencimientoLote
     *
     * @return Articulo
     */
    public function setTieneFechaVencimientoLote($tieneFechaVencimientoLote)
    {
        $this->tieneFechaVencimientoLote = $tieneFechaVencimientoLote;

        return $this;
    }

    /**
     * Get tieneFechaVencimientoLote.
     *
     * @return bool
     */
    public function getTieneFechaVencimientoLote()
    {
        return $this->tieneFechaVencimientoLote;
    }

    /**
     * Set nombreFoto.
     *
     * @param string|null $nombreFoto
     *
     * @return Articulo
     */
    public function setNombreFoto($nombreFoto = null)
    {
        $this->nombreFoto = $nombreFoto;

        return $this;
    }

    /**
     * Get nombreFoto.
     *
     * @return string|null
     */
    public function getNombreFoto()
    {
        return $this->nombreFoto;
    }

    /**
     * Set stockMinimo.
     *
     * @param string|null $stockMinimo
     *
     * @return Articulo
     */
    public function setStockMinimo($stockMinimo = null)
    {
        $this->stockMinimo = $stockMinimo;

        return $this;
    }

    /**
     * Get stockMinimo.
     *
     * @return string|null
     */
    public function getStockMinimo()
    {
        return $this->stockMinimo;
    }

    /**
     * Set stockCritico.
     *
     * @param string|null $stockCritico
     *
     * @return Articulo
     */
    public function setStockCritico($stockCritico = null)
    {
        $this->stockCritico = $stockCritico;

        return $this;
    }

    /**
     * Get stockCritico.
     *
     * @return string|null
     */
    public function getStockCritico()
    {
        return $this->stockCritico;
    }

    /**
     * Set stockOptimo.
     *
     * @param string|null $stockOptimo
     *
     * @return Articulo
     */
    public function setStockOptimo($stockOptimo = null)
    {
        $this->stockOptimo = $stockOptimo;

        return $this;
    }

    /**
     * Get stockOptimo.
     *
     * @return string|null
     */
    public function getStockOptimo()
    {
        return $this->stockOptimo;
    }

    /**
     * Set stockMaximo.
     *
     * @param string|null $stockMaximo
     *
     * @return Articulo
     */
    public function setStockMaximo($stockMaximo = null)
    {
        $this->stockMaximo = $stockMaximo;

        return $this;
    }

    /**
     * Get stockMaximo.
     *
     * @return string|null
     */
    public function getStockMaximo()
    {
        return $this->stockMaximo;
    }

    /**
     * Set esCritico.
     *
     * @param bool $esCritico
     *
     * @return Articulo
     */
    public function setEsCritico($esCritico)
    {
        $this->esCritico = $esCritico;

        return $this;
    }

    /**
     * Get esCritico.
     *
     * @return bool
     */
    public function getEsCritico()
    {
        return $this->esCritico;
    }

    /**
     * Set esGenerico.
     *
     * @param bool $esGenerico
     *
     * @return Articulo
     */
    public function setEsGenerico($esGenerico)
    {
        $this->esGenerico = $esGenerico;

        return $this;
    }

    /**
     * Get esGenerico.
     *
     * @return bool
     */
    public function getEsGenerico()
    {
        return $this->esGenerico;
    }

    /**
     * Set esReesterilizable.
     *
     * @param bool $esReesterilizable
     *
     * @return Articulo
     */
    public function setEsReesterilizable($esReesterilizable)
    {
        $this->esReesterilizable = $esReesterilizable;

        return $this;
    }

    /**
     * Get esReesterilizable.
     *
     * @return bool
     */
    public function getEsReesterilizable()
    {
        return $this->esReesterilizable;
    }

    /**
     * Set esVenta.
     *
     * @param bool $esVenta
     *
     * @return Articulo
     */
    public function setEsVenta($esVenta)
    {
        $this->esVenta = $esVenta;

        return $this;
    }

    /**
     * Get esVenta.
     *
     * @return bool
     */
    public function getEsVenta()
    {
        return $this->esVenta;
    }

    /**
     * Set esFacturable.
     *
     * @param bool $esFacturable
     *
     * @return Articulo
     */
    public function setEsFacturable($esFacturable)
    {
        $this->esFacturable = $esFacturable;

        return $this;
    }

    /**
     * Get esFacturable.
     *
     * @return bool
     */
    public function getEsFacturable()
    {
        return $this->esFacturable;
    }

    /**
     * Set esRebajaBotiquin.
     *
     * @param bool $esRebajaBotiquin
     *
     * @return Articulo
     */
    public function setEsRebajaBotiquin($esRebajaBotiquin)
    {
        $this->esRebajaBotiquin = $esRebajaBotiquin;

        return $this;
    }

    /**
     * Get esRebajaBotiquin.
     *
     * @return bool
     */
    public function getEsRebajaBotiquin()
    {
        return $this->esRebajaBotiquin;
    }

    /**
     * Set nombreGenerico.
     *
     * @param string|null $nombreGenerico
     *
     * @return Articulo
     */
    public function setNombreGenerico($nombreGenerico = null)
    {
        $this->nombreGenerico = $nombreGenerico;

        return $this;
    }

    /**
     * Get nombreGenerico.
     *
     * @return string|null
     */
    public function getNombreGenerico()
    {
        return $this->nombreGenerico;
    }

    /**
     * Set nombreAbreviado.
     *
     * @param string|null $nombreAbreviado
     *
     * @return Articulo
     */
    public function setNombreAbreviado($nombreAbreviado = null)
    {
        $this->nombreAbreviado = $nombreAbreviado;

        return $this;
    }

    /**
     * Get nombreAbreviado.
     *
     * @return string|null
     */
    public function getNombreAbreviado()
    {
        return $this->nombreAbreviado;
    }

    /**
     * Set margen.
     *
     * @param string|null $margen
     *
     * @return Articulo
     */
    public function setMargen($margen = null)
    {
        $this->margen = $margen;

        return $this;
    }

    /**
     * Get margen.
     *
     * @return string|null
     */
    public function getMargen()
    {
        return $this->margen;
    }

    /**
     * Set codigoIcon.
     *
     * @param string|null $codigoIcon
     *
     * @return Articulo
     */
    public function setCodigoIcon($codigoIcon = null)
    {
        $this->codigoIcon = $codigoIcon;

        return $this;
    }

    /**
     * Get codigoIcon.
     *
     * @return string|null
     */
    public function getCodigoIcon()
    {
        return $this->codigoIcon;
    }

    /**
     * Set codigoCenabast.
     *
     * @param string|null $codigoCenabast
     *
     * @return Articulo
     */
    public function setCodigoCenabast($codigoCenabast = null)
    {
        $this->codigoCenabast = $codigoCenabast;

        return $this;
    }

    /**
     * Get codigoCenabast.
     *
     * @return string|null
     */
    public function getCodigoCenabast()
    {
        return $this->codigoCenabast;
    }

    /**
     * Set idSubEmpresa.
     *
     * @param \Rebsol\HermesBundle\Entity\SubEmpresa $idSubEmpresa
     *
     * @return Articulo
     */
    public function setIdSubEmpresa(\Rebsol\HermesBundle\Entity\SubEmpresa $idSubEmpresa)
    {
        $this->idSubEmpresa = $idSubEmpresa;

        return $this;
    }

    /**
     * Get idSubEmpresa.
     *
     * @return \Rebsol\HermesBundle\Entity\SubEmpresa
     */
    public function getIdSubEmpresa()
    {
        return $this->idSubEmpresa;
    }

    /**
     * Set idTipoArticulo.
     *
     * @param \Rebsol\HermesBundle\Entity\TipoArticulo $idTipoArticulo
     *
     * @return Articulo
     */
    public function setIdTipoArticulo(\Rebsol\HermesBundle\Entity\TipoArticulo $idTipoArticulo)
    {
        $this->idTipoArticulo = $idTipoArticulo;

        return $this;
    }

    /**
     * Get idTipoArticulo.
     *
     * @return \Rebsol\HermesBundle\Entity\TipoArticulo
     */
    public function getIdTipoArticulo()
    {
        return $this->idTipoArticulo;
    }

    /**
     * Set idEstado.
     *
     * @param \Rebsol\HermesBundle\Entity\Estado $idEstado
     *
     * @return Articulo
     */
    public function setIdEstado(\Rebsol\HermesBundle\Entity\Estado $idEstado)
    {
        $this->idEstado = $idEstado;

        return $this;
    }

    /**
     * Get idEstado.
     *
     * @return \Rebsol\HermesBundle\Entity\Estado
     */
    public function getIdEstado()
    {
        return $this->idEstado;
    }

    /**
     * Set idUsuarioCreacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion
     *
     * @return Articulo
     */
    public function setIdUsuarioCreacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioCreacion)
    {
        $this->idUsuarioCreacion = $idUsuarioCreacion;

        return $this;
    }

    /**
     * Get idUsuarioCreacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol
     */
    public function getIdUsuarioCreacion()
    {
        return $this->idUsuarioCreacion;
    }

    /**
     * Set idUsuarioModificacion.
     *
     * @param \Rebsol\HermesBundle\Entity\UsuariosRebsol|null $idUsuarioModificacion
     *
     * @return Articulo
     */
    public function setIdUsuarioModificacion(\Rebsol\HermesBundle\Entity\UsuariosRebsol $idUsuarioModificacion = null)
    {
        $this->idUsuarioModificacion = $idUsuarioModificacion;

        return $this;
    }

    /**
     * Get idUsuarioModificacion.
     *
     * @return \Rebsol\HermesBundle\Entity\UsuariosRebsol|null
     */
    public function getIdUsuarioModificacion()
    {
        return $this->idUsuarioModificacion;
    }

    /**
     * Set idUnidadMedida.
     *
     * @param \Rebsol\HermesBundle\Entity\UnidadMedida $idUnidadMedida
     *
     * @return Articulo
     */
    public function setIdUnidadMedida(\Rebsol\HermesBundle\Entity\UnidadMedida $idUnidadMedida)
    {
        $this->idUnidadMedida = $idUnidadMedida;

        return $this;
    }

    /**
     * Get idUnidadMedida.
     *
     * @return \Rebsol\HermesBundle\Entity\UnidadMedida
     */
    public function getIdUnidadMedida()
    {
        return $this->idUnidadMedida;
    }

    /**
     * Set idItemPresupuestario.
     *
     * @param \Rebsol\HermesBundle\Entity\ItemPresupuestario|null $idItemPresupuestario
     *
     * @return Articulo
     */
    public function setIdItemPresupuestario(\Rebsol\HermesBundle\Entity\ItemPresupuestario $idItemPresupuestario = null)
    {
        $this->idItemPresupuestario = $idItemPresupuestario;

        return $this;
    }

    /**
     * Get idItemPresupuestario.
     *
     * @return \Rebsol\HermesBundle\Entity\ItemPresupuestario|null
     */
    public function getIdItemPresupuestario()
    {
        return $this->idItemPresupuestario;
    }

    /**
     * Set idSubEmpresaFacturadora.
     *
     * @param \Rebsol\HermesBundle\Entity\SubEmpresa|null $idSubEmpresaFacturadora
     *
     * @return Articulo
     */
    public function setIdSubEmpresaFacturadora(\Rebsol\HermesBundle\Entity\SubEmpresa $idSubEmpresaFacturadora = null)
    {
        $this->idSubEmpresaFacturadora = $idSubEmpresaFacturadora;

        return $this;
    }

    /**
     * Get idSubEmpresaFacturadora.
     *
     * @return \Rebsol\HermesBundle\Entity\SubEmpresa|null
     */
    public function getIdSubEmpresaFacturadora()
    {
        return $this->idSubEmpresaFacturadora;
    }
}
