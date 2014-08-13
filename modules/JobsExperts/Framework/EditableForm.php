<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Framework_EditableForm {
	public $model;
	public $mapped = array();
	public $container_classname;
	public $display_text;
	public $empty_text;
	public $html_options = array();
	private $id;

	public function __construct( $model, $display_text, $empty_text, $mapped = array(), $container_classname = '' ) {
		$this->model               = $model;
		$this->mapped              = $mapped;
		$this->container_classname = $container_classname;
		$this->display_text        = $display_text;
		$this->empty_text          = $empty_text;
		$this->id                  = uniqid();
	}

	public function render() {
		ob_start();
		//determine display empty text or display text
		$display_text = $this->display_text;
		$is_display   = false;
		foreach ( $this->mapped as $key => $val ) {
			if ( $this->model->$key ) {
				$is_display   = true;
				$display_text = str_replace( '{{' . $key . '}}', $this->model->$key, $display_text );
			}
		}
		?>
		<div id="<?php echo $this->id ?>">
			<div class="editable_content">
				<?php
				if ( $is_display ) {
					echo $display_text;
				} else {
					echo $this->empty_text;
				}
				?>
			</div>
			<?php
			//we display the model property for binding
			foreach ( $this->mapped as $key => $val ) {
				echo JobsExperts_Framework_Form::hiddenField( $this->_buildFormElementName( $this->model, $key ), esc_attr($this->model->$key) );
			}
			?>
		</div>
		<?php
		//output js script
		echo $this->_build_script();

		return ob_get_clean();
	}

	private function _build_dialog_form() {
		ob_start();
		?>
		<div style="margin-top: 0">
			<form method="post" class="form_<?php echo $this->id ?>">
				<?php foreach ( $this->mapped as $key => $val ): ?>
					<div class="form-group" style="margin-bottom: 5px">
						<label style="font-size: 12px;font-weight: normal;display: block"><?php echo $val['label'] ?></label>
						<?php
						$class = isset( $val['class'] ) ? $val['class'] : "";
						switch ( $val['type'] ) {
							case 'countryDropdown':
								echo JobsExperts_Framework_Form::countryDropdown( $val['id'], array( $this->model->$key ), array(
									'id'    => $val['id'], 'data-element' => $this->_buildFormElementName( $this->model, $key ),
									'class' => $class,
									'style' => 'width:200px'
								) );
								break;
							case 'textArea':
								echo JobsExperts_Framework_Form::textArea( $val['id'], $this->model->$key, array(
									'rows'  => 5, 'cols' => 40,
									'class' => $class,
									'data-element' => $this->_buildFormElementName( $this->model, $key ),
									'id'    => $val['id']
								) );
								break;
							default:
								echo JobsExperts_Framework_Form::$val['type']( $val['id'], $this->model->$key, array(
									'id'    => $val['id'], 'data-element' => $this->_buildFormElementName( $this->model, $key ),
									'class' => $class,
									'style' => 'padding:2px 0 5px 2px'
								) );
								break;
						}
						?>
					</div>
				<?php endforeach; ?>
				<div class="form-group" style="margin-bottom: 0">
					<button type="submit" class="btn-post btn btn-primary btn-xs"><?php _e( 'Save', JBP_TEXT_DOMAIN ) ?></button>
					&nbsp;
					<button type="button" class="btn btn-default popover-close btn-xs"><?php _e( 'Cancel', JBP_TEXT_DOMAIN ) ?></button>
				</div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	private function _build_script() {
		$form_id = 'form_' . $this->id;
		//$output     = '';
		ob_start();
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				var data = [];//use for store the editable data
				//parent
				var container = $('#<?php echo $this->id ?>');
				//enable validate
				var popover_element = container.find('.editable_content');
				//load popover
				popover_element.popover({
					content  : '<?php echo preg_replace('/^\s+|\n|\r|\s+$/m', '', $this->_build_dialog_form()); ?>',
					html     : true,
					trigger  : 'click',
					container: false,
					placement: 'top'
				}).on('shown.bs.popover', function () {
					//init the form
					var form = $('.<?php echo $form_id ?>');
					form.validationEngine('attach', {
						binded: false,
						scroll: false
					});
					form.on('submit', function () {
						//we will need to update the display_text or fallback to empty_text
						//first check does this form have enought data
						var form = $(this);
						if (form.validationEngine('validate')) {
							var display_text = '<?php echo addslashes($this->display_text) ?>';
							var empty_text = '<?php echo addslashes($this->empty_text )?>';
							var is_null = true;
							$.each($(this).serializeArray(), function (i, v) {
								//get current id
								var current = v.name;
								//get element name
								var element_name = form.find('#' + current).data('element');
								console.log(element_name);
								//now bind the data to element
								$('input[name="' + element_name + '"]').val(v.value);
								//next we need to update the element text
								display_text = display_text.replace('{{' + v.name + '}}', v.value);
								if ($.trim(v.value.length) > 0) {
									is_null = false;
								}
							});
							if (is_null == false) {
								container.find('.editable_content').html(display_text);
							} else {
								container.find('.editable_content').html(empty_text);
							}
							data = $(this).serializeArray();
							hide_popover();
						}
						return false;
					});
					$.each(data, function (i, v) {
						form.find(':input[name="' + v.name + '"]').val(v.value);
					})
				});

				function hide_popover() {
					popover_element.popover('hide');
				}

				$('html').on('mouseup', function (e) {
					if (!$(e.target).closest('.popover').length) {
						$('.popover').each(function () {
							$(this.previousSibling).popover('hide');
						});
					}
					//check does this target is cancel
					if ($(e.target).hasClass('popover-dismiss')) {
						$(this.previousSibling).popover('hide');
					}
				});
				$('body').on('click', '.popover-close', function () {
					popover_element.popover('hide');
				})
			})

		</script>
		<?php
		$script = ob_get_clean();

		return $script;

	}

	/**
	 * @param $model
	 * @param $attribute
	 *
	 * @return string
	 */
	private function _buildFormElementName( $model, $attribute ) {
		$model_class_name = get_class( $model );
		$frm_element_name = $model_class_name . "[$attribute]";

		return $frm_element_name;
	}
}