<?php

require_once('../../config.php');
require_once($CFG->dirroot . '/local/email/lib.php');

EmailTemplate::send('quote_followup', array('user' => 3));
