@extends('layout_login')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>
                <div class="card-body">
                    <form id="loginForm">
                        <div class="form-group">
                            <label for="email">{{ __('E-Mail') }}</label>
                            <input id="email" type="email" class="form-control" name="email" required autofocus value="admin@admin.com.br">
                            <span class="invalid-feedback" id="email-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="password">{{ __('Senha') }}</label>
                            <input id="password" type="password" class="form-control" name="password" required value="123456">
                            <span class="invalid-feedback" id="password-error"></span>
                        </div>
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-block">
                                {{ __('Entrar') }}
                            </button>
                        </div>
                    </form>
                    <form id="redirectForm" action="{{route('redirect')}}" method="post">
                        <input id="token" type="hidden" name="token">
                        <input id="id" type="hidden" name="id">
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(function() {
                const loginForm = $('#loginForm');
                const emailError = $('#email-error');
                const passwordError = $('#password-error');

                loginForm.on('submit', function(e) {
                    e.preventDefault();
                    emailError.text('');
                    passwordError.text('');

                    $.ajax({
                        type: 'POST',
                        url: '/api/v1/login',
                        data: {
                            email: $('#email').val(),
                            password: $('#password').val(),
                        },
                        success: function(response) {
                            if(response['role'] !== 'admin') {
                                location.href = '/';
                                return;
                            }
                            if(response['token']) {
                                $('#token').val(response['token']);
                                $('#id').val(response['id']);
                                $('#redirectForm').submit();
                            }
                        },
                        error: function(error) {
                            if (error.status === 422) {
                                const errors = error.responseJSON.errors;
                                if (errors.email) {
                                    emailError.text(errors.email[0]);
                                }
                                if (errors.password) {
                                    passwordError.text(errors.password[0]);
                                }
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
