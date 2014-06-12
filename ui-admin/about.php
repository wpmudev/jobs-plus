<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed!' );
}
/**
* @package Jobs +
* @author  Arnold Bailey
* @since   version 1.0
* @license GPL2+
*/

wp_enqueue_style( 'magnific-popup' );
?>
<div class="wrap jbp_started_page">
	<h2><?php esc_html_e( 'Getting Started with Jobs +', JBP_TEXT_DOMAIN ); ?></h2>

	<p class="jbp_light">Thank you! for using our Jobs & Expert plugin</p>

	<p class="jbp_default">This plugin is AWESOME ! It will – Slow-carb VHS 8-bit Thundercats, leggings bitters pickled hoodie vegan disrupt small batch. Wolf Echo Park DIY,
		beard small batch pork belly umami lo-ﬁ deep v asymmetrical Intelligentsia. Retro Echo Park PBR&B VHS farm-to-table Pinterest, Odd Future 3 wolf
		moon street art. 90's actually pour-over messenger bag. Wes Anderson butcher wolf, mixtape lo-ﬁ Intelligentsia messenger bag aesthetic
	skateboard PBR&B hashtag Odd Future vinyl cruciﬁx leggings.</p>

	<p><img src="<?php echo $this->plugin_url . 'img/getting-started.png'; ?>" /></p>

	<div class="jbp_plans">
		<div class="jbp_plan">
			<p class="first"><a href="<?php echo esc_attr( get_post_type_archive_link('jbp_job') ); ?>" class="jbp_button jbp_job_pages"><?php echo esc_html( sprintf(__('%s Page', JBP_TEXT_DOMAIN), $this->job_labels->name) );?></a></p>
			<p><a href="<?php echo esc_attr( admin_url('post.php?post='.$this->job_archive_page_id.'&action=edit') );?>"><?php _e( 'Edit', JBP_TEXT_DOMAIN ) ?></a><?php esc_html_e( ' this pattern', JBP_TEXT_DOMAIN ) ?></p>
			<p><a href="<?php echo esc_attr( get_permalink($this->job_update_page_id) );?>"><?php _e( 'Add', JBP_TEXT_DOMAIN ) ?></a> <?php echo esc_html( sprintf( __('a new %s', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name ) ); ?></p>
		</div>
		<div class="jbp_plan">
			<p class="first"><a href="<?php echo esc_attr( get_permalink($this->demo_landing_page_id) );?>" class="jbp_button jbp_landing_page"><?php esc_html_e('Landing Page',JBP_TEXT_DOMAIN );?></a></p>
			<p><a href="<?php echo esc_attr( admin_url('post.php?post='.$this->demo_landing_page_id.'&action=edit') );?>"><?php esc_html_e('Edit', JBP_TEXT_DOMAIN ); ?></a> <?php esc_html_e( ' this pattern', JBP_TEXT_DOMAIN ) ?></p>
		</div>
		<div class="jbp_plan">
			<p class="first"><a href="<?php echo esc_attr( get_post_type_archive_link('jbp_pro') ); ?>" class="jbp_button jbp_experts_pages"><?php echo esc_html( sprintf(__('%s Page', JBP_TEXT_DOMAIN), $this->pro_labels->name) );?></a></p>
			<p><a href="<?php echo esc_attr( admin_url('post.php?post='.$this->pro_archive_page_id.'&action=edit') ); ?>"><?php _e( 'Edit', JBP_TEXT_DOMAIN ) ?></a> <?php _e( ' this pattern', JBP_TEXT_DOMAIN ) ?></p>
			<p><a href="<?php echo esc_attr( get_permalink($this->pro_update_page_id) ); ?>"><?php esc_html_e( 'Add', JBP_TEXT_DOMAIN ) ?></a> <?php echo esc_html( sprintf( __('a new %s', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name ) ); ?> </p>
		</div>
	</div>
</div>
