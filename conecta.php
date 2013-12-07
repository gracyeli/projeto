<?php
	
	include ("con.php");
	
	$ponto = $_POST['idPonto'];
	$datainicio = $_POST['datainicio'];
	$horainicio = $_POST['horainicio'];
	$datafinal = $_POST['datafinal'];
	$horafinal = $_POST['horafinal'];
	
	/*$p_dtinicio = explode('/',$datainicio);
	$dataInicio1 = $p_dtinicio[2].'-'.$p_dtinicio[1].'-'.$p_dtinicio[0];
	return $dataInicio1;
	
	$p_dt = explode('/',$datafinal);
	$dataFinal1 = $p_dt[2].'-'.$p_dt[1].'-'.$p_dt[0];
	return $dataFinal1;*/
	
	$lista_pontos = explode(";", $ponto);
	$tamanho = count($lista_pontos);
	
	for ($j = 0; $j < $tamanho; $j++) {

		echo "<table border='1' style='border-collapse: collapse'>";
		echo "<tr> <td>Identificador</td> <td>Hora</td>	<td>Data</td> <td>Precipitacao</td> <td>Latitude</td>";
		echo "<td>Longitude</td></tr>";
			
		$pg_con = new conexao();
	
		$teste = $pg_con -> CRUD('read', 'SELECT pontosfk, 
			       hora, 
			       data, 
			       precipitac, 
			       latitude, 
			       longitude 
			FROM   pontos 
			       INNER JOIN seriehist 
			               ON pontosfk = ? 
			                  AND gid = ? 
			                  AND data >= ? 
			                  AND data <= ? 
			                  AND hora >= ? 
			                  AND hora <= ? 
			ORDER  BY data, 
		          hora', array($lista_pontos[$j], $lista_pontos[$j], $datainicio, $datafinal, $horainicio, $horafinal));
	
		for ($i = 0; $i < count($teste); $i++) {
				
			echo "<tr>";
				
			echo "<td>" . $teste[$i] -> pontosfk . "</td>";
			echo "<td>" . $teste[$i] -> hora . "</td>";
			echo "<td>" . $teste[$i] -> data . "</td>";
			echo "<td>" . $teste[$i] -> precipitac . "</td>";
			echo "<td>" . $teste[$i] -> latitude . "</td>";
			echo "<td>" . $teste[$i] -> longitude . "</td>";
				
			echo "</tr>";
		}
		
		echo "</table><br>";
	}
?>
