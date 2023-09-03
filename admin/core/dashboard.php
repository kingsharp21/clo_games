<?php
$warning_list = get_admin_warning();
if(!empty($warning_list)){
	echo('<div class="site-warning">');
	foreach ($warning_list as $val) {
		echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">'.$val.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
	}
	echo('</div>');
}
?>
<div class="update-info"></div>
<div class="row">
	<div class="col-lg-9">
		<div class="section">
			<select class="custom-select custom-select-sm stats-option" id="stats-option">
				<option value="week"><?php echo _t('Last %a days', 7) ?></option>
				<option value="month"><?php echo _t('Last %a days', 30) ?></option>
			</select>
			<h3 class="section-title"><?php echo _t('Statistics') ?></h3 class="section-title">
			<div class="container-stats">
				<div class="chart-container" style="position: relative; height:40vh; width:80vw">
					<canvas id="statistics"></canvas>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="section">
					<h3 class="section-title"><?php _e('Comments') ?></h3>
					<?php
					$index = 0;
					$conn = open_connection();
					$sql = "SELECT * FROM comments ORDER BY id DESC LIMIT 3";
					$st = $conn->prepare($sql);
					$st->execute();
					$row = $st->fetchAll();
					//
					if(count($row)){
						?>
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>#</th>
										<th>Sender</th>
										<th>Date</th>
										<th>Comment</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ( $row as $item ) {
										$index++;
										?>
										<tr>
											<th scope="row"><?php echo $index ?></th>
											<td>
												<?php echo $item['sender_username'] ?>
											</td>
											<td>
												<?php echo $item['created_date'] ?>
											</td>
											<td class="td-ellipsis">
												<?php echo $item['comment'] ?>
											</td>
										</tr>
										<?php
									}
									?>
											
								</tbody>
							</table>
						</div>
						<div class="text-center">
							<a href="dashboard.php?viewpage=plugin&name=comments-manager"><?php _e('Manage Comments') ?></a>
						</div>
						<?php
					} else {
						?>
						<div>
							<?php _e('No comment') ?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="section">
					<h3 class="section-title"><?php _e('Game Reports') ?></h3>
					<?php
					if(is_plugin_exist('game-reports')){
						$reports = get_option('game-reports');
						if($reports){
							$reports = json_decode($reports, true);
						} else {
							$reports = [];
						}
						if(count($reports)){
							?>
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>#</th>
											<th>Game</th>
											<th>Type</th>
											<th>Comment</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$index = 0;
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
											<tr>
												<th scope="row"><?php echo $index ?></th>
												<td class="td-ellipsis">
													<a href="<?php echo get_permalink('game', $game->slug) ?>" target="_blank"><?php echo $game->title ?></a>
												</td>
												<td>
													<span class="<?php echo $color ?> text-dark"> <?php echo $item['type'] ?> </span>
												</td>
												<td class="td-ellipsis">
													<?php echo $item['comment'] ?>
												</td>
											</tr>
											<?php
											if($index >= 3){
												break;
											}
										}
										?>	
									</tbody>
								</table>
							</div>
							<div class="text-center">
								<a href="dashboard.php?viewpage=plugin&name=game-reports"><?php _e('Manage Reports') ?></a>
							</div>
							<?php
						} else {
							?>
							<div>
								<?php _e('No report') ?>
							</div>
							<?php
						}
					} else {
						?>
						<div>
							<?php _e('Game Reports plugin not installed') ?>
						</div>
						<?php
					}
					?>	
				</div>
			</div>
		</div>
		<div class="section">
			<h3 class="section-title"><?php echo _t('Top games') ?></h3>
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th><?php _e('Game Name') ?></th>
							<th><?php _e('Played') ?></th>
							<th><?php _e('Category') ?></th>
							<th><?php _e('Likes') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$index = 0;
						$data = get_game_list('popular', 10);
						$games = $data['results'];
						foreach ( $games as $game ) {
							$index++;
							?>
						<tr>
							<th scope="row"><?php echo esc_int($index); ?></th>
							<td>
								<a href="<?php echo get_permalink('game', $game->slug) ?>" target="_blank"><?php echo esc_string($game->title); ?></a>
							</td>
							<td>
								<?php echo esc_int($game->views); ?>
							</td>
							<td>
								<?php echo '<span class="categories">'.esc_string($game->category).'</span>'; ?>
							</td>
							<td>
								<?php
									$vote_percentage = '- ';
									if($game->upvote+$game->downvote > 0){
										$vote_percentage = floor(($game->upvote/($game->upvote+$game->downvote))*100);
									}
									echo '<div class="row">';
									echo '<div class="col-4">'.$vote_percentage.' %</div>';
									echo '<div class="col-4"><i class="fa fa-thumbs-up" aria-hidden="true"></i>'.esc_int($game->upvote).'</div><div class="col-4"><i class="fa fa-thumbs-down" aria-hidden="true"></i>'.esc_int($game->downvote).'</div>';
									echo '</div>';
								?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-lg-3">
		<?php if(!ADMIN_DEMO) echo('<div class="section"><div class="official-info"></div></div>') ?>
		<div class="section">
			<div class="quote-box">
				<div id="quote"></div>
			</div>
		</div>
	</div>
</div>