<?php
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/

global $post, $wp_query;

$author = $post->post_author;
$status_div = '';

?>

<?php switch($size): case 'small': ?>

<div class="pro-archive pro-small" data-permalink="<?php esc_attr_e(get_permalink(get_the_ID())); ?>" >
	<div class="pro-archive-image">
		<?php echo get_avatar_or_gravatar($post->post_author, do_shortcode('[ct id="_ct_jbp_pro_Contact_Email"]'), 80); ?>
	</div>
	<div class="pro-more">
		<div><?php _e('Find Out More', JBP_TEXT_DOMAIN); ?></div>
	</div>
</div>

<?php break; ?>
<?php case 'medium': ?>

<div class="pro-archive pro-medium" data-permalink="<?php esc_attr_e(get_permalink(get_the_ID())); ?>" >
	<div class="pro-archive-image">
		<?php if( is_certified( $post->post_author ) ): ?>
		<div class="jbp-certify"><?php echo $this->get_setting('general->certification', ''); ?></div>
		<?php endif; ?>
		<?php echo get_avatar_or_gravatar($post->post_author, do_shortcode('[ct id="_ct_jbp_pro_Contact_Email"]'), 160); ?>
	</div>

	<div class="pro-more">
		<div class="">
			<div class="pro-archive-title"><?php the_title(); ?></div>
			<?php echo $status_div; ?>
		</div>
		<div class="pro-stats">
			<ul>
				<li class="pro-archive-rating"><?php the_rating(); ?></li>

				<?php if($this->is_certified($author) ): ?>
				<li class="pro-archive-certify"><?php echo $this->get_setting('general->certification'); ?></li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="pro-archive-skills">
			<?php
			$skills = do_shortcode('[ct id="_ct_jbp_pro_Skills"]');
			$skills = empty($skills) ? new stdClass : json_decode($skills);
			$skill =current(get_object_vars($skills) );
			?>
			<?php if( !empty($skill->skill) ): ?>
			<ul>
				<li class="skill-item">
					<div class="skill-bar"><div class="skill-percent" style="width: <?php echo $skill->percent;?>%"></div></div>
					<div><span><?php echo $skill->skill; ?></span></div>
				</li>
			</ul>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php break; ?>
<?php default: ?>

<div class="pro-archive pro-large" data-permalink="<?php esc_attr_e(get_permalink(get_the_ID())); ?>" >
	<div class="pro-archive-image">
		<?php if( is_certified( $post->post_author ) ): ?>
		<div class="jbp-certify"><?php echo $this->get_setting('general->certification', ''); ?></div>
		<?php endif; ?>
		<?php echo get_avatar_or_gravatar($post->post_author, do_shortcode('[ct id="_ct_jbp_pro_Contact_Email"]'), 160); ?>
	</div>

	<div class="pro-archive-title"><?php the_title() ?></div>
	<div class="pro-archive-stats">
		<ul>
			<li class="pro-archive-rating"><?php the_rating(); ?></li>
		</ul>
	</div>

	<div class="pro-archive-skills">
		<?php
		$skills = do_shortcode('[ct id="_ct_jbp_pro_Skills"]');
		$skills = empty($skills) ? new stdClass : (object)json_decode($skills);
		//Add blanks on the end if not enough empties
		$max_skills = 4;
		$count = 0;
		?>
		<ul>
			<?php foreach ( $skills as $key => $skill) : ?>
			<li >
				<div class="skill-bar"><div class="skill-percent" style="width: <?php echo $skill->percent;?>%"></div></div>
				<div><span><?php echo $skill->skill; ?></span></div>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<?php break; ?>
<?php endswitch; ?>
