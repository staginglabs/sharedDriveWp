<?php

    // get the url
    $plugin_url = WP_PLUGIN_URL;
?>
<script type="application/javascript">
    var update = function() {
        var plugin_url = '<?php echo $plugin_url?>';
        $.ajax({
            url: plugin_url + '/raidboxes-migrator/status.php',
            dataType:'json',
            cache: false,
            error: function() {
                $("#rb_migrator_status").html('Status wird gelesen...');
                window.setTimeout( update, 5000);
            },
            success: function(results) {
                if (results !== false) {
                    var progress;
                    var progressDiv = $("#barAndText");
                    var splits  = results.split(' ');
                    var task    = splits[0];
                    var percent = splits[1];
                    var downloadfactor = 0.9;
                    var dbfactor = 0.05;
                    switch (task) {
                        case "DOWNLOAD" :
                            progressDiv.show();
                            var download_splits = results.split(',');
                            var status_text = download_splits[1];
                            progress = (percent*downloadfactor);
                            setStatus(status_text);
                            setProgress(progress);
                            break;
                        case "EXPORT" :
                            progress = 90 + (percent*dbfactor) ;
                            setStatus("Exportiere Datenbank");
                            setProgress(progress);
                            break;
                        case "IMPORT" :
                            progress = 95 + (percent*dbfactor) ;
                            setStatus("Importiere Datenbank");
                            setProgress(progress);
                            break;
                        case "Erfolgreich" :
                            setStatus("Schließe Migration ab");
                            setProgress(100);
                            setTimeout(function () {
                                location = ''
                            },10000);
                            break;
                        case "Fehler" :
                            setStatus(results);
                            setTimeout(function () {
                                location = ''
                            }, 2000);
                            break;
                        default :
                            setStatus(results);
                            window.setTimeout(update, 5000);
                    }
                }
                else {
                    setStatus("Status wird geladen...");
                    window.setTimeout(update, 5000);
                }
            }
        });
    };

    var setStatus = function (status_text) {
        var plugins_url = '<?php echo plugins_url()?>';
        var status = $("#rb_migrator_status");
        status.html('<div class="flleft"><p><b>' + status_text + '</b></p></div><div class="flleft"><img id="migrationticker" src="' + plugins_url + '/raidboxes-migrator/img/rb_ticker.svg" alt="Raidboxes Migration Ticker"/></div><br class="clear">');
    };

    var setProgress = function (progress) {
        progress = Math.round(progress);
        var intr = setInterval(function () {
            var progbar = $("#progressBar");
            var indicator = $("#progressIndicator");
            var oldprog = progbar.width();
            var parent = $("#migrationProgress").width();
            oldprog = Math.round(100*oldprog/parent);
            if (oldprog < progress) {
                oldprog++;
                progbar.width(oldprog + "%");
                indicator.html(oldprog + "%");
            }
            else {
                clearInterval(intr);
                window.setTimeout(update, 3000);
            }
        }, 100);



    };


    $( document ).ready(update());
</script>
<div id="rb_migrator_status">
    <p>
        Status wird gelesen...
    </p>
</div>
<div id="barAndText" class="invisible">
    <div id="migrationProgress">
        <div id="progressBar" class="rbbluebg">
            <div id="progressIndicator" class="rbbluebg white font-roboto">0%</div>
        </div>
    </div>
</div>
<br>

