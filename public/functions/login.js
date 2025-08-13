$("#loginform").on("submit", function (e) {
    e.preventDefault();
    $(".error").html(" ");
    $("#loginbutton").html("Please wait...");

    const submiturl = $("#loginform").attr("action");
    const method = $("#loginform").attr("method");
    const formData = $("#loginform").serialize();

    $.ajax({
        url: submiturl,
        method: method,
        data: formData,
        success: function (result) {
            $("#loginbutton").html("Sign In");
            const to =
                result && result.redirect ? result.redirect : "/dashboard";
                console.log(to);
            window.location.replace(to); // hard redirect so session is used
        },
        error: function (xhr, status, error) {
            $("#loginbutton").html("Sign In");

            if (xhr.responseJSON && xhr.responseJSON.errors) {
                Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                    // Iterate over each error message for the current field
                    xhr.responseJSON.errors[key].forEach(function (value) {
                        // Display each error message in the corresponding element
                        $("#" + key + "-error").html(value);
                    });
                });
            } else {
                $("#message-error").html(xhr.responseJSON.message);
            }
        },
    });
});
