# Course Awards Plug-in for Moodle 2.0

A quick and easy way of getting a course's rating from students, awarding courses medals based on score and reporting on courses Moodle-wide.

## Introduction

The Course Awards plug-in system consists of two blocks ('Vote' and 'Medal') and a report. Its purpose is to easily collect user feedback about a course with appropriate back-end reporting.

It is probably a good idea to fully read through this readme before embarking on any installation or bug reporting.

## Purpose

The Course Awards blocks and report form part of a much larger Moodle course rating system (called Moodle Medals) which is used to recognise good practice on Moodle courses and also provide targets for course development. Learner feedback is used to inform medals awarded, however a course must also meet or exceed other criteria to gain a medal. The Bronze medal has basic criteria which must be met, the Silver medal has moderate criteria and the Gold medal has quite strict criteria.  The criteria themselves and how they are judged is performed by staff manually: this plugin is used to inform one aspect of that process.

Irrespective of this, the blocks and report can still be used to gain useful feedback from students about Moodle courses.

---

## The Vote Block

**Students** get to vote by clicking on a coloured star (with tool-tip explanation of what each star means). As default, the student can also add in a brief note (this can be turned off if required).

Once a student has voted, the block changes to show them their vote and, if they left one, their note, as well as average score so far and number of votes. As default they are prevented from removing their vote and voting again for a period of one day, but this is configurable from 'immediately' to 'never', depending on preference.

**Teachers** see a summary of votes cast and notes left, but not who voted and what notes they left. Teachers cannot vote, or manage votes or notes in any way. (Note that if a person has the Teacher role in one course and the Student role in another, they can vote for the course for which they have the Student role.)

**Administrators** cannot vote, but can see and manage all votes and notes for all courses, including the history of deleted votes and notes in summary. Admins also get a direct link to the report for that course, as well as a link to the report's index page.

