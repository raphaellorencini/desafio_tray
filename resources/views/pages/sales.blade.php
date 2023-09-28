@extends('layout')

@section('content')
    <div class="container mt-4">
        <h1>Listagem de Vendas</h1>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Produto</th>
                <th>Valor</th>
                <th>Vendedor</th>
                <th>Comissão</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody id="saleList"></tbody>
            <tfooter>
                <tr>
                    <th>Total</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th id="total_commission"></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfooter>
        </table>
        <nav aria-label="Navegação de página">
            <ul class="pagination" id="pagination"></ul>
        </nav>
    </div>

    <div class="modal fade" id="confirmCommissionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirmação de Comissão</h5>
                    <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Deseja reenviar esta comissão?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmSend">Enviar</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                const saleList = $('#saleList');
                const pagination = $('#pagination');
                let saleIdToCommission = null;

                function loadUsers(page = 1) {
                    $.ajax({
                        type: 'GET',
                        url: `/api/v1/sales?page=${page}`,
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer {{$token}}',
                        },
                        success: function(response) {
                            saleList.empty();

                            response.list.data.forEach(function(sale) {
                                saleList.append(`
                                    <tr>
                                        <td>${sale.name}</td>
                                        <td>${sale.value}</td>
                                        <td>${sale.user_name}</td>
                                        <td>${sale.commission}</td>
                                        <td>${sale.created_at}</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm commission-sale" data-id="${sale.id}" data-toggle="modal" data-target="#confirmCommissionModal">Comissão</button>
                                        </td>
                                    </tr>
                                `);
                            });
                            $('#total_commission').html(response.commission)
                            pagination.empty();
                            for (let i = 1; i <= response.list.last_page; i++) {
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

                saleList.on('click', '.commission-sale', function() {
                    saleIdToCommission = $(this).data('id');
                    $('#confirmCommissionModal').modal('show');
                });

                $('#confirmSend').click(function() {
                    if (saleIdToCommission) {
                        /*$.ajax({
                            type: 'GET',
                            url: `/api/v1/sales/comission/${saleIdToCommission}`,
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
                        });*/
                    }
                });

                $('button.modal-close').click(function () {
                    $('#confirmCommissionModal').modal('hide');
                });
            });
        </script>
    @endpush
@endsection
