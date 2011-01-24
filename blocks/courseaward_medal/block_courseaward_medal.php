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
 * Main block
 *
 * @package    block_courseaward_medal
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_courseaward_medal extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_courseaward_medal');
    }

    function instance_allow_multiple() {
        return false;
    }
    function has_config() {
        return false;
    }
    function applicable_formats() {
        // production:
//        return array('course-view' => true);
        // development:
        return array('all' => true);
    }

    function get_content() {
        global $CFG, $COURSE, $USER;

        $build = ''; // build the output into this variable
        $pathtoblock = $CFG->wwwroot.'/blocks/courseaward_medal/';

        // add in the functions
        require_once($CFG->dirroot.'/blocks/courseaward_medal/libmedal.php');

        // show the medal awarded, if there is one.
        if(has_medal($COURSE->id)) {
            $build .= '<div class="center bgborder"><img src="'.get_medal_img(get_medal($COURSE->id)).'" /></div>'."\n";
            $build .= '<div class="center awardtitle">'.get_string('medal-'.get_medal($COURSE->id), 'block_courseaward_medal').'</div>'."\n";
            // end testing
        } else {
            /**
             * We can add a string if there is no medal to display ("No medal yet" or similar) but then it is obvious that there are medals to be won,
             * and this course doesn't have one. The next line is commented out so that no block appears at all, except for admins.
             */
            //$build .= '<div class="center">'.get_string('user-nomedals', 'block_courseaward_medal').'</div>'."\n";
        }

        if(has_capability('block/courseaward_medal:admin', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
            /**
             * user has the 'admin' capability and can assign/remove medals
             */
            if(has_medal($COURSE->id)) {
                /**
                 * if the course has a medal, provide options to delete it
                 */
                $build .= "\n".'<div class="center smaller cleartop"><a href="'.$pathtoblock.'admin_unmedal.php?cid='.$COURSE->id.'">'.get_string('admin-medaldel', 'block_courseaward_medal').'</a></div>';
            } else {
                /**
                 * if the course doesn't have a medal, provide options to set one
                 */
                $build .= "\n".'<div class="center smaller">';
                $build .= '<a href="'.$pathtoblock.'admin_medal.php?cid='.$COURSE->id.'&medal=gold">'.get_string('admin-medaladdgold', 'block_courseaward_medal').'</a><br />'."\n";
                $build .= '<a href="'.$pathtoblock.'admin_medal.php?cid='.$COURSE->id.'&medal=silver">'.get_string('admin-medaladdsilver', 'block_courseaward_medal').'</a><br />'."\n";
                $build .= '<a href="'.$pathtoblock.'admin_medal.php?cid='.$COURSE->id.'&medal=bronze">'.get_string('admin-medaladdbronze', 'block_courseaward_medal').'</a><br />'."\n";
                $build .= '<a href="'.$pathtoblock.'admin_medal.php?cid='.$COURSE->id.'&medal=achievement">'.get_string('admin-medaladdachievement', 'block_courseaward_medal').'</a>';
                $build .= '</div>';
            }
            $build .= get_course_medal_history($COURSE->id);
        }

        $this->content          = new stdClass;
        $this->content->text    = $build;
        $this->content->footer  = '';

        return $this->content;
    }
}
