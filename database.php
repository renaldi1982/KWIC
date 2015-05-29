<?php 
	class DatabaseInterface{
		/* REY-VPS MYSQL Server Information */
		private $servername="localhost";

		private $username="previcti_sad";

		private $password="JeffReySAD";

		private $dbname="previcti_main";

		private $conn = null;

		

		/* Single and Array implementation of KWIC entity Table model*/

		private $kwic;

		private $kwic_array = array();

					

		public function __construct(){

			$this->connect();					

		}

		

		/* Array of Kwics */

		public function set_kwic_array($kwic_array){			

			$this->kwic_array = $kwic_array;

		}

		

		public function get_kwic_array(){

			return $this->kwic_array;

		}

		public function add_kwic(Kwic $kwic){
			$this->kwic_array[] = $kwic;
		}

		/* Single KWIC object */

		public function set_kwic($kwic){

			$this->kwic = $kwic;						

		}

		

		public function get_kwic(){

			return $this->kwic;

		}

		

		/* Connection properties */

		public function connect(){

			$this->conn = new mysqli($this->servername,$this->username,$this->password,$this->dbname,3306);			

		}			

		

		public function get_connection(){

			if($this->conn !== null){

				return $this->conn;

			}

		}

		

		public function get_status(){

			return $this->conn->stat();

		}

		

		public function is_connected(){

			return ($this->conn != null) ?'Connected' : 'Connection failed';

		}						

		

		/* Interface for KWIC+ - Store data in the database */		

		public function save_kwic(){

			$cs = $this->kwic->get_cs();				

			$original = $this->kwic->get_original();			

			$url = $this->kwic->get_url();

			

			$query = "INSERT INTO kwic(cs_descriptor,original_descriptor,url) VALUES('$cs','$original','$url')";

			

			$result = $this->dbinsert($query);	

			

			return $result;				

		}		

		

		private function dbinsert($query){			

			$result = FALSE;		

			

			if($this->conn->query($query) === TRUE){

				$result = TRUE;

			}						

									

			return $result;						

		}								

		

		/* Interface for Microminer and display all the rows in database interface */

		public function get_all_rows(){									

			$query = "SELECT * FROM kwic";
			$result = array();
			$result["flag"] = $this->conn->query($query);		
			$result["kwic"] = $this->RStoKwic($result["flag"]);
			if($result["flag"] === FALSE){
				exit("Result Set retrieval failed");
			}															

			return $result;			

		}								

		

		/* Convert Result Set to Kwic Array */									

		public function RStoKwic($result){

			$kwics = array();	
			while($row = $result->fetch_row()){
				$kwic = new Kwic();
				$kwic->set_id($row[0]);
				$kwic->set_original($row[2]);
				$kwic->set_cs($row[1]);
				$kwic->set_url($row[3]);
				$kwics[] = $kwic;	
			}
			return $kwics;

		}

		

		private function dbread($query){

			$result = $this->conn->query($query);

			return $result;

		}

		

		public function cleardb(){

			$query = "TRUNCATE TABLE kwic";

			

			if($this->conn->query($query) === TRUE){

				if($this->conn != null){

					//$this->closedb();					

				}

			}

		}

					

		public function closedb(){

			$this->conn->close();

			$this->conn = null;

		}

				

		

	}

	

	/* Represents Entity of our Table in the Database */

	class Kwic{

		private $id;

		private $cs;

		private $original;		

		private $url;		

		

		function __construct(){}

		

		/* Setters */

		

		public function set_id($id){

			$this->id = $id;

		}

		public function set_cs($cs){

			$this->cs = $cs;	

		}

		public function set_original($original){

			$this->original = $original;

		}		

		public function set_url($url){

			$this->url = $url;

		}

		

		

		/* Getters */

		public function get_id(){

			return $this->id;

		}

		public function get_cs(){

			return $this->cs;

		}

		public function get_original(){

			return $this->original;

		}		

		public function get_url(){

			return $this->url;

		}				

		

	}



?>

