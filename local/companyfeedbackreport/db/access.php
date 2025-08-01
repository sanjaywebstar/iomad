<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    'local/companyfeedbackreport:view' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_COMPANY,
        'archetypes' => array(
            'companymanager' => CAP_ALLOW,
            'companydepartmentmanager' => CAP_ALLOW,
            'clientadministrator' => CAP_ALLOW,
            'clientreporter' => CAP_ALLOW,
            'companyreporter' => CAP_ALLOW
        ),
    ),
);
