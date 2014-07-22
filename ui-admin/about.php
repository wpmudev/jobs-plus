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

//echo $this->demo_landing_page_id;

if ( ! empty( $_GET['create-demo'] ) ) {
	include_once $this->plugin_dir . 'class/class-demo.php';
	$this->notice_message( __( 'Demo Data Created', JBP_TEXT_DOMAIN ) );
}

?>
<div class="wrap jbp_started_page">
	<h2><?php esc_html_e( 'Welcome to Jobs & Experts - Getting Started & Overview Page', JBP_TEXT_DOMAIN ); ?></h2>

	<div style="display: inline-table; width: 20%">

		<p class="jbp_light"><?php esc_html_e( 'Thank you! for using our Jobs & Expert plugin', JBP_TEXT_DOMAIN ) ?></p>

		<p class="jbp_default">
			<?php esc_html_e( 'To get started just create some demo content or browse, edit and add content using the buttons below. You can return to this page at anytime.', JBP_TEXT_DOMAIN ) ?></p>

		<div class="jbp-demo">
			<p class="first">
				<a href="<?php echo esc_attr( 'edit.php?post_type=jbp_job&page=jobs-plus-about&create-demo=true' ); ?>" class="jbp_button"><?php  esc_html_e( 'Create demo Jobs & Experts content', JBP_TEXT_DOMAIN ); ?></a>
			</p>
		</div>

		<?php echo do_action( 'jbp_notice' ); ?>

		<p><img src="<?php echo $this->plugin_url . 'img/getting-started.png'; ?>" /></p>

		<div class="jbp_plans">
			<div class="jbp_plan">
				<p class="first">
					<a href="<?php echo esc_attr( get_post_type_archive_link( 'jbp_job' ) ); ?>" target="jobs" class="jbp_button jbp_job_pages"><?php echo esc_html( sprintf( __( '%s Listings', JBP_TEXT_DOMAIN ), $this->job_labels->name ) ); ?></a>
				</p>

				<p>
					<a href="<?php echo esc_attr( admin_url( 'post.php?post=' . $this->job_archive_page_id . '&action=edit' ) ); ?>"><?php _e( 'Edit', JBP_TEXT_DOMAIN ) ?></a><?php esc_html_e( ' this virtual page', JBP_TEXT_DOMAIN ) ?>
				</p>

				<p>
					<a href="<?php echo esc_attr( get_permalink( $this->job_update_page_id ) ); ?>"><?php _e( 'Add', JBP_TEXT_DOMAIN ) ?></a> <?php echo esc_html( sprintf( __( 'a new %s', JBP_TEXT_DOMAIN ), $this->job_labels->singular_name ) ); ?>
				</p>
			</div>
			<div class="jbp_plan">
				<p class="first">
					<a href="<?php echo esc_attr( get_permalink( $this->demo_landing_page_id ) ); ?>" target="landing" class="jbp_button jbp_landing_page"><?php esc_html_e( 'Jobs & Experts Overview', JBP_TEXT_DOMAIN ); ?></a>
				</p>

				<p>
					<a href="<?php echo esc_attr( admin_url( 'post.php?post=' . $this->demo_landing_page_id . '&action=edit' ) ); ?>"><?php esc_html_e( 'Edit', JBP_TEXT_DOMAIN ); ?></a> <?php esc_html_e( ' this virtual page', JBP_TEXT_DOMAIN ) ?>
				</p>
			</div>
			<div class="jbp_plan">
				<p class="first">
					<a href="<?php echo esc_attr( get_post_type_archive_link( 'jbp_pro' ) ); ?>" target="pros" class="jbp_button jbp_experts_pages"><?php echo esc_html( sprintf( __( '%s Listings', JBP_TEXT_DOMAIN ), $this->pro_labels->name ) ); ?></a>
				</p>

				<p>
					<a href="<?php echo esc_attr( admin_url( 'post.php?post=' . $this->pro_archive_page_id . '&action=edit' ) ); ?>"><?php _e( 'Edit', JBP_TEXT_DOMAIN ) ?></a> <?php _e( ' this virtual page', JBP_TEXT_DOMAIN ) ?>
				</p>

				<p>
					<a href="<?php echo esc_attr( get_permalink( $this->pro_update_page_id ) ); ?>"><?php esc_html_e( 'Add', JBP_TEXT_DOMAIN ) ?></a> <?php echo esc_html( sprintf( __( 'a new %s', JBP_TEXT_DOMAIN ), $this->pro_labels->singular_name ) ); ?>
				</p>
			</div>
		</div>
	</div>

</div>