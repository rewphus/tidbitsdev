$(document).ready(function() {

    /* platform checkbox change */
    $("[id^='platform'].panel-footer :checkbox").change(function() {

        // get the game and platform id out of the checkbox id
        match = this.id.match("platform_([0-9]+)_([0-9]+)");
        // if ids found and checkbox checked
        if (match.length == 3) {
            $(this).prop('disabled', true); // disable checkbox
            var checkbox = this;
            if (this.checked) {
                // add platform
                $.ajax({
                    type: 'POST',
                    url: '/games/addPlatform',
                    dataType: 'json',
                    data: {
                        GBID: match[1],
                        platformID: match[2]
                    },
                    success: function(data) {
                        if (data.error === true) {
                            $(checkbox).prop('disabled', false); // enable checkbox
                            $(checkbox).prop('checked', false); // reset to unchecked as add failed
                            showErrorModal(data.errorMessage, data.errorProgressURL, data.errorProgressCTA);
                        } else {
                            $(checkbox).prop('disabled', false); // enable checkbox
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $(checkbox).prop('disabled', false); // enable checkbox
                        $(checkbox).prop('checked', false); // reset to unchecked as add failed
                        showErrorModal('Well Platform: ' + +console.log(XMLHttpRequest.responseText));
                    }
                });
            } else {
                // remove platform
                $.ajax({
                    type: 'POST',
                    url: '/games/removePlatform',
                    dataType: 'json',
                    data: {
                        GBID: match[1],
                        platformID: match[2]
                    },
                    success: function(data) {
                        if (data.error === true) {
                            $(checkbox).prop('disabled', false); // enable checkbox
                            $(checkbox).prop('checked', true); // reset to checked as remove failed
                            showErrorModal(data.errorMessage);
                        } else {
                            $(checkbox).prop('disabled', false); // enable checkbox
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $(checkbox).prop('disabled', false); // enable checkbox
                        $(checkbox).prop('checked', true); // reset to checked as remove failed
                        showErrorModal('Well 2. Some kind of error gone done happened. Please try again.');
                    }
                });
            }
        }
    });
});



/* add/update game status in collection */
function addGame(giantbombID, listID, reloadPage) {
    var MotivationID = 99;
    var StatusID = 0;
    var FutureID = 0;
    var ValueID = 0;

    if (listID == 1) {
        StatusID = 1;
        FutureID = 1;
        ValueID = 1;
    } else if (listID == 2) {
        StatusID = 6;
        FutureID = 1;
        ValueID = 1;
    } else {
        StatusID = 9;
        FutureID = 1;
        ValueID = 1;
    }

    $('#gameButton' + giantbombID).addClass('disabled').html('Saving...');
    $.ajax({
        type: 'POST',
        url: '/games/add',
        dataType: 'json',
        data: {
            GBID: giantbombID,
            listID: listID,
            statusID: StatusID
        },
        success: function(data) {
            console.log("addGame: " + data);
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                if (reloadPage) {
                    location.reload(); //need to make this not require reload
                } else {
                    // update list button label/colour
                    $('#gameButton' + giantbombID).html(data.listName + ' <span class="caret"></span>').removeClass().addClass("btn btn-" + data.listStyle + " dropdown-toggle");
                    // display collection status button
                    $('#inCollectionControls' + giantbombID).removeClass("hidden");
                    // display collection status dropdown
                    $('#statusDropdown' + listID).removeClass("hidden");
                    // $('#concepts' + giantbombID).find('input[type=checkbox]').prop('checked', true);
                    // enable platform checkboxes
                    $('#platforms' + giantbombID).find('input[type=checkbox]').prop('readonly', false);
                    // if a platform was auto-selected, update checkbox
                    if (data.autoSelectPlatform != null) {
                        $('#platform_' + giantbombID + '_' + data.autoSelectPlatform).prop('checked', true);
                    }

                }
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('addGame' + console.log(XMLHttpRequest.responseText));
        }
    });
}

/* update game played motivation */
function changeMotivation(giantbombID, motivationID) {
    $('#motivationButton' + giantbombID).addClass('disabled').html('Saving...');
    $.ajax({
        type: 'POST',
        url: '/games/changeMotivation',
        dataType: 'json',
        data: {
            GBID: giantbombID,
            motivationID: motivationID
        },
        success: function(data) {
            if (data.error === true) {
                console.log("changeMotivation: " + JSON.parse(JSON.stringify(data)));
                showErrorModal(data.errorMessage);
            } else {
                location.reload(); //need to make this not require reload
                $('#motivationButton' + giantbombID).html(data.motivationName + ' <span class="caret"></span>').removeClass().addClass("btn btn-" + data.motivationStyle + " dropdown-toggle");
            }
        },
        error: function(XMLHttpRequest, textMotivation, errorThrown) {
            showErrorModal('changeMotivation' + console.log(XMLHttpRequest.responseText));
        }
    });
}

/* update game played status */
function changeStatus(giantbombID, statusID) {
    $('#statusButton' + giantbombID).addClass('disabled').html('Saving...');
    $.ajax({
        type: 'POST',
        url: '/games/changeStatus',
        dataType: 'json',
        data: {
            GBID: giantbombID,
            statusID: statusID
        },
        success: function(data) {
            console.log("changeStatus: " + JSON.parse(JSON.stringify(data)));
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                location.reload(); //need to make this not require reload
                $('#statusButton' + giantbombID).html(data.statusName + ' <span class="caret"></span>').removeClass().addClass("btn btn-" + data.statusStyle + " dropdown-toggle");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('changeStatus' + console.log(XMLHttpRequest.responseText));
        }
    });
}

/* update game played future */
function changeFuture(giantbombID, futureID) {
    $('#futureButton' + giantbombID).addClass('disabled').html('Saving...');
    $.ajax({
        type: 'POST',
        url: '/games/changeFuture',
        dataType: 'json',
        data: {
            GBID: giantbombID,
            futureID: futureID
        },
        success: function(data) {
            console.log("changeFuture: " + JSON.parse(JSON.stringify(data)));
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                $('#futureButton' + giantbombID).html(data.futureName + ' <span class="caret"></span>').removeClass().addClass("btn btn-" + data.futureStyle + " dropdown-toggle");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('changeFuture' + console.log(XMLHttpRequest.responseText));
        }
    });
}

/* update game played value */
function changeValue(giantbombID, valueID) {
    $('#valueButton' + giantbombID).addClass('disabled').html('Saving...');
    $.ajax({
        type: 'POST',
        url: '/games/changeValue',
        dataType: 'json',
        data: {
            GBID: giantbombID,
            valueID: valueID
        },
        success: function(data) {
            console.log("changeValue: " + JSON.parse(JSON.stringify(data)));
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                $('#valueButton' + giantbombID).html(data.valueName + ' <span class="caret"></span>').removeClass().addClass("btn btn-" + data.valueStyle + " dropdown-toggle");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('changeValue' + console.log(XMLHttpRequest.responseText));
        }
    });
}

/* update game played value */
function setValue(giantbombID, statusID, futureID) {
    var valueID = 99;

    if ((statusID == 1 || statusID == 2) & futureID == 1) { valueID = 1 } else
    if (statusID == 1 & futureID == 2) { valueID = 2 };

    console.log("valueID: " + valueID);

    $('#valueButton' + giantbombID).addClass('disabled').html('Saving...');
    $.ajax({
        type: 'POST',
        url: '/games/changeValue',
        dataType: 'json',
        data: {
            GBID: giantbombID,
            valueID: valueID
        },
        success: function(data) {
            console.log("success setValue: " + JSON.parse(JSON.stringify(data)));
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                $('#valueButton' + giantbombID).html(data.valueName + ' <span class="caret"></span>').removeClass().addClass("btn btn-" + data.valueStyle + " dropdown-toggle");
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('setValue ' + console.log(XMLHttpRequest.responseText));
        }
    });
}

/* display warning modal for removing game */
function showRemoveGameWarning(giantbombID) {
    $('#removeGameButtonPlaceholder').html("<a id='removeGameButton" + giantbombID + "' onclick='javascript:removeFromCollection(" + giantbombID + ");'' class='btn btn-danger'>Remove from Collection</a>");
    $('#removeGameModal').modal();
}

/* remove game from collection */
function removeFromCollection(giantbombID) {
    $('#removeGameButton' + giantbombID).addClass('disabled').html('Removing...');
    $.ajax({
        type: 'POST',
        url: '/games/remove',
        dataType: 'json',
        data: {
            GBID: giantbombID
        },
        success: function(data) {
            if (data.error === true) {
                $('#removeGameModal').modal('hide');
                showErrorModal(data.errorMessage);
            } else {
                // reload page to refresh state
                location.reload();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#removeGameModal').modal('hide');
            showErrorModal('Well remove. Some kind of error gone done happened. Please try again.');
        }
    });
}

/* save progression information */
function saveProgression(giantbombID) {
    $('#progressionSaveButton').addClass('disabled').html('Saving...');
    $.ajax({
        type: 'POST',
        url: '/games/saveProgression',
        dataType: 'json',
        data: {
            GBID: giantbombID,
            currentlyPlaying: $('#currentlyPlayingInput').val(),
            hoursPlayed: $('#hoursPlayedInput').val(),
            dateCompleted: $('#dateCompletedInput').val()
        },
        success: function(data) {
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                $('#progressionSaveButton').removeClass('disabled').html('Saved');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well save. Some kind of error gone done happened. Please try again.');
        }
    });
}