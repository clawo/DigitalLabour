<?php
    # This file is used to connect to the database and return the connection object.
    # > require_once('db_connect.php'); $db = getDB(); to get the connection object.

    define('DEBUG_MODE', true);
    $db = null;

    function getDB() {
        global $db;

        if ($db === null) {
            $servername = '212.227.103.77';
            $username = 'examwise_all';
            $password = 'vu467K9&m'; # TODO: outsource if necessary
            $dbname = 'examwise';
            $charset = 'utf8mb4';

            try {
                $db = new PDO("mysql:host=$servername;dbname=$dbname;charset=$charset", $username, $password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                if (DEBUG_MODE) {
                    echo '<script>console.log("Connected successfully to the database");</script>';
                }
            } catch (PDOException $e) {
                if (DEBUG_MODE) {
                    echo '<script>console.log("Connection failed: ' . $e->getMessage() . '");</script>';
                }
            }
        }

        return $db;
    }

    function closeDB() {
        global $db;

        if ($db !== null) {
            $db = null;
        }
    }
?>