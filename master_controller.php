<?php
	session_start();
	require_once("SortingAlgorithm.php");
	require_once("Builders.php");
    class MasterController{
        private $builders = array();
		private $output;
		private $search;
        public function __construct(){
            $this->addBuilder(new InputBuilder(),'input');
            $this->addBuilder(new CircularShiftBuilder(),'cs');
            $this->addBuilder(new AlphabetizeBuilder(),'alpha');
						$this->output = new Output();
						$this->search = new Search();
						$this->builders['alpha']->set_sort_algorithm(new MergeSort());
        }

        public function process($data){
						$_SESSION['lines'] = '';
            $this->builders['input']->process_module($data);
						$this->builders['cs']->process_module();
						$this->builders['alpha']->process_module($this->builders['cs']);
						return $this->output->display($this->builders['alpha']);
        }
				
		public function search_microminer($data){
			return $this->search->process($data);
		}
			
		private function addBuilder(Builder $builder,$assoc_index){
			$this->builders[$assoc_index] = $builder;
		}
				
		public function json_post(){
		    return str_replace(PHP_EOL,'',$this->output->get_json_post());
		}
    }

    

    class Output{
	    private $json_post = '';
			
		public function display(AlphabetizeBuilder $lines){
			$out_lines = '';
			$json_pass = array();				
			foreach($lines->get_lines() as $line){
				$json_pass[] = str_replace(' - ','-',$line);
				$tmp = explode(' - ',$line);
				$out_lines .= $tmp[0] . ' - ' . $tmp[2] . PHP_EOL;
			}
			$this->json_post = $json_pass;
			
			return $out_lines;
		}
				
		public function get_json_post(){
			
			if($this->json_post == null){
				return;
		    }
			
			//API SETUP
			$url = 'http://63.223.84.104/sdd-hw3/database_interface.php';
			$post= "kwic=".json_encode($this->json_post);
			
			$ch = curl_init($url);
			curl_setopt($ch,CURLOPT_URL,$url);
    		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    		curl_setopt($ch,CURLOPT_HEADER, false); 
    		curl_setopt($ch, CURLOPT_POST, count($post));
        	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);    			
			$content  = curl_exec($ch);
			
			curl_close($ch);
					
			return $content;
		}
    }
		
	class Search{
		public function process($data){
	
		    //API SETUP
			$url = 'http://63.223.84.104/sdd-hw3/microminer_interface.php';
			$post= "keywords=".json_encode(explode(' ',$data));	
			$ch = curl_init($url);
			curl_setopt($ch,CURLOPT_URL,$url);
    		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    		curl_setopt($ch,CURLOPT_HEADER, false); 
    		curl_setopt($ch,CURLOPT_POST, count($post));
        	curl_setopt($ch,CURLOPT_POSTFIELDS, $post);    			
			$result  = curl_exec($ch);
			
			curl_close($ch);
			$result = str_replace(PHP_EOL,'',$result);
			$result = str_replace("\t",'',$result);
			return $result;
		}
	}

    class LineDetail{
	
		//This will automatically push the word to the next index
		public function set_word($index,$word){
			$index = (int)$index;
			$_SESSION['lines'][$index-1][] = ltrim(rtrim($word));
		}
				
		public function get_word($line_index,$word_index){
			$line = $_SESSION['lines'][$line_index];
			return $line[$word_index];
		}
				
		public function set_url($line_index,$url){
			$_SESSION['lines'][$line_index-1]['url'] = $url;
		}
				
		public function get_url($line_index){
			return $_SESSION['lines'][$line_index]['url'];
		}
				
		public function get_line($line_index){
			return $_SESSION['lines'][$line_index];
		}
				
		public function word_count($index){
			return count($_SESSION['lines'][$index])-1;
		}
				
		public function line_count(){
			return count($_SESSION['lines']);
		}
    }
		
	//From the search Interface
	if(isset($_POST['btnSubmit'])){
		$mc = new MasterController();
		$result = json_decode($mc->search_microminer($_POST['keywords']),true);
		echo json_encode($result);
	}
	