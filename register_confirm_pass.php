<?php

include_once 'databaseConn.php';

include_once './lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();

include_once './class/Config.class.php';

$configObj = new Config();


include_once './class/XssClean.php';
$xssClean = new xssClean();
if(isset($_GET['email']) && $_GET['email']!='')
{
	$email = $xssClean->clean_input($_GET['email']);
}
if(isset($_GET['data']) && $_GET['data']!='')
{
	$email = $xssClean->clean_input($_GET['data']);
}

//$email= $xssClean->clean_input($_GET['email']);

$result3 = $DatabaseCo->dbLink->query("SELECT * FROM register,site_config where email = '$email'");

$rowcc = mysqli_fetch_array($result3);
$_SESSION['otp_varif_mem_email'] = $email;
$cpass = $rowcc['cpassword'];

if(isset($email) && $email != '')
{	
	
	if(isset($cpass) && $cpass == '' || is_null($cpass))
	{
		
		 function RandomPassword() 
	
		 {
	
					$chars = "abcdefghijkmnopqrstuvwxyz023456789";
	
					srand((double)microtime()*1000000);
	
					$i = 0;
	
					$pass = '' ;
	
					
	
					while ($i <= 7) {
	
					$num = rand() % 33;
	
					$tmp = substr($chars, $num, 1);
	
					$pass = $pass . $tmp;
	
					$i++;
	
					}
	
		   return $pass;
	
		}
	
		
	
	$pswd = RandomPassword();
	
		
	
	
	
	$up = $DatabaseCo->dbLink->query("update register set cpassword='$pswd' where email='$email'")or die("Could not update data because ".mysql_error());	
	
	

	$result3 = $DatabaseCo->dbLink->query("SELECT * FROM register,site_config where email = '$email'");
	
	$rowcc = mysqli_fetch_array($result3);
	
	
	
	$name = $rowcc['firstname']." ".$rowcc['lastname'];
	
	$matriid = $rowcc['matri_id'];
	
	$cpass = $rowcc['cpassword'];
	
	$website = $rowcc['web_name'];
	
	$mno = $rowcc['mobile'];
	
	$mno_data = $mno; 
	
	$webfriendlyname = $rowcc['web_frienly_name'];
	
	$from = $rowcc['from_email'];
	
	if(isset($_GET['email']) && $_GET['email']!='')
	{
		$to = $xssClean->clean_input($_GET['email']);
	}
	if(isset($_GET['data']) && $_GET['data']!='')
	{
		$to = $xssClean->clean_input($_GET['data']);
	}
	
	//$to = $xssClean->clean_input($_GET['data']);
	
	$mobile_verify_stored = $rowcc['mobile_verify_status'];
	
	$status = $rowcc['status'];
	
	       	
	$result45 = $DatabaseCo->dbLink->query("SELECT * FROM email_templates where EMAIL_TEMPLATE_NAME = 'Registration'");
	
	$rowcs5 = mysqli_fetch_array($result45);
	
	
	
	$subject = $rowcs5['EMAIL_SUBJECT'];	
	
	$message = $rowcs5['EMAIL_CONTENT'];
	
	$email_template = htmlspecialchars_decode($message,ENT_QUOTES);
	
	
	
	$trans = array("your site name" =>$webfriendlyname,"name"=>$name,"matriid"=>$matriid,"email_id"=>$to,"cpass"=>$cpass,"site domain name"=>$website,"../img/"=>$website.'img/');
	
	
	
	$email_template = strtr($email_template, $trans);
	
	
	
			$headers  = 'MIME-Version: 1.0' . "\r\n";
	
			$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
	
			$headers .= 'From:'.$from."\r\n";
	
	
	
	

			@mail($to,$subject,$email_template,$headers);
	
			// Getting SMS API, if yes proceed furthur //
			$sql="select * from sms_api where status='APPROVED'";
			$rr=mysqli_query($DatabaseCo->dbLink,$sql) or die(mysqli_error($DatabaseCo->dbLink));
			$num_sms=mysqli_num_rows($rr);
			$sms=mysqli_fetch_object($rr);
		
		
				if($num_sms>0)
				{
					// Getting predefined SMS Template //
					
					$result45 = $DatabaseCo->dbLink->query("SELECT * FROM sms_templete where temp_name = 'Registration'");
					$rowcs5 = mysqli_fetch_array($result45);	
					$message = $rowcs5['temp_value'];
					$sms_template = htmlspecialchars_decode($message,ENT_QUOTES);	
					$trans = array("web_frienly_name" =>$webfriendlyname);	
					$sms_template = strtr($sms_template, $trans);
					
					
					// Final action to send sms //
					
						$text =$sms_template;
						$message = str_replace(" ","%20",$text);
						$mno=$mno;
						
						$mobile=substr($mno,0,3);
						
						if($mobile=='+91')
						{
							$mno=substr($mno,3,15);
						}
						else
						{
							$mno=$mno;
						}
				
						$sms_api = htmlspecialchars_decode($sms->basic_url,ENT_QUOTES);
						$sms_trans = array("mno" =>$mno,"content_to_send"=>$message);
						$final_api = strtr($sms_api, $sms_trans);
							
							
					$ch = curl_init($final_api);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$curl_scraped_page = curl_exec($ch);
					curl_close($ch);
					$curl_scraped_page;
				
						
		
						
			}
		
			
		
	}
	else
	{
		$name = $rowcc['firstname']." ".$rowcc['lastname'];
	
		$matriid = $rowcc['matri_id'];
		
		$cpass = $rowcc['cpassword'];
		
		$website = $rowcc['web_name'];
		
		$mno = $rowcc['mobile'];
		
		$mno_data = $mno; 
		
		$webfriendlyname = $rowcc['web_frienly_name'];
			
		$mobile_verify_stored = $rowcc['mobile_verify_status'];
	
		$status = $rowcc['status'];
	}




if($status == 'Active' && $mobile_verify_stored == 'Yes')
{

	echo "<script>window.location='register_3.php?email=$email'</script>";

}

}

	/*require_once('auth.php');*/
   
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $configObj->getConfigTitle(); ?></title>
    <meta name="keyword" content="<?php echo $configObj->getConfigKeyword(); ?>" />
    <meta name="description" content="<?php echo $configObj->getConfigDescription(); ?>" />  
    <link type="image/x-icon" href="img/<?php echo $configObj->getConfigFevicon(); ?>" rel="shortcut icon" />

