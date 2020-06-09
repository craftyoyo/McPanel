<?php

// config header

 
$Server = "localhost"; // The ip of the server you want to control.

$mcLogs = '/var/www/html/MC/logs/latest.log'; // relative path instead of full path




$name = "Horatio"; // The name of your server (Not used that much)




if((isset($_GET['func']))&&(function_exists($_GET['func'])))   //Called every time	
	{
	call_user_func($_GET['func']);
	}


function isup($server, $port) {
	
	$socket = @fsockopen("$server", "$port");
	
	if ($socket === false) {
		
		return false;
	
	} 
	else {
		
		return true;
	
	}
}

function srvUP() {
	
	global $Server, $name, $port;

	if (isup("$server",$port)) { 
		
		echo "<span class='badge badge-success'>Status : $name is ready to go !</span>"; //The server is considered booted if ssh is running
	
	}else 
	 {
		echo "<span class='badge badge-danger'>Status : $name is dead :'(</span>";
	}
}

//CPU Usage

$stat1 = file('/proc/stat'); 
sleep(1); 
$stat2 = file('/proc/stat'); 
$info1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0])); 
$info2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0])); 
$dif = array(); 
$dif['user'] = $info2[0] - $info1[0]; 
$dif['nice'] = $info2[1] - $info1[1]; 
$dif['sys'] = $info2[2] - $info1[2]; 
$dif['idle'] = $info2[3] - $info1[3]; 
$total = array_sum($dif); 
$cpu = array(); 
foreach($dif as $x=>$y) $cpu[$x] = round($y / $total * 100, 1);


function ssh($cmd) {
	$connection = ssh2_connect('localhost', 22);
ssh2_auth_password($connection, 'root', 'password');
ssh2_exec($connection, $cmd);
}

function mc() {
	global $mcLogs;
$output = shell_exec("tail -n100  {$mcLogs} | tac"); //exec removed
//echo str_replace(PHP_EOL, '<br />', $output); // line OK
$console = $output;
$console = preg_split('/[\r\n]+/', $output);


$console = array_slice($console, 0, 100); 
echo "<pre><code>";
foreach ($console as $console) {                                                                                                     
    $string_color = array(
				   "[Server thread/WARN]",
				   "Done",
				   "[Server thread/INFO",                                
                   "[Server-Worker-3/INFO]",
                   "[Server-Worker-2/INFO]",
                   "[Server-Worker-1/INFO]",
                   "[0;33;1m",
                   "[0;37;22m",
                   "[0;30;1m",
                   "",
                   "[m",
                   "[0;35;22m",
                   "[0;32;22m",
                   "[21m",
                   "[0;36;22m",
                   "[0;36;1m",
                   "[0;34;1m");
    $string_code = array(
			   '<span style="background-color: red; color: #ff; font-weight: 900;" >[Server thread/WARN]',
			   '<span style="color: #08F600;">Done',
			   '<span style="color: #FFF;">[Server thread/INFO',        
               '<span style="color: #FFF;">[Server-Worker-3/INFO]',
               '<span style="color: #fff;">[Server-Worker-2/INFO]',
               '<span style="color: #fff;">[Server-Worker-1/INFO]',
               '<span style="color: #FEF600;">',
               '<span style="color: #FFF;">',
               '<span style="color: #FFF;">',
               '<span style="color: #00faff;">',
               '<span style="color: #000;">',
               '<span style="color: #800080;">',
               '<span style="color: green;">',
               '<span style="color: white;">',
               '<span style="color: #00faff;">',
               '<span style="color: #00faff;">',
               '<span style="color: #0000DD;">');
               
    $console = str_replace($string_color, $string_code, $console);  
    echo "<div style='line-height:20px'>".$console."<br/></div>";   
}
echo "</code></pre>";
}

function status($a) {
	if ($a) {
		
		echo "<span class='badge badge-success'>The server is up</span>";
	
	}  
	else {
		
		echo "<span class='badge badge-danger'>The server is down</span>";
	}
}


function servicePWR() {
	
	global $user, $Server;
	
	if(isset($_GET['srv']) && isset($_GET['act'])){

			$srv = $_GET['srv'];
			$act = $_GET['act'];

			if ($srv == 'mc') {
				
				if($act == 'START') {

						//ssh2_exec($connection, 'sh /var/www/html/MC/start.sh');
						ssh("sh /var/www/html/MC/start.sh");
				}
				
				if($act == 'STOP') {

					ssh("sh /var/www/html/MC/stop.sh");
					//var_dump(ssh2_exec($connection, 'screen -S minecraft -p 0 -X stuff "stop^M"'));
				}

				if($act == 'ISUP') {

					$gmodAnswer = ssh("screen -ls minecraft | tail -n 2' 2>&1'"); 
					$socketmc = @fsockopen("localhost", "25565");
					if (isup("$Server","25565")) { //So dirty
						
						status(true);
					
					} 
					else {
						
						status(false);
					
					}

				}
			}
	}
}


