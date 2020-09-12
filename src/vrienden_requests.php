<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Vrienden verzoeken', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container big">
			<div class="c_box">
				<div class="heading"><div class="icon group"></div>Vriendenverzoeken</div>
				<div class="inner">
					<?php
					$rQuery = DB::Query("SELECT * FROM users_friends_requests WHERE user_to = '" . $user->Id . "' ORDER BY id");
					if(DB::NumRows($rQuery) === 0)
					{
						echo'<center><i>Je hebt geen openstaande vrienden-verzoeken!</i></center>';
					} else {
						echo'	<table border="0" style="width: 100%;" cellspacing="1" class="top_list">';
						while($rFetch = DB::Fetch($rQuery))
						{
							$rUser = new User($rFetch['user_from'], false, false, true);
							echo'	<tr>
									<td style="padding: 5px; width: 48px;">' . $rUser->GetAvatar(48) . '</td>
									<td style="padding: 5px; width: 380px;">
										<strong>' . htmlspecialchars(((str_replace(" ", "", $rUser->GetSetting("displayname")) != "") ? $rUser->GetSetting("displayname") : $rUser->Username)) . '</strong><br/>
										Wilt vrienden met je worden
									</td>
									<td style="padding: 5px;"><input type="button" value="Accepteren" onclick="Glybe.Overlay.OpenUrlOverlay(\'/data/vriend_req.php\', { uId: ' . $rUser->Id . ', _act: \'acc\' });" /></td>
									<td style="padding: 5px;"><input type="button" value="Weigeren" onclick="Glybe.Overlay.OpenUrlOverlay(\'/data/vriend_req.php\', { uId: ' . $rUser->Id . ', _act: \'rem\' });" /></td>
								</tr>';
						}
						echo'	</table>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="container small">
			<div class="c_box">
				<div class="heading"><div class="icon house"></div>Menu</div>
				<div class="inner">
					<a href="/vrienden/index">
						<div class="nav_link">
							<div class="icon group"></div>
							Mijn vrienden
						</div>
					</a>
					<a href="/vrienden/requests">
						<div class="nav_link">
							<div class="icon user_comment"></div>
							Vrienden verzoeken 
							<?php
							if(count($user->FriendsReq) > 0)
							{
								echo '<strong>(' . count($user->FriendsReq) . ')</strong>';
							}
							?>
						</div>
					</a>
					<a href="/vrienden/suggesties">
						<div class="nav_link">
							<div class="icon group_add"></div>
							Vrienden van vrienden
						</div>
					</a>
					<a href="/vrienden/nieuw">
						<div class="nav_link">
							<div class="icon magnifier"></div>
							Zoek vrienden op
						</div>
					</a>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Vrienden' => '/vrienden', 'Mijn vriendenverzoeken' => '/vrienden/requests');
include'content/footer.php';
?>