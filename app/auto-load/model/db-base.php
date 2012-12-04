<?php

/**
 * Model__DbBase
 */
class Model__DbBase {

	private $_con			 = null;
	private $_connectionName = null;

	public function __construct($con = null) {
		if ($con instanceof AttoDbo__IConnection) {
			$this->_con = $con;
		}
		else {
			$this->_connectionName = $con;
		}
	}

	/**
	 * con
	 * 
	 * @method con
	 * @return \AttoDbo__IConnection 
	 */
	protected function _con() {
		if ($this->_con === null) {
			$this->_con = AttoDbo__Manager::getInstanse()->getConnection($this->_connectionName);
		}
		return $this->_con;
	}

	public function __destruct() {
		if ($this->_con !== null) {
			$this->_con = null;
			unset($this->_con);
		}
	}

	protected function _makeInsertSql($table, array $var_columns, array $fixed_columns = null) {
		$columns = array_filter($var_columns);
		$params	 = array_map(array(&$this, '_colon2head'), $columns);
		if ($fixed_columns) {
			foreach ($fixed_columns as $column => $param) {
				$columns[]	 = $column;
				$params[]	 = $param;
			}
		}
		return 'INSERT INTO ' . $table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $params) . ')';
	}

	protected function _makeUpdateSql($table, array $var_columns, array $fixed_columns = null, $id_name = 'id') {
		$columns = array_filter($var_columns);
		$params	 = array_map(array(&$this, '_colon2head'), $columns);
		if ($fixed_columns) {
			foreach ($fixed_columns as $column => $param) {
				$columns[]	 = $column;
				$params[]	 = $param;
			}
		}
		return 'UPDATE ' . $table . ' SET ' . $this->_joinForKeyValue(array_combine($columns, $params)) . ' WHERE ' . $id_name . '=:' . $id_name;
	}

	protected function _colon2head($value) {
		return ':' . $value;
	}

	protected function _removeHeadColon($value) {
		return ltrim($value, ':');
	}

	protected function _joinForKeyValue(array $arr, $glue1 = '=', $glue2 = ',') {
		$ret = array();
		foreach ($arr as $key => $value) {
			$ret[] = $key . $glue1 . $value;
		}
		return implode($glue2, $ret);
	}

}

class DataAccessException extends Exception {
	
}