<?php

namespace App\Controller\Caja\Supervisor\AsientoContable;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Controller\Caja\_Default\Supervisor\SupervisorController;
use Symfony\Component\HttpFoundation\StreamedResponse;


class DescargaController extends SupervisorController
{
    public function excelAction($fechaIni, $fechaFin, $idSucursal, $idUsuario)
    {
        $em = $this->getDoctrine()->getManager();

        $fechaIniFiltro = $fechaIni . ' 00:00:00';
        $fechaFinFiltro = $fechaFin . ' 23:59:59';

        $asientosContablesArray = $em->getRepository("RebsolHermesBundle:DocumentoPago")
            ->obtenerAsientosContables(
                array(
                    'fechaIni'   => $fechaIniFiltro,
                    'fechaFin'   => $fechaFinFiltro,
                    'idSucursal' => $idSucursal,
                    'idUsuario'  => $idUsuario
                )
            );

        $cuentasHaber = $em->getRepository("RebsolHermesBundle:FormaPago")->obtenerFormaPagoPadre();

        $excelService = new Spreadsheet();

        $excelService->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Cuenta')
            ->setCellValue('B1', 'Glosa')
            ->setCellValue('C1', 'Debe')
            ->setCellValue('D1', 'Haber')
            ->setCellValue('E1', 'Sub-cuenta')
            ->setCellValue('F1', 'Tipo Documento')
            ->setCellValue('G1', 'Documento')
            ->setCellValue('H1', 'Fecha de Emsión')
            ->setCellValue('I1', 'Fecha de Vencimiento');

        $cambio =  $asientosContablesArray[0]['id'];
        $haber = 0;
        $cuentaPadre = 0;
        $glosa = '';
        $numDocumento = '';
        $subCuenta = '';
        $fecha = '';
        $idCuentaPrevalece = 0;
        $LineaBase= 2;
        foreach ($asientosContablesArray as $m) {

            if ($m['id'] != $cambio) {

                $cambio = $m['id'];
                $BuscarCuenta = '';

                if ($idCuentaPrevalece != 0) {
                    $BuscarCuenta = $this->buscarCuentaHaber($idCuentaPrevalece, $cuentasHaber);
                } else {
                    $BuscarCuenta = $this->buscarCuentaHaber($cuentaPadre, $cuentasHaber);
                }

                $excelService->setActiveSheetIndex(0)
                    ->setCellValue("A" . $LineaBase, $BuscarCuenta)
                    ->setCellValue("B" . $LineaBase, 'TRASPASO DE: ' . strtoupper($glosa))
                    ->setCellValue("C" . $LineaBase, 0)
                    ->setCellValue("D" . $LineaBase, $haber)
                    ->setCellValue("E" . $LineaBase, strtoupper($subCuenta))
                    ->setCellValue('F' . $LineaBase, ' ---- ')
                    ->setCellValue("G" . $LineaBase, $numDocumento)
                    ->setCellValue("H" . $LineaBase, $fecha->format('d-m-Y'))
                    ->setCellValue("I" . $LineaBase, $fecha->format('d-m-Y'));

                $idCuentaPrevalece = 0;
                $haber = 0;
                $LineaBase++;
            }

            if (is_null($m['ultimos4Numeros'])) {
                $tipoDocumentos = $m['tipoDocumento'];
                $documento = '';
                if (!is_null($m['numeroVoucher'])) {
                    $documento = $m['numeroVoucher'];
                }

                if (!is_null($m['numeroDocumento'])) {
                    $documento = $m['numeroDocumento'];
                }

                if (is_null($m['numeroDocumento']) and is_null($m['numeroVoucher'])) {
                    $documento = 'S/D';
                }
            } else {
                if (strtoupper($m['tarjetaTipo']) == 'CR') {
                    $tipoDocumentos = 'TC';
                } else {
                    $tipoDocumentos = 'TD';
                }
                $documento = $m['ultimos4Numeros'];
            }

            if  (!is_null($m['fechaDocumento'])) {
                $fechaVencimiento = $m['fechaDocumento'];
            } else {
                $fechaVencimiento = $m['fechaPago'];
            }

            $excelService->setActiveSheetIndex(0)
                ->setCellValue("A" . $LineaBase, $m['cuentaContable'] ? $m['cuentaContable'] : '-')
                ->setCellValue("B" . $LineaBase, 'TRASPASO DE: ' .
                    strtoupper(
                        $m['nombrePnatural'] .
                        (!$m['apellidoPaterno']?: ' ' . $m['apellidoPaterno']) .
                        (!$m['apellidoMaterno']?: ' ' . $m['apellidoMaterno'])
                    )
                )
                ->setCellValue("C" . $LineaBase, $m['debe'])
                ->setCellValue("D" . $LineaBase, 0)
                ->setCellValue("E" . $LineaBase, strtoupper($m['identificacionExtranjero']))
                ->setCellValue("F" . $LineaBase, $m['tipoDocumento'] ? $m['tipoDocumento'] : '----')
                ->setCellValue("G" . $LineaBase, $documento)
                ->setCellValue("H" . $LineaBase, $m['fechaPago']->format('d-m-Y'))
                ->setCellValue("I" . $LineaBase, $fechaVencimiento->format('d-m-Y'));

            $LineaBase++;
            $cuentaPadre = $m['cuentaPadre'];
            $glosa = strtoupper(
                $m['nombrePnatural'] .
                (!$m['apellidoPaterno']?: ' ' . $m['apellidoPaterno']) .
                (!$m['apellidoMaterno']?: ' ' . $m['apellidoMaterno'])
            );
            $haber = $haber + $m['debe'];
            $numDocumento = $m['id'];
            $subCuenta = $m['identificacionExtranjero'];
            $fecha = $m['fechaPago'];
            if ($m['tipoForma'] == 3 or $m['tipoForma'] == 5 or $m['tipoForma'] == 11) {
                $idCuentaPrevalece = $m['cuentaPadre'];
            }
        }
        $BuscarCuenta = '';

        if ($idCuentaPrevalece != 0) {
            $BuscarCuenta = $this->buscarCuentaHaber($idCuentaPrevalece, $cuentasHaber);
        } else {
            $BuscarCuenta = $this->buscarCuentaHaber($cuentaPadre, $cuentasHaber);
        }

        $excelService->setActiveSheetIndex(0)
            ->setCellValue("A" . $LineaBase, $BuscarCuenta)
            ->setCellValue("B" . $LineaBase, 'TRASPASO DE: ' . strtoupper($glosa))
            ->setCellValue("C" . $LineaBase, 0)
            ->setCellValue("D" . $LineaBase, $haber)
            ->setCellValue("E" . $LineaBase, strtoupper($subCuenta))
            ->setCellValue('F' . $LineaBase, ' ---- ')
            ->setCellValue("G" . $LineaBase, $numDocumento)
            ->setCellValue("H" . $LineaBase, $fecha->format('d-m-Y'))
            ->setCellValue("I" . $LineaBase, $fecha->format('d-m-Y'));

        //Establecemos el nombre de la hoja
        $excelService->getActiveSheet()->setTitle('Asientos Contables');

        // Establece índice de la hoja activa a la primera hoja, por lo que Excel se abre esto como la primera hoja
        $excelService->setActiveSheetIndex(0);

        $writer = new Xlsx($excelService);

        $response =  new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');

        //Establecemos el nombre del archivo
        $date = new \DateTime('now');
        $response->headers->set('Content-Disposition', 'attachment;filename='.'Asientos Contables '.$fechaIni.' al '.$fechaFin.'.xlsx');

        // Si está utilizando una conexión https, tienes que fijar los dos encabezados por compatibilidad con IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;

    }

    private function buscarCuentaHaber($idCuenta, $cuentasHaber)
    {
        $resultadoBusqueda = array_search($idCuenta, array_column($cuentasHaber, 'id'));
        if (is_numeric($resultadoBusqueda)) {
            return $cuentasHaber[$resultadoBusqueda]['cuentaContable'];
        }
        return '-';//Error: no hay cuenta a asignar
    }

}