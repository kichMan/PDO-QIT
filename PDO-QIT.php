<?php
/**
* The script-helper in the administration of the MySQL database.
*
* Text search in all tables of a database of the current connection
* or all databases, available to the user.
* Uses PHP Data Objects (PDO) library.
*
* @author Igor Kechaykin
* @version v 0.1 2012-07-30;
* @example README.md;
* @todo add the ability to use an existing connection;
*/
class PDOQueryInTables extends PDO
{

    private	$arrQuery	= null;

    public function __construct(
        $dsn		= '',
        $username	= '',
        $password	= '',
        $options	= NULL)
    {

        try {
            parent::__construct($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        $this->genQuery();
    }

    /**
    * Generation of information about databases and tables.
    * @param bool $others add tables from other available databases
    */
    public function GenQuery($others = false)
    {
        if ($others) {
            /**
            * List of excluded tables.
            * Please note, current database must remain in this list.
            */
            $deletion = "c.table_schema <> (SELECT DATABASE())
              AND c.table_schema <> 'information_schema'";
        } else {
            $deletion = "c.table_schema = (SELECT DATABASE())";
        }

        $str = "SELECT
            CONCAT('`', c.table_schema, '`.`', c.table_name, '`'),
            CONCAT(
              GROUP_CONCAT(
                  CONCAT('`', c.column_name, '`') SEPARATOR ' LIKE ? OR '
              ),
              ' LIKE ?'
            )
          FROM information_schema.columns c
          WHERE {$deletion}
            AND c.data_type IN (
              'binary', 'blob', 'char', 'longblob', 'longtext',
              'mediumblob', 'mediumtext', 'text', 'tinyblob',
              'tinytext', 'varbinary', 'varchar'
            )
          GROUP BY c.table_name;";

        foreach (parent::query($str) as $row) {
            $this->arrQuery[$row[0]] = $row[1];
        }

        return $this;
    }

    /**
    * Query from list tables
    * @return null|array
    */
    public function MoveQuery($str = NULL)
    {
        if (empty($str)) {
            return;
        }

        $result = array();
        $str = parent::quote($str);

        foreach ($this->arrQuery as $k => $v) {
            $v = preg_replace('/\?/', $str, $v);

            if (parent::query("SELECT '{$k}'
                FROM {$k}
                WHERE {$v}
                LIMIT 1")->fetch()) {
                $result[] = $k;
            }
        }
        return $result;
    }

}