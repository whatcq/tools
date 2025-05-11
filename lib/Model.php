<?php

class Model
{
    public $page;

    protected $dbInstances; // 缓存master/slave的
    public $link;           // 初始化就确定了
    public $db;
    public $table;

    private $sql = array();

    public function __construct($targetTable = null)
    {
        if (is_null($targetTable)) {
            return;
        }
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

    public function dbInstance($dbConfig, $key, $forceReplace = false)
    {
        if ($forceReplace || empty($this->dbInstances[$key])) {
            try {
                $dsn = isset($dbConfig['DSN'])
                    ? $dbConfig['DSN']
                    : 'mysql:host=' . $dbConfig['HOST']
                    . (isset($dbConfig['PORT']) ? ':' . $dbConfig['PORT'] : '')
                    . (($db = !empty($this->db) ? $this->db : (isset($dbConfig['NAME']) ? $dbConfig['NAME'] : '')) ? ';dbname=' . $db : '')
                    . (isset($dbConfig['CHAR']) ? ';charset=' . $dbConfig['CHAR'] : '');

                $this->dbInstances[$key] = new PDO($dsn, $dbConfig['USER'], $dbConfig['PASS']);
            } catch (PDOException $e) {
                throw new Exception('Database Err: ' . $e->getMessage());
            }
        }
        return $this->dbInstances[$key];
    }

    public function execute($sql, $params = array(), $readonly = false)
    {
        //echo 'SQL: ' . $sql . PHP_EOL;
        $this->sql[] = $sql;

        if ($readonly && !empty(App::$configs[$this->link]['MYSQL_SLAVE'])) {
            $slave_key = array_rand(App::$configs[$this->link]['MYSQL_SLAVE']);
            $sth = $this->dbInstance(App::$configs[$this->link]['MYSQL_SLAVE'][$slave_key], 'slave_' . $slave_key)->prepare($sql);
        } else {
            $sth = $this->dbInstance(App::$configs[$this->link], 'master')->prepare($sql);
        }

        if (is_array($params) && !empty($params)) {
            foreach ($params as $k => &$v) {
                if (is_int($v)) {
                    $data_type = PDO::PARAM_INT;
                } elseif (is_bool($v)) {
                    $data_type = PDO::PARAM_BOOL;
                } elseif (is_null($v)) {
                    $data_type = PDO::PARAM_NULL;
                } else {
                    $data_type = PDO::PARAM_STR;
                }
                if (is_int($k)) $k = $k + 1;
                $sth->bindParam($k, $v, $data_type);
            }
        }

        if ($sth->execute()) return $readonly ? $sth->fetchAll(PDO::FETCH_ASSOC) : $sth->rowCount();
        $err = $sth->errorInfo();
        throw new Exception('Database SQL: "' . $sql . '", ErrorInfo: ' . $err[2]);
    }

    public function query($sql, $params = array())
    {
        return $this->execute($sql, $params, true);
    }

    /**
     * @param $conditions
     * @param $sort
     * @param $fields
     * @param $limit int|array($page, $pageSize, $scope 显示页数)
     * @return array|false|int|null
     */
    public function findAll($conditions = array(), $sort = null, $fields = '*', $limit = null)
    {
        $sort = !empty($sort) ? ' ORDER BY ' . $sort : '';
        $conditions = $this->_where($conditions);

        $sql = ' FROM ' . $this->table . $conditions["_where"];
        if (is_array($limit)) {
            $total = $this->query('SELECT COUNT(*) as M_COUNTER ' . $sql, $conditions["_bindParams"]);
            if (!isset($total[0]['M_COUNTER']) || $total[0]['M_COUNTER'] == 0) return array();

            $limit = $limit + array(1, 10, 10);
            $pager = $this->pager($limit[0], $limit[1], $limit[2], $total[0]['M_COUNTER']);
            $limit = empty($pager) ? '' : ' LIMIT ' . $pager['offset'] . ',' . $pager['limit'];
        } else {
            $limit = !empty($limit) ? ' LIMIT ' . $limit : '';
        }
        return $this->query('SELECT ' . $fields . $sql . $sort . $limit, $conditions["_bindParams"]);
    }

    public function find($conditions = array(), $sort = null, $fields = '*')
    {
        $res = $this->findAll($conditions, $sort, $fields, 1);
        return !empty($res) ? array_pop($res) : false;
    }

    public function findCount($conditions)
    {
        $conditions = $this->_where($conditions);
        $count = $this->query("SELECT COUNT(*) AS M_COUNTER FROM " . $this->table . $conditions["_where"], $conditions["_bindParams"]);
        return isset($count[0]['M_COUNTER']) && $count[0]['M_COUNTER'] ? $count[0]['M_COUNTER'] : 0;
    }

    public function update($conditions, $row)
    {
        $values = $sets = array();
        foreach ($row as $k => $v) {
            $values[":M_UPDATE_" . $k] = $v;
            $sets[] = "`{$k}` = " . ":M_UPDATE_" . $k;
        }
        $conditions = $this->_where($conditions);
        return $this->execute("UPDATE " . $this->table . " SET " . implode(', ', $sets) . $conditions["_where"], $conditions["_bindParams"] + $values);
    }

    public function incr($conditions, $field, $optval = 1)
    {
        $conditions = $this->_where($conditions);
        return $this->execute("UPDATE " . $this->table . " SET `{$field}` = `{$field}` + :M_INCR_VAL " . $conditions["_where"], $conditions["_bindParams"] + array(":M_INCR_VAL" => $optval));
    }

    public function decr($conditions, $field, $optval = 1)
    {
        return $this->incr($conditions, $field, -$optval);
    }

    public function delete($conditions)
    {
        $conditions = $this->_where($conditions);
        return $this->execute("DELETE FROM " . $this->table . $conditions["_where"], $conditions["_bindParams"]);
    }

    public function create($row)
    {
        $values = array();
        foreach ($row as $k => $v) {
            $keys[] = "`{$k}`";
            $values[":" . $k] = $v;
            $marks[] = ":" . $k;
        }
        $this->execute("INSERT INTO " . $this->table . " (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $marks) . ")", $values);
        return $this->dbInstance(App::$configs[$this->link], 'master')->lastInsertId();
    }

    /**
     * 批量插入数据
     *
     * @param array $rows 数据数组
     * @param array $columns 字段s 注意与 $rows 保持一致，包括顺序!!!
     * @return bool
     */
    public function batchInsert(array $rows, array $columns = []): bool
    {
        $columns = $columns ?: array_keys(current($rows));
        $columnNames = implode(',', $columns);
        $placeholders = [];
        $values = [];

        foreach ($rows as $row) {
            $placeholders[] = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';
            $values = array_merge($values, array_values($row));// @todo 兼容不一致问题
        }

        $sql = "INSERT INTO `$this->table` ($columnNames) VALUES " . implode(',', $placeholders);
        _log($sql, $values);
        return $this->execute($sql, $values);
    }

    /**
     * 实现 upsert 操作
     *
     * @param array $insertColumns 插入的字段和值
     * @param array $updateColumns 更新的字段和值
     * @return int 影响的行数
     */
    public function upsert(array $insertColumns, array $updateColumns): int
    {
        // 构造插入字段和占位符
        $allColumns = $insertColumns + $updateColumns;
        $columns = array_keys($allColumns);
        $placeholders = array_fill(0, count($allColumns), '?');
        $insertSql = "INSERT INTO `$this->table` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $placeholders) . ")";

        // 构造更新字段
        $updatePairs = [];
        foreach ($updateColumns as $column => $value) {
            $updatePairs[] = "`$column` = ?";
        }
        $updateSql = "ON DUPLICATE KEY UPDATE " . implode(', ', $updatePairs);

        // 合并 SQL 语句
        $sql = $insertSql . ' ' . $updateSql;

        // 合并绑定值
        $values = array_merge(array_values($allColumns), array_values($updateColumns));

        //_log($sql, $values);

        // 执行 SQL
        _log($sql, $values);
        return $this->execute($sql, $values);
    }

