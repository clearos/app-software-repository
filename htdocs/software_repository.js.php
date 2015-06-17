<?php

/**
 * Javascript helper for Software_Repository.
 *
 * @category   apps
 * @package    software-repository
 * @subpackage javascript
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2003-2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/account_import/
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
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

clearos_load_language('software_repository');
clearos_load_language('base');

// TODO: Merge dialog into theme

header('Content-Type: application/x-javascript');

echo "

$(document).ready(function() {
    // Default fields to hide
    //-----------------------
    $('#software_repository_warning_box').hide();

    get_list();
});

function get_list() {
    // Removes first row
    $('#list tbody').empty();
    var table_list = get_table_list();

    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: '/app/software_repository/get_repo_list',
        data: '',
        success: function(json) {
            var test_repo_enabled = false;
            var test_repos = [ 'clearos-dev', 'clearos-test', 'clearos-updates-testing', 'clearos-developer', 'clearos-epel' ];
            if (json.code < 0) {
                $('#software_repository_warning_box').show();
                $('#software_repository_warning').html(json.errmsg);
            } else {
                table_list.fnClearTable();
                for (var id in json.list) {
                    if ($.inArray(id, test_repos) >= 0 && json.list[id].enabled)
                        test_repo_enabled = true;
                    
                    table_list.fnAddData([
                        id,
                        json.list[id].name,
                        (json.list[id].enabled ? clearos_enabled() : clearos_disabled()),
                        json.list[id].packages,
                        (id.lastIndexOf('private-', 0) === 0) ? '' : toggle_button(id, (json.list[id].enabled ? 0 : 1))
                    ]);
                }

                table_list.fnAdjustColumnSizing();
                if (test_repo_enabled)
                    $('#software_repository_warning_box').show();
            }
        },
        error: function(xhr, text, err) {
            $('#software_repository_warning_box').show();
            $('#software_repository_warning').html(json.errmsg);
        }
    });
}

function toggle_button(basename, action) {
    
    return '<a href=\'/app/software_repository/update/' + basename + '/' + action + '\' id=\'' + basename + '\' class=\'btn btn-primary btn-sm\'>" . lang('base_toggle') . "</a>';
}


";

// vim: syntax=php ts=4