<!-------------------------------- Custome css ------------------------->
	
 	<link href="css/style.css" rel="stylesheet">
 
<!-------------------------------- Custome css End---------------------->
<!-------------------------------- Responsive css ------------------------->
	
 	<link href="css/responsive.css" rel="stylesheet">
 
<!----------	---------------------- Responsive css End---------------------->

<!-------------------------------- Bootstrap css use for tooltip------------>
<link href="css/bootstrap.css" rel="stylesheet">
<!-------------------------------- Bootstrap css use for tooltip End------------>

<!-------------------------------- Google font ------------------------->

	<link href='http://fonts.googleapis.com/css?family=Gentium+Book+Basic' rel='stylesheet' type='text/css'>

<!--------------------------------  Google font End---------------------->
<!--------------------------------  Button css---------------------->
<link rel="stylesheet" type="text/css" href="css/component.css" />
<!-------------------------------- Button css End---------------------->
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.js"></script>
    <![endif]-->
 
 <!-------------------chosen css ------------------>
 <link rel="stylesheet" href="css/chosen.css">
 <link rel="stylesheet" href="css/prism.css">
 <!-------------------chosen css end------------------>
<!-------------------Validation css ------------------>
 <link rel="stylesheet" href="css/validate.css">
<!-------------------Validation css------------------>
 
<!---------------Js Birth date------------------> 


<!--for new validation-->

 <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

