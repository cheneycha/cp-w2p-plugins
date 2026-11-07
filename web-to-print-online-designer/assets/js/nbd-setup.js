var nbdWizard = {
    get_license: function(){
        var formData = new FormData();
        formData.append('action', 'nbdesigner_get_license_key');
        formData.append('nbdesigner[name]', document.getElementById('license-name').value);
        formData.append('nbdesigner[email]', document.getElementById('license-email').value);
        formData.append('nbdesigner[domain]', document.getElementById('license-domain').value);
        formData.append('nbdesigner[title]', document.getElementById('license-title').value);
        formData.append('nbdesigner_getkey_hidden', document.getElementById('nbdesigner_getkey_hidden').value);
        var xmlhttp = new XMLHttpRequest(),
        elLoading = document.getElementById('license-loading'),
        elCheckMail = document.getElementById('license-check-mail');
        elLoading.style.display = 'inline-block';
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                elLoading.style.display = 'none';
                elCheckMail.style.display = 'inline-block';
            }
        }
        xmlhttp.open("POST", ajax_url, true);
        xmlhttp.send( formData );
    }
};