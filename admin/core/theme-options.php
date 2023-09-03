<div class="row">
	<div class="col-lg-8">
		<div class="section">
			<ul class="nav nav-tabs">
				<?php
				foreach($tab_list as $tab => $label){
					$active = '';
					if($tab == $slug){
						$active = 'active';
					}
					?>
					<li class="nav-item">
						<a class="nav-link <?php echo $active ?>" href="dashboard.php?viewpage=layout&slug=<?php echo $tab ?>"><?php _e($label) ?></a>
					</li>
					<?php
				}
				?>
			</ul>
			<div class="mb-4"></div>
			<?php
			if(file_exists( ABSPATH . TEMPLATE_PATH . '/options.php' )){
				require_once( ABSPATH . TEMPLATE_PATH . '/options.php' );
			}
			?>
		</div>
	</div>
</div>