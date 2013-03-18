<?php
namespace Orm\Backend\PDO;

class PDO implements \Orm\Backend\GeneralBackend {
    public $querySetClass = '\Orm\Backend\PDO\PDOQuerySet';
    protected $dbh;
    
    public function __construct($config) {
        $this->dbh = new \PDO(
            sprintf(
                '%s:host=%s;dbname=%s',
                $config['type'], $config['host'], $config['database']
            ), $config['user'], $config['password']);
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); 
    }

    /**
     * Get new instance of related QuerySet
     * @param \Orm\Manager
     * @return \Orm\QuerySet
     */
    public function getQuerySet($manager) {
        return new $this->querySetClass($manager); 
    }

    public function select($manager, $wh, $fields, $additions=array()) {
        return $this->buildExecuteFetch(
            'SELECT %s FROM %s WHERE %s %s',
            array(),
            implode(', ', $fields),
            $manager->getTable(),

            $this->whereParamsImplode($wh),
            $this->additionsParamsImplode($additions)
        );
    }

    public function update($manager, $kv, $wh, $additions=array()) {
        $this->buildExecute(
            'UPDATE %s SET %s WHERE %s %s',
            array(),

            $manager->getTable(),
            $this->setParamsImplode($kv),
            $this->whereParamsImplode($wh),
            $this->additionsParamsImplode($additions)
        );
    }

    public function insert($manager, $kv) {
        $this->buildExecute(
            'INSERT INTO %s SET %s', 
            array(),

            $manager->getTable(),
            $this->setParamsImplode($kv)
        );
    }

    public function delete($manager, $wh, $additions=array()) {
        $this->buildExecute(
            'DELETE FROM %s WHERE %s %s',
            array(),

            $manager->getTable(),
            $this->whereParamsImplode($wh),
            $this->additionsParamsImplode($additions)
        );
    }

    public function count($manager, $wh, $additions=array()) {
        return $this->buildExecuteFetch(
            'SELECT COUNT(*) FROM %s WHERE %s %s',
            array(),

            $manager->getTable(),
            $this->whereParamsImplode($wh),
            $this->additionsParamsImplode($additions)
        )[0]['COUNT(*)'];
    }
    public function prepare($manager, $opts, $print_callback) {
        $cls = $manager->getModel();
        $fields = (new $cls)->getFields();
        $prep_fields = array();
        $sql = array();

        if ($fields) foreach ($fields as $field) {
            $prep_fields[] = $this->provideField($field); 
        }

        $this->buildExecute(
            'CREATE TABLE IF NOT EXISTS %s (%s)',
            array(), 

            $manager->getTable() . '_tmp',
            implode(', ', $prep_fields)
        ); 

        if ($this->tableExists($manager->getTable())) {
            $cols_old = $this->getColumns($manager->getTable());
            $cols_new = $this->getColumns($manager->getTable() . '_tmp');

            $cols_add = array();
            $cols_delete = array();
            $cols_mod = array();

            foreach ($cols_new as $name => $opt) {
                if (!isset($cols_old[$name])) {
                    $cols_add[] = $name;
                    $sql[] = ($this->build(
                        'ALTER TABLE %s ADD %s',
                        array(), 

                        $manager->getTable(),
                        $this->provideField($fields[$name])
                    ));
                } else if ($cols_old[$name] !== $opt) {
                    $cols_mod[] = $name; 
                    $sql[] = ($this->build(
                        'ALTER TABLE %s MODIFY %s',
                        array(), 

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
                $sql[] = ($this->build(
                    'ALTER TABLE %s DROP COLUMN %s',
                    array(), 

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

            $sql[] = $this->build(
                'ALTER TABLE %s RENAME %s',
                array(), 

                $manager->getTable() . '_tmp',
                $manager->getTable()
            );

        } 

        if ($sql) {
            foreach ($sql as $qw) {
                if (isset($opts['sql']))
                    $print_callback('  ' . $qw[0]->queryString);
                else {
                    $this->execute($qw[0], array());
                }
            }
        } else {
            $print_callback(sprintf(
                'Table for app %s is up-to-date',
                $manager->getModel()
            ));
        }
        $this->buildExecute(
            'DROP TABLE IF EXISTS %s',
            array(), 

            $manager->getTable() . '_tmp'
        ); 

    }

    protected function build() {
        $args = func_get_args();

        $qw = array_shift($args);
        $data = array_shift($args);

        foreach ($args as &$arg) {
            if (isset($arg['data'])) {
                $data = array_merge($data, $arg['data']);
                $arg = $arg['sql'];
            }
        } 

        $qw = call_user_func_array('sprintf', array_merge(array($qw), $args));
        return array(
            $this->dbh->prepare($qw),
            $data
        );
    }

    protected function execute($st, $data) {
        try {
            $st->execute($data);
            return $st;
        } catch (\PDOException $e) {
            if ($this->config('debug'))
                throw new \Orm\Ex\ExecuteException($st->queryString, $e->getMessage());
            else
                throw new \Orm\Ex\ExecuteException('(debug mode off)', '');
        }
    }

    protected function setParamsImplode($kv) {
        $sql = array();
        $data = array();
        foreach ($kv as $k => $v) {
            $sql[] = $k . ' = ?';
            $data[] = $v;
        }

        return array(
            'sql' => implode(', ', $sql),
            'data' => $data
        );
    }

    protected function whereParamsImplode($wh) {
        $qw = '';
        $sql = array_map(function ($el) {
            return $el[0];
        }, $wh);
        $data = array();
        foreach ($wh as $el) {
            array_shift($el);
            $data = array_merge($data, $el);
        }

        return array(
            'sql' => implode(' ', $sql),
            'data' => $data
        );
    }


    protected function fetchArray($st) {
        $st->setFetchMode(\PDO::FETCH_ASSOC);
        $data = array();
        while ($row = $st->fetch()) {
            $data[] = $row;
        }

        return $data;
    }

    protected function buildExecuteFetch() {
        return $this->fetchArray(call_user_func_array(
            array($this, 'buildExecute'),
            func_get_args()
        ));
    }

    protected function buildExecute() {
        return call_user_func_array(
            array($this, 'execute'),
            call_user_func_array(
                array($this, 'build'),
                func_get_args()
            ));
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

    protected function tableExists($table) {
        return count($this->buildExecuteFetch(
            'SHOW TABLES LIKE \'%s\'',
            array(),

            $table
        ));
    }
    
    /**
     * Get columns from $table
     * @param string
     * @return array
     */
    protected function getColumns($table) {
        $cols = $this->buildExecuteFetch(
            'SHOW COLUMNS FROM %s',
            array(),

            $table
        );

        $cols_prep = array();
        foreach ($cols as $c) {
            $cols_prep[$c['Field']] = $c;
        }

        return $cols_prep;
    }

    /**
     * Parse and implode additions array
     * @param array
     * @return string
     */
    protected function additionsParamsImplode($additions) {
        return implode(' ', $this->parseAdditions($additions));
    }

    /**
     * Get description of field in sql
     * @param \Orm\Field\Field
     * @return string
     */
    protected function provideField($field) {
        $params = array($field->getName() . ' ' . strtoupper($field->getType()));
        if ($field->param('null') != true)
            $params[] = 'NOT NULL';

        if ($field->param('uniq'))
            $params[] = 'UNIQUE';

        if ($field instanceof \Orm\Field\PrimaryKey) {
            $params[] = 'PRIMARY KEY';
            if ($field->param('auto_increment') != false)
                $params[] = 'AUTO_INCREMENT';
        }

        return implode(' ', $params);
    }
}
