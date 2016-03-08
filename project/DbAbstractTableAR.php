<?php
namespace Cut;

abstract class DbAbstractTableAR
{
    static protected $dbh;
    static protected $tableName;
    static protected $fields;
    protected $params = [];

    /**
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        try {
            if (array_key_exists($property, $this->params)) {
                return $this->params[$property];
            } else {
                throw new \Exception("Property $property not exist");
            }
        } catch (\Exception $e) {
            die(" Error " . $e->getMessage() . " Line " . $e->getLine() . " File " . $e->getFile());
        }
    }

    public function __set($property, $value)
    {
        $this->params[$property] = $value;
    }

    public function __unset($property)
    {
        unset($this->params[$property]);
    }

    public function __isset($property)
    {
        return isset($this->params[$property]);
    }

    /**
     * Return one object or array objects
     *
     * @param array $findWhere
     * @param array $fields
     * @return array|bool
     */
    public static function find(array $findWhere, array $fields)
    {
        self::$dbh = DbConnect::getConnection();
        $fields = join(', ', $fields);
        $table = static::$tableName;

        $columnName = array_keys($findWhere);
        $columnValue = array_values($findWhere);
        $countFields = count($findWhere);

        $where = self::getFieldsHolder($countFields, $columnName, 'AND');
        $query = 'SELECT ' . $fields . ' FROM ' . $table . ' WHERE ' . $where;
        $sth = self::bindParams($query, $countFields, $columnValue);

        self::execQuery($sth);

        if (!$res = $sth->fetchAll(\PDO::FETCH_ASSOC)) {
            return false;
        }

        $objects = [];
        foreach ($res as $val) {
            $class = static::class;
            $objects[] = new $class();
        }
        for ($i = 0; $i <= count($res) - 1; $i++) {
            foreach ($res[$i] as $k => $v) {
                $objects[$i]->$k = $v;
            }
        }
        if (count($objects) === 1) {
            return $objects[0];
        }
        return $objects;
    }

    /**
     * INSERT new row in database or UPDATE if row exist
     * @return mixed
     */
    public function save()
    {
        self::$dbh = DbConnect::getConnection();
        $table = static::$tableName;
        $fields = array_keys($this->params);
        $values = array_values($this->params);
        $fieldsHolder = '';
        $valueHolder = '';
        $countFields = count($this->params);

        //if row exist
        if (array_key_exists('id', $this->params)) {
            $fieldsHolder = self::getFieldsHolder($countFields, $fields, ',');

            $query = 'UPDATE ' . $table . ' SET ' . $fieldsHolder . ' WHERE id=' . $this->params['id'];
            $sth = self::bindParams($query, $countFields, $values);
        } else {
            for ($i = $countFields - 1; $i >= 0; $i--) {
                $fieldsHolder .= "$fields[$i], ";
                $valueHolder .= ':v' . $i . ', ';
            }

            $fieldsHolder = rtrim($fieldsHolder, ', ');
            $valueHolder = rtrim($valueHolder, ', ');

            $query = 'INSERT INTO ' . $table . ' (' . $fieldsHolder . ') VALUES (' . $valueHolder . ')';
            $sth = self::bindParams($query, $countFields, $values);
        }
        return $this->execQuery($sth);
    }

    /**
     * DELETE row in database if id exist
     * @return mixed
     */
    public function delete()
    {
        self::$dbh = DbConnect::getConnection();
        $table = static::$tableName;

        $query = "DELETE FROM $table WHERE id=" . $this->params['id'];
        $sth = self::$dbh->prepare($query);

        return $this->execQuery($sth);
    }

    /**
     * @param $query
     * @param $countFields
     * @param array $values
     * @return mixed
     */
    private static function bindParams($query, $countFields, array $values)
    {
        $sth = self::$dbh->prepare($query);
        for ($i = $countFields - 1; $i >= 0; $i--) {
            $sth->bindValue(':v' . $i, $values[$i]);
        }
        return $sth;
    }

    /**
     * @param $countFields
     * @param array $fields
     * @param $delimiter
     * @return string
     */
    private static function getFieldsHolder($countFields, array $fields, $delimiter)
    {
        $fieldsHolder = '';
        for ($i = $countFields - 1; $i >= 0; $i--) {
            $fieldsHolder .= "$fields[$i]=:v$i $delimiter ";
        }
        return rtrim($fieldsHolder, " $delimiter ");
    }


    /**
     * Executes a prepared statement
     * @param $sth
     * @throws \Exception
     */
    private static function execQuery($sth)
    {
        try {
            $result = $sth->execute();
            if (!$result) {
                throw new \Exception("Can't perform database operations" . $sth->errorInfo());
            }
            return $result;
        } catch (\Exception $e) {
            die(" Error " . $e->getMessage() . " Line " . $e->getLine() . " File " . $e->getFile());
        }
    }
}
