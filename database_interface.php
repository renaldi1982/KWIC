<?php 
	require_once("database.php");			
	$jsontoarray = null;	
	$db = new DatabaseInterface();
	$db->connect();
	if(isset($_POST['kwic'])){		
		$jsondata = json_decode($_POST['kwic'],true);
		$kwic_array = array();	
		$pattern = "/(.+)\s*-\s*(.+)\s*-\s*(.+)$/is";
		if(count($jsondata) > 0){
			$total_inserts = 0;
			foreach ($jsondata as $value) {
				ltrim(rtrim(preg_match($pattern, $value,$matches)));	
				$kwic = new Kwic();	
				$kwic->set_cs($matches[1]);
				$kwic->set_original($matches[2]);
				$kwic->set_url($matches[3]);
				$kwic_array[] = $kwic;
				$total_inserts++;								
			}
			if($kwic_array !== null && count($kwic_array) > 0){
				$db_inserts = 0;
				$db->cleardb();
				foreach ($kwic_array as $kwic) {
					$db->set_kwic($kwic);				
					if(($result = $db->save_kwic()) === TRUE){
						$db_inserts++;																									
					}
				}		
				exit($db_inserts . ' of ' . $total_inserts . ' inserted into the database');
			}
			else{
				exit('Error with Kwic data');	
			}
				
		}
		
	}			 			 			 		
?>



<html>

	<head>

		<title></title>

	</head>

	<body>

		<div style="width:600px; margin-left:auto; margin-right:auto">

		    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">

		        <fieldset>

		        	<legend>Input Data</legend>

			        <?php

			        			$display = array();

			        			foreach ($jsontoarray as $key => $value) {

									ltrim(rtrim(preg_match($_SESSION['pattern'], $value,$matches)));									

									$display["original"][] = $matches[2]; 

									$display["cs"][] = $matches[1];

									$display["url"][] = $matches[3];									

								}		   

								$original = array_unique($display["original"],SORT_STRING);

								$cs = array_unique($display["cs"]);

								$url = array_unique($display["url"],SORT_STRING);																								     											 			        		

		        	?>

		        	<span><p> <b>Original:</b><hr/> <?php echo implode("<br/>",$original) ?></p></span>

		        	<span><p> <b>Circular Shifted:</b><hr/> <?php echo implode("<br/>",$cs) ?></p></span>

		        	<span><p> <b>URL:</b><hr/> <?php echo implode("<br/>",$url) ?></p></span>    			        

			        <br />

			        <div align="right">

			        	<input type="submit" id="btnSave" name="btnSave" value="Save"/>&nbsp;&nbsp;			        	

			        </div>		        			        		        

				</fieldset>

				

				<hr/>

				

				<h3> KWIC </h3>

				<table>

					<tr>

						<th>ID</th>

						<th>CIRCULAR SHIFTED</th>

						<th>ORIGINAL</th>						

						<th>URL</th>

					</tr>

					

					<?php 

						$retrieve = $db->get_all_rows();	

						if(isset($retrieve) && $retrieve !== null){ 														

							$kwic = $retrieve["kwic"];

																				

							foreach ($kwic as $value) {

								echo "<tr>";

								echo "<td>";

								echo $value->get_id();

								echo "</td>";

								echo "<td>";

								echo $value->get_cs();								

								echo "</td>";

								echo "<td>";

								echo $value->get_original();

								echo "</td>";

								echo "<td>";

								echo $value->get_url();

								echo "</td>";

								echo "</tr>";

							}													

						}else{

							echo "<tr><td>No Records exists in the Database</td></tr>";

						}																						

					?>

					

				</table>

			</form>

		</div>

	</body>

</html>

