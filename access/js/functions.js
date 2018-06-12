/**
 * Created by Jelle on 19-6-2017.
 */
$(document).ready(function() {
    function goBack() {
        window.history.go(-1);
    }

    $(".text-limit").each(function () {
        var maxLength = 47;
        if ($(this).text().length > maxLength) {
            $(this).text($(this).text().substr(0, maxLength) + "...");
        }
    });
});

