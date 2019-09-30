<?php
// Include and initialize DB class
require_once 'inc/user.php';
$query = new User();

// Fetch the users data
$users = $query->getRows('users');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Realtime Datatable</title>

    <link rel="Shorcut icon" type="text/ico" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">
                <h3>Manage Table Users</h3>
                <hr class="ma-hr">
                <!-- Add link -->
                <div class="float-right">
                    <a href="javascript:void(0);" class="btn btn-success" data-type="add" data-toggle="modal" data-target="#modalUserAddEdit"><i class="plus"></i> User Baru</a>
                </div>
            </div>
            <div class="statusMsg"></div>
            <!-- List the users -->
            <table class="cn-data-tables">
                <thead class="">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody id="userData">
                    <?php if (!empty($users)) {
                        foreach ($users as $row) { ?>
                            <tr>
                                <td><?php echo '#' . $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['phone']; ?></td>
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning" rowID="<?php echo $row['id']; ?>" data-type="edit" data-toggle="modal" data-target="#modalUserAddEdit">ubah</a>
                                    <a href="javascript:void(0);" class="btn btn-danger" onclick="return confirm('Are you sure to delete data?')?userAction('delete', '<?php echo $row['id']; ?>'):false;">hapus</a>
                                </td>
                            </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="5">No user(s) found...</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <p class="text-center mt-4 mb-3">Belajar jQuery AJAX di <a href="http://www.inixindosurabaya.id">inixindosurabaya.id</a></p>
    </div>



    <!-- Modal Add and Edit Form -->
    <div class="modal fade" id="modalUserAddEdit" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Menambah User Baru</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="statusMsg"></div>
                    <form role="form">
                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Tuliskan nama lengkap anda">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Tuliskan email valid anda">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telepon</label>
                            <input type="text" class="form-control" name="phone" id="phone" placeholder="Tuliskan nomor telepon anda">
                        </div>
                        <input type="hidden" class="form-control" name="id" id="id" />
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="userSubmit">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.dataTables.js"></script>
    <script>
        function getUsers() {
            $.ajax({
                type: 'POST',
                url: 'userAction.php',
                data: 'action_type=view',
                success: function(html) {
                    $('#userData').html(html);
                }
            });
        }

        // Mengirim permintaan CRUD ke server
        function userAction(type, id) {
            id = (typeof id == "undefined") ? '' : id;
            var userData = '',
                frmElement = '';

            if (type == 'add') {
                frmElement = $("#modalUserAddEdit");
                userData = frmElement.find('form').serialize() + '&action_type=' + type + '&id=' + id;
            } else if (type == 'edit') {
                frmElement = $("#modalUserAddEdit");
                userData = frmElement.find('form').serialize() + '&action_type=' + type;
            } else {
                frmElement = $(".row");
                userData = 'action_type=' + type + '&id=' + id;
            }
            frmElement.find('.statusMsg').html('');
            $.ajax({
                type: 'POST',
                url: 'userAction.php',
                dataType: 'JSON',
                data: userData,
                beforeSend: function() {
                    frmElement.find('form').css("opacity", "0.5");
                },
                success: function(resp) {
                    frmElement.find('.statusMsg').html(resp.msg);
                    if (resp.status == 1) {
                        if (type == 'add') {
                            frmElement.find('form')[0].reset();
                        }
                        getUsers();
                    }
                    frmElement.find('form').css("opacity", "");
                }
            });
        }

        function editUser(id) {
            $.ajax({
                type: 'POST',
                url: 'userAction.php',
                dataType: 'JSON',
                data: 'action_type=data&id=' + id,
                success: function(data) {
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                }
            });
        }

        $(function() {
            $('#modalUserAddEdit').on('show.bs.modal', function(e) {
                var type = $(e.relatedTarget).attr('data-type');
                var userFunc = "userAction('add');";
                if (type == 'edit') {
                    userFunc = "userAction('edit');";
                    var rowId = $(e.relatedTarget).attr('rowID');
                    editUser(rowId);
                }
                $('#userSubmit').attr("onclick", userFunc);
            });

            $('#modalUserAddEdit').on('hidden.bs.modal', function() {
                $('#userSubmit').attr("onclick", "");
                $(this).find('form')[0].reset();
                $(this).find('.statusMsg').html('');
            });
        });

        // Data Tables
        $(".cn-data-tables").dataTable();
        $('.dataTables_paginate > a').wrapInner('<span />');
        $('.dataTables_paginate > a span').addClass('btn-paginate');

        var cnTable = $(".cn-data-tables");
    </script>

</body>

</html>