<!DOCTYPE html>
<html>
<head>
    <title>SAD Homework III</title>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<body>
  <!-- http://63.223.84.104/sdd-hw3/microminer_interface.php !-->
	<div style="width:500px; margin-left:auto; margin-right:auto">
		<h1 align="center">Microminer</h1>
	    <form id="form_keywords" action="master_controller.php" method="post">
	        <fieldset>
	        	<textarea name="keywords" id="keywords" style="width: 500px;height: 100px" ></textarea>		    	  
		        <br/><br/>
		        <div align="center">
		        	<input type="submit" id="btnSubmit" name="btnSubmit" value="Get Result"/>
		        </div>		        		        
		        <br/>
	        	<textarea id="result" name="result" style="width: 500px;height: 200px" ></textarea>
			</fieldset>	        							         		       
	    </form>
	</div>
<script>
	$(function(){
		$('#form_keywords').submit(function(){
			$('#result').text('')
			var data = {
				btnSubmit : true,
				keywords : $("#keywords").val(),
			}
			$.ajax({
				url: 'master_controller.php',
				dataType:"json",
				data:data,
				type:"POST",
				success: function(result){
					for(var i=0; i < result.length; i++){
						$('#result').append(result[i] + "\n");
					}
				},
				error: function(a,b,c){
					alert(c);
				},
			});
			return false;
		});
	});
</script>
</body>
</html>