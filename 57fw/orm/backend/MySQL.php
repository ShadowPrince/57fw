<?php
namespace Orm\Backend;

class MySQL implements GeneralBackend {
    public function __construct($e, $connection) {
        $this->e = $e;
        mysql_connect($connection['host'], $connection['user'], $connection['password']);
        mysql_select_db($connection['database']);
    }

    public function select($manager, $wh, $fields, $additions=array()) {
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

        return $this->fetchArrayResult($this->executeQuery($this->buildFQuery(
            'SELECT %s FROM %s WHERE %s %s', 
            implode(', ', $fields),
            $manager->getTable(),
            $this->whereParamsImplode($wh),
            implode(' ', $additions_prepared)
        )));
    }

    public function update($manager, $kv, $wh) {
        $this->executeQuery($this->buildFQuery(
            'UPDATE %s SET %s WHERE %s',
            $manager->getTable(),
            $kv,
            $this->whereParamsImplode($wh)
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

    public function delete($manager, $wh) {
        $this->executeQuery($this->buildFQuery(
            'DELETE FROM %s WHERE %s',
            $manager->getTable(),
            $wh
        ));
    }

    public function count($manager, $wh) {
        return $this->fetchArrayResult($this->executeQuery($this->buildFQuery(
            'SELECT COUNT(*) FROM %s WHERE %s',
            $manager->getTable(),
            $wh
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
        mysql_query($x) or die(mysql_error());

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
                if ($opts['sql'])
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

    protected function provideField($field) {
        return $field->getName() . ' ' . strtoupper($field->getType()); 
    }

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
     */
    protected function executeQuery($qw) {
        $res = mysql_query($qw) or die(mysql_error());
        return $res;
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
        foreach ($args as &$arg)
            if (is_array($arg))
                $arg = $this->paramsImplode($arg);
        return call_user_func_array('sprintf', $args); 
    }

    /**
     * Escape array or string
     * @param mixed
     * @return mixed
     */
    public function escape($mixed) {
        if (is_array($mixed)) {
            if ($mixed) foreach ($mixed as &$el)
                $el = $this->escape($el);
        } else {
            $mixed = mysql_real_escape_string($mixed);
        }
        return $mixed;
    }

    /**
     * Implode $kv for "where" part of query
     * @param array
     * @return array
     */
    public function whereParamsImplode($kv) {
        if ($kv == '1') {
            return '1';
        }
        $params = array();
        foreach ($kv as $k => $v) {
            if (is_array($v)) {
                $v = $this->escape($v);
                $params[] = call_user_func_array(
                    'sprintf',
                    array_merge([$k], $v)
                );
            } else {
                $params[] = (string) $v;
            }
        }        

        return implode(', ', $params);
    }

    /**
     * Implode $kv for "set" part of query
     * @param array
     * @return array
     */
    public function paramsImplode($kv) {
        $params = array();
        foreach ($kv as $k => $v) {
            $v = $this->escape((string) $v); 
            $params[] = '`' . $k . '` = \'' . (string) $v . '\''; 
        }
        return implode(', ', $params);
    }
}
