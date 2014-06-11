<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post, $post_ID;

$this->no_comments();

$data   = array();
$selected_cats  = array();
$error = get_query_var('jbp_job_error');

$add_job = false;
$editing = false;
//Are we adding a Listing?
setup_postdata($post);
if ($post->ID == $this->job_update_page_id) {
	$post = $this->get_default_custom_post('jbp_job');
	$add_job = true;
	$editing = false;
} //Or are we editing a listing?
elseif(get_query_var('edit')){
	$editing = true;
}

$data = (array)$post;
$post_ID = $data['ID'];
setup_postdata($post);

//get related hierarchical taxonomies
$taxonomies = get_object_taxonomies('jbp_job', 'objects');
$taxonomies = empty($taxonomies) ? array() : $taxonomies;

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

//Styles
wp_enqueue_style('jobs-plus-custom');
wp_enqueue_style('jqueryui-editable');
wp_enqueue_style('select2');
wp_enqueue_style('magnific-popup');

//Scripts
wp_enqueue_script('select2');
wp_enqueue_script('jqueryui-editable');
wp_enqueue_script('jqueryui-editable-ext');
wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_script('magnific-popup');

//for job post widget
if(empty($data['post_title']) && isset($_GET['job_title']) && !empty($_GET['job_title'])){
	$data['post_title']=$_GET['job_title'];
}


?>

