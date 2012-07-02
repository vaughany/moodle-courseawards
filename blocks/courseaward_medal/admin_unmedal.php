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
 * Removes a medal
 *
 * @package    block
 * @subpackage courseaward_medal
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');

defined('MOODLE_INTERNAL') || die;

require_login();

if (!$COURSE = $DB->get_record('course', array('id'=>required_param('cid', PARAM_INT)))) {
    print_error(get_string('error-courseidnotset', 'block_courseaward_medal'));
}

if (!$USER->id) {
    print_error(get_string('error-useridnotset', 'block_courseaward_medal'));
}

if (!has_capability('block/courseaward_medal:admin', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
    print_error(get_string('error-notadmin', 'block_courseaward_medal'));
}

require_once($CFG->dirroot.'/blocks/courseaward_medal/libmedal.php');

if (!$medal_id = get_medal_id($COURSE->id)) {
    print_error(get_string('error-nomedalid', 'block_courseaward_medal'));
}

$dbupdate = new object();
$dbupdate->id               = $medal_id;
$dbupdate->course_id        = $COURSE->id;
$dbupdate->deleted          = 1;
$dbupdate->date_modified    = time();
$dbupdate->deleted_user_id  = $USER->id;

if (!$DB->update_record('block_courseaward_medal', $dbupdate)) {
    print_error(get_string('error-dbupdate', 'block_courseaward_medal'));
} else {
    redirect($CFG->wwwroot.'/course/view.php?id='.$COURSE->id);
}
