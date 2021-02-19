<?php
	require 'simple_html_dom.php';
	
	$conn = new mysqli("localhost", "root", "", "weather_forecast");
	/* проверка соединения */
	if (mysqli_connect_errno()) {
		echo '<script>
				console.log("Не удалось подключиться к MySQL: '.mysqli_connect_error().' ");
			  </script>';
		exit();
	}
	
	function sqlQuery($conn, $sqlAdd) {		
		if (mysqli_query($conn, $sqlAdd)) {
			echo '</br>
				  <script>
					console.log("New record created successfully");
				  </script>';
		} else {
			echo '</br>
				  <script>
					console.log("Error: '.$sqlAdd.'\n'.mysqli_error($conn).'");
				  </script>';
		}
	}
	
	function sqlAddGeoPosition($country, $region, $locality, $street) {
		$str = "INSERT INTO `geography_position` (`id`, `country`, `region`, `locality`, `street`) VALUES (NULL, '";
		$str .= $country;
		$str .= "', '";
		$str .= $region;
		$str .= "', '";
		$str .= $locality;
		$str .= "', '";
		$str .= $street;
		$str .= "')";
		return $str;
	}
	//$sqlAddGeo1 = sqlAddGeoPosition('USA', 'California', 'San Mateo', 'San Francisco International Airport');	
	function sqlAddLocation($name, $id_geography_position) {
		$str = "INSERT INTO `location` (`id`, `name`, `id_geography_position`) VALUES (NULL, '";
		$str .= $name;
		$str .= "', ";
		$str .= $id_geography_position;
		$str .= ");";
		return $str;
	}	
	//$sqlAddLoc1 = sqlAddLocation('AirportStation', 1);
	function sqlAddStatus($status) {
		$str = "INSERT INTO `status` (`id`, `status`) VALUES (NULL, '";
		$str .= $status;
		$str .= "');";
		return $str;
	}
	//$sqlAddStat2 = sqlAddStatus('Inactive');
	function sqlAddAcType($type) {
		$str = "INSERT INTO `account_types` (`id`, `type`) VALUES (NULL, '";
		$str .= $type;
		$str .= "');";
		return $str;
	}
	//$sqlAddAcType2 = sqlAddAcType('Admin');
	function sqlAddUser($full_name, $id_account_types, $id_status) {
		$str = "INSERT INTO `user` (`id`, `full_name`, `id_account_types`, `id_status`) VALUES (NULL, '";
		$str .= $full_name;
		$str .= "', ";
		$str .= $id_account_types;
		$str .= ", ";
		$str .= $id_status;
		$str .= ");";
		return $str;
	}
	//$sqlAddUser1 = sqlAddUser('Sugak, Gleb', 2, 1);
	function sqlAddIndType($name, $unit_of_measurement) {
		$str = "INSERT INTO `types_of_indicators` (`id`, `name`, `unit_of_measurement`) VALUES (NULL, '";
		$str .= $name;
		$str .= "', '";
		$str .= $unit_of_measurement;
		$str .= "');";
		return $str;
	}
	
	//$sqlAddUser1 = sqlAddUser('Sugak, Gleb', 2, 1);
	function sqlAddWithdrawal($id_location, $data_and_time, $id_indicator, $indicator_value) {
		$str = "INSERT INTO `withdrawal_of_indicators` (`id`, `id_location`, `data_and_time`, `id_indicator`, `indicator_value`) VALUES (NULL, '";
		$str .= $id_location;
		$str .= "', '";
		$str .= $data_and_time;
		$str .= "', '";
		$str .= $id_indicator;
		$str .= "', '";
		$str .= $indicator_value;
		$str .= "');";
		return $str;
	}
	
	function localArchDate1($data) {		
		$healthy = array("р.", "<br>", "<br />", "nbsp;");
		$yummy   = array("", "", "", "");	
		$newphrase = str_replace($healthy, $yummy, $data);
		$dataY = substr($newphrase, 0, 4);
		$GLOBALS['Year'] = $dataY;
		$pos = strpos($newphrase, '&');
		$dataD = substr($newphrase, 4, $pos-4);
		$pos2 = strpos($newphrase, ',');
		$dataM = substr($newphrase, $pos+1, $pos2-$pos-1);
				
		if(strlen ($dataD)==1) {
			$dataD = '0'.$dataD;
		}
		
		if(strcmp ($dataM, 'травня')==0) {
			$dataM = '05';
		} else if (strcmp ($dataM, 'березня')==0) {		
			$dataM = '03';
		} else if (strcmp ($dataM, 'квітня')==0) {		
			$dataM = '04';
		}
		return $dataY."-".$dataM."-".$dataD;	
	}
	function localArchDate2($data) {		
		$healthy = array("<br>", "nbsp;");
		$yummy   = array("", "");	
		$newphrase = str_replace($healthy, $yummy, $data);
		$DataY = $GLOBALS['Year'];
		$pos = strpos($newphrase, '&');
		$dataD = substr($newphrase, 0, $pos);
		$pos2 = strpos($newphrase, ',');
		$dataM = substr($newphrase, $pos+1, $pos2-$pos-1);		
		
		if(strlen ($dataD)==1) {
			$dataD = '0'.$dataD;
		}		
		if(strcmp ($dataM, 'квітня')==0) {
			$dataM = '04';
		} else if (strcmp ($dataM, 'березня')==0) {		
			$dataM = '03';
		}
		return $DataY."-".$dataM."-".$dataD;	
	}
	function localArchDateH($data) {		
		$healthy = array('<div class="dfs">', '</div>');
		$yummy   = array("", "");	
		$newphrase = str_replace($healthy, $yummy, $data);
		
		return $newphrase;	
	}
	function localArchDateT($data) {
		$data2 = substr($data, 1);
		$pos1 = strpos($data2, '>');
		$pos2 = strpos($data2, '<');	
		$data3 = substr($data2, $pos1+1, $pos2-$pos1-1);	
		return $data3;	
	}
	function localArchDateHM($data) {		
		$healthy = array('<div class="dfs">', '</div>');
		$yummy   = array("", "");	
		$newphrase = str_replace($healthy, $yummy, $data);
		
		return $newphrase;	
	}
	function localArchDatePR($data) {
		$data2 = substr($data, 1);
		$pos1 = strpos($data2, '>');
		$pos2 = strpos($data2, '<');	
		$data3 = substr($data2, $pos1+1, $pos2-$pos1-1);	
		return $data3;	
	}
	function localArchDateWD($data) {
		$pos1 = mb_strpos($data, 'Вітер');
		if(strlen($pos1)==0) {
			$data2 = 'Штиль';
		} else {
			$pos2 = mb_strpos($data, 'зі ');
			if(strlen($pos2)==0) {
				$pos3 = mb_strpos($data, 'з');
				$data2 = mb_substr($data, $pos3+2);
				$pos4 = mb_strpos($data2, '-');
				$pos5 = mb_strpos($data2, ' ');
			} else {
				$data2 = mb_substr($data, $pos2+3);
				$pos4 = mb_strpos($data2, '-');
				$pos5 = mb_strpos($data2, ' ');
			}
			$pos4n = strlen($pos4);
			$pos5n = strlen($pos5);
			if($pos4n==0) {
				if($pos5n==0) {				
					$pos6 = mb_strpos($data, 'заход');
					$pos7 = mb_strpos($data, 'ч');
					$pos8 = mb_strpos($data, 'вд');
					$pos9 = mb_strpos($data, 'сход');
					if(strlen($pos6)>0) {
						$data2 = 'Західний';
					} else if(strlen($pos7)>0) {
						$data2 = 'Північний';
					} else if(strlen($pos8)>0) {
						$data2 = 'Південний';
					} else if(strlen($pos9)>0) {
						$data2 = 'Східний';
					}
				}
			}
			if($pos4n>0) {
				$dataP1 = mb_substr($data2, 0, $pos4);
				$dataP2 = mb_substr($data2, $pos4+1, $pos5);
				$dataP3 = mb_substr($data2, $pos5+1);		
			
				$pos10 = mb_strpos($dataP1, 'заход');
				$pos11 = mb_strpos($dataP1, 'ч');
				$pos12 = mb_strpos($dataP1, 'вд');
				$pos13 = mb_strpos($dataP1, 'сход');
			
				$pos14 = mb_strpos($dataP2, 'заход');
				$pos15 = mb_strpos($dataP2, 'ч');
				$pos16 = mb_strpos($dataP2, 'вд');
				$pos17 = mb_strpos($dataP2, 'сход');
				
				$pos18 = mb_strpos($dataP3, 'заход');
				$pos19 = mb_strpos($dataP3, 'ч');
				$pos20 = mb_strpos($dataP3, 'вд');
				$pos21 = mb_strpos($dataP3, 'сход');
			
				if(strlen($pos11)>0&&strlen($pos15)>0&&strlen($pos21)>0) {
					$data2 = 'Північно-північно-східний';
				} else if(strlen($pos13)>0&&strlen($pos15)>0&&strlen($pos21)>0) {
					$data2 = 'Східно-північно-східний';
				} else if(strlen($pos13)>0&&strlen($pos16)>0&&strlen($pos21)>0) {
					$data2 = 'Східно-південно-східний';
				} else if(strlen($pos12)>0&&strlen($pos16)>0&&strlen($pos21)>0) {
					$data2 = 'Південно-південно-східний';
				} else if(strlen($pos12)>0&&strlen($pos16)>0&&strlen($pos18)>0) {
					$data2 = 'Південно-південно-західний';
				} else if(strlen($pos10)>0&&strlen($pos16)>0&&strlen($pos18)>0) {
					$data2 = 'Західно-південно-західний';
				} else if(strlen($pos10)>0&&strlen($pos15)>0&&strlen($pos18)>0) {
					$data2 = 'Західно-північно-західний';
				} else if(strlen($pos11)>0&&strlen($pos15)>0&&strlen($pos18)>0) {
					$data2 = 'Північно-північно-західний';
				}
			} else if($pos5n>0){
				$dataP1 = mb_substr($data2, 0, $pos5);
				$dataP3 = mb_substr($data2, $pos5+1);
				
				$pos11 = mb_strpos($dataP1, 'ч');
				$pos12 = mb_strpos($dataP1, 'вд');
				
				$pos18 = mb_strpos($dataP3, 'заход');
				$pos21 = mb_strpos($dataP3, 'сход');
				
				if(strlen($pos11)>0&&strlen($pos21)>0) {
					$data2 = 'Північно-східний';
				} else if(strlen($pos12)>0&&strlen($pos21)>0) {
					$data2 = 'Південно-східний';
				} else if(strlen($pos12)>0&&strlen($pos18)>0) {
					$data2 = 'Південно-західний';
				} else if(strlen($pos11)>0&&strlen($pos18)>0) {
					$data2 = 'Північно-західний';
				}			
			}
		}
		
		return $data2;	
	}	
	function localArchDateWS($data) {
		$pos1 = mb_strpos($data, '(');
		$pos2 = mb_strpos($data, 'м/с');
		if(strlen($pos2)==0) {
			$data2 = 0;
		} else {
			$data2 = mb_substr($data, $pos1+1, $pos2-$pos1-2);
			$pos2 = mb_strpos($data2, 'м/с');		
		}
		return $data2;	
	}
	function localArchDatePC($data) {		
		$data2 = substr($data, 1);
		$pos1 = strpos($data2, '>');
		$pos2 = strpos($data2, '<');	
		$data3 = substr($data2, $pos1+1, $pos2-$pos1-1);		
		$pos3 = mb_strpos($data3, '.');		
		$pos4 = mb_strpos($data3, 'ліди');
		if(strlen($pos3)==0&&strlen($pos4)==0) {
			$data = 'Без опадів';
		}
		return $data;	
	}
	function sqlAddWithdrS($conn, $data_and_time, $tempr, $humdt, $presu, $wdir, $wspee, $precip) {		
		$id_location = 3;
		$sqlAddWithdrawal1 = sqlAddWithdrawal($id_location, $data_and_time, 1, $tempr);
		$sqlAddWithdrawal2 = sqlAddWithdrawal($id_location, $data_and_time, 2, $humdt);
		$sqlAddWithdrawal3 = sqlAddWithdrawal($id_location, $data_and_time, 3, $presu);
		$sqlAddWithdrawal4 = sqlAddWithdrawal($id_location, $data_and_time, 4, $wdir);
		$sqlAddWithdrawal5 = sqlAddWithdrawal($id_location, $data_and_time, 5, $wspee);
		$sqlAddWithdrawal6 = sqlAddWithdrawal($id_location, $data_and_time, 6, $precip);
		$sqlSelect = 'SELECT data_and_time FROM `withdrawal_of_indicators` ORDER BY id DESC LIMIT 1';
		$result = $conn->query($sqlSelect);
		
		$date1 = date_create();
		date_date_set($date1, intval(substr($data_and_time,0,4)), intval(substr($data_and_time,5,2)), intval(substr($data_and_time,8,2)));
		
		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {				
				if (strtotime($data_and_time) > strtotime($row["data_and_time"])) {
					sqlQuery($conn, $sqlAddWithdrawal1);
					sqlQuery($conn, $sqlAddWithdrawal2);
					sqlQuery($conn, $sqlAddWithdrawal3);
					sqlQuery($conn, $sqlAddWithdrawal4);
					sqlQuery($conn, $sqlAddWithdrawal5);
					sqlQuery($conn, $sqlAddWithdrawal6);
				} else {
					$starsh = 100;
				}
			}
		} else {
			sqlQuery($conn, $sqlAddWithdrawal1);
			sqlQuery($conn, $sqlAddWithdrawal2);
			sqlQuery($conn, $sqlAddWithdrawal3);
			sqlQuery($conn, $sqlAddWithdrawal4);
			sqlQuery($conn, $sqlAddWithdrawal5);
			sqlQuery($conn, $sqlAddWithdrawal6);
		}
	}

	if(count($_REQUEST['weather'])>0) {
		$i = 0;
		while ($i <= count($_REQUEST['weather'])) {
			$pos1 = strpos($_REQUEST['weather'][$i], ' ');
			$ffT1 = substr($_REQUEST['weather'][$i], $pos1+1);
			$pos2 = strpos($ffT1, ' ');
			$ffT2 = substr($_REQUEST['weather'][$i], $pos1+$pos2+2);
			$pos3 = strpos($ffT2, ' ');
			$ffT3 = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+3);
			
			$pos4 = strpos($ffT3, ' ');
			$ffT4 = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+4);
			
			$pos5 = strpos($ffT4, ' ');
			$ffT5 = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+$pos5+5);
			
			$pos6 = strpos($ffT5, ' ');
			$ffT6 = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+$pos5+$pos6+6);
			
			$pos7 = strpos($ffT6, ' ');
			$ffT7 = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+$pos5+$pos6+$pos7+7);
			
			$pos8 = strpos($ffT7, ' ');
			$ffT8 = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+$pos5+$pos6+$pos7+$pos8+8);
			
			$pos9 = strpos($ffT8, ' ');
			$ffT9 = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+$pos5+$pos6+$pos7+$pos8+$pos9+9);
			
			$pos10 = strpos($ffT9, ' ');
			$ffT10 = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+$pos5+$pos6+$pos7+$pos8+$pos9+$pos10+10);
			
			$pos11 = strpos($ffT10, ' ');
			$ffT11 = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+$pos5+$pos6+$pos7+$pos8+$pos9+$pos10+$pos11+11);
			
			$data_and_time = substr($_REQUEST['weather'][$i], 0, $pos1+$pos2+2);
			
			$tempr = substr($_REQUEST['weather'][$i], $pos1+$pos2+2, $pos3);
			$humdt = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+3, $pos4);
			$presu  = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+4, $pos5);
			$wdir = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+$pos5+5, $pos6);
			$wspee = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+$pos5+$pos6+6, $pos7);
			$precip = substr($_REQUEST['weather'][$i], $pos1+$pos2+$pos3+$pos4+$pos5+$pos6+$pos7+7);
			sqlAddWithdrS($conn, $data_and_time, $tempr, $humdt, $presu, $wdir, $wspee, $precip);
			$i++;
		}
	}	
	
	$base = 'https://rp5.ua/%D0%90%D1%80%D1%85%D1%96%D0%B2_%D0%BF%D0%BE%D0%B3%D0%BE%D0%B4%D0%B8_%D1%83_%D0%92%D1%96%D0%BD%D0%BD%D0%B8%D1%86%D1%96';

	$curl = curl_init();
	
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_URL, $base);
	curl_setopt($curl, CURLOPT_REFERER, $base);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	$str = curl_exec($curl);
	curl_close($curl);

	// Create a DOM object
	$html_base = new simple_html_dom();
	// Load HTML from a string
	$html_base->load($str); 

	$tr_n = count($html_base->getElementById('archiveTable', 0)->getElementsByTagName('tr'));
	$i = 1;
	$iR = 1;
	$weather = array();
	while ($i < $tr_n) {
		$trs = $html_base->find('#archiveTable', 0)->find('tr', $i);
		$dateTR= $trs->find('td', 0)->innertext;
		$rows = $trs->find('td', 0)->getAttribute('rowspan');
		if($i==1) {
			$dateG = localArchDate1($dateTR);
			$iR=$iR+$rows;
			$dateHtr = $trs->find('td', 1)->innertext;
			$dateH = localArchDateH($dateHtr);
			$dateTtr = $trs->find('td', 2)->innertext;
			$dateT = localArchDateT($dateTtr);
			$dateHMtr = $trs->find('td', 6)->innertext;
			$dateHM = localArchDateHM($dateHMtr);
			$datePRtr = $trs->find('td', 3)->innertext;
			$datePR = localArchDatePR($datePRtr);
			$dateWDtr = $trs->find('td', 7)->innertext;
			$dateWD = localArchDateWD($dateWDtr);
			$dateWStr = $trs->find('td', 8)->innertext;
			$dateWS = localArchDateWS($dateWStr);
			$datePCtr = $trs->find('td', 24)->innertext;
			$datePC = localArchDatePC($datePCtr);
		}else if(strlen($rows)>0) {
			$dateG = localArchDate2($dateTR);
			$iR=$iR+$rows;
			$dateHtr = $trs->find('td', 1)->innertext;
			$dateH = localArchDateH($dateHtr);
			$dateTtr = $trs->find('td', 2)->innertext;
			$dateT = localArchDateT($dateTtr);
			$dateHMtr = $trs->find('td', 6)->innertext;
			$dateHM = localArchDateHM($dateHMtr);
			$datePRtr = $trs->find('td', 3)->innertext;
			$datePR = localArchDatePR($datePRtr);
			$dateWDtr = $trs->find('td', 7)->innertext;
			$dateWD = localArchDateWD($dateWDtr);
			$dateWStr = $trs->find('td', 8)->innertext;
			$dateWS = localArchDateWS($dateWStr);
			$datePCtr = $trs->find('td', 24)->innertext;
			$datePC = localArchDatePC($datePCtr);
		} else {
			$dateHtr = $trs->find('td', 0)->innertext;
			$dateH = localArchDateH($dateHtr);
			$dateTtr = $trs->find('td', 1)->innertext;
			$dateT = localArchDateT($dateTtr);
			$dateHMtr = $trs->find('td', 5)->innertext;
			$dateHM = localArchDateHM($dateHMtr);
			$datePRtr = $trs->find('td', 2)->innertext;
			$datePR = localArchDatePR($datePRtr);
			$dateWDtr = $trs->find('td', 6)->innertext;
			$dateWD = localArchDateWD($dateWDtr);
			$dateWStr = $trs->find('td', 7)->innertext;
			$dateWS = localArchDateWS($dateWStr);
			$datePCtr = $trs->find('td', 23)->innertext;
			$datePC = localArchDatePC($datePCtr);
		}
		$Gdate = $dateG.' '.$dateH.':00:00';
		array_unshift ($GLOBALS["weather"], array($Gdate, $dateT, $dateHM, $datePR, $dateWD, $dateWS, $datePC));
		$i++;
	}
	$i = 0;
	while ($i < count($GLOBALS['weather'])) {
		sqlAddWithdrS($conn, $GLOBALS["weather"][$i][0], $GLOBALS["weather"][$i][1], $GLOBALS["weather"][$i][2], $GLOBALS["weather"][$i][3], $GLOBALS["weather"][$i][4], $GLOBALS["weather"][$i][5], $GLOBALS["weather"][$i][6]);
		$i++;
	}
	$sqlSelect = 'SELECT indicator_value FROM `withdrawal_of_indicators` WHERE id_indicator=1 ORDER BY id DESC LIMIT 1';
	$result = $conn->query($sqlSelect);
	echo "<script> var temp = '",$result->fetch_assoc()["indicator_value"],"';</script>";
	
	$sqlSelect2 = 'SELECT indicator_value FROM `withdrawal_of_indicators` WHERE id_indicator=5 ORDER BY id DESC LIMIT 1';
	$result2 = $conn->query($sqlSelect2);
	echo "<script> var windsp = '",$result2->fetch_assoc()["indicator_value"],"';</script>";
	
	$sqlSelect3 = 'SELECT indicator_value FROM `withdrawal_of_indicators` WHERE id_indicator=2 ORDER BY id DESC LIMIT 1';
	$result3 = $conn->query($sqlSelect3);
	echo "<script> var humd = '",$result3->fetch_assoc()["indicator_value"],"';</script>";
	
	$sqlSelect4 = 'SELECT indicator_value FROM `withdrawal_of_indicators` WHERE id_indicator=3 ORDER BY id DESC LIMIT 1';
	$result4 = $conn->query($sqlSelect4);
	echo "<script> var press = '",$result4->fetch_assoc()["indicator_value"],"';</script>";
	
	$sqlSelect5 = 'SELECT indicator_value FROM `withdrawal_of_indicators` WHERE id_indicator=1 ORDER BY id DESC LIMIT 32';
	$result5 = $conn->query($sqlSelect5);
	while($row = $result5->fetch_assoc()) {
		echo "<script> tempr.unshift(Number(",$row["indicator_value"],"));</script>";
	}
	
	$sqlSelect6 = 'SELECT indicator_value FROM `withdrawal_of_indicators` WHERE id_indicator=2 ORDER BY id DESC LIMIT 32';
	$result6 = $conn->query($sqlSelect6);
	while($row = $result6->fetch_assoc()) {
		echo "<script> humdr.unshift(Number(",$row["indicator_value"],"));</script>";
	}
	
	$sqlSelect7 = 'SELECT indicator_value FROM `withdrawal_of_indicators` WHERE id_indicator=5 ORDER BY id DESC LIMIT 16';
	$result7 = $conn->query($sqlSelect7);
	while($row = $result7->fetch_assoc()) {
		echo "<script> windspr.unshift(Number(",$row["indicator_value"],"));</script>";
	}
	
	$conn->close();
?>

