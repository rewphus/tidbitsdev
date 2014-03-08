$(document).ready(function() {
    $(":checkbox").change(function(){
        // get the game and platform id out of the checkbox id
        match = this.id.match("platform_([0-9]+)_([0-9]+)");
        // if ids found and checkbox checked
        if(match.length == 3)
        {
            $(this).prop('disabled', true); // disable checkbox
            var checkbox = this;
            if(this.checked) {
                // add platform
                $.ajax({
                    type : 'POST',
                    url : baseUrl + 'games/addPlatform',
                    dataType : 'json',
                    data: {
                        gbID: match[1],
                        platformID: match[2]
                    },
                    success : function(data){
                        if (data.error === true) {
                            $(checkbox).prop('disabled', false); // enable checkbox
                            $(checkbox).prop('checked', false); // reset to unchecked as add failed
                            showErrorModal(data.errorMessage);
                        } else {
                            $(checkbox).prop('disabled', false); // enable checkbox
                        }
                    },
                    error : function(XMLHttpRequest, textStatus, errorThrown) {
                        $(checkbox).prop('disabled', false); // enable checkbox
                        $(checkbox).prop('checked', false); // reset to unchecked as add failed
                        showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
                    }
                });
            } else {
                 // remove platform
                $.ajax({
                    type : 'POST',
                    url : baseUrl + 'games/removePlatform',
                    dataType : 'json',
                    data: {
                        gbID: match[1],
                        platformID: match[2]
                    },
                    success : function(data){
                        if (data.error === true) {
                            $(checkbox).prop('disabled', false); // enable checkbox
                            $(checkbox).prop('checked', true); // reset to checked as remove failed
                            showErrorModal(data.errorMessage);
                        } else {
                            $(checkbox).prop('disabled', false); // enable checkbox
                        }
                    },
                    error : function(XMLHttpRequest, textStatus, errorThrown) {
                        $(checkbox).prop('disabled', false); // enable checkbox
                        $(checkbox).prop('checked', true); // reset to checked as remove failed
                        showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
                    }
                });
            }
        }
    });
});

function showErrorModal(error) {
    $('#errorModalMessage').html(error);
    $('#errorModal').modal();
}

/* add/update game status in collection */
function addGame(giantbombID, listID) {
    $('#gameButton' + giantbombID).addClass('disabled').html('Saving...');
    $.ajax({
        type : 'POST',
        url : baseUrl + 'games/add',
        dataType : 'json',
        data: {
            gbID: giantbombID,
            listID: listID
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                switch(listID)
                {
                    case 1:
                        buttonLabel = "Own";
                        buttonStyle = "success";
                        break;
                    case 2:
                        buttonLabel = "Want";
                        buttonStyle = "warning";
                        break;
                    case 3:
                        buttonLabel = "Borrowed";
                        buttonStyle = "info";
                        break;
                    case 4:
                        buttonLabel = "Lent";
                        buttonStyle = "danger";
                        break;
                    case 5:
                        buttonLabel = "Played";
                        buttonStyle = "primary";
                        break;
                }
                // update list button label/colour
                $('#gameButton' + giantbombID).html(buttonLabel + ' <span class="caret"></span>').removeClass().addClass("btn btn-" + buttonStyle + " dropdown-toggle");
                // display collection status button
                $('#inCollectionControls' + giantbombID).removeClass("hidden");
                // enable platform checkboxes
                $('#platforms' + giantbombID).find('input[type=checkbox]').prop('disabled', false);
                // if a platform was auto-selected, update checkbox
                if(data.autoSelectPlatform != null)
                {
                    $('#platform_' + giantbombID + '_' + data.autoSelectPlatform).prop('checked', true);
                }
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}

/* update game played status */
function changeStatus(giantbombID, statusID) {
    $('#statusButton' + giantbombID).addClass('disabled').html('Saving...');
    $.ajax({
        type : 'POST',
        url : baseUrl + 'games/changeStatus',
        dataType : 'json',
        data: {
            gbID: giantbombID,
            statusID: statusID
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                switch(statusID)
                {
                    case 1:
                        buttonLabel = "Unplayed";
                        buttonStyle = "default";
                        break;
                    case 2:
                        buttonLabel = "Unfinished";
                        buttonStyle = "warning";
                        break;
                    case 3:
                        buttonLabel = "Complete";
                        buttonStyle = "success";
                        break;
                    case 4:
                        buttonLabel = "Uncompletable";
                        buttonStyle = "primary";
                        break;
                }
                $('#statusButton' + giantbombID).html(buttonLabel + ' <span class="caret"></span>').removeClass().addClass("btn btn-" + buttonStyle + " dropdown-toggle");
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}

function showRemoveGameWarning(giantbombID) {
    $('#removeGameButtonPlaceholder').html("<a id='removeGameButton" + giantbombID + "' onclick='javascript:removeFromCollection(" + giantbombID + ");'' class='btn btn-danger'>Remove from Collection</a>");
    $('#removeGameModal').modal();
}

/* remove game from collection */
/* TODO: add warning dialogue */
function removeFromCollection(giantbombID) {
    $('#removeGameButton' + giantbombID).addClass('disabled').html('Removing...');
    $.ajax({
        type : 'POST',
        url : baseUrl + 'games/remove',
        dataType : 'json',
        data: {
            gbID: giantbombID
        },
        success : function(data){
            if (data.error === true) {
                $('#removeGameModal').modal('hide');
                showErrorModal(data.errorMessage);
            } else {
                // redirect to same page to refresh state
                window.location = document.URL;
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}

/* delete blog post */
function deleteBlogPost(ID) {
    $.ajax({
        type : 'POST',
        url : baseUrl + 'admin/deleteBlogPost',
        dataType : 'json',
        data: {
            postID: ID
        },
        success : function(data){
            if (data.error === true) {
                showErrorModal(data.errorMessage);
            } else {
                window.location = baseUrl + 'admin/blog/edit';
            }
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorModal('Well shit. Some kind of error gone done happened. Please try again.');
        }
    });
}