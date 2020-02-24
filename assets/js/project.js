const $ = require('jquery');
import '../css/project.scss';

var projectApp = {
    init: function() {
        $('#add-track').on('click', projectApp.addTrack);
    },
    addTrack: function(evt) {
        var form = $("#tracks");
        var index = $('#tracks tr:last').data('trackid');
        if(undefined === index) {
            index = 0;
        }
        index++;
        var newTrack = $('<tr>');
        newTrack.attr('data-trackid', index);
        var trackname = $('<td>');
        var tracknameInput = $('<input>');
        tracknameInput.addClass('track');
        tracknameInput.attr('name', 'app_project[tracks][track'+index+']');
        var instru = $('<td>');
        var instruInput = $('<input>');
        instruInput.addClass('instru');
        instruInput.attr('name', 'app_project[tracks][instru'+index+']');
        tracknameInput.attr('placeholder', 'Nom de la piste ' + index);
        instruInput.attr('placeholder', 'Instruments ' + index);
        instru.append(instruInput);
        trackname.append(tracknameInput);
        newTrack.append(trackname);
        newTrack.append(instru);
        form.append(newTrack)
    },
};

$(projectApp.init);