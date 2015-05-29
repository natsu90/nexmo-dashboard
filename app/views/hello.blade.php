<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Nexmo Dashboard Redesigned.</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="js/libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Select2 style -->
    <link href="js/libs/select2/select2.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="js/libs/ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

    <link href="//cdn.datatables.net/plug-ins/f2c75b7247b/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />

    <link href="//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-simple.css" rel="stylesheet" type="text/css" />

    <script data-main="js/main" src="js/libs/requirejs/require.js" type="text/javascript"></script>
    <script type="text/javascript">
      var pusher_key = "{{ Config::get('pusherer::key') }}",
          auth_token = "{{ JWTAuth::fromUser(Auth::user()) }}";
    </script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="skin-blue fixed">
    <div class="wrapper">
      
      <header class="main-header">
        <!-- Logo -->
        <a href="" class="logo"><b>Nexmo</b>Dashboard</a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <li>
                <a href="javascript:void()">
                  <i class="fa fa-user"></i> 
                  Logged in as <?php echo Auth::user()->username;?>
                </a>
              </li>
              <li>
                <a href="/logout" data-toggle="tooltip" title="Logout" data-placement="bottom">
                  <i class="fa fa-sign-out"></i>
                  Logout
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          
          <div class="user-panel">
            <div class="pull-left info">
              <p>Credit Balance: </p>
              <i class="fa fa-eur fa-lg"></i> <span style="font-size:200%" id="credit-balance"><?php echo $credit_balance; ?></span> 
              <a href="javascript:alert('Unfortunately it is not possible.')">
              <span class="fa-stack fa-lg pull-right" title="Topup Credit" data-toggle="tooltip" data-placement="bottom">
                <i class="fa fa-plus fa-stack-1x"></i>
                <i class="fa fa-circle-o fa-stack-2x"></i>
              </span>
              </a>
            </div>
          </div>

          <ul class="sidebar-menu">
            <li class="header">MESSAGES</li>
            <li>
              <a href="#/send">
                <i class="fa fa-pencil text-info"></i> <span class="text-info">New Message</span>
              </a>
            </li>
            <li>
              <a href="#/inbound">
                <i class="fa fa-envelope"></i> <span>Inbound</span>
              </a>
            </li>
            <li>
              <a href="#/outbound">
                <i class="fa fa-paper-plane"></i> <span>Outbound</span>
              </a>
            </li>
            <li class="header">NUMBERS</li>
            <li>
              <a href="#/buy">
                <i class="fa fa-plus-square text-info"></i> <span class="text-info">Buy Number</span>
              </a>
            </li>
            <li>
              <a href="#">
                <i class="fa fa-phone-square"></i>
                <span>Numbers</span>
                <span class="label label-primary pull-right notification"><?php echo count($numbers);?></span>
              </a>
              <ul class="treeview-menu">
                <?php if(count($numbers)):?>
                <?php foreach($numbers as $number):?>
                <li><a href="#/number/<?php echo $number->number;?>"><i class="fa fa-<?php echo strpos($number->type, 'mobile') === false ? 'phone' : 'mobile' ;?>"></i> <?php echo $number->number;?></a></li>
                <?php endforeach;?>
                <?php endif;?>
              </ul>
            </li>
            <li>
              <a href="#/calls">
                <i class="fa fa-exchange"></i> <span>Call Logs</span>
              </a>
            </li>
            <li>

          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper" id="page">
        
      </div><!-- /.content-wrapper -->

      <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b>Version</b> 2.0
        </div>
        <strong>Theme by <a href="http://almsaeedstudio.com">Almsaeed Studio</a>.</strong>
      </footer>

    </div><!-- ./wrapper -->

  </body>
</html>