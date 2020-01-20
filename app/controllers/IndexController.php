<?php


namespace app\controllers;


use lib\Controller;
use lib\QueryBuilder;

class IndexController extends Controller
{
    public function index()
    {
        return 'i am an index!';
    }

    public function other()
    {
        return 'i am an other!';
    }

    public function testSelect()
    {
        $sql = QueryBuilder::select('my_table')
            ->where('price', '>', 5)
            ->andWhere('discount', '=', 0.2)
            ->getRawSql();
        return $sql;
    }

    public function testSelectJoin()
    {
        $sql = QueryBuilder::select('my_table', ['id', 'price'])
            ->where('price', '>', 5)
            ->andWhere('discount', '=', 0.2)
            ->leftJoin('my_table2', 'my_table.id = my_table2.mt_id')
            ->getRawSql();
        return $sql;
    }

    public function testSelectBraket()
    {
        $sql = QueryBuilder::select('my_table')
            ->where('price', '>', 0)
            ->openBracket(QueryBuilder::OPERATOR_OR)
            ->where('id', '>', 100)
            ->openBracket(QueryBuilder::OPERATOR_AND)
            ->where('discount', '=', 0.2)
            ->orWhere('discount', '=', 0.5)
            ->closeBracket()
            ->closeBracket()
            ->getRawSql();
        return $sql;
    }

    public function testInsert()
    {
        $sql = QueryBuilder::insert('my_table', [
            'name' => 'ololo',
            'price' => 100,
            'discount' => 0.1,
        ])->getRawSql();
        return $sql;
    }

    public function testUpdate()
    {
        $sql = QueryBuilder::update('my_table', [
            'name' => 'ololo',
            'price' => 100,
            'discount' => 0.1,
        ])
            ->where('id', '=', 1)
            ->getRawSql();
        return $sql;
    }

    public function testDelete()
    {
        $sql = QueryBuilder::delete('my_table')
            ->where('id', '=', 1)
            ->getRawSql();
        return $sql;
    }

    public function __toString()
    {
        return "Index! ";
    }
}