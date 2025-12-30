<?php

namespace App\Controller\Caja\Servicios;

use PHPMailer\PHPMailer\PHPMailer;
use App\Controller\Caja\RecaudacionController;
use App\Controller\Caja\Servicios\se;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function dump;

class BuscarPrestacionesController extends RecaudacionController
{
    /**
     * @return Response renderView
     * Descripción: IndexAction() Busca las prestaciones según un texto a buscar.
     */
    public function indexAction(Request $request)
    {
        $textoBusqueda = $this->container->get('request_stack')->getCurrentRequest()->query->get('textoBusqueda');
        $em = $this->getDoctrine()->getManager();

        $oPrestaciones = $em->getRepository('RebsolHermesBundle:AccionClinica')
            ->busquedaPrestacionesPorNombreOCodigo(
                $textoBusqueda,
                $this->container->getParameter('estado_activo'),
                $this->ObtenerEmpresaLogin()->getId()
            );

        $renderView = $this->renderView('RecaudacionBundle:PrestacionesPaciente:_verPrestaciones.html.twig',
            array(
                'oPrestaciones' => $oPrestaciones
            )
        );
        return new Response($renderView);
    }

    public function buscarPaquetesPrestacionesAction()
    {
        dump('buscarPaquetesPrestacionesAction');
        exit();
        $textoBusqueda = $this->container->get('request_stack')->getCurrentRequest()->query->get('textoBusqueda');
        $em = $this->getDoctrine()->getManager();

        $oPrestaciones = $em->getRepository('RebsolHermesBundle:AccionClinica')
            ->busquedaPaquetePrestacionesPorNombreOCodigo(
                $textoBusqueda,
                $this->container->getParameter('estado_activo'),
                $this->ObtenerEmpresaLogin()->getId()
            );
        // foreach($oPrestaciones as $oAccionClinica) {
        // 	$oPP = $em->getRepository('RebsolHermesBundle:PaquetePrestacion')->findOneBy([
        // 		'idEstado' => $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo')),
        // 		'idAccionClinica' => $oAccionClinica
        // 	]);

        // 	$oAccionClinica->setNombreAccionClinica($oPP->getNombre());
        // }
        $renderView = $this->renderView('RecaudacionBundle:PrestacionesPaciente:_verPaquetesPrestaciones.html.twig',
            array(
                'oPrestaciones' => $oPrestaciones
            )
        );
        return new Response($renderView);
    }

