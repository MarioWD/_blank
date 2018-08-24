<?php
namespace classes;
use \classes\Config as Config;
use \PDO as PDO;
class Db
{
    private static $_inst = null;

    private
        $_pdo,
        $_query,
        $_error = false,
        $_results,
        $_count = 0;

    private function __construct()
    {
        $db_data = Config::get('db_data');
        try
        {
            $this->_pdo = new \PDO("mysql:host={$db_data['dbhost']};dbname={$db_data['dbname']}", $db_data['user'], $db_data['pass']);
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            dbg($e->getMessage());
        }
    }
    public function rawPDO(){
      return $this->_pdo;
    }
    public static function getInstance()
    {
        if (!isset(self::$_inst))
        {
            self::$_inst = new Db();
        }
        else
        {
           self::$_inst->_results = false;
           self::$_inst->_error = false;
           self::$_inst->_count = 0;
        }

        return self::$_inst;
    }

    public function query($sql, $params = array())
    {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql))
        {
            if (count($params))
            {
                $x = 1;
                foreach ($params as $param)
                {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
        }
    }

    public function act($act, $tbl, $where = array())
    {
        $sql = "$act FROM $tbl WHERE 1 ";
        $operators = array('=', '>', '<', '>=', '<=');

        if(!empty($where))
        {
            foreach($where as $clause)
            {
                $clause = explode("/", $clause);
                if (count($clause) == 3)
                {
                    $fld = $clause[0];
                    $opt = $clause[1];
                    $val[] = $clause[2];

                    if (in_array($opt, $operators))
                    {
                        $sql .= "AND $fld $opt ? ";
                    }
                }
            }
        }
        $this->query($sql, $val);
        return $this->_process();
    }

    public function get($tbl, $where = array())
    {
        if ($this->act("SELECT *", $tbl, $where))
        {
            $this->_results = $this->_query->fetch(PDO::FETCH_OBJ);
        }
        return $this;
    }

    public function getAll($tbl, $where)
    {
        if ($this->act("SELECT *", $tbl, $where))
        {
            $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
        }
        return $this;
    }

    public function delete($tbl, $where)
    {
        $this->_results = $this->act("DELETE", $tbl, $where);
        return $this;
    }

    public function insert($tbl, $fields = array())
    {
        if(count($fields))
        {
            $keys = "(`".implode("`,`", array_keys($fields))."`)";
            $values = "";
            $x = 1;

            foreach($fields as $field)
            {
                $values .= "?";
                if($x < count($fields))
                {
                    $values .= ",";
                }
                $x++;
            }
            $sql = "INSERT INTO `$tbl` $keys VALUES ($values)";
            $this->query($sql, $fields);
            $this->_results = $this->_process();
        }
        return $this;
    }

    public function update($tbl, $id,  $fields = array())
    {
        $set = "";
        $x = 1;
        foreach($fields as $name => $val)
        {
            $set .= "`$name` = ?";
            if($x < count($fields))
            {
                $set .= ",";
            }
            $x++;
        }
        $sql = "UPDATE `$tbl` SET $set WHERE `id` = $id";
        $this->query($sql, $fields);
        $this->_results = $this->_process();

        return $this;
    }

    public function count()
    {
        return $this->_count;
    }

    public function error()
    {
        return $this->_error;
    }

    public function results()
    {
        return $this->_results;
    }

    private function _process()
    {
        $res = $this->_query->execute();
        $this->_count = $this->_query->rowCount();
        $this->_error = $this->_query->errorInfo();

        return $res;
    }
}

?>
