<?php
require_once('src/snapchat.php');

#  ____                            ____                    
# / ___|___  _ __ ___  _ __  _   _/ ___| _ __   __ _ _ __  
#| |   / _ \| '_ ` _ \| '_ \| | | \___ \| '_ \ / _` | '_ \ 
#| |__| (_) | | | | | | |_) | |_| |___) | | | | (_| | |_) |
# \____\___/|_| |_| |_| .__/ \__,_|____/|_| |_|\__,_| .__/ 
#                     |_|                           |_|    
#                            Author: Ethan McMullan.

#Description: A Basic Snapchat Connector using API at: https://github.com/JorgenPhi/php-snapchat		

failte();

function failte(){
echo "Welcome to Snapchat.\n\n";
echo "Enter your username: ";
$username = trim(fgets(STDIN));
echo "Enter Your Password: ";
$password = trim(fgets(STDIN));
connect($username, $password);
}

function connect($username, $password){
 echo "Connecting to Snapchat ...\n\n";
  $snapchat = new Snapchat($username, $password);
   if($snapchat->username != null){
      echo "Connected to Snapchat!\n";
      welcome($snapchat);
      break;
    }else{
      echo "Username/Password combination incorrect.\n\n";
	  failte();
    }
}

function welcome($snapchat){
 echo "\nFunctions:";
 echo "\n[1] Check Your Snaps.\n[2] Send a Snap.\n[3] Other Functions.\n[4] Logout.";
 echo "\nWhat Would You Like to Do?: [1-4]: ";
  fscanf(STDIN, "%d\n", $i);
   if(is_numeric($i)){
      switch($i){
        case 1:
        checkSnaps($snapchat);
        break;

        case 2:
        send($snapchat);
        break;

        case 3:
        otherFunc($snapchat);
        break;

        case 4:
        $snapchat->logout();
        echo "\nSuccessfully logged out..";
        die();
        break;

        default:
        echo "\n Not a valid choice.";
        welcome($snapchat);
        break;
     }
    }
}

function otherFunc($snapchat){
echo "\n\n[1] Clear Feed.\n[2] Return to Functions.\n[3] Logout.\n";
echo "What Would You like to do? [1-3]: ";
 $in = trim(fgets(STDIN));
  switch($in){
     
	 case 1:
	 $snapchat->clearFeed();
	 echo"\nFeed cleared!";
	 otherFunc($snapchat);
	 break;
	 
	 case 2:
	 welcome($snapchat);
	 break;
	 
	 case 3:
	 $snapchat->logout();
	 echo"\nSuccessfully logged out!";
	 die();
	 break;
	 
	 default:
	 echo "\nNot a valid choice!";
	 otherFunc($snapchat);
	 break;
  }
}

function checkSnaps($snapchat){
 $snaps = $snapchat->getSnaps();
 $snapchatdata = "";
 $snapnames = "";
 $type = "";
 $status = "";
 $towho = "";
 $count = 0;
 $search = array('%u', '%l', '%t', '%a', '%s');
 $datastr = "Snap from: %u, To: %l, Type: %t, Available: %a, Status: %s \n";
 
 echo "\n\n\n\n[1] Download Available Snapchats and Mark Opened.\n[2] Download Available Snapchats and Keep Un-Opened.\n[3] Return to Functions.\n[4] Get Feed.\n[5] Logout.";
 echo "\n\nWhat would you like to do? [1-4]: ";
 $input = trim(fgets(STDIN));
  
  for($i = 0; $i <= sizeof($snaps); $i++){
     if(strpos($snaps[$i]->media_id, "-") == null){
        $snapchatdata .= $snaps[$i]->id . " ";
		$snapnames .= $snaps[$i]->sender . " ";
		$type .= $snaps[$i]->media_type . " ";
		$status .= $snaps[$i]->status . " ";
		$towho .= $snaps[$i]->recipient . " ";
     }
 }
   
 $data = explode(" ", $snapchatdata);
 $namedata = explode(" ", $snapnames);
 $stype = explode(" ", $type);
 $sstatus = explode(" ", $status);
 $recipient = explode(" ", $towho);
 
  switch($input){
      case 1:
	  echo "\n\n Attempting to save snapchats..\n\n";
	  echo "\n\n Please Note: Snaps may be downloaded more than once if they have not been marked opened.\n\n";
	    for($i = 0; $i <= sizeof($data); $i++){
            $save = $snapchat->getMedia(trim($data[$i]));
            if($save != false){
			if($stype[$i] == 1 | $stype[$i] == 2){
             file_put_contents('media/snap-' . strtolower($namedata[$i]) . "-" . substr($data[$i], 0, 5) . rand(1, 900) .  '.mov', $save);
			 $snapchat->markSnapViewed(trim($data[$i]));
             $count++;
			 }else{
			 file_put_contents('media/snap-' . strtolower($namedata[$i]) . "-" . substr($data[$i], 0, 5) . rand(1, 900) .  '.jpg', $save);
			 $snapchat->markSnapViewed(trim($data[$i]));
             $count++;
			 }
            }
        }
	  break;
	  
	  case 2:
	  echo "\n\n Attempting to save snapchats..\n\n";
	  echo "\n\n Please Note: Snaps may be downloaded more than once if they have not been marked opened.\n\n";
	    for($i = 0; $i <= sizeof($data); $i++){
            $save = $snapchat->getMedia(trim($data[$i]));
			if($save != false){
            if($stype[$i] == 1 | $stype[$i] == 2){
             file_put_contents('media/snap-' . strtolower($namedata[$i]) . "-" . substr($data[$i], 0, 5) . rand(1, 900) .  '.mov', $save);
             $count++;
			 }else{
			 file_put_contents('media/snap-' . strtolower($namedata[$i]) . "-" . substr($data[$i], 0, 5) . rand(1, 900) .  '.jpg', $save);
             $count++;
            }
			}
        }
	  break;
	  
	  case 3:
	  welcome($snapchat);
	  break;
	  
	  case 4:
	  for($i = 0; $i <= sizeof($data); $i++){
	      $tie = ($stype[$i] != 0 ? 'Video' : 'Image');
		  $name = $namedata[$i];
		  $avail = (strlen($data[$i]) > 5 ? 'Available' : 'Not Available');
		  $r = $recipient[$i];
		  $sls = "";
		  switch($sstatus[$i]){
		     
			 case Snapchat::STATUS_NONE:
			 $sls .= "None";
			 break;
			 
			 case Snapchat::STATUS_SENT:
			 $sls .= "Sent";
			 break;
			 
			 case Snapchat::STATUS_DELIVERED:
			 $sls .= "Delivered";
			 break;
			 
			 case Snapchat::STATUS_OPENED:
			 $sls .= "Opened";
			 break;
			 
			 case Snapchat::STATUS_SCREENSHOT:
			 $sls .= "Screenshot!";
			 break;
			 
			 default:
			 $sls .= "Unknown Status";
			 break;
		  }
              if(strlen($name) >= 3){
	          echo str_replace($search, array(ucfirst(strtolower($name)), ucfirst(strtolower($r)), $tie, $avail, $sls), $datastr);
			  }
	    }
	  checkSnaps($snapchat);
	  break;
	  
	  case 5:
	  $snapchat->logout();
	  echo "\nSuccessfully logged out.";
	  die();
	  break;
	  
	  default:
	  echo "\nNot a valid selection!\n";
	  checkSnaps($snapchat);
	  break;
  }  
 	
   if($count >= 1){
   echo "\n[$count] Snaps Downloaded and Saved.\n";
   echo "\n Would you like to return to main functions? [Y-N]: ";
   $stdin = strtoupper(trim(fgets(STDIN)));
        switch($stdin){
         case 'Y':
         welcome($snapchat);
         break;

         case 'N':
         $snapchat->logout();
         echo "\nSuccessfully logged out..";
         die();
         break;

         default:
         echo "\n\n Not a valid input..\n\n";
         welcome($snapchat);
         break;
        }
   }else{
   echo "\n\nNo Snaps to save!\n\n";
   welcome($snapchat);
   }
 }

