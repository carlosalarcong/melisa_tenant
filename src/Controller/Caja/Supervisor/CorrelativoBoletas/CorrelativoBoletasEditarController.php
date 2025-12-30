<?php

namespace App\Controller\Caja\Supervisor\CorrelativoBoletas;

use App\Entity\TalonarioDetalle;
use App\Controller\Caja\Supervisor\SupervisorController;
use App\Form\Supervisor\CorrelativoBoletas\CorrelativoBoletasEditarType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CorrelativoBoletasEditarController extends SupervisorController
{

    /**
     * @param Request $request .
     * @return Response()
     * Descripción: nuevoAction() Muestra el formulario para crear un nuevo talonario
     */
    public function editarAction(Request $request, $idTalonario)
    {

        $this->ValidadPeticionAjax($request, 'Supervisor_CorrelativoBoletas');

        $oEmpresa = $this->ObtenerEmpresaLogin();
        $em = $this->getDoctrine()->getManager();
        $oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->find($idTalonario);
        $idEstadoPilaOriginal = $oTalonario->getIdEstadoPila()->getId();
        $numeroTerminoOriginal = $oTalonario->getNumeroTermino();
        $numeroInicioOriginal = $oTalonario->getNumeroInicio();


        $edit_form = $this->createForm(CorrelativoBoletasEditarType::class, $oTalonario,
            array(
                'isNew' => true,
                'oEmpresa' => $oEmpresa,
                'estado_activado' => $this->container->getParameter('estado_activo'),
                'idEstadoPilaOriginal' => $idEstadoPilaOriginal,
                'numeroTerminoOriginal' => $numeroTerminoOriginal,
                'numeroInicioOriginal' => $numeroInicioOriginal,
                'database_default' => $this->obtenerEntityManagerDefault()

            )
        );

        $renderView = $this->renderView('RecaudacionBundle:Supervisor/CorrelativoBoletas:edit.html.twig',
            array(
                'oTalonario' => $oTalonario,
                'edit_form' => $edit_form->createView()
            )
        );
        return new Response($renderView);
    }

    /**
     * @param Request $request .
     * @return Response()
     * Descripción: crearAction() Valida el formulario para crear un nuevo Nivel Instrucción
     */
    public function actualizarAction(Request $request, $idTalonario)
    {

        $this->ValidadPeticionAjax($request, 'Supervisor_CorrelativoBoletas');
        $oEmpresa = $this->ObtenerEmpresaLogin();
        $em = $this->getDoctrine()->getManager();
        $oTalonario = $em->getRepository('RebsolHermesBundle:Talonario')->find($idTalonario);

        $folio = $em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FOLIO_GLOBAL');

        $idEstadoPilaOriginal = $oTalonario->getIdEstadoPila()->getId();
        $numeroTerminoOriginal = $oTalonario->getNumeroTermino();
        $numeroInicioOriginal = $oTalonario->getNumeroInicio();
        $idSubEmpresaOriginal = $oTalonario->getIdSubEmpresa()->getId();

        $edit_form = $this->createForm(CorrelativoBoletasEditarType::class, $oTalonario,
            array(
                'isNew' => true,
                'oEmpresa' => $oEmpresa,
                'estado_activado' => $this->container->getParameter('estado_activo'),
                'idEstadoPilaOriginal' => $idEstadoPilaOriginal,
                'numeroTerminoOriginal' => $numeroTerminoOriginal,
                'numeroInicioOriginal' => $numeroInicioOriginal,
                'idSubEmpresaOriginal' => $idSubEmpresaOriginal,
                'database_default' => $this->obtenerEntityManagerDefault()
            )
        );

        $edit_form->handleRequest($request);

        $idTipoDocumento = $oTalonario->getIdRelEmpresaTipoDocumento()->getId();
        $idSubEmpresa = $edit_form['idSubEmpresa']->getData();

        $numeroInicio = $edit_form['numeroInicio']->getData();
        $numeroTermino = $edit_form['numeroTermino']->getData();
        $numeroActual = $edit_form['numeroActual']->getData();

        //Obtenemos la fecha vigencia original para enviarlo a editarType para su validación
        $numeroTerminoOriginal = $edit_form['numeroTerminoOriginal']->getData();
        $numeroTerminoOriginal = (int)$numeroTerminoOriginal;

        //Obtenemos la fecha vigencia original para enviarlo a editarType para su validación
        $numeroInicioOriginal = $edit_form['numeroInicioOriginal']->getData();
        $numeroInicioOriginal = (int)$numeroInicioOriginal;

        if(($numeroActual < $numeroInicio) || ($numeroActual > $numeroTermino)){
            $edit_form['numeroActual']->addError(new FormError("Contador debe estar en el rango ". $numeroInicio . " y " .$numeroTermino));
        }

        //Valida de que los datos ingresados, no se repitan en otros talonarios
        $consultaIntervalo = $em->getRepository('RebsolHermesBundle:Talonario')->
        ConsultaIntervaloEditar(
            $oTalonario->getId(),
            $idTipoDocumento,
            $this->container->getParameter('estado_activo'),
            $numeroInicio,
            $numeroTermino
        );

        //Si encuentra alguna respuesta, es porque hay alguna coincidencia y arroja una respuesta de error.
        if (count($consultaIntervalo) >= 1) {
            $edit_form['numeroTermino']->addError(new FormError("No puede modificar este Talonario, ya que el Intervalo " . $numeroInicio . "-" . $numeroTermino . " existe tope con otros talonarios"));
        }

        if($folio['valor'] === '1'){

            //Valida que los datos que al modificar el talonario no hayan folios reservados u ocupados
            $estadoFolioOcupada = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
                ->find($this->container->getParameter('EstadoTalonarioDetalle.ocupada'));
            $estadoFolioReservada = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
                ->find($this->container->getParameter('EstadoTalonarioDetalle.reservada'));
            $estadosFolios = array($estadoFolioOcupada->getId(), $estadoFolioReservada->getId());

            $consultaFolios = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
                ->obtenerFoliosPorRangoYEstados($oTalonario->getId(), $estadosFolios, $numeroInicio, $numeroTermino);

            if (count($consultaFolios) >= 1) {
                $edit_form['numeroTermino']->addError(new FormError("No puede modificar este Talonario, ya que el Intervalo " . $numeroInicioOriginal . "-" . $numeroTerminoOriginal . " existe " . count($consultaFolios) . " folios reservados u ocupados que no se pueden eliminar"));
            }
        }

        if ($edit_form->isSubmitted() && $edit_form->isValid()) {

            if($folio['valor'] === '1') {
                //Eliminar los folios sólo en los bordes si o sólo si están disponibles
                $estadoFolioDisponible = $em->getRepository('RebsolHermesBundle:EstadoTalonarioDetalle')
                    ->find($this->container->getParameter('EstadoTalonarioDetalle.disponible'));
                $estadosFolios = array($estadoFolioDisponible->getId());
                $consultaFoliosAEliminar = $em->getRepository('RebsolHermesBundle:TalonarioDetalle')
                    ->obtenerFoliosPorRangoYEstados($oTalonario->getId(), $estadosFolios, $numeroInicio, $numeroTermino);

                if ($consultaFoliosAEliminar) {
                    foreach ($consultaFoliosAEliminar as $folio) {
                        $em->remove($folio);
                    }
                }

                //Agregar hacia la izquierda  $numeroTerminoOriginal
                $diferenciaIzquierda = $numeroInicioOriginal - $numeroInicio;
                if ($diferenciaIzquierda > 0) {
                    for ($i = $numeroInicio; $i < $numeroInicioOriginal; $i++) {
                        $oTalonarioDetalle = new TalonarioDetalle();
                        $oTalonarioDetalle->setIdEstadoTalonarioDetalle($estadoFolioDisponible);
                        $oTalonarioDetalle->setFechaDetalleBoleta(new \DateTime());
                        $oTalonarioDetalle->setIdTalonario($oTalonario);
                        $oTalonarioDetalle->setNumeroDocumento($i);
                        $em->persist($oTalonarioDetalle);
                    }
                }

                $diferenciaDerecha = $numeroTermino - $numeroTerminoOriginal;
                if ($diferenciaDerecha > 0) {
                    for ($i = $numeroTerminoOriginal + 1; $i <= $numeroTermino; $i++) {
                        $oTalonarioDetalle = new TalonarioDetalle();
                        $oTalonarioDetalle->setIdEstadoTalonarioDetalle($estadoFolioDisponible);
                        $oTalonarioDetalle->setFechaDetalleBoleta(new \DateTime());
                        $oTalonarioDetalle->setIdTalonario($oTalonario);
                        $oTalonarioDetalle->setNumeroDocumento($i);
                        $em->persist($oTalonarioDetalle);
                    }
                }
            }

            $EstadoPila = $edit_form['idEstadoPila']->getData();
            $numeroTermino = $edit_form['numeroTermino']->getData();
            $numeroActual = $edit_form['numeroActual']->getData();

            $oTalonario->setIdSubEmpresa($idSubEmpresa);
            $oTalonario->setIdEstadoPila($EstadoPila);
            $oTalonario->setNumeroTermino($numeroTermino);
            $oTalonario->setNumeroActual($numeroActual);

            $em->persist($oTalonario);
            $em->flush();

            $renderView = "Editado";
            return new Response($renderView);
        }

        $renderView = $this->renderView('RecaudacionBundle:Supervisor/CorrelativoBoletas:edit.html.twig',
            array(
                'oTalonario' => $oTalonario,
                'edit_form' => $edit_form->createView()
            )
        );
        return new Response($renderView);
    }

}
