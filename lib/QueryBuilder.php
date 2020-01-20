<?php


namespace lib;

/**
 * Class QueryBuilder
 * @package lib
 * @method QueryBuilder andWhere(string $fieldName, string $operator, $value)
 * @method QueryBuilder orWhere(string $fieldName, string $operator, $value)
 * @method QueryBuilder innerJoin(string $tableName, string $condition)
 * @method QueryBuilder leftJoin(string $tableName, string $condition)
 * @method QueryBuilder rightJoin(string $tableName, string $condition)
 */
class QueryBuilder
{
    const OPERATOR_AND = 'and';
    const OPERATOR_OR = 'or';

    public static function select(string $from, $fields = '*'): self
    {

    }

    public static function insert(string $tableName, array $values): self
    {

    }

    public static function update(string $tableName, array $values): self
    {

    }

    public static function delete(string $tableName): self
    {

    }

    public function where(string $fieldName, string $operator, $value): self
    {

    }

    public function join(string $tableName, string $on, string $type = 'left'): self
    {

    }

    public function openBracket(string $operator): self
    {

    }

    public function closeBracket(): self
    {

    }

    public function getRawSql(): string
    {

    }
}