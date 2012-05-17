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
 * Functions for use with the medal block
 *
 * @package    block
 * @subpackage courseaward_medal
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Checks to see if a medal exists for this course.
function has_medal($course) {
    if (empty($course)) {
        return false;
    }

    global $DB;
    return $DB->record_exists('block_courseaward_medal', array(
        'course_id' => $course,
        'deleted'   => 0,
    ));
}

// Gets the medal for this course.
function get_medal($course) {
    if (empty($course)) {
        return false;
    }

    global $DB;
    $res = $DB->get_record('block_courseaward_medal', array(
        'course_id' => $course,
        'deleted'   => 0,
    ));
    return $res->medal;
}

// Returns the path to the image based on a specific input or returns false on fail.
function get_medal_img($medal) {
    global $CFG;
    $imgpath = $CFG->wwwroot.'/blocks/courseaward_medal/img/';

    if (empty($medal)) {
        return false;
    }

    $imgmod = '';
    if (get_config('courseaward_medal', 'size') == 'small') {
        $imgmod = '_sm';
    }

    if ($medal == 'gold' || $medal == 'g') {
        $ret = $imgpath.'medal_gold'.$imgmod.'.png';
    } else if ($medal == 'silver' || $medal == 's') {
        $ret = $imgpath.'medal_silver'.$imgmod.'.png';
    } else if ($medal == 'bronze' || $medal == 'b') {
        $ret = $imgpath.'medal_bronze'.$imgmod.'.png';
    } else if ($medal == 'achievement' || $medal == 'a') {
        $ret = $imgpath.'medal_achievement'.$imgmod.'.png';
    } else {
        return 'image error';
    }
    return $ret;
}

// Get the medal ID.
function get_medal_id($course) {
    global $DB;
    $res = $DB->get_record('block_courseaward_medal', array(
        'course_id' => $course,
        'deleted'   => 0,
    ));
    return $res->id;
}

// Get the medal date awarded.
function get_medal_date($course) {
    global $DB;
    $res = $DB->get_record('block_courseaward_medal', array(
        'course_id' => $course,
        'deleted'   => 0,
    ));
    return $res->date_added;
}

// Get the history of awards.
function get_course_medal_history($cid) {
    global $DB;
    $res = $DB->get_records_select('block_courseaward_medal', 'course_id = \''.$cid.'\'', array(
        'date_added DESC',
        'medal, date_added'
    ));

    if ($res) {
        $build = '<div class="center smaller cleartop">
        <a href="javascript:hideshow(document.getElementById(\'courseaward_medal_history\'))">'.
            get_string('admin-history', 'block_courseaward_medal').'</a>
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
        <div id="courseaward_medal_history" class="cleartop" style="display: none;"><ul>'."\n";

        foreach ($res as $row) {
            if (!empty($row->medal)) {
                $build .= '<li><strong>'.date('M jS Y', $row->date_added).':</strong><br />'.
                    ucfirst(strtolower($row->medal)).' awarded</li>'."\n";
            }
        }
        $build .= '</ul></div>'."\n";

        return $build;
    } else {
        return '<div class="center smaller cleartop">'.get_string('admin-nohistory', 'block_courseaward_medal').'</div>';
    }
}
