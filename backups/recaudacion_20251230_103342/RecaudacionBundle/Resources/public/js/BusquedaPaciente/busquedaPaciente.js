$(document).ready(function($) {
    $('#buscarPaciente_tipoIdentificacion').on('change', function() {
        if ($(this).val() == 1 ) {
            ES_RUT_PRESUPUESTO = true;
            // El tipo identificación 1 es el RUT.
            $('#busquedaBasica').attr('disabled', true);
            $('#Limpiar').addClass('hidden');
            $('#buscarPaciente_identificacion').addClass('validacionRutJS');
            $('#buscarPaciente_identificacion').attr('maxlength', '12');

            $(".validacionRutJS").blur("click", function () {
                pasiregioncomunaBusca();
                if (!ES_RUT_PRESUPUESTO) { return; }

                if(!$('#busquedaBasica').hasClass('agendamientoSinRut')){



                    if (Rut($('.validacionRutJS'))) {
                        $('#iconRut').removeClass('icon-question-sign');
                        $('#iconRut').removeClass('icon-remove-sign red');
                        $('#iconRut').addClass('icon-ok-sign green');
                        $('#busquedaBasica').attr('disabled',false);
                        $('#Limpiar').removeClass('hidden');
                    }else{
                        $('#iconRut').removeClass('icon-question-sign');
                        $('#iconRut').removeClass('icon-ok-sign green');
                        $('#iconRut').addClass('icon-remove-sign red');
                        $('#busquedaBasica').attr('disabled',true);
                        $('#Limpiar').addClass('hidden');
                        $('#iconRut').focus();
                    }
                }
            });


            $(".validacionRutJS").on('input', function () {
                if (!ES_RUT_PRESUPUESTO) { return; }

                if ($(".validacionRutJS").val().length > 7 ) {
                    if (Rut($('.validacionRutJS'))) {
                        $('#iconRut').removeClass('icon-question-sign');
                        $('#iconRut').addClass('icon-ok-sign green');
                        $('#busquedaBasica').attr('disabled',false);
                        $('#Limpiar').removeClass('hidden');
                    }else{
                        $('#iconRut').removeClass('icon-question-sign');
                        $('#iconRut').removeClass('icon-ok-sign green');
                        $('#iconRut').addClass('icon-remove-sign red');
                        $('#busquedaBasica').attr('disabled',true);
                        $('#Limpiar').addClass('hidden');
                        $('#iconRut').focus();
                    }
                }else if ($(".validacionRutJS").val().length = 0 ){
                    $('#iconRut').addClass('icon-question-sign');
                    $('#busquedaBasica').attr('disabled',true);
                    $('#Limpiar').addClass('hidden');
                }
            });

            $(".validacionRutJS").keypress(function (e) {
                if (!ES_RUT_PRESUPUESTO) { return; }

                if (e.which === 13) {
                    if(!$('#Buscar').hasClass('agendamientoSinRut')){
                        if (Rut($('.validacionRutJS'))) {
                            $('#iconRut').removeClass('icon-question-sign');
                            $('#iconRut').addClass('icon-ok-sign green');
                            $('#Limpiar').removeClass('hidden');
                        }else{
                            $('#iconRut').removeClass('icon-question-sign');
                            $('#iconRut').addClass('icon-remove-sign red');
                            $('#Limpiar').addClass('hidden');
                            $('#iconRut').focus();
                        }
                    }
                }
            });

        } else {
            if($(this).val() == 0 ){
                $('#buscarPaciente_identificacion').attr('maxlength', '50');
            }else{
                $('#buscarPaciente_identificacion').attr('maxlength', '12');
            }
            console.log('not rut');
            ES_RUT_PRESUPUESTO = false;
            // Cualquier otro tipo de documento
            $('#busquedaBasica').attr('disabled', false);
            $('#Limpiar').removeClass('hidden');
            $('#buscarPaciente_identificacion').removeClass('validacionRutJS');
        }
    });

    $('#buscarPaciente_tipoIdentificacion').trigger('change');

    $(".continuarPasoUno, .btn-volverEdit ").on('click', function () {
        console.log('busquedaPaciente::continuarPasoUno::onclick')
        var ruta = Routing.generate("Caja_ConsultaPacienteIdPnatural");

        var tipoIdentificacion = $('#buscarPaciente_tipoIdentificacion').val();
        var identificacion = $('#buscarPaciente_identificacion').val();

        $.ajax({
            type: 'get',
            url: ruta,
            data: {
                tipoIdentificacion: tipoIdentificacion,
                identificacion: identificacion
            },
            success: function (datar) {
            },
            error: function (datar) {
                falla();
            }
        });

        $('.btn-edit-lista-prestaciones').hide();
        $('#btneditListaPrestaciones').hide();
        $(".btn-volverEdit").show();
        $("#pacienteform").slideUp(400);
        $(".alertaExisteFullDatos").slideUp(400);
        $("#pacienteresumen").slideDown(1000);
        $("#datosprestacion").slideDown(1000);
        $("#formprestador").show(1000);
        $("#agregar-prestacion-insumo-paquete").hide(1000);
        poblarResumenPaciente();
        post_create_update();
    });
});

const busquedaBasica = () => {
    console.log('busquedaPaciente::busquedaBasica')
    $('#busquedaBasica').attr('disabled', true);
    console.log($('#busquedaBasica').prop('disabled') == false)
    if ($('#busquedaBasica').prop('disabled') == false) {
        $('#mensajeError').addClass('hide');
        if ($('#buscarPaciente_identificacion').val() !== "") {

            console.log('1')
            if (parseInt($('#buscarPaciente_identificacion').val()) === 1) {

                console.log('2')
                $("#buscarPaciente_identificacion").each(function() {
                    var algo = $(this).val();
                    var rrb1 = algo.replace(/-/g, "");
                    $(this).val(rrb1);
                });

                var rutvar = $("#buscarPaciente_identificacion").val();
                var rayaindex = rutvar.indexOf('-');
                var rayalast = rutvar.lastIndexOf('-');

                if (rayaindex !== rayalast) {
                    console.log('3')
                    console.log('No debe haber mas de un "-"')
                    $("#buscarPaciente_identificacion").val().replace(/-/g, '');
                    $("#errormsjrut").html('No debe haber mas de un "-"');
                    accionerror();

                } else {
                    console.log('4')
                    $("#buscarPaciente_identificacion").val($.trim($("#buscarPaciente_identificacion").val()));

                    if ($("#buscarPaciente_identificacion").val() === '') {
                        console.log('Rut no debe ser vacío')
                        $("#errormsjrut").html('Rut no debe ser vacío');
                        accionerror();

                    } else {
                        console.log('5')
                        ValidaRutPaciente();
                        $("#errorrut").slideUp("slow");
                    }

                    $("#buscarPaciente_identificacion").click(function() {
                        if ($("#buscarPaciente_identificacion").val() === '') {
                            console.log('6')
                            var append = $("#buscarPaciente_identificacion").closest(".input-append").children('span').children('i');
                            append.removeClass(' icon-spinner icon-spin green icon-remove red  icon-large');
                            append.addClass('icon-asterisk');
                        }
                    });

                }

            } else {
                console.log('7')
                ValidaRutPaciente();
                $("#errorrut").slideUp("slow");
            }

        } else {
            // $('#buscarPaciente_tipoIdentificacion_chzn')[0].style.border = '1px solid red';
        }
    }else{
        $('#mensajeError').removeClass('hide');
    }
}

