<?php

namespace App\Service\Recaudacion\Common;

use Doctrine\ORM\EntityManager;

class CommonServices
{
    private $em;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function formatearRut($rut){

        $rut = str_replace("-", "", $rut);
        $rut = str_replace(".", "", $rut);

        $parte4 = substr($rut, -1);
        $parte3 = substr($rut, -4,3);
        $parte2 = substr($rut, -7,3);
        $parte1 = substr($rut, 0,-7);

        $formatoRut = $this->em->getRepository('RebsolHermesBundle:Parametro')->obtenerParametro('FORMATO_RUT');

        switch ($formatoRut['valor']){
            case '0':
                return $parte1.$parte2.$parte3.$parte4;
            case '1':
                return $parte1.$parte2.$parte3."-".$parte4;
            case '2':
                return $parte1.".".$parte2.".".$parte3."-".$parte4;
            default:
                return 'error de formaateo';
        }
    }

    public function formatearFecha()
    {

    }

    public function formatearHora(){

    }


}