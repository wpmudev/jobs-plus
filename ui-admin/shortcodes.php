<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/
?>
<div class="wrap">
	<?php screen_icon('jobs-plus'); ?>
	<h2><?php printf( __('Jobs+ Settings %s', JBP_TEXT_DOMAIN), JOBS_PLUS_VERSION );?></h2>
	<?php $this->render_tabs('shortcodes'); ?>
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
					<th scope="row"><?php printf( esc_html__('%s Profile Button', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name); ?></th>
					<td>
						<code>
							[jbp-pro-profile-btn text="<?php esc_html_e('My Profile', JBP_TEXT_DOMAIN);?>" view="loggedin | loggedout | both" class="some class"]
						</code>
						or
						<code>
							[jbp-pro-profile-btn view="loggedin | loggedout | both" class="some class"]
							<br/>&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php esc_html_e('My Profile', JBP_TEXT_DOMAIN );?>
							<br/>[/jbp-pro-profile-btn]
						</code>
						<span class="description"><?php esc_html_e('Links to the current user\'s profiles. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ) ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php printf( esc_html__('Post %s Button', JBP_TEXT_DOMAIN), $this->job_labels->singular_name);?></th>
					<td>
						<code>
							[jbp-job-post-btn text="<?php printf( esc_html__('Post a %s', JBP_TEXT_DOMAIN), $this->job_labels->singular_name);?>" view="loggedin | loggedout | both" class="some class"]
						</code>
						or
						<code>
							[jbp-job-post-btn view="loggedin | loggedout | both" class="some class"]
							<br/>&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_html__('Post a %s', JBP_TEXT_DOMAIN), $this->job_labels->singular_name);?>
							<br/>[/jbp-job-post-btn]
						</code>
						<span class="description"><?php printf( esc_html__( 'Links to the Post %s Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name) ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php printf( esc_html__('Post %s Button', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name);?></th>
					<td>
						<code>
							[jbp-pro-post-btn text="<?php printf( esc_html__('Post an %s', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name);?>" view="loggedin | loggedout | both" class="some class"]
						</code>
						or
						<code>
							[jbp-pro-post-btn view="loggedin | loggedout | both" class="some class"]
							<br/>&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_html__('Post an %s', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name);?>
							<br/>[/jbp-pro-post-btn]
						</code>
						<span class="description"><?php printf(esc_html__( 'Links to the Post %s Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name); ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php printf( esc_html__( '%s Archive Button:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
					<td>
						<code>
							[jbp-pro-browse-btn text="<?php printf( esc_html__('Browse %s', JBP_TEXT_DOMAIN), $this->job_labels->name);?>" view="loggedin | loggedout | both" class="some class"]
						</code>
						or
						<code>
							[jbp-pro-browse-btn view="loggedin | loggedout | both" class="some class"]
							<br/>&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_html__('Browse %s', JBP_TEXT_DOMAIN), $this->job_labels->name);?>
							<br/>[/jbp-pro-browse-btn]
						</code>
						<span class="description"><?php printf(esc_html__( 'Links to the %s Archive Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name ); ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php printf( esc_html__( 'Pro Archive Button:', JBP_TEXT_DOMAIN ), $this->pro_labels->name) ?></th>
					<td>
						<code>
							[jbp-job-browse-btn text="<?php printf( esc_html__('Browse %s', JBP_TEXT_DOMAIN), $this->pro_labels->name);?>" view="loggedin | loggedout | both" class="some class"]
						</code>
						or
						<code>
							[jbp-job-browse-btn view="loggedin | loggedout | both" class="some class"]
							<br/>&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_html__('Browse %s', JBP_TEXT_DOMAIN), $this->pro_labels->name);?>
							<br/>[/jbp-job-browse-btn]
						</code>
						<span class="description"><?php printf( esc_html__( 'Links to the %s Archive Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name) ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php printf( esc_html__( '%s Contact Button:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
					<td>
						<code>
							[jbp-pro-contact-btn text="<?php esc_html_e('Contact', JBP_TEXT_DOMAIN);?>" post="post_id" view="loggedin | loggedout | both" class="some class"]
						</code>
						or
						<code>
							[jbp-job-contact-btn  post="post_id" view="loggedin | loggedout | both" class="some class"]
							<br/>&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php esc_html_e('Contact', JBP_TEXT_DOMAIN);?>
							<br/>[/jbp-job-contact-btn]
						</code>
						<span class="description"><?php printf(esc_html__( 'Links to the %s Email Contact Page. If "post" is not used it defaults to the current global $post object. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name ); ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php printf( esc_html__( '%s Contact Button:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
					<td>
						<code>
							[jbp-pro-contact-btn text="<?php esc_html_e('Contact', JBP_TEXT_DOMAIN);?>" post="post_id" view="loggedin | loggedout | both" class="some class"]
						</code>
						or
						<code>
							[jbp-pro-contact-btn  post="post_id" view="loggedin | loggedout | both" class="some class"]
							<br/>&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php esc_html_e('Contact', JBP_TEXT_DOMAIN);?>
							<br/>[/jbp-pro-contact-btn]
						</code>
						<span class="description"><?php printf(esc_html__( 'Links to the %s email contact Page. If "post" is not used it defaults to the current global $post object. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name ); ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php printf( esc_html__('%s Search Form:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
					<td>
						<code>
							[jbp-job-search text="<?php printf( esc_html__('Search %s for', JBP_TEXT_DOMAIN), $this->job_labels->name);?>" view="loggedin | loggedout | both" class="some class"]
						</code>
						or
						<code>
							[jbp-job-search view="loggedin | loggedout | both" class="some class"]
							<br/>&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_html__('Search %s for', JBP_TEXT_DOMAIN), $this->job_labels->name);?>
							<br/>[/jbp-job-search]
						</code>
						<span class="description"><?php printf( esc_html__( 'Displays search form for %s search.', JBP_TEXT_DOMAIN ), $this->job_labels->name) ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php printf( esc_html__('%s Search Form:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
					<td>
						<code>
							[jbp-pro-search text="<?php printf( esc_html__('Search %s for', JBP_TEXT_DOMAIN), $this->pro_labels->name);?>" view="loggedin | loggedout | both" class="some class"]
						</code>
						or
						<code>
							[jbp-pro-search view="loggedin | loggedout | both" class="some class"]
							<br/>&lt;img src="<?php _e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_html__('Search %s for', JBP_TEXT_DOMAIN), $this->pro_labels->name);?>
							<br/>[/jbp-pro-search]
						</code>
						<span class="description"><?php printf( esc_html__( 'Displays search form for %s search.', JBP_TEXT_DOMAIN ), $this->pro_labels->name) ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e('Rating stars:', JBP_TEXT_DOMAIN ); ?></th>
					<td>
						<code>
							[jbp-ratings post="post_id"  view="loggedin | loggedout | both" class="some class"]
						</code>
						<span class="description"><?php esc_html_e( 'Displays ratings stars for the owner of "post". If "post" is not used it assumes the current global $post object.', JBP_TEXT_DOMAIN ); ?></span>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e('Rate this:', JBP_TEXT_DOMAIN ); ?></th>
					<td>
						<code>
							[jbp-rate-this post="post_id"  resetable=" true | false" view="loggedin | loggedout | both" class="some class"]
						</code>
						<span class="description"><?php esc_html_e( 'Allows input of stars rating for the owner of "post". If "post" is not used it assumes the current global $post object. "resetable" adds a button that allows clearing all stars.', JBP_TEXT_DOMAIN ); ?></span>
					</td>
				</tr>

			</table>
		</div>
	</div>
</div>
