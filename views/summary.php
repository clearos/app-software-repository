<?php

/**
 * Software repository overview.
 *
 * @category   apps
 * @package    software-repository
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011-2016 ClearFoundation
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

if (count($warnings))
    echo infobox_warning(
        lang('base_warning'),
        "<div>" . lang('software_repository_warning') . "</div>" .
        "<br />" .
        "<ul>" .
        "<li>" . implode("</li><li>", $warnings) . "</li>" .
        "</ul>"
    );

if ($highlight)
    echo infobox_highlight(
        lang('base_information'),
        lang('software_repository_add_note:') . " <b>" . $highlight . "</b>."
    );

///////////////////////////////////////////////////////////////////////////////
// Buttons
///////////////////////////////////////////////////////////////////////////////

$link = '/app/software_repository/index/detailed';
$anchor_title = lang('base_detailed_view');

//$buttons = array(
//    anchor_custom($link, $anchor_title, 'high')
//);

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('software_repository_name'),
    lang('base_description'),
    lang('base_enabled'),
    lang('software_repository_number_of_packages')
);

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

// Done in Ajax/JS

///////////////////////////////////////////////////////////////////////////////
// List table
///////////////////////////////////////////////////////////////////////////////

$options = array(
    'id' => 'list',
    'responsive' => array(1 => 'none'),
    'empty_table_message' => loading('normal', lang('base_loading...'))
);
echo summary_table(
    lang('software_repository_repo_list'),
    $buttons,
    $headers,
    NULL,
    $options
);
