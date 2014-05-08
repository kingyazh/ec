(function(){var b=AC.Class({initialize:function(a,i,h,l,j,k){this.__defaultOptions={resizeDelay:0.75,debug:false};
this.gallery=a;this.slideshow=j;this.id=a.triggerClassName;this.galleryView=a.view.view();
this.fluidFigures=i;this.previouslyPlaying=null;this.resizing=false;this.minDimensions=k;
this.currentSection=null;this.minSlideSize=1115;this.numSlides=a.orderedSections.length;
this.aspectRatio=h;this.footerHeight=0;if(typeof l!=="object"){l={}}this.options=Object.extend(Object.clone(this.__defaultOptions),l);
this.__setupHandlers();this.responsiveResize(true);this.adaptiveResize()},__setupHandlers:function(){Event.observe(window,"resize",this.__didResizeWindow.bindAsEventListener(this))
},__didResizeWindow:function(a){if(this.slideshow){if(this.previouslyPlaying===null){this.previouslyPlaying=this.slideshow._playing;
this.slideshow.pause()}}this.responsiveResize(false);this.__startResizeTimer()},__startResizeTimer:function(){this.__cancelResizeTimer();
if(typeof this.__boundAdaptiveResize==="undefined"){this.__boundAdaptiveResize=this.adaptiveResize.bind(this)
}this.__resizeAssetsFlag=setTimeout(this.__boundAdaptiveResize,(this.options.resizeDelay*1000))
},__cancelResizeTimer:function(){if(typeof this.__resizeAssetsFlag!=="undefined"){clearTimeout(this.__resizeAssetsFlag);
delete this.__resizeAssetsFlag}},getBoundingDimensions:function(a,e){var f=Math.max(Math.max(e,this.minDimensions.height)-this.footerHeight,0);
return{width:f*this.aspectRatio,height:f}},getCurrentFigureID:function(){return this.gallery.currentSection.id
},__getFigureIndex:function(d){for(var a=this.fluidFigures.length-1;a>=0;a--){if(this.fluidFigures[a].figure.id===d){return a
}}return 0},__getSectionIndex:function(d){for(var a=this.gallery.orderedSections.length-1;
a>=0;a--){if(this.gallery.orderedSections[a]===d){return a}}return 0},responsiveResize:function(a){if(this.currentSection===null){this.currentSection=this.gallery.currentSection.content;
AC.Element.addClassName(this.galleryView,"resize-mask");AC.Element.addClassName(this.currentSection,"resize-active");
this.galleryView.style.backgroundColor=AC.Element.getStyle(this.currentSection,"background-color")
}this.viewportSize=document.viewport.getDimensions();setTimeout(function(){if("ontouchstart" in window){this.viewportSize.height=window.innerHeight
}this.boundedDimensions=this.getBoundingDimensions(this.viewportSize.width,this.viewportSize.height);
this.responsiveResizeFluidFigures();this.responsiveResizeGallery(a)}.bind(this),1)
},responsiveResizeFluidFigures:function(){for(var a=this.fluidFigures.length-1;
a>=0;a--){AC.Element.setStyle(this.fluidFigures[a].figure,{width:this.viewportSize.width+"px",height:this.boundedDimensions.height+"px"});
this.fluidFigures[a].resize(this.boundedDimensions)}},responsiveResizeGallery:function(s){var n={};
var p=AC.Detector.isCSSAvailable("transition");var a=AC.Detector.isCSSAvailable("transform");
var m=this.viewportSize.width*this.numSlides;var r=this.boundedDimensions.height;
var o=this.__getSectionIndex(this.getCurrentFigureID());var l=Math.max(this.viewportSize.width,this.minDimensions.width);
var t=-1*o*l;var q=0;if(this.gallery.options.useTouchEvents===true&&typeof window.ontouchstart!=="undefined"){this.gallery.__maskWidth=this.gallery.__mask.getWidth()
}if(this.viewportSize.width<this.minSlideSize&&AC.Environment.Browser.name!=="IE"){q=-((this.minSlideSize-this.viewportSize.width)/2)
}n.width=m+"px";n.height=r+"px";n.marginLeft=q+"px";if(p&&a){if(!s){this.galleryView.setVendorPrefixStyle("transition","none")
}this.galleryView.setVendorPrefixStyle("transform","translate3d("+t+"px, 0, 0)")
}else{n.left=t+"px"}AC.Element.setStyle(this.galleryView,n);if(s){AC.Element.addClassName(this.galleryView,"initial-resize")
}},adaptiveResize:function(){AC.Element.removeClassName(this.galleryView,"resize-mask");
AC.Element.removeClassName(this.currentSection,"resize-active");this.galleryView.style.backgroundColor="";
this.currentSection=null;if(this.slideshow){if(this.previouslyPlaying){this.slideshow.play()
}this.previouslyPlaying=null}},adaptiveResizeFluidFigures:function(){}});window.FluidGallery=b
})();