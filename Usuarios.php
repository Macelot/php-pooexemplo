<?php

require_once 'CrudUsuario.php';

class Usuarios extends CrudUsuario {
    protected $tabela = 'tblUsuario';

    function descobreTotal($digitou){
        $sql="SELECT count(*) as total FROM $this->tabela WHERE tblUsuario_nome LIKE '%".$digitou."%' OR tblUsuario_email LIKE '%".$digitou."%';";
        //echo $sql;
        $stm = DB::prepare($sql);
        $stm->execute();
        return $stm->fetch();
    }

    function verificaVersao($id){
        $sql="SELECT tblUsuario_versao as versao FROM $this->tabela WHERE tblUsuario_id='".$id."';";
        $stm = DB::prepare($sql);
        $stm->execute();
        return $stm->fetch();
    }

    public function findUnit($id) {
        $sql = "SELECT * FROM $this->tabela WHERE tblUsuario_id = :id";
        $stm = DB::prepare($sql);
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetch();
    }

    public function findUnitJoin($id) {
        $sql = "SELECT tu.tblUsuario_id as idU, ".
        "tu.tblUsuario_nome, ".
        "tu.tblUsuario_email, ".
        "tu.tblUsuario_telefone, ".
        "tu.tblUsuario_senha, ".
        "tu.tblUsuario_salario, ".
        "tu.tblUsuario_cargo, ".
        "tu.tblUsuario_foto, ".
        "tu.tblUsuario_idGerente_tblUsuario, ".
        "tu.tblUsuario_observacoes, ".
        "tu.tblUsuario_sexo, ".
        "tu.tblUsuario_dias, ".
        "tu.tblUsuario_raca, ".
        "tu.tblUsuario_versao, ".
        "tg.tblUsuario_id as idG, ".
        "tg.tblUsuario_nome as nomeGerente ".
        "FROM $this->tabela as tu ".
        "LEFT JOIN $this->tabela as tg ON ".
        "tu.tblUsuario_idGerente_tblUsuario=tg.tblUsuario_id ".
        //"LEFT JOIN tblUsuario_has_tblDepartamento tud ON ".
        //"tu.tblUsuario_id = tud.tblUsuario_tblUsuario_id ".
        //"LEFT JOIN tblDepartamento as td ON ".
        //"tud.tblDepartamento_tblDepartamento_id=td.tblDepartamento_id ".
        "WHERE tu.tblUsuario_id = :id";
        $stm = DB::prepare($sql);
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        //var_dump($stm);
        //echo " coloca ". $id;
        return $stm->fetch();
    }

    public function findAll() {
        $sql = "SELECT * FROM $this->tabela";
        $stm = DB::prepare($sql);
        $stm->execute();
        return $stm->fetchAll();
    }

    public function findAllSelect($digitou,$pagina,$ordem,$tipo) {
		if(!isset($ordem))
			$ordem="tblUsuario_id";
		if(!isset($tipo))
		  $tipo="ASC";
      $sql = "SELECT * FROM $this->tabela WHERE tblUsuario_nome LIKE '%".$digitou."%' OR tblUsuario_email LIKE '%".$digitou."%' ORDER BY ".$ordem." ".$tipo." LIMIT ".$pagina.",10;";
      $stm = DB::prepare($sql);
      //echo $sql;
      $stm->execute();
      return $stm->fetchAll();
    }

    public function findLast() {
        $sql = "SELECT tblUsuario_id FROM $this->tabela Order by tblUsuario_id DESC LIMIT 1";
        //echo $sql;
        $stm = DB::prepare($sql);
        //$stm->bindParam(':nome', $nome_, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetch();
    }

	public function findOne($nome) {
        $sql = "SELECT * FROM $this->tabela WHERE tblUsuario_nome=$this->nome";
        $stm = DB::prepare($sql);
        $stm->execute();
        return $stm->fetchAll();
    }

