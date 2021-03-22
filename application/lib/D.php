<?php

    namespace application\lib;

    use PDO;
    use PDOException;

    class D{
        /**
         * Database settings;
         */
        private const HOST = 'localhost';
        private const DB   = 'mvc';
        private const USER = 'root';
        private const PASS = '';

        /**
         * For workings with queries;
         */
        protected static string $table;           # Choosing via this var table;
        private static object   $db;              # For creating PDO object;
        private static string   $type = 'insert'; # For choosing type of method what we want to do;

        public function __construct(){
            self::$db = new PDO('mysql:host='.self::HOST.';dbname='.self::DB.'',
                self::USER, self::PASS);
        }

        /**
         * Select table from database and return the object for filling;
         *
         * @param string  $tbl
         *
         * @return object
         */
        public static function load($tbl){
            self::$table = $tbl;
            return self::$db;
        }

        /**
         * Main function in this class, get the object and send it to necessary method (working like a Router);
         *
         * @param object|null $object
         * @param int|null    $id
         *
         * @return bool
         */
        public static function store($object = NULL, $id = NULL): bool{
            if(!empty($object)){
                $method = self::$type.'Rows';
                if(method_exists(__CLASS__, $method)){
                    echo 'ok';
                    self::$method($object, $id);
                }
                return false;
            }
            return true;
        }

        /**
         * Method to show "store" method, what need to do;
         * Changing type and get id what need to be updated;
         *
         * @param object $obj
         * @param int    $id
         */
        public static function update($obj, $id){
            self::$type = 'update';
            self::store($obj, $id);
        }

        public static function trash($ids = []){
            self::$type = 'delete';
            self::store($ids);
        }

        /**
         * Preparing and executing SQL string and BIND's array for inserting data;
         *
         * @param object   $obj
         * @param int|null $id // Not necessary
         */
        private static function insertRows($obj, $id = NULL){
            $array   = (array)$obj;
            $columns = implode(', ', array_keys($array));

            $questionMarks = trim(str_repeat('?, ', count($array)), ', ');

            $sql = 'INSERT INTO '.self::$table.' ('.$columns.') VALUES ('.$questionMarks.')';

            self::doQuery($sql, array_values($array));
            unset($obj);
        }

        /**
         * Preparing and executing SQL string and BIND's array for updating data;
         *
         * @param object   $obj
         * @param int|null $id
         */
        private static function updateRows($obj, $id){
            $array = (array)$obj;

            $columns = implode(' = ?, ', array_keys($array)).' = ?';

            array_push($array, $id);

            $sql = 'UPDATE '.self::$table.' SET '.$columns.' WHERE id = ?';

            self::doQuery($sql, array_values($array));
            unset($obj);
        }

        /**
         * Preparing and executing SQL string and BIND's array for deleting data;
         *
         * @param array $ids
         *
         */
        private static function deleteRows($ids = []){
            $list = trim(str_repeat('?, ', count($ids)), ', ');

            $sql = 'DELETE FROM '.self::$table.' WHERE id IN ('.$list.')';

            self::doQuery($sql, $ids);
        }

        /**
         * Making query to database using PDO;
         *
         * @param string $sql
         * @param array  $binds
         *
         * @return bool
         */
        private static function doQuery($sql, $binds = []){
            try{
                $stmt = self::$db->prepare($sql);
                $stmt->execute($binds);
                return true;
            }catch(PDOException $e){
                throw new PDOException($e->getMessage());
            }
        }

        /**
         * Getting last inserted ID;
         */
        public static function lastId(){
            return self::$db->lastInsertId();
        }

        /**
         * Main function in this class for searching rows, construct the query according to the required parameters;
         * If cond is NULL its find all rows;
         *
         * @param string      $tbl
         * @param string|null $cond
         * @param array       $bindings
         *
         * @return array
         */
        public static function find($tbl, $cond = NULL, $bindings = []){
            if($cond == NULL){
                $stmt = self::$db->query('SELECT * FROM '.$tbl);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            try {
                $stmt = self::$db->prepare('SELECT * FROM '.$tbl.' WHERE '.$cond);
                $stmt->execute($bindings);

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }catch (PDOException $e){
                throw new PDOException($e->getMessage());
            }
        }

        /**
         * Find one row by condition and LIMIT 1;
         *
         * @param string $tbl
         * @param string $cond
         * @param array  $binds
         *
         * @return array
         */
        public static function findOne($tbl, $cond, $binds = []){
            return self::find($tbl, $cond.' LIMIT 1', $binds);
        }

        /**
         * Find all rows in table, by default $cond is NULL, but whatever, if need some conditions u can write it;
         *
         * @param string      $tbl
         * @param string|null $cond
         * @param array       $binds
         *
         * @return array
         */
        public static function findAll($tbl, $cond = NULL, $binds = []){
            return self::find($tbl, $cond, $binds);
        }

        /**
         * Method for arbitrary SQL queries;
         *
         * @param string $sql
         * @param array  $bindings
         *
         * @return array
         */
        public static function free($sql, $bindings = []){
            try {
                $stmt = self::$db->prepare($sql);
                $stmt->execute($bindings);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }catch (PDOException $e){
                throw new PDOException($e->getMessage());
            }
        }
    }