<?php
    ini_set("display_errors",1);
    require_once('master_controller.php');
		$main = new MasterController();
		if($_POST['btnSubmitCS']){
    	$output = $main->process($_POST['dataInput']);
		}
?>
<html>
<head>
    <title>SAD Homework II</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<body>
<a href='search_interface.php'>Microminer Page</a>
<div style="width:600px; margin-left:auto; margin-right:auto;">
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <h3>Input Data</h3>
        <textarea name="dataInput" style="width:500px; height:300px;"><?= $_POST['dataInput'] ?></textarea>
        <br>
        <input type="submit" name="btnSubmitCS" value="Process" />
        <h3>Output Data</h3>
        <textarea name="dataOutput" style="width:500px; height:300px;"><?= $output ?></textarea>
        
    </form>

</div>
<?php if($main->json_post() != ''): ?>
<script>
	alert('<?=$main->json_post();?>');
</script>
<?php endif; ?>
</body>
</html>