<script type="text/javascript" src="http://flesler-plugins.googlecode.com/files/jquery.scrollTo-1.4.2.js"></script>
  
  
  
  </head>

  <body>
<div class="page-wrap  ne-aft-log">
	<!--<div class="container-fluid ne-aft-log">
	<div class="row">
		
		 
		 
	</div>
	</div>-->




<!--<div class="container">
	 	<div class="row">
	 		<div class="xxl-10 xl-10 l-10 m-16 s-16 xs-16">
		<h2 class="font-orange" style="margin-top: 38px;">Fill the form below to register and start your partner search</h2>
	</div>
	<div class="xxl-6 xl-6 l-6 m-16 s-16 xs-16">
		<div class="mybox" style="border:none!important;">
			<p>Already Registered ?</p>
						<a href="login" class="mynew-clr">Login Now</a>
		</div>
	</div>
	 	</div>
	 </div>-->
<div class="container-fluid register-main">

	<div class="row">

    	<?php

	include 'page-parts/header.php'; 

?>

        <div class="clearfix visible-xs visible-sm"></div>

		<div class="xxl-12 xxl-margin-left-2 xl-12 xl-margin-left-2 xs-16 xs-margin-left-0 s-16 xs-margin-left-0 m-16 m-margin-left-0 l-16 l-margin-left-0 margin-top-10px bg-offwhite padding-15px border-10px-bottom">

   			<div class="xxl-16 xs-16 s-16 m-16 l-16 xl-16">
<?php if($status != 'Active' && isset($_GET['data']) && $_GET['data']!='')

{?>
            	<div class="row">

                    <div class="xxl-16 xl-16 l-16 m-16 s-16 xs-16 font-red center-text">

                    	<h3>Congratulation you are registered now. We have sent you a verification email. Please check your email account.</h3>

                    </div>

                    <div class="border-10px xxl-16 xs-16 xl-16 s-16 m-16 l-16"></div>

                </div>
<?php } 
 elseif($status != 'Active')

