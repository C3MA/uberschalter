<!DOCTYPE html>
<html>
<head>
<title>Lamp Pi</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="refresh" content="60; index.php">
<link rel="stylesheet" href="style/jquery.mobile-1.4.0.min.css" />
<script type="text/javascript" src="style/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="style/jquery.mobile-1.4.0.min.js"></script>
</head>
<body>

	<script type="text/javascript">
		
	</script>

	<div data-role="page">

		<div data-role="header">
			<h1>Lamp control (Ueberschalter)</h1>
		</div>
		<!-- /header -->

		<div data-role="content">
			<div class="content-primary">

				<div data-role="tabs" id="tabs">
					<div data-role="navbar">
						<ul>
							<li><a href="#binary" data-ajax="false">Binary</a></li>
							<li><a href="#rgb" data-ajax="false">RGB</a></li>
						</ul>
					</div>
					<div id="binary" class="ui-body-d ui-content">
						<form action="index.php" method="get" name="binaryForm" id="binaryForm" >
							<label for="b1"> Lampe #1</label> <input type="checkbox"
								data-role="flipswitch" name="b1" id="b1" {if isset($bin1)}checked="checked"{/if} onchange="this.form.submit()">
							<label for="b2"> Lampe #2</label> <input type="checkbox"
								data-role="flipswitch" name="b2" id="b2" {if isset($bin2)}checked="checked"{/if} onchange="this.form.submit()">
							<label for="b3"> Lampe #3</label> <input type="checkbox"
								data-role="flipswitch" name="b3" id="b3" {if isset($bin3)}checked="checked"{/if} onchange="this.form.submit()">
							<label for="b4"> Lampe #4</label> <input type="checkbox"
								data-role="flipswitch" name="b4" id="b4" {if isset($bin4)}checked="checked"{/if} onchange="this.form.submit()">
							<label for="b5"> Lampe #5</label> <input type="checkbox"
								data-role="flipswitch" name="b5" id="b5" {if isset($bin5)}checked="checked"{/if} onchange="this.form.submit()">
							<label for="b6"> Lampe #6</label> <input type="checkbox"
								data-role="flipswitch" name="b6" id="b6" {if isset($bin6)}checked="checked"{/if} onchange="this.form.submit()">
						</form>
					</div>
					<div id="rgb">
						<form action="index.php" method="get" name="rgbForm" id="rgbForm" >
							<fieldset data-role="controlgroup" data-type="horizontal">
								<legend>Light Tiles:</legend>
								<input name="rgb1" id="rgb1" type="checkbox"> <label for="rgb1">1</label>
								<input name="rgb2" id="rgb2" type="checkbox"> <label for="rgb2">2</label>
								<input name="rgb3" id="rgb3" type="checkbox"> <label for="rgb3">3</label>
								<input name="rgb4" id="rgb4" type="checkbox"> <label for="rgb4">4</label>
								<input name="rgb5" id="rgb5" type="checkbox"> <label for="rgb5">5</label>
								<input name="rgb6" id="rgb6" type="checkbox"> <label for="rgb6">6</label>
							</fieldset>

							<label for="red">Red</label>
							<input name="red" id="red" min="0"
								max="255" value="128" data-show-value="true"
								data-popup-enabled="true" type="range" >
							<label	for="green">Green</label>
							<input name="green" id="green" min="0"
								max="255" value="128" data-show-value="true"
								data-popup-enabled="true" type="range" >
							<label for="blue">Blue</label>
							<input name="blue" id="blue" min="0"
								max="255" value="128" data-show-value="true"
								data-popup-enabled="true" type="range" >

							<input	type="submit" name="submit" id="submit" value="Submit" >

						</form>
					</div>
				</div>



			</div>
		</div>

	</div>
	<!-- /page -->

</body>
</html>
