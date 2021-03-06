<?php
namespace Orm\Backend\MySQL;

class MySQL extends \Core\Service implements \Orm\Backend\GeneralBackend {
    protected $querySetClass = '\Orm\Backend\MySQL\MyQuerySet';

    /**
     * @param mixed
     */
    public function __construct($config=array()) {
        parent::__construct($config);
        mysql_connect(
            $this->config('host'),
            $this->config('user'),
            $this->config('password')
        );
        mysql_select_db($this->config('database'));
    }

    public function select($manager, $wh, $fields, $additions=array()) {
        return $this->fetchArrayResult($this->executeQuery($this->buildFQuery(
            'SELECT %s FROM %s WHERE %s %s', 
            implode(', ', $fields),
            $manager->getTable(),
            $this->whereParamsImplode($wh),
            $this->additionsParamsImplode($additions)
        )));
    }

    public function update($manager, $kv, $wh, $additions=array()) {
        $this->executeQuery($this->buildFQuery(
            'UPDATE %s SET %s WHERE %s %s',
            $manager->getTable(),
            $kv,
            $this->whereParamsImplode($wh),
            $this->additionsParamsImplode($additions)
        ));
    }

    public function insert($manager, $kv) {
        $this->executeQuery($this->buildFQuery(
            'INSERT INTO %s SET %s',
            $manager->getTable(),
            $kv
        ));

        return mysql_insert_id();
    }

    public function delete($manager, $wh, $additions=array()) {
        $this->executeQuery($this->buildFQuery(
            'DELETE FROM %s WHERE %s %s',
            $manager->getTable(),
            $this->whereParamsImplode($wh),
            $this->additionsParamsImplode($additions)
        ));
    }

    public function count($manager, $wh, $additions=array()) {
        return $this->fetchSingleResult($this->executeQuery($this->buildFQuery(
            'SELECT COUNT(*) FROM %s WHERE %s',
            $manager->getTable(),
            $this->whereParamsImplode($wh),
            $this->additionsParamsImplode($additions)
        )))['COUNT(*)'];
    }

    public function prepare($manager, $opts, $print_callback) {
        $cls = $manager->getModel();
        $fields = (new $cls)->getFields();
        $prep_fields = array();
        $sql = array();

        if ($fields) foreach ($fields as $field) {
            $prep_fields[] = $this->provideField($field); 
        }

        $x = ($this->buildFQuery(
            'CREATE TABLE IF NOT EXISTS %s (%s)',
            $manager->getTable() . '_tmp',
            implode(', ', $prep_fields)
        )); 
        $this->executeQuery($x);

        if ($this->tableExists($manager->getTable())) {
            $cols_old = $this->getColumns($manager->getTable());
            $cols_new = $this->getColumns($manager->getTable() . '_tmp');

            $cols_add = array();
            $cols_delete = array();
            $cols_mod = array();

            foreach ($cols_new as $name => $opt) {
                if (!isset($cols_old[$name])) {
                    $cols_add[] = $name;
                    $sql[] = ($this->buildFQuery(
                        'ALTER TABLE %s ADD %s',
                        $manager->getTable(),
                        $this->provideField($fields[$name])
                    ));
                } else if ($cols_old[$name] !== $opt) {
                    $cols_mod[] = $name; 
                    $sql[] = ($this->buildFQuery(
                        'ALTER TABLE %s MODIFY %s',
                        $manager->getTable(),
                        $this->provideField($fields[$name])
                    ));
                } 
                if (isset($cols_old[$name])) {
                    unset($cols_old[$name]);
                }
            }

            if ($cols_old) foreach ($cols_old as $name => $opt) {
                $cols_del[] = $name;
                $sql[] = ($this->buildFQuery(
                    'ALTER TABLE %s DROP COLUMN %s',
                    $manager->getTable(),
                    $name
                ));
            }

            if (count($sql)) {
                if (!isset($opts['force'])) {
                    $print_callback(sprintf(
                        'Table for %s already exists, add -force to modify it. ',
                        $manager->getModel()
                    ));
                    $sql = array();
                } else {
                    $print_callback(sprintf(
                        'Modifying table %s for app %s ...',
                        $manager->getTable(),
                        $manager->getModel()
                    ));
                }
            }
        } else {
            $print_callback(sprintf(
                'Creating table %s for app %s ...',
                $manager->getTable(),
                $manager->getModel()
            ));
            $sql[] = $this->buildFQuery(
                'ALTER TABLE %s RENAME %s',
                $manager->getTable() . '_tmp',
                $manager->getTable()
            );

        } 

        if ($sql) {
            foreach ($sql as $qw) {
                if (isset($opts['sql']))
                    $print_callback('  ' . $qw);
                else
                    $this->executeQuery($qw);
            }
        } else {
            $print_callback(sprintf(
                'Table for app %s is up-to-date',
                $manager->getModel()
            ));
        }
        $this->executeQuery($this->buildFQuery(
            'DROP TABLE IF EXISTS %s',
            $manager->getTable() . '_tmp'
        )); 

    }
    
