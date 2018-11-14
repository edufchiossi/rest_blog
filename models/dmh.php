<?php
// functions
	function updateArrayKey(&$array, $oldKey, $newKey){
		$value = $array[$oldKey];
		unset($array[$oldKey]);
		$array[$newKey] = $value;
	}
class DMH {
	public $conn;
	public $form;

	public function __construct($conn){
		$this->conn = $conn;
	}
	
	public function getData($method, $form=null){
		try {
			try {
				$this->form = $form;
				$table = $this->getAndDeleteField('table');
				if(!$table)
					return ['msg' => 'The table was not passed'];
				if(array_key_exists('id', $this->form))
					updateArrayKey($this->form, 'id', "id_$table");
				// print_r($this->form);
				$LIMIT = $this->getLimit($method);
				switch ($method) {
					case 'DELETE':
						$verb = 'deleting';
						if(!array_key_exists("id_$table", $this->form))
							return ['msg' => "The id for table $table wasn't passed"];
						$conditionsKeys = $this->getConditionsKeys();
						$sql = "DELETE FROM $table WHERE id_$table = :id_$table $LIMIT";
						$sth = $this->getSth($sql);
						$res = $sth->rowCount();
						return $this->sucessMsg($res, "sucessful delete on $table $withConditions");

					case 'GET':
						$verb = 'reading';
						$orderByField = $this->getAndDeleteField('orderByField');
						if(is_int($orderByField))
							$orderByField = 'ORDER BY $orderByField';
						if($this->form){
							$withConditions = $this->getConditions();
							$conditionsKeys = $this->getConditionsKeys();
							$sql = "SELECT * FROM $table WHERE $conditionsKeys $orderByField $LIMIT";
							$sth = $this->getSth($sql);
							$res = $sth->fetch(PDO::FETCH_ASSOC);
						} else {
							$withConditions = null;
							$sql = "SELECT * FROM $table $orderByField $LIMIT";
							$sth = $this->getSth($sql);
							$res = $sth->fetchAll(PDO::FETCH_ASSOC);
						}
						if(!$res)
							return [ 'msg' => "There is no $table $withConditions" ];
						return $res;

					case 'POST':
						$verb = 'inserting';
						$fields = $this->getFields();
						$fieldsWithColon = $this->getFieldsWithColon();
						$sql = "INSERT INTO $table($fields) VALUES($fieldsWithColon)";
						$sthExec = $this->getSth($sql, 201, true);
						return $this->sucessMsg($sthExec, "sucessful insert on $table");

					case 'PUT':
						$verb = 'updating';
						if(!array_key_exists("id_$table", $this->form))
							return ['msg' => "The id for table $table wasn't passed"];
						$changesKeys = $this->getChangesKeys();
						echo $sql = "UPDATE $table SET $changesKeys WHERE id_$table = :id_$table $LIMIT";
						$sth = $this->getSth($sql);
						$res = $sth->rowCount();
						$id = $this->form["id_$table"];
						return $this->sucessMsg($res, "sucessful update on $table with id = $id");

					default:
						return ['msg' => "'$method' method not covered"];
				}
			} catch(PDOException $err){
				return [ 'msg' => "PDO Exception: Error while $verb: ".$err->getMessage() ];
			}
		} catch(Exception $err){
			return [ 'msg' => "Exception: ".$err->getMessage() ];
		}
	}

	public function getLimit($method){
		if(isset($this->form['LIMIT']))
			return 'LIMIT '.$this->getAndDeleteField('LIMIT');
		elseif($method == 'GET')
			return null;
		else
			return "LIMIT 1";
	}

	public function sucessMsg($condition, $sucessMsg){
		if($condition)
			return [ 'msg' => $sucessMsg ];
		else
			return [ 'msg' => "Un$sucessMsg" ];
	}

	public function getSth($sql, $code=null, $returnExec=false){
		$sth = $this->conn->prepare($sql);
		if($this->form){
			foreach ($this->form as $key => &$value) {
				$sth->bindParam($key, $value);
			}
		}
		try {
			if($returnExec)
				$res = $sth->execute();
			else {
				$sth->execute();
				$res = $sth;
			}
			if($code)
				http_response_code(201);
			return $res;
		} catch (PDOException $err) {
			throw $err;
		}
	}

	public function getAndDeleteField($fieldName){
		if(!isset($this->form[$fieldName]))
			return null;
		$field = $this->form[$fieldName];
		unset($this->form[$fieldName]);
		return $field;
	}

	public function getConditionsKeys($array=null){
		if(!$array)
			$array=$this->form;
		$str = '';
		foreach ($array as $key => $value) {
			$str .= "$key = :$key AND ";
		}
		return rtrim($str, ' AND ');
	}

	public function getConditions($array=null){
		if(!$array)
			$array = $this->form;
		if(!isset($array))
			return null;
		$str = 'with ';
		foreach ($array as $key => $value) {
			$str .= "$key = $value AND ";
		}
		if($str == 'with ')
			return null;
		return rtrim($str, ' AND ');
	}

	public function getChangesKeys($array=null){
		if(!$array)
			$array=$this->form;
		$str = '';
		foreach ($array as $key => $value) {
			$str .= "$key = :$key, ";
		}
		return rtrim($str, ', ');
	}

	public function getUpdateForm(){
		$newForm = [];
		foreach ($this->form as $array) {
			foreach ($array as $key => $value) {
				$newForm[$key] = $value;
			}
		}
		return $newForm;
	}

	public function getFields(){
		if(!isset($this->form))
			return null;
		$str = '';
		foreach ($this->form as $key => $value) {
			$str .= "$key, ";
		}
		return rtrim($str, ', ');
	}

	public function getFieldsWithColon(){
		$str = '';
		foreach ($this->form as $key => $value) {
			$str .= ":$key, ";
		}
		return rtrim($str, ', ');
	}
}
/*
			case 'PUT':
				$changesKeys = $this->getChangesKeys($this->form['changes']);
				$conditionsKeys = $this->getConditionsKeys($this->form['conditions']);
				$sql = "UPDATE $table SET $changesKeys WHERE $conditionsKeys $LIMIT";
				$withConditions = $this->getConditions($this->form['conditions']);
				$this->form = $this->getUpdateForm();
				try {
					$sth = $this->getSth($sql);
				} catch(PDOException $e) {
					return [ 'msg' => 'Error while updating: '.$e->getMessage() ];
				}
				$res = $sth->rowCount();
				return $this->sucessMsg($res, "sucessful update on $table $withConditions");
*/