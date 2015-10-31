
<?php
$host = "host=eventos.cyeijrrcwqvl.us-west-2.rds.amazonaws.com";
$port = "port=5590";
$dbname = "dbname=eventos";
$credentials = "user=eventos password=12345678";
$db = pg_connect("$host $port $dbname $credentials");

if (!$db)
	{
	$result['success'] = 'false';
	$result['error'] = 'Cannot open db connection';
	echo json_encode($result);
	}
  else
	{
	if ($_SERVER['REQUEST_METHOD'] == 'GET')
		{
		if (isset($_GET['selector']))
			{
			$selector = $_GET['selector'];
			switch ($selector)
				{
			case 2:
				if (isset($_GET['email']) && isset($_GET['password']))
					{
					$email = $_GET['email'];
					$password = $_GET['password'];
					userLogin($selector, $email, $password, $db);
					}
				  else
					{
					$result['success'] = 'false';
					$result['error'] = 'Missing Paramters';
					echo json_encode($result);
					}

				break;

			case 4:
				if (isset($_REQUEST['userid']))
					{
					displaygeofence($_REQUEST['userid'], $db);
					}
				  else
					{
					$result['success'] = 'false';
					$result['error'] = 'Invalid or missing UserId';
					echo json_encode($result);
					}

				break;

			case 5:
				if (isset($_REQUEST['geofenceid']))
					{
					displayevents($_REQUEST['geofenceid'], $db);
					}
				  else
					{
					$result['success'] = 'false';
					$result['error'] = 'Invalid or missing geofenceidId';
					echo json_encode($result);
        }

				break;

			default:
				$result['success'] = 'false';
				$result['error'] = 'Invalid selector value';
				echo json_encode($result);
				}
			}
		  else
			{
			$result['success'] = 'false';
			$result['error'] = 'Specify selector';
			echo json_encode($result);
			}
		}

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
		if (isset($_GET['selector']))
			{
			$selector = $_GET['selector'];
			switch ($selector)
				{
			case 1:
				if (isset($_REQUEST['name']) && isset($_REQUEST['emailid']) && isset($_REQUEST['password']) && isset($_REQUEST['phn']) && isset($_REQUEST['selector']))
					{
					userSignup($_REQUEST['name'], $_REQUEST['emailid'], $_REQUEST['password'], $_REQUEST['phn'], $db);
					}
				  else
					{
					$result = array();
					$result['success'] = 'false';
					$result['error'] = 'Some data is missing';
					echo json_encode($result);
					}

				break;

			case 3:
				if (isset($_REQUEST['userid']) && isset($_REQUEST['geofencename']) && isset($_REQUEST['long']) && isset($_REQUEST['lat']) && isset($_REQUEST['radius']))
					{
					addgeofence($_REQUEST['userid'], $_REQUEST['geofencename'], $_REQUEST['radius'], $_REQUEST['long'], $_REQUEST['lat'], $db);
					}
				  else
					{
					$result = array();
					$result['success'] = 'false';
					$result['error'] = 'Some data is missing';
					echo json_encode($result);
					}

				break;

			default:
				$result['success'] = 'false';
				$result['error'] = 'Invalid selector value';
				echo json_encode($result);
				}
			}
		}
	}