function send($snapchat){
 echo"\n\n[1] Send Image.\n[2] Send Video.\n[3] Cancel.\n";
 echo"What Would You Like to Do? [1-3]: ";
 $iei = trim(fgets(STDIN));
 switch($iei){
  case 1:
  sendSnap($snapchat, 'image');
  break;
  
  case 2:
  sendSnap($snapchat, 'video');
  break;
  
  case 3:
  welcome($snapchat);
  break;
  
  default:
  echo "\nNot a valid selection";
  send($snapchat);
  break;
 }
}


function sendSnap($snapchat, $type){
echo ($type == 'video' ? "\nPlease Enter the name of your image file [Ensure file is in the same directory and in the format IMAGE.MOV]: " : "\nPlease Enter the name of your image file [Ensure file is in the same directory and in the format IMAGE.PNG/JPG]: ");
  $image = trim(fgets(STDIN));
  if(file_exists($image)){
  echo "\nUploading Media...";
  $typee = ($type == 'video' ? Snapchat::MEDIA_VIDEO : Snapchat::MEDIA_IMAGE);
  $id = $snapchat->upload($typee, file_get_contents($image));
  }else{
  echo "\n Media does not exist!\n";
  goInput($snapchat);
  }

  if($id != FALSE){
     echo "\nMedia Uploaded .. Media ID: " . $id . "\n";
  }else{
     echo "\Media was not uploaded...";
     send($snapchat);
  }

  echo "\nPlease Enter the person you would like to send this snap to: ";
  $n = trim(fgets(STDIN));
  if($typee == Snapchat::MEDIA_VIDEO){
  echo "\nSending Media .. Media ID: " . $id . " To: " . ucfirst($n) . "\n";
       $send = $snapchat->send($id, array($n));
  }else{
  echo "\nHow many seconds would you like to send this snap for? [1-10]: ";
    $seconds = trim(fgets(STDIN));
       echo "\nSending Media .. Media ID: " . $id . " To: " . ucfirst($n) . " For $seconds Seconds.\n";
       $send = $snapchat->send($id, array($n), intval($seconds));
   }

    if($send != FALSE){
       echo "\nMedia was sent successfully! .. Media ID: " . $id;
       echo "\nWould you like to send another snap? [Y/N]: ";
       $stdin = strtoupper(trim(fgets(STDIN)));
        switch($stdin){
         case 'Y':
         send($snapchat);
         break;

         case 'N':
         welcome($snapchat);
         break;

         default:
         echo "\n Not a valid input..";
         goInput($snapchat);
         break;
        }
    }else{
       echo "\nMedia was not sent!";
       goInput($snapchat);
       break;
    }
}


function goInput($snapchat){
 echo "\nWould you like to send another snap? [Y/N]: ";
  $stdin = strtoupper(trim(fgets(STDIN)));
     switch($stdin){
         case 'Y':
         send($snapchat);
         break;

         case 'N':
         welcome($snapchat);
         break;

         default:
         echo "\n Not a valid input..";
         goInput($snapchat);
         break;
    }
}
?>