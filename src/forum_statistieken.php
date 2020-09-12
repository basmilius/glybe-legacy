<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Forum Statistieken', 'access' => Array(true, true));
include'content/header.php';
include'content/heading.php';

$todayPosts = DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE post_timestamp BETWEEN UNIX_TIMESTAMP(CURDATE()) AND UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL + 86399 SECOND))");
$yesterdayPosts = DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE post_timestamp BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 1 DAY)) AND UNIX_TIMESTAMP(CURDATE())");
$tdbyPosts = DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE post_timestamp BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 2 DAY)) AND UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL - 1 DAY))");

$myTodayPosts = DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE user_id = '" . ((isset($user)) ? $user->Id : 0) . "' AND post_timestamp BETWEEN UNIX_TIMESTAMP(CURDATE()) AND UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL + 86399 SECOND))");
$myYesterdayPosts = DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE user_id = '" . ((isset($user)) ? $user->Id : 0) . "' AND post_timestamp BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 1 DAY)) AND UNIX_TIMESTAMP(CURDATE())");
$myTdbyPosts = DB::NumRowsQuery("SELECT 1 FROM `forum_posts` WHERE user_id = '" . ((isset($user)) ? $user->Id : 0) . "' AND post_timestamp BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(),  INTERVAL - 2 DAY)) AND UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL - 1 DAY))");
?>
	<div class="content">
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon award_star_gold_3"></div>De 50 grootste spammers ooit!</div>
				<div class="inner">
					<?php
					$sQuery = DB::Query("	SELECT
											u.username uName,
											COUNT(p.user_id) cPosts
										FROM
											forum_posts p,
											users u
										WHERE
											u.id = p.user_id
										AND
											p.state != 'deleted'
										AND
											u.id != 3
										GROUP BY
											p.user_id
										ORDER BY
											cPosts DESC,
											u.id
										LIMIT
											50");
					if(DB::NumRows($sQuery) === 0)
					{
						echo'	<center><i>Er is vandaag nog niks gepost!</i></center>';
					} else {
						$p = 0;
						echo'	<table class="top_list" border="0" cellspacing="1" style="width: 100%;">';
						while($sFetch = DB::Fetch($sQuery))
						{
							$p++;
							echo'	<tr>
									<td style="width: 15px; text-align: center; font-weight: ' . (($p < 4) ? 'bold' : 'normal') . ';">' . $p . '</td>
									<td><a href="/profiel/' . strtolower($sFetch['uName']) . '">' . $sFetch['uName'] . '</a></td>
									<td style="width: 80px;">' . number_format($sFetch['cPosts'], 0, ",", ".") . ' posts</td>
								</tr>';
						}
						echo'	</table>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="container large">
			<div class="c_box">
				<div class="heading"><div class="icon chart_bar"></div>Statistieken</div>
				<div class="inner">
					<table class="top_list" border="0" cellspacing="1" style="width: 100%;">
						<tr>
							<td><strong>Aantal Topics</strong></td>
							<td><?php echo number_format(DB::NumRowsQuery("SELECT 1 FROM `forum_topics`"), 0, ",", "."); ?> Topics</td>
						</tr>
						<tr>
							<td><strong>Aantal Posts</strong></td>
							<td><?php echo number_format(DB::NumRowsQuery("SELECT 1 FROM `forum_posts`"), 0, ",", "."); ?> Posts</td>
						</tr>
						<tr>
							<td><strong>Aantal Posts vandaag</strong></td>
							<td><?php echo number_format($todayPosts, 0, ",", "."); ?> Posts</td>
						</tr>
						<tr>
							<td><strong>Aantal Posts gisteren</strong></td>
							<td><?php echo number_format($yesterdayPosts, 0, ",", "."); ?> Posts</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon chart_curve"></div>Vandaag ten opzichte van Gisteren</div>
				<div class="inner">
					<div id="chart_posts" style="height: 220px;">Laden..</div>
					<script type="text/javascript">
					google.load("visualization", "1", {packages:["corechart"]});
					google.setOnLoadCallback(drawChart);
					
					function drawChart()
					{
						var data = google.visualization.arrayToDataTable([
							['Dag', 'Totaal', 'Van mij'],
							['Eergisteren', <?php echo $tdbyPosts; ?>, <?php echo $myTdbyPosts; ?>],
							['Gisteren', <?php echo $yesterdayPosts; ?>, <?php echo $myYesterdayPosts; ?>],
							['Vandaag', <?php echo $todayPosts; ?>, <?php echo $myTodayPosts; ?>]
						]);
						var options = {
							title: 'Vandaag vs Gisteren'
						};
						var chart = new google.visualization.LineChart(document.getElementById('chart_posts'));
						chart.draw(data, options);
					}
					</script>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Forum' => '/forum/index', 'Statistieken' => '/forum/statistieken');
include'content/footer.php';
?>