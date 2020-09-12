<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Het team', 'access' => Array(true, true));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container smal" style="width: 315px; float: right;">
			<div class="c_box">
				<div class="heading"><div class="icon user_comment"></div>Het Glybe-team</div>
				<div class="inner">
					Hier naast zie je alle team-leden van Glybe.<br />
					Zij zorgen ervoor dat het hier allemaal volgens de regels gaat en dat leden die zich ongepast gedragen van de website verbannen worden.<br />
					Maar zij zijn ook de personen waarbij je terecht kunt voor al je vragen omtrent Glybe.<br /><br />
					Voor algemene vragen hebben we een speciaal helpdesk team, zij zijn er om jouw vragen te beantwoorden en je zo goed mogelijk te helpen.<br />
					Ook zijn er moderators en beheeerders, de taak van een moderator is het in de gaten houden van het forum en alles daaromheen en waar nodig ingrijpen om het gezellig te houden hier op Glybe.<br /><br />
					Een beheerder heeft net weer iets meer rechten dan een moderator, beheerders kunnen achter de schermen dingen regelen die een moderator niet kan.<br />
					Zo kan een beheerder wel profielen wijzigen en je helpen als je een fout op je account hebt, maar zij kunnen geen bugs oplossen.<br /><br />
					Hiervoor zijn de oprichters er, zij ontwikkelen Glybe en zorgen ervoor dat de fouten (bugs) verdwijnen uit het systeem, maar zij zorgen ook dat er regelmatig nieuwe updates komen op Glybe!<br /><br />
					Een teamlid van Glybe staat altijd open voor vragen en / of commentaar, wij streven er naar om mensen zo snel mogelijk te helpen zo zou iedereen binnen de 24 uur geholpen worden, dat wil niet zeggen dat het altijd een dag duurt, het kan ook zijn dat je binnen een paar uur, of zelfs binnen een paar minuten geholpen bent.<br />
					Dit hangt er ook vanaf wat het probleem of de vraag is.<br /><br />
					Neem gerust contact met ons op of bekijk ons profiel voor meer informatie.<br />
					Weet je niet bij wie je je vraag moet stellen? Dan kan je altijd een helpdesk medewerker om hulp vragen.
				</div>
			</div>
		</div>
		<div class="container big" style="width: 565px;">
			<?php
			$teamQuery = DB::Query("SELECT `id`, `caption` FROM `users_permissions` WHERE `id` > 2 AND on_teampage = true ORDER BY `volgorde` DESC");
			while($teamFetch = DB::Fetch($teamQuery)){
			?>
			<div class="c_box">
				<div class="heading"><div class="icon user_gray"></div><?php echo $teamFetch['caption']; ?>s</div>
				<div class="inner">
					<div>
						<?php
						$usersQuery = DB::Query("SELECT u.* FROM users u WHERE u.permission_id = '" . $teamFetch['id'] . "' ORDER BY u.username");
						if(DB::NumRows($usersQuery) === 0) { echo '<td><i>Nog geen teamleden in deze categorie.</i></td>'; }
						while($usersFetch = DB::Fetch($usersQuery))
						{
							if($usersFetch['id'] == 3) continue;
							$onlineUser = new User($usersFetch['id'], false, false, true);
							echo'	<div style="position: relative; padding: 5px; float: left; text-align: center;">
									<a href="/profiel/' . strtolower($onlineUser->Username) . '">
										' . $onlineUser->GetAvatar(120) . '
										<span style="color: maroon; font-weight:bold;">' . htmlspecialchars(((str_replace(" ", "", $onlineUser->GetSetting("displayname")) != "") ? $onlineUser->GetSetting("displayname") : ucfirst($onlineUser->Username))) . '</span>
									</a>
								</div>
							';
						}
						?>					
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Glybe' => '/glybe/over', 'Het team' => '/glybe/team');
include'content/footer.php';
?>