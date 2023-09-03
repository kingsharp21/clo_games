<?php include  TEMPLATE_PATH . "/includes/header.php" ?>
<div class="container">
	<div class="post-container">
		<div class="content-wrapper">
			<h3 class="page-title"><?php _e('LATEST POSTS') ?></h3>
			<section class="blog-list">
			<?php
				$cur_page = 1;
				if(isset($url_params[1])){
					$_GET['page'] = $url_params[1];
					if(!is_numeric($_GET['page'])){
						$_GET['page'] = 1;
					}
				}
				if(isset($_GET['page'])){
					$cur_page = htmlspecialchars($_GET['page']);
					if(!is_numeric($cur_page)){
						$cur_page = 1;
					}
				}
				$data = Post::getList(6, 'created_date', 6*($cur_page-1));
				$total_posts = $data['totalRows'];
				$total_page = $data['totalPages'];
				$posts = $data['results'];
				foreach($posts as $post){
					?>
						<div class="post-item">
							<div class="post-media">
								<div class="post-thumb">
									<img src="<?php echo ($post->thumbnail_url) ? $post->thumbnail_url : DOMAIN . 'images/post-no-thumb.png'  ?>">
								</div>
								<div class="post-body">
									<h3 class="post-title">
										<a href="<?php echo get_permalink('post', $post->slug) ?>"><?php echo $post->title ?></a>
									</h3>
									<div class="post-meta">
										<span class="date">Published on <?php echo gmdate("j M Y", $post->created_date) ?></span>
									</div>
									<div class="post-intro">
										<?php echo mb_strimwidth(strip_tags($post->content), 0, 250, "...") ?>
									</div>
									<a class="more-link" href="<?php echo get_permalink('post', $post->slug) ?>">Read more →</a>
								</div>
							</div>
						</div>
					<?php
				}
			?>
			</section>
			<div class="pagination-wrapper">
				<nav aria-label="Page navigation example">
					<ul class="pagination justify-content-center">
						<?php
						$cur_page = 1;
						if(isset($_GET['page'])){
							$cur_page = esc_string($_GET['page']);
						}
						if($total_page){
							$max = 8;
							$start = 0;
							$end = $max;
							if($max > $total_page){
								$end = $total_page;
							} else {
								$start = $cur_page-$max/2;
								$end = $cur_page+$max/2;
								if($start < 0){
									$start = 0;
								}
								if($end - $start < $max-1){
									$end = $max;
								}
								if($end > $total_page){
									$end = $total_page;
								}
							}
							if($start > 0){
								echo '<li class="page-item"><a class="page-link" href="'. get_permalink('post', '', array('page' => 1)) .'">1</a></li>';
								echo('<li class="page-item disabled"><span class="page-link">...</span></li>');
							}
							for($i = $start; $i<$end; $i++){
								$disabled = '';
								if($cur_page){
									if($cur_page == ($i+1)){
										$disabled = 'disabled';
									}
								}
								echo '<li class="page-item '.$disabled.'"><a class="page-link" href="'. get_permalink('post', '', array('page' => $i+1)) .'">'.($i+1).'</a></li>';
							}
							if($end < $total_page){
								echo('<li class="page-item disabled"><span class="page-link">...</span></li>');
								echo '<li class="page-item"><a class="page-link" href="'. get_permalink('post', '', array('page' => $total_page)) .'">'.$total_page.'</a></li>';
							}
						}
						?>
					</ul>
				</nav>
			</div>
		</div>
	</div>
</div>
<?php include  TEMPLATE_PATH . "/includes/footer.php" ?>