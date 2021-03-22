<?php

    namespace application\models;

    use application\lib\D;

    class Main{

        public function getNews(){
            echo 'model ok';
            pre(D::findAll('users'));
        }

    }