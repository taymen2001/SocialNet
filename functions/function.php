<?php
//function validate($data)
//{
// $data = trim($data);
//$data = stripslashes($data);
//$data = htmlspecialchars($data);
//return $data;
//}

function createTables($conn)
{
  if ($conn) {
    $query = "CREATE TABLE IF NOT EXISTS friends (
        friend_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        friend_email varchar(50) NOT NULL,
        password varchar(20) NOT NULL,
        profile_name varchar(30) NOT NULL,
        date_started date NOT NULL,
        num_of_friends int(10) UNSIGNED NOT NULL
        );";
    $result1 = mysqli_query($conn, $query);

    $query = "CREATE TABLE IF NOT EXISTS myfriends (
        friend_id1 int(10) UNSIGNED NOT NULL, 
        friend_id2 int(10) UNSIGNED NOT NULL,
        FOREIGN KEY (friend_id1) REFERENCES friends(friend_id),
        FOREIGN KEY (friend_id2) REFERENCES friends(friend_id)
        );";
    $result2 = mysqli_query($conn, $query);

    if ($result1 && $result2) {
      echo "<p>Create successfully</p>";
    }
  }
}

function ExecSqlFile($conn, $filename)
{
  // Temporary variable, used to store current query
  $query = '';
  $filename = file('data/new.sql');

  if ($filename) {
    foreach ($filename as $line) {

      $startWith = substr(trim($line), 0, 2);
      $endWith = substr(trim($line), -1, 1);

      if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
        continue;
      }

      $query = $query . $line;
      if ($endWith == ';') {
        mysqli_query($conn, $query);
        $query = '';
      }
    }
    echo "<p>Table has been created and populated successfully</p>";
  } else {
    echo "<p>Please check again</p>";
  }
}

function checkValTable($conn)
{
  $filename = "";

  if ($conn) {
    $query = "SELECT * FROM myfriends WHERE 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
      if (mysqli_num_rows($result) == 0) {
        ExecSqlFile($conn, $filename);
      } else {
        echo "<p>The tables have been popuated</p>";
      }
    }
  }
}

function checkTables($conn)
{
  if ($conn) {
    $query = "SELECT 1 FROM friends ";
    $result = mysqli_query($conn, $query);

    if ($result !== FALSE) {
      echo "<p>Tables exist\n</p>";
    } else {
      createTables($conn);
    }
  }
}

/****************************************************************/
/**********************CHECK DUPLICATE INFO**********************/
/****************************************************************/

function checkDupEmail($conn, $input)
{
  if ($conn) {
    $query = "SELECT * FROM friends WHERE friend_email = '$input';";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
      return false;
    } else {
      return true;
    }
  }
}

/****************************************************************/
/**********************CURRENT SESSION ID************************/
/****************************************************************/


function CurrentID($conn)
{
  $query = "SELECT * FROM friends ORDER BY profile_name ASC;";
  $result = mysqli_query($conn, $query);

  if ($result) {
    $row = mysqli_fetch_assoc($result);
    while ($row) {
      if ($_SESSION['name'] == $row['profile_name']) {
        $_SESSION['ID'] = $row['friend_id'];
      }
      $row = mysqli_fetch_assoc($result);
    }
  } else {
    echo "Cannot proceed the query!";
  }
}


/****************************************************************/
/**********************DISPLAY FRIEND LIST***********************/
/**********************___(UNFRIEND)___**************************/


function FriendsList($conn, $offset, $numPerPage)
{

  if ($conn) {
    $query = "SELECT * FROM friends ORDER BY profile_name ASC";
    $result = mysqli_query($conn, $query);

    if ($result) {
      CurrentID($conn);

      while ($row = mysqli_fetch_assoc($result)) {
        $f_ID = $row['friend_id'];
        $f_name = $row['profile_name'];

        $search = "SELECT * FROM myfriends WHERE friend_id1 = '" . $_SESSION['ID'] . "' LIMIT $offset, $numPerPage";
        $searchResult = mysqli_query($conn, $search);


        while ($row = mysqli_fetch_assoc($searchResult)) {
          $mf_ID2 = $row['friend_id2'];
          if ($mf_ID2 == $f_ID) {
            echo "<tr><td><p> $f_name </p></td>
                  <td><input type='submit' name='DEL_" . $f_ID . "' value='UNFRIEND'></td></tr>";
          }
        }
      }
      mysqli_free_result($searchResult);
      mysqli_free_result($result);

      if ($conn) {
        $query = "SELECT * FROM myfriends WHERE friend_id1 = '" . $_SESSION['ID'] . "'";
        $result = mysqli_query($conn, $query);

        if ($result) {
          while ($row = mysqli_fetch_assoc($result)) {
            $mf_ID2 = $row['friend_id2'];
            /*set the buttons to DEL_(fr id) and called removeFriend to get functions*/
            echo((isset($_POST["DEL_$mf_ID2"]))? removeFriend($conn, $mf_ID2): "");
          }

          mysqli_free_result($result);
          mysqli_close($conn);
        } else {
          echo "Please try again";
        }
      } else {
        die("<p>Connection failed\n" . "Error Code: "
          . mysqli_connect_errno() . ":" . mysqli_connect_error() . "</p>");
      }
    }
  } else {
    die("<p>Connection failed\n" . "Error Code: "
      . mysqli_connect_errno() . ":" . mysqli_connect_error() . "</p>");
  }
}


