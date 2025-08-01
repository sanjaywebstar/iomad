<?php

namespace local_companyfeedbackreport;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/feedback/lib.php');

class helper {
    public static function get_feedback_responses($filters = []) {
        global $DB, $USER;

        $responses = [];

        $systemcontext = \context_system::instance();

        // Get user's company.
        $companyid = \iomad::get_my_companyid($systemcontext);
        if (!$companyid) {
            return [];
        }

        // Get all courses linked to this company.
        $company = new \company($companyid);
        $companycourses = $company->get_menu_courses(true, false, false, false);
        if (empty($companycourses)) {
            return [];
        }
        $courseids = array_keys($companycourses);

        // Optional filters.
        $coursefilter = !empty($filters->course) ? "AND f.course = :courseid" : "";
        $firstnamefilter = !empty($filters->firstname) ? "AND u.firstname LIKE :firstname" : "";
        $lastnamefilter = !empty($filters->lastname) ? "AND u.lastname LIKE :lastname" : "";

        $params = [];

        if (!empty($filters->course)) {
            $params['courseid'] = $filters->course;
        }
        if (!empty($filters->firstname)) {
            $params['firstname'] = '%' . $filters->firstname . '%';
        }
        if (!empty($filters->lastname)) {
            $params['lastname'] = '%' . $filters->lastname . '%';
        }

        $sql = "
            SELECT fc.id AS id, f.id AS feedbackid, f.name AS feedbackname, fc.timemodified AS timesubmitted, 
                   c.id AS courseid, c.fullname AS coursename,
                   u.id AS userid, u.firstname, u.lastname
            FROM {feedback} f
            JOIN {course} c ON f.course = c.id
            JOIN {feedback_completed} fc ON fc.feedback = f.id
            JOIN {user} u ON u.id = fc.userid
            WHERE c.id IN (" . implode(',', $courseids) . ")
                  $coursefilter
                  $firstnamefilter
                  $lastnamefilter
            ORDER BY c.fullname, f.name, u.lastname
        ";

        $records = $DB->get_records_sql($sql, $params);
        $feedbackitems = array();

        foreach ($records as $record) {
            $userfullname = fullname($record);
            $response = [
                'coursename' => $record->coursename,
                'feedbackname' => $record->feedbackname,
                'timesubmitted' => $record->timesubmitted,
                'username' => $userfullname,
                'responses' => []
            ];

            // Get completed feedback ID.
            $completedid = $record->id;

            if ($completedid) {
                $items = $DB->get_records_sql("
                    SELECT i.*, v.value
                    FROM {feedback_item} i
                    JOIN {feedback_value} v ON v.item = i.id
                    WHERE i.feedback = :feedbackid AND v.completed = :completedid
                ", [
                    'feedbackid' => $record->feedbackid,
                    'completedid' => $completedid
                ]);

                foreach ($items as $item) {
                    if (!isset($feedbackitems[$item->id])) {
                        $feedbackitems[$item->id] = $item->name;
                    }
                    $itemobj = feedback_get_item_class($item->typ);
                    $printval = $itemobj->get_printval($item, (object) ['value' => $item->value]);


                    $response['responses'][$item->id] = $printval;
                }
            }

            $responses[] = $response;
        }

        return array($feedbackitems, $responses);
    }
}