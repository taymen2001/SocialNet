<?php
include("functions/function.php");
include("functions/dbh.php");
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="description" content="Web Development - Assignment 2">
  <meta name="keywords" content="HTML, CSS, JavaScript">
  <meta name="author" content="NGUYEN DINH LAM">
  <title>FRIEND SYSTEM - LOGIN</title>

  <link rel="stylesheet" href="style/style.css" />
</head>

<body>

  <body>
    <h1>FRIEND SYSTEM - LOGIN</h1>
    <form action="login.php" method="post" enctype="multipart/form-data">
    <?php if (isset($_GET['error'])) { ?>
     		<p class="error"><?php echo $_GET['error']; ?></p>
     	<?php } ?>
      <input type="email" name="email" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ''; ?>" placeholder="Email" required="required">

      <input type="password" name="pswd" placeholder="Password" required="required">

      <button type="submit" name="login">Login</button>

      <a href="signup.php" class="ca">Register Here</a>

      <a href="index.php" class="ca">Back to Homepage</a>
    </form>

    <?php
    if (isset($_POST['login'])) {
      $email = mysqli_real_escape_string($conn, $_POST['email']);
      $pass = mysqli_real_escape_string($conn, $_POST['pswd']);

      //Check the case sensitive for email and password
      $query = "SELECT * FROM friends where BINARY friend_email='$email' and BINARY password='$pass';";
      $result = mysqli_query($conn, $query);

      if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if ($row['friend_email'] === $email && $row['password'] === $pass) {
          $_SESSION['login'] = "success";
          $_SESSION['name'] = $row['profile_name'];
          $_SESSION['numOfFriends'] = $row['num_of_friends'];
          header("Location: friendlist.php");
          exit();
        } else {
          header("Location: login.php?error=Invalid Email ID/Password");
          exit();
        }
      } else {
        header("Location: login.php?error=Invalid Email ID/Password");
        exit();
      }
    }
    ?>



  </body>

</html>