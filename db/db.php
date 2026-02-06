<?php

class myDB
{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "school_attendance_db";
    public $res;
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    // INSERT DATA 
    public function insert($table, $data)
    {
        try {
            $table_columns = implode(',', array_keys($data));
            $prep = $types = '';

            foreach ($data as $value) {
                $prep .= '?,';
                $type = substr(gettype($value), 0, 1);
                if ($type === "N")
                    $type = "s";
                $types .= $type;
            }


            $prep = rtrim($prep, ',');
            $stmt = $this->conn->prepare("INSERT INTO $table ($table_columns) VALUES ($prep)");
            $stmt->bind_param($types, ...array_values($data));
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            die("Error while inserting data: " . $e->getMessage());
        }
    }

    // GET LAST INSERTED ID 
    public function getLastId()
    {
        return $this->conn->insert_id;
    }


    public function update($table, $data, $where)
    {
        try {
            $set = [];
            foreach ($data as $key => $value) {
                $set[] = "$key = ?";
            }

            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "$key = ?";
            }

            $sql = "UPDATE $table SET " . implode(", ", $set) . " WHERE " . implode(" AND ", $conditions);
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $types = str_repeat("s", count($data) + count($where));
            $stmt->bind_param($types, ...array_merge(array_values($data), array_values($where)));
            $stmt->execute();

            $affectedRows = $stmt->affected_rows; 

            $stmt->close();
            return $affectedRows; 
        } catch (Exception $e) {
            die("Error while updating data: " . $e->getMessage());
        }
    }


    // SELECT DATA
    public function select($table, $columns = "*", $where = null)
    {
        try {
            $sql = "SELECT $columns FROM $table";
            if ($where) {
                $conditions = [];
                foreach ($where as $key => $value) {
                    $conditions[] = "$key = ?";
                }
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            if ($where) {
                $types = str_repeat("s", count($where));
                $stmt->bind_param($types, ...array_values($where));
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }

            $stmt->close();
            return $rows;
        } catch (Exception $e) {
            die("Error while selecting data: " . $e->getMessage());
        }
    }

    // SELECT SINGLE ROW
    public function select_one($table, $columns = "*", $where = null)
    {
        try {
            $rows = $this->select($table, $columns, $where);
            return $rows ? $rows[0] : null; // return first row or null
        } catch (Exception $e) {
            die("Error while selecting single row: " . $e->getMessage());
        }
    }


    // DELETE DATA
    public function delete($table, $where)
    {
        try {
            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "$key = ?";
            }

            $sql = "DELETE FROM $table WHERE " . implode(" AND ", $conditions);
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $types = str_repeat("s", count($where));
            $stmt->bind_param($types, ...array_values($where));

            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            die("Error while deleting data: " . $e->getMessage());
        }
    }


    // DELETE DATA (new added function to para sa mga my joins)
    public function rawQuery($sql, $params = [], $types = "")
    {
        try {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            if ($params) {
                if (!$types) {
                    // Default: assume all strings
                    $types = str_repeat("s", count($params));
                }
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $rows = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
            }

            $stmt->close();
            return $rows;
        } catch (Exception $e) {
            die("Error while executing raw query: " . $e->getMessage());
        }
    }

}







