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
				top: 6em;
				left: 550px;
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

			OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
				defaultHandlerOptions : {
					'single' : true,
					'double' : false,
					'pixelTolerance' : 0,
					'stopSingle' : false,
					'stopDouble' : false
				},

				initialize : function(options) {
					this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
					OpenLayers.Control.prototype.initialize.apply(this, arguments);
					this.handler = new OpenLayers.Handler.Click(this, {
						'click' : this.trigger
					}, this.handlerOptions);
				},

				trigger : function(e) {
					var lonlat = map.getLonLatFromViewPortPx(e.xy);
					  lonlat.transform(
                      new OpenLayers.Projection("EPSG:4326"), 
                      new OpenLayers.Projection("EPSG:4326")
                    );
					alert("O ponto que voce clicou foi " + "LATITUDE:"+lonlat.lat + " " + 
														 "LONGITUDE:"+lonlat.lon + " ");
														
				}
			});

			
			var map, infocontrols, highlightlayer;
			function load() {
				var bkey = "AnyGyd4GaAzToU0sDaA0NaXDD88yChcUh8ySoNc32_ddxkrxkl9K5SIATkA8EpMn";
				var bounds = new OpenLayers.Bounds(-61.875, -22.125, -50.2247507568401, -7.34904988281315);
				var opcoes = {
					maxExtent : bounds,
					maxResolution : 0.0577185551452611,
					projection : "EPSG:4326",
					units : 'degrees'
				};

				map = new OpenLayers.Map('map', opcoes);

				var pontos = new OpenLayers.Layer.WMS("Pontos", "http://localhost:8080/geoserver/trmm/wms", {
					LAYERS : 'trmm:pontos'
				}, {
					buffer : 0,
					displayOutsideMaxExtent : true,
					isBaseLayer : true,
					yx : {
						'EPSG:4326' : true
					}
				});

				var bing = new OpenLayers.Layer.Bing({
					name : "Bing Satellite",
					key : bkey,
					type : "AerialWithLabels"
				});
				var road = new OpenLayers.Layer.Bing({
					name : "Bing Mapa",
					type : "Road",
					key : bkey
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

				map.addLayers([pontos, bing, road, highlightLayer]);
				for (var i in infoControls) {
					infoControls[i].events.register("getfeatureinfo", this, showInfo);

					map.addControl(infoControls[i]);
				}
				// Adicionando controles
				map.addControl(new OpenLayers.Control.Navigation());
				map.addControl(new OpenLayers.Control.LayerSwitcher());
				map.addControl(new OpenLayers.Control.MousePosition());
				map.addControl(new OpenLayers.Control.ScaleLine());
				map.addControl(new OpenLayers.Control.OverviewMap());
				map.addControl(new OpenLayers.Control.PanZoomBar());
				map.addControl(new OpenLayers.Control.MousePosition());
				map.addControl(new OpenLayers.Control.OverviewMap());
				map.addControl(new OpenLayers.Control.KeyboardDefaults());

				infoControls.click.activate();
				var click = new OpenLayers.Control.Click();
				map.addControl(click);
				click.activate();

				map.zoomToMaxExtent(bounds);

			}

			var a;
			function showInfo(evt) {
				if (evt.features && evt.features.length) {
					highlightLayer.destroyFeatures();
					highlightLayer.addFeatures(evt.features);
					highlightLayer.redraw();
				} else {
					
					document.getElementById('responseText').innerHTML = evt.text;
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
		<h1 id="title">Gerenciador de dados TRMM</h1>

		<p id="shortdesc">
			Dados de precipitação.
		</p>
		<div id="info">
			<br>
			<br>
			<br>
			<br>
			<h1>Trmm</h1>
			<p>
				Clique no mapa para obter informacoes.
			</p>
			<div id="responseText"></div>

		</div>

		<form action="conecta.php" method="POST">

			ID do ponto:
			<input type="text" name="idPonto"/>
			<br>
			Data de inicio:
			<input type="text" name="datainicio">
			Hora de inicio:
			<input type="text" name="horainicio">
			<br>
			Data final:
			<input type="text" name="datafinal">
			Hora final:
			<input type="text" name="horafinal">
			<br>
			<input type="submit" value="Buscar">
		</form>

		<div id="map" class="smallmap"></div>
		<div id="docs"></div>

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

	</body>
</html>

