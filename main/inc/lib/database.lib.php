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

    /**
     * @param EntityManager $em
     */
    public function setManager($em)
    {
        self::$em = $em;
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
        /*global $_configuration;
        return $_configuration['main_database'];*/
    }

    /**
     * A more generic method than the other get_main_xxx_table methods,
     * This one returns the correct complete name of any table of the main
     * database of which you pass the short name as a parameter.
     * Please, define table names as constants in this library and use them
     * instead of directly using magic words in your tool code.
     *
     * @param string $short_table_name, the name of the table
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
     * @param string $short_table_name, the name of the table
     * @param string $database_name, optional, name of the course database
     * - if you don't specify this, you work on the current course.
     */
    public static function get_course_table($table, $extra = null)
    {
        return DB_COURSE_PREFIX.$table;
        /*
        //forces fatal errors so we can debug more easily
        if (!empty($extra)) {
            var_dump($extra);
            //@todo remove this
            echo "<h3>Dev Message: get_course_table() doesn't have a 2nd parameter</h3>";
            //exit;
        }
        return self::format_table_name(self::get_main_database(), DB_COURSE_PREFIX.$short_table_name);*/
    }

    /*
        Query methods
        These methods execute a query and return the result(s).
    */

    /**
     * Counts the number of rows in a table
     * @param string $table The table of which the rows should be counted
     * @return int The number of rows in the given table.
     * @deprecated
     */
    public static function count_rows($table) {
        $obj = self::fetch_object(self::query("SELECT COUNT(*) AS n FROM $table"));
        return $obj->n;
    }

    /*
        An intermediate API-layer between the system and the database server.
    */

    /**
     * Returns the number of affected rows in the last database operation.
     * @param resource $connection (optional)   The database server connection, for detailed description see the method query().
     * @return int                              Returns the number of affected rows on success, and -1 if the last query failed.
     */
    public static function affected_rows(Statement $result)
    {
        return $result->rowCount();
        //return self::use_default_connection($connection) ? mysql_affected_rows() : mysql_affected_rows($connection);
    }

    /**
     * Closes non-persistent database connection.
     * @param resource $connection (optional)   The database server connection, for detailed description see the method query().
     * @return bool                             Returns TRUE on success or FALSE on failure.
     */
    public static function close($connection = null) {
        return self::use_default_connection($connection) ? mysql_close() : mysql_close($connection);
    }

    /**
     * @param array $params
     * @throws \Doctrine\ORM\ORMException
     */
    public function connect($params = array())
    {
        $config = self::getDoctrineConfig();
        $config->setEntityNamespaces(
            array(
                'ChamiloUserBundle' => 'Chamilo\UserBundle\Entity',
                'ChamiloCoreBundle' => 'Chamilo\CoreBundle\Entity',
                'ChamiloCourseBundle' => 'Chamilo\CourseBundle\Entity'
            )
        );

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
     * Returns error number from the last operation done on the database server.
     * @param resource $connection (optional)   The database server connection,
     * for detailed description see the method query().
     * @return int Returns the error number from the last database (operation, or 0 (zero) if no error occurred.
     */
    public static function errno($connection = null) {
        return self::use_default_connection($connection) ? mysql_errno() : mysql_errno($connection);
    }

    /**
     * Returns error text from the last operation done on the database server.
     * @param resource $connection (optional)   The database server connection, for detailed description see the method query().
     * @return string Returns the error text from the last database operation, or '' (empty string) if no error occurred.
     */
    public static function error($connection = null) {
        return self::use_default_connection($connection) ? mysql_error() : mysql_error($connection);
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
     * @param string    $string The string to escape
     * @param resource  $connection (optional)   The database server connection, for detailed description see the method query().
     * @param bool $addFix
     * @return string   he escaped string
     * @author Yannick Warnier <yannick.warnier@beeznest.com>
     * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
     */
    public static function escape_string($string, $connection = null, $addFix = true)
    {
        $string = self::getManager()->getConnection()->quote($string);
        return trim($string, "'");

        /*return get_magic_quotes_gpc()
            ? (self::use_default_connection($connection)
                ? mysql_real_escape_string(stripslashes($string))
                : mysql_real_escape_string(stripslashes($string), $connection))
            : (self::use_default_connection($connection)
                ? mysql_real_escape_string($string)
                : mysql_real_escape_string($string, $connection));-*/
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
        //if ($result === false) { return array(); }
        //return $option == 'ASSOC' ? mysql_fetch_array($result, MYSQL_ASSOC) : ($option == 'NUM' ? mysql_fetch_array($result, MYSQL_NUM) : mysql_fetch_array($result));
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
        //return mysql_fetch_assoc($result);
    }

    /**
     * Gets the next row of the result of the SQL query (as returned by Database::query) in an object form
     * @param   resource    The result from a call to sql_query (e.g. Database::query)
     * @param   string      Optional class name to instanciate
     * @param   array       Optional array of parameters
     * @return  object      Object of class StdClass or the required class, containing the query result row
     * @author  Yannick Warnier <yannick.warnier@beeznest.com>
     */
    //public static function fetch_object($result, $class = null, $params = null) {
    public static function fetch_object(Statement $result)
    {
        return $result->fetch(PDO::FETCH_OBJ);
        //return !empty($class) ? (is_array($params) ? mysql_fetch_object($result, $class, $params) : mysql_fetch_object($result, $class)) : mysql_fetch_object($result);
    }

    /**
     * Gets the array from a SQL result (as returned by Database::query) - help achieving database independence
     * @param resource      The result from a call to sql_query (see Database::query()).
     * @return array        Array of results as returned by php (mysql_fetch_row)
     */
    public static function fetch_row(Statement $result)
    {
        return $result->fetch(PDO::FETCH_NUM);
        //return mysql_fetch_row($result);
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
        //return mysql_free_result($result);
    }

    /**
     * Returns the database client library version.
     * @return strung       Returns a string that represents the client library version.
     */
    public static function get_client_info() {
        return mysql_get_client_info();
    }

    /**
     * Returns a list of databases created on the server. The list may contain all of the
     * available database names or filtered database names by using a pattern.
     * @param string $pattern (optional)        A pattern for filtering database names as if it was needed for the SQL's LIKE clause, for example 'chamilo_%'.
     * @param resource $connection (optional)   The database server connection, for detailed description see the method query().
     * @return array                            Returns in an array the retrieved list of database names.
     */
    public static function get_databases($pattern = '', $connection = null) {
        $result = array();
        $query_result = Database::query(!empty($pattern) ? "SHOW DATABASES LIKE '".self::escape_string($pattern, $connection)."'" : "SHOW DATABASES", $connection);
        while ($row = Database::fetch_row($query_result)) {
            $result[] = $row[0];
        }
        return $result;
    }

    /**
     * Returns information about the type of the current connection and the server host name.
     * @param resource $connection (optional)   The database server connection, for detailed description see the method query().
     * @return string/boolean                   Returns string data on success or FALSE on failure.
     */
    public static function get_host_info($connection = null) {
        return self::use_default_connection($connection) ? mysql_get_host_info() : mysql_get_host_info($connection);
    }

    /**
     * Retrieves database client/server protocol version.
     * @param resource $connection (optional)   The database server connection, for detailed description see the method query().
     * @return int/boolean                      Returns the protocol version on success or FALSE on failure.
     */
    public static function get_proto_info($connection = null) {
        return self::use_default_connection($connection) ? mysql_get_proto_info() : mysql_get_proto_info($connection);
    }

    /**
     * Retrieves the database server version.
     * @param resource $connection (optional)   The database server connection, for detailed description see the method query().
     * @return string/boolean                   Returns the MySQL server version on success or FALSE on failure.
     */
    public static function get_server_info($connection = null) {
        return self::use_default_connection($connection) ? mysql_get_server_info() : mysql_get_server_info($connection);
    }

    /**
     * Returns a list of tables within a database. The list may contain all of the
     * available table names or filtered table names by using a pattern.
     * @param string $database (optional)       The name of the examined database. If it is omited, the current database is assumed, see Database::select_db().
     * @param string $pattern (optional)        A pattern for filtering table names as if it was needed for the SQL's LIKE clause, for example 'access_%'.
     * @param resource $connection (optional)   The database server connection, for detailed description see the method query().
     * @return array                            Returns in an array the retrieved list of table names.
     */
    public static function get_tables($database = '', $pattern = '', $connection = null) {
        $result = array();
        $query = "SHOW TABLES";
        if (!empty($database)) {
            $query .= " FROM `".self::escape_string($database, $connection)."`";
        }
        if (!empty($pattern)) {
            $query .= " LIKE '".self::escape_string($pattern, $connection)."'";
        }
        $query_result = Database::query($query, $connection);
        while ($row = Database::fetch_row($query_result)) {
            $result[] = $row[0];
        }
        return $result;
    }

    /**
     * Gets the ID of the last item inserted into the database
     * This should be updated to use ADODB at some point
     * @param resource $connection (optional)   The database server connection, for detailed description see the method query().
     * @return int                              The last ID as returned by the DB function
     */
    public static function insert_id()
    {
        return self::getManager()->getConnection()->lastInsertId();
        //return self::use_default_connection($connection) ? mysql_insert_id() : mysql_insert_id($connection);
    }

    /**
     * Gets the number of rows from the last query result - help achieving database independence
     * @param resource      The result
     * @return integer      The number of rows contained in this result
     * @author Yannick Warnier <yannick.warnier@beeznest.com>
     **/
    public static function num_rows(Statement $result)
    {
        return $result->rowCount();
        //return is_resource($result) ? mysql_num_rows($result) : false;
    }

    /**
     * Acts as the relative *_result() function of most DB drivers and fetches a
     * specific line and a field
     * @param   resource    The database resource to get data from
     * @param   integer     The row number
     * @param   string      Optional field name or number
     * @return  mixed       One cell of the result, or FALSE on error
     */
    public static function result(Statement $resource, $row, $field = '')
    {
        if ($resource->rowCount() > 0) {
            $result = $resource->fetchAll(PDO::FETCH_BOTH);
            return $result[$row][$field];
        }
        //return self::num_rows($resource) > 0 ? (!empty($field) ? mysql_result($resource, $row, $field) : mysql_result($resource, $row)) : null;
    }

    /**
     * This method returns a resource
     * Documentation has been added by Arthur Portugal
     * Some adaptations have been implemented by Ivan Tcholakov, 2009, 2010
     * @author Olivier Brouckaert
     * @param string $query                     The SQL query
     * @param resource $connection (optional)   The database server (MySQL) connection.
     * If it is not specified, the connection opened by mysql_connect() is assumed.
     * If no connection is found, the server will try to create one as if mysql_connect() was called with no arguments.
     * If no connection is found or established, an E_WARNING level error is generated.
     * @param string $file (optional)           On error it shows the file in which the error has been trigerred (use the "magic" constant __FILE__ as input parameter)
     * @param string $line (optional)           On error it shows the line in which the error has been trigerred (use the "magic" constant __LINE__ as input parameter)
     *
     * @return Statement                         The returned result from the query
     *
     * Note: The parameter $connection could be skipped. Here are examples of this method usage:
     * Database::query($query);
     * $result = Database::query($query);
     * Database::query($query, $connection);
     * $result = Database::query($query, $connection);
     * The following ways for calling this method are obsolete:
     * Database::query($query, __FILE__, __LINE__);
     * $result = Database::query($query, __FILE__, __LINE__);
     * Database::query($query, $connection, __FILE__, __LINE__);
     * $result = Database::query($query, $connection, __FILE__, __LINE__);
     */
    public static function query($query, $connection = null, $file = null, $line = null)
    {
        $result = self::getManager()->getConnection()->executeQuery($query);

        return $result;

        $use_default_connection = self::use_default_connection($connection);
        if ($use_default_connection) {
            // Let us do parameter shifting, thus the method would be similar
            // (in regard to parameter order) to the original function mysql_query().
            $line = $file;
            $file = $connection;
            $connection = null;
        }

        // Check if the table contains a c_ (means a course id)
        if (api_get_setting('server_type') === 'test' && strpos($query, 'c_')) {
            //Check if the table contains inner joins
            if (
                strpos($query, 'assoc_handle') === false &&
                strpos($query, 'olpc_peru_filter') === false &&
                strpos($query, 'allow_public_certificates') === false &&
                strpos($query, 'DROP TABLE IF EXISTS') === false &&
                strpos($query, 'thematic_advance') === false &&
                strpos($query, 'thematic_plan') === false &&
                strpos($query, 'track_c_countries') === false &&
                strpos($query, 'track_c_os') === false &&
                strpos($query, 'track_c_providers') === false &&
                strpos($query, 'track_c_referers') === false &&
                strpos($query, 'track_c_browsers') === false &&
                strpos($query, 'settings_current') === false &&
                strpos($query, 'dokeos_classic_2D') === false &&
                strpos($query, 'cosmic_campus') === false &&
                strpos($query, 'static_') === false &&
                strpos($query, 'public_admin') === false &&
                strpos($query, 'chamilo_electric_blue') === false &&
                strpos($query, 'specific_field') === false &&
                strpos($query, 'down_doc_path') === false &&
                strpos($query, 'INNER JOIN') === false &&
                strpos($query, 'inner join') === false &&
                strpos($query, 'left join') === false &&
                strpos($query, 'LEFT JOIN') === false &&
                strpos($query, 'insert') === false &&
                strpos($query, 'INSERT') === false &&
                strpos($query, 'ALTER') === false &&
                strpos($query, 'alter') === false &&
                strpos($query, 'c_id') === false &&
                strpos($query, 'create table') === false &&
                strpos($query, 'CREATE TABLE') === false &&
                strpos($query, 'AUTO_INCREMENT') === false
            ) {
                //@todo remove this
                echo '<pre>';
                $message = '<h4>Dev message: please add the c_id field in this query or report this error in support.chamilo.org </h4>';
                $message .= $query;
                echo $message;
                echo '</pre>';
                //error_log($message);
            }
        }

        if (!($result = $use_default_connection ? mysql_query($query) : mysql_query($query, $connection))) {
            $backtrace = debug_backtrace(); // Retrieving information about the caller statement.
            if (isset($backtrace[0])) {
                $caller = & $backtrace[0];
            } else {
                $caller = array();
            }
            if (isset($backtrace[1])) {
                $owner = & $backtrace[1];
            } else {
                $owner = array();
            }
            if (empty($file)) {
                $file = $caller['file'];
            }
            if (empty($line) && $line !== false) {
                $line = $caller['line'];
            }
            $type = isset($owner['type']) ? $owner['type'] : null;
            $function = $owner['function'];
            $class = isset($owner['class']) ? $owner['class'] : null;
            $server_type = api_get_setting('server_type');
            if (!empty($line) && !empty($server_type) && $server_type != 'production') {
                $info = '<pre>' .
                    '<strong>DATABASE ERROR #'.self::errno($connection).':</strong><br /> ' .
                    self::remove_XSS(self::error($connection)) . '<br />' .
                    '<strong>QUERY       :</strong><br /> ' .
                    self::remove_XSS($query) . '<br />' .
                    '<strong>FILE        :</strong><br /> ' .
                    (empty($file) ? ' unknown ' : $file) . '<br />' .
                    '<strong>LINE        :</strong><br /> ' .
                    (empty($line) ? ' unknown ' : $line) . '<br />';
                if (empty($type)) {
                    if (!empty($function)) {
                        $info .= '<strong>FUNCTION    :</strong><br /> ' . $function;
                    }
                } else {
                    if (!empty($class) && !empty($function)) {
                        $info .= '<strong>CLASS       :</strong><br /> ' . $class . '<br />';
                        $info .= '<strong>METHOD      :</strong><br /> ' . $function;
                    }
                }
                $info .= '</pre>';
                echo $info;
            }

            if (isset(self::$log_queries) && self::$log_queries) {
                error_log("----------------  SQL error ---------------- ");
                error_log($query);

                error_log('error #'.self::errno($connection));
                error_log('error: '.self::error($connection));

                $info = 'FILE: ' .(empty($file) ? ' unknown ' : $file);
                $info .= ' +'.(empty($line) ? ' unknown ' : $line);
                error_log($info);

                if (empty($type)) {
                    if (!empty($function)) {
                        $info = 'FUNCTION: ' . $function;
                        error_log($info);
                    }
                } else {
                    if (!empty($class) && !empty($function)) {
                        $info = 'CLASS: ' . $class.' METHOD: '.$function;
                        error_log($info);
                    }
                }
                error_log("---------------- end ----------------");
            }
        }
        return $result;
    }

    /**
     * @param string $option
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
     * Selects a database.
     * @param string $database_name             The name of the database that is to be selected.
     * @param resource $connection (optional)   The database server connection, for detailed description see the method query().
     * @return bool                             Returns TRUE on success or FALSE on failure.
     */
    public static function select_db($database_name, $connection = null) {
        return self::use_default_connection($connection) ? mysql_select_db($database_name) : mysql_select_db($database_name, $connection);
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

        $array = array();
        if ($result !== false) { // For isolation from database engine's behaviour.
            while ($row = self::fetch_array($result, $option)) {
                $array[] = $row;
            }
        }
        return $array;
    }

    /*
        Encodings and collations supported by MySQL database server
    */

    /**
     * Checks whether a given encoding is supported by the database server.
     * @param string $encoding  The encoding (a system conventional id, for example 'UTF-8') to be checked.
     * @return bool             Returns a boolean value as a check-result.
     * @author Ivan Tcholakov
     */
    public static function is_encoding_supported($encoding) {
        static $supported = array();
        if (!isset($supported[$encoding])) {
            $supported[$encoding] = false;
            if (strlen($db_encoding = self::to_db_encoding($encoding)) > 0) {
                if (self::num_rows(self::query("SHOW CHARACTER SET WHERE Charset =  '".self::escape_string($db_encoding)."';")) > 0) {
                    $supported[$encoding] = true;
                }
            }
        }
        return $supported[$encoding];
    }

    /**
     * Constructs a SQL clause about default character set and default collation for newly created databases and tables.
     * Example: Database::make_charset_clause('UTF-8', 'bulgarian') returns
     *  DEFAULT CHARACTER SET `utf8` DEFAULT COLLATE `utf8_general_ci`
     * @param string $encoding (optional)   The default database/table encoding (a system conventional id) to be used.
     * @param string $language (optional)   Language (a system conventional id) used for choosing language sensitive collation (if it is possible).
     * @return string                       Returns the constructed SQL clause or empty string if $encoding is not correct or is not supported.
     * @author Ivan Tcholakov
     */
    public static function make_charset_clause($encoding = null, $language = null) {
        if (empty($encoding)) {
            $encoding = api_get_system_encoding();
        }
        if (empty($language)) {
            $language = api_get_interface_language();
        }
        $charset_clause = '';
        if (self::is_encoding_supported($encoding)) {
            $db_encoding = Database::to_db_encoding($encoding);
            $charset_clause .= " DEFAULT CHARACTER SET `".$db_encoding."`";
            $db_collation = Database::to_db_collation($encoding, $language);
            if (!empty($db_collation)) {
                $charset_clause .= " DEFAULT COLLATE `".$db_collation."`";
            }
        }
        return $charset_clause;
    }

    /**
     * Converts an encoding identificator to MySQL-specific encoding identifictor,
     * i.e. 'UTF-8' --> 'utf8'.
     * @param string $encoding  The conventional encoding identificator.
     * @return string           Returns the corresponding MySQL-specific encoding identificator if any, otherwise returns NULL.
     * @author Ivan Tcholakov
     */
    public static function to_db_encoding($encoding) {
        static $result = array();
        if (!isset($result[$encoding])) {
            $result[$encoding] = null;
            $encoding_map = & self::get_db_encoding_map();
            foreach ($encoding_map as $key => $value) {
                if (api_equal_encodings($encoding, $key)) {
                    $result[$encoding] = $value;
                    break;
                }
            }
        }
        return $result[$encoding];
    }

    /**
     * Converts a MySQL-specific encoding identifictor to conventional encoding identificator,
     * i.e. 'utf8' --> 'UTF-8'.
     * @param string $encoding  The MySQL-specific encoding identificator.
     * @return string           Returns the corresponding conventional encoding identificator if any, otherwise returns NULL.
     * @author Ivan Tcholakov
     */
    public static function from_db_encoding($db_encoding) {
        static $result = array();
        if (!isset($result[$db_encoding])) {
            $result[$db_encoding] = null;
            $encoding_map = & self::get_db_encoding_map();
            foreach ($encoding_map as $key => $value) {
                if (strtolower($db_encoding) == $value) {
                    $result[$db_encoding] = $key;
                    break;
                }
            }
        }
        return $result[$db_encoding];
    }

    /**
     * Chooses the default MySQL-specific collation from given encoding and language.
     * @param string $encoding              A conventional encoding id, i.e. 'UTF-8'
     * @param string $language (optional)   A conventional for the system language id, i.e. 'bulgarian'. If it is empty, the chosen collation is the default server value corresponding to the given encoding.
     * @return string                       Returns a suitable default collation, for example 'utf8_general_ci', or NULL if collation was not found.
     * @author Ivan Tcholakov
     */
    public static function to_db_collation($encoding, $language = null) {
        static $result = array();
        if (!isset($result[$encoding][$language])) {
            $result[$encoding][$language] = null;
            if (self::is_encoding_supported($encoding)) {
                $db_encoding = self::to_db_encoding($encoding);
                if (!empty($language)) {
                    $lang = api_purify_language_id($language);
                    $res = self::check_db_collation($db_encoding, $lang);
                    if (empty($res)) {
                        $db_collation_map = & self::get_db_collation_map();
                        if (isset($db_collation_map[$lang])) {
                            $res = self::check_db_collation($db_encoding, $db_collation_map[$lang]);
                        }
                    }
                    if (empty($res)) {
                        $res = self::check_db_collation($db_encoding, null);
                    }
                    $result[$encoding][$language] = $res;
                } else {
                    $result[$encoding][$language] = self::check_db_collation($db_encoding, null);
                }
            }
        }
        return $result[$encoding][$language];
    }

    /*
        Private methods
        You should not access these from outside the class
        No effort is made to keep the names / results the same.
    */

    /**
     * This private method is to be used by the other methods in this class for
     * checking whether the input parameter $connection actually has been provided.
     * If the input parameter connection is not a resource or if it is not FALSE (in case of error)
     * then the default opened connection should be used by the called method.
     * @param resource/boolean $connection  The checked parameter $connection.
     * @return boolean                      TRUE means that calling method should use the default connection.
     *                                      FALSE means that (valid) parameter $connection has been provided and it should be used.
     */
    private static function use_default_connection($connection) {
        return !is_resource($connection) && $connection !== false;
    }

    /**
     * This private method encapsulates a table with relations between
     * conventional and MuSQL-specific encoding identificators.
     * @author Ivan Tcholakov
     */
    private static function & get_db_encoding_map() {
        static $encoding_map = array(
            'ARMSCII-8'    => 'armscii8',
            'BIG5'         => 'big5',
            'BINARY'       => 'binary',
            'CP866'        => 'cp866',
            'EUC-JP'       => 'ujis',
            'EUC-KR'       => 'euckr',
            'GB2312'       => 'gb2312',
            'GBK'          => 'gbk',
            'ISO-8859-1'   => 'latin1',
            'ISO-8859-2'   => 'latin2',
            'ISO-8859-7'   => 'greek',
            'ISO-8859-8'   => 'hebrew',
            'ISO-8859-9'   => 'latin5',
            'ISO-8859-13'  => 'latin7',
            'ISO-8859-15'  => 'latin1',
            'KOI8-R'       => 'koi8r',
            'KOI8-U'       => 'koi8u',
            'SHIFT-JIS'    => 'sjis',
            'TIS-620'      => 'tis620',
            'US-ASCII'     => 'ascii',
            'UTF-8'        => 'utf8',
            'WINDOWS-1250' => 'cp1250',
            'WINDOWS-1251' => 'cp1251',
            'WINDOWS-1252' => 'latin1',
            'WINDOWS-1256' => 'cp1256',
            'WINDOWS-1257' => 'cp1257'
        );
        return $encoding_map;
    }

    /**
     * A helper language id translation table for choosing some collations.
     * @author Ivan Tcholakov
     */
    private static function & get_db_collation_map() {
        static $db_collation_map = array(
            'german' => 'german2',
            'simpl_chinese' => 'chinese',
            'trad_chinese' => 'chinese',
            'turkce' => 'turkish'
        );
        return $db_collation_map;
    }

    /**
     * Constructs a MySQL-specific collation and checks whether it is supported by the database server.
     * @param string $db_encoding   A MySQL-specific encoding id, i.e. 'utf8'
     * @param string $language      A MySQL-compatible language id, i.e. 'bulgarian'
     * @return string               Returns a suitable default collation, for example 'utf8_general_ci', or NULL if collation was not found.
     * @author Ivan Tcholakov
     */
    private static function check_db_collation($db_encoding, $language) {
        if (empty($db_encoding)) {
            return null;
        }
        if (empty($language)) {
            $result = self::fetch_array(self::query("SHOW COLLATION WHERE Charset = '".self::escape_string($db_encoding)."' AND  `Default` = 'Yes';"), 'NUM');
            return $result ? $result[0] : null;
        }
        $collation = $db_encoding.'_'.$language.'_ci';
        $query_result = self::query("SHOW COLLATION WHERE Charset = '".self::escape_string($db_encoding)."';");
        while ($result = self::fetch_array($query_result, 'NUM')) {
            if ($result[0] == $collation) {
                return $collation;
            }
        }
        return null;
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
        $result = self::getManager()->getConnection()->insert($table_name, $attributes);
        if ($result) {
            return self::insert_id();
        }
        return false;

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
