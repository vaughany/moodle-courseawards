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
 * Course Awards admin report table of contents
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

require_capability('moodle/site:viewreports', get_context_instance(CONTEXT_SYSTEM));

admin_externalpage_setup('reportcourseawards', '', null, '', array('pagelayout'=>'report'));

$output = '<h3>'.get_string('coursewith', 'report_courseawards').'</h3>'."\n";

$output .= '<ul>'."\n";
$output .= '    <li>'.get_string('highscore', 'report_courseawards').' <a href="report.php?q=c&l=1&s=s">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=c&l=5&s=s">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=c&l=10&s=s">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=c&l=20&s=s">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=c&l=50&s=s">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=c&l=0&s=s">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('mostvotes', 'report_courseawards').' <a href="report.php?q=c&l=1&s=v">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=c&l=5&s=v">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=c&l=10&s=v">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=c&l=20&s=v">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=c&l=50&s=v">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=c&l=0&s=v">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('mostnotes', 'report_courseawards').' <a href="report.php?q=c&l=1&s=n">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=c&l=5&s=n">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=c&l=10&s=n">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=c&l=20&s=n">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=c&l=50&s=n">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=c&l=0&s=n">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('mostdeletedvotes', 'report_courseawards').' <a href="report.php?q=c&l=1&s=d">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=c&l=5&s=d">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=c&l=10&s=d">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=c&l=20&s=d">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=c&l=50&s=d">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=c&l=0&s=d">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('alphalist', 'report_courseawards').' <a href="report.php?q=c&l=0&s=a">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '</ul>'."\n";

$output .= '<h3>'.get_string('usershave', 'report_courseawards').'</h3>'."\n";

$output .= '<ul>'."\n";
$output .= '    <li>'.get_string('votedmost', 'report_courseawards').' <a href="report.php?q=u&l=1&s=v">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=u&l=5&s=v">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=u&l=10&s=v">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=u&l=20&s=v">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=u&l=50&s=v">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=u&l=0&s=v">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('votedhighest', 'report_courseawards').' <a href="report.php?q=u&l=1&s=h">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=u&l=5&s=h">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=u&l=10&s=h">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=u&l=20&s=h">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=u&l=50&s=h">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=u&l=0&s=h">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('votedlowest', 'report_courseawards').' <a href="report.php?q=u&l=1&s=l">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=u&l=5&s=l">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=u&l=10&s=l">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=u&l=20&s=l">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=u&l=50&s=l">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=u&l=0&s=l">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('notedmost', 'report_courseawards').' <a href="report.php?q=u&l=1&s=n">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=u&l=5&s=n">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=u&l=10&s=n">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=u&l=20&s=n">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=u&l=50&s=n">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=u&l=0&s=n">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('deletedmost', 'report_courseawards').' <a href="report.php?q=u&l=1&s=d">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=u&l=5&s=d">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=u&l=10&s=d">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=u&l=20&s=d">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=u&l=50&s=d">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=u&l=0&s=d">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('alphalist', 'report_courseawards').' <a href="report.php?q=u&l=0&s=a">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '</ul>'."\n";

$output .= '<h3>'.get_string('medalsawarded', 'report_courseawards').'</h3>'."\n";

