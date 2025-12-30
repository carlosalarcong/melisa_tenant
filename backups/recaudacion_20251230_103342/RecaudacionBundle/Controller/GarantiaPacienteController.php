<?php

namespace Rebsol\RecaudacionBundle\Controller;


class GarantiaPacienteController extends RecaudacionController
{
    public function regularizarGarantiaPacienteAction(){

        return $this->render('RecaudacionBundle:Default\GarantiaPaciente:_regularizarGarantiaPaciente.html.twig');
    }

    public function resumenGarantiaPacienteAction(){

        return $this->render('RecaudacionBundle:Default\GarantiaPaciente:_resumenGarantiaPaciente.html.twig');
    }
}