const ValidaRutPaciente = () => {
    console.log('busquedaPaciente::ValidaRutPaciente')
    var Rut = $("#rebsol_hermesbundle_PagoType_rutPersona").val();
    var digitoVerificador = $("#rebsol_hermesbundle_PagoType_digitoVerifivador").val();
    if (parseInt($('#buscarPaciente_tipoIdentificacion').val()) === 1) {

        $('#btnCargaFormPaciente').hide();
        limpiarayapuntio();

        var append = $("#buscarPaciente_identificacion").closest(".input-append").children('span').children('i');
        append.removeClass('icon-asterisk green icon-remove icon-large red');
        append.addClass('icon-spinner icon-spin');
        if ($("#buscarPaciente_identificacion").val() !== '') {

            var rdv = $("#buscarPaciente_identificacion").val();
            var largo = rdv.length;
            var r = $("#buscarPaciente_identificacion").val().substr(0, largo - 1);
            var dv = $("#buscarPaciente_identificacion").val().substr(largo - 1, largo);
            $("#rebsol_hermesbundle_PagoType_rutPersona").val(r);
            $("#rebsol_hermesbundle_PagoType_digitoVerifivador").val(dv);
        }


        var rut = ($("#rebsol_hermesbundle_PagoType_rutPersona").val() == '' || $("#rebsol_hermesbundle_PagoType_digitoVerifivador").val() == '');
        var documento = ($("#rebsol_hermesbundle_PagoType_numeroDocumento").val() == '' || $("#rebsol_hermesbundle_PagoType_documento").val() == '');

        if (rut && documento) {

            /** sin valores rut y dv */
            if (idReservaAtencion) {

                IngresaDatosFormReservaExtranjero(0, null, null);

                BuscaComunaParaFormulario({
                    comuna: idComuna1
                });

                botonesFormPaciente(4, fechad);
                return false;
            } else {
                return false;
            }
        }
    } else {

        if ($('#otro-identificacion').val() === "") {

            $('#otro-identificacion')[0].style.border = "1px solid red";

            //Fix busquedaAvanzada PagoCuenta Con Tipo de Identificacion Distinto de Rut.
            //return false;
        }

    }

    // $('#otro-identificacion')[0].style.border = "1px solid #ccc";
    if (verificaExtranjero() == 0) {
        /** no es extranjero desde reserva */
        if($('#buscarPaciente_tipoIdentificacion').val() == 1){
            reformat();
        }

        var seleccionDocumento = '';

        if ($('#buscarPaciente_identificacion').is(':visible')) {

            seleccionDocumento = $('#buscarPaciente_identificacion').val();

        } else {

            seleccionDocumento = $('#otro-identificacion').val();
        }

        $.get(Routing.generate('Caja_Recaudacion_Pago_ValRut'), {
            idTipoIdentificacionExtranjero: $('#buscarPaciente_tipoIdentificacion').val(),
            tipoDocumentoExtranjero: $('#buscarPaciente_identificacion').val()
        }, function (datar) {

            datar = $.parseJSON(datar);

            if (datar) {

                if ($('#buscarPaciente_tipoIdentificacion').val() == 1) {

                    if ((datar !== 'Rut pertenece a Empresa') && (datar !== 'Rut Inválido') && (datar !== 'Rut no debe ser vacío')) {

                        $('.disableform').val('');

                        if (datar instanceof Object == false) {


                            if (idReservaAtencion) {

                                if ($("#rebsol_hermesbundle_PagoType_numeroDocumento").val() == '' && $("#rebsol_hermesbundle_PagoType_documento").val() == '') {
                                    IngresaDatosFormReservaExtranjero(6, null, null);
                                } else {
                                    IngresaDatosFormReservaExtranjero(2, extranjeroID, numDocumento);
                                }

                                BuscaComunaParaFormulario({
                                    comuna: idComuna1
                                });

                                botonesFormPaciente(5, fechad);
                            } else {

                                EventoSinDatosEnRegistro(0);
                            }

                        } else {

                            console.log('verificaRutDocumentoExtranjeroexiste:: '+verificaRutDocumentoExtranjeroexiste)
                            /** paciente existente, cargar los datos del paciente, y mostrar formulario */
                            if (verificaRutDocumentoExtranjeroexiste == 1) {

                                bootbox.dialog(
                                    "<div class='alert alert-block alert-warning'>" +
                                    "    <p>" +
                                    "    <strong><i class='icon-ok'></i> Se ha encontrado una coincidencia con Dato Ingresado,</strong>" +
                                    "    </p>" +
                                    "    <p>" +
                                    "   Advertencia: Se actualizarán los datos de Reserva con datos encontrados " +
                                    "    </p>" +
                                    "</div>", [{
                                        "label": "Aceptar",
                                        "class": "btn btn-mini btn-success",
                                        "callback": function () {

                                            /** si tiene rut y no hubo necesidad de validarlo (desde reserva de atencion) */
                                            IngresaDatosFormReservaExtranjeroRegistrado(0, datar);

                                            if (idReservaAtencion) {
                                                registroDireccion(idComuna1, direccion1, numero1, resto1);
                                                var data = {
                                                    comuna: idComuna1
                                                };
                                                BuscaComunaParaFormulario(data);
                                            } else {
                                                if (dataReserva) {
                                                    registroDireccionDataReserva(datar)
                                                } else {
                                                    var data = {
                                                        id: idPersona
                                                    };
                                                    ObtieneDomicilioIdPersona(data);
                                                }
                                            }
                                            if (usuario) {
                                                $(".disablepago").attr("disabled", "disabled");
                                                $(".disablepago").attr("readonly", "readonly");
                                                $("#rebsol_hermesbundle_PagoType_fechaNacimiento").attr("readonly", "readonly");
                                                $("#rebsol_hermesbundle_PagoType_fechaNacimiento").attr("disabled", "disabled");
                                                $(".infoesusuario").slideDown(800);
                                                setTimeout(function () {
                                                    $(".infoesusuario").slideUp(1000);
                                                }, 4800);
                                            }

                                            $("#busquedaPacientesForm").slideUp(1000);
                                            if($('#recaudacion-informacion-paciente').length > 0){
                                                $('#recaudacion-informacion-paciente').removeClass('hide');
                                            }
                                            disabledReadOnlyCampos();
                                            botonesFormPaciente(6, fechad);
                                            crearUsuarioSinRut = 1;
                                        }
                                    }]);

                            } else {

                                /** si tiene rut y no hubo necesidad de validarlo (desde reserva de atencion) */
                                IngresaDatosFormReservaExtranjeroRegistrado(2, datar);

                                if (idReservaAtencion) {
                                    registroDireccion(idComuna1, direccion1, numero1, resto1);
                                    var data = {
                                        comuna: idComuna1
                                    };
                                    BuscaComunaParaFormulario(data);
                                } else {
                                    if (dataReserva) {
                                        registroDireccionDataReserva(datar)
                                    } else {
                                        var data = {
                                            id: idPersona
                                        };
                                        ObtieneDomicilioIdPersona(data);
                                    }
                                }

                                if (usuario) {
                                    $(".disablepago").attr("disabled", "disabled");
                                    $(".disablepago").attr("readonly", "readonly");
                                    $("#rebsol_hermesbundle_PagoType_fechaNacimiento").attr("readonly", "readonly");
                                    $("#rebsol_hermesbundle_PagoType_fechaNacimiento").attr("disabled", "disabled");
                                    $(".infoesusuario").slideDown(800);
                                    setTimeout(function () {
                                        $(".infoesusuario").slideUp(1000);
                                    }, 4800);
                                }

                                $("#busquedaPacientesForm").slideUp(1000);
                                if($('#recaudacion-informacion-paciente').length > 0){
                                    $('#recaudacion-informacion-paciente').removeClass('hide');
                                }
                                disabledReadOnlyCampos()
                                botonesFormPaciente(7, fechad);
                            }

                            if (idReservaAtencion) {

                                $("#rebsol_hermesbundle_PagoType_fechaNacimiento").datepicker('update', fechanacimiento1);
                                $("#rebsol_hermesbundle_PagoType_telefonoMovil").val(celu1);
                                $("#rebsol_hermesbundle_PagoType_telefonoFijo").val(fijo1);
                                $("#rebsol_hermesbundle_PagoType_telefonoTrabajo").val(trabajo1);
                                $("#rebsol_hermesbundle_PagoType_correoElectronico").val(mail11);
                                $("#user").val(usuario1);
                                registroDireccion(idComuna1, direccion1, numero1, resto1);
                                verificaGarantia(garantias);
                                var data = {
                                    comuna: idComuna1
                                };
                                BuscaComunaParaFormulario(data);
                            }

                            if (!idReservaAtencion) {
                                //if (coreApi == 1) {
                                choisestyle_campos_prevision();
                                //}
                            }
                        }

                    } else {

                        $('#errormsjrut').html(datar);
                        accionerror();
                    }
                } else {

                    /** otro documento */
                    if (verificaRutDocumentoExtranjeroexiste == 1) {

                        bootbox.dialog(
                            "<div class='alert alert-block alert-warning'>" +
                            "    <p>" +
                            "    <strong><i class='icon-ok'></i> Se ha encontrado una coincidencia con Dato Ingresado,</strong>" +
                            "    </p>" +
                            "    <p>" +
                            "   Advertencia: Se actualizarán los datos de Reserva con datos encontrados " +
                            "    </p>" +
                            "</div>", [{
                                "label": "Aceptar",
                                "class": "btn btn-mini btn-success",
                                "callback": function () {

                                    /** si tiene rut y no hubo necesidad de validarlo (desde reserva de atencion) */
                                    IngresaDatosFormReservaExtranjeroRegistrado(0, datar);

                                    if (idReservaAtencion) {
                                        registroDireccion(idComuna1, direccion1, numero1, resto1);
                                        var data = {
                                            comuna: idComuna1
                                        };
                                        BuscaComunaParaFormulario(data);
                                    } else {
                                        if (dataReserva) {
                                            registroDireccionDataReserva(datar)
                                        } else {
                                            var data = {
                                                id: idPersona
                                            };
                                            ObtieneDomicilioIdPersona(data);
                                        }
                                    }
                                    if (usuario) {
                                        $(".disablepago").attr("disabled", "disabled");
                                        $(".disablepago").attr("readonly", "readonly");
                                        $("#rebsol_hermesbundle_PagoType_fechaNacimiento").attr("readonly", "readonly");
                                        $("#rebsol_hermesbundle_PagoType_fechaNacimiento").attr("disabled", "disabled");
                                        $(".infoesusuario").slideDown(800);
                                        setTimeout(function () {
                                            $(".infoesusuario").slideUp(1000);
                                        }, 4800);
                                    }

                                    $("#busquedaPacientesForm").slideUp(1000);
                                    if($('#recaudacion-informacion-paciente').length > 0){
                                        $('#recaudacion-informacion-paciente').removeClass('hide');
                                    }
                                    disabledReadOnlyCampos();
                                    botonesFormPaciente(6, fechad);
                                    crearUsuarioSinRut = 1;
                                }
                            }]);

                    } else {

                        /** si tiene rut y no hubo necesidad de validarlo (desde reserva de atencion) */
                        IngresaDatosFormReservaExtranjeroRegistrado(2, datar);

                        if (idReservaAtencion) {
                            registroDireccion(idComuna1, direccion1, numero1, resto1);
                            var data = {
                                comuna: idComuna1
                            };
                            BuscaComunaParaFormulario(data);
                        } else {
                            if (dataReserva) {
                                registroDireccionDataReserva(datar)
                            } else {
                                var data = {
                                    id: idPersona
                                };
                                ObtieneDomicilioIdPersona(data);
                            }
                        }

                        if (usuario) {
                            $(".disablepago").attr("disabled", "disabled");
                            $(".disablepago").attr("readonly", "readonly");
                            $("#rebsol_hermesbundle_PagoType_fechaNacimiento").attr("readonly", "readonly");
                            $("#rebsol_hermesbundle_PagoType_fechaNacimiento").attr("disabled", "disabled");
                            $(".infoesusuario").slideDown(800);
                            setTimeout(function () {
                                $(".infoesusuario").slideUp(1000);
                            }, 4800);
                        }

                        $("#busquedaPacientesForm").slideUp(1000);
                        if($('#recaudacion-informacion-paciente').length > 0){
                            $('#recaudacion-informacion-paciente').removeClass('hide');
                        }
                        disabledReadOnlyCampos()
                        botonesFormPaciente(7, fechad);
                    }

                    if (idReservaAtencion) {

                        $("#rebsol_hermesbundle_PagoType_fechaNacimiento").datepicker('update', fechanacimiento1);
                        $("#rebsol_hermesbundle_PagoType_telefonoMovil").val(celu1);
                        $("#rebsol_hermesbundle_PagoType_telefonoFijo").val(fijo1);
                        $("#rebsol_hermesbundle_PagoType_telefonoTrabajo").val(trabajo1);
                        $("#rebsol_hermesbundle_PagoType_correoElectronico").val(mail11);
                        $("#user").val(usuario1);
                        registroDireccion(idComuna1, direccion1, numero1, resto1);
                        verificaGarantia(garantias);
                        var data = {
                            comuna: idComuna1
                        };
                        BuscaComunaParaFormulario(data);
                    }

                    if (!idReservaAtencion) {
                        // if (coreApi == 1) {
                        choisestyle_campos_prevision();
                        // }
                    }

                    // console.log(datar)
                }
            } else {

                $('.alertaNoexiste').slideDown();
            }
        });
    }
}
/*let ES_RUT_PRESUPUESTO = null;
$(document).ready(function($) {
    $('#buscarPaciente_tipoIdentificacion').on('change', function() {
        if ($(this).val() == 1 ) {
            console.log('es rut');
            ES_RUT_PRESUPUESTO = true;
            // El tipo identificación 1 es el RUT.
            $('#busquedaBasica').attr('disabled', true);
            $('#Limpiar').addClass('hidden');
            $('#buscarPaciente_identificacion').addClass('validacionRutJS');
            $('#buscarPaciente_identificacion').attr('maxlength', '12');

            $(".validacionRutJS").blur("click", function () {
                pasiregioncomunaBusca();
                if (!ES_RUT_PRESUPUESTO) { return; }

                if(!$('#busquedaBasica').hasClass('agendamientoSinRut')){



                    if (Rut($('.validacionRutJS'))) {
                        $('#iconRut').removeClass('icon-question-sign');
                        $('#iconRut').removeClass('icon-remove-sign red');
                        $('#iconRut').addClass('icon-ok-sign green');
                        $('#busquedaBasica').attr('disabled',false);
                        $('#Limpiar').removeClass('hidden');
                    }else{
                        $('#iconRut').removeClass('icon-question-sign');
                        $('#iconRut').removeClass('icon-ok-sign green');
                        $('#iconRut').addClass('icon-remove-sign red');
                        $('#busquedaBasica').attr('disabled',true);
                        $('#Limpiar').addClass('hidden');
                        $('#iconRut').focus();
                    }
                }
            });


            $(".validacionRutJS").on('input', function () {
                if (!ES_RUT_PRESUPUESTO) { return; }

                if ($(".validacionRutJS").val().length > 7 ) {
                    if (Rut($('.validacionRutJS'))) {
                        $('#iconRut').removeClass('icon-question-sign');
                        $('#iconRut').addClass('icon-ok-sign green');
                        $('#busquedaBasica').attr('disabled',false);
                        $('#Limpiar').removeClass('hidden');
                    }else{
                        $('#iconRut').removeClass('icon-question-sign');
                        $('#iconRut').removeClass('icon-ok-sign green');
                        $('#iconRut').addClass('icon-remove-sign red');
                        $('#busquedaBasica').attr('disabled',true);
                        $('#Limpiar').addClass('hidden');
                        $('#iconRut').focus();
                    }
                }else if ($(".validacionRutJS").val().length = 0 ){
                    $('#iconRut').addClass('icon-question-sign');
                    $('#busquedaBasica').attr('disabled',true);
                    $('#Limpiar').addClass('hidden');
                }
            });

            $(".validacionRutJS").keypress(function (e) {
                if (!ES_RUT_PRESUPUESTO) { return; }

                if (e.which === 13) {
                    if(!$('#Buscar').hasClass('agendamientoSinRut')){
                        if (Rut($('.validacionRutJS'))) {
                            $('#iconRut').removeClass('icon-question-sign');
                            $('#iconRut').addClass('icon-ok-sign green');
                            $('#Limpiar').removeClass('hidden');
                        }else{
                            $('#iconRut').removeClass('icon-question-sign');
                            $('#iconRut').addClass('icon-remove-sign red');
                            $('#Limpiar').addClass('hidden');
                            $('#iconRut').focus();
                        }
                    }
                }
            });

        } else {
            if($(this).val() == 0 ){
                $('#buscarPaciente_identificacion').attr('maxlength', '50');
            }else{
                $('#buscarPaciente_identificacion').attr('maxlength', '12');
            }
            console.log('not rut');
            ES_RUT_PRESUPUESTO = false;
            // Cualquier otro tipo de documento
            $('#busquedaBasica').attr('disabled', false);
            $('#Limpiar').removeClass('hidden');
            $('#buscarPaciente_identificacion').removeClass('validacionRutJS');
        }
    });

    $('#buscarPaciente_tipoIdentificacion').trigger('change');

    $(".continuarPasoUno, .btn-volverEdit ").on('click', function () {
        console.log('busquedaPaciente::continuarPasoUno::onclick')
        var ruta = Routing.generate("Caja_ConsultaPacienteIdPnatural");

        var tipoIdentificacion = $('#buscarPaciente_tipoIdentificacion').val();
        var identificacion = $('#buscarPaciente_identificacion').val();

        $.ajax({
            type: 'get',
            url: ruta,
            data: {
                tipoIdentificacion: tipoIdentificacion,
                identificacion: identificacion
            },
            success: function (datar) {
            },
            error: function (datar) {
                falla();
            }
        });

        $('.btn-edit-lista-prestaciones').hide();
        $('#btneditListaPrestaciones').hide();
        $(".btn-volverEdit").show();
        $("#pacienteform").slideUp(400);
        $(".alertaExisteFullDatos").slideUp(400);
        $("#pacienteresumen").slideDown(1000);
        $("#datosprestacion").slideDown(1000);
        $("#formprestador").show(1000);
        $("#agregar-prestacion-insumo-paquete").hide(1000);
        poblarResumenPaciente();
        post_create_update();
    });
});

const reformat = () => {
    console.log('reformat')
    var Rut = $("#rebsol_hermesbundle_PagoType_rutPersona").val();
    var digitoVerificador = $("#rebsol_hermesbundle_PagoType_digitoVerifivador").val();
    console.log('Rut')
    console.log(Rut)
    console.log('digitoVerificador')
    console.log(digitoVerificador)
    var sRut = new String(Rut);
    var sRutFormateado = '';
    while (sRut.length > 3) {
        sRutFormateado = "." + sRut.substr(sRut.length - 3) + sRutFormateado;
        sRut = sRut.substring(0, sRut.length - 3);
    }
    sRutFormateado = sRut + sRutFormateado;
    if (sRutFormateado != "" && digitoVerificador) {
        sRutFormateado += "-" + digitoVerificador;
    } else if (digitoVerificador) {
        sRutFormateado += digitoVerificador;
    }
    $("#buscarPaciente_identificacion").val(sRutFormateado);
}

const accionerror = () => {
    var append = $("#buscarPaciente_identificacion").closest(".input-append").children('span').children('i');
    append.removeClass('icon-spinner icon-spin add-on dark-opaque green icon-asterisk');
    append.addClass('icon-remove icon-large red');
    $("#datosprestacion").hide();
    $("#errorrut").slideDown("Slow");
}

const limpiarayapuntio = () => {
    console.log('buscaPaciente::limpiarayapuntio')
    $("#buscarPaciente_identificacion").each(function() {
        var algo = $(this).val();
        var rrb1 = algo.replace(/\./g, "");
        $(this).val(rrb1);
    });

    $("#buscarPaciente_identificacion").each(function() {
        var algo = $(this).val();
        var rrb1 = algo.replace(/-/g, "");
        $(this).val(rrb1);
    });
    $("#buscarPaciente_identificacion").each(function() {
        var algo = $(this).val();
        var rrb1 = algo.replace(/ /g, "");
        $(this).val(rrb1);
    });
}

const accionok = () => {
    var append = $("#buscarPaciente_identificacion").closest(".input-append").children('span').children('i');
    append.removeClass('icon-spinner icon-spin add-on dark-opaque icon-asterisk');
    append.addClass('icon-ok icon-large green');
    $("#busquedaBasica").hide();
    $("#errorrut").hide();
}

const killScroll = () => {
    $(".tab-content").css("overflow", "hidden");
}

const validaComunaPais = (pais, comuna, tipoIdentificacion) => {
    let bValidacion = false;

    if (habilitarPaisExtranjero == 1 && (tipoIdentificacion == 2 || tipoIdentificacion == 4)) {
        if (pais != null) {
            bValidacion = true;
        }
    } else {
        if (comuna != null) {
            bValidacion = true;
        }
    }
    return bValidacion;
}

//funciones de acciones en pantalla, envian una reaccion un evento asociativo.
const PermiteCamposenFormulario = () => {
    $(".permitidoTemp").removeAttr("disabled");
    $(".permitidoTemp").removeAttr("readonly");
    $("#rebsol_hermesbundle_PagoType_comuna").removeAttr("disabled");
    $("#rebsol_hermesbundle_PagoType_comuna").removeAttr("readonly");
}

const choisestyle_comuna = () => {
//OBSERVACION: debe ejecutarse despues de haber cargado la comuna en una consulta, pues, se escribe sobre la respuesta de DB en el campo, y deja como vacia, la solución fue cargarla con un DELAY(), despues de
//carga comuna  en el input, esta es funcion pasiregioncomunaBuscaconId(comuna).
    $("#rebsol_hermesbundle_Perfiltype_comuna").addClass("chosen-select");
    $(".chosen-select").chosen({width: "215px", no_results_text: "No se ha encontrado Comuna", allow_single_deselect: true});
}

const choisestyle_campos_prevision  = () => {
    //OBSERVACION: debe ejecutarse despues de haber cargado la comuna en una consulta, pues, se escribe sobre la respuesta de DB en el campo, y deja como vacia, la solución fue cargarla con un DELAY(), despues de
    //carga comuna  en el input, esta es funcion pasiregioncomunaBuscaconId(comuna).
    if(idReservaAtencion){}else{
        $("#rebsol_hermesbundle_PrestacionType_prevision").addClass("chosen-select");
        $(".chosen-select").chosen({width: "215px", no_results_text: "No se ha encontrado Prevision"});
        $("#rebsol_hermesbundle_PrestacionType_convenio").addClass("chosen-select");
        $(".chosen-select").chosen({width: "215px", no_results_text: "No se ha encontrado Convenio"});
        $('#rebsol_hermesbundle_PrestacionType_convenio').prop('disabled', true).trigger("liszt:updated");

        // $('#rebsol_hermesbundle_PrestacionType_origenSelect').addClass("chosen-select");
        // $(".chosen-select").chosen({width: "215px", no_results_text: "No se ha encontrado Profesional"});

        $('#rebsol_hermesbundle_PrestacionType_derivadoSelect').addClass("chosen-select");
        $(".chosen-select").chosen({width: "215px", no_results_text: "No se ha encontrado Profesional"});

        $("#rebsol_hermesbundle_PrestacionType_plan").addClass("chosen-select select-plan");
        $("#rebsol_hermesbundle_PrestacionType_plan").chosen({width: "215px",
            allow_single_deselect: true, no_results_text: "No se ha encontrado Plan, disable_search_threshold: 2"
        });
        $('#rebsol_hermesbundle_PrestacionType_plan').prop('disabled', true).trigger("liszt:updated");
    }
}

const validaDatosCompletos = (fechad) => {
    if (!fechad) {
        if (
            $("#buscarPaciente_identificacion2").val() === '' ||
            $("#rebsol_hermesbundle_PagoType_rutPersona").val() === '' ||
            $("#rebsol_hermesbundle_PagoType_digitoVerifivador").val() === '' ||
            $("#rebsol_hermesbundle_PagoType_nombrePnatural").val() === '' ||
            $("#rebsol_hermesbundle_PagoType_apellidoPaterno").val() === '' ||
            $("#rebsol_hermesbundle_PagoType_apellidoMaterno").val() === '' ||
            $("#rebsol_hermesbundle_PagoType_idSexo").val() === '' ||
            $("#rebsol_hermesbundle_PagoType_fechaNacimiento").val() === '' ||
            $("#rebsol_hermesbundle_PagoType_comuna").val() === '' ||
            $("#rebsol_hermesbundle_PagoType_direccion").val() === ''
        ) {} else {
            if ($("#rebsol_hermesbundle_PagoType_telefonoMovil").val() === "" &&
                $("#rebsol_hermesbundle_PagoType_telefonoFijo").val() === "" &&
                $("#rebsol_hermesbundle_PagoType_telefonoTrabajo").val() === ""
            ) {} else {
                $(".btn-salvar").hide();
                $(".btn-volver").hide();
                $(".alertaExisteFullDatos").slideDown(900);
            }
        }
    }
}

const poblarResumenPaciente  = () => {
    tipoIdentificacion = $('#buscarPaciente_tipoIdentificacion').val();
    identificacion = $('#buscarPaciente_identificacion').val();

    var nombre = $("#rebsol_hermesbundle_PagoType_nombrePnatural").val();
    var apep = $("#rebsol_hermesbundle_PagoType_apellidoPaterno").val();
    var apem = $("#rebsol_hermesbundle_PagoType_apellidoMaterno").val();
    var fechan = $("#rebsol_hermesbundle_PagoType_fechaNacimiento").val();
    var anio_nacim = fechan.substr(6, 4);
    var mes_nacim = fechan.substr(3, 2);
    var dia_nacim = fechan.substr(0, 2);

    $($('#pacienteresumen').find('th')[0]).text($('#rebsol_hermesbundle_PagoType_documento option:selected').text() + ':');

    $("#rutspan").html(identificacion);

    $("#nombrespan").html(nombre);
    $("#apepspan").html(apep);
    $("#apemspan").html(apem);
    $("#fechanspan").html(fechan);
    $("#edadspan").html(calcular_edad(dia_nacim, mes_nacim, anio_nacim));
    $("#pacienteresumen").slideDown(1000);

    $('#btnvolverbuscador, #cancelar-desde-prestador, #cancelar-desde-mediopago').on('click', function () {
        $('#bodycontainer').html('Cargando...');

        rutaConsultarDatosD = Routing.generate('Caja_disponibilizarFolio');
        console.log('Caja_disponibilizarFolio')
        $.ajax({
            type: 'GET',
            url: rutaConsultarDatosD,
            data: {},
            async: false,
            success: function (data) {

                if (data == 'success') {
                    okDisponibilizarFolio();
                } else {
                    errorDisponibilizarFolio();
                }
            }
        });
        return false;
        //location.reload();
    });
}

const calcular_edad = (dia_nacim, mes_nacim, anio_nacim) => {
    var fecha_hoy = new Date();
    var ahora_anio = fecha_hoy.getYear();
    var ahora_mes = fecha_hoy.getMonth();
    var ahora_dia = fecha_hoy.getDate();
    var edad = (ahora_anio + 1900) - anio_nacim;
    if (ahora_mes < (mes_nacim - 1)) {
        edad--;
    }
    if (((mes_nacim - 1) == ahora_mes) && (ahora_dia < dia_nacim)) {
        edad--;
    }
    if (edad > 1900) {
        edad -= 1900;
    }
    return edad;
}

const okDisponibilizarFolio = () => {

    var error = "" +
        "<div class='alert alert-block alert-success'>" +
        "    <p>" +
        "    <strong>Folios Disponibles</strong>" +
        "    <br>" +
        "    <br>Los folios ocupados en caja fueron puesto como disponibles nuevamente para su uso " +
        "    </p>" +
        "</div>";
    bootbox.dialog(error, [{
        "label": "Aceptar",
        "class": "btn btn-mini",
        "callback": function () {
        }
    }]);
    return;
}

const errorDisponibilizarFolio = () => {

    var error = "" +
        "<div class='alert alert-block alert-danger'>" +
        "    <p>" +
        "    <strong>Error Folios Disponibles</strong>" +
        "    <br>" +
        "    <br>Motivo: Los folios ocupados en caja no pudieron quedar como disponibles nuevamente para su uso " +
        "    </p>" +
        "</div>";
    bootbox.dialog(error, [{
        "label": "Aceptar",
        "class": "btn btn-mini",
        "callback": function () {
        }
    }]);
    return;
}

const post_create_update = () => {
    $(".disablepago").attr("disabled", "disabled");
    $(".disablepago").attr("readonly", "readonly");
    $("#rebsol_hermesbundle_PagoType_comuna").attr("disabled", "disabled");
    $("#rebsol_hermesbundle_PagoType_comuna").attr("readonly", "readonly");
    $(".btn-salvar").fadeOut("slow");
    $(".btn-volver").fadeOut("slow");
    $(".mensajeErrorFormulario").fadeOut("slow");
    $("#datosprestacion").slideDown("slow");
    DatosPrestacion();
    $("#pacienteform").slideUp(400);
    $(".alertaExisteFullDatos").slideUp(400);
    poblarResumenPaciente();
    creaListaHistorica();
}

const creaListaHistorica = () => {
    console.log('Events-add-people::creaListaHistoricaaaaaaaa')
    $('#historicoPacientePagos').show().append("<i class='icon-spinner icon-spin'></i> <span id='infoCarga'>Buscando y actualizando listado de antecedentes111...</span>");
    var ruta = Routing.generate("recaudacion_busqueda_historial_paciente"); //Caja_PostPago_Historial
    console.log('recaudacion_busqueda_historial_paciente')
    $.post(ruta, null, function(respuesta) {
        if (respuesta.length > 0) { //historicoPacientePagos
            if($('#historicoPacientePagos').length > 0){
                $('#historicoPacientePagos').removeClass('hide')
                $('#recaudacion-informacion-paciente').addClass('hide')
                $('#recaudacion-resumen-informacion-paciente').removeClass('hide')
            }
            $('#historicoPacientePagos').html("");
            $('#historicoPacientePagos').html(respuesta);
        } else {
            $('#historicoPacientePagos').hide().html("");
        }
    });
}

const DatosPrestacion = () => {
    $(".btn-volver2").show();
    poblarResumenPaciente();
    ProfesionalExternoCheck();
    cargaProfesionalesSelect();
    cargaOrigenSelect();
    if($("#rebsol_hermesbundle_PrestacionType_prevision").val() === "" || $("#rebsol_hermesbundle_PrestacionType_prevision").val() === null){
        choisestyle_campos_prevision_api1();
    }else{
        choisestyle_campos_prevision_api1();
    }
    verificaCamposParaMostrarBuscarPrestacionInsumoPaquete();
    // KeyUp del input del buscar prestación.
    //------------------------------>  //verificar nombre del campo y accion
    $("#inputPrestacionBuscar").keypress(function(e) {
        if (e.which === 13) {
            buscarPrestacion();
        }
    });
    $('#inputPrestacionBuscar').on('keyup', function() {
        $('#resultadoInsumoBuscar').html("");
        $('#divInsumoBuscar').hide();
        $('#inputInsumoBuscar').val("");
        buscarPrestacion();
    });
    $("#inputInsumoBuscar").keypress(function(e) {
        if (e.which === 13) {
            buscarInsumo();
        }
    });
    $('#inputInsumoBuscar').on('keyup', function() {
        $('#resultadoPrestacionBuscar').html("");
        $('#divPrestacionBuscar').hide();
        $('#inputPrestacionBuscar').val("");
        buscarInsumo();
    });
    /!*
     $('#inputPrestacionBuscar').keypress(function(){
     $('.fade, .modal').empty().hide();
     });
     *!/
    // Click del botón buscar prestación.
    //------------------------------>  //verificar nombre del campo y accion
    $('#btnPrestacionBuscar').on('click', function() {
        buscarPrestacion();
    });


    $('#btn-pre-pagar').on('click', function() {
            $('.btn-edit-lista-prestaciones').show();
            $('#btneditListaPrestaciones').show();
            $('#agregar-prestacion-insumo-paquete').hide();
            prePagar();
            $('#btn-pre-pagar, .btn-pre-pagar').hide();
            $('.cancelar-desde-prestador').hide;
            $('#cancelar-desde-prestador, #saltosTeporales').hide();
            $("#mediodepago").slideDown();
            localStorage.setItem('totalmonto', parseFloat($("#sumaTotal").text()));
            var Valor_Pagar = localStorage.getItem('totalmonto');
            Caja_Valor_Pagar(Valor_Pagar);
        }
    );

    $('#btn-descuentos').on('click', function(e) {
        var AtencionesArray = new Array();
        var DetalleArray = new Array();
        var aux = 0;

        $('.cambioCantidad').each(function() {
            var id = $(this).closest('input').attr('id');
            var cantidad = $(this).val();
            var Total =parseInt($('#sumaCantidad'+id).text());
            DetalleArray['id'] = id;
            DetalleArray['cantidad'] = cantidad;
            DetalleArray['total'] = Total;
            AtencionesArray[aux] = [DetalleArray['id'], DetalleArray['cantidad'], DetalleArray['total']];
            aux = aux +1;
        });

//       //FYIconsole.log(AtencionesArray);return;

        e.preventDefault();
        bootbox.hideAll();
        $(".modalVerA").html('');
        var rout = Routing.generate("Caja_Diferencia");
        $.ajax({
            type: 'POST',
            url: rout,
            data: { data : AtencionesArray},
            async: false,
            success: function(datar) {
                bootbox.dialog(datar, [
                    {
                        "label": "Aceptar",
                        "class": "btn btn-mini btn-success",
                        "callback": function() {

                        }
                    }, {
                        "label": "<i class='icon-arrow-left'></i> Volver",
                        "class": "btn btn-mini",
                        "callback": function() {
                            $(".modalVerA").html('');
                            bootbox.hideAll();
                        }
                    }

                ]);
            }
        });






    });

    $(".LimpiarConvenioSelect  ").click(function() {
        limpiarconvenio();
        MotrarOcultarBuscarPrestacionInsumoPaquete();
    });

    $("#rebsol_hermesbundle_PrestacionType_prevision").change(function() {
        $('#rebsol_hermesbundle_PrestacionType_plan').val('').trigger('chosen:updated');
        $('#rebsol_hermesbundle_PrestacionType_plan').trigger("liszt:updated");
        $('#rebsol_hermesbundle_PrestacionType_plan').val('').trigger('chosen:updated');
        var idPrevision = $('#rebsol_hermesbundle_PrestacionType_prevision').val();
        var idConvenio = $('#rebsol_hermesbundle_PrestacionType_convenio').val();
        if (idConvenio === "") {

            prestador = idPrevision;
            cargaDatos(prestador);
        }
        //$('.alertafaltaPrestador').slideUp();
        $('#rebsol_hermesbundle_PrestacionType_convenio').prop('disabled', false).trigger("liszt:updated");
    });
    $("#rebsol_hermesbundle_PrestacionType_convenio").change(function() {
        $('#rebsol_hermesbundle_PrestacionType_plan').val('').trigger('chosen:updated');
        $('#rebsol_hermesbundle_PrestacionType_plan').trigger("liszt:updated");
        $('#rebsol_hermesbundle_PrestacionType_plan').val('').trigger('chosen:updated');
        var idPrevision = $('#rebsol_hermesbundle_PrestacionType_prevision').val();
        var idConvenio = $('#rebsol_hermesbundle_PrestacionType_convenio').val();
        verificaConvenioNovacio();
        if (idPrevision !== null) {
            prestador = idConvenio;
            cargaDatos(prestador);
        }
    });
}

const ProfesionalExternoCheck = () => {

    $("#rebsol_hermesbundle_PrestacionType_derivadoCheck").change(function() {
        if (this.checked) {
            $('#derivadoSelect').hide();
            $('#derivadoExterno').slideDown('slow');
            $('#rebsol_hermesbundle_PrestacionType_derivadoSelect').val('').trigger('chosen:updated');
            $('#rebsol_hermesbundle_PrestacionType_derivadoSelect').trigger("liszt:updated");
            $('#rebsol_hermesbundle_PrestacionType_derivadoSelect').val('').trigger('chosen:updated');
        } else {
            $('#derivadoSelect').slideDown('slow');
            $('#derivadoExterno').hide();
            $('#rebsol_hermesbundle_PrestacionType_derivadoExterno').val('').trigger('chosen:updated');
            $('#rebsol_hermesbundle_PrestacionType_derivadoExterno').trigger("liszt:updated");
            $('#rebsol_hermesbundle_PrestacionType_derivadoExterno').val('').trigger('chosen:updated');

        }
        MotrarOcultarBuscarPrestacionInsumoPaquete();
    });

}

const cargaProfesionalesSelect = () => {
    var data = {
        sucursal: idSucursal
    };
    const ruta = Routing.generate("Caja_Consulta_Profesionales");
    $.ajax({
        type: 'get',
        url: ruta,
        async: false,
        data: data,
        success: function (data) {
            data = $.parseJSON(data);
            ///////////////CARGA AMBOS CAMPOS DE PROFESIONALES///////
            $('#rebsol_hermesbundle_PrestacionType_derivadoSelect').html("");
            $('#rebsol_hermesbundle_PrestacionType_derivadoSelect').trigger("liszt:updated");
            var options = '<option value="" disabled="disabled" selected="selected">Seleccionar Profesional</option>';
            for (var i = 0; i < data.length; i++) {
                options += "<option value=" + data[i].id + ">" + data[i].apep + " " + data[i].apem + ", " + data[i].nombre + '</option>';
            }
            ;
            $('#rebsol_hermesbundle_PrestacionType_derivadoSelect').html(options);
            $('#rebsol_hermesbundle_PrestacionType_derivadoSelect').trigger("liszt:updated");
            //if (coreApi == 1) {
                choisestyle_campos_profesionales();
            //}
        }
    });
}

const choisestyle_campos_profesionales = () => {
    //OBSERVACION: debe ejecutarse despues de haber cargado la comuna en una consulta, pues, se escribe sobre la respuesta de DB en el campo, y deja como vacia, la solución fue cargarla con un DELAY(), despues de
    //carga comuna  en el input, esta es funcion pasiregioncomunaBuscaconId(comuna).
    $('#rebsol_hermesbundle_PrestacionType_derivadoSelect').addClass("chosen-select");
    $(".chosen-select").chosen({width: "215px", no_results_text: "No se ha encontrado Profesional"});
}

const cargaOrigenSelect = () => {
    var data = {
        sucursal: idSucursal
    };
    const ruta = Routing.generate("Caja_Consulta_Origen");
    $.ajax({
        type: 'get',
        url: ruta,
        async: false,
        data: data,
        success: function (data) {
            data = $.parseJSON(data);
            ///////////////CARGA AMBOS CAMPOS DE ORIGEN///////
            $('#rebsol_hermesbundle_PrestacionType_origenSelect').html("");
            var options = '<option value="" disabled="disabled" selected="selected">Seleccionar Origen</option>';
            for (var i = 0; i < data.length; i++) {
                options += "<option value=" + data[i].id + ">" + data[i].nombre + '</option>';
            }
            ;
            $('#rebsol_hermesbundle_PrestacionType_origenSelect').html(options);
            /!*if (coreApi == 1) {
                choisestyle_campos_origen();
            }*!/
        }

    });

}

/!*
function choisestyle_campos_origen() {
    //OBSERVACION: debe ejecutarse despues de haber cargado la comuna en una consulta, pues, se escribe sobre la respuesta de DB en el campo, y deja como vacia, la solución fue cargarla con un DELAY(), despues de
    //carga comuna  en el input, esta es funcion pasiregioncomunaBuscaconId(comuna).
    //   $('#rebsol_hermesbundle_PrestacionType_origenSelect').addClass("chosen-select");
    //   $(".chosen-select").chosen({width: "215px", no_results_text: "No se ha encontrado Origen"});
}*!/

const choisestyle_campos_prevision_api1 = () => {
    //OBSERVACION: debe ejecutarse despues de haber cargado la comuna en una consulta, pues, se escribe sobre la respuesta de DB en el campo, y deja como vacia, la solución fue cargarla con un DELAY(), despues de
    //carga comuna  en el input, esta es funcion pasiregioncomunaBuscaconId(comuna).


    $("#rebsol_hermesbundle_PrestacionType_convenio").addClass("chosen-select");
    $(".chosen-select").chosen({width: "215px", no_results_text: "No se ha encontrado Convenio"});
    $('#rebsol_hermesbundle_PrestacionType_prevision').prop('readOnly', true).trigger("liszt:updated");

    // $('#rebsol_hermesbundle_PrestacionType_origenSelect').addClass("chosen-select");
    // $(".chosen-select").chosen({width: "215px", no_results_text: "No se ha encontrado Profesional"});


    $("#rebsol_hermesbundle_PrestacionType_plan").addClass("chosen-select select-plan");
    $("#rebsol_hermesbundle_PrestacionType_plan").chosen({width: "215px",
        allow_single_deselect: true, no_results_text: "No se ha encontrado Plan"
    });
}

const verificaCamposParaMostrarBuscarPrestacionInsumoPaquete = () => {

    $("#rebsol_hermesbundle_PrestacionType_prevision, #rebsol_hermesbundle_PrestacionType_derivadoSelect, #rebsol_hermesbundle_PrestacionType_origenSelect, #rebsol_hermesbundle_PrestacionType_plan, #rebsol_hermesbundle_PrestacionType_convenio").change(function() {
        MotrarOcultarBuscarPrestacionInsumoPaquete();
    });
    $("#rebsol_hermesbundle_PrestacionType_derivadoExterno").blur(function() {
        if ($("#rebsol_hermesbundle_PrestacionType_derivadoExterno").val().length > 3) {
            MotrarOcultarBuscarPrestacionInsumoPaquete();
        }
    });
    //validacion de en cambio del check, se hará directamente con la acción del boton
    //validacion de limpiar convenio, se hará directamente con la acción del boton
}

const MotrarOcultarBuscarPrestacionInsumoPaquete = () => {
    var prevision = $("#rebsol_hermesbundle_PrestacionType_prevision").val();
    var plan = $("#rebsol_hermesbundle_PrestacionType_plan").val();
    var origen = $("#rebsol_hermesbundle_PrestacionType_origenSelect").val();

    if (prevision && plan && origen ) {
        var derivadoSelect = $("#rebsol_hermesbundle_PrestacionType_derivadoSelect").val();
        var derivadoField = $("#rebsol_hermesbundle_PrestacionType_derivadoExterno").val();
        if ($("#rebsol_hermesbundle_PrestacionType_derivadoCheck").is(':checked')) {
            if (derivadoField !== "") {

                if(cajaAgenda == 1){
                    $("#busquedaPrestacionesField").remove();
                }
                $("#agregar-prestacion-insumo-paquete").slideDown("slow");
                $("#saltosTeporales").hide();
                cargaresumenprestadorform();
            } else {
                $("#agregar-prestacion-insumo-paquete").slideUp("slow");
                $("#saltosTeporales").show();
            }
        } else {
            if (derivadoSelect !== null) {
                if(cajaAgenda == 1){
                    $("#busquedaPrestacionesField").remove();
                }
                $("#agregar-prestacion-insumo-paquete").slideDown("slow");
                $("#saltosTeporales").hide();
                cargaresumenprestadorform();
            } else {
                $("#agregar-prestacion-insumo-paquete").slideUp("slow");
                $("#saltosTeporales").show();
            }
        }
    } else {
        $("#agregar-prestacion-insumo-paquete").slideUp("slow");
        $("#saltosTeporales").show();
    }
}

const cargaresumenprestadorform = () => {
    $('#formprestador').slideUp("slow");

    var prevision = $("#rebsol_hermesbundle_PrestacionType_prevision option:selected").html();
    var derivadoSelect = $("#rebsol_hermesbundle_PrestacionType_derivadoSelect option:selected").html();
    var origenSelect = $("#rebsol_hermesbundle_PrestacionType_origenSelect option:selected").html();
    var plan = $("#rebsol_hermesbundle_PrestacionType_plan option:selected").html();
    var derivadoExterno = $("#rebsol_hermesbundle_PrestacionType_derivadoExterno").val();

    if ($("#rebsol_hermesbundle_PrestacionType_convenio").val() === null) {
        var convenio = "No eligió un Convenio";
    } else {
        var convenio = $("#rebsol_hermesbundle_PrestacionType_convenio option:selected").html();
    }

    if ($("#rebsol_hermesbundle_PrestacionType_derivadoSelect ").val() === null) {
        var derivado = derivadoExterno;
    } else {
        var derivado = derivadoSelect;
    }

    $("#conveniospan").html(convenio);
    $("#prestadorspan").html(prevision);
    $("#derivadospan").html(derivado);
    $("#origenspan").html(origenSelect);
    $("#planspan").html(plan);
    $('#prestadorresumen').slideDown("slow");

    if(idReservaAtencion){
        var count = Listaprestaciones.length;
        for (var i = 0; i <count; i++) {
            if(Listaprestaciones[i]){
                agregarPrestacion(Listaprestaciones[i]); //Verificar de dnd se debe agregar agregarPrestacion
                SumarColumna();
            }

        }
    }

}

const SumarColumna = () => {
    var resultVal = 0;
    $('.sumaCantidad').each(function() {
        resultVal += parseFloat($(this).text());
    });
    localStorage.setItem('VariableSumaCantidad', parseFloat(resultVal));
    var vSumaCantidad = localStorage.getItem('VariableSumaCantidad');
    Caja_Valmacena_Total_Suma(vSumaCantidad);

}

const Caja_Valmacena_Total_Suma = (vSumaCantidad) => {
    var data = {
        vSumaCantidad: vSumaCantidad
    };
    $.ajaxSetup({
        cache: false
    });
    const ruta = Routing.generate('Caja_Valmacena_Total_Suma');
    $.ajax({
        type: 'get',
        url: ruta,
        async: false,
        data: data,
        success: function (data) {
            $("#sumaTotal").html(addCommas(data));
        }
    });
}

const addCommas = (nStr) => {
    // cuadraCentavos = 0;
    // posicion = 0;
    console.log(nStr);
    // var x             = nStr.substr(0, nStr.length-3);
    // console.log("x=" + x);
    // if(x == '.00'){
    //   posicion = 2;
    // }
    nStrAux = Math.round(nStr);

    var temp = nStrAux + '';
    console.log('temp '+temp);
    temp = temp.split('.').join().replace(/,/g, '');
    temp = temp + '';
    console.log('temp 2 '+temp);
    // var x2             = nStr.substr(-posicion);
    // var x1             = nStr.substr(0, nStr.length-posicion);
    var x1 = temp;
    // x2Int = parseInt(x2);
    // if(x2Int > 50){
    //   cuadraCentavos = 1;
    // }
    var rgx = /(\d+)(\d{3})/;
    // x1 = parseInt(x1);
    // x1 = x1 + cuadraCentavos;
    // x1 += '';
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }

    return x1;
    // return x1 + x2;
}

const buscarPrestacion = () => {
    var texto = $('#inputPrestacionBuscar').val();
    if (enBusqueda) {
        return false;
    }
    ;
    if (texto.length < 4) {
        return false;
    }
    ;
    var append = $('#iconPrestacionBuscar');
    append.removeClass('icon-asterisk');
    append.addClass('icon-spinner icon-spin');
    $('#resultadoPrestacionBuscar').html('');
    $('#divPrestacionBuscar').hide();

    enBusqueda = true;
    var data = {
        textoBusqueda: texto
    };
    const ruta = Routing.generate('Caja_BuscarPrestacion');
    $.ajax({
        type: 'get',
        url: ruta,
        data: data,
        success: function (data) {
            $('#resultadoPrestacionBuscar').html(data);

            $('#divPrestacionBuscar').show();

            append.removeClass('icon-spinner icon-spin');
            append.addClass('icon-asterisk');
            enBusqueda = false;

        }
    });
}

const buscarInsumo = () => {
    var texto = $('#inputInsumoBuscar').val();
    if (enBusqueda) {
        return false;
    }
    ;
    if (texto.length < 4) {
        return false;
    }
    ;
    var append = $('#iconInsumoBuscar');
    append.removeClass('icon-asterisk');
    append.addClass('icon-spinner icon-spin');
    $('#resultadoInsumoBuscar').html('');
    $('#divInsumoBuscar').hide();

    enBusqueda = true;
    var data = {
        textoBusqueda: texto
    };
    $.ajax({
        type: 'get',
        url: "{{ path('Caja_BuscarInsumos') }}",
        data: data,
        success: function (data) {
            $('#resultadoInsumoBuscar').html(data);
            $('#divInsumoBuscar').show();
            append.removeClass('icon-spinner icon-spin');
            append.addClass('icon-asterisk');
            enBusqueda = false;
        }
    });
}

const prePagar =() =>{

    $('.ace-spinner').hide();
    $('.removePrestacion').hide();

    $('.cambioCantidad').each(function() {
        $(this).closest('td').find('.spanResumen').html($(this).val()).show().closest('td').find('div').first().hide();
    });

}*/

