<?php
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/
?>

<div class="jbp-register">
	<div class="header">
		<h1><?php echo esc_html( bloginfo('blogname')); ?></h1>
	</div>

	<form class="jbp-register-form" id="jbp-login-form" role="form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST">

		<p><?php esc_html_e('Log in', JBP_TEXT_DOMAIN); ?></p>

		<div class="editfield">
			<input type="text" name="lr[user_login]" id="lr[user_login]" value="" class="required" placeholder="<?php esc_attr_e('Username/Email', JBP_TEXT_DOMAIN); ?>" />
		</div>

		<div class="editfield">
			<input type="password" name="lr[user_password]" id="lr[user_password]" value="" class="required" placeholder="<?php esc_attr_e('Password', JBP_TEXT_DOMAIN); ?>" />
		</div>

		<div class="editfield left group ">
			<label for="lr[remember]">
				<input type="checkbox" name="lr[remember]" id="lr[remember]" value="1" placeholder="<?php esc_attr_e('Remember Me', JBP_TEXT_DOMAIN); ?>" />
				<?php esc_html_e(' remember me', JBP_TEXT_DOMAIN); ?>
			</label>
		</div>

		<input type="hidden" name="action" value="jbp-register" />
		<?php wp_nonce_field('jbp-register', '_wpnonce', false ); ?>

		<div class="jbp-register-buttons left group">
			<span class="jbp-login-btn"><button type="submit" id="jbp-login-btn" name="jbp-login-btn" value="1" class="jbp-button jbp-login-btn" ><?php esc_html_e('Log In', JBP_TEXT_DOMAIN); ?></button></span>
			<span class="jbp-login-link left">Forgot your <a href="<?php echo wp_lostpassword_url(); ?>" >password</a><br/>Create a <a id="to-register" href="" >new account</a></span>
		</div>

	<div class="alert result-message"></div>

	</form>

	<form class="jbp-register-form" id="jbp-register-form" role="form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST">

		<p><?php esc_html_e('Create an Account', JBP_TEXT_DOMAIN); ?></p>

		<div class="editfield">
			<input type="text" name="lr[user_login]" id="lr[user_login]" value="" class="required" placeholder="<?php esc_attr_e('Username', JBP_TEXT_DOMAIN); ?>" />
		</div>

		<div class="editfield">
			<input type="text" name="lr[user_email]" id="lr[user_email]" value="" class="email required" placeholder="<?php esc_attr_e('Your Email', JBP_TEXT_DOMAIN); ?>" />
		</div>

		<div class="editfield">
			<input type="password" name="lr[user_password]" id="lr[user_password]" value="" class="required" placeholder="<?php esc_attr_e('Password', JBP_TEXT_DOMAIN); ?>" />
		</div>

		<?php if( $this->get_setting('general->first_name', 0) ): ?>
		<div class="editfield">
			<input type="text" name="lr[first_name]" id="lr[first_name]" value="" class="required " placeholder="<?php esc_attr_e('Your First Name', JBP_TEXT_DOMAIN); ?>" />
		</div>
		<?php endif; ?>


		<?php if( $this->get_setting('general->last_name', 0) ): ?>
		<div class="editfield">
			<input type="text" name="lr[last_name]" id="lr[last_name]" value="" class="required " placeholder="<?php esc_attr_e('Your Last Name', JBP_TEXT_DOMAIN); ?>" />
		</div>
		<?php endif; ?>

		<?php if( $this->get_setting('general->display_name', 0) ): ?>
		<div class="editfield">
			<input type="text" name="lr[display_name]" id="jbp_name" value="" class="required " placeholder="<?php esc_attr_e('Your Display Name', JBP_TEXT_DOMAIN); ?>" />
		</div>
		<?php endif; ?>

		<?php if( $this->get_setting('general->user_url', 0) ): ?>
		<div class="editfield">
			<input type="text" name="lr[user_url]" id="lr[display_name]" value="" class="url required" placeholder="<?php esc_attr_e('Your Website', JBP_TEXT_DOMAIN); ?>" />
		</div>
		<?php endif; ?>

		<?php if( $this->get_setting('general->nickname', 0) ): ?>
		<div class="editfield">
			<input type="text" name="lr[nickname]" id="lr[nickname]" value="" class="required " placeholder="<?php esc_attr_e('Your Nickname', JBP_TEXT_DOMAIN); ?>" />
		</div>
		<?php endif; ?>

		<?php if( $this->get_setting('general->description', 0) ): ?>
		<div class="editfield">
			<textarea name="lr[description]" id="lr[description]" class="required " placeholder="<?php esc_attr_e('Something about You', JBP_TEXT_DOMAIN); ?>" ></textarea>
		</div>
		<?php endif; ?>

		<?php if( $this->get_setting('general->use_register_captcha', 0) ): ?>
		<div class="editfield">
			<label for="jbp_random_value"><?php esc_attr_e( 'Security image (required)', JBP_TEXT_DOMAIN ); ?></label>
			<p>
				<span class="captcha"><img src="<?php echo esc_attr(admin_url('admin-ajax.php?action=jbp-captcha') );?>" /></span>
				<span><input type="text" class="required" id="jbp_random_value" name ="jbp_random_value" value="" size="8" /></span>
				<br/><span class="description"><?php esc_html_e( 'Enter the characters from the image.', JBP_TEXT_DOMAIN ); ?></span>
			</p>
		</div>
		<?php endif;?>

		<input type="hidden" name="action" value="jbp-register" />
		<?php wp_nonce_field('jbp-register', '_wpnonce', false ); ?>

		<div class="jbp-register-buttons left group">
			<span class="jbp-login-btn"><button type="submit" id="jbp-login-btn" name="jbp-login-btn" value="1" class="jbp-button jbp-login-btn"  ><?php esc_html_e('Create Account', JBP_TEXT_DOMAIN); ?></button></span>
			<span class="jbp-login-link left"><br/>Already a member <a id="to-login" href="" >sign in</a></span>
		</div>
	<div class="alert result-message"></div>
	</form>

