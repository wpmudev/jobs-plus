<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post, $Jobs_Plus_Core;

$add_pro = false;
//Are we adding a Listing?
if ($post->ID == $Jobs_Plus_Core->add_pro_page_id) {
	$add_pro = true;
	$post = $Jobs_Plus_Core->get_default_custom_post('jbp_pro');
	setup_postdata($post);
	$link = add_query_arg('edit', 1, get_permalink($post->ID) );
	$editing = false;
}
elseif (get_query_var('edit')) { //Or are we editing a listing?
	$editing = current_user_can('edit_pro');
	$link = get_permalink($post->ID);
}

wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_style('jqueryui-editable');
wp_enqueue_script('jqueryui-editable');
wp_enqueue_script('jqueryui-editable-ext');

?>

<?php echo do_action('jbp_error'); ?>
<?php echo do_action('jbp_notice'); ?>

<label class="pro-content-editable pro-name show-on-edit">
	<h3><?php _e('Title: ', JBP_TEXT_DOMAIN); ?>
		<span class="editable editable-firstlast"
			data-type="text"
			data-onblur="submit"
			data-name="post_title"
			data-savenochange = "true"
			data-emptytext="<?php _e('No Title', JBP_TEXT_DOMAIN); ?>"
			data-value="<?php esc_attr_e( get_the_title() ); ?>"
			data-emptyclass="editable-required"
			data-original-title="<?php _e('Enter the Title', JBP_TEXT_DOMAIN); ?>">
		</span>
	</h3>
