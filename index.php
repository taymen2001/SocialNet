<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="description" content="Web Development - Assignment 2">
  <meta name="keywords" content="HTML, CSS, JavaScript">
  <meta name="author" content="NGUYEN DINH LAM">
  <title>FRIEND SYSTEM</title>

  <link rel="stylesheet" href="style/style.css" />
</head>

<body>
  <h1>My Friends System</h1>
  <div class = "box">
    <h2>Name: Nguyen Dinh Lam</h2>
    <h2>Student ID: 102953892</h2>
    <p>Email: 102953892@student.swin.edu.au</p>

    <p>I declare that this assignment is my individual work.
      I have not worked collaboratively nor have I copied from any other student’s work or from any other source.</p>
  
  <a href="signup.php" class = "ca font1">Sign Up</a>
  <a href="login.php" class = "ca font1">Log in</a>
  <a href="about.php" class = "ca font1">About this assignment</a>
</div>
  <?php
  include("functions/dbh.php");
  include("functions/function.php");

  if ($conn) {
    checkTables($conn);
    checkValTable($conn);
  } else {
    echo "<p style = 'color: red'>Please try again</p>";
  }

  mysqli_close($conn);
  ?>

</body>

</html>