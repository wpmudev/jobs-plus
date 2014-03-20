<?php if (!defined('ABSPATH')) die('No direct access allowed!'); 

$pro_obj = get_post_type_object('jbp_pro');
$job_obj = get_post_type_object('jbp_job');

?>

<div class="wrap">
	<?php screen_icon('jobs-plus'); ?>
	<h2><?php printf( __('Jobs+ Settings %s', JBP_TEXT_DOMAIN), JOBS_PLUS_VERSION );?></h2>
	<?php $this->render_tabs(); ?>
	<br />

	<div class="postbox">
		<h3 class='hndle'><span><?php _e( 'Jobs+ Shortcodes', JBP_TEXT_DOMAIN ) ?></span></h3>
		<div class="inside">
			<p>
				<?php _e( 'Shortcodes allow you to include dynamic store content in posts and pages on your site. Simply type or paste them into your post or page content where you would like them to appear. Optional attributes can be added in a format like <em>[shortcode attr1="value" attr2="value"]</em>.', JBP_TEXT_DOMAIN ) ?>
			</p>
			<p>
				<?php _e( 'Attributes: ("|" means use one OR the other. ie style="grid" or style="list" not style="grid | list")', JBP_TEXT_DOMAIN); ?>
				<br /><?php _e( 'text = <em>Text to display on a button</em>', JBP_TEXT_DOMAIN ) ?>
				<br /><?php _e( 'view = <em>Whether the button is visible when loggedin, loggedout, or both</em>', JBP_TEXT_DOMAIN ) ?>
				<br /><?php _e( 'class = <em>define a css class for this button.</em>', JBP_TEXT_DOMAIN ) ?>
			</p>
			<table class="form-table">
				<tr>
					<th scope="row"><?php printf( __('Add %s Button', JBP_TEXT_DOMAIN), $job_obj->labels->singular_name);?></th>
					<td>
						<code><strong>[add_job_btn text="<?php printf( esc_attr__('Add %s', JBP_TEXT_DOMAIN), $job_obj->labels->singular_name);?>" view="loggedin | loggedout | both" class=""]</strong></code> or
						<br /><code><strong>[add_job_btn view="loggedin | loggedout | both" class=""]&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_attr__('Add %s', JBP_TEXT_DOMAIN), $job_obj->labels->singular_name);?>[/add_job_btn]</strong></code>
						<br /><span class="description"><?php printf( __( 'Links to the Add %s Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $job_obj->labels->singular_name) ?></span>
					</td>
				</tr>  
				<tr>
					<th scope="row"><?php printf( __('Add %s Button', JBP_TEXT_DOMAIN), $pro_obj->labels->singular_name);?></th>
					<td>
						<code><strong>[add_pro_btn text="<?php printf( esc_attr__('Add %s', JBP_TEXT_DOMAIN), $pro_obj->labels->singular_name);?>" view="loggedin | loggedout | both" class=""]</strong></code> or
						<br /><code><strong>[add_pro_btn view="loggedin | loggedout | both" class=""]&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_attr__('Add %s', JBP_TEXT_DOMAIN), $pro_obj->labels->singular_name);?>[/add_pro_btn]</strong></code>
						<br /><span class="description"><?php printf(esc_attr__( 'Links to the Add %s Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $pro_obj->labels->singular_name); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( ' Jobs Archive Button:', JBP_TEXT_DOMAIN ) ?></th>
					<td>
						<code><strong>[job_archive_btn text="<?php printf( esc_attr__('Browse %s', JBP_TEXT_DOMAIN), $job_obj->labels->name);?>" view="loggedin | loggedout | both" class=""]</strong></code> or
						<br /><code><strong>[job_archive_btn view="loggedin | loggedout | both" class=""]&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_attr__('Browse %s', JBP_TEXT_DOMAIN), $job_obj->labels->name);?>[/job_archive_btn]</strong></code>
						<br /><span class="description"><?php printf(__( 'Links to the Job Archive Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $job_obj->labels->singular_name ); ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Pro Archive Button:', JBP_TEXT_DOMAIN ) ?></th>
					<td>
						<code><strong>[pro_archive_btn text="<?php printf( esc_attr__('Browse %s', JBP_TEXT_DOMAIN), $pro_obj->labels->name);?>" view="loggedin | loggedout | both" class=""]</strong></code> or
						<br /><code><strong>[pro_archive_btn view="loggedin | loggedout | both" class=""]&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_attr__('Browse %s', JBP_TEXT_DOMAIN), $pro_obj->labels->name);?>[/pro_archive_btn]</strong></code>
						<br /><span class="description"><?php printf( __( 'Links to the %s Archive Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $pro_obj->labels->singular_name) ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
