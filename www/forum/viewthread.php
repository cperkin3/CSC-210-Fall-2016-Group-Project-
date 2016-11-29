<?php

session_start();

// Connect to the class file for converting date_time to "Ago" format

include_once ("agoTimeFormat.php");

$myAgoObject = new convertToAgo; // Establish the object

//connect to thrones database
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "Thrones_Database";

$thread_id = preg_replace('#[^0-9]#i', '', $_GET['id']); 

try {
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
	// Set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Get thread title
	$stmt = $conn->prepare("SELECT title FROM Forum_Threads WHERE id = $thread_id");
	$stmt->execute();

	$thread_title = "";
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$thread_title = $row["title"];
	}

	echo "<h2>$thread_title</h2>";

	// Get original post with all replies in the thread
	$stmt = $conn->prepare("SELECT * FROM Forum_Posts WHERE thread_id = $thread_id ORDER BY created_datetime ASC");
	$stmt->execute();

	$responses = "";

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$post_author = $row["user_post_by"];
		$date_time = $row["created_datetime"];
		//$date_time = strftime("%b %d, %Y", strtotime($date_time));
		$post_content = $row["content"];
		$responses = '<div class="response_top_div">' . $date_time . ' &nbsp; &nbsp; &bull; &nbsp; &nbsp; ' . $post_author . ' said:</div>
		<div class="response_div">' . $post_content . '</div>'

		//echo '<div class="response_top_div">' . $date_time . ' &nbsp; &nbsp; &bull; &nbsp; &nbsp; ' . $post_author . ' said:</div>
		//<div class="response_div">' . $post_content . '</div>';
	}

} catch (PDOException $e) {
	echo "Error: " . $e->getMessage() . "<br/>";
	die();
}

?>

<?php 

	$replyButton = '<input name="myBtn1" type="submit" value="Post a Response" style="font-size:16px; padding:12px;" onmousedown="javascript:toggleForm(\'reponse_form\');" />';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>CSC 210 Project</title>
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="../css/nav-bar.css">
	<link rel="stylesheet" type="text/css" href="../css/login-bar.css">
	<link rel="stylesheet" type="text/css" href="../css/styles.css">
	<link rel="stylesheet" type="text/css" href="../css/viewthread.css">
	<!-- JavaScript --> 
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="../js/header.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/style.css" rel="stylesheet" type="text/css" />
<script src="../js/jquery-1.4.2.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">

//jquery

function toggleForm(x) {
		if ($('#'+x).is(":hidden")) {
			$('#'+x).slideDown(200);1
		} else {
			$('#'+x).slideUp(200);
		}
}

//ajax

$('#responseForm').submit(function(){$('input[type=submit]', this).attr('disabled', 'disabled');});

function parseResponse ( ) {
	  var thread_id = $("#thread_id");
	  var post_body = $("#content");
	 // var fs_id = $("#forum_section_id");
	  var fs_title = $("#category_name");
	  var u_id = $("#member_id");
	  var u_pass = $("#password");
	  var url = "parsepost.php";
      if (post_body.val() == "") {
           $("#formError").html('<font size="+2">Please type something</font>').show().fadeOut(3000);
      } else if (post_body.val().length < 2 ) { 
	         $("#formError").html('<font size="+2">Your post must be at least 2 characters long').show().fadeOut(3000);
      } else {
		$("#myBtn1").hide();
		$("#formProcessGif").show();
        $.post(url,{ post_type: "b", tid: thread_id.val(), post_body: post_body.val(), fsID: fs_id.val(), fsTitle: fs_title.val(), uid: u_id.val(), upass: u_pass.val() } , function(data) {
			  
        		//how u get the user that just replied to see their response
			   $("#none_yet_div").hide();
			   var MattDiv = document.getElementById('postz');
			   var ajaxdiv1 = document.createElement('div');
			   ajaxdiv1.setAttribute("class", "response_top_div");
			   ajaxdiv1.htmlContent = 'Re: <?php echo $thread_title ?>';
			   ajaxdiv1.innerHTML = 'Re: <?php echo $thread_title ?>';
			   MattDiv.appendChild(ajaxdiv1);
			   var ajaxdiv = document.createElement('div');
			   ajaxdiv.setAttribute("class", "response_div");
			   ajaxdiv.htmlContent = data;
			   ajaxdiv.innerHTML = data;
			   MattDiv.appendChild(ajaxdiv);
			   $('#reponse_form').slideUp("fast");
			   document.responseForm.post_body.value='';
			   $("#formProcessGif").hide();
			   $("#myBtn1").show();
         }); 
	  }
}

