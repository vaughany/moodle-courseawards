<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Adds a vote and optionally a note
 *
 * @package    block_courseaward_vote
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

if (!$course = $DB->get_record('course', array('id'=>required_param('cid', PARAM_INT)))) {
    error(get_string('error-courseidnotset', 'block_courseaward_vote'));
}

// require a login AND a course login, all the better to prevent fraud.
require_login($course);
require_course_login($course);

// logging goodness
// logging removed due to it not really fitting in with the way Moodle expecs log info to be passed - not from a block, apparently.
//add_to_log($course->id, 'courseaward', 'add vote', '', 'Voted using the Course Award block', 0, $USER->id);

/**
 * If we're collecting notes, the image submits a form rather than being just a hyperlink, so to get around IE being a
 * righteous pain in the bum, we have to look for the image's name field with _x and _y on the end (click coordinates)
 * instead of just the image's name on it's own (which is how firefox works).
 */
if($CFG->block_courseaward_vote_note == true) {
    // we're collecting notes so we need to optionally check some things
    if (optional_param('vote0_x', '', PARAM_INT) && optional_param('vote0_y', '', PARAM_INT)) {
        $vote = 0;
    } else if (optional_param('vote1_x', '', PARAM_INT) && optional_param('vote1_y', '', PARAM_INT)) {
        $vote = 1;
    } else if (optional_param('vote2_x', '', PARAM_INT) && optional_param('vote2_y', '', PARAM_INT)) {
        $vote = 2;
    } else if (optional_param('vote3_x', '', PARAM_INT) && optional_param('vote3_y', '', PARAM_INT)) {
        $vote = 3;
    } else {
        error(get_string('error-iefixfail', 'block_courseaward_vote'));
    }
} else {
    // we're not collecting notes
    $vote = required_param('vote', PARAM_INT);
}

// we still need to check for a note
$note = optional_param('note', '', PARAM_NOTAGS);

// validate the $vote variable
if ($vote < 0 || $vote > 3) {
    error(get_string('error-voteoutofrange', 'block_courseaward_vote'));
}
// not sure we need to validate this, but going to anyway.
if (!$USER->id) {
    error(get_string('error-useridnotset', 'block_courseaward_vote'));
}

// create the data object;
$now = time();
$dbinsert = new object();
$dbinsert->user_id          = $USER->id;
$dbinsert->course_id        = $COURSE->id;
$dbinsert->vote             = $vote;
$dbinsert->date_added       = $now;
$dbinsert->date_modified    = $now;
$dbinsert->note             = $note;

if(!$DB->insert_record('block_courseaward_vote', $dbinsert)) {
    error(get_string('error-dbinsert', 'block_courseaward_vote'));
} else {
    redirect($CFG->wwwroot.'/course/view.php?id='.$COURSE->id);
}
