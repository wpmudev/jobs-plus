<?php if (!defined('ABSPATH')) die('No direct access allowed!');
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/
?>
<div class="wrap">
	<?php $this->render_tabs('shortcodes'); ?>
	<h2><?php esc_html_e('Jobs+ Shortcode', JBP_TEXT_DOMAIN);?></h2>
	<br />

	<div class="postbox">
		<h3 class='hndle'><span><?php esc_html_e( 'Jobs+ Shortcodes', JBP_TEXT_DOMAIN ) ?></span></h3>
		<div class="inside">
			<p>
				<?php esc_html_e( 'Shortcodes allow you to include dynamic store content in posts and pages on your site. Simply type or paste them into your post or page content where you would like them to appear. Optional attributes can be added in a format like <em>[shortcode attr1="value" attr2="value"]</em>.', JBP_TEXT_DOMAIN ) ?>
			</p>
			<p><strong>
				<?php esc_html_e( 'Attributes: ("|" means use one OR the other. ie view="loggedin" or style="loggedout" NOT style="loggedin|loggedout")', JBP_TEXT_DOMAIN); ?>
				<br /><?php esc_html_e( 'text = <em>Text to display on a button</em>', JBP_TEXT_DOMAIN ) ?>
				<br /><?php esc_html_e( 'view = <em>Whether the button is visible when loggedin, loggedout, or both</em>', JBP_TEXT_DOMAIN ) ?>
				<br /><?php esc_html_e( 'class = <em>define a css class for this button.</em>', JBP_TEXT_DOMAIN ) ?>
			</strong>
		</p>
		<table class="form-table">
			<tr>
				<th colspan=2"><h2 style="text-align: center;"><?php echo esc_html( sprintf(__('%s Shortcodes', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name) ); ?></h2></th>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Profile Button', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name); ?></th>
				<td>
					<code>
						[jbp-expert-profile-btn text="<?php esc_html_e('My Profile', JBP_TEXT_DOMAIN);?>" view="loggedin|loggedout|both" class="some class" img="true|false"]
					</code>
					<br/>or<br/>
					<code>
						[jbp-expert-profile-btn view="loggedin|loggedout|both" class="some class" img="true|false"]
						<br/>&lt;img src="<?php esc_attr_e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php esc_html_e('My Profile', JBP_TEXT_DOMAIN );?>
						<br/>[/jbp-expert-profile-btn]
					</code>
					<br/><span class="description"><?php esc_html_e('Links to the current user\'s profiles. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('Post %s Button', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name);?></th>
				<td>
					<code>
						[jbp-expert-post-btn text="<?php printf( esc_html__('Post an %s', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name);?>" view="loggedin|loggedout|both" class="some class" img="true|false"]
					</code>
					<br/>or<br/>
					<code>
						[jbp-expert-post-btn view="loggedin|loggedout|both" class="some class" img="true|false"]
						<br/>&lt;img src="<?php esc_attr_e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_html__('Post an %s', JBP_TEXT_DOMAIN), $this->pro_labels->singular_name);?>
						<br/>[/jbp-expert-post-btn]
					</code>
					<br/><span class="description"><?php printf(esc_html__( 'Links to the Post %s Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name); ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__( '%s Archive Button:', JBP_TEXT_DOMAIN ), $this->pro_labels->name) ?></th>
				<td>
					<code>
						[jbp-expert-browse-btn text="<?php printf( esc_html__('Browse %s', JBP_TEXT_DOMAIN), $this->pro_labels->name);?>" view="loggedin|loggedout|both" class="some class" img="true|false"]
					</code>
					<br/>or<br/>
					<code>
						[jbp-expert-browse-btn view="loggedin|loggedout|both" class="some class" img="true|false"]
						<br/>&lt;img src="<?php esc_attr_e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_html__('Browse %s', JBP_TEXT_DOMAIN), $this->pro_labels->name);?>
						<br/>[/jbp-expert-browse-btn]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Links to the %s Archive Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__( '%s Contact Button:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
				<td>
					<code>
						[jbp-expert-contact-btn text="<?php esc_html_e('Contact', JBP_TEXT_DOMAIN);?>" post="post_id" view="loggedin|loggedout|both" class="some class"]
					</code>
					<br/>or<br/>
					<code>
						[jbp-expert-contact-btn  post="post_id" view="loggedin|loggedout|both" class="some class"]
						<br/>&lt;img src="<?php esc_attr_e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php esc_html_e('Contact', JBP_TEXT_DOMAIN);?>
						<br/>[/jbp-expert-contact-btn]
					</code>
					<br/><span class="description"><?php printf(esc_html__( 'Links to the %s email contact Page. If "post" is not used it defaults to the current global $post object. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name ); ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Search Form:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
				<td>
					<code>
						[jbp-expert-search text="<?php printf( esc_html__('Search %s for', JBP_TEXT_DOMAIN), $this->pro_labels->name);?>" view="loggedin|loggedout|both" class="some class"]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays search form for %s search.', JBP_TEXT_DOMAIN ), $this->pro_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Poster:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
				<td>
					<code>
						[jbp-expert-poster title="<?php printf(__('Our %s', $this->text_domain),$this->pro_labels->name);?>" legend="<?php printf(__('Become an %s', $this->text_domain),$this->pro_labels->singular_name);?>" link="<?php printf(__('Browse %s...', $this->text_domain),$this->pro_labels->name);?>" view="loggedin|loggedout|both" class="some class"]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays a group of random %s.', JBP_TEXT_DOMAIN ), $this->pro_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e('Rating stars:', JBP_TEXT_DOMAIN ); ?></th>
				<td>
					<code>
						[jbp-rating post="post_id"  view="loggedin|loggedout|both" class="some class"]
					</code>
					<br/><span class="description"><?php esc_html_e( 'Displays rating stars for the owner of "post". If "post" is not used it assumes the current global $post object.', JBP_TEXT_DOMAIN ); ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e('Rate this:', JBP_TEXT_DOMAIN ); ?></th>
				<td>
					<code>
						[jbp-rate-this post="post_id"  resetable=" true|false" view="loggedin|loggedout|both" class="some class"]
					</code>
					<br/><span class="description"><?php esc_html_e( 'Allows input of stars rating for the owner of "post". If "post" is not used it assumes the current global $post object. "resetable" adds a button that allows clearing all stars.', JBP_TEXT_DOMAIN ); ?></span>
				</td>
			</tr>


			<!--JOBS SHORTCODES-->
			<tr>
				<th colspan=2"><h2 style="text-align: center;"><?php echo esc_html( sprintf(__('%s Shortcodes', JBP_TEXT_DOMAIN), $this->job_labels->singular_name) ); ?></h2></th>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('Post %s Button', JBP_TEXT_DOMAIN), $this->job_labels->singular_name);?></th>
				<td>
					<code>
						[jbp-job-post-btn text="<?php printf( esc_html__('Post a %s', JBP_TEXT_DOMAIN), $this->job_labels->singular_name);?>" view="loggedin|loggedout|both" class="some class" img="true|false"]
					</code>
					<br/>or<br/>
					<code>
						[jbp-job-post-btn view="loggedin|loggedout|both" class="some class" img="true|false"]
						<br/>&lt;img src="<?php esc_attr_e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_html__('Post a %s', JBP_TEXT_DOMAIN), $this->job_labels->singular_name);?>
						<br/>[/jbp-job-post-btn]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Links to the Post %s Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__( '%s Archive Button:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
				<td>
					<code>
						[jbp-job-browse-btn text="<?php printf( esc_html__('Browse %s', JBP_TEXT_DOMAIN), $this->job_labels->name);?>" view="loggedin|loggedout|both" class="some class" img="true|false"]
					</code>
					<br/>or<br/>
					<code>
						[jbp-job-browse-btn view="loggedin|loggedout|both" class="some class" img="true|false"]
						<br/>&lt;img src="<?php esc_html_e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php printf( esc_html__('Browse %s', JBP_TEXT_DOMAIN), $this->job_labels->name);?>
						<br/>[/jbp-job-browse-btn]
					</code>
					<br/><span class="description"><?php printf(esc_html__( 'Links to the %s Archive Page. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name ); ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__( '%s Contact Button:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
				<td>
					<code>
						[jbp-expert-contact-btn text="<?php esc_html_e('Contact', JBP_TEXT_DOMAIN);?>" post="post_id" view="loggedin|loggedout|both" class="some class"]
					</code>
					<br/>or<br/>
					<code>
						[jbp-job-contact-btn  post="post_id" view="loggedin|loggedout|both" class="some class"]
						<br/>&lt;img src="<?php esc_html_e('someimage.jpg', JBP_TEXT_DOMAIN); ?>" /&gt;<?php esc_html_e('Contact', JBP_TEXT_DOMAIN);?>
						<br/>[/jbp-job-contact-btn]
					</code>
					<br/><span class="description"><?php printf(esc_html__( 'Links to the %s Email Contact Page. If "post" is not used it defaults to the current global $post object. Generates a &lt;button&gt; &lt;/button&gt; with the HTML contents you define.', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name ); ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Search Form:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
				<td>
					<code>
						[jbp-job-search text="<?php printf( esc_html__('Search %s for', JBP_TEXT_DOMAIN), $this->job_labels->name);?>" view="loggedin|loggedout|both" class="some class"]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays search form for %s search.', JBP_TEXT_DOMAIN ), $this->job_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Poster:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
				<td>
					<code>
						[jbp-job-poster title="<?php printf( esc_html__('Recently Posted %s', JBP_TEXT_DOMAIN), $this->job_labels->name);?>" legend="<?php printf(__('Post a %s', $this->text_domain),$this->job_labels->singular_name);?>" link="<?php printf(__('Browse More %s...', $this->text_domain),$this->job_labels->name);?>" count="3" view="loggedin|loggedout|both" class="some class"]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays a group of recent %s.', JBP_TEXT_DOMAIN ), $this->job_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php esc_html_e('Landing Page:', JBP_TEXT_DOMAIN ); ?></th>
				<td>
					<code>
						[jbp-landing-page view="loggedin|loggedout|both" class="some class"]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays a combination of %s and %s posters above.', JBP_TEXT_DOMAIN ), $this->job_labels->name, $this->pro_labels->name) ?></span>
				</td>
			</tr>
			
			<tr>
				<th colspan=2"><h2 style="text-align: center;"><?php echo esc_html( sprintf(__('Pattern Page Shortcodes', JBP_TEXT_DOMAIN), $this->job_labels->singular_name) ); ?></h2></th>
			</tr>
			
			<tr>
				<th scope="row"><?php printf( esc_html__('%s Archive Page:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
				<td>
					<code>
						[jbp-job-archive-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays the %s archive list. Only works in the %s Archive pattern page. ', JBP_TEXT_DOMAIN ), $this->job_labels->name, $this->job_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Taxonomy Page:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
				<td>
					<code>
						[jbp-job-taxonomy-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays the %s jbp_category and jbp_tags lists. Only works in the %s Taxononomy pattern page. ', JBP_TEXT_DOMAIN ), $this->job_labels->name, $this->job_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Contact Page:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
				<td>
					<code>
						[jbp-job-contact-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays the %s Contact page. Only works in the %s Contact pattern page. ', JBP_TEXT_DOMAIN ), $this->job_labels->name, $this->job_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Search Page:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
				<td>
					<code>
						[jbp-job-search-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays the %s Search results page. Only works in the %s Search pattern page. ', JBP_TEXT_DOMAIN ), $this->job_labels->name, $this->job_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Single Page:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
				<td>
					<code>
						[jbp-job-single-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays a single %s listing page. Only works in the %s Single pattern page. ', JBP_TEXT_DOMAIN ), $this->job_labels->name, $this->job_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Update Page:', JBP_TEXT_DOMAIN ), $this->job_labels->name); ?></th>
				<td>
					<code>
						[jbp-job-update-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays the %s Add and Update page. Only works in the %s Update pattern page. ', JBP_TEXT_DOMAIN ), $this->job_labels->name, $this->job_labels->name) ?></span>
				</td>
			</tr>


			<tr>
				<th scope="row"><?php printf( esc_html__('%s Archive Page:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
				<td>
					<code>
						[jbp-expert-archive-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays the %s archive list. Only works in the %s Archive pattern page. ', JBP_TEXT_DOMAIN ), $this->pro_labels->name, $this->pro_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Taxonomy Page:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
				<td>
					<code>
						[jbp-expert-taxonomy-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays the %s jbp_category and jbp_tags lists. Only works in the %s Taxononomy pattern page. ', JBP_TEXT_DOMAIN ), $this->pro_labels->name, $this->pro_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Contact Page:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
				<td>
					<code>
						[jbp-expert-contact-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays the %s Contact page. Only works in the %s Contact pattern page. ', JBP_TEXT_DOMAIN ), $this->pro_labels->name, $this->pro_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Search Page:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
				<td>
					<code>
						[jbp-expert-search-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays the %s Search results page. Only works in the %s Search pattern page. ', JBP_TEXT_DOMAIN ), $this->pro_labels->name, $this->pro_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Single Page:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
				<td>
					<code>
						[jbp-expert-single-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays a single %s listing page. Only works in the %s Single pattern page. ', JBP_TEXT_DOMAIN ), $this->pro_labels->name, $this->pro_labels->name) ?></span>
				</td>
			</tr>

			<tr>
				<th scope="row"><?php printf( esc_html__('%s Update Page:', JBP_TEXT_DOMAIN ), $this->pro_labels->name); ?></th>
				<td>
					<code>
						[jbp-expert-update-page]
					</code>
					<br/><span class="description"><?php printf( esc_html__( 'Displays the %s Add and Update page. Only works in the %s Update pattern page. ', JBP_TEXT_DOMAIN ), $this->pro_labels->name, $this->pro_labels->name) ?></span>
				</td>
			</tr>


		</table>
	</div>
</div>
</div>