function displayevents($geofenceid, $db)
	{
	$sql = "SELECT ST_AsGeoJSON(center),radius FROM geofence where geofenceid='" . $geofenceid . "'";
	$ret = pg_query($db, $sql);
	if (!$ret)
		{
		$result['success'] = 'false';
		$result['error'] = pg_last_error($db);
		echo json_encode($result);
		}
	  else
		{
		if (pg_num_rows($ret) == 0)
			{
			$result['success'] = 'false';
			$result['error'] = "No record found for this geofence id";
			echo json_encode($result);
			}
		  else
			{
			$row = pg_fetch_row($ret);
			$geometry = json_decode($row[0]);
			$radius = $row[1];
			$geometry = get_object_vars($geometry);
      // echo $geometry['coordinates'][0];
			// var_dump($radius);

      $sql = "SELECT * FROM events WHERE ST_DWithin(location, ST_SetSRID(ST_Point(".$geometry['coordinates'][0].",".$geometry['coordinates'][1]."), 4326),".($radius * 1609).")";

      echo $sql;
      $ret = pg_query($db, $sql);
      // exit;
      if (pg_num_rows($ret) == 0)
  			{
  			$result['success'] = 'false';
  			$result['error'] = "No record found";
  			echo json_encode($result);
  			}
  		  else
  			{

      while ($row = pg_fetch_row($ret))
				{

			$events[] = array(
				array(
					'eventid' => $row[0] ,
					'eventname' => $row[1],
					'description' => $row[2],
          'logo_url' => $row[3],
          'end_time' => $row[4],
          'start_time' => $row[5],
          'eventwebsiteurl' => $row[7],
				)
			);
      // break;
    }
			$result['success'] = 'True';
      if($events)
      {
			$result['events'] = $events;
    }
    $result['events'] = $events;
			echo json_encode($result);
    }
		}
	}
}

function displaygeofence($userid, $db)
	{
	$sql = "SELECT ST_AsGeoJSON(center),geofenceid,radius FROM geofence where userid='" . $userid . "'";
	$ret = pg_query($db, $sql);
	if (!$ret)
		{
		$result['success'] = 'false';
		$result['error'] = pg_last_error($db);
		echo json_encode($result);
		}
	  else
		{
		if (pg_num_rows($ret) == 0)
			{
			$result['success'] = 'false';
			$result['error'] = "No geofence found for this user id";
			echo json_encode($result);
			}
		  else
			{
			while ($row = pg_fetch_row($ret))
				{
				$geofence[] = array(
					array(
						'center' => json_decode($row[0]) ,
						'geofenceid' => $row[1],
						'radius' => $row[2]
					)
				);
				}

			$result['success'] = 'True';
			$result['geofences'] = $geofence;
			echo json_encode($result);
			}
		}
	}

function addgeofence($userid, $geofencename, $radius, $long, $lat, $db)
	{
	$sql = "INSERT INTO geofence (userid,geofencename,center,radius) VALUES ('" . $userid . "','" . $geofencename . "', ST_GeographyFromText('SRID=4326;POINT(" . $long . " " . $lat . ")'),'" . $radius . "')";
	echo $sql;
	$ret = pg_query($db, $sql);
	if (!$ret)
		{
		$result['success'] = 'false';
		$result['error'] = pg_last_error($db);
		echo json_encode($result);
		}
	  else
		{
		$result['success'] = 'True';
		echo json_encode($result);
		}
	}

function userSignup($name, $emailid, $password, $phn, $db)
	{
	$sql = "SELECT count(*) FROM usertable where email='" . $emailid . "'";
	$ret = pg_query($db, $sql);
	$row = pg_fetch_row($ret);

	// echo $row[0];
	// exit;

	if ($row[0] > 0)
		{
		$result['success'] = 'false';
		$result['error'] = 'email already exits';
		}
	  else
		{
		$sql = "INSERT INTO usertable (username,email,password,phonenumber) VALUES ('" . $name . "','" . $emailid . "','" . $password . "','" . $phn . "')";
		$ret = pg_query($db, $sql);
		if (!$ret)
			{
			$result['success'] = 'false';
			$result['error'] = pg_last_error($db);
			}
		  else
			{
			$sql = "select currval('usertable_userid_seq');";
			$ret = pg_query($db, $sql);
			$row = pg_fetch_row($ret);
			$result['success'] = 'True';
			$result['userid'] = $row[0];
			}
		}

	echo json_encode($result);
	}

function userLogin($selector, $email, $password, $db)
	{
	$sql = "SELECT userid from usertable where email = '" . $email . "'and password = '" . $password . "'";
	$ret = pg_query($db, $sql);
	if (!$ret)
		{
		$result['success'] = 'false';
		$result['error'] = pg_last_error($db);
		echo json_encode($result);
		}
	  else
		{
		$row = pg_fetch_row($ret);
		if ($row)
			{
			$result['success'] = 'True';
			$result['userid'] = $row[0];
			echo json_encode($result);
			}
		  else
			{
			$result['success'] = 'False';
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
