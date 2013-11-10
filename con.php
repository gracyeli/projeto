<?php
	/**
	* Classe de conex�o com o banco de dados e CRUD (Create, Read, Update e Delete)
	*
	*/
error_reporting(E_ALL);
	ini_set('max_execution_time', '300'); /* Tempo m�ximo de execu��o desta classe � de 300 segundos ou 5 minutos */

	class conexao {
		/* Atributos protegidos */
		protected $host;		/* Armazena o host do site */
		protected $dbname;		/* Armazena o nome do banco de dados */
		protected $username;	/* Armazena o nome do usu�rio do banco de dados */
		protected $password;	/* Armazena a senha do usu�rio do banco de dados */
                protected $port;
		/* Atributos privados */
		private $conexao;		/* Instancia um objeto da classe PDO e realiza a conex�o com o banco de dados */
		private $ps;			/* Armazena dados j� preparados contra invas�es (SQL Injection) */
		private $statement;		/* Armazena os comandos SQL ainda sem a limpeza necess�ria */
		private $parametros;	/* Armazena em um vetor os par�metros da consulta escapados quando for necess�rio */
		private $objeto;		/* S�o instanciados v�rios objetos em sequ�ncia, por�m substituindo o anterior */
		private $array_read;	/* Vetor que armazena objetos resultantes da busca */		


		/* M�todo chamado todas as vezes que a classe "conexao" for instanciada */
		public function __construct() {
			/* Atribuimos os valores padr�es em cada atributo. Esses valores podem ser alterados, caso seja necess�rio */
			$this->host = 'localhost';
			$this->dbname = 'trmmNormal';
			$this->username = 'postgres';
			$this->password = '1';
                        $this->port = 5432;

			/* Tratamento de exce��o � realizado para o caso de ocorrer algum erro na conex�o */
			try {
				$this->conexao = new PDO("pgsql:host=$this->host;port=$this->port;dbname=$this->dbname", $this->username, $this->password);
				$this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) {
				echo 'Erro ao realizar a conex�o com o banco de dados: '.$e->getMessage();
			}
		}


		/* M�todo chamado ap�s todas as requisi��es de m�todos da classe. O m�todo realizar� a desconex�o ao banco de dados */
		public function __destruct() {
			/* Destru�mos todos os atributos que armazenam dados da conex�o */
			unset($this->host, $this->dbname, $this->username, $this->password);
			unset($this->conexao, $this->statement, $this->ps, $this->objeto, $this->parametros);
			unset($this->array_read);
			/* unset($method, $statement, $param, $value, $e); */
		}


		/* O m�todo CRUD realiza inser��o, busca, atualiza��o e exclus�o de acordo com SQL informado.
		Os tr�s par�metros s�o obrigat�rios.
		Os par�metros seguem respectivamente a ordem: m�todo desejado, comando SQL e um array com dados para o SQL.
		Os m�todos desejados s�o: create para inserir, read para buscar, update para atualizar e delete para excluir.
		OBS.: Mesmo que n�o sejam necess�rios dados no array, � necess�rio colocar pelo menos um array vazio */
		public function CRUD($method, $statement, $param) {
			$this->array_read = array(); /* Realiza uma limpeza no array para eliminar registros de buscas anteriores */
			if ($method != 'read') /* N�o � necess�rio transa��o para read (leitura) */
				$this->conexao->beginTransaction();

			try {
				$this->ps = $this->conexao->prepare($statement);
				$this->ps->execute($param);

				if ($method == 'create' || $method == 'update' || $method == 'delete') { /* N�o � necess�rio commit e rollBack para read */
					$this->conexao->commit();
					return 1;
				} 
				else if ($method == 'read') {
					while ($this->objeto = $this->ps->fetchObject())
						$this->array_read[] = $this->objeto;

					if (!empty($this->array_read))
						return $this->array_read; /* Caso encontre algo, retorna um array contendo objetos. Cada objeto cont�m atributos das tuplas selecionadas */
					else
						return 0; /* Nenhum resultado encontrado! */
				}
			} catch (PDOException $e) {
				if ($method != 'read') /* N�o � necess�rio commit e rollBack para leitura */
					$this->conexao->rollBack();

				return -1; /* Algo est� errado em sua consulta SQL ou houve falha no processo */
			}
		}
	}
/*
$pg_con = new conexao();
$sql = "select nome from pessoa where idade=25";

       $teste = $pg_con->CRUD('read', 'SELECT nome from pessoa where idade =  ?', array(25));
echo $teste[0]->nome; */
        ?>

