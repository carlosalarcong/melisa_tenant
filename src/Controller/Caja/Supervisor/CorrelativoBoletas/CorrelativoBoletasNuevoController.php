<?php

namespace App\Controller\Caja\Supervisor\CorrelativoBoletas;

use Rebsol\HermesBundle\Entity\Talonario;
use Rebsol\HermesBundle\Entity\TalonarioDetalle;
use App\Controller\Caja\Supervisor\SupervisorController;
use App\Form\Supervisor\CorrelativoBoletas\CorrelativoBoletasType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CorrelativoBoletasNuevoController extends SupervisorController
{

    /**
     * @param Request $request .
     * @return Response()
     * Descripción: nuevoAction() Muestra el formulario para crear un nuevo talonario
     */
    public function nuevoAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $oEmpresa = $this->ObtenerEmpresaLogin();

        $this->ValidadPeticionAjax($request, 'Supervisor_CorrelativoBoletas');
        //Obtenemos el objeto de la sucursal según el usuario
        $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser());
        $talonario = new Talonario();

        $folio = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');

        $form = $this->createForm(CorrelativoBoletasType::class, $talonario,
            array(
                'isNew' => true,
                'oSucursal' => $SucursalUsuario,
                'estado_activado' => $this->container->getParameter('estado_activo'),
                'oEmpresa' => $oEmpresa,
                'database_default' => $this->obtenerEntityManagerDefault(),
                'folio' => $folio['valor']
            )
        );


        $renderView = $this->renderView('RecaudacionBundle:Supervisor/CorrelativoBoletas:new.html.twig',
            array(
                'talonario' => $talonario,
                'form' => $form->createView(),
                'folio_global' => $folio['valor']
            )
        );
        return new Response($renderView);
    }

    /**
     * @param Request $request .
     * @return Response()
     * Descripción: numeroPila() Genera un nuevo número de pila para cuando queremos ingresar un nuevo talonario
     */
    public function numeroPilaAction(Request $request)
    {

        //Traemos el tipo de documento desde ajax
        $TipoDocumento = $request->query->get('RelEmpresaTipoDocumento');
        $SubEmpresa = $request->query->get('SubEmpresa');

        $em = $this->getDoctrine()->getManager();

        //Obtenemos el objeto de la sucursal según el usuario
        $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser());

        //Llamamos a la función numeroPila del repositorio Talonario para que genere el número de pila para el nuevo talonario.
        $numeroPila = $em->getRepository('RebsolHermesBundle:Talonario')->
        numeroPila(
            $TipoDocumento,
            $this->container->getParameter('estado_activo'),
            $SucursalUsuario->getId(),
            $SubEmpresa
        );
        //var_dump($numeroPila);exit;

        //Si el tipo de documento aún no existe, al valor null se le asigna la pila 1
        if ($numeroPila[0]['pila'] == null) {
            $numeroPila[0]['pila'] = 1;
        }

        //Regresamos la respuesta al ajax
        return new Response(json_encode($numeroPila[0]['pila']));
    }


    /**
     * @param Request $request .
     * @return Response()
     * Descripción: generaNumeroDeInicioAction() Proporciona el número de inicio de un documento siguiendo su correlatividad o comenzando desde 1 si es nuevo.
     */
    public function generaNumeroDeInicioAction(Request $request)
    {

        //Traemos el tipo de documento desde ajax
        $idSucursal = $request->query->get('idSucursal');
        $idSubEmpresa = $request->query->get('idSubEmpresa');
        $idRelEmpresaTipoDocumento = $request->query->get('idRelEmpresaTipoDocumento');

        $em = $this->getDoctrine()->getManager();

        //Llamamos a la función numeroPila del repositorio Talonario para que genere el número de pila para el nuevo talonario.
        $numeroInicio = $em->getRepository('RebsolHermesBundle:Talonario')->
        numeroInicio(
            $idSucursal,
            $idSubEmpresa,
            $idRelEmpresaTipoDocumento
        );


        //Si la consulta no arroja resultados, se asigna como número de inicio el 1 porque seguramente es un documento nuevo.
        if ($numeroInicio[0]['numeroDeTermino'] == null) {
            $numeroInicio[0]['numeroDeTermino'] = 1;
        }


        //Regresamos la respuesta al ajax
        return new Response(json_encode($numeroInicio[0]['numeroDeTermino']));

    }


    /**
     * @param Request $request .
     * @return Response()
     * Descripción: crearAction() Valida el formulario para crear un nuevo Nivel Instrucción
     */
    public function crearAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $this->ValidadPeticionAjax($request, 'Supervisor_CorrelativoBoletas');
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $talonario = new Talonario();
        $sCommon = $this->get('common');
        $talonario->setIdEstado($sCommon->obtenerEstado());
        $sMantenedores = $this->get('mantenedores');

        //Obtenemos el objeto de la sucursal según el usuario
        $SucursalUsuario = $em->getRepository('RebsolHermesBundle:UsuariosRebsol')->obtenerSucursalUsuario($this->getUser());

        $folio = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');

        $form = $this->createForm(CorrelativoBoletasType::class, $talonario,
            array(
                'isNew' => true,
                'oSucursal' => $SucursalUsuario,
                'estado_activado' => $this->container->getParameter('estado_activo'),
                'oEmpresa' => $oEmpresa,
                'database_default' => $this->obtenerEntityManagerDefault(),
                'folio' => $folio['valor']
            )
        );

        $form->handleRequest($request);

        //Se recuperan los datos del Estado
        $oEstado = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));

        //Validación para que el número de término sea mayor al número de inicio
        $numeroDeInicio = $form['numeroInicio']->getData();
        $numeroDeTermino = $form['numeroTermino']->getData();


        if ($numeroDeTermino < $numeroDeInicio) {
            $form['numeroTermino']->addError(new FormError("Número de Término no debe ser menor al Número de inicio"));
        }

        if ($form['numeroPila']->getData() != null &&
            $form['idSucursal']->getData() != null &&
            $form['idEstadoPila']->getData() != null &&
            //$form['idUbicacionCaja']->getData() != null &&
            $form['idRelEmpresaTipoDocumento']->getData() != null &&
            $form['idSubEmpresa']->getData() != null) {

            $idTipoDocumento = $form['idRelEmpresaTipoDocumento']->getData();
            $idSubEmpresa = $form['idSubEmpresa']->getData();

            $TipoDocumento = $idTipoDocumento->getId();
            $SubEmpresa = $idSubEmpresa->getId();

            //Valida de que los datos ingresados, no se repitan en otros talonarios
            $consultaIntervalo = $em->getRepository('RebsolHermesBundle:Talonario')->
            ConsultaIntervaloCrear(
                $SubEmpresa,
                $TipoDocumento,
                $this->container->getParameter('estado_activo'),
                $numeroDeInicio,
                $numeroDeTermino
            );

            //Si encuentra alguna respuesta, es porque hay alguna coincidencia y arroja una respuesta de error.
            if (count($consultaIntervalo) >= 1) {
                $form['numeroTermino']->addError(new FormError("No puede ingresar este Talonario, ya que el Intervalo " . $numeroDeInicio . "-" . $numeroDeTermino . " existe"));
            }

        }

        if ($this->ValidarFormulario($form)) {

            $idUbicacionCaja = isset($form['idUbicacionCaja']) ? $form['idUbicacionCaja']->getData() : null;//$form['idUbicacionCaja']->getData();
            $sucursal = $form['idSucursal']->getData();
            $idTipoDocumento = $form['idRelEmpresaTipoDocumento']->getData();
            $numeroPila = $form['numeroPila']->getData();
            $idEstadoPila = $form['idEstadoPila']->getData();
            $fechaEntrega = $form['fechaEntrega']->getData();
            $numeroInicio = $form['numeroInicio']->getData();
            $numeroTermino = $form['numeroTermino']->getData();
            $numeroActual = $form['numeroActual']->getData();
            $subEmpresa = $form['idSubEmpresa']->getData();
            $fechaCreacion = new \DateTime(date("Y-m-d"));

            //Creamos los objetos para que provengan desde el Entity Manager y así evitar problemas con SQL Server.
            $objUbicacionCaja = isset($form['idUbicacionCaja']) ? $em->getRepository('RebsolHermesBundle:UbicacionCaja')->find($idUbicacionCaja->getId()) : null;
            $objSubEmpresa = $em->getRepository('RebsolHermesBundle:SubEmpresa')->find($subEmpresa->getId());
            $objSucursal = $em->getRepository('RebsolHermesBundle:Sucursal')->find($sucursal->getId());
            $objRelEmpresaTipoDocumento = $em->getRepository('RebsolHermesBundle:RelEmpresaTipoDocumento')->find($idTipoDocumento->getId());
            $objEstadoPila = $em->getRepository('RebsolHermesBundle:EstadoPila')->find($idEstadoPila->getId());

            $Talonario = new Talonario();
            $Talonario->setIdSubEmpresa($objSubEmpresa);
            $Talonario->setIdUbicacionCaja($objUbicacionCaja);
            $Talonario->setIdSucursal($objSucursal);
            $Talonario->setIdRelEmpresaTipoDocumento($objRelEmpresaTipoDocumento);
            $Talonario->setIdEstadoPila($objEstadoPila);
            $Talonario->setIdEstado($oEstado);
            $Talonario->setNumeroPila($numeroPila);
            $Talonario->setFechaEstadoPila($fechaCreacion);
            $Talonario->setFechaEntrega($fechaEntrega);
            $Talonario->setNumeroInicio($numeroInicio);
            $Talonario->setNumeroTermino($numeroTermino);
            $Talonario->setNumeroActual($numeroActual);

            $folio = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');

            if($folio['valor'] === '1'){
                $oEstadoTalonarioDetalle = $em->getRepository("RebsolHermesBundle:EstadoTalonarioDetalle")
                    ->find($this->container->getParameter('EstadoTalonarioDetalle.disponible'));

                for ($i = $numeroInicio; $i <= $numeroTermino; $i++) {
                    $talonarioDetalle = new TalonarioDetalle();
                    $talonarioDetalle->setIdEstadoTalonarioDetalle($oEstadoTalonarioDetalle);
                    $talonarioDetalle->setFechaDetalleBoleta(new \DateTime());
                    $talonarioDetalle->setIdTalonario($Talonario);
                    $talonarioDetalle->setNumeroDocumento($i);
                    $em->persist($talonarioDetalle);
                }
            }

            $em->persist($Talonario);
            $em->flush();

            $renderView = "Creado";
            return new Response($renderView);
        }

        $renderView = $this->renderView('RecaudacionBundle:Supervisor/CorrelativoBoletas:new.html.twig',
            array(
                'talonario' => $talonario,
                'form' => $form->createView(),
                'folio_global' => $folio['valor']
            )
        );
        return new Response($renderView);
    }

}