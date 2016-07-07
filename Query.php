<?php
/**
*   class php para evitar reescrita de query
*   foi feita para utilizar em um grid de exibição de itens
*   onde getQueryPaginacao() retorna a query utilizada para recuperar os itens a exibir de acordo com a painação
*   e getQueryTotal() retorna apenas a quantidade total de itens
*/

class Query {

    
    private $select;
    private $from;
    private $where;
    private $join;
    private $orderby;
    private $groupby;

    public function __construct($select = null, $from = null, $join = null, $where = null, $orderby = null, $groupby = null)
    {

        if (!is_null($select)) {
            $this->select = $select;
        }
        if (!is_null($from)) {
            $this->from = $from;
        }
        if (!is_null($where)) {
            $this->where = $where;
        }
        if (!is_null($join)) {
            $this->join = $join;
        }
        if (!is_null($orderby)) {
            $this->orderby = $orderby;
        }
        if (!is_null($groupby)) {
            $this->groupby = $groupby;
        }
    }

    public function getQuery()
    {
        return $this->getQueryCompleta(true, false, false, false, false);
    }

    public function getQueryPaginacao($start = 0, $limit = 100, $page = 1)
    {
        return $this->getQueryCompleta(true, false, false, false, true, $start, $limit, $page);
    }

    public function getQueryTotal()
    {
        return $this->getQueryCompleta(false, false, true);
    }

    public function getQueryCompleta($orderby = false, $groupby = false, $count = false, $distinct = false, $paginacao = false, $start = 0, $limit = 100, $page = 1)
    {
        $sql = "SELECT";

        if ($distinct) {
            $sql .= " DISTINCT ";
        }

        if ($count) {
            $sql .= " count(*) ";
        } else if ($paginacao) {
            $sql .=" *
                        FROM
                          ( SELECT " . $this->select . ",
                                   ROW_NUMBER() OVER ( ORDER BY funcs_nome) AS rnum ";
        } else {

            $sql .= $this->select . " ";
        }

        $sql .= "FROM " . $this->from . " ";


        $sql .= $this->join . " ";

        if (!is_null($this->where)) {
            $sql .= "WHERE " . $this->where . " ";
        }

        if ($paginacao) {

            $sql .= ") as tmp WHERE rnum > " . $start . " AND rnum <= " . ($limit * $page) . " ";

            //Precisa ser adaptado para aceitar multiplos criterios
            $orderby_sql = trim($this->orderby);
            $orderby_sql = explode('.', $orderby_sql);
            if (isset($orderby_sql[1]))
                $sql .= "ORDER BY tmp." . $orderby_sql[1] . " ";
        }else if ((!is_null($this->orderby)) && $orderby) {
            $sql .= "ORDER BY " . $this->orderby . " ";
        }
        if ((!is_null($this->groupby)) && $groupby) {
            $sql .= "GROUP BY " . $this->groupby . " ";
        }



        return $sql;
    }

    public function setSelect($fragmentoSql)
    {
        $this->select = $fragmentoSql;
    }

    public function setFrom($fragmentoSql)
    {
        $this->from = $fragmentoSql;
    }

    public function setWhere($fragmentoSql)
    {
        $this->where = $fragmentoSql;
    }

    public function setJoin($fragmentoSql)
    {
        $this->join = $fragmentoSql;
    }

    public function setOrderby($fragmentoSql)
    {
        $this->orderby = $fragmentoSql;
    }

    public function setGroupby($fragmentoSql)
    {
        $this->groupby = $fragmentoSql;
    }

}
