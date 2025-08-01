<?php

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/classes/form/filter_form.php');

require_login();

$context = context_system::instance();
require_capability('local/companyfeedbackreport:view', $context);

$PAGE->set_url(new moodle_url('/local/companyfeedbackreport/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'local_companyfeedbackreport'));
$PAGE->set_heading(get_string('pluginname', 'local_companyfeedbackreport'));

$form = new \local_companyfeedbackreport\form\filter_form();

$filterdata = [];
if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/companyfeedbackreport/index.php'));
} else if ($data = $form->get_data()) {
    $filterdata = $data;
}

$output = '';
$output .= $OUTPUT->header();

$output .= $form->render();

// Fetch feedback responses.
list($feedbackitems, $feedbackdata) = \local_companyfeedbackreport\helper::get_feedback_responses($filterdata);

if (empty($feedbackdata)) {
    $output .= $OUTPUT->notification(get_string('noresults', 'local_companyfeedbackreport'), 'notifymessage');
} else {
    $output .= html_writer::start_tag('form', ['method' => 'post']);
    $output .= html_writer::start_tag('table', ['class' => 'generaltable']);

    // Table header.
    $output .= html_writer::start_tag('thead');
    $output .= html_writer::start_tag('tr');
    $output .= html_writer::tag('th', get_string('course', 'local_companyfeedbackreport'));
    $output .= html_writer::tag('th', get_string('feedback', 'local_companyfeedbackreport'));
    $output .= html_writer::tag('th', get_string('user', 'local_companyfeedbackreport'));
    $output .= html_writer::tag('th', get_string('timesubmitted', 'local_companyfeedbackreport'));

    if (!empty($feedbackitems)) {
        foreach ($feedbackitems as $itemid => $itemname) {
            $output .= html_writer::tag('th', $itemname);
        }
    }
    $output .= html_writer::end_tag('tr');
    $output .= html_writer::end_tag('thead');

    $output .= html_writer::start_tag('tbody');
    foreach ($feedbackdata as $record) {
        $output .= html_writer::start_tag('tr');
        $output .= html_writer::tag('td', $record['coursename']);
        $output .= html_writer::tag('td', $record['feedbackname']);
        $output .= html_writer::tag('td', $record['username']);
        $output .= html_writer::tag('td', userdate($record['timesubmitted']));

        if (!empty($feedbackitems)) {
            foreach ($feedbackitems as $itemid => $itemname) {
                if (isset($record['responses'][$itemid])) {
                    $output .= html_writer::tag('td', $record['responses'][$itemid]);
                } else {
                    $output .= html_writer::tag('td', '-');
                }
            }
        }

        $output .= html_writer::end_tag('tr');
    }
    $output .= html_writer::end_tag('tbody');

    $output .= html_writer::end_tag('table');
    $output .= html_writer::end_tag('form');

}

$output .= $OUTPUT->footer();
echo $output;