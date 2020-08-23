const $ = require('jquery');
import '../css/mixing.scss';

var mixingApp = {
    audioContext: new (window.AudioContext || window.webkitAudioContext)(),
    sources: [],
    originalSVG: {
        'play': $('#play').children('path').attr('d'),
        'pause': $('#pause').children('path').attr('d'),
    },
    init: function() {
        $('#play').on('click', mixingApp.play);
        $('#pause').on('click', mixingApp.pause);
    },
    loadTrack: function (audio, start) {
            window.fetch(audio)
            .then(response => response.arrayBuffer())
            .then(arrayBuffer => mixingApp.audioContext.decodeAudioData(arrayBuffer))
            .then(audioBuffer => {
                var source = mixingApp.audioContext.createBufferSource();
                source.buffer = audioBuffer;
                source.startTime = start;
                source.connect(mixingApp.audioContext.destination);
                mixingApp.sources.push(source);
            })
    },
    play: function() {
        $('#play').hide();
        $('#pause').show().focus();
        $('#stop').removeClass('disabled');
        mixingApp.sources = [];
        var trackList = $('tr[data-audio]');
        trackList.each(function () {
            mixingApp.loadTrack('http://127.0.0.1:8000/' + $(this).data('audio'), $(this).data('start'))
        });
        var waitLoading = setInterval(function(){
            if(mixingApp.sources.length === trackList.length) {
                $(mixingApp.sources).each(function(track){
                    var startTime = mixingApp.sources[track].startTime;
                    mixingApp.sources[track].start(startTime);
                    clearInterval(waitLoading);
                })
            }
        }, 300);
    },
    pause: function() {
        // TODO: détecter le status de la lecture
        // if playing -> pause
        if(mixingApp.audioContext.state === 'running') {
            mixingApp.audioContext.suspend();
            $('#pause').children('path').attr('d', mixingApp.originalSVG.play);
        }
        // if suspended -> resume
        else if (mixingApp.audioContext.state === 'suspended') {
            mixingApp.audioContext.resume();
            $('#pause').children('path').attr('d', mixingApp.originalSVG.pause);
        }
        else {
            console.log('ERROR')
        }
    },
    onEnd: function(evt) {

    },
};

$(mixingApp.init);