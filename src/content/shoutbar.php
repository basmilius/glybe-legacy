	<?php if(isset($user) && isset($akjdklajsdlkjaklsdjlasd)) { ?>
	<div class="shoutbar">
		<div class="shouts">
			<div class="shout">
				<table border="0" cellspacing="0">
					<tr>
						<td style="padding: 4px;"><?php echo $user->GetAvatar(48); ?></td>
						<td style="padding: 6px;"><?php echo UBB::Parse("Hoi ik ben een shout. :')"); ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<?php } ?>