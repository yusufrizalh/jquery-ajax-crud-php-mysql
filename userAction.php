<?php
// Include dan initialisasi class DB
require_once 'inc/user.php';
$db = new User();

// Nama tabel dalam database
$tblName = 'users';

// Jika form di submit / menekan tombol
if (!empty($_POST['action_type'])) {
    if ($_POST['action_type'] == 'data') {
        $conditions['where'] = array('id' => $_POST['id']);
        $conditions['return_type'] = 'single';
        $user = $db->getRows($tblName, $conditions);
        
        // Mengembalikan data ke format JSON
        echo json_encode($user);
    } elseif($_POST['action_type'] == 'view') {
        
        $users = $db->getRows($tblName);
        
        // Output - Format HTML
        if (!empty($users)) {
            foreach($users as $row) {
                echo '<tr>';
                echo '<td>'.$row['id'] . '</td>';
                echo '<td>'.$row['name'] . '</td>';
                echo '<td>'.$row['email'] . '</td>';
                echo '<td>'.$row['phone'] . '</td>';
                echo '<td><a href="javascript:void(0);" class="btn btn-warning" rowID="' . $row['id'] . '" data-type="edit" data-toggle="modal" data-target="#modalUserAddEdit">edit</a>
                <a href="javascript:void(0);" class="btn btn-danger" onclick="return confirm(\'Are you sure to delete data?\')?userAction(\'delete\', \'' . $row['id'] . '\'):false;">delete</a></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">No user(s) found...</td></tr>';
        }
    } elseif ($_POST['action_type'] == 'add') {
        $msg = '';
        $status = $verr = 0;
        
        // Inputan pengguna
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        
        // Validasi input
        if (empty($name)) {
            $verr = 1;
            $msg .= 'Tuliskan nama lengkap anda.<br/>';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $verr = 1;
            $msg .= 'Tuliskan alamat email valid anda.<br/>';
        }
        if (empty($phone)) {
            $verr = 1;
            $msg .= 'Tuliskan nomor telepon anda.<br/>';
        }
        
        if ($verr == 0) {
            // Memasukkan data ke database
            $userData = array(
                'name'  => $name,
                'email' => $email,
                'phone' => $phone
            );
            $insert = $db->insert($tblName, $userData);
            
            if ($insert) {
                $status = 1;
                $msg .= 'User data berhasil ditambahkan.';
            } else {
                $msg .= 'Terjadi kesalahan, periksa kembali.';
            }
        }
        
        // Respon berupa JSON
        $alertType = ($status == 1)?'alert-success':'alert-danger';
        $statusMsg = '<p class="alert '.$alertType.'">'.$msg.'</p>';
        $response = array(
            'status' => $status,
            'msg' => $statusMsg
        );
        echo json_encode($response);
    } elseif ($_POST['action_type'] == 'edit') {
        $msg = '';
        $status = $verr = 0;
        
        if (!empty($_POST['id'])) {
            // Inputan pengguna
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            
            // Validasi input
            if (empty($name)) {
                $verr = 1;
                $msg .= 'Tuliskan nama lengkap anda.<br/>';
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $verr = 1;
                $msg .= 'Tuliskan alamat email valid anda.<br/>';
            }
            if (empty($phone)) {
                $verr = 1;
                $msg .= 'Tuliskan nomor telepon anda.<br/>';
            }
            
            if ($verr == 0) {
                // Update data pada database
                $userData = array(
                    'name'  => $name,
                    'email' => $email,
                    'phone' => $phone
                );
                $condition = array('id' => $_POST['id']);
                $update = $db->update($tblName, $userData, $condition);
                
                if ($update) {
                    $status = 1;
                    $msg .= 'User data berhasil diubah.';
                } else {
                    $msg .= 'Terjadi kesalahan, periksa kembali.';
                }
            }
        } else {
            $msg .= 'Terjadi kesalahan, periksa kembali.';
        }
        
        // Respon berupa JSON
        $alertType = ($status == 1) ? 'alert-success':'alert-danger';
        $statusMsg = '<p class="alert ' . $alertType . '">' . $msg . '</p>';
        $response = array(
            'status' => $status,
            'msg' => $statusMsg
        );
        echo json_encode($response);

    } elseif ($_POST['action_type'] == 'delete') {
        $msg = '';
        $status = 0;
        
        if (!empty($_POST['id'])) {
            // Menghapus data yang ada pada database
            $condition = array('id' => $_POST['id']);
            $delete = $db->delete($tblName, $condition);
            
            if ($delete) {
                $status = 1;
                $msg .= 'User data berhasil dihapus.';
            } else {
                $msg .= 'Terjadi kesalahan, periksa kembali.';
            }
        } else {
            $msg .= 'Terjadi kesalahan, periksa kembali.';
        }  

        $alertType = ($status == 1) ? 'alert-success' : 'alert-danger';
        $statusMsg = '<p class="alert ' . $alertType . '">' . $msg . '</p>';
        $response = array(
            'status' => $status,
            'msg' => $statusMsg
        );
        echo json_encode($response);
    }
}

exit;
?>