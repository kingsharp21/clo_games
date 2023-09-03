<?php

class Widget_HTML extends Widget {
	function __construct() {
 		$this->name = 'HTML';
 		$this->id_base = 'html';
 		$this->description = 'Show HTML / TEXT';
	}
	public function widget( $instance, $args = array() ){
		echo $instance['text'];
	}

	public function form( $instance = array() ){

		if(!isset( $instance['text'] )){
			$instance['text'] = '';
		}
		?>
		<div class="form-group">
			<label>HTML / TEXT:</label>
			<textarea class="form-control" rows="5" name="text"><?php echo $instance['text'] ?></textarea>
		</div>
		<?php
	}
}

register_widget( 'Widget_HTML' );

class Widget_Paragraph extends Widget {
	function __construct() {
 		$this->name = 'Paragraph';
 		$this->id_base = 'paragraph';
 		$this->description = 'Show text paragraph (HTML not allowed)';
	}
	public function widget( $instance, $args = array() ){
		if(!isset( $instance['text'] )){
			$instance['text'] = '';
		}
		echo '<p>';
		echo htmlentities(nl2br($instance['text']));
		echo '</p>';
	}

	public function form( $instance = array() ){

		if(!isset( $instance['text'] )){
			$instance['text'] = '';
		}
		?>
		<div class="form-group">
			<label>TEXT:</label>
			<textarea class="form-control" rows="5" name="text"><?php echo $instance['text'] ?></textarea>
		</div>
		<?php
	}
}

register_widget( 'Widget_Paragraph' );

class Widget_Heading extends Widget {
	function __construct() {
 		$this->name = 'Heading';
 		$this->id_base = 'heading';
 		$this->description = 'Heading typography, can be used as widget title or label.';
	}
	public function widget( $instance, $args = array() ){
		if(!isset( $instance['tag'] )){
			$instance['tag'] = 'h3';
		}
		if(!isset( $instance['class'] )){
			$instance['class'] = '';
		}
		if(!isset( $instance['text'] )){
			$instance['text'] = '';
		}
		echo '<'.$instance['tag'].' class="'.$instance['class'].'">';
		echo htmlentities($instance['text']);
		echo '</'.$instance['tag'].'>';
	}

	public function form( $instance = array() ){

		if(!isset( $instance['tag'] )){
			$instance['tag'] = 'h3';
		}
		if(!isset( $instance['class'] )){
			$instance['class'] = '';
		}
		if(!isset( $instance['text'] )){
			$instance['text'] = '';
		}
		?>
		<div class="form-group">
			<label><?php _e('Heading tag') ?>:</label>
			<select class="form-control" name="tag">
				<?php

				$opts = array(
					'h1' => 'h1',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'div' => 'div',
				);

				foreach ($opts as $key => $value) {
					$selected = '';
					if($key == $instance['tag']){
						$selected = 'selected';
					}
					echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group">
			<label>TEXT:</label>
			<textarea class="form-control" rows="5" name="text"><?php echo $instance['text'] ?></textarea>
		</div>
		<div class="form-group">
			<label><?php _e('Div class (Optional)') ?>:</label>
			<input type="text" class="form-control" name="class" placeholder="widget" value="<?php echo $instance['class'] ?>">
		</div>
		<?php
	}
}

register_widget( 'Widget_Heading' );

?>