<?php
function youtube($url, $width=560, $height=315, $fullscreen=true)
{
    parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
    $youtube= '<iframe allowtransparency="true" scrolling="no" width="'.$width.'" height="'.$height.'" src="//www.youtube.com/embed/'.$my_array_of_vars['v'].'?start=15&end=18&autoplay=1&loop=1" frameborder="0"'.($fullscreen?' allowfullscreen':NULL).'></iframe>';
    return $youtube;
}

// show youtube on my page
$url='http://www.youtube.com/watch?v=yvTd6XxgCBE';

echo youtube($url, 560, 315, true);
?>


$(document).ready(function() {
    function getQueryVariable(a, b) {
        if (a.indexOf('?') != -1) {
            var a = a.substring(a.indexOf('?') + 1, a.length)
        }
        if (a.indexOf('#') != -1) {
            var a = a.substring(0, a.indexOf('#'))
        }
        var c = a.split("&");
        for (var i = 0; i < c.length; i++) {
            var d = c[i].split("=");
            if (d[0] == b) {
                return d[1]
            }
        }
        return false
    }

    function getLink() {
        if ($('#min').val() == "min") {
            var a = "0"
        } else {
            var a = parseInt($('#min').val(), 10)
        }
        if ($('#sec').val() == "sec") {
            var b = "0"
        } else {
            var b = parseInt($('#sec').val(), 10)
        }
        var c = getQueryVariable($("#startURL").val(), 'v');
        if (c == false || c == undefined) {
            $('#newURL').val('');
            $('#newURL').attr("disabled", "disabled");
            $('#newURL, #newURLlabel').addClass("inactive");
            $("#one p.error").show();
            $("#startURL").addClass("error");
            return false
        }
        $('#newURL').removeAttr("disabled");
        $('#newURL, #newURLlabel').removeClass("inactive");
        a = parseInt(Math.floor(b / 60), 10) + parseInt(a, 10);
        b = b % 60;
        $("#newURL").val("http://www.youtube.com/watch?v=" + c + "&t=" + a + "m" + b + "s")
    }

    function previewLink() {
        if ($('#min').val() == "min") {
            var a = "0"
        } else {
            var a = parseInt($('#min').val(), 10)
        }
        if ($('#sec').val() == "sec") {
            var b = "0"
        } else {
            var b = parseInt($('#sec').val(), 10)
        }
        var c = getQueryVariable($("#startURL").val(), 'v');
        if (c == false || c == undefined) {
            $('#newURL').val('');
            $('#newURL').attr("disabled", "disabled");
            $('#newURL, #newURLlabel').addClass("inactive");
            $("#one p.error").show();
            $("#startURL").addClass("error");
            return false
        }
        a = parseInt(Math.floor(b / 60), 10) + parseInt(a, 10);
        b = b % 60;
        $(this).attr("href", "http://www.youtube.com/watch?v=" + c + "&t=" + a + "m" + b + "s")
    }
    $("#getLink").click(getLink);
    $("#previewLink").click(previewLink);
    $("#startURL").keypress(function() {
        $("#one p.error").hide();
        $(this).removeClass("error")
    });
    $('#min, #sec').focus(function() {
        if ($(this).val() == "min" || $(this).val() == "sec") {
            $(this).val('')
        }
    });
    $('#min, #sec').keypress(function(e) {
        if (e.which > 31 && (e.which < 48 || e.which > 57)) {
            return false
        }
    });
    $('#min').blur(function() {
        if ($(this).val().length == 0) {
            $(this).val('min')
        }
    });
    $('#sec').blur(function() {
        if ($(this).val().length == 0) {
            $(this).val('sec')
        }
    })
});