/*const  Caja_Valor_Pagar = (Valor_Pagar) => {
    var data = {
        Valor_Pagar: Valor_Pagar
    };
    $.ajaxSetup({
        cache: false
    });
    $.ajax({
        type: 'get',
        url: "{{ path('Caja_Valor_Pagar') }}",
        async: false,
        data: data,
        success: function (data) {
            IniciamedioPago(addCommas(data));
            IniciamedioPagoPagoParcial(addCommas(data));
        }
    });
}*/

/*
const limpiarconvenio = () => {
    $('#rebsol_hermesbundle_PrestacionType_convenio').val('').trigger('chosen:updated');
    $('#rebsol_hermesbundle_PrestacionType_convenio').trigger("liszt:updated");
    $('#rebsol_hermesbundle_PrestacionType_convenio').val('').trigger('chosen:updated');
    $('#rebsol_hermesbundle_PrestacionType_plan').val('').trigger('chosen:updated');
    $('#rebsol_hermesbundle_PrestacionType_plan').trigger("liszt:updated");
    $('#rebsol_hermesbundle_PrestacionType_plan').val('').trigger('chosen:updated');
    var idPrevision = $('#rebsol_hermesbundle_PrestacionType_prevision').val();
    cargaDatos(idPrevision);
}
*/

