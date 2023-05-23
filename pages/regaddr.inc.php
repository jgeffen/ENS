<?php

	if( isset($_SESSION['confnum']) ) {
		unset($_SESSION['confnum']);
		unset($_SESSION['address']);
		unset($_SESSION['newaccount']);
	}

	// Reset the title
	$TITLE = $newaddrheadertext;

	$mode = param('mode');
	#printf ("Mode is %s<br>", $mode);

	$newaccount = param('newaccount');
	$redirect = param('redirect');

	if($newaccount == 'y' || $redirect == 'y') {
		$redirect = true;
	}
	else {
		$redirect = false;
	}

	include("tabs.inc.php");

	if ($mode == "request") {
        validateCSRF();
        
		$r_email = trim(param('address'));
		// Check if the address contains a space
		if( strpos($r_email, ' ') !== false ) {
			print '<h3 class="alert error">Error: Email address cannot contain spaces.</h3>';
		}
		else {
			$r_replacement = intval(param('replacement'));
			if($r_replacement != 0 && $r_replacement != '')
				$r_doreplace = "Y";
			else
				$r_doreplace = "N";
			$r_format = param('format');
			if($r_format == '') {
				$r_format = "HTML";
			}
			$r_begin = timeresolve(intval(param('day_begin')) - $USER_INFO['timezone']);
			$r_end = timeresolve(intval(param('day_end')) - $USER_INFO['timezone']);
			$r_confnum = rand(99,999);
			$r_hconf = md5($r_confnum);
			$mailtext = sprintf($newaddremailconftext, $r_confnum);
			$headers = "Mime-Version: 1.0\r\n";
			$r_strippedemail = stripslashes($r_email);

			$is_phone = isPhone($r_strippedemail);
			if( $is_phone ) {
				$headers .= "Content-type: text/plain; charset=UTF-8 \r\n";
			}
			else {
				$headers .= "Content-type: text/html; charset=UTF-8 \r\n";
			}

			// Make sure this address is not already associated with their account
			$query_exists = $database->prepare("SELECT * FROM mailaddresses WHERE uid=:uid AND email=:email");
			$rs_exists = $query_exists->execute(array(
				':uid' => $USER_INFO['id'],
				':email' => $r_email
			));
			if (!$rs_exists) {
				$query_exists = $database->prepare("SELECT * FROM mailconfirm WHERE uid=:uid AND email=:email");
				$rs_exists = $query_exists->execute(array(
					':uid' => $USER_INFO['id'],
					':email' => $r_email
				));

				/**
				 * 10/04/11 -- EMM:
				 * It turns out that limiting to only the most recent request was
				 * too difficult for users to understand. We will always generate
				 * a new confirmation and then subsequently allow any previously
				 * generated confirmation number to "confirm" the email address.
				 *
				 */
				// Check if they have a request in already, if so we are going to update it
				//if(($rs_exists = mysql_query($query_exists)) && mysql_num_rows($rs_exists) > 0) {
					//$pending = mysql_fetch_assoc($rs_exists);
					//$query_request = sprintf("UPDATE mailconfirm SET hashconf='%s', format='%s', day_begin=%s, day_end=%s, doreplace='%s', rid=%s WHERE uid=%s AND email='%s'",
						//$r_hconf, $r_format, $r_begin, $r_end, $r_doreplace, $r_replacement, $USER_INFO['id'], $r_email);
				//} else { // This is a new request
				$query_request = $database->prepare("INSERT INTO mailconfirm (uid, email, format, day_begin, day_end, hashconf, doreplace, rid)
						VALUES (:uid, :r_email, :r_format, :r_begin, :r_end, :r_hconf, :r_doreplace, :r_replacement)");

				$rs_request = $query_request->execute(array(
					':uid' => $USER_INFO['id'],
					':r_email' => $r_email,
					':r_format' => $r_format,
					':r_begin' => $r_begin,
					':r_end' => $r_end,
					':r_hconf' => $r_hconf,
					':r_doreplace' => $r_doreplace,
					':r_replacement' => $r_replacement
				));
				if ($rs_request) {
          $confirmid = $database->lastInsertId();
					$subject = $newaddremailsubjtext . ' ' . $r_confnum;
          admin_email ($r_strippedemail, $subject, $mailtext, $headers, $confirmid);
					printf("<h3 class=\"alert success\">Confirmation email sent to %s!</h3>\n", $r_strippedemail);
				} else {
					printf("<h3 class=\"alert error\">An error occured while generating the confirmation message.  Please try again.</h3>\n");
				}
			} else {
				printf("<h3 class=\"alert warning\">The address %s is already registered to your account!</h3>\n", $r_strippedemail);
			}
		}
	} else if ($mode == "confirm") {
        // Not checking CSRF as email address confirmation email contains a link to this API endpoint.
        // $isValidRequest = validateCSRF(param('ens_confirm_token'));

        $address = param('address');
        // Check to see if the address is already registered to this account
        $query_exists = $database->prepare("SELECT * FROM mailaddresses WHERE uid=:uid AND email=:email");

        // If it is..
        $rs_exists = $query_exists->execute(array(
            ':uid' => $USER_INFO['id'],
            ':email' => $address
        ));
        $addressrow = $query_exists->fetch(PDO::FETCH_ASSOC);
        #printf ("Found address %s for uid %d<br>", $addressrow['email'], $USER_INFO['id']);
        if($addressrow['email'] == '') {
            $confnum = md5(param('confnum'));
            $query_confirmed = $database->prepare("SELECT * FROM mailconfirm WHERE uid=:uid AND email=:email AND hashconf=:confnum LIMIT 1");

            // If the confirmation number was correct
            $rs_confirmed = $query_confirmed->execute(array(
                ':uid' => $USER_INFO['id'],
                ':email' => $address,
                ':confnum' => $confnum
            ));
            $cid = 0;
            $confirmrow = $query_confirmed->fetch(PDO::FETCH_ASSOC);
            $cid = $confirmrow['cid'];
            if ($cid != 0) {
                if($confirmrow['doreplace'] == 'Y') {
                    $query_addaddr = $database->prepare("UPDATE mailaddresses SET email=:email, format=:format, day_begin=:day_begin, day_end=:day_end WHERE uid=:uid AND eid=:eid");
                    $rs_addaddr = $query_addaddr->execute(array(
                        ':email' => $address,
                        ':format' => $confirmrow['format'],
                        ':day_begin' =>  $confirmrow['day_begin'],
                        ':day_end' => $confirmrow['day_end'],
                        ':uid' => $USER_INFO['id'],
                        ':eid' => $confirmrow['rid']
                    ));
                    $eid = $confirmrow['rid'];
                } else {
                    $query_addaddr = $database->prepare("INSERT INTO mailaddresses (uid, email, format, day_begin, day_end)
                    VALUES (:uid, :email, :format, :day_begin, :day_end)");
                    $rs_addaddr = $query_addaddr->execute(array(
                        ':uid' => $USER_INFO['id'],
                        ':email' => $address,
                        ':format' => $confirmrow['format'],
                        ':day_begin' =>  $confirmrow['day_begin'],
                        ':day_end' => $confirmrow['day_end']
                    ));
                    $eid = $database->lastInsertId();
                } // End $confirmrow['doreplace']

                // Associate with ALL existing profiles (params)
                $query_params = $database->prepare("SELECT pid FROM mailparams WHERE userid=:uid");
                $rs_params = $query_params->execute(array(
                    ':uid' => $USER_INFO['id']
                ));


                while($params = $query_params->fetch(PDO::FETCH_ASSOC)) {
                    #printf ("Inserting into email_param_bridge eid/pid %d/%d<br>", $eid, $params['pid']);
                    $query_associate = $database->prepare("INSERT into email_param_bridge (emailid,paramid) values (:eid,:pid)");
                    $rs_associate = $query_associate->execute(array(
                        ':eid' => $eid,
                        ':pid' => $params['pid']
                    ));
                } // End while loop

                // Add it to the USER_EMAILS array
                $USER_EMAILS[$eid] = array("uid"=>$USER_INFO['id'], "eid"=>$eid, "email"=>$address, "format"=>$confirmrow['format'], "day_begin"=>$confirmrow['day_begin'],
                    "day_end"=>$confirmrow['day_end']);

                // Delete from confirm address
                $query_clean = $database->prepare("DELETE FROM mailconfirm WHERE uid=:uid AND email=:email");
                $is_success = $query_clean->execute(array(
                    ':uid' => $USER_INFO['id'],
                    ':email' => $address
                ));

                // Now let's see how we did in this whole process
                if($is_success) {
                    // redirect to the homepage if necessary
                    if($redirect) {
                            // redirect to the homepage
                                    // First try to use php, but if that doesn't work (because of the template), use javascript
                        if( !headers_sent() ) {
                                            header("Location: userhome_map?confirmed=y");
                                            return;
                                    }
                                    else {
                                            print "You are being redirected to the Earthquake Notification Service homepage. <a href='userhome_map'>Go there now</a>";
                                            print "<script type='text/javascript'>
                                                    location.href='userhome_map?confirmed=y';
                                                    </script>";
                                            return;
                                    }

                    } else {
                        printf("<h3 class=\"alert success\">Successfully confirmed %s and associated it with all your profiles!</h3>\n", $address);
                    }
                } else {
                    print ("<h3 class=\"alert error\">An error occurred while processing your request.<br />" .
                        "Please contact <a href=\"mailto:ensadmin@ens.usgs.gov\">ENS Admin</a> and explain what happened to have the problem corrected.</h3>\n");
                }
            } else {
                 printf("<h3 class=\"alert error\">The combination of %s with %s does not match our records.<br />" .
                "Please verify your confirmation number and try again.</h3>\n", $address, param('confnum'));
            }
        } else {
           printf("<h4>The address %s is already registered to your account.</h4>\n", $address);
        }
    } // END:: mode == confirm

	// Show the confirmations form if user has pending confirmations
	$query_pending = $database->prepare("SELECT * FROM mailconfirm WHERE uid=:uid");
	$rs_pending = $query_pending->execute(array(
		':uid' => $USER_INFO['id']
	));
	#$pending = $query_pending->fetch(PDO::FETCH_ASSOC);
	while($pending = $query_pending->fetch(PDO::FETCH_ASSOC)) {
		#printf ("Pending address confirmation for uid %d<br>\n", $pending['uid']);
		printf("<p style=\"text-indent: 10px;\" class=\"alert success\">%s</p>\n", $pendingtext);
		print ("<form method=\"post\" action=\"regaddr\">\n");
		// The obligatory parameters
		printf($obligatory_fields, $USER_INFO['username'], $USER_INFO['hashpasswd'], $page);
		print ("<input type=\"hidden\" name=\"mode\" value=\"confirm\" />");
		print ("\t<p style=\"text-indent: 10px;\">Confirm the address <select name=\"address\">");               
		printf("\t\t<option value=\"%s\">%s</option>\n", $pending['email'], $pending['email']);
		print ("\t</select> by entering the <span style=\"color: #DD0000;\">confirmation number</span> here: <input type=\"text\" name=\"confnum\" size=\"3\" maxlength=\"3\" />\n");

        print "<input type=\"hidden\" id=\"ens_confirm_token\" name=\"ens_confirm_token\" value=\"" .
            htmlentities(getCSRFToken(), ENT_QUOTES, 'UTF-8') . "\" />\n";
        
		print ("\t<input type=\"submit\" name=\"submit\" value=\"Confirm\" />\n\t</p>\n</form>");
		print "<hr />\n";
	}
    
    // SHOULD NOT BE USED? 
    // - the following code with $newaccount appears to be old and only gets displayed when confirming with bad or empty confirm code. 
    // - day begin and day ends is not formatted correctly.
    // 
	// Only give them the rest of the form if they aren't coming from the register.inc.php page,
	// which means they have just created the profile
	if(false && !$newaccount) {
        
	print ("<form method=\"post\" action=\"regaddr\" name=\"frm_regaddr\" onsubmit=\"return emailCheck(this.address.value);\" >\n");
	print ("\t<input type=\"hidden\" name=\"mode\" value=\"request\" />\n");
	?>
	<table cellpadding="2" cellspacing="2" border="0px" id="tbl_regaddr">
	<tr>
		<th><label for="address"><?=$newaddremailtext?></label></th>
		<td><input type="text" name="address" size="30" /></td>
	</tr>
	<tr class="important">
		<th title="<?=$newaddrexpltext?>"><label for="replacement"><?=$newaddrreplacetext?></label></th>
		<td><select name="replacement">
				<option value="0"><?=$newaddrtext?></option>
				<?php
				foreach($USER_EMAILS as $eid=>$info)
					printf("<option value=\"%s\">%s</option>\n", $info['eid'], $info['email']);
				?>
			</select>
		</td>
	</tr>
	<?php
	// Day begins
	printf("\t<tr><th><label for=\"day_begin\">%s:</label></th>\n\t\t<td>", sprintf(
		"%s/help#day_begin", $WEB_PATH), "Day Begins");

	$options = "";
	for($i = 0; $i < 24; ++$i)
		$options .= sprintf("\t\t\t<option value=\"%s\">%s:00</option>\n", $i, $i);
	$begin = sprintf("<select name=\"day_begin\">\n%s\t\t</select>\n\t\t</td>\n\t</tr>\n", $options);
	$begin = str_replace("value=\"8\"", "value=\"8\" selected=\"selected\"", $begin);
	print $begin;

	// Day Ends
	printf("\t<tr><th><label for=\"day_end\">%s:</label></th>\n\t\t<td>", sprintf(
		"%s/help?ispopup=true#day_begin", $WEB_PATH), "Day Ends");
	$end = sprintf("<select name=\"day_end\">\n%s\t\t\t<option value=\"24\">24:00</option>\n\t\t</select>\n\t\t</td>\n\t</tr>\n", $options);
	$end = str_replace("value=\"20\"", "value=\"20\" selected=\"selected\"", $end);
	print $end;
	?>
	<tr>
		<th><label for="format">Message Format:</label></th>
		<td>
      <select name="format">
        <option value="HTML" selected="selected">HTML Format</option>
        <option value="long">Regular Email</option>
        <option value="short">Pager/Cell Phone</option>
        <option value="raw">Raw CUBE format</option>
      </select>
		</td>
	</tr>
	<tr><td colspan="2"><?=$newaddrnotetext?></td></tr>
	<tr><td colspan="2" align="center" valign="bottom"><br />
		<input type="image" src="<?=$WEB_PATH . "/images/submit.gif"?>" name="submit" />
	</td></tr>
	</table>
	</form>

	<?php
	} // end of if(!$newaccount)

		/*print make_img_button(sprintf("%s/images/back_to_profiles.gif", $WEB_PATH), "userhome", "ENS Home");*/ ?>
