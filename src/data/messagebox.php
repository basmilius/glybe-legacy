<script type="text/javascript">
Glybe.Overlay.SetSize(400, 100);
</script>
<div class="heading"><div class="icon exclamation"></div><?php echo htmlspecialchars($_POST['mt']); ?></div>
<div class="inner" style="text-align: center;">
	<?php echo htmlspecialchars($_POST['mc']); ?>
	<div style="border-top: 1px solid #C6C6C6; margin: 5px 1px;"></div>
	<input type="button" value="Ok&eacute;" onclick="<?php echo $_POST['ma']; ?>Glybe.Overlay.Close();" />
</div>