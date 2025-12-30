<?php

namespace App\Controller\Caja;

class InformacionActualPacienteController extends RecaudacionController
{
    public function formularioInformacionPacienteAction(){

        return $this->render('RecaudacionBundle:Default\InformacionActualPaciente:_formularioInformacionPaciente.html.twig');
    }

    public function informacionPacienteAction(){

        return $this->render('RecaudacionBundle:Default\InformacionActualPaciente:_informacionPaciente.html.twig');
    }
}
