const $ = require('jquery');

var mixingApp = {
    init: function() {
        mixingApp.loadAudioContext();
        $('#play').on('click', mixingApp.play)
    },
    loadAudioContext: function(evt) {
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
    },
    loadTrack: function (audio, start) {
            window.fetch(audio)
            .then(response => response.arrayBuffer())
            .then(arrayBuffer => audioContext.decodeAudioData(arrayBuffer))
            .then(audioBuffer => {
                audioSource = audioBuffer;
                source = audioContext.createBufferSource();
                source.buffer = audioBuffer;
                source.startTime = start;
                source.connect(audioContext.destination);
                sources.push(source);
            })
    },
    play: function(evt) {
        // TODO: détecter le status de la lecture
        // if playing -> pause

        // if suspended -> resume

        // if stopped -> play
        $('#play').children('path').attr('d', $('#pause').children('path').attr('d'));
        sources = [];
        var trackList = $('tr[data-audio]');
        console.log(trackList)
        trackList.each(function () {
            mixingApp.loadTrack('http://127.0.0.1:8000/' + $(this).data('audio'), $(this).data('start'))
        });
        var waitLoading = setInterval(function(){
            if(sources.length === trackList.length) {
                $(sources).each(function(track){
                    var startTime = sources[track].startTime;
                    console.log(startTime)
                    sources[track].start(startTime);
                    clearInterval(waitLoading);
                })
            }
        }, 300);
    },
};

$(mixingApp.init);