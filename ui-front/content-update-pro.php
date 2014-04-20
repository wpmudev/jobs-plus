<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post, $Jobs_Plus_Core, $CustomPress_Core;

//Turn off comments
no_comments();

$data   = '';
$selected_cats  = array();
$error = get_query_var('jbp_pro_error');

//Are we adding a Listing?
if ($post->ID == $this->add_pro_page_id) {
	$post = $this->get_default_custom_post('jbp_pro');
	$editing = false;
} //Or are we editing a listing?
elseif(get_query_var('edit')){
	$editing = true;
}
$data = (array)$post;
$post_ID = $data['ID'];

//if ( isset( $_POST['data'] ) ) $data = $_POST['data'];

//code for wp_editor
require_once(ABSPATH . 'wp-admin/includes/template.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/post.php');

$editor_settings =   array(
'wpautop' => true, // use wpautop?
'media_buttons' => true, // show insert/upload button(s)
'textarea_name' => 'data[post_content]', // set the textarea name to something different, square brackets [] can be used here
'textarea_rows' => 5, //get_option('default_post_edit_rows', 10), // rows="..."
'tabindex' => '',
'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
'editor_class' => 'required', // add extra class(es) to the editor textarea
'teeny' => false, // output the minimal editor config used in Press This
'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
);
?>
<!-- script type="text/javascript" src="<?php echo $this->plugin_url . 'ui-front/js/media-post.js'; ?>" ></script -->

