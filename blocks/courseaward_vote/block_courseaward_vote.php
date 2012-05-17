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
 * Course Award Vote block
 *
 * @package    block
 * @subpackage courseaward_vote
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Set to true to get some extra debugging info relating to capabilities/roles.
define('DEBUG', false);

class block_courseaward_vote extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_courseaward_vote');
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return true;
    }

    public function applicable_formats() {
        return array('course-view' => true);
    }

    public function get_content() {
        global $CFG, $COURSE, $USER;

        $build = "\n"; // Build the output into this variable.
        $imgw = 30;
        $imgh = 30;
        $alttxt = array(
            get_string('vote0', 'block_courseaward_vote'),
            get_string('vote1', 'block_courseaward_vote'),
            get_string('vote2', 'block_courseaward_vote'),
            get_string('vote3', 'block_courseaward_vote')
        );
        $pathtoblock = $CFG->wwwroot.'/blocks/courseaward_vote/';

        require_once($CFG->dirroot.'/blocks/courseaward_vote/libvote.php');

        if (has_capability('block/courseaward_vote:admin', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
            // User has the 'admin' capability and can do pretty much anything.
            // Note that this is 'block/courseaward_vote:admin' and not 'moodle/site:doanything'.

            // Debugging.
            if (DEBUG) {
                $build .= '<p style="color:#f00;font-weight:bold;text-align:center;">CAPABILITY IS ADMIN</p>'."\n";
            }

            $build .= "\n".'<div class="center clear">';
            $build .= '<a href="'.$CFG->wwwroot.'/report/courseawards/report.php?q=v&c='.$COURSE->id.'&s=c">'.
                get_string('admin-reportcourse', 'block_courseaward_vote').'</a><br />'."\n";
            $build .= '<a href="'.$CFG->wwwroot.'/report/courseawards/index.php">'.
                get_string('admin-reportall', 'block_courseaward_vote').'</a>'."\n";
            $build .='</div>';

            // True means show deleted (different colour).
            $build .= get_notes($COURSE->id, true);

            // Show the stars in summary.
            $build .= get_votes_summary($COURSE->id, true);

        } else if (has_capability('block/courseaward_vote:vote', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
            // User has the 'vote' capability so can vote on the block.

            // Debugging.
            if (DEBUG) {
                $build .= '<p style="color:#f00;font-weight:bold;text-align:center;">CAPABILITY IS VOTE</p>'."\n";
            }

            if (has_voted($USER->id, $COURSE->id)) {
                // If the user has already voted.
                $voted = get_vote($USER->id, $COURSE->id);
                $build .= '<div class="center">'.get_string('user-alreadyvoted', 'block_courseaward_vote').'</div>'."\n";
                $build .= '<div class="center bgborder">'."\n";
                $build .= '<div class="clear"><img src="'.$pathtoblock.'img/'.$voted.'.png" width="'.
                    $imgw.'" height="'.$imgh.'" alt="'.$alttxt[$voted].'" />'."\n";
                $build .= '<br />&ldquo;'.$alttxt[$voted].'&rdquo;</div>'."\n";

                // Additionally, if the user left a note.
                if ($got_note = get_vote_note($USER->id, $COURSE->id)) {
                    $build .= '<div class="clear">'.get_string('note_noted', 'block_courseaward_vote').' &ldquo;'.$got_note.'&rdquo;</div>'."\n";
                }

                // If the user has waited (the agreed delay).
                if (can_change_vote($USER->id, $COURSE->id)) {
                    $build .= '<span class="smaller">(<a href="'.$pathtoblock.'unvote.php?cid='.$COURSE->id.'">'.
                        get_string('user-clicktounvote', 'block_courseaward_vote').'</a>)</span>'."\n";
                } else {
                    $build .= '<span class="smaller">('.get_string('error-cantunvoteyet', 'block_courseaward_vote').')</span>'."\n";
                }
                $build .= '</div>'."\n";

            } else {
                // User has not voted before (or has voted, then deleted it after the delay) so present the voting options.
                if (get_config('courseaward_vote', 'note') == false) {

                    // Voting style 1: no text box, clicky stars only.
                    $build .= "\n".'<div class="center clear">'.get_string('user-clicktovote', 'block_courseaward_vote').'</div>';
                    $build .= "\n".'<div class="center clear">';
                    for ($j=0; $j<>4; $j++) {
                        $build .= '<a href="'.$pathtoblock.'vote.php?cid='.$COURSE->id.'&vote='.$j.
                            '" title="'.$alttxt[$j].'">';
                        $build .= '<img src="'.$pathtoblock.'img/'.$j.'.png" width="'.$imgw.'" height="'.$imgh.
                            '" alt="'.$alttxt[$j].'" style="border: 0;" /></a>'."\n";
                    }
                    $build .='</div>'."\n";

                } else if (get_config('courseaward_vote', 'note') == true) {

                    // Voting style 2: text box and clicky stars.
                    $build .= '<div class="center votetitle clear">'.get_string('user-title', 'block_courseaward_vote').'</div>'."\n";
                    $build .= '<div class="center">'.get_string('user-clicktovotetextbox', 'block_courseaward_vote').'</div>'."\n";
                    $build .= '<form method="post" action="'.$pathtoblock.'vote.php">'."\n";
                    $build .= '<input type="hidden" name="cid" value="'.$COURSE->id.'" />'."\n";
                    $build .= '<div class="center"><textarea name="note" rows="2" cols="16" style="votetextarea"></textarea></div>'."\n";
                    $build .= '<div class="center">'.get_string('user-clicktovote', 'block_courseaward_vote').'</div>'."\n";
                    $build .= '<div id="vote_images" class="center cleartop clear">'."\n";
                    for ($j=0; $j<>4; $j++) {
                        $build .= '<input type="image" name="vote'.$j.'" value="'.$j.'" src="'.$pathtoblock.'img/'.$j.'.png" width="'.
                            $imgw.'" height="'.$imgh.'" style="border: 0;" title="'.$alttxt[$j].'" />'."\n";
                    }
                    $build .='</div></form>'."\n";
                }
            }

        } else {
            // The user does not have a 'vote' or an 'admin' capability so we just show results.

            // Debugging.
            if (DEBUG) {
                $build .= '<p style="color:#f00;font-weight:bold;text-align:center;">NO CAPABILITIES (DEFAULT)</p>'."\n";
            }

            $build .= '<div class="center clear">'.get_string('novote-header', 'block_courseaward_vote').'</div>'."\n";

            $build .= '<div class="center clear">';
            $build .= '<a href="'.$CFG->wwwroot.'/report/courseawards/report.php?q=v&c='.$COURSE->id.'&s=c">'.
                get_string('admin-reportcourse', 'block_courseaward_vote').'</a><br />'."\n";
            $build .='</div>';

            $build .= get_notes($COURSE->id);

            $build .= get_votes_summary($COURSE->id, true);

        } // End function get_content().

        $build .= '<div class="center">'.get_course_score_average($COURSE->id).'</div>';

        $this->content          = new stdClass;
        $this->content->text    = $build;
        $this->content->footer  = '';

        return $this->content;
    }
}
