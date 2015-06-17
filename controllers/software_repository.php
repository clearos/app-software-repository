<?php

/**
 * Software repository controller.
 *
 * @category   apps
 * @package    software-repository
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011-2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_repository/
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
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Software respository controller.
 *
 * @category   apps
 * @package    software-repository
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011-2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_repository/
 */

class Software_Repository extends ClearOS_Controller
{
    /**
     * Software_Repository default controller
     *
     * @return view
     */

    function index()
    {
        // Load libraries
        //---------------

        $this->lang->load('software_repository');
        $this->load->library('base/Yum');

        // Load view data
        //---------------

        $options = array();

        // Load views
        //-----------

        $this->page->view_form('software_repository/summary', $data, lang('software_repository_app_name'), $options);
    }

    /**
     * Software_Repository update repo enable status
     *
     * @return view
     */

    function update($name, $enabled)
    {
        // Load libraries
        //---------------

        $this->load->library('base/Yum');

        try {
            $this->yum->set_enabled($name, $enabled);
            redirect('/software_repository');
            return;
        } catch (Exception $e) {
            $this->page->set_message(clearos_exception_code($e), 'info');
        }

        $this->index('simple');
    }

    /**
     * Ajax get repo list controller
     *
     * @return JSON
     */

    function get_repo_list()
    {
        clearos_profile(__METHOD__, __LINE__);

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');
        try {

            $this->lang->load('software_repository');
            $this->load->library('base/Yum');

            // This is an AJAX call anyway...let Marketplace stuff run first
            sleep(2);
            $counter = 0;
            if ($this->yum->is_busy()) {
                sleep(3);
                $counter++;
                if ($counter > 3)
                    throw new Yum_Busy_Exception();
            }

            echo json_encode(Array('code' => 0, 'list' => $this->yum->get_live_repo_list()));

        } catch (Yum_Busy_Exception $e) {
            echo json_encode(Array('code' => clearos_exception_code($e), 'errmsg' => lang('software_repository_busy')));
        } catch (\Exception $e) {
            echo json_encode(Array('code' => clearos_exception_code($e), 'errmsg' => clearos_exception_message($e)));
        }
    }
}
