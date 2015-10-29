<?php
$host = "host=eventos.cyeijrrcwqvl.us-west-2.rds.amazonaws.com";
$port = "port=5590";
$dbname = "dbname=eventos";
$credentials = "user=eventos password=12345678";
$db = pg_connect("$host $port $dbname $credentials");

if (!$db) {
  $result['success'] = 'false';
  $result['error'] = 'Cannot open db connection';
  echo json_encode($result);

}
else {


	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		if (isset($_GET['selector'])) {
			$selector = $_GET['selector'];
			switch ($selector) {
			case 2:
      if(isset($_GET['email'])&&isset($_GET['password']))
      {
        $email = $_GET['email'];
        $password = $_GET['password'];
				userLogin($selector,$email,$password,$db);
      }
      else {
        $result['success'] = 'false';
        $result['error'] = 'Missing Paramters';
        echo json_encode($result);

      }
				break;

			case 3:
				echo $selector;
				break;

			case 4:
				echo $selector;
				break;

			default:
				$result['success'] = 'false';
				$result['error'] = 'Invalid selector value';
				echo json_encode($result);
			}
		}
		else {
			$result['success'] = 'false';
			$result['error'] = 'Specify selector';
			echo json_encode($result);
		}
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_REQUEST['name']) && isset($_REQUEST['emailid']) && isset($_REQUEST['password']) && isset($_REQUEST['phn']) && isset($_REQUEST['selector'])) {
			$sql = "SELECT count(*) FROM usertable where email='" . $_REQUEST['emailid'] . "'";
			$ret = pg_query($db, $sql);
			$row = pg_fetch_row($ret);
			echo $row[0];

			// exit;

			if ($row[0] > 0) {
				$result['success'] = 'false';
				$result['error'] = 'email already exits';
			}
			else {
				$sql = "INSERT INTO usertable (username,email,password,phonenumber) VALUES ('" . $_REQUEST['name'] . "','" . $_REQUEST['emailid'] . "','" . $_REQUEST['password'] . "','" . $_REQUEST['phn'] . "')";
				$ret = pg_query($db, $sql);
				if (!$ret) {
					$result['success'] = 'false';
					$result['error'] = pg_last_error($db);
				}
				else {
					$sql = "select currval('usertable_userid_seq');";
					$ret = pg_query($db, $sql);
					$row = pg_fetch_row($ret);
					$result['success'] = 'True';
					$result['userid'] = $row[0];
				}
			}

			echo json_encode($result);
		}
		else {
			$result = array();
			$result['success'] = 'false';
			$result['error'] = 'Some data is missing';
			echo json_encode($result);
		}
	}
}

function userLogin($selector,$email,$password,$db)
{
  $sql ="SELECT userid from usertable where email = '".$email."'and password = '".$password."'";
  $ret = pg_query($db, $sql);
  if (!$ret) {
    $result['success'] = 'false';
    $result['error'] = pg_last_error($db);
    echo json_encode($result);
  }
  else {
    $row = pg_fetch_row($ret);
    
    if($row)
    {
      $result['success'] = 'True';
      $result['userid'] = $row[0];
      echo json_encode($result);

    }
    else {
      $result['success'] = 'True';
      $result['error'] = 'Invalid credentials';
      echo json_encode($result);

    }

  }
}
?>


<!-- if (isset($_GET['long']) && isset($_GET['lat']) && isset($_GET['radius'])) {
  echo "long" . $_GET['long'] . "<br/><br/>";
  echo "lat" . $_GET['lat'] . "<br/><br/>";
  echo "radius" . $_GET['radius'] . "<br/><br/>";


  //  $sql ="SELECT * FROM events WHERE ST_Distance_Sphere(location, ST_MakePoint(".$_GET['long'].",".$_GET['lat'].")) <= ".$_GET['radius'] ;


  $sql = "SELECT st_astext(location) FROM events WHERE ST_DWithin(Geography(location),Geography(ST_MakePoint(" . $_GET['long'] . "," . $_GET['lat'] . "))," . $_GET['radius'] . ")";
  $ret = pg_query($db, $sql);
  if (!$ret) {
    echo pg_last_error($db);
    exit;
  }

  while ($row = pg_fetch_row($ret)) {


    // echo  $row[0] . "\n";


    echo $row[0] . "<br/><br/>";


    // echo  $row[2] ."<br/><br/>";
    // echo $row[3] ."<br/><br/>";
    // echo $row[4] ."<br/><br/>";
    // echo $row[5] ."<br/><br/>";
    // echo $row[6] ."<br/><br/>";
    // echo $row[7] ."<br/><br/>";
    // echo $row[8] ."<br/><br/>";


    echo "<br/><br/><br/><br/>"; -->
