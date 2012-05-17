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
 * Language strings for the Course Awards admin report
 *
 * @package    report
 * @subpackage courseawards
 * @copyright  2011 onwards Paul Vaughan, paulvaughan@southdevon.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Admin panel string.
$string['pluginname'] = 'Course Awards';
$string['courseawardstoctitle'] = 'Course Awards - Various Reports';

// Report ToC strings.
$string['coursewith'] = 'Course with...';
$string['usershave'] = 'Users who have...';
$string['medalsawarded'] = 'Medals Awarded...';
$string['blocklist'] = 'Links to Moodle\'s own blocks list';
$string['stats'] = 'Statistics';

$string['top'] = 'Top';
$string['five'] = '5';
$string['ten'] = '10';
$string['twenty'] = '20';
$string['fifty'] = '50';
$string['onehundred'] = '100';
$string['all'] = 'All';
$string['date'] = 'Date';
$string['course'] = 'Course';

$string['highscore'] = '...the highest score:';
$string['mostvotes'] = '...the most votes:';
$string['mostnotes'] = '...the most notes:';
$string['mostdeletedvotes'] = '...the most deleted votes:';
$string['alphalist'] = '...an alphabetical list:';

$string['votedmost'] = '...voted the most:';
$string['votedhighest'] = '...voted the highest:';
$string['votedlowest'] = '...voted the lowest:';
$string['notedmost'] = '...noted the most:';
$string['deletedmost'] = '...deleted the most votes:';

$string['gold'] = '...Gold:';
$string['silver'] = '...Silver:';
$string['bronze'] = '...Bronze:';
$string['achievement'] = '...Achievement Ribbon:';
$string['listby'] = '...list by:';

$string['listvote'] = 'A list of courses with the Vote block added';
$string['listmedal'] = 'A list of courses with the Medal block added';

// Report strings.
$string['sortimg1'] = 'This image: ';
$string['sortimg2'] = ' indicates the column which is sorted.';
$string['outstanding'] = 'Outstanding';
$string['good'] = 'Good';
$string['satisfactory'] = 'Satisfactory';
$string['inadequate'] = 'Inadequate';
$string['noresults'] = 'No results found. <a href="index.php">Go back</a> to try another report.';
$string['novotesnotes'] = 'No votes or notes found.';
$string['asof'] = 'as of ';
$string['backtolisting'] = 'Report Index';

$string['position'] = 'Pos';
$string['course'] = 'Course';
$string['user'] = 'User';
$string['score'] = 'Score (out of 3) and %age';
$string['votes'] = 'Votes (Deleted)';
$string['notes'] = 'Notes (Deleted)';
$string['medals'] = 'Medal';
$string['date'] = 'Date';
$string['name'] = 'Name';
$string['deleted'] = ' deleted';

// Course report strings.
$string['coursereport_single'] = 'Single Course Report';
$string['coursereport_highscore'] = 'Highest Scoring Courses';
$string['coursereport_mostvoted'] = 'Most Voted-For Courses';
$string['coursereport_mostdeleted'] = 'Courses with Most Deleted Votes';
$string['coursereport_mostnoted'] = 'Most Noted Courses';
$string['coursereport_list'] = 'Simple List of all courses with votes';

// User report strings.
$string['userreport_single'] = 'Single User Report';
$string['userreport_votedmost'] = 'Users who Voted the Most';
$string['userreport_votedhighest'] = 'Users who Voted the Highest';
$string['userreport_votedlowest'] = 'Users who Voted the Lowest';
$string['userreport_notedmost'] = 'Users who Added the most Notes';
$string['userreport_mostdeleted'] = 'Users who Deleted the most Votes';
$string['userreport_list'] = 'Simple List of all courses with votes';