{?>
            	<div class="row">

                    <div class="xxl-16 xl-16 l-16 m-16 s-16 xs-16 font-red center-text">

                    	<h3> We have sent you a verification email. Please check your email account.</h3>

                    </div>

                    <div class="border-10px xxl-16 xs-16 xl-16 s-16 m-16 l-16"></div>

                </div>
<?php } ?>
            </div>

            <div class="xxl-16 xl-8 l-8 m-16 s-16 xs-16">
				<!-- mob ver -->
                	<?php
						if($mobile_verify_stored!='Yes')
						{
					?>		
						<div class="row" id="vefifymobdiv">
							<div class="xxl-16 xl-16 s-16 m-16 l-16">
								<div align="center" id="loader_var" style="display:none"><img src="img/6.gif" alt="Please wait.." ></div>
									
								<p class="font font-green xxl-16 xs-16 xl-16 s-16 m-16 l-16 center-text" style="color:#7c7c85">Help us build a safe & authentic matchmaking platform.</p>
								
								<h2 class="font font-green xxl-16 xs-16 xl-16 s-16 m-16 l-16 center-text" style="color:#7c7c85">Verify your number & Complete your Profile</h2>
									
								<p class="xxl-16 xl-16 m-16 s-16 xs-16 center-text margin-top-10px" id="varify_btn">
								
								<a href="javascript:;" id="varify_now_mobile" onClick="varify_mobile('m','<?php echo $mno_data; ?>')" class="button-green-border xxl-12 xxl-margin-left-2 xl-16 m-12 m-margin-left-2 s-16 xs-16 l-12 l-margin-left-2">Verify Now</a>
								</p>
								<div class="clearfix"></div>
								<br>
						<div align="center" style="display:none; background:#fef6e2; width:100%;padding:13px;" id="response_varify"> <i class="fa fa-info-circle" style="color:#269ccc;"></i>
							We have sent a 4 digit PIN to your mobile via SMS/Text Message.</div>
							<div align="" id="varify_mobile" style="display:none">
								<br/>
								<div id="show_edit_mobile_no" style="display:block">
									<div class="xxl-15 xxl-margin-left-3 xl-16 l-16 m-16 xs-16 s-16 margin-top-5px form-group">
										<div class="row">
										<div class="xxl-4 xs-16">
											<label class="font-orange"style="color:#b7b7b7;">
												Enter Mobile Number :
											</label>
										</div>
										<div class="xxl-6 xs-16">
										
										<?php 
											
											$mno = substr($mno_data,0,3);//explode("-",$mno_data);
											
										?>
                                        	<input type="hidden" name="email" id="email" value="<?php echo $_SESSION['otp_varif_mem_email'];?>" />
											<input type="hidden" name="mobile_country_code" id="mobile_country_code" value="+91" />
											<input type="text" class="form-control inp-left" placeholder="Enter Mobile Number" name="mobile_no" id="mobile_no" value="<?php echo $mno_data;?>" style=" margin-top:-6px;">
											<p style="color:#E54D50;font-size:13px;margin-top:5px;">Enter Only mobile number EX - 98989XXXXX</p>
											<span id="error-msg" class="hide font-red">Enter valid Phone Number</span>
   										</div>  
										</div>
									</div>
									<br/>
								</div>
								<div id="show_otp" style="display:block">
									<div class="xxl-15 xxl-margin-left-3 xl-16 l-16 m-16 xs-16 s-16 margin-top-15px form-group">
										<div class="row">
										<div class="xxl-3 xs-16 margin-top-5px">
											<label class="font-orange"style="color:#b7b7b7;">
											Enter PIN Number :
											</label>
										</div>
										<div class="xxl-5 xs-16">
											<input type="text" class="form-control" placeholder="Enter PIN Number" name="varify_code" id="varify_code">
										</div>
									<div class="xxl-4 xs-16 margin-top-5">
										<button type="button" name="verify_submit" id="go_go_go" class="button-green button mobile-btn margin-bottom-10px" style="width:110px;border-radius:4px; background:#2dbcd1;"  onclick="varifycode('m','<?php echo $mno_data; ?>')"> Verify  <i class="fa fa-play" style="font-size:9px;"></i>
                                        </button>
									</div>
								</div>
							</div>
							<br/>
						</div>
										
					    <center  class="m-left">
							Didn't get your PIN?
							<a onclick="resend_varifycode('m','<?php echo $mno_data; ?>')" name="resend_varifycode" type="button" name="resend_parent" class="" style="width:110px; color:#2dbcd1;cursor:pointer;">Re-send OTP</a> to <span id="show_mobile"><?php echo $mno_data; ?></span> 
							<a id="edit_mobile_no" onClick="edit_mobile_no()" style ="color:#2dbcd1;cursor:pointer;">(Edit)</a> <br> try alternate method to verify  
						</center>
							</div>
							</div>
						</div>
					
							<div class="clearfix"><br/></div>
							<div class="border-10px xxl-16"></div>
					   
						<div class="clearfix"><br/></div>
							<!-- mob ver end-->
							<div class="xxl-16 xl-16 l-16 m-16 s-16 xs-16 font-red">
								<p style="color:#72727c;"> If you have not been able to verify your mobile number using OTP, please send a WhatsApp message to <span style="color:#2dbcd1;"><?php echo $configObj->getConfigContactno();?>,</span> from your registered mobile number. For any further assistance send email to <span style="color:#2dbcd1;"><?php echo $configObj->getConfigContact();?>.</span></p>
							</div>
                            <?php
						}
					   ?>
            	
            </div>

            <div class="clearfix visible-xs"></div>

           
            
            
            <?php include "advertise/ad_level_2.php";?>

        </div>

		
        
	 </div>
					
                    
                    
     <div class="clearfix"></div>

</div>

