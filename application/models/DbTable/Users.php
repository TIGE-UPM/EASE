<?php
	class Model_DbTable_Users extends Zend_Db_Table{
		protected $_name = 'users';
		function getAllUSers(){
			$results=$this->fetchAll();
			return $results;
		}
		function getUsersInCompany($company_id){
			return $this->fetchAll('company_id = '.$company_id);
		}
		function getUser($user_id){
			return $this->fetchRow('id = '.$user_id)->toArray();
		}
		function getUserByEmail($email){
			return $this->fetchRow('email = '.'"'.$email.'"');
		}
		function addUser($user_data){
			$this->insert($user_data);
		}
		function exists($conditions){
			return (null != self::fetchRow($conditions));
		}
		function get($conditions=null){
			return self::fetchRow($conditions);
		}		
		function deleteUser($user_id){
			$this->delete('id = '.$user_id);
		}
		function getUserRole($user_email){
			$result=$this->fetchRow('email = '.'"'.$user_email.'"');
			return $result['role'];
		}
	}
?>