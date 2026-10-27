<?php
    session_start();
    header("Content-type: text/xml");

    # Recall prior variable state.
    $id = $_SESSION['id'] ?? "";

    # Prevent data corruption to next application.
    if ($id == "sumer") {

        # Recall prior variable state;
        $d1 = $_SESSION['d1']; # percent of population starved per year on average
        $p1 = $_SESSION['p1']; # total people died during reign
        $z = $_SESSION['z'];  # year of reign
        $p = $_SESSION['p'];  # population
        $s = $_SESSION['s'];  # bushels in storage
        $h = $_SESSION['h'];  # harvest in bushels
        $e = $_SESSION['e'];  # bushels eaten by rats
        $y = $_SESSION['y'];  # yeild and rate (bushels/acre)
        $a = $_SESSION['a'];  # acres of land available to plant
        $i = $_SESSION['i'];  # immigrants to the city
        $q = $_SESSION['q'];  # acres to buy/sell or bushels to feed or plant
        $d = $_SESSION['d'];  # people died of starvation
		$f = $_SESSION['f'];  # quantity of bushesl to feed people
	
        $lastCmd = $_SESSION['cmd'] ?? 0;
        $mesg = "";
        }

    else {
        # Initialize program variables.
        $d1 = 0; $p1 = 0;
        $z = 1; $p = 100; $s = 2800; $h = 3000; $e = $h - $s;
        $y = 3; $a = $h / $y; $i = 5; $q = 1;
        $d = 0; $f = 0;
        
        # Determine the number of bushels per acre harvested.
        $c = mt_rand(0,9);
        $y = $c + 17;

        $mesg = "Hamurabi:  I beg to report...";

        # $cmd = 10;
        }
    

    # Get SoftKey selection and determine which action to take.
    $cmd = $_GET['key'] ?? 0;
    $q = $_GET['q'] ?? 0;


    # Prompt the user for information based on the key pressed.
    switch($cmd) {
        case 1 :
            $mesg = "How many do you wish to buy?";
            $units = "Acres";
            break;
            
        case 2 :
            $units = "Acres";
            $mesg = "How many do you wish to sell?";
            break;
            
        case 3 :
            $units = "Bushels";
            $mesg = "How many to distribute as food?";
            break;
            
        case 4 :
            $units = "Acres";
            $mesg = "How many do you wish to plant?";
            break;
    }
    
    
    # Process user input to buy, sell, feed or plant, then do calculations.
    switch($lastCmd) {
        case 1 : # How many acres to buy?
            if ($y * $q > $s) {
                $mesg = "You don't have enough bushels!";
                break;
            }
            
            if ($q == 0) {
                $cmd = 2;
                $units = "Acres";
                $mesg = "How many do you wish to sell?";
            }
            
            $a += $q;
            $s -= ($y * $q);
            $c = 0;

            break;
            
        case 2 : # How many acres to sell?
            if ($q >= $a) {
                $mesg = "You don't own enough acres!";
                break;
            }

            $a -= $q;
            $s += ($y * $q);
            $c = 0;

            break;
            
        case 3 : # How many to distribute as food?
            if ($q > $s) {
                $mesg = "You don't have enough bushels!";
                break;
            }
            
            $s = $s - $q + $f;
            $c = 1;
            $f = $q;

            break;
            
        case 4 : # How many do you wish to plant?
            if ($q == 0)
                break;
            
            if ($q > $a) {
                $mesg = "You don't own enough acres!";
                break;
            }
            
            if (floor($q / 2) > $s) {
                $mesg = "You don't have enough bushels!";
                break;
            }
            
            if ($q >= 10 * $p) {
                $mesg = "Not enough people to tend crops!";
                break;
            }
            
            $s -= floor($q / 2);
            
            
            # Calculate the numbers for next year.
            
            $mesg = "Hamurabi: I beg to report...";

            # Re-assign variables to match original code.
            $d = $q;
            $q = $f;
            $f = 0;
            
            # A bountyfull harvest!
            $c = mt_rand(1,5);
            
            $y = $c;
            $h = $d * $y;
            $e = 0;
            
            # Do rats consume some of the bushels?
            $c = mt_rand(1,5);
            
            if (floor($c / 2) == $c / 2)
                $e = floor($s / $c);
            
            $s += -$e +$h;
            
            # Lets have some babies.
            $c = mt_rand(1,5);
            $i = floor($c * (20 * $a + $s) / $p / 100 + 1);
            
            # How many people had full tummies?
            $c = floor($q / 20);
            
            # Horrors, a 15% chance of plague.
            $q = floor(10 * (2 * mt_rand() / mt_getrandmax() - 0.3));
    
            # If everyone had food, no one starved.
            if ($p < $c) {
                $d = 0;
            }
            else {
                # Starve too many people for impeachment?
                $d = $p - $c;
                if ($d > 0.45 * $p) {
                    $mesg = "$d starved. You're impeached!";
					$cmd = -1;
				
					# Clear session id to restart game.
                    $id = "";
                }
            
                $p1 = floor((($z - 1) * $p1 + $d * 100 / $p) / $z);
                $p = $c;
                $d1 += $d;
            }
            
            $z++;
            
            $p += $i;
            if ($q == 0) {
                $p = floor($p / 2);
                $mesg = "A horrible plague struck!";
            }
            
            # Determine the trading rate of bushels per acre.
            $c = mt_rand(0,9);
            $y = $c + 17;
            
            # Determine rating after 10-year reign has completed.
            if ($z > 10) {
                $cmd = 5;
                $z--;

                $l = round($a / $p);
				$q = abs(1000 - $a);

                if ($a > 1000)
                    $text = ", and added $q acres of land.";
                elseif ($a < 1000)
                    $text = ", and lost $q acres of land.";
                else
                    $text = ".";
                
                if (($p1 > 33) || ($l < 7))
                    $mesg = "You've been impeached!";
                
                elseif (($p1 > 10) || ($l < 9))
                    $mesg = "Heavy-handed performance!";
                
                elseif (($p1 > 3) || ($l < 10))
                    $mesg = "Could have been better!";
                
                else
                    $mesg = "Fantastic performance!";
            }
    }
    
    
    # Update IP Phone display.
	switch($cmd) {
        case -1 : 
		
        case 0 :
            echo "<CiscoIPPhoneText>";
            echo "  <Title>Sumer  -  Year $z</Title>";
            echo "  <Text>";
            
            if ($z > 1)
                echo "$d starved. ";
            
            echo "You have $p people, $a acres, ";
            echo "and $s bushels, trading at $y ";
            echo "bushels/acre.";

            # echo "You have $p people, $a acres,\n";
            # echo "and $s bushels. Land is trading\n";
            # echo "at $y bushels/acre.";
            
            echo "  </Text>";
            
            if ($cmd == 0) {
                echo "  <SoftKeyItem>";
                echo "	<Name>Buy/Sell</Name>";
                echo "	<URL>http://diskstation/cisco/sumer.php?key=1</URL>";
                echo "	<Position>1</Position>";
                echo "  </SoftKeyItem>";
            
                echo "  <SoftKeyItem>";
                echo "    <Name>Feed</Name>";
                echo "	<URL>http://diskstation/cisco/sumer.php?key=3</URL>";
                echo "    <Position>2</Position>";
                echo "  </SoftKeyItem>";
            
                echo "  <SoftKeyItem>";
                echo "    <Name>Plant</Name>";
                echo "	<URL>http://diskstation/cisco/sumer.php?key=4</URL>";
                echo "    <Position>3</Position>";
                echo "  </SoftKeyItem>";
            }
            
            echo "  <SoftKeyItem>";
            echo "    <Name>Exit</Name>";
            echo "    <URL>SoftKey:Exit</URL>";
            echo "    <Position>4</Position>";
            echo "  </SoftKeyItem>";
            
            echo "  <Prompt>".$mesg."</Prompt>";
            echo "</CiscoIPPhoneText>"; 
            
            break;
            
        case 1 :
        case 2 :
        case 3 :
        case 4 :
            echo "<CiscoIPPhoneInput>";
            echo "  <Title>Sumer - Year $z</Title>";
            echo "  <URL method=\"post\">http://diskstation/cisco/sumer.php</URL>";
            
            echo "  <InputItem>";
            echo "    <DisplayName>$units</DisplayName>";
            echo "    <QueryStringParam>q</QueryStringParam>";
            echo "    <DefaultValue> </DefaultValue>";
            echo "    <InputFlags>N</InputFlags>";
            echo "  </InputItem>";
            
            echo "  <SoftKeyItem>";
            echo "	<Name>Enter</Name>";
            echo "	<URL>SoftKey:Submit</URL>";
            echo "	<Position>1</Position>";
            echo "  </SoftKeyItem>";
            
            echo "  <SoftKeyItem>";
            echo "    <Name>Cancel</Name>";
            echo "    <URL>http://diskstation/cisco/sumer.php?key=0</URL>";
            echo "    <Position>3</Position>";
            echo "  </SoftKeyItem>";
            
            echo "  <Prompt>$mesg</Prompt>";
            echo "</CiscoIPPhoneInput>";
            
            break;
            
        case 5 :
            echo "<CiscoIPPhoneText>";
            echo "  <Title>Sumer  -  $z Year Report</Title>";
            echo "  <Text>";
            echo "$p1% starved per year. ";
            echo "You now have $l acres per person$text";
            echo "  </Text>";
            
            echo "  <SoftKeyItem>";
            echo "    <Name>Exit</Name>";
            echo "    <URL>SoftKey:Exit</URL>";
            echo "    <Position>4</Position>";
            echo "  </SoftKeyItem>";
            
            echo "  <Prompt>".$mesg."</Prompt>";
            echo "</CiscoIPPhoneText>";

			break;

		case 10 :
            echo "<CiscoIPPhoneText>";
            echo "  <Title>Sumer</Title>";
            echo "  <Text>";
            echo "Try  your  hand  at  governing\n";
            echo "Ancient  Sumeria  for  a  10-year\n";
            echo "term  of  office.\n";
			echo "  </Text>";

            echo "  <SoftKeyItem>";
            echo "    <Name>Next</Name>";
            echo "    <URL>SoftKey:Update</URL>";
            echo "    <Position>1</Position>";
            echo "  </SoftKeyItem>";
            
            echo "  <SoftKeyItem>";
            echo "    <Name>Exit</Name>";
            echo "    <URL>SoftKey:Exit</URL>";
            echo "    <Position>3</Position>";
            echo "  </SoftKeyItem>";
            
            echo "  <Prompt></Prompt>";
            echo "</CiscoIPPhoneText>";

            $cmd = 0;

            break;
    }
    
    # Save current variable state.
    $_SESSION['id'] = "sumer";
    $_SESSION['d1'] = $d1;
    $_SESSION['p1'] = $p1;
    $_SESSION['z'] = $z;
    $_SESSION['p'] = $p;
    $_SESSION['s'] = $s;
    $_SESSION['h'] = $h;
    $_SESSION['e'] = $e;
    $_SESSION['y'] = $y;
    $_SESSION['a'] = $a;
    $_SESSION['i'] = $i;
    $_SESSION['q'] = $q;
    $_SESSION['d'] = $d;
    $_SESSION['f'] = $f;
    $_SESSION['cmd'] = $cmd;

?>
