<?php

class Model
{
    public $link; // key of App::$configs like ['my_diary_db']
    public $db;
    public $table;

    private $sqls = array();

    public function __construct($targetTable = null)
    {
        if (is_null($targetTable)) return;
        $p = explode('.', $targetTable);
        // link.db.table
        // .db.table
        if (count($p) === 3) {
            $p[0] && $this->link = $p[0];
            $p[1] && $this->db = $p[1];
            $this->table = $p[2];
            return;
        }
        // link.table (link包含了db)
        if (count($p) === 2) {
            $p[0] && $this->link = $p[0];
            $this->table = $p[1];
            return;
        }
        // table
        if (count($p) === 1) {
            $this->table = $p[0];
        }
    }

    public function dbInstance($dbConfig, $key, $forceReplace = false): PDO
    {
        if ($forceReplace || empty(App::$caches['dbInstances'][$this->link][$key])) {
            try {
                $dsn = $dbConfig['DSN']
                    ?? 'mysql:host=' . $dbConfig['HOST']
                    . (isset($dbConfig['PORT']) ? ':' . $dbConfig['PORT'] : '')
                    . (($db = !empty($this->db) ? $this->db : ($dbConfig['NAME'] ?? '')) ? ';dbname=' . $db : '')
                    . (isset($dbConfig['CHAR']) ? ';charset=' . $dbConfig['CHAR'] : '');

                App::$caches['dbInstances'][$this->link][$key] = new PDO($dsn, $dbConfig['USER'], $dbConfig['PASS']);
            } catch (PDOException $e) {
                throw new PDOException('Database connection error: ' . $e->getMessage());
            }
        }
        return App::$caches['dbInstances'][$this->link][$key];
    }

    public function getDbInstance(bool $readonly = false): PDO
    {
        if ($readonly && !empty(App::$configs[$this->link]['SLAVE']) && is_array(App::$configs[$this->link]['SLAVE'])) {
            if (isset(App::$configs[$this->link]['SLAVE'][0])) {
                $slaveIndex = array_rand(App::$configs[$this->link]['SLAVE']);
                return $this->dbInstance(App::$configs[$this->link]['SLAVE'][$slaveIndex], 'slave_' . $slaveIndex);
            } else {
                return $this->dbInstance(App::$configs[$this->link]['SLAVE'], 'slave');
            }
        } else {
            return $this->dbInstance(App::$configs[$this->link], 'master');
        }
    }

    private function getDataType($value): int
    {
        if (is_int($value)) return PDO::PARAM_INT;
        if (is_bool($value)) return PDO::PARAM_BOOL;
        if (is_null($value)) return PDO::PARAM_NULL;
        return PDO::PARAM_STR;
    }

    public function execute(string $sql, array $params = [], bool $readonly = false): PDOStatement
    {
        $this->sqls[] = $sql;

        $stmt = $this->getDbInstance($readonly);
        $stmt = $stmt->prepare($sql);

        if (is_array($params) && !empty($params)) {
            foreach ($params as $k => $v) {
                $data_type = $this->getDataType($v);
                if (is_int($k)) $k = $k + 1;
                $stmt->bindValue($k, $v, $data_type);
            }
        }

        if (!$stmt->execute()) {
            $err = $stmt->errorInfo();
            throw new PDOException('Database SQL: "' . $sql . '", ErrorInfo: ' . $err[2]);
        }

        return $stmt;
    }

