<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed!' );
}
/**
 * @package Jobs +
 * @author  Arnold Bailey
 * @since   version 1.0
 * @license GPL2+
 */

global $post, $wp_roles, $post_ID;

$add_pro = false;
$editing = false;
//Are we adding a Listing?
if ( $post->ID == $this->pro_update_page_id ) {
	$add_pro = true;
	$post    = $this->get_default_custom_post( 'jbp_pro' );
	$editing = false;
	//for become expert widget
	if ( ! empty( $_GET['expert_title'] ) ) {
		$post->post_title = strip_slashes( $_GET['expert_title'] );
	}
} elseif ( $post->post_status == 'auto-draft' ) {
	$add_pro = true;
} elseif ( get_query_var( 'edit' ) ) { //Or are we editing a listing?
	$editing = current_user_can( EDIT_PRO, $post->ID );
}

$post_ID = $post->ID;
setup_postdata( $post );

$excerpt = $this->make_clickable( strip_tags( get_the_excerpt() ) );;

//Styles
wp_enqueue_style( 'jobs-plus-custom' );
wp_enqueue_style( 'jqueryui-editable' );
wp_enqueue_style( 'magnific-popup' );

//Scripts
wp_enqueue_script( 'element-query' );
wp_enqueue_script( 'jobs-plus' );
wp_enqueue_script( 'jquery-ui-dialog' );
wp_enqueue_script( 'jqueryui-editable' );
wp_enqueue_script( 'jqueryui-editable-ext' );
wp_enqueue_script( 'jquery-ui-dialog' );
wp_enqueue_script( 'magnific-popup' );
wp_enqueue_script( 'jquery-ui-slider' );

