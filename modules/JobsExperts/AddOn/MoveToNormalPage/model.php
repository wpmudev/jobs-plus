<?php

/**
 * Author: Hoang Ngo
 */
class JobsExpert_AddOn_MoveToNormalPage_Model extends JobsExperts_Framework_OptionModel
{
    public $add_new_job;
    public $list_jobs;
    public $my_jobs;
    public $edit_job;
    public $contact_job;

    public $add_new_expert;
    public $list_experts;
    public $my_profiles;
    public $edit_expert;
    public $contact_expert;

    public function storage_name()
    {
        return 'jbp_mtp_model';
    }

}