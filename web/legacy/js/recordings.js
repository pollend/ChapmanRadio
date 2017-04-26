var rec = new Object();

rec.data = new Array();

rec.speed = 800;
rec.top = -600;
rec.bottom = 600;
rec.cur = "";

$(document).ready(function(){
	$('.recording').css({opacity:.4}).click(function(){ rec.show(this.id,true); });
	rec.show(rec.firstmp3id);
	$('.recording:last').css({marginBottom:0});
});

rec.show = function(mp3id, autoScrollLi) {
	if(typeof mp3id == 'string') { mp3id = mp3id.substr(9); }
	if(rec.cur == mp3id) return;
	if(autoScrollLi) {
		var top = $('#li'+mp3id)[0].offsetTop;
		top -= 120;
		if(top < 0) top = 0;
		if(mp3id == rec.lastmp3id) top = 'max';
		//if(mp3id != rec.firstmp3id) top += 22;
		$('.rec_list ul').scrollTo(top, rec.speed);
	}
	if(rec.cur) {
		$('#recording'+rec.cur).css({opacity:.4});
		$('#li'+rec.cur+' .arrow').stop().animate({width:0});
	}
	$('#recording'+mp3id).css({opacity:1});
	$('#li'+mp3id+' .arrow').stop().animate({width:28});
	var obj = $('#recording'+mp3id);
	if(!obj) return;
	var top = obj[0].offsetTop;
	if(mp3id != rec.firstmp3id) top -= 33; 
	$('.recordings').scrollTo(top, rec.speed);
	rec.cur = mp3id;
};

rec.setup = new Array();
rec.playerWidth = 200;
rec.faded = .6;

rec.play = function(mp3id, url) {
	if(!rec.setup[mp3id]) {
		$('#recording'+mp3id+' .rec_player').animate({width:250});
		$('#recording'+mp3id+' .rec_player .bar').append("<div class='loaded'></div><div class='playhead'></div>").delay(100).animate({width:rec.playerWidth},200);
		$('#recording'+mp3id+' .rec_player .disp').html("Loading...").animate({top:3});
		// pause all other sounds
		for(x in rec.setup) {
			rec.setup[x].sound.pause();
			rec.setup[x].isPlaying = false;
			$('.rec_player .play').css({backgroundImage:'url(/img/icons/play16.png)'});
		}
		rec.setup[mp3id] = new Object();
		rec.setup[mp3id].isLoaded = false;
		rec.setup[mp3id].isPlaying = true;
		// setup this sound object
		rec.setup[mp3id].sound = soundManager.createSound({
			 id:'sound'+mp3id,
			 url:url,
			 autoPlay:true,
			 autoLoad:true,
			 whileplaying:function(){
				$('#recording'+mp3id+' .playhead').css({width:Math.floor(rec.playerWidth*this.position/this.durationEstimate)});
				if(rec.setup[mp3id].isLoaded) {
					$('#recording'+mp3id+' .rec_player .disp').html(rec.time(this.position));
					}
				else {
					$('#recording'+mp3id+' .rec_player .disp').html(rec.time(this.position));
					}
				},
			whileloading:function(){
				$('#recording'+mp3id+' .loaded').css({width:Math.floor(rec.playerWidth*this.bytesLoaded/this.bytesTotal)});
				}
			});
		$("#recording"+mp3id+" .play").css({opacity:rec.faded,backgroundImage:'url(/img/icons/pause16.png)'}).mouseover(function(){$(this).css({opacity:1})}).mouseout(function(){$(this).css({opacity:rec.faded})}).click(function() {
			if(rec.setup[mp3id].isPlaying) {
				this.style.backgroundImage = 'url(/img/icons/play16.png)';
				rec.setup[mp3id].sound.pause();
				rec.setup[mp3id].isPlaying = false;
			}
			else {
			    this.style.backgroundImage = 'url(/img/icons/pause16.png)';
				rec.setup[mp3id].sound.resume();
				rec.setup[mp3id].isPlaying = true;
			}
		});
		$("#recording"+mp3id+" .loaded, #recording"+mp3id+" .playhead").click(function(e) {
			var obj = this;
			var left = 0;
			do {
				left += obj.offsetLeft;
			} while(obj = obj.offsetParent);
			var x = e.pageX - left;
			var newPos = Math.round(rec.setup[mp3id].sound.durationEstimate*x/rec.playerWidth);
			rec.setup[mp3id].sound.setPosition( newPos );
			if(!rec.setup[mp3id].sound.isPlaying) {
				$('#recording'+mp3id+" .play").css({backgroundImage:'url(/img/icons/pause16.png)'});
				rec.setup[mp3id].sound.resume();
				rec.setup[mp3id].isPlaying = true;
			}
		});
	} else {
	}
};

rec.time = function(ms){
	var min = Math.floor(ms / 60000);
	var sec = Math.floor((ms / 1000)%60);;
	if(sec < 10) return min+":0"+sec;
	else return min+":"+sec;
};