$output .= '<ul>'."\n";
$output .= '    <li>'.get_string('gold', 'report_courseawards').' <a href="report.php?q=m&l=1&m=g&s=d">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=m&l=5&m=g&s=d">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=m&l=10&m=g&s=d">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=m&l=20&m=g&s=d">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=m&l=50&m=g&s=d">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=m&l=0&m=g&s=d">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('silver', 'report_courseawards').' <a href="report.php?q=m&l=1&m=s&s=d">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=m&l=5&m=s&s=d">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=m&l=10&m=s&s=d">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=m&l=20&m=s&s=d">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=m&l=50&m=s&s=d">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=m&l=0&m=s&s=d">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('bronze', 'report_courseawards').' <a href="report.php?q=m&l=1&m=b&s=d">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=m&l=5&m=b&s=d">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=m&l=10&m=b&s=d">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=m&l=20&m=b&s=d">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=m&l=50&m=b&s=d">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=m&l=0&m=b&s=d">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('achievement', 'report_courseawards').' <a href="report.php?q=m&l=1&m=a&s=d">'.
    get_string('top', 'report_courseawards').'</a> / <a href="report.php?q=m&l=5&m=a&s=d">'.
    get_string('five', 'report_courseawards').'</a> / <a href="report.php?q=m&l=10&m=a&s=d">'.
    get_string('ten', 'report_courseawards').'</a> / <a href="report.php?q=m&l=20&m=a&s=d">'.
    get_string('twenty', 'report_courseawards').'</a> / <a href="report.php?q=m&l=50&m=a&s=d">'.
    get_string('fifty', 'report_courseawards').'</a> / <a href="report.php?q=m&l=0&m=a&s=d">'.
    get_string('all', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li>'.get_string('listby', 'report_courseawards').' <a href="report.php?q=m&l=0&s=d">'.
    get_string('date', 'report_courseawards').'</a> / <a href="report.php?q=m&l=0&s=c">'.
    get_string('course', 'report_courseawards').'</a></li>'."\n";
$output .= '</ul>'."\n";

$output .= '<h3>'.get_string('blocklist', 'report_courseawards').'</h3>'."\n";

$output .= '<ul>'."\n";

// Get the block id of the courseaward_* blocks so we can link to the list of where they're installed.
if ($res = $DB->get_record('block', array('name'=>'courseaward_vote'))) {
    $vid =  $res->id;
} else {
    print_error(get_string('error_noblock', 'report_courseawards'));
    die();
}
$res = $DB->get_record('block', array('name'=>'courseaward_medal'));
$mid = $res->id;

$output .= '    <li><a href="'.$CFG->wwwroot.'/course/search.php?blocklist='.$vid.'&sesskey='.$USER->sesskey.'">'.
    get_string('listvote', 'report_courseawards').'</a></li>'."\n";
$output .= '    <li><a href="'.$CFG->wwwroot.'/course/search.php?blocklist='.$mid.'&sesskey='.$USER->sesskey.'">'.
    get_string('listmedal', 'report_courseawards').'</a></li>'."\n";
$output .= "</ul>\n";

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string(get_string('courseawardstoctitle', 'report_courseawards')));
echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');
echo $output;
echo $OUTPUT->box_end();


// A section for a few minor admin operations.
if ($CFG->dbtype == 'mysqli') {
    $output_admin = '<h3>'.get_string('admin_backuptitle', 'report_courseawards').'</h3>'."\n";
    $output_admin .= "<ul>\n";
    $output_admin .= '    <li><a href="admin.php?q=backup">'.
        get_string('admin_backupdatabase', 'report_courseawards')."</a></li>\n";
    $output_admin .= "</ul>\n";
}
$output_admin .= '<h3>'.get_string('admin_medalstitle', 'report_courseawards').'</h3>'."\n";
$output_admin .= "<ul>\n";
$output_admin .= '    <li><a href="admin.php?q=medalremove">'.
    get_string('admin_removeallmedals', 'report_courseawards')."</a></li>\n";
$output_admin .= '    <li><a href="admin.php?q=medaldelete">'.
    get_string('admin_deleteallmedalhistory', 'report_courseawards')."</a></li>\n";
$output_admin .= "</ul>\n";
$output_admin .= '<h3>'.get_string('admin_notestitle', 'report_courseawards').'</h3>'."\n";
$output_admin .= "<ul>\n";
$output_admin .= '    <li><a href="admin.php?q=noteswipe">'.
    get_string('admin_removeallnotes', 'report_courseawards')."</a></li>\n";
$output_admin .= "</ul>\n";
$output_admin .= '<h3>'.get_string('admin_votestitle', 'report_courseawards').'</h3>'."\n";
$output_admin .= "<ul>\n";
$output_admin .= '    <li><a href="admin.php?q=voteremove">'.
    get_string('admin_removeallvotes', 'report_courseawards')."</a></li>\n";
$output_admin .= '    <li><a href="admin.php?q=votedelete">'.
    get_string('admin_deleteallvotehistory', 'report_courseawards')."</a></li>\n";
$output_admin .= "</ul>\n";

