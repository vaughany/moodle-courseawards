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
 * Configuration page for the vote block
 *
 * @package    block
 * @subpackage courseaward_vote
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $delay = array(
        '0'           => 'No delay',
        '5'           => '5 seconds',
        '30'          => '30 seconds',
        '60'          => '1 minute',
        '120'         => '2 minutes',
        '300'         => '5 minutes',
        '600'         => '10 minutes',
        '1200'        => '20 minutes',
        '1800'        => '30 minutes',
        '3600'        => '1 hour',
        '7200'        => '2 hours',
        '10800'       => '3 hours',
        '21600'       => '6 hours',
        '43200'       => '12 hours',
        '86400'       => '1 day',
        '172800'      => '2 days',
        '345600'      => '4 day',
        '604800'      => '1 week',
        '1209600'     => '2 weeks',
        '2592000'     => '1 month',
        '5184000'     => '2 months',
        '7776000'     => '3 months',
        '15552000'    => '6 months',
        '31536000'    => '1 year',
        '99999999999' => 'Never'    // Actually 3,170.9 years.
    );

    $settings->add(new admin_setting_configselect(
        'courseaward_vote/wait',
        get_string('config_time', 'block_courseaward_vote'),
        get_string('config_time_long', 'block_courseaward_vote'),
        86400,
        $delay)
    );

    $settings->add(new admin_setting_configcheckbox(
        'courseaward_vote/note',
        get_string('config_collect_notes', 'block_courseaward_vote'),
        get_string('config_note_true', 'block_courseaward_vote'),
        true,
        true,
        false)
    );

}