	<?php
	$pTimeEnd = microtime();
	$pExplode = explode(" ", $pTimeEnd);
	$pTimeEnd2 = $pExplode[0];
	$pSec2 = date("U");
	$pEnd = $pTimeEnd2 + $pSec2;
	$parseTime = $pEnd - $pStart;
	$parseTime = round($parseTime,5);
	$parseTime = str_replace(',', '.', $parseTime);
	?>
	<div class="footer">
		<div class="heading">
			<a href="/"><div class="item home"></div></a>
			<?php
			if(isset($footerLinks) && is_array($footerLinks))
			{
				foreach($footerLinks as $caption => $url)
				{
					echo '<a href="' . $url . '"><div class="item text"><div class="caption">' . $caption . '</div></div></a>';
				}
			}
			?>
			<a><div class="item text" style="background: transparent; padding-right: 14px; float: right;"><div class="caption"><?php echo $version; ?></div></div></a>
			<div class="clear"></div>
		</div>
		<div class="inner" style="padding: 7px 20px;">
			<table border="0" cellspacing="0" style="width: 100%;">
				<tr>
					<td style="width: 201px;" valign="top">
						<div class="nav_link static"><div class="icon house"></div><strong>Glybe</strong></div>
						<a href="/"><div class="nav_link static"><div class="icon house"></div>Homepage</div></a>
						<a href="/glybe/over"><div class="nav_link static"><div class="icon information"></div>Over Glybe</div></a>
						<a href="/glybe/team"><div class="nav_link static"><div class="icon user_gray"></div>Het Team</div></a>
						<a href="/glybe/av"><div class="nav_link static"><div class="icon shield"></div>AV &amp; Privacy</div></a>
					</td>
					<td style="width: 201px;" valign="top">
						<div class="nav_link static"><div class="icon group"></div><strong>Leden</strong></div>
						<?php if(!isset($user)) { ?>
						<a href="/login"><div class="nav_link static"><div class="icon user_go"></div>Inloggen</div></a>
						<a href="/aanmelden"><div class="nav_link static"><div class="icon user_add"></div>Aanmelden</div></a>
						<?php } else { ?>
						<a href="/profiel/<?php echo strtolower($user->Username); ?>"><div class="nav_link static"><div class="icon user_green"></div>Mijn Profiel</div></a>
						<a href="/account/instellingen"><div class="nav_link static"><div class="icon user_edit"></div>Account Instellingen</div></a>
						<a href="/vrienden/index"><div class="nav_link static"><div class="icon group_go"></div>Mijn Vrienden</div></a>
						<a href="/glybe/online"><div class="nav_link static"><div class="icon status_online"></div><strong><?php echo count(Glybe::GetOnlineUsersAsArray()); ?></strong> Online Leden</div></a>
						<?php } ?>
					</td>
					<td style="width: 201px;" valign="top">
						<div class="nav_link static"><div class="icon comments"></div><strong>Het Forum</strong></div>
						<a href="/forum/index"><div class="nav_link static"><div class="icon comments"></div>Forum Homepage</div></a>
						<a href="/forum/statistieken"><div class="nav_link static"><div class="icon chart_bar"></div>Forum Statistieken</div></a>
						<a href="/forum/forum?id=1"><div class="nav_link static"><div class="icon exclamation"></div>Mededelingen</div></a>
					</td>
					<td style="width: 201px;" valign="top">
						<div class="nav_link static"><div class="icon information"></div><strong>Informatie</strong></div>
						<div class="nav_link static"><div class="icon clock"></div>Parsetijd: <strong><?php echo $parseTime; ?></strong></div>
						<div class="nav_link static"><div class="icon group"></div><strong><?php echo DB::NumRowsQuery("SELECT 1 FROM users"); ?></strong> Leden</div>
						<div class="nav_link static"><div class="icon note"></div><strong><?php echo number_format(DB::NumRowsQuery("SELECT 1 FROM forum_posts"), 0, ",", "."); ?></strong> Posts in het Forum</div>
						<div class="nav_link static"><div class="icon vcard"></div><strong><?php echo number_format(DB::NumRowsQuery("SELECT 1 FROM forum_topics"), 0, ",", "."); ?></strong> Topics in het Forum</div>
					</td>
				</tr>
			</table>
			<center><i>Copyright &copy; <?php echo date("Y"); ?> Glybe - Alle rechten voorbehouden aan <a href="http://www.basmilius.com/" target="_blank">Bas Milius</a>.</i></center>
		</div>
	</div>
	<div class="notif_bar" style="bottom: -30px;">
		<div class="notif_holder">
			<div></div>
		</div>
	</div>
</body>
</html>