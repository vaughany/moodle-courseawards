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
 * Language strings for medal block
 *
 * @package    block
 * @subpackage courseaward_medal
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// General strings.
$string['pluginname'] = 'Course Awards - Medal';

// Medal strings.
$string['medal-gold'] = 'Gold Medal';
$string['medal-silver'] = 'Silver Medal';
$string['medal-bronze'] = 'Bronze Medal';
$string['medal-achievement'] = 'Notable Achievement Ribbon';

// User strings.
$string['user-nomedals'] = 'This course has not been awarded any medals yet.';
$string['user-awardedon'] = 'Awarded on';

// Admin strings.
$string['admin-nomedals'] = 'No medals have been awarded yet.';
$string['admin-medaldel'] = '(Delete this medal?)';
$string['admin-medaladdgold'] = 'Add a gold medal?';
$string['admin-medaladdsilver'] = 'Add a silver medal?';
$string['admin-medaladdbronze'] = 'Add a bronze medal?';
$string['admin-medaladdachievement'] = 'Add an \'achievement\' ribbon?';
$string['admin-history'] = '(Show/hide history)';
$string['admin-nohistory'] = '(No history to show)';

// Config strings.
$string['config_size']      = 'Medal image size';
$string['config_size_long'] = 'Choose from regular or small images.';

// Error strings.
$string['error-notadmin'] = 'You are not a Moodle admin so you can\'t do this.';
$string['error-dbupdate'] = 'Medal not updated in the database for some reason.';
$string['error-badmedaltype'] = 'Sorry, incorrect medal type.';
$string['error-dbinsert'] = 'Medal not added into the database for some reason.';
$string['error-nomedalid'] = 'No medal ID.';
$string['error-courseidnotset'] = 'Course ID not set, for some reason.';
$string['error-useridnotset'] = 'User ID not set, for some reason.';

// Capabilities strings.
$string['courseaward_medal:admin'] = 'Administrate the Course Awards - Medal block';
