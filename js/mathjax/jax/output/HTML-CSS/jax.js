/*
 *  /MathJax/jax/output/HTML-CSS/jax.js
 *  
 *  Copyright (c) 2012 Design Science, Inc.
 *
 *  Part of the MathJax library.
 *  See http://www.mathjax.org for details.
 * 
 *  Licensed under the Apache License, Version 2.0;
 *  you may not use this file except in compliance with the License.
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 */

(function(g,b,d){
    var f,h=b.Browser.isMobile;
    var e=MathJax.Object.Subclass(
    {
        timeout:(h?15:8)*1000,
        FontInfo:{
            STIX:{
                family:"STIXSizeOneSym",
                testString:"() {} []"
            },
            TeX:{
                family:"MathJax_Size1",
                testString:"() {} []"
            }
        },
        comparisonFont:["sans-serif","monospace","script","Times","Courier","Arial","Helvetica"],
        testSize:["40px","50px","60px","30px","20px"],
        Init:function(){
            this.div=MathJax.HTML.addElement(
                document.body,"div",{
                    id:"MathJax_Font_Test",
                    style:{
                        position:"absolute",
                        visibility:"hidden",
                        top:0,
                        left:0,
                        width:"auto",
                        padding:0,
                        border:0,
                        margin:0,
                        whiteSpace:"nowrap",
                        textAlign:"left",
                        textIndent:0,
                        textTransform:"none",
                        lineHeight:"normal",
                        letterSpacing:"normal",
                        wordSpacing:"normal",
                        fontSize:this.testSize[0],
                        fontWeight:"normal",
                        fontStyle:"normal",
                        fontSizeAdjust:"none"
                    }
                },[""]
                );
            this.text=this.div.firstChild
            },
        findFont:function(n,k)
        {
            if(k&&this.testCollection(k))
            {
                return k
            }
            for(var l=0,j=n.length;l<j;l++)
            {
                if(n[l]===k)
                {
                    continue
                }
                if(this.testCollection(n[l]))
                {
                    return n[l]
                }
            }
            return null
        },
        testCollection:function(j)
        {
            return this.testFont(this.FontInfo[j])
        },
        testFont:function(l)
        {
            if(l.isWebFont&&d.FontFaceBug)
            {
                this.div.style.fontWeight=this.div.style.fontStyle="normal"
            }
            else
            {
                this.div.style.fontWeight=(l.weight||"normal");
                this.div.style.fontStyle=(l.style||"normal")
            }
            var k=this.getComparisonWidths(l.testString,l.noStyleChar);
            if(k)
            {
                this.div.style.fontFamily="'"+l.family+"',"+this.comparisonFont[0];
                if(this.div.offsetWidth==k[0])
                {
                    this.div.style.fontFamily="'"+l.family+"',"+this.comparisonFont[k[2]];
                    if(this.div.offsetWidth==k[1])
                    {
                        return false
                    }
                }
                if(this.div.offsetWidth!=k[3]||this.div.offsetHeight!=k[4])
                {
                    if(l.noStyleChar||!d.FONTDATA||!d.FONTDATA.hasStyleChar)
                    {
                        return true
                    }
                    for(var n=0,j=this.testSize.length;n<j;n++)
                    {
                        if(this.testStyleChar(l,this.testSize[n]))
                        {
                            return true
                        }
                    }
                }
            }
            return false
        },
        styleChar:"\uEFFD",
        versionChar:"\uEFFE",
        compChar:"\uEFFF",
        testStyleChar:function(l,o)
        {
            var r=3+(l.weight?2:0)+(l.style?4:0);
            var k="",m=0;
            var q=this.div.style.fontSize;
            this.div.style.fontSize=o;
            if(d.msieItalicWidthBug&&l.style==="italic")
            {
                this.text.nodeValue=k=this.compChar;
                m=this.div.offsetWidth
            }
            if(d.safariTextNodeBug)
            {
                this.div.innerHTML=this.compChar+k
            }
            else
            {
                this.text.nodeValue=this.compChar+k
            }
            var j=this.div.offsetWidth-m;
            if(d.safariTextNodeBug)
            {
                this.div.innerHTML=this.styleChar+k
            }
            else
            {
                this.text.nodeValue=this.styleChar+k
            }
            var p=Math.floor((this.div.offsetWidth-m)/j+0.5);
            if(p===r)
            {
                if(d.safariTextNodeBug)
                {
                    this.div.innerHTML=this.versionChar+k
                }
                else
                {
                    this.text.nodeValue=this.versionChar+k
                }
                l.version=Math.floor((this.div.offsetWidth-m)/j+1.5)/2
            }
            this.div.style.fontSize=q;
            return(p===r)
        },
        getComparisonWidths:function(o,l){
            if(d.FONTDATA&&d.FONTDATA.hasStyleChar&&!l){
                o+=this.styleChar+" "+this.compChar
                }
                if(d.safariTextNodeBug){
                this.div.innerHTML=o
                }else{
                this.text.nodeValue=o
                }
                this.div.style.fontFamily=this.comparisonFont[0];
            var k=this.div.offsetWidth;
            this.div.style.fontFamily=d.webFontDefault;
            var q=this.div.offsetWidth,n=this.div.offsetHeight;
            for(var p=1,j=this.comparisonFont.length;p<j;p++){
                this.div.style.fontFamily=this.comparisonFont[p];
                if(this.div.offsetWidth!=k){
                    return[k,this.div.offsetWidth,p,q,n]
                    }
                }
            return null
        },
    loadWebFont:function(k){
        b.Startup.signal.Post("HTML-CSS Jax - Web-Font "+d.fontInUse+"/"+k.directory);
        var m=MathJax.Message.File("Web-Font "+d.fontInUse+"/"+k.directory);
        var j=MathJax.Callback({});
        var l=MathJax.Callback(["loadComplete",this,k,m,j]);
        g.timer.start(g,[this.checkWebFont,k,l],0,this.timeout);
        return j
        },
    loadComplete:function(l,o,k,j){
        MathJax.Message.Clear(o);
        if(j===g.STATUS.OK){
            this.webFontLoaded=true;
            k();
            return
        }
        this.loadError(l);
        if(b.Browser.isFirefox&&d.allowWebFonts){
            var m=document.location.protocol+"//"+document.location.hostname;
            if(document.location.port!=""){
                m+=":"+document.location.port
                }
                m+="/";
            if(g.fileURL(d.webfontDir).substr(0,m.length)!==m){
                this.firefoxFontError(l)
                }
            }
        if(!this.webFontLoaded){
        d.loadWebFontError(l,k)
        }else{
        k()
        }
    },
loadError:function(j){
    MathJax.Message.Set("Can't load web font "+d.fontInUse+"/"+j.directory,null,2000);
    b.Startup.signal.Post(["HTML-CSS Jax - web font error",d.fontInUse+"/"+j.directory,j])
    },
firefoxFontError:function(j){
    MathJax.Message.Set("Firefox can't load web fonts from a remote host",null,3000);
    b.Startup.signal.Post("HTML-CSS Jax - Firefox web fonts on remote host error")
    },
checkWebFont:function(j,k,l){
    if(j.time(l)){
        return
    }
    if(d.Font.testFont(k)){
        l(j.STATUS.OK)
        }else{
        setTimeout(j,j.delay)
        }
    },
fontFace:function(l){
    var m=d.allowWebFonts;
    var o=d.FONTDATA.FONTS[l];
    if(d.msieFontCSSBug&&!o.family.match(/-Web$/)){
        o.family+="-Web"
        }
        var k=g.fileURL(d.webfontDir+"/"+m);
    var j=l.replace(/-b/,"-B").replace(/-i/,"-I").replace(/-Bold-/,"-Bold");
    if(!j.match(/-/)){
        j+="-Regular"
        }
        if(m==="svg"){
        j+=".svg#"+j
        }else{
        j+="."+m
        }
        var n={
        "font-family":o.family,
        src:"url('"+k+"/"+j+"')"
        };
        
    if(m==="otf"){
        n.src+=" format('opentype')";
        k=g.fileURL(d.webfontDir+"/woff");
        n.src="url('"+k+"/"+j.replace(/otf$/,"woff")+"') format('woff'), "+n.src
        }else{
        if(m!=="eot"){
            n.src+=" format('"+m+"')"
            }
        }
    if(!(d.FontFaceBug&&o.isWebFont)){
    if(l.match(/-bold/)){
        n["font-weight"]="bold"
        }
        if(l.match(/-italic/)){
        n["font-style"]="italic"
        }
    }
return n
}
});
var i,a,c;
d.Augment({
    config:{
        styles:{
            ".MathJax":{
                display:"inline",
                "font-style":"normal",
                "font-weight":"normal",
                "line-height":"normal",
                "font-size":"100%",
                "font-size-adjust":"none",
                "text-indent":0,
                "text-align":"left",
                "text-transform":"none",
                "letter-spacing":"normal",
                "word-spacing":"normal",
                "word-wrap":"normal",
                "white-space":"nowrap",
                "float":"none",
                direction:"ltr",
                border:0,
                padding:0,
                margin:0
            },
            ".MathJax_Display":{
                position:"relative",
                display:"block",
                width:"100%"
            },
            ".MathJax img, .MathJax nobr, .MathJax a":{
                border:0,
                padding:0,
                margin:0,
                "max-width":"none",
                "max-height":"none",
                "vertical-align":0,
                "line-height":"normal",
                "text-decoration":"none"
            },
            "img.MathJax_strut":{
                border:"0 !important",
                padding:"0 !important",
                margin:"0 !important",
                "vertical-align":"0 !important"
            },
            ".MathJax span":{
                display:"inline",
                position:"static",
                border:0,
                padding:0,
                margin:0,
                "vertical-align":0,
                "line-height":"normal",
                "text-decoration":"none"
            },
            ".MathJax nobr":{
                "white-space":"nowrap ! important"
            },
            ".MathJax img":{
                display:"inline ! important",
                "float":"none ! important"
            },
            ".MathJax_Processing":{
                visibility:"hidden",
                position:"fixed",
                width:0,
                height:0,
                overflow:"hidden"
            },
            ".MathJax_Processed":{
                display:"none!important"
            },
            ".MathJax_ExBox":{
                display:"block",
                overflow:"hidden",
                width:"1px",
                height:"60ex"
            },
            ".MathJax .MathJax_EmBox":{
                display:"block",
                overflow:"hidden",
                width:"1px",
                height:"60em"
            },
            ".MathJax .MathJax_HitBox":{
                cursor:"text",
                background:"white",
                opacity:0,
                filter:"alpha(opacity=0)"
            },
            ".MathJax .MathJax_HitBox *":{
                filter:"none",
                opacity:1,
                background:"transparent"
            },
            "#MathJax_Tooltip":{
                position:"absolute",
                left:0,
                top:0,
                width:"auto",
                height:"auto",
                display:"none"
            },
            "#MathJax_Tooltip *":{
                filter:"none",
                opacity:1,
                background:"transparent"
            },
            "@font-face":{
                "font-family":"MathJax_Blank",
                src:"url('about:blank')"
            }
        }
    },
settings:b.config.menuSettings,
hideProcessedMath:true,
Font:null,
webFontDefault:"MathJax_Blank",
Config:function(){
    if(!this.require){
        this.require=[]
        }
        this.Font=e();
    this.SUPER(arguments).Config.call(this);
    var k=this.settings;
    if(this.adjustAvailableFonts){
        this.adjustAvailableFonts(this.config.availableFonts)
        }
        if(k.scale){
        this.config.scale=k.scale
        }
        if(k.font&&k.font!=="Auto"){
        if(k.font==="TeX (local)"){
            this.config.availableFonts=["TeX"];
            this.config.preferredFont="TeX";
            this.config.webFont="TeX"
            }else{
            if(k.font==="STIX (local)"){
                this.config.availableFonts=["STIX"];
                this.config.preferredFont="STIX";
                this.config.webFont="TeX"
                }else{
                if(k.font==="TeX (web)"){
                    this.config.availableFonts=[];
                    this.config.preferredFont="";
                    this.config.webFont="TeX"
                    }else{
                    if(k.font==="TeX (image)"){
                        this.config.availableFonts=[];
                        this.config.preferredFont="";
                        this.config.webFont=""
                        }
                    }
            }
    }
}
var j=this.Font.findFont(this.config.availableFonts,this.config.preferredFont);
if(!j&&this.allowWebFonts){
    j=this.config.webFont;
    if(j){
        this.webFonts=true
        }
    }
if(!j&&this.config.imageFont){
    j=this.config.imageFont;
    this.imgFonts=true
    }
    if(j){
    this.fontInUse=j;
    this.fontDir+="/"+j;
    this.webfontDir+="/"+j;
    this.require.push(this.fontDir+"/fontdata.js");
    if(this.imgFonts){
        this.require.push(this.directory+"/imageFonts.js");
        b.Startup.signal.Post("HTML-CSS Jax - using image fonts")
        }
    }else{
    MathJax.Message.Set("Can't find a valid font using ["+this.config.availableFonts.join(", ")+"]",null,3000);
    this.FONTDATA={
        TeX_factor:1,
        baselineskip:1.2,
        lineH:0.8,
        lineD:0.2,
        ffLineH:0.8,
        FONTS:{},
        VARIANT:{
            normal:{
                fonts:[]
            }
        },
    RANGES:[],
    DELIMITERS:{},
    RULECHAR:45,
    REMAP:{}
};

if(MathJax.InputJax.TeX&&MathJax.InputJax.TeX.Definitions){
    MathJax.InputJax.TeX.Definitions.macros.overline[1]="002D";
    MathJax.InputJax.TeX.Definitions.macros.underline[1]="002D"
    }
    b.Startup.signal.Post("HTML-CSS Jax - no valid font")
}
this.require.push(MathJax.OutputJax.extensionDir+"/MathEvents.js")
},
Startup:function(){
    i=MathJax.Extension.MathEvents.Event;
    a=MathJax.Extension.MathEvents.Touch;
    c=MathJax.Extension.MathEvents.Hover;
    this.ContextMenu=i.ContextMenu;
    this.Mousedown=i.AltContextMenu;
    this.Mouseover=c.Mouseover;
    this.Mouseout=c.Mouseout;
    this.Mousemove=c.Mousemove;
    this.hiddenDiv=this.Element("div",{
        style:{
            visibility:"hidden",
            overflow:"hidden",
            position:"absolute",
            top:0,
            height:"1px",
            width:"auto",
            padding:0,
            border:0,
            margin:0,
            textAlign:"left",
            textIndent:0,
            textTransform:"none",
            lineHeight:"normal",
            letterSpacing:"normal",
            wordSpacing:"normal"
        }
    });
if(!document.body.firstChild){
    document.body.appendChild(this.hiddenDiv)
    }else{
    document.body.insertBefore(this.hiddenDiv,document.body.firstChild)
    }
    this.hiddenDiv=this.addElement(this.hiddenDiv,"div",{
    id:"MathJax_Hidden"
});
var k=this.addElement(this.hiddenDiv,"div",{
    style:{
        width:"5in"
    }
});
this.pxPerInch=k.offsetWidth/5;
this.hiddenDiv.removeChild(k);
this.startMarker=this.createStrut(this.Element("span"),10,true);
this.endMarker=this.addText(this.Element("span"),"x").parentNode;
this.HDspan=this.Element("span");
if(this.operaHeightBug){
    this.createStrut(this.HDspan,0)
    }
    if(this.msieInlineBlockAlignBug){
    this.HDimg=this.addElement(this.HDspan,"img",{
        style:{
            height:"0px",
            width:"1px"
        }
    });
try{
    this.HDimg.src="about:blank"
    }catch(j){}
}else{
    this.HDimg=this.createStrut(this.HDspan,0)
    }
    this.EmExSpan=this.Element("span",{
    style:{
        position:"absolute",
        "font-size-adjust":"none"
    }
},[["span",{
    className:"MathJax_ExBox"
}],["span",{
    className:"MathJax"
},[["span",{
    className:"MathJax_EmBox"
}]]]]);
this.linebreakSpan=this.Element("span",null,[["hr",{
    style:{
        width:"100%",
        size:1,
        padding:0,
        border:0,
        margin:0
    }
}]]);
return g.Styles(this.config.styles,["InitializeHTML",this])
},
removeSTIXfonts:function(l){
    for(var k=0,j=l.length;k<j;k++){
        if(l[k]==="STIX"){
            l.splice(k,1);
            j--;
            k--
        }
    }
    if(this.config.preferredFont==="STIX"){
    this.config.preferredFont=l[0]
    }
},
PreloadWebFonts:function(){
    if(!d.allowWebFonts||!d.config.preloadWebFonts){
        return
    }
    for(var k=0,j=d.config.preloadWebFonts.length;k<j;k++){
        var l=d.FONTDATA.FONTS[d.config.preloadWebFonts[k]];
        if(!l.available){
            d.Font.testFont(l)
            }
        }
    },
InitializeHTML:function(){
    this.PreloadWebFonts();
    document.body.appendChild(this.EmExSpan);
    document.body.appendChild(this.linebreakSpan);
    this.defaultEx=this.EmExSpan.firstChild.offsetHeight/60;
    this.defaultEm=this.EmExSpan.lastChild.firstChild.offsetHeight/60;
    this.defaultWidth=this.linebreakSpan.firstChild.offsetWidth;
    document.body.removeChild(this.linebreakSpan);
    document.body.removeChild(this.EmExSpan)
    },
preTranslate:function(n){
    var t=n.jax[this.id],u,q=t.length,y,s,z,l,x,k,w,p,r,j,v=false,A=this.config.linebreaks.automatic,o=this.config.linebreaks.width;
    if(A){
        v=(o.match(/^\s*(\d+(\.\d*)?%\s*)?container\s*$/)!=null);
        if(v){
            o=o.replace(/\s*container\s*/,"")
            }else{
            j=this.defaultWidth
            }
            if(o===""){
            o="100%"
            }
        }else{
    j=100000
    }
    for(u=0;u<q;u++){
    y=t[u];
    if(!y.parentNode){
        continue
    }
    s=y.previousSibling;
    if(s&&String(s.className).match(/^MathJax(_Display)?$/)){
        s.parentNode.removeChild(s)
        }
        k=y.MathJax.elementJax;
    k.HTMLCSS={
        display:(k.root.Get("display")==="block")
        };
        
    z=l=this.Element("span",{
        className:"MathJax",
        id:k.inputID+"-Frame",
        isMathJax:true,
        jaxID:this.id,
        oncontextmenu:i.Menu,
        onmousedown:i.Mousedown,
        onmouseover:i.Mouseover,
        onmouseout:i.Mouseout,
        onmousemove:i.Mousemove,
        onclick:i.Click,
        ondblclick:i.DblClick
        });
    if(b.Browser.noContextMenu){
        z.ontouchstart=a.start;
        z.ontouchend=a.end
        }
        if(k.HTMLCSS.display){
        l=this.Element("div",{
            className:"MathJax_Display"
        });
        l.appendChild(z)
        }else{
        if(this.msieDisappearingBug){
            z.style.display="inline-block"
            }
        }
    l.setAttribute("role","textbox");
l.setAttribute("aria-readonly","true");
l.className+=" MathJax_Processing";
y.parentNode.insertBefore(l,y);
y.parentNode.insertBefore(this.EmExSpan.cloneNode(true),y);
if(v){
    l.parentNode.insertBefore(this.linebreakSpan.cloneNode(true),l)
    }
}
for(u=0;u<q;u++){
    y=t[u];
    if(!y.parentNode){
        continue
    }
    x=y.previousSibling;
    l=x.previousSibling;
    k=y.MathJax.elementJax;
    w=x.firstChild.offsetHeight/60;
    p=x.lastChild.firstChild.offsetHeight/60;
    if(v){
        j=l.previousSibling.firstChild.offsetWidth
        }
        if(w===0||w==="NaN"){
        this.hiddenDiv.appendChild(l);
        k.HTMLCSS.isHidden=true;
        w=this.defaultEx;
        p=this.defaultEm;
        if(v){
            j=this.defaultWidth
            }
        }
    r=Math.floor(Math.max(this.config.minScaleAdjust/100,(w/this.TeX.x_height)/p)*this.config.scale);
    k.HTMLCSS.scale=r/100;
    k.HTMLCSS.fontSize=r+"%";
    k.HTMLCSS.em=k.HTMLCSS.outerEm=p;
    this.em=p*r/100;
    k.HTMLCSS.ex=w;
    k.HTMLCSS.lineWidth=(A?this.length2em(o,1,j/this.em):1000000)
    }
    for(u=0;u<q;u++){
    y=t[u];
    if(!y.parentNode){
        continue
    }
    x=t[u].previousSibling;
    k=t[u].MathJax.elementJax;
    if(v){
        z=x.previousSibling;
        if(!k.HTMLCSS.isHidden){
            z=z.previousSibling
            }
            z.parentNode.removeChild(z)
        }
        x.parentNode.removeChild(x)
    }
    n.HTMLCSSeqn=n.HTMLCSSlast=0;
n.HTMLCSSchunk=this.config.EqnChunk;
n.HTMLCSSdelay=false
},
Translate:function(k,o){
    if(!k.parentNode){
        return
    }
    if(o.HTMLCSSdelay){
        o.HTMLCSSdelay=false;
        b.RestartAfter(MathJax.Callback.Delay(this.config.EqnChunkDelay))
        }
        var j=k.MathJax.elementJax,n=j.root,l=document.getElementById(j.inputID+"-Frame"),p=(j.HTMLCSS.display?l.parentNode:l);
    this.em=f.mbase.prototype.em=j.HTMLCSS.em*j.HTMLCSS.scale;
    this.outerEm=j.HTMLCSS.em;
    this.scale=j.HTMLCSS.scale;
    this.linebreakWidth=j.HTMLCSS.lineWidth;
    l.style.fontSize=j.HTMLCSS.fontSize;
    this.initImg(l);
    this.initHTML(n,l);
    n.setTeXclass();
    try{
        n.toHTML(l,p)
        }catch(m){
        if(m.restart){
            while(l.firstChild){
                l.removeChild(l.firstChild)
                }
            }
        throw m
    }
    if(j.HTMLCSS.isHidden){
    k.parentNode.insertBefore(p,k)
    }
    p.className=p.className.split(/ /)[0];
if(this.hideProcessedMath){
    p.className+=" MathJax_Processed";
    if(k.MathJax.preview){
        j.HTMLCSS.preview=k.MathJax.preview;
        delete k.MathJax.preview
        }
        o.HTMLCSSeqn++;
    if(o.HTMLCSSeqn>=o.HTMLCSSlast+o.HTMLCSSchunk){
        this.postTranslate(o);
        o.HTMLCSSchunk=Math.floor(o.HTMLCSSchunk*this.config.EqnChunkFactor);
        o.HTMLCSSdelay=true
        }
    }
},
postTranslate:function(q){
    var k=q.jax[this.id];
    if(!this.hideProcessedMath){
        return
    }
    for(var o=q.HTMLCSSlast,j=q.HTMLCSSeqn;o<j;o++){
        var l=k[o];
        if(l){
            l.previousSibling.className=l.previousSibling.className.split(/ /)[0];
            var p=l.MathJax.elementJax.HTMLCSS;
            if(p.preview){
                p.preview.innerHTML="";
                l.MathJax.preview=p.preview;
                delete p.preview
                }
            }
    }
    if(this.forceReflow){
    var n=(document.styleSheets||[])[0]||{};
    
    n.disabled=true;
    n.disabled=false
    }
    q.HTMLCSSlast=q.HTMLCSSeqn
},
getJaxFromMath:function(j){
    if(j.parentNode.className==="MathJax_Display"){
        j=j.parentNode
        }
        return b.getJaxFor(j.nextSibling)
    },
getHoverSpan:function(j,k){
    return j.root.HTMLspanElement()
    },
getHoverBBox:function(j,m,n){
    var o=m.bbox,l=j.HTMLCSS.outerEm;
    var k={
        w:o.w*l,
        h:o.h*l,
        d:o.d*l
        };
        
    if(o.width){
        k.width=o.width
        }
        return k
    },
Zoom:function(k,u,t,j,r){
    u.className="MathJax";
    u.style.fontSize=k.HTMLCSS.fontSize;
    var w=u.appendChild(this.EmExSpan.cloneNode(true));
    var n=w.lastChild.firstChild.offsetHeight/60;
    this.em=f.mbase.prototype.em=n;
    this.outerEm=n/k.HTMLCSS.scale;
    w.parentNode.removeChild(w);
    this.idPostfix="-zoom";
    k.root.toHTML(u,u);
    this.idPostfix="";
    var m=k.root.HTMLspanElement().bbox.width;
    if(m){
        u.style.width=Math.floor(j-1.5*d.em)+"px";
        u.style.display="inline-block";
        var l=(k.root.id||"MathJax-Span-"+k.root.spanID)+"-zoom";
        var o=document.getElementById(l).firstChild;
        while(o&&o.style.width!==m){
            o=o.nextSibling
            }
            if(o){
            o.style.width="100%"
            }
        }
    u.style.position=t.style.position="absolute";
var s=u.offsetWidth,q=u.offsetHeight,v=t.offsetHeight,p=t.offsetWidth;
if(p===0){
    p=t.parentNode.offsetWidth
    }
    u.style.position=t.style.position="";
return{
    Y:-i.getBBox(u).h,
    mW:p,
    mH:v,
    zW:s,
    zH:q
}
},
initImg:function(j){},
initHTML:function(k,j){},
initFont:function(j){
    var l=d.FONTDATA.FONTS,k=d.config.availableFonts;
    if(k&&k.length&&d.Font.testFont(l[j])){
        l[j].available=true;
        return null
        }
        if(!this.allowWebFonts){
        return null
        }
        l[j].isWebFont=true;
    if(d.FontFaceBug){
        l[j].family=j;
        if(d.msieFontCSSBug){
            l[j].family+="-Web"
            }
        }
    return g.Styles({
    "@font-face":this.Font.fontFace(j)
    })
},
Remove:function(j){
    var k=document.getElementById(j.inputID+"-Frame");
    if(k){
        if(j.HTMLCSS.display){
            k=k.parentNode
            }
            k.parentNode.removeChild(k)
        }
        delete j.HTMLCSS
    },
getHD:function(k){
    var j=k.style.position;
    k.style.position="absolute";
    this.HDimg.style.height="0px";
    k.appendChild(this.HDspan);
    var l={
        h:k.offsetHeight
        };
        
    this.HDimg.style.height=l.h+"px";
    l.d=k.offsetHeight-l.h;
    l.h-=l.d;
    l.h/=this.em;
    l.d/=this.em;
    k.removeChild(this.HDspan);
    k.style.position=j;
    return l
    },
getW:function(n){
    var k,m,l=(n.bbox||{}).w,o=n;
    if(n.bbox&&n.bbox.exactW){
        return l
        }
        if((n.bbox&&l>=0&&!this.initialSkipBug)||this.negativeBBoxes||!n.firstChild){
        k=n.offsetWidth;
        m=n.parentNode.offsetHeight
        }else{
        if(n.bbox&&l<0&&this.msieNegativeBBoxBug){
            k=-n.offsetWidth,m=n.parentNode.offsetHeight
            }else{
            if(this.initialSkipBug){
                var j=n.style.position;
                n.style.position="absolute";
                o=this.startMarker;
                n.insertBefore(o,n.firstChild)
                }
                n.appendChild(this.endMarker);
            k=this.endMarker.offsetLeft-o.offsetLeft;
            n.removeChild(this.endMarker);
            if(this.initialSkipBug){
                n.removeChild(o);
                n.style.position=j
                }
            }
    }
if(m!=null){
    n.parentNode.HH=m/this.em
    }
    return k/this.em
},
Measured:function(l,k){
    var m=l.bbox;
    if(m.width==null&&m.w&&!m.isMultiline){
        var j=this.getW(l);
        m.rw+=j-m.w;
        m.w=j;
        m.exactW=true
        }
        if(!k){
        k=l.parentNode
        }
        if(!k.bbox){
        k.bbox=m
        }
        return l
    },
Remeasured:function(k,j){
    j.bbox=this.Measured(k,j).bbox
    },
MeasureSpans:function(n){
    var q=[],s,p,l,t,j,o,k;
    for(p=0,l=n.length;p<l;p++){
        s=n[p];
        if(!s){
            continue
        }
        t=s.bbox;
        if(t.exactW||t.width||t.w===0||t.isMultiline){
            if(!s.parentNode.bbox){
                s.parentNode.bbox=t
                }
                continue
        }
        if(this.negativeBBoxes||!s.firstChild||(t.w>=0&&!this.initialSkipBug)||(t.w<0&&this.msieNegativeBBoxBug)){
            q.push([s])
            }else{
            if(this.initialSkipBug){
                j=this.startMarker.cloneNode(true);
                o=this.endMarker.cloneNode(true);
                s.insertBefore(j,s.firstChild);
                s.appendChild(o);
                q.push([s,j,o,s.style.position]);
                s.style.position="absolute"
                }else{
                o=this.endMarker.cloneNode(true);
                s.appendChild(o);
                q.push([s,null,o])
                }
            }
    }
    for(p=0,l=q.length;p<l;p++){
    s=q[p][0];
    t=s.bbox;
    var r=s.parentNode;
    if((t.w>=0&&!this.initialSkipBug)||this.negativeBBoxes||!s.firstChild){
        k=s.offsetWidth;
        r.HH=s.parentNode.offsetHeight/this.em
        }else{
        if(t.w<0&&this.msieNegativeBBoxBug){
            k=-s.offsetWidth,r.HH=s.parentNode.offsetHeight/this.em
            }else{
            k=q[p][2].offsetLeft-((q[p][1]||{}).offsetLeft||0)
            }
        }
    k/=this.em;
t.rw+=k-t.w;
t.w=k;
t.exactW=true;
if(!r.bbox){
    r.bbox=t
    }
}
for(p=0,l=q.length;p<l;p++){
    s=q[p];
    if(s[1]){
        s[1].parentNode.removeChild(s[1]),s[0].style.position=s[3]
        }
        if(s[2]){
        s[2].parentNode.removeChild(s[2])
        }
    }
},
Em:function(j){
    if(Math.abs(j)<0.0006){
        return"0em"
        }
        return j.toFixed(3).replace(/\.?0+$/,"")+"em"
    },
Percent:function(j){
    return(100*j).toFixed(1).replace(/\.?0+$/,"")+"%"
    },
length2em:function(q,k,o){
    if(typeof(q)!=="string"){
        q=q.toString()
        }
        if(q===""){
        return""
        }
        if(q===f.SIZE.NORMAL){
        return 1
        }
        if(q===f.SIZE.BIG){
        return 2
        }
        if(q===f.SIZE.SMALL){
        return 0.71
        }
        if(q==="infinity"){
        return d.BIGDIMEN
        }
        var n=this.FONTDATA.TeX_factor;
    if(q.match(/mathspace$/)){
        return d.MATHSPACE[q]*n
        }
        var l=q.match(/^\s*([-+]?(?:\.\d+|\d+(?:\.\d*)?))?(pt|em|ex|mu|px|pc|in|mm|cm|%)?/);
    var j=parseFloat(l[1]||"1"),p=l[2];
    if(o==null){
        o=1
        }
        if(k==null){
        k=1
        }
        if(p==="em"){
        return j*n
        }
        if(p==="ex"){
        return j*d.TeX.x_height*n
        }
        if(p==="%"){
        return j/100*o
        }
        if(p==="px"){
        return j/d.em
        }
        if(p==="pt"){
        return j/10*n
        }
        if(p==="pc"){
        return j*1.2*n
        }
        if(p==="in"){
        return j*this.pxPerInch/d.em
        }
        if(p==="cm"){
        return j*this.pxPerInch/d.em/2.54
        }
        if(p==="mm"){
        return j*this.pxPerInch/d.em/25.4
        }
        if(p==="mu"){
        return j/18*n*k
        }
        return j*n*o
    },
thickness2em:function(k,j){
    var l=d.TeX.rule_thickness;
    if(k===f.LINETHICKNESS.MEDIUM){
        return l
        }
        if(k===f.LINETHICKNESS.THIN){
        return 0.67*l
        }
        if(k===f.LINETHICKNESS.THICK){
        return 1.67*l
        }
        return this.length2em(k,j,l)
    },
getPadding:function(k){
    var m={
        top:0,
        right:0,
        bottom:0,
        left:0
    },j=false;
    for(var n in m){
        if(m.hasOwnProperty(n)){
            var l=k.style["padding"+n.charAt(0).toUpperCase()+n.substr(1)];
            if(l){
                m[n]=this.length2em(l);
                j=true
                }
            }
    }
    return(j?m:false)
},
getBorders:function(o){
    var l={
        top:0,
        right:0,
        bottom:0,
        left:0
    },m={},k=false;
    for(var p in l){
        if(l.hasOwnProperty(p)){
            var j="border"+p.charAt(0).toUpperCase()+p.substr(1);
            var n=o.style[j+"Style"];
            if(n){
                k=true;
                l[p]=this.length2em(o.style[j+"Width"]);
                m[j]=[o.style[j+"Width"],o.style[j+"Style"],o.style[j+"Color"]].join(" ")
                }
            }
    }
    l.css=m;
return(k?l:false)
},
setBorders:function(j,k){
    if(k){
        for(var l in k.css){
            if(k.css.hasOwnProperty(l)){
                j.style[l]=k.css[l]
                }
            }
        }
    },
createStrut:function(l,k,m){
    var j=this.Element("span",{
        isMathJax:true,
        style:{
            display:"inline-block",
            overflow:"hidden",
            height:k+"px",
            width:"1px",
            marginRight:"-1px"
        }
    });
if(m){
    l.insertBefore(j,l.firstChild)
    }else{
    l.appendChild(j)
    }
    return j
},
createBlank:function(k,j,l){
    var m=this.Element("span",{
        isMathJax:true,
        style:{
            display:"inline-block",
            overflow:"hidden",
            height:"1px",
            width:this.Em(j)
            }
        });
if(l){
    k.insertBefore(m,k.firstChild)
    }else{
    k.appendChild(m)
    }
    return m
},
createShift:function(k,j,m){
    var l=this.Element("span",{
        style:{
            marginLeft:this.Em(j)
            },
        isMathJax:true
    });
    if(m){
        k.insertBefore(l,k.firstChild)
        }else{
        k.appendChild(l)
        }
        return l
    },
createSpace:function(n,m,q,j,k){
    if(m<-q){
        q=-m
        }
        var l=this.Em(m+q),o=this.Em(-q);
    if(this.msieInlineBlockAlignBug){
        o=this.Em(d.getHD(n.parentNode).d-q)
        }
        if(n.isBox||n.className=="mspace"){
        var p=(n.scale==null?1:n.scale);
        n.bbox={
            exactW:true,
            h:m*p,
            d:q*p,
            w:j*p,
            rw:j*p,
            lw:0
        };
        
        n.style.height=l;
        n.style.verticalAlign=o;
        n.HH=(m+q)*p
        }else{
        n=this.addElement(n,"span",{
            style:{
                height:l,
                verticalAlign:o
            },
            isMathJax:true
        })
        }
        if(j>=0){
        n.style.width=this.Em(j);
        n.style.display="inline-block";
        n.style.overflow="hidden"
        }else{
        if(this.msieNegativeSpaceBug){
            n.style.height=""
            }
            n.style.marginLeft=this.Em(j);
        if(d.safariNegativeSpaceBug&&n.parentNode.firstChild==n){
            this.createBlank(n,0,true)
            }
        }
    if(k&&k!==f.COLOR.TRANSPARENT){
    n.style.backgroundColor=k;
    n.style.position="relative"
    }
    return n
},
createRule:function(q,m,o,r,k){
    if(m<-o){
        o=-m
        }
        var l=d.TeX.min_rule_thickness,n=1;
    if(r>0&&r*this.em<l){
        r=l/this.em
        }
        if(m+o>0&&(m+o)*this.em<l){
        n=1/(m+o)*(l/this.em);
        m*=n;
        o*=n
        }
        if(!k){
        k="solid"
        }else{
        k="solid "+k
        }
        k=this.Em(r)+" "+k;
    var s=(n===1?this.Em(m+o):l+"px"),j=this.Em(-o);
    var p=this.addElement(q,"span",{
        style:{
            borderLeft:k,
            display:"inline-block",
            overflow:"hidden",
            width:0,
            height:s,
            verticalAlign:j
        },
        bbox:{
            h:m,
            d:o,
            w:r,
            rw:r,
            lw:0,
            exactW:true
        },
        noAdjust:true,
        HH:m+o,
        isMathJax:true
    });
    if(r>0&&p.offsetWidth==0){
        p.style.width=this.Em(r)
        }
        if(q.isBox||q.className=="mspace"){
        q.bbox=p.bbox,q.HH=m+o
        }
        return p
    },
createFrame:function(r,p,q,s,v,k){
    if(p<-q){
        q=-p
        }
        var o=(this.msieBorderWidthBug?0:2*v);
    var u=this.Em(p+q-o),j=this.Em(-q-v),n=this.Em(s-o);
    var l=this.Em(v)+" "+k;
    var m=this.addElement(r,"span",{
        style:{
            border:l,
            display:"inline-block",
            overflow:"hidden",
            width:n,
            height:u
        },
        bbox:{
            h:p,
            d:q,
            w:s,
            rw:s,
            lw:0,
            exactW:true
        },
        noAdjust:true,
        HH:p+q,
        isMathJax:true
    });
    if(j){
        m.style.verticalAlign=j
        }
        return m
    },
createStack:function(l,n,k){
    if(this.msiePaddingWidthBug){
        this.createStrut(l,0)
        }
        var m=String(k).match(/%$/);
    var j=(!m&&k!=null?k:0);
    l=this.addElement(l,"span",{
        noAdjust:true,
        HH:0,
        isMathJax:true,
        style:{
            display:"inline-block",
            position:"relative",
            width:(m?"100%":this.Em(j)),
            height:0
        }
    });
if(!n){
    l.parentNode.bbox=l.bbox={
        exactW:true,
        h:-this.BIGDIMEN,
        d:-this.BIGDIMEN,
        w:j,
        lw:this.BIGDIMEN,
        rw:(!m&&k!=null?k:-this.BIGDIMEN)
        };
        
    if(m){
        l.bbox.width=k
        }
    }
return l
},
createBox:function(k,j){
    var l=this.addElement(k,"span",{
        style:{
            position:"absolute"
        },
        isBox:true,
        isMathJax:true
    });
    if(j!=null){
        l.style.width=j
        }
        return l
    },
addBox:function(j,k){
    k.style.position="absolute";
    k.isBox=true;
    return j.appendChild(k)
    },
placeBox:function(s,q,p,n){
    s.isMathJax=true;
    var u=s.parentNode,B=s.bbox,w=u.bbox;
    if(this.msiePlaceBoxBug){
        this.addText(s,this.NBSP)
        }
        if(this.imgSpaceBug){
        this.addText(s,this.imgSpace)
        }
        var v,E=0;
    if(s.HH!=null){
        v=s.HH
        }else{
        if(B){
            v=Math.max(3,B.h+B.d)
            }else{
            v=s.offsetHeight/this.em
            }
        }
    if(!s.noAdjust){
    v+=1;
    if(this.msieInlineBlockAlignBug){
        this.addElement(s,"img",{
            className:"MathJax_strut",
            border:0,
            src:"about:blank",
            isMathJax:true,
            style:{
                width:0,
                height:this.Em(v)
                }
            })
    }else{
    this.addElement(s,"span",{
        isMathJax:true,
        style:{
            display:"inline-block",
            width:0,
            height:this.Em(v)
            }
        })
}
}
if(B){
    if(this.initialSkipBug){
        if(B.lw<0){
            E=B.lw;
            d.createBlank(s,-E,true)
            }
            if(B.rw>B.w){
            d.createBlank(s,B.rw-B.w+0.1)
            }
        }
    if(!this.msieClipRectBug&&!B.noclip&&!n){
    var A=3/this.em;
    var z=(B.H==null?B.h:B.H),k=(B.D==null?B.d:B.D);
    var C=v-z-A,o=v+k+A,m=B.lw-3*A,j=1000;
    if(this.initialSkipBug&&B.lw<0){
        m=-3*A
        }
        if(B.isFixed){
        j=B.width-m
        }
        s.style.clip="rect("+this.Em(C)+" "+this.Em(j)+" "+this.Em(o)+" "+this.Em(m)+")"
    }
}
s.style.top=this.Em(-p-v);
s.style.left=this.Em(q+E);
if(B&&w){
    if(B.H!=null&&(w.H==null||B.H+p>w.H)){
        w.H=B.H+p
        }
        if(B.D!=null&&(w.D==null||B.D-p>w.D)){
        w.D=B.D-p
        }
        if(B.h+p>w.h){
        w.h=B.h+p
        }
        if(B.d-p>w.d){
        w.d=B.d-p
        }
        if(w.H!=null&&w.H<=w.h){
        delete w.H
        }
        if(w.D!=null&&w.D<=w.d){
        delete w.D
        }
        if(B.w+q>w.w){
        w.w=B.w+q;
        if(w.width==null){
            u.style.width=this.Em(w.w)
            }
        }
    if(B.rw+q>w.rw){
    w.rw=B.rw+q
    }
    if(B.lw+q<w.lw){
    w.lw=B.lw+q
    }
    if(B.width!=null&&!B.isFixed){
    if(w.width==null){
        u.style.width=w.width="100%"
        }
        s.style.width=B.width
    }
}
},
alignBox:function(m,s,q){
    this.placeBox(m,0,q);
    var o=m.bbox;
    if(o.isMultiline){
        return
    }
    var k=o.width!=null&&!o.isFixed;
    var n=0,p=-o.w/2,j="50%";
    if(this.initialSkipBug){
        n=o.w-o.rw-0.1;
        p+=o.lw
        }
        if(this.msieMarginScaleBug){
        p=(p*this.em)+"px"
        }else{
        p=this.Em(p)
        }
        if(k){
        p="";
        j=(50-parseFloat(o.width)/2)+"%"
        }
        b.Insert(m.style,({
        right:{
            left:"",
            right:this.Em(n)
            },
        center:{
            left:j,
            marginLeft:p
        }
    })[s])
},
setStackWidth:function(k,j){
    if(typeof(j)==="number"){
        k.style.width=this.Em(Math.max(0,j));
        var l=k.bbox;
        if(l){
            l.w=j;
            l.exactW=true
            }
            l=k.parentNode.bbox;
        if(l){
            l.w=j;
            l.exactW=true
            }
        }else{
    k.style.width=k.parentNode.style.width="100%";
    if(k.bbox){
        k.bbox.width=j
        }
        if(k.parentNode.bbox){
        k.parentNode.bbox.width=j
        }
    }
},
createDelimiter:function(t,j,l,p,n){
    if(!j){
        t.bbox={
            h:0,
            d:0,
            w:this.TeX.nulldelimiterspace,
            lw:0
        };
        
        t.bbox.rw=t.bbox.w;
        this.createSpace(t,t.bbox.h,t.bbox.d,t.bbox.w);
        return
    }
    if(!p){
        p=1
        }
        if(!(l instanceof Array)){
        l=[l,l]
        }
        var s=l[1];
    l=l[0];
    var k={
        alias:j
    };
    while(k.alias){
        j=k.alias;
        k=this.FONTDATA.DELIMITERS[j];
        if(!k){
            k={
                HW:[0,this.FONTDATA.VARIANT[f.VARIANT.NORMAL]]
                }
            }
    }
if(k.load){
    b.RestartAfter(g.Require(this.fontDir+"/fontdata-"+k.load+".js"))
    }
    for(var r=0,o=k.HW.length;r<o;r++){
    if(k.HW[r][0]*p>=l-0.01||(r==o-1&&!k.stretch)){
        if(k.HW[r][2]){
            p*=k.HW[r][2]
            }
            if(k.HW[r][3]){
            j=k.HW[r][3]
            }
            var q=this.addElement(t,"span");
        this.createChar(q,[j,k.HW[r][1]],p,n);
        t.bbox=q.bbox;
        t.offset=0.65*t.bbox.w;
        t.scale=p;
        return
    }
}
if(k.stretch){
    this["extendDelimiter"+k.dir](t,s,k.stretch,p,n)
    }
},
extendDelimiterV:function(z,s,D,E,v){
    var m=this.createStack(z,true);
    var u=this.createBox(m),t=this.createBox(m);
    this.createChar(u,(D.top||D.ext),E,v);
    this.createChar(t,(D.bot||D.ext),E,v);
    var l={
        bbox:{
            w:0,
            lw:0,
            rw:0
        }
    },C=l,o;
var A=u.bbox.h+u.bbox.d+t.bbox.h+t.bbox.d;
var q=-u.bbox.h;
this.placeBox(u,0,q,true);
q-=u.bbox.d;
if(D.mid){
    C=this.createBox(m);
    this.createChar(C,D.mid,E,v);
    A+=C.bbox.h+C.bbox.d
    }
    if(s>A){
    l=this.Element("span");
    this.createChar(l,D.ext,E,v);
    var B=l.bbox.h+l.bbox.d,j=B-0.05,w,p,x=(D.mid?2:1);
    p=w=Math.ceil((s-A)/(x*j));
    if(!D.fullExtenders){
        j=(s-A)/(x*w)
        }
        var r=(w/(w+1))*(B-j);
    j=B-r;
    q+=r+j-l.bbox.h;
    while(x-->0){
        while(w-->0){
            if(!this.msieCloneNodeBug){
                o=l.cloneNode(true)
                }else{
                o=this.Element("span");
                this.createChar(o,D.ext,E,v)
                }
                q-=j;
            this.placeBox(this.addBox(m,o),0,q,true)
            }
            q+=r-l.bbox.d;
        if(D.mid&&x){
            this.placeBox(C,0,q-C.bbox.h,true);
            w=p;
            q+=-(C.bbox.h+C.bbox.d)+r+j-l.bbox.h
            }
        }
}else{
    q+=(A-s)/2;
    if(D.mid){
        this.placeBox(C,0,q-C.bbox.h,true);
        q+=-(C.bbox.h+C.bbox.d)
        }
        q+=(A-s)/2
    }
    this.placeBox(t,0,q-t.bbox.h,true);
q-=t.bbox.h+t.bbox.d;
z.bbox={
    w:Math.max(u.bbox.w,l.bbox.w,t.bbox.w,C.bbox.w),
    lw:Math.min(u.bbox.lw,l.bbox.lw,t.bbox.lw,C.bbox.lw),
    rw:Math.max(u.bbox.rw,l.bbox.rw,t.bbox.rw,C.bbox.rw),
    h:0,
    d:-q,
    exactW:true
};

z.scale=E;
z.offset=0.55*z.bbox.w;
z.isMultiChar=true;
this.setStackWidth(m,z.bbox.w)
},
extendDelimiterH:function(A,m,D,F,v){
    var q=this.createStack(A,true);
    var o=this.createBox(q),B=this.createBox(q);
    this.createChar(o,(D.left||D.rep),F,v);
    this.createChar(B,(D.right||D.rep),F,v);
    var j=this.Element("span");
    this.createChar(j,D.rep,F,v);
    var C={
        bbox:{
            h:-this.BIGDIMEN,
            d:-this.BIGDIMEN
            }
        },l;
this.placeBox(o,-o.bbox.lw,0,true);
var t=(o.bbox.rw-o.bbox.lw)+(B.bbox.rw-B.bbox.lw)-0.05,s=o.bbox.rw-o.bbox.lw-0.025,u;
if(D.mid){
    C=this.createBox(q);
    this.createChar(C,D.mid,F,v);
    t+=C.bbox.w
    }
    if(m>t){
    var E=j.bbox.rw-j.bbox.lw,p=E-0.05,y,r,z=(D.mid?2:1);
    r=y=Math.ceil((m-t)/(z*p));
    if(!D.fillExtenders){
        p=(m-t)/(z*y)
        }
        u=(y/(y+1))*(E-p);
    p=E-u;
    s-=j.bbox.lw+u;
    while(z-->0){
        while(y-->0){
            if(!this.msieCloneNodeBug){
                l=j.cloneNode(true)
                }else{
                l=this.Element("span");
                this.createChar(l,D.rep,F,v)
                }
                this.placeBox(this.addBox(q,l),s,0,true);
            s+=p
            }
            if(D.mid&&z){
            this.placeBox(C,s,0,true);
            s+=C.bbox.w-u;
            y=r
            }
        }
}else{
    u=Math.min(t-m,o.bbox.w/2);
    s-=u/2;
    if(D.mid){
        this.placeBox(C,s,0,true);
        s+=C.bbox.w
        }
        s-=u/2
    }
    this.placeBox(B,s,0,true);
A.bbox={
    w:s+B.bbox.rw,
    lw:0,
    rw:s+B.bbox.rw,
    H:Math.max(o.bbox.h,j.bbox.h,B.bbox.h,C.bbox.h),
    D:Math.max(o.bbox.d,j.bbox.d,B.bbox.d,C.bbox.d),
    h:j.bbox.h,
    d:j.bbox.d,
    exactW:true
};

A.scale=F;
A.isMultiChar=true;
this.setStackWidth(q,A.bbox.w)
},
createChar:function(r,o,l,j){
    r.isMathJax=true;
    var q=r,s="",n={
        fonts:[o[1]],
        noRemap:true
    };
    
    if(j&&j===f.VARIANT.BOLD){
        n.fonts=[o[1]+"-bold",o[1]]
        }
        if(typeof(o[1])!=="string"){
        n=o[1]
        }
        if(o[0] instanceof Array){
        for(var p=0,k=o[0].length;p<k;p++){
            s+=String.fromCharCode(o[0][p])
            }
        }else{
    s=String.fromCharCode(o[0])
    }
    if(o[4]){
    l*=o[4]
    }
    if(l!==1||o[3]){
    q=this.addElement(r,"span",{
        style:{
            fontSize:this.Percent(l)
            },
        scale:l,
        isMathJax:true
    });
    this.handleVariant(q,n,s);
    r.bbox=q.bbox
    }else{
    this.handleVariant(r,n,s)
    }
    if(o[2]){
    r.style.marginLeft=this.Em(o[2])
    }
    if(o[3]){
    r.firstChild.style.verticalAlign=this.Em(o[3]);
    r.bbox.h+=o[3];
    if(r.bbox.h<0){
        r.bbox.h=0
        }
    }
if(o[5]){
    r.bbox.h+=o[5]
    }
    if(o[6]){
    r.bbox.d+=o[6]
    }
    if(this.AccentBug&&r.bbox.w===0){
    q.firstChild.nodeValue+=this.NBSP
    }
},
positionDelimiter:function(k,j){
    j-=k.bbox.h;
    k.bbox.d-=j;
    k.bbox.h+=j;
    if(j){
        if(this.safariVerticalAlignBug||this.konquerorVerticalAlignBug||(this.operaVerticalAlignBug&&k.isMultiChar)){
            if(k.firstChild.style.display===""&&k.style.top!==""){
                k=k.firstChild;
                j-=parseFloat(k.style.top)
                }
                k.style.position="relative";
            k.style.top=this.Em(-j)
            }else{
            k.style.verticalAlign=this.Em(j);
            if(d.ffVerticalAlignBug){
                d.createRule(k.parentNode,k.bbox.h,0,0)
                }
            }
    }
},
handleVariant:function(z,o,r){
    var y="",w,B,s,C,j=z,k=!!z.style.fontFamily;
    if(r.length===0){
        return
    }
    if(!z.bbox){
        z.bbox={
            w:0,
            h:-this.BIGDIMEN,
            d:-this.BIGDIMEN,
            rw:-this.BIGDIMEN,
            lw:this.BIGDIMEN
            }
        }
    if(!o){
    o=this.FONTDATA.VARIANT[f.VARIANT.NORMAL]
    }
    C=o;
for(var A=0,x=r.length;A<x;A++){
    o=C;
    w=r.charCodeAt(A);
    B=r.charAt(A);
    if(w>=55296&&w<56319){
        A++;
        w=(((w-55296)<<10)+(r.charCodeAt(A)-56320))+65536;
        if(this.FONTDATA.RemapPlane1){
            var D=this.FONTDATA.RemapPlane1(w,o);
            w=D.n;
            o=D.variant
            }
        }else{
    var t,q,u=this.FONTDATA.RANGES;
    for(t=0,q=u.length;t<q;t++){
        if(u[t].name==="alpha"&&o.noLowerCase){
            continue
        }
        var p=o["offset"+u[t].offset];
        if(p&&w>=u[t].low&&w<=u[t].high){
            if(u[t].remap&&u[t].remap[w]){
                w=p+u[t].remap[w]
                }else{
                w=w-u[t].low+p;
                if(u[t].add){
                    w+=u[t].add
                    }
                }
            if(o["variant"+u[t].offset]){
            o=this.FONTDATA.VARIANT[o["variant"+u[t].offset]]
            }
            break
    }
    }
}
if(o.remap&&o.remap[w]){
    if(o.remap[w] instanceof Array){
        var l=o.remap[w];
        w=l[0];
        o=this.FONTDATA.VARIANT[l[1]]
        }else{
        if(typeof(o.remap[w])==="string"){
            r=o.remap[w]+r.substr(A+1);
            A=0;
            x=r.length;
            w=r.charCodeAt(0)
            }else{
            w=o.remap[w];
            if(o.remap.variant){
                o=this.FONTDATA.VARIANT[o.remap.variant]
                }
            }
    }
}
if(this.FONTDATA.REMAP[w]&&!o.noRemap){
    w=this.FONTDATA.REMAP[w];
    if(w instanceof Array){
        o=this.FONTDATA.VARIANT[w[1]];
        w=w[0]
        }
        if(typeof(w)==="string"){
        r=w+r.substr(A+1);
        A=0;
        x=r.length;
        w=w.charCodeAt(0)
        }
    }
s=this.lookupChar(o,w);
B=s[w];
if(k||(!this.checkFont(s,j.style)&&!B[5].img)){
    if(y.length){
        this.addText(j,y);
        y=""
        }
        var v=!!j.style.fontFamily||!!z.style.fontStyle||!!z.style.fontWeight||!s.directory||k;
    k=false;
    if(j!==z){
        v=!this.checkFont(s,z.style);
        j=z
        }
        if(v){
        j=this.addElement(z,"span",{
            isMathJax:true,
            subSpan:true
        })
        }
        this.handleFont(j,s,j!==z)
    }
    y=this.handleChar(j,s,B,w,y);
if(B[0]/1000>z.bbox.h){
    z.bbox.h=B[0]/1000
    }
    if(B[1]/1000>z.bbox.d){
    z.bbox.d=B[1]/1000
    }
    if(z.bbox.w+B[3]/1000<z.bbox.lw){
    z.bbox.lw=z.bbox.w+B[3]/1000
    }
    if(z.bbox.w+B[4]/1000>z.bbox.rw){
    z.bbox.rw=z.bbox.w+B[4]/1000
    }
    z.bbox.w+=B[2]/1000
}
if(y.length){
    this.addText(j,y)
    }
    if(z.scale&&z.scale!==1){
    z.bbox.h*=z.scale;
    z.bbox.d*=z.scale;
    z.bbox.w*=z.scale;
    z.bbox.lw*=z.scale;
    z.bbox.rw*=z.scale
    }
    if(r.length==1&&s.skew&&s.skew[w]){
    z.bbox.skew=s.skew[w]
    }
},
checkFont:function(j,k){
    var l=(k.fontWeight||"normal");
    if(l.match(/^\d+$/)){
        l=(parseInt(l)>=600?"bold":"normal")
        }
        return(j.family.replace(/'/g,"")===k.fontFamily.replace(/'/g,"")&&(j.style||"normal")===(k.fontStyle||"normal")&&(j.weight||"normal")===l)
    },
handleFont:function(l,j,n){
    l.style.fontFamily=j.family;
    if(!j.directory){
        l.style.fontSize=Math.floor(100/d.scale+0.5)+"%"
        }
        if(!(d.FontFaceBug&&j.isWebFont)){
        var k=j.style||"normal",m=j.weight||"normal";
        if(k!=="normal"||n){
            l.style.fontStyle=k
            }
            if(m!=="normal"||n){
            l.style.fontWeight=m
            }
        }
},
handleChar:function(k,j,r,q,p){
    var o=r[5];
    if(o.img){
        return this.handleImg(k,j,r,q,p)
        }
        if(o.isUnknown&&this.FONTDATA.DELIMITERS[q]){
        if(p.length){
            this.addText(k,p)
            }
            var m=k.scale;
        d.createDelimiter(k,q,0,1,j);
        k.scale=m;
        r[0]=k.bbox.h*1000;
        r[1]=k.bbox.d*1000;
        r[2]=k.bbox.w*1000;
        r[3]=k.bbox.lw*1000;
        r[4]=k.bbox.rw*1000;
        return""
        }
        if(o.c==null){
        if(q<=65535){
            o.c=String.fromCharCode(q)
            }else{
            var l=q-65536;
            o.c=String.fromCharCode((l>>10)+55296)+String.fromCharCode((l&1023)+56320)
            }
        }
    if(o.rfix){
    this.addText(k,o.c);
    d.createShift(k,o.rfix/1000);
    return""
    }
    if(r[2]||!this.msieAccentBug||p.length){
    return p+o.c
    }
    d.createShift(k,r[3]/1000);
d.createShift(k,(r[4]-r[3])/1000);
this.addText(k,o.c);
d.createShift(k,-r[4]/1000);
return""
},
handleImg:function(k,j,o,m,l){
    return l
    },
lookupChar:function(o,r){
    var l,j;
    if(!o.FONTS){
        var q=this.FONTDATA.FONTS;
        var p=(o.fonts||this.FONTDATA.VARIANT.normal.fonts);
        if(!(p instanceof Array)){
            p=[p]
            }
            if(o.fonts!=p){
            o.fonts=p
            }
            o.FONTS=[];
        for(l=0,j=p.length;l<j;l++){
            if(q[p[l]]){
                o.FONTS.push(q[p[l]]);
                q[p[l]].name=p[l]
                }
            }
        }
    for(l=0,j=o.FONTS.length;l<j;l++){
    var k=o.FONTS[l];
    if(typeof(k)==="string"){
        delete o.FONTS;
        this.loadFont(k)
        }
        if(k[r]){
        if(k[r].length===5){
            k[r][5]={}
        }
        if(d.allowWebFonts&&!k.available){
        this.loadWebFont(k)
        }else{
        return k
        }
    }else{
    this.findBlock(k,r)
    }
}
return this.unknownChar(o,r)
},
unknownChar:function(j,l){
    var k=(j.defaultFont||{
        family:d.config.undefinedFamily
        });
    if(j.bold){
        k.weight="bold"
        }
        if(j.italic){
        k.style="italic"
        }
        if(!k[l]){
        k[l]=[800,200,500,0,500,{
            isUnknown:true
        }]
        }
        b.signal.Post(["HTML-CSS Jax - unknown char",l,j]);
    return k
    },
findBlock:function(l,q){
    if(l.Ranges){
        for(var p=0,k=l.Ranges.length;p<k;p++){
            if(q<l.Ranges[p][0]){
                return
            }
            if(q<=l.Ranges[p][1]){
                var o=l.Ranges[p][2];
                for(var n=l.Ranges.length-1;n>=0;n--){
                    if(l.Ranges[n][2]==o){
                        l.Ranges.splice(n,1)
                        }
                    }
                this.loadFont(l.directory+"/"+o+".js")
            }
        }
    }
},
loadFont:function(k){
    var j=MathJax.Callback.Queue();
    j.Push(["Require",g,this.fontDir+"/"+k]);
    if(this.imgFonts){
        if(!MathJax.isPacked){
            k=k.replace(/\/([^\/]*)$/,d.imgPacked+"/$1")
            }
            j.Push(["Require",g,this.webfontDir+"/png/"+k])
        }
        b.RestartAfter(j.Push({}))
    },
loadWebFont:function(j){
    j.available=j.isWebFont=true;
    if(d.FontFaceBug){
        j.family=j.name;
        if(d.msieFontCSSBug){
            j.family+="-Web"
            }
        }
    b.RestartAfter(this.Font.loadWebFont(j))
},
loadWebFontError:function(k,j){
    b.Startup.signal.Post("HTML-CSS Jax - disable web fonts");
    k.isWebFont=false;
    if(this.config.imageFont&&this.config.imageFont===this.fontInUse){
        this.imgFonts=true;
        b.Startup.signal.Post("HTML-CSS Jax - switch to image fonts");
        b.Startup.signal.Post("HTML-CSS Jax - using image fonts");
        MathJax.Message.Set("Web-Fonts not available -- using image fonts instead",null,3000);
        g.Require(this.directory+"/imageFonts.js",j)
        }else{
        this.allowWebFonts=false;
        j()
        }
    },
Element:MathJax.HTML.Element,
addElement:MathJax.HTML.addElement,
TextNode:MathJax.HTML.TextNode,
addText:MathJax.HTML.addText,
ucMatch:MathJax.HTML.ucMatch,
BIGDIMEN:10000000,
ID:0,
idPostfix:"",
GetID:function(){
    this.ID++;
    return this.ID
    },
MATHSPACE:{
    veryverythinmathspace:1/18,
    verythinmathspace:2/18,
    thinmathspace:3/18,
    mediummathspace:4/18,
    thickmathspace:5/18,
    verythickmathspace:6/18,
    veryverythickmathspace:7/18,
    negativeveryverythinmathspace:-1/18,
    negativeverythinmathspace:-2/18,
    negativethinmathspace:-3/18,
    negativemediummathspace:-4/18,
    negativethickmathspace:-5/18,
    negativeverythickmathspace:-6/18,
    negativeveryverythickmathspace:-7/18
},
TeX:{
    x_height:0.430554,
    quad:1,
    num1:0.676508,
    num2:0.393732,
    num3:0.44373,
    denom1:0.685951,
    denom2:0.344841,
    sup1:0.412892,
    sup2:0.362892,
    sup3:0.288888,
    sub1:0.15,
    sub2:0.247217,
    sup_drop:0.386108,
    sub_drop:0.05,
    delim1:2.39,
    delim2:1,
    axis_height:0.25,
    rule_thickness:0.06,
    big_op_spacing1:0.111111,
    big_op_spacing2:0.166666,
    big_op_spacing3:0.2,
    big_op_spacing4:0.6,
    big_op_spacing5:0.1,
    scriptspace:0.1,
    nulldelimiterspace:0.12,
    delimiterfactor:901,
    delimitershortfall:0.1,
    min_rule_thickness:1.25
},
NBSP:"\u00A0",
rfuzz:0
});
MathJax.Hub.Register.StartupHook("mml Jax Ready",function(){
    f=MathJax.ElementJax.mml;
    f.mbase.Augment({
        toHTML:function(n){
            n=this.HTMLcreateSpan(n);
            if(this.type!="mrow"){
                n=this.HTMLhandleSize(n)
                }
                for(var k=0,j=this.data.length;k<j;k++){
                if(this.data[k]){
                    this.data[k].toHTML(n)
                    }
                }
            var p=this.HTMLcomputeBBox(n);
        var l=n.bbox.h,o=n.bbox.d;
        for(k=0,j=p.length;k<j;k++){
            p[k].HTMLstretchV(n,l,o)
            }
            if(p.length){
            this.HTMLcomputeBBox(n,true)
            }
            if(this.HTMLlineBreaks(n)){
            n=this.HTMLmultiline(n)
            }
            this.HTMLhandleSpace(n);
        this.HTMLhandleColor(n);
        return n
        },
    HTMLlineBreaks:function(){
        return false
        },
    HTMLmultiline:function(){
        f.mbase.HTMLautoloadFile("multiline")
        },
    HTMLcomputeBBox:function(p,o,n,j){
        if(n==null){
            n=0
            }
            if(j==null){
            j=this.data.length
            }
            var l=p.bbox={
            exactW:true
        },q=[];
        while(n<j){
            var k=this.data[n];
            if(!k){
                continue
            }
            if(!o&&k.HTMLcanStretch("Vertical")){
                q.push(k);
                k=(k.CoreMO()||k)
                }
                this.HTMLcombineBBoxes(k,l);
            n++
        }
        this.HTMLcleanBBox(l);
        return q
        },
    HTMLcombineBBoxes:function(j,k){
        if(k.w==null){
            this.HTMLemptyBBox(k)
            }
            var m=(j.bbox?j:j.HTMLspanElement());
        if(!m||!m.bbox){
            return
        }
        var l=m.bbox;
        if(l.d>k.d){
            k.d=l.d
            }
            if(l.h>k.h){
            k.h=l.h
            }
            if(l.D!=null&&l.D>k.D){
            k.D=l.D
            }
            if(l.H!=null&&l.H>k.H){
            k.H=l.H
            }
            if(m.style.paddingLeft){
            k.w+=parseFloat(m.style.paddingLeft)*(m.scale||1)
            }
            if(k.w+l.lw<k.lw){
            k.lw=k.w+l.lw
            }
            if(k.w+l.rw>k.rw){
            k.rw=k.w+l.rw
            }
            k.w+=l.w;
        if(m.style.paddingRight){
            k.w+=parseFloat(m.style.paddingRight)*(m.scale||1)
            }
            if(l.width){
            k.width=l.width
            }
            if(l.ic){
            k.ic=l.ic
            }else{
            delete k.ic
            }
            if(k.exactW&&!l.exactW){
            delete k.exactW
            }
        },
    HTMLemptyBBox:function(j){
        j.h=j.d=j.H=j.D=j.rw=-d.BIGDIMEN;
        j.w=0;
        j.lw=d.BIGDIMEN;
        return j
        },
    HTMLcleanBBox:function(j){
        if(j.h===this.BIGDIMEN){
            j.h=j.d=j.H=j.D=j.w=j.rw=j.lw=0
            }
            if(j.D<=j.d){
            delete j.D
            }
            if(j.H<=j.h){
            delete j.H
            }
        },
HTMLzeroBBox:function(){
    return{
        h:0,
        d:0,
        w:0,
        lw:0,
        rw:0
    }
},
HTMLcanStretch:function(j){
    if(this.isEmbellished()){
        return this.Core().HTMLcanStretch(j)
        }
        return false
    },
HTMLstretchH:function(k,j){
    return this.HTMLspanElement()
    },
HTMLstretchV:function(k,j,l){
    return this.HTMLspanElement()
    },
HTMLnotEmpty:function(j){
    while(j){
        if((j.type!=="mrow"&&j.type!=="texatom")||j.data.length>1){
            return true
            }
            j=j.data[0]
        }
        return false
    },
HTMLmeasureChild:function(k,j){
    if(this.data[k]){
        d.Measured(this.data[k].toHTML(j),j)
        }else{
        j.bbox=this.HTMLzeroBBox()
        }
    },
HTMLboxChild:function(k,j){
    if(this.data[k]){
        return this.data[k].toHTML(j)
        }
        return null
    },
HTMLcreateSpan:function(j){
    if(this.spanID){
        var k=this.HTMLspanElement();
        if(k){
            while(k.firstChild){
                k.removeChild(k.firstChild)
                }
                k.bbox={
                w:0,
                h:0,
                d:0,
                lw:0,
                rw:0
            };
            
            k.scale=1;
            k.isMultChar=null;
            k.style.cssText="";
            return k
            }
        }
    if(this.href){
    j=d.addElement(j,"a",{
        href:this.href,
        isMathJax:true
    })
    }
    j=d.addElement(j,"span",{
    className:this.type,
    isMathJax:true
});
if(d.imgHeightBug){
    j.style.display="inline-block"
    }
    if(this["class"]){
    j.className+=" "+this["class"]
    }
    if(!this.spanID){
    this.spanID=d.GetID()
    }
    j.id=(this.id||"MathJax-Span-"+this.spanID)+d.idPostfix;
j.bbox={
    w:0,
    h:0,
    d:0,
    lw:0,
    rw:0
};

this.styles={};

if(this.style){
    j.style.cssText=this.style;
    if(j.style.fontSize){
        this.mathsize=j.style.fontSize;
        j.style.fontSize=""
        }
        this.styles={
        border:d.getBorders(j),
        padding:d.getPadding(j)
        };
        
    if(this.styles.border){
        j.style.border=""
        }
        if(this.styles.padding){
        j.style.padding=""
        }
    }
if(this.href){
    j.parentNode.bbox=j.bbox
    }
    return j
},
HTMLspanElement:function(){
    if(!this.spanID){
        return null
        }
        return document.getElementById((this.id||"MathJax-Span-"+this.spanID)+d.idPostfix)
    },
HTMLhandleVariant:function(k,j,l){
    d.handleVariant(k,j,l)
    },
HTMLhandleSize:function(j){
    if(!j.scale){
        j.scale=this.HTMLgetScale();
        if(j.scale!==1){
            j.style.fontSize=d.Percent(j.scale)
            }
        }
    return j
},
HTMLhandleColor:function(v){
    var x=this.getValues("mathcolor","color");
    if(this.mathbackground){
        x.mathbackground=this.mathbackground
        }
        if(this.background){
        x.background=this.background
        }
        if(this.style&&v.style.backgroundColor){
        x.mathbackground=v.style.backgroundColor;
        v.style.backgroundColor="transparent"
        }
        var s=(this.styles||{}).border,u=(this.styles||{}).padding;
    if(x.color&&!this.mathcolor){
        x.mathcolor=x.color
        }
        if(x.background&&!this.mathbackground){
        x.mathbackground=x.background
        }
        if(x.mathcolor){
        v.style.color=x.mathcolor
        }
        if((x.mathbackground&&x.mathbackground!==f.COLOR.TRANSPARENT)||s||u){
        var z=v.bbox,y=(z.exact?0:1/d.em),t=0,r=0,l=v.style.paddingLeft,p=v.style.paddingRight;
        if(this.isToken){
            t=z.lw;
            r=z.rw-z.w
            }
            if(l!==""){
            t+=parseFloat(l)*(v.scale||1)
            }
            if(p!==""){
            r-=parseFloat(p)*(v.scale||1)
            }
            var k=(d.PaddingWidthBug||z.keepPadding||z.exactW?0:r-t);
        var n=Math.max(0,d.getW(v)+k);
        var w=z.h+z.d,j=-z.d,q=0,o=0;
        if(n>0){
            n+=2*y;
            t-=y
            }
            if(w>0){
            w+=2*y;
            j-=y
            }
            r=-n-t;
        if(s){
            r-=s.right;
            j-=s.bottom;
            q+=s.left;
            o+=s.right;
            z.h+=s.top;
            z.d+=s.bottom;
            z.w+=s.left+s.right;
            z.lw-=s.left;
            z.rw+=s.right
            }
            if(u){
            w+=u.top+u.bottom;
            n+=u.left+u.right;
            r-=u.right;
            j-=u.bottom;
            q+=u.left;
            o+=u.right;
            z.h+=u.top;
            z.d+=u.bottom;
            z.w+=u.left+u.right;
            z.lw-=u.left;
            z.rw+=u.right
            }
            if(o){
            v.style.paddingRight=d.Em(o)
            }
            var m=d.Element("span",{
            id:"MathJax-Color-"+this.spanID+d.idPostfix,
            isMathJax:true,
            style:{
                display:"inline-block",
                backgroundColor:x.mathbackground,
                width:d.Em(n),
                height:d.Em(w),
                verticalAlign:d.Em(j),
                marginLeft:d.Em(t),
                marginRight:d.Em(r)
                }
            });
    d.setBorders(m,s);
    if(z.width){
        m.style.width=z.width;
        m.style.marginRight="-"+z.width
        }
        if(d.msieInlineBlockAlignBug){
        m.style.position="relative";
        m.style.width=m.style.height=0;
        m.style.verticalAlign=m.style.marginLeft=m.style.marginRight="";
        m.style.border=m.style.padding="";
        if(s&&d.msieBorderWidthBug){
            w+=s.top+s.bottom;
            n+=s.left+s.right
            }
            m.style.width=d.Em(q+y);
        d.placeBox(d.addElement(m,"span",{
            noAdjust:true,
            isMathJax:true,
            style:{
                display:"inline-block",
                position:"absolute",
                overflow:"hidden",
                background:(x.mathbackground||"transparent"),
                width:d.Em(n),
                height:d.Em(w)
                }
            }),t,z.h+y);
    d.setBorders(m.firstChild,s)
    }
    v.parentNode.insertBefore(m,v);
if(d.msieColorPositionBug){
    v.style.position="relative"
    }
    return m
}
return null
},
HTMLremoveColor:function(){
    var j=document.getElementById("MathJax-Color-"+this.spanID+d.idPostfix);
    if(j){
        j.parentNode.removeChild(j)
        }
    },
HTMLhandleSpace:function(n){
    if(this.useMMLspacing){
        if(this.type!=="mo"){
            return
        }
        var l=this.getValues("scriptlevel","lspace","rspace");
        if(l.scriptlevel<=0||this.hasValue("lspace")||this.hasValue("rspace")){
            var k=this.HTMLgetMu(n);
            l.lspace=Math.max(0,d.length2em(l.lspace,k));
            l.rspace=Math.max(0,d.length2em(l.rspace,k));
            var j=this,m=this.Parent();
            while(m&&m.isEmbellished()&&m.Core()===j){
                j=m;
                m=m.Parent();
                n=j.HTMLspanElement()
                }
                if(l.lspace){
                n.style.paddingLeft=d.Em(l.lspace)
                }
                if(l.rspace){
                n.style.paddingRight=d.Em(l.rspace)
                }
            }
    }else{
    var o=this.texSpacing();
    if(o!==""){
        o=d.length2em(o,this.HTMLgetScale())/(n.scale||1);
        if(n.style.paddingLeft){
            o+=parseFloat(n.style.paddingLeft)
            }
            n.style.paddingLeft=d.Em(o)
        }
    }
},
HTMLgetScale:function(){
    var l=1,j=this.getValues("mathsize","scriptlevel","fontsize");
    if(this.style){
        var k=this.HTMLspanElement();
        if(k.style.fontSize!=""){
            j.fontsize=k.style.fontSize
            }
        }
    if(j.fontsize&&!this.mathsize){
    j.mathsize=j.fontsize
    }
    if(j.scriptlevel!==0){
    if(j.scriptlevel>2){
        j.scriptlevel=2
        }
        l=Math.pow(this.Get("scriptsizemultiplier"),j.scriptlevel);
    j.scriptminsize=d.length2em(this.Get("scriptminsize"));
    if(l<j.scriptminsize){
        l=j.scriptminsize
        }
    }
l*=d.length2em(j.mathsize);
return l
},
HTMLgetMu:function(l){
    var j=1,k=this.getValues("scriptlevel","scriptsizemultiplier");
    if(l.scale&&l.scale!==1){
        j=1/l.scale
        }
        if(k.scriptlevel!==0){
        if(k.scriptlevel>2){
            k.scriptlevel=2
            }
            j=Math.sqrt(Math.pow(k.scriptsizemultiplier,k.scriptlevel))
        }
        return j
    },
HTMLgetVariant:function(){
    var j=this.getValues("mathvariant","fontfamily","fontweight","fontstyle");
    j.hasVariant=this.Get("mathvariant",true);
    if(!j.hasVariant){
        j.family=j.fontfamily;
        j.weight=j.fontweight;
        j.style=j.fontstyle
        }
        if(this.style){
        var l=this.HTMLspanElement();
        if(!j.family&&l.style.fontFamily){
            j.family=l.style.fontFamily
            }
            if(!j.weight&&l.style.fontWeight){
            j.weight=l.style.fontWeight
            }
            if(!j.style&&l.style.fontStyle){
            j.style=l.style.fontStyle
            }
        }
    if(j.weight&&j.weight.match(/^\d+$/)){
    j.weight=(parseInt(j.weight)>600?"bold":"normal")
    }
    var k=j.mathvariant;
if(this.variantForm){
    k="-"+d.fontInUse+"-variant"
    }
    if(j.family&&!j.hasVariant){
    if(!j.weight&&j.mathvariant.match(/bold/)){
        j.weight="bold"
        }
        if(!j.style&&j.mathvariant.match(/italic/)){
        j.style="italic"
        }
        return{
        FONTS:[],
        fonts:[],
        noRemap:true,
        defaultFont:{
            family:j.family,
            style:j.style,
            weight:j.weight
            }
        }
}
if(j.weight==="bold"){
    k={
        normal:f.VARIANT.BOLD,
        italic:f.VARIANT.BOLDITALIC,
        fraktur:f.VARIANT.BOLDFRAKTUR,
        script:f.VARIANT.BOLDSCRIPT,
        "sans-serif":f.VARIANT.BOLDSANSSERIF,
        "sans-serif-italic":f.VARIANT.SANSSERIFBOLDITALIC
        }
        [k]||k
    }else{
    if(j.weight==="normal"){
        k={
            bold:f.VARIANT.normal,
            "bold-italic":f.VARIANT.ITALIC,
            "bold-fraktur":f.VARIANT.FRAKTUR,
            "bold-script":f.VARIANT.SCRIPT,
            "bold-sans-serif":f.VARIANT.SANSSERIF,
            "sans-serif-bold-italic":f.VARIANT.SANSSERIFITALIC
            }
            [k]||k
        }
    }
if(j.style==="italic"){
    k={
        normal:f.VARIANT.ITALIC,
        bold:f.VARIANT.BOLDITALIC,
        "sans-serif":f.VARIANT.SANSSERIFITALIC,
        "bold-sans-serif":f.VARIANT.SANSSERIFBOLDITALIC
        }
        [k]||k
    }else{
    if(j.style==="normal"){
        k={
            italic:f.VARIANT.NORMAL,
            "bold-italic":f.VARIANT.BOLD,
            "sans-serif-italic":f.VARIANT.SANSSERIF,
            "sans-serif-bold-italic":f.VARIANT.BOLDSANSSERIF
            }
            [k]||k
        }
    }
return d.FONTDATA.VARIANT[k]
}
},{
    HTMLautoload:function(){
        var j=d.autoloadDir+"/"+this.type+".js";
        b.RestartAfter(g.Require(j))
        },
    HTMLautoloadFile:function(j){
        var k=d.autoloadDir+"/"+j+".js";
        b.RestartAfter(g.Require(k))
        },
    HTMLstretchH:function(k,j){
        this.HTMLremoveColor();
        return this.toHTML(k,j)
        },
    HTMLstretchV:function(k,j,l){
        this.HTMLremoveColor();
        return this.toHTML(k,j,l)
        }
    });
f.chars.Augment({
    toHTML:function(m,l,k,n){
        var q=this.data.join("").replace(/[\u2061-\u2064]/g,"");
        if(k){
            q=k(q,n)
            }
            if(!l){
            var p=Math.floor(100/d.scale+0.5)+"%";
            d.addElement(m,"span",{
                style:{
                    "font-size":p
                }
            },[q]);
        var o=d.getHD(m),j=d.getW(m);
        m.bbox={
            h:o.h,
            d:o.d,
            w:j,
            lw:0,
            rw:j,
            exactW:true
        }
    }else{
    this.HTMLhandleVariant(m,l,q)
    }
}
});
f.entity.Augment({
    toHTML:function(l,k){
        var o=this.toString().replace(/[\u2061-\u2064]/g,"");
        if(!k){
            var n=Math.floor(100/d.scale+0.5)+"%";
            d.addElement(l,"span",{
                style:{
                    "font-size":n
                }
            },[o]);
        var m=d.getHD(l),j=d.getW(l);
        l.bbox={
            h:m.h,
            d:m.d,
            w:j,
            lw:0,
            rw:j,
            exactW:true
        }
    }else{
    this.HTMLhandleVariant(l,k,o)
    }
}
});
f.mi.Augment({
    toHTML:function(n){
        n=this.HTMLhandleSize(this.HTMLcreateSpan(n));
        n.bbox=null;
        var l=this.HTMLgetVariant();
        for(var k=0,j=this.data.length;k<j;k++){
            if(this.data[k]){
                this.data[k].toHTML(n,l)
                }
            }
        if(!n.bbox){
        n.bbox={
            w:0,
            h:0,
            d:0,
            rw:0,
            lw:0
        }
    }
    var p=this.data.join(""),o=n.bbox;
    if(o.skew&&p.length!==1){
    delete o.skew
    }
    if(o.rw>o.w&&p.length===1&&!l.noIC){
    o.ic=o.rw-o.w;
    d.createBlank(n,o.ic);
    o.w=o.rw
    }
    this.HTMLhandleSpace(n);
    this.HTMLhandleColor(n);
    return n
    }
});
f.mn.Augment({
    toHTML:function(n){
        n=this.HTMLhandleSize(this.HTMLcreateSpan(n));
        n.bbox=null;
        var l=this.HTMLgetVariant();
        for(var k=0,j=this.data.length;k<j;k++){
            if(this.data[k]){
                this.data[k].toHTML(n,l)
                }
            }
        if(!n.bbox){
        n.bbox={
            w:0,
            h:0,
            d:0,
            rw:0,
            lw:0
        }
    }
    if(this.data.join("").length!==1){
    delete n.bbox.skew
    }
    this.HTMLhandleSpace(n);
    this.HTMLhandleColor(n);
    return n
    }
});
f.mo.Augment({
    toHTML:function(t){
        t=this.HTMLhandleSize(this.HTMLcreateSpan(t));
        if(this.data.length==0){
            return t
            }else{
            t.bbox=null
            }
            var w=this.data.join("");
        var o=this.HTMLgetVariant();
        var v=this.getValues("largeop","displaystyle");
        if(v.largeop){
            o=d.FONTDATA.VARIANT[v.displaystyle?"-largeOp":"-smallOp"]
            }
            var u=this.CoreParent(),n=(u&&u.isa(f.msubsup)&&this!==u.data[u.base]),k=(n?this.HTMLremapChars:null);
        if(w.length===1&&u&&u.isa(f.munderover)&&this.CoreText(u.data[u.base]).length===1){
            var r=u.data[u.over],s=u.data[u.under];
            if(r&&this===r.CoreMO()&&u.Get("accent")){
                k=d.FONTDATA.REMAPACCENT
                }else{
                if(s&&this===s.CoreMO()&&u.Get("accentunder")){
                    k=d.FONTDATA.REMAPACCENTUNDER
                    }
                }
        }
    if(n&&d.fontInUse==="STIX"&&w.match(/['`"\u00B4\u2032-\u2037]/)){
    o=d.FONTDATA.VARIANT["-STIX-variant"]
    }
    for(var q=0,l=this.data.length;q<l;q++){
    if(this.data[q]){
        this.data[q].toHTML(t,o,this.HTMLremap,k)
        }
    }
if(!t.bbox){
    t.bbox={
        w:0,
        h:0,
        d:0,
        rw:0,
        lw:0
    }
}
if(w.length!==1){
    delete t.bbox.skew
    }
    if(d.AccentBug&&t.bbox.w===0&&w.length===1&&t.firstChild){
    t.firstChild.nodeValue+=d.NBSP;
    d.createSpace(t,0,0,-t.offsetWidth/d.em)
    }
    if(v.largeop){
    var j=(t.bbox.h-t.bbox.d)/2-d.TeX.axis_height*t.scale;
    if(d.safariVerticalAlignBug&&t.lastChild.nodeName==="IMG"){
        t.lastChild.style.verticalAlign=d.Em(parseFloat(t.lastChild.style.verticalAlign||0)/d.em-j/t.scale)
        }else{
        if(d.konquerorVerticalAlignBug&&t.lastChild.nodeName==="IMG"){
            t.style.position="relative";
            t.lastChild.style.position="relative";
            t.lastChild.style.top=d.Em(j/t.scale)
            }else{
            t.style.verticalAlign=d.Em(-j/t.scale)
            }
        }
    t.bbox.h-=j;
t.bbox.d+=j;
if(t.bbox.rw>t.bbox.w){
    t.bbox.ic=t.bbox.rw-t.bbox.w;
    d.createBlank(t,t.bbox.ic);
    t.bbox.w=t.bbox.rw
    }
}
this.HTMLhandleSpace(t);
this.HTMLhandleColor(t);
return t
},
CoreParent:function(){
    var j=this;
    while(j&&j.isEmbellished()&&j.CoreMO()===this&&!j.isa(f.math)){
        j=j.Parent()
        }
        return j
    },
CoreText:function(j){
    if(!j){
        return""
        }
        if(j.isEmbellished()){
        return j.CoreMO().data.join("")
        }while(j.isa(f.mrow)&&j.data.length===1&&j.data[0]){
        j=j.data[0]
        }
        if(!j.isToken){
        return""
        }else{
        return j.data.join("")
        }
    },
HTMLremapChars:{
    "*":"\u2217",
    '"':"\u2033",
    "\u00B0":"\u2218",
    "\u00B2":"2",
    "\u00B3":"3",
    "\u00B4":"\u2032",
    "\u00B9":"1"
},
HTMLremap:function(k,j){
    k=k.replace(/-/g,"\u2212");
    if(j){
        k=k.replace(/'/g,"\u2032").replace(/`/g,"\u2035");
        if(k.length===1){
            k=j[k]||k
            }
        }
    return k
},
HTMLcanStretch:function(m){
    if(!this.Get("stretchy")){
        return false
        }
        var n=this.data.join("");
    if(n.length>1){
        return false
        }
        var k=this.CoreParent();
    if(k&&k.isa(f.munderover)&&this.CoreText(k.data[k.base]).length===1){
        var l=k.data[k.over],j=k.data[k.under];
        if(l&&this===l.CoreMO()&&k.Get("accent")){
            n=d.FONTDATA.REMAPACCENT[n]||n
            }else{
            if(j&&this===j.CoreMO()&&k.Get("accentunder")){
                n=d.FONTDATA.REMAPACCENTUNDER[n]||n
                }
            }
    }
n=d.FONTDATA.DELIMITERS[n.charCodeAt(0)];
return(n&&n.dir==m.substr(0,1))
},
HTMLstretchV:function(l,m,n){
    this.HTMLremoveColor();
    var q=this.getValues("symmetric","maxsize","minsize");
    var o=this.HTMLspanElement(),r=this.HTMLgetMu(o),p;
    var j=d.TeX.axis_height,k=o.scale;
    if(q.symmetric){
        p=2*Math.max(m-j,n+j)
        }else{
        p=m+n
        }
        q.maxsize=d.length2em(q.maxsize,r,o.bbox.h+o.bbox.d);
    q.minsize=d.length2em(q.minsize,r,o.bbox.h+o.bbox.d);
    p=Math.max(q.minsize,Math.min(q.maxsize,p));
    o=this.HTMLcreateSpan(l);
    d.createDelimiter(o,this.data.join("").charCodeAt(0),p,k);
    if(q.symmetric){
        p=(o.bbox.h+o.bbox.d)/2+j
        }else{
        p=(o.bbox.h+o.bbox.d)*m/(m+n)
        }
        d.positionDelimiter(o,p);
    this.HTMLhandleSpace(o);
    this.HTMLhandleColor(o);
    return o
    },
HTMLstretchH:function(n,j){
    this.HTMLremoveColor();
    var l=this.getValues("maxsize","minsize","mathvariant","fontweight");
    if((l.fontweight==="bold"||parseInt(l.fontweight)>=600)&&!this.Get("mathvariant",true)){
        l.mathvariant=f.VARIANT.BOLD
        }
        var m=this.HTMLspanElement(),k=this.HTMLgetMu(m),o=m.scale;
    l.maxsize=d.length2em(l.maxsize,k,m.bbox.w);
    l.minsize=d.length2em(l.minsize,k,m.bbox.w);
    j=Math.max(l.minsize,Math.min(l.maxsize,j));
    m=this.HTMLcreateSpan(n);
    d.createDelimiter(m,this.data.join("").charCodeAt(0),j,o,l.mathvariant);
    this.HTMLhandleSpace(m);
    this.HTMLhandleColor(m);
    return m
    }
});
f.mtext.Augment({
    toHTML:function(n){
        n=this.HTMLhandleSize(this.HTMLcreateSpan(n));
        var l=this.HTMLgetVariant();
        if(d.config.mtextFontInherit||this.Parent().type==="merror"){
            l=null
            }
            for(var k=0,j=this.data.length;k<j;k++){
            if(this.data[k]){
                this.data[k].toHTML(n,l)
                }
            }
        if(!n.bbox){
        n.bbox={
            w:0,
            h:0,
            d:0,
            rw:0,
            lw:0
        }
    }
    if(this.data.join("").length!==1){
    delete n.bbox.skew
    }
    this.HTMLhandleSpace(n);
    this.HTMLhandleColor(n);
    return n
    }
});
f.merror.Augment({
    toHTML:function(k){
        var m=MathJax.HTML.addElement(k,"span",{
            style:{
                display:"inline-block"
            }
        });
    k=this.SUPER(arguments).toHTML.call(this,m);
    var l=d.getHD(m),j=d.getW(m);
    m.bbox={
        h:l.h,
        d:l.d,
        w:j,
        lw:0,
        rw:j,
        exactW:true
    };
    
    m.id=k.id;
    k.id=null;
    return m
    }
});
f.ms.Augment({
    toHTML:f.mbase.HTMLautoload
    });
f.mglyph.Augment({
    toHTML:f.mbase.HTMLautoload
    });
f.mspace.Augment({
    toHTML:function(n){
        n=this.HTMLhandleSize(this.HTMLcreateSpan(n));
        var l=this.getValues("height","depth","width");
        var k=this.HTMLgetMu(n);
        l.mathbackground=this.mathbackground;
        if(this.background&&!this.mathbackground){
            l.mathbackground=this.background
            }
            var m=d.length2em(l.height,k)/n.scale,o=d.length2em(l.depth,k)/n.scale,j=d.length2em(l.width,k)/n.scale;
        d.createSpace(n,m,o,j,l.mathbackground);
        return n
        }
    });
f.mphantom.Augment({
    toHTML:function(n,k,p){
        n=this.HTMLcreateSpan(n);
        if(this.data[0]!=null){
            var o=this.data[0].toHTML(n);
            if(p!=null){
                d.Remeasured(this.data[0].HTMLstretchV(n,k,p),n)
                }else{
                if(k!=null){
                    d.Remeasured(this.data[0].HTMLstretchH(n,k),n)
                    }else{
                    o=d.Measured(o,n)
                    }
                }
            n.bbox={
            w:o.bbox.w,
            h:o.bbox.h,
            d:o.bbox.d,
            lw:0,
            rw:0,
            exactW:true
        };
        
        for(var l=0,j=n.childNodes.length;l<j;l++){
            n.childNodes[l].style.visibility="hidden"
            }
        }
        this.HTMLhandleSpace(n);
    this.HTMLhandleColor(n);
    return n
    },
HTMLstretchH:f.mbase.HTMLstretchH,
HTMLstretchV:f.mbase.HTMLstretchV
});
f.mpadded.Augment({
    toHTML:function(r,l,j){
        r=this.HTMLcreateSpan(r);
        if(this.data[0]!=null){
            var p=d.createStack(r,true);
            var m=d.createBox(p);
            var k=this.data[0].toHTML(m);
            if(j!=null){
                d.Remeasured(this.data[0].HTMLstretchV(m,l,j),m)
                }else{
                if(l!=null){
                    d.Remeasured(this.data[0].HTMLstretchH(m,l),m)
                    }else{
                    d.Measured(k,m)
                    }
                }
            var s=this.getValues("height","depth","width","lspace","voffset"),q=0,o=0,t=this.HTMLgetMu(r);
        if(s.lspace){
            q=this.HTMLlength2em(m,s.lspace,t)
            }
            if(s.voffset){
            o=this.HTMLlength2em(m,s.voffset,t)
            }
            d.placeBox(m,q,o);
        r.bbox={
            h:m.bbox.h,
            d:m.bbox.d,
            w:m.bbox.w,
            exactW:true,
            lw:Math.min(0,m.bbox.lw+q),
            rw:Math.max(m.bbox.w,m.bbox.rw+q),
            H:Math.max((m.bbox.H==null?-d.BIGDIMEN:m.bbox.H),m.bbox.h+o),
            D:Math.max((m.bbox.D==null?-d.BIGDIMEN:m.bbox.D),m.bbox.d-o)
            };
            
        if(s.height!==""){
            r.bbox.h=this.HTMLlength2em(m,s.height,t,"h",0)
            }
            if(s.depth!==""){
            r.bbox.d=this.HTMLlength2em(m,s.depth,t,"d",0)
            }
            if(s.width!==""){
            r.bbox.w=this.HTMLlength2em(m,s.width,t,"w",0)
            }
            if(r.bbox.H<=r.bbox.h){
            delete r.bbox.H
            }
            if(r.bbox.D<=r.bbox.d){
            delete r.bbox.D
            }
            var n=/^\s*(\d+(\.\d*)?|\.\d+)\s*(pt|em|ex|mu|px|pc|in|mm|cm)\s*$/;
        r.bbox.exact=!!((this.data[0]&&this.data[0].data.length==0)||n.exec(s.height)||n.exec(s.width)||n.exec(s.depth));
        d.setStackWidth(p,r.bbox.w)
        }
        this.HTMLhandleSpace(r);
    this.HTMLhandleColor(r);
    return r
    },
HTMLlength2em:function(p,q,k,r,j){
    if(j==null){
        j=-d.BIGDIMEN
        }
        var n=String(q).match(/width|height|depth/);
    var o=(n?p.bbox[n[0].charAt(0)]:(r?p.bbox[r]:0));
    var l=d.length2em(q,k,o);
    if(r&&String(q).match(/^\s*[-+]/)){
        return Math.max(j,p.bbox[r]+l)
        }else{
        return l
        }
    },
HTMLstretchH:f.mbase.HTMLstretchH,
HTMLstretchV:f.mbase.HTMLstretchV
});
f.mrow.Augment({
    HTMLlineBreaks:function(j){
        if(!this.parent.linebreakContainer){
            return false
            }
            return(d.config.linebreaks.automatic&&j.bbox.w>d.linebreakWidth)||this.hasNewline()
        },
    HTMLstretchH:function(l,j){
        this.HTMLremoveColor();
        var k=this.HTMLspanElement();
        this.data[this.core].HTMLstretchH(k,j);
        this.HTMLcomputeBBox(k,true);
        this.HTMLhandleColor(k);
        return k
        },
    HTMLstretchV:function(l,k,m){
        this.HTMLremoveColor();
        var j=this.HTMLspanElement();
        this.data[this.core].HTMLstretchV(j,k,m);
        this.HTMLcomputeBBox(j,true);
        this.HTMLhandleColor(j);
        return j
        }
    });
f.mstyle.Augment({
    toHTML:function(k,j,l){
        k=this.HTMLcreateSpan(k);
        if(this.data[0]!=null){
            var m=this.data[0].toHTML(k);
            if(l!=null){
                this.data[0].HTMLstretchV(k,j,l)
                }else{
                if(j!=null){
                    this.data[0].HTMLstretchH(k,j)
                    }
                }
            k.bbox=m.bbox
        }
        this.HTMLhandleSpace(k);
    this.HTMLhandleColor(k);
    return k
    },
HTMLstretchH:f.mbase.HTMLstretchH,
HTMLstretchV:f.mbase.HTMLstretchV
});
f.mfrac.Augment({
    toHTML:function(C){
        C=this.HTMLcreateSpan(C);
        var l=d.createStack(C);
        var o=d.createBox(l),n=d.createBox(l);
        d.MeasureSpans([this.HTMLboxChild(0,o),this.HTMLboxChild(1,n)]);
        var j=this.getValues("displaystyle","linethickness","numalign","denomalign","bevelled");
        var G=this.HTMLgetScale(),B=j.displaystyle;
        var F=d.TeX.axis_height*G;
        if(j.bevelled){
            var E=(B?0.4:0.15);
            var r=Math.max(o.bbox.h+o.bbox.d,n.bbox.h+n.bbox.d)+2*E;
            var D=d.createBox(l);
            d.createDelimiter(D,47,r);
            d.placeBox(o,0,(o.bbox.d-o.bbox.h)/2+F+E);
            d.placeBox(D,o.bbox.w-E/2,(D.bbox.d-D.bbox.h)/2+F);
            d.placeBox(n,o.bbox.w+D.bbox.w-E,(n.bbox.d-n.bbox.h)/2+F-E)
            }else{
            var k=Math.max(o.bbox.w,n.bbox.w);
            var x=d.thickness2em(j.linethickness,G),z,y,w,s;
            var A=d.TeX.min_rule_thickness/this.em;
            if(B){
                w=d.TeX.num1;
                s=d.TeX.denom1
                }else{
                w=(x===0?d.TeX.num3:d.TeX.num2);
                s=d.TeX.denom2
                }
                w*=G;
            s*=G;
            if(x===0){
                z=Math.max((B?7:3)*d.TeX.rule_thickness,2*A);
                y=(w-o.bbox.d)-(n.bbox.h-s);
                if(y<z){
                    w+=(z-y)/2;
                    s+=(z-y)/2
                    }
                }else{
            z=Math.max((B?2:0)*A+x,x/2+1.5*A);
            y=(w-o.bbox.d)-(F+x/2);
            if(y<z){
                w+=z-y
                }
                y=(F-x/2)-(n.bbox.h-s);
            if(y<z){
                s+=z-y
                }
                var m=d.createBox(l);
            d.createRule(m,x,0,k+2*x);
            d.placeBox(m,0,F-x/2)
            }
            d.alignBox(o,j.numalign,w);
        d.alignBox(n,j.denomalign,-s)
        }
        this.HTMLhandleSpace(C);
    this.HTMLhandleColor(C);
    return C
    },
HTMLcanStretch:function(j){
    return false
    },
HTMLhandleSpace:function(j){
    if(!this.texWithDelims){
        var k=(this.useMMLspacing?0:d.length2em(this.texSpacing()||0))+0.12;
        j.style.paddingLeft=d.Em(k);
        j.style.paddingRight=".12em"
        }
    }
});
f.msqrt.Augment({
    toHTML:function(v){
        v=this.HTMLcreateSpan(v);
        var y=d.createStack(v);
        var m=d.createBox(y),s=d.createBox(y),r=d.createBox(y);
        var o=this.HTMLgetScale();
        var z=d.TeX.rule_thickness*o,l,k,w,n;
        if(this.Get("displaystyle")){
            l=d.TeX.x_height*o
            }else{
            l=z
            }
            k=Math.max(z+l/4,1.5*d.TeX.min_rule_thickness/this.em);
        var j=this.HTMLboxChild(0,m);
        w=j.bbox.h+j.bbox.d+k+z;
        d.createDelimiter(r,8730,w,o);
        d.MeasureSpans([j,r]);
        n=j.bbox.w;
        var u=0;
        if(r.isMultiChar||(d.AdjustSurd&&d.imgFonts)){
            r.bbox.w*=0.95
            }
            if(r.bbox.h+r.bbox.d>w){
            k=((r.bbox.h+r.bbox.d)-(w-z))/2
            }
            var A=d.FONTDATA.DELIMITERS[d.FONTDATA.RULECHAR];
        if(!A||n<A.HW[0][0]*o||o<0.75){
            d.createRule(s,z,0,n)
            }else{
            d.createDelimiter(s,d.FONTDATA.RULECHAR,n,o)
            }
            w=j.bbox.h+k+z;
        k=w*d.rfuzz;
        if(r.isMultiChar){
            k=d.rfuzz
            }
            u=this.HTMLaddRoot(y,r,u,r.bbox.h+r.bbox.d-w,o);
        d.placeBox(r,u,w-r.bbox.h);
        d.placeBox(s,u+r.bbox.w,w-s.bbox.h+k);
        d.placeBox(m,u+r.bbox.w,0);
        this.HTMLhandleSpace(v);
        this.HTMLhandleColor(v);
        return v
        },
    HTMLaddRoot:function(l,k,j,n,m){
        return j
        }
    });
f.mroot.Augment({
    toHTML:f.msqrt.prototype.toHTML,
    HTMLaddRoot:function(r,k,p,n,j){
        var l=d.createBox(r);
        if(this.data[1]){
            var o=this.data[1].toHTML(l);
            o.style.paddingRight=o.style.paddingLeft="";
            d.Measured(o,l)
            }else{
            l.bbox=this.HTMLzeroBBox()
            }
            var m=this.HTMLrootHeight(k.bbox.h+k.bbox.d,j,l)-n;
        var q=Math.min(l.bbox.w,l.bbox.rw);
        p=Math.max(q,k.offset);
        d.placeBox(l,p-q,m);
        return p-k.offset
        },
    HTMLrootHeight:function(l,k,j){
        return 0.45*(l-0.9*k)+0.6*k+Math.max(0,j.bbox.d-0.075)
        }
    });
f.mfenced.Augment({
    toHTML:function(n){
        n=this.HTMLcreateSpan(n);
        if(this.data.open){
            this.data.open.toHTML(n)
            }
            if(this.data[0]!=null){
            this.data[0].toHTML(n)
            }
            for(var k=1,j=this.data.length;k<j;k++){
            if(this.data[k]){
                if(this.data["sep"+k]){
                    this.data["sep"+k].toHTML(n)
                    }
                    this.data[k].toHTML(n)
                }
            }
        if(this.data.close){
        this.data.close.toHTML(n)
        }
        var p=this.HTMLcomputeBBox(n);
    var l=n.bbox.h,o=n.bbox.d;
    for(k=0,j=p.length;k<j;k++){
        p[k].HTMLstretchV(n,l,o)
        }
        if(p.length){
        this.HTMLcomputeBBox(n,true)
        }
        this.HTMLhandleSpace(n);
    this.HTMLhandleColor(n);
    return n
    },
HTMLcomputeBBox:function(o,n){
    var k=o.bbox={},p=[];
    this.HTMLcheckStretchy(this.data.open,k,p,n);
    this.HTMLcheckStretchy(this.data[0],k,p,n);
    for(var l=1,j=this.data.length;l<j;l++){
        if(this.data[l]){
            this.HTMLcheckStretchy(this.data["sep"+l],k,p,n);
            this.HTMLcheckStretchy(this.data[l],k,p,n)
            }
        }
    this.HTMLcheckStretchy(this.data.close,k,p,n);
    this.HTMLcleanBBox(k);
    return p
    },
HTMLcheckStretchy:function(j,k,m,l){
    if(j){
        if(!l&&j.HTMLcanStretch("Vertical")){
            m.push(j);
            j=(j.CoreMO()||j)
            }
            this.HTMLcombineBBoxes(j,k)
        }
    }
});
f.menclose.Augment({
    toHTML:f.mbase.HTMLautoload
    });
f.maction.Augment({
    toHTML:f.mbase.HTMLautoload
    });
f.semantics.Augment({
    toHTML:function(j){
        if(this.data[0]!=null){
            j=this.data[0].toHTML(j);
            this.spanID=this.data[0].spanID;
            this.HTMLhandleSpace(j)
            }
            return j
        },
    HTMLspanElement:function(){
        return(this.data[0]!=null?this.data[0].HTMLspanElement():null)
        },
    HTMLstretchH:function(k,j){
        return(this.data[0]!=null?this.data[0].HTMLstretchH(k,j):k)
        },
    HTMLstretchV:function(k,j,l){
        return(this.data[0]!=null?this.data[0].HTMLstretchV(k,j,l):k)
        }
    });
f.munderover.Augment({
    toHTML:function(K,G,E){
        var j=this.getValues("displaystyle","accent","accentunder","align");
        if(!j.displaystyle&&this.data[this.base]!=null&&this.data[this.base].CoreMO().Get("movablelimits")){
            return f.msubsup.prototype.toHTML.call(this,K)
            }
            K=this.HTMLcreateSpan(K);
        var O=this.HTMLgetScale();
        var p=d.createStack(K);
        var q=[],n=[],M=[],v,L,H;
        for(L=0,H=this.data.length;L<H;L++){
            if(this.data[L]!=null){
                v=q[L]=d.createBox(p);
                n[L]=this.data[L].toHTML(v);
                if(L==this.base){
                    if(E!=null){
                        this.data[this.base].HTMLstretchV(v,G,E)
                        }else{
                        if(G!=null){
                            this.data[this.base].HTMLstretchH(v,G)
                            }
                        }
                    M[L]=(E==null&&G!=null?false:this.data[L].HTMLcanStretch("Horizontal"))
                }else{
                M[L]=this.data[L].HTMLcanStretch("Horizontal")
                }
            }
        }
    d.MeasureSpans(n);
var l=-d.BIGDIMEN,J=l;
for(L=0,H=this.data.length;L<H;L++){
    if(this.data[L]){
        if(q[L].bbox.w>J){
            J=q[L].bbox.w
            }
            if(!M[L]&&J>l){
            l=J
            }
        }
}
if(E==null&&G!=null){
    l=G
    }else{
    if(l==-d.BIGDIMEN){
        l=J
        }
    }
for(L=J=0,H=this.data.length;L<H;L++){
    if(this.data[L]){
        v=q[L];
        if(M[L]){
            v.bbox=this.data[L].HTMLstretchH(v,l).bbox
            }
            if(v.bbox.w>J){
            J=v.bbox.w
            }
        }
}
var C=d.TeX.rule_thickness,F=d.FONTDATA.TeX_factor;
var o=q[this.base]||{
    bbox:this.HTMLzeroBBox()
    };
    
var u,r,z,w,s,B,I,N=0;
if(o.bbox.ic){
    N=1.3*o.bbox.ic+0.05
    }
    for(L=0,H=this.data.length;L<H;L++){
    if(this.data[L]!=null){
        v=q[L];
        s=d.TeX.big_op_spacing5*O;
        var A=(L!=this.base&&j[this.ACCENTS[L]]);
        if(A&&v.bbox.w<=1/d.em+0.0001){
            v.bbox.w=v.bbox.rw-v.bbox.lw;
            v.bbox.noclip=true;
            if(v.bbox.lw&&!d.zeroWidthBug){
                v.insertBefore(d.createSpace(v.parentNode,0,0,-v.bbox.lw),v.firstChild)
                }
                d.createBlank(v,0,0,v.bbox.rw+0.1)
            }
            B={
            left:0,
            center:(J-v.bbox.w)/2,
            right:J-v.bbox.w
            }
            [j.align];
        u=B;
        r=0;
        if(L==this.over){
            if(A){
                I=Math.max(C*O*F,2.5/this.em);
                s=0;
                if(o.bbox.skew){
                    u+=o.bbox.skew
                    }
                }else{
            z=d.TeX.big_op_spacing1*O*F;
            w=d.TeX.big_op_spacing3*O*F;
            I=Math.max(z,w-Math.max(0,v.bbox.d))
            }
            I=Math.max(I,1.5/this.em);
        u+=N/2;
        r=o.bbox.h+v.bbox.d+I;
        v.bbox.h+=s
        }else{
        if(L==this.under){
            if(A){
                I=3*C*O*F;
                s=0
                }else{
                z=d.TeX.big_op_spacing2*O*F;
                w=d.TeX.big_op_spacing4*O*F;
                I=Math.max(z,w-v.bbox.h)
                }
                I=Math.max(I,1.5/this.em);
            u-=N/2;
            r=-(o.bbox.d+v.bbox.h+I);
            v.bbox.d+=s
            }
        }
    d.placeBox(v,u,r)
    }
}
this.HTMLhandleSpace(K);
this.HTMLhandleColor(K);
return K
},
HTMLstretchH:f.mbase.HTMLstretchH,
HTMLstretchV:f.mbase.HTMLstretchV
});
f.msubsup.Augment({
    toHTML:function(J,H,B){
        J=this.HTMLcreateSpan(J);
        var M=this.HTMLgetScale(),G=this.HTMLgetMu(J);
        var o=d.createStack(J),k,m=[];
        var n=d.createBox(o);
        if(this.data[this.base]){
            m.push(this.data[this.base].toHTML(n));
            if(B!=null){
                this.data[this.base].HTMLstretchV(n,H,B)
                }else{
                if(H!=null){
                    this.data[this.base].HTMLstretchH(n,H)
                    }
                }
        }else{
    n.bbox=this.HTMLzeroBBox()
    }
    var K=d.TeX.x_height*M,A=d.TeX.scriptspace*M*0.75;
var j,w;
if(this.HTMLnotEmpty(this.data[this.sup])){
    j=d.createBox(o);
    m.push(this.data[this.sup].toHTML(j))
    }
    if(this.HTMLnotEmpty(this.data[this.sub])){
    w=d.createBox(o);
    m.push(this.data[this.sub].toHTML(w))
    }
    d.MeasureSpans(m);
    if(j){
    j.bbox.w+=A;
    j.bbox.rw=Math.max(j.bbox.w,j.bbox.rw)
    }
    if(w){
    w.bbox.w+=A;
    w.bbox.rw=Math.max(w.bbox.w,w.bbox.rw)
    }
    d.placeBox(n,0,0);
    var l=(this.data[this.sup]||this.data[this.sub]||this).HTMLgetScale();
    var E=d.TeX.sup_drop*l,C=d.TeX.sub_drop*l;
    var y=n.bbox.h-E,x=n.bbox.d+C,L=0,F;
    if(n.bbox.ic){
    n.bbox.w-=n.bbox.ic;
    L=1.3*n.bbox.ic+0.05
    }
    if(this.data[this.base]&&(this.data[this.base].type==="mi"||this.data[this.base].type==="mo")){
    if(this.data[this.base].data.join("").length===1&&n.bbox.scale===1&&!this.data[this.base].Get("largeop")){
        y=x=0
        }
    }
var I=this.getValues("subscriptshift","superscriptshift");
I.subscriptshift=(I.subscriptshift===""?0:d.length2em(I.subscriptshift,G));
I.superscriptshift=(I.superscriptshift===""?0:d.length2em(I.superscriptshift,G));
if(!j){
    if(w){
        x=Math.max(x,d.TeX.sub1*M,w.bbox.h-(4/5)*K,I.subscriptshift);
        d.placeBox(w,n.bbox.w,-x,w.bbox)
        }
    }else{
    if(!w){
        k=this.getValues("displaystyle","texprimestyle");
        F=d.TeX[(k.displaystyle?"sup1":(k.texprimestyle?"sup3":"sup2"))];
        y=Math.max(y,F*M,j.bbox.d+(1/4)*K,I.superscriptshift);
        d.placeBox(j,n.bbox.w+L,y,j.bbox)
        }else{
        x=Math.max(x,d.TeX.sub2*M);
        var z=d.TeX.rule_thickness*M;
        if((y-j.bbox.d)-(w.bbox.h-x)<3*z){
            x=3*z-y+j.bbox.d+w.bbox.h;
            E=(4/5)*K-(y-j.bbox.d);
            if(E>0){
                y+=E;
                x-=E
                }
            }
        d.placeBox(j,n.bbox.w+L,Math.max(y,I.superscriptshift));
    d.placeBox(w,n.bbox.w,-Math.max(x,I.subscriptshift))
    }
}
this.HTMLhandleSpace(J);
this.HTMLhandleColor(J);
return J
},
HTMLstretchH:f.mbase.HTMLstretchH,
HTMLstretchV:f.mbase.HTMLstretchV
});
f.mmultiscripts.Augment({
    toHTML:f.mbase.HTMLautoload
    });
f.mtable.Augment({
    toHTML:f.mbase.HTMLautoload
    });
f["annotation-xml"].Augment({
    toHTML:f.mbase.HTMLautoload
    });
f.math.Augment({
    toHTML:function(s,k){
        var o=this.Get("alttext");
        if(o&&o!==""){
            k.setAttribute("aria-label",o)
            }
            var l=d.addElement(s,"nobr",{
            isMathJax:true
        });
        s=this.HTMLcreateSpan(l);
        var q=d.createStack(s),m=d.createBox(q),r;
        q.style.fontSize=l.parentNode.style.fontSize;
        l.parentNode.style.fontSize="";
        if(this.data[0]!=null){
            if(d.msieColorBug){
                if(this.background){
                    this.data[0].background=this.background;
                    delete this.background
                    }
                    if(this.mathbackground){
                    this.data[0].mathbackground=this.mathbackground;
                    delete this.mathbackground
                    }
                }
            f.mbase.prototype.displayAlign=b.config.displayAlign;
        f.mbase.prototype.displayIndent=b.config.displayIndent;
        r=d.Measured(this.data[0].toHTML(m),m)
        }
        d.placeBox(m,0,0);
    var j=1/d.em,n=d.em/d.outerEm;
    d.em/=n;
    s.bbox.h*=n;
    s.bbox.d*=n;
    s.bbox.w*=n;
    s.bbox.lw*=n;
    s.bbox.rw*=n;
    if(r&&r.bbox.width!=null){
        q.style.width=r.bbox.width;
        m.style.width="100%"
        }
        this.HTMLhandleColor(s);
    if(r){
        d.createRule(s,(r.bbox.h+j)*n,(r.bbox.d+j)*n,0)
        }
        if(!this.isMultiline&&this.Get("display")==="block"&&s.bbox.width==null){
        var t=this.getValues("indentalignfirst","indentshiftfirst","indentalign","indentshift");
        if(t.indentalignfirst!==f.INDENTALIGN.INDENTALIGN){
            t.indentalign=t.indentalignfirst
            }
            if(t.indentalign===f.INDENTALIGN.AUTO){
            t.indentalign=this.displayAlign
            }
            k.style.textAlign=t.indentalign;
        if(t.indentshiftfirst!==f.INDENTSHIFT.INDENTSHIFT){
            t.indentshift=t.indentshiftfirst
            }
            if(t.indentshift==="auto"){
            t.indentshift=this.displayIndent
            }
            if(t.indentshift&&t.indentalign!==f.INDENTALIGN.CENTER){
            s.style[{
                left:"marginLeft",
                right:"marginRight"
            }
            [t.indentalign]]=d.Em(d.length2em(t.indentshift))
            }
        }
    return s
},
HTMLspanElement:f.mbase.prototype.HTMLspanElement
});
f.TeXAtom.Augment({
    toHTML:function(k){
        k=this.HTMLcreateSpan(k);
        if(this.data[0]!=null){
            if(this.texClass===f.TEXCLASS.VCENTER){
                var j=d.createStack(k);
                var l=d.createBox(j);
                d.Measured(this.data[0].toHTML(l),l);
                d.placeBox(l,0,d.TeX.axis_height-(l.bbox.h+l.bbox.d)/2+l.bbox.d)
                }else{
                k.bbox=this.data[0].toHTML(k).bbox
                }
            }
        this.HTMLhandleSpace(k);
    this.HTMLhandleColor(k);
    return k
    }
});
MathJax.Hub.Register.StartupHook("onLoad",function(){
    setTimeout(MathJax.Callback(["loadComplete",d,"jax.js"]),0)
    })
});
b.Register.StartupHook("End Config",function(){
    b.Browser.Select({
        MSIE:function(j){
            var n=(document.documentMode||0);
            var m=j.versionAtLeast("7.0");
            var l=j.versionAtLeast("8.0")&&n>7;
            var k=(document.compatMode==="BackCompat");
            if(n<9){
                d.config.styles[".MathJax .MathJax_HitBox"]["background-color"]="white";
                d.config.styles[".MathJax .MathJax_HitBox"].opacity=0;
                d.config.styles[".MathJax .MathJax_HitBox"].filter="alpha(opacity=0)"
                }
                d.Augment({
                PaddingWidthBug:true,
                msieAccentBug:true,
                msieColorBug:true,
                msieColorPositionBug:true,
                msieRelativeWidthBug:k,
                msieDisappearingBug:(n>=8),
                msieMarginScaleBug:(n<8),
                msiePaddingWidthBug:true,
                msieBorderWidthBug:k,
                msieInlineBlockAlignBug:(!l||k),
                msiePlaceBoxBug:(l&&!k),
                msieClipRectBug:!l,
                msieNegativeSpaceBug:k,
                msieCloneNodeBug:(l&&j.version==="8.0"),
                initialSkipBug:(n<8),
                msieNegativeBBoxBug:(n>=8),
                msieIE6:!m,
                msieItalicWidthBug:true,
                zeroWidthBug:(n<8),
                FontFaceBug:true,
                msieFontCSSBug:j.isIE9,
                allowWebFonts:(n>=9?"woff":"eot")
                })
            },
        Firefox:function(k){
            var l=false;
            if(k.versionAtLeast("3.5")){
                var j=String(document.location).replace(/[^\/]*$/,"");
                if(document.location.protocol!=="file:"||b.config.root.match(/^https?:\/\//)||(b.config.root+"/").substr(0,j.length)===j){
                    l="otf"
                    }
                }
            d.Augment({
            ffVerticalAlignBug:true,
            AccentBug:true,
            allowWebFonts:l
        })
        },
    Safari:function(o){
        var m=o.versionAtLeast("3.0");
        var l=o.versionAtLeast("3.1");
        var j=navigator.appVersion.match(/ Safari\/\d/)&&navigator.appVersion.match(/ Version\/\d/)&&navigator.vendor.match(/Apple/);
        var k=(navigator.appVersion.match(/ Android (\d+)\.(\d+)/));
        var p=(l&&o.isMobile&&((navigator.platform.match(/iPad|iPod|iPhone/)&&!o.versionAtLeast("5.0"))||(k!=null&&(k[1]<2||(k[1]==2&&k[2]<2)))));
        d.Augment({
            config:{
                styles:{
                    ".MathJax img, .MathJax nobr, .MathJax a":{
                        "max-width":"5000em",
                        "max-height":"5000em"
                    }
                }
            },
        rfuzz:0.011,
        AccentBug:true,
        AdjustSurd:true,
        negativeBBoxes:true,
        safariNegativeSpaceBug:true,
        safariVerticalAlignBug:!l,
        safariTextNodeBug:!m,
        forceReflow:true,
        allowWebFonts:(l&&!p?"otf":false)
        });
    if(j){
        d.Augment({
            webFontDefault:(o.isMobile?"sans-serif":"serif")
            })
        }
        if(o.isPC){
        d.Augment({
            adjustAvailableFonts:d.removeSTIXfonts,
            checkWebFontsTwice:true
        })
        }
        if(p){
        var n=b.config["HTML-CSS"];
        if(n){
            n.availableFonts=[];
            n.preferredFont=null
            }else{
            b.config["HTML-CSS"]={
                availableFonts:[],
                preferredFont:null
            }
        }
    }
},
Chrome:function(j){
    d.Augment({
        rfuzz:0.011,
        AccentBug:true,
        AdjustSurd:true,
        negativeBBoxes:true,
        safariNegativeSpaceBug:true,
        safariWebFontSerif:[""],
        forceReflow:true,
        allowWebFonts:(j.versionAtLeast("4.0")?"otf":"svg")
        })
    },
Opera:function(j){
    j.isMini=(navigator.appVersion.match("Opera Mini")!=null);
    d.config.styles[".MathJax .merror"]["vertical-align"]=null;
    d.config.styles[".MathJax span"]["z-index"]=0;
    d.Augment({
        operaHeightBug:true,
        operaVerticalAlignBug:true,
        operaFontSizeBug:j.versionAtLeast("10.61"),
        initialSkipBug:true,
        zeroWidthBug:true,
        FontFaceBug:true,
        PaddingWidthBug:true,
        allowWebFonts:(j.versionAtLeast("10.0")&&!j.isMini?"otf":false),
        adjustAvailableFonts:d.removeSTIXfonts
        })
    },
Konqueror:function(j){
    d.Augment({
        konquerorVerticalAlignBug:true
    })
    }
})
});
MathJax.Hub.Register.StartupHook("End Cookie",function(){
    if(b.config.menuSettings.zoom!=="None"){
        g.Require("[MathJax]/extensions/MathZoom.js")
        }
    })
})(MathJax.Ajax,MathJax.Hub,MathJax.OutputJax["HTML-CSS"]);

