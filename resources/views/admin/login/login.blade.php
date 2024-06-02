<!DOCTYPE html>
<html lang="en" class="body-full-height">
<head>
    <!-- META SECTION -->
    <title>Qareeb - Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="{{asset('/admin/img/favicon.ico')}}" type="image/x-icon" />
    <!-- END META SECTION -->

    <!-- CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="{{asset('/admin/css/theme-default.css')}}"/>
    <!-- EOF CSS INCLUDE -->
</head>
<body>
{{--{{dd($errors)}}--}}
<div class="login-container">
    <div class="login-box animated fadeInDown">
        <h2 style="text-align: center; color: white;">Qareeb</h2>
        <div class="login-body" style="direction: ltr;">
            <div class="login-title"><strong>{{ __('language.Hello') }} </strong>, {{ __('language.please log in.') }}</div>
            <div class="login-title" style="color: red;"><strong>{{Session::get('error')}}</strong></div>
            <form action="/admin/login" class="form-horizontal" method="post" id="loginForm" name="loginForm">
                {{csrf_field()}}
                <div class="form-group">
                  <div class="col-md-12">
                    <select class="form-control" onchange="setLoginAction()" id="adminScope" name="adminScope">
                      <option value="Qreeb" {{ old('adminScope') == 'Qreeb' ? 'selected' : '' }}>Qreeb</option>
                      <option value="Provider" {{ old('adminScope') == 'Provider' ? 'selected' : '' }}>Provider</option>
                      <option value="Company" {{ old('adminScope') == 'Company' ? 'selected' : '' }}>Company</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="text" class="form-control" name="username" placeholder="Email"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <input type="password" class="form-control" name="password" placeholder="Password"/>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <button class="btn btn-info btn-block">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script type="text/javascript">
    function setLoginAction() {
        var adminScope = document.getElementById('adminScope').value;

        switch (adminScope) {
          case 'Qreeb':
              document.loginForm.action = "/admin/login";
            break;
          case 'Provider':
              document.loginForm.action = "/provider/login";
            break;
          case 'Company':
              document.loginForm.action = "/company/login";
            break;
        }
    }
</script>

</body>
</html>
