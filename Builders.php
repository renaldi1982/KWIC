<?php

	abstract class Builder{
		abstract public function process_module();
	}

	class InputBuilder extends Builder{
    
		// @arg1 = data
		public function process_module(){
			$data = func_get_arg(0);
			$lines = new LineDetail();
			//Split $data into an array by the line breaks
			$i = 1;
			foreach(explode(PHP_EOL,$data) as $line){
				if(mb_strlen($line) < 1){
					continue;
				}
				$tmp = explode('-',$line);
				$lines->set_url($i,ltrim(rtrim($tmp[1])));
				foreach(explode(' ',ltrim(rtrim($tmp[0]))) as $word){
					$lines->set_word($i,$word);
				}
				$lines->set_url($i,ltrim(rtrim($tmp[1])));
				$i++;
			}
		}
   }

   class CircularShiftBuilder extends Builder{
   	private $noise_words = array(
    	"a", 
			"an", 
			"the", 
			"and", 
			"or", 
			"of", 
			"to", 
			"be", 
			"is", 
			"in", 
			"out", 
			"by", 
			"as", 
			"at", 
			"off",
			"am", 
    );
		//This will be a two dimensional array with line indicies as the 1st dimension
		// and an array of word indices as the second dimension
		private $index_array = array();
		public function setup(){
		$lines = new LineDetail();
			for($i=0;$i<$lines->line_count();$i++){
				$tmp = array();
				for($j=0;$j<$lines->word_count($i);$j++){
					$tmp[] = $j;
				}
				$this->index_array[$i][] = $tmp;
			
			}
		}
    public function shift(){
			$lines = new LineDetail();
      for($i=0;$i<count($this->index_array);$i++){
				$line = $this->index_array[$i];
				$words = $line[0];
				$this->index_array[$i] = array();
				for($j=0;$j<count($words);$j++){
					//Split the words into two arrays where the first would
          $split_1 = array_slice($words, $j);
          $split_2 = array_slice($words, 0, $j);
					$first_word = strtolower($lines->get_word($i,$split_1[0]));
					if(in_array($first_word,$this->noise_words)){
						continue;
					}	
         	$new_line = array_merge($split_1, $split_2);
					$this->index_array[$i][] = $new_line;
        }
			}
   	}
				
		public function process_module(){
			$this->setup();
			$this->shift();
		}
				
		public function get_cs_line($shift,$index){
			return $this->index_array[$shift][$index];
		}
				
		public function get_shift_count(){
			return count($this->index_array);
		}
				
		public function cs_line_count($shift_index){
			return count($this->index_array[$shift_index]);
		}
	}

  class AlphabetizeBuilder extends Builder{
		private $lines = array();
		private $sort_algorithm;
		// @arg[0] : Circular Shift object $cs
		public function process_module(){
			$cs = func_get_arg(0);
			$this->sort_lines($cs);	
		}
		
		public function sort_lines(CircularShiftBuilder $cs){
			$lines = new LineDetail();
			$cs = func_get_arg(0);
			$all_lines = array();
			for($i=0;$i<$cs->get_shift_count();$i++){
				for($j=0;$j<$cs->cs_line_count($i);$j++){
					$cs_line = $cs->get_cs_line($i,$j);
					$line = array();
					for($k=0;$k<count($cs_line);$k++){
						$line[] = $lines->get_word($i,$cs_line[$k]);
					}
					$tmp = '';
					for($k=0;$k<$lines->word_count($i);$k++){
						$tmp .= $lines->get_word($i,$k) . ' ';
					}
					$tmp = rtrim($tmp);
					$line[] = '- ' . $tmp . ' - ' . $lines->get_url($i);
					$all_lines[] = implode(' ',$line);
				}
			}				
			$this->lines = $this->sort_algorithm->sort_array($all_lines);
		}
			
		public function set_sort_algorithm(SortAlgorithm $sort){
			$this->sort_algorithm = $sort;
		}
			
		public function get_lines(){
			return $this->lines;
		}
	}