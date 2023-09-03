<div class="section">
	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#pagelist"><?php _e('Pages') ?></a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#addpage"><?php _e('Add page') ?></a>
		</li>
	</ul>
	<!-- Tab panes -->
	<div class="tab-content">
		<div class="tab-pane tab-container active" id="pagelist">
			<table class="table">
				<thead>
				<tr>
					<th>#</th>
					<th><?php _e('ID') ?></th>
					<th><?php _e('Title') ?></th>
					<th><?php _e('Created') ?></th>
					<th><?php _e('Slug') ?></th>
					<th><?php _e('URL') ?></th>
					<th><?php _e('Action') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$results = array();
				$data = Page::getList();
				$results['pages'] = $data['results'];
				$results['totalRows'] = $data['totalRows'];
				$index = 0;
				foreach ( $results['pages'] as $page ) {
				$index++;
				?>
				<tr>
					<th scope="row"><?php echo esc_int($index); ?></th>
					<td>
						<?php echo esc_string($page->id)?>
					</td>
					<td>
						<?php echo esc_string($page->title)?>
					</td>
					<td>
						<?php echo date('j M Y', $page->createdDate) ?>
					</td>
					<td>
						<?php echo esc_string($page->slug)?>
					</td>
					<td><a href="<?php echo get_permalink('page', $page->slug) ?>" target="_blank"><?php _e('Visit') ?></a></td>
					<td><span class="actions"><a class="editpage" href="#" id="<?php echo esc_int($page->id)?>"><i class="fa fa-pencil-alt circle" aria-hidden="true"></i></a><a class="deletepage" href="#" id="<?php echo esc_int($page->id)?>"><i class="fa fa-trash circle" aria-hidden="true"></i></a></span></td>
				</tr>
				<?php }
				?>
			</tbody>
			</table>
			<p><?php _e('%a pages in total.', esc_int($results['totalRows'])) ?></p>
		</div>
		<div class="tab-pane tab-container fade" id="addpage">
			<?php
			$results = array();
			$results['page'] = new Page;
			?>
			<form id="form-newpage" method="post">
				<input type="hidden" name="pageId" value="<?php echo esc_int($results['page']->id) ?>"/>
				<div class="form-group">
					<label for="title"><?php _e('Page Title') ?>:</label>
					<input type="text" class="form-control" id="newpagetitle" name="title" placeholder="Name of the page" required autofocus maxlength="255" value="<?php echo htmlspecialchars( $results['page']->title )?>"/>
				</div>
				<div class="form-group">
					<label for="slug"><?php _e('Page Slug') ?>:</label>
					<input type="text" class="form-control" id="newpageslug" name="slug" placeholder="Page url ex: this-is-sample-page" required autofocus maxlength="255" value="<?php echo htmlspecialchars( $results['page']->slug )?>"/>
				</div>
				<div class="form-group">
					<label for="content"><?php _e('Content') ?>:</label>
					<textarea class="form-control" name="content" rows="12" placeholder="The HTML content of the page" required maxlength="100000"><?php echo htmlspecialchars( $results['page']->content )?></textarea>
				</div>
				<div class="form-group">
					<label for="title"><?php _e('Created Date') ?>:</label>
					<input type="date" class="form-control" name="createdDate" placeholder="YYYY-MM-DD" required maxlength="10" value="<?php echo date( "Y-m-d" ) ?>" />
				</div>
				<input type="submit" class="btn btn-primary"  name="saveChanges" value="<?php _e('Publish') ?>" />
			</form>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-page" tabindex="-1" role="dialog" aria-labelledby="edit-page-modal-label" aria-hidden="true">
	<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
		<h5 class="modal-title" id="edit-page-label"><?php _e('Edit page') ?></h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		</div>
		<div class="modal-body">
		<form id="form-editpage">
			<input type="hidden" id="edit-id" name="id" value=""/>
			<input type="hidden" id="edit-createdDate" name="createdDate" value=""/>
			<div class="form-group">
				<label for="title"><?php _e('Page Title') ?>:</label>
				<input type="text" class="form-control" id="edit-title" name="title" placeholder="Name of the page" required minlength="3" maxlength="255" value=""/>
			</div>
			<div class="form-group">
				<label for="slug"><?php _e('Page Slug') ?>:</label>
				<input type="text" class="form-control" id="edit-slug" name="slug" placeholder="Page url ex: this-is-sample-page" required minlength="3" maxlength="255" value=""/>
			</div>
			<div class="form-group">
				<label for="content"><?php _e('Content') ?>:</label>
				<textarea class="form-control" name="content" id="edit-content" rows="12" placeholder="The HTML content of the page" required minlength="3" maxlength="100000"></textarea>
			</div>
			<input type="submit" class="btn btn-primary" value="<?php _e('Save changes') ?>" />
			<input type="button" class="btn btn-secondary" data-dismiss="modal" value="<?php _e('Close') ?>" />
		</form>
		</div>
	</div>
	</div>
</div>