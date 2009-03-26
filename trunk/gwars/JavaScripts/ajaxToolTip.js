/*

AjaxTooltip by aNTRaX

Based in Sweet Titles by Dustin Diaz | http://www.dustindiaz.com
Under license (c) Creative Commons 2005
http://creativecommons.org/licenses/by-sa/2.5/
*/


var ajaxTooltip={
	//Properties
	tip:null,// @Element: The actual toolTip itself
	tipElements: ['a','abbr','acronym','img','div','span','td'],// @Array: Allowable elements that can have the toolTip
	xCord:0,// @Number: x pixel value of current cursor position
	yCord:0,// @Number: y pixel value of current cursor position
	showTime:1000,// @Number: time needed to show the tooltip
	loaderEvent:null,//Function invoked when the tip is loading data
	element:null,// @Element: That of which you're hovering over

	//Functions
	initialize:function(){
		ajaxTooltip.tip = document.createElement('div');
		ajaxTooltip.tip.id = 'toolTip';
		ajaxTooltip.tip.style.position='absolute';
		ajaxTooltip.tip.style.zIndex='9999';
		ajaxTooltip.tip.style.padding='5px';
		ajaxTooltip.tip.style.opacity = '0';
		ajaxTooltip.tip.style.minHeight='1em';
		ajaxTooltip.tip.onmouseout=ajaxTooltip.tipOut;
		ajaxTooltip.tip.onmouseover=function(){
			if(ajaxTooltip.hideTimerID)
			clearTimeout(ajaxTooltip.hideTimerID);
		};

		document.getElementsByTagName('body')[0].appendChild(ajaxTooltip.tip);
		ajaxTooltip.scanElements();
	},
	scanElements:function(){//Re-scan the document to look for new tips
		for ( i=0; i<ajaxTooltip.tipElements.length; i++ ) {
			var current = document.getElementsByTagName(ajaxTooltip.tipElements[i]);
			var curLen = current.length;
			for ( j=0; j<curLen; j++ ) {
				var element=current[j];
				if(element.getAttribute('tip')!=null)
				{
					element.onmouseover=function(){//Mouse enter in the element
						ajaxTooltip.showTimerID = window.setTimeout(ajaxTooltip.tipShow,ajaxTooltip.showTime);
						ajaxTooltip.element=this;

						this.onmousemove=function(event){
							if ( document.captureEvents ) {
								ajaxTooltip.xCord = event.pageX;
								ajaxTooltip.yCord = event.pageY;
							} else if ( window.event.clientX ) {
								ajaxTooltip.xCord = window.event.clientX+document.documentElement.scrollLeft;
								ajaxTooltip.yCord = window.event.clientY+document.documentElement.scrollTop;
							}
						}
					};
					element.onmouseout=ajaxTooltip.tipOut;
				}
			}
		}
	},
	tipShow : function() {
		var tipValue=ajaxTooltip.element.getAttribute('tip');
		var currentElement=ajaxTooltip.element;
		if(ajaxTooltip.element.getAttribute('htmlTip')==null)
		{
			eval(ajaxTooltip.loaderEvent);
			ajaxTooltip.ajax=$.ajax({
				type: "GET",
				url: tipValue.split("?")[0],
				data: tipValue.split("?")[1],
				success: function(data){
					currentElement.setAttribute('htmlTip','true');
					currentElement.setAttribute('tip',data);
					if(currentElement==ajaxTooltip.element)//Check that element has not changed
					{
						$(ajaxTooltip.tip).html(data);
						ajaxTooltip.setTipPosition();
					}
				},
				error:function(){
					$(ajaxTooltip.tip).html("Server error");

				}
			});
		}
		else
		$(ajaxTooltip.tip).html(tipValue);

		ajaxTooltip.element.removeAttribute('onmousemove');
		ajaxTooltip.tip.style.opacity = '0';
		ajaxTooltip.tip.style.display='block';
		ajaxTooltip.setTipPosition();
		$('#toolTip').fadeTo(250, 0.95);
	},
	tipOut:function(){
		if (ajaxTooltip.showTimerID!=null)
		clearTimeout(ajaxTooltip.showTimerID);
		ajaxTooltip.showTimerID=null;

		ajaxTooltip.hideTimerID=window.setTimeout(function(){
			if(ajaxTooltip.showTimerID==null)
			{
				if(ajaxTooltip.tip.style.opacity!='0')
				{
					$('#toolTip').fadeTo(250, 0,function(){
						ajaxTooltip.tip.style.display='none';
					});
				}

				if(ajaxTooltip.ajax && ajaxTooltip.ajax.status!=200)
				ajaxTooltip.ajax.Abort();//Cancelar otras peticiones existentes
			}},ajaxTooltip.showTime);
	},
	setTipPosition:function() {
		x=Number(ajaxTooltip.xCord);
		y=Number(ajaxTooltip.yCord);

		if(x+ajaxTooltip.tip.clientWidth>document.documentElement.clientWidth && ajaxTooltip.tip.style.left!=(x-ajaxTooltip.tip.clientWidth-8)+'px')
		ajaxTooltip.tip.style.left = (x-ajaxTooltip.tip.clientWidth-8)+'px';
		else if(ajaxTooltip.tip.style.left!=(x+8)+'px')
		ajaxTooltip.tip.style.left = (x+8)+'px';

		if(y+ajaxTooltip.tip.clientHeight>document.documentElement.clientHeight && ajaxTooltip.tip.style.top !=(y-ajaxTooltip.tip.clientHeight-8)+'px')
		ajaxTooltip.tip.style.top = (y-ajaxTooltip.tip.clientHeight-8)+'px';
		else if(ajaxTooltip.tip.style.top!=(y+12)+'px')
		ajaxTooltip.tip.style.top = (y+12)+'px';
	}
};

