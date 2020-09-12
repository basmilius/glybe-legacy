<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Over', 'access' => Array(true, true));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon newspaper"></div>Over Glybe</div>
				<div class="inner">
					Glybe is een online community voor jong en oud waar je door middel van ons forum met elkaar kan kletsen over vanalles en niets. Glybe is zo gericht om haar leden, we luisteren namelijk naar wat onze leden willen.<br/>
					<br/>
					Glybe is een onderdeel van <a href="http://www.basmilius.com/" target="_blank">Bas Milius</a> en word ook door Bas ontwikkeld.
				</div>
			</div>
		</div>
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon user_gray"></div>Glybe CEO</div>
				<div class="inner">
					Glybe is in bezit van <a href="http://www.basmilius.com/" target="_blank">Bas Milius</a>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon award_star_gold_2"></div>Met dank aan..</div>
				<div class="inner">
					<strong>Bas Milius</strong><br/>
					<i>Voor het ontwikkelen en financieren van Glybe</i><br/>
					<br/>
					<strong>Jesse Reitsma</strong><br/>
					<i>Kleine ontwikkeling rondom Glybe</i><br/>
					<br/>
					<strong>Onze leden</strong><br/>
					<i>Voor alles in het forum eigenlijk..</i>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Glybe' => '/glybe/over', 'Over' => '/glybe/over');
include'content/footer.php';
?>