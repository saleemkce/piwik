<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\UserCountry\Repository\Mysql;

use Piwik\Common;
use Piwik\Db;
use Piwik\Plugins\UserCountry\Repository\LogsRepository as LogsRepositoryInterface;

class LogsRepository implements LogsRepositoryInterface
{
    /**
     * @param string $from
     * @param string $to
     * @param array $locationFields
     * @param int $fromId
     * @param int $limit
     * @return array
     */
    public function getVisitsWithDatesLimit($from, $to, $locationFields = array(), $fromId = 0, $limit = 1000)
    {
        $sql = array(
            "SELECT idvisit, location_ip, " . implode(',', $locationFields),
            "FROM " . Common::prefixTable('log_visit'),
            "WHERE visit_first_action_time >= ? AND visit_last_action_time < ?",
            "AND idvisit > ?",
            sprintf("LIMIT %d", $limit)
        );

        $bind = array($from, $to, $fromId);

        return Db::get()->fetchAll(implode(' ', $sql), $bind);
    }

    /**
     * @param string $from
     * @param string $to
     * @return int
     */
    public function countVisitsWithDatesLimit($from, $to)
    {
        $sql = array(
            "SELECT COUNT(*) AS num_rows",
            "FROM " . Common::prefixTable('log_visit'),
            "WHERE visit_first_action_time >= ? AND visit_last_action_time < ?"
        );

        $bind = array($from, $to);

        return (int) Db::get()->fetchOne(implode(' ', $sql), $bind);
    }

    /**
     * @param array $columnsToSet
     * @param array $bind
     */
    public function updateVisits(array $columnsToSet, array $bind)
    {
        $sql = array(
            "UPDATE " . Common::prefixTable('log_visit'),
            "SET " . $this->getColumnBinds($columnsToSet),
            "WHERE idvisit = ?"
        );

        Db::query(implode(' ', $sql), $bind);
    }

    /**
     * @param array $columnsToSet
     * @param array $bind
     */
    public function updateConversions(array $columnsToSet, array $bind)
    {
        $sql = array(
            "UPDATE " . Common::prefixTable('log_conversion'),
			"SET " . $this->getColumnBinds($columnsToSet),
			"WHERE idvisit = ?"
        );

        Db::query(implode(' ', $sql), $bind);
    }

    /**
     * @param array $columnsToSet
     * @return string
     */
    protected function getColumnBinds(array $columnsToSet)
    {
        $columnsToSet = array_map(
            function ($column) {
                return sprintf('%s = ?', $column);
            },
            $columnsToSet
        );

        return implode(', ', $columnsToSet);
    }
}