/*

ajaxWindow by aNTRaX

This class use the Inteface plugin for jQuery
*/

var ajaxWindow={
	transferClass:'windowTransfer',
	tipElements: ['a','abbr','acronym','img','div','span','td'],// @Array: Allowable elements that can have a window
	imageLocation:'windows/',
	loaderEvent:null,

	scanElements:function(){//Re-scan the document to look for new windows
		for ( i=0; i<ajaxWindow.tipElements.length; i++ ) {
			var current = document.getElementsByTagName(ajaxWindow.tipElements[i]);
			var curLen = current.length;
			for ( j=0; j<curLen; j++ ) {
				var element=current[j];
				if(element.getAttribute('window')!=null)
				{
					$(element).bind(
					'click',
					function() {//Open the window

						//Create the window div
						var windowDiv=$('<div class="window"></div>').appendTo("body");
						var windowTop=$('<div class="windowTop"></div>').appendTo(windowDiv);
						var windowBottom=$('<div class="windowBottom"></div>').appendTo(windowDiv);
						var windowBottomContent=$('<div class="windowBottomContent">&nbsp;</div>').appendTo(windowBottom);
						var windowContent=$('<div class="windowContent"></div>').appendTo(windowDiv);
						var windowResize=$('<img src="'+ajaxWindow.imageLocation+'window_resize.gif"  class="windowResize"/>').appendTo(windowDiv);

						var windowTopContent=$('<div class="windowTopContent">'+this.getAttribute('windowTitle')+'</div>').appendTo(windowTop);
						var windowMin=$('<img src="'+ajaxWindow.imageLocation+'window_min.jpg" class="windowMin" />').appendTo(windowTop);
						var windowMax=$('<img src="'+ajaxWindow.imageLocation+'window_max.jpg" class="windowMax" />').appendTo(windowTop);
						var windowClose=$('<img src="'+ajaxWindow.imageLocation+'window_close.jpg" class="windowClose"/>').appendTo(windowTop);

						//Set events
						if(windowDiv.css('display') == 'none') {
							$(this).TransferTo(
							{
								to:windowDiv.get(0),
								className:ajaxWindow.transferClass,
								duration: 400,
								complete: function()
								{
									windowDiv.show();

									eval(ajaxWindow.loaderEvent);
									ajaxTooltip.aja=$.ajax({
										type: "GET",
										url: this.getAttribute('window').split("?")[0],
										data: this.getAttribute('window').split("?")[1],
										success: function(data){
											windowContent.html(data);
										},
										error:function(object,description){
												windowContent.html("Error: "+description);
										}
									});
								}
							}
							);
						}
						
						$(windowClose).bind(
						'click',
						function()
						{
							$(windowDiv).DropOutUp(500,function(){
								windowDiv.remove();
							}
							);
						}
						);

						windowMin.bind(
						'click',
						function()
						{
							windowContent.SlideToggleUp(300);
							windowBottom.animate({height: 10}, 300);
							windowBottomContent.animate({height: 10}, 300);
							windowDiv.animate({height:40},300).get(0).isMinimized = true;
							$(this).hide();
							windowResize.hide();
							windowMax.show();
						}
						);

						windowMax.bind(
						'click',
						function()
						{
							var windowSize = $.iUtil.getSize(windowContent.get(0));
							windowContent.SlideToggleUp(300);
							windowBottom.animate({height: windowSize.hb + 13}, 300);
							windowBottomContent.animate({height: windowSize.hb + 13}, 300);
							windowDiv.animate({height:windowSize.hb+43}, 300).get(0).isMinimized = false;
							$(this).hide();
							windowMin.show();
							windowResize.show();
						}
						);

						windowDiv.Resizable(
						{
							minWidth: 200,
							minHeight: 60,
							maxWidth: 700,
							maxHeight: 400,
							dragHandle: getElementXPath(windowTop.get(0)),
							handlers: {
								se: windowResize.get(0)
							},
							onResize : function(size, position) {
								windowBottom.css('height', size.height-33 + 'px');
								windowBottomContent.css('height', size.height-33 + 'px');
								var windowContentEl = windowContent.css('width', size.width - 25 + 'px');
								if (!windowDiv.get(0).isMinimized) {
									windowContentEl.css('height', size.height - 48 + 'px');
								}
							}
						}
						);

						this.blur();
						return false;
					});
				}
			}
		}
	}
};

function getElementXPath(elt)
{
	var path = "";
	for (; elt && elt.nodeType == 1; elt = elt.parentNode)
	{
		idx = getElementIdx(elt);
		xname = elt.tagName;
		xname += ":eq(" + (idx-1) + ")";
		path = "/" + xname + path;
	}
	return path;
}

function getElementIdx(elt)
{
	var count = 1;
	for (var sib = elt.previousSibling; sib ; sib = sib.previousSibling)
	{
		if(sib.nodeType == 1 && sib.tagName == elt.tagName)     count++
	}

	return count;
}