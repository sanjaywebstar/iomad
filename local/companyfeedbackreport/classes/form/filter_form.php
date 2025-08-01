<?php

namespace local_companyfeedbackreport\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class filter_form extends \moodleform {
    public function definition() {
        $mform = $this->_form;

        // Course dropdown.
        $mform->addElement('select', 'course', get_string('course'), $this->get_company_courses());
        $mform->setType('course', PARAM_INT);

        $mform->addElement('text', 'firstname', get_string('firstname'), ['size' => 20]);
        $mform->setType('firstname', PARAM_TEXT);

        $mform->addElement('text', 'lastname', get_string('lastname'), ['size' => 20]);
        $mform->setType('lastname', PARAM_TEXT);

        // Submit button.
        $mform->addElement('submit', 'submitbutton', get_string('filter'));
    }

    private function get_company_courses() {
        global $USER;

        $courses = ['' => get_string('allcourses', 'local_companyfeedbackreport')];

        $systemcontext = \context_system::instance();
        $companyid = \iomad::get_my_companyid($systemcontext);
        if (!$companyid) {
            return $courses;
        }
        $company = new \company($companyid);
        $companycourses = $company->get_menu_courses(true, false, false, false);
        foreach ($companycourses as $courseid => $course) {
            $courses[$courseid] = $course;
        }

        return $courses;
    }
}
