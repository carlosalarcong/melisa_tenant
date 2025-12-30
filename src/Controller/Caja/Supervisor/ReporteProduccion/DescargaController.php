<?php

namespace App\Controller\Caja\Supervisor\ReporteProduccion;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Controller\Caja\Supervisor\SupervisorController;
use Rebsol\RegistroClinicoHospitalarioBundle\Repository\TrasladoRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;


class DescargaController extends SupervisorController
{
    public function excelAction($fechaIni, $fechaFin)
    {
        $sToolsService = $this->get('hermesTools.Tools');
        $sFunciones = $this->get('libreria_funciones');
        $em = $this->getDoctrine()->getManager();

        $fechaIniFiltro = $fechaIni . ' 00:00:00';
        $fechaFinFiltro = $fechaFin . ' 23:59:59';
        $nombreEmpresa = $this->getUser()->getIdPersona()->getIdEmpresa()->getNombreEmpresa();
        $fechaActual    = new \DateTime();

        $registros = $em->getRepository('RebsolHermesBundle:AccionClinicaPaciente')->obtieneInformeProduccion($fechaIniFiltro, $fechaFinFiltro);

        $excelService = new Spreadsheet();

        $excelService->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Sucursal')
            ->setCellValue('B1', 'Zona')
            ->setCellValue('C1', 'DCentro')
            ->setCellValue('D1', 'N° Admisión')
            ->setCellValue('E1', 'Rut Paciente')
            ->setCellValue('F1', 'Paciente')
            ->setCellValue('G1', 'Sexo')
            ->setCellValue('H1', 'Edad')
            ->setCellValue('I1', 'DOrden')
            ->setCellValue('J1', 'Codigo Prestacion Int')
            ->setCellValue('K1', 'DArtículo')
            ->setCellValue('L1', 'Cantidad')
            ->setCellValue('M1', 'Financiador')
            ->setCellValue('N1', 'Plan')
            ->setCellValue('O1', 'Código')
            ->setCellValue('P1', 'convenio')
            ->setCellValue('Q1', 'Convenio Empresa')
            ->setCellValue('R1', 'Rut Médico')
            ->setCellValue('S1', 'DMedico')
            ->setCellValue('T1', 'DEspecialidad')
            ->setCellValue('U1', 'DEmpresa')
            ->setCellValue('V1', 'N° Pago')
            ->setCellValue('W1', 'Fecha Ingreso')
            ->setCellValue('X1', 'Hora Ingreso')
            ->setCellValue('Y1', 'Fecha Alta')
            ->setCellValue('Z1', 'Hora Alta')
            ->setCellValue('AA1', 'Fecha Egreso')
            ->setCellValue('AB1', 'Hora Egreso')
            ->setCellValue('AC1', 'Nombre Cajero')
            ->setCellValue('AD1', 'Rut Cajero')
            ->setCellValue('AE1', 'Valor Prestación')
            ->setCellValue('AF1', 'Afecto')
            ->setCellValue('AG1', 'Exento')
            ->setCellValue('AH1', 'Total s/IVA')
            ->setCellValue('AI1', 'Total c/IVA')
            ->setCellValue('AJ1', 'Tipo Descuento')
            ->setCellValue('AK1', 'Diferencia')
            ->setCellValue('AL1', 'Horario')
            ->setCellValue('AM1', 'Atendido')
            ->setCellValue('AN1', 'Estado');

        $linea = 2;
        foreach ($registros as $registro) {
            $edadPaciente = array('');
            if (!is_null($registro['fechaNacimientoPaciente'])) {
               $edadPaciente = $sFunciones->devuelveEdad($registro['fechaNacimientoPaciente']->format('Y-m-d'), $fechaActual->format('Y-m-d'));
            }

            $datosCajero = $registro['nombrePnaturalCajero']
                .' '. $registro['apellidoPaternoCajero'] .' '.$registro['apellidoMaternoCajero'];

            $atendido = !is_null($registro['idReservaAtencion']) ? !is_null($registro['fechaAtencion']) ? 'Sí' : 'No' : 'S/A';

            $signoDiferencia = '+'; //sentido a favor
            if ($registro['idTipoSentidoDiferencia'] == 2 || $registro['idTipoSentidoDiferenciaGlobal'] == 2) { //sentido en contra
                $signoDiferencia = '-';
            }

            $oTraslado = $this->get("registroClinicoHospitalario.traslado")->obtenerTrasladoAlta($registro['idPaciente']);

            $fechaEgreso = empty($oTraslado) ? null :($oTraslado[0]['idTipoAlta']  ? $oTraslado[0]['fechaTraslado'] : null);

            $excelService->setActiveSheetIndex(0)
                ->setCellValue('A' . $linea, !is_null($registro['nombreSucursalAdm']) ? $registro['nombreSucursalAdm']: $registro['nombreSucursalResAten'])
                ->setCellValue('B' . $linea, $registro['ambito'])
                ->setCellValue('C' . $linea, $registro['ambito'])
                ->setCellValue('D' . $linea, $registro['numeroIngreso'])
                ->setCellValue('E' . $linea, $registro['rutPaciente'])
                ->setCellValue('F' . $linea, $registro['nombrePnaturalPaciente'] .' '. $registro['apellidoPaternoPaciente'] .' '.$registro['apellidoMaternoPaciente'])
                ->setCellValue('G' . $linea, $registro['sexo'])
                ->setCellValue('H' . $linea, $edadPaciente[0])
                ->setCellValue('I' . $linea, $registro['tipoPrestacion'])
                ->setCellValue('J' . $linea, $registro['codigoPrestacion'])
                ->setCellValue('K' . $linea, $registro['nombreAccionClinica'])
                ->setCellValue('L' . $linea, $registro['cantidad'])
                ->setCellValue('M' . $linea, $registro['nombrePrevision'])
                ->setCellValue('N' . $linea, $registro['nombrePlan'])
                ->setCellValue('O' . $linea, $registro['codigoAccionClinica'])
                ->setCellValue('P' . $linea, $registro['nombreConvenio'])
                ->setCellValue('Q' . $linea, $registro['convenioEmpresa'])
                ->setCellValue('R' . $linea, !is_null($registro['rutProfesionalAdmision'])? $registro['rutProfesionalAdmision']: $registro['rutProfesional'] )
                ->setCellValue('S' . $linea, !is_null($registro['rutProfesionalAdmision'])? $registro['nombrePnaturalProfesionalAdmision'] .' '. $registro['apellidoPaternoProfesionalAdmision'] .' '.$registro['apellidoMaternoProfesionalAdmision'] :$registro['nombrePnaturalProfesional'] .' '. $registro['apellidoPaternoProfesional'] .' '.$registro['apellidoMaternoProfesional'])
                ->setCellValue('T' . $linea, !is_null($registro['nombreEspecialidadMedica']) ? $registro['nombreEspecialidadMedica'] : $registro['especialidadReservaAtenc'])
                ->setCellValue('U' . $linea, $nombreEmpresa)
                ->setCellValue('V' . $linea, $registro['idPagoCuenta'])
                ->setCellValue('W' . $linea, (is_null($registro['fechaIngreso'])) ? $registro['fechaAgenda']->format('d-m-Y'):   $registro['fechaIngreso']->format('d-m-Y'))
                ->setCellValue('X' . $linea, (is_null($registro['fechaIngreso'])) ? $registro['fechaAgenda']->format('d-m-Y'):   $registro['fechaIngreso']->format('H:i'))
                ->setCellValue('Y' . $linea, is_null($registro['idDatoIngreso'])? (is_null($registro['fechaAtencion'])? '':$registro['fechaAtencion']->format('d-m-Y')):(is_null($registro['fechaAlta'])? '':$registro['fechaAlta']->format('d-m-Y')))
                ->setCellValue('Z' . $linea, is_null($registro['idDatoIngreso'])? (is_null($registro['fechaAtencion'])? '':$registro['fechaAtencion']->format('H:i')):(is_null($registro['fechaAlta'])? '':$registro['fechaAlta']->format('H:i')))
                ->setCellValue('AA' . $linea, is_null($registro['idDatoIngreso'])? (is_null($registro['fechaAtencion'])? '':$registro['fechaAtencion']->format('d-m-Y')):(is_null($fechaEgreso)? '':$fechaEgreso->format('d-m-Y'))) // egreso
                ->setCellValue('AB' . $linea, is_null($registro['idDatoIngreso'])? (is_null($registro['fechaAtencion'])? '':$registro['fechaAtencion']->format('H:i')):(is_null($fechaEgreso)? '':$fechaEgreso->format('H:i')))
                ->setCellValue('AC' . $linea, (is_null($registro['idPagoWeb'])) ? $datosCajero: 'Pago Web')
                ->setCellValue('AD' . $linea, (is_null($registro['idPagoWeb'])) ? $registro['rutCajero']: 'Pago Web')
                ->setCellValue('AE' . $linea, $registro['valorPrestacion'])
                ->setCellValue('AF' . $linea, $registro['montoAfectoSinIva'])
                ->setCellValue('AG' . $linea, $registro['montoExento'])
                ->setCellValue('AH' . $linea, ($registro['montoAfectoSinIva']+$registro['montoExento']))
                ->setCellValue('AI' . $linea, $registro['precioDiferencia'])
                ->setCellValue('AJ' . $linea, $registro['tipoDiferencia'])
                ->setCellValue('AK' . $linea, $signoDiferencia . (is_null($registro['totalDescuento']) ? 0 : $registro['totalDescuento'] ))
                ->setCellValue('AL' . $linea, 'hábil')
                ->setCellValue('AM' . $linea, $atendido)
                ->setCellValue('AN' . $linea, is_null($registro['fechaAnulacion'])? 'Pagada' : 'Anulada' );
            $linea++;
        }
        foreach($excelService->getActiveSheet()->getColumnIterator() as $column) {
            $excelService->getActiveSheet()->getColumnDimension($column->getColumnIndex())
                ->setAutoSize(true);
        }

        //Establecemos el nombre de la hoja
        $excelService->getActiveSheet()->setTitle('Reporte Produccion');

        // Establece índice de la hoja activa a la primera hoja, por lo que Excel se abre esto como la primera hoja
        $excelService->setActiveSheetIndex(0);

        $writer = new Xlsx($excelService);

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');

        //Establecemos el nombre del archivo
        $date = new \DateTime('now');
        $response->headers->set('Content-Disposition', 'attachment;filename=' . 'Reporte Produccion ' . $fechaIni . ' al ' . $fechaFin . '.xlsx');

        // Si está utilizando una conexión https, tienes que fijar los dos encabezados por compatibilidad con IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}