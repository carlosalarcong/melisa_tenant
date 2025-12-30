<?php

namespace App\Controller\Caja;

use Rebsol\AdmisionBundle\Form\Type\IdentificacionType;
use App\Form\Recaudacion\Pago\AdjuntoType;
use App\Form\Recaudacion\Pago\BusquedaAvanzadaDirectorioPacienteType;
use App\Form\Recaudacion\Pago\MediosPagoType;
use App\Form\Recaudacion\Pago\PagoType;
use App\Form\Recaudacion\Pago\PrestacionType;
use App\Entity\Legacy\PersonaDomicilio;
use App\Controller\Legacy\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;


class RecaudacionController extends DefaultController
{
    public function __construct(
        protected RequestStack $requestStack
    ) {
    }


    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $vieneLogin = $this->get('session')->getFlashBag()->get('vieneLoginParaCaja');
        $idUser = $this->getUser();
        $esCajero = 'false';
        $esUbicacionCajero = 'false';

        $oUbicacionCajero = $em->getRepository('App\Entity\Legacy\RelUbicacionCajero')->findOneBy(array(
                'idUsuario' => $idUser,
                'idEstado' => $this->getParameter('Estado.activo')
            )
        );

        if ($oUbicacionCajero !== null) {
            $esUbicacionCajero = 'true';
        }

        if (count($vieneLogin) == 1) {
            $esCajero = $this->verificarUsuarioCajero();
        }

