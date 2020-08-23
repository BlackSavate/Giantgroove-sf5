const $ = require('jquery');
import '../css/mixing.scss';

var mixingApp = {
    audioContext: new (window.AudioContext || window.webkitAudioContext)(),
    audioContextStart:undefined,
    sources: [],
    originalSVG: {
        'play': $('#play').children('path').attr('d'),
        'pause': $('#pause').children('path').attr('d'),
    },
    init: function() {
        $('#play').on('click', mixingApp.play);
        $('#pause').on('click', mixingApp.pause);
    },
    getTrackListData: function(data) {
        var trackList = $('tr[data-audio]');
        if(!data) {
            return trackList;
        }
        var dataVal = [];
        trackList.each(function() {
            dataVal.push($(this).data(data))
        })
        return dataVal;
    },
    loadTrack: function (audio, start) {
            window.fetch(audio)
            .then(response => response.arrayBuffer())
            .then(arrayBuffer => mixingApp.audioContext.decodeAudioData(arrayBuffer))
            .then(audioBuffer => {
                var source = mixingApp.audioContext.createBufferSource();
                source.buffer = audioBuffer;
                source.startTime = start;
                source.onended = mixingApp.onEnd;
                source.connect(mixingApp.audioContext.destination);
                mixingApp.sources.push(source);
            })
    },
    play: function() {
        $('#play').hide();
        $('#pause').show().focus();
        $('#stop').removeClass('disabled');
        mixingApp.sources = [];
        var trackList = mixingApp.getTrackListData();
        trackList.each(function () {
            mixingApp.loadTrack('http://127.0.0.1:8000/' + $(this).data('audio'), $(this).data('start'))
        });
        var waitLoading = setInterval(function(){
            if(mixingApp.sources.length === trackList.length) {
                $(mixingApp.sources).each(function(track){
                    var startTime = mixingApp.sources[track].startTime;
                    mixingApp.sources[track].start(startTime + mixingApp.audioContextStart);
                    clearInterval(waitLoading);
                })
            }
        }, 300);
        mixingApp.audioContextStart = mixingApp.audioContext.currentTime;
        mixingApp.updateCurrentTime();
    },
    pause: function() {
        console.log(mixingApp.getTrackListData('audio'))
        if(mixingApp.audioContext.state === 'running') {
            mixingApp.audioContext.suspend();
            $('#pause').children('path').attr('d', mixingApp.originalSVG.play);
        }
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
    updateCurrentTime: function () {
        var currentTimeInterval = setInterval(function(){
            mixingApp.currentTime =  mixingApp.audioContext.currentTime - mixingApp.audioContextStart;
            $('#currentTime').html(Math.round(mixingApp.currentTime))
        }, 1000);
    }
};

$(mixingApp.init);