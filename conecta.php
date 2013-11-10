<?php
include("con.php");
/*
$conn = pg_connect('host=localhost  port=5432 dbname=trmmNormal user=postgres password=1') or die('Erro ao conectar.');

print "<h2>NOME DO BANCO CONECTADO</h2>";
  echo pg_dbname(); // mary
  
   if ($dbcon) {
       echo "<br>Conectado com sucesso ao banco: " . pg_dbname($dbcon) .
         " em " .  pg_host($dbcon) . "<br/>\n";
   } else {
       print pg_last_error($dbcon);
       exit;
   }*/
$ponto = $_POST['idPonto'];
$datainicio = $_POST['datainicio'];
$horainicio = $_POST['horainicio'];
$datafinal = $_POST['datafinal'];
$horafinal = $_POST['horafinal'];


$pg_con = new conexao();
$teste = $pg_con->CRUD('read', 'select hora, data, precipitac, latitude, longitude from seriehist,pontos where 
pontosfk = ? and gid = ? and data >= ? and data <= ? and hora >= ? and hora <= ? order by data,hora', 
array($ponto,$ponto,$datainicio,$datafinal,$horainicio,$horafinal));
?>
<table border="1" style="border-collapse: collapse">

<tr><td>Hora</td>
<td>Data</td>
<td>Precipitação</td>
<td>Latitude</td>
<td>Longitude</td>
</tr>
<?php
//$teste = new ArrayObject($teste);
//$teste = $teste->getArrayCopy();

for($i=0;$i<count($teste);$i++){
	echo"<tr>";		
	

	echo"<td>". $teste[$i]->hora ."</td>";
	echo"<td>". $teste[$i]->data ."</td>";
	echo"<td>". $teste[$i]->precipitac ."</td>";
	echo"<td>". $teste[$i]->latitude ."</td>";
	echo"<td>". $teste[$i]->longitude ."</td>";
 
	echo"</tr>";
	//var_dump($teste[$i]);
	
	
}
?>
</table>
<?php
   
?>