</div>
<?php /*?>
<div class="xxl-16 xl-16 s-16 xs-16 m-16 l-16 center-text copyright-social" id="neFooter2" style="text-align: center; background-color: rgba(234, 135, 22, 0.7490196078431373);">
    		<!--
			<span class="font-white"> <?php echo $configObj->getConfigFooter();?></span>
			-->
			<div class="row">
                <div class="xxl-8 xl-8 s-16 xs-16 m-8 l-8">
                    <p style="color:#fff;font-size:14px;margin:3px;"><?php echo $configObj->getConfigFooter();?></p>
                </div>
                <div class="xxl-8 xl-8 s-16 xs-16 m-8 l-8">
                    <a href="<?php echo $configObj->getConfigFacebookLink();?>"><i class="fa fa-facebook social"></i></a>
                    <a href="<?php echo $configObj->getConfigTwitterLink();?>"><i class="fa fa-twitter social"></i></a>
                    <!--<a href="https://www.instagram.com/"><i class="fa fa-instagram social"></i></a>
                    <a href="https://in.pinterest.com/"><i class="fa fa-pinterest social"></i></a>-->
					<a href="<?php echo $configObj->getConfigGoogleLink();?>"><i class="fa fa-google-plus social"></i></a>
                    <a href="<?php echo $configObj->getConfigInstagramLInk();?>"><i class="fa fa-instagram social" ></i></a>
					<a href="<?php echo $configObj->getConfigPinterestmLInk();?>"><i class="fa fa-pinterest-p social" ></i></a>
                </div>
            </div>
        </div><?php */?>
<?php

	include 'page-parts/footer.php';

?>





    

  </body>
<!-------------------------------------- jQuery Letest ----------------------------------->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>


<!-------------------------------------- jQuery Letest End ------------------------------->


  <!-------------------------------------- jQuery Letest -------------------------------->
	<link rel="stylesheet" href="build/css/intlTelInput.css">
	<script src="js/jquery-1.11.2.min.js"></script>
	<script src="http://code.jquery.com/ui/1.8.21/jquery-ui.min.js"></script>
	<script src="build/js/intlTelInput.min.js"></script>
<!-------------------------------------- jQuery Letest End ---------------------------->
<!-------------------------------------- jQuery Letest ----------------------------------->

    

<!-------------------------------------- jQuery Letest End ------------------------------->

<!--------------------------------------Pace js ----------------------------------->

    <script src="js/pace.min.js"></script>

<!--------------------------------------Pace js End ------------------------------->

