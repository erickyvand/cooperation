<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email</title>
  <style>
    .main {
      width: 90%;
      margin: auto;
    }

    .header {
      background-color: #518791;
      color: white;
    }

    .header h1{
      text-align: center;
    }

    .content {
      min-height: 300px;
    }

    .content p {
      font-size: 1.2em;
    }

    .footer {
      background-color: #518791;
    }

    .footer p {
      text-align: center;
      color: white;
    }
  </style>
</head>
<body>
  <div class="main">
    <div class="header">
      <h1>Cooperation LTD</h1>
    </div>
    <div class="content">
      <p>Dear {{ $data['names'] }}</p>
      <p>Thank you for your enquire, in order to reset your password click on the link below</p>
      <p>Click on this link to reset your password</p></br><a href="">{{ $data['token'] }}</a>
    </div>
    <div class="footer">
      <p>Cooperation &copy; <?=date('Y');?> . All right reserved</p>
    </div>
  </div>
</body>
</html>
