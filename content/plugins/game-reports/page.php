<div id="action-info"></div>
<div class="section">
	<p>
		<a href="https://cloudarcade.net/tutorial/game-reports-plugin/" target="_blank">Guide: how to use "Game Reports" plugin</a>
	</p>
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Type</th>
					<th>Game</th>
					<th>Date</th>
					<th>Comment</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php

				$index = 0;
				$reports = get_option('game-reports');
				if($reports){
					$reports = json_decode($reports, true);
					foreach ( $reports as $item ) {
						$index++;
						$color = '';
						if($item['type'] == 'bug'){
							$color = 'bg-warning';
						} elseif($item['type'] == 'error'){
							$color = 'bg-danger';
						} elseif($item['type'] == 'other'){
							$color = 'bg-success';
						}
						$game = Game::getById($item['game_id']);
						?>

						<tr id="tr-<?php echo esc_int($item['id']) ?>">
							<th scope="row"><?php echo $index ?></th>
							<td>
								<span class="<?php echo $color ?> text-dark"> <?php echo $item['type'] ?> </span>
							</td>
							<td>
								<a href="<?php echo get_permalink('game', $game->slug) ?>" target="_blank"><?php echo $game->title ?></a>
							</td>
							<td>
								<?php echo $item['date'] ?>
							</td>
							<td>
								<?php echo $item['comment'] ?>
							</td>
							<td>
								<span class="actions">
									<a href="#" class="deleteReport" id="<?php echo esc_int($item['id']) ?>"><i class="fa fa-trash circle" aria-hidden="true"></i></a>
								</span>
							</td>
						</tr>

						<?php
					}
				}
					
				?>
						
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.deleteReport').click(function(){
			let id = $(this).attr('id');
			$.ajax({
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
			});
		});
	});
</script>