Once the Vote block is added to a course (by anyone with permission to do so), anyone with the role of Student can vote using a system of stars (using the UK's Ofsted grades of Outstanding, Good, Satisfactory or Poor) and can optionally write a note too.

*Note:* Only users with the role of *Student* can vote, but anyone can see the average score so far and number of votes.

### Important

Remember that one person can have different roles (e.g. student, teacher, non-editing teacher) in different courses. Simon Student may have the role of Student on course 1 but may be a Teacher on course 2, therefore will be allowed to vote on course 1 only.  This is customisable using Moodle's roles and capabilities: see the section on *Changing who can Vote* for more information.

---

## The Medal Block

The medal block is for use by administrators only. When added to a course, it presents options to add one of the three medals or single achievement ribbon available, or remove one if it has already been awarded. It also shows a history of previously awarded/removed medals. If no medal has been awarded, the block is hidden from students and teachers.

The Medal block allows Admins *only* to award the course with a gold, silver or bronze medal, or an 'achievement' ribbon. This is a manual process as the awarding of the medals is based on the score from the Vote block **as well as a number of other factors about the course determined externally to Moodle**.
Anyone can see the medal once it has been awarded, as long as the Medal block is visible on the course.

---

## The Admin Report

For administrators only, there is a comprehensive report detailing the courses with most/least votes, all notes, students who voted the most/highest/lowest, etc. Each report can be sorted by different criteria and can be saved as a CSV file if desired.

Admins can jump directly from a course to the admin report using the vote block, and the report is also accessible by clicking Reports &rarr; Course Awards on the Admin block.

### Administrative Options (Destructive!)

*Here be dragons. Consider yourself warned.*

A more recent addition is the destructive administrative options section further down. As the title suggests, they are operations to clear out part or all of the live and/or deleted votes and notes. They perform actions on the database tables used by the blocks, and any data removed is gone for good: a student deleting a vote simply flags that vote as deleted, but removing all votes via this report will permanently remove them from the database for good.

---

## Installation

Installation is a matter of copying files to the correct locations within your Moodle installation, but it is always wise to test new plugins in a sandbox environment first, and have the ability to roll back changes

Download the archive(s) and extract the files, or [clone the repository from GitHub](https://github.com/vaughany/moodle-courseawards/). You should see the following files and structure:

    courseawards/
    |-- blocks
    |   |-- courseaward_medal
    |   |   |-- admin_medal.php
    |   |   |-- admin_unmedal.php
    |   |   |-- block_courseaward_medal.php
    |   |   |-- db
    |   |   |   |-- access.php
    |   |   |   `-- install.xml
    |   |   |-- img
    |   |   |   |-- medal_achievement.png
    |   |   |   |-- medal_bronze.png
    |   |   |   |-- medal_gold.png
    |   |   |   `-- medal_silver.png
    |   |   |-- lang
    |   |   |   `-- en
    |   |   |       `-- block_courseaward_medal.php
    |   |   |-- libmedal.php
    |   |   |-- styles.css
    |   |   `-- version.php
    |   `-- courseaward_vote
    |       |-- block_courseaward_vote.php
    |       |-- db
    |       |   |-- access.php
    |       |   `-- install.xml
    |       |-- img
    |       |   |-- 0.png
    |       |   |-- 1.png
    |       |   |-- 2.png
    |       |   |-- 3.png
    |       |   |-- d.png
    |       |   `-- p.png
    |       |-- lang
    |       |   `-- en
    |       |       `-- block_courseaward_vote.php
    |       |-- libvote.php
    |       |-- settings.php
    |       |-- styles.css
    |       |-- unvote.php
    |       |-- version.php
    |       `-- vote.php
    `-- report
        `-- courseawards
            |-- admin.php
            |-- get_csv.php
            |-- img
            |   |-- arrow_down.png
            |   |-- cross.png
            |   `-- tick.png
            |-- index.php
            |-- lang
            |   `-- en
            |       `-- report_courseawards.php
            |-- report.php
            |-- settings.php
            |-- styles.css
            `-- version.php

Copy the extracted 'blocks' and 'report' folders into the root of your Moodle installation and all the files will automatically go into the correct places. Note that nothing else will be overwritten.

Log in to your Moodle as Admin and click on Notifications on the Admin menu.

The blocks and admin report should successfully install. If you receive any error messages, please [raise an issue on GitHub](https://github.com/vaughany/moodle-courseawards/issues).

> **Note:** The two blocks and report were designed to be installed together, but all the data is created by the Vote block, so ensure you install that one as a minimum. The reports are nice to have, but not essential, and the medal block stands alone and can go unused if preferred.

### CSV save path

The CSV file, created when a report is run, is saved to the following location:

    $CFG->dataroot.'/temp/courseawards-report.csv'

...which is defined in two places:

    /report/courseawards/report.php:62:     define('FILE_CSV', $CFG->dataroot.'/temp/courseawards-report.csv');
    /report/courseawards/get_csv.php:34:    define('FILE_CSV', $CFG->dataroot.'/temp/courseawards-report.csv');

This location is part of Moodle and should already be writable by the web server. If you experience problems, ensure the path is writable, touch the file and give it full read/write permissions, or change to a different location (remember to change both the above DEFINE statements and ensure they are identical).


---

## Use

### Vote Block

* Go to a course as Teacher role or better.
* Turn on editing and add the 'Course Award - Vote' block and move it where you wish. At this time you can only add one per course.
* Students can now vote on that course. Teachers can see the number of votes and average score so far and Admins can also jump to the report.
* Delete the block in the usual manner.

> **Note:** If you remove the block, the data is retained. Simply re-add the block to get the data back.

### Medal Block

* Go to a course as Teacher role or better.
* Turn on editing and add the 'Course Award - Medal' block and move it where you wish.
* *Administrators* can now award a medal.
* Delete the block in the usual manner.

> **Note:** If you remove the block, the data is retained. Simply re-add the block to get the data back.

### Report

* Reports are *currently* available to site administrators only.
* On the Admin menu, click Reports, then Course Awards.
* Click on the report you would like to see.
* You can sort the reports differently by clicking on the column titles.
* If the course has votes or notes, click on the number in the votes/notes column and you will see specific details about that course's votes and notes.
* If no courses have been voted on, no data will be available.
* Click 'Save as CSV' to do just that.
* Pie charts are and require an internet connection.

> **Note:** The pie chart is dependent on [Google Chart Tools](http://code.google.com/apis/chart/interactive/docs/gallery/piechart.html) to generate charts on the fly. This requires an internet connection. If one is not available, the image will not be shown. Due to the interactive nature of the chart, it cannot be saved as an image in the same way as the previous version. There are many good desktop apps and browser plugins for saving screenshots.

### Save as CSV

This option appears on all pages where there is a table of data relating to courses or users. Use it to save the results out as a CSV file, which can then be opened with your favourite spreadsheet or word processing application.

> **Note:** If two admins are generating reports concurrently, the CSV file will be overwritten by the most recently generated report.

---

## Configuration of the Vote block

The Vote block is the only block which has configuration options. These options can only be changed by a site Admin, and affect the block throughout the whole site.

Either:

* on the Admin block, click *Site Administration &rarr; Plugins &rarr; Blocks &rarr; Course Awards - Vote*

...or:

* from the Manage Blocks screen, click on *Settings* to the right of *Course Award - Vote*.

### Vote timeout

This drop-down menu specifies how much time should pass before the voter can remove their vote and (potentially) vote again. The current range is 'no delay' to 'never'.

### Collect Notes?

As default, the block presents a small text box through which the user may write a note or comment about the course being voted on. (In reality, this will store several pages of text but the box is presented small to try to keep notes brief and concise.)

It can be turned off if notes are not required (although any already collected will be saved): simply un-tick the checkbox. Students will be presented with the same block but without the textbox.

---

## Changing who can Vote and Administrate

Installing the blocks gives Moodle some new capabilities:

* Administer the *Course Awards - Medal* block: `block/courseaward_medal:admin`
* Administer the *Course Awards - Vote* block: `block/courseaward_vote:admin`
* Vote in the *Course Awards - Vote* block: `block/courseaward_vote:vote`

> **Note:** Think of capabilities of rules which govern how Moodle decides who can do things.

Also, the capabilities were specifically assigned to these roles:

* The two 'administer' capabilities are assigned to the *Administrator* role.
* The 'vote' capability is assigned to the *Student* role.
* *Teachers* and *Non-Editing Teachers* are specifically prevented from being allowed to vote.

So when you install the blocks, Admins can administrate the blocks and Students can vote. This works well as a default, but you can make changes if you wish.

> ***Important:*** Teachers can still add and remove the Vote and Medal blocks, just as they can with any block, however this does not mean that the votes already cast or medals already awarded will be lost. If a block is removed, just add it again, and all votes and medals will reappear.

> **Note:** *Uninstalling* the plugin drops the database tables and removes all the data for good.

### Assigning Capabilities to Roles

If you want to change the defaults for who (or more correctly, which role) can do what (has which capabilities), this is how.

* Log in to your Moodle as Administrator
* On the Admin block, click *Site Administration &rarr; Users &rarr; Permissions &rarr; Define Roles*
* Click the role you want to change. For the purposes of example, we have a role called 'School Pupil' we want to allow to vote on courses.
* Click the Edit icon (hand-and-pen) next to the role to be edited.
* Find the correct permission. This is a big list so use your browser's search function and locate '*course award*'.
* Change the permission for '*Vote in the Course Awards - Vote block*' to Allow: place a tick in the checkbox.
* Scroll to the bottom of the page and click *Save*.

You should now find that anyone with the role you modified can now vote. Remember this will affect all users with that role.

> **Caution:** Caution should be exercised when assigning permissions to roles generally.

### Changing Permissions on the Admin Report

Moodle's reports (Admin block, click Reports) can be viewed only by site administrators. As such, the Course Awards reports are available only to site administrators, not Course Awards block admins.

---

## Code and Image Changes

Some aspects of the blocks/report can be changed but this cannot be achieved though configuration, and will require changes to the code. A good code management and versioning system, such as Git, is highly recommended.

### Replacing the Images

It is quite possible to replace the default images with those of your own choosing:

* In each block's folder, as well as that of the admin report, there is an 'img/' folder containing images.
* Replace these images with your chosen images.
* To avoid having to change the code, save new images over the existing images. Do not use new names.

> **Note:** You may want to make copies of the images before you change them so you can replace them later.

> **Caution:** The code does not specify any widths or heights for images, so that you can use your own and they do not have to be the same size as the default images. However, it is not recommended to go too much wider than the default images as you may start experiencing layout problems with your columns. Experiment.

---

## Known Issues

There are no known bugs at this time, but it doesn't mean they're not lurking. Should you find a bug, please [log an issue in the tracker](https://github.com/vaughany/moodle-courseawards/issues) or fork the repo, fix the problem and submit a pull request.

---

## To Do

* Currently the reports are available to Admins only. Moodle now differentiates between site reports and course reports, so a teacher-accessible course report is on the agenda for the next version.
* Logging of use of the admin reports was around in the 1.9 version, so I should probably add it back in again. For completeness' sake, if nothing else.

Suggestions for features or submissions of non-en language packs are most welcome.

---

## History

**February 8th, 2012**

* Version 2.0 for Moodle 2.x
* Build 2012020800