<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed!' );
}
/**
 * @package Jobs +
 * @author  Hoang Ngo
 * @since   version 1.0
 * @license GPL2+
 */

?>

<?php
$tab_url = 'edit.php?post_type=' . $_GET['post_type'] . '&page=' . $_GET['page'] . '&tab=' . $_GET['tab'];
?>
<div class="wrap">
	<?php screen_icon( 'jobs-plus' ); ?>
	<h2><?php esc_html_e( 'Page Creation', JBP_TEXT_DOMAIN ); ?></h2>
	<?php $this->render_tabs(); ?>
	<form action="<?php echo $tab_url ?>" method="post">
		<br />

		<div class="postbox">
			<div class="handlediv"><br /></div>
			<h3 class="hndle"><span><?php esc_html_e( 'Page Creation', JBP_TEXT_DOMAIN ) ?></span></h3>

			<div class="inside">
				<table class="form-table">

					<tr>
						<th>
							<label><?php echo esc_html__( 'Add Job', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<?php
							$job_page_id = isset( $_GET['add_job_page_id'] ) ? $_GET['add_job_page_id'] : $this->get_setting( 'pages_define->add_job' ) ?>
							<?php wp_dropdown_pages( array(
								'name'             => 'jbp[pages_define][add_job]',
								'post_type'        => 'jbp_job',
								'post_status'      => 'virtual',
								'show_option_none' => __( 'Select a page', JBP_TEXT_DOMAIN ),
								'selected'         => $job_page_id
							) );?>
							<a href="<?php echo wp_nonce_url( $tab_url . '&action=jbp_create_add_job_page', 'jbp_create_add_job_page' ) ?>" class="button-primary"><?php _e( 'Create Page', JBP_TEXT_DOMAIN ) ?></a><br />
							<?php if ( $this->get_setting( 'pages_define->add_job', false ) !== false ): ?>
								<a target="_blank" href="<?php echo get_permalink( $this->get_setting( 'pages_define->add_job' ) ); ?>"><?php _e( 'view page', JBP_TEXT_DOMAIN ); ?></a> |
								<a target="_blank" href="<?php echo admin_url( 'post.php?post=' . $this->get_setting( 'pages_define->add_job' ) . '&action=edit' ); ?>"><?php _e( 'edit page', JBP_TEXT_DOMAIN ); ?></a>

							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php echo esc_html__( 'Add Expert', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<?php $pro_page_id = isset( $_GET['jbp_add_pro_page_id'] ) ? $_GET['jbp_add_pro_page_id'] : $this->get_setting( 'pages_define->add_pro' ) ?>
							<?php wp_dropdown_pages( array(
								'name'             => 'jbp[pages_define][add_pro]',
								'post_type'        => 'jbp_pro',
								'post_status'      => 'virtual',
								'show_option_none' => __( 'Select a page', JBP_TEXT_DOMAIN ),
								'selected'         => $pro_page_id
							) ) ?>
							<a href="<?php echo wp_nonce_url( $tab_url . '&action=jbp_create_add_pro_page', 'jbp_create_add_pro_page' ) ?>" class="button-primary"><?php _e( 'Create Page', JBP_TEXT_DOMAIN ) ?></a><br />
							<?php if ( $this->get_setting( 'pages_define->add_pro', false ) !== false ): ?>
								<a target="_blank" href="<?php echo get_permalink( $this->get_setting( 'pages_define->add_pro' ) ); ?>"><?php _e( 'view page', JBP_TEXT_DOMAIN ); ?></a> |
								<a target="_blank" href="<?php echo admin_url( 'post.php?post=' . $this->get_setting( 'pages_define->add_pro' ) . '&action=edit' ); ?>"><?php _e( 'edit page', JBP_TEXT_DOMAIN ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th>
							<label><?php echo esc_html__( 'Create Job Board page', JBP_TEXT_DOMAIN ) ?></label>
						</th>
						<td>
							<?php if ( $this->get_setting( 'pages_define->job_board', false ) != false && get_post( $this->get_setting( 'pages_define->job_board' ), OBJECT) instanceof WP_Post ): ?>
								<p><a target="_blank" href="<?php echo admin_url( 'post.php?post=' . $this->get_setting( 'pages_define->job_board' ) . '&action=edit' );  ?>" class="button button-primary"><?php _e('Edit Page',JBP_TEXT_DOMAIN) ?></a> | <a href="<?php echo get_permalink( $this->get_setting( 'pages_define->job_board' ) ); ?>" class="button button-primary"><?php _e('View Page',JBP_TEXT_DOMAIN) ?></a> </p>
							<?php else: ?>
								<p><?php _e( 'Do you want to create a job board page ?', JBP_TEXT_DOMAIN ) ?>
									<a target="_blank" href="<?php echo wp_nonce_url( $tab_url . '&action=jbp_create_full_page', 'jbp_create_full_page' ) ?>" class="button button-primary"><?php _e( 'Yes, do that for me', JBP_TEXT_DOMAIN ) ?></a>
								</p>
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<p class="submit">
			<?php wp_nonce_field( 'jobs-plus-settings' ); ?>
			<input type="hidden" name="jobs-plus-settings" value="1" />
			<input type="submit" class="button-primary" name="general-settings" value="<?php esc_attr_e( 'Save Changes', JBP_TEXT_DOMAIN ); ?>">
		</p>
	</form>
</div>

