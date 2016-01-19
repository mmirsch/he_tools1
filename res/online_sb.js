	function ajaxExecute(eidUrl,redirectUrl,reload) {
		$.ajax({
			url: eidUrl,
			async: false,
			success: function(data, request) {
				if (redirectUrl) {
					window.location.href = redirectUrl;
				} else if (reload) {
					window.location.reload();
				}
			}
		});
		return false;
	}

	function checkFeUser(username) {
		var result = false;
		$.ajax({
			url: "index.php?eID=he_tools&action=fe_user_exists&username="+username,
			async: false,
			success: function(data, request) {
				if (data=="1" || data=="true") {
					result = true;
				} else {
					result = false;
				}
			}
		});
		return result;
	}
	
	function checkUsername(username,kuerzel) {
		var userVorhanden = checkFeUser(kuerzel + username); 
		$("#userExistsError").detach();
		if (userVorhanden) {
			var fehlerMeldung = "Der eingegebene Benutzername '" + username + "' wird bereits verwendet, bitte wählen Sie einen anderen Benutzernamen!";
			$("form.registration").before($('<h3 id="userExistsError" class="error">' + fehlerMeldung + "</h3>"));
			return false;
		} 
		return true;
	}

	function checkPasswords(password1,password2,minLength) {
		$("#passwordError").detach();
		if (password1.length<minLength || password1.length<minLength) {
			var fehlerMeldung = "Das eingegebene Passwort ist kürzer als " + minLength + " Zeichen!";
			$("form.registration").before($('<h3 id="passwordError" class="error">' + fehlerMeldung + "</h3>"));
			return false;
		} else if (password1!=password2) {
			var fehlerMeldung = "Die beiden Passwörter stimmen nicht überein!";
			$("form.registration").before($('<h3 id="passwordError" class="error">' + fehlerMeldung + "</h3>"));
			return false;
		}
		return true;
	}