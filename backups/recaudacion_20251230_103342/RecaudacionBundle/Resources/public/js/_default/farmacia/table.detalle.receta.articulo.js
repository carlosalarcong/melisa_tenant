$(document).ready(function() {

	$('#datatable-detalle-receta-articulo').dataTable({
		"oLanguage": {
			"sUrl": DATATABLES
		},
		"aoColumns": [{
			sWidth: '9%'
		}, {
			sWidth: '9%'
		}, {
			sWidth: '9%'
		}, {
			sWidth: '9%'
		}, {
			sWidth: '9%'
		}, {
			sWidth: '5%'
		}, {
			sWidth: '9%'
		}, ],
		"initComplete": function(setting) {

			actualizarMontoTotal(setting.aoData);
			var valorTotal = 0;

			Object.keys(setting.aoData).forEach(function(key) {
				valorTotal += parseInt(setting.aoData[key].anCells[6].textContent);
			});

			$('#agregar-total').html('' + ' ( CLP $ ' + '<span id="setear-total-cuenta" class="total-cuenta" data-total-cuenta="' + valorTotal + '" >' + valorTotal + '</span>' + ' )');

		},
		"fnDrawCallback": function(oSettings) {

				actualizarMontoTotal(oSettings.aoData);
				var valorTotal = 0;

				Object.keys(oSettings.aoData).forEach(function(key) {
					valorTotal += parseInt(oSettings.aoData[key].anCells[6].textContent);
				});
				$('#agregar-total').html('' + ' ( CLP $ ' + '<span id="setear-total-cuenta" class="total-cuenta" data-total-cuenta="' + valorTotal + '" >' + valorTotal + '</span>' + ' )');
		}

	});



	function actualizarMontoTotal(aoData) {

		$("input[type=number]").bind('keyup input', function() {

			var valorTotal = 0;

			Object.keys(aoData).forEach(function(key) {
				valorTotal += parseInt(aoData[key].anCells[6].textContent);
			});

			$('#agregar-total').html('' + ' ( CLP $ ' + '<span id="setear-total-cuenta" class="total-cuenta" data-total-cuenta="' + valorTotal + '" >' + valorTotal + '</span>' + ' )');

		});

	}

});