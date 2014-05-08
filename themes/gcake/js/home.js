(function(l){var j=AC.Storage.getItem;var i=AC.Storage.setItem;var n=AC.Environment.Browser.name;
var h=AC.Environment.Browser.version;var k=n.toLowerCase()==="ie"&&h<9;var m={colors:["white"],colorIndexKey:"home-gallery-colors-20130827",skinAttribute:"data-skin",skinsByColor:["black"],contentSelector:"#hero .gallery-content",minSlideWidth:1115,heroGalleryOptions:{continuous:!k,useTouchEvents:true,duration:1,useKeyboardNav:true,alwaysUseKeyboardNav:true,heightFromFirstSection:false,silentTriggers:true},heroSlideShowOptions:{autoplay:false,delay:7000,stopOnUserInteraction:true},init:function(){this.wrapper=AC.Element.select("#billboard");
this.galleryView=AC.Element.select("#hero");this.navContainer=AC.Element.select(".nav");
this.colorSlide = AC.Element.select(".lunbo3"); this.galleryContent = AC.Element.selectAll(this.contentSelector);
this.updateVariableSlideStyles(this.galleryContent.length);this.heroGallery=this.createHeroGallery(this.galleryContent,this.heroGalleryOptions);
this.heroSlideShow=this.createHeroSlideShow(this.heroSlideShowOptions);this.dotNavUsed=false;
this.minDimensions=this.calcMinDimensions();this.addEvents();this.initColors();
this.initTracking();this.setupFluidGallery();this.heroSlideShow.pause();this.setupInlineVideoDeepLink();
this.setupFlexBox()},updateVariableSlideStyles:function(a){var b=(100/a)+"%";var c=a*this.minSlideWidth+"px";
[].forEach.call(this.galleryContent,function(d){AC.Element.setStyle(d,{width:b})
});AC.Element.setStyle(this.galleryView,{minWidth:c})},setupFlexBox:function(){var a=AC.Environment.Feature.cssPropertyAvailable;
var b=a("flex")&&!("ontouchstart" in window);if(b){AC.Element.addClassName(this.wrapper,"flexible")
}},initColors:function(){var a=AC.Storage.getItem(this.colorIndexKey)||0;var b=a%this.colors.length;
this.setColor(this.colors[b]);AC.Storage.setItem(this.colorIndexKey,b+1)},initTracking:function(){if(typeof AC.ViewMaster.Tracker==="function"){window.tracker=new AC.ViewMaster.Tracker("click")
}},calcMinDimensions:function(){var b=AC.Element.selectAll(".gallery-content-wrapper");
var a=AC.Element.getStyle(b[0],"minHeight");a=(a)?parseInt(a,10):0;return{width:this.minSlideWidth,height:a}
},addEvents:function(){AC.Element.addEventListener(document,"click",this.handleClick.bind(this),false);
AC.Element.addEventListener(window,"load",function(){this.heroSlideShow.start()
}.bind(this))},swapIfNecessary:function(){var d=AC.Element.selectAll(this.contentSelector);
var b=AC.Element.select(".dots",this.navContainer);var a=AC.Element.selectAll(".dot",b);
var e=this.galleryView;var c=this.visitsIndex%2;if(c){this.galleryView.insertBefore(d[1],d[0]);
b.insertBefore(a[1],a[0])}},setColor:function(a){[].forEach.call(this.colors,function(b){AC.Element.removeClassName(this.colorSlide,b)
}.bind(this));AC.Element.addClassName(this.colorSlide,a)},updateSkin:function(d,e){var a=this.wrapper;
var b;if(d===this.colorSlide){var c=AC.Storage.getItem(this.colorIndexKey);var f=(e)?c%this.colors.length:(c-1)%this.colors.length;
b=this.skinsByColor[f]}else{b=d.getAttribute(this.skinAttribute)}this._navSkins.forEach(function(g){AC.Element.removeClassName(a,g)
});AC.Element.addClassName(a,b)},stashSkins:function(){var a=AC.Element.selectAll(this.contentSelector);
this._navSkins=a.map(function(b){return b.getAttribute(this.skinAttribute)}.bind(this))
},lastToFirst:function(a,e,f){var c=a.length;var b=a[0];var d=a[c-1];return e.id===d.key&&f.id===b.key
},swapColors:function(){var b;var a=AC.Storage.getItem(this.colorIndexKey);b=a%this.colors.length;
this.setColor(this.colors[b]);AC.Storage.setItem(this.colorIndexKey,a+1)},createHeroGallery:function(b,c){var a=this.galleryView.id;
var d=new AC.ViewMaster.SlideViewer(b,a,a,c);AC.Element.addClassName(this.galleryView,"slides"+d.orderedSections.length);
this.stashSkins();d.setDelegate({willShow:function(e,g,f){this.updateSkin(f.content)
}.bind(this),didShow:function(e,g,f){if(g&&g.content===this.colorSlide){this.swapColors()
}}.bind(this)});this.updateSkin(AC.Element.selectAll(this.contentSelector)[0],true);
return d},createHeroSlideShow:function(a){return new AC.ViewMaster.Slideshow(this.heroGallery,null,a)
},createWrapperGallery:function(){var e=AC.Element.selectAll(".gallery-content-wrapper");
var a=new AC.ViewMaster.Viewer(e,"gallery-wrapper","gallery-wrapper-triggers",{silentTriggers:true,escapeToClose:true,shouldAnimateContentChange:!(AC.Environment.Browser.name==="IE")});
var c=this.minDimensions;var b=AC.Element.select("#homefooter").clientHeight;var d=AC.Element.selectAll(".gallery-content .scrim .blur");
a.setDelegate({willShow:function(u,s,v){var f=document.viewport.getDimensions();
var t=AC.Element.select(".gallery-wrapper");var g=(f.height>c.height)?f.height:c.height;
g=g-b;AC.Element.setStyle(t,{height:g+"px"});if(s&&s.id==="gallery-content-wrapper-billboard"){AC.Element.addClassName(s.content,".resize-mask")
}},didShow:function(v,t,f){if(t&&f.id==="gallery-content-wrapper-billboard"){var u=AC.Element.select(".gallery-wrapper");
AC.Element.setStyle(u,{height:""});AC.Element.removeClassName(t.content,".resize-mask");
for(var s=fluidFigures.length-1;s>=0;s--){if(AC.Element.hasClassName(fluidFigures[s].figure,"active")){var g=fluidFigures[s].getBoundingDimensions(document.viewport.getDimensions().height);
fluidFigures[s].resize(g);break}}}}});return a},setupFluidGallery:function(){var a=[];
var b=new FluidGallery(this.heroGallery,a,1.17,{},this.heroSlideShow,this.minDimensions);
return b},handleClick:function(f){var e=AC.Element.select(".nav");var g=AC.Event.target(f);
var a=AC.Element.select('a[href*="#see-all"]',e);var c=AC.Element.select('a[href*="#close"]',e);
var b=AC.Element.hasClassName(g.parentNode,"dot");if(AC.Element.hasClassName(g,"gallery-video")){f.stop();
var d=g.getAttribute("data-uri");wrapperViewer.show(wrapperViewer.sectionWithId(d))
}if(b){this.dotNavUsed=true}},setupInlineVideoDeepLink:function(){var a=AC.Element.select("a.gallery-video");
var b=document.location.hash.replace("#","");if(a&&b===a.getAttribute("data-uri")){wrapperViewer.show(wrapperViewer.sectionWithId(b))
}}};window.homepageGallery=m;m.init()})();