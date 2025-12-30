<?php

namespace Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\ApoyoFacturacion;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Rebsol\RecaudacionBundle\Controller\_Default\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\StreamedResponse;


class DescargaController extends SupervisorController
{
    public function excelAction($mes)
    {
        $fecha = preg_split('[-]', $mes);
        $fechaInicio = new \DateTime($fecha[1].'-'.$fecha[0].'-01 00:00:00');
        $fechaFinal = $fechaInicio->format('Y-m-t 23:59:00');

        $sToolsService = $this->get('hermesTools.Tools');
        $em = $this->getDoctrine()->getManager();

        $registrosDefinitivos = $em->getRepository('RebsolHermesBundle:AccionClinicaPaciente')->obtienePagosApoyoFacturacion($fechaInicio, $fechaFinal);

        $excelService = new Spreadsheet();

        $excelService->setActiveSheetIndex(0)
            ->setCellValue('A1', 'N° Pago')
            ->setCellValue('B1', 'Caja')
            ->setCellValue('C1', 'Zona')
            ->setCellValue('D1', 'Tipo Pago')
            ->setCellValue('E1', 'Fecha Pago')
            ->setCellValue('F1', 'Hora Pago')
            ->setCellValue('G1', 'Paciente')
            ->setCellValue('H1', 'RUN')
            ->setCellValue('I1', 'Sexo')
            ->setCellValue('J1', 'Previsión')
            ->setCellValue('K1', 'Convenio')
            ->setCellValue('L1', 'Plan')
            ->setCellValue('M1', 'Empresa Solicitante')
            ->setCellValue('N1', 'Rut Empresa Solicitante')
            ->setCellValue('O1', 'Monto Afecto')
            ->setCellValue('P1', 'IVA')
            ->setCellValue('Q1', 'Monto Exento')
            ->setCellValue('R1', 'Total')
            ->setCellValue('S1', 'Monto Medio de Pago')
            ->setCellValue('T1', 'Diferencia')
            ->setCellValue('U1', 'Forma Pago Copago')
            ->setCellValue('V1', 'Receptor Pago')
            ->setCellValue('W1', 'Boleta')
            ->setCellValue('X1', 'Bono/RVD')
            ->setCellValue('Y1', 'Copago')
            ->setCellValue('Z1', 'Seguro Complementario')
            ->setCellValue('AA1', 'N° Tarjeta')
            ->setCellValue('AB1', 'Nombre Sociedad')
            ->setCellValue('AC1', 'Folio')
            ->setCellValue('AD1', 'Estado');

        $linea = 2;
        foreach ($registrosDefinitivos as $registro) {

            $rutPaciente = $registro['rutPaciente'];
            if ($registro['idTipoIdentificacionExtranjeroPaciente'] == 1) {
                $rutPaciente = $this->get('CommonServices')->formatearRut($registro['rutPaciente']);
            }

            $signoDiferencia = '+'; //sentido a favor
            if ($registro['idTipoSentidoDiferencia'] == 2) { //sentido en contra
                $signoDiferencia = '-';
            }

            $oRut = '';
            if (!is_null($registro['nombreEmpresaSolicitante'])) {
                $iRutPersona = $registro['rutEmpresaSolicitante'];
                $rutDv = $registro['dvEmpresaSolicitante'];
                $formatRut = number_format($iRutPersona, 0, ".", ".");
                $oRut = implode('-', array($formatRut, $rutDv));
            }

            $excelService->setActiveSheetIndex(0)
                ->setCellValue('A' . $linea, $registro['idPagoCuenta'])
                ->setCellValue('B' . $linea, (is_null($registro['idPagoWeb'])) ? $registro['idCaja']: 'PagoWeb')
                ->setCellValue('C' . $linea, $registro['ambito'])
                ->setCellValue('D' . $linea, $registro['formaPagoDescripcion'])
                ->setCellValue('E' . $linea, $registro['fechaPago']->format('d-m-Y'))
                ->setCellValue('F' . $linea, $registro['fechaPago']->format('H:i'))
                ->setCellValue('G' . $linea, $registro['nombrePnaturalPaciente'] .' '. $registro['apellidoPaternoPaciente'] .' '.$registro['apellidoMaternoPaciente'])
                ->setCellValue('H' . $linea, $rutPaciente)
                ->setCellValue('I' . $linea, $registro['sexo'])
                ->setCellValue('J' . $linea, $registro['nombrePrevision'])
                ->setCellValue('K' . $linea, $registro['nombreConvenio'])
                ->setCellValue('L' . $linea, $registro['nombrePlan'])
                ->setCellValue('M' . $linea, $registro['nombreEmpresaSolicitante'])
                ->setCellValue('N' . $linea, $oRut)
                ->setCellValue('O' . $linea, $registro['montoAfectoSinIva'])
                ->setCellValue('P' . $linea, $registro['montoIva'])
                ->setCellValue('Q' . $linea, $registro['montoExento'])
                ->setCellValue('R' . $linea, $registro['precioDiferenciaPagoCuenta'])
                ->setCellValue('S' . $linea, $registro['montoTotalDocumento'])
                ->setCellValue('T' . $linea, !is_null($registro['totalDiferencia']) ? $signoDiferencia . $registro['totalDiferencia'] : 0)
                ->setCellValue('U' . $linea, $registro['formaPagoCopago'])
                ->setCellValue('V' . $linea, (is_null($registro['idPagoWeb'])) ? $registro['nombrePnaturalCajero'].' '.$registro['apellidoPaternoCajero'].' '.$registro['apellidoMaternoCajero']: 'Pago Web')
                ->setCellValue('W' . $linea, $registro['numeroBoleta'])
                ->setCellValue('X' . $linea,  is_null($registro['numeroDocumento']) ? $registro['numeroDocumentoGeneral'] : $registro['numeroDocumento'])
                ->setCellValue('Y' . $linea, ($registro['copagoImed'] == 0 || is_null($registro['copagoImed'])) ? '' : $registro['copagoImed'])
                ->setCellValue('Z' . $linea, $registro['montoSeguroComplementario'])
                ->setCellValue('AA' . $linea, $registro['ultimos4Numeros'])
                ->setCellValue('AB' . $linea, $registro['nombreSucursal'])
                ->setCellValue('AC' . $linea, (is_null($registro['idPagoWeb'])) ? $registro['idCaja']: 'PagoWeb')
                ->setCellValue('AD' . $linea, is_null($registro['fechaAnulacion'])? 'Pagada' : 'Anulada' );
            $linea++;
        }

        foreach(range('A','Z') as $columnID) {
            $excelService->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        //Establecemos el nombre de la hoja
        $excelService->getActiveSheet()->setTitle('Apoyo Facturacion');

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
        $response->headers->set('Content-Disposition', 'attachment;filename=' . 'Apoyo Facturacion ' . $mes . '.xlsx');

        // Si está utilizando una conexión https, tienes que fijar los dos encabezados por compatibilidad con IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}