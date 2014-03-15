<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

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
					<th scope="row"><?php _e( 'New Job Button:', JBP_TEXT_DOMAIN ) ?></th>
					<td>
						<code><strong>[new_job_btn text="<?php _e('New Job', JBP_TEXT_DOMAIN);?>" view="loggedin | loggedout | both" class=""]</strong></code> or
						<br /><code><strong>[new_job_btn view="loggedin | loggedout | both" class=""]&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php _e('New Job', JBP_TEXT_DOMAIN);?>[/new_job_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the New Job Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'New Pro Button:', JBP_TEXT_DOMAIN ) ?></th>
					<td>
						<code><strong>[new_pro_btn text="<?php _e('New Pro', JBP_TEXT_DOMAIN);?>" view="loggedin | loggedout | both" class=""]</strong></code> or
						<br /><code><strong>[new_pro_btn view="loggedin | loggedout | both" class=""]&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php _e('New Pro', JBP_TEXT_DOMAIN);?>[/new_pro_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the New Job Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( ' Jobs Archive Button:', JBP_TEXT_DOMAIN ) ?></th>
					<td>
						<code><strong>[job_archive_btn text="<?php _e('Browse Jobs', JBP_TEXT_DOMAIN);?>" view="loggedin | loggedout | both" class=""]</strong></code> or
						<br /><code><strong>[job_archive_btn view="loggedin | loggedout | both" class=""]&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php _e('Browse Jobs', JBP_TEXT_DOMAIN);?>[/job_archive_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the Job Archive Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ) ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Pro Archive Button:', JBP_TEXT_DOMAIN ) ?></th>
					<td>
						<code><strong>[pro_archive_btn text="<?php _e('Browse Pros', JBP_TEXT_DOMAIN);?>" view="loggedin | loggedout | both" class=""]</strong></code> or
						<br /><code><strong>[pro_archive_btn view="loggedin | loggedout | both" class=""]&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php _e('Browse Pros', JBP_TEXT_DOMAIN);?>[/pro_archive_btn]</strong></code>
						<br /><span class="description"><?php _e( 'Links to the Pros Archive Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ) ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