    public function dumpSql()
    {
        return $this->sql;
    }

    public function pager($page, $pageSize = 10, $scope = 10, $total = 0)
    {
        $this->page = null;
        if ($total > $pageSize) {
            $total_page = ceil($total / $pageSize);
            $page = min(intval(max($page, 1)), $total_page);
            $this->page = array(
                'total_count' => $total,
                'page_size' => $pageSize,
                'total_page' => $total_page,
                'first_page' => 1,
                'prev_page' => ((1 == $page) ? 1 : ($page - 1)),
                'next_page' => (($page == $total_page) ? $total_page : ($page + 1)),
                'last_page' => $total_page,
                'current_page' => $page,
                'offset' => ($page - 1) * $pageSize,
                'limit' => $pageSize,
            );
            $scope = (int)$scope;
            if ($total_page <= $scope) {
                $this->page['all_pages'] = range(1, $total_page);
            } elseif ($page <= $scope / 2) {
                $this->page['all_pages'] = range(1, $scope);
            } elseif ($page <= $total_page - $scope / 2) {
                $right = $page + (int)($scope / 2);
                $this->page['all_pages'] = range($right - $scope + 1, $right);
            } else {
                $this->page['all_pages'] = range($total_page - $scope + 1, $total_page);
            }
        }
        return $this->page;
    }

    /**
     * @param array $conditions 支持 rawWhere+...params 以及 key=>value
     * @return array
     */
    private function _where($conditions)
    {
        $result = array("_where" => " ", "_bindParams" => array());
        if (is_array($conditions) && !empty($conditions)) {
            $sql = null;
            $join = array();
            if (isset($conditions[0]) && $sql = $conditions[0]) unset($conditions[0]);
            foreach ($conditions as $key => $condition) {
                if (substr($key, 0, 1) != ":") {
                    unset($conditions[$key]);
                    $conditions[":" . $key] = $condition;
                }
                $join[] = "`{$key}` = :{$key}";
            }
            if (!$sql) $sql = join(" AND ", $join);

            $result["_where"] = " WHERE " . $sql;
            $result["_bindParams"] = $conditions;
        }
        return $result;
    }
}