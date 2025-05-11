<?php

class QueryBuilder extends Model
{
    protected $select = '*';
    protected $where = '';
    protected $groupBy = '';
    protected $orderBy = '';
    protected $limit = null;
    protected $offset = null;
    protected $indexBy = null;
    protected $bindParams = [];

    /**
     * 设置查询字段
     *
     * @param string|array $fields 查询字段
     * @return $this
     */
    public function select($fields)
    {
        if (is_array($fields)) {
            $fields = implode(', ', $fields);
        }
        $this->select = $fields;
        return $this;
    }

    /**
     * 设置查询条件
     *
     * @param string|array $conditions 查询条件
     * @param array $params 绑定参数
     * @return $this
     */
    public function where($conditions, $params = [])
    {
        if (is_array($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "`{$key}` = :{$key}";
                $this->bindParams[":{$key}"] = $value;
            }
            $this->where = ' WHERE ' . implode(' AND ', $where);
        } else {
            $this->where = ' WHERE ' . $conditions;
            $this->bindParams = array_merge($this->bindParams, $params);
        }
        return $this;
    }

    /**
     * 设置分组字段
     *
     * @param string $groupBy 分组字段
     * @return $this
     */
    public function groupBy($groupBy)
    {
        $this->groupBy = ' GROUP BY ' . $groupBy;
        return $this;
    }

    /**
     * 设置排序字段
     *
     * @param string $orderBy 排序字段
     * @return $this
     */
    public function orderBy($orderBy)
    {
        $this->orderBy = ' ORDER BY ' . $orderBy;
        return $this;
    }

    /**
     * 设置分页限制
     *
     * @param int $limit 限制条数
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * 设置偏移量
     *
     * @param int $offset 偏移量
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * 设置索引字段
     *
     * @param string $indexBy 索引字段
     * @return $this
     */
    public function indexBy($indexBy)
    {
        $this->indexBy = $indexBy;
        return $this;
    }

    /**
     * 执行查询并返回所有结果
     *
     * @return array
     */
    public function all()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}{$this->where}{$this->groupBy}{$this->orderBy}";
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        $result = $this->query($sql, $this->bindParams);
        if ($this->indexBy !== null) {
            $indexedResult = [];
            foreach ($result as $row) {
                $indexedResult[$row[$this->indexBy]] = $row;
            }
            return $indexedResult;
        }
        return $result;
    }

    /**
     * 执行查询并返回单条结果
     *
     * @return array|false
     */
    public function one()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}{$this->where}{$this->groupBy}{$this->orderBy}";
        if ($this->limit !== null) {
            $sql .= " LIMIT 1";
        }
        $result = $this->query($sql, $this->bindParams);
        return !empty($result) ? array_shift($result) : false;
    }

    /**
     * 执行查询并返回标量值
     *
     * @return mixed
     */
    public function scalar()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}{$this->where}{$this->groupBy}{$this->orderBy}";
        if ($this->limit !== null) {
            $sql .= " LIMIT 1";
        }
        $result = $this->query($sql, $this->bindParams);
        return !empty($result) ? array_shift($result)[array_key_first($result[0])] : null;
    }
}