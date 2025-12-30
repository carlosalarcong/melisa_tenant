(function($, document, window, undefined) {

	'use strict'

	var cajaAgenda = 1;

	var DEFAULT_PAGO_CUENTA = DEFAULT_PAGO_CUENTA || {};

	DEFAULT_PAGO_CUENTA = function() {

		this.init();
	};

	$.fn.dataTable.moment( 'DD-MM-YYYY HH:mm');
	let groupColumn = 1;
	let table = $('#mostrar-datos-cuenta-pago-paciente').DataTable({
		columnDefs: [{ visible: false, targets: groupColumn },
			{
				"targets": [7], // Oculta columna (índice 7)
				"visible": false,
				"searchable": false
			},
			{
				"targets": [8], // Oculta la última columna (índice 7)
				"visible": false,
				"searchable": false
			},
			{
				targets: '_all', // Deshabilita ordenamiento en todas las columnas
				orderable: false
			}
		],
		order: [[groupColumn, 'desc']],
		displayLength: 25,
		drawCallback: function (settings) {
			let api = this.api();
			let rows = api.rows({ page: 'current' }).nodes();
			let last = null;

			api.column(groupColumn, { page: 'current' })
				.data()
				.each(function (group, i) {
					if (last !== group) {
						let columnIdPaciente = api.cell(rows[i], 7).data();
						let columnIdEstadoDetalleTalonario = api.cell(rows[i], 8).data();

						let ruta = Routing.generate('Caja_PostPago_PrintDetalleAtencion',{id: columnIdPaciente});
						let rutaDetalleBoleta = Routing.generate('Caja_PostPago_PrintDetalleBoleta',{id: columnIdPaciente});

						/*
						  se comenta código de validación para anular pago multiple
						  en standby
						 */
						// if (CAJA_ANULAR_PAGO_PAGO_CUENTA_MULTIPLE) {
						// 	$(rows)
						// 		.eq(i)
						// 		.before(
						// 			'<tr class="group" style="background-color: #dde1e3 !important"><td colspan="5">' +
						// 			group +
						// 			'</td><td colspan="2" style="text-align: center;">' +
						// 			'                        <a title="Anular Pagos Seleccionados" class="btn btn-mini btn-danger anular-multiple"\n' +
						// 			'                        >\n' +
						// 			'                            <i class="icon-remove icon-large"></i>\n' +
						// 			'                        </a>'
						// 			+'</td><tr>'
						// 		);
						// } else {

						let botonPrintDetalleBoleta = '';
						if (columnIdEstadoDetalleTalonario == 1){
							botonPrintDetalleBoleta = '<a title="Ver Comprobante de Pago"\n' +
								'                               class="btn btn-mini btn-mini-doc btn-info Print-Detalle-Boleta"\n' +
								'                               href="'+ rutaDetalleBoleta +'"\n' +
								'                            >\n' +
								'                                <i class="icon-file-text icon-large"></i>\n' +
								'                            </a>';
						}

							$(rows)
								.eq(i)
								.before(
									'<tr class="group" style="background-color: #dde1e3 !important"><td colspan="5">' +
									group +
									'</td><td colspan="2" style="text-align: center;">' +
									'<a title="Ver Detalle Atención"\n' +
									'                           class="btn btn-mini  btn-mini-doc Print-Detalle-Atencion remove-botones-index-historial-pago"\n' +
									'                           href="'+ruta+'"\n' +
									'                        >\n' +
									'                            <i class="icon-stethoscope icon-large"></i>\n' +
									'                        </a>'+botonPrintDetalleBoleta+'</td><tr>'
								);
						// }


						last = group;
					}
				});
		}
	});


// Order by the grouping
// 	$('#mostrar-datos-cuenta-pago-paciente tbody').on('click', 'tr.group', function () {
// 		var currentOrder = table.order()[0];
// 		if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
// 			table.order([groupColumn, 'desc']).draw();
// 		}
// 		else {
// 			table.order([groupColumn, 'asc']).draw();
// 		}
// 	});

	$.fn.dataTable.moment( 'DD-MM-YYYY HH:mm');
	let tableTutor = $('#mostrar-datos-cuenta-pago-paciente-tutor').DataTable({
		columnDefs: [{ visible: false, targets: groupColumn },
			{
				"targets": [8], // Oculta columna (índice 8)
				"visible": false,
				"searchable": false
			},
			{
				"targets": [9], // Oculta la última columna (índice 9)
				"visible": false,
				"searchable": false
			},
			{
				targets: '_all', // Deshabilita ordenamiento en todas las columnas
				orderable: false
			}
		],
		order: [[groupColumn, 'desc']],
		displayLength: 25,
		drawCallback: function (settings) {
			let api = this.api();
			let rows = api.rows({ page: 'current' }).nodes();
			let last = null;

			api.column(groupColumn, { page: 'current' })
				.data()
				.each(function (group, i) {
					if (last !== group) {
						let columnIdPaciente = api.cell(rows[i], 8).data();
						let columnIdEstadoDetalleTalonario = api.cell(rows[i], 9).data();

						let ruta = Routing.generate('Caja_PostPago_PrintDetalleAtencion',{id: columnIdPaciente});
						let rutaDetalleBoleta = Routing.generate('Caja_PostPago_PrintDetalleBoleta',{id: columnIdPaciente});

						let botonPrintDetalleBoleta = '';
						if (columnIdEstadoDetalleTalonario == 1){
							botonPrintDetalleBoleta = '<a title="Ver Comprobante de Pago"\n' +
								'                               class="btn btn-mini btn-mini-doc btn-info Print-Detalle-Boleta"\n' +
								'                               href="'+ rutaDetalleBoleta +'"\n' +
								'                            >\n' +
								'                                <i class="icon-file-text icon-large"></i>\n' +
								'                            </a>';
						}

						$(rows)
							.eq(i)
							.before(
								'<tr class="group" style="background-color: #dde1e3 !important"><td colspan="6">' +
								group +
								'</td><td colspan="2" style="text-align: center;">' +
								'<a title="Ver Detalle Atención"\n' +
								'                           class="btn btn-mini  btn-mini-doc Print-Detalle-Atencion remove-botones-index-historial-pago"\n' +
								'                           href="'+ruta+'"\n' +
								'                        >\n' +
								'                            <i class="icon-stethoscope icon-large"></i>\n' +
								'                        </a>'+botonPrintDetalleBoleta+'</td><tr>'
							);

						last = group;
					}
				});
		}
	});

	$(".Print-Detalle-Pago").on('click', function(e) {
		e.preventDefault();
		$(".modalVerA").html('');
		$(".modalVerA").load($(this).attr('href'), function(response, status, xhr) {

			var vistaHtml = response;
			bootbox.dialog(vistaHtml, [
				{
					"label": "<i class='icon-arrow-left'></i> Volver",
					"class": "btn btn-mini",
					"callback": function() {
						$(".modalVerA").html('');
					}
				},
				{
					"label": "<i class='icon-print'></i> Imprimir",
					"class": "btn btn-mini btn-success btn-print",
					"callback": function() {
						$("#formPago").submit();

					}
				}
			]);
		});
	});

	$(".Print-Detalle-Atencion").on('click', function(e) {
		e.preventDefault();
		$(".modalVerA").html('');
		$(".modalVerA").load($(this).attr('href'), function(response, status, xhr) {

			var vistaHtml = response;
			bootbox.dialog(vistaHtml, [
				{
					"label": "<i class='icon-arrow-left'></i> Volver",
					"class": "btn btn-mini",
					"callback": function() {
						$(".modalVerA").html('');
					}
				},
				{
					"label": "<i class='icon-print'></i> Imprimir",
					"class": "btn btn-mini btn-success btn-print",
					"callback": function() {
						$("#formAtencion").submit();
					}
				}
			]);
		});
	});

	$(".Print-Detalle-Boleta").on('click', function(e) {
		e.preventDefault();
		var a = idReservaEmpty();
		$("."+a).load($(this).attr('href'), function(response, status, xhr) {
			idReservaEmpty();
			var vistaHtml = response;
			bootbox.dialog(vistaHtml, [
				{
					"label": "<i class='icon-arrow-left'></i> Volver",
					"class": "btn btn-mini",
					"callback": function(){
						idReservaEmpty();
						verificaCorrelativo();
					}
				},

				{
					"label": "<i class='icon-print'></i> Imprimir",
					"class": "btn btn-mini btn-success btn-print",
					"callback": function() {
						$( "#formBoleta" ).submit();
					}
				}
			]);
		});
	});

	function anulacionPagoPagoCuenta(href, tipomensaje, tipoboton) {
		bootbox.dialog(
			"<div class='alert alert-block alert-danger loadAnulaPago'>" +
			"<br><strong>Anulará " + tipomensaje + "</strong>,<br>¿Está seguro de querer continuar?." +
			"</div>"
			, [
				{
					"label": "Cancelar",
					"class": "btn btn-mini",
					"callback": function () {
					}
				},
				{
					"label": tipoboton,
					"class": "btn btn-mini btn-danger",
					"callback": function () {
						$(this).closest('.modal').modal('hide');
						let bootAnulandoPago = bootbox.dialog(
							"<div class='alert alert-block alert-success loadAnulaPago'>" +
							"<div id='cargando'><br><br><br><center><h3><i class='icon-spinner icon-spin icon-large icon-4x'></i><div class='clearfix'></div><br><strong> Eliminando</strong></h3></center></div>" +
							"</div>");

						$.post(href, function (response) {
							var response = $.parseJSON(response);
							if (response.done == 1) {
								$(this).closest('.modal').modal('hide');
								var div = "<div class='alert alert-block alert-success'><p><strong><i class='icon-ok'></i> Anulado Correctamente</strong></p>";
								bootAnulandoPago.modal("hide")
								buscarDatosPersona();
							} else {
								$(this).closest('.modal').modal('hide');
								var div = "<div class='alert alert-block alert-danger'><p><strong><i class='icon-ok'></i> Se produjo un Error en Anulación</strong></p>";
								bootAnulandoPago.modal("hide")
								buscarDatosPersona();
							}
							$(this).closest('.modal').modal('hide');
							bootbox.dialog(div, [
								{
									"label": "Ok",
									"class": "btn btn-mini btn-info",
									"callback": function () {
										if (response == 1) {
											$(".modalAnular").html('');
											$(".widget-box").removeClass("collapsed");
											$('.icon-list-historico').attr('class', function () {
												return $(this).attr('class').replace('icon-chevron-down', 'icon-chevron-up');
											}).delay(500);
											bootbox.hideAll();
										} else {
											$(".modalAnular").html('');
											bootbox.hideAll();
										}
									}
								}
							]);
						});
					}
				}
			]);
	}

	$(".anular").on('click', function (e) {
		$(".modalAnular").html('');
		e.preventDefault();
		var href = $(this).attr('href');
		var ruta = Routing.generate("Caja_Pregunta_Pago_esImed");
		var data = {paciente: $(this).data('id')};
		console.log(href)
		console.log(ruta)
		console.log(data)
		// return false
		$.ajax({
			type: 'get',
			url: ruta,
			data: data,
			success: function (datar) {
				if (datar == 0) {
					var tipoMensaje = "el Pago seleccionado";
					var tipoboton = "Anular Pago";
					console.log(datar);
					$(this).closest('.modal').modal('hide');
					anulacionPagoPagoCuenta(href, tipoMensaje, tipoboton);
				} else {
					var tipoMensaje = "un Pago que registra un Bono Electrónico IMED, esto anulará a su vez el Bono Generado";
					var tipoboton = "Anular Pago y Bono Electrónico";
					console.log(datar);
					$(this).closest('.modal').modal('hide');
					anulacionPagoPagoCuenta(href, tipoMensaje, tipoboton);
				}
			}
		});
	});


	DEFAULT_PAGO_CUENTA.prototype = {

		init: function() {
			this.eventosClickPagar();
		},
		defaultAjax: function(parametersArray) {

			var _self = this;
			var parametersArrayAjax = parametersArrayAjax || {};
			var setParametersArray = setParametersArray || {};

			if (typeof parametersArray['type'] !== 'undefined') {

				parametersArrayAjax['type'] = parametersArray['type'];
			}

			if (typeof parametersArray['url'] !== 'undefined') {

				parametersArrayAjax['url'] = parametersArray['url'];
			}

			if (typeof parametersArray['async'] !== 'undefined') {

				parametersArrayAjax['async'] = parametersArray['async'];
			}

			if (typeof parametersArray['data'] !== 'undefined') {

				parametersArrayAjax['data'] = parametersArray['data'];
			}

			if (typeof parametersArray['setParameterId'] !== 'undefined') {

				parametersArrayAjax['success'] = function(result) {

					setParametersArray['getSwitchParameter'] = parametersArray['setSwitchParameter'];
					setParametersArray['getParameterId'] = parametersArray['setParameterId'];
					setParametersArray['getResult'] = result;
					_self.getAjaxResult(setParametersArray);
				};
			} else {

				parametersArrayAjax['success'] = function(result) {

					setParametersArray['getSwitchParameter'] = parametersArray['setSwitchParameter'];

					if (parametersArray['setParametersAdditional'] !== 'undefined') {

						setParametersArray['getParametersAdditional'] = parametersArray['setParametersAdditional'];
					}

					setParametersArray['getResult'] = result;
					_self.getAjaxResult(setParametersArray);

				};

			}

			if (typeof parametersArray['parameterErrorAjax'] === 'pagado') {

				parametersArrayAjax['error'] = function(jqXHR, textStatus, errorThrown) {
					falla();
				};

			} else if (typeof parametersArray['parameterErrorAjax'] === 'realizarPago') {
				parametersArrayAjax['error'] = function(jqXHR, textStatus, errorThrown) {

					var mensageBootboxError = `<strong class='red'>ERROR EN PAGO</strong>,
					<br><strong>Se agotó el tiempo de ejecución</strong>,
					<br>No se ha podido efectuar el pago, verifique su conexión y reintente.`;

					bootbox.dialog(mensageBootboxError, [{
						"label": "Aceptar",
						"class": "btn btn-mini alert",
						"callback": function() {
							bootbox.hideAll();
						}
					}]);

				};
			}

			$.ajax(parametersArrayAjax);

		},
		getAjaxResult: function(arrayResultado) {

			var _self = this;

			switch (arrayResultado['getSwitchParameter']) {

				case 'insertarHtmlParaVista':

					$(arrayResultado['getParameterId']).html(arrayResultado['getResult']).hide().slideDown(1000);
					$('.realizar-pago-cuenta').css('pointer-events', 'visible')
						.css('display', 'none');
					break;

				case 'cargandoDatosPasoTres':

					arrayResultado['getParametersAdditional']['respuestaTrim'] = $.trim(arrayResultado['getResult']);
					_self.preCargandoDatosPasoTres(arrayResultado['getParametersAdditional']);
					break;

				case 'preCargandoDatosPasoTres':
					_self.resultadoPreCargandoDatosPasoTres(arrayResultado);
					break;
			}
		},
		eventosClickPagar: function() {

			var _self = this,
				parametersArrayInsertaListadoPagoCuenta = parametersArrayInsertaListadoPagoCuenta || {},
				parametersArrayInsertarFormularioDePago = parametersArrayInsertarFormularioDePago || {},
				parametersArrayInsertarResguardoFinanciero = parametersArrayInsertarResguardoFinanciero || {},
				parametersArrayCasePay = parametersArrayCasePay || {};

			$('.pagar-cuenta-pago').on('click', function(e) {
				console.log('eventosClickPagar::pagar-cuenta-pago')
				e.preventDefault();
				var datosAdicionalesCuenta = JSON.parse($(this).closest('td').find('#datos-adicionales').val());
				console.log(datosAdicionalesCuenta);
				bootbox.dialog(
				    "<div class='alert alert-block alert-success modalEsperaGarantia '>" +
				    " <i class='icon-spinner icon-spin green bigger-125'> </i><strong class='green'> Generando Información </strong><br>Por favor espere..." +
				    "<div class='clearfix'></div>" +
				    "</div>"
				).delay(500).promise().done(function () {
					$('.bar').css({
						width: '20%'
					}).delay(100).promise().done(function() {
						_self.preparaDatos(datosAdicionalesCuenta,parametersArrayInsertarResguardoFinanciero, parametersArrayCasePay);
					});
				});
			});

			$('.realizar-pago-cuenta').on('click', function(e) {

				e.preventDefault();

				$('.realizar-pago-cuenta').css('pointer-events', 'none');

				var htmlBotboox = `<div class="progress progress-success progress-striped active">
										<div class="bar" style="width: 1%"></div>
									</div>
									<div id="aniadir-text" class="alert alert-block alert-success">
										<strong class="green">Generando Pago,</strong>
										<br>
										Por favor espere...
									</div>`;

				bootbox.modal(htmlBotboox).delay(500).promise().done(function(event) {

					localStorage.setItem('CMP_VP', removeComas($("#sumaTotalMediosDePago").text()));
					var CMP_VP = localStorage.getItem('CMP_VP');

					$('.bar').css({
						width: '20%'
					}).delay(100).promise().done(function() {
						_self.cargandoDatosPasoDos();
					});
				});

			});

			$('#agregar-botones').on('click', '#volver-mascota', function(e) {
				e.preventDefault();
				$('#mediodepago').slideUp('slow');
				$('#datos-cuenta-paciente').slideDown('slow');
				$('#volver-mascota').remove();
				$('#adicional-informacion').addClass('hide');
			});
		},
		preparaDatos: function(datosAdicionalesCuenta, parametersArrayInsertarResguardoFinanciero, parametersArrayCasePay) {
			console.log('preparaDatos::datosAdicionalesCuenta');
			console.log(datosAdicionalesCuenta);
			var _self = this,
				idPaciente = datosAdicionalesCuenta.idPaciente,
				idDatoIngreso = datosAdicionalesCuenta.idDatoIngreso,
				intMonto = parseInt(datosAdicionalesCuenta.saldoCuenta),
				idPago = datosAdicionalesCuenta.idPagoCuenta,
				nombreTipoCuenta = datosAdicionalesCuenta.nombreTipoCuenta,
				totalCuentaPaquetizado = datosAdicionalesCuenta.totalCuentaPaquetizado == null? 0:datosAdicionalesCuenta.totalCuentaPaquetizado,
				idTipoCuenta = datosAdicionalesCuenta.idTipoCuenta;
				if (datosAdicionalesCuenta.esTutor){
					poblarTutor(datosAdicionalesCuenta);
				}
			console.log('pago.cuenta::cargaPrevision')
			cargaPrevision();
			$('#idPacientePagoCuenta').val(idPaciente);
			$('.omdp').remove();
			$('#input-id-dato-ingreso').val(idDatoIngreso);

			$('#agregar-total-cuenta').text('$'+addCommas(intMonto));


			/** Inserta html reguardo financiero */
			parametersArrayInsertarResguardoFinanciero['setSwitchParameter'] = 'insertarHtmlParaVista';
			parametersArrayInsertarResguardoFinanciero['type'] = 'POST';
			parametersArrayInsertarResguardoFinanciero['url'] = Routing.generate('Caja_PagoCuenta_ConsultarDatos_CuentaPaciente_InsertarReguardoFinanciero');
			parametersArrayInsertarResguardoFinanciero['async'] = false;
			parametersArrayInsertarResguardoFinanciero['setParameterId'] = '#insertar-resguardo-financiero';

			parametersArrayInsertarResguardoFinanciero['data'] = {
				idDatoIngreso: idDatoIngreso
			};

			_self.defaultAjax(parametersArrayInsertarResguardoFinanciero);
			PreparaVista(idPago);

			$('#mediodepago').slideDown('slow');
			$('#sumaTotalMediosDePago').text(addCommas(intMonto));
			$('#SaldoTotalMediosDePago').text(addCommas(intMonto));
			$('.formularios_Pago').find('input.Input_num').attr('onkeyup', 'javascript:calculaSaldoParcial()');

			$('#datos-cuenta-paciente').slideUp('slow');
			// $('#cancelar-desde-mediopago').addClass('btn-mini');
			// $('#agregar-botones').append('<a id="volver-mascota" class="btn btn-success">Volver  <a>');

			$('.Input_checkbox').each(function() {
				$(this).prop('checked', false);
				$('.formularios_Pago').css('display', 'none');
			});
			$(".validaBonoOculto").each(function() {
				let campoBono = $(this).attr("id");
				$("#"+campoBono).val("2");
			});
			$('#adicional-informacion').appendTo('#adicional');
			$('#adicional-informacion').removeClass('hide');

			$('.realizar-pago-cuenta').remove();
			// $('#btn-diferencia-saldo').hide();

			$('#agregar-numero-admision').text(datosAdicionalesCuenta.idDatoIngreso);
			$('#agregar-fecha-cuenta').text(datosAdicionalesCuenta.fechaEstadoCuentaLog);
			$('#agregar-estado-cuenta').text(datosAdicionalesCuenta.nombreEstadoCuentaPaciente);

			$('#agregar-paquete').text(nombreTipoCuenta);

			console.log('aqui')
			$('#agregar-monto-paquete').text('$'+addCommas(totalCuentaPaquetizado));
			//$('#datosprestacion').hide();
			parametersArrayCasePay['type'] = 'POST';
			parametersArrayCasePay['url'] = Routing.generate('Caja_CasePay');
			parametersArrayCasePay['async'] = false;
			parametersArrayCasePay['setParameterId'] = undefined;
			parametersArrayCasePay['dataType'] = 'json';

			localStorage.setItem('pagoCuenta.idPaciente', idPaciente);
			parametersArrayCasePay['data'] = {
				idPaciente: idPaciente
			};

			_self.defaultAjax(parametersArrayCasePay);
		},
		cargandoDatosPasoDos: function() {

			$('.bar').css({
				width: '40%'
			}).delay(100);

			var _self = this,
				listaPrestaciones = [],
				listaDiferencia = [],
				listaDiferenciaSaldo = [],
				parametersArrayPeopleId = parametersArrayPeopleId || {},
				indexKey = 0;

			$('.cambioCantidad').each(function() {

				var a = $(this).parents().eq(6).attr('name'),
					aa = $(this).parents().eq(5).attr('id').replace("td", ""),
					b = $(this).val(),
					c = removeComas($("#precio" + aa).text()),
					x = $("#tipoDoc" + aa).text(),
					prestaciones = [a, b, c, x];

				listaPrestaciones[indexKey] = prestaciones;
				indexKey++;

				var prestaciones = [];
			});

			if (esDiferencia == 1) {
				for (i = 0; i < 7; i++) {
					var n = arrayParaDiferencia(i);
					listaDiferencia[i] = ListaInformacionDiferencia[n];
				}
			}

			if (esDiferenciaSaldo == 1) {
				for (i = 0; i < 4; i++) {
					var n = arrayParaDiferenciaSaldo(i);
					listaDiferenciaSaldo[i] = ListaInformacionDiferenciaSaldo[n];
				}
			}

			parametersArrayPeopleId['setParametersAdditional'] = {
				'listaPrestaciones': listaPrestaciones,
				'formaPago': 0,
				'listaDiferencia': listaDiferencia,
				'listaDiferenciaSaldo': listaDiferenciaSaldo
			};

			_self.cargandoDatosPasoTres(parametersArrayPeopleId);


		},
		cargandoDatosPasoTres: function(parametersArray) {

			var _self = this,
				parametersArrayCasePay = parametersArrayCasePay || {},
				auxBoleta = '';

			$('.bar').css({
				width: '50%'
			}).delay(100);

			var data = parametersArray['data'],
				listaPrestaciones = parametersArray['listaPrestaciones'],
				formaPago = parametersArray['formaPago'],
				listaDiferencia = parametersArray['listaDiferencia'],
				listaDiferenciaSaldo = parametersArray['listaDiferenciaSaldo'];

			if ($('#rebsol_hermesbundle_PrestacionType_derivadoExternoRut').val() != null) {

				var derivadoRutExtAux = $('#rebsol_hermesbundle_PrestacionType_derivadoExternoRut').val(),
					largo = derivadoRutExtAux.replace('-', '').length,
					r = derivadoRutExtAux.replace('-', '').substr(0, largo - 1),
					dv = derivadoRutExtAux.replace('-', '').substr(largo - 1, largo),
					derivadoRutExtArray = [r, dv];

			} else {
				var derivadoRutExtArray = null;
			}

			var inputIdDatoIngreso = parseInt($('#input-id-dato-ingreso').val()),
				inputMonto = parseInt($('#input-monto').val()),
				formularios = {
					inputIdDatoIngreso: inputIdDatoIngreso,
					inputMonto: inputMonto,
					idPnatural: data,
					derivadoRutExt: derivadoRutExtArray,
					caja: idCaja,
					sucursal: idSucursal,
					ListaPrestacion: listaPrestaciones,
					idReservaAtencion: idReservaAtencion,
					TipoDeMedioPago: formaPago,
					ListaTratamiento: (esTratamiento == 1) ? TratamientoArray : null,
					ListaDiferencia: (esDiferencia == 1) ? listaDiferencia : null,
					ListaDiferenciaSaldo: (esDiferenciaSaldo == 1) ? listaDiferenciaSaldo : null,
					idDerivadoExterno: idDerivadoExterno
				};

			parametersArrayCasePay['setSwitchParameter'] = 'cargandoDatosPasoTres';
			parametersArrayCasePay['type'] = 'GET';
			parametersArrayCasePay['url'] = Routing.generate('Caja_CasePay');
			parametersArrayCasePay['async'] = false;
			parametersArrayCasePay['setParameterId'] = undefined;
			parametersArrayCasePay['setParametersAdditional'] = formularios;
			parametersArrayCasePay['data'] = formularios;

			_self.defaultAjax(parametersArrayCasePay);

		},
		preCargandoDatosPasoTres: function(parametersArray) {

			var _self = this,
				parametersArrayRealizarPago = parametersArrayRealizarPago || {},
				respuestaTrim = parametersArray['respuestaTrim'],
				idReservaAtencion = parametersArray['idReservaAtencion'];

			if (respuestaTrim === 'ok') {

				var formMedios = $('#formMedios').serializeArray(),
					formMediosUniqueValues = [];

				formMedios.forEach(function(data, key) {

					if (data.value !== '') {

						if ($.inArray(data.value, formMediosUniqueValues) === -1) {

							formMediosUniqueValues.push(data);
						}
					}
				});

				$('.bar').css({
					width: '90%'
				}).delay(100);

				parametersArrayRealizarPago['setSwitchParameter'] = 'preCargandoDatosPasoTres';
				parametersArrayRealizarPago['type'] = 'POST';
				parametersArrayRealizarPago['url'] = Routing.generate('Caja_PagoCuenta_RealizarPago_CuentaPaciente');
				parametersArrayRealizarPago['async'] = false;
				parametersArrayRealizarPago['setParameterId'] = undefined;
				parametersArrayRealizarPago['setParametersAdditional'] = parametersArray;
				parametersArrayRealizarPago['data'] = formMediosUniqueValues;
				parametersArrayRealizarPago['parameterErrorAjax'] = 'realizarPago';
				_self.defaultAjax(parametersArrayRealizarPago);

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
					"callback": function() {}
				}]);

			}
		},
		resultadoPreCargandoDatosPasoTres: function(parametersArray) {

			var _self = this,
				parametersArrayConsultaDatos = parametersArrayConsultaDatos || {},
				respuestaTrim = $.trim(parametersArray['getResult']),
				idReservaAtencion = $.trim(parametersArray['getParametersAdditional']['idReservaAtencion']);

			if (typeof respuestaTrim !== 'pagado') {

				$('.bar').css({
					width: '100%'
				}).delay(1000);

				var mensageBootbox = `<div class='alert alert-block alert-success'>
				<p><strong><i class='icon-ok'></i> Pagado Correctamente</strong></p></div>`;

				bootbox.dialog(mensageBootbox, [{
					"label": "Aceptar",
					"class": "btn btn-mini alert",
					"callback": function() {

						buscarDatosPersona();

						var mediodepago = $('#mediodepago').html();
						$('#mediodepago').html(mediodepago);

						bootbox.hideAll();

						$('#mediodepago').slideUp('slow');
						$('#datos-cuenta-paciente').slideDown('slow');
						$('#volver-mascota').remove();
						$('#adicional-informacion').addClass('hide');
						$('#formMedios')[0].reset();
					}
				}]);

			} else if (typeof respuestaTrim !== 'varios') {

				if (!idReservaAtencion) {
					creaListaHistoricaPagoCuenta();
				} else {
					if (cajaAgenda === 0) {
						$('#calendar').fullCalendar('refetchEvents');
						$('#calendarPanoramica').fullCalendar('refetchEvents');
					} else {
						creaListaHistoricaPagoCuenta();
					}
				}

				$('.bar').css({
					width: '100%'
				}).delay(1000);

				$('div.progress').parents('div').fadeOut();

				var mensageBootbox = `<div class='alert alert-block alert-success'>
				<p><strong><i class='icon-ok'></i> Pagado Correctamente</strong></p></div>`;

				bootbox.dialog(mensageBootbox, [{
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
								verificaCorrelativo();
								PagadoAceptarDocumentos();
							} else {
								PagadoAceptarDocumentos();
							}
						}
					}
				}]);
			}

		}
	};

	const poblarTutor = (param) => {
		var nombre1 = param['nombrePnatural'];
		var apep1 = param['apellidoPaterno'];
		var apem1 = param['apellidoMaterno'];
		var fechanacimiento1 = param['fechaNacimiento'];
		var anio_nacim = fechanacimiento1.substr(6, 4);
		var mes_nacim = fechanacimiento1.substr(3, 2);
		var dia_nacim = fechanacimiento1.substr(0, 2);
		$("#rutspan").html(param['rutPaciente']);
		$("#nombrespan").html(nombre1);
		$("#apepspan").html(apep1);
		$("#apemspan").html(apem1);
		$("#fechanspan").html(fechanacimiento1);
		$("#edadspan").html(calcular_edad(dia_nacim, mes_nacim, anio_nacim));
	}

	function arrayParaDiferencia(i) {

		switch (i) {
			case 0:
				n = 'como';
				break;
			case 1:
				n = 'agrupcion';
				break;
			case 2:
				n = 'tipoDiferenciaGlobal';
				break;
			case 3:
				n = 'subTotal';
				break;
			case 4:
				n = 'DifTotal';
				break;
			case 5:
				n = 'FullTotal';
				break;
			case 6:
				n = 'listadoPrestaciones';
				break;
		}
		return n;
	}

	function arrayParaDiferenciaSaldo(i) {

		switch (i) {
			case 0:
				n = 'tipoDiferenciaGlobal';
				break;
			case 1:
				n = 'subTotal';
				break;
			case 2:
				n = 'DifTotal';
				break;
			case 3:
				n = 'FullTotal';
				break;
		}
		return n;
	}

	function buscarDatosPersona() {

        let data = {
			idPaciente: localStorage.getItem('pagoCuenta.idPaciente'),
			idPnaturalTutor: localStorage.getItem('pagoCuenta.idPnaturalTutor')
		};
		$.ajax({
			type: 'POST',
			url: Routing.generate('Caja_PagoCuenta_ConsultarDatos_CuentaPaciente'),
			data: data,
			success: function(result) {

				$('#insertar-pagos-realizados').html('');
				$('#saldototalpagar').removeClass('alert-success').addClass('alert-danger');

				let divDatosCuentaPacietne = $('#datos-cuenta-paciente');
				divDatosCuentaPacietne.fadeIn(880).slideDown(1000);
				divDatosCuentaPacietne.html(result);
			},
			error: function(result) {
				falla();
			}
		});
	}

	if (document.readyState === 'complete') {
		return new DEFAULT_PAGO_CUENTA;
	}

}(jQuery, document, window));