</label>
<div class="pro-profile-wrapper jbp-clear">
	<form action="" method="POST" id="custom-fields-form" >
		<?php wp_nonce_field('verify'); ?>
		<input type="hidden" name="post_id" value="<?php echo $post->ID; ?>" />
		<input type="hidden" name="data[ID]" value="<?php echo $post->ID; ?>" />
		<input type="hidden" name="jbp-pro-update" value="save" />

		<div class="pro-content">
			<div class="pro-pad jbp-clear">
				<div class="pro-content-wrapper pro-profile">
					<label class="pro-content-editable pro-name">
						<span class="editable editable-firstlast"
							data-type="firstlast"
							data-name="_ct_jbp_pro_First_Last"
							data-emptytext="<?php _e('Your Name', JBP_TEXT_DOMAIN); ?>"
							data-value="<?php esc_attr_e(do_shortcode('[ct id="_ct_jbp_pro_First_Last"]') ); ?>"
							data-original-title="<?php _e('Enter Your First and Last Name', JBP_TEXT_DOMAIN); ?>">
						</span>
						<?php printf(' is a member since %s', date("M Y", strtotime(get_the_author_meta('user_registered') ) ) ); ?>
					</label>
					<label for="company" class="pro-content-editable pro-company">
						<strong><?php _e('Company: ', JBP_TEXT_DOMAIN); ?></strong>
						<span class="editable"
							id="company"
							data-type="link"
							data-name="_ct_jbp_pro_Company_URL"
							data-emptytext="<?php _e('Your Company', JBP_TEXT_DOMAIN); ?>"
							data-link-label="<?php _e('Company:', JBP_TEXT_DOMAIN); ?>"
							data-link-placeholder="<?php _e('Company Name', JBP_TEXT_DOMAIN); ?>"
							data-url-placeholder="<?php _e('www.company.com', JBP_TEXT_DOMAIN); ?>"
							data-value="<?php esc_attr_e(do_shortcode('[ct id="_ct_jbp_pro_Company_URL"]') ); ?>"
							data-original-title="<?php _e('Company and URL', JBP_TEXT_DOMAIN); ?>">
						</span>
					</label>
					<label class="pro-content-editable pro-location">
						<strong><?php _e('Location: ', JBP_TEXT_DOMAIN); ?></strong>
						<span class="editable"
							data-type="select"
							data-name="_ct_jbp_pro_Location"
							data-emptytext="<?php _e('Your Location', JBP_TEXT_DOMAIN); ?>"
							data-value="<?php esc_attr_e(do_shortcode('[ct id="_ct_jbp_pro_Location"]') ); ?>"
							data-source="<?php echo JBP_PLUGIN_URL . 'data/countries.json';?>"
							data-original-title="<?php _e('Enter Location from dropdown', JBP_TEXT_DOMAIN); ?>"
						><?php esc_html_e(do_shortcode('[ct id="_ct_jbp_pro_Location"]') ); ?></span>
					</label>
					<label class="pro-content-editable pro-contact-email">
						<strong><?php _e('Contact Email: ', JBP_TEXT_DOMAIN); ?></strong>
						<span class="editable"
							data-type="text"
							data-mode="popup"
							data-name="_ct_jbp_pro_Contact_Email"
							data-emptytext="<?php _e('Your contact email', JBP_TEXT_DOMAIN); ?>"
							data-value="<?php esc_attr_e(do_shortcode('[ct id="_ct_jbp_pro_Contact_Email"]')); ?>"
							data-label="<?php esc_attr_e('Contact Email', JBP_TEXT_DOMAIN ); ?>"
							data-emptyclass="editable-required"
							data-original-title="<?php _e('Contact email', JBP_TEXT_DOMAIN); ?>">
						</span>
					</label>
					<label class="pro-rate-me">
						<strong><?php _e('Rate me: ', JBP_TEXT_DOMAIN); ?></strong>
						<?php echo do_shortcode('[jbp-rate-this]'); ?>
					</label>
				</div>
				<div class="pro-content-wrapper pro-biography pro-content-editable">
					<h3>Biography</h3>
					<?php if(current_user_can('edit_pros') ): ?>
					<span class="pro-edit"><button type="button" id="toggle-pro-edit" class="pro-button pro-edit-button hide-on-edit"><?php esc_html_e('Edit', JBP_TEXT_DOMAIN); ?></button></span>
					<?php endif; ?>
					<div class="editable"
						data-type="textarea"
						data-name="post_content"
						data-mode="inline"
						data-emptytext="<?php esc_attr_e('Tell us about yourself', JBP_TEXT_DOMAIN); ?>"
						data-original-title="<?php _e('Biography Description', JBP_TEXT_DOMAIN); ?>"
					><?php echo $this->make_clickable(strip_tags(get_the_content() ) ); ?></div>
				</div>

				<div class="pro-content-wrapper pro-excerpt pro-content-editable">
					<h3>Excerpt</h3>
					<div class="editable"
						data-type="textarea"
						data-name="post_excerpt"
						data-mode="inline"
						data-emptytext="<?php esc_attr_e('Tell us about yourself', JBP_TEXT_DOMAIN); ?>"
						data-original-title="<?php _e('Short Excerpt', JBP_TEXT_DOMAIN); ?>"
					><?php echo $this->make_clickable(strip_tags(get_the_excerpt() ) ); ?></div>
				</div>

				<div class="pro-content-wrapper pro-portfolio">
					<h3><?php _e('Portfolio', JBP_TEXT_DOMAIN); ?></h3>
					<?php echo do_shortcode('[jbp-pro-portfolio]'); ?>
				</div>

				<div>
					<?php
					//Any custom fields not already handled
					echo do_shortcode(
					'[custom_fields_input style="editfield"]
					[ct_filter not="true"]
					_ct_jbp_pro_First_Last,
					_ct_jbp_pro_Company_URL,
					_ct_jbp_pro_Location,
					_ct_jbp_pro_Contact_Email,
					_ct_jbp_pro_Social,
					_ct_jbp_pro_Facebook_URL,
					_ct_jbp_pro_LinkedIn_URL,
					_ct_jbp_pro_Twitter_URL,
					_ct_jbp_pro_Skype_URL,
					_ct_jbp_pro_Portfolio,
					_ct_jbp_pro_Skills,
					[/ct_filter]
					[/custom_fields_input]');
					?>
				</div>
			</div>
		</div>
		<div class="pro-left">
			<?php echo do_shortcode('[jbp-pro-gravatar]'); ?>
			<div class="hide-on-edit">
				<?php echo do_shortcode('[jbp-pro-contact-btn class="pro-contact"]'); ?>
				<?php //echo do_shortcode('[pro_points]'); ?>
			</div>

			<?php if(current_user_can('edit_pros') ): ?>
			<div class="show-on-edit">
				<?php if($Jobs_Plus_Core->get_setting('pro->moderation->publish') ): ?>
				<div class="pro-go-public">
					<button type="submit" id="pro-publish" name="data[post_status]" value="publish" class="toggle-pro-save pro-go-public-button" ><?php esc_html_e('Save', JBP_TEXT_DOMAIN); ?></button>
				</div>
				<?php endif; ?>

				<?php if($Jobs_Plus_Core->get_setting('pro->moderation->pending') ): ?>
				<div class="pro-go-public">
					<button type="submit" id="pro-pending" name="data[post_status]" value="pending" class="toggle-pro-save pro-go-public-button" ><?php esc_html_e('Review', JBP_TEXT_DOMAIN); ?></button>
				</div>
				<?php endif; ?>

				<?php if($Jobs_Plus_Core->get_setting('pro->moderation->draft') ): ?>
				<div class="pro-go-public">
					<button type="submit" id="pro-draft" name="data[post_status]" value="draft" class="toggle-pro-save pro-go-public-button" ><?php esc_html_e('Draft', JBP_TEXT_DOMAIN); ?></button>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="pro-content-wrapper pro-social">
				<h3><?php _e('Social', JBP_TEXT_DOMAIN); ?></h3>
				<?php echo do_shortcode('[jbp-pro-social]'); ?>
			</div>
			<div class="pro-content-wrapper pro-skills">
				<h3><?php _e('Skills', JBP_TEXT_DOMAIN); ?></h3>
				<?php echo do_shortcode('[jbp-pro-skills]'); ?>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($) {
		//Setup Globals
		jbpAddPro = <?php echo $add_pro ? 'true':'false'; ?>;
		jbpPopupEnabled = <?php echo ($editing || $add_pro) ? 'true':'false'; ?>;
		canEditPro = <?php echo current_user_can('edit_pros') ? 'true' : 'false'; ?>;

		jbpEditableDefaults();
		$.fn.editable.defaults.pk = '<?php the_ID(); ?>';
		$.fn.editable.defaults.url = '<?php echo admin_url('/admin-ajax.php'); ?>';
		$.fn.editable.defaults.params = {'action': 'jbp_pro', '_wpnonce': '<?php echo wp_create_nonce('jbp_pro');?>'};

		var $editables = $('.editable'); //Get a list of editable fields

		$editables.on('hidden', function(e, reason){
			if(reason === 'save' || reason === 'nochange' || reason === 'cancel') {
				var $next = $editables.eq( ($editables.index(this) + 1) % $editables.length );

				if( jbpAddPro && $(this).attr('data-name') == 'post_title') {
					if( reason === 'cancel') {
						window.history.go(-1);
						jbp_create_dialog(
						"<?php _e('Canceling Profile', JBP_TEXT_DOMAIN); ?>",
						"<?php _e('Canceling Profile,<br />Please Wait', JBP_TEXT_DOMAIN); ?>",
						{
							dialogClass: 'dialogcenter',
							height: 150,
							modal: true
						});
					} else {
						window.location = '<?php echo $link ?>';
						jbp_create_dialog(
						"<?php _e('Creating Profile', JBP_TEXT_DOMAIN); ?>",
						"<?php _e('Creating Your Profile,<br />Please Wait', JBP_TEXT_DOMAIN); ?>",
						{
							dialogClass: 'dialogcenter',
							height: 150,
							modal: true
						});
					}
				} else {
					setTimeout(function() { $next.editable('show'); }, 300);
				}
			}
		});

		//Toggle whether edit or popup
		$('#toggle-pro-edit').click( function(){ jbpPopup(); });

		$('#custom-fields-form').submit( function( e ){
			var result = jbp_required_dialog( e,
			"<?php _e('Required Fields', JBP_TEXT_DOMAIN); ?>",
			"<?php _e('<p>Please complete the required fields</p>', JBP_TEXT_DOMAIN); ?>"
			);
			return result;
		});

		jbpPopup();
		jbpFirstField($editables);

	});

</script>