//var_dump( $editing );
//var_dump( current_user_can( EDIT_PRO, $post->ID) );
$this->no_thumbnail();
?>
<div class="pro-profile-wrapper group" data-eq-pts="tablet: 420, desk:768">

	<div class="group">
		<?php if ( dynamic_sidebar( 'pro-widget' ) ) : else: endif; ?>
	</div>
	<?php echo do_action( 'jbp_error' ); ?>
	<?php echo do_action( 'jbp_notice' ); ?>

	<form action="" method="POST" id="custom-fields-form">
		<?php wp_nonce_field( 'verify' ); ?>
		<input type="hidden" name="post_id" value="<?php echo $post->ID; ?>" />
		<input type="hidden" name="data[ID]" value="<?php echo $post->ID; ?>" />
		<input type="hidden" name="jbp-pro-update" value="save" />


		<?php if ( post_type_supports( 'jbp_pro', 'title' ) ): ?>
		<?php endif; ?>

		<div class="pro-content">
			<div class="pro-general-info">
				<?php echo do_shortcode( '[jbp-expert-gravatar]' ); ?>
				<div class="hide-on-edit pro-contact">
					<?php echo do_shortcode( '[jbp-expert-contact-btn class="pro-contact"]' ); ?>
				</div>
				<?php $socials = do_shortcode( '[ct id="_ct_jbp_pro_Social"]' );
				if ( ! empty( $socials ) || ( $editing == true || $add_pro == true ) ): ?>
					<div class="pro-content-wrapper">
						<h2 class="pro-social"><?php esc_html_e( 'Social', JBP_TEXT_DOMAIN ); ?></h2>

						<div class="pro-content-editable">
							<?php echo do_shortcode( '[jbp-expert-social]' ); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php $skills = do_shortcode( '[ct id="_ct_jbp_pro_Skills"]' );
				if ( ! empty( $skills ) || ( $editing == true || $add_pro == true ) ): ?>
					<div class="pro-content-wrapper">
						<h2 class="pro-skills"><?php esc_html_e( 'Skills', JBP_TEXT_DOMAIN ); ?></h2>

						<div class="pro-content-editable">
							<?php echo do_shortcode( '[jbp-expert-skills]' ); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<div class="pro-detail-info">
				<div class="pro-content-wrapper pro-tagline show-on-edit">
					<h2 class="subheader1 pro-content-editable pro-title"><?php esc_html_e( 'Expert Name: ', JBP_TEXT_DOMAIN ); ?>
						<span class="editable editable-title"
							  data-type="text"
							  data-onblur="submit"
							  data-name="post_title"
							  data-savenochange="true"
							  data-emptytext="<?php esc_attr_e( 'No Name', JBP_TEXT_DOMAIN ); ?>"
							  data-value="<?php echo esc_attr( get_the_title() ); ?>"
							  data-emptyclass="editable-required"
							  data-original-title="<?php esc_attr_e( 'Enter the Title', JBP_TEXT_DOMAIN ); ?>">
						</span>
					</h2>
				</div>
				<h3 class="subheader1 pro-content-editable pro-tagline <?php $tag_line = get_post_meta( $post->ID, '_ct_jbp_pro_Tagline', true );
				echo ( $editing == false && empty( $tag_line ) ) ? 'show-on-edit' : null; ?>">
						<span class="editable pro-tagline"
							  data-type="text"
							  data-name="_ct_jbp_pro_Tagline"
							  data-mode="popup"
							  data-emptytext="<?php esc_attr_e( 'Your Tagline', JBP_TEXT_DOMAIN ); ?>"
							  data-original-title="<?php esc_attr_e( 'Tagline', JBP_TEXT_DOMAIN ); ?>"
							  data-value="<?php echo esc_attr( do_shortcode( '[ct id="_ct_jbp_pro_Tagline"]' ) ); ?>"
							>
						</span>
				</h3>

				<?php
				$empty_name = 'Your Name';
				if ( $editing || $add_pro ) {
					if ( is_object( $post ) && ! empty( $post->post_title ) ) {
						$empty_name = $post->post_title;
					}
				}
				$name = do_shortcode( '[ct id="_ct_jbp_pro_First_Last"]' );
				if ( $editing == false && $add_pro == false ) {
					if ( empty( $name ) ) {
						if ( is_object( $post ) && ! empty( $post->post_title ) ) {
							$name = $post->post_title;
						}
					}
				}
				?>
				<label class="pro-content-editable pro-name">
					<span class="editable editable-firstlast"
						  data-type="firstlast"
						  data-name="_ct_jbp_pro_First_Last"
						  data-emptytext="<?php esc_attr_e( $empty_name, JBP_TEXT_DOMAIN ); ?>"
						  data-value="<?php echo esc_attr( $name ); ?>"
						  data-original-title="<?php echo esc_attr( 'Enter Your First and Last Name', JBP_TEXT_DOMAIN ); ?>">
						</span>
					<?php
					printf( ' is a member since %s', date( "M Y", strtotime( get_the_author_meta( 'user_registered' ) ) ) ); ?>
				</label>
				<label for="company" class="pro-content-editable pro-company <?php $company = get_post_meta( $post->ID, '_ct_jbp_pro_Company_URL', true );
				echo ( $editing == false && empty( $company ) ) ? 'show-on-edit' : null; ?>">
					<strong><?php esc_html_e( 'Company: ', JBP_TEXT_DOMAIN ); ?></strong>
						<span class="editable"
							  id="company"
							  data-type="link"
							  data-name="_ct_jbp_pro_Company_URL"
							  data-emptytext="<?php esc_attr_e( 'Your Company', JBP_TEXT_DOMAIN ); ?>"
							  data-link-label="<?php esc_attr_e( 'Company:', JBP_TEXT_DOMAIN ); ?>"
							  data-link-placeholder="<?php esc_attr_e( 'Company Name', JBP_TEXT_DOMAIN ); ?>"
							  data-url-placeholder="<?php esc_attr_e( 'www.company.com', JBP_TEXT_DOMAIN ); ?>"
							  data-value="<?php echo esc_attr( do_shortcode( '[ct id="_ct_jbp_pro_Company_URL"]' ) ); ?>"
							  data-original-title="<?php esc_attr_e( 'Company and URL', JBP_TEXT_DOMAIN ); ?>">
						</span>
				</label>
				<label class="pro-content-editable pro-location <?php $location = get_post_meta( $post->ID, '_ct_jbp_pro_Location', true );
				echo ( $editing == false && empty( $location ) ) ? 'show-on-edit' : null; ?>">
					<strong><?php esc_html_e( 'Location: ', JBP_TEXT_DOMAIN ); ?></strong>
						<span class="editable"
							  data-type="select"
							  data-name="_ct_jbp_pro_Location"
							  data-emptytext="<?php esc_attr_e( 'Your Location', JBP_TEXT_DOMAIN ); ?>"
							  data-value="<?php echo esc_attr( do_shortcode( '[ct id="_ct_jbp_pro_Location"]' ) ); ?>"
							  data-source="<?php echo esc_attr( JBP_PLUGIN_URL . 'data/countries.json' ); ?>"
							  data-original-title="<?php esc_attr_e( 'Enter Location from dropdown', JBP_TEXT_DOMAIN ); ?>"
							><?php echo esc_html( do_shortcode( '[ct id="_ct_jbp_pro_Location"]' ) ); ?></span>
				</label>
				<label class="pro-content-editable pro-contact-email">
					<strong><?php esc_html_e( 'Contact Email: ', JBP_TEXT_DOMAIN ); ?></strong>
						<span class="editable"
							  data-type="text"
							  data-mode="popup"
							  data-name="_ct_jbp_pro_Contact_Email"
							  data-emptytext="<?php esc_attr_e( 'Your contact email', JBP_TEXT_DOMAIN ); ?>"
							  data-value="<?php echo esc_attr( do_shortcode( '[ct id="_ct_jbp_pro_Contact_Email"]' ) ); ?>"
							  data-label="<?php esc_attr_e( 'Contact Email', JBP_TEXT_DOMAIN ); ?>"
							  data-emptyclass="editable-required"
							  data-original-title="<?php esc_attr_e( 'Contact email', JBP_TEXT_DOMAIN ); ?>">
						</span>

					<p class="show-on-edit"><?php _e( 'Your email will not be published over the site', JBP_TEXT_DOMAIN ) ?></p>
				</label>
				<?php if ( post_type_supports( 'jbp_pro', 'editor' ) ): ?>
					<div class="pro-content-wrapper pro-biography">
						<h2 class="pro-biography"><?php esc_html_e( 'Biography', JBP_TEXT_DOMAIN ); ?></h2>

						<div class="pro-content-editable">
							<div class="editable"
								 data-type="textarea"
								 data-name="post_content"
								 data-mode="popup"
								 data-emptytext="<?php esc_attr_e( ( current_user_can( 'manage_options' ) ) ? 'Tell us about the expert' : 'Tell us about yourself', JBP_TEXT_DOMAIN ); ?>"
								 data-original-title="<?php esc_attr_e( 'Biography Description', JBP_TEXT_DOMAIN ); ?>"
								><?php echo $this->make_clickable( strip_tags( get_the_content() ) ); ?></div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( post_type_supports( 'jbp_pro', 'excerpt' ) ): ?>
					<div class="pro-content-wrapper pro-excerpt">
						<h2 class="pro-excerpt "><?php esc_html_e( 'Excerpt', JBP_TEXT_DOMAIN ); ?></h2>

						<div class="pro-content-editable">
							<div class="editable"
								 data-type="textarea"
								 data-name="post_excerpt"
								 data-mode="popup"
								 data-emptytext="<?php esc_attr_e( 'Tell us about yourself', JBP_TEXT_DOMAIN ); ?>"
								 data-original-title="<?php esc_attr_e( 'Short Excerpt', JBP_TEXT_DOMAIN ); ?>"
								><?php echo $excerpt; ?></div>
						</div>
					</div>
				<?php endif; ?>

				<div class="pro-content-wrapper pro-portfolio">
					<h2 class="pro-portfolio"><?php esc_html_e( 'Portfolio', JBP_TEXT_DOMAIN ); ?></h2>

					<div class="pro-content-editable">
						<?php echo do_shortcode( '[jbp-expert-portfolio]' ); ?>
					</div>

				</div>

				<?php if ( post_type_supports( 'jbp_pro', 'custom-fields' ) ): ?>
					<div>
						<?php
						//Any custom fields not already handled
						echo do_shortcode(
							'[custom_fields_input style="editfield"]
							[ct_filter not="true"]
							_ct_jbp_pro_Tagline,
							_ct_jbp_pro_First_Last,
							_ct_jbp_pro_Company_URL,
							_ct_jbp_pro_Location,
							_ct_jbp_pro_Contact_Email,
							_ct_jbp_pro_Social,
							_ct_jbp_pro_Portfolio,
							_ct_jbp_pro_Skills,
							[/ct_filter]
							[/custom_fields_input]' );
						?>
					</div>
				<?php endif; ?>
			</div>
			<div style="clear: both"></div>
			<?php if ( current_user_can( EDIT_PROS ) ): ?>
				<div class="pro-button-group show-on-edit">
					<?php if ( $this->get_setting( 'pro->moderation->publish', 1 ) ): ?>
						<button type="submit" id="pro-publish" name="data[post_status]" value="publish" class="toggle-pro-save jbp-button pro-go-public-button"><?php esc_html_e( 'Publish', JBP_TEXT_DOMAIN ); ?></button>
					<?php endif; ?>

					<?php if ( ! $this->get_setting( 'pro->moderation->publish', 1 ) ): ?>
						<button type="submit" id="pro-pending" name="data[post_status]" value="pending" class="toggle-pro-save pro-jbp-button go-public-button"><?php esc_html_e( 'Review', JBP_TEXT_DOMAIN ); ?></button>
					<?php endif; ?>

					<?php if ( $this->get_setting( 'pro->moderation->draft', 1 ) ): ?>
						<button type="submit" id="pro-draft" name="data[post_status]" value="draft" class="toggle-pro-save jbp-button pro-go-public-button"><?php esc_html_e( 'Save Draft', JBP_TEXT_DOMAIN ); ?></button>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( current_user_can( EDIT_PRO, $post->ID ) ): ?>
				<div class="pro-edit" style="text-align: left">
					<span><button type="button" id="toggle-pro-edit" class="pro-button jbp-button pro-edit-button hide-on-edit"><?php esc_html_e( 'Edit', JBP_TEXT_DOMAIN ); ?></button></span>
				</div>
			<?php endif; ?>
		</div>
	</form>
</div>


<script type="text/javascript">
	jQuery(document).ready(function ($) {
		//Setup Globals
		jbpAddPro = <?php echo $add_pro ? 'true':'false'; ?>;
		jbpPopupEnabled = <?php echo ($editing || $add_pro) ? 'true':'false'; ?>;
		canEditPro = <?php echo current_user_can(EDIT_PRO, $post->ID ) ? 'true' : 'false'; ?>;


		jbpEditableDefaults();
		$.fn.editable.defaults.pk = '<?php the_ID(); ?>';
		$.fn.editable.defaults.url = '<?php echo admin_url('/admin-ajax.php'); ?>';
		$.fn.editable.defaults.params = {'action': 'jbp_pro', '_wpnonce': '<?php echo wp_create_nonce('jbp_pro');?>'};

		var $editables = $('.editable'); //Get a list of editable fields

		$('#jbp_certified').on('change', function (e) {
			e.preventDefault();
			$.ajax({
				url : '<?php echo admin_url('/admin-ajax.php'); ?>',
				type: 'post',
				data: $('#jbp_certified_form input').serialize()
			});
		});

		$editables.on('hidden', function (e, reason) {
			if (reason === 'save' || reason === 'nochange' || reason === 'cancel') {
				var $next = $editables.eq(($editables.index(this) + 1) % $editables.length);

				if (jbpAddPro && $(this).attr('data-name') == 'post_title') {
					if (reason === 'cancel') {
						window.history.go(-1);
						jbp_create_dialog(
							"<?php esc_html_e('Canceling Profile', JBP_TEXT_DOMAIN); ?>",
							"<?php esc_html_e('Canceling Profile, Please Wait', JBP_TEXT_DOMAIN); ?>",
							{
								dialogClass: 'dialogcenter',
								height     : 150,
								modal      : true
							});
					} else {
						window.location = '<?php echo get_edit_post_link($post->ID); ?>';
						jbp_create_dialog(
							"<?php _e('Creating Profile', JBP_TEXT_DOMAIN); ?>",
							"<?php _e('Creating Your Profile, Please Wait', JBP_TEXT_DOMAIN); ?>",
							{
								dialogClass: 'dialogcenter',
								height     : 150,
								modal      : true
							});
					}
				} else {
					if (jbpAddPro) {
						setTimeout(function () {
							$next.editable('show');
						}, 300);
					}
				}
			}
		});

		//Toggle whether edit or popup
		$('#toggle-pro-edit').click(function () {
			jbpPopup();
		});

		$('#custom-fields-form').submit(function (e) {
			var result = jbp_required_dialog(e,
				"<?php esc_html_e('Required Fields', JBP_TEXT_DOMAIN); ?>",
				"<p><?php esc_html_e('Please complete the required fields', JBP_TEXT_DOMAIN); ?></p>"
			);
			return result;
		});

		jbpPopup();
		jbpFirstField($editables);

		var general_height = $('.pro-general-info').height();
		var detail_height = $('.pro-detail-info').height();
		/*if(general_height > detail_height){
			$('.pro-general-info').css('border-right','solid 1px #ddd');
		}else{
			$('.pro-detail-info').css('border-right','solid 1px #ddd');
		}*/
	});

</script>
