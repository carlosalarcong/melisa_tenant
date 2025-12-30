const busquedaBasica = () => {
    console.log('busquedaBasica')
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
                        ValidaRut();
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
                ValidaRut();
                $("#errorrut").slideUp("slow");
            }

        } else {
            // $('#buscarPaciente_tipoIdentificacion_chzn')[0].style.border = '1px solid red';
        }
    }else{
        $('#mensajeError').removeClass('hide');
    }
}

