<?php
include'../includes/inc.bootstrap.php';
$page = Array('title' => 'Beheer', 'access' => Array(true, false, 'is_team'));
include'../content/header.php';
include'../content/heading.php';
?>
<style type="text/css">
.ding
	{
		position: relative;
		float: left;
		width: 20%;
		color: #6B6B6B;
		text-align: center;
		padding: 10px 0px;
		cursor: pointer;
		-webkit-user-select: none;
		-moz-user-select: none;
		-o-user-select: none;
		-ms-user-select: none;
		text-shadow: 0px 1px #FFFFFF;
	}
.ding .count
	{
		font-weight: bold;
		font-size: 18px;
	}
.ding .what
	{
		font-weight: normal;
		font-size: 11px;
		color: #7C7C7C;
	}
.ding:hover
	{
		background: #C4C4C4;
		border-radius: 4px;
	}
</style>
<?php
$rQuery = DB::NumRowsQuery("SELECT 1 FROM forum_reports WHERE behandeld = 0");
$tQuery = DB::NumRowsQuery("SELECT 1 FROM tickets WHERE gelezen = 0");
$bQuery = DB::NumRowsQuery("SELECT 1 FROM users WHERE permission_id = 0");
?>
	<div class="content">
		<div class="container epic_large">
			<div class="c_box">
				<div class="inner stats">
					<div class="ding">
						<span class="count"><?php echo $rQuery; ?></span><br />
						<span class="what">Nieuwe report<?php echo ($rQuery != 1 ? 's' : ''); ?></span>
					</div>
					
					<div class="ding">
						<span class="count"><?php echo $tQuery; ?></span><br />
						<span class="what">Nieuwe ticket<?php echo ($tQuery != 1 ? 's' : ''); ?></span>
					</div>
					
					<div class="ding">
						<span class="count"><?php echo $bQuery; ?></span><br />
						<span class="what">Ban<?php echo ($bQuery != 1 ? 's' : ''); ?></span>
					</div>
					<!--
					<div class="ding">
						<span class="count">0</span><br />
						<span class="what">ding</span>
					</div>
					
					<div class="ding">
						<span class="count">0</span><br />
						<span class="what">ding</span>
					</div>
					-->
					<div style="clear: both;"></div>
				</div>
			</div>
		</div>
		
		<div class="container" style="width: 440px;">
			<div class="c_box">
				<div class="heading"><div class="icon building"></div>Algemeen</div>
				<div class="inner">
					hgfhgh
				</div>
			</div>
		</div>
		
		<div class="container" style="width: 440px;">
			<div class="c_box">
				<div class="heading"><div class="icon comments"></div>Forum</div>
				<div class="inner">
					hgfhgh
				</div>
			</div>
		</div>
		
		<div class="container" style="width: 440px;">
			<div class="c_box">
				<div class="heading"><div class="icon help"></div>Support</div>
				<div class="inner">
					hgfhgh
				</div>
			</div>
		</div>
		
		<div class="container" style="width: 440px;">
			<div class="c_box">
				<div class="heading"><div class="icon group"></div>Gebruikers</div>
				<div class="inner">
					hgfhgh
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
include'../content/footer.php';
?>