function srvSENSORS() {
//CPU USAGE
	$stat1 = file('/proc/stat'); 
	sleep(1); 
	$stat2 = file('/proc/stat'); 
	$info1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0])); 
	$info2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0])); 
	$dif = array(); 
	$dif['user'] = $info2[0] - $info1[0]; 
	$dif['nice'] = $info2[1] - $info1[1]; 
	$dif['sys'] = $info2[2] - $info1[2]; 
	$dif['idle'] = $info2[3] - $info1[3]; 
	$total = array_sum($dif); 
	$cpu = array(); 
	foreach($dif as $x=>$y) $cpu[$x] = round($y / $total * 100, 1);
	// Again, this is coded for the v20z, but on any other server I guess you can replace "sensor get" with "lm-sensors" and use preg_match as well.
//RAM USAGE
$free = 0;

if (shell_exec('cat /proc/meminfo'))
{
    $free    = shell_exec('grep MemFree /proc/meminfo | awk \'{print $2}\'');
    $buffers = shell_exec('grep Buffers /proc/meminfo | awk \'{print $2}\'');
    $cached  = shell_exec('grep Cached /proc/meminfo | awk \'{print $2}\'');

    $free = (int)$free + (int)$buffers + (int)$cached;
}

// Total
if (!($total = shell_exec('grep MemTotal /proc/meminfo | awk \'{print $2}\'')))
{
    $total = 0;
}
$tot = $total /1024;
$freeB = $free /1024;

// Used
$used = $total - $free;

// Percent used
$percent_used = 0;
if ($total > 0)
	$percent_used = 100 - (round($free / $total * 100));
	$percent_free = (round($free / $total * 100));

	
	$sensor = shell_exec("sensors -A -j");
	$sens = json_decode($sensor, true);
	$loadtime = shell_exec("uptime");
	//echo "<h4> SP sensors : </h4>";
	
	echo  "<pre>";
	echo "====Load Avg.====\r";
	print_r($loadtime);
	echo  "</pre>";
	$cpu0 = $sens['coretemp-isa-0000']['Core 0']['temp2_input'];
	$cpu1 = $sens['coretemp-isa-0000']['Core 1']['temp3_input'];
	echoBar("CORE0 temp :", "$cpu0"," °C", "100");
	echoBar("CORE1 temp :", "$cpu1"," °C", "100");
	echo "<h4>CPU Usage</h4>";
	echoBar("User:", "$cpu[user]"," %", "100");
	echoBar("Sys:", "$cpu[sys]"," %", "100");
	echoBar("IDLE:", "$cpu[idle]"," %", "100");
	echo "<h4>Ram Usage</h4>";
	//echoBar("Used:", "100"," %", "100");
	echo "<label>Aviable:</label> <p class='text text-success'>$tot M</p>";
	echoBar("Used:", "$percent_used"," %", "100");
	echoBar("Free:", "$percent_free"," %", "100");
	

}

function echoBar($title, $str, $unit, $max){
	
	$percent = floor((($str/$max)*100));
	
	if($unit == " RPM") {
		
		if($percent<40) 
			{

				$color = "progress-bar-danger";
			}

		else 
		{ 
			if ($percent >= 40 && $percent<80) 
			{
				$color = "progress-bar-success";
			} 
			else 
			{
				$color ="progress-bar-info"; 
			}
		}

	}
	else 
	{
		if($unit == " °C") 
		{
		if($percent<40) 
			{
				$color = "progress-bar-success";
			} 
		else 
		{ 
			if ($percent >= 40 && $percent<80) 
			{
				$color = "progress-bar-info";
			} 
			else 
			{
				$color ="progress-bar-danger"; 
			}
		}

	}
	}
	echo "<label>$title</label>";

	echo "<div class='progress'>";
  	
  	echo "<div class='progress-bar $color' role='progressbar' aria-valuenow='$str' aria-valuemin='0' aria-valuemax='$max' style='width: ".$percent."%;'>";
    
    echo $str;
    
    echo $unit;
  	
  	echo "</div>";
	echo "</div>";
}


?>