        return $this->render('RecaudacionBundle::index.html.twig', array(
            'esCajero' => $esCajero,
            'esUbicacionCajero' => $esUbicacionCajero,
        ));
    }

    public function ObtenerLogoEmpresaLogin()
    {
        $oUsuarioRebsol = $this->getUser();
        $oPersona = $oUsuarioRebsol->getIdPersona();
        $oEmpresa = $oPersona->getIdEmpresa();
        $srtLogo = 'empresa_' . $oEmpresa->getId() . '' . $oEmpresa->getPathLogoEmpresa() . '';
        return $srtLogo;
    }

    private function obtenerSucursalPorUsuario()
    {

        $em = $this->getDoctrine()->getManager();
        $oUsuarioRebsol = $this->getUser();
        $rRepository = $this->getDoctrine()->getRepository('App\Entity\Legacy\UsuariosRebsol');
        $entities = $rRepository->obtenerSucursalPorUsuario($oUsuarioRebsol->getId());
        $arrReturn = count($entities);

        return $arrReturn;
    }

    private function verificarUsuarioCajero()
    {

        $em = $this->getDoctrine()->getManager();
        $oUsuarioRebsol = $this->getUser();

        $oCajero = $this->getDoctrine()->getRepository('App\Entity\Legacy\RelUbicacionCajero')->findBy(array(
                'idUsuario' => $oUsuarioRebsol->getId(),
                'idEstado' => $this->getParameter('EstadoRelUbicacionCajero.Activo')
            )
        );

        if (!empty($oCajero)) {
            $esCajero = 'true';
        } else {
            $esCajero = 'false';
        }

        return $esCajero;
    }

    /**
     * @return Empresa
     * Descripción: ObtenerEmpresaLogin() Obtiene el Objeto Empresa desde la sesión
     */
    public function obtenerEmpresaLogin()
    {

        $oUsuarioRebsol = $this->getUser();
        $oPersona = $oUsuarioRebsol->getIdPersona();
        $oEmpresa = $oPersona->getIdEmpresa();
        return $oEmpresa;

    }

    /**
     * @param String $srtNombreEntidad .
     * @param Objeto $oEmpresa .
     * @return |RedirectResponse()
     * Descripción: ResetearValorDefault() Resetea a 0 el valor_default de las demás entidades.
     */
    public function buscarValorDefault($oEmpresa, $strNombreEntidad)
    {

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository("App\\Entity\\Legacy\\" . $strNombreEntidad)->findBy(array("idEmpresa" => $oEmpresa->getId()));

        foreach ($entities as $entity) {
            if ($entity->getValorDefault() == 1) {
                return $entity;
            }
        }

        return false;

    }

    /**
     * @param Request $request .
     * @param String $router Módulo desde el que se está haciendo la petición.
     * @return |RedirectResponse()
     * Descripción: ValidadPeticionPost() Valida que la petición se esté realizando por POST.
     */
    public function validadPeticionPost(Request $request, $router)
    {
        if ($request->getMethod() !== 'POST') {

            $urlGenerate = 'Caja_' . $router . '';
            $urlResponse = $this->generateUrl($urlGenerate);
            $RedirectResponse = new RedirectResponse($urlResponse);
            $RedirectResponse->send();

        }
    }

    /**
     * @param Request $request .
     * @param String $router Módulo desde el que se está haciendo la petición.
     * @return |RedirectResponse()
     * Descripción: ValidadPeticionAjax() Valida que la petición se esté realizando por AJAX.
     */
    public function ValidadPeticionAjax(Request $request, $router)
    {
        if (!$request->isXmlHttpRequest()) {
            $urlGenerate = 'Caja_' . $router . '';
            $urlResponse = $this->generateUrl($urlGenerate);
            $RedirectResponse = new RedirectResponse($urlResponse);
            $RedirectResponse->send();
        }
    }


    /**
     * Protecteds Repositorio
     * Descripción: Funciones Protegidas para utilizarlas solo en las subClases de Caja, evitando generar siempre un rRepositorio en caso de usar varios
     * repositorios en un mismo controlador. solo se llama con un estarndar "r"+NombreEntidad.
     */

    protected function rFormaPago()
    {
        return $this->getDoctrine()->getRepository("App\Entity\Legacy\FormaPago");
    }


    protected function rPaciente()
    {
        return $this->getDoctrine()->getRepository("App\Entity\Legacy\Paciente");
    }

    protected function rPnatural()
    {
        return $this->getDoctrine()->getRepository("App\Entity\Legacy\Pnatural");
    }

    protected function rPagoCuenta()
    {
        return $this->getDoctrine()->getRepository("App\Entity\Legacy\PagoCuenta");
    }

    protected function rCaja()
    {
        return $this->getDoctrine()->getRepository("App\Entity\Legacy\Caja");
    }

    protected function rDetalleCaja()
    {
        return $this->getDoctrine()->getRepository("App\Entity\Legacy\DetalleCaja");
    }

    protected function rDocumentoPago()
    {
        return $this->getDoctrine()->getRepository("App\Entity\Legacy\DocumentoPago");
    }

    protected function rDiferencia()
    {
        return $this->getDoctrine()->getRepository("App\Entity\Legacy\Diferencia");
    }

    protected function rParametro()
    {
        return $this->getDoctrine()->getRepository("App\Entity\Legacy\Parametro");
    }

    protected function rUsuariosRebsol()
    {
        return $this->getDoctrine()->getRepository("App\Entity\Legacy\UsuariosRebsol");
    }

    /**
     * Protecteds AJAX
     * Descripción: Funciones Protegidas para estandarizar el llamado de una variable o array desde un Ajax.
     */

    protected function request($a)
    {
        return $this->requestStack->getCurrentRequest()->get($a);
    }

    protected function ajax($a)
    {
        return $this->requestStack->getCurrentRequest()->query->get($a);
    }

    /**
     * Protecteds Session
     * Descripción: Funcion para mayor facilidad de tratacion de Variables de session, para obtenerlas, declararlas y eliminarlas.
     */

    protected function getSession($n)
    {
        return $this->requestStack->getSession()->get($n);
    }

    protected function setSession($n, $v)
    {
        return $this->requestStack->getSession()->set($n, $v);
    }

    protected function killSession($n)
    {
        return $this->requestStack->getSession()->remove($n);
    }

    /**
     * Utilización de Servicios
     */
    protected function getRutUser($id)
    {
        return $this->get('libreria_funciones')->getRutUser($id);
    }

    protected function SubEmpresaPorPrestacionTalonario($a, $b)
    {
        return $this->get('Caja_Valida')->SubEmpresa($a, $b);
    }

    protected function ObtenerHonarioGarantia($a, $b, $c, $d, $e, $f, $g, $h, $i, $j)
    {
        return $this->get('Caja_Valida')->ObtenerHonarioGarantia($a, $b, $c, $d, $e, $f, $g, $h, $i, $j);
    }


    protected function getRutPnatural($id)
    {
        $x = $this->get('libreria_funciones')->getRutPnatural($id);

        if (strlen($x) == 10) {
            $formating = '00' . $x;
        } else if (strlen($x) == 9) {
            $formating = '000' . $x;
        } else if (strlen($x) == 8) {
            $formating = '0000' . $x;
        } else if (strlen($x) == 7) {
            $formating = '00000' . $x;
        } else if (strlen($x) == 6) {
            $formating = '000000' . $x;
        }
        return $formating;
    }

    protected function getUserFromPnatural($id)
    {
        $x = $this->get('libreria_funciones')->getUserFromPnatural($id);
        if ($x) {
            if (strlen($this->getRutUser($x)) == 9) {
                $z = '000' . $this->getRutUser($x);
            }
            if (strlen($this->getRutUser($x)) == 10) {
                $z = '00' . $this->getRutUser($x);
            }
        } else {
            $z = '0000000000-0';
        }
        return $z;
    }

    protected function getCompleteNameFromIdPnatural($id)
    {
        return ($id) ? $this->get('libreria_funciones')->getCompleteNameFromIdPnatural($id) : null;
    }

    /**
     * Functiones varias
     */
    protected function count_digit($number)
    {
        return strlen($number);
    }

    /**
     * Protecteds Estado
     * Descripción: Funcion consultar de manera mas agil por estados comunes a utilizar referentes de Caja.
     */

    protected function estado($var)
    {

        $em = $this->getDoctrine()->getManager();

        switch ($var) {
            case "EstadoPilaActiva":
                return $em->getRepository('App\Entity\Legacy\EstadoPila')->find($this->getParameter('EstadoPila.activo'));
                break;
            case "EstadoPilaInaciva":
                return $em->getRepository('App\Entity\Legacy\EstadoPila')->find($this->getParameter('EstadoPila.inactivo'));
                break;
            case "EstadoReaperturaCerrada":
                return $em->getRepository('App\Entity\Legacy\EstadoReapertura')->find($this->getParameter('EstadoReapertura.cerrada'));
                break;
            case "EstadoReaperturaAbierta":
                return $em->getRepository('App\Entity\Legacy\EstadoReapertura')->find($this->getParameter('EstadoReapertura.abierta'));
                break;
            case "EstadoActivo":
                return $em->getRepository('App\Entity\Legacy\Estado')->find($this->getParameter('Estado.activo'));
                break;
            case "EstadoInc":
                return $em->getRepository('App\Entity\Legacy\Estado')->find($this->getParameter('Estado.inactivo'));
                break;
            case "EstadoPagoActiva":
                return $em->getRepository('App\Entity\Legacy\EstadoPago')->find($this->getParameter('EstadoPago.pagadoNormal'));
                break;
            case "EstadoPagoAnulada":
                return $em->getRepository('App\Entity\Legacy\EstadoPago')->find($this->getParameter('EstadoPago.anulado'));
                break;
            case "EstadoPagoGarantia":
                return $em->getRepository('App\Entity\Legacy\EstadoPago')->find($this->getParameter('EstadoPago.garantia'));
                break;
            case "EstadoPagoRegularizada":
                return $em->getRepository('App\Entity\Legacy\EstadoPago')->find($this->getParameter('EstadoPago.garantiaRegularizada'));
                break;
            case "EstadoPagoPendientePago":
                return $em->getRepository('App\Entity\Legacy\EstadoPago')->find($this->getParameter('EstadoPago.pendientePago'));
                break;
            case "EstadoCuentaCerradaPagada":
                return $em->getRepository('App\Entity\Legacy\EstadoCuenta')->find($this->getParameter('EstadoCuenta.cerradaPagada'));
                break;
            case "EstadoCuentaAnulada":
                return $em->getRepository('App\Entity\Legacy\EstadoCuenta')->find($this->getParameter('EstadoCuenta.anulado'));
                break;
            case "EstadoAbiertaPendientePago":
                return $em->getRepository('App\Entity\Legacy\EstadoCuenta')->find($this->getParameter('EstadoCuenta.abiertaPendientePago'));
                break;
            case "EstadoAbiertaPagadaTotal":
                return $em->getRepository('App\Entity\Legacy\EstadoCuenta')->find($this->getParameter('EstadoCuenta.abiertaPagadaTotal'));
                break;
            case "EstadoCerradaPendientePago":
                return $em->getRepository('App\Entity\Legacy\EstadoCuenta')->find($this->getParameter('EstadoCuenta.cerradaPendientePago'));
                break;
            case "EstadoCerradaRevisionInterna":
                return $em->getRepository('App\Entity\Legacy\EstadoCuenta')->find($this->getParameter('EstadoCuenta.cerradaRevisionInterna'));
                break;
            case "EstadoCerradaPagadaTotal":
                return $em->getRepository('App\Entity\Legacy\EstadoCuenta')->find($this->getParameter('EstadoCuenta.cerradaPagadaTotal'));
                break;
            case "EstadoBoletaActiva":
                return $em->getRepository('App\Entity\Legacy\EstadoDetalleTalonario')->find($this->getParameter('EstadoDetalleTalonario.emitidas'));
                break;
            case "EstadoBoletaAnulada":
                return $em->getRepository('App\Entity\Legacy\EstadoDetalleTalonario')->find($this->getParameter('EstadoDetalleTalonario.anulada'));
                break;
            case "EstadoAccionClinicaSolicitado":
                return $em->getRepository('App\Entity\Legacy\EstadoAccionClinica')->find($this->getParameter('EstadoAccionClinica.solicitado'));
                break;
            case "EstadoTratamientoFinalizado":
                return $em->getRepository('App\Entity\Legacy\EstadoTratamiento')->find($this->getParameter('EstadoTratamiento.Finalizado'));
                break;
            case "EstadoTratamientoEnProceso":
                return $em->getRepository('App\Entity\Legacy\EstadoTratamiento')->find($this->getParameter('EstadoTratamiento.EnProceso'));
                break;
            case "EstadoTratamientoAnulado":
                return $em->getRepository('App\Entity\Legacy\EstadoTratamiento')->find($this->getParameter('EstadoTratamiento.Anulado'));
                break;
            case "EstadoApi":
                return $this->obtenerApiModulo($this->getParameter("modulo_caja"));
                break;
            case "DiferenciacajeroPideAutorizacion":
                return $em->getRepository('App\Entity\Legacy\EstadoDiferencia')->find($this->getParameter('EstadoDiferencia.cajeroPideAutorizacion'));
                break;
            case "Diferenciaautorizada":
                return $em->getRepository('App\Entity\Legacy\EstadoDiferencia')->find($this->getParameter('EstadoDiferencia.autorizada'));
                break;
            case "DiferenciadescuentoNoRequiereAutorizacion":
                return $em->getRepository('App\Entity\Legacy\EstadoDiferencia')->find($this->getParameter('EstadoDiferencia.descuentoNoRequiereAutorizacion'));
                break;
            case "DiferenciacajeroCancelaSolicitud":
                return $em->getRepository('App\Entity\Legacy\EstadoDiferencia')->find($this->getParameter('EstadoDiferencia.cajeroCancelaSolicitud'));
                break;
            case "Diferenciarechazada":
                return $em->getRepository('App\Entity\Legacy\EstadoDiferencia')->find($this->getParameter('EstadoDiferencia.rechazada'));
                break;
            default:
                return null;
        }
    }

    /**
     * Protecteds Estado
     */
    protected function parametro($var)
    {

        switch ($var) {
            case "Estado.activo":
                return $this->getParameter('Estado.activo');
                break;
            case "Estado.inactivo":
                return $this->getParameter('Estado.inactivo');
                break;
            case "EstadoUsuarios.activo":
                return $this->getParameter('EstadoUsuarios.activo');
                break;
            case "EstadoUsuarios.inactivo":
                return $this->getParameter('EstadoUsuarios.inactivo');
                break;
            case "EstadoEspecialidadMedica.activo":
                return $this->getParameter('EstadoEspecialidadMedica.activo');
                break;
            case "EstadoEspecialidadMedica.inactivo":
                return $this->getParameter('EstadoEspecialidadMedica.inactivo');
                break;
            case "EstadoRelUsuarioServicio.Activo":
                return $this->getParameter('EstadoRelUsuarioServicio.Activo');
                break;
            case "EstadoRelUsuarioServicio.Inactivo":
                return $this->getParameter('EstadoRelUsuarioServicio.Inactivo');
                break;
            case "EstadoRelUsuarioServicio.Bloqueado":
                return $this->getParameter('EstadoRelUsuarioServicio.Bloqueado');
                break;
            case "EstadoPago.garantia":
                return $this->getParameter('EstadoPago.garantia');
                break;
            case "EstadoPago.pagadoNormal":
                return $this->getParameter('EstadoPago.pagadoNormal');
                break;
            case "EstadoPila.inactivo":
                return $this->getParameter('EstadoPila.inactivo');
                break;
            case "FormaPagoTipo.Efectivo":
                return $this->getParameter('FormaPagoTipo.Efectivo');
                break;
            case "FormaPagoTipo.Gratuidad":
                return $this->getParameter('FormaPagoTipo.Gratuidad');
                break;
            case "FormaPagoTipo.BonoElectronico":
                return $this->getParameter('FormaPagoTipo.BonoElectronico');
                break;
            case "FormaPagoTipo.TarjetaCredito":
                return $this->getParameter('FormaPagoTipo.TarjetaCredito');
                break;
            case "FormaPagoTipo.BonoManual":
                return $this->getParameter('FormaPagoTipo.BonoManual');
                break;
            case "FormaPagoTipo.TarjetaDebito":
                return $this->getParameter('FormaPagoTipo.TarjetaDebito');
                break;
            case "FormaPagoTipo.ChequeFecha":
                return $this->getParameter('FormaPagoTipo.ChequeFecha');
                break;
            case "FormaPagoTipo.ChequeDia":
                return $this->getParameter('FormaPagoTipo.ChequeDia');
                break;
            case "FormaPagoTipo.ConvenioLasik":
                return $this->getParameter('FormaPagoTipo.ConvenioLasik');
                break;
            case "FormaPagoTipo.ConvenioImed":
                return $this->getParameter('FormaPagoTipo.ConvenioImed');
                break;
            case "FormaPagoTipo.SeguroComplementario":
                return $this->getParameter('FormaPagoTipo.SeguroComplementario');
                break;
            case "EstadoDetalleTalonario.emitidas":
                return $this->getParameter('EstadoDetalleTalonario.emitidas');
                break;
            case "FormaPagoTipo.Excedente":
                return $this->getParameter('FormaPagoTipo.Excedente');
                break;
            case "FormaPagoTipo.Transbank":
                return $this->getParameter('FormaPagoTipo.Transbank');
                break;
            default:
                return null;
        }
    }

    /**
     * Descripción: A ELIMINARSE
     */
    protected function Tipos($em, $oEmpresa)
    {

        $oTipoDocumentoAfecto = $em->getRepository('App\Entity\Legacy\TipoDocumento')->find($this->getParameter('tipo_documento_afecto'));
        $oTipoDOcumentoExento = $em->getRepository('App\Entity\Legacy\TipoDocumento')->find($this->getParameter('tipo_documento_Exento'));

        $datosArray = [
            'TipoAtencionFcAmbulatoria' => $em->getRepository('App\Entity\Legacy\TipoAtencionFc')->find($this->getParameter('ambulatoria')),
            'BoletaAfecta' => $em->getRepository('App\Entity\Legacy\RelEmpresaTipoDocumento')->findOneBy(array(
                'idTipoDocumento' => $oTipoDocumentoAfecto->getid(),
                'idEmpresa' => $oEmpresa->getid()
            )),
            'BoletaExenta' => $em->getRepository('App\Entity\Legacy\RelEmpresaTipoDocumento')->findOneBy(array(
                'idTipoDocumento' => $oTipoDOcumentoExento->getid(),
                'idEmpresa' => $oEmpresa->getid()
            )),
            'TipoLogRecepcion' => $em->getRepository('App\Entity\Legacy\ReservaAtencionTipoLog')->find($this->getParameter('tipo_log_recepcion')),
            'TipoLogPagoReserva' => $em->getRepository('App\Entity\Legacy\ReservaAtencionTipoLog')->find($this->getParameter('tipo_log_pago_Reserva'))
        ];

        return $datosArray;
    }

    /**
     * Protecteds Tipo
     * Descripción: Funcion consultar de manera mas agil por Tipos comunes de documentos o tipos para tratacion de dato en general.
     */

    // protected function entityManagerProtected($nombreEntidad) {
    // return $this->getDoctrine()->getManager()->getRepository($nombreEntidad);
    // }

    protected function tipo($var)
    {

        $oEmpresa = $this->ObtenerEmpresaLogin();
        $em = $this->getDoctrine()->getManager();

        switch ($var) {
            case "TipoAtencionFcAmbulatoria":
                return $em->getRepository('App\Entity\Legacy\TipoAtencionFc')->find($this->getParameter('ambulatoria'));
                break;
            case "BoletaAfecta":
                $oTipoDocumentoAfecto = $em->getRepository('App\Entity\Legacy\TipoDocumento')->find($this->getParameter('tipo_documento_afecto'));
                return $em->getRepository('App\Entity\Legacy\RelEmpresaTipoDocumento')->findOneBy(array("idTipoDocumento" => $oTipoDocumentoAfecto->getid(), "idEmpresa" => $oEmpresa->getid()));
                break;
            case "BoletaExenta":
                $oTipoDocumentoExento = $em->getRepository('App\Entity\Legacy\TipoDocumento')->find($this->getParameter('tipo_documento_Exento'));
                return $em->getRepository('App\Entity\Legacy\RelEmpresaTipoDocumento')->findOneBy(array("idTipoDocumento" => $oTipoDocumentoExento->getid(), "idEmpresa" => $oEmpresa->getid()));
                break;
            case "TipoLogRecepcion":
                return $em->getRepository('App\Entity\Legacy\ReservaAtencionTipoLog')->find($this->getParameter('tipo_log_recepcion'));
                break;
            case "TipoLogPagoReserva":
                return $em->getRepository('App\Entity\Legacy\ReservaAtencionTipoLog')->find($this->getParameter('tipo_log_pago_Reserva'));
                break;
            case "TipoLogAnulado":
                return $em->getRepository('App\Entity\Legacy\ReservaAtencionTipoLog')->find(11);
                break;
            default:
                return null;
        }
    }

    /**
     * Protecteds Errores
     * Descripción: Funcion consultar distintos tipo de errores en aplicación para poder devolver un valor.
     */
    protected function ErrorImedHermes($var)
    {

        switch ($var) {
            case "VtaBonInterfaz":
                return "Error en Generar Venta Bono Interfaz";
                break;
            case "ObtBonInterfaz":
                return "Error en Obtener Bono por Interfaz";
                break;
            case "noSubEmpresa":
                return 'Prestaciones no Corresponden a Sub-E,mpresa de Cajero';
                break;
            case "sinPreciosPrestacion":
                return 'Prestación no cuenta con sus Precios Correctamente';
                break;
            case "sinParameters":
                return 'No fue posible generar Parametros, reintente';
                break;
            case "SetGlobalsVar":
                return 'No se han encontrado datos básicos para establecer comunicación con I-MED, debe ponerse en contacto con el Administrador';
                break;
            case " noSendPostLogin":
                return 'No fue posible establecer Comunicación con I-MED. Error: Envio Post = False';
                break;
            case "errorCajaRecaudacion":
                return 'El usuario no esta relacionado como Cajero';
                break;
            case "errorEjecucion":
                return 'No ha sido Posible inicializar Caja, Error Interno';
                break;
            case "pagoConFactura":
                return 'El pago que intenta Anular, tiene relacionada una Factura';
                break;
            case "fechaCajaAnulacion":
                return 'La fecha del Pago qaue intenta Anular, debe ser igual a la fecha Actual';
                break;
            case 'errorImedVacio':
                return 'No hay prestaciones homologadas con Imed para valorizar';
                break;
            default:
                return null;
        }
    }

    /**
     * FUNCIONES INFORME CAJA
     * RENDER DE INFORME CAJA PARA RECAUDACION Y CONSOLIDADO
     */
    protected function RenderViewInformeCaja($arr)
    {

        $estadoApi = $this->estado('EstadoApi');

        if ($estadoApi != 'core') {
            if ($estadoApi['rutaApi'] === 'ApiPV') {
                $estadoApi = 'core';
            }
        }

        $id = $arr['id'];
        $renderType = ($arr['print'] == 1) ? 'renderView' : 'render';
        $oDocumentoPagoDetalles = NULL;
        $arrMediosMonto = NULL;
        $detalleCaja = NULL;
        $detalleCajat = NULL;
        $arrayPagosCuenta = array();
        $arrayBoletas = array();
        $EstadoBoletaActiva = $this->estado('EstadoBoletaActiva');
        $EstadoPagoActiva = $this->estado('EstadoPagoActiva');
        $EstadoApi = ($this->estado('EstadoApi') == "core") ? 1 : 0;
        $EstadoPagoRegularizada = $this->estado('EstadoPagoRegularizada');
        $EstadoActivo = $this->estado('EstadoActivo');
        $EstadoPagoGarantia = $this->estado('EstadoPagoGarantia');
        $EstadoPagoAnulada = $this->estado('EstadoPagoAnulada');
        $EstadoReaperturaAbierta = $this->estado('EstadoReaperturaAbierta');
        $em = $this->getDoctrine()->getManager();
        $oCajaDetalles = $em->getRepository("App\Entity\Legacy\Caja")->find($id);
        $idUser = $oCajaDetalles->getIdUsuario();
        $folio = $em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('FOLIO_GLOBAL');
        $pCajaUsaDeposito = $em->getRepository("App\Entity\Legacy\Parametro")->obtenerParametro('CAJA_USA_DEPOSITO');

        /**
         * SI ES EXCEL INFORME CAJAS COMPLETAS
         */
        if ($arr['from'] === 2) {

            $arrMediosMonto = array();
            $arrayTipos = array();
            $detalleCaja = array();
            $detalleCajat = array();
            $arrayTiposs = array();
            $oEmpresa = $this->ObtenerEmpresaLogin();
            $idSucursalCaja = $this->getSession('sucursal');
            $fechaFormat = new \DateTime($this->getSession('fecha'));
            $fechaFormat = $fechaFormat->format('Y-m-d');
            $fecha = explode("-", $this->getSession('fecha'));
            $fechaIngresada = $fecha[2] . "-" . $fecha[1] . "-" . $fecha[0] . "%";
            $oFormaPago = $em->getRepository("App\Entity\Legacy\FormaPago")->findBy(array("idEstado" => $EstadoActivo));
            $oTiposFormaPago = $em->getRepository("App\Entity\Legacy\FormaPagoTipo")->findBy(array("idEstado" => $EstadoActivo));

            foreach ($oTiposFormaPago as $tfp) {
                $arrMediosMonto[] = $em->getRepository('App\Entity\Legacy\PagoCuenta')->obtenerCopago($id, $tfp);
            }
            foreach ($oFormaPago as $tipos) {
                $arrayTipos[$tipos->getid()] = $this->rPagoCuenta()->obtenerMontoCierreCajayBonos($id, $tipos->getId(), $EstadoActivo);
            }
            $detalleCaja = $this->rPagoCuenta()->arrayDetalleCajaExcel($arrayTipos, $EstadoActivo, $em);
            foreach ($oFormaPago as $tipos) {
                $arrayTiposs[$tipos->getid()] = $this->rPagoCuenta()->obtenerMontoCierreCaja($id, $tipos->getId(), $EstadoActivo->getId());
            }
            $detalleCajat = $this->rPagoCuenta()->arrayDetalleCajaTodoExcel($arrayTiposs, $EstadoActivo, $oEmpresa, $em);
        }

        if ($EstadoApi == 1) {

            if ($arr['from'] == 2) {

                //EXCEL->CORE
                $oCajaPrincipal = $this->rPagoCuenta()->obtenerOCaja($fechaIngresada, $idSucursalCaja, $idUser);
                $oCajaConBoleta = $this->rPagoCuenta()->obtenerCajaC($fechaFormat, $idSucursalCaja, $EstadoPagoActiva);
                $oCajaConGarantia = $this->rPagoCuenta()->obtenerGarantias($fechaIngresada, $idSucursalCaja, $EstadoPagoGarantia);
                $oCajaPagoAnulados = $this->rPagoCuenta()->obtenerPagosAnulados($fechaIngresada, $idSucursalCaja, $EstadoPagoAnulada);
                $oCajaConGarantiaAnulada = $this->rPagoCuenta()->obtenerGarantiasAnuladas($fechaIngresada, $idSucursalCaja, $EstadoPagoAnulada);

            } else {

                /** INFORMES->CORE */
                $oCajaPrincipal = $this->rCaja()->GetInformacionDetalladaCaja($id, $idUser, $folio['valor']);
                foreach ($oCajaPrincipal as $key => $result) {
                    $oCajaPrincipal[$key]['identificacionExtranjero'] = $this->get('CommonServices')->formatearRut($result['identificacionExtranjero']);
                }
                $oCajaConBoleta = $this->rCaja()->GetInformacionDetalladaCajaSecundaria($id, $idUser, $EstadoBoletaActiva, $EstadoPagoActiva, $folio['valor']);
                foreach ($oCajaConBoleta as $key => $result) {
                    $oCajaConBoleta[$key]['identificacionExtranjero'] = $this->get('CommonServices')->formatearRut($result['identificacionExtranjero']);
                }

                /** revisar Informe Garantias */
                $oCajaConGarantia = $this->rCaja()->GetInformacionDetalladaCajaGarantia($id, $idUser, $EstadoPagoRegularizada, $EstadoPagoGarantia);
                $oCajaPagoAnulados = $this->rCaja()->GetInformacionDetalladaCajaAnulada($id, $idUser, $EstadoPagoAnulada);
//                dump($oCajaPagoAnulados);exit;
                $oCajaConGarantiaAnulada = $this->rCaja()->GetInformacionDetalladaGarantiaCajaAnulada($id, $idUser, $EstadoPagoAnulada);


            }
        } else {
            if ($arr['from'] == 2) {

                //EXCEL->API
                $oCajaPrincipal = $this->rPagoCuenta()->obtenerOCajaApi1($fechaIngresada, $idSucursalCaja, $idUser);
                $oCajaConBoleta = $this->rPagoCuenta()->obtenerCajaCApi1($fechaFormat, $idSucursalCaja, $EstadoPagoActiva);
                $oCajaConGarantia = $this->rPagoCuenta()->obtenerGarantiasApi1($fechaIngresada, $idSucursalCaja, $EstadoPagoGarantia);
                $oCajaPagoAnulados = $this->rPagoCuenta()->obtenerPagosAnuladosApi1($fechaIngresada, $idSucursalCaja, $EstadoPagoAnulada);
                $oCajaConGarantiaAnulada = $this->rPagoCuenta()->obtenerGarantiasAnuladasApi1($fechaIngresada, $idSucursalCaja, $EstadoPagoAnulada);
            } else {
                //INFORMES->API
                $oCajaPrincipal = $this->rCaja()->GetInformacionDetalladaCajaApi1($id, $idUser);
                $oCajaConBoleta = $this->rCaja()->GetInformacionDetalladaCajaSecundariaApi1($id, $idUser, $EstadoBoletaActiva, $EstadoPagoActiva);
                $oCajaConGarantia = $this->rCaja()->GetInformacionDetalladaCajaGarantiaApi1($id, $idUser, $EstadoPagoRegularizada, $EstadoPagoGarantia);
                $oCajaPagoAnulados = $this->rCaja()->GetInformacionDetalladaCajaAnuladaApi1($id, $idUser, $EstadoPagoAnulada);
                $oCajaConGarantiaAnulada = $this->rCaja()->GetInformacionDetalladaGarantiaCajaAnuladaApi1($id, $idUser, $EstadoPagoAnulada);
            }
        }

        if ($arr['from'] == 2) {
            /** EXCEL->TODOS LOS CASOS */
            $formaPago = $this->rPagoCuenta()->obtenerFormaPago($fechaFormat, $idSucursalCaja);
            $formaPagoGarantia = $this->rPagoCuenta()->obtenerFormaPagoGarantias($fechaIngresada, $idSucursalCaja);
            $documentos = $this->rPagoCuenta()->obtenerDocumentosDePago($fechaIngresada, $idSucursalCaja);
            $documentosGarantia = $this->rPagoCuenta()->obtenerDocumentosDePagoGarantias($fechaIngresada, $idSucursalCaja);
            $detallesMontosTipos = $this->rPagoCuenta()->obtenerDatosCuadratura($fechaFormat, $idSucursalCaja);
            $detallesMontosBonosManuales = $this->rPagoCuenta()->obtenerDatosCuadraturaBono($fechaIngresada, $idSucursalCaja);
            $detalleMontosDeposito = $this->rDetalleCaja()->ObtieneDetalleCajaDeposito($fechaIngresada, $idSucursalCaja, $EstadoActivo);
            $detalleMontosGarantias = $this->rPagoCuenta()->obtenerDatosCuadraturaGarantia($fechaIngresada, $idSucursalCaja);
        } else {
            /** INFORMES->TODOS LOS CASOS */
            $formaPago = $this->rCaja()->GetFormasPago($id);
            $formaPagoGarantia = $this->rCaja()->GetFormasPagoGarantia($id);
            $documentos = $this->rCaja()->GetResultadoDocumentos($id);
            $documentosGarantia = $this->rCaja()->GetResultadoDocumentosGarantia($id);
            $detallesMontosTipos = $this->rCaja()->GetResultadosCuadratura($id);
            $detallesMontosBonosManuales = $this->rCaja()->GetResultadosCuadraturaBono($id, $EstadoPagoActiva);
            $detalleMontosDeposito = $this->rCaja()->GetDetalleCajaDeposito($id, $EstadoActivo);
            $detalleMontosGarantias = $this->rCaja()->GetResultadosCuadraturaGarantia($id);
            $detalleDescuentos = $this->rCaja()->GetResultadosDescuentos($formaPago);
        }

        $detalleMontosTiposBonos = $this->rCaja()->GetResultadosCuadraturaBonos($id);


        foreach ($oCajaConBoleta as $c) {
            $arrayPagosCuenta[] = $c['idPagoCuenta'];
        }

        foreach ($arrayPagosCuenta as $c) {

            $BoletasQueryResult = $this->rCaja()->GetBoletasPorCaja($c, $EstadoBoletaActiva, $folio['valor']);

            if ($BoletasQueryResult) {

                foreach ($BoletasQueryResult as $b) {
                    $arrayBoletas[] = $b;
                }
            }
        }

        $arrMediosPagos = array();
        $countAux = 0;

        foreach ($formaPago as $c) {
            $arrMediosPagos[$countAux] = array('id' => $c['idForma'], 'monto' => 0);
            $countAux = $countAux + 1;
        }
        //dump($oCajaPrincipal,$oCajaConBoleta);exit;
//dump('CajaBundle:' . $arr['path'] . ':' . $arr['source'] . '.html.twig');exit;
        return $this->$renderType('RecaudacionBundle:' . $arr['path'] . ':' . $arr['source'] . '.html.twig', array(
            'caja' => $oCajaPrincipal,
            'cajac' => $oCajaConBoleta,
            'datosCaja' => $this->rCaja()->GetInformacionCaja($id),
            'cajero' => $this->rCaja()->GetCajeroInforme($idUser),
            'cajag' => $oCajaConGarantia,
            'cajaAnul' => $oCajaPagoAnulados,
            'cajaAnulg' => $oCajaConGarantiaAnulada,
            'cajaDetalles' => $oCajaDetalles,
            'documentos' => $documentos,
            'documentosg' => $documentosGarantia,
            'formaPago' => $formaPago,
            'formaPagog' => $formaPagoGarantia,
            'formasPago' => $arrMediosPagos,
            'numerosBoletas' => $arrayBoletas,
            'estadoActivo' => $EstadoActivo,
            'estadoReapertura' => $EstadoReaperturaAbierta->getid(),
            'estadoBoletaActiva' => $EstadoBoletaActiva->getid(),
            'detallesMontosTipos' => $detallesMontosTipos,
            'detallesMontosBonosManuales' => $detallesMontosBonosManuales,
            'detalleMontosDeposito' => $detalleMontosDeposito,
            'detalleMontosTiposBonos' => $detalleMontosTiposBonos,
            'detalleMontosGarantias' => $detalleMontosGarantias,
            'detalleDescuentos' => $detalleDescuentos,
            'idcaja' => $id,
            'detalleCount' => $oDocumentoPagoDetalles,
            'copagoCount' => $arrMediosMonto,
            'detalleCaja' => $detalleCaja,
            'detalleCajaTodo' => $detalleCajat,
            'coreApi' => ($this->estado('EstadoApi') === "core") ? 1 : 0,
            'pCajaUsaDeposito' => $pCajaUsaDeposito['valor'],
        ));
    }

    /**
     * [getMySqlQuery description]
     * @return [type] [description]
     */
    protected function getMySqlQuery()
    {
        return $this->getContainer()->get('doctrine')->getManager('mysql');
    }

    public function RenderViewInformeCajaPagosWeb($arr)
    {
        $em = $this->getDoctrine()->getManager();

        $estadoApi = $this->estado('EstadoApi');
        $id = $arr['id'];
        $renderType = ($arr['print'] == 1) ? 'renderView' : 'render';
        $oFecha = new \DateTime($this->getSession('fecha'));
        $idSucursalCaja = $this->getSession('sucursal');

        $EstadoActivo = $this->estado('EstadoActivo');
        $EstadoReaperturaAbierta = $this->estado('EstadoReaperturaAbierta');
        $EstadoBoletaActiva = $this->estado('EstadoBoletaActiva');
        $EstadoPagoActiva = $this->estado('EstadoPagoActiva');
        $EstadoApi = ($estadoApi == "core") ? 1 : 0;


        $folio = $em->getRepository('App\Entity\Legacy\Parametro')->obtenerParametro('FOLIO_GLOBAL');
        /** INFORMES->CORE */
        $oCajaPrincipal = $this->rCaja()->GetInformacionDetalladaCaja(null, null, $folio['valor'], $oFecha);
        $oCajaConBoleta = $this->rCaja()->GetInformacionDetalladaCajaSecundaria(null, null, $EstadoBoletaActiva, $EstadoPagoActiva, $folio['valor'], $oFecha);

        $formaPago = $this->rCaja()->GetFormasPago(null, $oFecha);

        $detallesMontosTipos = $this->rCaja()->GetResultadosCuadratura(null,$oFecha);

        $oCajaConGarantia = array();
        $oCajaPagoAnulados = array();
        $oCajaConGarantiaAnulada = array();

        $oCajaDetalles = array();
        $documentos = array();
        $documentosGarantia = array();

        $formaPagoGarantia = array();
        $detallesMontosBonosManuales = array();
        $detalleMontosDeposito = array();
        $detalleMontosTiposBonos = array();
        $detalleMontosGarantias = array();
        $oDocumentoPagoDetalles = null;
        $arrMediosMonto = null;
        $detalleCaja = null;
        $detalleCajat = null;
        $arrayPagosCuenta = array();

        $arrMediosPagos = array();
        $countAux = 0;

        $cajero = null;

        foreach ($formaPago as $c) {
            $arrMediosPagos[$countAux] = array('id' => $c['idForma'], 'monto' => 0);
            $countAux = $countAux + 1;
        }

        $arrayBoletas = array();

        foreach ($oCajaConBoleta as $c) {
            $arrayPagosCuenta[] = $c['idPagoCuenta'];
        }

        foreach ($arrayPagosCuenta as $c) {

            $BoletasQueryResult = $this->rCaja()->GetBoletasPorCaja($c, $EstadoBoletaActiva, $folio['valor']);

            if ($BoletasQueryResult) {

                foreach ($BoletasQueryResult as $b) {
                    $arrayBoletas[] = $b;
                }
            }
        }
        return $this->$renderType('RecaudacionBundle:' . $arr['path'] . ':' . $arr['source'] . '.html.twig', [
            'caja' => $oCajaPrincipal,
            'cajac' => $oCajaConBoleta,
            'cajero' => $cajero,
            'cajag' => $oCajaConGarantia,
            'cajaAnul' => $oCajaPagoAnulados,
            'cajaAnulg' => $oCajaConGarantiaAnulada,
            'cajaDetalles' => $oCajaDetalles,
            'documentos' => $documentos,
            'documentosg' => $documentosGarantia,
            'formaPago' => $formaPago,
            'formaPagog' => $formaPagoGarantia,
            'formasPago' => $arrMediosPagos,
            'numerosBoletas' => $arrayBoletas,
            'estadoActivo' => $EstadoActivo,
            'estadoReapertura' => $EstadoReaperturaAbierta->getid(),
            'estadoBoletaActiva' => $EstadoBoletaActiva->getid(),
            'detallesMontosTipos' => $detallesMontosTipos,
            'detallesMontosBonosManuales' => $detallesMontosBonosManuales,
            'detalleMontosDeposito' => $detalleMontosDeposito,
            'detalleMontosTiposBonos' => $detalleMontosTiposBonos,
            'detalleMontosGarantias' => $detalleMontosGarantias,
            'idcaja' => $id,
            'detalleCount' => $oDocumentoPagoDetalles,
            'copagoCount' => $arrMediosMonto,
            'detalleCaja' => $detalleCaja,
            'detalleCajaTodo' => $detalleCajat,
            'coreApi' => ($estadoApi === "core") ? 1 : 0,
            'oFecha' => $oFecha
        ]);

    }

    public function getPagoCuentaWebByFecha($fecha, $idSucursal)
    {
        $fechaOb = $fecha;
        $nuevafecha = clone $fecha;
        $nuevafecha->modify('+1 day');
        return $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('pc')
            ->from('App\Entity\Legacy\PagoCuenta', 'pc')
            ->join('App\Entity\Legacy\DetallePagoCuenta', 'dpg', 'WITH', 'dpg.idPagoCuenta = pc.id')
            ->innerJoin('dpg.idFormaPago', 'fp')
            ->innerJoin('pc.idPaciente', 'paciente')
            ->join('App\Entity\Legacy\ReservaAtencion', 'ra', 'WITH', 'ra.idPaciente = paciente.id')
            ->innerJoin('ra.idHorarioConsulta', 'hc')
            ->where('pc.idPagoWeb IS NOT NULL')
            ->andWhere('pc.fechaPago >= :fechaPago')
            ->andWhere('hc.idSucursal = :idSucursal')
            ->andWhere('pc.fechaPago < :fechaPagoLast')
            ->andWhere('fp.pagoProfesional = 0 OR fp.pagoProfesional IS NULL')
            ->setParameter('fechaPago', $fechaOb)
            ->setParameter('idSucursal', $idSucursal)
            ->setParameter('fechaPagoLast', $nuevafecha->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function imprimirMediosDePagoAction(Request $request, $id) {

        return $this->RenderViewInformeCaja(
            array(
                'id'        => $id,
                'print'     => 0,
                'from'      => 0,
                'path'      => 'Recaudacion\GestionCaja\Informes',
                'source'    => 'InformeMedioDePago'
            )
        );
    }

    public function formatear()
    {

    }

}
