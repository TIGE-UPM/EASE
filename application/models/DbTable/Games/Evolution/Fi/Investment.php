<?php
	class Model_DbTable_Games_Evolution_Fi_Investment extends Model_DbTable_Games_Evolution_Base{
		protected $_name = 'games_evolution_fi_investment';
		
		function getInvestment($game_id, $round_number, $investment_number){
			$result=$this->fetchRow('game_id = '.$game_id.' AND round_number = '.$round_number.' AND investment_number = '.$investment_number);
			return $result['interest'];
		}

		function setInvestment($game_id, $round_number, $investment_number, $interest){
			$this->add(array('game_id'=>$game_id, 'round_number'=>$round_number, 'investment_number'=>$investment_number, 'interest'=>$interest));
		}

		function updateInvestmentEvolution($game_id, $data){
			$this->delete('game_id = '.$game_id);
			$investmentParamCounter=1;
			$game= new Model_DbTable_Games();
			$n_rounds=$game->getNumberOfRounds($game_id);
			while (isset($data['investment_param_'.$investmentParamCounter])){
				$investment_param=$data['investment_param_'.$investmentParamCounter];
				$limit=(double) $investment_param['limit'];
				$investment_param_number=$investmentParamCounter;
				for($round_number=1;$round_number<=$n_rounds;$round_number++){
					$average_performace=(double) $investment_param['investment_param_average_performance'];
					$random=rand(0, 100)/100*2-1;
					$interest =($random*$limit+$average_performace)/100;
					$this->insert(array('game_id'=>$game_id, 'round_number'=>$round_number,'investment_number'=>$investment_param_number, 'interest'=>$interest));
				}
				
				$investmentParamCounter++;
			}
		}		
	}
?>