</div>

<script type="text/javascript">
	jQuery(document).ready( function($){

		var foptions = {
			dataType: 'json',
			beforeSubmit : function( data, form) { return form.validate().form(); },
			success: function( data ){
				$('.result-message').html(data.message);
				if(data.status == 'success') setTimeout( function() { location.reload( true ) }, 1000);
			}
		}

		var voptions = {
			errorPlacement: function(error, element) { /* No error label just highlight */ },
			highlight: function(element, errorClass, validClass) {
				$(element).addClass(errorClass).removeClass(validClass);
				if( $(element.form).find('.error').length ) $(element.form).find("button").addClass('inactive');
			},
			unhighlight: function(element, errorClass, validClass) {
				$(element).removeClass(errorClass).addClass(validClass);
				if( !$(element.form).find('.error').length ) $(element.form).find("button").removeClass('inactive');
			}
		}

		var $active_form = $('#jbp-login-form');

		$('#jbp-login-form').validate( voptions).form();
		$('#jbp-register-form').validate( voptions);
		
		//Do it twice with a delay so it can recognize browser form fill inserts.
		setTimeout( function(){ $active_form.validate().form(); }, 100 );

		$('#jbp-login-form').ajaxForm( foptions );
		$('#jbp-register-form').ajaxForm( foptions );


		$('#to-register').click( function(e){
			e.preventDefault();
			$active_form.hide();
			$active_form = $('#jbp-register-form');
			$active_form.show();
			setTimeout( function(){ $active_form.validate().form(); }, 100 );
		});

		$('#to-login').click( function(e){
			e.preventDefault();
			$active_form.hide();
			$active_form = $('#jbp-login-form');
			$active_form.show();
			setTimeout( function(){ $active_form.validate().form(); }, 100 );
		});

		$('button.mfp-close').html(' ');
	});
</script>
