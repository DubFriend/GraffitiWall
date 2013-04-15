<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);

require 'graffiti_wall.php';

$GraffitiWall = new Graffiti_Wall('moves.json');
if(isset($_GET['act']) && $_GET['act'] === "save_painting") {
	$GraffitiWall->save_painting($_POST['paint_moves']);
}
else {
?>
<html>
<head>

<title>Graffiti Wall Example</title>
<link rel='stylesheet' href='http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css' />

</head>
<body>

	<div id='graffiti_wall_container'>
		<div id='pallete'>
			<div id='paint_color'>
				<input class='color' value='57A5FF'>
			</div>
			<div id='brush_size'>
				<div id='brush_size_slider'></div>
			</div>
		</div>
		<form id='save_painting' method='post' enctype='application/x-www-form-urlencoded'>
			<input type='hidden' id='paint_moves' name='paint_moves'/>
			<input type='submit' value='Save Painting' />
		</form>
		
		<canvas id='canvas' width='<?php echo Graffiti_Wall::MAX_X; ?>'
			                height='<?php echo Graffiti_Wall::MAX_Y; ?>'></canvas>
	</div>

	<span id='old_moves_data' style='display:none;'><?php echo $GraffitiWall->get_json_data(); ?></span>

	<script src='jscolor/jscolor.js'></script>
	<script src='http://code.jquery.com/jquery-latest.min.js'></script>
	<script src='http://code.jquery.com/ui/1.10.0/jquery-ui.js'></script>
	<script src='graffiti_wall.js'></script>

</body>

</html>
<?php
}
?>