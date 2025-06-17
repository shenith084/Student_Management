<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.dataTables.css" />


    <title>Home page</title>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Navbar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>

                </ul>

            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="card mt-5 shadow">
                    <div class="card-header d-flex justify-content-between ">

                        <h5>Student Management</h5>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-light" data-bs-toggle="modal"
                            data-bs-target="#addNewStudent">
                            <i class="bi bi-plus-circle">Add New Student</i>
                        </button>

                    </div>
                    <div class="card-body">
                        <table id="myTable" class="display">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Avatar</th>
                                    <th>Name</th>
                                    <th>E-mail</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td>ID</td>
                                <td>Avatar</td>
                                <td>Name</td>
                                <td>E-mail</td>
                                <td>Action</td>
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- Modal -->
                <div class="modal fade" id="addNewStudent" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add New Student</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="#" method="POST" id="add_student_form" enctype="multipart/form-data">
                                <div class="modal-body">

                                    @csrf
                                    <div class="row">
                                        <div class="col-lg">
                                            <label for="fname">First Name</label>
                                            <input type="text" name="fname" class="form-control" required>
                                        </div>
                                        <div class="col-lg">
                                            <label for="lname">Last Name</label>
                                            <input type="text" name="lname" class="form-control" required>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg">
                                            <label for="email">Email</label>
                                            <input type="email" name="email" class="form-control" required>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg">
                                            <label for="avatar">Avatar</label>
                                            <input type="file" name="avatar" class="form-control" required>
                                        </div>

                                    </div>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" id="add_student_button" class="btn btn-primary">Add Student</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>




    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
       $(document).ready(function() {
    // Initialize DataTable with AJAX
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '/students',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            {
                data: 'avatar',
                render: function(data) {
                    return `<img src="/storage/images/${data}" width="50" class="img-thumbnail">`;
                }
            },
            {
                data: null,
                render: function(data) {
                    return `${data.first_name} ${data.last_name}`;
                }
            },
            { data: 'email' },
            {
                data: null,
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-primary edit-btn" data-id="${data.id}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${data.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    // Add new student
    $('#add_student_form').submit(function(e) {
        e.preventDefault();
        const fd = new FormData(this);

        $('#add_student_button').text('Adding...').prop('disabled', true);

        $.ajax({
            url: '{{ route("store") }}',
            method: 'POST',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if(response.status === 200) {
                    $('#add_student_form')[0].reset();
                    $('#addNewStudent').modal('hide');
                    table.ajax.reload();
                    showAlert('success', 'Student added successfully!');
                }
            },
            error: function(xhr) {
                handleErrors(xhr);
            },
            complete: function() {
                $('#add_student_button').text('Add Student').prop('disabled', false);
            }
        });
    });

    // Edit student (open modal with data)
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');

        $.get(`/students/${id}/edit`, function(response) {
            // Create or show edit modal
            // Similar to your add modal but populated with response data
            // You'll need to create an edit modal in your HTML
            $('#editStudentModal').modal('show');
            $('#edit_student_form input[name="fname"]').val(response.first_name);
            $('#edit_student_form input[name="lname"]').val(response.last_name);
            $('#edit_student_form input[name="email"]').val(response.email);
            $('#edit_student_form input[name="id"]').val(response.id);
        });
    });

    // Delete student
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');

        if(confirm('Are you sure you want to delete this student?')) {
            $.ajax({
                url: `/students/${id}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.status === 200) {
                        table.ajax.reload();
                        showAlert('success', 'Student deleted successfully!');
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'Error deleting student');
                }
            });
        }
    });

    // Helper function to show alerts
    function showAlert(type, message) {
        const alert = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;

        $('.container').prepend(alert);

        setTimeout(() => {
            $('.alert').alert('close');
        }, 3000);
    }

    // Helper function to handle validation errors
    function handleErrors(xhr) {
        if(xhr.status === 422) {
            let errors = xhr.responseJSON.errors;
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $.each(errors, function(key, value) {
                $(`[name="${key}"]`).addClass('is-invalid')
                    .after(`<div class="invalid-feedback">${value[0]}</div>`);
            });
        } else {
            showAlert('error', 'An error occurred. Please try again.');
        }
    }
});
    </script>

</body>

</html>
