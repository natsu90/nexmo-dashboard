<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Nexmo Dashboard | Setup</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="themes/AdminLTE-2.0.2/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="themes/AdminLTE-2.0.2/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="themes/AdminLTE-2.0.2/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="/"><b>Nexmo</b>Dashboard</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">Enter your Nexmo API credentials</p>
        <?php if(Session::has('message')):?>
        <div class="alert alert-warning alert-dismissable">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-warning"></i> Alert!</h4>
          <?php echo Session::get('message'); ?>
        </div>
        <?php endif; ?>
        <form action="" method="post">
          <div class="form-group has-feedback">
            <input type="text" class="form-control" placeholder="Nexmo Key" name="nexmo_key"/>
            <span class="form-control-feedback"><i class="fa fa-key"></i></span>
          </div>
          <div class="form-group has-feedback">
            <input type="text" class="form-control" placeholder="Nexmo Secret" name="nexmo_secret"/>
            <span class="form-control-feedback"><i class="fa fa-lock"></i></span>
          </div>
          <div class="row">
            <div class="col-xs-8">                         
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat" data-loading-text="Configuring...">Continue</button>
            </div><!-- /.col -->
          </div>
        </form>

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.3 -->
    <script src="themes/AdminLTE-2.0.2/plugins/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="themes/AdminLTE-2.0.2/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="themes/AdminLTE-2.0.2/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
  </body>
</html>