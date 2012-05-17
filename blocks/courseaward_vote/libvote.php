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
 * Functions for running the vote block
 *
 * @package    block
 * @subpackage courseaward_vote
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * return the number of votes total and an average score.
 */
function get_course_score_average($course) {
    global $COURSE, $DB;
    $res = $DB->get_records_select('block_courseaward_vote', 'deleted = \'0\' and course_id = \''.$course.'\'', array('id, vote'));
    if ($res) {
        $votes_tot = 0;
        foreach ($res as $row) {
            $votes_tot += $row->vote;
        }
    }

    $votes_no = $DB->count_records('block_courseaward_vote', array(
        'course_id'=>$course,
        'deleted'=>0
    ));

    if ($votes_no) {
        $votes_avg = $votes_tot / $votes_no;
        $votes_avg = substr($votes_avg, 0, 4); // Change the 4 for a 5 for more sig figs!

        $build = get_string('scoreavg1', 'block_courseaward_vote');
        // Get the right string for the plurality.
        if ($votes_no == 1) {
            $build .= $votes_no.get_string('scoreavg2sing', 'block_courseaward_vote');
        } else {
            $build .= $votes_no.get_string('scoreavg2', 'block_courseaward_vote');
        }
        $build .= $votes_avg.get_string('scoreavg3', 'block_courseaward_vote');
        $build .= number_format(($votes_avg/3)*100).get_string('scoreavg4', 'block_courseaward_vote');

    } else {
        if (!has_capability('block/courseaward_vote:vote', get_context_instance(CONTEXT_COURSE, $COURSE->id))
            || has_capability('block/courseaward_vote:admin', get_context_instance(CONTEXT_COURSE, $COURSE->id)) ) {
            $build = get_string('scoreavgaltnonstudent', 'block_courseaward_vote');
        } else {
            $build = get_string('scoreavgalt', 'block_courseaward_vote');
        }
    }

    return $build;
}

/**
 * see if a vote has already been made
 */
function has_voted($user, $course) {
    global $DB;
    return $DB->record_exists('block_courseaward_vote', array(
        'user_id'=>$user,
        'course_id'=>$course,
        'deleted'=>0
    ));
}

/**
 * get the actual vote
 */
function get_vote($user, $course) {
    global $DB;
    $res = $DB->get_record('block_courseaward_vote', array('user_id'=>$user, 'course_id'=>$course, 'deleted'=>0), 'vote');
    return $res->vote;
}

/**
 * get the vote ID
 */
function get_vote_id($user, $course) {
    global $DB;
    $res = $DB->get_record('block_courseaward_vote', array('user_id'=>$user, 'course_id'=>$course, 'deleted'=>0), 'id');
    return $res->id;
}

/**
 * check to see if the user can change their vote
 */
function can_change_vote($user, $course) {
    global $DB;
    $res = $DB->get_record('block_courseaward_vote', array('user_id'=>$user, 'course_id'=>$course, 'deleted'=>0), 'date_added');
    $date_vote = $res->date_added;
    $date_diff = time() - $date_vote;

    if ($date_diff < get_config('courseaward_vote', 'wait')) {
        return false;
    } else {
        return true;
    }
}

/**
 * Get a note associated with a vote
 */
function get_vote_note($user, $course) {
    global $DB;
    $res = $DB->get_record('block_courseaward_vote', array('user_id'=>$user, 'course_id'=>$course, 'deleted'=>0), 'note');
    if (isset($res->note)) {
        return $res->note;
    } else {
        return false;
    }
}

/**
 * a function to present the notes in a collapsed/expandable form
 */
function get_notes($cid, $deleted = false) {
    global $DB;

    if ($deleted == false) {
        $del = "deleted = '0' AND ";
    } else {
        $del = '';
    }
    $res = $DB->get_records_select('block_courseaward_vote', $del.'course_id = \''.$cid.'\' AND note <> \'\'',
        array('id ASC', 'id, note, deleted'));
    if ($res) {
        $build = '<div class="center smaller clear">
        <a href="javascript:hideshow(document.getElementById(\'courseaward_vote_feedback\'))">'.
            get_string('note_get', 'block_courseaward_vote').'</a>
        </div>
        <script type="text/javascript">
        //<![CDATA[
            function hideshow(which){
                if (!document.getElementById)
                    return
                if (which.style.display=="block")
                    which.style.display="none"
                else
                    which.style.display="block"
            }
        //]]>
        </script>
        <div id="courseaward_vote_feedback" class="clear" style="display: none;"><ul>'."\n";

        foreach ($res as $row) {
            if (!empty($row->note)) {
                $build .= '<li';
                if ($row->deleted) {
                    $build .= ' class="deleted"';
                }
                $build .= '>&ldquo;<em>'.$row->note.'</em>&rdquo;';
                $build .= '</li>'."\n";
            }
        }
        $build .= '</ul></div>'."\n";

        return $build;
    } else {
        return '<div class="center smaller clear">'.get_string('note_none', 'block_courseaward_vote').'</div>';

    }
}

/**
 * gets all the votes for this course and (currenty) displays an appropriate star
 */
function get_votes_summary($cid, $deleted = false) {
    global $CFG, $DB;
    $res = $DB->get_records_select('block_courseaward_vote', 'course_id = \''.$cid.'\' AND deleted = 0', array('id ASC', 'id, vote'));
    if ($res) {
        $build = '<div class="center smaller clear">
        <a href="javascript:hideshow(document.getElementById(\'courseaward_vote_summary\'))">'.
            get_string('admin-votesummary', 'block_courseaward_vote').'</a>
        </div>
        <script type="text/javascript">
            function hideshow(which){
            if (!document.getElementById)
                return
            if (which.style.display=="block")
                which.style.display="none"
            else
                which.style.display="block"
        }
        </script>
        <div id="courseaward_vote_summary" class="center clear slightborder" style="display: none;">'."\n";

        foreach ($res as $row) {
            $build .= '<img src="'.$CFG->wwwroot.'/blocks/courseaward_vote/img/'.$row->vote.'.png" />'."\n";
        }
        $build .= '</div>'."\n";

        return $build;
    } else {
        // Print 'no notes' (or whatever's in the language pack) if there are no notes to show.
        return '<div class="center smaller clear">'.get_string('admin-novotesummary', 'block_courseaward_vote').'</div>';
        // NOTE: comment out the above line to show no block at all in the event of there being no notes.
    }

}
