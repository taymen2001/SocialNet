<?php
include("functions/dbh.php");
include("functions/function.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="description" content="Web Development - Assignment 2">
  <meta name="keywords" content="HTML, CSS, JavaScript">
  <meta name="author" content="NGUYEN DINH LAM">
  <title>FRIEND SYSTEM - SIGN UP</title>

  <link rel="stylesheet" href="style/style.css" />
</head>

<body>

  <body>
    <h1>FRIEND SYSTEM - SIGN UP</h1>
    <form method="POST" action="signup.php">
            <p><input type="email" name="email" placeholder="Email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ''; ?>"></p>
            <p><input type="text" name="name" placeholder="Your name" value="<?php echo isset($_POST["name"]) ? $_POST["name"] : ''; ?>"></p>
            <p><input type="password" placeholder="Password" name="pswd"></p>
            <p><input type="password" placeholder="Confirm Password" name="re_pswd"></p>
      
      <button type="reset" name="reset">Clear</button>
      <button type="submit" name="reg_user">Register</button>

    <a href="index.php" class="ca">Home</a>
    <a href="login.php" class="ca">Already have an account?</a>
    
     

    </form>

    <?php
    $errMsg = "";

    if (isset($_POST["reg_user"])) {
      require_once("functions/function.php");
      $err = false;

      $name = mysqli_real_escape_string($conn, $_POST['name']);
      $email = mysqli_real_escape_string($conn, $_POST['email']);
      $pass = mysqli_real_escape_string($conn, $_POST['pswd']);
      $repass = mysqli_real_escape_string($conn, $_POST['re_pswd']);

      if (empty($email)) {
        $err = true;
        echo "<p>Error email: Please fill in the form.</p>";
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) > 50) {
        $err = true;
        echo "<p>Error email: Please fill in the correct form and max of 50 charater";
      }

      if (!checkDupEmail($conn, $email)) {
        $err = true;
        echo "<p>Error email: Email has been existed</p>";
      }

      if (empty($name)) {
        $err = true;
        echo "<p>Error profile name: Please fill in the form.</p>";
      } elseif (!preg_match("/^([A-Za-z][\s]*){1,20}$/", $name) && strlen($name) > 30) {
        $err = true;
        echo "<p>Error profile name: Please fill in only letters and max of 30 charater";
      }

      if (empty($pass)) {
        $err = true;
        echo "<p>Password email: Please fill in the form.</p>";
      } elseif (!preg_match("/^(\w*){1,20}$/", $pass) && strlen($pass) > 20) {
        $err = true;
        echo "<p>Error profile name: Please fill in only letters and max of 20 charater";
      }

      if (empty($repass)) {
        $err = true;
        echo "<p>Error confirm password: Please fill in the form.</p>";
      } elseif (strcmp($repass, $pass)) {
        $err = true;
        echo "<p>The password confirmation does not match</p>";
      }

      if (!$err) {
        require_once("functions/function.php");
        $pass = md5($pass);

        if ($conn) {
          $query = "INSERT INTO friends 
        (friend_email, password, profile_name, date_started) 
        VALUES ('" . $_POST["email"] . "','" . $_POST["pswd"] . "','" . $_POST["name"] . "',now())";
          $result = mysqli_query($conn, $query);

          if ($result) {
            $_SESSION['login'] = "success";
            $_SESSION['name'] = $name;
            $_SESSION['noOfFriends'] = 0;
            header("Location: index.php");
          }
        }
      }
    }

    ?>
  </body>

</html>