<div id="post-jbp-pro">
	<?php if(dynamic_sidebar('pro-widget') ) : else: endif; ?>
	<?php echo do_action('jbp_error'); ?>
	<?php echo do_action('jbp_notice'); ?>

	<h3><?php _e('Create a New Pro Profile', JBP_TEXT_DOMAIN); ?></h3>

	<form action="#" method="post" enctype="multipart/form-data">
		<!--
		<input type="hidden" name="data[post_status]" value="<?php echo ($data['post_status'] == 'auto-draft') ? $this->get_setting('pro->moderation') : $data['post_status']; ?>" />
		-->
		<input type="hidden" name="data[ID]" value="<?php echo $data['ID']; ?>" />
		<input type="hidden" name="post_id" value="<?php echo $data['ID']; ?>" />

		<?php if(post_type_supports('jbp_pro','title') ): ?>
		<div class="editfield">
			<label for="title"><?php _e( 'Name:', JBP_TEXT_DOMAIN ); ?></label>
			<br /><input class="required" type="text" id="title" name="data[post_title]" value="<?php echo ( isset( $data['post_title'] ) ) ? esc_attr($data['post_title']) : ''; ?>" />
		</div>
		<?php endif; ?>

		<div class="editfield">
			<label><?php _e( 'Company URL:', JBP_TEXT_DOMAIN ); ?></label>
			<br /><?php echo do_shortcode('[ct_in id="_ct_jbp_pro_Company_URL"]'); ?>
		</div>

		<div class="editfield">
			<label><?php _e( 'Location:', JBP_TEXT_DOMAIN ); ?></label>
			<br /><?php echo do_shortcode('[ct_in id="_ct_jbp_pro_Location"]'); ?>
			<label class="pro-content-editable pro-location">
				<strong><?php _e('Location: ', JBP_TEXT_DOMAIN); ?></strong>
				<span class="editable"
					data-type="select"
					data-name="_ct_jbp_pro_Location"
					data-emptytext="<?php _e('Your Location', JBP_TEXT_DOMAIN); ?>"
					data-value="<?php esc_attr_e(do_shortcode('[ct id="_ct_jbp_pro_Location"]') ); ?>"
					data-source="<?php echo JBP_PLUGIN_URL . 'data/countries.json';?>"
					data-original-title="<?php _e('Enter Location from dropdown', JBP_TEXT_DOMAIN); ?>">
				</span>
			</label>
		</div>

		<div class="editfield">
			<label><?php _e( 'Contact Email:', JBP_TEXT_DOMAIN ); ?></label>
			<br /><?php echo do_shortcode('[ct_in id="_ct_jbp_pro_Contact_Email"]'); ?>
		</div>

		<?php if(post_type_supports('jbp_pro','editor') ): ?>
		<div class="editfield">
			<label><?php _e( 'Bio:', JBP_TEXT_DOMAIN ); ?></label><br />
			<?php wp_editor( $data['post_content'], 'prodescription', $editor_settings); ?>
		</div>
		<?php endif; ?>

		<?php if(post_type_supports('jbp_pro','excerpt') ): ?>
		<div class="editfield">
			<label for="excerpt"><?php _e( 'Excerpt:', JBP_TEXT_DOMAIN ); ?></label>
			<textarea id="excerpt" name="data[post_excerpt]" rows="2" ><?php echo (isset( $data['post_excerpt'] ) ) ? esc_textarea($data['post_excerpt']) : ''; ?></textarea>
			<p class="description"><?php _e( 'A short excerpt of your Bio.', JBP_TEXT_DOMAIN ); ?></p>
		</div>
		<?php endif; ?>

		<?php
		//get related hierarchical taxonomies
		$taxonomies = get_object_taxonomies('jbp_pro', 'objects');
		$taxonomies = empty($taxonomies) ? array() : $taxonomies;

		//Loop through the taxonomies that apply
		foreach($taxonomies as $taxonomy):
		if( ! $taxonomy->hierarchical) continue;
		$tax_name = $taxonomy->name;
		$labels = $taxonomy->labels;
		//Get this Taxonomies terms
		$selected_cats = array_values( wp_get_post_terms($data['ID'], $tax_name, array('fields' => 'ids') ) );
		?>
		<div class="editfield">
			<label><?php echo $labels->all_items; ?></label><br />

			<?php
			$name = ( $tax_name == 'category' ) ? 'post_category' : 'tax_input[' . $tax_name . ']';
			echo "<input type='hidden' name='{$name}[]' value='0' />"; 		// Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.

			$args = array(
			'show_option_all'    => '',
			'show_option_none'   => '',
			'orderby'            => 'ID',
			'order'              => 'ASC',
			'show_count'         => 0,
			'hide_empty'         => 0,
			'child_of'           => 0,
			'exclude'            => '',
			'echo'               => 1,
			'selected'           => $selected_cats[0],
			'hierarchical'       => 1,
			'name'               => "{$tax_name}[]",
			'id'                 => $tax_name,
			'class'              => 'postform',
			'depth'              => 0,
			'tab_index'          => 0,
			'taxonomy'           => $tax_name,
			'hide_if_empty'      => false
			);
			wp_dropdown_categories($args);
			?>
		</div>
		<?php endforeach; ?>


		<?php
		//get related non-hierarchical taxonomies

		//Loop through the taxonomies that apply
		foreach($taxonomies as $tag):
		if( $tag->hierarchical) continue;

		$tag_name = $tag->name;
		$labels = $tag->labels;

		//Get this Taxonomies terms
		$tag_list = strip_tags(get_the_term_list( $data['ID'], $tag_name, '', ',', '' ));

		?>

		<div class="editfield">
			<div id="<?php echo $tag_name; ?>-checklist" class="tagchecklist">
				<label><?php echo $labels->name; ?>
					<input id="tag_<?php echo $tag_name; ?>" name="tag_input[<?php echo $tag_name; ?>]" type="text" value="<?php echo $tag_list?>" />
				</label>
			</div>
		</div>

		<script type="text/javascript" > jQuery('#tag_<?php echo $tag_name; ?>').tagsInput({width:'auto',height: '30px', defaultText: '<?php _e("add a tag", JBP_TEXT_DOMAIN); ?>'}); </script>
		<?php endforeach; ?>

		<div class="editfield clearfix">
			<label><?php _e( 'Skills:', JBP_TEXT_DOMAIN ); ?></label>
			<div class="pro-content-wrapper pro-skills show-on-edit">
				<div class="pro-skills-edit"></div>
				<?php echo do_shortcode('[jbp-pro-skills]'); ?>
				<script id="add-skill" type="text/template">
					<li>
					<div
					class="editable skill"
					data-type="skill"
					data-mode="popup"
					data-name="_ct_jbp_pro_Skills[]"
					data-value=""
					data-emptytext="<?php esc_attr_e('What is your skill?', JBP_TEXT_DOMAIN); ?>"
					data-original-title="<?php esc_attr_e(__('Skill', JBP_TEXT_DOMAIN) ); ?>">
					</div>
					</li>
				</script>
				<div class="pro-content-command">
					<a href="#" id="add-skill-link" class="pro-add">+Add</a>
				</div>
			</div>
		</div>

		<span class="pro-edit"><button type="button" id="toggle-edit" class="pro-button pro-edit-button"><?php esc_html_e('Edit', JBP_TEXT_DOMAIN); ?></button></span>

		<div class="editfield group">
			<label><?php _e( 'Portfolio examples:', JBP_TEXT_DOMAIN ); ?></label>
			<div class="pro-content-wrapper pro-portfolio group">
				<?php echo do_shortcode('[jbp-pro-portfolio]'); ?>
				<div class="pro-content-command">
					<a href="#" id="add-portfolio-link" class="pro-add"><?php _e('+Add', JBP_TEXT_DOAMIN); ?></a>
				</div>
				<script id="add-portfolio" type="text/template">
					<li class="new-portfolio">
					<div
					class="editable portfolio"
					data-type="portfolio"
					data-name="_ct_jbp_pro_Portfolio[]"
					data-value=""
					data-emptytext="<?php esc_attr_e('No Image Selected', JBP_TEXT_DOMAIN); ?>"
					data-image-label="Chose Image File"
					data-original-title="<?php esc_attr_e(__('Select an Image', JBP_TEXT_DOMAIN) ); ?>"
					data-button-label="<?php _e('Choose an image', JBP_TEXT_DOMAIN); ?>"
					>
					</div>
					</li>
				</script>
			</div>
		</div>

		<?php
		//Any custom fields not already handled
		echo do_shortcode(
		'[custom_fields_input style="editfield"]
		[ct_filter not="true"]
		_ct_jbp_pro_Contact_Email,
		_ct_jbp_pro_Company_URL,
		_ct_jbp_pro_Location,
		_ct_jbp_pro_Facebook_URL,
		_ct_jbp_pro_Twitter_URL,
		_ct_jbp_pro_LinkedIn_URL,
		_ct_jbp_pro_Skype_URL,
		_ct_jbp_pro_Skills,
		_ct_jbp_pro_Portfolio
		[/ct_filter]
		[/custom_fields_input]');
		?>

		<?php
		$tos_txt = $this->get_setting('pro->tos_txt', '');
		if(! empty( $tos_txt ) ):
		?>
		<strong><?php _e( 'Terms of Service', JBP_TEXT_DOMAIN ); ?></strong>
		<div class="pro-tos"><?php echo nl2br( $tos_txt ); ?></div>
		<?php endif; ?>

		<div class="submit">
			<?php wp_nonce_field( 'verify' ); ?>
			<input type="hidden" name="jbp-pro-update" />
			<?php if($Jobs_Plus_Core->get_setting('pro->moderation->publish') ): ?>
			<input type="submit" value="<?php _e( 'Publish', JBP_TEXT_DOMAIN ); ?>" name="data[post_status]" value="publish"/>
			<?php endif; ?>
			<?php if($Jobs_Plus_Core->get_setting('pro->moderation->pending') ): ?>
			<input type="submit" value="<?php _e( 'Pending Review', JBP_TEXT_DOMAIN ); ?>" name="data[post_status]" value="pending" />
			<?php endif; ?>
			<?php if($Jobs_Plus_Core->get_setting('pro->moderation->draft') ): ?>
			<input type="submit" value="<?php _e( 'Draft', JBP_TEXT_DOMAIN ); ?>" name="data[post_status]" value="draft" />
			<?php endif; ?>

			<input type="button" value="<?php _e( 'Cancel', JBP_TEXT_DOMAIN ); ?>" onclick="location.href='<?php esc_attr_e(get_permalink($post->ID) ); ?>'">
		</div>

	</form>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($) {

		//Portfolio popup
		var new_pro = <?php echo $new_pro ? 'true':'false'; ?>;
		var popupEnabled = true;//new_pro;

		$.fn.editable.defaults.pk = '<?php the_ID(); ?>';
		$.fn.editable.defaults.url = '<?php echo admin_url('/admin-ajax.php'); ?>';
		$.fn.editable.defaults.params = {'action': 'jbp_pro', '_wpnonce': '<?php echo wp_create_nonce('jbp_pro');?>'};
		$.fn.editable.defaults.disabled = false;

		var $editables = $('.editable'); //Get a list of editable fields
		$editables.editable();

		// Add Portfolio
		$('#add-portfolio-link').on('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			var $template = $($('#add-portfolio').html()),
			$portfolio = $template.find('.editable'),
			count = $('.pro-content-wrapper .pro-portfolio ul li').size();
			$portfolio.attr('data-name', '_ct_jbp_pro_Portfolio['+count+']');
			$('.pro-content-wrapper .pro-portfolio ul').append($template);
			$portfolio.editable().editable('show');
		});

		// Add Skills
		$('#add-skill-link').on('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			var $template = $($('#add-skill').html()),
			$portfolio = $template.find('.editable'),
			count = $('.pro-content-wrapper .pro-skills ul li').size();
			$portfolio.attr('data-name', '_ct_jbp_pro_Skills['+count+']');
			$('.pro-content-wrapper .pro-skills ul').append($template);
			$portfolio.editable().editable('show');
		});

		<?php if(current_user_can( EDIT_PROS ) ): ?>

		var $toggleDisabled = $('#toggle-edit');
		var can_edit = <?php echo current_user_can( EDIT_PROS ) ? 'true' : 'false'; ?>;
		popupEnabled = false;
		//Toggle whether edit or popup
		$toggleDisabled.click( function(){
			popupEnabled = ! popupEnabled;
			$('.editable').editable('toggleDisabled');
		});

		<?php endif; ?>

	});
