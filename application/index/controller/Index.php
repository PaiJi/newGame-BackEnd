<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        return '<section><h1>newGame,故事的起点</h1><p>您访问的是newGame社团管理系统的后端，感谢使用。</p></section> <style>section{position:absolute;bottom:50px;right:70px;color:#dedede;text-align:right;}body{background-color:#171717;}</style>';
    }

    public function hello($name = 'ThinkPHP5')
    {
        return 'hello,' . $name;
    }
}
