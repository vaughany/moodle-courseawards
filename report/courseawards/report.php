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
 * Generates reports for the Course Awards admin report
 *
 * @package    report
 * @subpackage courseawards
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/blocks/courseaward_medal/libmedal.php');

defined('MOODLE_INTERNAL') || die;

require_login();

admin_externalpage_setup('reportcourseawards', '', null, '', array('pagelayout'=>'report'));

require_capability('moodle/site:viewreports', get_context_instance(CONTEXT_SYSTEM));

$qid = required_param('q', PARAM_ALPHA);

$now_fmt = 'F jS Y, g:i a';
$now = date($now_fmt, time());

$position = 0;
$save_csv = false;

define('PREFIX', $CFG->prefix);
define('TBL_VOTE', 'block_courseaward_vote');
define('TBL_MEDAL', 'block_courseaward_medal');
define('PATH_COURSE', $CFG->wwwroot.'/course/view.php?id=');
define('PATH_USER', $CFG->wwwroot.'/user/view.php?id=');
define('PATH_REPORT', $CFG->wwwroot.'/report/courseawards');
define('PATH_VOTE', $CFG->wwwroot.'/blocks/courseaward_vote/');
define('PATH_MEDAL', $CFG->wwwroot.'/blocks/courseaward_medal/');
// Define the image here so we can change it across the report.
define('SORT_IMG', PATH_REPORT.'/img/arrow_down.png');
define('DEL_IMG', PATH_REPORT.'/img/cross.png');
// Define the location and name of the saved CSV file - do this in get_csv.php too.
define('FILE_CSV', $CFG->dataroot.'/temp/courseawards-report.csv');

function get_course_shortname($id) {
    global $DB;
    $res = $DB->get_record('course', array('id'=>$id));
    return '<a href="'.PATH_COURSE.$id.'">'.$res->shortname.'</a>';
}
function get_chart2($chart, $votes, $title='Graphical breakdown of votes', $big=false) {
    if ($big == true) {
        $sizew = '800';
        $sizeh = '500';
    } else {
        $sizew = '400';
        $sizeh = '250';
    }

    // Number of decimal places.
    $nfdec = 1;

    $build      = '';
    $data       = '';
    $colours    = '';
    if (isset($chart[3])) {
        $data       .= "['".get_string('outstanding', 'report_courseawards')."', ".$chart[3]."], ";
        $colours    .= "{color: '#00cc00'}, ";
    }
    if (isset($chart[2])) {
        $data       .= "['".get_string('good', 'report_courseawards')."', ".$chart[2]."], ";
        $colours    .= "{color: '#eeee00'}, ";
    }
    if (isset($chart[1])) {
        $data       .= "['".get_string('satisfactory', 'report_courseawards')."', ".$chart[1]."], ";
        $colours    .= "{color: '#ff9600'}, ";
    }
    if (isset($chart[0])) {
        $data       .= "['".get_string('inadequate', 'report_courseawards')."', ".$chart[0]."]";
        $colours    .= "{color: '#dd0000'}";
    }
    if (substr($data, -2) == ', ') {
        $data = substr($data, 0, strlen($data)-2);
    }
    if (substr($colours, -2) == ', ') {
        $colours = substr($colours, 0, strlen($colours)-2);
    }

    /* Google Chart Tools pie chart docs                                        */
    /* http://code.google.com/apis/chart/interactive/docs/gallery/piechart.html */

    $build .= "<script type=\"text/javascript\">
    //<![CDATA[
        google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Vote');
            data.addColumn('number', 'Number of Votes');
            data.addRows([
                $data
            ]);

            var options = {
                width: $sizew, height: $sizeh,
                title: '$title',
                is3D: false,
                backgroundColor: '#f00',
                backgroundColor: {stroke: '#bbb', strokeWidth: 2},
                slices: [$colours]
            };

            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    //]]>
    </script>";

    return $build;
}
// Set up the variables in which we will slowly create either the for-screen data or data for the file.
$build          = '';
$build_heading  = '';
$csv            = '';

// Adding the Google Chart Tools JS.
$build .= '<script type="text/javascript" src="https://www.google.com/jsapi"></script>'."\n";

$build .= '<link rel="stylesheet" type="text/css" href="styles.css" />'."\n";

// Wrap everything in a id'd div tag.
$build .= "\n".'<div id="courseawards">'."\n";

