<!DOCTYPE html>
<html>
	<head>
		<title>TRMM</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

		<script src="OpenLayers-2.12/OpenLayers.js"></script>
		<link rel="stylesheet" href="OpenLayers-2.12/theme/default/style.css" type="text/css">
		<link rel="stylesheet" href="style.css" type="text/css">
		<script src="jquery-2.0.3.min.js"></script>

		<style type="text/css">
			ul, li {
				padding-left: 0px;
				margin-left: 0px;
				list-style: none;
			}
			#info {
				position: absolute;
				top: 1em;
				left: 700px;
			}
			#info table td {
				border: 1px solid #ddd;
				border-collapse: collapse;
				margin: 0;
				padding: 0;
				font-size: 90%;
				padding: .2em .1em;
				background: #fff;
			}
			#info table th {
				padding: .2em .2em;
				text-transform: uppercase;
				font-weight: bold;
				background: #eee;
			}
			tr.odd td {
				background: #eee;
			}
			table.featureInfo caption {
				text-align: left;
				font-size: 100%;
				font-weight: bold;
				padding: .2em .2em;
			}

		</style>
		<script defer="defer" type="text/javascript">
			OpenLayers.ProxyHost = "/cgi-bin/proxy.cgi?url=";
			var map, infocontrols, highlightlayer;
			function load() {
				var bounds = new OpenLayers.Bounds(-61.875, -22.125, -50.2247507568401, -7.34904988281315);

				map = new OpenLayers.Map('map', {
					controls : [new OpenLayers.Control.Navigation(), new OpenLayers.Control.PanZoomBar(), new OpenLayers.Control.LayerSwitcher({
						'ascending' : false
					}), new OpenLayers.Control.ScaleLine(), new OpenLayers.Control.MousePosition(), new OpenLayers.Control.OverviewMap()],
					numZoomLevels : 20,
					maxExtent : bounds,
					maxResolution : 0.025,
					projection : "EPSG:4326",

				});

				var pontos = new OpenLayers.Layer.WMS("Pontos", "http://localhost:8080/geoserver/trmm/wms", {
					LAYERS : 'trmm:MT,pontos'
				}, {
					buffer : 0,
					displayOutsideMaxExtent : true,
					isBaseLayer : true,
					yx : {
						'EPSG:4326' : true
					}
				});

				highlightLayer = new OpenLayers.Layer.Vector("Highlighted Features", {
					displayInLayerSwitcher : false,
					isBaseLayer : false
				});

				infoControls = {
					click : new OpenLayers.Control.WMSGetFeatureInfo({
						url : 'http://localhost:8080/geoserver/trmm/wms',
						title : 'Identify features by clicking',
						layers : [pontos],
						queryVisible : true
					}),
					hover : new OpenLayers.Control.WMSGetFeatureInfo({
						url : 'http://localhost:8080/geoserver/trmm/wms',
						title : 'Identify features by clicking',
						layers : [pontos],
						hover : true,
						// defining a custom format options here
						formatOptions : {
							featureNS : 'http://localhost:8080/geoserver/trmm/wms'
						},
						queryVisible : true
					})
				};

				map.addLayers([pontos, highlightLayer]);

				for (var i in infoControls) {
					infoControls[i].events.register("getfeatureinfo", this, showInfo);

					map.addControl(infoControls[i]);
				}

				infoControls.click.activate();

				map.zoomToMaxExtent(bounds);
				map.setCenter(new OpenLayers.LonLat(-12.625000000000000, -55.875000000000000).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()));

			}

			var a;
			function showInfo(evt) {
				if (evt.features && evt.features.length) {
					highlightLayer.destroyFeatures();
					highlightLayer.addFeatures(evt.features);
					highlightLayer.redraw();
				} else {
					var mt = evt.text.split("<td>")[2].split("</td>")[0];
					//Aqui a gente pega o que esta dentro da caixinha.
					var parte1 = $("#gidbuscado").val();
                    document.getElementById('responseText').innerHTML = evt.text; 
					//Depois que se clica num ponto, se quebra a resposta atras da chave.
					var parte2 = evt.text.split("<td>")[2].split("</td>")[0];

					if (mt == "1") {
						var parte = evt.text.split("<td>")[15].split("</td>")[0];
						//Aqui a gente coloca dentro da caixinha.
						if (parte1 == "") {
							//se nao tiver nada na caixinha, ele coloca o ponto clicado
							$("#gidbuscado").val(parte);
						} else {
							//se ja tiver coisa na caixinha, ele pega o da caixinha e adiciona ponto e virgula e o novo.
							$("#gidbuscado").val(parte1 + "; " + parte);
						}

						document.getElementById('responseText').innerHTML = evt.text;
					} else {
						//Aqui a gente coloca dentro da caixinha.
						if (parte1 == "") {
							//se nao tiver nada na caixinha, ele coloca o ponto clicado
							$("#gidbuscado").val(parte2);
						} else {
							//se ja tiver coisa na caixinha, ele pega o da caixinha e adiciona ponto e virgula e o novo.
							$("#gidbuscado").val(parte1 + "; " + parte2);
						}

						document.getElementById('responseText').innerHTML = evt.text;
					}
				}
			}

			function toggleControl(element) {
				for (var key in infoControls) {
					var control = infoControls[key];
					if (element.value == key && element.checked) {
						control.activate();
					} else {
						control.deactivate();
					}
				}
			}

			function toggleFormat(element) {
				for (var key in infoControls) {
					var control = infoControls[key];
					control.infoFormat = element.value;
				}
			}

			function toggleLayers(element) {
				for (var key in infoControls) {
					var control = infoControls[key];
					if (element.value == 'Specified') {
						control.layers = [pontos];
					} else {
						control.layers = null;
					}
				}
			}

			// function toggle(key);

		</script>

	</head>

	<body onload="load()">
		<div id="info">
			<h1 id="title">Gerenciador de dados TRMM - Dados de precipitação</h1>
			<p id="shortdesc">
				
			</p>
			<form target="_blank"  action="conecta.php" method="POST"  >
				ID do ponto:
				<p>
					<textarea rows="2" id="gidbuscado" required name="idPonto" cols="63"></textarea>
				</p>

				Data de inicio:
				<input type="text" required name="datainicio" size="10" value="AAAA-MM-DD" maxlength="10" onclick="this.value=''">
				Data final:
				<input type="text" required name="datafinal" size="10" value="AAAA-MM-DD" maxlength="10" onclick="this.value=''" >
				<br>
				Hora de inicio:
				<input type="text" required name="horainicio" size="10">
				Hora final:
				<input type="text" required name="horafinal" size="10">
				<br>
				<input type="submit" value="Buscar" name="buscar">
				
				
				<br>
			</form>

			<ul id="control">
				<li>
					<input type="radio" name="controlType" value="click" id="click"
					onclick="toggleControl(this);" checked="checked" />
					<label for="click">Click para mostrar os dados</label>
				</li>
				<li>
					<input type="radio" name="controlType" value="hover" id="hover"
					onclick="toggleControl(this);" />
					<label for="hover">Passe o mouse para mostrar os dados</label>
				</li>
			</ul>
			<ul id="format">
				<li>
					<input type="radio" name="formatType" value="text/html" id="html"
					onclick="toggleFormat(this);" checked="checked" />
					<label for="html">Mostrar dados</label>

				</li>
				<li>
					<input type="radio" name="formatType" value="application/vnd.ogc.gml" id="highlight"
					onclick="toggleFormat(this);" />
					<label for="highlight">Destaque de recursos no mapa</label>
				</li>
			</ul>
			<ul id="layers">
				<li>
					<input type="radio" name="layerSelection" value="Specified" id="Specified"
					onclick="toggleLayers(this);" checked="checked" />
					<label for="Specified">Informacoes dos pontos</label>
				</li>

				<li>
					<input type="radio" name="layerSelection" value="Auto" id="Auto"
					onclick="toggleLayers(this);" />
					<label for="Auto">Informacoes gerais</label>
				</li>
			</ul>
			
			<h1>Trmm</h1>
			<p>
				Clique no mapa para obter informacoes.
			</p>
			<div id="responseText"></div>

		</div>

		<div id="map" class="smallmap"></div>
		<div id="docs"></div>

	</body>
</html>

