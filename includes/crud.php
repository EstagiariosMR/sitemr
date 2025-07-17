<?php
require 'db.php';

function create($tabela, $dados){
    global $pdo;

    if(empty($dados)){
        return false;
    }

    $colunas = implode(", ", array_keys($dados));
    //$valores = implode(", ", array_map(fn($key) => ":$key", array_keys($dados)));
    $valores = implode(", ", array_map(function($key) {
        return ":$key";
    }, array_keys($dados)));

    $sql = "INSERT INTO $tabela ($colunas) VALUES ($valores)";
    $stmt = $pdo->prepare($sql);

    try{
        foreach($dados as $key => $value){
            $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
        }

        if($stmt->execute()){
            return $pdo->lastInsertId();
        }
        else{
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Erro ao executar a query: " . $errorInfo[2]);
        }
    }
    catch(PDOException $e){
        if(strpos($e->getMessage(), 'Limite de 4 imagens no carrossel atingido') !== false){
            return ['warning' => 'Limite de 4 imagens no carrossel atingido'];
        }
        
        echo "Erro ao inserir no banco de dados: " . $e->getMessage();
        return false;
    }
}

function read($tabela, $colunas='*', $where=false, $valores=[], $umRegistro=false, $order=null, $limitOffset=null){
    global $pdo;
    
    $query = "SELECT $colunas FROM $tabela";

    if($where){
        if(is_array($where)){
            $where = implode(' AND ', $where);
        }

        $query .= " WHERE $where";  
    } 

    if(!empty($order)){
        $query .= " ORDER BY $order";
    }

    if($limitOffset){
        $query .= " " . $limitOffset;
    }
    
    try{
        $stmt = $pdo->prepare($query);

        if($where && !empty($valores)){
            foreach($valores as $param => $valor){
                $stmt->bindValue(":$param", $valor);
            }
        }

        $stmt->execute();

        return $umRegistro ? $stmt->fetch() : $stmt->fetchAll();
    }
    catch(PDOException $e){
        echo "Erro ao realizar a consulta: " . $e->getMessage();
        return false;
    }
}

function update($tabela, $dados, $where = false, $valoresWhere = []) {
    global $pdo;

    if (empty($dados) || empty($where)) {
        return false; // Proteção contra update sem WHERE
    }

    $set = implode(", ", array_map(function($key) {
        return "$key = :set_$key";
    }, array_keys($dados)));

    if (is_array($where)) {
        $where = implode(' AND ', $where);
    }

    $sql = "UPDATE $tabela SET $set WHERE $where";

    try {
        $stmt = $pdo->prepare($sql);

        // Bind dos dados a atualizar
        foreach ($dados as $key => $value) {
            $stmt->bindValue(":set_$key", $value);
        }

        // Bind dos valores da cláusula WHERE
        foreach ($valoresWhere as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    } catch (PDOException $e) {
        echo "Erro ao atualizar registro: " . $e->getMessage();
        return false;
    }
}

function delete($tabela, $where = false, $valoresWhere = []) {
    global $pdo;

    if (empty($where)) {
        return false; // Nunca deletar sem WHERE
    }

    if (is_array($where)) {
        $where = implode(' AND ', $where);
    }

    $sql = "DELETE FROM $tabela WHERE $where";

    try {
        $stmt = $pdo->prepare($sql);

        foreach ($valoresWhere as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    } catch (PDOException $e) {
        echo "Erro ao deletar registro: " . $e->getMessage();
        return false;
    }
}

function countAll($tabela, $where=false, $valores=[]){
    global $pdo;

    $query = "SELECT COUNT(*) as total FROM $tabela";

    if($where){
        if(is_array($where)){
            $where = implode(' AND ', $where);
        }

        $query .= " WHERE $where";
    }

    try{
        $stmt = $pdo->prepare($query);

        if($where && !empty($valores)){
            foreach($valores as $param => $valor){
                $stmt->bindValue(":$param", $valor);
            }
        }

        $stmt->execute();

        $resultado = $stmt->fetch();

        return $resultado['total'] ?? 0;
    }
    catch(PDOException $e){
        echo "Erro ao contar registros: " . $e->getMessage();
        return 0;
    }
}