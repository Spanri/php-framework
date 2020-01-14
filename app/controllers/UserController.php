<?php


namespace app\controllers;


use lib\Controller;

class UserController extends Controller
{
    public function get(string $group, string $username)
    {
        return "User, group - {$group}, username - {$username}";
    }

    public function __toString()
    {
        return "Index! ";
    }
}