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
 * Removes a vote [and note] (sets it to deleted)
 *
 * @package    block
 * @subpackage courseaward_vote
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');

defined('MOODLE_INTERNAL') || die;

if (!$course = $DB->get_record('course', array('id'=>required_param('cid', PARAM_INT)))) {
    print_error(get_string('error-courseidnotset', 'block_courseaward_vote'));
}

require_login($course);

require_once($CFG->dirroot.'/blocks/courseaward_vote/libvote.php');

if (!$USER->id) {
    print_error(get_string('error-useridnotset', 'block_courseaward_vote'));
}

if (!can_change_vote($USER->id, $course->id)) {
    print_error(get_string('error-cantunvoteyet', 'block_courseaward_vote'));
}

if (!$vote_id = get_vote_id($USER->id, $course->id)) {
    print_error(get_string('error-novoteid', 'block_courseaward_vote'));
}

$dbupdate = new object();
$dbupdate->id               = $vote_id;
$dbupdate->date_modified    = time();
$dbupdate->deleted          = 1;
$dbupdate->deleted_user_id  = $USER->id;

if (!$DB->update_record('block_courseaward_vote', $dbupdate)) {
    print_error(get_string('error-dbupdate', 'block_courseaward_vote'));
} else {
    redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
}