<div class="job-profile-wrapper">

	<div class="group">
		<?php if(dynamic_sidebar('job-widget') ) : else: endif; ?>
	</div>

	<?php echo do_action('jbp_error'); ?>
	<?php echo do_action('jbp_notice'); ?>

	<?php if($add_job): ?>
	<h2><?php esc_html_e('Create a New Job', JBP_TEXT_DOMAIN); ?></h2>
	<?php else: ?>
	<h2><?php printf( '%s %s', __('Editing Job &raquo;', JBP_TEXT_DOMAIN), get_the_title()); ?></h2>
	<?php endif; ?>
	<form id="job-form" action="#" method="POST">

		<input type="hidden" name="jbp-job-update" value="1" />
		<input type="hidden" name="data[ID]" value="<?php echo $data['ID']; ?>" />

		<table class="job-form-table">
			<tbody>
				<?php
				//Loop through the taxonomies that apply
				foreach($taxonomies as $taxonomy):
				if( ! $taxonomy->hierarchical) continue;
				$tax_name = $taxonomy->name;
				$labels = $taxonomy->labels;
				//Get this Taxonomies terms
				$selected_cats = ( wp_get_post_terms($data['ID'], $tax_name, array('fields' => 'ids') ) ) ;
				?>
				<tr>
					<th>
						<label><?php esc_html_e('Chose a Category',JBP_TEXT_DOMAIN)?></label>
					</th>
					<td>
						<div class="job-field">
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
							'selected'           => 0,
							'hierarchical'       => 1,
							'name'               => "{$name}[]",
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
					</td>
				</tr>
				<?php endforeach; ?>

				<?php if(post_type_supports('jbp_job','title') ): ?>
				<tr>
					<th>
						<label><?php esc_html_e('Give Your Job a  Title',JBP_TEXT_DOMAIN)?></label>
					</th>
					<td>
						<div class="job-field">
							<div>
								<input class="required" type="text" id="title" name="data[post_title]" value="<?php echo ( isset( $data['post_title'] ) ) ? esc_attr($data['post_title']) : ''; ?>" />
							</div>
						</div>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<th>
						<label><?php esc_html_e('Describe the Work to be Done',JBP_TEXT_DOMAIN)?></label>
					</th>
					<td>
						<textarea name="data[post_content]" rows="4"><?php echo esc_textarea( strip_tags(get_the_content() ) ); ?></textarea>
					</td>
				</tr>

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
				<tr>
					<th>
						<label><?php echo ($tag_name == 'jobs_skills_tag') ? __('What skills are needed?', JBP_TEXT_DOMAIN) : $labels->name; ?></label>
					</th>
					<td>
						<input class="job-tags" style="100%" id="tag_<?php echo $tag_name; ?>" name="tag_input[<?php echo $tag_name; ?>]" type="hidden" value="<?php echo $tag_list?>" />
						<span class="job-description"><?php esc_html_e('Skills Required for this job', JBP_TEXT_DOMAIN); ?></span>

						<script type="text/javascript">
							jQuery(document).ready( function($){
								$('#tag_<?php echo $tag_name; ?>').select2({
									tags: <?php echo json_encode(get_terms($tag_name, array('fields'=>'names', 'get' => 'all' ) ) ); ?>,
									placeholder: "<?php esc_attr_e('Add a tag, use commas to separate'); ?>",
									tokenSeparators: [","]
								});
							});
						</script>

					</td>
				</tr>
				<?php endforeach; ?>

				<tr>
					<th>
						<?php if($this->get_setting('job->use_budget_range', false) ): ?>
						<label><?php esc_html_e('Budget Range',JBP_TEXT_DOMAIN)?></label>
						<?php else: ?>
						<label><?php esc_html_e('Budget',JBP_TEXT_DOMAIN)?></label>
						<?php endif; ?>
					</th>
					<td>
						<?php if($this->get_setting('job->use_budget_range', false) ): ?>
						<?php echo do_shortcode('[ct_in id="_ct_jbp_job_Min_Budget" class="number"]'); ?> &mdash;
						<?php endif; ?>

						<?php echo do_shortcode('[ct_in id="_ct_jbp_job_Budget" class="number"]'); ?>

						<?php if($this->get_setting('job->use_budget_range', false) ): ?>
						<br /><span class="job-description"><?php echo do_shortcode('[ct_in id="_ct_jbp_job_Min_Budget" property="description"]'); ?></span>
						<?php else: ?>
						<br /><span class="job-description"><?php echo do_shortcode('[ct_in id="_ct_jbp_job_Budget" property="description"]'); ?></span>
						<?php endif; ?>

					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Contact Email',JBP_TEXT_DOMAIN)?></label>
					</th>
					<td>
						<?php echo do_shortcode('[ct_in id="_ct_jbp_job_Contact_Email"]'); ?>
						<span class="job-description"><?php echo do_shortcode('[ct_in id="_ct_jbp_job_Contact_Email" property="description"]'); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php esc_html_e('Completion Date',JBP_TEXT_DOMAIN)?></label>
					</th>
					<td>
						<?php echo do_shortcode('[ct_in id="_ct_jbp_job_Due"]'); ?>
						<span class="job-description"><?php echo do_shortcode('[ct_in id="_ct_jbp_job_Due" property="description"]'); ?></span>
					</td>
				</tr>

				<tr>
					<th>
						<label><?php esc_html_e('Job Open for',JBP_TEXT_DOMAIN)?></label>
					</th>
					<td>
						<?php echo do_shortcode('[ct_in id="_ct_jbp_job_Open_for"]'); ?>
						<span class="job-description"><?php echo do_shortcode('[ct_in id="_ct_jbp_job_Open_for" property="description"]'); ?></span>
					</td>
				</tr>

				<tr>
					<th style="vertical-align: top">
						<label><?php esc_html_e('Attach Examples',JBP_TEXT_DOMAIN)?></label>
					</th>
					<td>
						<div class="job-content-wrapper job-portfolio">
							<h2><?php esc_html_e('Portfolio', JBP_TEXT_DOMAIN); ?></h2>
							<?php echo do_shortcode('[jbp-job-portfolio]'); ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<?php
		//Any custom fields not already handled
		echo do_shortcode(
		'[custom_fields_input style="table"]
		[ct_filter not="true"]
		_ct_jbp_job_Budget,
		_ct_jbp_job_Min_Budget,
		_ct_jbp_job_Due,
		_ct_jbp_job_Portfolio,
		_ct_jbp_job_Contact_Email,
		_ct_jbp_job_Open_for
		[/ct_filter]
		[/custom_fields_input]');
		?>
		<?php
		$tos_txt = $this->get_setting('job->tos_txt', '');
		if( !empty($tos_text) ): ?>

		<strong><?php esc_html_e( 'Terms of Service', JBP_TEXT_DOMAIN ); ?></strong>
		<div class="job-tos"><?php echo nl2br( $tos_txt ); ?></div>
		<?php endif; ?>

		<?php if( current_user_can( EDIT_JOB, $post_ID) ): ?>
		<div class="jbp-button-wrap group">
			<div class="jbp-button-group ">
				<?php wp_nonce_field( 'verify' ); ?>
				<?php if($this->get_setting('job->moderation->publish', 1) ): ?>
				<button type="submit" id="job-publish" name="data[post_status]" value="publish" class="toggle-job-save jbp-button job-go-public-button" ><?php esc_html_e('Save', JBP_TEXT_DOMAIN); ?></button>
				<?php endif; ?>

				<?php if( !$this->get_setting('job->moderation->publish', 1) ): ?>
				<button type="submit" id="job-pending" name="data[post_status]" value="pending" class="toggle-job-save jbp-button job-go-public-button" ><?php esc_html_e('Review', JBP_TEXT_DOMAIN); ?></button>
				<?php endif; ?>

				<?php if($this->get_setting('job->moderation->draft', 1) ): ?>
				<button type="submit" id="job-draft" name="data[post_status]" value="draft" class="toggle-job-save jbp-button job-go-public-button" ><?php esc_html_e('Draft', JBP_TEXT_DOMAIN); ?></button>
				<?php endif; ?>

				<?php if($editing): ?>
				<button type="button" class="jbp-button" onclick="window.location='<?php echo get_permalink( get_the_id() ); ?>';"><?php esc_html_e('Cancel', JBP_TEXT_DOMAIN); ?></button>
				<?php else: ?>
				<button type="button" class="jbp-button" onclick="window.location='<?php echo get_post_type_archive_link('jbp_jobs'); ?>';"><?php esc_html_e('Cancel', JBP_TEXT_DOMAIN); ?></button>
				<?php endif; ?>

			</div>
		</div>
		<?php endif; ?>
	</form>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($) {

		//Setup Globals
		jbpAddJob = <?php echo $add_job ? 'true':'false'; ?>;
		jbpPopupEnabled = <?php echo ($editing || $add_job) ? 'true':'false'; ?>;
		canEditJob = <?php echo current_user_can( EDIT_JOBS ) ? 'true' : 'false'; ?>;

		jbpEditableDefaults();
		$.fn.editable.defaults.pk = '<?php the_ID(); ?>';
		$.fn.editable.defaults.url = '<?php echo admin_url('/admin-ajax.php'); ?>';
		$.fn.editable.defaults.params = {"action": "jbp_job", "_wpnonce": "<?php echo wp_create_nonce('jbp_job');?>"};

		var $editables = $('.editable'); //Get a list of editable fields

		//		$('.toggle-job-save').click( function(){
		//			$.get( '<?php echo admin_url('admin-ajax.php'); ?>', {
		//				"action": "jbp_job_status",
		//				"post_id": "<?php the_ID(); ?>",
		//				"post_status": $(this).val(),
		//				"_wpnonce": "<?php echo wp_create_nonce('jbp_job');?>"
		//			});
		//			jbpPopup();
		//		});

		jbpPopup();
		$editables.editable();
console.log(jbpPopupEnabled);

		$('#_ct_jbp_job_Min_Budget').keyup(function () {
		var value = $('#_ct_jbp_job_Min_Budget').val();
		$('[name="_ct_jbp_job_Budget"]').rules('remove', 'min');
		$('[name="_ct_jbp_job_Budget"]').rules('add',{
		min:value
		})
		});
		});

	</script>
