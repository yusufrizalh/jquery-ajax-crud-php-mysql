<?php
require_once 'db_config.php';

class User extends DB_Config
{

    public $db;
    public function __construct()
    {
        // Koneksi ke database
        $this->db = new mysqli($this->db_host, $this->db_username, $this->db_password, $this->db_name);
        if ($this->db->connect_errno) {
            printf("Koneksi gagal: %s\n", $this->db->connect_error);
            exit();
        }
    }

    /*
     * Melakukan permintaan data yang ada pada database sesuai parameternya
     * @param table adalah nama tabel
     * @param condition, merupakan sebuah aturan permintaan data misalnya limit, order_by atau where.
     */
    public function getRows($table, $conditions = array())
    {
        $sql = 'SELECT ';
        $sql .= array_key_exists("select", $conditions) ? $conditions['select'] : '*';
        $sql .= ' FROM ' . $table;

        if (array_key_exists("where", $conditions)) {
            $sql .= ' WHERE ';
            $i = 0;
            foreach ($conditions['where'] as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $sql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }

        if (array_key_exists("order_by", $conditions)) {
            $sql .= ' ORDER BY ' . $conditions['order_by'];
        } else {
            $sql .= ' ORDER BY id DESC ';
        }

        if (array_key_exists("start", $conditions) && array_key_exists("limit", $conditions)) {
            $sql .= ' LIMIT ' . $conditions['start'] . ',' . $conditions['limit'];
        } elseif (!array_key_exists("start", $conditions) && array_key_exists("limit", $conditions)) {
            $sql .= ' LIMIT ' . $conditions['limit'];
        }

        $result = $this->db->query($sql);

        if (array_key_exists("return_type", $conditions) && $conditions['return_type'] != 'all') {
            switch ($conditions['return_type']) {
                case 'count':
                    $data = $result->num_rows;
                    break;
                case 'single':
                    $data = $result->fetch_assoc();
                    break;
                default:
                    $data = '';
            }
        } else {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
        }
        return !empty($data) ? $data : false;
    }

    /*
     * Memasukkan data ke dalam database
     * @param table, adalah nama tabel
     * @param data, adalah data yang akan dimasukkan ke database
     */
    public function insert($table, $data)
    {
        if (!empty($data) && is_array($data)) {
            $columns = '';
            $values  = '';
            $i = 0;
            if (!array_key_exists('created', $data)) {
                $data['created'] = date("Y-m-d H:i:s");
            }
            if (!array_key_exists('modified', $data)) {
                $data['modified'] = date("Y-m-d H:i:s");
            }
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $columns .= $pre . $key;
                $values  .= $pre . "'" . $this->db->real_escape_string($val) . "'";
                $i++;
            }
            $query = "INSERT INTO " . $table . " (" . $columns . ") VALUES (" . $values . ")";
            $insert = $this->db->query($query);
            return $insert ? $this->db->insert_id : false;
        } else {
            return false;
        }
    }

    /*
     * Update / memperbaharui data di database
     * @param table, adalah nama tabel
     * @param data, adalah data yang ingin diperbaharui
     * @param condition, merupakan sebuah aturan permintaan data misalnya limit, order_by atau where.
     */
    public function update($table, $data, $conditions)
    {
        if (!empty($data) && is_array($data)) {
            $colvalSet = '';
            $whereSql = '';
            $i = 0;
            if (!array_key_exists('modified', $data)) {
                $data['modified'] = date("Y-m-d H:i:s");
            }
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ? ', ' : '';
                $colvalSet .= $pre . $key . "='" . $this->db->real_escape_string($val) . "'";
                $i++;
            }
            if (!empty($conditions) && is_array($conditions)) {
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach ($conditions as $key => $value) {
                    $pre = ($i > 0) ? ' AND ' : '';
                    $whereSql .= $pre . $key . " = '" . $value . "'";
                    $i++;
                }
            }
            $query = "UPDATE " . $table . " SET " . $colvalSet . $whereSql;
            $update = $this->db->query($query);
            return $update ? $this->db->affected_rows : false;
        } else {
            return false;
        }
    }

    /*
     * Menghapus data yang ada pada database
     * @param table, adalah nama tabel
     * @param condition, merupakan sebuah aturan permintaan data misalnya limit, order_by atau where.
     */
    public function delete($table, $conditions)
    {
        $whereSql = '';
        if (!empty($conditions) && is_array($conditions)) {
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach ($conditions as $key => $value) {
                $pre = ($i > 0) ? ' AND ' : '';
                $whereSql .= $pre . $key . " = '" . $value . "'";
                $i++;
            }
        }
        $query = "DELETE FROM " . $table . $whereSql;
        $delete = $this->db->query($query);
        return $delete ? true : false;
    }
}
