<?php
/**
* @package Jobs +
* @author Arnold Bailey
* @since version 1.0
* @license GPL2+
*/
?>

<div class="jbp-register">
	<form class="jbp-register-form" id="jbp-register-form" role="form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST">
		<h1><?php echo esc_html( bloginfo('blogname')); ?></h1>

		<h2><?php esc_html_e('Login with your Username and Password', JBP_TEXT_DOMAIN); ?></h2>
		<div class="editfield">
			<label for="lr[user_login]"><?php esc_html_e('Username', JBP_TEXT_DOMAIN); ?></label>
			<input type="text" name="lr[user_login]" id="lr[user_login]" value="" class="required" placeholder="<?php esc_attr_e('Username', JBP_TEXT_DOMAIN); ?>" />
		</div>

		<div class="editfield">
			<label for="lr[user_password]"><?php esc_html_e('Password', JBP_TEXT_DOMAIN); ?></label>
			<input type="password" name="lr[user_password]" id="lr[user_password]" value="" class="required" placeholder="<?php esc_attr_e('Password', JBP_TEXT_DOMAIN); ?>" />
		</div>

		<div class="editfield">
			<label for="lr[remember]">
				<input type="checkbox" name="lr[remember]" id="lr[remember]" value="1" placeholder="<?php esc_attr_e('Remember Me', JBP_TEXT_DOMAIN); ?>" />
			<?php esc_html_e('remember me', JBP_TEXT_DOMAIN); ?></label>
		</div>

		<h2><?php esc_html_e('or if Registering, please enter all fields.', JBP_TEXT_DOMAIN); ?></h2>

		<div class="editfield">
			<label for="lr[user_email]"><?php esc_html_e('Email', JBP_TEXT_DOMAIN); ?></label>
			<input type="text" name="lr[user_email]" id="lr[user_email]" value="" class="email if-reg" placeholder="<?php esc_attr_e('Your Email', JBP_TEXT_DOMAIN); ?>" />
		</div>

		<?php if( $this->get_setting('general->first_name', 0) ): ?>
		<div class="editfield">
			<label for="lr[first_name]"><?php esc_html_e('First Name', JBP_TEXT_DOMAIN); ?></label>
			<input type="text" name="lr[first_name]" id="lr[first_name]" value="" class="if-reg " placeholder="<?php esc_attr_e('Your First Name', JBP_TEXT_DOMAIN); ?>" />
		</div>
		<?php endif; ?>
		
		
		<?php if( $this->get_setting('general->last_name', 0) ): ?>
		<div class="editfield">
			<label for="lr[last_name]"><?php esc_html_e('Last Name', JBP_TEXT_DOMAIN); ?></label>
			<input type="text" name="lr[last_name]" id="lr[last_name]" value="" class="if-reg " placeholder="<?php esc_attr_e('Your Last Name', JBP_TEXT_DOMAIN); ?>" />
		</div>
		<?php endif; ?>

		<?php if( $this->get_setting('general->display_name', 0) ): ?>
		<div class="editfield">
			<label for="lr[display_name]"><?php esc_html_e('Display Name', JBP_TEXT_DOMAIN); ?></label>
			<input type="text" name="lr[display_name]" id="jbp_name" value="" class="if-reg " placeholder="<?php esc_attr_e('Your Display Name', JBP_TEXT_DOMAIN); ?>" />
		</div>
		<?php endif; ?>

		<?php if( $this->get_setting('general->user_url', 0) ): ?>
		<div class="editfield">
			<label for="lr[user_url]"><?php esc_html_e('Website', JBP_TEXT_DOMAIN); ?></label>
			<input type="text" name="lr[user_url]" id="lr[display_name]" value="" class="url if-reg" placeholder="<?php esc_attr_e('Your Website', JBP_TEXT_DOMAIN); ?>" />
		</div>
		<?php endif; ?>

		<?php if( $this->get_setting('general->nickname', 0) ): ?>
		<div class="editfield">
			<label for="lr[nickname]"><?php esc_html_e('Nickname', JBP_TEXT_DOMAIN); ?></label>
			<input type="text" name="lr[nickname]" id="lr[nickname]" value="" class="if-reg " placeholder="<?php esc_attr_e('Your Nickname', JBP_TEXT_DOMAIN); ?>" />
		</div>
		<?php endif; ?>

		<?php if( $this->get_setting('general->description', 0) ): ?>
		<div class="editfield">
			<label for="lr[description]"><?php esc_html_e('Biography', JBP_TEXT_DOMAIN); ?></label>
			<textarea name="lr[description]" id="lr[description]" class="if-reg " placeholder="<?php esc_attr_e('Something about You', JBP_TEXT_DOMAIN); ?>" ></textarea>
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

		<div class="jbp-register-buttons">
			<span class="jbp-login-btn"><button type="submit" name="jbp-login-btn" value="1" class="jbp-button jbp-login-btn" id="jbp-login-btn" ><?php esc_html_e('Login', JBP_TEXT_DOMAIN); ?></button></span>
			<span class="jbp-register-btn"><button type="submit" name="jbp-register-btn" value="1" class="jbp-button jbp-register-btn" id="jbp-register-btn" ><?php esc_html_e('Register', JBP_TEXT_DOMAIN); ?></button></span>
		</div>
	</form>

	<div class="alert result-message"></div>
</div>

<script type="text/javascript">
	jQuery(document).ready( function($){

		$('#jbp-register-form').ajaxForm({
			target: '.result-message',
			dataType: 'json',
			beforeSubmit : function() { return $('#jbp-register-form').validate().form(); },
			success: function( data ){
				console.log( data.message );
				$('.result-message').html(data.message);
				if(data.status == 'success') setTimeout( function() { location.reload( true ) }, 1000);
			}
		});

		$('#jbp-register-form').validate();
		$('#jbp-register-btn').click( function(){ $('.if-reg').each( function(){ $(this).rules('add', {required: true})} ); });
		$('#jbp-login-btn').click( function(){ $('.if-reg').each( function(){ $(this).rules('remove', 'required')} ); });

	});
</script>