$output_admin .= '<h3>'.get_string('admin_courseclearingtitle', 'report_courseawards').'</h3>'."\n";
$output_admin .= 'Remove all votes from: ';
$output_admin .= '<form name="wipecourse" method="get" action="admin.php"><select name="w">';
$res = $DB->get_records_sql('SELECT DISTINCT course_id FROM '.$CFG->prefix.'block_courseaward_vote ORDER BY course_id ASC');
if ($res) {
    foreach ($res as $row) {
        $res2 = $DB->get_record_select('course', 'id = '.$row->course_id, array('fullname'));
        if ($res2) {
            $output_admin .= '<option value="'.$row->course_id.'">['.$row->course_id.'] '.$res2->fullname.'</option>'."<br />\n";
        }
    }
    $output_admin .= '</select>';
    $output_admin .= '<input type="hidden" name="q" value="wipecoursevotes" />';
    $output_admin .= '<input type="submit" value="Clear this course" />';
    $output_admin .= '</form>';
} else {
    $output_admin .= '<option disabled="disabled" selected="selected">No courses with votes.</option>';
    $output_admin .= '<input disabled="disabled" type="submit" value="Clear this course" />';
    $output_admin .= '</select></form>';
}

// Statistics.
$output_admin .= '<h3>'.get_string('stats', 'report_courseawards').'</h3>'."\n";
$output_admin .= '<ul>'."\n";

// Votes.
$vlive = $DB->get_record_sql("SELECT COUNT(id) AS cid FROM ".
    $CFG->prefix."block_courseaward_vote WHERE deleted = '0'");
$output_admin .= '    <li>'.get_string('debugvotes', 'report_courseawards').
    get_string('debuglive', 'report_courseawards').$vlive->cid;
$vdel = $DB->get_record_sql("SELECT COUNT(id) AS cid FROM ".
    $CFG->prefix."block_courseaward_vote WHERE deleted = '1'");
$output_admin .= '; '.get_string('debugdeleted', 'report_courseawards').$vdel->cid;
$output_admin .= '; '.get_string('debugtotal', 'report_courseawards').($vlive->cid + $vdel->cid).';</li>'."\n";

// Notes.
$nlive = $DB->get_record_sql("SELECT COUNT(id) AS cid FROM ".
    $CFG->prefix."block_courseaward_vote WHERE deleted = '0' AND note <> ''");
$output_admin .= '    <li>'.get_string('debugnotes', 'report_courseawards').
    get_string('debuglive', 'report_courseawards').$nlive->cid;
$ndel = $DB->get_record_sql("SELECT COUNT(id) AS cid FROM ".
    $CFG->prefix."block_courseaward_vote WHERE deleted = '1' AND note <> ''");
$output_admin .= '; '.get_string('debugdeleted', 'report_courseawards').$ndel->cid;
$output_admin .= '; '.get_string('debugtotal', 'report_courseawards').($nlive->cid + $ndel->cid).';</li>'."\n";

// Users and courses.
$uno = $DB->get_record_sql("SELECT COUNT(DISTINCT user_id) AS cid FROM ".$CFG->prefix."block_courseaward_vote");
$cno = $DB->get_record_sql("SELECT COUNT(DISTINCT course_id) AS cid FROM ".$CFG->prefix."block_courseaward_vote");
$output_admin .= '    <li>'.$uno->cid.get_string('debugusers', 'report_courseawards').$cno->cid.
    get_string('debugcourses', 'report_courseawards').'</li>'."\n";

// Medals.
$mlive = $DB->get_record_sql("SELECT COUNT(id) AS cid FROM ".
    $CFG->prefix."block_courseaward_medal WHERE deleted = '0'");
$output_admin .= '    <li>'.get_string('debugmedals', 'report_courseawards').
    get_string('debugawarded', 'report_courseawards').$mlive->cid;
$mdel = $DB->get_record_sql("SELECT COUNT(id) AS cid FROM ".$CFG->prefix."block_courseaward_medal WHERE deleted = '1'");
$output_admin .= '; '.get_string('debugdeleted', 'report_courseawards').$mdel->cid;
$output_admin .= '; '.get_string('debugtotal', 'report_courseawards').($mlive->cid + $mdel->cid).';</li>'."\n";
$output_admin .= "</ul>\n";

echo $OUTPUT->heading(format_string(get_string('admin_toc', 'report_courseawards')));
echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');
echo $output_admin;
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