</script>

</head>

<body>

	<header> 
		<div class="login-top">
			<div id="logged-in">
				<span id="welcome-name"></span>
				<form method="POST" action="../cgi-bin/logout.py">
					<input type="submit" value="Log out"/>
				</form>
			</div>
			<div id="logged-out">
				<form method="POST" action="../cgi-bin/login.py">
					Username: <input type="text" name="username" required/> 
					Password: <input type="password" name="password" required/>
					<input type="submit" value="Log in!"/>
				</form>
				<a href="../create-account.php">Create Account</a>
			</div>
			<script type="text/javascript">
				showHeader();
			</script>
		</div>
	</header>
	<nav><!-- All Top-level .html files should have exactly the same header contents -->
		<ul>
			<li>
				<a href="../index.php">Home</a>
			</li>
			<li>
				<a class="current" href="forum.php">Forum</a>
			</li>	
			<li>
				<a href="../wiki.php">Wiki</a>
			</li>
			<li>
				<a href="../about.php">About</a>
			</li>
			<li>
				<a href="../user-account.php">User Account</a>
			</li>
		</ul>
	</nav>

<table style="background-color: #F0F0F0; border:#069 1px solid; border-top:none;" width="900" border="0" align="center" cellpadding="12" cellspacing="0">

  <tr>

    <td width="731" valign="top" style="line-height:1.5em;">

    <div id=""><a href="">Home</a> &larr; <a href="">Forum Home</a> &larr; <a href="section.php?id=<?php echo $section_id; ?>"><?php echo $section_title; ?></a></div>

    <br />    

    <span class="topicTitles"><?php echo $thread_title; ?></span><br /><br />

    Topic Started By: <a href="../profile.php?id=<?php echo $post_author_id; ?>"><?php echo $post_author; ?></a>

    &nbsp; &nbsp; &nbsp; Created: <span class="topicCreationDate"><?php echo $date_time; ?></span>

    

    <div class="topic_div"><?php echo $post_body; ?></div>

<div id="postz"><?php echo $responses; ?></div>



<!-- START DIV that contains the form -->

<div id="reponse_form" style="display:none; background-color: #BAE1FE; border:#06C 1px solid; padding:16px;">

<form action="javascript:parseResponse();" name="responseForm" id="responseForm" method="post">

    Please type in your response here <?php echo $u_name; ?>:<br /><textarea name="post_body" id="post_body" cols="64" rows="12" style="width:98%;"></textarea>

    <div id="formError" style="display:none; padding:16px; color:#F00;"></div>

    <br /><br /><input name="myBtn1" id="myBtn1" type="submit" value="Submit Your Response" style="padding:6px;" /> <span id="formProcessGif" style="display:none;"><img src="../images/loading.gif" width="28" height="10" alt="Loading" vspace="2" hspace="48" /></span>

    or <a href="#" onclick="return false" onmousedown="javascript:toggleForm('reponse_form');">Cancel</a>

    <input name="thread_id" id="thread_id" type="hidden" value="<?php echo $thread_id; ?>" />

    <input name="forum_section_id" id="forum_section_id" type="hidden" value="<?php echo $section_id; ?>" />

    <input name="forum_section_title" id="category_name" type="hidden" value="<?php echo $section_title; ?>" />

    <input name="member_id" id="member_id" type="hidden" value="<?php echo $_SESSION['id']; ?>" />

    <input name="member_password" id="password" type="hidden" value="<?php echo $_SESSION['password']; ?>" />

</form>

</div>

<!-- END DIV that contains the form -->

<?php echo $replyButton; ?>

<br />
<br />
</td>
    <td width="189" valign="top"><div style=" width:160px; height:600px; background-color: #999; color: #CCC; padding:12px;"> <br />
      <br />
      <br />
      <h3>Ad Space or Whatever</h3>
    </div></td>
  </tr>
</table>

</body>

</html>