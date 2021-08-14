var global_email;
function sendOtp(){
	const email=document.getElementById("email").value;
	global_email=email;
	const xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4) {
			if(this.status == 200){
				document.getElementById("message").innerHTML = "OTP sent succesfully";
				document.getElementById("email").value = "";
				setTimeout(() => {
					document.getElementById("formotp").style.display="block";
					document.getElementById("formemail").style.display="none";
					document.getElementById('message').innerHTML= "";
				}, 1000);
			}else if(this.status===403){
				document.getElementById('message').innerHTML= "Wrong email address format.";
				document.getElementById("email").value = "";
			}else if(this.status===400){
				document.getElementById('message').innerHTML= "User already exists.";
				document.getElementById("email").value = "";
			}else{
				document.getElementById('message').innerHTML= "Internal Server Error";
			}
			setTimeout(() => {
				document.getElementById('message').innerHTML= "";
			}, 1000);
		}
	};
	xmlhttp.open("POST","otp_send.php");
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("email="+email);
	return false;
}
function verifyOtp(){
	const xmlhttp = new XMLHttpRequest();
	const otp =document.getElementById('otp').value;
	const email=global_email;
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 ) {
			if(this.status == 200){
				document.getElementById("message").innerHTML = "you have been verified and will recieve XKCD comics from now";
				document.getElementById('otp').value = "";
				setTimeout(() => {
					document.getElementById("formotp").style.display="none";
					document.getElementById("formemail").style.display="block";
					document.getElementById("message").innerHTML = '';
				}, 2000);
			}else if(this.status===403){
				document.getElementById('message').innerHTML= "wrong otp format";
				document.getElementById('otp').value = "";
			}else if(this.status == 400){
				document.getElementById("message").innerHTML= "you have entered wrong OTP";
				document.getElementById('otp').value = "";
			}else if(this.status == 404){
				document.getElementById("message").innerHTML= "Can't verify you";
				document.getElementById('otp').value = "";
			}else{
				document.getElementById("message").innerHTML="Internal Server Error";
			}
			setTimeout(() => {
				document.getElementById('message').innerHTML= "";
			}, 2000);
		}
	};
	xmlhttp.open("POST","otp_verification.php",true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("otp="+otp+"&email="+email);
	return false;
}
function ValidateEmail(email){
	var mailformat = /^[a-z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-z0-9-]+(?:\.[a-z0-9-]+)*$/;
	if(document.form.email.value.match(mailformat) && document.form.email.value != null){
		document.getElementById("msg").innerHTML = "";
		document.getElementById("sendotp").disabled = false;
		return true;
	}else{
		document.getElementById("msg").innerHTML = "invalid email address";
		document.getElementById("sendotp").disabled = true;
		document.form.email.focus();
		return false;
	}
}
function ValidateOtp(otp){
	var otpformat = /^[0-9]{6,6}$/;
	if(document.sendotp.otp.value.match(otpformat) && document.sendotp.otp.value != null){
		document.getElementById("msg").innerHTML = "";
		document.getElementById("verify").disabled = false;
		return true;
	}else{
		document.getElementById("verify").disabled = true;
		document.getElementById("msg").innerHTML = "invalid otp";
		document.sendotp.otp.focus();
		return false;
	}
}   