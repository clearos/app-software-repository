<?php

/**
 * Software repository overview.
 *
 * @category   Apps
 * @package    Software_Repository
 * @subpackage Views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearcenter.com/support/documentation/clearos/software_repository/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.  
//  
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('base');
$this->lang->load('software_repository');

///////////////////////////////////////////////////////////////////////////////
// Warning box
///////////////////////////////////////////////////////////////////////////////

echo "<div id='software_repository_warning_box' class='theme-hidden'>";
echo infobox_warning(
    lang('base_warning'),
    "<div id='software_repository_warning'>" . lang('software_repository_warning') . "</div>"
);
echo "</div>";

if ($highlight)
    echo infobox_highlight(
        lang('base_information'),
        lang('software_repository_add_note') . ": <b>" . $highlight . "</b>."
    );

///////////////////////////////////////////////////////////////////////////////
// Buttons
///////////////////////////////////////////////////////////////////////////////

$link = '/app/software_repository/index/detailed';
if ($report_type === 'detailed')
    $link = '/app/software_repository';

$buttons = array(
    anchor_custom(
        $link,
        ($report_type === 'detailed' ? lang('software_repository_simple') : lang('software_repository_detailed')), 'high'
    )
);

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

if ($report_type === 'detailed')
    $headers = array(
        lang('software_repository_name'),
        lang('base_description'),
        lang('software_repository_enabled'),
        lang('software_repository_number_of_packages')
    );
else
    $headers = array(
        lang('software_repository_name'),
        lang('base_description'),
        lang('software_repository_enabled')
    );

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

// Done in Ajax/JS

///////////////////////////////////////////////////////////////////////////////
// List table
///////////////////////////////////////////////////////////////////////////////

echo summary_table(
    lang('software_repository_repo_list'),
    $buttons,
    $headers,
    NULL,
    array('id' => 'list', 'no_action' => ($report_type === 'detailed' ? FALSE : TRUE))
);
echo "<input type='hidden' name='report_type' id='report_type' value='$report_type'>";