/*
const cargaDatos = (datoDesdeSelect) => {
    $('#rebsol_hermesbundle_PrestacionType_plan').prop('disabled', false).trigger("liszt:updated");
    $('.alertaSinDatos').slideUp('slow');
    $.ajaxSetup({
        cache: false
    });
    var data = {
        sucursal: idSucursal,
        dato: datoDesdeSelect
    }
    //Envia por ajax la variable data que contiene el tipo de previsión y sucursal y se dirige a la ruta del controlador del url
    $.ajax({
        type: 'get',
        url: "{{ path('Caja_Consulta_Planes') }}",
        async: false,
        data: data,
        success: function (data) {
            data = $.parseJSON(data);
            if (data) {
                /!*var options = '<option value="" disabled="disabled" selected="selected">Seleccionar Plan</option>';
                for (var i = 0; i < data.length; i++) {
                    options += "<option value=" + data[i].id + ">" + data[i].nombre + '</option>';
                }*!/
                var options = '';
                if($("#rebsol_hermesbundle_PrestacionType_plan").val()=== null){
                    options = '<option value="" disabled="disabled" selected="selected">Seleccionar Plan</option>';
                }
                let seleccionado = '';
                for (var i = 0; i < data.length; i++) {
                    if(parseInt($("#rebsol_hermesbundle_PrestacionType_plan").val()) === data[i].id){
                        seleccionado = data[i].id;
                        options += '<option value="' + data[i].id + '" selected="selected">' + data[i].nombre + '</option>';
                    }else {
                        options += "<option value=" + data[i].id + ">" + data[i].nombre + '</option>';
                    }
                }
                ;
                $('#rebsol_hermesbundle_PrestacionType_plan').html(options);
                //$('.alertafalta').slideUp('slow');
                $('#rebsol_hermesbundle_PrestacionType_plan').prop('disabled', false).trigger("liszt:updated");
                $('.alertaSinDatos').slideUp('slow');
                //choisestyle_campo_plan();
                // $('#rebsol_hermesbundle_PrestacionType_plan').val('').trigger('chosen:updated');
                $('#rebsol_hermesbundle_PrestacionType_plan').val(seleccionado).trigger('chosen:updated');
                $('#rebsol_hermesbundle_PrestacionType_plan').trigger("liszt:updated");
                // $('#rebsol_hermesbundle_PrestacionType_plan').val('').trigger('chosen:updated');
                $('#rebsol_hermesbundle_PrestacionType_plan').val(seleccionado).trigger('chosen:updated');

                //$('.alertafaltaPrestador').hide();
                $('#rebsol_hermesbundle_PrestacionType_plan').prop('disabled', false).trigger("liszt:updated");
                $("#ConvenioSelect").slideDown('slow').fadeIn('slow');
                $('#selectplan').slideDown('slow').fadeIn('slow');
                verificaConvenioNovacio();
                // Desliza el combobox de previsión una vez que la sucursal y el tipo de previsión se hayan seleccionado #

            } else {
                $('.alertaSinDatos').slideDown('slow');
                verificaConvenioNovacio();
            }
        }
    });
}

const verificaConvenioNovacio = () => {
    var idConvenio = $('#rebsol_hermesbundle_PrestacionType_convenio').val();
    if (idConvenio !== "") {
        $('.LimpiarConvenioSelect').show();
    } else {
        $('.LimpiarConvenioSelect').hide();
    }
}*/
