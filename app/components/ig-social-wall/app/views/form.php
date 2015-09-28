<div class="ig-container">
	<div class="social-add-form" style="max-width: 430px">
		<div class="alert alert-danger hide">
		</div>
		<div class="row">
			<form method="post" class="social-form">
				<div class="col-md-4 hidden-xs hidden-sm">
					<div class="social-preview">
						<?php if ( is_object( $model ) ): ?>
							<h4><?php echo $social['name'] ?></h4>
							<img src="<?php echo $social['url'] ?>">
						<?php else: ?>
							<h4><?php _e( "Name", ig_social_wall()->domain ) ?></h4>
							<img>
						<?php endif; ?>
					</div>
				</div>
				<div class="col-md-8 col-xs-12 col-sm-12">

					<label><?php _e( 'Select Social', ig_social_wall()->domain ) ?></label>
					<select name="social">
						<?php foreach ( ig_social_wall()->get_social_list() as $val ): ?>
							<option
								value="<?php echo $val['key'] ?>" <?php is_object( $model ) ? selected( $model->name, $val['key'] ) : null ?>>
								<?php echo $val['name'] ?>
							</option>
						<?php endforeach; ?>
					</select>
					<label
						class="note"><?php _e( 'Social information (Url or Username)', ig_skill()->domain ) ?></label>
					<input type="text" name="value" value="<?php echo is_object( $model ) ? $model->value : null ?>">
					<button class="btn btn-primary btn-sm hn-save-social"
					        type="submit"><?php _e( 'Submit', ig_skill()->domain ) ?></button>
					&nbsp;
					<button class="btn btn-default btn-sm hn-cancel-social"
					        type="button"><?php _e( 'Cancel', ig_skill()->domain ) ?></button>
					<?php if ( is_object( $model ) ): ?>
						&nbsp;
						<button class="btn btn-danger btn-sm hn-delete-social"
						        type="button"><?php _e( 'Delete', ig_skill()->domain ) ?></button>
					<?php endif; ?>

				</div>
				<div class="clearfix"></div>
			</form>
		</div>
	</div>
	<div class="ig-overlay hide"
	     style="position: absolute;width: 100%;height:100%;background: white;opacity: 0.5;top:0">
		<img style="
    display: block;
    position: relative;
    margin: auto;
    top: 50%;
    margin-top: -24px;
" src="<?php echo ig_social_wall()->plugin_url . 'assets/ajax-loader.gif' ?>">
	</div>
</div>