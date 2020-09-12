<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Ticket aanmaken', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container epic_large">
			<div class="c_box">
				<div class="heading"><div class="icon information"></div>Nieuwe ticket aanmaken</div>
				<div class="inner">
					<?php
					if($_SERVER['REQUEST_METHOD'] == 'POST')
					{
						if(!empty($_POST['title']))
						{
							if(!empty($_POST['vraag']))
							{
								$time = time();
								DB::Query("INSERT INTO tickets (user_id, caption, content, date) VALUES ('" . $user->Id . "', '" . DB::Escape($_POST['title']) . "', '" . DB::Escape($_POST['vraag']) . "', '".$time."')");
								echo '<div class="error_notif success">Je ticket is aangemaakt, je wordt nu naar je ticket doorgelinkt.</div>';
								?>
								<meta HTTP-EQUIV="REFRESH" content="2; url=../../ticket/lees?id=<?= DB::InsertId(); ?>">
								<?php
							}
							else
							{
								echo '<div class="error_notif error">Je hebt geen vraag of opmerkingen o.i.d. ingevuld.</div>';
							}
						}
						else
						{
							echo '<div class="error_notif error">Je hebt geen titel ingevuld.</div>';
						}
					}
					?>
					<form action="" method="POST">
						<strong>Titel:</strong><br />
						<input type="text" name="title" style="width: 97%;" placeholder="Onderwerp van je ticket" value="<?php if(isset($_POST['title'])){ echo htmlentities($_POST['title']); } ?>" /><br />
						<strong>Je vraag, opmerking, o.i.d.:</strong><br />
						<textarea name="vraag" placeholder="Beschrijf hier je vraag, opmerking, o.i.d. zo duidelijk mogelijk..." style="height: 200px; width: 97%;"><?php if(isset($_POST['vraag'])){ echo htmlentities($_POST['vraag']); } ?></textarea><br />
						<input type="submit" name="submit" value="Verzend ticket" />
					</form>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Tickets' => '/ticket/index', 'Nieuw ticket' => '/ticket/nieuw');
include'content/footer.php';
?>