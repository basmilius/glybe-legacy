<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Ticket bekijken', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<style type="text/css">
	hr
	{
		height: 1px;
		border: 0;
		color: #000;
		background-color: #000;
	}
	</style>
	<div class="content">
		<div class="container epic_large">
			<div class="c_box">
				<div class="heading"><div class="icon information"></div>Bekijk ticket</div>
				<div class="inner">
					<?php
					if($user->HasPermissions('is_support'))
					{
						DB::Query("UPDATE tickets SET gelezen = 1 WHERE id = '" . DB::Escape($_GET['id']) . "'");
					}
					if((isset($_GET['optie'])) && ($_GET['optie'] == 'sluit'))
					{
						if($user->HasPermissions('is_support'))
						{
							DB::Query("UPDATE tickets SET state = 'closed' WHERE id = '" . DB::Escape($_GET['id']) . "'");
							echo '<div class="error_notif success">Ticket is gesloten!</div><br />';
						}
					}
					if((isset($_GET['optie'])) && ($_GET['optie'] == 'open'))
					{
						if($user->HasPermissions('is_support'))
						{
							DB::Query("UPDATE tickets SET state = 'open' WHERE id = '" . DB::Escape($_GET['id']) . "'");
							echo '<div class="error_notif success">Ticket is nu weer open!</div><br />';
						}
					}
					$query = DB::Query("SELECT * FROM tickets WHERE id = '" . DB::Escape($_GET['id']) . "'");
					if(DB::NumRows($query) > 0)
					{
						$fetch = DB::Fetch($query);
						if(($fetch['user_id'] == $user->Id) OR ($user->HasPermissions('is_support')))
						{
							$user2 = new User($fetch['user_id'], false);
							echo '<strong>Ticket: ' . htmlspecialchars($fetch['caption']) . '</strong><br /><br />';
							echo '<strong><a href="../../profiel/' . htmlspecialchars($user2->Username) . '">' . htmlspecialchars($user2->Username) . '</a> schreef  ' . Glybe::TimeAgo($fetch['date']) . '</strong><hr />';
								echo UBB::Parse($fetch['content']);
							echo '<hr /><br />';
							
							
							
							$query2 = DB::Query("SELECT * FROM tickets_reactions WHERE ticket_id = '" . DB::Escape($_GET['id']) . "'");
							while($fetch2 = DB::Fetch($query2))
							{
								$user3 = new User($fetch2['user_id'], false);
								if($user3->HasPermissions('is_support'))
								{
									echo '<strong>Glybe (<a href="../../profiel/' . htmlspecialchars($user3->Username) . '">' . htmlspecialchars($user3->Username) . '</a>) schreef  ' . Glybe::TimeAgo($fetch2['date']) . '</strong><hr />';
								}
								else
								{
									echo '<strong><a href="../../profiel/' . htmlspecialchars($user3->Username) . '">' . htmlspecialchars($user3->Username) . '</a> schreef  ' . Glybe::TimeAgo($fetch2['date']) . '</strong><hr />';
								}
									echo UBB::Parse($fetch2['content']);
								echo '<hr /><br />';
							}
							?>
							</div>
							</div>
							</div>
							<div class="container epic_large">
							<div class="c_box">
							<div class="heading"><div class="icon information"></div>Plaats een reactie</div>
							<div class="inner">
								<?php
								if($fetch['state'] == 'closed')
								{
									echo '<div class="error_notif error">Deze ticket is al afgehandeld, mocht je een nieuwe vraag hebben dan kan je een nieuw ticket aanmaken!</div>';
								}
								else
								{
									if($user->HasPermissions('is_support'))
									{
										echo '<a href="/ticket/lees?id='.$_GET['id'].'&optie=sluit">Sluit ticket</a><br /><br />';
									}
									if(isset($_POST['submit']))
									{
										if(!empty($_POST['bericht']))
										{
											$time = time();
											DB::Query("INSERT INTO tickets_reactions (ticket_id, user_id, content, date) VALUES ('" . DB::Escape($_GET['id']) . "', '" . $user->Id . "', '" . DB::Escape($_POST['bericht']) . "', '".$time."')");											
											echo '<div class="error_notif success">Je reactie is geplaatst!</div>';
											if(!$user->HasPermissions('is_support'))
											{
												DB::Query("UPDATE tickets SET gelezen = 0 WHERE id = '" . DB::Escape($_GET['id']) . "'");												
											}
											else
											{
												$messagePM = 'Hoi,\n\nEr is een reactie op je ticket geplaatst door een teamlid.\n[url=http://www.glybe.nl/ticket/lees?id='.DB::Escape($_GET['id']).']Klik hier om je ticket te bekijken![/url]\n\nGroet,\nGlybe';
												DB::Query("INSERT INTO messages (user_from_id, user_to_id, sended_on, subject, message) VALUES ('3', '".DB::Escape($fetch['user_id'])."', UNIX_TIMESTAMP(), 'Je hebt een reactie op je ticket!', '".$messagePM."')") or die(mysql_error());
											}
											?>
											<meta HTTP-EQUIV="REFRESH" content="0; url=../../ticket/lees?id=<?= $_GET['id']; ?>">
											<?php
										}
										else
										{
											echo '<div class="error_notif error">Je hebt geen reactie ingevuld!</div>';
										}
									}
									else
									{
										?>
										<form action="" method="POST">
											<textarea name="bericht" style="height: 150px; width: 97%;" placeholder="Plaats een reactie op dit ticket..."></textarea><br />
											<input type="submit" name="submit" value="Plaats reactie" />
										</form>
										<?php
									}
								}
						}
						else
						{
							echo '<div class="error_notif error">Je hebt geen toegang tot deze ticket!</div>';
						}
					}
					else
					{
						echo '<div class="error_notif error">Deze ticket bestaat niet!</div>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Tickets' => '/ticket/index', 'Ticket bekijken' => '/ticket/index');
include'content/footer.php';
?>