<?php
include('Online_Surveys.dbconfig.inc');
$user_id = $_SESSION["USER_ID"];
?>

<html>
<head>
  <title>Vote+ (User View Survey)</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link href='http://fonts.googleapis.com/css?family=Open+Sans:600' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="css/login.css"/>
  <link rel="stylesheet" href="css/user.css"/>
</head>
<body>
	<div id="nav">
			<ul class="pull-left">
				<!--
				<li class="buttonNav"><a href="index.html">HOME</a></li>
				<li>/</li>-->
				<li><a href="user_homepage.php">USER HOME</a></li>
				<li>/</li>
				<li><a href="user_view_survey_list.php">VIEW SURVEYS</a></li>
			</ul>
			<div id="logo"><a href="#"><img src="img/voteLogo.png" height="35" width="62.2"></a></div>
			<ul class="pull-right">
				<li><a href="logout.php">LOGOUT</a></li>
			</ul>
		</div>
		<div id="results">
<?php

$con = mysqli_connect($servername, $username, $password);
if(!$con)
{
	die('Could not connect: ' . mysqli_error());
}
mysqli_select_db($con, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$survey_id=$_POST["survey_id"];
	$questions_count=$_POST["questions_count"];
	$date_begin=$_POST["date_begin"];

	// Get List of questions which the user has answered.
	$sql="SELECT tbl_questions.question_content, tbl_questions.question_id FROM tbl_questions INNER JOIN tbl_survey_questions ON tbl_questions.question_id = tbl_survey_questions.question_id WHERE tbl_survey_questions.survey_id=$survey_id";
    $result = mysqli_query($con, $sql);
	$num_rows = mysqli_num_rows($result);
	$count=0;
	while($row=mysqli_fetch_array($result))
	{
		$count++;
		$questions_id_list[$count]=$row['question_id'];
	}

	// Insert user answered responses to the respective questions in the database tables: tbl_user_questions, tbl_user_surveys
	for ($i=1; $i<=$questions_count; $i++)
	{
		if (isset($_POST[$questions_id_list[$i]]))
		{
			$response[$i]=$_POST[$questions_id_list[$i]];
			#echo "<br>Response: ".$response[$i];
			$sql1="INSERT INTO tbl_user_questions VALUES (null, '$user_id','$questions_id_list[$i]', '$response[$i]')";
			$result1 = mysqli_query($con, $sql1);

			if($result1)
			{
				#echo "<br>DEBUG: response: ".$response[$i]."<br>Query: ".$sql1;
				echo "<br>Survey question: ".$i." response inserted into database successfully!";
			} else
			{
				echo "<br>Error: " . $sql1 . "<br>" . $conn->error;
				echo "<br>Response: ".$response[$i];
			}
		} else
		{
			echo "<br>Question: ".$i." with question id: ".$questions_id_list[$i]." not answered.";
		}
	}

	// Mark this survey as completed for the user.
	//if ($result1)
	{
		$date_submit=date("Y-m-d H:i:s");
		$sql2 = "INSERT INTO tbl_user_surveys VALUES (null, '$user_id','$survey_id', '$date_begin', '$date_submit')";
		$result2 = mysqli_query($con, $sql2);
		if($result2)
		{
			echo "<br><br>Survey submitted successfully!<br>";
		} else
		{
			echo "<br>Error: " . $sql2 . "<br>" . $conn->error."<br>";
		}
	}

} // End if request method = POST

echo " userId: ".$user_id." surveyId: ".$survey_id." questions count: ".$questions_count."<br>";

$con->close();

?>
</div>
</body>
</html>