    public function query($sql, $params = array())
    {
        return $this->execute($sql, $params, true)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function queryColumn($sql, $params = array())
    {
        return $this->execute($sql, $params, true)->fetchColumn();
    }

    public function queryOne($sql, $params = array())
    {
        $sql .= ' LIMIT 1';
        return $this->execute($sql, $params, true)->fetch();
    }

    public function queryScalar($sql, $params = array())
    {
        $sql .= ' LIMIT 1';
        return $this->execute($sql, $params, true)->fetchColumn();
    }

    /**
     * @param array $conditions 复杂用 rawWhere+...params ; 简单用 key=>value
     * @return array
     */
    private function _where($conditions)
    {
        $result = array('_where' => " ", '_bindParams' => array());
        if (is_array($conditions) && !empty($conditions)) {
            $sql = null;
            $join = array();
            if (isset($conditions[0]) && $sql = $conditions[0]) unset($conditions[0]);
            foreach ($conditions as $key => $condition) {
                if (substr($key, 0, 1) != ':') {
                    unset($conditions[$key]);
                    $conditions[":$key"] = $condition;
                }
                $join[] = "`{$key}` = :{$key}";
            }
            if (!$sql) $sql = join(' AND ', $join);

            $result['_where'] = " WHERE $sql";
            $result['_bindParams'] = $conditions;
        }
        return $result;
    }

    public function buildQuery($conditions = array(), $sort = null, $fields = '*', $limit = null, $extra = array())
    {
        $conditions = $this->_where($conditions);
        $sql = "SELECT $fields FROM `$this->table`"
            . (isset($extra['alias']) ? " AS {$extra['alias']}" : '')
            . (isset($extra['join']) ? " {$extra['join']}" : '')
            . $conditions['_where']
            . (isset($extra['groupBy']) ? " GROUP BY {$extra['groupBy']}" : '')
            . (!empty($sort) ? " ORDER BY $sort" : '')
            . (!empty($limit) ? " LIMIT $limit" : '');
        return array($sql, $conditions['_bindParams']);
    }

    public function findAll($conditions = array(), $sort = null, $fields = '*', $limit = null, $extra = array())
    {
        list($sql, $bindParams) = $this->buildQuery($conditions, $sort, $fields, $limit, $extra);
        return $this->query($sql, $bindParams);
    }

    public function find($conditions = array(), $sort = null, $fields = '*', $extra = array())
    {
        list($sql, $bindParams) = $this->buildQuery($conditions, $sort, $fields, null, $extra);
        return $this->queryOne($sql, $bindParams);
    }

    public function findCount($conditions)
    {
        list($sql, $bindParams) = $this->buildQuery($conditions);
        return $this->queryScalar($sql, $bindParams) ?: 0;
    }

    public function update($conditions, $row)
    {
        $values = $sets = array();
        foreach ($row as $k => $v) {
            $values[":M_UPDATE_" . $k] = $v;
            $sets[] = "`{$k}` = " . ":M_UPDATE_" . $k;
        }
        $conditions = $this->_where($conditions);
        $sql = "UPDATE `$this->table` SET " . implode(', ', $sets) . $conditions['_where'];
        return $this->execute($sql, $conditions['_bindParams'] + $values)->rowCount();
    }

    public function incr($conditions, $field, $val = 1)
    {
        $conditions = $this->_where($conditions);
        $sql = "UPDATE `$this->table` SET `{$field}` = `{$field}` + :M_INCR_VAL{$conditions['_where']}";
        return $this->execute($sql, $conditions['_bindParams'] + array(":M_INCR_VAL" => $val))->rowCount();
    }

    public function decr($conditions, $field, $val = 1)
    {
        return $this->incr($conditions, $field, -$val);
    }

    public function delete($conditions)
    {
        $conditions = $this->_where($conditions);
        return $this->execute("DELETE FROM `$this->table`{$conditions['_where']}", $conditions['_bindParams'])->rowCount();
    }

    public function create($row)
    {
        if (!$row) return false;
        $values = $keys = $marks = array();
        foreach ($row as $k => $v) {
            $keys[] = "`{$k}`";
            $marks[] = ":$k";
            $values[":$k"] = $v;
        }
        $sql = "INSERT INTO `$this->table` (" . implode(', ', $keys) . ') VALUES (' . implode(', ', $marks) . ")";
        $this->execute($sql, $values);
        return $this->getDbInstance()->lastInsertId();
    }

    /**
     * 批量插入数据
     *
     * @param array $rows 数据数组
     * @param array $columns 字段s 注意与 $rows 保持一致，包括顺序!!!
     * @return int 影响的行数
     */
    public function batchInsert(array $rows, array $columns = [])
    {
        if (empty($rows)) return 0;

        $columns = $columns ?: array_keys(current($rows));
        $columnNames = implode(',', $columns);
        $placeholders = [];
        $values = [];

        foreach ($rows as $row) {
            $placeholders[] = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';
            $values = array_merge($values, array_values($row));
        }

        $sql = "INSERT INTO `$this->table` ($columnNames) VALUES " . implode(',', $placeholders);
        return $this->execute($sql, $values)->rowCount();
    }

    /**
     * 实现 upsert 操作（有pk/unique key才行）
     *
     * @param array $insertColumns 插入的字段和值
     * @param array $updateColumns 更新的字段和值
     * @return int 影响的行数
     */
    public function upsert(array $insertColumns, array $updateColumns)
    {
        $allColumns = $insertColumns + $updateColumns;
        $columns = array_keys($allColumns);
        $placeholders = array_fill(0, count($allColumns), '?');
        $insertSql = "INSERT INTO `$this->table` (`" . implode('`, `', $columns) . '`) VALUES (' . implode(', ', $placeholders) . ')';

        $updatePairs = [];
        foreach ($updateColumns as $column => $value) {
            $updatePairs[] = "`$column` = ?";
        }
        $updateSql = 'ON DUPLICATE KEY UPDATE ' . implode(', ', $updatePairs);

        $sql = $insertSql . ' ' . $updateSql;
        $values = array_merge(array_values($allColumns), array_values($updateColumns));

        return $this->execute($sql, $values)->rowCount();
    }

    public function dumpSql()
    {
        return $this->sqls;
    }
}
