<?php

/**
 * Author: WPMUDEV
 */
class JobsExperts_Core_Views_Settings_Shortcode extends JobsExperts_Framework_Render
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function _to_html()
    {
        $plugin = JobsExperts_Plugin::instance();
        ?>
        <div class="page-header">
            <h3><?php _e('Buttons Shortcode') ?></h3>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6 col-sm-6 text-center">
                <img class=""
                     src="<?php echo $plugin->_module_url ?>assets/image/icons/Add_an_Expert/Add_an_Experts_Dark.svg">
                <p><strong><?php _e("Become an Expert Button", JBP_TEXT_DOMAIN) ?></strong></p>

                <div class="clearfix"></div>

                <div class="text-left">
                    <p><code>[jbp-expert-post-btn]</code></p>
                    <ul>
                        <li>
                            <mark><?php _e("text", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The text will show below this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("view", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php echo sprintf(__("Scenario for people can view this button, we have %s %s %s", JBP_TEXT_DOMAIN), "<strong>both</strong>", "<strong>loggedin</strong>", "<strong>loggedout</strong>") ?>
                        </li>
                        <li>
                            <mark><?php _e("class", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("Custom Css Class for this button", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("url", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The destination of this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-xs-6 col-sm-6 text-center">
                <img class="" src="<?php echo $plugin->_module_url ?>assets/image/icons/Post_a_Job/Post_a_Job_Dark.svg">
                <p><strong><?php _e("Add new Post Button", JBP_TEXT_DOMAIN) ?></strong></p>
                <div class="clearfix"></div>

                <div class="text-left">
                    <p><code>[jbp-job-post-btn]</code></p>
                    <ul>
                        <li>
                            <mark><?php _e("text", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The text will show below this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("view", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php echo sprintf(__("Scenario for people can view this button, we have %s %s %s", JBP_TEXT_DOMAIN), "<strong>both</strong>", "<strong>loggedin</strong>", "<strong>loggedout</strong>") ?>
                        </li>
                        <li>
                            <mark><?php _e("class", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("Custom Css Class for this button", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("url", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The destination of this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-xs-6 col-sm-6 text-center">
                <img class=""
                     src="<?php echo $plugin->_module_url ?>assets/image/icons/Browse_Jobs/Browse_Jobs_Dark.svg">
                <p><strong><?php _e("Listing Jobs Button", JBP_TEXT_DOMAIN) ?></strong></p>
                <div class="clearfix"></div>

                <div class="text-left">
                    <p><code>[jbp-job-browse-btn]</code></p>
                    <ul>
                        <li>
                            <mark><?php _e("text", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The text will show below this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("view", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php echo sprintf(__("Scenario for people can view this button, we have %s %s %s", JBP_TEXT_DOMAIN), "<strong>both</strong>", "<strong>loggedin</strong>", "<strong>loggedout</strong>") ?>
                        </li>
                        <li>
                            <mark><?php _e("class", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("Custom Css Class for this button", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("url", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The destination of this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-xs-6 col-sm-6 text-center">
                <img class=""
                     src="<?php echo $plugin->_module_url ?>assets/image/icons/Browse_Experts/Browse_Experts_Dark.svg">
                <p><strong><?php _e("Listing Experts Button", JBP_TEXT_DOMAIN) ?></strong></p>
                <div class="clearfix"></div>

                <div class="text-left">
                    <p><code>[jbp-expert-browse-btn]</code></p>
                    <ul>
                        <li>
                            <mark><?php _e("text", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The text will show below this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("view", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php echo sprintf(__("Scenario for people can view this button, we have %s %s %s", JBP_TEXT_DOMAIN), "<strong>both</strong>", "<strong>loggedin</strong>", "<strong>loggedout</strong>") ?>
                        </li>
                        <li>
                            <mark><?php _e("class", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("Custom Css Class for this button", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("url", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The destination of this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-xs-6 col-sm-6 text-center">
                <img class=""
                     src="<?php echo $plugin->_module_url ?>assets/image/icons/My_Job/My_Job_Dark.svg">
                <p><strong><?php _e("Listing My Posted Jobs", JBP_TEXT_DOMAIN) ?></strong></p>
                <div class="clearfix"></div>

                <div class="text-left">
                    <p><code>[jbp-my-job-btn]</code></p>
                    <ul>
                        <li>
                            <mark><?php _e("text", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The text will show below this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("view", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php echo sprintf(__("Scenario for people can view this button, we have %s %s %s", JBP_TEXT_DOMAIN), "<strong>both</strong>", "<strong>loggedin</strong>", "<strong>loggedout</strong>") ?>
                        </li>
                        <li>
                            <mark><?php _e("class", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("Custom Css Class for this button", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("url", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The destination of this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 col-xs-6 col-sm-6 text-center">
                <img class=""
                     src="<?php echo $plugin->_module_url ?>assets/image/icons/My_Profile/My_Profile_Dark.svg">
                <p><strong><?php _e("Listing My Expert's profiles", JBP_TEXT_DOMAIN) ?></strong></p>
                <div class="clearfix"></div>

                <div class="text-left">
                    <p><code>[jbp-expert-profile-btn]</code></p>
                    <ul>
                        <li>
                            <mark><?php _e("text", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The text will show below this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("view", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php echo sprintf(__("Scenario for people can view this button, we have %s %s %s", JBP_TEXT_DOMAIN), "<strong>both</strong>", "<strong>loggedin</strong>", "<strong>loggedout</strong>") ?>
                        </li>
                        <li>
                            <mark><?php _e("class", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("Custom Css Class for this button", JBP_TEXT_DOMAIN) ?>
                        </li>
                        <li>
                            <mark><?php _e("url", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("The destination of this button.", JBP_TEXT_DOMAIN) ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="page-header">
	        <h3><?php _e('Listing Page') ?></h3>
        </div>
        <div class="row">
            <div class="col-md-6 col-xs-6 col-sm-6 text-center">
                <p><strong><?php _e("Job Archive Page", JBP_TEXT_DOMAIN) ?></strong></p>
                <div class="clearfix"></div>

                <div class="text-left">
                    <p><code>[jbp-job-archive-page]</code></p>
                    <ul>
                        <li>
                            <mark><?php _e("post_per_page", JBP_TEXT_DOMAIN) ?></mark>
                            : <?php _e("Number of jobs you want to list on a page. Default is the config from setting page.", JBP_TEXT_DOMAIN) ?>
                        </li>
                    </ul>
                </div>
            </div>
	        <div class="col-md-6 col-xs-6 col-sm-6 text-center">
		        <p><strong><?php _e("Expert Archive Page", JBP_TEXT_DOMAIN) ?></strong></p>
		        <div class="clearfix"></div>

		        <div class="text-left">
			        <p><code>[jbp-expert-archive-page]</code></p>
			        <ul>
				        <li>
					        <mark><?php _e("post_per_page", JBP_TEXT_DOMAIN) ?></mark>
					        : <?php _e("Number of jobs you want to list on a page. Default is the config from setting page.", JBP_TEXT_DOMAIN) ?>
				        </li>
			        </ul>
		        </div>
	        </div>
            <div class="clearfix"></div>
        </div>
	    <div class="page-header">
		    <h3><?php _e('Form Page') ?></h3>
	    </div>
	    <div class="row">
		    <div class="col-md-6 col-xs-6 col-sm-6 text-center">
			    <p><strong><?php _e("Job Add/Update Page", JBP_TEXT_DOMAIN) ?></strong></p>
			    <div class="clearfix"></div>

			    <div class="text-left">
				    <p><code>[jbp-job-update-page]</code></p>
				    <ul>
					    <li>
						    <?php _e("This shortcode doesn't have any parameters.", JBP_TEXT_DOMAIN) ?>
					    </li>
				    </ul>
			    </div>
		    </div>
		    <div class="col-md-6 col-xs-6 col-sm-6 text-center">
			    <p><strong><?php _e("Expert Add/Update Page", JBP_TEXT_DOMAIN) ?></strong></p>
			    <div class="clearfix"></div>

			    <div class="text-left">
				    <p><code>[jbp-expert-update-page]</code></p>
				    <ul>
					    <li>
						    <?php _e("This shortcode doesn't have any parameters.", JBP_TEXT_DOMAIN) ?>
					    </li>
				    </ul>
			    </div>
		    </div>
		    <div class="clearfix"></div>
	    </div>
	    <div class="page-header">
		    <h3><?php _e('Communication Page') ?></h3>
	    </div>
	    <div class="row">
		    <div class="col-md-6 col-xs-6 col-sm-6 text-center">
			    <p><strong><?php _e("Contact Job Poster Page", JBP_TEXT_DOMAIN) ?></strong></p>
			    <div class="clearfix"></div>

			    <div class="text-left">
				    <p><code>[jbp-job-contact-page]</code></p>
				    <ul>
					    <li>
						    <mark>id</mark>:
						    <?php _e("ID of the job you want to send contact to.",JBP_TEXT_DOMAIN) ?>
					    </li>
					    <li>
						    <mark><?php _e("success_text", JBP_TEXT_DOMAIN) ?></mark>
						    : <?php _e("The text to display after user submission successfully.", JBP_TEXT_DOMAIN) ?>
					    </li>
					    <li>
						    <mark><?php _e("error_text", JBP_TEXT_DOMAIN) ?></mark>
						    : <?php _e("The text to display when error happen after user submission.", JBP_TEXT_DOMAIN) ?>
					    </li>
				    </ul>
			    </div>
		    </div>
		    <div class="col-md-6 col-xs-6 col-sm-6 text-center">
			    <p><strong><?php _e("Contact Expert Page", JBP_TEXT_DOMAIN) ?></strong></p>
			    <div class="clearfix"></div>

			    <div class="text-left">
				    <p><code>[jbp-expert-contact-page]</code></p>
				    <ul>
					    <li>
						    <mark>id</mark>:
						    <?php _e("ID of the expert you want to send contact to.",JBP_TEXT_DOMAIN) ?>
					    </li>
					    <li>
						    <mark><?php _e("success_text", JBP_TEXT_DOMAIN) ?></mark>
						    : <?php _e("The text to display after user submission successfully.", JBP_TEXT_DOMAIN) ?>
					    </li>
					    <li>
						    <mark><?php _e("error_text", JBP_TEXT_DOMAIN) ?></mark>
						    : <?php _e("The text to display when error happen after user submission.", JBP_TEXT_DOMAIN) ?>
					    </li>
				    </ul>
			    </div>
		    </div>
		    <div class="clearfix"></div>
	    </div>
	    <div class="page-header">
		    <h3><?php _e('Single Page') ?></h3>
	    </div>
	    <div class="row">
		    <div class="col-md-6 col-xs-6 col-sm-6 text-center">
			    <p><strong><?php _e("Job Single Page", JBP_TEXT_DOMAIN) ?></strong></p>
			    <div class="clearfix"></div>

			    <div class="text-left">
				    <p><code>[jbp-job-single-page]</code></p>
				    <ul>
					    <li>
						    <mark><?php _e("id", JBP_TEXT_DOMAIN) ?></mark>
						    : <?php _e("ID of the Job", JBP_TEXT_DOMAIN) ?>
					    </li>
				    </ul>
			    </div>
		    </div>
		    <div class="col-md-6 col-xs-6 col-sm-6 text-center">
			    <p><strong><?php _e("Expert Single Page", JBP_TEXT_DOMAIN) ?></strong></p>
			    <div class="clearfix"></div>

			    <div class="text-left">
				    <p><code>[jbp-expert-single-page]</code></p>
				    <ul>
					    <li>
						    <mark><?php _e("id", JBP_TEXT_DOMAIN) ?></mark>
						    : <?php _e("ID of the Expert", JBP_TEXT_DOMAIN) ?>
					    </li>
				    </ul>
			    </div>
		    </div>
		    <div class="clearfix"></div>
	    </div>
	    <div class="page-header">
		    <h3><?php _e('Others Page') ?></h3>
	    </div>
	    <div class="row">
		    <div class="col-md-6 col-xs-6 col-sm-6 text-center">
			    <p><strong><?php _e("Landing Page", JBP_TEXT_DOMAIN) ?></strong></p>
			    <div class="clearfix"></div>

			    <div class="text-left">
				    <p><code>[jbp-landing-page]</code></p>
				    <ul>
					    <li>
						    <mark><?php _e("job_show_count", JBP_TEXT_DOMAIN) ?></mark>
						    : <?php _e("Number of jobs to show, default 3.", JBP_TEXT_DOMAIN) ?>
					    </li>
					    <li>
						    <mark><?php _e("expert_show_count", JBP_TEXT_DOMAIN) ?></mark>
						    : <?php _e("Number of experts to show, default 6.", JBP_TEXT_DOMAIN) ?>
					    </li>
				    </ul>
			    </div>
		    </div>
		    <div class="col-md-6 col-xs-6 col-sm-6 text-center">
			    <p><strong><?php _e("My Jobs Page", JBP_TEXT_DOMAIN) ?></strong></p>
			    <div class="clearfix"></div>

			    <div class="text-left">
				    <p><code>[jbp-my-job-page]</code></p>
				    <ul>
					    <li>
						    <?php _e("This shortcode doesn't have any parameters.", JBP_TEXT_DOMAIN) ?>
					    </li>
				    </ul>
			    </div>
		    </div>
		    <div class="clearfix"></div>
		    <div class="col-md-6 col-xs-6 col-sm-6 text-center">
			    <p><strong><?php _e("My Experts Page", JBP_TEXT_DOMAIN) ?></strong></p>
			    <div class="clearfix"></div>

			    <div class="text-left">
				    <p><code>[jbp-my-expert-page]</code></p>
				    <ul>
					    <li>
						    <?php _e("This shortcode doesn't have any parameters.", JBP_TEXT_DOMAIN) ?>
					    </li>
				    </ul>
			    </div>
		    </div>
		    <div class="clearfix"></div>
	    </div>
    <?php
    }
}