    /**
     * @return Response Json
     * Descripción: obtenerPrestacionJsonAction() Obtiene los datos de una prestación
     */
    public function obtenerPrestacionJsonAction()
    {
        $em = $this->getDoctrine()->getManager();
        $fService = $this->get('Caja_valida');

        $idPrestacion = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPrestacion');
        $idPagoCuenta = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPagoCuenta');
        $idAccionClinicaPaciente = $this->container->get('request_stack')->getCurrentRequest()->query->get('idAccionClinicaPaciente');

        $ArrTalonarios = $this->get('session')->get('idTalonario');
        //
        $idExamenPacienteFc = $this->container->get('request_stack')->getCurrentRequest()->query->get('idExamenPacienteFc');

        $idPaquetePrestacion = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPaquetePrestacion');

        if ($idExamenPacienteFc) {

            $oExamenPacienteFc = $em->getRepository('RebsolHermesBundle:ExamenPacienteFc')->find($idExamenPacienteFc);
            $arrExamenPacienteFcDetalle = $em->getRepository('RebsolHermesBundle:ExamenPacienteFcDetalle')->findBy(['idExamenPacienteFc' => $oExamenPacienteFc]);

            $arrAccionClinica = array();
            $esPaquete = false;
            foreach ($arrExamenPacienteFcDetalle as $oExamenPacienteFcDetalle) {
                $oPaquetePrestacion = $oExamenPacienteFcDetalle->getIdPaquetePrestacion();

                $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($oExamenPacienteFcDetalle->getIdAccionClinicA());
                $idPrestacion = $oAccionClinica->getId();

                if ($this->getsession('idSubEmpresaItem') == null) {
                    $this->setsession('idSubEmpresaItem', $oAccionClinica->getIdSubEmpresa()->getId());
                }

                $TipoAtencion = $this->container->get('request_stack')->getCurrentRequest()->query->get('TipoAtencion');
                $plan = $this->container->get('request_stack')->getCurrentRequest()->query->get('plan');
                $fechahoy = new \DateTime();
                $fechahoy = $fechahoy->format("Y-m-d H:i:s");
                $oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
                $idUser = $this->getUser();
                $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($idUser);
                $oRelTipoAtencion = $em->getRepository('RebsolHermesBundle:RelSucursalTipoAtencion')->findOneBy(array('idSucursal' => $SucursalUsuario->getId(), 'idTipoAtencion' => $TipoAtencion, 'idEstado' => $oEstadoAct->getId()));
                $TipoAtencion = $oRelTipoAtencion->getId();

                if ($oPaquetePrestacion) {
                    //SI ES FACTURABLE
                    if ($oPaquetePrestacion->getFacturable()) {
                        if ($esPaquete == false) {
                            $esPaquete = true;
                            $idP = $oPaquetePrestacion->getIdAccionClinica()->getId();

                            $oPlan = $em->getRepository('RebsolHermesBundle:PrPlan')->find($plan);
                            if ($oPlan->getIdPrPlanPaquetePrestacion()) {
                                $plan = $oPlan->getIdPrPlanPaquetePrestacion()->getId();
                            }

                            $aAC = $fService->ObtenerPrestacion($idP, $plan, $fechahoy, $oEstadoAct, $TipoAtencion);

                            if ($aAC) {
                                $aAC['idPaquetePrestacion'] = $oPaquetePrestacion->getIdAccionClinica()->getId();
                                $arrAccionClinica[] = $aAC;
                            } else {
                                $arrAccionClinica[] = array(
                                    'id' => $oPaquetePrestacion->getIdAccionClinica()->getId(),
                                    'nombre' => $oPaquetePrestacion->getIdAccionClinica()->getNombreAccionClinica(),
                                    'codigo' => $oPaquetePrestacion->getIdAccionClinica()->getCodigoAccionClinica(),
                                    'codigoFonasa' => $oPaquetePrestacion->getIdAccionClinica()->getCodigoFonasa(),
                                    'precio' => "0",
                                    'pabellon' => $oPaquetePrestacion->getIdAccionClinica()->getIdGuarismo()->getId(),
                                    'idPaquetePrestacion' => $oPaquetePrestacion->getIdAccionClinica()->getId(),
                                    'idAccionClinicaPaciente' => $idAccionClinicaPaciente
                                );
                            }
                        }
                        $valorNulo = "0";
                        $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);

                        if ($this->getsession('idSubEmpresaItem') == null) {

                            $this->setsession('idSubEmpresaItem', $oAccionClinica->getIdSubEmpresa()->getId());
                        }

                        $arrAccionClinica[] = array(
                            'id' => $oAccionClinica->getId(),
                            'nombre' => $oAccionClinica->getNombreAccionClinica(),
                            'codigo' => $oAccionClinica->getCodigoAccionClinica(),
                            'codigoFonasa' => $oAccionClinica->getCodigoFonasa(),
                            'precio' => $valorNulo,
                            'pabellon' => $oAccionClinica->getIdGuarismo()->getId(),
                            'idPaquetePrestacion' => $oPaquetePrestacion->getIdAccionClinica()->getId(),
                            'idAccionClinicaPaciente' => $idAccionClinicaPaciente
                        );

                    } else { // SI NO ES FACTURABLE

                        $aAccionClinica = $fService->ObtenerPrestacion($idPrestacion, $plan, $fechahoy, $oEstadoAct, $TipoAtencion);
                        if ($aAccionClinica) {
                            $arrAccionClinica[] = $aAccionClinica;
                        } else {
                            $valorNulo = "0";
                            $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);

                            $arrAccionClinica[] = array(
                                'id' => $oAccionClinica->getId(),
                                'nombre' => $oAccionClinica->getNombreAccionClinica(),
                                'codigo' => $oAccionClinica->getCodigoAccionClinica(),
                                'codigoFonasa' => $oAccionClinica->getCodigoFonasa(),
                                'precio' => $valorNulo,
                                'pabellon' => $oAccionClinica->getIdGuarismo()->getId(),
                                'idAccionClinicaPaciente' => $idAccionClinicaPaciente
                            );
                        }

                    }
                } else {

                    $aAccionClinica = $fService->ObtenerPrestacion($idPrestacion, $plan, $fechahoy, $oEstadoAct, $TipoAtencion);

                    if ($aAccionClinica) {

                        $arrAccionClinica[] = $aAccionClinica;

                    } else {
                        $valorNulo = "0";
                        $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);

                        $arrAccionClinica[] = array(
                            'id' => $oAccionClinica->getId(),
                            'nombre' => $oAccionClinica->getNombreAccionClinica(),
                            'codigo' => $oAccionClinica->getCodigoAccionClinica(),
                            'codigoFonasa' => $oAccionClinica->getCodigoFonasa(),
                            'precio' => $valorNulo,
                            'pabellon' => $oAccionClinica->getIdGuarismo()->getId(),
                            'idAccionClinicaPaciente' => $idAccionClinicaPaciente
                        );
                    }

                }
            }
            return new Response(json_encode($arrAccionClinica));

        } elseif (!is_null($idPaquetePrestacion)) {

            $oPaquetePrestacion = $em->getRepository('RebsolHermesBundle:PaquetePrestacion')->find($idPaquetePrestacion);
            $plan = $this->container->get('request_stack')->getCurrentRequest()->query->get('plan');
            $fechahoy = new \DateTime();
            $fechahoy = $fechahoy->format("Y-m-d H:i:s");
            $oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
            $idUser = $this->getUser();
            $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($idUser);
            $TipoAtencion = $em->getRepository('RebsolHermesBundle:TipoAtencionFc')->findOneBy(['nombreTipoAtencionFc' => 'AMBULATORIA']);
            $oRelTipoAtencion = $em->getRepository('RebsolHermesBundle:RelSucursalTipoAtencion')->findOneBy(array('idSucursal' => $SucursalUsuario->getId(), 'idTipoAtencion' => $TipoAtencion, 'idEstado' => $oEstadoAct->getId()));

            $TipoAtencion = $oRelTipoAtencion->getId();

            $showFormRisLis = false;

            $arrAccionClinica = array();
            if ($oPaquetePrestacion->getFacturable()) {
                $idP = $oPaquetePrestacion->getIdAccionClinica()->getId();

                $oPlan = $em->getRepository('RebsolHermesBundle:PrPlan')->find($plan);
                if ($oPlan->getIdPrPlanPaquetePrestacion()) {
                    $plan = $oPlan->getIdPrPlanPaquetePrestacion()->getId();
                }

                $aAC = $fService->ObtenerPrestacion($idP, $plan, $fechahoy, $oEstadoAct, $TipoAtencion);

                if ($aAC) {
                    $aAC['idPaquetePrestacion'] = $oPaquetePrestacion->getIdAccionClinica()->getId();
                    $arrAccionClinica[] = $aAC;
                } else {
                    $arrAccionClinica[] = array(
                        'id' => $oPaquetePrestacion->getIdAccionClinica()->getId(),
                        'nombre' => $oPaquetePrestacion->getIdAccionClinica()->getNombreAccionClinica(),
                        'codigo' => $oPaquetePrestacion->getIdAccionClinica()->getCodigoAccionClinica(),
                        'codigoFonasa' => $oPaquetePrestacion->getIdAccionClinica()->getCodigoFonasa(),
                        'precio' => "0",
                        'pabellon' => $oPaquetePrestacion->getIdAccionClinica()->getIdGuarismo()->getId(),
                        'idPaquetePrestacion' => $oPaquetePrestacion->getIdAccionClinica()->getId()
                    );
                }

                $arrPPD = $em->getRepository('RebsolHermesBundle:PaquetePrestacionDetalle')->findBy([
                    'idPaquetePrestacion' => $oPaquetePrestacion,
                    'idEstado' => $oEstadoAct
                ]);
                foreach ($arrPPD as $keyPPD => $oPPD) {
                    $valorNulo = "0";
                    $oAccionClinica = $oPPD->getIdAccionClinica();

                    if ($this->getsession('idSubEmpresaItem') == null) {
                        $this->setsession('idSubEmpresaItem', $oAccionClinica->getIdSubEmpresa()->getId());
                    }

                    $arrAccionClinica[] = array(
                        'id' => $oAccionClinica->getId(),
                        'nombre' => $oAccionClinica->getNombreAccionClinica(),
                        'codigo' => $oAccionClinica->getCodigoAccionClinica(),
                        'codigoFonasa' => $oAccionClinica->getCodigoFonasa(),
                        'precio' => $valorNulo,
                        'pabellon' => $oAccionClinica->getIdGuarismo()->getId(),
                        'idPaquetePrestacion' => $oPaquetePrestacion->getIdAccionClinica()->getId()
                    );

                    if ($oAccionClinica->getIdTipoPrestacion()->getTipoTipoPrestacion() == 'RIS' ||
                        $oAccionClinica->getIdTipoPrestacion()->getTipoTipoPrestacion() == 'LIS') {
                        $showFormRisLis = true;
                    }
                }
            } else {
                $idP = $oPaquetePrestacion->getIdAccionClinica()->getId();
                $aAccionClinica = $fService->ObtenerPrestacion($idP, $plan, $fechahoy, $oEstadoAct, $TipoAtencion);
                if ($aAccionClinica) {
                    $arrAccionClinica[] = $aAccionClinica;
                } else {
                    $valorNulo = "0";
                    $oAccionClinica = $oPaquetePrestacion->getIdAccionClinica();

                    $arrAccionClinica[] = array(
                        'id' => $oAccionClinica->getId(),
                        'nombre' => $oAccionClinica->getNombreAccionClinica(),
                        'codigo' => $oAccionClinica->getCodigoAccionClinica(),
                        'codigoFonasa' => $oAccionClinica->getCodigoFonasa(),
                        'precio' => $valorNulo,
                        'pabellon' => $oAccionClinica->getIdGuarismo()->getId(),
                    );
                }

                $arrPPD = $em->getRepository('RebsolHermesBundle:PaquetePrestacionDetalle')->findBy([
                    'idPaquetePrestacion' => $oPaquetePrestacion,
                    'idEstado' => $oEstadoAct
                ]);

                foreach ($arrPPD as $keyPPD => $oPPD) {
                    $idP = $oPPD->getIdAccionClinica()->getId();
                    $aAccionClinica = $fService->ObtenerPrestacion($idP, $plan, $fechahoy, $oEstadoAct, $TipoAtencion);

                    if ($oPPD->getIdAccionClinica()->getIdTipoPrestacion()->getTipoTipoPrestacion() == 'RIS' ||
                        $oPPD->getIdAccionClinica()->getIdTipoPrestacion()->getTipoTipoPrestacion() == 'LIS') {
                        $showFormRisLis = true;
                    }

                    if ($aAccionClinica) {
                        $arrAccionClinica[] = $aAccionClinica;
                    } else {
                        $valorNulo = "0";
                        $oAccionClinica = $oPPD->getIdAccionClinica();

                        $arrAccionClinica[] = array(
                            'id' => $oAccionClinica->getId(),
                            'nombre' => $oAccionClinica->getNombreAccionClinica(),
                            'codigo' => $oAccionClinica->getCodigoAccionClinica(),
                            'codigoFonasa' => $oAccionClinica->getCodigoFonasa(),
                            'precio' => $valorNulo,
                            'pabellon' => $oAccionClinica->getIdGuarismo()->getId(),
                        );
                    }
                }

            }
            return new Response(json_encode(['arrayPrestaciones' => $arrAccionClinica, 'showFormRisLis' => $showFormRisLis]));

        } else {
            if ($fService->SubEmpresa($idPrestacion, $ArrTalonarios)) {

                $oAccionclinicaAux = $em->getRepository('RebsolHermesBundle:Accionclinica')->find($idPrestacion);

                if ($this->getsession('idSubEmpresaItem') == null or $this->getsession('idSubEmpresaItem') == $oAccionclinicaAux->getIdSubEmpresa()->getId()) {

                    if ($this->getsession('idSubEmpresaItem') == null) {

                        $this->setsession('idSubEmpresaItem', $oAccionclinicaAux->getIdSubEmpresa()->getId());
                    }

                    $TipoAtencion = $this->container->get('request_stack')->getCurrentRequest()->query->get('TipoAtencion');
                    $plan = $this->container->get('request_stack')->getCurrentRequest()->query->get('plan');
                    $fechahoy = new \DateTime();
                    $fechahoy = $fechahoy->format("Y-m-d H:i:s");
                    $oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));

                    $idUser = $this->getUser();
                    $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($idUser);
                    $oRelTipoAtencion = $em->getRepository('RebsolHermesBundle:RelSucursalTipoAtencion')->findOneBy(array('idSucursal' => $SucursalUsuario->getId(), 'idTipoAtencion' => $TipoAtencion, 'idEstado' => $oEstadoAct->getId()));

                    $TipoAtencion = $oRelTipoAtencion->getId();


                    $categoria = $fService->CategoriaPrestador($idPrestacion);

                    /**
                     * @var $aAccionClinica se agrego variable no inicializada
                     */
                    $aAccionClinica = null;

                    $bPagoCuenta = $this->container->get('request_stack')->getCurrentRequest()->query->get('bPagoCuenta') === 'true';
                    $idAccionClinicaPaciente = $this->container->get('request_stack')->getCurrentRequest()->query->get('idAccionClinicaPaciente');

                    if ($categoria == "PRESTACION" || $categoria == "DÍA CAMA") {

                        if ($bPagoCuenta === true) {
                            $aAccionClinica = $fService->ObtenerPrestacion($idPrestacion, $plan, $fechahoy, $oEstadoAct, $TipoAtencion, $bPagoCuenta, $idAccionClinicaPaciente);
                        } else {
                            $aAccionClinica = $fService->ObtenerPrestacion($idPrestacion, $plan, $fechahoy, $oEstadoAct, $TipoAtencion);
                        }

                        //CHECK IF ACCIONCLINICA ES RIS O LIS
                        if ($aAccionClinica) {

                            $oPP = $em->getRepository('RebsolHermesBundle:PaquetePrestacion')->findOneBy([
                                'idEstado' => $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo')),
                                'idAccionClinica' => $idPrestacion
                            ]);

                            if (!is_null($oPP)) {
                                $aAccionClinica['nombre'] = $oPP->getNombre();
                                $aAccionClinica['codigo'] = $oPP->getCodigo();
                            }

                            $oAC = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);
                            $oTP = $oAC->getIdTipoPrestacion();
                            if ($oTP->getTipoTipoPrestacion() == 'RIS' || $oTP->getTipoTipoPrestacion() == 'LIS') {
                                $aAccionClinica['showFormRisLis'] = true;
                            } else {
                                $aAccionClinica['showFormRisLis'] = false;
                            }
                            $aAccionClinica['idPagoCuenta'] = $idPagoCuenta;
                        }
                    }

                    if ($categoria == "HONORARIOS") {
                        return new Response(json_encode(0));
                    }
                    if (!$aAccionClinica) {
                        $valorNulo = "0";
                        $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);

                        $oPP = $em->getRepository('RebsolHermesBundle:PaquetePrestacion')->findOneBy([
                            'idEstado' => $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo')),
                            'idAccionClinica' => $idPrestacion
                        ]);

                        //CHECK IF ACCIONCLINICA ES RIS O LIS
                        $oTP = $oAccionClinica->getIdTipoPrestacion();

                        $aAccionClinica = array(
                            'id' => $oAccionClinica->getId(),
                            'nombre' => is_null($oPP) ? $oAccionClinica->getNombreAccionClinica() : $oPP->getNombre(),
                            'codigo' => is_null($oPP) ? $oAccionClinica->getCodigoAccionClinica() : $oPP->getCodigo(),
                            'codigoFonasa' => $oAccionClinica->getCodigoFonasa(),
                            'precio' => $valorNulo,
                            'pabellon' => $oAccionClinica->getIdGuarismo()->getId(),
                        );

                        if ($oTP->getTipoTipoPrestacion() == 'RIS' || $oTP->getTipoTipoPrestacion() == 'LIS') {
                            $aAccionClinica['showFormRisLis'] = true;
                        } else {
                            $aAccionClinica['showFormRisLis'] = false;
                        }
                    }

                    $idReservaAtencion = $this->container->get('request_stack')->getCurrentRequest()->query->get('idReservaAtencion');
                    if ($idReservaAtencion != '' || !is_null($idReservaAtencion)) {
                        $oReservaAtencion = $em->getRepository('RebsolHermesBundle:ReservaAtencion')->find($idReservaAtencion);
                        $oRelReservaAccionClinica = $em->getRepository('RebsolHermesBundle:RelReservaAccionClinica')->findOneBy([
                            'idAccionClinica' => $oAccionclinicaAux,
                            'idReservaAtencion' => $oReservaAtencion
                        ]);
                        if (!is_null($oRelReservaAccionClinica)) {
                            if (!is_null($oRelReservaAccionClinica->getIdExamenPacienteFc())) {
                                $aAccionClinica['idExamenPacienteFc'] = $oRelReservaAccionClinica->getIdExamenPacienteFc()->getId();
                            }
                        }
                    }
                    $aAccionClinica['idAccionClinicaPaciente'] = $idAccionClinicaPaciente;
                    return new Response(json_encode($aAccionClinica));
                } else {
                    return new Response("nosubempresa");
                }
            } else {
                return new Response("nosubempresa");
            }

        }
    }

    public function obtenerPrestacionHonorariosJsonAction()
    {

        $em = $this->getDoctrine()->getManager();
        $fService = $this->get('Caja_valida');
        $idPrestacion = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPrestacion');
        $ArrTalonarios = $this->get('session')->get('idTalonario');
        if ($fService->SubEmpresa($idPrestacion, $ArrTalonarios)) {
            $TipoAtencion = $this->container->get('request_stack')->getCurrentRequest()->query->get('TipoAtencion');
            $plan = $this->container->get('request_stack')->getCurrentRequest()->query->get('plan');
            $idScurusal = $this->container->get('request_stack')->getCurrentRequest()->query->get('sucursal');
            $fechahoy = new \DateTime();
            $fechahoy = $fechahoy->format("Y-m-d H:i:s");
            $oEstadoAct = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
            $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);
            $iPabellon = $oAccionClinica->getidGuarismo()->getId();

            $query = $em->createQuery("
				SELECT
				ap.id as id
				FROM
				Rebsol\HermesBundle\Entity\AbiertaPrecio ap
				WHERE 1=1
				AND ap.idAccionClinica     = ?1
				AND ap.idPlan                      = ?2
				AND ap.idEstado     = ?4
				AND ap.fechaVigencia     <= ?5
				ORDER BY
				ap.fechaVigencia DESC
				")->setMaxResults('1');
            $query->setParameter(1, $oAccionClinica->getid());
            $query->setParameter(2, $plan);
            $query->setParameter(4, $oEstadoAct);
            $query->setParameter(5, $fechahoy);
            $resultado = $query->getResult();

            $idPrestacion = $oAccionClinica->getid();


            if (!$resultado) {
                $aAccionClinica = array();
                $valorNulo = "0";
                $idad = "";
                $pabellon = "";
                $nombreItem = "";
                $oAccionClinica = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);
                $aAccionClinicaCero = array(
                    'id' => $oAccionClinica->getId(),
                    'nombre' => $oAccionClinica->getNombreAccionClinica(),
                    'codigo' => $oAccionClinica->getCodigoAccionClinica(),
                    'codigoFonasa' => $oAccionClinica->getCodigoFonasa(),
                    'precio' => $valorNulo,
                    'idad' => $idad,
                    'nombreItem' => $nombreItem,
                    'pabellon' => $pabellon

                );
                $aAccionClinica[] = $aAccionClinicaCero;

            } else {

                $idAbiertaPrecio = $resultado[0]["id"];
                $oAbiertaDistribucion = $em->getRepository('RebsolHermesBundle:AbiertaDistribucion')->findBy(array("idPrecio" => $idAbiertaPrecio, "idEstado" => $oEstadoAct));
                $ItemsArray = array();
                foreach ($oAbiertaDistribucion as $caca) {
                    $ItemsArray[] = $caca->getid();
                }
                $idUser = $this->getUser();
                $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($idUser);
                $oRelTipoAtencion = $em->getRepository('RebsolHermesBundle:RelSucursalTipoAtencion')->findOneBy(array('idSucursal' => $SucursalUsuario->getId(), 'idTipoAtencion' => $TipoAtencion, 'idEstado' => $oEstadoAct->getId()));
                $TipoAtencion = $oRelTipoAtencion->getId();
                $aAccionClinica = $fService->ObtenerHonario($idPrestacion, $idAbiertaPrecio, $plan, $fechahoy, $oEstadoAct, $ItemsArray, $iPabellon, $idScurusal, $this->getSession('prevision'));

            }

            if (!empty($aAccionClinica)) {
                return new Response(json_encode($aAccionClinica));
            } else {
                return new Response("nohonorario");
            }
        } else {
            return new Response("nosubempresa");
        }


    }

    public function buscaPrestacionSubEmpresaDesdeAgendaAction()
    {

    }

    public function reservarFolioAction($agendamientoWeb = null)
    {
        $em = $this->getDoctrine()->getManager();
        $folioGlobal = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');

        if ($agendamientoWeb === null) {
            $idPrestacion = $this->container->get('request_stack')->getCurrentRequest()->query->get('idPrestacion');
            $oAccionClinicaNueva = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);
            $oSubEmpresa = $em->getRepository('RebsolHermesBundle:SubEmpresa')->find($oAccionClinicaNueva->getIdSubEmpresa()->getId());
            $oEmpresa = $oSubEmpresa->getIdEmpresa();
        } else {
            $idPrestacion = $agendamientoWeb['idPrestacion'];
            $oEmpresa = $agendamientoWeb['oEmpresa'];
            $oAccionClinicaNueva = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($idPrestacion);
        }


        if ($folioGlobal['valor'] === '1') {

            $listaPrestaciones = $this->ajax('ListaPrestacion');
            if (is_null($listaPrestaciones) || is_null($this->getSession('folioReservados'))) {
                $folio = $this->verificarFoliosDisponibles($oAccionClinicaNueva, 0, $oEmpresa);
                return new JsonResponse($folio);
            } else {
                $nuevoFolio = true;
                foreach ($listaPrestaciones as $value) { //$value[0] = idPrestacion
                    $oAccionClinicaListada = $em->getRepository('RebsolHermesBundle:AccionClinica')->find($value[0]);
                    if ($oAccionClinicaNueva->getIdEmpresa() === $oAccionClinicaListada->getIdEmpresa()) {
                        $nuevoFolio = false;
                    }
                }
                if ($nuevoFolio) {
                    $folio = $this->verificarFoliosDisponibles($oAccionClinicaNueva, count($listaPrestaciones), $oEmpresa);
                    return new JsonResponse($folio);
                }
            }


            return new JsonResponse(array(
                    'subempresa' => $oAccionClinicaNueva->getIdEmpresa()->getNombreEmpresa(),
                    'message' => 'noseagregafolio')
            );

        } else {
            return new JsonResponse(array(
                    'subempresa' => $oAccionClinicaNueva->getIdEmpresa()->getNombreEmpresa(),
                    'message' => 'noseagregafolio')
            );
        }
    }

    private function verificarFoliosDisponibles($oAccionClinicaNueva, $countListaPrestaciones, $oEmpresa)
    {
        $this->killSession('folioReservados');
        $talonarios = $this->extraerTalonarios($oAccionClinicaNueva);
        // Si existe problema que no hayan talonarios. Si hay talonarios disponibles ocupar el mínimo folio disponible
        if ($talonarios['resultado'] !== false) {
            $folios = $this->extraerFolios($talonarios['talonarioDisponible'], $oAccionClinicaNueva, $countListaPrestaciones);
            // Si existe problema que no hayan folios disponibles
            if ($folios['resultado'] !== false) {
                // Existen folios disponibles menor al minimo disponible
                if (!empty($folios['folioLimite'])) {
                    return array(
                        'subempresa' => $this->documentoNoDisponible($folios['folioLimite'], $oEmpresa),
                        'message' => 'alertaminimofolios'
                    );
                } else {
                    return array(
                        'subempresa' => $folios['folioLimite'],
                        'message' => 'folioreservado'
                    );
                }
            }
            return array(
                'subempresa' => $this->documentoNoDisponible($folios['folioNoDisponible'], $oEmpresa),
                'message' => 'nohayfolios'
            );
        } else {
//            return $this->getSession('folioReservados');
            return array(
                'subempresa' => $this->documentoNoDisponible($talonarios['talonarioNoDisponible'], $oEmpresa),
                'message' => 'nohaytalonario'
            );
        }
    }

    public function obtenerCantidadFoliosUltimoTalonario($talonarioDisponibles)
    {
        $em = $this->getDoctrine()->getManager();
        $ultimoTalonario = count($talonarioDisponibles) - 1;
        $idUltimoTalonario = $talonarioDisponibles[$ultimoTalonario]->getId();

        $folioDisponibles = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
            ->findBy(
                array('idTalonario' => $idUltimoTalonario,
                    'idEstadoTalonarioDetalle' => $this->container->getParameter('EstadoTalonarioDetalle.disponible')
                ));

        $cantidad = $folioDisponibles !== null ? count($folioDisponibles) : 0;
        return $cantidad;

    }

    public function disponibilizarFolioAction()
    {
        $em = $this->getDoctrine()->getManager();
        $folioGlobal = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');

        if ($folioGlobal['valor'] === '1') {
            $folioReservados = $this->getSession('folioReservados');

            if (!empty($folioReservados)) {
                $estadoDisponible = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
                    ->find($this->container->getParameter('EstadoTalonarioDetalle.disponible'));
                $oFecha = new \DateTime("now");
                foreach ($folioReservados as $value) {
                    $folioDisponible = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')->find($value);
                    $folioDisponible->setIdEstadoTalonarioDetalle($estadoDisponible);
                    $folioDisponible->setFechaDetalleBoleta($oFecha);
                    $em->persist($folioDisponible);
                }
                $em->flush();
            }
        }
        return 'success';
    }

    public function enviarAlarmaCorreoFolio($oEmpresa)
    {

        $em = $this->getDoctrine()->getManager();
        $oParametroCorreo = $em->getRepository("RebsolHermesBundle:Parametro")->obtenerParametrosAces($oEmpresa->getId());

        if ($oParametroCorreo['FOLIO_GLOBAL_CORREO'] !== '0') {

            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
            $mail->CharSet = 'UTF-8';
            $mail->Host = $oParametroCorreo['EMAIL_CONFIRMACION_HOST'];
            $mail->Port = $oParametroCorreo['EMAIL_CONFIRMACION_PORT'];
            $mail->SMTPAuth = true;
            $mail->Username = $oParametroCorreo['EMAIL_CONFIRMACION_USERNAME'];
            $mail->Password = $oParametroCorreo['EMAIL_CONFIRMACION_PASSWORD'];
            $mail->SetFrom($oParametroCorreo['EMAIL_CONFIRMACION_USERNAME'], $oEmpresa->getNombreEmpresa());

            $correos = $this->getCorreosAviso($oParametroCorreo['FOLIO_GLOBAL_CORREO']);
            foreach ($correos as $correo) {
                $mail->AddAddress($correo);
            }

            $mail->Subject = 'Alarma de Folios';
            $mail->MsgHTML('Se están acabando los folios para ' . $oEmpresa->getNombreEmpresa() . '. Por favor agregar nuevo talonario');
            $mail->AltBody = 'Se están acabando los folios para ' . $oEmpresa->getNombreEmpresa() . '. Por favor agregar nuevo talonario';

            return $mail->Send();
        }
        return false;
    }

    public function getCorreosAviso($aCorreos)
    {
        $aCorreos = trim($aCorreos);
        $correos = explode(",", $aCorreos);
        return $correos;
    }

    public function obtenerFolio($talonarioDisponibles, $codigoSii, $oAccionClinicaNueva)
    {
        $oFecha = new \DateTime("now");
        $em = $this->getDoctrine()->getManager();
        $alerta = false;
        $tipoTalonario = '';
        $folioDisponible = null;
        foreach ($talonarioDisponibles as $value) {
            $folioReservadoFueraPlazo = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
                ->ObtenerFoliosReservadoFueraTiempo(
                    $value->getId(),
                    $this->container->getParameter('EstadoTalonarioDetalle.reservada'));

            if (!empty($folioReservadoFueraPlazo)) {
                //Se busca un folio que haya pasado el plazo máximo como reservado
                $folioDisponible = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
                    ->find($folioReservadoFueraPlazo[0]['id']);
            } else {
                //Sino se busca el mínimo folio disponible de un talonario
                $folioDisponible = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
                    ->findOneBy(
                        array('idTalonario' => $value->getId(),
                            'idEstadoTalonarioDetalle' => $this->container->getParameter('EstadoTalonarioDetalle.disponible'),
                        ));
            }
            if (!empty($folioDisponible)) {
                $estadoReservada = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
                    ->find($this->container->getParameter('EstadoTalonarioDetalle.reservada'));
                $folioDisponible->setIdEstadoTalonarioDetalle($estadoReservada);
                $folioDisponible->setFechaDetalleBoleta($oFecha);
                $folioDisponible->setIdSubEmpresaFacturadora($oAccionClinicaNueva->getIdEmpresa()->getId());
                $em->persist($folioDisponible);
                $em->flush();

                //Buscar la cantidad de folios dispobibles sobre el último talonario
                $minimoFolioGlobal = $em->getRepository('RebsolHermesBundle:Parametro')
                    ->obtenerParametro('MINIMO_FOLIO_GLOBAL');

                $cantidadFoliosUltimoTalonario = $this->obtenerCantidadFoliosUltimoTalonario($talonarioDisponibles);

                if ($cantidadFoliosUltimoTalonario < intval($minimoFolioGlobal['valor'])) {
                    $oTipoDocumento = $em->getRepository('RebsolHermesBundle:TipoDocumento')
                        ->findOneBy(array('codigoSii' => $codigoSii));
                    $tipoTalonario = $oTipoDocumento->getNombre();
                    $alerta = true;
                }
                return array(
                    'folioDisponible' => $folioDisponible->getId(),
                    'tipoTalonario' => $tipoTalonario,
                    'codigoSii' => $codigoSii,
                    'alerta' => $alerta);
            }
        }
        if ($folioDisponible === null) {
            return $folioDisponible;
        }
        return $folioDisponible;
    }


    public function extraerTalonarios($oAccionClinicaNueva)
    {
        if(!$oAccionClinicaNueva->getEsAfecto()){
            return $this->obtenerTalonarioPorCodigo($oAccionClinicaNueva, '41'); //TODO: CAMBIAR a variable
        }else{
            return $this->obtenerTalonarioPorCodigo($oAccionClinicaNueva, '39');
        }
    }


    public function obtenerTalonarioPorCodigo($oAccionClinicaNueva, $codigoSii)
    {
        $em = $this->getDoctrine()->getManager();
        //buscar si talonario ya fue reservado según código
        $oEstado = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));

        $oTipoDocumento = $em->getRepository('RebsolHermesBundle:TipoDocumento')
            ->findOneBy(array('codigoSii' => $codigoSii));
        $oRelEmpresaTipoDocumento = $em->getRepository('RebsolHermesBundle:RelEmpresaTipoDocumento')
            ->findOneBy(array('idTipoDocumento' => $oTipoDocumento->getId()));
        $estadoActivoPila = $this->container->getParameter('EstadoPila.activo');
        //buscar si accion clinica es afecta o exenta y reservar folio corespondiente
        $talonarioDisponibles = $em->getRepository('RebsolHermesBundle:Talonario')
            ->obtenerTalonariosPorAccionClinica($oAccionClinicaNueva, $oEstado, $oRelEmpresaTipoDocumento, $estadoActivoPila);
        $aTalonarioDisponibles = array();
        if (!empty($talonarioDisponibles)) {
            $aTalonarioDisponibles[0]['codigoSii'] = $codigoSii;
            $aTalonarioDisponibles[0]['talonarios'] = $talonarioDisponibles;

        }

        return array(
            'talonarioDisponible' => $aTalonarioDisponibles,
            'talonarioNoDisponible' => $oTipoDocumento->getNombre(),
            'resultado' => !empty($talonarioDisponibles));

    }

    public function extraerFolios($talonarios, $oAccionClinicaNueva, $countListaPrestaciones)
    {
        $em = $this->getDoctrine()->getManager();
        $aFoliosNoDisponible = array();
        $aFoliosLimite = array();
        $i = 0;
        $cantidadFolios = count($talonarios);
        $aFolios = array();
        foreach ($talonarios as $talonario) {
            $folio = $this->obtenerFolio($talonario['talonarios'], $talonario['codigoSii'], $oAccionClinicaNueva);
            $oTipoDocumento = $em->getRepository('RebsolHermesBundle:TipoDocumento')
                ->findOneBy(array('codigoSii' => $talonario['codigoSii']));
            if (empty($folio)) {
                $aFoliosNoDisponible[] = $oTipoDocumento->getNombre();
                $this->killSession('folioReservados');

            } else {
                $aFolios[] = $folio;
                if ($folio['alerta'] === true) {
                    $aFoliosLimite[] = $oTipoDocumento->getNombre();
                }
                $i++;
            }
        }

        $this->tomarFolio($countListaPrestaciones, $aFolios);
        return
            array(
                'folioLimite' => $aFoliosLimite,
                'folioNoDisponible' => $aFoliosNoDisponible,
                'resultado' => $cantidadFolios === $i);
    }

    public function tomarFolio($countListaPrestaciones, $aFolio)
    {
        //Guarda los folios agregados por sessión al momento de realizar el pago
        $folioReservados = $this->getSession('folioReservados');
        if ($countListaPrestaciones === 0) {
            $this->killSession('folioReservados');
            $this->setSession('folioReservados', $aFolio);
        } else {
            $aFolios = array_merge($folioReservados, $aFolio);
            $this->setSession('folioReservados', $aFolios);
        }
    }

    public function documentoNoDisponible($documentos, $oEmpresa)
    {
        $documentoNoDisponible = '';
        $i = 0;
        foreach ((array)$documentos as $documento) {
            $documentoNoDisponible = ($i === 0) ? $documento : $documentoNoDisponible . ' - ' . $documento;
            $i++;
        }

        if ($oEmpresa === null) {
            $this->enviarAlarmaCorreoFolio($this->ObtenerEmpresaLogin());
        } else {
            $this->enviarAlarmaCorreoFolio($oEmpresa);
        }

        return $documentoNoDisponible;
    }

}
