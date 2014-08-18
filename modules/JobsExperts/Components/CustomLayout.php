<?php

/**
 * Name: Custom layout
 * Description: Dynamic layout for jobs/experts listing page
 * Author: Hoang Ngo
 */
class JobsExpert_Compnents_CustomLayout extends JobsExperts_Components {
	public function __construct() {
		$this->_add_action( 'jbp_setting_menu', 'menu' );
		$this->_add_action( 'jbp_setting_content', 'content', 10, 2 );
		$this->_add_action( 'jbp_after_save_settings', 'save_setting' );
		$this->_add_filter( 'jbp_jobs_list_layout', 'layout_modify' );
		$this->_add_filter( 'jbp_expert_list_layout', 'expert_layout_modify' );
	}

	function expert_layout_modify( $layouts ) {
		$custom_layout = get_option( 'jbp_experts_custom_layout' );
		if ( ! empty( $custom_layout ) ) {
			$custom_layout = trim( nl2br( $custom_layout ) );
			$custom_layout = explode( '<br />', $custom_layout );
			//filter empty row
			$custom_layout = array_filter( $custom_layout );

			return $custom_layout;
		}

		return $layouts;
	}

	function layout_modify( $layouts ) {
		$custom_layout = get_option( 'jbp_jobs_custom_layout' );
		if ( ! empty( $custom_layout ) ) {
			$custom_layout = trim( nl2br( $custom_layout ) );
			$custom_layout = explode( '<br />', $custom_layout );
			//filter empty row
			$custom_layout = array_filter( $custom_layout );

			return $custom_layout;
		}

		return $layouts;
	}

	function save_setting() {
		if ( isset( $_POST['jobs_custom_layout'] ) ) {
			update_option( 'jbp_jobs_custom_layout', $_POST['jobs_custom_layout'] );
		}
		if ( isset( $_POST['experts_custom_layout'] ) ) {
			update_option( 'jbp_experts_custom_layout', $_POST['experts_custom_layout'] );
		}
	}

	function menu() {
		$plugin = JobsExperts_Plugin::instance();
		?>
		<li <?php echo $this->active_tab( 'custom_layout' ) ?>>
			<a href="<?php echo admin_url( 'edit.php?post_type=jbp_job&page=jobs-plus-menu&tab=custom_layout' ) ?>">
				<i class="dashicons dashicons-align-center"></i> <?php _e( 'Custom Layout', JBP_TEXT_DOMAIN ) ?>
			</a></li>
	<?php
	}

	function content( JobsExperts_Framework_ActiveForm $form, JobsExperts_Core_Models_Settings $model ) {
		if ( $this->is_current_tab( 'custom_layout' ) ) {
			?>
			<div class="page-header" style="margin-top: 0">
				<h3><?php _e( 'Jobs listing page custom layout', JBP_TEXT_DOMAIN ) ?></h3>
			</div>
			<p>We have 4 size:</p>
			<ul>
				<li><strong>lg</strong> <?php _e( 'mean the job block will have 100% width', JBP_TEXT_DOMAIN ) ?></li>
				<li><strong>md</strong> <?php _e( 'mean the job block will have 1/2 width', JBP_TEXT_DOMAIN ) ?></li>
				<li><strong>sm</strong> <?php _e( 'mean the job block will have 1/3 width', JBP_TEXT_DOMAIN ) ?></li>
				<li><strong>xs</strong> <?php _e( 'mean the job block will have 1/4 width', JBP_TEXT_DOMAIN ) ?></li>
			</ul>
			<p><?php _e( 'You can customize the layout, each row should have 100% score, and the default is sm', JBP_TEXT_DOMAIN ) ?></p>
			<p><?php _e( 'Example for default layout', JBP_TEXT_DOMAIN ) ?></p>
			<div class="well well-sm">
				<span>lg</span>

				<div class="clearfix"></div>
				<span>md,md</span>

				<div class="clearfix"></div>
				<span>lg</span>

				<div class="clearfix"></div>
				<span>md,md</span>

				<div class="clearfix"></div>
			</div>
			<?php $result = get_option( 'jbp_jobs_custom_layout' ) != false ? get_option( 'jbp_jobs_custom_layout' ) : null; ?>
			<textarea name="jobs_custom_layout" style="width: 100%" rows="5"><?php echo $result ?></textarea>

			<div class="page-header" style="margin-top: 0">
				<h3><?php _e( 'Expert listing page custom layout', JBP_TEXT_DOMAIN ) ?></h3>
			</div>
			<p>We have 4 size:</p>
			<ul>
				<li><strong>lg</strong> <?php _e( 'mean the expert block will have 100% width', JBP_TEXT_DOMAIN ) ?>
				</li>
				<li><strong>md</strong> <?php _e( 'mean the expert block will have 1/2 width', JBP_TEXT_DOMAIN ) ?></li>
				<li><strong>sm</strong> <?php _e( 'mean the expert block will have 1/3 width', JBP_TEXT_DOMAIN ) ?></li>
				<li><strong>xs</strong> <?php _e( 'mean the expert block will have 1/4 width', JBP_TEXT_DOMAIN ) ?></li>
			</ul>
			<p><?php _e( 'You can customize the layout, each row should have 100% score, and the default is xs', JBP_TEXT_DOMAIN ) ?></p>
			<p><?php _e( 'Example for default layout', JBP_TEXT_DOMAIN ) ?></p>
			<div class="well well-sm">
				<span>sm,sm,sm</span>

				<div class="clearfix"></div>
				<span>sm,sm,sm</span>
			</div>
			<?php $result = get_option( 'jbp_experts_custom_layout' ) != false ? get_option( 'jbp_experts_custom_layout' ) : null;?>
			<textarea name="experts_custom_layout" style="width: 100%" rows="5"><?php echo $result ?></textarea>
		<?php
		}
	}
}

new JobsExpert_Compnents_CustomLayout();