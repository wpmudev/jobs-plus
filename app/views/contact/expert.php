<div class="ig-container">
	<div class="jobs-contact">
		<ol class="breadcrumb">
			<li><a href="<?php echo home_url() ?>"><?php _e( 'Home', je()->domain ) ?></a></li>
			<li>
				<a href="<?php echo get_post_type_archive_link( get_post_type() ) ?>"><?php _e( "Experts", je()->domain ) ?></a>
			</li>
			<li>
				<a href="<?php echo get_permalink( $model->id ) ?>"><?php echo get_the_title( $model->id ) ?></a>
			</li>
			<li class="active">Contact</li>
		</ol>
		<?php if ( je()->settings()->expert_contact_form == 1 ): ?>
			<?php _e( "This feature has been disabled by the admin.", je()->domain ) ?>
		<?php else: ?>
			<?php if ( isset( $_GET['status'] ) ): ?>
				<?php if ( $_GET['status'] == 'success' ): ?>
					<div class="alert alert-success">
						<strong><?php echo esc_html( $a['success_text'] ) ?></strong>
					</div>
				<?php else: ?>
					<div class="alert alert-danger">
						<strong><?php echo esc_html( $a['error_text'] ) ?></strong>
					</div>
				<?php endif; ?>
			<?php else: ?>
				<?php $form = new IG_Active_Form( $contact );
				$form->open( array( "attributes" => array( "class" => "form-horizontal" ) ) ); ?>
				<input type="hidden" name="id" value="<?php echo $model->id ?>">
				<div class="form-group <?php echo $model->has_error( "name" ) ? "has-error" : null ?>">
					<?php $form->label( "name", array(
						"text"       => __( "Your Name:", je()->domain ),
						"attributes" => array( "class" => "col-lg-3 control-label" )
					) ) ?>
					<div class="col-lg-9">
						<?php $form->text( "name", array( "attributes" => array( "class" => "form-control" ) ) ) ?>
						<span class="help-block m-b-none error-name"><?php $form->error( "name" ) ?></span>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group <?php echo $model->has_error( "email" ) ? "has-error" : null ?>">
					<?php $form->label( "email", array(
						"text"       => __( "Contact email:", je()->domain ),
						"attributes" => array( "class" => "col-lg-3 control-label" )
					) ) ?>
					<div class="col-lg-9">
						<?php $form->text( "email", array( "attributes" => array( "class" => "form-control" ) ) ) ?>
						<span class="help-block m-b-none error-email"><?php $form->error( "email" ) ?></span>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="form-group <?php echo $model->has_error( "content" ) ? "has-error" : null ?>">
					<?php $form->label( "content", array(
						"text"       => __( "Content", je()->domain ),
						"attributes" => array( "class" => "col-lg-3 control-label" )
					) ) ?>
					<div class="col-lg-9">
						<?php $form->text_area( "content", array(
							"attributes" => array(
								"class" => "form-control",
								"style" => "height:150px"
							)
						) ) ?>
						<span class="help-block m-b-none error-content"><?php $form->error( "content" ) ?></span>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php wp_nonce_field( 'expert_contact' ) ?>
				<div class="form-group">
					<div class="col-lg-9 col-lg-offset-3">
						<button type="submit" name="contact_expert" class="btn btn-primary">
							<i class="fa fa-envelope"> <?php _e( "Send Message", je()->domain ) ?></i>
						</button>
					</div>
				</div>
				<?php $form->close(); ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>