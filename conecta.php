                <?php

                include ("con.php");

                $ponto = $_POST['idPonto'];
                $datainicio = $_POST['datainicio'];
                $horainicio = $_POST['horainicio'];
                $datafinal = $_POST['datafinal'];
                $horafinal = $_POST['horafinal'];

                if (isset($_POST['buscar'])) {
                        $operacao = 'buscar';
                } else {
                        $operacao = 'csv';
                        header('Content-type: application/csv');
                        header('Content-Disposition: attachment; filename="pontos.csv"');
                        $arquivo = fopen('php://output', 'w');
                        fputcsv($arquivo, array("ID", "hora", "data", "precipitacao", "latitude", "longitude"));
                 
                }
            
                 $aux = explode('/',$datainicio);
				
                 $aux2 = explode('/',$datafinal);
				
				// Exibe a data no formato para inserir no MySQL (aaaa-mm-dd)
				$Dataformatadai = $aux[2]."-".$aux[1]."-".$aux[0];
				$Dataformatadaf = $aux2[2]."-".$aux2[1]."-".$aux2[0];
				
				
					
                $lista_pontos = explode(";", $ponto);
                $tamanho = count($lista_pontos);

                for ($j = 0; $j < $tamanho; $j++) {

                        if (isset($_POST['buscar'])) {
                                echo "<table border='1' style='border-collapse: collapse'>";
                                echo "<tr> <td>Identificador</td> <td>Hora</td>        <td>Data</td> <td>Precipitacao</td> <td>Latitude</td>";
                                echo "<td>Longitude</td></tr>";
                        }

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
                          hora', array($lista_pontos[$j], $lista_pontos[$j], $Dataformatadai, $Dataformatadaf, $horainicio, $horafinal));

                        for ($i = 0; $i < count($teste); $i++) {

                                if (isset($_POST['buscar'])) {
                                        echo "<tr>";

                                        echo "<td>" . $teste[$i] -> pontosfk . "</td>";
                                        echo "<td>" . $teste[$i] -> hora . "</td>";
                                        echo "<td>" . $teste[$i] -> data . "</td>";
                                        echo "<td>" . $teste[$i] -> precipitac . "</td>";
                                        echo "<td>" . $teste[$i] -> latitude . "</td>";
                                        echo "<td>" . $teste[$i] -> longitude . "</td>";

                                        echo "</tr>";

                                } else {
                                        fputcsv($arquivo, array($teste[$i] -> pontosfk, $teste[$i] -> hora, $teste[$i] -> data, $teste[$i] -> precipitac, $teste[$i] -> latitude, $teste[$i] -> longitude));

                                }
                        }

                        if (isset($_POST['buscar'])) {
                                echo "</table><br>";
                        }
                }

                if (isset($_POST['csv'])) {
                        fclose($arquivo);
                }
                
        ?>