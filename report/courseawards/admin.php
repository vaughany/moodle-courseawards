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
 * Administrative actions for the Course Awards report
 *
 * @package    report
 * @subpackage courseawards
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

defined('MOODLE_INTERNAL') || die;

require_login();

admin_externalpage_setup('reportcourseawards', '', null, '', array('pagelayout'=>'report'));

require_capability('moodle/site:viewreports', get_context_instance(CONTEXT_SYSTEM));

$qid = required_param('q', PARAM_ALPHA);

// Run code based on the $qid received from index.php.
if (strtolower($qid) == 'delvote') {

    // Deletes votes from the admin interface.
    $vid    = required_param('v', PARAM_INT);
    $course = optional_param('c', '', PARAM_INT);
    $user   = optional_param('u', '', PARAM_INT);

    $dbupdate = new object();
    $dbupdate->id               = $vid;
    $dbupdate->date_modified    = time();
    $dbupdate->deleted          = 1;
    $dbupdate->deleted_user_id  = $USER->id;

    if (!$DB->update_record('block_courseaward_vote', $dbupdate)) {
        print_error(get_string('error_dbupdate', 'report_courseawards'));
    } else {
        if (isset($user) && !empty($user)) {
            redirect($CFG->wwwroot.'/report/courseawards/report.php?q=v&u='.$user);
        } else if (isset($course) && !empty($course)) {
            redirect($CFG->wwwroot.'/report/courseawards/report.php?q=v&c='.$course);
        } else {
            redirect($CFG->wwwroot.'/report/courseawards/');
        }
    }

} else if (strtolower($qid) == 'backup') {

    // Backs up the course awards tables.
    if ($CFG->dbtype != 'mysqli') {
        print_error(get_string('error_notmysql', 'report_courseawards'));
    }

    header('Content-type: text/plain');

    $command = 'mysqldump -h '.$CFG->dbhost.' -u '.$CFG->dbuser.' --password='.$CFG->dbpass.' --skip-opt --no-create-info '.
        $CFG->dbname.' --tables '.$CFG->prefix.'block_courseaward_medal '.$CFG->prefix.'block_courseaward_vote';
    system($command, $ret);

} else if (strtolower($qid) == 'medalremove') {

    // Removes all live medals, adds to medal history.
    if ($res = $DB->get_records('block_courseaward_medal', array('deleted'=>0), '', 'id', '', '')) {
        foreach ($res as $row) {
            $now = time();
            $dbupdate = new object();
            $dbupdate->id               = $row->id;
            $dbupdate->date_modified    = $now;
            $dbupdate->deleted          = 1;
            $dbupdate->deleted_user_id  = $USER->id;

            if (!$DB->update_record('block_courseaward_medal', $dbupdate)) {
                print_error(get_string('error_dbupdate', 'report_courseawards'));
            }
        }
        redirect($CFG->wwwroot.'/report/courseawards/');
    } else {
        print_error(get_string('error_nomedals', 'report_courseawards'));
    }

} else if (strtolower($qid) == 'medaldelete') {

    // Deletes all removed medals, forever.
    if (!$DB->delete_records('block_courseaward_medal', array('deleted'=>1))) {
        print_error(get_string('error_noremovedmedals', 'report_courseawards'));
    } else {
        redirect($CFG->wwwroot.'/report/courseawards/');
    }

} else if (strtolower($qid) == 'noteswipe') {

    // Wipes out all notes.
    if ($res = $DB->get_records_select('block_courseaward_vote', 'deleted = 0 AND note <> \'\'', array(''), '', 'id', '', '')) {
        foreach ($res as $row) {
            $now = time();
            $dbupdate = new object();
            $dbupdate->id               = $row->id;
            $dbupdate->date_modified    = $now;
            $dbupdate->note             = '';

            if (!$DB->update_record('block_courseaward_vote', $dbupdate)) {
                print_error(get_string('error_dbupdate', 'report_courseawards'));
            }
        }
        redirect($CFG->wwwroot.'/report/courseawards/');
    } else {
        print_error(get_string('error_nonotes', 'report_courseawards'));
    }
} else if (strtolower($qid) == 'voteremove') {

    // Removes all live votes (and assoc. notes), adds to vote history.
    if ($res = $DB->get_records('block_courseaward_vote', array('deleted'=>0), '', 'id', '', '')) {
        foreach ($res as $row) {
            $now = time();
            $dbupdate = new object();
            $dbupdate->id               = $row->id;
            $dbupdate->date_modified    = $now;
            $dbupdate->deleted          = 1;
            $dbupdate->deleted_user_id  = $USER->id;

            if (!$DB->update_record('block_courseaward_vote', $dbupdate)) {
                print_error(get_string('error_dbupdate', 'report_courseawards'));
            }
        }
        redirect($CFG->wwwroot.'/report/courseawards/');
    } else {
        print_error(get_string('error_novotes', 'report_courseawards'));
    }

} else if (strtolower($qid) == 'votedelete') {

    // Deletes all votes and notes, forever.
    if (!$DB->delete_records('block_courseaward_vote', array('deleted'=>1))) {
        print_error(get_string('error_noremovedmedals', 'report_courseawards'));
    } else {
        redirect($CFG->wwwroot.'/report/courseawards/');
    }

} else if (strtolower($qid) == 'wipecoursevotes') {

    // Deletes all votes and notes, forever.
    $wid    = required_param('w', PARAM_INT);

    if (!$DB->delete_records('block_courseaward_vote', array('course_id'=>$wid))) {
        print_error(get_string('error_noanyvotes', 'report_courseawards'));
    } else {
        redirect($CFG->wwwroot.'/report/courseawards/');
    }
}