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
 * Sends the generated report CSV file to the user
 *
 * @package    report
 * @subpackage courseawards
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('reportcourseawards', '', null, '', array('pagelayout'=>'report'));

require_capability('moodle/site:viewreports', get_context_instance(CONTEXT_SYSTEM));

// Define the location and name of the saved CSV file - do this in report.php too.
define('FILE_CSV', $CFG->dataroot.'/temp/courseawards-report.csv');

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');       // Open or download.
// Header("Content-Type: application/force-download");     // Download ONLY.
header('Content-Disposition: attachment; filename='.basename(FILE_CSV));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize(FILE_CSV));
readfile(FILE_CSV);
