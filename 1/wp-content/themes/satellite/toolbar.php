<?php global $qode_options_satellite; 

$qode_header_top="";
if (isset($_SESSION['qode_header_top'])) $qode_header_top = $_SESSION['qode_header_top'];

?>
<div id="panel" style="margin-left: -318px;">   
    <div id="panel-admin">
		<h4>Customization Options</h4>
		<!--
		<div class="panel-admin-box">
			<select id="tootlbar_header_top">
				<option value="">Header top menu</option>
				<option <?php //if ($qode_header_top == "yes") { echo "selected='selected'"; } ?> value="yes">Yes</option>
				<option <?php //if ($qode_header_top == "no") { echo "selected='selected'"; } ?> value="no">No</option>
		
			</select>
		</div>
		-->
		<div class="panel-admin-box">
			<select id="tootlbar_boxed">
				<option value="">Boxed Layout</option>
				<option value="">No</option>
				<option value="boxed">Yes</option>
			</select>
		</div>
		<div class="panel-admin-box">
			<select id="tootlbar_background">
				<option value="no">Choose background</option>
				<option value="background1">Background 1</option>
				<option value="background2">Background 2</option>
				<option value="background3">Background 3</option>
			</select>
		</div>
		<div class="panel-admin-box">
			<select id="tootlbar_pattern">
				<option value="no">Choose pattern</option>
				<option value="pattern11">Retina Wood</option>
				<option value="pattern12">Retina Wood Grey</option>
				<option value="pattern1">Transparent</option>
				<option value="pattern3">Cubes</option>
				<option value="pattern4">Diamond</option>
				<option value="pattern5">Escheresque</option>
				<option value="pattern10">Whitediamond</option>
				
			</select>
		</div>
		<div class="panel-admin-box">
			<p>Colors</p>
			<div id="tootlbar_colors">
				<div class="color active" data-color="#0079ff" style="background-color:#0079ff;"></div>
				<div class="color" data-color="#aac2d3" style="background-color:#aac2d3;"></div>
				<div class="color" data-color="#93b711" style="background-color:#93b711;"></div>
				<div class="color" data-color="#16a085" style="background-color:#16a085;"></div>
				<div class="color" data-color="#f75100" style="background-color:#f75100;"></div>
				<div class="color" data-color="#f22a8d" style="background-color:#f22a8d;"></div>
			</div>
		</div>
		
    </div>
    <a class="open" href="#"></a>

</div>