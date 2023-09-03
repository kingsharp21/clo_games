<?php

global $custom_path;

if($custom_path == 'game') { ?>
<div id="report-modal" class="report-modal">
	<div class="report-modal-content">
		<span class="close">&times;</span>
		<p><strong><?php _e('Report Game') ?></strong></p>
		<form id="report-form" enctype="multipart">
			<div>
				<div class="report-label" style="background-color: #ffdd24;">
					<input type="radio" id="r-bug" name="report[]" value="bug" checked>
					<label for="r-bug"><?php _e('Bug') ?></label>
				</div>
				<div class="report-label" style="background-color: #fd6d6d;">
					<input type="radio" id="r-error" name="report[]" value="error">
					<label for="r-error"><?php _e('Error') ?></label>
				</div>
				<div class="report-label" style="background-color: #a9df8b;">
					<input type="radio" id="r-other" name="report[]" value="other">
					<label for="r-other"><?php _e('Other') ?></label>
				</div>
			</div>
			<textarea style="width: 100%" rows="3" name="comment" maxlength="150" autocomplete="off" placeholder="<?php _e('Optional') ?>"></textarea>
			<input type="submit" style="margin-top: 10px;" value="<?php _e('Submit') ?>">
		</form>
	</div>
</div>
<script type="text/javascript" src="<?php echo DOMAIN ?>content/plugins/game-reports/script.js"></script>
<?php } ?>