// Votes notes report.
$string['vnreport_title'] = 'Votes and Notes for ';
$string['vnreport_errortype'] = 'Type could not be determined as either a course or a user.';
$string['vnreport_summary'] = 'Summary (Live votes only)';
$string['vnreport_vote'] = 'Vote';
$string['vnreport_votecast'] = 'Votes Cast';
$string['vnreport_percentage'] = 'Percentage';
$string['vnreport_note'] = 'Note';
$string['vnreport_coursescore'] = 'This course\'s score is ';
$string['vnreport_orpercentage'] = ' out of 3, or ';
$string['vnreport_percent'] = '%.';
$string['vnreport_bigchart'] = 'Click for larger version';
$string['vnreport_medalawarded'] = 'Medal awarded';
$string['vnreport_courseawarded'] = 'This course has been awarded ';
$string['vnreport_detail'] = 'Detail';
$string['vnreport_greytext'] = 'Greyed out text is a deleted vote.';
$string['vnreport_del'] = 'Del?';
$string['vnreport_added'] = 'Added: ';
$string['vnreport_deleted'] = 'Deleted: ';
$string['vnreport_delvotenote'] = 'Delete this vote (and note)?';

// Medals report.
$string['medalsreport_gold'] = 'Gold Medals';
$string['medalsreport_silver'] = 'Silver Medals';
$string['medalsreport_bronze'] = 'Bronze Medals';
$string['medalsreport_achievement'] = 'Achievement Ribbons';
$string['medalsreport_medals'] = 'Medals';
$string['medalsreport_awarded'] = ' Awarded ';
$string['medalsreport_dateachieved'] = 'Date Achieved ';
$string['medalsreport_del'] = 'Del: ';


// CSV strings.
$string['score_csv'] = 'Score (out of 3)';
$string['percentage_csv'] = 'Percentage';
$string['votes_csv'] = 'Votes';
$string['notes_csv'] = 'Notes';
$string['vote_csv'] = 'Vote';
$string['note_csv'] = 'Note';
$string['deleted_csv'] = 'Deleted ';
$string['medalawarded_csv'] = 'Medal Awarded';
$string['medalawardedby_csv'] = 'Awarded by';
$string['yes'] = 'yes';
$string['no'] = 'no';
$string['saveascsv'] = 'Click here to save this table as a CSV (comma separated values) file';

// Error strings.
$string['error_admin'] = 'Sorry, you need to be a site Admin to see the Course Reports.';
$string['error_query'] = 'Sorry, the query could not be composed properly and cannot be run.';
$string['error_noquery'] = 'Sorry, the URL is malformed and an appropriate report could not be generated.';
$string['error_notmysql'] = 'Sorry, you need to be using MySQL for this to work.';
$string['error_nomedals'] = 'There are no medals to remove.';
$string['error_noremovedmedals'] = 'There are no removed medals to delete.';
$string['error_nonotes'] = 'There are no live notes to remove.';
$string['error_novotes'] = 'There are no live votes to remove.';
$string['error_noanyvotes'] = 'There are no votes to remove.';
$string['error_noblock'] = '"Sorry, couldn\'t get the block ID for the \'courseawards_vote\' block. Have you installed it? If so, please report this error to the developer.';
$string['error_dbupdate'] = 'Database not updated for some reason.';

// Debugging section.
$string['debugvotes'] = 'Votes: ';
$string['debugnotes'] = 'Notes: ';
$string['debugmedals'] = 'Medals: ';
$string['debuglive'] = 'Live: ';
$string['debugawarded'] = 'Awarded: ';
$string['debugdeleted'] = 'Deleted: ';
$string['debugtotal'] = 'Total: ';
$string['debugusers'] = ' distinct users have voted on ';
$string['debugcourses'] = ' distinct courses';

// Administration section.
$string['admin_toc'] = 'Administrative Options (Destructive!)';
$string['admin_backuptitle'] = 'Backup Options';
$string['admin_backupdatabase'] = '<strong>EXPERIMENTAL:</strong> Backup the votes, notes and medals. (MySQL databases only.)';
$string['admin_medalstitle'] = 'Medals Options';
$string['admin_removeallmedals'] = 'Remove all medals from all courses, keeping the history of what has been awarded/removed. (Will show as deleted by you.)';
$string['admin_deleteallmedalhistory'] = 'Delete all medal <em>history</em> (what has previously been awarded/removed).';
$string['admin_notestitle'] = 'Notes Options';
$string['admin_removeallnotes'] = 'Wipe all notes from all courses, keeping votes.';
$string['admin_votestitle'] = 'Votes Options';
$string['admin_removeallvotes'] = 'Remove all votes from all courses, keeping the history of votes and notes.';
$string['admin_deleteallvotehistory'] = 'Delete all votes (and notes) <em>history</em> (what has previously been awarded and removed).';
$string['admin_courseclearingtitle'] = 'Course Clearing Options';
