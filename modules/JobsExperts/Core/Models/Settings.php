<?php

/**
 * Author: Hoang Ngo
 */
class JobsExperts_Core_Models_Settings extends JobsExperts_Framework_OptionModel
{

    //general setting
    public $theme;
    public $use_certification;
    public $certification_label;
    public $open_for_days;
    public $plugins;

    public $terms;

    public $job_max_records;
    public $job_per_page;
    public $job_budget_range;
    public $job_new_job_status;
    public $job_allow_draft;
    public $job_sample_size;

    public $job_min_search_budget;
    public $job_max_search_budget;

    public $job_contact_form;
    public $job_contact_form_captcha;
    public $job_cc_admin;
    public $job_cc_sender;
    public $job_email_subject;
    public $job_email_content;

    public $expert_max_records;
    public $expert_per_page;
    public $expert_new_expert_status;
    public $expert_allow_draft;
    public $expert_sample_size;

    public $expert_contact_form;
    public $expert_contact_form_captcha;
    public $expert_cc_admin;
    public $expert_cc_sender;
    public $expert_email_subject;
    public $expert_email_content;

    //virtual page
    public $job_add;
    public $job_edit;
    public $job_contact;
    public $my_job;
    public $job_listing;

    public $expert_add;
    public $expert_edit;
    public $expert_listing;
    public $expert_contact;
    public $my_expert;

    public $landing_page;

    public $social_list;


    public function storage_name()
    {
        return 'jobs_experts_settings';
    }

    public function __construct()
    {
        $can_load = $this->load();
        //$can_load=false;
        if ($can_load == false) {
            //no init, now we do the setting
            $this->theme = 'dark';
            $this->use_certification = 0;
            $this->certification_label = 'Jobs+ Certified';
            $this->open_for_days = '3,7,14,21';

            $this->job_max_records = 4;
            $this->job_per_page = 12;
            $this->job_budget_range = 1;
            $this->job_new_job_status = 'publish';
            $this->job_allow_draft = 1;
            $this->job_sample_size = 4;
            $this->job_contact_form = 0;
            $this->job_contact_form_captcha = 0;
            $this->job_min_search_budget = 0;
            $this->job_max_search_budget = 2000;
            $this->job_cc_admin = false;
            $this->job_cc_sender = false;
            $this->job_email_subject = 'SITE_NAME Contact Request: [ POST_TITLE ]';
            $this->job_email_content = 'Hi TO_NAME, you have received a message from

	Name: FROM_NAME
	Email: FROM_EMAIL
	Message:

	FROM_MESSAGE


	Job link: POST_LINK
	';

            $this->expert_max_records = 1;
            $this->expert_per_page = 12;
            $this->expert_budget_range = 1;
            $this->expert_new_expert_status = 'publish';
            $this->expert_allow_draft = 1;
            $this->expert_sample_size = 4;
            $this->expert_contact_form = 0;
            $this->expert_contact_form_captcha = 0;
            $this->expert_cc_admin = false;
            $this->expert_cc_sender = false;
            $this->expert_email_subject = 'SITE_NAME Contact Request: [ POST_TITLE ]';
            $this->expert_email_content = 'Hi TO_NAME, you have received a message from

	Name: FROM_NAME
	Email: FROM_EMAIL
	Message:

	FROM_MESSAGE


	Expert link: POST_LINK
	';

            $this->save();
        } else {
            $this->load();
        }

        $this->social_list = array(
            'addthis' => 'Add This',
            'blogger' => 'Blogger',
            'deviantart' => 'Deviant Art',
            'digg' => 'Digg',
            'dribbble' => 'Dribbble',
            'email' => 'Email',
            'etsy' => 'Etsy',
            'facebook' => 'Facebook',
            'flickr' => 'Flickr',
            'foursquare' => 'Four Square',
            'github' => 'Github',
            'googleplus' => 'Google Plus',
            'hangouts' => 'Hangouts',
            'instagram' => 'Instagram',
            'linkedin' => 'Linkedin',
            'pinterest' => 'Pinterest',
            'reddit' => 'Reddit',
            'rss' => 'Rss',
            'skype' => 'Skype',
            'stackoverflow' => 'Stackoverflow',
            'tumblr' => 'Tumblr',
            'twitter' => 'Twitter',
            'vimeo' => 'Vimeo',
            'yelp' => 'Yelp',
            'youtube' => 'Youtube'
        );
    }
}