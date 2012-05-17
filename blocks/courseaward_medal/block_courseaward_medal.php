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
 * Course Award Medal block
 *
 * @package    block
 * @subpackage courseaward_medal
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_courseaward_medal extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_courseaward_medal');
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function has_config() {
        return false;
    }

    public function applicable_formats() {
        return array('course-view' => true);
    }

    public function get_content() {
        global $CFG, $COURSE, $USER;

        $build = '';
        $pathtoblock = $CFG->wwwroot.'/blocks/courseaward_medal/';

        require_once($CFG->dirroot.'/blocks/courseaward_medal/libmedal.php');

        // Show the medal awarded, if there is one.
        if (has_medal($COURSE->id)) {
            $build .= '<div class="center bgborder"><img src="'.get_medal_img(get_medal($COURSE->id)).'" /></div>'."\n";
            $build .= '<div class="center"><span class="awardtitle">'.get_string('medal-'.get_medal($COURSE->id),
                'block_courseaward_medal').'</span>';
            $build .= '<br /><span class="smaller">'.get_string('user-awardedon', 'block_courseaward_medal').' '.
                date('jS F Y', get_medal_date($COURSE->id)).'</span></div>'."\n";
        }

        if (has_capability('block/courseaward_medal:admin', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {

            // User has the 'admin' capability and can assign/remove medals.
            if (has_medal($COURSE->id)) {
                // If the course has a medal, provide options to delete it.
                $build .= "\n".'<div class="center smaller cleartop"><a href="'.$pathtoblock.'admin_unmedal.php?cid='.
                    $COURSE->id.'">'.get_string('admin-medaldel', 'block_courseaward_medal').'</a></div>';
            } else {
                // If the course doesn't have a medal, provide options to set one.
                $build .= "\n".'<div class="center smaller">';
                $build .= '<a href="'.$pathtoblock.'admin_medal.php?cid='.$COURSE->id.'&medal=gold">'.
                    get_string('admin-medaladdgold', 'block_courseaward_medal').'</a><br />'."\n";
                $build .= '<a href="'.$pathtoblock.'admin_medal.php?cid='.$COURSE->id.'&medal=silver">'.
                    get_string('admin-medaladdsilver', 'block_courseaward_medal').'</a><br />'."\n";
                $build .= '<a href="'.$pathtoblock.'admin_medal.php?cid='.$COURSE->id.'&medal=bronze">'.
                    get_string('admin-medaladdbronze', 'block_courseaward_medal').'</a><br />'."\n";
                $build .= '<a href="'.$pathtoblock.'admin_medal.php?cid='.$COURSE->id.'&medal=achievement">'.
                    get_string('admin-medaladdachievement', 'block_courseaward_medal').'</a>';
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
