<?php


namespace app\controllers;


use lib\Controller;

class NewsController extends Controller
{
    public function list()
    {
        return 'list of news';
    }

    public function detail(int $id)
    {
        return "news item #{$id}";
    }
}