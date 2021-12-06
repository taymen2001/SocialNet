<?php
include("functions/function.php");
include("functions/dbh.php");
session_start();

if (!isset($_SESSION['login'])) {
  header("Location: index.php");
  exit();
}

echo "<h1>WELCOME " . $_SESSION['name'] . "!</h1>
  <h3>You can add friends here. Your friends: " . $_SESSION['numOfFriends'] . " .</h3>";

  if ($_SESSION['numOfFriends'] == 0) {
    echo "<h2> ༼ つ ◕_◕ ༽つFeeling lonely, add some friends! ༼ つ ◕_◕ ༽つ</h2>";
  }else{
    echo "<h2> ༼ つ ◕_◕ ༽つ Okilabum! You are all set. ༼ つ ◕_◕ ༽つ</h2>";
  }

if (isset($_GET['pageNo'])) {
  $pageNo = $_GET['pageNo'];
} else {
  $pageNo = 1;
}

require_once("functions/function.php");
$offSet = ($pageNo - 1) * 5;
$totalFriends = totalFriend($conn);
//round totalPage as a whole number
$totalPage = ceil(($totalFriends) / 5);

if ($totalFriends > 5){
  if ($pageNo < 2) {
    echo "<a class='button' href='?pageNo=" . ($pageNo + 1) . "'> Next </a>";
  } elseif ($pageNo > $totalPage - 1) {
    echo "<a class='button' href='?pageNo=" . ($pageNo - 1) . "'> Prev </a>";
  } else {
    echo "<a class='button' href='?pageNo=" . ($pageNo - 1) . "'> Prev </a>";
    echo "<a class='button' href='?pageNo=" . ($pageNo + 1) . "'> Next </a>";
  }
}
?>

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
  <form method="POST" action="friendadd.php">
  <?php if (isset($_GET['error'])) { ?>
     		<p class="Msg"><?php echo $_GET['error']; ?></p>
     	<?php } ?>
    <table>
      <?php
      showOtherUsers($conn, $offSet, 5);
      echo "<a href = 'friendlist.php' class = 'ca font1'>Check your friendlist</a>";
      echo "<a href = 'logout.php' class = 'ca font1'>Wanna log out!</a>";

      ?>
    </table>
  </form>
</body>

</html>