// Define the SQL query per character fed into this script.
if (strtolower($qid) == 'c') {
    // This query does all per-course queries: score, votes, notes and even a basic list.

    // We needs the 's' (sort) setting for this query.
    $sort = required_param('s', PARAM_ALPHA);
    $limit  = required_param('l', PARAM_INT);
    $course = optional_param('c', '', PARAM_INT);
    if (is_numeric($course) && $course > 0) {
        // SCV meaning 'single course view'.
        define('SCV', true);
    } else {
        define('SCV', false);
    }

    // Build the query.
    $query =   "SELECT course_id, shortname, fullname, (

                    SELECT COUNT(vote)
                    FROM ".PREFIX.TBL_VOTE."
                    WHERE deleted = '0'
                    AND ".PREFIX.TBL_VOTE.".course_id = ".PREFIX."course.id
                    GROUP BY course_id
                ) AS votecount, (

                    SELECT COUNT(vote)
                    FROM ".PREFIX.TBL_VOTE."
                    WHERE deleted = '1'
                    AND ".PREFIX.TBL_VOTE.".course_id = ".PREFIX."course.id
                    GROUP BY course_id
                ) AS votecountdeleted, (

                    SELECT COUNT(note)
                    FROM ".PREFIX.TBL_VOTE."
                    WHERE deleted = '0'
                    AND note <> ''
                    AND ".PREFIX.TBL_VOTE.".course_id = ".PREFIX."course.id
                    GROUP BY course_id
                ) AS notecount, (

                    SELECT COUNT(note)
                    FROM ".PREFIX.TBL_VOTE."
                    WHERE deleted = '1'
                    AND note <> ''
                    AND ".PREFIX.TBL_VOTE.".course_id = ".PREFIX."course.id
                    GROUP BY course_id
                ) AS notecountdeleted, (

                    SELECT medal
                    FROM ".PREFIX.TBL_MEDAL."
                    WHERE ".PREFIX.TBL_MEDAL.".course_id = ".PREFIX.TBL_VOTE.".course_id
                    AND deleted = '0'
                ) AS medal, (

                    SELECT AVG(vote)
                    FROM ".PREFIX.TBL_VOTE."
                    WHERE deleted = '0'
                    AND ".PREFIX.TBL_VOTE.".course_id = ".PREFIX."course.id
                    GROUP BY course_id
                ) AS voteavg

                FROM ".PREFIX.TBL_VOTE.", ".PREFIX."course
                WHERE ".PREFIX.TBL_VOTE.".course_id = ".PREFIX."course.id ";
    if (SCV) {
        $query .= "AND ".PREFIX."course.id = '".$course."' ";
    }
    $query .= "GROUP BY ".PREFIX."course.id, course_id, shortname, fullname ";
    if (SCV) {
        // No sorting if only one course specified.
        $title = get_string('coursereport_single', 'report_courseawards');
    } else if ($sort == 's') {
        $query .= "ORDER BY voteavg DESC, votecount DESC";
        $title = get_string('coursereport_highscore', 'report_courseawards');
    } else if ($sort == 'v') {
        $query .= "ORDER BY votecount DESC, voteavg DESC";
        $title = get_string('coursereport_mostvoted', 'report_courseawards');
    } else if ($sort == 'd') {
        $query .= "ORDER BY votecountdeleted DESC, votecount DESC";
        $title = get_string('coursereport_mostdeleted', 'report_courseawards');
    } else if ($sort == 'n') {
        $query .= "ORDER BY notecount DESC, voteavg DESC";
        $title = get_string('coursereport_mostnoted', 'report_courseawards');
    } else if ($sort == 'a') {
        $query .= "ORDER BY shortname ASC, voteavg DESC";
        $title = get_string('coursereport_list', 'report_courseawards');
    }
    if ($limit > 0) {
        $query .= " LIMIT ".$limit.";";
    } else {
        $query .= ";";
    }

    // Run the query, stop everything if no rows returned.
    $res = $DB->get_records_sql($query);
    if (!$res) {
        $build .= '<p>'.get_string('noresults', 'report_courseawards').'</p>'."\n";
        die($build);
    }

    $save_csv = true;

    // Do the heading.
    $csv .= '"'.$title.' ('.get_string('asof', 'report_courseawards').$now.')"'."\n";
    $build_heading = $OUTPUT->heading($title.' ('.get_string('asof', 'report_courseawards').$now.')');

    // Brief note about what the sort arrow means.
    $build .= '<p>'.get_string('sortimg1', 'report_courseawards').'<img src="'.SORT_IMG.'" />'.
        get_string('sortimg2', 'report_courseawards').'</p>'."\n";

    // Start building the data.
    $csv .= '"'.get_string('course', 'report_courseawards').'","'.get_string('score_csv', 'report_courseawards').'","'.
        get_string('percentage_csv', 'report_courseawards').'","'.get_string('votes_csv', 'report_courseawards').'","'.
        get_string('deleted_csv', 'report_courseawards').get_string('votes_csv', 'report_courseawards').'","'.
        get_string('notes_csv', 'report_courseawards').'","'.get_string('deleted_csv', 'report_courseawards').
        get_string('notes_csv', 'report_courseawards').'","'.get_string('medals', 'report_courseawards').'"'."\n";
    $build .= '<table>'."\n".'    <tr>'."\n";

    if (SCV == false) {
        $build .= '        <th>'.get_string('position', 'report_courseawards').'</th>'."\n";
    }

    // Sort out the 'course' column.
    if ($sort == 'a') {
        $build .= '        <th>'.get_string('course', 'report_courseawards').'<img src="'.SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th><a href="report.php?q=c&l='.$limit.'&s=a">'.get_string('course', 'report_courseawards').'</a></th>'."\n";
    }
    // Sort out the 'score' column.
    if ($sort == 's') {
        $build .= '        <th>'.get_string('score', 'report_courseawards').'<img src="'.SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th><a href="report.php?q=c&l='.$limit.'&s=s">'.get_string('score', 'report_courseawards').'</a></th>'."\n";
    }
    // Sort out the 'votes' column.
    if ($sort == 'v' || $sort == 'd') {
        $build .= '        <th>'.get_string('votes', 'report_courseawards').'<img src="'.SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th><a href="report.php?q=c&l='.$limit.'&s=v">'.get_string('votes', 'report_courseawards').'</a></th>'."\n";
    }
    // Sort out the 'notes' column.
    if ($sort == 'n') {
        $build .= '        <th>'.get_string('notes', 'report_courseawards').'<img src="'.SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th><a href="report.php?q=c&l='.$limit.'&s=n">'.get_string('notes', 'report_courseawards').'</a></th>'."\n";
    }
    // Sort out the 'medals' column.
    if ($sort == 'n') {
        $build .= '        <th>'.get_string('medals', 'report_courseawards').'<img src="'.SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th>'.get_string('medals', 'report_courseawards').'</th>'."\n";
    }
    $build .= '    </tr>'."\n";

    // Cyclicly get the results.
    foreach ($res as $row) {
        // CSV data first.

        // Course.
        $csv .= '"'.$row->shortname.'",';
        // Score.
        $csv .= '"'.number_format($row->voteavg, 2).'",';
        // Percentage.
        if ($row->voteavg <> 0) {
            $csv .= '"'.number_format(($row->voteavg/3)*100).'",';
        } else {
            $csv .= '"0",';
        }
        // Votes.
        if (!$row->votecount > 0) {
            $csv .= '"0",';
        } else {
            $csv .= '"'.$row->votecount.'",';
        }
        // Deleted votes.
        if (!$row->votecountdeleted > 0) {
            $csv .= '"0",';
        } else {
            $csv .= '"'.$row->votecountdeleted.'",';
        }
        // Notes.
        if (!$row->notecount > 0) {
            $csv .= '"0",';
        } else {
            $csv .= '"'.$row->notecount.'",';
        }
        // Deleted notes.
        if (!$row->notecountdeleted > 0) {
            $csv .= '"0",';
        } else {
            $csv .= '"'.$row->notecountdeleted.'",';
        }
        // Medal.
        $csv .= '"'.ucfirst($row->medal).'"'."\n";

        // Data for screen next.

        $build .= '    <tr>'."\n";
        if (SCV == false) {
            $build .= '        <td>'.++$position.'</td>'."\n";
        }
        $build .= '        <td><a href="'.PATH_COURSE.$row->course_id.'">'.$row->shortname.'</a></td>'."\n";
        if ($row->voteavg <> 0) {
            // Catching division by zero errors.
            $build .= '        <td>'.number_format($row->voteavg, 2).' ('.number_format(($row->voteavg/3)*100).'%)</td>'."\n";
        } else {
            $build .= '        <td>'.number_format($row->voteavg, 2).' (0%)</td>'."\n";
        }
        $build .= '        <td><a href="report.php?q=v&c='.$row->course_id.'">';
        if (!$row->votecount > 0) {
            $build .= '0'."\n";
        } else {
            $build .= $row->votecount;
        }
        if ($row->votecountdeleted <> 0) {
            $build .= ' ('.$row->votecountdeleted.get_string('deleted', 'report_courseawards').')';
        }
        $build .= '</a></td>'."\n";
        $build .= '        <td><a href="report.php?q=v&c='.$row->course_id.'">';
        if (!$row->notecount > 0) {
            $build .= '0'."\n";
        } else {
            $build .= $row->notecount;
        }
        if ($row->notecountdeleted <> 0) {
            // Don't bother showing 'deleted' if there are none.
            $build .= ' ('.$row->notecountdeleted.get_string('deleted', 'report_courseawards').')';
        }
        $build .= '</a></td>'."\n";
        if (!empty($row->medal)) {
            $build .= '        <td><a href="report.php?q=v&c='.$row->course_id.'">'.ucfirst($row->medal).'</a></td>'."\n";
        } else {
            $build .= '        <td>-</td>'."\n";
        }
        $build .= '    </tr>'."\n";
    }

    // End table and print 'get csv file' link.
    $build .= '</table>'."\n";

} else if (strtolower($qid) == 'u') {
    /* This section does all user queries: most votes, highest votes, lowest votes, most notes, deleted the most...     */
    /* Sort can be most [v]otes, [h]ighest votes, [l]owest votes, most [n]otes, most [d]eleted or [a]lphabetical list   */

    // We needs the 's' (sort) setting for this query.
    $sort = required_param('s', PARAM_ALPHA);
    $user = optional_param('u', '', PARAM_INT);
    if (is_numeric($user) && $user > 0) {
        // SCV meaning 'single course view'.
        define('SCV', true);
    } else {
        define('SCV', false);
    }
    $limit  = required_param('l', PARAM_INT);

    // Build the query.
    $query =   "SELECT ".PREFIX."user.id AS user_id, firstname, lastname, (

                    SELECT COUNT(vote)
                    FROM ".PREFIX.TBL_VOTE."
                    WHERE deleted = 0
                    AND ".PREFIX.TBL_VOTE.".user_id = ".PREFIX."user.id
                    GROUP BY user_id
                ) AS votecount, (

                    SELECT COUNT(vote)
                    FROM ".PREFIX.TBL_VOTE."
                    WHERE deleted = 1
                    AND ".PREFIX.TBL_VOTE.".user_id = ".PREFIX."user.id
                    GROUP BY user_id
                ) AS votecountdeleted, (

                    SELECT COUNT(note)
                    FROM ".PREFIX.TBL_VOTE."
                    WHERE ".PREFIX.TBL_VOTE.".deleted = '0'
                    AND note <> ''
                    AND ".PREFIX.TBL_VOTE.".user_id = ".PREFIX."user.id
                    GROUP BY user_id
                ) AS notecount, (

                    SELECT COUNT(note)
                    FROM ".PREFIX.TBL_VOTE."
                    WHERE deleted = '1'
                    AND note <> ''
                    AND ".PREFIX.TBL_VOTE.".user_id = ".PREFIX."user.id
                    GROUP BY user_id
                ) AS notecountdeleted, (

                    SELECT AVG(vote)
                    FROM ".PREFIX.TBL_VOTE."
                    WHERE deleted = '0'
                    AND ".PREFIX.TBL_VOTE.".user_id = ".PREFIX."user.id
                    GROUP BY user_id
                ) AS voteavg

                FROM ".PREFIX.TBL_VOTE.", ".PREFIX."user
                WHERE ".PREFIX.TBL_VOTE.".user_id = ".PREFIX."user.id ";
    if (SCV) {
        $query .= "AND ".PREFIX."user.id = '".$user."' ";
    }
    $query .= "GROUP BY ".PREFIX."user.id, firstname, lastname ";
    if (SCV) {
        // No sorting if only one course specified.
        $title = get_string('userreport_single', 'report_courseawards');
    } else if ($sort == 'v') {
        // Most votes.
        $query .= "ORDER BY votecount DESC";
        $title = get_string('userreport_votedmost', 'report_courseawards');
    } else if ($sort == 'h') {
        // Users who voted the highest.
        $query .= "ORDER BY voteavg DESC";
        $title = get_string('userreport_votedhighest', 'report_courseawards');
    } else if ($sort == 'l') {
        // Users who voted the lowest.
        $query .= "ORDER BY voteavg ASC";
        $title = get_string('userreport_votedlowest', 'report_courseawards');
    } else if ($sort == 'n') {
        // Users who wrote the most notes.
        $query .= "ORDER BY notecount DESC";
        $title = get_string('userreport_notedmost', 'report_courseawards');
    } else if ($sort == 'd') {
        // Users who deleted the most votes.
        $query .= "ORDER BY votecountdeleted DESC";
        $title = get_string('userreport_mostdeleted', 'report_courseawards');
    } else if ($sort == 'a') {
        $query .= "ORDER BY lastname ASC, firstname ASC, voteavg DESC";
        $title = get_string('userreport_list', 'report_courseawards');
    }
    if ($limit > 0) {
        $query .= " LIMIT ".$limit.";";
    } else {
        $query .= ";";
    }

    // Run the query, stop everything if no rows returned.
    $res = $DB->get_records_sql($query);
    if (!$res) {
        $build .= '<p>'.get_string('noresults', 'report_courseawards').'</p>'."\n";
        die($build);
    }

    $save_csv = true;

    $csv .= '"'.$title.' ('.get_string('asof', 'report_courseawards').$now.')"'."\n";
    $build_heading = $OUTPUT->heading($title.' ('.get_string('asof', 'report_courseawards').$now.')');
    $build .= '<p>'.get_string('sortimg1', 'report_courseawards').'<img src="'.SORT_IMG.'" />'.
        get_string('sortimg2', 'report_courseawards').'</p>'."\n";

    // Start building the data.
    $csv .= '"'.get_string('user', 'report_courseawards').'","'.get_string('score_csv', 'report_courseawards').'","'.
        get_string('percentage_csv', 'report_courseawards').'","'.get_string('votes_csv', 'report_courseawards').'","'.
        get_string('deleted_csv', 'report_courseawards').get_string('votes_csv', 'report_courseawards').'","'.
        get_string('notes_csv', 'report_courseawards').'","'.get_string('deleted_csv', 'report_courseawards').
        get_string('notes_csv', 'report_courseawards').'"'."\n";
    $build .= '<table>'."\n".'    <tr>'."\n";
    if (SCV == false) {
        $build .= '        <th>'.get_string('position', 'report_courseawards').'</th>'."\n";
    }
    // Sort out the 'user' column.
    if ($sort == 'a') {
        $build .= '        <th>'.get_string('user', 'report_courseawards').'<img src="'.SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th><a href="report.php?q=u&l='.$limit.'&s=a">'.
            get_string('user', 'report_courseawards').'</a></th>'."\n";
    }
    // Sort out the 'highest/lowest score' column.
    if ($sort == 'h' || $sort == 'l') {
        $build .= '        <th>'.get_string('score', 'report_courseawards').'<img src="'.SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th><a href="report.php?q=u&l='.$limit.'&s=h">'.
            get_string('score', 'report_courseawards').'</a></th>'."\n";
    }
    // Sort out the 'votes/deleted votes' column.
    if ($sort == 'v' || $sort == 'd') {
        $build .= '        <th>'.get_string('votes', 'report_courseawards').'<img src="'.SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th><a href="report.php?q=u&l='.$limit.'&s=v">'.
            get_string('votes', 'report_courseawards').'</a></th>'."\n";
    }
    // Sort out the 'notes' column.
    if ($sort == 'n') {
        $build .= '        <th>'.get_string('notes', 'report_courseawards').'<img src="'.SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th><a href="report.php?q=u&l='.$limit.'&s=n">'.
            get_string('notes', 'report_courseawards').'</a></th>'."\n";
    }
    $build .= '    </tr>'."\n";

    // Cyclicly get the results.
    foreach ($res as $row) {
        // CSV data first.

        // User.
        $csv .= '"'.$row->firstname.' '.$row->lastname.'",';
        // Score.
        $csv .= '"'.number_format($row->voteavg, 2).'",';
        // Percentage.
        if ($row->voteavg <> 0) {
            $csv .= '"'.number_format(($row->voteavg/3)*100).'",';
        } else {
            $csv .= '"0",';
        }
        // Votes.
        if (!$row->votecount > 0) {
            $csv .= '"0",';
        } else {
            $csv .= '"'.$row->votecount.'",';
        }
        // Deleted votes.
        if (!$row->votecountdeleted > 0) {
            $csv .= '"0",';
        } else {
            $csv .= '"'.$row->votecountdeleted.'",';
        }
        // Notes.
        if (!$row->notecount > 0) {
            $csv .= '"0",';
        } else {
            $csv .= '"'.$row->notecount.'",';
        }
        // Deleted notes.
        if (!$row->notecountdeleted > 0) {
            $csv .= '"0"'."\n";
        } else {
            $csv .= '"'.$row->notecountdeleted.'"'."\n";
        }

        // Data for screen next.

        $build .= '    <tr>'."\n";
        if (SCV == false) {
            $build .= '        <td>'.++$position.'</td>'."\n";
        }
        $build .= '        <td><a href="'.PATH_USER.$row->user_id.'">'.$row->firstname.' '.$row->lastname.'</a></td>'."\n";
        if ($row->voteavg <> 0) {
            // Catching division by zero errors.
            $build .= '        <td>'.number_format($row->voteavg, 2).' ('.number_format(($row->voteavg/3)*100).'%)</td>'."\n";
        } else {
            $build .= '        <td>'.number_format($row->voteavg, 2).' (0%)</td>'."\n";
        }
        $build .= '        <td><a href="report.php?q=v&u='.$row->user_id.'">';
        if (!$row->votecount > 0) {
            $build .= '0';
        } else {
            $build .= $row->votecount;
        }
        if ($row->votecountdeleted <> 0) {
            $build .= ' ('.$row->votecountdeleted.get_string('deleted', 'report_courseawards').')';
        }
        $build .= '</a></td>'."\n";
        $build .= '        <td><a href="report.php?q=v&u='.$row->user_id.'">';
        if (!$row->notecount > 0) {
            $build .= '0';
        } else {
            $build .= $row->notecount;
        }
        if ($row->notecountdeleted <> 0) {
            // Don't bother showing 'deleted' if there are none.
            $build .= ' ('.$row->notecountdeleted.get_string('deleted', 'report_courseawards').')';
        }
        $build .= '</a></td>'."\n".'    </tr>'."\n";
    }

    // End table.
    $build .= '</table>'."\n";

} else if (strtolower($qid) == 'v') {
    /* This section shows detail about all the votes and notes, as well as deleted votes and notes, */
    /* attributed to a course or an individual.                                                     */

    // Figure out what type of report this is.
    if ($user = optional_param('u', '', PARAM_INT)) {
        define('TYPE', 'user');
        $data = $DB->get_record('user', array('id'=>$user));
        $build_heading = $OUTPUT->heading(get_string('vnreport_title', 'report_courseawards').$data->firstname.' '.
            $data->lastname.' ('.get_string('asof', 'report_courseawards').$now.')');
        $csv .= '"'.get_string('vnreport_title', 'report_courseawards').$data->firstname.' '.$data->lastname.' ('.
            get_string('asof', 'report_courseawards').$now.')"'."\n";
    } else if ($course = optional_param('c', '', PARAM_INT)) {
        define('TYPE', 'course');
        $data = $DB->get_record('course', array('id'=>$course));
        $build_heading = $OUTPUT->heading(get_string('vnreport_title', 'report_courseawards').$data->fullname.' ('.
            $data->shortname.') ('.get_string('asof', 'report_courseawards').$now.')');
        $csv .= '"'.get_string('vnreport_title', 'report_courseawards').$data->fullname.' ('.$data->shortname.') ('.
            get_string('asof', 'report_courseawards').$now.')"'."\n";
    } else {
        print_error(get_string('vnreport_errortype', 'report_courseawards'));
    }

    // New summary section, simply votes against the coloured star.

    // Build the query.
    $query =   "SELECT vote, COUNT(vote) AS votecount, (
                    SELECT COUNT(vote)
                    FROM ".PREFIX.TBL_VOTE." ";
    if (TYPE == 'user') {
        $query .= "WHERE ".PREFIX.TBL_VOTE.".user_id = '".$user."' ";
    } else if (TYPE == 'course') {
        $query .= "WHERE ".PREFIX.TBL_VOTE.".course_id = '".$course."' ";
    }
    $query .=      "AND deleted = 0 )
                AS total
                FROM ".PREFIX.TBL_VOTE."
                WHERE deleted = 0 ";
    if (TYPE == 'user') {
        $query .= "AND ".PREFIX.TBL_VOTE.".user_id = '".$user."' ";
    } else if (TYPE == 'course') {
        $query .= "AND ".PREFIX.TBL_VOTE.".course_id = '".$course."' ";
    }
    $query .= "GROUP BY vote ORDER BY vote DESC;";

    $res = $DB->get_records_sql($query);
    if ($res) {

        $build .= '<h3>'.get_string('vnreport_summary', 'report_courseawards').'</h3>'."\n";
        $build .= '<table>'."\n";
        $build .= '    <tr><th>'.get_string('vnreport_vote', 'report_courseawards').'</th><th>'.
            get_string('vnreport_votecast', 'report_courseawards').'</th><th>'.
            get_string('vnreport_percentage', 'report_courseawards').'</th></tr>'."\n";

        $score  = 0;
        $chart  = array();
        $votes  = 0;
        foreach ($res as $row) {
            $build .= '    <tr><td><img src="'.PATH_VOTE.'img/'.$row->vote.'.png" /></td><td>'.$row->votecount.
                '</td><td>'.number_format(($row->votecount/$row->total)*100, 1).'%</td></tr>'."\n";
            $score += ($row->vote * $row->votecount)/$row->total;
            $chart[$row->vote] = $row->votecount;
            $votes += $row->votecount; // For use by the chart function.
        }
        $build .= '</table>'."\n";

        $build .= '<p>'.get_string('vnreport_coursescore', 'report_courseawards').number_format($score, 2).
            get_string('vnreport_orpercentage', 'report_courseawards').number_format(($score/3)*100, 1).
            get_string('vnreport_percent', 'report_courseawards').'</p>'."\n";

        // GETCHART2.
        if (TYPE == 'user') {
            $build .= get_chart2($chart, $votes, $data->firstname.' '.$data->lastname, false);
        } else if (TYPE == 'course') {
            $build .= get_chart2($chart, $votes, $data->fullname.' ('.$data->shortname.')', false);
        }

        $build .= '<div id="chart_div"></div>'."\n";
    }

    // Put the medal on-screen if one exists.
    if (TYPE == 'course' && has_medal($course)) {
        // Course has a medal.
        $awarded = get_medal($course);
        $build .= '<h3>'.get_string('vnreport_medalawarded', 'report_courseawards').'</h3>'."\n";
        $build .= '<p>'.get_string('vnreport_courseawarded', 'report_courseawards').'&ldquo;<strong>'.
            ucfirst($awarded).'</strong>&rdquo;:</p>'."\n";
        $build .= '<p><img src="'.get_medal_img($awarded).'" alt="'.ucfirst($awarded).'" title="'.
            ucfirst($awarded).'" /></p>'."\n";
    }

    // End of summary section.

    // Original section.

    // Build the query.
    $query =   "SELECT ".PREFIX.TBL_VOTE.".id AS vote_id, firstname, lastname, shortname, fullname, user_id,
                    course_id, vote, date_added, date_modified, note, ".PREFIX.TBL_VOTE.".deleted
                FROM ".PREFIX.TBL_VOTE.", ".PREFIX."course, ".PREFIX."user
                WHERE ".PREFIX.TBL_VOTE.".course_id = ".PREFIX."course.id
                AND ".PREFIX.TBL_VOTE.".user_id = ".PREFIX."user.id ";
    if (TYPE == 'user') {
        $query .= "AND ".PREFIX.TBL_VOTE.".user_id = '".$user."' ";
    } else if (TYPE == 'course') {
        $query .= "AND ".PREFIX.TBL_VOTE.".course_id = '".$course."' ";
    } else {
        print_error(get_string('error_query', 'report_courseawards'));
    }
    $query .= "ORDER BY date_added DESC;";

    // Run the query, stop everything if no rows returned.
    $res = $DB->get_records_sql($query);
    if (!$res) {
        $build .= '<p>'.get_string('novotesnotes', 'report_courseawards').'</p>'."\n";
    } else {

        $save_csv = true;

        $build .= '<h3>'.get_string('vnreport_detail', 'report_courseawards').'</h3>'."\n";
        $build .= '<p>'.get_string('vnreport_greytext', 'report_courseawards').'</p>'."\n";

        // Start building the data.
        $csv .= '"'.get_string('date', 'report_courseawards').'","'.get_string('name', 'report_courseawards').'","'.
            get_string('vote_csv', 'report_courseawards').'","'.get_string('note_csv', 'report_courseawards').'","'.
            get_string('deleted_csv', 'report_courseawards').'"'."\n";
        // Start building the table.
        $build .= '<table>'."\n".'    <tr>'."\n";
        $build .= '        <th>'.get_string('position', 'report_courseawards').'</th>'."\n";
        $build .= '        <th>'.get_string('date', 'report_courseawards').' <img src="'.SORT_IMG.'" /></th>'."\n";
        $build .= '        <th>'.get_string('name', 'report_courseawards').'</th>'."\n";
        $build .= '        <th>'.get_string('vnreport_vote', 'report_courseawards').'</th>'."\n";
        $build .= '        <th>'.get_string('vnreport_note', 'report_courseawards').'</th>'."\n";
        $build .= '        <th class="delete">'.get_string('vnreport_del', 'report_courseawards').'</th>'."\n";
        $build .= '    </tr>'."\n";

        // Cyclicly put the results on the screen.
        foreach ($res as $row) {
            // CSV data first.

            // Date.
            $csv .= '"'.date($now_fmt, $row->date_added).'",';
            // Name: course / user.
            if (TYPE == 'course') {
                $csv .= '"'.$row->firstname.' '.$row->lastname.'",';
            } else if (TYPE == 'user') {
                $csv .= '"'.$row->fullname.' ('.$row->shortname.')",';
            }
            // Votes.
                $csv .= '"'.$row->vote.'",';
            // Notes.
            if (!$row->note > 0) {
                $csv .= '"",';
            } else {
                $csv .= '"'.$row->note.'",';
            }
            // Deleted notes.
            if (!$row->deleted > 0) {
                $csv .= '"'.get_string('no', 'report_courseawards').'"'."\n";
            } else {
                $csv .= '"'.get_string('yes', 'report_courseawards').'"'."\n";
            }

            // Data for screen next.
            if ($row->deleted == 1) {
                $build .= '    <tr class="deleted">'."\n";
            } else {
                $build .= '    <tr>'."\n";
            }
            $build .= '        <td>'.++$position.'</td>'."\n";
            $build .= '        <td>'.get_string('vnreport_added', 'report_courseawards').
                date($now_fmt, $row->date_added);
            if ($row->deleted == 1) {
                $build .= '<br /><span class="deleted">'.get_string('vnreport_deleted', 'report_courseawards').
                    date($now_fmt, $row->date_modified).'</span>';
            }
            $build .= '</td>'."\n";
            if (TYPE == 'course') {
                $build .= '        <td><a href="'.PATH_USER.$row->user_id.'">'.$row->firstname.' '.
                    $row->lastname.'</a></td>'."\n";
            } else if (TYPE == 'user') {
                $build .= '        <td><a href="'.PATH_COURSE.$row->course_id.'">'.$row->fullname.' ('.
                    $row->shortname.')</a></td>'."\n";
            }
            $build .= '        <td><img src="'.PATH_VOTE.'img/'.$row->vote.'.png" /></td>'."\n";
            if (!empty($row->note)) {
                $build .= '        <td>&ldquo;'.$row->note.'&rdquo;</td>'."\n";
            } else {
                $build .= '        <td>-</td>'."\n";
            }
            if ($row->deleted == 0) {
                if (TYPE == 'user') {
                    $build .= '        <td><a href="admin.php?q=delvote&v='.$row->vote_id.'&u='.$user.'">';
                } else if (TYPE == 'course') {
                    $build .= '        <td><a href="admin.php?q=delvote&v='.$row->vote_id.'&c='.$course.'">';
                }
                $build .= '<img src="'.DEL_IMG.'" alt="'.get_string('vnreport_delvotenote', 'report_courseawards').'" /></a></td>'."\n";
            } else {
                $build .= '        <td>&nbsp;</td>'."\n";
            }
            $build .= '    </tr>'."\n";
        }
    }

    // End table.
    $build .= '</table>'."\n";

} else if (strtolower($qid) == 'm') {
    // This section shows the medals which have been awarded.

    // We needs the 's' (sort) setting for this query. ([d]ate and [c]ourse).
    $sort   = required_param('s', PARAM_ALPHA);
    $limit  = required_param('l', PARAM_INT);
    $medal  = optional_param('m', '', PARAM_ALPHA);

    // Build the query.
    $query =   "SELECT ".PREFIX.TBL_MEDAL.".id AS mid, user_id, course_id, medal, date_added, date_modified, ".
                PREFIX.TBL_MEDAL.".deleted AS deleted, shortname, fullname, firstname, lastname
                FROM ".PREFIX.TBL_MEDAL.", ".PREFIX."course, ".PREFIX."user
                WHERE ".PREFIX.TBL_MEDAL.".course_id = ".PREFIX."course.id
                AND ".PREFIX.TBL_MEDAL.".user_id = ".PREFIX."user.id ";
    if ($medal == 'g') {
        $query .= "AND medal = 'gold' ";
    } else if ($medal == 's') {
        $query .= "AND medal = 'silver' ";
    } else if ($medal == 'b') {
        $query .= "AND medal = 'bronze' ";
    } else if ($medal == 'a') {
        $query .= "AND medal = 'achievement' ";
    }

    if ($sort == 'd') {
        $query .= "ORDER BY date_added DESC ";
    } else if ($sort == 'c') {
        $query .= "ORDER BY fullname ASC ";
    }
    if ($limit > 0) {
        $query .= " LIMIT ".$limit.";";
    } else {
        $query .= ";";
    }

    // Run the query, stop everything if no rows returned.
    $res = $DB->get_records_sql($query);
    if (!$res) {
        $build .= '<p>'.get_string('noresults', 'report_courseawards').'</p>'."\n";
        die($build);
    }

    $save_csv = true;

    if ($medal == 'g') {
        $title = get_string('medalsreport_gold', 'report_courseawards');
    } else if ($medal == 's') {
        $title = get_string('medalsreport_silver', 'report_courseawards');
    } else if ($medal == 'b') {
        $title = get_string('medalsreport_bronze', 'report_courseawards');
    } else if ($medal == 'a') {
        $title = get_string('medalsreport_achievement', 'report_courseawards');
    } else {
        $title = get_string('medalsreport_medals', 'report_courseawards');
    }
    $build_heading = $OUTPUT->heading($title.get_string('medalsreport_awarded', 'report_courseawards').' ('.
        get_string('asof', 'report_courseawards').$now.')');
    $csv .= '"'.$title.get_string('medalsreport_awarded', 'report_courseawards').'('.
        get_string('asof', 'report_courseawards').$now.')"'."\n";

    if (isset($medal) && !empty($medal)) {
        $build .= '<p style="text-align: center;"><img src="'.get_medal_img($medal).'" /></p>';
    }

    $build .= '<p>'.get_string('sortimg1', 'report_courseawards').'<img src="'.SORT_IMG.'" />'.
        get_string('sortimg2', 'report_courseawards').'</p>'."\n";
    $build .= '<p>'.get_string('vnreport_greytext', 'report_courseawards').'</p>'."\n";

    $csv .= '"'.get_string('course', 'report_courseawards').'","'.
        get_string('date', 'report_courseawards').'","'.
        get_string('medalawarded_csv', 'report_courseawards').'","'.
        get_string('medalawardedby_csv', 'report_courseawards').'","'.
        get_string('deleted_csv', 'report_courseawards').'"'."\n";

    // Start building the table.
    $build .= '<table>'."\n".'    <tr>'."\n";
    $build .= '        <th>'.get_string('position', 'report_courseawards').'</th>'."\n";
    // Sort out the 'course' column.
    if ($sort == 'c') {
        $build .= '        <th>'.get_string('course', 'report_courseawards').'<img src="'.SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th><a href="report.php?q=m&l='.$limit.'&s=c&m='.$medal.'">'.
            get_string('course', 'report_courseawards').'</a></th>'."\n";
    }
    // Sort out the 'date' column.
    if ($sort == 'd') {
        $build .= '        <th>'.get_string('medalsreport_dateachieved', 'report_courseawards').'<img src="'.
            SORT_IMG.'" /></th>'."\n";
    } else {
        $build .= '        <th><a href="report.php?q=m&l='.$limit.'&s=d&m='.$medal.'">'.
            get_string('medalsreport_dateachieved', 'report_courseawards').'</a></th>'."\n";
    }
    $build .= '        <th>'.get_string('medalawarded_csv', 'report_courseawards').'</th>'."\n";
    $build .= '        <th>'.get_string('medalawardedby_csv', 'report_courseawards').'</th>'."\n";
    $build .= '    </tr>'."\n";

    // Cyclicly put the results on the screen.
    foreach ($res as $row) {
        // CSV data first.

        // Course.
        $csv .= '"'.$row->fullname.' ('.$row->shortname.')",';
        // Date.
        $csv .= '"'.date($now_fmt, $row->date_added).'",';
        // Medal.
        $csv .= '"'.ucfirst($row->medal).'",';
        // Awarded by.
        $csv .= '"'.$row->firstname.' '.$row->lastname.'",';
        // Deleted notes.
        if (!$row->deleted > 0) {
            $csv .= '"'.get_string('no', 'report_courseawards').'"'."\n";
        } else {
            $csv .= '"'.get_string('yes', 'report_courseawards').'"'."\n";
        }

        // Data for screen next.
        if ($row->deleted == 1) {
            $build .= '    <tr class="deleted">'."\n";
        } else {
            $build .= '    <tr>'."\n";
        }
        $build .= '        <td>'.++$position.'</td>'."\n";
        $build .= '        <td><a href="'.PATH_COURSE.$row->course_id.'">'.$row->fullname.' ('.$row->shortname.')</a></td>'."\n";
        $build .= '        <td>'.date($now_fmt, $row->date_added);
        if ($row->deleted == 1) {
            $build .= '<br /><span class="deleted">'.get_string('debugdeleted', 'report_courseawards').
                date($now_fmt, $row->date_modified).'</span>';
        }
        $build .= '</td>'."\n";
        $build .= '        <td>'.ucfirst($row->medal).'</td>'."\n";
        $build .= '        <td><a href="'.PATH_USER.$row->user_id.'">'.$row->firstname.' '.$row->lastname.'</a>';
        if ($row->deleted == 1) {
            $build .= '<br /><span class="deleted">'.get_string('medalsreport_del', 'report_courseawards').
                $row->firstname.' '.$row->lastname.'</span>';
        }
        $build .= '</td>'."\n";

        $build .= '    </tr>'."\n";
    }

    // End table.
    $build .= '</table>'."\n";

} else {
    // What to do if the query ID doesn't match any query.
    $build .= get_string('error_noquery', 'report_courseawards')."\n";
}

// Sort out the output, whatever that's going to be.

if ($save_csv) {
    // Write the csv file to disk.
    $fh = fopen(FILE_CSV, 'w');
    if (!$fh) {
        die('File error: could not open file '.FILE_CSV.' for writing.');
    } else {
        fwrite($fh, $csv);
        fclose($fh);
    }

    $build .= '<p><a href="get_csv.php">'.get_string('saveascsv', 'report_courseawards').'</a>.</p>'."\n";
}
$build .= '<p><a href="index.php">'.get_string('backtolisting', 'report_courseawards').'</a>.</p>'."\n";
$build .= '</div>'."\n";

echo $OUTPUT->header();
echo $build_heading;
echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');
echo $build;
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