    public function insert() {
        $sql = "INSERT INTO $this->tabela (
          tblUsuario_nome,
          tblUsuario_email,
          tblUsuario_telefone,
          tblUsuario_senha,
          tblUsuario_salario,
          tblUsuario_cargo,
          tblUsuario_foto,
          tblUsuario_idGerente_tblUsuario,
          tblUsuario_observacoes,
          tblUsuario_sexo,
          tblUsuario_dias,
          tblUsuario_raca,
          tblUsuario_usuarioCadastro,
          tblUsuario_usuarioAlteracao
        )
        VALUES (
          :bdpNome,
          :bdpEmail,
          :bdpTelefone,
          :bdpSenha,
          :bdpSalario,
          :bdpCargo,
          :bdpFoto,
          :bdpIdGerente,
          :bdpObservacoes,
          :bdpSexo,
          :bdpDias,
          :bdpRaca,
          :bdpUsuarioCadastro,
          :bdpUsuarioAlteracao)";
        $stm = DB::prepare($sql);
        $stm->bindParam(':bdpNome', $this->nome);
        $stm->bindParam(':bdpEmail', $this->email);
        $stm->bindParam(':bdpTelefone', $this->telefone);
        $stm->bindParam(':bdpSenha', $this->senha);
        $stm->bindParam(':bdpSalario', $this->salario);
        $stm->bindParam(':bdpCargo', $this->cargo);
        $stm->bindParam(':bdpFoto', $this->foto);
        $stm->bindParam(':bdpIdGerente', $this->idGerente);
        $stm->bindParam(':bdpObservacoes', $this->obs);
        $stm->bindParam(':bdpSexo', $this->sexo);
        $stm->bindParam(':bdpDias', $this->dias);
        $stm->bindParam(':bdpRaca', $this->raca);
        $stm->bindParam(':bdpUsuarioCadastro', $this->usuarioCadastro);
        $stm->bindParam(':bdpUsuarioAlteracao', $this->usuarioAlteracao);
        //echo "sql<br>".$sql."<br> VEJA";
        //var_dump($stm);
        if(!$stm->execute()){
          //erro na inser????o do usu??rio
          return false;
        }else{
          //inserir os departamentos
          //pegar o ultimoUsuario
          $valueLast=$this->findLast($this->nome);
          $idLast=$valueLast->tblUsuario_id;
          //gerar array com os departamentos a serem inseridos
          $idsDep=explode(",",$this->idDepartamento);
          //remover em branco
          array_pop($idsDep);
          foreach ($idsDep as $key => $value) {
            $usuarioHasDep = new UsuariosHasDepartamento();
            $usuarioHasDep->setUsuario($idLast);
  			    $usuarioHasDep->setDepartamento($value);
            if (!$usuarioHasDep->insert()) {
              //erro na inser????o dos departamentos para o usu??rio
              //acho que teria um rollback
              return false;
            }
          }
        }
        return true;
    }


    public function update($id,$versaoTela) {
      //verificar vers??o ainda
      $qual=$this->verificaVersao($id);
      //echo "kkkkk".$qual->versao;
      //echo " vvt".$versaoTela;
      if($qual->versao==$versaoTela){
        $versaoTela++;
        $sql = "UPDATE $this->tabela SET
        tblUsuario_nome = :bdpNome,
        tblUsuario_email = :bdpEmail,
        tblUsuario_telefone = :bdpTelefone,
        tblUsuario_senha = :bdpSenha,
        tblUsuario_salario = :bdpSalario,
        tblUsuario_cargo = :bdpCargo,
        tblUsuario_foto = :bdpFoto,
        tblUsuario_idGerente_tblUsuario = :bdpIdGerente,
        tblUsuario_observacoes = :bdpObservacoes,
        tblUsuario_sexo = :bdpSexo,
        tblUsuario_dias = :bdpDias,
        tblUsuario_raca = :bdpRaca,
        tblUsuario_usuarioAlteracao = :bdpUsuarioAlteracao,
        tblUsuario_versao = :bdpVersao WHERE tblUsuario_id = :id";

        $stm = DB::prepare($sql);
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        $stm->bindParam(':bdpNome', $this->nome);
        $stm->bindParam(':bdpEmail', $this->email);
        $stm->bindParam(':bdpTelefone', $this->telefone);
        $stm->bindParam(':bdpSenha', $this->senha);
        $stm->bindParam(':bdpSalario', $this->salario);
        $stm->bindParam(':bdpCargo', $this->cargo);
        $stm->bindParam(':bdpFoto', $this->foto);
        $stm->bindParam(':bdpIdGerente', $this->idGerente);
        $stm->bindParam(':bdpObservacoes', $this->obs);
        $stm->bindParam(':bdpSexo', $this->sexo);
        $stm->bindParam(':bdpDias', $this->dias);
        $stm->bindParam(':bdpRaca', $this->raca);
        $stm->bindParam(':bdpUsuarioAlteracao', $this->usuarioAlteracao);
        $stm->bindParam(':bdpVersao', $versaoTela);
        //return $stm->execute();
        if(!$stm->execute()){
          //erro na altera????o do usu??rio
          return false;
        }else{
          //inserir os departamentos
          //pegar o id do usuario
          $idLast=$id;
          //gerar array com os departamentos a serem inseridos
          $idsDep=explode(",",$this->idDepartamento);
          //remover em branco
          array_pop($idsDep);
          foreach ($idsDep as $key => $value) {
            $usuarioHasDep = new UsuariosHasDepartamento();
            $usuarioHasDep->setUsuario($idLast);
  			    $usuarioHasDep->setDepartamento($value);
            if (!$usuarioHasDep->insert()) {
              //erro na inser????o dos departamentos para o usu??rio
              return false;
            }
          }
        }
        return true;
      }else{
        return 2;
      }
    }


    public function delete($id) {
        $sql = "DELETE FROM $this->tabela WHERE tblUsuario_id = :id";
        //echo $sql;
        //echo "..........".$id;
        //die();
        $stm = DB::prepare($sql);
        $stm->bindParam(':id', $id, PDO::PARAM_INT);
        return $stm->execute();
    }

}
