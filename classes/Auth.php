<?php
namespace classes;
use \classes\Config as Config;
Class Auth 
{
	private $_role = 1;
	private $_errors = [];
	private $_ready_to_Register = 0;
	private $_db;

	function __construct()
	{
		$this->_db = \classes\Db::getInstance()->rawPDO();
	}
	function logout()
	{
		unset($_SESSION["u_id"]);
	}
	function getErrors()
	{
		return $this->_errors;
	}
	function registerUser($data)
	{
		$result = false;
		if($this->ready_to_register)
		{
			$userId = time();
			$role = $this->_role;
			$first_name = html_entity_decode($data["first_name"]);
			$last_name = html_entity_decode($data["last_name"]);
			$hash = $this->_hashPassword($data["pass"]);
			$email = $this->_validateEmail($data["email"]);

			$regiSql = "INSERT INTO users VALUES ($userId, $role, '$first_name', '$last_name', '$email', '$hash')";
			$regiQuery = $this->_db->prepare($regiSql);
			$result = $regiQuery->execute();
			if($result)
			{
				$_SESSION["u_id"] = $userId;
			}
		}

		return $result;
	}
	function validateRegistration($data)
	{
		if(!$data["first_name"])
		{
			$this->_errors[] = "No first Name provided";
		}
		if(!$data["last_name"])
		{
			$this->_errors[] = "No last name provided";
		}
		if(!$data["email"])
		{
			$this->_errors[] = "No email provided";
		}
		if (!$this->_validateEmail($data["email"]))
		{
			$this->_errors[] = "email is not valid";
		}	
		if($this->_emailExists($data["email"]))
		{
			$this->_errors[] = "email already exists in our records";
		}
		if (!$data["password"])
		{
			$this->_errors[] = "No password given";
		}
		if (strlen($data["password"]) <= 6)
		{
			$this->_errors[] = "Password too short, it's only ".strlen($data["password"])." characters long";
		}
		if ($data["password"] !== $data["re-password"])
		{
			$this->_errors[] = "Password entries do not match";
		}


		if(empty($this->_errors))
		{
			$this->ready_to_register = 1;
		}
		else
		{
			$this->ready_to_register = 0;
		}
		return $this->ready_to_register;
	}
	function authById($id=false)
	{
		$result = false;
		if($id)
		{
			$sql = "select first_name, last_name, role, email from users where id = $id;";
			$query = $this->_db->prepare($sql);
			$result = $query->execute();
		}

		if($result)
		{
			return $query->fetch(\PDO::FETCH_OBJ);	
		}
		return $result;
	}
	function login($email, $pass)
	{
		$sql = "select * from users where email = '$email';";
		$query = $this->_db->prepare($sql);
		$result = $query->execute();
		$user = $query->fetch(\PDO::FETCH_OBJ);
		if(!$result)
		{
			$this->_errors[] = "Incorrect Email used";
			return false;
		}
		elseif($this->_verifyPassword($pass, $user->hash))
		{
			$_SESSION["u_id"] = $user->id;
			return true;
		}
		else
		{
			$this->_errors[] = "Wrong password";
			return false;
		}
	}

	private function _emailExists($email)
	{
		$sql =  "select email from users where email = '$email';";
		$query = $this->_db->prepare($sql);
		$result = $query->execute();
		$user_email = $query->fetch(\PDO::FETCH_OBJ);
		return $user_email->email == $email;

	}
	private function _generateTableSql()
	{
		$dbname = Config::get("db_data/dbname");
		$sql = "CREATE TABLE `$dbname`.`Users` ( `id` INT(11) NOT NULL , `role` TINYINT(1) NOT NULL DEFAULT '0' , `first_name` VARCHAR(255) NOT NULL , `last_name` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `hash` VARCHAR(255) NOT NULL ) ENGINE = InnoDB;";

		return $sql;
	}
	private function _hashPassword($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}
	private function _verifyPassword($pass, $hash)
	{
		return password_verify($pass, $hash);
	}
	private function _validateEmail($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
}
