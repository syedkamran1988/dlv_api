<?php

class Database {

    private $host = DB_SERVER;
    private $db_user = DB_USER;
    private $db_password = DB_PASS;
    private $db_name = DB_NAME;
    private $conn;
    private $stmt;

    function __construct() {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->db_user, $this->db_password);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    function query($query) {
        try {
            $this->stmt = $this->conn->prepare($query);
            $result = $this->stmt->execute();
            if (!$result) {
                $this->error($this->conn->errorInfo() . ' (' . $query . ')');
                return false;
            } else {
                return $result;
            }
        } catch (PDOException $e) {
            $this->error($e->getMessage());
        }
    }

    function fetch_array() {
        return $this->stmt->fetch();
    }

    function fetch() {
        $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->stmt->fetch();
    }

    function fetch_all() {
        $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->stmt->fetchAll();
    }

    function num_rows($result) {
        return $this->stmt->rowCount($result);
    }

    function insert_id() {
        return intval($this->conn->lastInsertId());
    }

    function insert($table_name, $arr) {
        $cols = implode(",", array_keys($arr));
        $query_text = "";
        $data = array();
        foreach ($arr as $key => $values) {
            if ($query_text == "") {
                $query_text = "?";
                $data[] = $values;
            } else {
                $query_text.=", ?";
                $data[] = $values;
            }
        }
        $query = "insert into {$table_name} ($cols) values($query_text)";
        $this->stmt = $this->conn->prepare($query);
        $result = $this->stmt->execute($data);
        if ($result) {
            return true;
        } else {
            return false;
        }      
    }

    function update($table_name, $arr, $field, $val) {
        $q = '';
        $data = array();
        foreach ($arr as $key => $value) {
            if ($q == '') {
                $q.="$key=?";
                $data[] = $value;
            } else {
                $q.=",$key=?";
                $data[] = $value;
            }
        }
        $query = "UPDATE {$table_name} SET $q WHERE $field = '" . $val . "' ";
        $this->stmt = $this->conn->prepare($query);
        $result = $this->stmt->execute($data);
        return $result;
    }

    function delete($table_name, $column, $value) {
        $query = "delete from {$table_name} where $column = '" . $value . "' ";
        $result = $this->query($query);
        return $result;
    }
    
    function query_error() {
        echo "<pre>";
        print_r($this->stmt->errorInfo());
        echo "</pre>";
    }

    function error($error) {
        $aray = array('error' => $error);
        echo json_encode($aray);
        exit;
    }

    function __destruct() {
        $this->conn = null;
    }
}
?>