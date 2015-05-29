<?php
	abstract class SortingAlgorithm{
		public abstract function sort_array($array);
	}
	
	class QuickSort extends SortingAlgorithm{
			
			public function sort_array($array){
				shuffle($array);
				
				//Quick sort with custom comparator
				uasort($array,array($this,'compare'));
				return $array;
			}
		
			//Compare function to control the order of precedence for characters
			private function compare($a,$b){
				//Set the character precedence. In this instance we want lower case letters to have less value
  			$precedence = ' -0123456789aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ'; 
  			$char_order; 
    		if ($a == $b){ 
        	return 0; 
    		} 

     		$order = 1; 
     		$len = mb_strlen($precedence); 
				//This loop will build the character orders
				for ($order=0; $order<$len; ++$order){ 
					$char_order[mb_substr($precedence, $order, 1)] = $order; 
				} 

				//Get the lengths of $a and $b
    		$len_a = mb_strlen($a); 
    		$len_b = mb_strlen($b); 
				
				//Only search as far as needed
    		$max=min($len_a, $len_b); 
    		for($i=0; $i<$max; ++$i){ 
      		$tmp_a= mb_substr($a, $i, 1); 
      		$tmp_b= mb_substr($b, $i, 1); 
   
      		if ($tmp_a == $tmp_b){
						 continue;
					}
   
	    		$order_a = (isset($char_order[$tmp_a])) ? $char_order[$tmp_a] : 9999; 
      		$order_b = (isset($char_order[$tmp_b])) ? $char_order[$tmp_b] : 9999;  
      
      		return ($order_a < $order_b) ? -1 : 1; 
    		} 
    		return ($len_a < $len_b) ? -1 : 1; 
			}
	}
	
	class InsertionSort extends SortingAlgorithm{
		public function sort_array($array){
			$length=count($array);
        for ($i=1;$i<$length;$i++) {
            $element=$array[$i];
            $j=$i;
            while($j>0 && $this->compare($array[$j-1],$element) != -1) {
                //move value to right and key to previous smaller index
                $array[$j]=$array[$j-1];
                $j=$j-1;
                }
            //put the element at index $j
            $array[$j]=$element;
            }
        return $array;
		}
		
		//Compare function to control the order of precedence for characters
		private function compare($a,$b){
			//Set the character precedence. In this instance we want lower case letters to have less value
  		$precedence = ' -0123456789aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ'; 
  		$char_order; 
    	if ($a == $b){ 
       	return 0; 
    	} 
   		$order = 1; 
   		$len = mb_strlen($precedence); 
			//This loop will build the character orders
			for ($order=0; $order<$len; ++$order){ 
				$char_order[mb_substr($precedence, $order, 1)] = $order; 
			} 
  
			//Get the lengths of $a and $b
   		$len_a = mb_strlen($a); 
   		$len_b = mb_strlen($b); 
				
			//Only search as far as needed
   		$max=min($len_a, $len_b); 
   		for($i=0; $i<$max; ++$i){ 
     		$tmp_a= mb_substr($a, $i, 1); 
     		$tmp_b= mb_substr($b, $i, 1); 
  
     		if ($tmp_a == $tmp_b){
				 continue;
				}
   
	   		$order_a = (isset($char_order[$tmp_a])) ? $char_order[$tmp_a] : 9999; 
     		$order_b = (isset($char_order[$tmp_b])) ? $char_order[$tmp_b] : 9999;  
      
     		return ($order_a < $order_b) ? -1 : 1; 
   		} 
   		return ($len_a < $len_b) ? -1 : 1; 
		}
	}
	
	class MergeSort extends SortingAlgorithm{
		public function sort_array($array){
			if(count($array)>1) {
				
				//Find the middle of the array and round down
				$array_mid = round(count($array)/2, 0, PHP_ROUND_HALF_DOWN);
				
				//Split arrays and run sort on them
				$array1 = $this->sort_array(array_slice($array, 0, $array_mid));
				$array2 = $this->sort_array(array_slice($array, $array_mid, count($array)));
				
				$counter1 = $counter2 = 0;
				for($i=0; $i<count($array); $i++) {
					if($counter1 == count($array1)) {
						$array[$i] = $array2[$counter2];
						++$counter2;
					} 
					elseif(($counter2 == count($array2)) || $this->compare($array1[$counter1],$array2[$counter2]) == -1) { 
						$array[$i] = $array1[$counter1];
						++$counter1;
					}
					else{
						$array[$i] = $array2[$counter2];
						++$counter2;
					}
				}
			}
			
			return $array;
		}
		
		//Compare function to control the order of precedence for characters
		private function compare($a,$b){
			//Set the character precedence. In this instance we want lower case letters to have less value
  		$precedence = ' -0123456789aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ'; 
  		$char_order; 
    	if ($a == $b){ 
       	return 0; 
    	} 
   		$order = 1; 
   		$len = mb_strlen($precedence); 
			//This loop will build the character orders
			for ($order=0; $order<$len; ++$order){ 
				$char_order[mb_substr($precedence, $order, 1)] = $order; 
			} 
  
			//Get the lengths of $a and $b
   		$len_a = mb_strlen($a); 
   		$len_b = mb_strlen($b); 
				
			//Only search as far as needed
   		$max=min($len_a, $len_b); 
   		for($i=0; $i<$max; ++$i){ 
     		$tmp_a= mb_substr($a, $i, 1); 
     		$tmp_b= mb_substr($b, $i, 1); 
  
     		if ($tmp_a == $tmp_b){
				 continue;
				}
   
	   		$order_a = (isset($char_order[$tmp_a])) ? $char_order[$tmp_a] : 9999; 
     		$order_b = (isset($char_order[$tmp_b])) ? $char_order[$tmp_b] : 9999;  
      
     		return ($order_a < $order_b) ? -1 : 1; 
   		} 
   		return ($len_a < $len_b) ? -1 : 1; 
		}
	}