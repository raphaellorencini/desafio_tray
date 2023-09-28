@extends('layout')

@section('content')
    <div class="container mt-4">
        <h1>Listagem de Usuários</h1>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody id="userList"></tbody>
        </table>
        <nav aria-label="Navegação de página">
            <ul class="pagination" id="pagination"></ul>
        </nav>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                    <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza de que deseja excluir este usuário?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuário</h5>
                    <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <div class="form-group">
                            <label for="editUserName">Nome</label>
                            <input type="text" class="form-control" id="editUserName" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="editUserEmail">Email</label>
                            <input type="email" class="form-control" id="editUserEmail" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="editUserPassword">Senha</label>
                            <input type="password" class="form-control" id="editUserPassword" name="password">
                        </div>
                        <div class="form-group">
                            <label for="editUserPasswordConfirm">Confirmar Senha</label>
                            <input type="password" class="form-control" id="editUserPasswordConfirm" name="password_confirmation">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveUserChanges">Salvar Alterações</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                const userList = $('#userList');
                const pagination = $('#pagination');
                let userIdToDelete = null;
                let userIdToEdit = null;

                function loadUsers(page = 1) {
                    $.ajax({
                        type: 'GET',
                        url: `/api/v1/users?page=${page}`,
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer {{$token}}',
                        },
                        success: function(response) {
                            userList.empty();

                            response.data.forEach(function(user) {
                                userList.append(`
                                    <tr>
                                        <td>${user.name}</td>
                                        <td>${user.email}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm edit-user" data-id="${user.id}" data-toggle="modal" data-target="#editUserModal">Editar</button>
                                            <button class="btn btn-danger btn-sm delete-user" data-id="${user.id}" data-toggle="modal" data-target="#confirmDeleteModal">Excluir</button>
                                        </td>
                                    </tr>
                                `);
                            });


                            pagination.empty();
                            for (let i = 1; i <= response.last_page; i++) {
                                pagination.append(`
                                    <li class="page-item${i === page ? ' active' : ''}">
                                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                                    </li>
                                `);
                            }
                        },
                        error: function(error) {
                            console.error(error);
                        }
                    });
                }

                loadUsers();

                pagination.on('click', 'a.page-link', function(e) {
                    e.preventDefault();
                    const page = $(this).data('page');
                    loadUsers(page);
                });

                userList.on('click', '.delete-user', function() {
                    userIdToDelete = $(this).data('id');
                    $('#confirmDeleteModal').modal('show');
                });

                $('#confirmDelete').click(function() {
                    if (userIdToDelete) {
                        $.ajax({
                            type: 'DELETE',
                            url: `/api/v1/users/${userIdToDelete}`,
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': 'Bearer {{$token}}',
                            },
                            success: function() {
                                loadUsers();
                                $('#confirmDeleteModal').modal('hide');
                            },
                            error: function(error) {
                                console.error(error);
                                $('#confirmDeleteModal').modal('hide');
                            }
                        });
                    }
                });

                // Edição do usuário
                userList.on('click', '.edit-user', function() {
                    userIdToEdit = $(this).data('id');
                    // Limpar campos do formulário de edição
                    $('#editUserName').val('');
                    $('#editUserEmail').val('');
                    $('#editUserPassword').val('');
                    $('#editUserPasswordConfirm').val('');
                    // Carregar dados do usuário para edição
                    $.ajax({
                        type: 'GET',
                        url: `/api/v1/users/${userIdToEdit}`,
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer {{$token}}',
                        },
                        success: function(user) {
                            $('#editUserName').val(user.name);
                            $('#editUserEmail').val(user.email);
                        },
                        error: function(error) {
                            // Lidar com erros
                            console.error(error);
                        }
                    });
                    $('#editUserModal').modal('show');
                });

                $('#saveUserChanges').click(function() {
                    const name = $('#editUserName').val();
                    const email = $('#editUserEmail').val();
                    const password = $('#editUserPassword').val();
                    const passwordConfirm = $('#editUserPasswordConfirm').val();

                    if (!name || !email) {
                        alert('Nome e Email são campos obrigatórios.');
                        return;
                    }

                    if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))) {
                        alert('Email inválido.');
                        return;
                    }

                    if (password.length < 6) {
                        alert('As senhas tem que ter no mínimo 6 caracteres.');
                        return;
                    }

                    if (password && password !== passwordConfirm) {
                        alert('As senhas não coincidem.');
                        return;
                    }

                    const userData = {
                        name: name,
                        email: email,
                    };

                    if (password) {
                        userData.password = password;
                    }

                    $.ajax({
                        type: 'PUT',
                        url: `/api/v1/users/${userIdToEdit}`,
                        data: JSON.stringify(userData),
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer {{$token}}',
                        },
                        success: function() {
                            loadUsers();
                            $('#editUserModal').modal('hide');
                        },
                        error: function(error) {
                            // Lidar com erros
                            console.error(error);
                            //$('#editUserModal').modal('hide');
                        }
                    });
                });

                $('button.modal-close').click(function () {
                    $('#editUserModal').modal('hide');
                    $('#confirmDeleteModal').modal('hide');
                });
            });
        </script>
    @endpush
@endsection