<!--------------------------------------Pace js ----------------------------------->

    <script src="js/jquery.ui.touch-punch.js"></script>

	
    <script>$('#widget').draggable();</script>

    <script>

  $(function() {

    $( "#resizable" ).resizable();
	if($("#varify_now_mobile").length > 0)
	{
		$("#varify_now_mobile").trigger('click');

		
	}

  });
   function varifycode(type, mobile)
	{
		var varify_code = $("#varify_code").val();
		var email = $("#email").val();		
		if( varify_code == "" )
        {
            alert( "Please your otp code" );
			$("#varify_code").focus();
            return false;
        }
		$("#loader_var").show();
		var dataString = 'mobile='+ mobile+'&email='+email+'&type='+type+'&varify_code='+varify_code;
		$.ajax({
			url:"web-services/verify_otp",
			type:"POST",
			data:dataString,
			cache: false,
			dataType:"json",
			success: function(response)
			{
				$("#loader_var").hide();
				//var trimmedResponse = $.trim(response);
				//response.trim();
				if(response.successStatus)
				{
					$("#response_varify").html("<span style='color:#024e21;font-weight:bold'><i class='fa fa-check' style='font-size:35px;'></i> Your mobile number Verified Successfully.</span>");
					$("#varify_mobile").slideUp();
					window.setTimeout(function() {
						window.location.href = "register_3.php?email="+email;
					}, 2000);
					
					//$("#vefifymobdiv").remove();
					 //setTimeout(function(){ alert("Hello"); }, 3000);
				}
				else
				{
					$('#response_varify').html("<h4 style='margin-top:5px; color:red;'><b><i class='fa fa-times' style='font-size:17px;'></i>  Wrong OTP entered. Please try again</b></h4>");
				}
			}
	   });
	}
	function varify_mobile(type, mobile)
	{
		$("#varify_btn").hide();
		$("#varify_mobile").slideDown();
		$("#response_varify").show();	
		resend_varifycode(type, mobile);
		return false;
	}
	
	function resend_varifycode(type, mobile)
	{	
	
		var mobile = $("#mobile_no").val();
		$("#show_mobile").text(mobile);
		//var code = $("#mobile_country_code").val();
		var email = $("#email").val();	
		var confirm_mobile = mobile;
		if( mobile == "" )
        {
            alert( "Please mobile number" );
			$("#mobile_no").focus();
            return false;
        }
		
		var show_edit_mobile_no = document.getElementById('show_edit_mobile_no');
		var show_otp = document.getElementById('show_otp');
		if (show_edit_mobile_no.style.display == 'block') {
			show_edit_mobile_no.style.display = 'none';
			show_otp.style.display = 'block';
		}
		
		
		
		
		var dataString = 'mobile='+confirm_mobile+'&email='+email+'&type='+type;
	//	alert(dataString);
		$("#loader_var").show();
		$.ajax
		({
			type: "POST",
			url: "resend_varification_code",
			data: dataString,
			cache: false,
			success: function(html)
			{
				alert("An SMS with verification PIN has been sent to "+mobile);
				$("#loader_var").hide();
			}
		});
	}
	
	function edit_mobile_no(){
		var show_edit_mobile_no = document.getElementById('show_edit_mobile_no');
		
		var show_otp = document.getElementById('show_otp');
		if (show_edit_mobile_no.style.display == 'none') {
			show_edit_mobile_no.style.display = 'block';
			show_otp.style.display = 'none';
		}
		
		}
</script>
<script type="text/javascript">

window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=

d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.

_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');

$.src='//v2.zopim.com/?5PomTwKttdcex1igRRI3BDGrmr2WeYns';z.t=+new Date;$.

type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');

</script>

<script>
	
	var telInput = $("#mobile_no"),
	errorMsg = $("#error-msg");
	//validMsg = $("#valid-msg");

	// initialise plugin
	telInput.intlTelInput({
		autoFormat: false,
		utilsScript:"build/js/utils.js"
		
	});


	var reset = function() {
		telInput.removeClass("error");
		errorMsg.addClass("hide");
		//validMsg.addClass("hide");
	};
	
	// on blur: validate
	telInput.blur(function() {
		

	reset();
		if ($.trim(telInput.val())) {
			if (telInput.intlTelInput("isValidNumber")) {
				//validMsg.removeClass("hide");
				/* get code here*/
				var getCode = telInput.intlTelInput('getSelectedCountryData').dialCode;
				//alert(getCode);
				$('input[name="mobile_country_code"]').val(getCode);
			} else {
				telInput.addClass("error");
				errorMsg.removeClass("hide");
			}
		}
	});

	// on keyup / change flag: reset
	telInput.on("keyup change", reset);
	
	
	/*$("#mobile_no").intlTelInput({
        utilsScript: "build/js/utils.js"
	});
	
	$("#mobile_no").change(function()
	{
		var telInput = $("#mobile_no");
		//alert(telInput.val());
		if ($.trim(telInput.val())) 
		{
			if (telInput.intlTelInput("isValidNumber")) 
			{
				console.log(telInput.intlTelInput("getNumber"));
				var getCode = telInput.intlTelInput('getSelectedCountryData').dialCode;
				//alert(getCode);
				$('input[name="mobile_country_code"]').val(getCode);
			}
			else 
			{
				console.log(telInput.intlTelInput("getValidationError"));
				alert('Please enter respective country mobile number.');
			}
		}
	});
	*/
	
	
	</script>
	
      <script src="js/jquery.validate.min.js"></script>
	  
<!--------------------------------------Pace js End ------------------------------->

</html>