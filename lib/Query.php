<?php

class Query extends Model
{
    protected $select = '*';
    protected $from = '';
    protected $alias = '';
    protected $where = '';
    protected $groupBy = '';
    protected $orderBy = '';
    protected $limit = null;
    protected $offset = null;
    protected $indexBy = null;
    protected $bindParams = [];
    protected $joins = [];

    public function select($fields)
    {
        if (is_array($fields)) {
            $fields = implode(', ', $fields);
        }
        $this->select = $fields;
        return $this;
    }

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

    public function groupBy($groupBy)
    {
        $this->groupBy = ' GROUP BY ' . $groupBy;
        return $this;
    }

    public function orderBy($orderBy)
    {
        $this->orderBy = ' ORDER BY ' . $orderBy;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function indexBy($indexBy)
    {
        $this->indexBy = $indexBy;
        return $this;
    }

    public function alias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    public function join($table, $condition, $type = 'INNER')
    {
        $this->joins[] = "{$type} JOIN {$table} ON {$condition}";
        return $this;
    }

    public function all()
    {
        $sql = "SELECT {$this->select} FROM {$this->table} {$this->alias}";
        foreach ($this->joins as $join) {
            $sql .= " {$join}";
        }
        $sql .= "{$this->where}{$this->groupBy}{$this->orderBy}";
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

    public function one()
    {
        $sql = "SELECT {$this->select} FROM {$this->table} {$this->alias}";
        foreach ($this->joins as $join) {
            $sql .= " {$join}";
        }
        $sql .= "{$this->where}{$this->groupBy}{$this->orderBy} LIMIT 1";
        $result = $this->query($sql, $this->bindParams);
        return !empty($result) ? array_shift($result) : false;
    }

    public function scalar()
    {
        $result = $this->one();
        return !empty($result) ? current($result) : null;
    }
}