/**
 *   schéma de câblage
 *
 *   | source |----->| panner |----->| gain |----->| destination |
 *
 **/
const $ = require('jquery');
import '../css/mixing.scss';
const WIDTH=1000;
const HEIGHT=100;

var mixingApp = {
    audioContext: new (window.AudioContext || window.webkitAudioContext)(),
    audioContextStart:undefined,
    sources: [],
    panners: [],
    gains: [],
    startTimes: [],
    analysers: [],
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
        $('.startTimeInput').on('change', mixingApp.updateStartTime);
        $('.colorInput').on('change', mixingApp.changeColor);
        mixingApp.initStartTime();
    },
    initStartTime: function() {
        var startTimes = $('.startTimeInput');
        startTimes.each(function() {
            mixingApp.startTimes['st'+$(this).data('id')] = parseFloat($(this).val());
        });
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
                source.startTime = mixingApp.startTimes['st'+id];
                source.onended = mixingApp.onEnd;
                source.connect(gainNode);
                gainNode.connect(stereoPanner);
                stereoPanner.connect(mixingApp.audioContext.destination);
                source.id = id;
                stereoPanner.id = id;
                gainNode.id = id;
                mixingApp.sources['s'+id] = source;
                mixingApp.panners['p'+id] = stereoPanner;
                mixingApp.gains['g'+id] = gainNode;
                mixingApp.changeVolume($('.volumeBar[data-id='+id+']').first());
                mixingApp.changePan($('.panBar[data-id='+id+']').first());
            });
    },
    play: function() {
        $('.startTimeInput').attr('disabled','disabled');
        $('.startTimeInput').addClass('disabled');
        $('#play').hide();
        $('#pause').show().focus();
        $('#stop').removeClass('disabled');
        mixingApp.sources = [];
        mixingApp.gains = [];
        mixingApp.panners = [];
        var trackList = mixingApp.getTrackListData();
        trackList.each(function () {
            mixingApp.loadTrack($(this))
        });
        var waitLoading = setInterval(function(){
            if(Object.keys(mixingApp.sources).length === trackList.length) {
                // Tracks are loaded here
                mixingApp.audioContextStart = mixingApp.audioContext.currentTime;
                mixingApp.updateCurrentTime();
                var totalTime = [];
                clearInterval(waitLoading);
                for (var track in mixingApp.sources) {
                    var startTime = mixingApp.sources[track].startTime;
                    totalTime.push(mixingApp.sources[track].buffer.duration + startTime);
                    mixingApp.sources[track].start(startTime + mixingApp.audioContextStart);
                }

                mixingApp.totalTime = Math.max(...totalTime);

                for (var track in mixingApp.sources) {
                    var id = track.replace('s', '');
                    var visualTrack = $('#visual-track-' + id);
                    visualTrack.css({
                        'width': ((mixingApp.sources[track].buffer.duration / mixingApp.totalTime) * 100) + '%',
                        'left': ((mixingApp.sources[track].startTime / mixingApp.totalTime * 100) + '%')
                    })
                }
                $('#totalTime').html(mixingApp.secondsToDhms(mixingApp.totalTime));
            }
        }, 1500);
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
        for (var track in mixingApp.sources) {
            if(mixingApp.audioContext.state === 'suspended') {
                mixingApp.audioContext.resume()
            }
            mixingApp.sources[track].stop();
        };
        mixingApp.stopTimer();
        $('#pause').children('path').attr('d', mixingApp.originalSVG.play);
        $('#currentTime').html('00:00:00');
        $('.startTimeInput').attr('disabled',false);
        $('.startTimeInput').removeClass('disabled');;
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
            var percent = mixingApp.currentTime / mixingApp.totalTime
            $('.visual-track-cursor').css({
                'left': percent*100+'%'
            })
            var seconds = Math.round(mixingApp.currentTime)

            var currentTimeFormated = mixingApp.secondsToDhms(seconds)
            $('#currentTime').html(currentTimeFormated)
        }, 100);
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
        var volume =     ($(evt.target).val() != undefined ) ? $(evt.target).val() : evt.val();
        var trackId = ($(evt.target).data('id') != undefined ) ? $(evt.target).data('id') : evt.data('id');
        if(mixingApp.gains['g' + trackId] !== undefined) {
            mixingApp.gains['g' + trackId].gain.value = volume / 50;
        }
    },
    changePan: function(evt) {
        var pan =     ($(evt.target).val() != undefined ) ? $(evt.target).val() : evt.val();
        var trackId = ($(evt.target).data('id') != undefined ) ? $(evt.target).data('id') : evt.data('id');

        if(mixingApp.panners['p'+trackId] !== undefined) {
            mixingApp.panners['p'+trackId].pan.value = pan/100;
        }
    },
    volumePlus: function(evt) {

    },
    updateStartTime: function (evt) {
       var id = $(evt.target).data('id');
       if(parseFloat($(evt.target).val()) < 0)
           $(evt.target).val(0);
       mixingApp.startTimes['st'+id] = parseFloat($(evt.target).val());
    },
    changeColor: function(evt) {
        var id = $(evt.target).data('id');
        var visualTrack = $('#visual-track-'+id);
        visualTrack.css({
            'backgroundColor': $(evt.target).val()
        })
    },
    // loadAnalyser: function() {
    //     $(mixingApp.sources).each(function(id) {
    //         var analyser = mixingApp.audioContext.createAnalyser();
    //         analyser.id=mixingApp.sources[id].id;
    //         mixingApp.sources[id].connect(analyser)
    //         analyser.fftSize = 2048;
    //         analyser.tailleMemoireTampon = analyser.frequencyBinCount;
    //         analyser.tableauDonnees = new Uint8Array(analyser.tailleMemoireTampon);
    //         analyser.getByteTimeDomainData(analyser.tableauDonnees);
    //         mixingApp.analysers.push(analyser)
    //     })
    //     mixingApp.dessiner();
    // },
    // dessiner: function() {
    //     $(mixingApp.sources).each(function(id) {
    //         // dessine un oscilloscope de la source audio
    //         var trackId = mixingApp.sources[id].id;
    //         // var canvas = $("#oscilloscope-"+trackId).first();
    //         var canvas = document.getElementById("oscilloscope-"+trackId);
    //         var contexteCanvas = canvas.getContext("2d");
    //         var analyser = null;
    //         $(mixingApp.analysers).each(function(key){
    //             if(mixingApp.analysers[key].id == trackId) {
    //                 analyser = mixingApp.analysers[key];
    //             }
    //         })
    //         requestAnimationFrame(mixingApp.dessiner);
    //
    //         analyser.getByteTimeDomainData(analyser.tableauDonnees);
    //
    //         contexteCanvas.fillStyle = 'rgb(200, 200, 200)';
    //         contexteCanvas.fillRect(0, 0, WIDTH, HEIGHT);
    //
    //         contexteCanvas.lineWidth = 2;
    //         contexteCanvas.strokeStyle = 'rgb(0, 0, 0)';
    //
    //         contexteCanvas.beginPath();
    //
    //         var sliceWidth = WIDTH * 1.0 / analyser.tailleMemoireTampon;
    //         var x = 0;
    //
    //         for (var i = 0; i < analyser.tailleMemoireTampon; i++) {
    //
    //             var v = analyser.tableauDonnees[i] / 128.0;
    //             var y = v * HEIGHT / 2;
    //
    //             if (i === 0) {
    //                 contexteCanvas.moveTo(x, y);
    //             } else {
    //                 contexteCanvas.lineTo(x, y);
    //             }
    //
    //             x += sliceWidth;
    //         }
    //
    //         contexteCanvas.lineTo(canvas.width, canvas.height / 2);
    //         contexteCanvas.stroke();
    //     })
    // },
};

$(mixingApp.init);