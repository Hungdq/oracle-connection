<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <script>
        var server = "/"
    </script>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"
            integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"
            integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.5/TweenMax.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.5/jquery.gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.5/plugins/ScrollToPlugin.min.js"></script>
    <script src="../js/freewall.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsrender/0.9.76/jsrender.min.js"></script>
    <link href="https://code.jquery.com/ui/1.11.4/themes/cupertino/jquery-ui.css" rel="stylesheet">
    <style>
        html body {
            font-family: Arial, Helvetica, sans-serif
        }

        label,
        input {
            display: block;
            font-size: 80%;
        }

        input.text {
            margin-bottom: 12px;
            width: 95%;
            padding: .4em;
        }

        fieldset {
            padding: 0;
            border: 0;
            margin-top: 25px;
            font-size: 75%;
        }

        .container {
            width: 90%;
            margin: auto;
        }

        .peopleContent {
            flex-grow: 1;
        }

        .portrait {
            width: 120px;
        }

        .peopleItem {
            width: 350px;
            border: 1px solid #777777;
            background: #FAFAFA;
            margin: 3px;
            padding: 2px;
            display: flex;
        }

        .peopleTitle {
            width: 100%;
            background: #FAFAFA;
            margin: 3px;
            padding: 2px;
            display: flex;
        }

        #editDialog {
            display: none;
        }

        #addDialog {
            display: none;
        }
    </style>

    <script id="peopleTemplate" type="text/x-jsrender">
    <div class="peopleItem">
      <div class="portrait"><img src="../img/nopic.png"></div>
      <div class="peopleContent">
        <h4>@{{:firstName}} @{{:lastName}}</h4>
        @{{:title}}<br>
        <small>@{{:department}}</small>
      </div>
    </div>

    </script>
    <script id="detailTemplate" type="text/x-jsrender">
    <div class="peopleTitle">
      <div class="portrait"><a href="#" class="backButton"><img src="../img/back.png"></a></div>
      <div class="portrait"><img src="../img/nopic.png"></div>
      <div class="peopleContent">
        <h3>@{{:firstName}} @{{:lastName}}</h3>
        @{{:title}}<br>
        <small>@{{:department}}</small>
      </div>
    </div>
    <div style="padding-left:35px;">
      <img src="../img/birthdate.png">@{{:birthDate}}
    </div>
    <div style="padding-left:35px;">
      <a href="mailto:@{{:email}}"><img src="../img/email.png">@{{:email}}</a>
      <div style="padding-left:35px;">
      </div>
      <a href="tel:@{{:phone}}"><img src="../img/phone.png"> @{{:phone}}</a>
    </div>
    <div style="padding-left:35px;">
      <a href="#" class="editButton"><img src="../img/edit.png">Edit</a>
      <div style="padding-left:35px;">
      </div>
      <a href="#" class="deleteButton"><img src="../img/delete.png">Delete</a>
    </div>




    </script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <script>

        $(function () {
            var peopleTemplate = $.templates("#peopleTemplate");
            var detailTemplate = $.templates("#detailTemplate");

            var wall = new Freewall(".container");
            wall.reset({
                selector: '.peopleItem',
                animate: true,
                cellW: 350,
                cellH: 'auto',
                onResize: function () {
                    wall.fitWidth();
                }
            });


            function bindDetail(element, employee) {
                element.find(".backButton").on("click", function () {
                    $("#detail").hide(400, "swing", function () {
                        $("#people").show(400, "swing")
                    });
                });
                element.find(".deleteButton").on("click", function () {
                    $('<div></div>').dialog({
                        modal: true,
                        title: "Confirm Delete",
                        open: function () {
                            var markup = 'Are you sure you want to delete ' + employee.firstName + ' ' + employee.lastName + "?";
                            $(this).html(markup);
                        },
                        buttons: {
                            Ok: function () {
                                $("#detail").html("DELETING...");
                                $(this).dialog("close");
                                $.ajax({
                                    url: server + "employees/" + employee.id,
                                    method: "DELETE"
                                }).done(function (data) {
                                    $("#detail").hide();
                                    loadEmployees();
                                });
                            },
                            Cancel: function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                });
                element.find(".editButton").on("click", function () {
                    $("#editFirstName").val(employee.firstName);
                    $("#editLastName").val(employee.lastName);
                    $("#editEmail").val(employee.email);
                    $("#editPhone").val(employee.phone);
                    $("#editBirthDate").val(employee.birthDate);
                    $("#editTitle").val(employee.title);
                    $("#editDept").val(employee.department);

                    $('#editDialog').dialog({
                        modal: true,
                        title: employee.firstName + ' ' + employee.lastName,
                        buttons: {
                            "Update": function () {
                                var editEmployee = {
                                    firstName: $("#editFirstName").val(),
                                    lastName: $("#editLastName").val(),
                                    email: $("#editEmail").val(),
                                    phone: $("#editPhone").val(),
                                    birthDate: $("#editBirthDate").val(),
                                    title: $("#editTitle").val(),
                                    dept: $("#editDept").val()
                                };
                                $("#detail").html("UPDATING...");
                                $(this).dialog("close");
                                $.ajax({
                                    url: server + "employees/" + employee.id,
                                    method: "PUT",
                                    data: JSON.stringify(editEmployee),
                                    contentType: 'application/json',
                                    success: function (data) {
                                        $("#detail").hide();
                                        loadEmployees();
                                    }
                                }).done(function (data) {

                                });
                            },
                            Cancel: function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                });
            }

            $("#addButton").button().on("click", function () {

                $("#addFirstName").val("");
                $("#addLastName").val("");
                $("#addEmail").val("");
                $("#addPhone").val("");
                $("#addBirthDate").val("");
                $("#addTitle").val("");
                $("#addDept").val("");

                $("#addDialog").dialog({
                    modal: true,
                    title: "Add new employee",
                    buttons: {
                        "Add": function () {
                            var addEmployee = {
                                firstName: $("#addFirstName").val(),
                                lastName: $("#addLastName").val(),
                                email: $("#addEmail").val(),
                                phone: $("#addPhone").val(),
                                birthDate: $("#addBirthDate").val(),
                                title: $("#addTitle").val(),
                                dept: $("#addDept").val()
                            };
                            $("#detail").html("ADDING...");
                            $(this).dialog("close");
                            $.ajax({
                                url: server + "employees",
                                method: "POST",
                                data: JSON.stringify(addEmployee),
                                contentType: 'application/json',
                            }).done(function (data) {
                                $("#detail").hide();
                                loadEmployees();
                            });
                        },
                        "Cancel": function () {
                            $(this).dialog("close");
                        }
                    }
                });
            });

            $("#searchButton").button().on("click", function () {
                var searchTerm = $("#searchText").val().trim();
                if (searchTerm != "") {
                    $("#people").show();
                    $("#people").html("SEARCHING...");
                    $.ajax({
                        url: server + "employees/" + $("#searchType").val() + "/" + encodeURIComponent(searchTerm),
                        method: "GET"
                    }).done(function (data) {
                        $("#people").html("");
                        if (data.length == 0) {
                            $("#people").html("No results found...");
                        } else {
                            data.forEach(function (employee) {
                                var item = $(peopleTemplate.render(employee));
                                item.on("click", function () {
                                    var detailItem = $(detailTemplate.render(employee));
                                    $("#detail").empty();
                                    $("#detail").append(detailItem);
                                    bindDetail(detailItem, employee);
                                    $("#people").hide(400, "swing", function () {
                                        $("#detail").show(400, "swing")
                                    });
                                });
                                $("#people").append(item);
                            });
                        }
                        wall.fitWidth();
                    });
                }
            });
            $("#searchText").on("keyup", function (e) {
                if (e.keyCode == 13) {
                    $("#searchButton").trigger("click");
                }
            });

            function loadEmployees() {
                $("#people").show();
                $("#people").html("LOADING...");
                $.ajax({
                    url: server + "employees",
                    dataType: "json",
                    method: "GET"
                }).done(function (data) {
                    $("#people").empty();
                    data.forEach(function (employee) {

                        var item = $(peopleTemplate.render(employee));
                        item.on("click", function () {
                            var detailItem = $(detailTemplate.render(employee));
                            $("#detail").empty();
                            $("#detail").append(detailItem);
                            bindDetail(detailItem, employee);
                            $("#people").hide(400, "swing", function () {
                                $("#detail").show(400, "swing")
                            });
                        });
                        $("#people").append(item);
                    })
                    wall.fitWidth();
                });
            }

            loadEmployees();
        });
    </script>
</head>

<body>
<h1>Cloud Employee App</h1>
<nav>
    <h2>People</h2>
    <table width="100%">
        <tr>
            <td>
                <input style="display:inline-block;" type="text" size="20" id="searchText"><select id="searchType">
                    <option value="lastname">By last name</option>
                    <option value="department">By department</option>
                    <option value="title">By title</option>
                </select>
                <input id="searchButton" value="search" type="button">
            </td>
            <td align="right" width="10%">
                <input id="addButton" value="Add New" type="button">
            </td>
        </tr>
    </table>
</nav>
<div id="people" class="container">
    LOADING...
</div>
<div id="detail">
</div>

<div id="editDialog">
    <p class="validateTips">All form fields are required.</p>
    <form>
        <fieldset>

            <label for="editFirstName">First Name</label><input type="text" id="editFirstName" value=""
                                                                class="text ui-widget-content ui-corner-all">
            <label for="editLastName">Last Name</label><input type="text" id="editLastName" value=""
                                                              class="text ui-widget-content ui-corner-all">
            <label for="editEmail">Email</label><input type="text" id="editEmail" value=""
                                                       class="text ui-widget-content ui-corner-all">
            <label for="editPhone">Phone</label><input type="text" id="editPhone" value=""
                                                       class="text ui-widget-content ui-corner-all">
            <label for="editBirthDate">Birthdate</label><input type="text" id="editBirthDate" value=""
                                                               class="text ui-widget-content ui-corner-all">
            <label for="editTitle">Title</label><input type="text" id="editTitle" value=""
                                                       class="text ui-widget-content ui-corner-all">
            <label for="editDept">Department</label><input type="text" id="editDept" value=""
                                                           class="text ui-widget-content ui-corner-all">

            <!-- Allow form submission with keyboard without duplicating the dialog button -->
            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
        </fieldset>
    </form>
</div>

<div id="addDialog">
    <p class="validateTips">All form fields are required.</p>
    <form>
        <fieldset>

            <label for="addFirstName">First Name</label><input type="text" id="addFirstName" value=""
                                                               class="text ui-widget-content ui-corner-all">
            <label for="addLastName">Last Name</label><input type="text" id="addLastName" value=""
                                                             class="text ui-widget-content ui-corner-all">
            <label for="addEmail">Email</label><input type="text" id="addEmail" value=""
                                                      class="text ui-widget-content ui-corner-all">
            <label for="addPhone">Phone</label><input type="text" id="addPhone" value=""
                                                      class="text ui-widget-content ui-corner-all">
            <label for="addBirthDate">Birthdate</label><input type="text" id="addBirthDate" value=""
                                                              class="text ui-widget-content ui-corner-all">
            <label for="addTitle">Title</label><input type="text" id="addTitle" value=""
                                                      class="text ui-widget-content ui-corner-all">
            <label for="addDept">Department</label><input type="text" id="addDept" value=""
                                                          class="text ui-widget-content ui-corner-all">

            <!-- Allow form submission with keyboard without duplicating the dialog button -->
            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
        </fieldset>
    </form>
</div>
</body>

</html>