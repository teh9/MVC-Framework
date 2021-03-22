<?php

    namespace application\controllers;

    use application\core\Controller;
    use application\lib\D;

    class MainController extends Controller {

        public function indexAction(){

            $db = new D;

            /*$item = D::load('users');
            $item->name    = 'Ivan';
            $item->fname   = 'Kozlov';
            $item->age     = 22;
            D::store($item);*/
            /*
            $item = D::load('users');
            $item->name  = 'Tengiz';
            $item->fname = 'Gusev';
            $item->age   = 23;
            D::update($item, 4);

            $item = D::load('users');
            D::trash([5,8]);*/

            #pre(D::lastId());

            $this->model->getNews();

            $this->view->render('Glavaja');
        }

    }