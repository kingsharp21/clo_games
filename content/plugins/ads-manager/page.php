<?php

$list = get_option('ads-manager');
$array;
if(!$list){
	$array = array(
		'IMA' => array(
			'value' => '',
			'description' => 'Also called Google Ads',
			'placeholder' => 'IMA Ad Tag',
			'default' => true
		),
		'Banner' => array(
			'value' => '',
			'description' => 'Custom banner ads (Image)',
			'placeholder' => 'Image url',
			'url' => '',
			'default' => false
		)
	);
	update_option('ads-manager', json_encode($array));
} else {
	$array = json_decode($list, true);
}

if(isset($_POST['action'])){
	if($_POST['action'] == 'update_tag'){
		foreach ($array as $tag => $item) {
			if($tag == $_POST['Default']){
				$array[$tag]['default'] = true;
			} else {
				$array[$tag]['default'] = false;
			}
			if(isset($_POST[$tag])){
				$array[$tag]['value'] = $_POST[$tag];
				if(isset($array[$tag]['url'])){
					if(isset($_POST[$tag.'-url'])){
						$array[$tag]['url'] = $_POST[$tag.'-url'];
					}
				}
			}
		}
		update_option('ads-manager', json_encode($array));
		show_alert('Tags Updated', 'success');
	}
}
?>

<div id="action-info"></div>
<div class="section">
	<p>
		Ads Manager plugin is used to manage and show pre-roll or in-game ads, or simply advertisement that loaded and shown inside the game.<br>
		This ad only works with self-uploaded/self-hosted games that integrated with the latest version of CloudArcade API.
	</p>
	<p>
		<a href="https://cloudarcade.net/tutorial/ads-manager-plugin/" target="_blank">Guide: how to use "Ads Manager" plugin</a>
	</p>
	<div class="mb-5"></div>
	<form id="form-ads-manager" method="post" enctype="multipart">
		<input type="hidden" name="action" value="update_tag">
	  <div class="form-group row">
	    <label for="select-default" class="col-sm-2 col-form-label">Default Ad</label>
	    <div class="col-sm-10">
	      <select id="select-default" class="form-control" name="Default">
	      	<?php
	      	foreach ($array as $tag => $item) {
	      		$selected = '';
	      		if($item['default']){
	      			$selected = 'selected';
	      		}
	      		echo '<option value="'.$tag.'" '.$selected.'>'.$tag.'</option>';
	      	}
	      	?>
	      </select>
	    </div>
	  </div>
	  <hr>
	  <?php
	  foreach ($array as $tag => $item) {
	  	?>
	  	<div class="form-group row">
		    <label class="col-sm-2 col-form-label"><?php echo $tag ?> <i class="ml-3 fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="<?php echo $item['description'] ?>"></i></label>
		    <div class="col-sm-10">
		      <input type="text" class="form-control" name="<?php echo $tag ?>" placeholder="<?php echo $item['placeholder'] ?>" value="<?php echo $item['value'] ?>">
		      <?php if(isset($item['url'])){ ?>
		      	<div class="mb-2"></div>
		      	<input type="text" class="form-control" name="<?php echo $tag ?>-url" placeholder="Banner url" value="<?php echo $item['url'] ?>">
		      <?php } ?>
		    </div>
		  </div>
	  	<?php
	  }
	  ?>
	  <button type="submit" class="btn btn-primary btn-md">Save</button>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('form#form-ads-manager').submit(function(event){
			//event.preventDefault();
			console.log($(this).serializeArray());
			/*$.ajax({
				url: '/content/plugins/game-reports/action.php',
				type: 'POST',
				dataType: 'json',
				data: {action: 'delete', id: id},
				complete: function (data) {
					console.log(data.responseText);
					if(data.responseText === 'deleted'){
						$('#action-info').html('<div class="alert alert-success alert-dismissible fade show" role="alert">Report deleted!<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						$('#tr-'+id).remove();
					} else {
						$('#action-info').html('<div class="alert alert-warning alert-dismissible fade show" role="alert">Failed! Check console log<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
					}
				}
			});*/
		});
	});
</script>