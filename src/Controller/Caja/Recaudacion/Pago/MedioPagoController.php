<?php

namespace App\Controller\Caja\Recaudacion\Pago;

// use Rebsol\HermesBundle\Controller\Caja\Recaudacion\RecaudacionController;


use App\Form\Recaudacion\Pago\MediosPagoType;
use App\Controller\Caja\RecaudacionController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author ovaldenegro
 * @version 1.0.0
 * Fecha Creación: 18/12/2013
 */
class MedioPagoController extends RecaudacionController {

	public Function nuevoFormDinamicoAction()
	{
		$em                   = $this->getDoctrine()->getManager();
		$rRepository          = $this->getDoctrine()->getRepository("RebsolHermesBundle:FormaPago");
		$idCantidad           = $this->container->get('request_stack')->getCurrentRequest()->query->get('cantidad');
		$FormaPago            = $this->container->get('request_stack')->getCurrentRequest()->query->get('idMedioPago');
		$oEmpresa             = $this->ObtenerEmpresaLogin();
		$oEstado              = $em->getRepository('RebsolHermesBundle:Estado')->find($this->container->getParameter('estado_activo'));
		$ListadoOtrosMedios   = $rRepository->ListadoFormasDePagoParaOtrosMedios($oEmpresa, $oEstado);
		$arrayOtrosFormasPago = array();

		foreach ($ListadoOtrosMedios as $id)
		{
			$arrayOtrosFormasPago[] = $id['id'];
		}
		$MediosPagoform = $this->createForm(MediosPagoType::class, null, array(
			'validaform' => null,
			'idFrom' => $FormaPago,
			'idCantidad' => $idCantidad,
			'clone' => true,
			'nuevo' => false,
			'sucursal' => null,
			'idFromOtros' => $arrayOtrosFormasPago,
			'iEmpresa' => $oEmpresa->getId(),
			'estado_activado' => $this->container->getParameter('estado_activo'),
			));

		$querymp = $em->createQuery("
			SELECT
			fp.id  as id
			,fp.nombre as nombre
			FROM
			Rebsol\HermesBundle\Entity\FormaPagoTipo fpt
			LEFT JOIN Rebsol\HermesBundle\Entity\FormaPago fp
			WITH fp.idTipoFormaPago = fpt.id
			WHERE 1=1
			AND fp.id                      = ?3
			AND fpt.idEstado          = ?1
			AND fp.idEmpresa      = ?2
			")->setMaxResults('1');
		$querymp->setParameter(1, $oEstado);
		$querymp->setParameter(2, $oEmpresa);
		$querymp->setParameter(3, $FormaPago);
		$ListadoMediosPago = $querymp->getResult();



		$querytfp = $em->createQuery("
			SELECT
			fpt.id  as id
			FROM
			Rebsol\HermesBundle\Entity\FormaPagoTipo fpt
			LEFT JOIN Rebsol\HermesBundle\Entity\FormaPago fp
			WITH fp.idTipoFormaPago = fpt.id
			WHERE 1=1
			AND fp.id                      = ?3
			")->setMaxResults('1');
		$querytfp->setParameter(3, $FormaPago);

		$TipoPago = $querytfp->getSingleResult();

		switch ($TipoPago['id'])
		{

			case 3:

			return $this->render('RecaudacionBundle:FormasDePago:FormaDePago_BonoElectronico.html.twig', array(
				'mediospago_form' => $MediosPagoform->createView(),
				'listadoMediosPago' => array_shift($ListadoMediosPago),
				'cantidad' => $idCantidad,
				));
			break;

			case 5:

			return $this->render('RecaudacionBundle:FormasDePago:FormaDePago_BonoManual.html.twig', array(
				'mediospago_form' => $MediosPagoform->createView(),
				'listadoMediosPago' => array_shift($ListadoMediosPago),
				'cantidad' => $idCantidad,
				));

			break;

			case 7:

			return $this->render('RecaudacionBundle:FormasDePago:FormaDePago_ChequeFecha.html.twig', array(
				'mediospago_form' => $MediosPagoform->createView(),
				'listadoMediosPago' => array_shift($ListadoMediosPago),
				'cantidad' => $idCantidad,
				));

			break;

			case 8:

            return $this->render('RecaudacionBundle:FormasDePago:FormaDePago_ChequeDia.html.twig', array(
                'mediospago_form' => $MediosPagoform->createView(),
                'listadoMediosPago' => array_shift($ListadoMediosPago),
                'cantidad' => $idCantidad,
            ));
            break;

            case 13:

                return $this->render('RecaudacionBundle:FormasDePago:FormaDePago_Transbank.html.twig', array(
                    'mediospago_form' => $MediosPagoform->createView(),
                    'listadoMediosPago' => array_shift($ListadoMediosPago),
                    'cantidad' => $idCantidad,
                ));
                break;
		}
	}

	public Function cajaValmacenaTotalSumaAction()
	{
		$vSumaCantidad = $this->container->get('request_stack')->getCurrentRequest()->query->get('vSumaCantidad');
		$this->get('session')->set('vSumaCantidad', $vSumaCantidad);
		$SumaCantidad  = $this->get('session')->get('vSumaCantidad');
		return new JsonResponse(intval($SumaCantidad));
	}

	public Function cajaValorPagarCompararTabsAction()
	{
		$valorPagar = $this->container->get('request_stack')->getCurrentRequest()->query->get('Valor_Pagar');
		$valorPagarSesion = $this->get('session')->get('Valor_Pagar');

		if (intVal($valorPagar) === intVal($valorPagarSesion)) {
			if(intVal($valorPagar) == 0){
				$this->killSession('idSubEmpresa');
			}
			return new JsonResponse(intVal($valorPagarSesion));
		} else {
			if(intVal($valorPagar) == 0){
				$this->killSession('idSubEmpresa');
			}
			return new JsonResponse(intVal($valorPagarSesion));
		}
	}

	public Function cajaValorPagarAction() {

		$valorPagar   = $this->container->get('request_stack')->getCurrentRequest()->query->get('Valor_Pagar');
		$SumaCantidad = $this->get('session')->get('vSumaCantidad');

		$this->get('session')->set('Valor_Pagar', $valorPagar);

		$valorPagarSesion = $this->get('session')->get('Valor_Pagar');

		if (intVal($SumaCantidad) !== intVal($valorPagarSesion)) {

			if(intVal($SumaCantidad) == 0){
				$this->killSession('idSubEmpresa');
			}

			$valorPagar = intVal($SumaCantidad);
			return new JsonResponse($valorPagar);

		} else {

			if(intVal($SumaCantidad) == 0){
				$this->killSession('idSubEmpresa');
			}

			return new JsonResponse(intval($valorPagar));
		}
	}

    public Function cajaValorPagarPagoCuentaAction() {

        $valorPagar   = $this->container->get('request_stack')->getCurrentRequest()->query->get('Valor_Pagar');

        $this->get('session')->set('Valor_Pagar', $valorPagar);

        return new JsonResponse($valorPagar);

    }

	public Function CMPVPAction(){

		$VLS        = $this->container->get('request_stack')->getCurrentRequest()->query->get('val_ALS');
		$VPS        = $this->get('session')->get('Valor_Pagar');
		$SC         = $this->get('session')->get('vSumaCantidad');
		$cajaValida = $this->get('Caja_valida')->vap($VLS, $VPS,  $SC);

		return new JsonResponse($cajaValida);
	}

    public Function validaBonoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $numeroDocumentoGeneral = $request->get('numeroDocumentoGeneral');
        $idFormaPago = $request->get('idFormaPago');

        $oExisteBono  = $em->getRepository('RebsolHermesBundle:DocumentoPago')->findBy(
            array(
                'numeroDocumentoGeneral' => $numeroDocumentoGeneral,
                'idFormaPago' => $idFormaPago
            )
        );

        $bExisteBono = true;
        if (empty($oExisteBono)) {
            $bExisteBono = false;
        }
        return new JsonResponse(array('existe' => $bExisteBono));
    }

}
