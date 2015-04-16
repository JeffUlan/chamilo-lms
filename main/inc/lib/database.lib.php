<?php
/* For licensing terms, see /license.txt */

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Class Database
 */
class Database
{
    /* Variable use only in the installation process to log errors.
    See the Database::query function */
    static $log_queries = false;

    /**
     * @var EntityManager
     */
    private static $em;
    private static $connection;

    /**
     * @param EntityManager $em
     */
    public function setManager($em)
    {
        self::$em = $em;
    }

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection)
    {
        self::$connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return self::$connection;
    }

    /**
     * @return EntityManager
     */
    public static function getManager()
    {
        return self::$em;
    }

    /**
     *  Returns the name of the main database.
     */
    public static function get_main_database()
    {
        return self::getManager()->getConnection()->getDatabase();
    }

    /**
     * A more generic method than the other get_main_xxx_table methods,
     * This one returns the correct complete name of any table of the main
     * database of which you pass the short name as a parameter.
     * Please, define table names as constants in this library and use them
     * instead of directly using magic words in your tool code.
     *
     * @param string $table, the name of the table
     */
    public static function get_main_table($table)
    {
        return $table;
    }

    /**
     * A more generic method than the older get_course_xxx_table methods,
     * This one can return the correct complete name of any course table of
     * which you pass the short name as a parameter.
     * Please, define table names as constants in this library and use them
     * instead of directly using magic words in your tool code.
     *
     * @param string $table, the name of the table
     */
    public static function get_course_table($table)
    {
        return DB_COURSE_PREFIX.$table;
    }

    /**
     * Counts the number of rows in a table
     * @param string $table The table of which the rows should be counted
     * @return int The number of rows in the given table.
     * @deprecated
     */
    public static function count_rows($table)
    {
        $obj = self::fetch_object(self::query("SELECT COUNT(*) AS n FROM $table"));

        return $obj->n;
    }


    /**
     * Returns the number of affected rows in the last database operation.
     * @param Statement $result
     *
     * @return int
     */
    public static function affected_rows(Statement $result)
    {
        return $result->rowCount();
    }

    /**
     * Connect to the database sets the entity manager.
     * @param array $params
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function connect($params = [])
    {
        $config = self::getDoctrineConfig();
        $config->setEntityNamespaces(
            array(
                'ChamiloUserBundle' => 'Chamilo\UserBundle\Entity',
                'ChamiloCoreBundle' => 'Chamilo\CoreBundle\Entity',
                'ChamiloCourseBundle' => 'Chamilo\CourseBundle\Entity'
            )
        );

        $params['charset'] = 'utf8';
        $entityManager = EntityManager::create($params, $config);

        // Registering Constraints
        AnnotationRegistry::registerAutoloadNamespace(
            'Symfony\Component\Validator\Constraint',
            api_get_path(SYS_PATH)."vendor/symfony/validator"
        );

        AnnotationRegistry::registerFile(
            api_get_path(SYS_PATH)."vendor/symfony/doctrine-bridge/Symfony/Bridge/Doctrine/Validator/Constraints/UniqueEntity.php"
        );

        // Registering gedmo extensions
        AnnotationRegistry::registerAutoloadNamespace(
            'Gedmo\Mapping\Annotation',
            api_get_path(SYS_PATH)."vendor/gedmo/doctrine-extensions/lib"
        );

        $this->setConnection($entityManager->getConnection());
        $this->setManager($entityManager);

        // A MySQL-specific implementation.
        /*if (!isset($parameters['server'])) {
            $parameters['server'] = @ini_get('mysql.default_host');
            if (empty($parameters['server'])) {
                $parameters['server'] = 'localhost:3306';
            }
        }
        if (!isset($parameters['username'])) {
            $parameters['username'] = @ini_get('mysql.default_user');
        }
        if (!isset($parameters['password'])) {
            $parameters['password'] = @ini_get('mysql.default_password');
        }
        if (!isset($parameters['new_link'])) {
            $parameters['new_link'] = false;
        }
        if (!isset($parameters['client_flags']) || empty($parameters['client_flags'])) {
            $parameters['client_flags'] = 0;
        }

        $persistent = isset($parameters['persistent']) ? $parameters['persistent'] : null;
        $server = isset($parameters['server']) ? $parameters['server'] : null;
        $username = isset($parameters['username']) ? $parameters['username'] : null;
        $password = isset($parameters['password']) ? $parameters['password'] : null;
        $client_flag = isset($parameters['client_flags']) ? $parameters['client_flags'] : null;
        $new_link = isset($parameters['new_link']) ? $parameters['new_link'] : null;
        $client_flags = isset($parameters['client_flags']) ? $parameters['client_flags'] : null;
        return $persistent
            ? mysql_pconnect($server, $username, $password, $client_flags)
            : mysql_connect($server, $username, $password, $new_link, $client_flags);*/
    }

    /**
     * Escape MySQL wildchars _ and % in LIKE search
     * @param string            The string to escape
     * @return string           The escaped string
     */
    public static function escape_sql_wildcards($in_txt)
    {
        $out_txt = api_preg_replace("/_/", "\_", $in_txt);
        $out_txt = api_preg_replace("/%/", "\%", $out_txt);

        return $out_txt;
    }

    /**
     * Escapes a string to insert into the database as text
     * @param $string
     *
     * @return string
     */
    public static function escape_string($string)
    {
        $string = self::getManager()->getConnection()->quote($string);

        return trim($string, "'");
    }

    /**
     * Gets the array from a SQL result (as returned by Database::query) - help achieving database independence
     * @param resource      The result from a call to sql_query (e.g. Database::query)
     * @param string        Optional: "ASSOC","NUM" or "BOTH", as the constant used in mysql_fetch_array.
     * @return array        Array of results as returned by php
     * @author Yannick Warnier <yannick.warnier@beeznest.com>
     */
    public static function fetch_array(Statement $result, $option = 'BOTH')
    {
        if ($result === false) {
            return array();
        }

        return $result->fetch(self::customOptionToDoctrineOption($option));
    }

    /**
     * Gets an associative array from a SQL result (as returned by Database::query).
     * This method is equivalent to calling Database::fetch_array() with 'ASSOC' value for the optional second parameter.
     * @param resource $result  The result from a call to sql_query (e.g. Database::query).
     * @return array            Returns an associative array that corresponds to the fetched row and moves the internal data pointer ahead.
     */
    public static function fetch_assoc(Statement $result)
    {
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Gets the next row of the result of the SQL query (as returned by Database::query) in an object form
     *
     * @param Statement $result
     * @return mixed
     */
    public static function fetch_object(Statement $result)
    {
        return $result->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Gets the array from a SQL result (as returned by Database::query) - help achieving database independence
     * @param Statement $result
     *
     * @return mixed
     */
    public static function fetch_row(Statement $result)
    {
        return $result->fetch(PDO::FETCH_NUM);
    }

    /**
     * Frees all the memory associated with the provided result identifier.
     * @return bool     Returns TRUE on success or FALSE on failure.
     * Notes: Use this method if you are concerned about how much memory is being used for queries that return large result sets.
     * Anyway, all associated result memory is automatically freed at the end of the script's execution.
     */
    public static function free_result(Statement $result)
    {
        $result->closeCursor();
    }

    /**
     * Gets the ID of the last item inserted into the database
     * @return string
     */
    public static function insert_id()
    {
        return self::getManager()->getConnection()->lastInsertId();
    }

    /**
     * @param Statement $result
     *
     * @return int
     */
    public static function num_rows(Statement $result)
    {
        return $result->rowCount();
    }

    /**
     * Acts as the relative *_result() function of most DB drivers and fetches a
     * specific line and a field
     *
     * @param Statement $resource
     * @param int $row
     * @param string $field
     *
     * @return mixed
     */
    public static function result(Statement $resource, $row, $field = '')
    {
        if ($resource->rowCount() > 0) {
            $result = $resource->fetchAll(PDO::FETCH_BOTH);

            return $result[$row][$field];
        }
    }

    /**
     * @param $query
     * @return Statement
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function query($query)
    {
        $result = self::getManager()->getConnection()->executeQuery($query);

        return $result;
    }

    /**
     * @param string $option
     *
     * @return int
     */
    public static function customOptionToDoctrineOption($option)
    {
        switch($option) {
            case 'ASSOC':
                return PDO::FETCH_ASSOC;
                break;
            case 'NUM':
                return PDO::FETCH_NUM;
                break;
            case 'BOTH':
            default:
                return PDO::FETCH_BOTH;
                break;
        }
    }

    /**
     * Stores a query result into an array.
     *
     * @author Olivier Brouckaert
     * @param  resource $result - the return value of the query
     * @param  option BOTH, ASSOC, or NUM
     * @return array - the value returned by the query
     */
    public static function store_result(Statement $result, $option = 'BOTH')
    {
        return $result->fetchAll(self::customOptionToDoctrineOption($option));
    }

    /**
     * Database insert
     * @param string $table_name
     * @param array $attributes
     * @param bool $show_query
     * @return bool|int
     */
    public static function insert($table_name, $attributes, $show_query = false)
    {
        if (empty($attributes) || empty($table_name)) {
            return false;
        }
        $filtred_attributes = array();
        foreach($attributes as $key => $value) {
            $filtred_attributes[$key] = "'".self::escape_string($value)."'";
        }
        //@todo check if the field exists in the table we should use a describe of that table
        $params = array_keys($filtred_attributes);
        $values = array_values($filtred_attributes);
        if (!empty($params) && !empty($values)) {
            $sql    = 'INSERT INTO '.$table_name.' ('.implode(',',$params).') VALUES ('.implode(',',$values).')';
            self::query($sql);
            if ($show_query) {
                var_dump($sql);
                error_log($sql);
            }

            return self::insert_id();
        }

        return false;
    }

    /**
     * Experimental useful database finder
     * @todo lot of stuff to do here
     * @todo known issues, it doesn't work when using LIKE conditions
     * @example array('where'=> array('course_code LIKE "?%"'))
     * @example array('where'=> array('type = ? AND category = ?' => array('setting', 'Plugins'))
     * @example array('where'=> array('name = "Julio" AND lastname = "montoya"'))
     */
    public static function select($columns, $table_name, $conditions = array(), $type_result = 'all', $option = 'ASSOC')
    {
        $conditions = self::parse_conditions($conditions);

        //@todo we could do a describe here to check the columns ...
        if (is_array($columns)) {
            $clean_columns = implode(',', $columns);
        } else {
            if ($columns == '*') {
                $clean_columns = '*';
            } else {
                $clean_columns = (string)$columns;
            }
        }

        $sql    = "SELECT $clean_columns FROM $table_name $conditions";
        $result = self::query($sql);
        $array = array();

        if ($type_result == 'all') {
            while ($row = self::fetch_array($result, $option)) {
                if (isset($row['id'])) {
                    $array[$row['id']] = $row;
                } else {
                    $array[] = $row;
                }
            }
        } else {
            $array = self::fetch_array($result, $option);
        }

        return $array;
    }

    /**
     * Parses WHERE/ORDER conditions i.e array('where'=>array('id = ?' =>'4'), 'order'=>'id DESC'))
     * @todo known issues, it doesn't work when using
     * LIKE conditions example: array('where'=>array('course_code LIKE "?%"'))
     * @param   array $conditions
     */
    public static function parse_conditions($conditions)
    {
        if (empty($conditions)) {
            return '';
        }
        $return_value = $where_return = '';
        foreach ($conditions as $type_condition => $condition_data) {
            if ($condition_data == false) {
                continue;
            }
            $type_condition = strtolower($type_condition);
            switch ($type_condition) {
                case 'where':
                    foreach ($condition_data as $condition => $value_array) {
                        if (is_array($value_array)) {
                            $clean_values = array();
                            foreach($value_array as $item) {
                                $item = Database::escape_string($item);
                                $clean_values[]= $item;
                            }
                        } else {
                            $value_array = Database::escape_string($value_array);
                            $clean_values = $value_array;
                        }

                        if (!empty($condition) && $clean_values != '') {
                            $condition = str_replace('%',"'@percentage@'", $condition); //replace "%"
                            $condition = str_replace("'?'","%s", $condition);
                            $condition = str_replace("?","%s", $condition);

                            $condition = str_replace("@%s@","@-@", $condition);
                            $condition = str_replace("%s","'%s'", $condition);
                            $condition = str_replace("@-@","@%s@", $condition);

                            // Treat conditions as string
                            $condition = vsprintf($condition, $clean_values);
                            $condition = str_replace('@percentage@','%', $condition); //replace "%"
                            $where_return .= $condition;
                        }
                    }

                    if (!empty($where_return)) {
                        $return_value = " WHERE $where_return" ;
                    }
                    break;
                case 'order':
                    $order_array = $condition_data;

                    if (!empty($order_array)) {
                        // 'order' => 'id desc, name desc'
                        $order_array = self::escape_string($order_array, null, false);
                        $new_order_array = explode(',', $order_array);
                        $temp_value = array();

                        foreach($new_order_array as $element) {
                            $element = explode(' ', $element);
                            $element = array_filter($element);
                            $element = array_values($element);

                            if (!empty($element[1])) {
                                $element[1] = strtolower($element[1]);
                                $order = 'DESC';
                                if (in_array($element[1], array('desc', 'asc'))) {
                                    $order = $element[1];
                                }
                                $temp_value[]= $element[0].' '.$order.' ';
                            } else {
                                //by default DESC
                                $temp_value[]= $element[0].' DESC ';
                            }
                        }
                        if (!empty($temp_value)) {
                            $return_value .= ' ORDER BY '.implode(', ', $temp_value);
                        } else {
                            //$return_value .= '';
                        }
                    }
                    break;
                case 'limit':
                    $limit_array = explode(',', $condition_data);
                    if (!empty($limit_array)) {
                        if (count($limit_array) > 1) {
                            $return_value .= ' LIMIT '.intval($limit_array[0]).' , '.intval($limit_array[1]);
                        }  else {
                            $return_value .= ' LIMIT '.intval($limit_array[0]);
                        }
                    }
                    break;
            }
        }

        return $return_value;
    }

    /**
     * @param array $conditions
     * @return string
     */
    public static function parse_where_conditions($conditions)
    {
        return self::parse_conditions(array('where' => $conditions));
    }

    /**
     * Experimental useful database update
     * @todo lot of stuff to do here
     */
    public static function delete($table_name, $where_conditions, $show_query = false)
    {
        $where_return = self::parse_where_conditions($where_conditions);
        $sql    = "DELETE FROM $table_name $where_return ";
        if ($show_query) { echo $sql; echo '<br />'; }
        $result = self::query($sql);
        $affected_rows = self::affected_rows($result);
        //@todo should return affected_rows for
        return $affected_rows;
    }

    /**
     * @param string $table_name use Database::get_main_table
     * @param array $attributes Values to updates
     * Example: $params['name'] = 'Julio'; $params['lastname'] = 'Montoya';
     * @param array $where_conditions where conditions i.e array('id = ?' =>'4')
     * @param bool $show_query
     * @return bool|int
     */
    public static function update(
        $table_name,
        $attributes,
        $where_conditions = array(),
        $show_query = false
    ) {
        if (!empty($table_name) && !empty($attributes)) {
            $update_sql = '';
            //Cleaning attributes
            $count = 1;
            foreach ($attributes as $key=>$value) {
                if (!is_array($value)) {
                    $value = self::escape_string($value);
                }
                $update_sql .= "$key = '$value' ";
                if ($count < count($attributes)) {
                    $update_sql.=', ';
                }
                $count++;
            }
            if (!empty($update_sql)) {
                //Parsing and cleaning the where conditions
                $where_return = self::parse_where_conditions($where_conditions);
                $sql    = "UPDATE $table_name SET $update_sql $where_return ";
                if ($show_query) {
                    var_dump($sql);
                }
                $result = self::query($sql);
                $affected_rows = self::affected_rows($result);
                return $affected_rows;
            }
        }
        return false;
    }

    /**
     * @return \Doctrine\ORM\Configuration
     */
    public static function getDoctrineConfig()
    {
        $isDevMode = true;
        $isSimpleMode = false;
        $proxyDir = null;
        $cache = null;

        $paths = array(
            api_get_path(SYS_PATH).'src/Chamilo/CoreBundle/Entity',
            api_get_path(SYS_PATH).'src/Chamilo/UserBundle/Entity',
            api_get_path(SYS_PATH).'src/Chamilo/CourseBundle/Entity'
        );

        /*$doctrineCache = api_get_path(SYS_ARCHIVE_PATH).'doctrine/';

        if (!is_dir($doctrineCache)) {
            mkdir($doctrineCache, api_get_permissions_for_new_directories(), true);
        }*/

        return \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
            $paths,
            $isDevMode,
            $proxyDir,
            $cache,
            $isSimpleMode
        );
    }
}
