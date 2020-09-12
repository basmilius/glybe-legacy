<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Kladblok', 'access' => Array(true, false));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<div class="container epic_large">
			<div class="c_box">
				<div class="heading"><div class="icon group"></div>Mijn kladblok</div>
				<div class="inner">
				<?php
				if(isset($_POST['submit']))
				{
					DB::Query("UPDATE users SET kladblok = '" . DB::Escape($_POST['kladblok']) . "' WHERE id = '" . $user->Id . "'");
					header('Location: /account/kladblok');
				}
				?>
					<form action="" method="POST">
						<textarea style="height: 200px; width: 850px;" name="kladblok"><?php echo  htmlspecialchars($user->Kladblok); ?></textarea><br />
						<input type="submit" name="submit" value="Bewaar">
					</form>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Account' => '/profiel/' . strtolower($user->Username), 'Kladblok' => '/account/kladblok');
include'content/footer.php';
?>