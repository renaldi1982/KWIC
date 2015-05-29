<?php 	
	require_once("./database.php");
		
	class MicroMiner{
		private $keywords;		
		private $stopwords;
		private static $patternURL = "(http://[a-zA-Z|0-9.]+.[edu|com|org|net])";				
		private $data = array("index"=>array(),"original"=>array(),"cs"=>array(),"url"=>array());		
		private $db;
		
		function __construct(){
			$this->set_db(new DatabaseInterface());						
		}
		
		private function exit_error($message){			
			exit("$message <br/> <a href='{$_SERVER['HTTP_REFERER']}'>Go Back</a>");
		}				

		public function lines_to_word($lines_of_word){
			$temp = array();	
			foreach ($lines_of_word as $value) {
				$line_exploded = explode(" ",$value);
				foreach ($line_exploded as $word) {
					$temp[] = $word;
				}
			}
			return $temp;
		}
		
		public function process($keywords,$stopwords){					  			 					
			
			/* Process Keywords */																
			$this->set_keywords($keywords);																				
			$this->set_stopwords($stopwords);	
			$this->remove_noisewords();				
			$this->set_keywords(array_filter($this->lines_to_word($this->keywords)));	
			
			/* Retrieve all Rows from the Database */
			$source = $this->db->get_all_rows();
			$kwics = $source["kwic"];			
			$kwics_cs = $this->get_cs($kwics);	
			if(count($kwics) < 1){
				return array("Database Record is empty");
			}
			
			/* Perform search to find matches between keywords and records in the Database */
			$result = array();			 
			$matches = array();									
			foreach ($kwics as $value) {		
				foreach ($this->keywords as $key) {
					/* If matches is empty we need to run the search at least one time and fill up the array 
					 * We also need to remove element from KWIC cs array after each found so that the cs array is
					 * getting smaller and finally no more records to search* */
					if(count($kwics_cs) < 1){
						break;
					}	
					$exists = $this->binary_search($kwics_cs, $key);
					
					if($exists !== FALSE){
						$temp_kwic = $exists;
						$kwics_cs = $this->reset_array($kwics_cs, $temp_kwic);	
						$cs = $this->get_cs_kwics_unique($kwics, $temp_kwic);	
						$matches[] = $cs->get_original() . " ~ " . $cs->get_url();																						
					}
				}
			}					

		
			/* If we found matches then array matches will not be 0 
			 * We can get the index to retrieve the element from kwics array
			 * */
			if(count($matches) < 1){
				return array("Match not found");
			}															

			/* Make sure that we do not have any duplicates value */
			$response = array_unique($matches);
			$tmp = array();
			foreach($response as $new){
				$tmp[] = $new;
			}

			$response = $tmp;

			//$this->db->cleardb();
			return $response;			 			 																		
		}										

				

		private function binary_search($array,$key){
			if(count($array) <= 0){
				return -1;
			}

			$lo = 0;
			$hi = count($array) - 1;								
			
			while($lo <= $hi){

				$mid = intval(($hi + $lo) / 2);								
				if(preg_match_all("/\b" . strtolower($key) . "\b/i", strtolower($array[$mid])) !== 0){					
					return $array[$mid];
				} 
				if(strcmp(strtolower($key), strtolower($array[$mid])) < 0){					
					$hi = $mid - 1;					
				}else if(strcmp(strtolower($key), strtolower($array[$mid])) > 0){					
					$lo = $mid + 1;					
				}				
			}	
			return FALSE;	
		}				

		private function remove_noisewords(){							
			foreach ($this->stopwords as &$word) {
				$word = ltrim(rtrim($word));
			  $word = '/\b' . preg_quote($word, '/') . '\b/';
			}						

			foreach ($this->keywords as &$value) {
				$value = ltrim(rtrim($value));
				$value = preg_replace($this->stopwords, '', $value);
			}																															

		}
		
		private function reset_array($original,$remove_cs){
			foreach($original as $key => $value) {
				if($remove_cs === $value)
					unset($original[$key]);
			}
			$new_kwic_cs = array_values($original);

			return $new_kwic_cs;											 		
		}

		

		/* Find occurence of cs in array of kwics object and retrieve the corresponding object */
		private function get_cs_kwics_unique($kwics,$find){			
			$result;
			foreach ($kwics as $key => $value) {
				if($find === $value->get_cs()){
					return $value;
				}

			}		
			return FALSE;						
		}
		private function get_cs($kwics){
			$cs = array();	
			foreach ($kwics as $key => $value) {
				$cs["$key"] = $value->get_cs();
			}			
			return $cs;			
		}
		public function set_db($db){
			$this->db = $db;
		}

		public function get_db(){
			return $this->db;

		}

		public function set_stopwords($stopwords){
			$this->stopwords = $stopwords;		
		}
		
		public function get_stopwords(){
			return $this->stopwords;
		}

		

		public function set_keywords($keywords){

			$this->keywords = $keywords;

		}		

		public function get_keywords(){							
			return $this->keywords;			
		}												

						

	}		

?>





	