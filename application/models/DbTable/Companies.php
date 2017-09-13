<?php
	class Model_DbTable_Companies extends Zend_Db_Table{
		protected $_name = 'companies';

		function getUsersInCompany($company_id){
			$users = new Model_DbTable_Users();
			$results=$users->fetchAll('company_id = '.$company_id);
			return $results;			
		}
		function getCompany($company_id){
			return $this->fetchRow('id = '.$company_id)->toArray();
		}
		function addCompany($company_data){
			$this->insert($company_data);
		}
		function getAllCompanies(){
			$results=$this->fetchAll();
			return $results;
		}
		function exists($options=null){
			return (! null==$this->fetchRow($options));
		}
	}
?>