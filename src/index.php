<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Home', 'access' => Array(false, true));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container" style="width: 540px;">
			<div class="c_box">
				<div class="heading"><div class="icon eye"></div>Kijk eens rond!</div>
				<div class="inner">
					<i>Op Glybe kun je je eigen profiel ontwerpen, bekijk eens een profiel van iemand en zie hoe hun het hebben gedaan om weer inspiratie te krijgen voor je eigen profiel!</i><br/>
					<div style="position: relative; margin: 5px; border-top: 1px solid #C6C6C6;"></div>
					<?php
					// Niels update: Random (Actieve) gebruikers :D (To-Do: Activiteit waarde verhogen bij meer leden)
					$userQuery = DB::Query("SELECT id FROM users WHERE permission_id >= 1 AND avatar != '0_default.png' AND (active >= 35 OR permission_id = 7) ORDER BY RAND() LIMIT 9");
					
					$users = Array(1, 8, 29, 6, 4);
					while($userFetch = DB::Fetch($userQuery))
					{
						$uId = $userFetch['id'];
						$usr = new User($uId, false, false, true);
						echo'	<a href="/profiel/'.strtolower($usr->Username).'">
								<div class="sp_pf_user">
									<img src="' . str_replace("thumb_", "thumb_149_", $usr->Avatar) . '" height="140" width="140" />
									<div class="name">' . htmlspecialchars(((str_replace(" ", "", $usr->GetSetting("displayname")) != "") ? $usr->GetSetting("displayname") : $usr->Username)) . '</div>
									<div class="transpixel"></div>
									<div class="over"></div>
								</div>
							</a>';
					}
					?>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="container" style="width: 340px;">
			<div class="c_box">
				<div class="heading"><div class="icon user_go"></div>Inloggen</div>
				<div class="inner">
					<form action="/login" method="post">
						<strong>Gebruikersnaam</strong><br/>
						<input type="text" name="username" style="width: 300px;" />
						<strong>Wachtwoord</strong><br/>
						<input type="password" name="password" style="width: 300px;" />
						<input type="submit" value="Inloggen" />
					</form>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon clock"></div>Laatste posts op Glybe</div>
				<div class="inner">
					<table class="top_list" border="0" cellspacing="1" style="width: 100%;">
						<tr>
							<td style="width: 20px;" align="center"><div class="icon folder_link"></div></td>
							<td><strong>Topic</strong></td>
							<td><strong>Laatste door</strong></td>
						</tr>
						<?php
						$ltQuery = DB::Query("	SELECT
												t.caption tName,
												p.id pId,
												p.post_timestamp postTimestamp,
												p.user_id
											FROM
												(SELECT * FROM forum_posts ORDER BY id DESC) AS p,
												forum_topics t,
												forum_categories c,
												forum_foras f
											WHERE
												t.id = p.topic_id
											AND
												f.id = t.forum_id
											AND
												c.id = f.category_id
											AND
												t.state != 'deleted'
											AND
												c.min_post_permissions <= '" . ((isset($user)) ? $user->RawData["permission_id"] : 0) . "'
											AND
												f.min_post_permissions <= '" . ((isset($user)) ? $user->RawData["permission_id"] : 0) . "'
											AND
												p.state != 'deleted'
											GROUP BY
												p.topic_id
											ORDER BY
												p.post_timestamp DESC
											LIMIT
												5");
						while($ltFetch = DB::Fetch($ltQuery))
						{
							$us = new User($ltFetch['user_id'], false, false, true);
							echo'	<tr>
									<td style="width: 20px;" align="center"><div class="icon folder_go"></div></td>
									<td><a href="/forum/postredir?pid=' . $ltFetch['pId'] . '">' . htmlspecialchars($ltFetch['tName']) . '</a></td>
									<td><a href="/profiel/' . strtolower($us->Username) . '">' . htmlspecialchars(((str_replace(" ", "", $us->GetSetting("displayname")) != "") ? $us->GetSetting("displayname") : $us->Username)) . '</a></td>
								</tr>';
						}
						?>
					</table>
				</div>
			</div>
			<div class="c_box">
				<div class="heading"><div class="icon status_online"></div>Wie <?php echo((count(Glybe::GetOnlineUsersAsArray()) == 1) ? 'is' : 'zijn'); ?> er al online?</div>
				<div class="inner">
					<?php
					echo Glybe::GetOnlineUsersAsString();
					?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
include'content/footer.php';
?>