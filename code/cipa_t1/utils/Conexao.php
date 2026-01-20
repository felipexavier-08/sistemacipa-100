<?php

    abstract class Conexao {

        private static ?PDO $pdo = null;
        public static function pegarConexao() {
            try {
                $dsn = "mysql:host=127.0.0.1;dbname=cipa_t1;port=3307";
                $user = "root";
                $password = ""; 
                self::$pdo = new PDO($dsn, $user, $password);
                return self::$pdo;  
            } catch(PDOException $e) {
                echo("ERRO em conexao: " . $e->getMessage());
                return null;
            }
        }

    }

?>