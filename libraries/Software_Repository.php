<?php

/**
 * Software repository class.
 *
 * @category   apps
 * @package    software-repository
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012-2016 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_repository/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\software_repository;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('software_repository');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\base\Engine as Engine;
use \clearos\apps\base\Yum as Yum;
use \clearos\apps\events\Event_Utils as Event_Utils;
use \clearos\apps\tasks\Cron as Cron;

clearos_load_library('base/Engine');
clearos_load_library('base/Yum');
clearos_load_library('events/Event_Utils');
clearos_load_library('tasks/Cron');

// Exceptions
//-----------

use \Exception as Exception;


///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Software repository class.
 *
 * @category   apps
 * @package    software-repository
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012-2016 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_repository/
 */

class Software_Repository extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const WARN_TEST_REPO  = 100;
    const WARN_UPDATES_DISABLE  = 200;
    const WARN_UPDATES_ENABLE  = 201;
    const WARN_CENTOS_DISABLE = 300;
    const WARN_CENTOS_ENABLE = 301;
    const WARN_CENTOS_UPDATES_DISABLE = 403;
    const WARN_CENTOS_UPDATES_ENABLE = 404;
    const WARN_EPEL_DISABLE = 500;
    const WARN_EPEL_ENABLE = 501;
    const WARN_EPEL_DUPLICATE = 502;

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Software repository constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    /**
     * Returns any relavent warnings.
     *
     * @return array warnings
     * @throws Engine_Exception
     */

    public function get_warnings($disable_event = FALSE)
    {
        clearos_profile(__METHOD__, __LINE__);
        $warnings = array();
        $test_count = 0;

        $test_repos = [
            'clearos-dev', 'clearos-test', 'clearos-updates-testing', 'clearos-developer', 'clearos-zfs',
            'clearos-zfs-testing', 'clearos-paid-testing', 'clearos-infra-testing', 'clearos-epel-verified-testing',
            'clearos-epel-testing', 'clearos-contribs-testing', 'centos-extras-unverified', 'centos-fasttrack-unverified',
            'centos-unverified', 'centos-updates-unverified', 'centosplus-unverified', 'clearos-epel-testing-unverified',
            'clearos-epel-unverified', 'centos-sclo-sclo-debuginfo', 'centos-sclo-sclo-source', 'centos-sclo-sclo-testing',
            'centos-sclo-rh-debuginfo', 'centos-sclo-rh-source', 'centos-sclo-rh-testing'
        ];

        $yum = new Yum();
        // Get a list of repos (flag set to disable cache)
        $repos = $yum->get_repo_list(TRUE);
        foreach ($repos as $name => $repo) {
            if ($name == 'clearos-updates' && $repo['enabled']) {
                $clearos_updates = TRUE;
                if (isset($verified_clearos_updates))
                    $warnings[self::WARN_UPDATES_DISABLE] = lang('software_repository_warn_updates_disable');
            }
            if ($name == 'private-clearcenter-verified-updates' && $repo['enabled']) {
                $verified_clearos_updates = TRUE;
                if (isset($clearos_updates))
                    $warnings[self::WARN_UPDATES_DISABLE] = lang('software_repository_warn_updates_disable');
            }
            if ($name == 'clearos-epel' && $repo['enabled']) {
                $clearos_epel = TRUE;
                if (isset($clearos_epel_verified))
                    $warnings[self::WARN_EPEL_DUPLICATE] = lang('software_repository_warn_epel_duplicate');
            }
            if ($name == 'clearos-epel-verified' && $repo['enabled']) {
                $clearos_epel_verified = TRUE;
                if (isset($clearos_epel))
                    $warnings[self::WARN_EPEL_DUPLICATE] = lang('software_repository_warn_epel_duplicate');
            }
            if ($name == 'clearos-centos' && $repo['enabled']) {
                $clearos_centos = TRUE;
                if (isset($clearos_centos_verified))
                    $warnings[self::WARN_CENTOS_DISABLE] = lang('software_repository_warn_centos_disable');
            }
            if ($name == 'clearos-centos-verified' && $repo['enabled']) {
                $clearos_centos_verified = TRUE;
                if (isset($clearos_centos))
                    $warnings[self::WARN_CENTOS_DISABLE] = lang('software_repository_warn_centos_disable');
            }
            if ($name == 'clearos-centos-updates' && $repo['enabled']) {
                $clearos_centos_updates = TRUE;
                if (isset($clearos_centos_verified))
                    $warnings[self::WARN_CENTOS_UPDATES_DISABLE] = lang('software_repository_warn_centos_updates_disable');
            }
            if ($name == 'clearos-fast-updates' && $repo['enabled'])
                $clearos_fast_updates = TRUE;

            if (in_array($name, $test_repos)) {
                if ($repo['enabled']) {
                    $warnings[self::WARN_TEST_REPO + $test_count] = sprintf(lang('software_repository_warn_test_repo'), $name);
                    $test_count++;
                }
            }
        }

        // Warn if user does not have verified updates and has updates disabled
        if (!isset($clearos_updates) && !isset($verified_clearos_updates))
            $warnings[self::WARN_UPDATES_ENABLE] = lang('software_repository_warn_updates_enable');

        // Warn if user does has epel enabled
        if (isset($clearos_epel))
            $warnings[self::WARN_EPEL_ENABLE] = lang('software_repository_warn_epel_is_enabled');

        // Warn if user does not have verified centos and has centos disabled
        if (!isset($clearos_centos) && !isset($clearos_centos_verified))
            $warnings[self::WARN_CENTOS_ENABLE] = lang('software_repository_warn_centos_enable');

        // Warn if user does not have verified centos and has centos updates disabled
        if (!isset($clearos_centos_updates) && !isset($clearos_centos_verified))
            $warnings[self::WARN_CENTOS_UPDATES_ENABLE] = lang('software_repository_warn_centos_updates_enable');

        // Warn if user has clearos-centos but not clear-centos-updates enabled
        if (isset($clearos_centos) && !isset($clearos_centos_updates))
            $warnings[self::WARN_CENTOS_UPDATES_ENABLE] = lang('software_repository_warn_centos_updates_enable');

        if (count($warnings) && !$disable_event)
            Event_Utils::add_event(lang('software_repository_updates_warning_event'), 'WARN', 'SOFTWARE_REPOSITORY_CONFIG_WARNING', 'software_repository', FALSE);
        else if (count($warnings) == 0)
            Event_Utils::resolve_event('SOFTWARE_REPOSITORY_CONFIG_WARNING');

        return $warnings;
    }
}
