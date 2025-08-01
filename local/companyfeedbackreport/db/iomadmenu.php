<?php

// Define the Iomad menu items that are defined by this plugin

function local_companyfeedbackreport_menu() {

        return array(
            'companyfeedbackreport' => array(
                'category' => 'Reports',
                'tab' => 8,
                'name' => get_string('pluginname', 'local_companyfeedbackreport'),
                'url' => '/local/companyfeedbackreport/index.php',
                'cap' => 'local/companyfeedbackreport:view',
                'icondefault' => 'report',
                'style' => 'report',
                'icon' => 'fa-users',
                'iconsmall' => 'fa-bar-chart-o',
            ),
        );
}
