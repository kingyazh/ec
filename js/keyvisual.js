/*###########################################################################################################
// key visual display
// parameter: 	Array keyVisualImgList
				,string : id01  		(div)
				,string : id02			(div)
				,string : paging id 	(div)
				,number : view number 	(div)
// use
				var keyControl = new keyVisualView (keyVisualImgList,"keyvisualimage","keyvisualimage2","keyvisual_page",0);
				function leftView(){
					keyControl.leftView();
				};
				
				function rightView(){
					keyControl.rightView();
				};
				function selectView(num){
					keyControl.selectView(num);
				};
##############################################################################################################*/
/*
function keyvisualRun() {
	var obj=document.getElementById("header");
	if(obj.getAttribute("isFirstAutoPlay")==0) {
		keyControl.rightView();
		obj.setAttribute("isFirstAutoPlay",0);
	} else {
		selectView(1);
		obj.setAttribute("isFirstAutoPlay",0);
	}
	
	obj.autotid=setTimeout(keyvisualRun, 25000);
}

function keyvisualAutoPlay() {
	var obj=document.getElementById("header");
	if(obj.autotid)
		clearTimeout(obj.autotid);
	keyvisualRun();
}
function keyvisualAutoStop() {
	var obj=document.getElementById("header");
	if(obj.autotid) {
		clearTimeout(obj.autotid);
		obj.autotid = null;
	}
}
*/
function keyvisualAutoPlay() {
	if(list_keyvisualimg.length > 1){
		var obj=document.getElementById("headerShow");
		if(obj.getAttribute("isFirstAutoPlay")==0) {
			keyControl.rightView();
			obj.setAttribute("isFirstAutoPlay",0);
		} else {
			selectView(1);
			obj.setAttribute("isFirstAutoPlay",0);
		}
		obj.autotid=setTimeout(keyvisualAutoPlay,5000);
	}
}
function keyvisualAutoStop() {
	if(list_keyvisualimg.length > 1){
		var obj=document.getElementById("headerShow");
		clearTimeout(obj.autotid);
		obj.setAttribute("isFirstAutoPlay",1);
	}
}
function keyVisualView(){
	this.keyImgs;
	this.gnbColor;
	this.headerObj;
	this.keyObj1;// = document.getElementById(key01);
	this.keyObj2;// = document.getElementById(key02);
	this.viewNum;
	this.pagingObj;
	this.timer;
	this.init = function (KeyImgList,GnbList,headerId,key01,key02,pagingId, viewCnt) {
		this.keyImgs = KeyImgList;
		this.gnbColor = GnbList;
		this.headerObj = document.getElementById(headerId);
		this.keyObj1 = document.getElementById(key01);
		if(KeyImgList.length > 1){
			this.keyObj2 = document.getElementById(key02);
			this.pagingObj = document.getElementById(pagingId);
			pagingSet(this.keyImgs,this.pagingObj,viewCnt); 
			this.keyVisualViewProcess(viewCnt,null);
		}else if(KeyImgList.length === 1){
			this.keyObj1.innerHTML = KeyImgList[0];
			this.headerObj.className = GnbList[0];			
			//alert()
		}
	};
	/*###########################################################################################################
	 * image move right
	 ###########################################################################################################*/
	this.rightView = function(){
		var selectNum;
		var preNum;
		if(this.keyImgs.length-1 === this.viewNum){
			selectNum = 0;	
			preNum = this.viewNum;
		}else{
			selectNum = this.viewNum+1;	
			preNum = this.viewNum;
		}
			
		this.keyVisualViewProcess(selectNum,preNum);
	};
	/*###########################################################################################################
	 * image move left
	 ###########################################################################################################*/
	this.leftView = function(){
		var selectNum;
		var preNum;
		if(0 === this.viewNum){
			selectNum = this.keyImgs.length-1;	
			preNum = this.viewNum;
		}else{
			selectNum = this.viewNum-1;	
			preNum = this.viewNum;
		}	
		this.keyVisualViewProcess(selectNum,preNum);
	};
	this.selectView = function(selectNum){
		this.keyVisualViewProcess(selectNum-1,this.viewNum);
	};
	/*###########################################################################################################
	 *  paging set private function 
	 ###########################################################################################################*/
	pagingSet = function(keyImgs,pagingObj,curCnt) { /* num: 1~n */
		var tmp_pagetxt	= "<ul>";
		var viewNumber		= "";
		for(var i=0 ; i < keyImgs.length ;i++) {
			if(i <= 9) {
				viewNumber = "0"+(i+1);
			}else {
				viewNumber = i+1;
			}
			
			if( i === curCnt ) {
				tmp_pagetxt=tmp_pagetxt+'<li><a href="javascript:selectView('+(i+1)+');" class="on" id="paging'+ (i+1) +'">'+viewNumber+'</a></li>';
			} else {
				tmp_pagetxt=tmp_pagetxt+'<li><a href="javascript:selectView('+(i+1)+');" id="paging'+ (i+1) +'">' + viewNumber + '</a></li>';
			}
		}
		tmp_pagetxt=tmp_pagetxt+"</ul>";
		pagingObj.innerHTML = tmp_pagetxt;
	};
	/*###########################################################################################################
	 * key visual view Fuction
	 ###########################################################################################################*/
	this.keyVisualViewProcess = function(curCnt,oldCnt) {
		
		this.viewNum = curCnt;
		this.keyObj1.tid 			= "";
		this.keyObj1.tmp_opacity	= 0; 
		this.keyObj1.curr_opacity	= 100; 
		this.keyObj2.tid 			= "";
		this.keyObj2.tmp_opacity	= 100; 
		this.keyObj2.curr_opacity	= 0;

		var btype	=	navigator.userAgent;
		
		this.keyObj1.innerHTML = this.keyImgs[curCnt];		
		this.headerObj.className = this.gnbColor[curCnt];

		if( oldCnt !== null ){
			this.keyObj2.innerHTML = this.keyImgs[oldCnt];
		}
		
		if (btype.indexOf("MSIE")!=-1) { // for IE
			this.keyObj1.style.filter	= "alpha(opacity="+this.keyObj1.tmp_opacity+")";
			this.keyObj2.style.filter	= "alpha(opacity="+this.keyObj2.tmp_opacity+")";
		} else {
			this.keyObj1.style.opacity	= this.keyObj1.tmp_opacity;
			this.keyObj2.style.opacity	= this.keyObj2.tmp_opacity / 100;
		};
		
		this.keyObj1.style.display="block";
		this.keyObj2.style.display="block";
		
		pagingSet(this.keyImgs,this.pagingObj,curCnt);
		this.timer = window.setInterval(	function() {
			var btype	= navigator.userAgent;
			if(this.keyObj1.tmp_opacity != this.keyObj1.curr_opacity) {
				if (btype.indexOf("MSIE")!=-1) { // for IE
					this.keyObj1.tmp_opacity = this.keyObj1.tmp_opacity+5;
					this.keyObj1.style.filter="alpha(opacity="+this.keyObj1.tmp_opacity+")";
					this.keyObj2.tmp_opacity = this.keyObj2.tmp_opacity-5;
					this.keyObj2.style.filter="alpha(opacity="+this.keyObj2.tmp_opacity+")";
				} else {
					this.keyObj1.tmp_opacity = this.keyObj1.tmp_opacity+5;
					this.keyObj1.style.opacity=this.keyObj1.tmp_opacity/100;
					this.keyObj2.tmp_opacity = this.keyObj2.tmp_opacity-5;
					this.keyObj2.style.opacity=this.keyObj2.tmp_opacity/100;
				}
			} else {
				window.clearInterval(this.timer);
				if (btype.indexOf("MSIE") !=- 1) { // for IE
					this.keyObj1.style.filter="alpha(opacity=100)";
					this.keyObj2.style.filter="alpha(opacity=0)";
				} else {
					
					this.keyObj1.style.opacity=1;
					this.keyObj2.style.opacity=0;
				}
			}
		 }.bindArguments(this), 
		100);
	}
	this.init.apply(this, arguments);
};

/*###########################################################################################################
 * prototype Funtion 
 * Aargument bind at Funtion
 ###########################################################################################################*/
Function.prototype.bindArguments = function() {	
	var fnc = this; 
	var arg = argControl(arguments); 
	var o = arg.shift();
	return 	function() {
							return fnc.apply(o, arg);
						};
};
/*###########################################################################################################
 * Function 
 * returen argumnets array
 ###########################################################################################################*/
argControl = function(arraylike) {	
	var len = arraylike.length, a = [];	
	while (len--) {		
		a[len] = arraylike[len];		
	}
	return a;
};