/**************************************************************/
/*******************REMOVE FRIEND FUNCTION*********************/
/**************************************************************/

function removeFriend($conn, $userID)
{
  if ($conn) {
    $query = "DELETE FROM myfriends WHERE friend_id1 = " . $_SESSION['ID'] . " AND friend_id2 = $userID;";
    $result = mysqli_query($conn, $query);

    if ($result) {
      $_SESSION['numOfFriends']--;
      $query = "UPDATE friends SET num_of_friends = '" . $_SESSION['numOfFriends'] . "' WHERE friend_id  = '" . $_SESSION['ID'] . "'";
      $update = mysqli_query($conn, $query);

      header("Location: friendlist.php?error=Remove Completed!");
      exit();
    }
  } else {
    die("<p>Connection failed\n" . "Error Code: "
      . mysqli_connect_errno() . ":" . mysqli_connect_error() . "</p>");
  }
}


/**************************************************************/
/*********************ADD FRIEND FUNCTION**********************/
/**************************************************************/


function addFriend($conn, $userID)
{
  if ($conn) {
    CurrentID($conn);
    $query = "INSERT INTO myfriends VALUES(" . $_SESSION['ID'] . ", $userID);";
    $result = mysqli_query($conn, $query);

    if ($result) {
      $_SESSION['numOfFriends']++;
      $query = "UPDATE friends SET num_of_friends = '" . $_SESSION['numOfFriends'] . "' WHERE friend_id  = '" . $_SESSION['ID'] . "'";
      $result = mysqli_query($conn, $query);

      $query = "SELECT profile_name FROM friends WHERE friend_id  = '$userID'";
      $result = mysqli_query($conn, $query);
     
      while ( $row = mysqli_fetch_assoc($result)) {
        $_SESSION['friend'] = $row['profile_name'];
        header("Location: friendadd.php?error=Added Completed! '".$_SESSION['friend']."' is now your friend.");
        exit();
      }
    }
  } else {
    die("<p>Connection failed\n" . "Error Code: "
      . mysqli_connect_errno() . ":" . mysqli_connect_error() . "</p>");
  }
}


/****************************************************************/
/**********************DISPLAY FRIEND LIST***********************/
/**********************___(ADDFRIEND)___************************/

function showOtherUsers($conn, $offset, $numPerPage)
{

  if ($conn) {
    CurrentID($conn);

    $query = "SELECT friend_id, profile_name FROM friends 
      WHERE friend_id NOT IN (SELECT friend_id2 FROM myfriends where friend_id1=" . $_SESSION['ID'] . ")  
      AND friend_id != " . $_SESSION['ID'] . "
      GROUP BY profile_name ASC LIMIT $offset, $numPerPage;";

    $result = mysqli_query($conn, $query);

    if ($result) {
      while ($row = mysqli_fetch_assoc($result)) {
        $f_ID = $row['friend_id'];
        $f_name = $row['profile_name'];
        echo "<tr><td><p>$f_name</p></td>
                <td><input type='submit' name='ADD_" . $f_ID . "' value='ADD FRIEND'></td></tr>";
      }
      mysqli_free_result($result);
      
      if ($conn) {
        $query = "SELECT * FROM friends WHERE friend_id != '".$_SESSION['ID']."';";
        $result = mysqli_query($conn, $query);

        if ($result) {
          while ($row = mysqli_fetch_assoc($result)) {
            $f_userID = $row['friend_id']; 
              echo((isset($_POST["ADD_$f_userID"]))? addFriend($conn, $f_userID): "");
          }

          mysqli_free_result($result);
          mysqli_close($conn);
        } else {
          echo "Please try again";
        }
      } 

    } else {
      echo "Please try again";
    }
    return true;
  } else {
    die("<p>Connection failed\n" . "Error Code: "
      . mysqli_connect_errno() . ":" . mysqli_connect_error() . "</p>");
    return false;
  }
}


/**************************************************************/
/*******************GET TOTAL FRIEND FUNCTION******************/
/**************************************************************/

function totalFriend($conn){
  if($conn){
    CurrentID($conn);
    $query = "SELECT COUNT(*) AS 'total' FROM friends WHERE friend_id != '".$_SESSION['ID']."';";
    $result = mysqli_query($conn, $query);

    if ($result){
      $row = mysqli_fetch_assoc($result);
      return $row['total'];
    }
  }else{
    die("<p>Connection failed\n" . "Error Code: "
      . mysqli_connect_errno() . ":" . mysqli_connect_error() . "</p>");
  }
}