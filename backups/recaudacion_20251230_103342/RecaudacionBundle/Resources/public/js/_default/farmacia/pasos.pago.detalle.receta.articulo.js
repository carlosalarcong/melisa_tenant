$(document).ready(function() {


	$('#btn-pagar').click(function() {

		$('#btn-pagar').css('pointer-events', 'none');

		var htmlBotboox = `<div class="progress progress-success progress-striped active"><div class="bar" style="width: 1%"></div></div>
		<div id="aniadir-text" class="alert alert-block alert-success"><strong class="green">Generando Pago,</strong> <br> Por favor espere...</div>`;

		bootbox.dialog(htmlBotboox).delay(500).promise().done(function() {
			pasoUno();
			return false;
		});

	});

	function pasoUno() {

		localStorage.setItem('CMP_VP', removeComas($('#sumaTotalMediosDePago').text()));
		var CMP_VP = localStorage.getItem('CMP_VP');

		$('.bar').css({
			width: '20%'
		}).delay(100).promise().done(function() {
			pasoDos();
		});

	}

	function reemplazarTodo(text, busca, reemplaza) {
		while (text.toString().indexOf(busca) != -1)
			text = text.toString().replace(busca, reemplaza);
		return text;
	}


	function pasoDos() {

		$('.bar').css({
			width: '40%'
		}).delay(100);

		var CantidadPrestaciones = $('.cambioCantidad').length;
		var ListaDiferencia = [];
		var ListaDiferenciaSaldo = [];

		var detalleRecetaArticulo = $('#datatable-detalle-receta-articulo tbody > tr.tr-column').map(function() {
			var $row = $(this);
			var datos = {
				codArticulo: $row.find('.cod-articulo').text(),
				cantidad: parseInt($row.find(':nth-child(6) .cantidad-articulo').val()),
				total: $.trim($row.find(':nth-child(7)').text()),
				tipoDoc: 2,
			};
			return datos;
		}).get();

		var listaPrestaciones = [];

		Object.keys(detalleRecetaArticulo).forEach(function(key) {

			var a = detalleRecetaArticulo[key].codArticulo;
			var b = detalleRecetaArticulo[key].cantidad;
			var c = detalleRecetaArticulo[key].total;
			var x = detalleRecetaArticulo[key].tipoDoc;
			var prestaciones = [a, b, c, x];
			listaPrestaciones[key] = prestaciones;

		});

		if (esDiferencia == 1) {

			for (i = 0; i < 7; i++) {
				var n = arrayParaDiferencia(i);
				ListaDiferencia[i] = ListaInformacionDiferencia[n];
			}

		}

		if (esDiferenciaSaldo == 1) {

			for (i = 0; i < 4; i++) {
				var n = arrayParaDiferenciaSaldo(i);
				ListaDiferenciaSaldo[i] = ListaInformacionDiferenciaSaldo[n];
			}

		}

		if ($('.mdp').hasClass('active')) {
			var formaPago = 0;
		}

		if ($('.omdp').hasClass('active')) {
			var formaPago = 1;
		}

		// var array = $('.obtener-rut').val().split("-");
		// var eliminarDespuesDelGuion = $('.obtener-rut').val().replace("-" + array[array.length - 1], "");

		// var user = {
		// 	rut: reemplazarTodo(eliminarDespuesDelGuion, '.', '')
		// };

		var rut = $('#rebsol_hermesbundle_PagoType_rutPersona').val();
		var dv = $('#rebsol_hermesbundle_PagoType_digitoVerifivador').val();

		var documentoExtranjero = $('#rebsol_hermesbundle_PagoType_numeroDocumento').val();
		var tipoDocumentoExtranjero = $('#rebsol_hermesbundle_PagoType_documento').val();

		var documentoPorDefecto = rut + '-' + dv;

		if (tipoDocumentoExtranjero == 1) {

			documentoPorDefecto = documentoPorDefecto;
		} else {

			documentoPorDefecto = documentoExtranjero;
		}

		$.ajax({
			type: 'GET',
			url: Routing.generate('Caja_PeopleId'),
			data: {
				'tipoDocumentoExtranjero': tipoDocumentoExtranjero,
				'documentoPorDefecto': documentoPorDefecto
			},
			success: function(data) {

				var data = $.parseJSON(data);

				$('.bar').css({
					width: '40%'
				}).delay(100).promise().done(function() {
					pasoTres(data, listaPrestaciones, formaPago, ListaDiferencia, ListaDiferenciaSaldo);
				});

			}
		});
	}

	function pasoTres(data, listaPrestaciones, formaPago, ListaDiferencia, ListaDiferenciaSaldo) {

		var auxBoleta = "";

		$('.bar').css({
			width: '50%'
		}).delay(100);

		var idReserva = idReservaAtencion;

		if ($('#rebsol_hermesbundle_PrestacionType_derivadoExternoRut').val() != null) {
			var derivadoRutExtAux = $('#rebsol_hermesbundle_PrestacionType_derivadoExternoRut').val();
			var largo = derivadoRutExtAux.replace('-', '').length;
			var r = derivadoRutExtAux.replace('-', '').substr(0, largo - 1);
			var dv = derivadoRutExtAux.replace('-', '').substr(largo - 1, largo);
			var derivadoRutExtArray = [r, dv];
		} else {
			var derivadoRutExtArray = null;
		}

		var formularios = {
			idPnatural: data,
			derivadoRutExt: derivadoRutExtArray,
			caja: idCaja,
			sucursal: idSucursal,
			idReservaAtencion: idReserva,
			TipoDeMedioPago: formaPago,
			ListaPrestacion: listaPrestaciones,
			ListaTratamiento: (esTratamiento == 1) ? TratamientoArray : null,
			ListaDiferencia: (esDiferencia == 1) ? ListaDiferencia : null,
			ListaDiferenciaSaldo: (esDiferenciaSaldo == 1) ? ListaDiferenciaSaldo : null,
			idDerivadoExterno: idDerivadoExterno
		};

		$.ajax({
			type: 'GET',
			url: Routing.generate('Caja_CasePay'),
			data: formularios,
			success: function(respuesta) {

				respuestaTrimUno = $.trim(respuesta);

				if (respuestaTrimUno === 'ok') {

					if (formaPago == 0) {
						var formPagos = $('#formMedios').serializeArray();
					} else {
						var formPagos = $('#formOtros').serializeArray();
					}

					$('.bar').css({
						width: '90%'
					}).delay(100);

					$.ajax({
						type: 'POST',
						url: Routing.generate('Caja_PagoCuenta_RealizarPago_Farmacia_Default_Servet'),
						data: formPagos,
						// timeout: 20000,
						dataType: 'JSON',
						success: function(respuesta) {
							// respuestaTrimDos = $.trim(respuesta);
							if (respuesta.tipo === 'pagado') {

								$(".bar").css({
									width: "100%"
								}).delay(1000);

								$('div.progress').parents('div').fadeOut();

								var mensaje_pagado = `<div class="alert alert-block alert-success"> <p> <strong><i class="icon-ok"></i> Pagado Correctamente</strong></p> </div>`;

								bootbox.dialog(mensaje_pagado, [{
									"label": "Aceptar",
									"class": "btn btn-mini alert",
									"callback": function() {

										if (idReservaAtencion) {
											if (cajaAgenda === 0) {
												$('#calendar').fullCalendar('refetchEvents');
												$('#calendarPanoramica').fullCalendar('refetchEvents');
											}
										}

										bootbox.hideAll();

										$('.cancelar-desde-mediopago').remove();
										$('.realizar-pago-cuenta').remove();
										$("#ocultar-detalle-receta").slideUp(1600);
										$("#realizar-pago-articulos").slideUp(1600);

										var array = $('.obtener-rut').val().split("-");

										var eliminarDespuesDelGuion = $('.obtener-rut').val().replace("-" + array[array.length - 1], "");

										var ruta = Routing.generate('Ruta_Caja_Farmacia_Consulta_Paciente_PorRut_Default_Servet');

										var data = {
											// rut: reemplazarTodo(eliminarDespuesDelGuion, '.', ''),
											rut: $.trim($('#resultado-tipo-documento').text()),
											otroTipoDocumento: $('#obtener-otro-documento').val(),
											idTipoIdentificacionExtranjero: $('#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento').val(),
										};

										$.ajax({
											type: 'GET',
											url: ruta,
											data: data,
											success: function(resultado) {

												$('#pacienteresumen').hide();
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

												var rutaDos = Routing.generate('Ruta_Caja_Farmacia_Consulta_Receta_NoValidas_Default_Servet');

												$.ajax({
													type: 'GET',
													url: rutaDos,
													data: data,
													success: function(resultado) {

														var tipoDocumento = readCookie('tipoDocumento');
														$('#id-tipo-documento-dos').text(tipoDocumento);
														$('#listadoRecetasPaciente').hide();
														$('#listadoRecetasPaciente').html(resultado).slideDown(1000);

														var ruta = Routing.generate('Caja_PostPago_PrintDetallePago'),
														rutaConcatId = ruta.concat('/' + respuesta.idOPagoCuenta);
														$.post(rutaConcatId, function(result) {

															bootbox.dialog(result, [{
																"label": "<i class='icon-arrow-left'></i> Volver",
																"class": "btn-mini",
																"callback": function() {
																	creaListaHistoricaFarmacia();
																	// creaListaHistorica();
																}
															}, {
																"label": "<i class='icon-print'></i> Imprimir",
																"class": "btn-mini btn-success btn-print",
																"callback": function() {
																	creaListaHistoricaFarmacia();
																	// creaListaHistorica();
																	$("#valoridPago").val(respuesta.idOPagoCuenta);
																	$("#formPago").submit();
																}
															}]);
														});

													},
													error: function(datar) {
														falla();
													}
												});

											},
											error: function(datar) {
												falla();
											}
										});

										$('.Input_checkbox').each(function() {
											$(this).prop('checked', false);
											$('.formularios_Pago').css('display', 'none');
										});
										$('.saldoMedio').val('');
									}
								}]);

							} else if (respuesta.tipo === 'varios') {

								if (!idReservaAtencion) {
									creaListaHistoricaFarmacia();
									// creaListaHistorica();
								} else {
									if (cajaAgenda === 0) {
										$('#calendar').fullCalendar('refetchEvents');
										$('#calendarPanoramica').fullCalendar('refetchEvents');
									} else {
										creaListaHistoricaFarmacia();
										// creaListaHistorica();
									}
								}

								$('.bar').css({
									width: '100%'
								}).delay(1000);

								$('div.progress').parents('div').fadeOut();

								var mensaje_varios = `<div class="alert alert-block alert-success"> <p> <strong><i class="icon-ok"></i> Pagado Correctamente</strong></p> </div>`;

								bootbox.dialog(mensaje_varios, [{
									"label": "Aceptar",
									"class": "btn btn-mini alert",
									"callback": function() {

										if (!idReservaAtencion) {
											PagadoAceptarDocumentos();
											bootbox.hideAll();
										} else {
											if (cajaAgenda === 0) {
												$('#calendar').fullCalendar('refetchEvents');
												$('#calendarPanoramica').fullCalendar('refetchEvents');
												bootbox.hideAll();
												verificaCorrelativo();
												PagadoAceptarDocumentos();
											} else {
												PagadoAceptarDocumentos();
											}
										}
										reloadPageFarmacia();
									}

								}]);

							} else if (respuesta.tipo == 1) {

								if (!idReservaAtencion) {
									creaListaHistoricaFarmacia();
									// creaListaHistorica();
								} else {
									if (cajaAgenda === 0) {
										$('#calendar').fullCalendar('refetchEvents');
										$('#calendarPanoramica').fullCalendar('refetchEvents');
									} else {
										creaListaHistoricaFarmacia();
										// creaListaHistorica();
									}
								}

								$('.bar').css({
									width: '100%'
								}).delay(1000);

								var mensaje_1 = `<div class="alert alert-block alert-success"> <p> <strong><i class="icon-ok"></i> Pagado Correctamente</strong></p> </div>`;

								bootbox.dialog(mensaje_1, [{
									"label": "Aceptar",
									"class": "btn btn-mini alert",
									"callback": function() {

										if (!idReservaAtencion) {
											bootbox.hideAll();
										} else {
											if (cajaAgenda === 0) {
												$('#calendar').fullCalendar('refetchEvents');
												$('#calendarPanoramica').fullCalendar('refetchEvents');
											} else {
												bootbox.hideAll();
											}
										}
										PagoConBoleta();
										reloadPageFarmacia();
									}

								}]);

							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							if (textStatus === 'timeout') {
								if (!idReservaAtencion) {
									bootbox.hideAll();
								} else {
									if (cajaAgenda === 0) {} else {
										bootbox.hideAll();
									}
								}
								bootbox.dialog("<strong class='red'>ERROR EN PAGO</strong>,<br><strong>Se agotó el tiempo de ejecución</strong>,<br>No se ha podido efectuar el pago, verifique su conexión y reintente.", [{
									"label": "Aceptar",
									"class": "btn btn-mini alert",
									"callback": function() {
										if (!idReservaAtencion) {
											bootbox.hideAll();
										}

									}
								}]);
							}
						}
					});
} else {
	if (!idReservaAtencion) {
		bootbox.hideAll();
	} else {
		if (cajaAgenda === 0) {
			$('#calendar').fullCalendar('refetchEvents');
			$('#calendarPanoramica').fullCalendar('refetchEvents');
		} else {
			bootbox.hideAll();
		}
	}
	bootbox.dialog("<strong class='red'>ERROR EN PAGO</strong>,<br><strong>Problemas de Comunicación</strong>,<br>No se ha podido efectuar el pago..", [{
		"label": "Aceptar",
		"class": "btn btn-mini alert",
		"callback": function() {

		}
	}]);
}
}
});

}


function readCookie(name) {               
	var nameEQ = name + "=";               
	var ca = document.cookie.split(';');               
	for (var i = 0; i < ca.length; i++) {                              
		var c = ca[i];                              
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);                              
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);               
	}               
	return null;
}

/* PagoConBoleta */
function PagoConBoleta() {

	$.ajax({
		type: 'get',
		url: Routing.generate('Caja_PostPago_Boleta'),
		success: function(respuesta) {
			if (!idReservaAtencion) {
				bootbox.hideAll();
			} else {
				if (cajaAgenda === 0) {} else {
					bootbox.hideAll();
				}
			}
			$(".modalVerBoleta").html('');
			var vistaHtml = respuesta;
			bootbox.dialog(vistaHtml, [{
				"label": "<i class='icon-arrow-left'></i> Aceptar",
				"class": "btn btn-mini",
				"callback": function() {
					$(".modalVerBoleta").html('');
					if (idReservaAtencion) {
						if (cajaAgenda === 0) {
							bootbox.hideAll();
							$("#loading").hide();
						} else {
							bootbox.hideAll();
							verificaCorrelativo();
							PagadoAceptar();
						}
					} else {
						bootbox.hideAll();
						verificaCorrelativo();
						PagadoAceptar();
					}
				}
			}, {
				"label": "<i class='icon-print'></i> Imprimir",
				"class": "btn btn-mini btn-success btn-print",
				"callback": function() {
					bucleBoleta();
					$("#formBoleta").submit();
					if (!idReservaAtencion) {
						bootbox.hideAll();
					} else {
						if (cajaAgenda === 0) {} else {
							bootbox.hideAll();
						}
					}
				}
			}]);
		}
	});
}


/* RELOAD PAGE FARMACIA */

function reloadPageFarmacia() {

	$('.cancelar-desde-mediopago').remove();
	$('.realizar-pago-cuenta').remove();
	$("#ocultar-detalle-receta").slideUp(1600);
	$("#realizar-pago-articulos").slideUp(1600);

	var array = $('.obtener-rut').val().split("-");
	var eliminarDespuesDelGuion = $('.obtener-rut').val().replace("-" + array[array.length - 1], "");
	var ruta = Routing.generate('Ruta_Caja_Farmacia_Consulta_Paciente_PorRut_Default_Servet');
	var data = {
			// rut: reemplazarTodo(eliminarDespuesDelGuion, '.', ''),
			rut: $.trim($('#resultado-tipo-documento').text()),
			otroTipoDocumento: $('#obtener-otro-documento').val(),
			idTipoIdentificacionExtranjero: $('#rebsol_cajabundle_SeleccionarTiposDocumentosType_documento').val(),
		};

		$.ajax({
			type: 'GET',
			url: ruta,
			data: data,
			success: function(resultado) {

				$('#pacienteresumen').hide();
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

				var rutaDos = Routing.generate('Ruta_Caja_Farmacia_Consulta_Receta_NoValidas_Default_Servet');

				$.ajax({
					type: 'GET',
					url: rutaDos,
					data: data,
					success: function(resultado) {

						var tipoDocumento = readCookie('tipoDocumento');
						$('#id-tipo-documento-dos').text(tipoDocumento);
						$('#listadoRecetasPaciente').hide();
						$('#listadoRecetasPaciente').html(resultado).slideDown(1000);
						creaListaHistoricaFarmacia();
						// creaListaHistorica();

					},
					error: function(datar) {
						falla();
					}
				});

			},
			error: function(datar) {
				falla();
			}
		});

		$('.Input_checkbox').each(function() {
			$(this).prop('checked', false);
			$('.formularios_Pago').css('display', 'none');
		});
		$('.saldoMedio').val('');
	}

});