<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1280">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no"> -->
    <title>PHP-Chat-App</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/Bootstrap-Chat.css">
</head>

<?php 
exec('php bin\chat-server.php > /dev/null 2>&1 &');
date_default_timezone_set('Asia/Jakarta');

session_start();
if (isset($_POST['submit'])) {
  $name = $_POST['name'];
  if ($name == '') {
    $_SESSION['name'] = 'anonymous';
    $_SESSION['createtime'] = date('Y-m-d H:i:s');
  }else{
    $_SESSION['name'] = strtoupper($name);
    $_SESSION['createtime'] = date('Y-m-d H:i:s');
  }
}
?>

<body>
<div style="text-align: center;">
  <a href="https://github.com/akbarnov/phpchatapp-websocket" target="_blank">Github Source code</a>
</div>
<div class="bootstrap_chat">
<div class="container py-5 px-4">
  <!-- For demo purpose-->
  <header class="text-center">
    <h1 class="display-4 text-white">PHP Chat Room</h1>
  </header>

  <div class="row rounded-lg overflow-hidden shadow">
    <!-- Users box-->
    <div class="col-5 px-0">
      <div class="bg-white">

        <div class="bg-gray px-4 py-2 bg-light">
          <p class="h5 mb-0 py-1" id="count-user">User Active :</p>
        </div>

        <div class="messages-box">
          <div class="list-group rounded-0" id="active-user">

          </div>
        </div>
      </div>
    </div>
    <!-- Chat Box-->
    <div class="col-7 px-0">
      <div class="px-4 py-5 chat-box bg-white" id="msg_box">
      </div>

      <!-- Typing area -->
      <div class="bg-light">
        <div class="input-group">
          <input type="text" placeholder="Type a message" id="msg" aria-describedby="button-addon2" class="form-control rounded-0 border-0 py-4 bg-light">
          <div class="input-group-append">
            <button id="button-send" type="submit" class="btn btn-link"> <i class="fa fa-paper-plane"></i></button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
</div>

<div class="modal fade" role="dialog" tabindex="-1" id="modal-username">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create username</h4><button class="close" type="button" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                  <div class="modal-body">
                      <div class="form-group">
                         <input autocomplete="off" class="form-control" type="text" id="name" name="name" placeholder="Insert name here.">
                      </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit" name="submit">Save</button>
                  </div>
                </form>
            </div>
        </div>
    </div>
    <div style="text-align: center;">
      <a href="https://github.com/akbarnov/phpchatapp-websocket" target="_blank">Github Source code</a>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>

    <script type="text/javascript">
      <?php if (!isset($_SESSION['name'])) { ?>
        $('#modal-username').modal('show');

        $('#msg').click(function () {
          $('#modal-username').modal('show');  
        })

        $('#active-user').html('');

      <?php }else{ ?>

        $('#active-user').append(
          '<a href="logout.php" class="list-group-item list-group-item-action list-group-item-light rounded-0">'
            +'<div class="media"><img src="https://res.cloudinary.com/mhmd/image/upload/v1564960395/avatar_usae7z.svg" alt="user" width="40" class="rounded-circle">'
              +'<div class="media-body ml-4">'
                +'<div class="d-flex align-items-center justify-content-between mb-1">'
                  +'<h6 class="mb-0"><?php if (isset($_SESSION['name'])) { echo $_SESSION['name']; } ?></h6>'
                  +'Logout'
                +'</div>'
                +'<p class="font-italic text-muted mb-0 text-small"><?php if (isset($_SESSION['name'])) { echo "Create time : ".$_SESSION['createtime']; } ?></p>'
              +'</div>'
            +'</div>'
          +'</a>'
          );

        var conn = new WebSocket('ws://localhost:8080');
        conn.onopen = function(e) {
            console.log("Connection established!");
        };

        conn.onmessage = function(e) {
            var getData = $.parseJSON(e.data);
            var html = "<b>"+getData.name+"</b>: "+getData.msg+"</br>";

            if (getData.count != undefined) {
              $('#count-user').html('User Active : '+getData.count);
            }

            if (getData.msg != undefined) {
              $('#msg_box').append(
                '<div class="media w-50 mb-3"><img src="https://res.cloudinary.com/mhmd/image/upload/v1564960395/avatar_usae7z.svg" alt="user" width="40" class="rounded-circle">'
                  +'<div class="media-body ml-3">'
                    +'<div class="bg-light rounded py-2 px-3 mb-2">'
                      +'<p class="text-small mb-0 text-muted">'+getData.msg+'</p>'
                    +'</div>'
                    +'<p class="text-muted" style="font-size:11px;"><b>'+getData.name+'</b> '+getData.createtime+'</p>'
                  +'</div>'
                +'</div>'
              );
            }
        };

        $('#button-send').click(function () {
            var today = new Date();
            var createtime = today.toLocaleString();
            var msg = $('#msg').val();
            var name = "<?php echo strtolower($_SESSION['name']) ?>";
            var content = {
                msg:msg,
                name:name,
                createtime:createtime
            };
            conn.send(JSON.stringify(content));


            $('#msg').val('');

            $('#msg_box').append(
              '<div class="media w-50 ml-auto mb-3">'
              +'<div class="media-body">'
                +'<div class="bg-primary rounded py-2 px-3 mb-2">'
                  +'<p class="text-small mb-0 text-white">'+msg+'</p>'
                +'</div>'
                +'<p class="text-muted" style="font-size:11px;"><b>'+name+'</b> '+createtime+'</p>'
              +'</div>'
            +'</div>'
            );
        })

        $('#msg').keypress(function (e) {
          if (e.which == 13) {
            var today = new Date();
            var createtime = today.toLocaleString();
            var msg = $('#msg').val();
            var name = "<?php echo strtolower($_SESSION['name']) ?>";
            var content = {
                msg:msg,
                name:name,
                createtime:createtime
            };
            conn.send(JSON.stringify(content));

            $('#msg').val('');

            $('#msg_box').append(
              '<div class="media w-50 ml-auto mb-3">'
              +'<div class="media-body">'
                +'<div class="bg-primary rounded py-2 px-3 mb-2">'
                  +'<p class="text-small mb-0 text-white">'+msg+'</p>'
                +'</div>'
                +'<p class="text-muted" style="font-size:11px;"><b>'+name+'</b> '+createtime+'</p>'
              +'</div>'
            +'</div>'
            );
          }
        });

      <?php } ?>
      
    </script>
</body>

</html>