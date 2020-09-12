<?php
include'includes/inc.bootstrap.php';
$page = Array('title' => 'Linkpartners', 'access' => Array(true, true));
include'content/header.php';
include'content/heading.php';
?>
	<div class="content">
		<?php
		$catQuery = DB::Query("SELECT id, cat, link FROM glybe_partners WHERE type = 'cat'");
		
		while($catFetch = DB::Fetch($catQuery)){
		?>
		<div class="container" style="width: 290px;">
			<div class="c_box">
				<div class="heading"><div class="icon <?php echo DB::Escape($catFetch['link']); ?>"></div><?php echo DB::Escape($catFetch['cat']); ?></div>
				<div class="inner">
					<?php
					$linkQuery = DB::Query("SELECT link, recommed, name FROM glybe_partners WHERE cat = '" . DB::Escape($catFetch['id']) . "'");
					
					if(DB::NumRows($linkQuery) <= 0)
						echo '<i>Er zijn nog geen partners in deze categorie</i>';
					else while($linkFetch = DB::Fetch($linkQuery)){
						
						echo ($linkFetch['recommed'] == 'true' ? '<b>' : '');
						
						echo '<a href="' . (substr($linkFetch['link'], 0, 7) != 'http://' ? 'http://' : '') . $linkFetch['link'] . '?s=Glybe" alt="' . $linkFetch['link'] . '" target="_new">';
						echo htmlspecialchars($linkFetch['name']);
						echo '</a>';
						
						echo ($linkFetch['recommed'] == 'true' ? '</b>' : '');
						
						echo '<br />';
					}
					?>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		<div class="clear"></div>
	</div>
<?php
$footerLinks = Array('Glybe' => '/glybe/over', 'Het team' => '/glybe/team');
include'content/footer.php';
?>