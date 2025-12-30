$(document).ready(function() {


	if (CAJA === true) {

		$("#limpiar, #limpiaradv,  .text-error, .ocultarDiv").hide();

		$(document).on('ready', function() {

			$('#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento').on('change', function(e) {

				e.preventDefault();

				if (parseInt(this.value) === 1) {

					$('#obtener-documento-rut').val("");
					$('#obtener-otro-documento').val("");

					$('#buscar-con-rut').hide();
					$('#buscar-con-rut').fadeIn(1);
					$('#buscar-con-otro-documento').fadeOut(1);

				} else if (this.value !== 1) {

					$('#obtener-documento-rut').val("");
					$('#obtener-otro-documento').val("");
					$('#buscar-con-otro-documento').fadeIn(1);
					$('#buscar-con-rut').fadeOut(1);

				}

			});

			$('#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento_chzn_o_0').remove();
			$("#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento option[value='']").remove();
			$("#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento").addClass("chosen-select");

			$(".chosen-select").chosen({
				width: "215px",
				no_results_text: "No se ha encontrado el tipo documento",
				allow_single_deselect: true
			});

			$('#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento_chzn').css({
				'text-align': 'left'
			});


			$('#rutdv').focus();
			$("#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento").val(1).delay(200);
			$('#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento').trigger('liszt:updated');
			$("#limpiar, #limpiaradv,  .text-error, .ocultarDiv").removeClass('hide');

			loadScriptvalidakeyup();
			loadScriptEventosDivBusqueda();
			loadScriptBusquedaRutDv();
			loadScriptEventosDivBusquedaMacro();
			loadScriptBusquedaAvanzada();
			GestionCaja_CajaSinCerrar();
			GestionCaja_InformesCaja();
			anulacionAjax();

			$(".btn-gestionCaja-AbrirCerrar, .dropdown").show();

			$(`${"#rebsol_cajabundle_pagoFarmaciaType_nombrePnatural,"}
				${"#rebsol_cajabundle_pagoFarmaciaType_apellidoPaterno,"}
				${"#rebsol_cajabundle_pagoFarmaciaType_apellidoMaterno,"}
				${"#rebsol_cajabundle_pagoFarmaciaType_fechaNacimiento,"}
				${"#rebsol_cajabundle_pagoFarmaciaType_idSexo,"}
				${"#rutdv2,"}
				${".camposExtranjero"}`).prop("readonly", true);

			$(`${"#rebsol_cajabundle_pagoFarmaciaType_fechaNacimiento,"}
				${"#rebsol_cajabundle_pagoFarmaciaType_idSexo"}
				`).prop('disabled', true);

		});


	} else {


		loadScriptvalidakeyup();
		loadScriptEventosDivBusqueda();
		loadScriptBusquedaRutDv();
		loadScriptEventosDivBusquedaMacro();
		loadScriptBusquedaAvanzada();
		GestionCaja_CajaSinCerrar();
		GestionCaja_InformesCaja();
		anulacionAjax();
		$(".btn-gestionCaja-AbrirCerrar").show();


	}


	window.onbeforeunload = function() {
		if ($('.AlertImedMedioPago').is(':visible')) {
			$('.AlertImedMedioPago').parent().parent().find('.btn-group').show();
			$('.AlertImedMedioPago').hide();
			var numAux = $('.AlertImedMedioPago').parent().parent().find('.formularios_Pago').attr('id').replace('tabla_', '')
			$('#exedente_' + numAux).hide();
			$('#rebsol_hermesbundle_MediosPagoType_exedente_' + numAux).val('');
			$(".Input_checkbox_" + numAux).click();
			$(".bElectronico").prop('readonly', false);
			bootbox.dialog(
				"<div class='alert alert-block alert-warning'>" +
				"<br><i class='icon-warning-sign '></i><strong>Operación Cancelada</strong>,<br>Se ha anulado Correctamente Bono Electrónico IMED." +
				"</div>", [{
					"label": "Aceptar",
					"class": "btn btn-mini",
					"callback": function() {}
				}]);

			var ruta = Routing.generate("Caja_Cierre_Anula_Imed");
			$.ajax({
				type: 'get',
				url: ruta,
				async: false
			});
			return 'Ud, a elegido no terminar el Pago en Caja, y se registra venta de Bono Electónico Imed Activa, Por Precaución, el Bono ha sido Anulado, si decide no salir y continuar, deberá volver a realizar la venta del Bono Electrónico IMED.';
		}
	}



	$('.volver-formulario-busqueda').click(function() {

		$('#busquedaPacientesForm').slideDown(1000);
		$('#sin-resultado-rut').hide();
		$('#formulario-paciente').slideUp(1000);
		$(':input').val('');
		$('.formulario-paciente').trigger('reset');

		$("#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento").val(1).delay(200);
		$('#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento').trigger('liszt:updated');

		$('#obtener-documento-rut').val("");
		$('#obtener-otro-documento').val("");

		$('#buscar-con-rut').hide();
		$('#buscar-con-rut').fadeIn(1);
		$('#buscar-con-otro-documento').fadeOut(1);
		$('#mensaje-tramo-bloqueado').text("");
		$("#actualizar-datos").show();

	});

	$('#continuar-busqueda-por-Rut, #buscar-otro-documento').click(function() {

		var array = $('.obtener-rut').val().split("-"),
			eliminarDespuesDelGuion = $('.obtener-rut').val().replace("-" + array[array.length], ""),

			dataRutPersona = {
				rutPersona: reemplazarTodo(eliminarDespuesDelGuion, '.', ''),
				otroDocumento: $('#obtener-otro-documento').val(),
				idTipoIdentificacionExtranjero: $('#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento').val(),
			};

		var rutaWbRutPersona = Routing.generate('Ruta_Caja_Farmacia_Consulta_Wb_PorRut_Default_Servet');

		$('td.insertar-mensaje-error-comuna > div.input-append > span.add-on').remove();

		$.ajax({
			type: 'POST',
			url: rutaWbRutPersona,
			data: dataRutPersona,
			async: false,
			success: function(resultado) {

				if (resultado.data == true) {

					$('#formulario-paciente').slideDown(1000);

					$("#btn-salvar-edit").show();
					$(".btn-volver").show();
					$('.btn-edit-lista-prestaciones').hide();
					$('#btneditListaPrestaciones').hide();
					$(".alertaExisteDatos").slideDown(400);
					$('#busquedaPacientesForm').hide();
					$('.btn-edit-lista-prestaciones').hide();
					$('#btneditListaPrestaciones').hide();
					$(".alertaExisteFullDatos").slideUp(400);
					$("#formprestador").remove();
					$("#formPrestaciones").remove();

					var tipoDocumento = $("select#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento option:selected").text();
					$('#id-tipo-documento-uno').text(tipoDocumento);

					if (resultado.respuestaConsulta == 'CONSULTA_DATOS_WEB_SERVICES_OPERATIVO') {

						var rutBeneficiario = resultado.arrayBenificiario.rutbenef,
							digitoVerificadorBeneficiario = resultado.arrayBenificiario.dgvbenef,

							nombresBeneficiario = resultado.arrayBenificiario.nombres,
							apellidoPaternoBeneficiario = resultado.arrayBenificiario.apell1,
							apellidoMaternoBeneficiario = resultado.arrayBenificiario.apell2,

							direccionBeneficiario = resultado.arrayBenificiario.direccion,
							codigoComunaBeneficiario = resultado.arrayBenificiario.cdgComuna,
							codigoNacionalidadBeneficiario = resultado.arrayBenificiario.cdgNacionalidad,
							codigoRegionBeneficiario = resultado.arrayBenificiario.cdgRegion,

							descripcionComunaBeneficiario = resultado.arrayBenificiario.desComuna,
							descripcionNacionalidadBeneficiario = resultado.arrayBenificiario.desNacionalidad,
							descripcionRregionBeneficiario = resultado.arrayBenificiario.desRegion,

							fechaFallecimientoBeneficiario = resultado.arrayBenificiario.fechaFallecimiento,
							fechaNacimientoBeneficiario = resultado.arrayBenificiario.fechaNacimiento,
							generoBeneficiario = resultado.arrayBenificiario.genero,
							descripcionGeneroBeneficiario = resultado.arrayBenificiario.generoDes,
							telefonoBeneficiario = resultado.arrayBenificiario.telefono;

						var apellidoPaternoAfiliado = resultado.arrayAfiliado.apell1,
							apellidoMaternoAfiliado = resultado.arrayAfiliado.apell2,
							codigoEstadoAfiliado = resultado.arrayAfiliado.cdgEstado,
							descripcionEstadoAfiliado = resultado.arrayAfiliado.desEstado,
							digitoVerificadorAfiliado = resultado.arrayAfiliado.dgvafili,
							fechaNacimientoAfiliado = resultado.arrayAfiliado.fecnac,
							generorAfiliado = resultado.arrayAfiliado.genero,
							descripcionGeneroAfiliado = resultado.arrayAfiliado.generoDes,
							nombresAfiliado = resultado.arrayAfiliado.nombres,
							rutAfiliado = resultado.arrayAfiliado.rutafili,
							tramoAfiliado = resultado.arrayAfiliado.tramo;

						$("input[name='rutdv2']").val($("input[name='rutdv']").val());
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[nombrePnatural]']").val(nombresBeneficiario);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[apellidoPaterno]']").val(apellidoPaternoBeneficiario);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[apellidoMaterno]']").val(apellidoMaternoBeneficiario);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[fechaNacimiento]']").val(fechaNacimientoBeneficiario);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoFijo]']").val(telefonoBeneficiario);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoMovil]']").val();
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoTrabajo]']").val();
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[direccion]']").val(direccionBeneficiario);

						document.getElementById('rebsol_cajabundle_pagoFarmaciaType_idSexo').value = resultado.sexo;
						var idSexo = $("#rebsol_cajabundle_pagoFarmaciaType_idSexo").val();
						$("#rebsol_cajabundle_pagoFarmaciaType_idSexo").chosen().change();
						$('#rebsol_cajabundle_pagoFarmaciaType_idSexo').trigger('chosen:updated');
						$("#rebsol_cajabundle_pagoFarmaciaType_idSexo").val(idSexo).delay(200, choisestyle());
						$('#rebsol_cajabundle_pagoFarmaciaType_idSexo').trigger('liszt:updated');


						$("input[name='rebsol_cajabundle_pagoFarmaciaType[numero]']").val();
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[resto]']").val();
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[correoElectronico]']").val();

						pasiregioncomunaBusca();
						document.getElementById('rebsol_cajabundle_pagoFarmaciaType_comuna').value = parseInt(codigoComunaBeneficiario);
						var idComuna = $("#rebsol_cajabundle_pagoFarmaciaType_comuna").val();
						$("#rebsol_cajabundle_pagoFarmaciaType_comuna").chosen().change();
						$('#rebsol_cajabundle_pagoFarmaciaType_comuna').trigger('chosen:updated');
						$("#rebsol_cajabundle_pagoFarmaciaType_comuna").val(idComuna).delay(200, choisestyle());
						$('#rebsol_cajabundle_pagoFarmaciaType_comuna').trigger('liszt:updated');

						/* Tramos */
						if (tramoAfiliado == "") {

							$('#info-tramo-bloqueado').removeClass('alert-info');
							$('#info-tramo-bloqueado').addClass('alert-danger');
							$('#info-tramo-bloqueado').delay(700).fadeIn(800).fadeTo(1000, 0.7);

							$('#mensaje-tramo-bloqueado').text(resultado.mensajeTramoBloqueado.replace(/[&\/\\#,+()$~%.'":*?<>{}-]/g, ''));

							/* Financiador */
							document.getElementById('rebsol_hermesbundle_PrestacionType_prevision').value = parseInt(resultado.respuestaPrevisionDos);
							var idPrevision = $("#rebsol_hermesbundle_PrestacionType_prevision").val();
							$("#rebsol_hermesbundle_PrestacionType_prevision").chosen().change();
							$('#rebsol_hermesbundle_PrestacionType_prevision').trigger('chosen:updated');
							$("#rebsol_hermesbundle_PrestacionType_prevision").val(idPrevision).delay(200, choisestyle());
							$('#rebsol_hermesbundle_PrestacionType_prevision').trigger('liszt:updated');

							/* Convenio */
							document.getElementById('rebsol_hermesbundle_PrestacionType_convenio').value = parseInt(resultado.repuestaPrevisionUno);
							var idPrevision = $("#rebsol_hermesbundle_PrestacionType_convenio").val();
							$("#rebsol_hermesbundle_PrestacionType_convenio").chosen().change();
							$('#rebsol_hermesbundle_PrestacionType_convenio').trigger('chosen:updated');
							$("#rebsol_hermesbundle_PrestacionType_convenio").val(idPrevision).delay(200, choisestyle());
							$('#rebsol_hermesbundle_PrestacionType_convenio').trigger('liszt:updated');

						}

						if (tramoAfiliado != "") {

							$('#info-tramo-bloqueado').removeClass('alert-danger');
							$('#info-tramo-bloqueado').addClass('alert-info');
							$('#info-tramo-bloqueado').delay(700).fadeIn(800).fadeTo(1000, 0.7);
							$('#mensaje-tramo-bloqueado').text(resultado.mensajeTramoNoBloqueado.replace(/[&\/\\#,+()$~%.'":*?<>{}-]/g, ''));

							/* Financiador */
							document.getElementById('rebsol_hermesbundle_PrestacionType_prevision').value = parseInt(resultado.repuestaPrevisionUno);
							var idPrevision = $("#rebsol_hermesbundle_PrestacionType_prevision").val();
							$("#rebsol_hermesbundle_PrestacionType_prevision").chosen().change();
							$('#rebsol_hermesbundle_PrestacionType_prevision').trigger('chosen:updated');
							$("#rebsol_hermesbundle_PrestacionType_prevision").val(idPrevision).delay(200, choisestyle());
							$('#rebsol_hermesbundle_PrestacionType_prevision').trigger('liszt:updated');

							/* Convenio */
							document.getElementById('rebsol_hermesbundle_PrestacionType_convenio').value = parseInt(resultado.respuestaPrevisionDos);
							var idPrevision = $("#rebsol_hermesbundle_PrestacionType_convenio").val();
							$("#rebsol_hermesbundle_PrestacionType_convenio").chosen().change();
							$('#rebsol_hermesbundle_PrestacionType_convenio').trigger('chosen:updated');
							$("#rebsol_hermesbundle_PrestacionType_convenio").val(idPrevision).delay(200, choisestyle());
							$('#rebsol_hermesbundle_PrestacionType_convenio').trigger('liszt:updated');
						}

					}


					if (resultado.respuestaConsulta == 'CONSULTA_DATOS_DB_WEB_SERVICES_NO_OPERATIVO') {


						$("input[name='rutdv2']").val(resultado.rutPersona);

						$("input[name='rebsol_cajabundle_pagoFarmaciaType[nombrePnatural]']").val(resultado.nombrePnatural);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[apellidoPaterno]']").val(resultado.apellidoPaternoPnatural);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[apellidoMaterno]']").val(resultado.apellidoMaternoPnatural);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[fechaNacimiento]']").val(resultado.fechaNacimientoPnatural);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoFijo]']").val(resultado.telefonoFijoPersona);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoMovil]']").val(resultado.telefonoMovilPersona);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoTrabajo]']").val(resultado.telefonoTrabajoPersona);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[direccion]']").val(resultado.direccionPersonaDomicilio);

						document.getElementById('rebsol_cajabundle_pagoFarmaciaType_idSexo').value = resultado.sexoPnatural;
						var idSexo = $("#rebsol_cajabundle_pagoFarmaciaType_idSexo").val();
						$("#rebsol_cajabundle_pagoFarmaciaType_idSexo").chosen().change();
						$('#rebsol_cajabundle_pagoFarmaciaType_idSexo').trigger('chosen:updated');
						$("#rebsol_cajabundle_pagoFarmaciaType_idSexo").val(idSexo).delay(200, choisestyle());
						$('#rebsol_cajabundle_pagoFarmaciaType_idSexo').trigger('liszt:updated');


						$("input[name='rebsol_cajabundle_pagoFarmaciaType[numero]']").val(resultado.numeroPersonaDomicilio);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[resto]']").val();
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[correoElectronico]']").val(resultado.correoElectronicoPersona);

						pasiregioncomunaBusca();
						document.getElementById('rebsol_cajabundle_pagoFarmaciaType_comuna').value = parseInt(resultado.comunaPersonaDomicilio);
						var idComuna = $("#rebsol_cajabundle_pagoFarmaciaType_comuna").val();
						$("#rebsol_cajabundle_pagoFarmaciaType_comuna").chosen().change();
						$('#rebsol_cajabundle_pagoFarmaciaType_comuna').trigger('chosen:updated');
						$("#rebsol_cajabundle_pagoFarmaciaType_comuna").val(idComuna).delay(200, choisestyle());
						$('#rebsol_cajabundle_pagoFarmaciaType_comuna').trigger('liszt:updated');

						/* Financiador */
						document.getElementById('rebsol_hermesbundle_PrestacionType_prevision').value = parseInt(resultado.idPrevisonPnaturalPrevision);
						var idPrevision = $("#rebsol_hermesbundle_PrestacionType_prevision").val();
						$("#rebsol_hermesbundle_PrestacionType_prevision").chosen().change();
						$('#rebsol_hermesbundle_PrestacionType_prevision').trigger('chosen:updated');
						$("#rebsol_hermesbundle_PrestacionType_prevision").val(idPrevision).delay(200, choisestyle());
						$('#rebsol_hermesbundle_PrestacionType_prevision').trigger('liszt:updated');

						/* Convenio */
						document.getElementById('rebsol_hermesbundle_PrestacionType_convenio').value = parseInt(resultado.idConvenioPnaturalPrevision);
						var idPrevision = $("#rebsol_hermesbundle_PrestacionType_convenio").val();
						$("#rebsol_hermesbundle_PrestacionType_convenio").chosen().change();
						$('#rebsol_hermesbundle_PrestacionType_convenio').trigger('chosen:updated');
						$("#rebsol_hermesbundle_PrestacionType_convenio").val(idPrevision).delay(200, choisestyle());
						$('#rebsol_hermesbundle_PrestacionType_convenio').trigger('liszt:updated');

						$('#info-tramo-bloqueado').hide();
						if (resultado.descripcionPnaturalPrevision !== null) {
							$('#info-tramo-bloqueado').removeClass('alert-danger');
							$('#info-tramo-bloqueado').removeClass('alert-info');
							$('#info-tramo-bloqueado').addClass('alert-info');
							$('#info-tramo-bloqueado').delay(700).fadeIn(800).fadeTo(1000, 0.7);
							$('#mensaje-tramo-bloqueado').text(resultado.descripcionPnaturalPrevision.toUpperCase());
						}

					}

					if (resultado.respuestaConsulta == 'CONSULTA_DATOS_CON_FECHA_ACTUAL_PREVISION_PNATURAL') {

						$("input[name='rutdv2']").val(resultado.rutPersona);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[nombrePnatural]']").val(resultado.nombrePnatural);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[apellidoPaterno]']").val(resultado.apellidoPaternoPnatural);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[apellidoMaterno]']").val(resultado.apellidoMaternoPnatural);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[fechaNacimiento]']").val(resultado.fechaNacimientoPnatural);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoFijo]']").val(resultado.telefonoFijoPersona);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoMovil]']").val(resultado.telefonoMovilPersona);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoTrabajo]']").val(resultado.telefonoTrabajoPersona);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[direccion]']").val(resultado.direccionPersonaDomicilio);

						document.getElementById('rebsol_cajabundle_pagoFarmaciaType_idSexo').value = resultado.sexoPnatural;
						var idSexo = $("#rebsol_cajabundle_pagoFarmaciaType_idSexo").val();
						$("#rebsol_cajabundle_pagoFarmaciaType_idSexo").chosen().change();
						$('#rebsol_cajabundle_pagoFarmaciaType_idSexo').trigger('chosen:updated');
						$("#rebsol_cajabundle_pagoFarmaciaType_idSexo").val(idSexo).delay(200, choisestyle());
						$('#rebsol_cajabundle_pagoFarmaciaType_idSexo').trigger('liszt:updated');


						$("input[name='rebsol_cajabundle_pagoFarmaciaType[numero]']").val(resultado.numeroPersonaDomicilio);
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[resto]']").val();
						$("input[name='rebsol_cajabundle_pagoFarmaciaType[correoElectronico]']").val(resultado.correoElectronicoPersona);

						pasiregioncomunaBusca();
						document.getElementById('rebsol_cajabundle_pagoFarmaciaType_comuna').value = parseInt(resultado.comunaPersonaDomicilio);
						var idComuna = $("#rebsol_cajabundle_pagoFarmaciaType_comuna").val();
						$("#rebsol_cajabundle_pagoFarmaciaType_comuna").chosen().change();
						$('#rebsol_cajabundle_pagoFarmaciaType_comuna').trigger('chosen:updated');
						$("#rebsol_cajabundle_pagoFarmaciaType_comuna").val(idComuna).delay(200, choisestyle());
						$('#rebsol_cajabundle_pagoFarmaciaType_comuna').trigger('liszt:updated');

						/* Financiador */
						document.getElementById('rebsol_hermesbundle_PrestacionType_prevision').value = parseInt(resultado.idPrevisonPnaturalPrevision);
						var idPrevision = $("#rebsol_hermesbundle_PrestacionType_prevision").val();
						$("#rebsol_hermesbundle_PrestacionType_prevision").chosen().change();
						$('#rebsol_hermesbundle_PrestacionType_prevision').trigger('chosen:updated');
						$("#rebsol_hermesbundle_PrestacionType_prevision").val(idPrevision).delay(200, choisestyle());
						$('#rebsol_hermesbundle_PrestacionType_prevision').trigger('liszt:updated');

						/* Convenio */
						document.getElementById('rebsol_hermesbundle_PrestacionType_convenio').value = parseInt(resultado.idConvenioPnaturalPrevision);
						var idPrevision = $("#rebsol_hermesbundle_PrestacionType_convenio").val();
						$("#rebsol_hermesbundle_PrestacionType_convenio").chosen().change();
						$('#rebsol_hermesbundle_PrestacionType_convenio').trigger('chosen:updated');
						$("#rebsol_hermesbundle_PrestacionType_convenio").val(idPrevision).delay(200, choisestyle());
						$('#rebsol_hermesbundle_PrestacionType_convenio').trigger('liszt:updated');

						$('#info-tramo-bloqueado').hide();

						if (resultado.descripcionPnaturalPrevision !== null) {
							$('#info-tramo-bloqueado').removeClass('alert-danger');
							$('#info-tramo-bloqueado').removeClass('alert-info');
							$('#info-tramo-bloqueado').addClass('alert-info');
							$('#info-tramo-bloqueado').delay(700).fadeIn(800).fadeTo(1000, 0.7);
							$('#mensaje-tramo-bloqueado').text(resultado.descripcionPnaturalPrevision.toUpperCase());
						}

					}


					if (resultado.comprobarEstadoDeFechaDefuncion === true) {

						var mensajeFallecimiento = 'LA PERSONA SELECCIONADA FALLECIÓ';

						$('#info-tramo-bloqueado').removeClass('alert-info alert-warning alert-success');
						$('#info-tramo-bloqueado').addClass('alert-danger');
						$('#mensaje-tramo-bloqueado').text(mensajeFallecimiento.toUpperCase());
						$("#actualizar-datos").hide();

					}



				} else {
					$('#busquedaPacientesForm').hide();
					$('div#sin-resultado-rut').delay(500).fadeIn(800);
				}

			},
			error: function(datar) {}
		});

	});

	$('#actualizar-datos').on('click', function(e) {

		e.preventDefault();

		var array = $('.obtener-rut').val().split("-"),
			eliminarDespuesDelGuion = $('.obtener-rut').val().replace("-" + array[array.length], ""),
			rutPersona = $("input[name='rutdv2']").val(),
			nombrePnatural = $("input[name='rebsol_cajabundle_pagoFarmaciaType[nombrePnatural]']").val(),
			apellidoPaterno = $("input[name='rebsol_cajabundle_pagoFarmaciaType[apellidoPaterno]']").val(),
			apellidoMaterno = $("input[name='rebsol_cajabundle_pagoFarmaciaType[apellidoMaterno]']").val(),
			fechaNacimiento = $("input[name='rebsol_cajabundle_pagoFarmaciaType[fechaNacimiento]']").val(),
			previson = $("#rebsol_hermesbundle_PrestacionType_prevision").val();

		// comuna = $("#rebsol_cajabundle_pagoFarmaciaType_comuna").val();

		if (rutPersona != null && nombrePnatural != null && apellidoPaterno != null && apellidoMaterno != null && fechaNacimiento != null && previson != null) {
			// if (rutPersona != null && nombrePnatural != null && apellidoPaterno != null && apellidoMaterno != null && fechaNacimiento != null && previson != null && comuna != null) {

			datosFormulario = {
				rutdv: reemplazarTodo(eliminarDespuesDelGuion, '.', ''),
				otroDocumento: $('#obtener-otro-documento').val(),
				idTipoIdentificacionExtranjero: $('#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento').val(),
				nombrePnatural: nombrePnatural,
				apellidoPaterno: apellidoPaterno,
				apellidoMaterno: apellidoMaterno,
				fechaNacimiento: fechaNacimiento,
				telefonoFijo: $("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoFijo]']").val(),
				telefonoMovil: $("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoMovil]']").val(),
				telefonoTrabajo: $("input[name='rebsol_cajabundle_pagoFarmaciaType[telefonoTrabajo]']").val(),
				direccion: $("input[name='rebsol_cajabundle_pagoFarmaciaType[direccion]']").val(),
				numero: $("input[name='rebsol_cajabundle_pagoFarmaciaType[numero]']").val(),
				resto: $("input[name='rebsol_cajabundle_pagoFarmaciaType[resto]']").val(),
				comuna: $("select[name='rebsol_cajabundle_pagoFarmaciaType[comuna]']").val(),
				correoElectronico: $("input[name='rebsol_cajabundle_pagoFarmaciaType[correoElectronico]']").val(),
				idSexo: parseInt($("#rebsol_cajabundle_pagoFarmaciaType_idSexo").val()),
				mensajeTramo: $('#mensaje-tramo-bloqueado').text(),
				previson: previson,
				convenio: $('#rebsol_hermesbundle_PrestacionType_convenio').val(),
			};

			$.ajax({
				type: 'POST',
				url: Routing.generate('Ruta_Caja_Farmacia_Actualiza_DatosPersona_Wb_PorRut_Default_Servet'),
				data: datosFormulario,
				async: false,
				success: function(resultado) {


					if (resultado.data == true) {

						var ruta = Routing.generate('Ruta_Caja_Farmacia_Consulta_Paciente_PorRut_Default_Servet');
						// var array = $('.obtener-rut').val().split("-");
						// var eliminarDespuesDelGuion = $('.obtener-rut').val().replace("-" + array[array.length - 1], "");

						var data = {
							rut: $('#rutdv2').val(),
							otroTipoDocumento: $('#obtener-otro-documento').val(),
							idTipoIdentificacionExtranjero: $('#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento').val(),
						};

						$.ajax({
							type: 'GET',
							url: ruta,
							data: data,
							success: function(resultado) {

								$('#formulario-paciente').hide();

								$('#pacienteresumen').html(resultado).slideDown(1000);
								$('#busquedaPacientesForm').hide();
								$('.btn-edit-lista-prestaciones').hide();
								$('#btneditListaPrestaciones').hide();
								$(".btn-volverEdit").show();
								$("#pacienteform").slideUp(400);
								$(".alertaExisteFullDatos").slideUp(400);
								$("#pacienteresumen").slideDown(1000);
								$("#datosprestacion").slideDown(1000);
								$("#formprestador").remove();
								$("#formPrestaciones").remove();
								$("#agregar-prestacion-insumo-paquete").hide(1000);

								$('#tipos-documentos').text($('#rutdv2').val());

								var tipoDocumento = $("select#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento option:selected").text();
								document.cookie = "tipoDocumento=" + tipoDocumento + "";
								$('#id-tipo-documento-dos').text(tipoDocumento);

								var rutaDos = Routing.generate('Ruta_Caja_Farmacia_Consulta_Receta_NoValidas_Default_Servet');

								$.ajax({
									type: 'GET',
									url: rutaDos,
									data: data,
									success: function(resultado) {
										$('#listadoRecetasPaciente').html(resultado).slideDown(1000);
									},
									error: function(datar) {
										falla();
									}
								});

								// creaListaHistorica();
								creaListaHistoricaFarmacia();

							},
							error: function(datar) {
								falla();
							}
						});
					}


				},
				error: function(datar) {}
			});

		} else {

			$('#actualizar-datos').addClass('hide');
			$('#actualizar-datos').delay(100).fadeOut(400);
			$('#actualizar-datos').delay(100).fadeIn(400);

			if (rutPersona == null) {
				$('.insertar-mensaje-error').append('<div id="text-mensaje-error-uno" class="hide text-error">Este campo es requerido</div>');
				$('#text-mensaje-error-uno').delay(500).fadeIn(800);
				$('#text-mensaje-error-uno').fadeOut(1800);
			}

			if (nombrePnatural == null) {
				$('.insertar-mensaje-error').append('<div id="text-mensaje-error-dos" class="hide text-error">Este campo es requerido</div>');
				$('#text-mensaje-error-dos').delay(500).fadeIn(800);
				$('#text-mensaje-error-dos').fadeOut(1800);
			}

			if (apellidoPaterno == null) {
				$('.insertar-mensaje-error').append('<div id="text-mensaje-error-tres" class="hide text-error">Este campo es requerido</div>');
				$('#text-mensaje-error-tres').delay(500).fadeIn(800);
				$('#text-mensaje-error-tres').fadeOut(1800);
			}

			if (apellidoMaterno == null) {
				$('.insertar-mensaje-error').append('<div id="text-mensaje-error-cuatro" class="hide text-error">Este campo es requerido</div>');
				$('#text-mensaje-error-cuatro').delay(500).fadeIn(800);
				$('#text-mensaje-error-cuatro').fadeOut(1800);
			}

			if (fechaNacimiento == null) {
				$('.insertar-mensaje-error').append('<div id="text-mensaje-error-cinco" class="hide text-error">Este campo es requerido</div>');
				$('#text-mensaje-error-cinco').delay(500).fadeIn(800);
				$('#text-mensaje-error-cinco').fadeOut(1800);
			}

			if (previson == null) {
				$('.insertar-mensaje-error').append('<div id="text-mensaje-error-seis" class="hide text-error">Este campo es requerido</div>');
				$('#text-mensaje-error-seis').delay(500).fadeIn(800);
				$('#text-mensaje-error-seis').fadeOut(1800);
			}

			// if (comuna == null) {
			// 	$('.insertar-mensaje-error-comuna').append('<div id="text-mensaje-error-siete" class="hide text-error">Este campo es requerido</div>');
			// 	$('#text-mensaje-error-siete').delay(500).fadeIn(800);
			// 	$('#text-mensaje-error-siete').fadeOut(1800);
			// }

		}

	});



	function reemplazarTodo(text, busca, reemplaza) {
		while (text.toString().indexOf(busca) != -1)
			text = text.toString().replace(busca, reemplaza);
		return text;
	}

	$(document).ready(function() {

		$(".validacionRutJS").blur("click", function() {
			if (!$('#btnBuscarPaciente').hasClass('agendamientoSinRut')) {
				if (Rut($('.validacionRutJS'))) {
					$('#iconRut').removeClass('icon-question-sign');
					$('#iconRut').removeClass('icon-remove-sign red');
					$('#iconRut').addClass('icon-ok-sign green');
					$('#btnBuscarPaciente').attr('disabled', false);
					$('#btnLimpiarPaciente').removeClass('hidden');
				} else {
					$('#iconRut').removeClass('icon-question-sign');
					$('#iconRut').removeClass('icon-ok-sign green');
					$('#iconRut').addClass('icon-remove-sign red');
					$('#btnBuscarPaciente').attr('disabled', true);
					$('#btnLimpiarPaciente').addClass('hidden');
					$('#iconRut').focus();
				}
			}
		});

		$(".busquedaMascotaCampoTexto").on('input', function() {
			if ($(this).val().length >= 3) {
				$(this).parent().parent().find('.btn-buscar').attr('disabled', false);
			} else {
				$(this).parent().parent().find('.btn-buscar').attr('disabled', true)
			}
		});

		$(".validacionRutJS").on('input', function() {
			if ($(".validacionRutJS").val().length > 7) {
				if (Rut($('.validacionRutJS'))) {
					$('#iconRut').removeClass('icon-question-sign');
					$('#iconRut').addClass('icon-ok-sign green');
					$('#btnBuscarPaciente').attr('disabled', false);
					$('#btnLimpiarPaciente').removeClass('hidden');
				} else {
					$('#iconRut').removeClass('icon-question-sign');
					$('#iconRut').removeClass('icon-ok-sign green');
					$('#iconRut').addClass('icon-remove-sign red');
					$('#btnBuscarPaciente').attr('disabled', true);
					$('#btnLimpiarPaciente').addClass('hidden');
					$('#iconRut').focus();
				}
			} else if ($(".validacionRutJS").val().length = 0) {
				$('#iconRut').addClass('icon-question-sign');
				$('#btnBuscarPaciente').attr('disabled', true);
				$('#btnLimpiarPaciente').addClass('hidden');
			}
		});

		$(".validacionRutJS").keypress(function(e) {
			if (e.which === 13) {
				if (!$('#btnBuscarPaciente').hasClass('agendamientoSinRut')) {
					if (Rut($('.validacionRutJS'))) {
						$('#iconRut').removeClass('icon-question-sign');
						$('#iconRut').addClass('icon-ok-sign green');
						$('#btnLimpiarPaciente').removeClass('hidden');
						// buscarPaciente();
					} else {
						$('#iconRut').removeClass('icon-question-sign');
						$('#iconRut').addClass('icon-remove-sign red');
						$('#btnLimpiarPaciente').addClass('hidden');
						$('#iconRut').focus();
					}
				}
			}
		});
	});


	/**
	 * Estupidez temporal
	 */
	function choisestyle() {

		$("#rebsol_hermesbundle_PrestacionType_prevision").addClass("chosen-select");
		$("#rebsol_hermesbundle_PrestacionType_convenio").addClass("chosen-select");
		$("#rebsol_cajabundle_pagoFarmaciaType_comuna").addClass("chosen-select");
		$("#rebsol_cajabundle_pagoFarmaciaType_idSexo").addClass("chosen-select");

		$(".chosen-select").chosen({
			width: "215px",
			no_results_text: "No se ha encontrado Comuna",
			allow_single_deselect: true
		});

		$(`${"#rebsol_cajabundle_pagoFarmaciaType_nombrePnatural,"}
			${"#rebsol_cajabundle_pagoFarmaciaType_apellidoPaterno,"}
			${"#rebsol_cajabundle_pagoFarmaciaType_apellidoMaterno,"}
			${"#rebsol_cajabundle_pagoFarmaciaType_fechaNacimiento,"}
			${"#rebsol_cajabundle_pagoFarmaciaType_idSexo,"}
			${"#rutdv2,"}
			${".camposExtranjero"}`).prop("readonly", true);

		$(`${"#rebsol_cajabundle_pagoFarmaciaType_fechaNacimiento,"}
			${"#rebsol_cajabundle_pagoFarmaciaType_idSexo"}
			`).prop('disabled', true);

	}

	function accionokadvv() {


		// var append = $("#rebsol_cajabundle_pagoFarmaciaType_comuna").closest(".input-append").children('span').children('i');
		// append.removeClass('icon-spinner icon-spin add-on dark-opaque icon-asterisk');
		// append.addClass('icon-ok icon-large green');

		var sexo = $("#rebsol_cajabundle_pagoFarmaciaType_idSexo").closest(".input-append").children('span').children('i');
		sexo.removeClass('icon-spinner icon-spin add-on dark-opaque icon-asterisk');
		sexo.addClass('icon-ok icon-large green');


	}

	function pasiregioncomunaBusca() {

		$("#rebsol_cajabundle_pagoFarmaciaType_comuna").change(function() {

			// $(".mensajeErrorFormulario").hide();
			// var append = $(this).closest(".input-append").children('span').children('i');
			// append.removeClass('icon-asterisk');
			// append.addClass('icon-spinner icon-spin');

			var data = {
				comuna: $(this).val()
			};

			$.ajax({
				type: 'get',
				url: Routing.generate('Caja_ProvinciaporComuna'),
				data: data,
				success: function(datar) {
					datar = $.parseJSON(datar);
					if (datar instanceof Object == false) {} else {

						accionokadvv();
						var provincia = datar["0"];
						var region = datar["1"];
						var pais = datar["2"];
						$("#provincia, #provincialabel").slideDown();
						$("#provincia").html(provincia);
						$("#region, #regionlabel").slideDown();
						$("#region").html(region);
						$("#pais, #paislabel").slideDown();
						$("#pais").html(pais);

					}
				}
			});
		});

	}

});