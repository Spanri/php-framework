<?php


namespace app\controllers;


use lib\Controller;

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
}