<script>
    var splashurl = '{{ $url }}';
</script>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Please wait</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<p id="client_mac" style="display: none;">
    <unifi var="mac"/>
</p>
<p id="ap_mac" style="display: none;">
    <unifi var="ap_mac"/>
</p>
<script>
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    };

    var ap_mac = document.getElementById('ap_mac').textContent.trim();
    var client_mac = document.getElementById('client_mac').textContent.trim();
    var loginurl = window.location.protocol + '//' + window.location.host + window.location.pathname + 'auth.html';
    window.location.replace(splashurl + "?ap_mac=" + ap_mac + "&client_mac=" + client_mac + "&login_url=" + loginurl);
</script>
</body>
</html>
    