    /**
     * Get new instance of related QuerySet
     * @param \Orm\Manager
     * @return \Orm\QuerySet
     */
    public function getQuerySet($manager) {
        return new $this->querySetClass($manager); 
    }

    /**
     * Get description of field in sql
     * @param \Orm\Field\Field
     * @return string
     */
    protected function provideField($field) {
        $params = array($field->getName() . ' ' . strtoupper($field->getType()));
        if ($field->config('null') != true)
            $params[] = 'NOT NULL';

        if ($field->config('uniq'))
            $params[] = 'UNIQUE';

        if ($field instanceof \Orm\Field\PrimaryKey) {
            $params[] = 'PRIMARY KEY';
            if ($field->config('auto_increment') != false)
                $params[] = 'AUTO_INCREMENT';
        }

        return implode(' ', $params);
    }

    /**
     * Get columns from $table
     * @param string
     * @return array
     */
    protected function getColumns($table) {
        $cols = $this->fetchArrayResult($this->executeQuery($this->buildFQuery(
            'SHOW COLUMNS FROM %s',
            $table
        )));
        $cols_prep = array();
        foreach ($cols as $c) {
            $cols_prep[$c['Field']] = $c;
        }

        return $cols_prep;
    }

    /**
     * @param string
     * @return bool
     */
    protected function tableExists($table) {
        return count($this->fetchArrayResult($this->executeQuery($this->buildFQuery(
            'SHOW TABLES LIKE \'%s\'',
            $table
        ))));
    }

    /**
     * Execute query
     * @param string
     */
    protected function executeQuery($qw) {
        $res = mysql_query($qw) or ($this->executeError($qw));
        return $res;
    }

    /**
     * Handle query execution error
     * @param string
     * @throw \Orm\Ex\ExecuteException
     */
    protected function executeError($qw) {
        if ($this->config('debug'))
            throw new \Orm\Ex\ExecuteException(mysql_error(), $qw);
        else
            throw new \Orm\Ex\ExecuteException('(debug disabled)', '(debug disabled)');
    }

    /**
     * Fetch query result to rows array
     * @param mixed
     * @return array
     */
    protected function fetchArrayResult($res) {
        $data = array();
        while ($row = mysql_fetch_assoc($res))
            $data[] = $row;
        return $data;
    }

    /**
     * Fetch query result to single row array
     * @param mixed
     * @return array
     */
    protected function fetchSingleResult($res) {
        return mysql_fetch_array($res);
    }
    
    /**
     * Build query $qw with printf syntax
     * @return string
     */
    public function buildFQuery($qw) {
        $args = func_get_args();
        foreach ($args as &$arg) {
            $arg = str_replace('?', '%s', $arg);
            if (is_array($arg))
                $arg = $this->paramsImplode($arg);
        }
        return call_user_func_array('sprintf', $args); 
    }

    /**
     * Prepare and escape different values
     * @param mixed
     * @return mixed
     */
    public function escape($mixed) {
        if (is_array($mixed)) {
            if ($mixed) foreach ($mixed as &$el)
                $el = $this->escape($el);
        } else {
            if ($mixed === null) {
                $mixed = 'NULL';
            } else if (is_string($mixed)) {
                $mixed = mysql_real_escape_string($mixed);
                $mixed = "'" . $mixed . "'";
            } else if (is_bool($mixed))  {
                $mixed = $mixed ? 'TRUE' : 'FALSE';
            }
        }
        return $mixed;
    }

    /**
     * Implode $kv for "where" part of query
     * @param array
     * @return array
     */
    public function whereParamsImplode($kv) {
        if (is_string($kv)) {
            return $kv;
        }
        $params = array();
        foreach ($kv as $k => $v) {
            if (is_array($v)) {
                $str = str_replace('?', '%s', array_shift($v));
                $v = $this->escape($v);
                $params[] = call_user_func_array(
                    'sprintf',
                    array_merge(array($str), $v)
                );
            } else {
                $params[] = (string) $v;
            }
        }        

        return implode(' ', $params);
    }

    /**
     * Implode $kv for "set" part of query
     * @param array
     * @return array
     */
    public function paramsImplode($kv) {
        $params = array();
        foreach ($kv as $k => $v) {
            $params[] = '`' . $k . '` = ' . $this->escape($v) . ''; 
        }
        return implode(', ', $params);
    }

    /**
     * Parse additions array
     * @param array
     * @return array
     */
    protected function parseAdditions($additions) {
        $additions_prepared = array();
        $limit = '';
        foreach ($additions as $add => &$val) {
            switch ($add) {
            case 'limit':
                $limit = 'LIMIT ' . $val;
                break;
            case 'order':
                $additions_prepared[] = 'ORDER BY ' . $val;
                break;
            } 
        }
        $additions_prepared[] = $limit;
        return $additions_prepared;
    }

    /**
     * Parse and implode additions array
     * @param array
     * @return string
     */
    protected function additionsParamsImplode($additions) {
        return implode(' ', $this->parseAdditions($additions));
    }
}
