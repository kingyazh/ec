Event.Publisher=Class.create();Object.extend(Event.Publisher,{_ls_event_targets:null,_event_source_id:null,_fl_trace_events:false,getEventSourceId:function(){if(typeof this._event_source_id=="function"){return this._event_source_id()
}else{return this._event_source_id}},getEventTarget:function(b){if(!this._ls_event_targets){this._ls_event_targets=new Array()
}if(!this._ls_event_targets[b]){document.body.appendChild(this._ls_event_targets[b]=document.createElement("A"))
}return this._ls_event_targets[b]},addEventListener:function(h,j,g){var f=this.getEventTarget(h);
Event.observe(f,"click",j,g);if(this._fl_trace_events){var i={publisher:this.getEventSourceId(),event_name:h,listener:j,capturing:g,event_source_proxy:f};
this.dispatchEvent("eventListenerAdded",i,true,true)}},removeEventListener:function(h,j,g){var f=this.getEventTarget(h);
Event.stopObserving(f,"click",j,g);if(this._fl_trace_events){var i={publisher:this.getEventSourceId(),event_name:h,listener:j,capturing:g,event_source_proxy:f};
this.dispatchEvent("eventListenerRemoved",i,true,true)}},dispatchEvent:function(j,k,n,h){var m=this.getEventTarget(j);
var i={event_name:j,event_target:this,data:k?k:null};if(!n){n=false}if(!h){h=false
}var l=Event.create(i,n,h,true,m);if(this._fl_trace_events){if(j.match(/event(?:ListenerAdded|ListenerRemoved|Dispatched|Received)/)){return
}var k={publisher:this.getEventSourceId(),event_name:j,event_data:i,can_bubble:n,cancelable:h,event_source_proxy:m,result:l};
this.dispatchEvent("eventDispatched",k,true,true)}},toggleEventsTrace:function(){var b=Event.Tracer.findTracer();
if(!b||!this._fl_trace_events){this._fl_trace_events=true;b=Event.Tracer.startTrace();
b.registerPublisher(this)}else{this._fl_trace_events=false;if(b){b.unregisterPublisher(this)
}}return this._fl_trace_events},isEventsTraceActive:function(){return this._fl_trace_events
}});Event.Listener=Class.create();Object.extend(Event.Listener,{_listens:null,getEventHandlerName:function(c){var d=c.split(/[ _]/).join("-").camelize();
return"on"+d.charAt(0).toUpperCase()+d.substr(1)},listenForEvent:function(l,i,j,g){if(!g){g=this.getEventHandlerName(i)
}if(!this._listens){this._listens=new Array()}var k=this[g];if(typeof(g)=="function"){k=g
}var h=function(a){if(a.event_data){k.bindAsEventListener(this)(a)}}.bindAsEventListener(this);
this._listens.push([l,i,j,g,h]);l.addEventListener(i,h,j)},stopListeningForEvent:function(l,i,k,g){if(!this._listens){return false
}if(!g){g=this.getEventHandlerName(i)}var j=-1;var h=this._listens.detect(function(a,b){if((a[0]==l)&&(a[1]==i)&&(a[2]==k)&&(a[3]==g)){j=b;
return true}});if(j>=0){this._listens.splice(j,1);l.removeEventListener(i,h[4],k);
return true}return false}});Object.extend(Event,{create:function(k,l,g,h,i){var j;
if(document.createEvent){if(!l){l=false}if(!g){g=false}if(/Konqueror|Safari|KHTML/.test(navigator.userAgent)){j=document.createEvent("HTMLEvents");
j.initEvent("click",l,g)}else{j=document.createEvent("MouseEvents");j.initMouseEvent("click",l,g,window,0,0,0,0,0,false,false,false,false,0,null)
}}else{j=document.createEventObject();j.event_type="onclick"}j.event_data=k;if(h){Event.dispatch(i,j)
}return j},dispatch:function(c,d){if(document.createEvent){return c.dispatchEvent(d)
}else{return c.fireEvent((typeof(d.event_type)!="undefined")?d.event_type:"onclick",d)
}}});