</script>


<script type="text/html">
	jQuery(document).ready( function($) {

		//Portfolio popup
		var new_pro = <?php echo $new_pro ? 'true':'false'; ?>;
		var popupEnabled = true;//new_pro;
		var $editables = $('.editable'); //Get a list of editable fields

		$editables.editable({dataType: 'json'});

		$.fn.editable.defaults.pk = '<?php the_ID(); ?>';
		$.fn.editable.defaults.url = '<?php echo admin_url('/admin-ajax.php'); ?>';
		$.fn.editable.defaults.params = {'action': 'jbp_pro', '_wpnonce': '<?php echo wp_create_nonce('jbp_pro');?>'};
		$.fn.editable.defaults.mode = 'popup';
		$.fn.editable.defaults.showbuttons = 'bottom';
		$.fn.editable.defaults.disabled = true;

		// Overwrite done and cancel buttons
		$.fn.editableform.buttons = '<button type="button" class="editable-cancel">Cancel</button>'+'<button type="submit" class="editable-submit">Done</button>';
		$.extend($.fn.editableform.Constructor.prototype, {
			initButtons: function() {
				var $btn = this.$form.find('.editable-buttons');
				$btn.append($.fn.editableform.buttons);
				if(this.options.showbuttons === 'bottom') {
					$btn.addClass('editable-buttons-bottom');
				}
			}
		});
		// Overwrite popup placement
		$.extend($.fn.editableContainer.Popup.prototype, {
			setPosition: function() {
				this.tip().position({
					of: this.$element.closest('.pro-content-editable'),
					my: 'left top',
					at: 'left top',
					collision: 'none'
				});
			},
		});


		var $editables = $('.editable'); //Get a list of editable fields

		$editables.editable({dataType: 'json'});

		<?php if(current_user_can( EDIT_JOBS ) ): ?>

		var $toggleDisabled = $('#toggle-edit');
		var can_edit = <?php echo current_user_can( EDIT_PROS ) ? 'true' : 'false'; ?>;
		popupEnabled = false;
		//Toggle whether edit or popup
		$toggleDisabled.click( function(){
			popupEnabled = ! popupEnabled;
			popup();
		});

		// Add Portfolio
		$('#add-portfolio-link').click(function(e){
			e.preventDefault();
			e.stopPropagation();
			var $template = $($('#add-portfolio').html()),
			$editable = $template.find('.editable'),
			count = $('.pro-content-wrapper .pro-portfolio ul li').size();
			$editable.attr('data-name', 'ct_Job_Portfolio_text['+count+']');
			$('.pro-content-wrapper .pro-portfolio ul').append($template);
			$editable.editable({
				'display': portfolio_display,
				'disabled': false
			});

			attach_editables_event($editable); // Attach editable events
			setTimeout(function() { $editable.editable('show'); }, 100);
		});

		$editables.on('hidden', function(e, reason){
			if(reason === 'save' || reason === 'nochange') {
				var $next = $editables.eq( ($editables.index(this) + 1) % $editables.length );

				if( new_pro && $(this).attr('data-name') == 'post_title') {
					window.location = '<?php echo $link ?>';
					$('#create-dialog').dialog({
						height: 140,
						modal: true
					});
				}
				else setTimeout(function() { $next.editable('show'); }, 300);
				}
			});

			function remove_handler_attach_display(e){
				e.preventDefault();
				e.stopPropagation();
				var $editable = $(this).closest('.editable');
				$editable.children().css('opacity', '0.2');
				remove_handler($editable);
			}

			function remove_handler_attach_inline(e){
				e.preventDefault();
				e.stopPropagation();
				var $editable = $(this).closest('.editable-container').prev('.editable');
				$(this).closest('.editable-container').css('opacity', '0.2');
				remove_handler($editable);
			}

			function remove_handler($editable){
				var editable = $editable.data('editable');
				editable.value.remove = 'remove';
				$editable.editable('submit', {
					url: editable.options.url,
					data: {
						name: editable.options.name,
						value: editable.value,
						pk: editable.options.pk
					},
					success: function(){
						$editable.closest('li').remove();
					}
				})
			}

			function portfolio_display(value){
				if ( !value ){
					$(this).empty();
					return;
				}
				if ( value.src ){
					var $img = $('<img>').attr( {src: value.src, title: value.caption} );
					var $del_icon = $('<a>').attr( {href: '#', class: 'pro-editable-remove'} );
					$(this).empty();
					$(this).append($img);
					$(this).append($del_icon);
					$del_icon.on('click', remove_handler_attach_display);
				}
			}

			function attach_editables_event( $ed ){
				$ed.on('hidden', function(e, reason){
					var $editables = $('.editable').filter(':visible'); // refresh editables list
					var type = $(this).attr('data-type');

					if(reason === 'save' || reason === 'nochange') {
						var index = $editables.index(this),
						$next = $editables.eq( index + 1 );

						if( new_pro && $(this).attr('data-name') == 'post_title') {
							window.location = '<?php echo $link ?>';
							$('#create-dialog').dialog({
								height: 140,
								modal: true
							});
						}
						else if ( $next && index > 0 ) setTimeout(function() { $next.editable('show'); }, 300);

							if ( type == 'portfolio' ){
								$(this).closest('.new-portfolio').removeClass('new-portfolio');
							}
						}
						if ( type == 'portfolio' ){
							var name = $(this).attr('data-name'),
							value = $(this).editable('getValue');
							if ( !value[name] || value[name] == "" ) // Remove this
							$(this).closest('li').remove();

						}
						$(this).closest('.pro-content-editable').removeClass('pro-content-editable-open');
					}).on('shown', function(e){
						$(this).closest('.pro-content-editable').addClass('pro-content-editable-open');
						var editable = $(this).data('editable');
						var type = $(this).attr('data-type');
						if ( editable.options.mode == 'popup' ){
							var w = editable.container.tip().outerWidth(),
							w2 = $(this).closest('.pro-content-editable').outerWidth();
							if ( w2 > w )
							editable.container.tip().width(w2);
						}
						if ( type == 'skill' ){
							var $del_icon = $('<a>').attr( {href: '#', class: 'pro-editable-remove'} );
							editable.container.tip().append($del_icon);
							$del_icon.on('click', remove_handler_attach_inline);
						}
					});
				}

				attach_editables_event($editables);

				function popup(){
					if (!can_edit)
					return;
					//var $editables = $('.editable');
					if(popupEnabled) {
						$('.pro-profile-wrapper').addClass('pro-profile-edit');
						$editables.filter('[data-type=portfolio]').editable('option', 'display', portfolio_display);
					} else {
						$('.pro-profile-wrapper').removeClass('pro-profile-edit');
						$editables.filter('[data-type=portfolio]').editable('option', 'display', null);
					}
					// Re-render editables html
					$editables.each(function(){
						var editable = $(this).data('editable');
						editable.render();
						$editables.editable( popupEnabled ? 'disable' : 'enable' );
						var $del_icon = $('<a>').attr( {href: '#', class: 'pro-editable-remove'} );
						$(this).append($del_icon);
						$del_icon.on('click', remove_handler_attach_display);
					});
				}

				popup();

				<?php endif; ?>

			});

		</script>