// Variable to control message box timer event
var timer = null;

// Alternates forms to display.
function displayForms(value) {

    userLogin = document.querySelector("#userLogin");
    userRegister = document.querySelector("#userRegister");

    loginButton = document.querySelector("#loginButton");
    registerButton = document.querySelector("#registerButton");

    if (value == 'userLogin') {
        userLogin.style.display = 'block';
        userRegister.style.display = 'none';
        loginButton.disabled = true;
        registerButton.disabled = false;

    } else {
        userLogin.style.display = 'none';
        userRegister.style.display = 'block';
        loginButton.disabled = false;
        registerButton.disabled = true;
    }

}

$(document).ready(function () {
    //console.log("Script OK");

    // Login via AJAX
    $('#loginSubmit').click(function () {

        var login = $("#loginInput").val();
        var pass = $("#passwordInput").val();

        $.ajax({
            type: "POST",
            url: "exec.php",
            dataType: "json",
            data: {
                action: "login",
                login: login,
                password: pass
            },
            success: function (data) {
                if (data.success) {
                    $(location).attr("href", "dashboard.php");
                } else {
                    displayMessage(data.message);
                }
            },
            error: function (result) {
                displayMessage('There was an error logging in');
            }
        });

    });

    // Register via AJAX
    $('#registerSubmit').click(function () {

        var name = $("#newUserName").val();
        var login = $("#newUserLogin").val();
        var email = $("#newUserEmail").val();
        var pass = $("#newUserPassword").val();
        var passConfirm = $("#newUserPasswordConfirm").val();

        $.ajax({
            type: "POST",
            url: "exec.php",
            dataType: "json",
            data: {
                action: "register",
                name: name,
                login: login,
                email: email,
                password: pass,
                confirm: passConfirm
            },
            success: function (data) {
                if (data.success) {
                    $(location).attr("href", "dashboard.php");
                } else {
                    displayMessage(data.message);
                }
            },
            error: function (result) {
                displayMessage('There was an error in your register');
            }
        });

    });

    // Update Pass via AJAX
    $('#passSubmit').click(function () {

        var id = $("#userId").val();
        var pass = $("#newUserPassword").val();
        var passConfirm = $("#newUserPasswordConfirm").val();

        $.ajax({
            type: "POST",
            url: "execUser.php",
            dataType: "json",
            data: {
                action: "updatePass",
                password: pass,
                confirm: passConfirm,
                id: id
            },
            success: function (data) {
                if (data.success) {
                    //console.log(data.message);
                    displayMessage(data.message);
                    $("#newUserPassword").val("");
                    $("#newUserPasswordConfirm").val("");
                } else {
                    displayMessage(data.message);
                }
            },
            error: function (result) {
                displayMessage('There was an error in your request');
            }
        });

    });

    // Update User via AJAX
    $('#updateSubmit').click(function () {

        var id = $("#userId").val();
        var name = $("#newUserName").val();
        var login = $("#newUserLogin").val();
        var email = $("#newUserEmail").val();

        $.ajax({
            type: "POST",
            url: "execUser.php",
            dataType: "json",
            data: {
                action: "updateInfo",
                name: name,
                login: login,
                email: email,
                id: id
            },
            success: function (data) {
                if (data.success) {
                    displayMessage(data.message);
                    $("#greetingName").html(name);
                } else {
                    displayMessage(data.message);
                }
            },
            error: function (result) {
                displayMessage('There was an error in your request');
            }
        });

    });

    // Send message
    $('#contactSend').click(function () {
        var message = $("#messageText").val();
        var contactName = $("#contactName").val();
        var contactEmail = $("#contactEmail").val();

        $.ajax({
            type: "POST",
            url: "mail.php",
            dataType: "json",
            data: {
                name: contactName,
                email: contactEmail,
                message: message
            },
            success: function (data) {
                if (data.success) {
                    displayMessage(data.message);
                    $("#messageText").val("");
                    $("#contactName").val("");
                    $("#contactEmail").val("");
                } else {
                    displayMessage(data.message);
                }
            },
            error: function (result) {
                displayMessage('There was an error sending your message');
            }
        });
    });

    // Show Search
    $('#showSearch').click(function () {
        $("#search").toggleClass("hide");
        $('#showSearch').toggleClass("hidden");
    });
    // Hide Search
    $('#closeSearch').click(function () {
        $("#search").toggleClass("hide");
        $('#showSearch').toggleClass("hidden");
    });

    //Show menu
    $('#showMenu').click(function () {
        $("#siteNav").addClass("displaySiteNav");
    });

    //Hide menu
    $('#closeSiteNav').click(function () {
        $("#siteNav").removeClass("displaySiteNav");
    });

    //Show user menu
    $('#showUserMenu').click(function () {
        $("#userNav").addClass("displayUserNav");
    });

    //Hide user menu
    $('#closeUserNav').click(function () {
        $("#userNav").removeClass("displayUserNav");
    });

    //Show subcategories
    $('li.category > span').click(function () {
        $(this).siblings("ul.subcategories").toggleClass("hide");
        $(this).toggleClass("fa-angle-down");
        $(this).toggleClass("fa-angle-up");
    });

    // Submit search
    $('#searchSubmit').click(function () {
        $('#searchForm').submit();
    });

    // Close message box
    $('#closeMessage').click(function () {
        $("#message").removeClass("showMessage");
        clearInterval(timer);
        timer = null;
    });

    //Implement form validations for search
    $.validate({
        form: '#searchForm'
    });


    // Cancel reservation in dashboard table
    $('span.cancelReservation').click(function () {
        var parent = $(this).closest("tr");
        var item_id = $(this).attr("itemId");

        $.ajax({
            type: "POST",
            url: "execUser.php",
            dataType: "json",
            data: {
                action: "cancelReserve",
                item_id: item_id
            },
            success: function (data) {
                if (data.success) {
                    displayMessage(data.message);
                    parent.remove();

                } else {
                    displayMessage(data.message);
                }
            },
            error: function (result) {
                displayMessage('There was an error in your request');
            }
        });
    });

    // Cancel / reserve
    $('span.reservation').click(function (event) {
        var element = $(this);
        var item_id = $(this).attr("itemId");
        var action = $(this).attr("calltoaction");

        $.ajax({
            type: "POST",
            url: "execUser.php",
            dataType: "json",
            data: {
                action: action,
                item_id: item_id
            },
            success: function (data) {
                if (data.success) {
                    displayMessage(data.message);

                    if (action == 'reserveItem') {
                        element.attr("calltoaction", "cancelReserve");
                        element.html("Cancel");
                    } else {
                        element.attr("calltoaction", "reserveItem");
                        element.html("Reserve");
                    }

                } else {
                    displayMessage(data.message);
                }
            },
            error: function (result) {
                displayMessage('There was an error in your request');
            }
        });
    });

});

// Displays the message box whith given text and sets it to hide in 4 seconds
function displayMessage(message) {
    $("#message").addClass("showMessage");

    $("#message .messageText").html();

    var html = "";

    message.forEach(function (item) {

        html += item + "<br>";
    });


    $("#message .messageText").html(html);

    clearInterval(timer);
    timer = window.setInterval(function () {
        $("#message").removeClass("showMessage");
    }, 4000);
}
