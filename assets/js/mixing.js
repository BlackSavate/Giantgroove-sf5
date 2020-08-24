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
        $('#stop').on('click', mixingApp.stop);
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
                var totalTime = [];
                $(mixingApp.sources).each(function(track){
                console.log(mixingApp.sources[track]);
                    var startTime = mixingApp.sources[track].startTime;
                    totalTime.push(mixingApp.sources[track].buffer.duration + startTime);
                    mixingApp.sources[track].start(startTime + mixingApp.audioContextStart);
                    clearInterval(waitLoading);
                })
                mixingApp.totalTime = Math.max(...totalTime);
                $('#totalTime').html(mixingApp.secondsToDhms(mixingApp.totalTime));
            }
        }, 300);
        mixingApp.audioContextStart = mixingApp.audioContext.currentTime;
        mixingApp.updateCurrentTime();
    },
    pause: function() {
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
    stop: function() {
        $(mixingApp.sources).each(function(track){
            if(mixingApp.audioContext.state === 'suspended') {
                mixingApp.audioContext.resume()
            }
            mixingApp.sources[track].stop();
        });
        mixingApp.stopTimer();
        $('#pause').children('path').attr('d', mixingApp.originalSVG.play);
        $('#currentTime').html('00:00:00');
        $('#stop').addClass('disabled');
        $('#play').show().focus();
        $('#pause').hide();
        $('#pause').children('path').attr('d', mixingApp.originalSVG.pause);
    },
    onEnd: function(evt) {
        if((evt.target.buffer.duration + evt.target.startTime) == mixingApp.totalTime) {
            mixingApp.stop();
        }
    },
    updateCurrentTime: function () {
        mixingApp.currentTimeInterval = setInterval(function(){
            mixingApp.currentTime =  mixingApp.audioContext.currentTime - mixingApp.audioContextStart;
            var seconds = Math.round(mixingApp.currentTime)
            var currentTimeFormated = mixingApp.secondsToDhms(seconds)
            $('#currentTime').html(currentTimeFormated)
        }, 1000);
    },
    secondsToDhms: function (seconds) {
        seconds = Number(seconds);
        var h = Math.floor(seconds % (3600*24) / 3600);
        var m = Math.floor(seconds % 3600 / 60);
        var s = Math.floor(seconds % 60);

        var hDisplay = h < 10 ? '0'+ h : h;
        var mDisplay = m < 10 ? '0'+ m : m;
        var sDisplay = s < 10 ? '0'+ s : s;
        return hDisplay + ':' + mDisplay + ':' + sDisplay;
    },
    stopTimer: function() {
        clearInterval(mixingApp.currentTimeInterval);
    }
};

$(mixingApp.init);