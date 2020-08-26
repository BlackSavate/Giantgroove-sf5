/**
 *   schéma de câblage
 *
 *   | source |----->| panner |----->| gain |----->| destination |
 *
 **/
const $ = require('jquery');
import '../css/mixing.scss';

var mixingApp = {
    audioContext: new (window.AudioContext || window.webkitAudioContext)(),
    audioContextStart:undefined,
    sources: [],
    panners: [],
    gains: [],
    originalSVG: {
        'play': $('#play').children('path').attr('d'),
        'pause': $('#pause').children('path').attr('d'),
    },
    init: function() {
        $('#play').on('click', mixingApp.play);
        $('#pause').on('click', mixingApp.pause);
        $('#stop').on('click', mixingApp.stop);
        $('#volumePlus').on('click', mixingApp.volumePlus);
        $('.volumeBar').on('change', mixingApp.changeVolume);
        $('.panBar').on('change', mixingApp.changePan);
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
    loadTrack: function (track) {
        var audio = 'http://127.0.0.1:8000/' + track.data('audio');
        var start = track.data('start');
        var id = track.data('id');
        window.fetch(audio)
            .then(response => response.arrayBuffer())
            .then(arrayBuffer => mixingApp.audioContext.decodeAudioData(arrayBuffer))
            .then(audioBuffer => {
                var source = mixingApp.audioContext.createBufferSource();
                var gainNode = mixingApp.audioContext.createGain();
                var stereoPanner = mixingApp.audioContext.createStereoPanner();
                source.buffer = audioBuffer;
                source.startTime = start;
                source.onended = mixingApp.onEnd;
                source.connect(gainNode);
                gainNode.connect(stereoPanner);
                stereoPanner.connect(mixingApp.audioContext.destination);
                source.id = id;
                stereoPanner.id = id;
                gainNode.id = id;
                mixingApp.sources.push(source);
                mixingApp.panners.push(stereoPanner);
                mixingApp.gains.push(gainNode);
                mixingApp.changeVolume($('.volumeBar[data-id='+id+']').first());
                mixingApp.changePan($('.panBar[data-id='+id+']').first());
            })
    },
    play: function() {
        $('#play').hide();
        $('#pause').show().focus();
        $('#stop').removeClass('disabled');
        mixingApp.sources = [];
        mixingApp.gains = [];
        var trackList = mixingApp.getTrackListData();
        trackList.each(function () {
            mixingApp.loadTrack($(this))
        });
        var waitLoading = setInterval(function(){
            if(mixingApp.sources.length === trackList.length) {
                var totalTime = [];
                $(mixingApp.sources).each(function(track){
                    var startTime = mixingApp.sources[track].startTime;
                    totalTime.push(mixingApp.sources[track].buffer.duration + startTime);
                    mixingApp.sources[track].start(startTime + mixingApp.audioContextStart);
                    clearInterval(waitLoading);
                })
                mixingApp.totalTime = Math.max(...totalTime);
                $('#totalTime').html(mixingApp.secondsToDhms(mixingApp.totalTime));
                $(mixingApp.sources).each(function(){
                    $('#visual-track-'+this.id).css({
                        'width': ((this.buffer.duration / mixingApp.totalTime)*100) + '%',
                        'left': ((this.startTime/mixingApp.totalTime*100)+'%')
                    })

                })
            }
        }, 1500);
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
        }, 250);
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
    },
    changeVolume: function(evt) {
        var volume =     ($(evt.target) != undefined ) ? $(evt.target).val()      : evt.val();
        var trackId = ($(evt.target) != undefined ) ? $(evt.target).data('id') : evt.data('id');

        $(mixingApp.sources).each(function(track) {
            if(mixingApp.gains[track].id == trackId) {
                mixingApp.gains[track].gain.value = volume/50;
            }
        })
    },
    changePan: function(evt) {
        var pan =     ($(evt.target) != undefined ) ? $(evt.target).val()      : evt.val();
        var trackId = ($(evt.target) != undefined ) ? $(evt.target).data('id') : evt.data('id');

        $(mixingApp.sources).each(function(track) {
            if(mixingApp.panners[track].id == trackId) {
                mixingApp.panners[track].pan.value = pan/100
            }
        })
    },
    volumePlus: function(evt) {

    },
};

$(mixingApp.init);