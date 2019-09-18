<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Raspberry Pi Gpio</title>
    </head>
 
    <body style="background-color: black;">
	 <?php 

// Define your username and password 
$username = "admin"; 
$password = "pilife"; 

if ($_POST['txtUsername'] != $username || $_POST['txtPassword'] != $password) { 

?> 

<h1 style="color: white">Login</h1> 

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
    <p style="color: white"><label for="txtUsername">Username:</label> 
    <br /><input type="text" title="Enter your Username" name="txtUsername" /></p> 

    <p style="color: white"><label for="txtpassword">Password:</label> 
    <br /><input type="password" title="Enter your password" name="txtPassword" /></p> 

    <p><input type="submit" name="Submit" value="Login" /></p> 

</form> 

<?php 

} 
else { 

?> 
	 <?php
	 //this php script generate the first page in function of the gpio's status
	 $status = array (0, 0, 0, 0, 0, 0, 0, 0);
	 for ($i = 0; $i < count($status); $i++) {
		//set the pin's mode to output and read them
		system("gpio mode ".$i." out");
		exec ("gpio read ".$i, $status[$i], $return );
		//if off
		if ($status[$i][0] == 0 ) {
		echo ("<img id='button_".$i."' src='data/img/red/red_".$i.".jpg' alt='off'/>");
		}
		//if on
		if ($status[$i][0] == 1 ) {
		echo ("<img id='button_".$i."' src='data/img/green/green_".$i.".jpg' alt='on'/>");
		}	 
	 }
	 ?>
        
<?php 
    

} 

?>
	 
	 <!-- javascript -->
	 <script src="script.js"></script>
    </body>
</html>