/*
 *  /MathJax/config/TeX-AMS-MML_HTMLorMML.js
 *  
 *  Copyright (c) 2010-11 Design Science, Inc.
 *
 *  Part of the MathJax library.
 *  See http://www.mathjax.org for details.
 * 
 *  Licensed under the Apache License, Version 2.0;
 *  you may not use this file except in compliance with the License.
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 */

MathJax.Hub.Config({
    delayJaxRegistration: true
});

MathJax.Ajax.Preloading(
    "[MathJax]/jax/input/TeX/config.js",
    "[MathJax]/jax/input/MathML/config.js",
    "[MathJax]/jax/output/HTML-CSS/config.js",
    "[MathJax]/jax/output/NativeMML/config.js",
    "[MathJax]/config/MMLorHTML.js",
    "[MathJax]/extensions/tex2jax.js",
    "[MathJax]/extensions/mml2jax.js",
    "[MathJax]/extensions/MathEvents.js",
    "[MathJax]/extensions/MathZoom.js",
    "[MathJax]/extensions/MathMenu.js",
    "[MathJax]/jax/element/mml/jax.js",
    "[MathJax]/extensions/toMathML.js",
    "[MathJax]/extensions/TeX/noErrors.js",
    "[MathJax]/extensions/TeX/noUndefined.js",
    "[MathJax]/jax/input/TeX/jax.js",
    "[MathJax]/extensions/TeX/AMSmath.js",
    "[MathJax]/extensions/TeX/AMSsymbols.js",
    "[MathJax]/jax/input/MathML/jax.js"
    );

MathJax.Hub.Config({
    "v1.0-compatible":false
});

MathJax.InputJax.TeX=MathJax.InputJax({
    id:"TeX",
    version:"2.0",
    directory:MathJax.InputJax.directory+"/TeX",
    extensionDir:MathJax.InputJax.extensionDir+"/TeX",
    config:{
        TagSide:"right",
        TagIndent:"0.8em",
        MultLineWidth:"85%",
        equationNumbers:{
            autoNumber:"none",
            formatNumber:function(a){
                return a
            },
            formatTag:function(a){
                return"("+a+")"
            },
            formatID:function(a){
                return"mjx-eqn-"+String(a).replace(/[:"'<>&]/g,"")
            },
            formatURL:function(a){
                return"#"+escape(a)
            },
            useLabelIds:true
        }
    }
});
MathJax.InputJax.TeX.Register("math/tex");
MathJax.InputJax.TeX.loadComplete("config.js");

MathJax.InputJax.MathML=MathJax.InputJax({
    id:"MathML",
    version:"2.0",
    directory:MathJax.InputJax.directory+"/MathML",
    extensionDir:MathJax.InputJax.extensionDir+"/MathML",
    entityDir:MathJax.InputJax.directory+"/MathML/entities",
    config:{
        useMathMLspacing:false
    }
});
MathJax.InputJax.MathML.Register("math/mml");
MathJax.InputJax.MathML.loadComplete("config.js");

MathJax.OutputJax["HTML-CSS"]=MathJax.OutputJax({
    id:"HTML-CSS",
    version:"2.0.1",
    directory:MathJax.OutputJax.directory+"/HTML-CSS",
    extensionDir:MathJax.OutputJax.extensionDir+"/HTML-CSS",
    autoloadDir:MathJax.OutputJax.directory+"/HTML-CSS/autoload",
    fontDir:MathJax.OutputJax.directory+"/HTML-CSS/fonts",
    webfontDir:MathJax.OutputJax.fontDir+"/HTML-CSS",
    config:{
        scale:100,
        minScaleAdjust:50,
        availableFonts:["STIX","TeX"],
        preferredFont:"TeX",
        webFont:"TeX",
        imageFont:"TeX",
        undefinedFamily:"STIXGeneral,'Arial Unicode MS',serif",
        mtextFontInherit:false,
        EqnChunk:(MathJax.Hub.Browser.isMobile?10:50),
        EqnChunkFactor:1.5,
        EqnChunkDelay:100,
        linebreaks:{
            automatic:false,
            width:"container"
        },
        styles:{
            ".MathJax_Display":{
                "text-align":"center",
                margin:"1em 0em"
            },
            ".MathJax .merror":{
                "background-color":"#FFFF88",
                color:"#CC0000",
                border:"1px solid #CC0000",
                padding:"1px 3px",
                "font-style":"normal",
                "font-size":"90%"
            },
            "#MathJax_Tooltip":{
                "background-color":"InfoBackground",
                color:"InfoText",
                border:"1px solid black",
                "box-shadow":"2px 2px 5px #AAAAAA",
                "-webkit-box-shadow":"2px 2px 5px #AAAAAA",
                "-moz-box-shadow":"2px 2px 5px #AAAAAA",
                "-khtml-box-shadow":"2px 2px 5px #AAAAAA",
                filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=2, OffY=2, Color='gray', Positive='true')",
                padding:"3px 4px"
            }
        }
    }
});
if(MathJax.Hub.Browser.isMSIE&&document.documentMode>=9){
    delete MathJax.OutputJax["HTML-CSS"].config.styles["#MathJax_Tooltip"].filter
}
if(!MathJax.Hub.config.delayJaxRegistration){
    MathJax.OutputJax["HTML-CSS"].Register("jax/mml")
}
MathJax.Hub.Register.StartupHook("End Config",[function(b,c){
    var a=b.Insert({
        minBrowserVersion:{
            Firefox:3,
            Opera:9.52,
            MSIE:6,
            Chrome:0.3,
            Safari:2,
            Konqueror:4
        },
        inlineMathDelimiters:["$","$"],
        displayMathDelimiters:["$$","$$"],
        multilineDisplay:true,
        minBrowserTranslate:function(f){
            var e=b.getJaxFor(f),k=["[Math]"],j;
            var h=document.createElement("span",{
                className:"MathJax_Preview"
            });
            if(e.inputJax==="TeX"){
                if(e.root.Get("displaystyle")){
                    j=a.displayMathDelimiters;
                    k=[j[0]+e.originalText+j[1]];
                    if(a.multilineDisplay){
                        k=k[0].split(/\n/)
                    }
                }else{
                    j=a.inlineMathDelimiters;
                    k=[j[0]+e.originalText.replace(/^\s+/,"").replace(/\s+$/,"")+j[1]]
                }
            }
            for(var g=0,d=k.length;g<d;g++){
                h.appendChild(document.createTextNode(k[g]));
                if(g<d-1){
                    h.appendChild(document.createElement("br"))
                }
            }
            f.parentNode.insertBefore(h,f)
        }
    },(b.config["HTML-CSS"]||{}));
    if(b.Browser.version!=="0.0"&&!b.Browser.versionAtLeast(a.minBrowserVersion[b.Browser]||0)){
        c.Translate=a.minBrowserTranslate;
        b.Config({
            showProcessingMessages:false
        });
        MathJax.Message.Set("Your browser does not support MathJax",null,4000);
        b.Startup.signal.Post("MathJax not supported")
    }
},MathJax.Hub,MathJax.OutputJax["HTML-CSS"]]);
MathJax.OutputJax["HTML-CSS"].loadComplete("config.js");

MathJax.OutputJax.NativeMML=MathJax.OutputJax({
    id:"NativeMML",
    version:"2.0.1",
    directory:MathJax.OutputJax.directory+"/NativeMML",
    extensionDir:MathJax.OutputJax.extensionDir+"/NativeMML",
    config:{
        scale:100,
        minScaleAdjust:50,
        styles:{
            "DIV.MathJax_MathML":{
                "text-align":"center",
                margin:".75em 0px"
            }
        }
    }
});
if(!MathJax.Hub.config.delayJaxRegistration){
    MathJax.OutputJax.NativeMML.Register("jax/mml")
}
MathJax.OutputJax.NativeMML.loadComplete("config.js");

(function(c,g){
    var f="2.0";
    var a=MathJax.Hub.CombineConfig("MMLorHTML",{
        prefer:{
            MSIE:"MML",
            Firefox:"HTML",
            Opera:"HTML",
            Chrome:"HTML",
            Safari:"HTML",
            other:"HTML"
        }
    });
    var e={
        Firefox:3,
        Opera:9.52,
        MSIE:6,
        Chrome:0.3,
        Safari:2,
        Konqueror:4
    };

    var b=(g.version==="0.0"||g.versionAtLeast(e[g]||0));
    var d=(g.isFirefox&&g.versionAtLeast("1.5"))||(g.isMSIE&&g.hasMathPlayer)||(g.isSafari&&g.versionAtLeast("5.0"))||(g.isOpera&&g.versionAtLeast("9.52"));
    c.Register.StartupHook("End Config",function(){
        var h=(a.prefer&&typeof(a.prefer)==="object"?a.prefer[MathJax.Hub.Browser]||a.prefer.other||"HTML":a.prefer);
        if(b||d){
            if(d&&(h==="MML"||!b)){
                if(MathJax.OutputJax.NativeMML){
                    MathJax.OutputJax.NativeMML.Register("jax/mml")
                }else{
                    c.config.jax.unshift("output/NativeMML")
                }
                c.Startup.signal.Post("NativeMML output selected")
            }else{
                if(MathJax.OutputJax["HTML-CSS"]){
                    MathJax.OutputJax["HTML-CSS"].Register("jax/mml")
                }else{
                    c.config.jax.unshift("output/HTML-CSS")
                }
                c.Startup.signal.Post("HTML-CSS output selected")
            }
        }else{
            c.PreProcess.disabled=true;
            c.prepareScripts.disabled=true;
            MathJax.Message.Set("Your browser does not support MathJax",null,4000);
            c.Startup.signal.Post("MathJax not supported")
        }
    })
})(MathJax.Hub,MathJax.Hub.Browser);
MathJax.Ajax.loadComplete("[MathJax]/config/MMLorHTML.js");

MathJax.Extension.tex2jax={
    version:"2.0",
    config:{
        inlineMath:[["\\(","\\)"]],
        displayMath:[["$$","$$"],["\\[","\\]"]],
        balanceBraces:true,
        skipTags:["script","noscript","style","textarea","pre","code"],
        ignoreClass:"tex2jax_ignore",
        processClass:"tex2jax_process",
        processEscapes:false,
        processEnvironments:true,
        processRefs:true,
        preview:"TeX"
    },
    PreProcess:function(a){
        if(!this.configured){
            this.config=MathJax.Hub.CombineConfig("tex2jax",this.config);
            if(this.config.Augment){
                MathJax.Hub.Insert(this,this.config.Augment)
            }
            if(typeof(this.config.previewTeX)!=="undefined"&&!this.config.previewTeX){
                this.config.preview="none"
            }
            this.configured=true
        }
        if(typeof(a)==="string"){
            a=document.getElementById(a)
        }
        if(!a){
            a=document.body
        }
        if(this.createPatterns()){
            this.scanElement(a,a.nextSibling)
        }
    },
    createPatterns:function(){
        var d=[],e=[],c,a,b=this.config;
        this.match={};
    
        for(c=0,a=b.inlineMath.length;c<a;c++){
            d.push(this.patternQuote(b.inlineMath[c][0]));
            this.match[b.inlineMath[c][0]]={
                mode:"",
                end:b.inlineMath[c][1],
                pattern:this.endPattern(b.inlineMath[c][1])
            }
        }
        for(c=0,a=b.displayMath.length;c<a;c++){
            d.push(this.patternQuote(b.displayMath[c][0]));
            this.match[b.displayMath[c][0]]={
                mode:"; mode=display",
                end:b.displayMath[c][1],
                pattern:this.endPattern(b.displayMath[c][1])
            }
        }
        if(d.length){
            e.push(d.sort(this.sortLength).join("|"))
        }
        if(b.processEnvironments){
            e.push("\\\\begin\\{([^}]*)\\}")
        }
        if(b.processEscapes){
            e.push("\\\\*\\\\\\$")
        }
        if(b.processRefs){
            e.push("\\\\(eq)?ref\\{[^}]*\\}")
        }
        this.start=new RegExp(e.join("|"),"g");
        this.skipTags=new RegExp("^("+b.skipTags.join("|")+")$","i");
        this.ignoreClass=new RegExp("(^| )("+b.ignoreClass+")( |$)");
        this.processClass=new RegExp("(^| )("+b.processClass+")( |$)");
        return(e.length>0)
    },
    patternQuote:function(a){
        return a.replace(/([\^$(){}+*?\-|\[\]\:\\])/g,"\\$1")
    },
    endPattern:function(a){
        return new RegExp(this.patternQuote(a)+"|\\\\.|[{}]","g")
    },
    sortLength:function(d,c){
        if(d.length!==c.length){
            return c.length-d.length
        }
        return(d==c?0:(d<c?-1:1))
    },
    scanElement:function(c,b,g){
        var a,e,d,f;
        while(c&&c!=b){
            if(c.nodeName.toLowerCase()==="#text"){
                if(!g){
                    c=this.scanText(c)
                }
            }else{
                a=(typeof(c.className)==="undefined"?"":c.className);
                e=(typeof(c.tagName)==="undefined"?"":c.tagName);
                if(typeof(a)!=="string"){
                    a=String(a)
                }
                f=this.processClass.exec(a);
                if(c.firstChild&&!a.match(/(^| )MathJax/)&&(f||!this.skipTags.exec(e))){
                    d=(g||this.ignoreClass.exec(a))&&!f;
                    this.scanElement(c.firstChild,b,d)
                }
            }
            if(c){
                c=c.nextSibling
            }
        }
    },
    scanText:function(b){
        if(b.nodeValue.replace(/\s+/,"")==""){
            return b
        }
        var a,c;
        this.search={
            start:true
        };
    
        this.pattern=this.start;
        while(b){
            this.pattern.lastIndex=0;
            while(b&&b.nodeName.toLowerCase()==="#text"&&(a=this.pattern.exec(b.nodeValue))){
                if(this.search.start){
                    b=this.startMatch(a,b)
                }else{
                    b=this.endMatch(a,b)
                }
            }
            if(this.search.matched){
                b=this.encloseMath(b)
            }
            if(b){
                do{
                    c=b;
                    b=b.nextSibling
                }while(b&&(b.nodeName.toLowerCase()==="br"||b.nodeName.toLowerCase()==="#comment"));
                if(!b||b.nodeName!=="#text"){
                    return(this.search.close?this.prevEndMatch():c)
                }
            }
        }
        return b
    },
    startMatch:function(a,b){
        var f=this.match[a[0]];
        if(f!=null){
            this.search={
                end:f.end,
                mode:f.mode,
                pcount:0,
                open:b,
                olen:a[0].length,
                opos:this.pattern.lastIndex-a[0].length
            };
            
            this.switchPattern(f.pattern)
        }else{
            if(a[0].substr(0,6)==="\\begin"){
                this.search={
                    end:"\\end{"+a[1]+"}",
                    mode:"; mode=display",
                    pcount:0,
                    open:b,
                    olen:0,
                    opos:this.pattern.lastIndex-a[0].length,
                    isBeginEnd:true
                };
            
                this.switchPattern(this.endPattern(this.search.end))
            }else{
                if(a[0].substr(0,4)==="\\ref"||a[0].substr(0,6)==="\\eqref"){
                    this.search={
                        mode:"",
                        end:"",
                        open:b,
                        pcount:0,
                        olen:0,
                        opos:this.pattern.lastIndex-a[0].length
                    };
                    
                    return this.endMatch([""],b)
                }else{
                    var d=a[0].substr(0,a[0].length-1),g,c;
                    if(d.length%2===0){
                        c=[d.replace(/\\\\/g,"\\")];
                        g=1
                    }else{
                        c=[d.substr(1).replace(/\\\\/g,"\\"),"$"];
                        g=0
                    }
                    c=MathJax.HTML.Element("span",null,c);
                    var e=MathJax.HTML.TextNode(b.nodeValue.substr(0,a.index));
                    b.nodeValue=b.nodeValue.substr(a.index+a[0].length-g);
                    b.parentNode.insertBefore(c,b);
                    b.parentNode.insertBefore(e,c);
                    this.pattern.lastIndex=g
                }
            }
        }
        return b
    },
    endMatch:function(a,c){
        var b=this.search;
        if(a[0]==b.end){
            if(!b.close||b.pcount===0){
                b.close=c;
                b.cpos=this.pattern.lastIndex;
                b.clen=(b.isBeginEnd?0:a[0].length)
            }
            if(b.pcount===0){
                b.matched=true;
                c=this.encloseMath(c);
                this.switchPattern(this.start)
            }
        }else{
            if(a[0]==="{"){
                b.pcount++
            }else{
                if(a[0]==="}"&&b.pcount){
                    b.pcount--
                }
            }
        }
        return c
    },
    prevEndMatch:function(){
        this.search.matched=true;
        var a=this.encloseMath(this.search.close);
        this.switchPattern(this.start);
        return a
    },
    switchPattern:function(a){
        a.lastIndex=this.pattern.lastIndex;
        this.pattern=a;
        this.search.start=(a===this.start)
    },
    encloseMath:function(b){
        var a=this.search,f=a.close,e,c;
        if(a.cpos===f.length){
            f=f.nextSibling
        }else{
            f=f.splitText(a.cpos)
        }
        if(!f){
            e=f=MathJax.HTML.addText(a.close.parentNode,"")
        }
        a.close=f;
        c=(a.opos?a.open.splitText(a.opos):a.open);
        while(c.nextSibling&&c.nextSibling!==f){
            if(c.nextSibling.nodeValue!==null){
                if(c.nextSibling.nodeName==="#comment"){
                    c.nodeValue+=c.nextSibling.nodeValue.replace(/^\[CDATA\[((.|\n|\r)*)\]\]$/,"$1")
                }else{
                    c.nodeValue+=c.nextSibling.nodeValue
                }
            }else{
                if(this.msieNewlineBug){
                    c.nodeValue+=(c.nextSibling.nodeName.toLowerCase()==="br"?"\n":" ")
                }else{
                    c.nodeValue+=" "
                }
            }
            c.parentNode.removeChild(c.nextSibling)
        }
        var d=c.nodeValue.substr(a.olen,c.nodeValue.length-a.olen-a.clen);
        c.parentNode.removeChild(c);
        if(this.config.preview!=="none"){
            this.createPreview(a.mode,d)
        }
        c=this.createMathTag(a.mode,d);
        this.search={};

        this.pattern.lastIndex=0;
        if(e){
            e.parentNode.removeChild(e)
        }
        return c
    },
    insertNode:function(b){
        var a=this.search;
        a.close.parentNode.insertBefore(b,a.close)
    },
    createPreview:function(c,a){
        var b;
        if(this.config.preview==="TeX"){
            b=[this.filterPreview(a)]
        }else{
            if(this.config.preview instanceof Array){
                b=this.config.preview
            }
        }
        if(b){
            b=MathJax.HTML.Element("span",{
                className:MathJax.Hub.config.preRemoveClass
            },b);
            this.insertNode(b)
        }
    },
    createMathTag:function(c,b){
        var a=document.createElement("script");
        a.type="math/tex"+c;
        MathJax.HTML.setScript(a,b);
        this.insertNode(a);
        return a
    },
    filterPreview:function(a){
        return a
    },
    msieNewlineBug:(MathJax.Hub.Browser.isMSIE&&document.documentMode<9)
};

MathJax.Hub.Register.PreProcessor(["PreProcess",MathJax.Extension.tex2jax]);
MathJax.Ajax.loadComplete("[MathJax]/extensions/tex2jax.js");

MathJax.Extension.mml2jax={
    version:"2.0",
    config:{
        preview:"alttext"
    },
    MMLnamespace:"http://www.w3.org/1998/Math/MathML",
    PreProcess:function(e){
        if(!this.configured){
            this.config=MathJax.Hub.CombineConfig("mml2jax",this.config);
            if(this.config.Augment){
                MathJax.Hub.Insert(this,this.config.Augment)
            }
            this.InitBrowser();
            this.configured=true
        }
        if(typeof(e)==="string"){
            e=document.getElementById(e)
        }
        if(!e){
            e=document.body
        }
        this.ProcessMathArray(e.getElementsByTagName("math"));
        if(e.getElementsByTagNameNS){
            this.ProcessMathArray(e.getElementsByTagNameNS(this.MMLnamespace,"math"))
        }
        var d,b;
        if(document.namespaces){
            for(d=0,b=document.namespaces.length;d<b;d++){
                var f=document.namespaces[d];
                if(f.urn===this.MMLnamespace){
                    this.ProcessMathArray(e.getElementsByTagName(f.name+":math"))
                }
            }
        }else{
            var c=document.getElementsByTagName("html")[0];
            if(c){
                for(d=0,b=c.attributes.length;d<b;d++){
                    var a=c.attributes[d];
                    if(a.nodeName.substr(0,6)==="xmlns:"&&a.nodeValue===this.MMLnamespace){
                        this.ProcessMathArray(e.getElementsByTagName(a.nodeName.substr(6)+":math"))
                    }
                }
            }
        }
    },
    ProcessMathArray:function(b){
        var a;
        if(b.length){
            if(this.MathTagBug){
                for(a=b.length-1;a>=0;a--){
                    if(b[a].nodeName==="MATH"){
                        this.ProcessMathFlattened(b[a])
                    }else{
                        this.ProcessMath(b[a])
                    }
                }
            }else{
                for(a=b.length-1;a>=0;a--){
                    this.ProcessMath(b[a])
                }
            }
        }
    },
    ProcessMath:function(e){
        var d=e.parentNode;
        var a=document.createElement("script");
        a.type="math/mml";
        d.insertBefore(a,e);
        if(this.AttributeBug){
            var b=this.OuterHTML(e);
            if(this.CleanupHTML){
                b=b.replace(/<\?import .*?>/i,"").replace(/<\?xml:namespace .*?\/>/i,"");
                b=b.replace(/&nbsp;/g,"&#xA0;")
            }
            MathJax.HTML.setScript(a,b);
            d.removeChild(e)
        }else{
            var c=MathJax.HTML.Element("span");
            c.appendChild(e);
            MathJax.HTML.setScript(a,c.innerHTML)
        }
        if(this.config.preview!=="none"){
            this.createPreview(e,a)
        }
    },
    ProcessMathFlattened:function(f){
        var d=f.parentNode;
        var b=document.createElement("script");
        b.type="math/mml";
        d.insertBefore(b,f);
        var c="",e,a=f;
        while(f&&f.nodeName!=="/MATH"){
            e=f;
            f=f.nextSibling;
            c+=this.NodeHTML(e);
            e.parentNode.removeChild(e)
        }
        if(f&&f.nodeName==="/MATH"){
            f.parentNode.removeChild(f)
        }
        b.text=c+"</math>";
        if(this.config.preview!=="none"){
            this.createPreview(a,b)
        }
    },
    NodeHTML:function(e){
        var c,b,a;
        if(e.nodeName==="#text"){
            c=this.quoteHTML(e.nodeValue)
        }else{
            if(e.nodeName==="#comment"){
                c="<!--"+e.nodeValue+"-->"
            }else{
                c="<"+e.nodeName.toLowerCase();
                for(b=0,a=e.attributes.length;b<a;b++){
                    var d=e.attributes[b];
                    if(d.specified){
                        c+=" "+d.nodeName.toLowerCase().replace(/xmlns:xmlns/,"xmlns")+"=";
                        var f=d.nodeValue;
                        if(f==null&&d.nodeName==="style"&&e.style){
                            f=e.style.cssText
                        }
                        c+='"'+this.quoteHTML(f)+'"'
                    }
                }
                c+=">";
                if(e.outerHTML!=null&&e.outerHTML.match(/(.<\/[A-Z]+>|\/>)$/)){
                    for(b=0,a=e.childNodes.length;b<a;b++){
                        c+=this.OuterHTML(e.childNodes[b])
                    }
                    c+="</"+e.nodeName.toLowerCase()+">"
                }
            }
        }
        return c
    },
    OuterHTML:function(d){
        if(d.nodeName.charAt(0)==="#"){
            return this.NodeHTML(d)
        }
        if(!this.AttributeBug){
            return d.outerHTML
        }
        var c=this.NodeHTML(d);
        for(var b=0,a=d.childNodes.length;b<a;b++){
            c+=this.OuterHTML(d.childNodes[b])
        }
        c+="</"+d.nodeName.toLowerCase()+">";
        return c
    },
    quoteHTML:function(a){
        if(a==null){
            a=""
        }
        return a.replace(/&/g,"&#x26;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;")
    },
    createPreview:function(b,a){
        var c;
        if(this.config.preview==="alttext"){
            var d=b.getAttribute("alttext");
            if(d!=null){
                c=[this.filterPreview(d)]
            }
        }else{
            if(this.config.preview instanceof Array){
                c=this.config.preview
            }
        }
        if(c){
            c=MathJax.HTML.Element("span",{
                className:MathJax.Hub.config.preRemoveClass
            },c);
            a.parentNode.insertBefore(c,a)
        }
    },
    filterPreview:function(a){
        return a
    },
    InitBrowser:function(){
        var b=MathJax.HTML.Element("span",{
            id:"<",
            className:"mathjax",
            innerHTML:"<math><mi>x</mi><mspace /></math>"
        });
        var a=b.outerHTML||"";
        this.AttributeBug=a!==""&&!(a.match(/id="&lt;"/)&&a.match(/class="mathjax"/)&&a.match(/<\/math>/));
        this.MathTagBug=b.childNodes.length>1;
        this.CleanupHTML=MathJax.Hub.Browser.isMSIE
    }
};

MathJax.Hub.Register.PreProcessor(["PreProcess",MathJax.Extension.mml2jax]);
MathJax.Ajax.loadComplete("[MathJax]/extensions/mml2jax.js");

(function(d,h,l,g,b,j){
    var p="2.0.1";
    var i=MathJax.Extension;
    var c=i.MathEvents={
        version:p
    };
    
    var k=d.config.menuSettings;
    var o={
        hover:500,
        frame:{
            x:3.5,
            y:5,
            bwidth:1,
            bcolor:"#A6D",
            hwidth:"15px",
            hcolor:"#83A"
        },
        button:{
            x:-4,
            y:-3,
            wx:-2,
            src:l.fileURL(b.imageDir+"/MenuArrow-15.png")
        },
        fadeinInc:0.2,
        fadeoutInc:0.05,
        fadeDelay:50,
        fadeoutStart:400,
        fadeoutDelay:15*1000,
        styles:{
            ".MathJax_Hover_Frame":{
                "border-radius":".25em",
                "-webkit-border-radius":".25em",
                "-moz-border-radius":".25em",
                "-khtml-border-radius":".25em",
                "box-shadow":"0px 0px 15px #83A",
                "-webkit-box-shadow":"0px 0px 15px #83A",
                "-moz-box-shadow":"0px 0px 15px #83A",
                "-khtml-box-shadow":"0px 0px 15px #83A",
                border:"1px solid #A6D ! important",
                display:"inline-block",
                position:"absolute"
            },
            ".MathJax_Hover_Arrow":{
                position:"absolute",
                width:"15px",
                height:"11px",
                cursor:"pointer"
            }
        }
    };

    var m=c.Event={
        LEFTBUTTON:0,
        RIGHTBUTTON:2,
        MENUKEY:"altKey",
        Mousedown:function(q){
            return m.Handler(q,"Mousedown",this)
        },
        Mouseup:function(q){
            return m.Handler(q,"Mouseup",this)
        },
        Mousemove:function(q){
            return m.Handler(q,"Mousemove",this)
        },
        Mouseover:function(q){
            return m.Handler(q,"Mouseover",this)
        },
        Mouseout:function(q){
            return m.Handler(q,"Mouseout",this)
        },
        Click:function(q){
            return m.Handler(q,"Click",this)
        },
        DblClick:function(q){
            return m.Handler(q,"DblClick",this)
        },
        Menu:function(q){
            return m.Handler(q,"ContextMenu",this)
        },
        Handler:function(t,r,s){
            if(l.loadingMathMenu){
                return m.False(t)
            }
            var q=b[s.jaxID];
            if(!t){
                t=window.event
            }
            t.isContextMenu=(r==="ContextMenu");
            if(q[r]){
                return q[r](t,s)
            }
            if(i.MathZoom){
                return i.MathZoom.HandleEvent(t,r,s)
            }
        },
        False:function(q){
            if(!q){
                q=window.event
            }
            if(q){
                if(q.preventDefault){
                    q.preventDefault()
                }
                if(q.stopPropagation){
                    q.stopPropagation()
                }
                q.cancelBubble=true;
                q.returnValue=false
            }
            return false
        },
        ContextMenu:function(r,y,u){
            var w=b[y.jaxID],t=w.getJaxFromMath(y);
            var z=(w.config.showMathMenu!=null?w:d).config.showMathMenu;
            if(!z||(k.context!=="MathJax"&&!u)){
                return
            }
            if(c.msieEventBug){
                r=window.event||r
            }
            m.ClearSelection();
            f.ClearHoverTimer();
            if(t.hover){
                if(t.hover.remove){
                    clearTimeout(t.hover.remove);
                    delete t.hover.remove
                }
                t.hover.nofade=true
            }
            var s=MathJax.Menu;
            if(s){
                s.jax=t;
                var q=s.menu.Find("Show Math As").menu;
                q.items[1].name=(j[t.inputJax].sourceMenuTitle||"Original Form");
                q.items[0].hidden=(t.inputJax==="Error");
                var v=s.menu.Find("Math Settings","MathPlayer");
                v.hidden=!(t.outputJax==="NativeMML"&&d.Browser.hasMathPlayer);
                return s.menu.Post(r)
            }else{
                if(!l.loadingMathMenu){
                    l.loadingMathMenu=true;
                    var x={
                        pageX:r.pageX,
                        pageY:r.pageY,
                        clientX:r.clientX,
                        clientY:r.clientY
                    };
                
                    g.Queue(l.Require("[MathJax]/extensions/MathMenu.js"),function(){
                        delete l.loadingMathMenu;
                        if(!MathJax.Menu){
                            MathJax.Menu={}
                        }
                    },["ContextMenu",this,x,y,u])
                }
                return m.False(r)
            }
        },
        AltContextMenu:function(s,r){
            var t=b[r.jaxID];
            var q=(t.config.showMathMenu!=null?t:d).config.showMathMenu;
            if(q){
                q=(t.config.showMathMenuMSIE!=null?t:d).config.showMathMenuMSIE;
                if(k.context==="MathJax"&&!k.mpContext&&q){
                    if(!c.noContextMenuBug||s.button!==m.RIGHTBUTTON){
                        return
                    }
                }else{
                    if(!s[m.MENUKEY]||s.button!==m.LEFTBUTTON){
                        return
                    }
                }
                return t.ContextMenu(s,r,true)
            }
        },
        ClearSelection:function(){
            if(c.safariContextMenuBug){
                setTimeout("window.getSelection().empty()",0)
            }
            if(document.selection){
                setTimeout("document.selection.empty()",0)
            }
        },
        getBBox:function(s){
            s.appendChild(c.topImg);
            var r=c.topImg.offsetTop,t=s.offsetHeight-r,q=s.offsetWidth;
            s.removeChild(c.topImg);
            return{
                w:q,
                h:r,
                d:t
            }
        }
    };

    var f=c.Hover={
        Mouseover:function(s,r){
            if(k.discoverable||k.zoom==="Hover"){
                var u=s.fromElement||s.relatedTarget,t=s.toElement||s.target;
                if(u&&t&&(u.isMathJax!=t.isMathJax||d.getJaxFor(u)!==d.getJaxFor(t))){
                    var q=this.getJaxFromMath(r);
                    if(q.hover){
                        f.ReHover(q)
                    }else{
                        f.HoverTimer(q,r)
                    }
                    return m.False(s)
                }
            }
        },
        Mouseout:function(s,r){
            if(k.discoverable||k.zoom==="Hover"){
                var u=s.fromElement||s.relatedTarget,t=s.toElement||s.target;
                if(u&&t&&(u.isMathJax!=t.isMathJax||d.getJaxFor(u)!==d.getJaxFor(t))){
                    var q=this.getJaxFromMath(r);
                    if(q.hover){
                        f.UnHover(q)
                    }else{
                        f.ClearHoverTimer()
                    }
                    return m.False(s)
                }
            }
        },
        Mousemove:function(s,r){
            if(k.discoverable||k.zoom==="Hover"){
                var q=this.getJaxFromMath(r);
                if(q.hover){
                    return
                }
                if(f.lastX==s.clientX&&f.lastY==s.clientY){
                    return
                }
                f.lastX=s.clientX;
                f.lastY=s.clientY;
                f.HoverTimer(q,r);
                return m.False(s)
            }
        },
        HoverTimer:function(q,r){
            this.ClearHoverTimer();
            this.hoverTimer=setTimeout(g(["Hover",this,q,r]),o.hover)
        },
        ClearHoverTimer:function(){
            if(this.hoverTimer){
                clearTimeout(this.hoverTimer);
                delete this.hoverTimer
            }
        },
        Hover:function(q,u){
            if(i.MathZoom&&i.MathZoom.Hover({},u)){
                return
            }
            var t=b[q.outputJax],v=t.getHoverSpan(q,u),y=t.getHoverBBox(q,v,u),w=(t.config.showMathMenu!=null?t:d).config.showMathMenu;
            var A=o.frame.x,z=o.frame.y,x=o.frame.bwidth;
            if(c.msieBorderWidthBug){
                x=0
            }
            q.hover={
                opacity:0,
                id:q.inputID+"-Hover"
            };
        
            var r=h.Element("span",{
                id:q.hover.id,
                isMathJax:true,
                style:{
                    display:"inline-block",
                    width:0,
                    height:0,
                    position:"relative"
                }
            },[["span",{
                className:"MathJax_Hover_Frame",
                isMathJax:true,
                style:{
                    display:"inline-block",
                    position:"absolute",
                    top:this.Px(-y.h-z-x-(y.y||0)),
                    left:this.Px(-A-x+(y.x||0)),
                    width:this.Px(y.w+2*A),
                    height:this.Px(y.h+y.d+2*z),
                    opacity:0,
                    filter:"alpha(opacity=0)"
                }
            }]]);
            var s=h.Element("span",{
                isMathJax:true,
                id:q.hover.id+"Menu",
                style:{
                    display:"inline-block",
                    "z-index":1,
                    width:0,
                    height:0,
                    position:"relative"
                }
            },[["img",{
                className:"MathJax_Hover_Arrow",
                isMathJax:true,
                math:u,
                src:o.button.src,
                onclick:this.HoverMenu,
                jax:t.id,
                style:{
                    left:this.Px(y.w+A+x+(y.x||0)+o.button.x),
                    top:this.Px(-y.h-z-x-(y.y||0)-o.button.y),
                    opacity:0,
                    filter:"alpha(opacity=0)"
                }
            }]]);
            if(y.width){
                r.style.width=s.style.width=y.width;
                r.style.marginRight=s.style.marginRight="-"+y.width;
                r.firstChild.style.width=y.width;
                s.firstChild.style.left="";
                s.firstChild.style.right=this.Px(o.button.wx)
            }
            v.parentNode.insertBefore(r,v);
            if(w){
                v.parentNode.insertBefore(s,v)
            }
            if(v.style){
                v.style.position="relative"
            }
            this.ReHover(q)
        },
        ReHover:function(q){
            if(q.hover.remove){
                clearTimeout(q.hover.remove)
            }
            q.hover.remove=setTimeout(g(["UnHover",this,q]),o.fadeoutDelay);
            this.HoverFadeTimer(q,o.fadeinInc)
        },
        UnHover:function(q){
            if(!q.hover.nofade){
                this.HoverFadeTimer(q,-o.fadeoutInc,o.fadeoutStart)
            }
        },
        HoverFade:function(q){
            delete q.hover.timer;
            q.hover.opacity=Math.max(0,Math.min(1,q.hover.opacity+q.hover.inc));
            q.hover.opacity=Math.floor(1000*q.hover.opacity)/1000;
            var s=document.getElementById(q.hover.id),r=document.getElementById(q.hover.id+"Menu");
            s.firstChild.style.opacity=q.hover.opacity;
            s.firstChild.style.filter="alpha(opacity="+Math.floor(100*q.hover.opacity)+")";
            if(r){
                r.firstChild.style.opacity=q.hover.opacity;
                r.firstChild.style.filter=s.style.filter
            }
            if(q.hover.opacity===1){
                return
            }
            if(q.hover.opacity>0){
                this.HoverFadeTimer(q,q.hover.inc);
                return
            }
            s.parentNode.removeChild(s);
            if(r){
                r.parentNode.removeChild(r)
            }
            if(q.hover.remove){
                clearTimeout(q.hover.remove)
            }
            delete q.hover
        },
        HoverFadeTimer:function(q,s,r){
            q.hover.inc=s;
            if(!q.hover.timer){
                q.hover.timer=setTimeout(g(["HoverFade",this,q]),(r||o.fadeDelay))
            }
        },
        HoverMenu:function(q){
            if(!q){
                q=window.event
            }
            return b[this.jax].ContextMenu(q,this.math,true)
        },
        ClearHover:function(q){
            if(q.hover.remove){
                clearTimeout(q.hover.remove)
            }
            if(q.hover.timer){
                clearTimeout(q.hover.timer)
            }
            f.ClearHoverTimer();
            delete q.hover
        },
        Px:function(q){
            if(Math.abs(q)<0.006){
                return"0px"
            }
            return q.toFixed(2).replace(/\.?0+$/,"")+"px"
        },
        getImages:function(){
            var q=new Image();
            q.src=o.button.src
        }
    };

    var a=c.Touch={
        last:0,
        delay:500,
        start:function(r){
            var q=new Date().getTime();
            var s=(q-a.last<a.delay&&a.up);
            a.last=q;
            a.up=false;
            if(s){
                a.timeout=setTimeout(a.menu,a.delay,r,this);
                r.preventDefault()
            }
        },
        end:function(r){
            var q=new Date().getTime();
            a.up=(q-a.last<a.delay);
            if(a.timeout){
                clearTimeout(a.timeout);
                delete a.timeout;
                a.last=0;
                a.up=false;
                r.preventDefault();
                return m.Handler((r.touches[0]||r.touch),"DblClick",this)
            }
        },
        menu:function(r,q){
            delete a.timeout;
            a.last=0;
            a.up=false;
            return m.Handler((r.touches[0]||r.touch),"ContextMenu",q)
        }
    };

    if(d.Browser.isMobile){
        var n=o.styles[".MathJax_Hover_Arrow"];
        n.width="25px";
        n.height="18px";
        o.button.x=-6
    }
    d.Browser.Select({
        MSIE:function(q){
            var s=(document.documentMode||0);
            var r=q.versionAtLeast("8.0");
            c.msieBorderWidthBug=(document.compatMode==="BackCompat");
            c.msieEventBug=q.isIE9;
            c.msieAlignBug=(!r||s<8);
            if(s<9){
                m.LEFTBUTTON=1
            }
        },
        Safari:function(q){
            c.safariContextMenuBug=true
        },
        Opera:function(q){
            c.operaPositionBug=true
        },
        Konqueror:function(q){
            c.noContextMenuBug=true
        }
    });
    c.topImg=(c.msieAlignBug?h.Element("img",{
        style:{
            width:0,
            height:0,
            position:"relative"
        },
        src:"about:blank"
    }):h.Element("span",{
        style:{
            width:0,
            height:0,
            display:"inline-block"
        }
    }));
    if(c.operaPositionBug){
        c.topImg.style.border="1px solid"
    }
    c.config=o=d.CombineConfig("MathEvents",o);
    var e=function(){
        var q=o.styles[".MathJax_Hover_Frame"];
        q.border=o.frame.bwidth+"px solid "+o.frame.bcolor+" ! important";
        q["box-shadow"]=q["-webkit-box-shadow"]=q["-moz-box-shadow"]=q["-khtml-box-shadow"]="0px 0px "+o.frame.hwidth+" "+o.frame.hcolor
    };
    
    g.Queue(d.Register.StartupHook("End Config",{}),[e],["getImages",f],["Styles",l,o.styles],["Post",d.Startup.signal,"MathEvents Ready"],["loadComplete",l,"[MathJax]/extensions/MathEvents.js"])
})(MathJax.Hub,MathJax.HTML,MathJax.Ajax,MathJax.Callback,MathJax.OutputJax,MathJax.InputJax);

(function(a,d,f,c,j){
    var k="2.0";
    var i=a.CombineConfig("MathZoom",{
        styles:{
            "#MathJax_Zoom":{
                position:"absolute",
                "background-color":"#F0F0F0",
                overflow:"auto",
                display:"block",
                "z-index":301,
                padding:".5em",
                border:"1px solid black",
                margin:0,
                "font-weight":"normal",
                "font-style":"normal",
                "text-align":"left",
                "text-indent":0,
                "text-transform":"none",
                "line-height":"normal",
                "letter-spacing":"normal",
                "word-spacing":"normal",
                "word-wrap":"normal",
                "white-space":"nowrap",
                "float":"none",
                "box-shadow":"5px 5px 15px #AAAAAA",
                "-webkit-box-shadow":"5px 5px 15px #AAAAAA",
                "-moz-box-shadow":"5px 5px 15px #AAAAAA",
                "-khtml-box-shadow":"5px 5px 15px #AAAAAA",
                filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=2, OffY=2, Color='gray', Positive='true')"
            },
            "#MathJax_ZoomOverlay":{
                position:"absolute",
                left:0,
                top:0,
                "z-index":300,
                display:"inline-block",
                width:"100%",
                height:"100%",
                border:0,
                padding:0,
                margin:0,
                "background-color":"white",
                opacity:0,
                filter:"alpha(opacity=0)"
            },
            "#MathJax_ZoomEventTrap":{
                position:"absolute",
                left:0,
                top:0,
                "z-index":302,
                display:"inline-block",
                border:0,
                padding:0,
                margin:0,
                "background-color":"white",
                opacity:0,
                filter:"alpha(opacity=0)"
            }
        }
    });
    var e,b,g;
    MathJax.Hub.Register.StartupHook("MathEvents Ready",function(){
        g=MathJax.Extension.MathEvents.Event;
        e=MathJax.Extension.MathEvents.Event.False;
        b=MathJax.Extension.MathEvents.Hover
    });
    var h=MathJax.Extension.MathZoom={
        version:k,
        settings:a.config.menuSettings,
        scrollSize:18,
        HandleEvent:function(n,l,m){
            if(h.settings.CTRL&&!n.ctrlKey){
                return true
            }
            if(h.settings.ALT&&!n.altKey){
                return true
            }
            if(h.settings.CMD&&!n.metaKey){
                return true
            }
            if(h.settings.Shift&&!n.shiftKey){
                return true
            }
            if(!h[l]){
                return true
            }
            return h[l](n,m)
        },
        Click:function(m,l){
            if(this.settings.zoom==="Click"){
                return this.Zoom(m,l)
            }
        },
        DblClick:function(m,l){
            if(this.settings.zoom==="Double-Click"){
                return this.Zoom(m,l)
            }
        },
        Hover:function(m,l){
            if(this.settings.zoom==="Hover"){
                this.Zoom(m,l);
                return true
            }
            return false
        },
        Zoom:function(n,s){
            this.Remove();
            b.ClearHoverTimer();
            g.ClearSelection();
            var q=MathJax.OutputJax[s.jaxID];
            var o=q.getJaxFromMath(s);
            if(o.hover){
                b.UnHover(o)
            }
            var l=Math.floor(0.85*document.body.clientWidth),r=Math.floor(0.85*Math.max(document.body.clientHeight,document.documentElement.clientHeight));
            var m=d.Element("span",{
                style:{
                    position:"relative",
                    display:"inline-block",
                    height:0,
                    width:0
                },
                id:"MathJax_ZoomFrame"
            },[["span",{
                id:"MathJax_ZoomOverlay",
                onmousedown:this.Remove
            }],["span",{
                id:"MathJax_Zoom",
                onclick:this.Remove,
                style:{
                    visibility:"hidden",
                    fontSize:this.settings.zscale,
                    "max-width":l+"px",
                    "max-height":r+"px"
                }
            },[["span",{
                style:{
                    display:"inline-block",
                    "white-space":"nowrap"
                }
            }]]]]);
            var x=m.lastChild,u=x.firstChild,p=m.firstChild;
            s.parentNode.insertBefore(m,s);
            if(u.addEventListener){
                u.addEventListener("mousedown",this.Remove,true)
            }
            if(this.msieTrapEventBug){
                var w=d.Element("span",{
                    id:"MathJax_ZoomEventTrap",
                    onmousedown:this.Remove
                });
                m.insertBefore(w,x)
            }
            if(this.msieZIndexBug){
                var t=d.addElement(document.body,"img",{
                    src:"about:blank",
                    id:"MathJax_ZoomTracker",
                    width:0,
                    height:0,
                    style:{
                        width:0,
                        height:0,
                        position:"relative"
                    }
                });
                m.style.position="relative";
                m.style.zIndex=i.styles["#MathJax_ZoomOverlay"]["z-index"];
                m=t
            }
            var v=q.Zoom(o,u,s,l,r);
            if(this.msiePositionBug){
                if(this.msieSizeBug){
                    x.style.height=v.zH+"px";
                    x.style.width=v.zW+"px"
                }
                if(x.offsetHeight>r){
                    x.style.height=r+"px";
                    x.style.width=(v.zW+this.scrollSize)+"px"
                }
                if(x.offsetWidth>l){
                    x.style.width=l+"px";
                    x.style.height=(v.zH+this.scrollSize)+"px"
                }
            }
            if(this.operaPositionBug){
                x.style.width=Math.min(l,v.zW)+"px"
            }
            if(x.offsetWidth<l&&x.offsetHeight<r){
                x.style.overflow="visible"
            }
            this.Position(x,v);
            if(this.msieTrapEventBug){
                w.style.height=x.clientHeight+"px";
                w.style.width=x.clientWidth+"px";
                w.style.left=(parseFloat(x.style.left)+x.clientLeft)+"px";
                w.style.top=(parseFloat(x.style.top)+x.clientTop)+"px"
            }
            x.style.visibility="";
            if(this.settings.zoom==="Hover"){
                p.onmouseover=this.Remove
            }
            if(window.addEventListener){
                addEventListener("resize",this.Resize,false)
            }else{
                if(window.attachEvent){
                    attachEvent("onresize",this.Resize)
                }else{
                    this.onresize=window.onresize;
                    window.onresize=this.Resize
                }
            }
            a.signal.Post(["math zoomed",o]);
            return e(n)
        },
        Position:function(p,r){
            var q=this.Resize(),m=q.x,s=q.y,l=r.mW;
            var o=-Math.floor((p.offsetWidth-l)/2),n=r.Y;
            p.style.left=Math.max(o,10-m)+"px";
            p.style.top=Math.max(n,10-s)+"px";
            if(!h.msiePositionBug){
                h.SetWH()
            }
        },
        Resize:function(n){
            if(h.onresize){
                h.onresize(n)
            }
            var l=0,q=0,o,p=document.getElementById("MathJax_ZoomFrame"),m=document.getElementById("MathJax_ZoomOverlay");
            o=p;
            while(o.offsetParent){
                l+=o.offsetLeft;
                o=o.offsetParent
            }
            if(h.operaPositionBug){
                p.style.border="1px solid"
            }
            o=p;
            while(o.offsetParent){
                q+=o.offsetTop;
                o=o.offsetParent
            }
            if(h.operaPositionBug){
                p.style.border=""
            }
            m.style.left=(-l)+"px";
            m.style.top=(-q)+"px";
            if(h.msiePositionBug){
                setTimeout(h.SetWH,0)
            }else{
                h.SetWH()
            }
            return{
                x:l,
                y:q
            }
        },
        SetWH:function(){
            var l=document.getElementById("MathJax_ZoomOverlay");
            l.style.width=l.style.height="1px";
            var m=document.documentElement||document.body;
            l.style.width=m.scrollWidth+"px";
            l.style.height=Math.max(m.clientHeight,m.scrollHeight)+"px"
        },
        Remove:function(n){
            var p=document.getElementById("MathJax_ZoomFrame");
            if(p){
                var o=MathJax.OutputJax[p.nextSibling.jaxID];
                var l=o.getJaxFromMath(p.nextSibling);
                a.signal.Post(["math unzoomed",l]);
                p.parentNode.removeChild(p);
                p=document.getElementById("MathJax_ZoomTracker");
                if(p){
                    p.parentNode.removeChild(p)
                }
                if(h.operaRefreshBug){
                    var m=d.addElement(document.body,"div",{
                        style:{
                            position:"fixed",
                            left:0,
                            top:0,
                            width:"100%",
                            height:"100%",
                            backgroundColor:"white",
                            opacity:0
                        },
                        id:"MathJax_OperaDiv"
                    });
                    document.body.removeChild(m)
                }
                if(window.removeEventListener){
                    removeEventListener("resize",h.Resize,false)
                }else{
                    if(window.detachEvent){
                        detachEvent("onresize",h.Resize)
                    }else{
                        window.onresize=h.onresize;
                        delete h.onresize
                    }
                }
            }
            return e(n)
        }
    };

    a.Browser.Select({
        MSIE:function(l){
            var n=(document.documentMode||0);
            var m=(n>=9);
            h.msiePositionBug=!m;
            h.msieSizeBug=l.versionAtLeast("7.0")&&(!document.documentMode||n===7||n===8);
            h.msieZIndexBug=(n<=7);
            h.msieInlineBlockAlignBug=(n<=7);
            h.msieTrapEventBug=!window.addEventListener;
            if(document.compatMode==="BackCompat"){
                h.scrollSize=52
            }
            if(m){
                delete i.styles["#MathJax_Zoom"].filter
            }
        },
        Opera:function(l){
            h.operaPositionBug=true;
            h.operaRefreshBug=true
        }
    });
    h.topImg=(h.msieInlineBlockAlignBug?d.Element("img",{
        style:{
            width:0,
            height:0,
            position:"relative"
        },
        src:"about:blank"
    }):d.Element("span",{
        style:{
            width:0,
            height:0,
            display:"inline-block"
        }
    }));
    if(h.operaPositionBug||h.msieTopBug){
        h.topImg.style.border="1px solid"
    }
    MathJax.Callback.Queue(["StartupHook",MathJax.Hub.Register,"Begin Styles",{}],["Styles",f,i.styles],["Post",a.Startup.signal,"MathZoom Ready"],["loadComplete",f,"[MathJax]/extensions/MathZoom.js"])
})(MathJax.Hub,MathJax.HTML,MathJax.Ajax,MathJax.OutputJax["HTML-CSS"],MathJax.OutputJax.NativeMML);

(function(c,g,k,f,b){
    var p="2.0.1";
    var j=MathJax.Callback.Signal("menu");
    MathJax.Extension.MathMenu={
        version:p,
        signal:j
    };
    
    var n=c.Browser.isPC,l=c.Browser.isMSIE,e=((document.documentMode||0)>8);
    var i=(n?null:"5px");
    var o=c.CombineConfig("MathMenu",{
        delay:150,
        helpURL:"http://www.mathjax.org/help-v2/user/",
        closeImg:k.fileURL(b.imageDir+"/CloseX-31.png"),
        showRenderer:true,
        showMathPlayer:true,
        showFontMenu:false,
        showContext:false,
        showDiscoverable:false,
        windowSettings:{
            status:"no",
            toolbar:"no",
            locationbar:"no",
            menubar:"no",
            directories:"no",
            personalbar:"no",
            resizable:"yes",
            scrollbars:"yes",
            width:100,
            height:50
        },
        styles:{
            "#MathJax_About":{
                position:"fixed",
                left:"50%",
                width:"auto",
                "text-align":"center",
                border:"3px outset",
                padding:"1em 2em",
                "background-color":"#DDDDDD",
                color:"black",
                cursor:"default",
                "font-family":"message-box",
                "font-size":"120%",
                "font-style":"normal",
                "text-indent":0,
                "text-transform":"none",
                "line-height":"normal",
                "letter-spacing":"normal",
                "word-spacing":"normal",
                "word-wrap":"normal",
                "white-space":"nowrap",
                "float":"none",
                "z-index":201,
                "border-radius":"15px",
                "-webkit-border-radius":"15px",
                "-moz-border-radius":"15px",
                "-khtml-border-radius":"15px",
                "box-shadow":"0px 10px 20px #808080",
                "-webkit-box-shadow":"0px 10px 20px #808080",
                "-moz-box-shadow":"0px 10px 20px #808080",
                "-khtml-box-shadow":"0px 10px 20px #808080",
                filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=2, OffY=2, Color='gray', Positive='true')"
            },
            ".MathJax_Menu":{
                position:"absolute",
                "background-color":"white",
                color:"black",
                width:"auto",
                padding:(n?"2px":"5px 0px"),
                border:"1px solid #CCCCCC",
                margin:0,
                cursor:"default",
                font:"menu",
                "text-align":"left",
                "text-indent":0,
                "text-transform":"none",
                "line-height":"normal",
                "letter-spacing":"normal",
                "word-spacing":"normal",
                "word-wrap":"normal",
                "white-space":"nowrap",
                "float":"none",
                "z-index":201,
                "border-radius":i,
                "-webkit-border-radius":i,
                "-moz-border-radius":i,
                "-khtml-border-radius":i,
                "box-shadow":"0px 10px 20px #808080",
                "-webkit-box-shadow":"0px 10px 20px #808080",
                "-moz-box-shadow":"0px 10px 20px #808080",
                "-khtml-box-shadow":"0px 10px 20px #808080",
                filter:"progid:DXImageTransform.Microsoft.dropshadow(OffX=2, OffY=2, Color='gray', Positive='true')"
            },
            ".MathJax_MenuItem":{
                padding:(n?"2px 2em":"1px 2em"),
                background:"transparent"
            },
            ".MathJax_MenuTitle":{
                "background-color":"#CCCCCC",
                margin:(n?"-1px -1px 1px -1px":"-5px 0 0 0"),
                "text-align":"center",
                "font-style":"italic",
                "font-size":"80%",
                color:"#444444",
                padding:"2px 0",
                overflow:"hidden"
            },
            ".MathJax_MenuArrow":{
                position:"absolute",
                right:".5em",
                color:"#666666",
                "font-family":(l?"'Arial unicode MS'":null)
            },
            ".MathJax_MenuActive .MathJax_MenuArrow":{
                color:"white"
            },
            ".MathJax_MenuCheck":{
                position:"absolute",
                left:".7em",
                "font-family":(l?"'Arial unicode MS'":null)
            },
            ".MathJax_MenuRadioCheck":{
                position:"absolute",
                left:(n?"1em":".7em")
            },
            ".MathJax_MenuLabel":{
                padding:(n?"2px 2em 4px 1.33em":"1px 2em 3px 1.33em"),
                "font-style":"italic"
            },
            ".MathJax_MenuRule":{
                "border-top":(n?"1px solid #CCCCCC":"1px solid #DDDDDD"),
                margin:(n?"4px 1px 0px":"4px 3px")
            },
            ".MathJax_MenuDisabled":{
                color:"GrayText"
            },
            ".MathJax_MenuActive":{
                "background-color":(n?"Highlight":"#606872"),
                color:(n?"HighlightText":"white")
            },
            ".MathJax_Menu_Close":{
                position:"absolute",
                width:"31px",
                height:"31px",
                top:"-15px",
                left:"-15px"
            }
        }
    });
    var h,d;
    c.Register.StartupHook("MathEvents Ready",function(){
        h=MathJax.Extension.MathEvents.Event.False;
        d=MathJax.Extension.MathEvents.Hover
    });
    var a=MathJax.Menu=MathJax.Object.Subclass({
        version:p,
        items:[],
        posted:false,
        title:null,
        margin:5,
        Init:function(q){
            this.items=[].slice.call(arguments,0)
        },
        With:function(q){
            if(q){
                c.Insert(this,q)
            }
            return this
        },
        Post:function(r,B){
            if(!r){
                r=window.event
            }
            var z=(!this.title?null:[["div",{
                className:"MathJax_MenuTitle"
            },[this.title]]]);
            var q=document.getElementById("MathJax_MenuFrame");
            if(!q){
                q=a.Background(this);
                delete m.lastItem;
                delete m.lastMenu;
                delete a.skipUp;
                j.Post(["post",a.jax])
            }
            var s=g.addElement(q,"div",{
                onmouseup:a.Mouseup,
                ondblclick:h,
                ondragstart:h,
                onselectstart:h,
                oncontextmenu:h,
                menuItem:this,
                className:"MathJax_Menu"
            },z);
            for(var u=0,t=this.items.length;u<t;u++){
                this.items[u].Create(s)
            }
            if(a.isMobile){
                g.addElement(s,"span",{
                    className:"MathJax_Menu_Close",
                    menu:B,
                    ontouchstart:a.Close,
                    ontouchend:h,
                    onmousedown:a.Close,
                    onmouseup:h
                },[["img",{
                    src:o.closeImg,
                    style:{
                        width:"100%",
                        height:"100%"
                    }
                }]])
            }
            this.posted=true;
            s.style.width=(s.offsetWidth+2)+"px";
            var A=r.pageX,w=r.pageY;
            if(!A&&!w){
                A=r.clientX+document.body.scrollLeft+document.documentElement.scrollLeft;
                w=r.clientY+document.body.scrollTop+document.documentElement.scrollTop
            }
            if(!B){
                if(A+s.offsetWidth>document.body.offsetWidth-this.margin){
                    A=document.body.offsetWidth-s.offsetWidth-this.margin
                }
                if(a.isMobile){
                    A=Math.max(5,A-Math.floor(s.offsetWidth/2));
                    w-=20
                }
                a.skipUp=r.isContextMenu
            }else{
                var v="left",C=B.offsetWidth;
                A=(a.isMobile?30:C-2);
                w=0;
                while(B&&B!==q){
                    A+=B.offsetLeft;
                    w+=B.offsetTop;
                    B=B.parentNode
                }
                if(A+s.offsetWidth>document.body.offsetWidth-this.margin&&!a.isMobile){
                    v="right";
                    A=Math.max(this.margin,A-C-s.offsetWidth+6)
                }
                if(!n){
                    s.style["borderRadiusTop"+v]=0;
                    s.style["WebkitBorderRadiusTop"+v]=0;
                    s.style["MozBorderRadiusTop"+v]=0;
                    s.style["KhtmlBorderRadiusTop"+v]=0
                }
            }
            s.style.left=A+"px";
            s.style.top=w+"px";
            if(document.selection&&document.selection.empty){
                document.selection.empty()
            }
            return h(r)
        },
        Remove:function(q,r){
            j.Post(["unpost",a.jax]);
            var s=document.getElementById("MathJax_MenuFrame");
            if(s){
                s.parentNode.removeChild(s);
                if(this.msieFixedPositionBug){
                    detachEvent("onresize",a.Resize)
                }
            }
            if(a.jax.hover){
                delete a.jax.hover.nofade;
                d.UnHover(a.jax)
            }
            return h(q)
        },
        Find:function(r){
            var t=[].slice.call(arguments,1);
            for(var s=0,q=this.items.length;s<q;s++){
                if(this.items[s].name===r){
                    if(t.length){
                        if(!this.items[s].menu){
                            return null
                        }
                        return this.items[s].menu.Find.apply(this.items[s].menu,t)
                    }
                    return this.items[s]
                }
            }
            return null
        },
        IndexOf:function(r){
            for(var s=0,q=this.items.length;s<q;s++){
                if(this.items[s].name===r){
                    return s
                }
            }
            return null
        }
    },{
        config:o,
        div:null,
        Close:function(q){
            return a.Event(q,this.menu||this.parentNode,(this.menu?"Touchend":"Remove"))
        },
        Remove:function(q){
            return a.Event(q,this,"Remove")
        },
        Mouseover:function(q){
            return a.Event(q,this,"Mouseover")
        },
        Mouseout:function(q){
            return a.Event(q,this,"Mouseout")
        },
        Mousedown:function(q){
            return a.Event(q,this,"Mousedown")
        },
        Mouseup:function(q){
            return a.Event(q,this,"Mouseup")
        },
        Touchstart:function(q){
            return a.Event(q,this,"Touchstart")
        },
        Touchend:function(q){
            return a.Event(q,this,"Touchend")
        },
        Event:function(s,u,q,t){
            if(a.skipMouseover&&q==="Mouseover"&&!t){
                return h(s)
            }
            if(a.skipUp){
                if(q.match(/Mouseup|Touchend/)){
                    delete a.skipUp;
                    return h(s)
                }
                if(q==="Touchstart"||(q==="Mousedown"&&!a.skipMousedown)){
                    delete a.skipUp
                }
            }
            if(!s){
                s=window.event
            }
            var r=u.menuItem;
            if(r&&r[q]){
                return r[q](s,u)
            }
            return null
        },
        BGSTYLE:{
            position:"absolute",
            left:0,
            top:0,
            "z-index":200,
            width:"100%",
            height:"100%",
            border:0,
            padding:0,
            margin:0
        },
        Background:function(r){
            var s=g.addElement(document.body,"div",{
                style:this.BGSTYLE,
                id:"MathJax_MenuFrame"
            },[["div",{
                style:this.BGSTYLE,
                menuItem:r,
                onmousedown:this.Remove
            }]]);
            var q=s.firstChild;
            if(r.msieBackgroundBug){
                q.style.backgroundColor="white";
                q.style.filter="alpha(opacity=0)"
            }
            if(r.msieFixedPositionBug){
                s.width=s.height=0;
                this.Resize();
                attachEvent("onresize",this.Resize)
            }else{
                q.style.position="fixed"
            }
            return s
        },
        Resize:function(){
            setTimeout(a.SetWH,0)
        },
        SetWH:function(){
            var q=document.getElementById("MathJax_MenuFrame");
            if(q){
                q=q.firstChild;
                q.style.width=q.style.height="1px";
                q.style.width=document.body.scrollWidth+"px";
                q.style.height=document.body.scrollHeight+"px"
            }
        },
        saveCookie:function(){
            g.Cookie.Set("menu",this.cookie)
        },
        getCookie:function(){
            this.cookie=g.Cookie.Get("menu")
        },
        getImages:function(){
            if(a.isMobile){
                var q=new Image();
                q.src=o.closeImg
            }
        }
    });
    var m=a.ITEM=MathJax.Object.Subclass({
        name:"",
        Create:function(r){
            if(!this.hidden){
                var q={
                    onmouseover:a.Mouseover,
                    onmouseout:a.Mouseout,
                    onmouseup:a.Mouseup,
                    onmousedown:a.Mousedown,
                    ondragstart:h,
                    onselectstart:h,
                    onselectend:h,
                    ontouchstart:a.Touchstart,
                    ontouchend:a.Touchend,
                    className:"MathJax_MenuItem",
                    menuItem:this
                };
            
                if(this.disabled){
                    q.className+=" MathJax_MenuDisabled"
                }
                g.addElement(r,"div",q,this.Label(q,r))
            }
        },
        Mouseover:function(u,w){
            if(!this.disabled){
                this.Activate(w)
            }
            if(!this.menu||!this.menu.posted){
                var v=document.getElementById("MathJax_MenuFrame").childNodes,r=w.parentNode.childNodes;
                for(var s=0,q=r.length;s<q;s++){
                    var t=r[s].menuItem;
                    if(t&&t.menu&&t.menu.posted){
                        t.Deactivate(r[s])
                    }
                }
                q=v.length-1;
                while(q>=0&&w.parentNode.menuItem!==v[q].menuItem){
                    v[q].menuItem.posted=false;
                    v[q].parentNode.removeChild(v[q]);
                    q--
                }
                if(this.Timer&&!a.isMobile){
                    this.Timer(u,w)
                }
            }
        },
        Mouseout:function(q,r){
            if(!this.menu||!this.menu.posted){
                this.Deactivate(r)
            }
            if(this.timer){
                clearTimeout(this.timer);
                delete this.timer
            }
        },
        Mouseup:function(q,r){
            return this.Remove(q,r)
        },
        Touchstart:function(q,r){
            return this.TouchEvent(q,r,"Mousedown")
        },
        Touchend:function(q,r){
            return this.TouchEvent(q,r,"Mouseup")
        },
        TouchEvent:function(r,s,q){
            if(this!==m.lastItem){
                if(m.lastMenu){
                    a.Event(r,m.lastMenu,"Mouseout")
                }
                a.Event(r,s,"Mouseover",true);
                m.lastItem=this;
                m.lastMenu=s
            }
            if(this.nativeTouch){
                return null
            }
            a.Event(r,s,q);
            return false
        },
        Remove:function(q,r){
            r=r.parentNode.menuItem;
            return r.Remove(q,r)
        },
        Activate:function(q){
            this.Deactivate(q);
            q.className+=" MathJax_MenuActive"
        },
        Deactivate:function(q){
            q.className=q.className.replace(/ MathJax_MenuActive/,"")
        },
        With:function(q){
            if(q){
                c.Insert(this,q)
            }
            return this
        }
    });
    a.ITEM.COMMAND=a.ITEM.Subclass({
        action:function(){},
        Init:function(q,s,r){
            this.name=q;
            this.action=s;
            this.With(r)
        },
        Label:function(q,r){
            return[this.name]
        },
        Mouseup:function(q,r){
            if(!this.disabled){
                this.Remove(q,r);
                j.Post(["command",this]);
                this.action.call(this,q)
            }
            return h(q)
        }
    });
    a.ITEM.SUBMENU=a.ITEM.Subclass({
        menu:null,
        marker:(n&&!c.Browser.isSafari?"\u25B6":"\u25B8"),
        Init:function(q,s){
            this.name=q;
            var r=1;
            if(!(s instanceof a.ITEM)){
                this.With(s),r++
            }
            this.menu=a.apply(a,[].slice.call(arguments,r))
        },
        Label:function(q,r){
            this.menu.posted=false;
            return[this.name+" ",["span",{
                className:"MathJax_MenuArrow"
            },[this.marker]]]
        },
        Timer:function(q,r){
            if(this.timer){
                clearTimeout(this.timer)
            }
            q={
                clientX:q.clientX,
                clientY:q.clientY
            };
            
            this.timer=setTimeout(f(["Mouseup",this,q,r]),o.delay)
        },
        Touchend:function(r,t){
            var s=this.menu.posted;
            var q=this.SUPER(arguments).Touchend.apply(this,arguments);
            if(s){
                this.Deactivate(t);
                delete m.lastItem;
                delete m.lastMenu
            }
            return q
        },
        Mouseup:function(r,t){
            if(!this.disabled){
                if(!this.menu.posted){
                    if(this.timer){
                        clearTimeout(this.timer);
                        delete this.timer
                    }
                    this.menu.Post(r,t)
                }else{
                    var s=document.getElementById("MathJax_MenuFrame").childNodes,q=s.length-1;
                    while(q>=0){
                        var u=s[q];
                        u.menuItem.posted=false;
                        u.parentNode.removeChild(u);
                        if(u.menuItem===this.menu){
                            break
                        }
                        q--
                    }
                }
            }
            return h(r)
        }
    });
    a.ITEM.RADIO=a.ITEM.Subclass({
        variable:null,
        marker:(n?"\u25CF":"\u2713"),
        Init:function(r,q,s){
            this.name=r;
            this.variable=q;
            this.With(s);
            if(this.value==null){
                this.value=this.name
            }
        },
        Label:function(r,s){
            var q={
                className:"MathJax_MenuRadioCheck"
            };
    
            if(o.settings[this.variable]!==this.value){
                q={
                    style:{
                        display:"none"
                    }
                }
            }
            return[["span",q,[this.marker]]," "+this.name]
        },
        Mouseup:function(t,u){
            if(!this.disabled){
                var v=u.parentNode.childNodes;
                for(var r=0,q=v.length;r<q;r++){
                    var s=v[r].menuItem;
                    if(s&&s.variable===this.variable){
                        v[r].firstChild.style.display="none"
                    }
                }
                u.firstChild.display="";
                o.settings[this.variable]=this.value;
                a.cookie[this.variable]=o.settings[this.variable];
                a.saveCookie();
                j.Post(["radio button",this])
            }
            this.Remove(t,u);
            if(this.action&&!this.disabled){
                this.action.call(a,this)
            }
            return h(t)
        }
    });
    a.ITEM.CHECKBOX=a.ITEM.Subclass({
        variable:null,
        marker:"\u2713",
        Init:function(r,q,s){
            this.name=r;
            this.variable=q;
            this.With(s)
        },
        Label:function(r,s){
            var q={
                className:"MathJax_MenuCheck"
            };
        
            if(!o.settings[this.variable]){
                q={
                    style:{
                        display:"none"
                    }
                }
            }
            return[["span",q,[this.marker]]," "+this.name]
        },
        Mouseup:function(q,r){
            if(!this.disabled){
                r.firstChild.display=(o.settings[this.variable]?"none":"");
                o.settings[this.variable]=!o.settings[this.variable];
                a.cookie[this.variable]=o.settings[this.variable];
                a.saveCookie();
                j.Post(["checkbox",this])
            }
            this.Remove(q,r);
            if(this.action&&!this.disabled){
                this.action.call(a,this)
            }
            return h(q)
        }
    });
    a.ITEM.LABEL=a.ITEM.Subclass({
        Init:function(q,r){
            this.name=q;
            this.With(r)
        },
        Label:function(q,r){
            delete q.onmouseover,delete q.onmouseout;
            delete q.onmousedown;
            q.className+=" MathJax_MenuLabel";
            return[this.name]
        }
    });
    a.ITEM.RULE=a.ITEM.Subclass({
        Label:function(q,r){
            delete q.onmouseover,delete q.onmouseout;
            delete q.onmousedown;
            q.className+=" MathJax_MenuRule";
            return null
        }
    });
    a.About=function(){
        var t=b["HTML-CSS"]||{
            fontInUse:""
        };
    
        var v=(t.webFonts?"":"local "),r=(t.webFonts?" web":"");
        var s=(t.imgFonts?"Image":v+t.fontInUse+r)+" fonts";
        if(s==="local  fonts"&&b.SVG){
            s="web SVG fonts"
        }
        var q=["MathJax.js v"+MathJax.fileversion,["br"]];
        q.push(["div",{
            style:{
                "border-top":"groove 2px",
                margin:".25em 0"
            }
        }]);
        a.About.GetJax(q,MathJax.InputJax,"Input Jax");
        a.About.GetJax(q,MathJax.OutputJax,"Output Jax");
        a.About.GetJax(q,MathJax.ElementJax,"Element Jax");
        q.push(["div",{
            style:{
                "border-top":"groove 2px",
                margin:".25em 0"
            }
        }]);
        a.About.GetJax(q,MathJax.Extension,"Extension",true);
        q.push(["div",{
            style:{
                "border-top":"groove 2px",
                margin:".25em 0"
            }
        }],["center",{},[c.Browser+" v"+c.Browser.version+(t.webFonts&&!t.imgFonts?" \u2014 "+t.allowWebFonts+" fonts":"")]]);
        a.About.div=a.Background(a.About);
        var w=g.addElement(a.About.div,"div",{
            id:"MathJax_About",
            onclick:a.About.Remove
        },[["b",{
            style:{
                fontSize:"120%"
            }
        },["MathJax"]]," v"+MathJax.version,["br"],"using "+s,["br"],["br"],["span",{
            style:{
                display:"inline-block",
                "text-align":"left",
                "font-size":"80%",
                "max-height":"20em",
                overflow:"auto",
                "background-color":"#E4E4E4",
                padding:".4em .6em",
                border:"1px inset"
            }
        },q],["br"],["br"],["a",{
            href:"http://www.mathjax.org/"
        },["www.mathjax.org"]]]);
        var x=(document.documentElement||{});
        var u=window.innerHeight||x.clientHeight||x.scrollHeight||0;
        if(a.prototype.msieAboutBug){
            w.style.width="20em";
            w.style.position="absolute";
            w.style.left=Math.floor((document.documentElement.scrollWidth-w.offsetWidth)/2)+"px";
            w.style.top=(Math.floor((u-w.offsetHeight)/3)+document.body.scrollTop)+"px"
        }else{
            w.style.marginLeft=Math.floor(-w.offsetWidth/2)+"px";
            w.style.top=Math.floor((u-w.offsetHeight)/3)+"px"
        }
    };

    a.About.Remove=function(q){
        if(a.About.div){
            document.body.removeChild(a.About.div);
            delete a.About.div
        }
    };

    a.About.GetJax=function(r,w,u,t){
        var v=[];
        for(var x in w){
            if(w.hasOwnProperty(x)&&w[x]){
                if((t&&w[x].version)||(w[x].isa&&w[x].isa(w))){
                    v.push((w[x].id||x)+" "+u+" v"+w[x].version)
                }
            }
        }
        v.sort();
        for(var s=0,q=v.length;s<q;s++){
            r.push(v[s],["br"])
        }
        return r
    };

    a.Help=function(){
        window.open(o.helpURL,"MathJaxHelp")
    };
    
    a.ShowSource=function(t){
        if(!t){
            t=window.event
        }
        var s={
            screenX:t.screenX,
            screenY:t.screenY
        };
        
        if(!a.jax){
            return
        }
        if(this.format==="MathML"){
            var q=MathJax.ElementJax.mml;
            if(q&&typeof(q.mbase.prototype.toMathML)!=="undefined"){
                try{
                    a.ShowSource.Text(a.jax.root.toMathML(),t)
                }catch(r){
                    if(!r.restart){
                        throw r
                    }
                    f.After([this,a.ShowSource,s])
                }
            }else{
                if(!k.loadingToMathML){
                    k.loadingToMathML=true;
                    a.ShowSource.Window(t);
                    f.Queue(k.Require("[MathJax]/extensions/toMathML.js"),function(){
                        delete k.loadingToMathML;
                        if(!q.mbase.prototype.toMathML){
                            q.mbase.prototype.toMathML=function(){}
                        }
                    },[this,a.ShowSource,s]);
                    return
                }
            }
        }else{
            if(a.jax.originalText==null){
                alert("No original form available");
                return
            }
            a.ShowSource.Text(a.jax.originalText,t)
        }
    };

    a.ShowSource.Window=function(r){
        if(!a.ShowSource.w){
            var s=[],q=o.windowSettings;
            for(var t in q){
                if(q.hasOwnProperty(t)){
                    s.push(t+"="+q[t])
                }
            }
            a.ShowSource.w=window.open("","_blank",s.join(","))
        }
        return a.ShowSource.w
    };

    a.ShowSource.Text=function(z,v){
        var s=a.ShowSource.Window(v);
        z=z.replace(/^\s*/,"").replace(/\s*$/,"");
        z=z.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
        if(a.isMobile){
            s.document.open();
            s.document.write("<html><head><meta name='viewport' content='width=device-width, initial-scale=1.0' /><title>MathJax Equation Source</title></head><body style='font-size:85%'>");
            s.document.write("<pre>"+z+"</pre>");
            s.document.write("<hr><input type='button' value='Close' onclick='window.close()' />");
            s.document.write("</body></html>");
            s.document.close()
        }else{
            s.document.open();
            s.document.write("<html><head><title>MathJax Equation Source</title></head><body style='font-size:85%'>");
            s.document.write("<table><tr><td><pre>"+z+"</pre></td></tr></table>");
            s.document.write("</body></html>");
            s.document.close();
            var u=s.document.body.firstChild;
            var t=(s.outerHeight-s.innerHeight)||30,r=(s.outerWidth-s.innerWidth)||30;
            r=Math.min(Math.floor(0.5*screen.width),u.offsetWidth+r+25);
            t=Math.min(Math.floor(0.5*screen.height),u.offsetHeight+t+25);
            s.resizeTo(r,t);
            if(v&&v.screenX!=null){
                var q=Math.max(0,Math.min(v.screenX-Math.floor(r/2),screen.width-r-20)),A=Math.max(0,Math.min(v.screenY-Math.floor(t/2),screen.height-t-20));
                s.moveTo(q,A)
            }
        }
        delete a.ShowSource.w
    };

    a.Scale=function(){
        var r=b["HTML-CSS"],q=b.NativeMML;
        var t=(r?r.config.scale:q.config.scale);
        var s=prompt("Scale all mathematics (compared to surrounding text) by",t+"%");
        if(s){
            if(s.match(/^\s*\d+\s*%?\s*$/)){
                s=parseInt(s);
                if(s){
                    if(s!==t){
                        if(r){
                            r.config.scale=s
                        }
                        if(q){
                            q.config.scale=s
                        }
                        a.cookie.scale=s;
                        a.saveCookie();
                        c.Reprocess()
                    }
                }else{
                    alert("The scale should not be zero")
                }
            }else{
                alert("The scale should be a perentage (e.g., 120%)")
            }
        }
    };

    a.Zoom=function(){
        if(!MathJax.Extension.MathZoom){
            k.Require("[MathJax]/extensions/MathZoom.js")
        }
    };

    a.Renderer=function(){
        var r=c.outputJax["jax/mml"];
        if(r[0]!==o.settings.renderer){
            var u=c.Browser,t,q=a.Renderer.Messages,s;
            switch(o.settings.renderer){
                case"NativeMML":
                    if(!o.settings.warnedMML){
                        if(u.isChrome||(u.isSafari&&!u.versionAtLeast("5.0"))){
                            t=q.MML.WebKit
                        }else{
                            if(u.isMSIE){
                                if(!u.hasMathPlayer){
                                    t=q.MML.MSIE
                                }
                            }else{
                                t=q.MML[u]
                            }
                        }
                        s="warnedMML"
                    }
                    break;
                case"SVG":
                    if(!o.settings.warnedSVG){
                        if(u.isMSIE&&!e){
                            t=q.SVG.MSIE
                        }
                    }
                    break
            }
            if(t){
                t+="\n\nSwitch the renderer anyway?\n\n(Press OK to switch, CANCEL to continue with the current renderer)";
                a.cookie.renderer=r[0].id;
                a.saveCookie();
                if(!confirm(t)){
                    return
                }
                if(s){
                    a.cookie[s]=o.settings[s]=true
                }
                a.cookie.renderer=o.settings.renderer;
                a.saveCookie()
            }
            c.Queue(["setRenderer",c,o.settings.renderer,"jax/mml"],["Rerender",c])
        }
    };

    a.Renderer.Messages={
        MML:{
            WebKit:"Your browser doesn't seem to support MathML natively, so switching to MathML output may cause the mathematics on the page to become unreadable.",
            MSIE:"Internet Explorer requires the MathPlayer plugin in order to process MathML output.",
            Opera:"Opera's support for MathML is limited, so switching to MathML output may cause some expressions to render poorly.",
            Firefox:"Your browser's native MathML does not implement all the features used by MathJax, so some expressions may render improperly."
        },
        SVG:{
            MSIE:"SVG is not implemented in Internet Explorer prior to IE9, or when the browser is emulating IE8 or below. Switching to SVG output will cause the mathemtics to not display properly."
        }
    };

    a.Font=function(){
        var q=b["HTML-CSS"];
        if(!q){
            return
        }
        document.location.reload()
    };
    
    a.MPEvents=function(s){
        var r=o.settings.discoverable,q=a.MPEvents.Messages;
        if(!e){
            if(o.settings.mpMouse&&!confirm(q.IE8warning)){
                delete a.cookie.mpContext;
                delete o.settings.mpContext;
                delete a.cookie.mpMouse;
                delete o.settings.mpMouse;
                a.saveCookie();
                return
            }
            o.settings.mpContext=o.settings.mpMouse;
            a.cookie.mpContext=a.cookie.mpMouse=o.settings.mpMouse;
            a.saveCookie();
            MathJax.Hub.Queue(["Rerender",MathJax.Hub])
        }else{
            if(!r&&s.name==="Menu Events"&&o.settings.mpContext){
                alert(q.IE9warning)
            }
        }
    };

    a.MPEvents.Messages={
        IE8warning:"This will disable the MathJax menu and zoom features, but you can Alt-Click on an expression to obtain the MathJax menu instead.\n\nReally change the MathPlayer settings?",
        IE9warning:"The MathJax contextual menu will be disabled, but you can Alt-Click on an expression to obtain the MathJax menu instead."
    };

    c.Browser.Select({
        MSIE:function(q){
            var r=(document.compatMode==="BackCompat");
            var s=q.versionAtLeast("8.0")&&document.documentMode>7;
            a.Augment({
                margin:20,
                msieBackgroundBug:true,
                msieFixedPositionBug:(r||!s),
                msieAboutBug:r
            });
            if(e){
                delete o.styles["#MathJax_About"].filter;
                delete o.styles[".MathJax_Menu"].filter
            }
        },
        Firefox:function(q){
            a.skipMouseover=q.isMobile&&q.versionAtLeast("6.0");
            a.skipMousedown=q.isMobile
        }
    });
    a.isMobile=c.Browser.isMobile;
    a.noContextMenu=c.Browser.noContextMenu;
    c.Register.StartupHook("End Config",function(){
        o.settings=c.config.menuSettings;
        if(typeof(o.settings.showRenderer)!=="undefined"){
            o.showRenderer=o.settings.showRenderer
        }
        if(typeof(o.settings.showFontMenu)!=="undefined"){
            o.showFontMenu=o.settings.showFontMenu
        }
        if(typeof(o.settings.showContext)!=="undefined"){
            o.showContext=o.settings.showContext
        }
        a.getCookie();
        a.menu=a(m.SUBMENU("Show Math As",m.COMMAND("MathML Code",a.ShowSource,{
            nativeTouch:true,
            format:"MathML"
        }),m.COMMAND("Original Form",a.ShowSource,{
            nativeTouch:true
        }),m.RULE(),m.CHECKBOX("Show TeX hints in MathML","texHints")),m.RULE(),m.SUBMENU("Math Settings",m.SUBMENU("Zoom Trigger",m.RADIO("Hover","zoom",{
            action:a.Zoom
        }),m.RADIO("Click","zoom",{
            action:a.Zoom
        }),m.RADIO("Double-Click","zoom",{
            action:a.Zoom
        }),m.RADIO("No Zoom","zoom",{
            value:"None"
        }),m.RULE(),m.LABEL("Trigger Requires:"),m.CHECKBOX((c.Browser.isMac?"Option":"Alt"),"ALT"),m.CHECKBOX("Command","CMD",{
            hidden:!c.Browser.isMac
        }),m.CHECKBOX("Control","CTRL",{
            hidden:c.Browser.isMac
        }),m.CHECKBOX("Shift","Shift")),m.SUBMENU("Zoom Factor",m.RADIO("125%","zscale"),m.RADIO("133%","zscale"),m.RADIO("150%","zscale"),m.RADIO("175%","zscale"),m.RADIO("200%","zscale"),m.RADIO("250%","zscale"),m.RADIO("300%","zscale"),m.RADIO("400%","zscale")),m.RULE(),m.SUBMENU("Math Renderer",{
            hidden:!o.showRenderer
        },m.RADIO("HTML-CSS","renderer",{
            action:a.Renderer
        }),m.RADIO("MathML","renderer",{
            action:a.Renderer,
            value:"NativeMML"
        }),m.RADIO("SVG","renderer",{
            action:a.Renderer
        })),m.SUBMENU("MathPlayer",{
            hidden:!c.Browser.isMSIE||!o.showMathPlayer,
            disabled:!c.Browser.hasMathPlayer
        },m.LABEL("Let MathPlayer Handle:"),m.CHECKBOX("Menu Events","mpContext",{
            action:a.MPEvents,
            hidden:!e
        }),m.CHECKBOX("Mouse Events","mpMouse",{
            action:a.MPEvents,
            hidden:!e
        }),m.CHECKBOX("Mouse and Menu Events","mpMouse",{
            action:a.MPEvents,
            hidden:e
        })),m.SUBMENU("Font Preference",{
            hidden:!o.showFontMenu
        },m.LABEL("For HTML-CSS:"),m.RADIO("Auto","font",{
            action:a.Font
        }),m.RULE(),m.RADIO("TeX (local)","font",{
            action:a.Font
        }),m.RADIO("TeX (web)","font",{
            action:a.Font
        }),m.RADIO("TeX (image)","font",{
            action:a.Font
        }),m.RULE(),m.RADIO("STIX (local)","font",{
            action:a.Font
        })),m.SUBMENU("Contextual Menu",{
            hidden:!o.showContext
        },m.RADIO("MathJax","context"),m.RADIO("Browser","context")),m.COMMAND("Scale All Math ...",a.Scale),m.RULE().With({
            hidden:!o.showDiscoverable,
            name:"discover_rule"
        }),m.CHECKBOX("Highlight on Hover","discoverable",{
            hidden:!o.showDiscoverable
        })),m.RULE(),m.COMMAND("About MathJax",a.About),m.COMMAND("MathJax Help",a.Help));
        if(a.isMobile){
            (function(){
                var r=o.settings;
                var q=a.menu.Find("Math Settings","Zoom Trigger").menu;
                q.items[0].disabled=q.items[1].disabled=true;
                if(r.zoom==="Hover"||r.zoom=="Click"){
                    r.zoom="None"
                }
                q.items=q.items.slice(0,4);
                if(navigator.appVersion.match(/[ (]Android[) ]/)){
                    a.ITEM.SUBMENU.Augment({
                        marker:"\u00BB"
                    })
                }
            })()
        }
    });
    a.showRenderer=function(q){
        a.cookie.showRenderer=o.showRenderer=q;
        a.saveCookie();
        a.menu.Find("Math Settings","Math Renderer").hidden=!q
    };
    
    a.showMathPlayer=function(q){
        a.cookie.showMathPlayer=o.showMathPlayer=q;
        a.saveCookie();
        a.menu.Find("Math Settings","MathPlayer").hidden=!q
    };
    
    a.showFontMenu=function(q){
        a.cookie.showFontMenu=o.showFontMenu=q;
        a.saveCookie();
        a.menu.Find("Math Settings","Font Preference").hidden=!q
    };
    
    a.showContext=function(q){
        a.cookie.showContext=o.showContext=q;
        a.saveCookie();
        a.menu.Find("Math Settings","Contextual Menu").hidden=!q
    };
    
    a.showDiscoverable=function(q){
        a.cookie.showContext=o.showContext=q;
        a.saveCookie();
        a.menu.Find("Math Settings","Highlight on Hover").hidden=!q;
        a.menu.Find("Math Settings","discover_rule").hidden=!q
    };
    
    MathJax.Hub.Register.StartupHook("HTML-CSS Jax Ready",function(){
        if(!MathJax.OutputJax["HTML-CSS"].config.imageFont){
            a.menu.Find("Math Settings","Font Preference","TeX (image)").disabled=true
        }
    });
    f.Queue(c.Register.StartupHook("End Config",{}),["getImages",a],["Styles",k,o.styles],["Post",c.Startup.signal,"MathMenu Ready"],["loadComplete",k,"[MathJax]/extensions/MathMenu.js"])
})(MathJax.Hub,MathJax.HTML,MathJax.Ajax,MathJax.CallBack,MathJax.OutputJax);

MathJax.ElementJax.mml=MathJax.ElementJax({
    mimeType:"jax/mml"
},{
    id:"mml",
    version:"2.0",
    directory:MathJax.ElementJax.directory+"/mml",
    extensionDir:MathJax.ElementJax.extensionDir+"/mml",
    optableDir:MathJax.ElementJax.directory+"/mml/optable"
});
MathJax.ElementJax.mml.Augment({
    Init:function(){
        if(arguments.length===1&&arguments[0].type==="math"){
            this.root=arguments[0]
        }else{
            this.root=MathJax.ElementJax.mml.math.apply(this,arguments)
        }
        if(this.root.attr&&this.root.attr.mode){
            if(!this.root.display&&this.root.attr.mode==="display"){
                this.root.display="block";
                this.root.attrNames.push("display")
            }
            delete this.root.attr.mode;
            for(var b=0,a=this.root.attrNames.length;b<a;b++){
                if(this.root.attrNames[b]==="mode"){
                    this.root.attrNames.splice(b,1);
                    break
                }
            }
        }
    }
},{
    INHERIT:"_inherit_",
    AUTO:"_auto_",
    SIZE:{
        INFINITY:"infinity",
        SMALL:"small",
        NORMAL:"normal",
        BIG:"big"
    },
    COLOR:{
        TRANSPARENT:"transparent"
    },
    VARIANT:{
        NORMAL:"normal",
        BOLD:"bold",
        ITALIC:"italic",
        BOLDITALIC:"bold-italic",
        DOUBLESTRUCK:"double-struck",
        FRAKTUR:"fraktur",
        BOLDFRAKTUR:"bold-fraktur",
        SCRIPT:"script",
        BOLDSCRIPT:"bold-script",
        SANSSERIF:"sans-serif",
        BOLDSANSSERIF:"bold-sans-serif",
        SANSSERIFITALIC:"sans-serif-italic",
        SANSSERIFBOLDITALIC:"sans-serif-bold-italic",
        MONOSPACE:"monospace",
        INITIAL:"inital",
        TAILED:"tailed",
        LOOPED:"looped",
        STRETCHED:"stretched",
        CALIGRAPHIC:"-tex-caligraphic",
        OLDSTYLE:"-tex-oldstyle"
    },
    FORM:{
        PREFIX:"prefix",
        INFIX:"infix",
        POSTFIX:"postfix"
    },
    LINEBREAK:{
        AUTO:"auto",
        NEWLINE:"newline",
        NOBREAK:"nobreak",
        GOODBREAK:"goodbreak",
        BADBREAK:"badbreak"
    },
    LINEBREAKSTYLE:{
        BEFORE:"before",
        AFTER:"after",
        DUPLICATE:"duplicate",
        INFIXLINBREAKSTYLE:"infixlinebreakstyle"
    },
    INDENTALIGN:{
        LEFT:"left",
        CENTER:"center",
        RIGHT:"right",
        AUTO:"auto",
        ID:"id",
        INDENTALIGN:"indentalign"
    },
    INDENTSHIFT:{
        INDENTSHIFT:"indentshift"
    },
    LINETHICKNESS:{
        THIN:"thin",
        MEDIUM:"medium",
        THICK:"thick"
    },
    NOTATION:{
        LONGDIV:"longdiv",
        ACTUARIAL:"actuarial",
        RADICAL:"radical",
        BOX:"box",
        ROUNDEDBOX:"roundedbox",
        CIRCLE:"circle",
        LEFT:"left",
        RIGHT:"right",
        TOP:"top",
        BOTTOM:"bottom",
        UPDIAGONALSTRIKE:"updiagonalstrike",
        DOWNDIAGONALSTRIKE:"downdiagonalstrike",
        VERTICALSTRIKE:"verticalstrike",
        HORIZONTALSTRIKE:"horizontalstrike",
        MADRUWB:"madruwb"
    },
    ALIGN:{
        TOP:"top",
        BOTTOM:"bottom",
        CENTER:"center",
        BASELINE:"baseline",
        AXIS:"axis",
        LEFT:"left",
        RIGHT:"right"
    },
    LINES:{
        NONE:"none",
        SOLID:"solid",
        DASHED:"dashed"
    },
    SIDE:{
        LEFT:"left",
        RIGHT:"right",
        LEFTOVERLAP:"leftoverlap",
        RIGHTOVERLAP:"rightoverlap"
    },
    WIDTH:{
        AUTO:"auto",
        FIT:"fit"
    },
    ACTIONTYPE:{
        TOGGLE:"toggle",
        STATUSLINE:"statusline",
        TOOLTIP:"tooltip",
        INPUT:"input"
    },
    LENGTH:{
        VERYVERYTHINMATHSPACE:"veryverythinmathspace",
        VERYTHINMATHSPACE:"verythinmathspace",
        THINMATHSPACE:"thinmathspace",
        MEDIUMMATHSPACE:"mediummathspace",
        THICKMATHSPACE:"thickmathspace",
        VERYTHICKMATHSPACE:"verythickmathspace",
        VERYVERYTHICKMATHSPACE:"veryverythickmathspace",
        NEGATIVEVERYVERYTHINMATHSPACE:"negativeveryverythinmathspace",
        NEGATIVEVERYTHINMATHSPACE:"negativeverythinmathspace",
        NEGATIVETHINMATHSPACE:"negativethinmathspace",
        NEGATIVEMEDIUMMATHSPACE:"negativemediummathspace",
        NEGATIVETHICKMATHSPACE:"negativethickmathspace",
        NEGATIVEVERYTHICKMATHSPACE:"negativeverythickmathspace",
        NEGATIVEVERYVERYTHICKMATHSPACE:"negativeveryverythickmathspace"
    },
    OVERFLOW:{
        LINBREAK:"linebreak",
        SCROLL:"scroll",
        ELIDE:"elide",
        TRUNCATE:"truncate",
        SCALE:"scale"
    },
    UNIT:{
        EM:"em",
        EX:"ex",
        PX:"px",
        IN:"in",
        CM:"cm",
        MM:"mm",
        PT:"pt",
        PC:"pc"
    },
    TEXCLASS:{
        ORD:0,
        OP:1,
        BIN:2,
        REL:3,
        OPEN:4,
        CLOSE:5,
        PUNCT:6,
        INNER:7,
        VCENTER:8,
        NONE:-1
    },
    TEXCLASSNAMES:["ORD","OP","BIN","REL","OPEN","CLOSE","PUNCT","INNER","VCENTER"],
    copyAttributes:{
        fontfamily:true,
        fontsize:true,
        fontweight:true,
        fontstyle:true,
        color:true,
        background:true,
        id:true,
        "class":true,
        href:true,
        style:true
    },
    skipAttributes:{
        texClass:true,
        useHeight:true,
        texprimestyle:true
    },
    copyAttributeNames:["fontfamily","fontsize","fontweight","fontstyle","color","background","id","class","href","style"]
});
(function(a){
    a.mbase=MathJax.Object.Subclass({
        type:"base",
        isToken:false,
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT
        },
        noInherit:{},
        noInheritAttribute:{
            texClass:true
        },
        linebreakContainer:false,
        Init:function(){
            this.data=[];
            if(this.inferRow&&!(arguments.length===1&&arguments[0].inferred)){
                this.Append(a.mrow().With({
                    inferred:true
                }))
            }
            this.Append.apply(this,arguments)
        },
        With:function(d){
            for(var e in d){
                if(d.hasOwnProperty(e)){
                    this[e]=d[e]
                }
            }
            return this
        },
        Append:function(){
            if(this.inferRow&&this.data.length){
                this.data[0].Append.apply(this.data[0],arguments)
            }else{
                for(var e=0,d=arguments.length;e<d;e++){
                    this.SetData(this.data.length,arguments[e])
                }
            }
        },
        SetData:function(d,e){
            if(e!=null){
                if(!(e instanceof a.mbase)){
                    e=(this.isToken?a.chars(e):a.mtext(e))
                }
                e.parent=this;
                e.setInherit(this.inheritFromMe?this:this.inherit)
            }
            this.data[d]=e
        },
        Parent:function(){
            var d=this.parent;
            while(d&&d.inferred){
                d=d.parent
            }
            return d
        },
        Get:function(e,j){
            if(this[e]!=null){
                return this[e]
            }
            if(this.attr&&this.attr[e]!=null){
                return this.attr[e]
            }
            var f=this.Parent();
            if(f&&f["adjustChild_"+e]!=null){
                return(f["adjustChild_"+e])(f.childPosition(this))
            }
            var i=this.inherit;
            var d=i;
            while(i){
                var h=i[e];
                if(h==null&&i.attr){
                    h=i.attr[e]
                }
                if(h!=null&&!i.noInheritAttribute[e]){
                    var g=i.noInherit[this.type];
                    if(!(g&&g[e])){
                        return h
                    }
                }
                d=i;
                i=i.inherit
            }
            if(!j){
                if(this.defaults[e]===a.AUTO){
                    return this.autoDefault(e)
                }
                if(this.defaults[e]!==a.INHERIT&&this.defaults[e]!=null){
                    return this.defaults[e]
                }
                if(d){
                    return d.defaults[e]
                }
            }
            return null
        },
        hasValue:function(d){
            return(this.Get(d,true)!=null)
        },
        getValues:function(){
            var e={};
    
            for(var f=0,d=arguments.length;f<d;f++){
                e[arguments[f]]=this.Get(arguments[f])
            }
            return e
        },
        adjustChild_scriptlevel:function(d){
            return this.Get("scriptlevel")
        },
        adjustChild_displaystyle:function(d){
            return this.Get("displaystyle")
        },
        adjustChild_texprimestyle:function(d){
            return this.Get("texprimestyle")
        },
        childPosition:function(f){
            if(f.parent.inferred){
                f=f.parent
            }
            for(var e=0,d=this.data.length;e<d;e++){
                if(this.data[e]===f){
                    return e
                }
            }
            return null
        },
        setInherit:function(f){
            if(f!==this.inherit&&this.inherit==null){
                this.inherit=f;
                for(var e=0,d=this.data.length;e<d;e++){
                    if(this.data[e]&&this.data[e].setInherit){
                        this.data[e].setInherit(f)
                    }
                }
            }
        },
        setTeXclass:function(d){
            this.getPrevClass(d);
            return(typeof(this.texClass)!=="undefined"?this:d)
        },
        getPrevClass:function(d){
            if(d){
                this.prevClass=d.Get("texClass");
                this.prevLevel=d.Get("scriptlevel")
            }
        },
        updateTeXclass:function(d){
            if(d){
                this.prevClass=d.prevClass;
                delete d.prevClass;
                this.prevLevel=d.prevLevel;
                delete d.prevLevel;
                this.texClass=d.Get("texClass")
            }
        },
        texSpacing:function(){
            var e=(this.prevClass!=null?this.prevClass:a.TEXCLASS.NONE);
            var d=(this.Get("texClass")||a.TEXCLASS.ORD);
            if(e===a.TEXCLASS.NONE||d===a.TEXCLASS.NONE){
                return""
            }
            if(e===a.TEXCLASS.VCENTER){
                e=a.TEXCLASS.ORD
            }
            if(d===a.TEXCLASS.VCENTER){
                d=a.TEXCLASS.ORD
            }
            var f=this.TEXSPACE[e][d];
            if(this.prevLevel>0&&this.Get("scriptlevel")>0&&f>=0){
                return""
            }
            return this.TEXSPACELENGTH[Math.abs(f)]
        },
        TEXSPACELENGTH:["",a.LENGTH.THINMATHSPACE,a.LENGTH.MEDIUMMATHSPACE,a.LENGTH.THICKMATHSPACE],
        TEXSPACE:[[0,-1,2,3,0,0,0,1],[-1,-1,0,3,0,0,0,1],[2,2,0,0,2,0,0,2],[3,3,0,0,3,0,0,3],[0,0,0,0,0,0,0,0],[0,-1,2,3,0,0,0,1],[1,1,0,1,1,1,1,1],[1,-1,2,3,1,0,1,1]],
        autoDefault:function(d){
            return""
        },
        isSpacelike:function(){
            return false
        },
        isEmbellished:function(){
            return false
        },
        Core:function(){
            return this
        },
        CoreMO:function(){
            return this
        },
        hasNewline:function(){
            if(this.isEmbellished()){
                return this.CoreMO().hasNewline()
            }
            if(this.isToken||this.linebreakContainer){
                return false
            }
            for(var e=0,d=this.data.length;e<d;e++){
                if(this.data[e]&&this.data[e].hasNewline()){
                    return true
                }
            }
            return false
        },
        array:function(){
            if(this.inferred){
                return this.data
            }else{
                return[this]
            }
        },
        toString:function(){
            return this.type+"("+this.data.join(",")+")"
        }
    },{
        childrenSpacelike:function(){
            for(var e=0,d=this.data.length;e<d;e++){
                if(!this.data[e].isSpacelike()){
                    return false
                }
            }
            return true
        },
        childEmbellished:function(){
            return(this.data[0]&&this.data[0].isEmbellished())
        },
        childCore:function(){
            return this.data[0]
        },
        childCoreMO:function(){
            return(this.data[0]?this.data[0].CoreMO():null)
        },
        setChildTeXclass:function(d){
            if(this.data[0]){
                d=this.data[0].setTeXclass(d);
                this.updateTeXclass(this.data[0])
            }
            return d
        },
        setBaseTeXclasses:function(f){
            this.getPrevClass(f);
            this.texClass=null;
            if(this.isEmbellished()){
                f=this.data[0].setTeXclass(f);
                this.updateTeXclass(this.Core())
            }else{
                if(this.data[0]){
                    this.data[0].setTeXclass()
                }
                f=this
            }
            for(var e=1,d=this.data.length;e<d;e++){
                if(this.data[e]){
                    this.data[e].setTeXclass()
                }
            }
            return f
        },
        setSeparateTeXclasses:function(f){
            this.getPrevClass(f);
            for(var e=0,d=this.data.length;e<d;e++){
                if(this.data[e]){
                    this.data[e].setTeXclass()
                }
            }
            if(this.isEmbellished()){
                this.updateTeXclass(this.Core())
            }
            return this
        }
    });
    a.mi=a.mbase.Subclass({
        type:"mi",
        isToken:true,
        texClass:a.TEXCLASS.ORD,
        defaults:{
            mathvariant:a.AUTO,
            mathsize:a.INHERIT,
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT
        },
        autoDefault:function(e){
            if(e==="mathvariant"){
                var d=(this.data[0]||"").toString();
                return(d.length===1||(d.length===2&&d.charCodeAt(0)>=55296&&d.charCodeAt(0)<56320)?a.VARIANT.ITALIC:a.VARIANT.NORMAL)
            }
            return""
        }
    });
    a.mn=a.mbase.Subclass({
        type:"mn",
        isToken:true,
        texClass:a.TEXCLASS.ORD,
        defaults:{
            mathvariant:a.INHERIT,
            mathsize:a.INHERIT,
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT
        }
    });
    a.mo=a.mbase.Subclass({
        type:"mo",
        isToken:true,
        defaults:{
            mathvariant:a.INHERIT,
            mathsize:a.INHERIT,
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            form:a.AUTO,
            fence:a.AUTO,
            separator:a.AUTO,
            lspace:a.AUTO,
            rspace:a.AUTO,
            stretchy:a.AUTO,
            symmetric:a.AUTO,
            maxsize:a.AUTO,
            minsize:a.AUTO,
            largeop:a.AUTO,
            movablelimits:a.AUTO,
            accent:a.AUTO,
            linebreak:a.LINEBREAK.AUTO,
            lineleading:a.INHERIT,
            linebreakstyle:a.AUTO,
            linebreakmultchar:a.INHERIT,
            indentalign:a.INHERIT,
            indentshift:a.INHERIT,
            indenttarget:a.INHERIT,
            indentalignfirst:a.INHERIT,
            indentshiftfirst:a.INHERIT,
            indentalignlast:a.INHERIT,
            indentshiftlast:a.INHERIT,
            texClass:a.AUTO
        },
        defaultDef:{
            form:a.FORM.INFIX,
            fence:false,
            separator:false,
            lspace:a.LENGTH.THICKMATHSPACE,
            rspace:a.LENGTH.THICKMATHSPACE,
            stretchy:false,
            symmetric:true,
            maxsize:a.SIZE.INFINITY,
            minsize:"0em",
            largeop:false,
            movablelimits:false,
            accent:false,
            linebreak:a.LINEBREAK.AUTO,
            lineleading:"1ex",
            linebreakstyle:"before",
            indentalign:a.INDENTALIGN.AUTO,
            indentshift:"0",
            indenttarget:"",
            indentalignfirst:a.INDENTALIGN.INDENTALIGN,
            indentshiftfirst:a.INDENTSHIFT.INDENTSHIFT,
            indentalignlast:a.INDENTALIGN.INDENTALIGN,
            indentshiftlast:a.INDENTSHIFT.INDENTSHIFT,
            texClass:a.TEXCLASS.REL
        },
        SPACE_ATTR:{
            lspace:1,
            rspace:2,
            form:4
        },
        useMMLspacing:7,
        autoDefault:function(f,l){
            var k=this.def;
            if(!k){
                if(f==="form"){
                    this.useMMLspacing&=~this.SPACE_ATTR.form;
                    return this.getForm()
                }
                var j=this.data.join("");
                var e=[this.Get("form"),a.FORM.INFIX,a.FORM.POSTFIX,a.FORM.PREFIX];
                for(var g=0,d=e.length;g<d;g++){
                    var h=this.OPTABLE[e[g]][j];
                    if(h){
                        k=this.makeDef(h);
                        break
                    }
                }
                if(!k){
                    k=this.CheckRange(j)
                }
                if(!k&&l){
                    k={}
                }else{
                    if(!k){
                        k=MathJax.Hub.Insert({},this.defaultDef)
                    }
                    k.form=e[0];
                    this.def=k
                }
            }
            this.useMMLspacing&=~(this.SPACE_ATTR[f]||0);
            if(k[f]!=null){
                return k[f]
            }else{
                if(!l){
                    return this.defaultDef[f]
                }
            }
            return""
        },
        CheckRange:function(h){
            var j=h.charCodeAt(0);
            if(j>=55296&&j<56320){
                j=(((j-55296)<<10)+(h.charCodeAt(1)-56320))+65536
            }
            for(var f=0,d=this.RANGES.length;f<d&&this.RANGES[f][0]<=j;f++){
                if(j<=this.RANGES[f][1]){
                    if(this.RANGES[f][3]){
                        var e=a.optableDir+"/"+this.RANGES[f][3]+".js";
                        this.RANGES[f][3]=null;
                        MathJax.Hub.RestartAfter(MathJax.Ajax.Require(e))
                    }
                    var g=a.TEXCLASSNAMES[this.RANGES[f][2]];
                    g=this.OPTABLE.infix[h]=a.mo.OPTYPES[g==="BIN"?"BIN3":g];
                    return this.makeDef(g)
                }
            }
            return null
        },
        makeDef:function(e){
            if(e[2]==null){
                e[2]=this.defaultDef.texClass
            }
            if(!e[3]){
                e[3]={}
            }
            var d=MathJax.Hub.Insert({},e[3]);
            d.lspace=this.SPACE[e[0]];
            d.rspace=this.SPACE[e[1]];
            d.texClass=e[2];
            if(d.texClass===a.TEXCLASS.REL&&(this.movablelimits||this.data.join("").match(/^[a-z]+$/i))){
                d.texClass=a.TEXCLASS.OP
            }
            return d
        },
        getForm:function(){
            var d=this,f=this.parent,e=this.Parent();
            while(e&&e.isEmbellished()){
                d=f;
                f=e.parent;
                e=e.Parent()
            }
            if(f&&f.type==="mrow"&&f.NonSpaceLength()!==1){
                if(f.FirstNonSpace()===d){
                    return a.FORM.PREFIX
                }
                if(f.LastNonSpace()===d){
                    return a.FORM.POSTFIX
                }
            }
            return a.FORM.INFIX
        },
        isEmbellished:function(){
            return true
        },
        hasNewline:function(){
            return(this.Get("linebreak")===a.LINEBREAK.NEWLINE)
        },
        setTeXclass:function(d){
            this.getValues("lspace","rspace");
            if(this.useMMLspacing){
                this.texClass=a.TEXCLASS.NONE;
                return this
            }
            this.texClass=this.Get("texClass");
            if(this.texClass===a.TEXCLASS.NONE){
                return d
            }
            if(d){
                this.prevClass=d.texClass||a.TEXCLASS.ORD;
                this.prevLevel=d.Get("scriptlevel")
            }else{
                this.prevClass=a.TEXCLASS.NONE
            }
            if(this.texClass===a.TEXCLASS.BIN&&(this.prevClass===a.TEXCLASS.NONE||this.prevClass===a.TEXCLASS.BIN||this.prevClass===a.TEXCLASS.OP||this.prevClass===a.TEXCLASS.REL||this.prevClass===a.TEXCLASS.OPEN||this.prevClass===a.TEXCLASS.PUNCT)){
                this.texClass=a.TEXCLASS.ORD
            }else{
                if(this.prevClass===a.TEXCLASS.BIN&&(this.texClass===a.TEXCLASS.REL||this.texClass===a.TEXCLASS.CLOSE||this.texClass===a.TEXCLASS.PUNCT)){
                    d.texClass=this.prevClass=a.TEXCLASS.ORD
                }
            }
            return this
        }
    });
    a.mtext=a.mbase.Subclass({
        type:"mtext",
        isToken:true,
        isSpacelike:function(){
            return true
        },
        texClass:a.TEXCLASS.ORD,
        defaults:{
            mathvariant:a.INHERIT,
            mathsize:a.INHERIT,
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT
        }
    });
    a.mspace=a.mbase.Subclass({
        type:"mspace",
        isToken:true,
        isSpacelike:function(){
            return true
        },
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            width:"0em",
            height:"0ex",
            depth:"0ex",
            linebreak:a.LINEBREAK.AUTO
        },
        hasNewline:function(){
            return(this.Get("linebreak")===a.LINEBREAK.NEWLINE)
        }
    });
    a.ms=a.mbase.Subclass({
        type:"ms",
        isToken:true,
        texClass:a.TEXCLASS.ORD,
        defaults:{
            mathvariant:a.INHERIT,
            mathsize:a.INHERIT,
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            lquote:'"',
            rquote:'"'
        }
    });
    a.mglyph=a.mbase.Subclass({
        type:"mglyph",
        isToken:true,
        texClass:a.TEXCLASS.ORD,
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            alt:"",
            src:"",
            width:a.AUTO,
            height:a.AUTO,
            valign:"0em"
        }
    });
    a.mrow=a.mbase.Subclass({
        type:"mrow",
        isSpacelike:a.mbase.childrenSpacelike,
        inferred:false,
        isEmbellished:function(){
            var e=false;
            for(var f=0,d=this.data.length;f<d;f++){
                if(this.data[f]==null){
                    continue
                }
                if(this.data[f].isEmbellished()){
                    if(e){
                        return false
                    }
                    e=true;
                    this.core=f
                }else{
                    if(!this.data[f].isSpacelike()){
                        return false
                    }
                }
            }
            return e
        },
        NonSpaceLength:function(){
            var f=0;
            for(var e=0,d=this.data.length;e<d;e++){
                if(this.data[e]&&!this.data[e].isSpacelike()){
                    f++
                }
            }
            return f
        },
        FirstNonSpace:function(){
            for(var e=0,d=this.data.length;e<d;e++){
                if(this.data[e]&&!this.data[e].isSpacelike()){
                    return this.data[e]
                }
            }
            return null
        },
        LastNonSpace:function(){
            for(var d=this.data.length-1;d>=0;d--){
                if(this.data[0]&&!this.data[d].isSpacelike()){
                    return this.data[d]
                }
            }
            return null
        },
        Core:function(){
            if(!(this.isEmbellished())||typeof(this.core)==="undefined"){
                return this
            }
            return this.data[this.core]
        },
        CoreMO:function(){
            if(!(this.isEmbellished())||typeof(this.core)==="undefined"){
                return this
            }
            return this.data[this.core].CoreMO()
        },
        toString:function(){
            if(this.inferred){
                return"["+this.data.join(",")+"]"
            }
            return this.SUPER(arguments).toString.call(this)
        },
        setTeXclass:function(f){
            for(var e=0,d=this.data.length;e<d;e++){
                if(this.data[e]){
                    f=this.data[e].setTeXclass(f)
                }
            }
            if(this.data[0]){
                this.updateTeXclass(this.data[0])
            }
            return f
        }
    });
    a.mfrac=a.mbase.Subclass({
        type:"mfrac",
        num:0,
        den:1,
        linebreakContainer:true,
        texClass:a.TEXCLASS.INNER,
        isEmbellished:a.mbase.childEmbellished,
        Core:a.mbase.childCore,
        CoreMO:a.mbase.childCoreMO,
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            linethickness:a.LINETHICKNESS.MEDIUM,
            numalign:a.ALIGN.CENTER,
            denomalign:a.ALIGN.CENTER,
            bevelled:false
        },
        adjustChild_displaystyle:function(d){
            return false
        },
        adjustChild_scriptlevel:function(e){
            var d=this.Get("scriptlevel");
            if(!this.Get("displaystyle")||d>0){
                d++
            }
            return d
        },
        adjustChild_texprimestyle:function(d){
            if(d==this.den){
                return true
            }
            return this.Get("texprimestyle")
        },
        setTeXclass:a.mbase.setSeparateTeXclasses
    });
    a.msqrt=a.mbase.Subclass({
        type:"msqrt",
        inferRow:true,
        linebreakContainer:true,
        texClass:a.TEXCLASS.ORD,
        setTeXclass:a.mbase.setSeparateTeXclasses,
        adjustChild_texprimestyle:function(d){
            return true
        }
    });
    a.mroot=a.mbase.Subclass({
        type:"mroot",
        linebreakContainer:true,
        texClass:a.TEXCLASS.ORD,
        adjustChild_displaystyle:function(d){
            if(d===1){
                return false
            }
            return this.Get("displaystyle")
        },
        adjustChild_scriptlevel:function(e){
            var d=this.Get("scriptlevel");
            if(e===1){
                d+=2
            }
            return d
        },
        adjustChild_texprimestyle:function(d){
            if(d===0){
                return true
            }
            return this.Get("texprimestyle")
        },
        setTeXclass:a.mbase.setSeparateTeXclasses
    });
    a.mstyle=a.mbase.Subclass({
        type:"mstyle",
        isSpacelike:a.mbase.childrenSpacelike,
        isEmbellished:a.mbase.childEmbellished,
        Core:a.mbase.childCore,
        CoreMO:a.mbase.childCoreMO,
        inferRow:true,
        defaults:{
            scriptlevel:a.INHERIT,
            displaystyle:a.INHERIT,
            scriptsizemultiplier:Math.sqrt(1/2),
            scriptminsize:"8pt",
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            infixlinebreakstyle:a.LINEBREAKSTYLE.BEFORE,
            decimalseparator:"."
        },
        adjustChild_scriptlevel:function(f){
            var e=this.scriptlevel;
            if(e==null){
                e=this.Get("scriptlevel")
            }else{
                if(String(e).match(/^ *[-+]/)){
                    delete this.scriptlevel;
                    var d=this.Get("scriptlevel");
                    this.scriptlevel=e;
                    e=d+parseInt(e)
                }
            }
            return e
        },
        inheritFromMe:true,
        noInherit:{
            mpadded:{
                width:true,
                height:true,
                depth:true,
                lspace:true,
                voffset:true
            },
            mtable:{
                width:true,
                height:true,
                depth:true,
                align:true
            }
        },
        setTeXclass:a.mbase.setChildTeXclass
    });
    a.merror=a.mbase.Subclass({
        type:"merror",
        inferRow:true,
        linebreakContainer:true,
        texClass:a.TEXCLASS.ORD
    });
    a.mpadded=a.mbase.Subclass({
        type:"mpadded",
        inferRow:true,
        isSpacelike:a.mbase.childrenSpacelike,
        isEmbellished:a.mbase.childEmbellished,
        Core:a.mbase.childCore,
        CoreMO:a.mbase.childCoreMO,
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            width:"",
            height:"",
            depth:"",
            lspace:0,
            voffset:0
        },
        setTeXclass:a.mbase.setChildTeXclass
    });
    a.mphantom=a.mbase.Subclass({
        type:"mphantom",
        texClass:a.TEXCLASS.ORD,
        inferRow:true,
        isSpacelike:a.mbase.childrenSpacelike,
        isEmbellished:a.mbase.childEmbellished,
        Core:a.mbase.childCore,
        CoreMO:a.mbase.childCoreMO,
        setTeXclass:a.mbase.setChildTeXclass
    });
    a.mfenced=a.mbase.Subclass({
        type:"mfenced",
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            open:"(",
            close:")",
            separators:","
        },
        texClass:a.TEXCLASS.OPEN,
        setTeXclass:function(g){
            this.getPrevClass(g);
            var e=this.getValues("open","close","separators");
            e.open=e.open.replace(/[ \t\n\r]/g,"");
            e.close=e.close.replace(/[ \t\n\r]/g,"");
            e.separators=e.separators.replace(/[ \t\n\r]/g,"");
            if(e.open!==""){
                this.SetData("open",a.mo(e.open).With({
                    stretchy:true,
                    texClass:a.TEXCLASS.OPEN
                }));
                g=this.data.open.setTeXclass(g)
            }
            if(e.separators!==""){
                while(e.separators.length<this.data.length){
                    e.separators+=e.separators.charAt(e.separators.length-1)
                }
            }
            if(this.data[0]){
                g=this.data[0].setTeXclass(g)
            }
            for(var f=1,d=this.data.length;f<d;f++){
                if(this.data[f]){
                    if(e.separators!==""){
                        this.SetData("sep"+f,a.mo(e.separators.charAt(f-1)));
                        g=this.data["sep"+f].setTeXclass(g)
                    }
                    g=this.data[f].setTeXclass(g)
                }
            }
            if(e.close!==""){
                this.SetData("close",a.mo(e.close).With({
                    stretchy:true,
                    texClass:a.TEXCLASS.CLOSE
                }));
                g=this.data.close.setTeXclass(g)
            }
            this.updateTeXclass(this.data.open);
            return g
        }
    });
    a.menclose=a.mbase.Subclass({
        type:"menclose",
        inferRow:true,
        linebreakContainer:true,
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            notation:a.NOTATION.LONGDIV,
            texClass:a.TEXCLASS.ORD
        },
        setTeXclass:a.mbase.setSeparateTeXclasses
    });
    a.msubsup=a.mbase.Subclass({
        type:"msubsup",
        base:0,
        sub:1,
        sup:2,
        linebreakContainer:true,
        isEmbellished:a.mbase.childEmbellished,
        Core:a.mbase.childCore,
        CoreMO:a.mbase.childCoreMO,
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            subscriptshift:"",
            superscriptshift:"",
            texClass:a.AUTO
        },
        autoDefault:function(d){
            if(d==="texClass"){
                return(this.isEmbellished()?this.CoreMO().Get(d):a.TEXCLASS.ORD)
            }
            return 0
        },
        adjustChild_displaystyle:function(d){
            if(d>0){
                return false
            }
            return this.Get("displaystyle")
        },
        adjustChild_scriptlevel:function(e){
            var d=this.Get("scriptlevel");
            if(e>0){
                d++
            }
            return d
        },
        adjustChild_texprimestyle:function(d){
            if(d===this.sub){
                return true
            }
            return this.Get("texprimestyle")
        },
        setTeXclass:a.mbase.setBaseTeXclasses
    });
    a.msub=a.msubsup.Subclass({
        type:"msub"
    });
    a.msup=a.msubsup.Subclass({
        type:"msup",
        sub:2,
        sup:1
    });
    a.mmultiscripts=a.msubsup.Subclass({
        type:"mmultiscripts",
        adjustChild_texprimestyle:function(d){
            if(d%2===1){
                return true
            }
            return this.Get("texprimestyle")
        }
    });
    a.mprescripts=a.mbase.Subclass({
        type:"mprescripts"
    });
    a.none=a.mbase.Subclass({
        type:"none"
    });
    a.munderover=a.mbase.Subclass({
        type:"munderover",
        base:0,
        under:1,
        over:2,
        sub:1,
        sup:2,
        ACCENTS:["","accentunder","accent"],
        linebreakContainer:true,
        isEmbellished:a.mbase.childEmbellished,
        Core:a.mbase.childCore,
        CoreMO:a.mbase.childCoreMO,
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            accent:a.AUTO,
            accentunder:a.AUTO,
            align:a.ALIGN.CENTER,
            texClass:a.AUTO,
            subscriptshift:"",
            superscriptshift:""
        },
        autoDefault:function(d){
            if(d==="texClass"){
                return(this.isEmbellished()?this.CoreMO().Get(d):a.TEXCLASS.ORD)
            }
            if(d==="accent"&&this.data[this.over]){
                return this.data[this.over].CoreMO().Get("accent")
            }
            if(d==="accentunder"&&this.data[this.under]){
                return this.data[this.under].CoreMO().Get("accent")
            }
            return false
        },
        adjustChild_displaystyle:function(d){
            if(d>0){
                return false
            }
            return this.Get("displaystyle")
        },
        adjustChild_scriptlevel:function(f){
            var e=this.Get("scriptlevel");
            var d=(this.data[this.base]&&!this.Get("displaystyle")&&this.data[this.base].CoreMO().Get("movablelimits"));
            if(f==this.under&&(d||!this.Get("accentunder"))){
                e++
            }
            if(f==this.over&&(d||!this.Get("accent"))){
                e++
            }
            return e
        },
        adjustChild_texprimestyle:function(d){
            if(d===this.base&&this.data[this.over]){
                return true
            }
            return this.Get("texprimestyle")
        },
        setTeXclass:a.mbase.setBaseTeXclasses
    });
    a.munder=a.munderover.Subclass({
        type:"munder"
    });
    a.mover=a.munderover.Subclass({
        type:"mover",
        over:1,
        under:2,
        sup:1,
        sub:2,
        ACCENTS:["","accent","accentunder"]
    });
    a.mtable=a.mbase.Subclass({
        type:"mtable",
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            align:a.ALIGN.AXIS,
            rowalign:a.ALIGN.BASELINE,
            columnalign:a.ALIGN.CENTER,
            groupalign:"{left}",
            alignmentscope:true,
            columnwidth:a.WIDTH.AUTO,
            width:a.WIDTH.AUTO,
            rowspacing:"1ex",
            columnspacing:".8em",
            rowlines:a.LINES.NONE,
            columnlines:a.LINES.NONE,
            frame:a.LINES.NONE,
            framespacing:"0.4em 0.5ex",
            equalrows:false,
            equalcolumns:false,
            displaystyle:false,
            side:a.SIDE.RIGHT,
            minlabelspacing:"0.8em",
            texClass:a.TEXCLASS.ORD,
            useHeight:1
        },
        inheritFromMe:true,
        noInherit:{
            mtable:{
                align:true,
                rowalign:true,
                columnalign:true,
                groupalign:true,
                alignmentscope:true,
                columnwidth:true,
                width:true,
                rowspacing:true,
                columnspacing:true,
                rowlines:true,
                columnlines:true,
                frame:true,
                framespacing:true,
                equalrows:true,
                equalcolumns:true,
                side:true,
                minlabelspacing:true,
                texClass:true,
                useHeight:1
            }
        },
        linebreakContainer:true,
        Append:function(){
            for(var e=0,d=arguments.length;e<d;e++){
                if(!((arguments[e] instanceof a.mtr)||(arguments[e] instanceof a.mlabeledtr))){
                    arguments[e]=a.mtd(arguments[e])
                }
            }
            this.SUPER(arguments).Append.apply(this,arguments)
        },
        setTeXclass:a.mbase.setSeparateTeXclasses
    });
    a.mtr=a.mbase.Subclass({
        type:"mtr",
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            rowalign:a.INHERIT,
            columnalign:a.INHERIT,
            groupalign:a.INHERIT
        },
        inheritFromMe:true,
        noInherit:{
            mrow:{
                rowalign:true,
                columnalign:true,
                groupalign:true
            },
            mtable:{
                rowalign:true,
                columnalign:true,
                groupalign:true
            }
        },
        linebreakContainer:true,
        Append:function(){
            for(var e=0,d=arguments.length;e<d;e++){
                if(!(arguments[e] instanceof a.mtd)){
                    arguments[e]=a.mtd(arguments[e])
                }
            }
            this.SUPER(arguments).Append.apply(this,arguments)
        },
        setTeXclass:a.mbase.setSeparateTeXclasses
    });
    a.mtd=a.mbase.Subclass({
        type:"mtd",
        inferRow:true,
        linebreakContainer:true,
        isEmbellished:a.mbase.childEmbellished,
        Core:a.mbase.childCore,
        CoreMO:a.mbase.childCoreMO,
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            rowspan:1,
            columnspan:1,
            rowalign:a.INHERIT,
            columnalign:a.INHERIT,
            groupalign:a.INHERIT
        },
        setTeXclass:a.mbase.setSeparateTeXclasses
    });
    a.maligngroup=a.mbase.Subclass({
        type:"malign",
        isSpacelike:function(){
            return true
        },
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            groupalign:a.INHERIT
        },
        inheritFromMe:true,
        noInherit:{
            mrow:{
                groupalign:true
            },
            mtable:{
                groupalign:true
            }
        }
    });
    a.malignmark=a.mbase.Subclass({
        type:"malignmark",
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            edge:a.SIDE.LEFT
        },
        isSpacelike:function(){
            return true
        }
    });
    a.mlabeledtr=a.mtr.Subclass({
        type:"mlabeledtr"
    });
    a.maction=a.mbase.Subclass({
        type:"maction",
        defaults:{
            mathbackground:a.INHERIT,
            mathcolor:a.INHERIT,
            actiontype:a.ACTIONTYPE.TOGGLE,
            selection:1
        },
        selected:function(){
            return this.data[this.Get("selection")-1]||a.NULL
        },
        isEmbellished:function(){
            return this.selected().isEmbellished()
        },
        isSpacelike:function(){
            return this.selected().isSpacelike()
        },
        Core:function(){
            return this.selected().Core()
        },
        CoreMO:function(){
            return this.selected().CoreMO()
        },
        setTeXclass:function(d){
            return this.selected().setTeXclass(d)
        }
    });
    a.semantics=a.mbase.Subclass({
        type:"semantics",
        isEmbellished:a.mbase.childEmbellished,
        Core:a.mbase.childCore,
        CoreMO:a.mbase.childCoreMO,
        defaults:{
            definitionURL:null,
            encoding:null
        },
        setTeXclass:a.mbase.setChildTeXclass
    });
    a.annotation=a.mbase.Subclass({
        type:"annotation",
        isToken:true,
        linebreakContainer:true,
        defaults:{
            definitionURL:null,
            encoding:null,
            cd:"mathmlkeys",
            name:"",
            src:null
        }
    });
    a["annotation-xml"]=a.mbase.Subclass({
        type:"annotation-xml",
        linebreakContainer:true,
        defaults:{
            definitionURL:null,
            encoding:null,
            cd:"mathmlkeys",
            name:"",
            src:null
        }
    });
    a.math=a.mstyle.Subclass({
        type:"math",
        defaults:{
            mathvariant:a.VARIANT.NORMAL,
            mathsize:a.SIZE.NORMAL,
            mathcolor:"",
            mathbackground:a.COLOR.TRANSPARENT,
            scriptlevel:0,
            displaystyle:a.AUTO,
            display:"inline",
            maxwidth:"",
            overflow:a.OVERFLOW.LINEBREAK,
            altimg:"",
            "altimg-width":"",
            "altimg-height":"",
            "altimg-valign":"",
            alttext:"",
            cdgroup:"",
            scriptsizemultiplier:Math.sqrt(1/2),
            scriptminsize:"8px",
            infixlinebreakstyle:a.LINEBREAKSTYLE.BEFORE,
            lineleading:"1ex",
            indentshift:"auto",
            indentalign:a.INDENTALIGN.AUTO,
            indentalignfirst:a.INDENTALIGN.INDENTALIGN,
            indentshiftfirst:a.INDENTSHIFT.INDENTSHIFT,
            indentalignlast:a.INDENTALIGN.INDENTALIGN,
            indentshiftlast:a.INDENTSHIFT.INDENTSHIFT,
            decimalseparator:".",
            texprimestyle:false
        },
        autoDefault:function(d){
            if(d==="displaystyle"){
                return this.Get("display")==="block"
            }
            return""
        },
        linebreakContainer:true,
        setTeXclass:a.mbase.setChildTeXclass
    });
    a.chars=a.mbase.Subclass({
        type:"chars",
        Append:function(){
            this.data.push.apply(this.data,arguments)
        },
        value:function(){
            return this.data.join("")
        },
        toString:function(){
            return this.data.join("")
        }
    });
    a.entity=a.mbase.Subclass({
        type:"entity",
        Append:function(){
            this.data.push.apply(this.data,arguments)
        },
        value:function(){
            if(this.data[0].substr(0,2)==="#x"){
                return parseInt(this.data[0].substr(2),16)
            }else{
                if(this.data[0].substr(0,1)==="#"){
                    return parseInt(this.data[0].substr(1))
                }else{
                    return 0
                }
            }
        },
        toString:function(){
            var d=this.value();
            if(d<=65535){
                return String.fromCharCode(d)
            }
            d-=65536;
            return String.fromCharCode((d>>10)+55296)+String.fromCharCode((d&1023)+56320)
        }
    });
    a.xml=a.mbase.Subclass({
        type:"xml",
        Init:function(){
            this.div=document.createElement("div");
            return this.SUPER(arguments).Init.apply(this,arguments)
        },
        Append:function(){
            for(var e=0,d=arguments.length;e<d;e++){
                var f=this.Import(arguments[e]);
                this.data.push(f);
                this.div.appendChild(f)
            }
        },
        Import:function(h){
            if(document.importNode){
                return document.importNode(h,true)
            }
            var e,f,d;
            if(h.nodeType===1){
                e=document.createElement(h.nodeName);
                if(h.className){
                    e.className=iNode.className
                }
                for(f=0,d=h.attributes.length;f<d;f++){
                    var g=h.attributes[f];
                    if(g.specified&&g.nodeValue!=null&&g.nodeValue!=""){
                        e.setAttribute(g.nodeName,g.nodeValue)
                    }
                    if(g.nodeName==="style"){
                        e.style.cssText=g.nodeValue
                    }
                }
                if(h.className){
                    e.className=h.className
                }
            }else{
                if(h.nodeType===3||h.nodeType===4){
                    e=document.createTextNode(h.nodeValue)
                }else{
                    if(h.nodeType===8){
                        e=document.createComment(h.nodeValue)
                    }else{
                        return document.createTextNode("")
                    }
                }
            }
            for(f=0,d=h.childNodes.length;f<d;f++){
                e.appendChild(this.Import(h.childNodes[f]))
            }
            return e
        },
        value:function(){
            return this.div
        },
        toString:function(){
            return this.div.innerHTML
        }
    });
    a.TeXAtom=a.mbase.Subclass({
        type:"texatom",
        inferRow:true,
        texClass:a.TEXCLASS.ORD,
        setTeXclass:function(d){
            this.getPrevClass(d);
            this.data[0].setTeXclass();
            return this
        }
    });
    a.NULL=a.mbase().With({
        type:"null"
    });
    var b=a.TEXCLASS;
    var c={
        ORD:[0,0,b.ORD],
        ORD11:[1,1,b.ORD],
        ORD21:[2,1,b.ORD],
        ORD02:[0,2,b.ORD],
        ORD55:[5,5,b.ORD],
        OP:[1,2,b.OP,{
            largeop:true,
            movablelimits:true,
            symmetric:true
        }],
        OPFIXED:[1,2,b.OP,{
            largeop:true,
            movablelimits:true
        }],
        INTEGRAL:[0,1,b.OP,{
            largeop:true,
            symmetric:true
        }],
        INTEGRAL2:[1,2,b.OP,{
            largeop:true,
            symmetric:true
        }],
        BIN3:[3,3,b.BIN],
        BIN4:[4,4,b.BIN],
        BIN01:[0,1,b.BIN],
        BIN5:[5,5,b.BIN],
        TALLBIN:[4,4,b.BIN,{
            stretchy:true
        }],
        BINOP:[4,4,b.BIN,{
            largeop:true,
            movablelimits:true
        }],
        REL:[5,5,b.REL],
        REL1:[1,1,b.REL,{
            stretchy:true
        }],
        REL4:[4,4,b.REL],
        RELSTRETCH:[5,5,b.REL,{
            stretchy:true
        }],
        RELACCENT:[5,5,b.REL,{
            accent:true
        }],
        WIDEREL:[5,5,b.REL,{
            accent:true,
            stretchy:true
        }],
        OPEN:[0,0,b.OPEN,{
            fence:true,
            stretchy:true,
            symmetric:true
        }],
        CLOSE:[0,0,b.CLOSE,{
            fence:true,
            stretchy:true,
            symmetric:true
        }],
        INNER:[0,0,b.INNER],
        PUNCT:[0,3,b.PUNCT],
        ACCENT:[0,0,b.ORD,{
            accent:true
        }],
        WIDEACCENT:[0,0,b.ORD,{
            accent:true,
            stretchy:true
        }]
    };
    
    a.mo.Augment({
        SPACE:["0em","0.1111em","0.1667em","0.2222em","0.2667em","0.3333em"],
        RANGES:[[32,127,b.REL,"BasicLatin"],[160,255,b.ORD,"Latin1Supplement"],[256,383,b.ORD],[384,591,b.ORD],[688,767,b.ORD,"SpacingModLetters"],[768,879,b.ORD,"CombDiacritMarks"],[880,1023,b.ORD,"GreekAndCoptic"],[7680,7935,b.ORD],[8192,8303,b.PUNCT,"GeneralPunctuation"],[8304,8351,b.ORD],[8352,8399,b.ORD],[8400,8447,b.ORD,"CombDiactForSymbols"],[8448,8527,b.ORD,"LetterlikeSymbols"],[8528,8591,b.ORD],[8592,8703,b.REL,"Arrows"],[8704,8959,b.BIN,"MathOperators"],[8960,9215,b.ORD,"MiscTechnical"],[9312,9471,b.ORD],[9472,9631,b.ORD],[9632,9727,b.ORD,"GeometricShapes"],[9984,10175,b.ORD,"Dingbats"],[10176,10223,b.ORD,"MiscMathSymbolsA"],[10224,10239,b.REL,"SupplementalArrowsA"],[10496,10623,b.REL,"SupplementalArrowsB"],[10624,10751,b.ORD,"MiscMathSymbolsB"],[10752,11007,b.BIN,"SuppMathOperators"],[11008,11263,b.ORD,"MiscSymbolsAndArrows"],[119808,120831,b.ORD]],
        OPTABLE:{
            prefix:{
                "\u2200":c.ORD21,
                "\u2202":c.ORD21,
                "\u2203":c.ORD21,
                "\u2207":c.ORD21,
                "\u220F":c.OP,
                "\u2210":c.OP,
                "\u2211":c.OP,
                "\u2212":c.BIN01,
                "\u2213":c.BIN01,
                "\u221A":[1,1,b.ORD,{
                    stretchy:true
                }],
                "\u2220":c.ORD,
                "\u222B":c.INTEGRAL,
                "\u222E":c.INTEGRAL,
                "\u22C0":c.OP,
                "\u22C1":c.OP,
                "\u22C2":c.OP,
                "\u22C3":c.OP,
                "\u2308":c.OPEN,
                "\u230A":c.OPEN,
                "\u27E8":c.OPEN,
                "\u27EE":c.OPEN,
                "\u2A00":c.OP,
                "\u2A01":c.OP,
                "\u2A02":c.OP,
                "\u2A04":c.OP,
                "\u2A06":c.OP,
                "\u00AC":c.ORD21,
                "\u00B1":c.BIN01,
                "(":c.OPEN,
                "+":c.BIN01,
                "-":c.BIN01,
                "[":c.OPEN,
                "{":c.OPEN,
                "|":c.OPEN
            },
            postfix:{
                "!":[1,0,b.CLOSE],
                "&":c.ORD,
                "\u2032":c.ORD02,
                "\u203E":c.WIDEACCENT,
                "\u2309":c.CLOSE,
                "\u230B":c.CLOSE,
                "\u23DE":c.WIDEACCENT,
                "\u23DF":c.WIDEACCENT,
                "\u266D":c.ORD02,
                "\u266E":c.ORD02,
                "\u266F":c.ORD02,
                "\u27E9":c.CLOSE,
                "\u27EF":c.CLOSE,
                "\u02C6":c.WIDEACCENT,
                "\u02C7":c.WIDEACCENT,
                "\u02C9":c.WIDEACCENT,
                "\u02CA":c.ACCENT,
                "\u02CB":c.ACCENT,
                "\u02D8":c.ACCENT,
                "\u02D9":c.ACCENT,
                "\u02DC":c.WIDEACCENT,
                "\u0302":c.WIDEACCENT,
                "\u00A8":c.ACCENT,
                "\u00AF":c.WIDEACCENT,
                ")":c.CLOSE,
                "]":c.CLOSE,
                "^":c.WIDEACCENT,
                _:c.WIDEACCENT,
                "`":c.ACCENT,
                "|":c.CLOSE,
                "}":c.CLOSE,
                "~":c.WIDEACCENT
            },
            infix:{
                "":c.ORD,
                "%":[3,3,b.ORD],
                "\u2022":c.BIN4,
                "\u2026":c.INNER,
                "\u2044":c.TALLBIN,
                "\u2061":c.ORD,
                "\u2062":c.ORD,
                "\u2063":[0,0,b.ORD,{
                    linebreakstyle:"after",
                    separator:true
                }],
                "\u2064":c.ORD,
                "\u2190":c.WIDEREL,
                "\u2191":c.RELSTRETCH,
                "\u2192":c.WIDEREL,
                "\u2193":c.RELSTRETCH,
                "\u2194":c.WIDEREL,
                "\u2195":c.RELSTRETCH,
                "\u2196":c.RELSTRETCH,
                "\u2197":c.RELSTRETCH,
                "\u2198":c.RELSTRETCH,
                "\u2199":c.RELSTRETCH,
                "\u21A6":c.WIDEREL,
                "\u21A9":c.WIDEREL,
                "\u21AA":c.WIDEREL,
                "\u21BC":c.WIDEREL,
                "\u21BD":c.WIDEREL,
                "\u21C0":c.WIDEREL,
                "\u21C1":c.WIDEREL,
                "\u21CC":c.WIDEREL,
                "\u21D0":c.WIDEREL,
                "\u21D1":c.RELSTRETCH,
                "\u21D2":c.WIDEREL,
                "\u21D3":c.RELSTRETCH,
                "\u21D4":c.WIDEREL,
                "\u21D5":c.RELSTRETCH,
                "\u2208":c.REL,
                "\u2209":c.REL,
                "\u220B":c.REL,
                "\u2212":c.BIN4,
                "\u2213":c.BIN4,
                "\u2215":c.TALLBIN,
                "\u2216":c.BIN4,
                "\u2217":c.BIN4,
                "\u2218":c.BIN4,
                "\u2219":c.BIN4,
                "\u221D":c.REL,
                "\u2223":c.REL,
                "\u2225":c.REL,
                "\u2227":c.BIN4,
                "\u2228":c.BIN4,
                "\u2229":c.BIN4,
                "\u222A":c.BIN4,
                "\u223C":c.REL,
                "\u2240":c.BIN4,
                "\u2243":c.REL,
                "\u2245":c.REL,
                "\u2248":c.REL,
                "\u224D":c.REL,
                "\u2250":c.REL,
                "\u2260":c.REL,
                "\u2261":c.REL,
                "\u2264":c.REL,
                "\u2265":c.REL,
                "\u226A":c.REL,
                "\u226B":c.REL,
                "\u227A":c.REL,
                "\u227B":c.REL,
                "\u2282":c.REL,
                "\u2283":c.REL,
                "\u2286":c.REL,
                "\u2287":c.REL,
                "\u228E":c.BIN4,
                "\u2291":c.REL,
                "\u2292":c.REL,
                "\u2293":c.BIN4,
                "\u2294":c.BIN4,
                "\u2295":c.BIN4,
                "\u2296":c.BIN4,
                "\u2297":c.BIN4,
                "\u2298":c.BIN4,
                "\u2299":c.BIN4,
                "\u22A2":c.REL,
                "\u22A3":c.REL,
                "\u22A4":c.ORD55,
                "\u22A5":c.REL,
                "\u22A8":c.REL,
                "\u22C4":c.BIN4,
                "\u22C5":c.BIN4,
                "\u22C6":c.BIN4,
                "\u22C8":c.REL,
                "\u22EE":c.ORD55,
                "\u22EF":c.INNER,
                "\u22F1":[5,5,b.INNER],
                "\u25B3":c.BIN4,
                "\u25B5":c.BIN4,
                "\u25B9":c.BIN4,
                "\u25BD":c.BIN4,
                "\u25BF":c.BIN4,
                "\u25C3":c.BIN4,
                "\u2758":c.REL,
                "\u27F5":c.WIDEREL,
                "\u27F6":c.WIDEREL,
                "\u27F7":c.WIDEREL,
                "\u27F8":c.WIDEREL,
                "\u27F9":c.WIDEREL,
                "\u27FA":c.WIDEREL,
                "\u27FC":c.WIDEREL,
                "\u2A2F":c.BIN4,
                "\u2A3F":c.BIN4,
                "\u2AAF":c.REL,
                "\u2AB0":c.REL,
                "\u00B1":c.BIN4,
                "\u00B7":c.BIN4,
                "\u00D7":c.BIN4,
                "\u00F7":c.BIN4,
                "*":c.BIN3,
                "+":c.BIN4,
                ",":[0,3,b.PUNCT,{
                    linebreakstyle:"after",
                    separator:true
                }],
                "-":c.BIN4,
                ".":[3,3,b.ORD],
                "/":c.ORD11,
                ":":[1,2,b.REL],
                ";":[0,3,b.PUNCT,{
                    linebreakstyle:"after",
                    separator:true
                }],
                "<":c.REL,
                "=":c.REL,
                ">":c.REL,
                "?":[1,1,b.CLOSE],
                "\\":c.ORD,
                "^":c.ORD11,
                _:c.ORD11,
                "|":[2,2,b.ORD,{
                    fence:true,
                    stretchy:true,
                    symmetric:true
                }],
                "#":c.ORD,
                "$":c.ORD,
                "\u002E":[0,3,b.PUNCT,{
                    separator:true
                }],
                "\u02B9":c.ORD,
                "\u0300":c.ACCENT,
                "\u0301":c.ACCENT,
                "\u0303":c.WIDEACCENT,
                "\u0304":c.ACCENT,
                "\u0306":c.ACCENT,
                "\u0307":c.ACCENT,
                "\u0308":c.ACCENT,
                "\u030C":c.ACCENT,
                "\u0332":c.WIDEACCENT,
                "\u0338":c.REL4,
                "\u2015":[0,0,b.ORD,{
                    stretchy:true
                }],
                "\u2017":[0,0,b.ORD,{
                    stretchy:true
                }],
                "\u2020":c.BIN3,
                "\u2021":c.BIN3,
                "\u20D7":c.ACCENT,
                "\u2111":c.ORD,
                "\u2113":c.ORD,
                "\u2118":c.ORD,
                "\u211C":c.ORD,
                "\u2205":c.ORD,
                "\u221E":c.ORD,
                "\u2305":c.BIN3,
                "\u2306":c.BIN3,
                "\u2322":c.REL4,
                "\u2323":c.REL4,
                "\u2329":c.OPEN,
                "\u232A":c.CLOSE,
                "\u23AA":c.ORD,
                "\u23AF":[0,0,b.ORD,{
                    stretchy:true
                }],
                "\u23B0":c.OPEN,
                "\u23B1":c.CLOSE,
                "\u2500":c.ORD,
                "\u25EF":c.BIN3,
                "\u2660":c.ORD,
                "\u2661":c.ORD,
                "\u2662":c.ORD,
                "\u2663":c.ORD,
                "\u3008":c.OPEN,
                "\u3009":c.CLOSE,
                "\uFE37":c.WIDEACCENT,
                "\uFE38":c.WIDEACCENT
            }
        }
    },{
        OPTYPES:c
    });
    a.mo.prototype.OPTABLE.infix["^"]=c.WIDEREL;
    a.mo.prototype.OPTABLE.infix._=c.WIDEREL
})(MathJax.ElementJax.mml);
MathJax.ElementJax.mml.loadComplete("jax.js");

MathJax.Hub.Register.LoadHook("[MathJax]/jax/element/mml/jax.js",function(){
    var b="2.0";
    var a=MathJax.ElementJax.mml;
    SETTINGS=MathJax.Hub.config.menuSettings;
    a.mbase.Augment({
        toMathML:function(k){
            var g=(this.inferred&&this.parent.inferRow);
            if(k==null){
                k=""
            }
            var e=this.type,d=this.toMathMLattributes();
            if(e==="mspace"){
                return k+"<"+e+d+" />"
            }
            var j=[];
            var h=(this.isToken?"":k+(g?"":"  "));
            for(var f=0,c=this.data.length;f<c;f++){
                if(this.data[f]){
                    j.push(this.data[f].toMathML(h))
                }else{
                    if(!this.isToken){
                        j.push(h+"<mrow />")
                    }
                }
            }
            if(this.isToken){
                return k+"<"+e+d+">"+j.join("")+"</"+e+">"
            }
            if(g){
                return j.join("\n")
            }
            if(j.length===0||(j.length===1&&j[0]==="")){
                return k+"<"+e+d+" />"
            }
            return k+"<"+e+d+">\n"+j.join("\n")+"\n"+k+"</"+e+">"
        },
        toMathMLattributes:function(){
            var j=[],g=this.defaults;
            var c=(this.attrNames||a.copyAttributeNames),l=a.skipAttributes;
            if(this.type==="math"){
                j.push('xmlns="http://www.w3.org/1998/Math/MathML"')
            }
            if(!this.attrNames){
                if(this.type==="mstyle"){
                    g=a.math.prototype.defaults
                }
                for(var d in g){
                    if(!l[d]&&g.hasOwnProperty(d)){
                        var e=(d==="open"||d==="close");
                        if(this[d]!=null&&(e||this[d]!==g[d])){
                            var k=this[d];
                            delete this[d];
                            if(e||this.Get(d)!==k){
                                j.push(d+'="'+this.toMathMLattribute(k)+'"')
                            }
                            this[d]=k
                        }
                    }
                }
            }
            for(var h=0,f=c.length;h<f;h++){
                if(c[h]==="class"){
                    continue
                }
                k=(this.attr||{})[c[h]];
                if(k==null){
                    k=this[c[h]]
                }
                if(k!=null){
                    j.push(c[h]+'="'+this.toMathMLquote(k)+'"')
                }
            }
            this.toMathMLclass(j);
            if(j.length){
                return" "+j.join(" ")
            }else{
                return""
            }
        },
        toMathMLclass:function(c){
            var e=[];
            if(this["class"]){
                e.push(this["class"])
            }
            if(this.isa(a.TeXAtom)&&SETTINGS.texHints){
                var d=["ORD","OP","BIN","REL","OPEN","CLOSE","PUNCT","INNER","VCENTER"][this.texClass];
                if(d){
                    e.push("MJX-TeXAtom-"+d)
                }
            }
            if(this.mathvariant&&this.toMathMLvariants[this.mathvariant]){
                e.push("MJX"+this.mathvariant)
            }
            if(this.arrow){
                e.push("MJX-arrow")
            }
            if(this.variantForm){
                e.push("MJX-variant")
            }
            if(e.length){
                c.unshift('class="'+e.join(" ")+'"')
            }
        },
        toMathMLattribute:function(c){
            if(typeof(c)==="string"&&c.replace(/ /g,"").match(/^(([-+])?(\d+(\.\d*)?|\.\d+))mu$/)){
                return((1/18)*RegExp.$1).toFixed(3).replace(/\.?0+$/,"")+"em"
            }else{
                if(this.toMathMLvariants[c]){
                    return this.toMathMLvariants[c]
                }
            }
            return this.toMathMLquote(c)
        },
        toMathMLvariants:{
            "-tex-caligraphic":a.VARIANT.SCRIPT,
            "-tex-caligraphic-bold":a.VARIANT.BOLDSCRIPT,
            "-tex-oldstyle":a.VARIANT.NORMAL,
            "-tex-oldstyle-bold":a.VARIANT.BOLD,
            "-tex-mathit":a.VARIANT.ITALIC
        },
        toMathMLquote:function(e){
            e=String(e).split("");
            for(var f=0,d=e.length;f<d;f++){
                var h=e[f].charCodeAt(0);
                if(h<32||h>126){
                    e[f]="&#x"+h.toString(16).toUpperCase()+";"
                }else{
                    var g={
                        "&":"&amp;",
                        "<":"&lt;",
                        ">":"&gt;",
                        '"':"&quot;"
                    }
                    [e[f]];
                    if(g){
                        e[f]=g
                    }
                }
            }
            return e.join("")
        }
    });
    a.msubsup.Augment({
        toMathML:function(h){
            var e=this.type;
            if(this.data[this.sup]==null){
                e="msub"
            }
            if(this.data[this.sub]==null){
                e="msup"
            }
            var d=this.toMathMLattributes();
            delete this.data[0].inferred;
            var g=[];
            for(var f=0,c=this.data.length;f<c;f++){
                if(this.data[f]){
                    g.push(this.data[f].toMathML(h+"  "))
                }
            }
            return h+"<"+e+d+">\n"+g.join("\n")+"\n"+h+"</"+e+">"
        }
    });
    a.munderover.Augment({
        toMathML:function(h){
            var e=this.type;
            if(this.data[this.under]==null){
                e="mover"
            }
            if(this.data[this.over]==null){
                e="munder"
            }
            var d=this.toMathMLattributes();
            delete this.data[0].inferred;
            var g=[];
            for(var f=0,c=this.data.length;f<c;f++){
                if(this.data[f]){
                    g.push(this.data[f].toMathML(h+"  "))
                }
            }
            return h+"<"+e+d+">\n"+g.join("\n")+"\n"+h+"</"+e+">"
        }
    });
    a.TeXAtom.Augment({
        toMathML:function(d){
            var c=this.toMathMLattributes();
            if(!c&&this.data[0].data.length===1){
                return d.substr(2)+this.data[0].toMathML(d)
            }
            return d+"<mrow"+c+">\n"+this.data[0].toMathML(d+"  ")+"\n"+d+"</mrow>"
        }
    });
    a.chars.Augment({
        toMathML:function(c){
            return(c||"")+this.toMathMLquote(this.toString())
        }
    });
    a.entity.Augment({
        toMathML:function(c){
            return(c||"")+"&"+this.data[0]+";<!-- "+this.toString()+" -->"
        }
    });
    a.xml.Augment({
        toMathML:function(c){
            return(c||"")+this.toString()
        }
    });
    MathJax.Hub.Register.StartupHook("TeX mathchoice Ready",function(){
        a.TeXmathchoice.Augment({
            toMathML:function(c){
                return this.Core().toMathML(c)
            }
        })
    });
    MathJax.Hub.Startup.signal.Post("toMathML Ready")
});
MathJax.Ajax.loadComplete("[MathJax]/extensions/toMathML.js");

(function(b,e){
    var d="2.0";
    var a=b.CombineConfig("TeX.noErrors",{
        disabled:false,
        multiLine:true,
        inlineDelimiters:["",""],
        style:{
            "font-size":"90%",
            "text-align":"left",
            color:"black",
            padding:"1px 3px",
            border:"1px solid"
        }
    });
    var c="\u00A0";
    MathJax.Extension["TeX/noErrors"]={
        version:d,
        config:a
    };

    b.Register.StartupHook("TeX Jax Ready",function(){
        var f=MathJax.InputJax.TeX.formatError;
        MathJax.InputJax.TeX.Augment({
            formatError:function(j,i,k,g){
                if(a.disabled){
                    return f.apply(this,arguments)
                }
                var h=j.message.replace(/\n.*/,"");
                b.signal.Post(["TeX Jax - parse error",h,i,k,g]);
                var m=a.inlineDelimiters;
                var l=(k||a.multiLine);
                if(!k){
                    i=m[0]+i+m[1]
                }
                if(l){
                    i=i.replace(/ /g,c)
                }else{
                    i=i.replace(/\n/g," ")
                }
                return MathJax.ElementJax.mml.merror(i).With({
                    isError:true,
                    multiLine:l
                })
            }
        })
    });
    b.Register.StartupHook("HTML-CSS Jax Config",function(){
        b.Config({
            "HTML-CSS":{
                styles:{
                    ".MathJax .noError":b.Insert({
                        "vertical-align":(b.Browser.isMSIE&&a.multiLine?"-2px":"")
                    },a.style)
                }
            }
        })
    });
    b.Register.StartupHook("HTML-CSS Jax Ready",function(){
        var g=MathJax.ElementJax.mml;
        var h=MathJax.OutputJax["HTML-CSS"];
        var f=g.math.prototype.toHTML,i=g.merror.prototype.toHTML;
        g.math.Augment({
            toHTML:function(j,k){
                var l=this.data[0];
                if(l&&l.data[0]&&l.data[0].isError){
                    j.style.fontSize="";
                    j=this.HTMLcreateSpan(j);
                    j.bbox=l.data[0].toHTML(j).bbox
                }else{
                    j=f.call(this,j,k)
                }
                return j
            }
        });
        g.merror.Augment({
            toHTML:function(p){
                if(!this.isError){
                    return i.call(this,p)
                }
                p=this.HTMLcreateSpan(p);
                p.className="noError";
                if(this.multiLine){
                    p.style.display="inline-block"
                }
                var r=this.data[0].data[0].data.join("").split(/\n/);
                for(var o=0,l=r.length;o<l;o++){
                    h.addText(p,r[o]);
                    if(o!==l-1){
                        h.addElement(p,"br",{
                            isMathJax:true
                        })
                    }
                }
                var q=h.getHD(p.parentNode),k=h.getW(p.parentNode);
                if(l>1){
                    var n=(q.h+q.d)/2,j=h.TeX.x_height/2;
                    p.parentNode.style.verticalAlign=h.Em(q.d+(j-n));
                    q.h=j+n;
                    q.d=n-j
                }
                p.bbox={
                    h:q.h,
                    d:q.d,
                    w:k,
                    lw:0,
                    rw:k
                };
    
                return p
            }
        })
    });
    b.Register.StartupHook("SVG Jax Config",function(){
        b.Config({
            SVG:{
                styles:{
                    ".MathJax_SVG .noError":b.Insert({
                        "vertical-align":(b.Browser.isMSIE&&a.multiLine?"-2px":"")
                    },a.style)
                }
            }
        })
    });
    b.Register.StartupHook("SVG Jax Ready",function(){
        var g=MathJax.ElementJax.mml;
        var f=g.math.prototype.toSVG,h=g.merror.prototype.toSVG;
        g.math.Augment({
            toSVG:function(i,j){
                var k=this.data[0];
                if(k&&k.data[0]&&k.data[0].isError){
                    i=k.data[0].toSVG(i)
                }else{
                    i=f.call(this,i,j)
                }
                return i
            }
        });
        g.merror.Augment({
            toSVG:function(n){
                if(!this.isError||this.Parent().type!=="math"){
                    return h.call(this,n)
                }
                n=e.addElement(n,"span",{
                    className:"noError",
                    isMathJax:true
                });
                if(this.multiLine){
                    n.style.display="inline-block"
                }
                var o=this.data[0].data[0].data.join("").split(/\n/);
                for(var l=0,j=o.length;l<j;l++){
                    e.addText(n,o[l]);
                    if(l!==j-1){
                        e.addElement(n,"br",{
                            isMathJax:true
                        })
                    }
                }
                if(j>1){
                    var k=n.offsetHeight/2;
                    n.style.verticalAlign=(-k+(k/j))+"px"
                }
                return n
            }
        })
    });
    b.Register.StartupHook("NativeMML Jax Ready",function(){
        var h=MathJax.ElementJax.mml;
        var g=MathJax.Extension["TeX/noErrors"].config;
        var f=h.math.prototype.toNativeMML,i=h.merror.prototype.toNativeMML;
        h.math.Augment({
            toNativeMML:function(j){
                var k=this.data[0];
                if(k&&k.data[0]&&k.data[0].isError){
                    j=k.data[0].toNativeMML(j)
                }else{
                    j=f.call(this,j)
                }
                return j
            }
        });
        h.merror.Augment({
            toNativeMML:function(n){
                if(!this.isError){
                    return i.call(this,n)
                }
                n=n.appendChild(document.createElement("span"));
                var o=this.data[0].data[0].data.join("").split(/\n/);
                for(var l=0,k=o.length;l<k;l++){
                    n.appendChild(document.createTextNode(o[l]));
                    if(l!==k-1){
                        n.appendChild(document.createElement("br"))
                    }
                }
                if(this.multiLine){
                    n.style.display="inline-block";
                    if(k>1){
                        n.style.verticalAlign="middle"
                    }
                }
                for(var p in g.style){
                    if(g.style.hasOwnProperty(p)){
                        var j=p.replace(/-./g,function(m){
                            return m.charAt(1).toUpperCase()
                        });
                        n.style[j]=g.style[p]
                    }
                }
                return n
            }
        })
    });
    b.Startup.signal.Post("TeX noErrors Ready")
})(MathJax.Hub,MathJax.HTML);
MathJax.Ajax.loadComplete("[MathJax]/extensions/TeX/noErrors.js");

MathJax.Extension["TeX/noUndefined"]={
    version:"2.0",
    config:MathJax.Hub.CombineConfig("TeX.noUndefined",{
        disabled:false,
        attributes:{
            mathcolor:"red"
        }
    })
};

MathJax.Hub.Register.StartupHook("TeX Jax Ready",function(){
    var b=MathJax.Extension["TeX/noUndefined"].config;
    var a=MathJax.ElementJax.mml;
    var c=MathJax.InputJax.TeX.Parse.prototype.csUndefined;
    MathJax.InputJax.TeX.Parse.Augment({
        csUndefined:function(d){
            if(b.disabled){
                return c.apply(this,arguments)
            }
            MathJax.Hub.signal.Post(["TeX Jax - undefined control sequence",d]);
            this.Push(a.mtext(d).With(b.attributes))
        }
    });
    MathJax.Hub.Startup.signal.Post("TeX noUndefined Ready")
});
MathJax.Ajax.loadComplete("[MathJax]/extensions/TeX/noUndefined.js");

(function(d,c,i){
    var h,g="\u00A0";
    var e=MathJax.Object.Subclass({
        Init:function(l,k){
            this.global={
                isInner:k
            };
            
            this.data=[b.start(this.global)];
            if(l){
                this.data[0].env=l
            }
            this.env=this.data[0].env
        },
        Push:function(){
            var l,k,n,o;
            for(l=0,k=arguments.length;l<k;l++){
                n=arguments[l];
                if(n instanceof h.mbase){
                    n=b.mml(n)
                }
                n.global=this.global;
                o=(this.data.length?this.Top().checkItem(n):true);
                if(o instanceof Array){
                    this.Pop();
                    this.Push.apply(this,o)
                }else{
                    if(o instanceof b){
                        this.Pop();
                        this.Push(o)
                    }else{
                        if(o){
                            this.data.push(n);
                            if(n.env){
                                for(var p in this.env){
                                    if(this.env.hasOwnProperty(p)){
                                        n.env[p]=this.env[p]
                                    }
                                }
                                this.env=n.env
                            }else{
                                n.env=this.env
                            }
                        }
                    }
                }
            }
        },
        Pop:function(){
            var k=this.data.pop();
            if(!k.isOpen){
                delete k.env
            }
            this.env=(this.data.length?this.Top().env:{});
            return k
        },
        Top:function(k){
            if(k==null){
                k=1
            }
            if(this.data.length<k){
                return null
            }
            return this.data[this.data.length-k]
        },
        Prev:function(k){
            var l=this.Top();
            if(k){
                return l.data[l.data.length-1]
            }else{
                return l.Pop()
            }
        },
        toString:function(){
            return"stack[\n  "+this.data.join("\n  ")+"\n]"
        }
    });
    var b=e.Item=MathJax.Object.Subclass({
        type:"base",
        closeError:"Extra close brace or missing open brace",
        rightError:"Missing \\left or extra \\right",
        Init:function(){
            if(this.isOpen){
                this.env={}
            }
            this.data=[];
            this.Push.apply(this,arguments)
        },
        Push:function(){
            this.data.push.apply(this.data,arguments)
        },
        Pop:function(){
            return this.data.pop()
        },
        mmlData:function(k,l){
            if(k==null){
                k=true
            }
            if(this.data.length===1&&!l){
                return this.data[0]
            }
            return h.mrow.apply(h,this.data).With((k?{
                inferred:true
            }:{}))
        },
        checkItem:function(k){
            if(k.type==="over"&&this.isOpen){
                k.num=this.mmlData(false);
                this.data=[]
            }
            if(k.type==="cell"&&this.isOpen){
                if(k.linebreak){
                    return false
                }
                d.Error("Misplaced "+k.name)
            }
            if(k.isClose&&this[k.type+"Error"]){
                d.Error(this[k.type+"Error"])
            }
            if(!k.isNotStack){
                return true
            }
            this.Push(k.data[0]);
            return false
        },
        With:function(k){
            for(var l in k){
                if(k.hasOwnProperty(l)){
                    this[l]=k[l]
                }
            }
            return this
        },
        toString:function(){
            return this.type+"["+this.data.join("; ")+"]"
        }
    });
    b.start=b.Subclass({
        type:"start",
        isOpen:true,
        Init:function(k){
            this.SUPER(arguments).Init.call(this);
            this.global=k
        },
        checkItem:function(k){
            if(k.type==="stop"){
                return b.mml(this.mmlData())
            }
            return this.SUPER(arguments).checkItem.call(this,k)
        }
    });
    b.stop=b.Subclass({
        type:"stop",
        isClose:true
    });
    b.open=b.Subclass({
        type:"open",
        isOpen:true,
        stopError:"Extra open brace or missing close brace",
        checkItem:function(l){
            if(l.type==="close"){
                var k=this.mmlData();
                return b.mml(h.TeXAtom(k))
            }
            return this.SUPER(arguments).checkItem.call(this,l)
        }
    });
    b.close=b.Subclass({
        type:"close",
        isClose:true
    });
    b.subsup=b.Subclass({
        type:"subsup",
        stopError:"Missing superscript or subscript argument",
        checkItem:function(l){
            var k=["","subscript","superscript"][this.position];
            if(l.type==="open"||l.type==="left"){
                return true
            }
            if(l.type==="mml"){
                this.data[0].SetData(this.position,l.data[0]);
                return b.mml(this.data[0])
            }
            if(this.SUPER(arguments).checkItem.call(this,l)){
                d.Error("Missing open brace for "+k)
            }
        },
        Pop:function(){}
    });
    b.over=b.Subclass({
        type:"over",
        isClose:true,
        name:"\\over",
        checkItem:function(m,k){
            if(m.type==="over"){
                d.Error("Ambiguous use of "+m.name)
            }
            if(m.isClose){
                var l=h.mfrac(this.num,this.mmlData(false));
                if(this.thickness!=null){
                    l.linethickness=this.thickness
                }
                if(this.open||this.close){
                    l.texClass=h.TEXCLASS.INNER;
                    l.texWithDelims=true;
                    l=h.mfenced(l).With({
                        open:this.open,
                        close:this.close
                    })
                }
                return[b.mml(l),m]
            }
            return this.SUPER(arguments).checkItem.call(this,m)
        },
        toString:function(){
            return"over["+this.num+" / "+this.data.join("; ")+"]"
        }
    });
    b.left=b.Subclass({
        type:"left",
        isOpen:true,
        delim:"(",
        stopError:"Extra \\left or missing \\right",
        checkItem:function(l){
            if(l.type==="right"){
                var k=h.mfenced(this.data.length===1?this.data[0]:h.mrow.apply(h,this.data));
                return b.mml(k.With({
                    open:this.delim,
                    close:l.delim
                }))
            }
            return this.SUPER(arguments).checkItem.call(this,l)
        }
    });
    b.right=b.Subclass({
        type:"right",
        isClose:true,
        delim:")"
    });
    b.begin=b.Subclass({
        type:"begin",
        isOpen:true,
        checkItem:function(k){
            if(k.type==="end"){
                if(k.name!==this.name){
                    d.Error("\\begin{"+this.name+"} ended with \\end{"+k.name+"}")
                }
                if(!this.end){
                    return b.mml(this.mmlData())
                }
                return this.parse[this.end].call(this.parse,this,this.data)
            }
            if(k.type==="stop"){
                d.Error("Missing \\end{"+this.name+"}")
            }
            return this.SUPER(arguments).checkItem.call(this,k)
        }
    });
    b.end=b.Subclass({
        type:"end",
        isClose:true
    });
    b.style=b.Subclass({
        type:"style",
        checkItem:function(l){
            if(!l.isClose){
                return this.SUPER(arguments).checkItem.call(this,l)
            }
            var k=h.mstyle.apply(h,this.data).With(this.styles);
            return[b.mml(k),l]
        }
    });
    b.position=b.Subclass({
        type:"position",
        checkItem:function(l){
            if(l.isClose){
                d.Error("Missing box for "+this.name)
            }
            if(l.isNotStack){
                var k=l.mmlData();
                switch(this.move){
                    case"vertical":
                        k=h.mpadded(k).With({
                            height:this.dh,
                            depth:this.dd,
                            voffset:this.dh
                        });
                        return[b.mml(k)];
                    case"horizontal":
                        return[b.mml(this.left),l,b.mml(this.right)]
                }
            }
            return this.SUPER(arguments).checkItem.call(this,l)
        }
    });
    b.array=b.Subclass({
        type:"array",
        isOpen:true,
        arraydef:{},
        Init:function(){
            this.table=[];
            this.row=[];
            this.env={};
        
            this.frame=[];
            this.SUPER(arguments).Init.apply(this,arguments)
        },
        checkItem:function(l){
            if(l.isClose&&l.type!=="over"){
                if(l.isEntry){
                    this.EndEntry();
                    this.clearEnv();
                    return false
                }
                if(l.isCR){
                    this.EndEntry();
                    this.EndRow();
                    this.clearEnv();
                    return false
                }
                this.EndTable();
                this.clearEnv();
                var k=h.mtable.apply(h,this.table).With(this.arraydef);
                if(this.frame.length===4){
                    k.frame=(this.frame.dashed?"dashed":"solid")
                }else{
                    if(this.frame.length){
                        k.hasFrame=true;
                        if(this.arraydef.rowlines){
                            this.arraydef.rowlines=this.arraydef.rowlines.replace(/none( none)+$/,"none")
                        }
                        k=h.menclose(k).With({
                            notation:this.frame.join(" "),
                            isFrame:true
                        });
                        if((this.arraydef.columnlines||"none")!="none"||(this.arraydef.rowlines||"none")!="none"){
                            k.padding=0
                        }
                    }
                }
                if(this.open||this.close){
                    k=h.mfenced(k).With({
                        open:this.open,
                        close:this.close
                    })
                }
                k=b.mml(k);
                if(this.requireClose){
                    if(l.type==="close"){
                        return k
                    }
                    d.Error("Missing close brace")
                }
                return[k,l]
            }
            return this.SUPER(arguments).checkItem.call(this,l)
        },
        EndEntry:function(){
            this.row.push(h.mtd.apply(h,this.data));
            this.data=[]
        },
        EndRow:function(){
            this.table.push(h.mtr.apply(h,this.row));
            this.row=[]
        },
        EndTable:function(){
            if(this.data.length||this.row.length){
                this.EndEntry();
                this.EndRow()
            }
            this.checkLines()
        },
        checkLines:function(){
            if(this.arraydef.rowlines){
                var k=this.arraydef.rowlines.split(/ /);
                if(k.length===this.table.length){
                    this.frame.push("bottom");
                    k.pop();
                    this.arraydef.rowlines=k.join(" ")
                }else{
                    if(k.length<this.table.length-1){
                        this.arraydef.rowlines+=" none"
                    }
                }
            }
            if(this.rowspacing){
                var l=this.arraydef.rowspacing.split(/ /);
                while(l.length<this.table.length){
                    l.push(this.rowspacing)
                }
                this.arraydef.rowspacing=l.join(" ")
            }
        },
        clearEnv:function(){
            for(var k in this.env){
                if(this.env.hasOwnProperty(k)){
                    delete this.env[k]
                }
            }
        }
    });
    b.cell=b.Subclass({
        type:"cell",
        isClose:true
    });
    b.mml=b.Subclass({
        type:"mml",
        isNotStack:true,
        Add:function(){
            this.data.push.apply(this.data,arguments);
            return this
        }
    });
    b.fn=b.Subclass({
        type:"fn",
        checkItem:function(l){
            if(this.data[0]){
                if(l.type!=="mml"||!l.data[0]){
                    return[this.data[0],l]
                }
                if(l.data[0].isa(h.mspace)){
                    return[this.data[0],l]
                }
                var k=l.data[0];
                if(k.isEmbellished()){
                    k=k.CoreMO()
                }
                if([0,0,1,1,0,1,1,0,0,0][k.Get("texClass")]){
                    return[this.data[0],l]
                }
                return[this.data[0],h.mo(h.entity("#x2061")).With({
                    texClass:h.TEXCLASS.NONE
                }),l]
            }
            return this.SUPER(arguments).checkItem.apply(this,arguments)
        }
    });
    b.not=b.Subclass({
        type:"not",
        checkItem:function(l){
            var k,m;
            if(l.type==="open"||l.type==="left"){
                return true
            }
            if(l.type==="mml"&&l.data[0].type.match(/^(mo|mi|mtext)$/)){
                k=l.data[0],m=k.data.join("");
                if(m.length===1&&!k.movesupsub){
                    m=b.not.remap[m.charCodeAt(0)];
                    if(m){
                        k.SetData(0,h.chars(String.fromCharCode(m)))
                    }else{
                        k.Append(h.chars("\u0338"))
                    }
                    return l
                }
            }
            k=h.mpadded(h.mtext("\u29F8")).With({
                width:0
            });
            k=h.TeXAtom(k).With({
                texClass:h.TEXCLASS.REL
            });
            return[k,l]
        }
    });
    b.not.remap={
        8712:8713,
        8715:8716,
        8739:8740,
        8741:8742,
        8764:8769,
        126:8769,
        8771:8772,
        8773:8775,
        8776:8777,
        61:8800,
        8801:8802,
        60:8814,
        62:8815,
        8804:8816,
        8805:8817,
        8818:8820,
        8819:8821,
        8822:8824,
        8823:8825,
        8826:8832,
        8827:8833,
        8834:8836,
        8835:8837,
        8838:8840,
        8839:8841,
        8866:8876,
        8872:8877,
        8873:8878,
        8875:8879,
        8828:8928,
        8829:8929,
        8849:8930,
        8850:8931,
        8882:8938,
        8883:8939,
        8884:8940,
        8885:8941,
        8707:8708
    };

    b.dots=b.Subclass({
        type:"dots",
        checkItem:function(l){
            if(l.type==="open"||l.type==="left"){
                return true
            }
            var m=this.ldots;
            if(l.type==="mml"&&l.data[0].isEmbellished()){
                var k=l.data[0].CoreMO().Get("texClass");
                if(k===h.TEXCLASS.BIN||k===h.TEXCLASS.REL){
                    m=this.cdots
                }
            }
            return[m,l]
        }
    });
    var f={
        Add:function(k,n,m){
            if(!n){
                n=this
            }
            for(var l in k){
                if(k.hasOwnProperty(l)){
                    if(typeof k[l]==="object"&&!(k[l] instanceof Array)&&(typeof n[l]==="object"||typeof n[l]==="function")){
                        this.Add(k[l],n[l],k[l],m)
                    }else{
                        if(!n[l]||!n[l].isUser||!m){
                            n[l]=k[l]
                        }
                    }
                }
            }
            return n
        }
    };

    var j=function(){
        h=MathJax.ElementJax.mml;
        c.Insert(f,{
            letter:/[a-z]/i,
            digit:/[0-9.]/,
            number:/^(?:[0-9]+(?:\{,\}[0-9]{3})*(?:\.[0-9]*)*|\.[0-9]+)/,
            special:{
                "\\":"ControlSequence",
                "{":"Open",
                "}":"Close",
                "~":"Tilde",
                "^":"Superscript",
                _:"Subscript",
                " ":"Space",
                "\t":"Space",
                "\r":"Space",
                "\n":"Space",
                "'":"Prime",
                "%":"Comment",
                "&":"Entry",
                "#":"Hash",
                "\u2019":"Prime"
            },
            remap:{
                "-":"2212",
                "*":"2217"
            },
            mathchar0mi:{
                alpha:"03B1",
                beta:"03B2",
                gamma:"03B3",
                delta:"03B4",
                epsilon:"03F5",
                zeta:"03B6",
                eta:"03B7",
                theta:"03B8",
                iota:"03B9",
                kappa:"03BA",
                lambda:"03BB",
                mu:"03BC",
                nu:"03BD",
                xi:"03BE",
                omicron:"03BF",
                pi:"03C0",
                rho:"03C1",
                sigma:"03C3",
                tau:"03C4",
                upsilon:"03C5",
                phi:"03D5",
                chi:"03C7",
                psi:"03C8",
                omega:"03C9",
                varepsilon:"03B5",
                vartheta:"03D1",
                varpi:"03D6",
                varrho:"03F1",
                varsigma:"03C2",
                varphi:"03C6",
                S:"00A7",
                aleph:["2135",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                hbar:"210F",
                imath:"0131",
                jmath:"0237",
                ell:"2113",
                wp:["2118",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                Re:["211C",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                Im:["2111",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                partial:["2202",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                infty:["221E",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                prime:["2032",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                emptyset:["2205",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                nabla:["2207",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                top:["22A4",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                bot:["22A5",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                angle:["2220",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                triangle:["25B3",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                backslash:["2216",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                forall:["2200",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                exists:["2203",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                neg:["00AC",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                lnot:["00AC",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                flat:["266D",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                natural:["266E",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                sharp:["266F",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                clubsuit:["2663",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                diamondsuit:["2662",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                heartsuit:["2661",{
                    mathvariant:h.VARIANT.NORMAL
                }],
                spadesuit:["2660",{
                    mathvariant:h.VARIANT.NORMAL
                }]
            },
            mathchar0mo:{
                surd:"221A",
                coprod:["2210",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                bigvee:["22C1",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                bigwedge:["22C0",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                biguplus:["2A04",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                bigcap:["22C2",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                bigcup:["22C3",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                "int":["222B",{
                    texClass:h.TEXCLASS.OP
                }],
                intop:["222B",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true,
                    movablelimits:true
                }],
                iint:["222C",{
                    texClass:h.TEXCLASS.OP
                }],
                iiint:["222D",{
                    texClass:h.TEXCLASS.OP
                }],
                prod:["220F",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                sum:["2211",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                bigotimes:["2A02",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                bigoplus:["2A01",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                bigodot:["2A00",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                oint:["222E",{
                    texClass:h.TEXCLASS.OP
                }],
                bigsqcup:["2A06",{
                    texClass:h.TEXCLASS.OP,
                    movesupsub:true
                }],
                smallint:["222B",{
                    largeop:false
                }],
                triangleleft:"25C3",
                triangleright:"25B9",
                bigtriangleup:"25B3",
                bigtriangledown:"25BD",
                wedge:"2227",
                land:"2227",
                vee:"2228",
                lor:"2228",
                cap:"2229",
                cup:"222A",
                ddagger:"2021",
                dagger:"2020",
                sqcap:"2293",
                sqcup:"2294",
                uplus:"228E",
                amalg:"2A3F",
                diamond:"22C4",
                bullet:"2219",
                wr:"2240",
                div:"00F7",
                odot:["2299",{
                    largeop:false
                }],
                oslash:["2298",{
                    largeop:false
                }],
                otimes:["2297",{
                    largeop:false
                }],
                ominus:["2296",{
                    largeop:false
                }],
                oplus:["2295",{
                    largeop:false
                }],
                mp:"2213",
                pm:"00B1",
                circ:"2218",
                bigcirc:"25EF",
                setminus:"2216",
                cdot:"22C5",
                ast:"2217",
                times:"00D7",
                star:"22C6",
                propto:"221D",
                sqsubseteq:"2291",
                sqsupseteq:"2292",
                parallel:"2225",
                mid:"2223",
                dashv:"22A3",
                vdash:"22A2",
                leq:"2264",
                le:"2264",
                geq:"2265",
                ge:"2265",
                lt:"003C",
                gt:"003E",
                succ:"227B",
                prec:"227A",
                approx:"2248",
                succeq:"2AB0",
                preceq:"2AAF",
                supset:"2283",
                subset:"2282",
                supseteq:"2287",
                subseteq:"2286",
                "in":"2208",
                ni:"220B",
                notin:"2209",
                owns:"220B",
                gg:"226B",
                ll:"226A",
                sim:"223C",
                simeq:"2243",
                perp:"22A5",
                equiv:"2261",
                asymp:"224D",
                smile:"2323",
                frown:"2322",
                ne:"2260",
                neq:"2260",
                cong:"2245",
                doteq:"2250",
                bowtie:"22C8",
                models:"22A8",
                notChar:"29F8",
                Leftrightarrow:"21D4",
                Leftarrow:"21D0",
                Rightarrow:"21D2",
                leftrightarrow:"2194",
                leftarrow:"2190",
                gets:"2190",
                rightarrow:"2192",
                to:"2192",
                mapsto:"21A6",
                leftharpoonup:"21BC",
                leftharpoondown:"21BD",
                rightharpoonup:"21C0",
                rightharpoondown:"21C1",
                nearrow:"2197",
                searrow:"2198",
                nwarrow:"2196",
                swarrow:"2199",
                rightleftharpoons:"21CC",
                hookrightarrow:"21AA",
                hookleftarrow:"21A9",
                longleftarrow:"27F5",
                Longleftarrow:"27F8",
                longrightarrow:"27F6",
                Longrightarrow:"27F9",
                Longleftrightarrow:"27FA",
                longleftrightarrow:"27F7",
                longmapsto:"27FC",
                ldots:"2026",
                cdots:"22EF",
                vdots:"22EE",
                ddots:"22F1",
                dotsc:"2026",
                dotsb:"22EF",
                dotsm:"22EF",
                dotsi:"22EF",
                dotso:"2026",
                ldotp:["002E",{
                    texClass:h.TEXCLASS.PUNCT
                }],
                cdotp:["22C5",{
                    texClass:h.TEXCLASS.PUNCT
                }],
                colon:["003A",{
                    texClass:h.TEXCLASS.PUNCT
                }]
            },
            mathchar7:{
                Gamma:"0393",
                Delta:"0394",
                Theta:"0398",
                Lambda:"039B",
                Xi:"039E",
                Pi:"03A0",
                Sigma:"03A3",
                Upsilon:"03A5",
                Phi:"03A6",
                Psi:"03A8",
                Omega:"03A9",
                _:"005F",
                "#":"0023",
                "$":"0024",
                "%":"0025",
                "&":"0026",
                And:"0026"
            },
            delimiter:{
                "(":"(",
                ")":")",
                "[":"[",
                "]":"]",
                "<":"27E8",
                ">":"27E9",
                "\\lt":"27E8",
                "\\gt":"27E9",
                "/":"/",
                "|":["|",{
                    texClass:h.TEXCLASS.ORD
                }],
                ".":"",
                "\\\\":"\\",
                "\\lmoustache":"23B0",
                "\\rmoustache":"23B1",
                "\\lgroup":"27EE",
                "\\rgroup":"27EF",
                "\\arrowvert":"23D0",
                "\\Arrowvert":"2016",
                "\\bracevert":"23AA",
                "\\Vert":["2225",{
                    texClass:h.TEXCLASS.ORD
                }],
                "\\|":["2225",{
                    texClass:h.TEXCLASS.ORD
                }],
                "\\vert":["|",{
                    texClass:h.TEXCLASS.ORD
                }],
                "\\uparrow":"2191",
                "\\downarrow":"2193",
                "\\updownarrow":"2195",
                "\\Uparrow":"21D1",
                "\\Downarrow":"21D3",
                "\\Updownarrow":"21D5",
                "\\backslash":"\\",
                "\\rangle":"27E9",
                "\\langle":"27E8",
                "\\rbrace":"}",
                "\\lbrace":"{",
                "\\}":"}",
                "\\{":"{",
                "\\rceil":"2309",
                "\\lceil":"2308",
                "\\rfloor":"230B",
                "\\lfloor":"230A",
                "\\lbrack":"[",
                "\\rbrack":"]"
            },
            macros:{
                displaystyle:["SetStyle","D",true,0],
                textstyle:["SetStyle","T",false,0],
                scriptstyle:["SetStyle","S",false,1],
                scriptscriptstyle:["SetStyle","SS",false,2],
                rm:["SetFont",h.VARIANT.NORMAL],
                mit:["SetFont",h.VARIANT.ITALIC],
                oldstyle:["SetFont",h.VARIANT.OLDSTYLE],
                cal:["SetFont",h.VARIANT.CALIGRAPHIC],
                it:["SetFont","-tex-mathit"],
                bf:["SetFont",h.VARIANT.BOLD],
                bbFont:["SetFont",h.VARIANT.DOUBLESTRUCK],
                scr:["SetFont",h.VARIANT.SCRIPT],
                frak:["SetFont",h.VARIANT.FRAKTUR],
                sf:["SetFont",h.VARIANT.SANSSERIF],
                tt:["SetFont",h.VARIANT.MONOSPACE],
                tiny:["SetSize",0.5],
                Tiny:["SetSize",0.6],
                scriptsize:["SetSize",0.7],
                small:["SetSize",0.85],
                normalsize:["SetSize",1],
                large:["SetSize",1.2],
                Large:["SetSize",1.44],
                LARGE:["SetSize",1.73],
                huge:["SetSize",2.07],
                Huge:["SetSize",2.49],
                arcsin:["NamedFn"],
                arccos:["NamedFn"],
                arctan:["NamedFn"],
                arg:["NamedFn"],
                cos:["NamedFn"],
                cosh:["NamedFn"],
                cot:["NamedFn"],
                coth:["NamedFn"],
                csc:["NamedFn"],
                deg:["NamedFn"],
                det:"NamedOp",
                dim:["NamedFn"],
                exp:["NamedFn"],
                gcd:"NamedOp",
                hom:["NamedFn"],
                inf:"NamedOp",
                ker:["NamedFn"],
                lg:["NamedFn"],
                lim:"NamedOp",
                liminf:["NamedOp","lim&thinsp;inf"],
                limsup:["NamedOp","lim&thinsp;sup"],
                ln:["NamedFn"],
                log:["NamedFn"],
                max:"NamedOp",
                min:"NamedOp",
                Pr:"NamedOp",
                sec:["NamedFn"],
                sin:["NamedFn"],
                sinh:["NamedFn"],
                sup:"NamedOp",
                tan:["NamedFn"],
                tanh:["NamedFn"],
                limits:["Limits",1],
                nolimits:["Limits",0],
                overline:["UnderOver","00AF"],
                underline:["UnderOver","005F"],
                overbrace:["UnderOver","23DE",1],
                underbrace:["UnderOver","23DF",1],
                overrightarrow:["UnderOver","2192"],
                underrightarrow:["UnderOver","2192"],
                overleftarrow:["UnderOver","2190"],
                underleftarrow:["UnderOver","2190"],
                overleftrightarrow:["UnderOver","2194"],
                underleftrightarrow:["UnderOver","2194"],
                overset:"Overset",
                underset:"Underset",
                stackrel:["Macro","\\mathrel{\\mathop{#2}\\limits^{#1}}",2],
                over:"Over",
                overwithdelims:"Over",
                atop:"Over",
                atopwithdelims:"Over",
                above:"Over",
                abovewithdelims:"Over",
                brace:["Over","{","}"],
                brack:["Over","[","]"],
                choose:["Over","(",")"],
                frac:"Frac",
                sqrt:"Sqrt",
                root:"Root",
                uproot:["MoveRoot","upRoot"],
                leftroot:["MoveRoot","leftRoot"],
                left:"LeftRight",
                right:"LeftRight",
                middle:"Middle",
                llap:"Lap",
                rlap:"Lap",
                raise:"RaiseLower",
                lower:"RaiseLower",
                moveleft:"MoveLeftRight",
                moveright:"MoveLeftRight",
                ",":["Spacer",h.LENGTH.THINMATHSPACE],
                ":":["Spacer",h.LENGTH.MEDIUMMATHSPACE],
                ">":["Spacer",h.LENGTH.MEDIUMMATHSPACE],
                ";":["Spacer",h.LENGTH.THICKMATHSPACE],
                "!":["Spacer",h.LENGTH.NEGATIVETHINMATHSPACE],
                enspace:["Spacer",".5em"],
                quad:["Spacer","1em"],
                qquad:["Spacer","2em"],
                thinspace:["Spacer",h.LENGTH.THINMATHSPACE],
                negthinspace:["Spacer",h.LENGTH.NEGATIVETHINMATHSPACE],
                hskip:"Hskip",
                hspace:"Hskip",
                kern:"Hskip",
                mskip:"Hskip",
                mspace:"Hskip",
                mkern:"Hskip",
                Rule:["Rule"],
                Space:["Rule","blank"],
                big:["MakeBig",h.TEXCLASS.ORD,0.85],
                Big:["MakeBig",h.TEXCLASS.ORD,1.15],
                bigg:["MakeBig",h.TEXCLASS.ORD,1.45],
                Bigg:["MakeBig",h.TEXCLASS.ORD,1.75],
                bigl:["MakeBig",h.TEXCLASS.OPEN,0.85],
                Bigl:["MakeBig",h.TEXCLASS.OPEN,1.15],
                biggl:["MakeBig",h.TEXCLASS.OPEN,1.45],
                Biggl:["MakeBig",h.TEXCLASS.OPEN,1.75],
                bigr:["MakeBig",h.TEXCLASS.CLOSE,0.85],
                Bigr:["MakeBig",h.TEXCLASS.CLOSE,1.15],
                biggr:["MakeBig",h.TEXCLASS.CLOSE,1.45],
                Biggr:["MakeBig",h.TEXCLASS.CLOSE,1.75],
                bigm:["MakeBig",h.TEXCLASS.REL,0.85],
                Bigm:["MakeBig",h.TEXCLASS.REL,1.15],
                biggm:["MakeBig",h.TEXCLASS.REL,1.45],
                Biggm:["MakeBig",h.TEXCLASS.REL,1.75],
                mathord:["TeXAtom",h.TEXCLASS.ORD],
                mathop:["TeXAtom",h.TEXCLASS.OP],
                mathopen:["TeXAtom",h.TEXCLASS.OPEN],
                mathclose:["TeXAtom",h.TEXCLASS.CLOSE],
                mathbin:["TeXAtom",h.TEXCLASS.BIN],
                mathrel:["TeXAtom",h.TEXCLASS.REL],
                mathpunct:["TeXAtom",h.TEXCLASS.PUNCT],
                mathinner:["TeXAtom",h.TEXCLASS.INNER],
                vcenter:["TeXAtom",h.TEXCLASS.VCENTER],
                mathchoice:["Extension","mathchoice"],
                buildrel:"BuildRel",
                hbox:["HBox",0],
                text:"HBox",
                mbox:["HBox",0],
                fbox:"FBox",
                strut:"Strut",
                mathstrut:["Macro","\\vphantom{(}"],
                phantom:"Phantom",
                vphantom:["Phantom",1,0],
                hphantom:["Phantom",0,1],
                smash:"Smash",
                acute:["Accent","02CA"],
                grave:["Accent","02CB"],
                ddot:["Accent","00A8"],
                tilde:["Accent","02DC"],
                bar:["Accent","02C9"],
                breve:["Accent","02D8"],
                check:["Accent","02C7"],
                hat:["Accent","02C6"],
                vec:["Accent","20D7"],
                dot:["Accent","02D9"],
                widetilde:["Accent","02DC",1],
                widehat:["Accent","02C6",1],
                matrix:"Matrix",
                array:"Matrix",
                pmatrix:["Matrix","(",")"],
                cases:["Matrix","{","","left left",null,".1em",null,true],
                eqalign:["Matrix",null,null,"right left",h.LENGTH.THICKMATHSPACE,".5em","D"],
                displaylines:["Matrix",null,null,"center",null,".5em","D"],
                cr:"Cr",
                "\\":"CrLaTeX",
                newline:"Cr",
                hline:["HLine","solid"],
                hdashline:["HLine","dashed"],
                eqalignno:["Matrix",null,null,"right left right",h.LENGTH.THICKMATHSPACE+" 3em",".5em","D"],
                leqalignno:["Matrix",null,null,"right left right",h.LENGTH.THICKMATHSPACE+" 3em",".5em","D"],
                bmod:["Macro","\\mathbin{\\mmlToken{mo}{mod}}"],
                pmod:["Macro","\\pod{\\mmlToken{mi}{mod}\\kern 6mu #1}",1],
                mod:["Macro","\\mathchoice{\\kern18mu}{\\kern12mu}{\\kern12mu}{\\kern12mu}\\mmlToken{mi}{mod}\\,\\,#1",1],
                pod:["Macro","\\mathchoice{\\kern18mu}{\\kern8mu}{\\kern8mu}{\\kern8mu}(#1)",1],
                iff:["Macro","\\;\\Longleftrightarrow\\;"],
                skew:["Macro","{{#2{#3\\mkern#1mu}\\mkern-#1mu}{}}",3],
                mathcal:["Macro","{\\cal #1}",1],
                mathscr:["Macro","{\\scr #1}",1],
                mathrm:["Macro","{\\rm #1}",1],
                mathbf:["Macro","{\\bf #1}",1],
                mathbb:["Macro","{\\bbFont #1}",1],
                Bbb:["Macro","{\\bbFont #1}",1],
                mathit:["Macro","{\\it #1}",1],
                mathfrak:["Macro","{\\frak #1}",1],
                mathsf:["Macro","{\\sf #1}",1],
                mathtt:["Macro","{\\tt #1}",1],
                textrm:["Macro","\\mathord{\\rm\\text{#1}}",1],
                textit:["Macro","\\mathord{\\it{\\text{#1}}}",1],
                textbf:["Macro","\\mathord{\\bf{\\text{#1}}}",1],
                pmb:["Macro","\\rlap{#1}\\kern1px{#1}",1],
                TeX:["Macro","T\\kern-.14em\\lower.5ex{E}\\kern-.115em X"],
                LaTeX:["Macro","L\\kern-.325em\\raise.21em{\\scriptstyle{A}}\\kern-.17em\\TeX"],
                " ":["Macro","\\text{ }"],
                not:"Not",
                dots:"Dots",
                space:"Tilde",
                begin:"Begin",
                end:"End",
                newcommand:["Extension","newcommand"],
                renewcommand:["Extension","newcommand"],
                newenvironment:["Extension","newcommand"],
                renewenvironment:["Extension","newcommand"],
                def:["Extension","newcommand"],
                let:["Extension","newcommand"],
                verb:["Extension","verb"],
                boldsymbol:["Extension","boldsymbol"],
                tag:["Extension","AMSmath"],
                notag:["Extension","AMSmath"],
                label:["Extension","AMSmath"],
                ref:["Extension","AMSmath"],
                eqref:["Extension","AMSmath"],
                nonumber:["Macro","\\notag"],
                unicode:["Extension","unicode"],
                color:"Color",
                href:["Extension","HTML"],
                "class":["Extension","HTML"],
                style:["Extension","HTML"],
                cssId:["Extension","HTML"],
                bbox:["Extension","bbox"],
                mmlToken:"MmlToken",
                require:"Require"
            },
            environment:{
                array:["AlignedArray"],
                matrix:["Array",null,null,null,"c"],
                pmatrix:["Array",null,"(",")","c"],
                bmatrix:["Array",null,"[","]","c"],
                Bmatrix:["Array",null,"\\{","\\}","c"],
                vmatrix:["Array",null,"\\vert","\\vert","c"],
                Vmatrix:["Array",null,"\\Vert","\\Vert","c"],
                cases:["Array",null,"\\{",".","ll",null,".1em"],
                eqnarray:["Array",null,null,null,"rcl",h.LENGTH.THICKMATHSPACE,".5em","D"],
                "eqnarray*":["Array",null,null,null,"rcl",h.LENGTH.THICKMATHSPACE,".5em","D"],
                equation:[null,"Equation"],
                "equation*":[null,"Equation"],
                align:["ExtensionEnv",null,"AMSmath"],
                "align*":["ExtensionEnv",null,"AMSmath"],
                aligned:["ExtensionEnv",null,"AMSmath"],
                multline:["ExtensionEnv",null,"AMSmath"],
                "multline*":["ExtensionEnv",null,"AMSmath"],
                split:["ExtensionEnv",null,"AMSmath"],
                gather:["ExtensionEnv",null,"AMSmath"],
                "gather*":["ExtensionEnv",null,"AMSmath"],
                gathered:["ExtensionEnv",null,"AMSmath"],
                alignat:["ExtensionEnv",null,"AMSmath"],
                "alignat*":["ExtensionEnv",null,"AMSmath"],
                alignedat:["ExtensionEnv",null,"AMSmath"]
            },
            p_height:1.2/0.85
        });
        if(this.config.Macros){
            var k=this.config.Macros;
            for(var l in k){
                if(k.hasOwnProperty(l)){
                    if(typeof(k[l])==="string"){
                        f.macros[l]=["Macro",k[l]]
                    }else{
                        f.macros[l]=["Macro"].concat(k[l])
                    }
                    f.macros[l].isUser=true
                }
            }
        }
    };

    var a=MathJax.Object.Subclass({
        Init:function(l,m){
            this.string=l;
            this.i=0;
            this.macroCount=0;
            var k;
            if(m){
                k={};
            
                for(var n in m){
                    if(m.hasOwnProperty(n)){
                        k[n]=m[n]
                    }
                }
            }
            this.stack=d.Stack(k,!!m);
            this.Parse();
            this.Push(b.stop())
        },
        Parse:function(){
            var l,k;
            while(this.i<this.string.length){
                l=this.string.charAt(this.i++);
                k=l.charCodeAt(0);
                if(k>=55296&&k<56320){
                    l+=this.string.charAt(this.i++)
                }
                if(f.special[l]){
                    this[f.special[l]](l)
                }else{
                    if(f.letter.test(l)){
                        this.Variable(l)
                    }else{
                        if(f.digit.test(l)){
                            this.Number(l)
                        }else{
                            this.Other(l)
                        }
                    }
                }
            }
        },
        Push:function(){
            this.stack.Push.apply(this.stack,arguments)
        },
        mml:function(){
            if(this.stack.Top().type!=="mml"){
                return null
            }
            return this.stack.Top().data[0]
        },
        mmlToken:function(k){
            return k
        },
        ControlSequence:function(n){
            var k=this.GetCS(),m=this.csFindMacro(k);
            if(m){
                if(!(m instanceof Array)){
                    m=[m]
                }
                var l=m[0];
                if(!(l instanceof Function)){
                    l=this[l]
                }
                l.apply(this,[n+k].concat(m.slice(1)))
            }else{
                if(f.mathchar0mi[k]){
                    this.csMathchar0mi(k,f.mathchar0mi[k])
                }else{
                    if(f.mathchar0mo[k]){
                        this.csMathchar0mo(k,f.mathchar0mo[k])
                    }else{
                        if(f.mathchar7[k]){
                            this.csMathchar7(k,f.mathchar7[k])
                        }else{
                            if(f.delimiter["\\"+k]!=null){
                                this.csDelimiter(k,f.delimiter["\\"+k])
                            }else{
                                this.csUndefined(n+k)
                            }
                        }
                    }
                }
            }
        },
        csFindMacro:function(k){
            return f.macros[k]
        },
        csMathchar0mi:function(k,m){
            var l={
                mathvariant:h.VARIANT.ITALIC
            };
        
            if(m instanceof Array){
                l=m[1];
                m=m[0]
            }
            this.Push(this.mmlToken(h.mi(h.entity("#x"+m)).With(l)))
        },
        csMathchar0mo:function(k,m){
            var l={
                stretchy:false
            };
    
            if(m instanceof Array){
                l=m[1];
                l.stretchy=false;
                m=m[0]
            }
            this.Push(this.mmlToken(h.mo(h.entity("#x"+m)).With(l)))
        },
        csMathchar7:function(k,m){
            var l={
                mathvariant:h.VARIANT.NORMAL
            };
        
            if(m instanceof Array){
                l=m[1];
                m=m[0]
            }
            if(this.stack.env.font){
                l.mathvariant=this.stack.env.font
            }
            this.Push(this.mmlToken(h.mi(h.entity("#x"+m)).With(l)))
        },
        csDelimiter:function(k,m){
            var l={};
    
            if(m instanceof Array){
                l=m[1];
                m=m[0]
            }
            if(m.length===4){
                m=h.entity("#x"+m)
            }else{
                m=h.chars(m)
            }
            this.Push(this.mmlToken(h.mo(m).With({
                fence:false,
                stretchy:false
            }).With(l)))
        },
        csUndefined:function(k){
            d.Error("Undefined control sequence "+k)
        },
        Variable:function(l){
            var k={};
    
            if(this.stack.env.font){
                k.mathvariant=this.stack.env.font
            }
            this.Push(this.mmlToken(h.mi(h.chars(l)).With(k)))
        },
        Number:function(m){
            var k,l=this.string.slice(this.i-1).match(f.number);
            if(l){
                k=h.mn(l[0].replace(/[{}]/g,""));
                this.i+=l[0].length-1
            }else{
                k=h.mo(h.chars(m))
            }
            if(this.stack.env.font){
                k.mathvariant=this.stack.env.font
            }
            this.Push(this.mmlToken(k))
        },
        Open:function(k){
            this.Push(b.open())
        },
        Close:function(k){
            this.Push(b.close())
        },
        Tilde:function(k){
            this.Push(h.mtext(h.chars(g)))
        },
        Space:function(k){},
        Superscript:function(m){
            if(this.GetNext().match(/\d/)){
                this.string=this.string.substr(0,this.i+1)+" "+this.string.substr(this.i+1)
            }
            var k,l=this.stack.Prev();
            if(!l){
                l=h.mi("")
            }
            if(l.isEmbellishedWrapper){
                l=l.data[0].data[0]
            }
            if(l.type==="msubsup"){
                if(l.data[l.sup]){
                    if(!l.data[l.sup].isPrime){
                        d.Error("Double exponent: use braces to clarify")
                    }
                    l=h.msubsup(l,null,null)
                }
                k=l.sup
            }else{
                if(l.movesupsub){
                    if(l.type!=="munderover"||l.data[l.over]){
                        if(l.movablelimits&&l.isa(h.mi)){
                            l=this.mi2mo(l)
                        }
                        l=h.munderover(l,null,null).With({
                            movesupsub:true
                        })
                    }
                    k=l.over
                }else{
                    l=h.msubsup(l,null,null);
                    k=l.sup
                }
            }
            this.Push(b.subsup(l).With({
                position:k
            }))
        },
        Subscript:function(m){
            if(this.GetNext().match(/\d/)){
                this.string=this.string.substr(0,this.i+1)+" "+this.string.substr(this.i+1)
            }
            var k,l=this.stack.Prev();
            if(!l){
                l=h.mi("")
            }
            if(l.isEmbellishedWrapper){
                l=l.data[0].data[0]
            }
            if(l.type==="msubsup"){
                if(l.data[l.sub]){
                    d.Error("Double subscripts: use braces to clarify")
                }
                k=l.sub
            }else{
                if(l.movesupsub){
                    if(l.type!=="munderover"||l.data[l.under]){
                        if(l.movablelimits&&l.isa(h.mi)){
                            l=this.mi2mo(l)
                        }
                        l=h.munderover(l,null,null).With({
                            movesupsub:true
                        })
                    }
                    k=l.under
                }else{
                    l=h.msubsup(l,null,null);
                    k=l.sub
                }
            }
            this.Push(b.subsup(l).With({
                position:k
            }))
        },
        PRIME:"\u2032",
        SMARTQUOTE:"\u2019",
        Prime:function(m){
            var l=this.stack.Prev();
            if(!l){
                l=h.mi()
            }
            if(l.type==="msubsup"&&l.data[l.sup]){
                d.Error("Prime causes double exponent: use braces to clarify")
            }
            var k="";
            this.i--;
            do{
                k+=this.PRIME;
                this.i++,m=this.GetNext()
            }while(m==="'"||m===this.SMARTQUOTE);
            k=this.mmlToken(h.mo(h.chars(k)).With({
                isPrime:true
            }));
            this.Push(h.msup(l,k))
        },
        mi2mo:function(k){
            var l=h.mo();
            l.Append.apply(l,k.data);
            var m;
            for(m in l.defaults){
                if(l.defaults.hasOwnProperty(m)&&k[m]!=null){
                    l[m]=k[m]
                }
            }
            for(m in h.copyAttributes){
                if(h.copyAttributes.hasOwnProperty(m)&&k[m]!=null){
                    l[m]=k[m]
                }
            }
            return l
        },
        Comment:function(k){
            while(this.i<this.string.length&&this.string.charAt(this.i)!="\n"){
                this.i++
            }
        },
        Hash:function(k){
            d.Error("You can't use 'macro parameter character #' in math mode")
        },
        Other:function(m){
            var l={
                stretchy:false
            },k;
            if(this.stack.env.font){
                l.mathvariant=this.stack.env.font
            }
            if(f.remap[m]){
                m=f.remap[m];
                if(m instanceof Array){
                    l=m[1];
                    m=m[0]
                }
                k=h.mo(h.entity("#x"+m)).With(l)
            }else{
                k=h.mo(m).With(l)
            }
            if(k.autoDefault("texClass",true)==""){
                k=h.TeXAtom(k)
            }
            this.Push(this.mmlToken(k))
        },
        SetFont:function(l,k){
            this.stack.env.font=k
        },
        SetStyle:function(l,k,m,n){
            this.stack.env.style=k;
            this.stack.env.level=n;
            this.Push(b.style().With({
                styles:{
                    displaystyle:m,
                    scriptlevel:n
                }
            }))
        },
        SetSize:function(k,l){
            this.stack.env.size=l;
            this.Push(b.style().With({
                styles:{
                    mathsize:l+"em"
                }
            }))
        },
        Color:function(m){
            var l=this.GetArgument(m);
            var k=this.stack.env.color;
            this.stack.env.color=l;
            var n=this.ParseArg(m);
            if(k){
                this.stack.env.color
            }else{
                delete this.stack.env.color
            }
            this.Push(h.mstyle(n).With({
                mathcolor:l
            }))
        },
        Spacer:function(k,l){
            this.Push(h.mspace().With({
                width:l,
                mathsize:h.SIZE.NORMAL,
                scriptlevel:0
            }))
        },
        LeftRight:function(k){
            this.Push(b[k.substr(1)]().With({
                delim:this.GetDelimiter(k)
            }))
        },
        Middle:function(k){
            var l=this.GetDelimiter(k);
            if(this.stack.Top().type!=="left"){
                d.Error(k+" must be within \\left and \\right")
            }
            this.Push(h.mo(l).With({
                stretchy:true
            }))
        },
        NamedFn:function(l,m){
            if(!m){
                m=l.substr(1)
            }
            var k=h.mi(m).With({
                texClass:h.TEXCLASS.OP
            });
            this.Push(b.fn(this.mmlToken(k)))
        },
        NamedOp:function(l,m){
            if(!m){
                m=l.substr(1)
            }
            m=m.replace(/&thinsp;/,"\u2006");
            var k=h.mo(m).With({
                movablelimits:true,
                movesupsub:true,
                form:h.FORM.PREFIX,
                texClass:h.TEXCLASS.OP
            });
            k.useMMLspacing&=~k.SPACE_ATTR.form;
            this.Push(this.mmlToken(k))
        },
        Limits:function(l,k){
            var m=this.stack.Prev("nopop");
            if(m.texClass!==h.TEXCLASS.OP){
                d.Error(l+" is allowed only on operators")
            }
            m.movesupsub=(k?true:false);
            m.movablelimits=false
        },
        Over:function(m,l,n){
            var k=b.over().With({
                name:m
            });
            if(l||n){
                k.open=l;
                k.close=n
            }else{
                if(m.match(/withdelims$/)){
                    k.open=this.GetDelimiter(m);
                    k.close=this.GetDelimiter(m)
                }
            }
            if(m.match(/^\\above/)){
                k.thickness=this.GetDimen(m)
            }else{
                if(m.match(/^\\atop/)||l||n){
                    k.thickness=0
                }
            }
            this.Push(k)
        },
        Frac:function(l){
            var k=this.ParseArg(l);
            var m=this.ParseArg(l);
            this.Push(h.mfrac(k,m))
        },
        Sqrt:function(m){
            var o=this.GetBrackets(m),k=this.GetArgument(m);
            if(k==="\\frac"){
                k+="{"+this.GetArgument(k)+"}{"+this.GetArgument(k)+"}"
            }
            var l=d.Parse(k,this.stack.env).mml();
            if(!o){
                l=h.msqrt.apply(h,l.array())
            }else{
                l=h.mroot(l,this.parseRoot(o))
            }
            this.Push(l)
        },
        Root:function(l){
            var m=this.GetUpTo(l,"\\of");
            var k=this.ParseArg(l);
            this.Push(h.mroot(k,this.parseRoot(m)))
        },
        parseRoot:function(p){
            var l=this.stack.env,k=l.inRoot;
            l.inRoot=true;
            var o=d.Parse(p,l);
            p=o.mml();
            var m=o.stack.global;
            if(m.leftRoot||m.upRoot){
                p=h.mpadded(p);
                if(m.leftRoot){
                    p.width=m.leftRoot
                }
                if(m.upRoot){
                    p.voffset=m.upRoot;
                    p.height=m.upRoot
                }
            }
            l.inRoot=k;
            return p
        },
        MoveRoot:function(k,m){
            if(!this.stack.env.inRoot){
                d.Error(k+" can appear only within a root")
            }
            if(this.stack.global[m]){
                d.Error("Multiple use of "+k)
            }
            var l=this.GetArgument(k);
            if(!l.match(/-?[0-9]+/)){
                d.Error("The argument to "+k+" must be an integer")
            }
            l=(l/15)+"em";
            if(l.substr(0,1)!=="-"){
                l="+"+l
            }
            this.stack.global[m]=l
        },
        Accent:function(m,k,p){
            var o=this.ParseArg(m);
            var n={
                accent:true
            };
    
            if(this.stack.env.font){
                n.mathvariant=this.stack.env.font
            }
            var l=this.mmlToken(h.mo(h.entity("#x"+k)).With(n));
            l.stretchy=(p?true:false);
            this.Push(h.TeXAtom(h.munderover(o,null,l).With({
                accent:true
            })))
        },
        UnderOver:function(m,p,k){
            var o={
                o:"over",
                u:"under"
            }
            [m.charAt(1)];
            var n=this.ParseArg(m);
            if(n.Get("movablelimits")){
                n.movablelimits=false
            }
            var l=h.munderover(n,null,null);
            if(k){
                l.movesupsub=true
            }
            l.data[l[o]]=this.mmlToken(h.mo(h.entity("#x"+p)).With({
                stretchy:true,
                accent:(o=="under")
            }));
            this.Push(l)
        },
        Overset:function(k){
            var m=this.ParseArg(k),l=this.ParseArg(k);
            this.Push(h.mover(l,m))
        },
        Underset:function(k){
            var m=this.ParseArg(k),l=this.ParseArg(k);
            this.Push(h.munder(l,m))
        },
        TeXAtom:function(n,p){
            var o={
                texClass:p
            },m;
            if(p==h.TEXCLASS.OP){
                o.movesupsub=o.movablelimits=true;
                var k=this.GetArgument(n);
                var l=k.match(/^\s*\\rm\s+([a-zA-Z0-9 ]+)$/);
                if(l){
                    o.mathvariant=h.VARIANT.NORMAL;
                    m=b.fn(this.mmlToken(h.mi(l[1]).With(o)))
                }else{
                    m=b.fn(h.TeXAtom(d.Parse(k,this.stack.env).mml()).With(o))
                }
            }else{
                m=h.TeXAtom(this.ParseArg(n)).With(o)
            }
            this.Push(m)
        },
        MmlToken:function(m){
            var n=this.GetArgument(m),k=this.GetBrackets(m,"").replace(/^\s+/,""),p=this.GetArgument(m),o={
                attrNames:[]
            },l;
            if(!h[n]||!h[n].prototype.isToken){
                d.Error(n+" is not a token element")
            }while(k!==""){
                l=k.match(/^([a-z]+)\s*=\s*('[^']*'|"[^"]*"|[^ ]*)\s*/i);
                if(!l){
                    d.Error("Invalid MathML attribute: "+k)
                }
                if(!h[n].prototype.defaults[l[1]]&&!this.MmlTokenAllow[l[1]]){
                    d.Error(l[1]+" is not a recognized attribute for "+n)
                }
                o[l[1]]=l[2].replace(/^(['"])(.*)\1$/,"$2");
                o.attrNames.push(l[1]);
                k=k.substr(l[0].length)
            }
            this.Push(this.mmlToken(h[n](p).With(o)))
        },
        MmlTokenAllow:{
            fontfamily:1,
            fontsize:1,
            fontweight:1,
            fontstyle:1,
            color:1,
            background:1,
            id:1,
            "class":1,
            href:1,
            style:1
        },
        Strut:function(k){
            this.Push(h.mpadded(h.mrow()).With({
                height:"8.6pt",
                depth:"3pt",
                width:0
            }))
        },
        Phantom:function(l,k,m){
            var n=h.mphantom(this.ParseArg(l));
            if(k||m){
                n=h.mpadded(n);
                if(m){
                    n.height=n.depth=0
                }
                if(k){
                    n.width=0
                }
            }
            this.Push(h.TeXAtom(n))
        },
        Smash:function(m){
            var l=this.trimSpaces(this.GetBrackets(m,""));
            var k=h.mpadded(this.ParseArg(m));
            switch(l){
                case"b":
                    k.depth=0;
                    break;
                case"t":
                    k.height=0;
                    break;
                default:
                    k.height=k.depth=0
            }
            this.Push(h.TeXAtom(k))
        },
        Lap:function(l){
            var k=h.mpadded(this.ParseArg(l)).With({
                width:0
            });
            if(l==="\\llap"){
                k.lspace="-1 width"
            }
            this.Push(h.TeXAtom(k))
        },
        RaiseLower:function(k){
            var l=this.GetDimen(k);
            var m=b.position().With({
                name:k,
                move:"vertical"
            });
            if(l.charAt(0)==="-"){
                l=l.slice(1);
                k={
                    raise:"\\lower",
                    lower:"\\raise"
                }
                [k.substr(1)]
            }
            if(k==="\\lower"){
                m.dh="-"+l;
                m.dd="+"+l
            }else{
                m.dh="+"+l;
                m.dd="-"+l
            }
            this.Push(m)
        },
        MoveLeftRight:function(k){
            var n=this.GetDimen(k);
            var m=(n.charAt(0)==="-"?n.slice(1):"-"+n);
            if(k==="\\moveleft"){
                var l=n;
                n=m;
                m=l
            }
            this.Push(b.position().With({
                name:k,
                move:"horizontal",
                left:h.mspace().With({
                    width:n,
                    mathsize:h.SIZE.NORMAL
                }),
                right:h.mspace().With({
                    width:m,
                    mathsize:h.SIZE.NORMAL
                })
            }))
        },
        Hskip:function(k){
            this.Push(h.mspace().With({
                width:this.GetDimen(k),
                mathsize:h.SIZE.NORMAL
            }))
        },
        Rule:function(m,o){
            var k=this.GetDimen(m),n=this.GetDimen(m),q=this.GetDimen(m);
            var l,p={
                width:k,
                height:n,
                depth:q
            };
    
            if(o!=="blank"){
                if(parseFloat(k)&&parseFloat(n)+parseFloat(q)){
                    p.mathbackground=(this.stack.env.color||"black")
                }
                l=h.mpadded(h.mrow()).With(p)
            }else{
                l=h.mspace().With(p)
            }
            this.Push(l)
        },
        MakeBig:function(k,n,l){
            l*=f.p_height;
            l=String(l).replace(/(\.\d\d\d).+/,"$1")+"em";
            var m=this.GetDelimiter(k);
            this.Push(h.TeXAtom(h.mo(m).With({
                minsize:l,
                maxsize:l,
                scriptlevel:0,
                fence:true,
                stretchy:true,
                symmetric:true
            })).With({
                texClass:n
            }))
        },
        BuildRel:function(k){
            var l=this.ParseUpTo(k,"\\over");
            var m=this.ParseArg(k);
            this.Push(h.TeXAtom(h.munderover(m,null,l)).With({
                mclass:h.TEXCLASS.REL
            }))
        },
        HBox:function(k,l){
            this.Push.apply(this,this.InternalMath(this.GetArgument(k),l))
        },
        FBox:function(k){
            this.Push(h.menclose.apply(h,this.InternalMath(this.GetArgument(k))).With({
                notation:"box"
            }))
        },
        Not:function(k){
            this.Push(b.not())
        },
        Dots:function(k){
            this.Push(b.dots().With({
                ldots:this.mmlToken(h.mo(h.entity("#x2026")).With({
                    stretchy:false
                })),
                cdots:this.mmlToken(h.mo(h.entity("#x22EF")).With({
                    stretchy:false
                }))
            }))
        },
        Require:function(k){
            var l=this.GetArgument(k).replace(/.*\//,"").replace(/[^a-z0-9_.-]/ig,"");
            this.Extension(null,l)
        },
        Extension:function(k,l,m){
            if(k&&!typeof(k)==="string"){
                k=k.name
            }
            l=d.extensionDir+"/"+l;
            if(!l.match(/\.js$/)){
                l+=".js"
            }
            if(!i.loaded[i.fileURL(l)]){
                if(k!=null){
                    delete f[m||"macros"][k.replace(/^\\/,"")]
                }
                c.RestartAfter(i.Require(l))
            }
        },
        Macro:function(m,p,o,q){
            if(o){
                var l=[];
                if(q!=null){
                    var k=this.GetBrackets(m);
                    l.push(k==null?q:k)
                }
                for(var n=l.length;n<o;n++){
                    l.push(this.GetArgument(m))
                }
                p=this.SubstituteArgs(l,p)
            }
            this.string=this.AddArgs(p,this.string.slice(this.i));
            this.i=0;
            if(++this.macroCount>d.config.MAXMACROS){
                d.Error("MathJax maximum macro substitution count exceeded; is there a recursive macro call?")
            }
        },
        Matrix:function(l,n,s,p,r,m,k,t){
            var q=this.GetNext();
            if(q===""){
                d.Error("Missing argument for "+l)
            }
            if(q==="{"){
                this.i++
            }else{
                this.string=q+"}"+this.string.slice(this.i+1);
                this.i=0
            }
            var o=b.array().With({
                requireClose:true,
                arraydef:{
                    rowspacing:(m||"4pt"),
                    columnspacing:(r||"1em")
                }
            });
            if(t){
                o.isCases=true
            }
            if(n||s){
                o.open=n;
                o.close=s
            }
            if(k==="D"){
                o.arraydef.displaystyle=true
            }
            if(p!=null){
                o.arraydef.columnalign=p
            }
            this.Push(o)
        },
        Entry:function(n){
            this.Push(b.cell().With({
                isEntry:true,
                name:n
            }));
            if(this.stack.Top().isCases){
                var l=this.string;
                var q=0,o=this.i,k=l.length;
                while(o<k){
                    var r=l.charAt(o);
                    if(r==="{"){
                        q++;
                        o++
                    }else{
                        if(r==="}"){
                            if(q===0){
                                k=0
                            }else{
                                q--;
                                o++
                            }
                        }else{
                            if(r==="&"&&q===0){
                                d.Error("Extra alignment tab in \\cases text")
                            }else{
                                if(r==="\\"){
                                    if(l.substr(o).match(/^((\\cr)[^a-zA-Z]|\\\\)/)){
                                        k=0
                                    }else{
                                        o+=2
                                    }
                                }else{
                                    o++
                                }
                            }
                        }
                    }
                }
                var p=l.substr(this.i,o-this.i);
                if(!p.match(/^\s*\\text[^a-zA-Z]/)){
                    this.Push.apply(this,this.InternalMath(p));
                    this.i=o
                }
            }
        },
        Cr:function(k){
            this.Push(b.cell().With({
                isCR:true,
                name:k
            }))
        },
        CrLaTeX:function(k){
            var o=this.GetBrackets(k,"").replace(/ /g,"");
            if(o&&!o.match(/^(((\.\d+|\d+(\.\d*)?))(pt|em|ex|mu|mm|cm|in|pc))$/)){
                d.Error("Bracket argument to "+k+" must be a dimension")
            }
            this.Push(b.cell().With({
                isCR:true,
                name:k,
                linebreak:true
            }));
            var m=this.stack.Top();
            if(m.isa(b.array)){
                if(o&&m.arraydef.rowspacing){
                    var l=m.arraydef.rowspacing.split(/ /);
                    if(!m.rowspacing){
                        m.rowspacing=this.dimen2em(l[0])
                    }while(l.length<m.table.length){
                        l.push(m.rowspacing)
                    }
                    l[m.table.length-1]=(m.rowspacing+this.dimen2em(o))+"em";
                    m.arraydef.rowspacing=l.join(" ")
                }
            }else{
                if(o){
                    this.Push(h.mspace().With({
                        depth:o
                    }))
                }
                this.Push(h.mo().With({
                    linebreak:h.LINEBREAK.NEWLINE
                }))
            }
        },
        emPerInch:7.2,
        dimen2em:function(o){
            var l=o.match(/^((?:\.\d+|\d+(?:\.\d*)?))(pt|em|ex|mu|pc|in|mm|cm)/);
            var k=parseFloat(l[1]||"1"),n=l[2];
            if(n==="em"){
                return k
            }
            if(n==="ex"){
                return k*0.43
            }
            if(n==="pt"){
                return k/10
            }
            if(n==="pc"){
                return k*1.2
            }
            if(n==="in"){
                return k*this.emPerInch
            }
            if(n==="cm"){
                return k*this.emPerInch/2.54
            }
            if(n==="mm"){
                return k*this.emPerInch/25.4
            }
            if(n==="mu"){
                return k/18
            }
            return 0
        },
        HLine:function(l,m){
            if(m==null){
                m="solid"
            }
            var n=this.stack.Top();
            if(!n.isa(b.array)||n.data.length){
                d.Error("Misplaced "+l)
            }
            if(n.table.length==0){
                n.frame.push("top")
            }else{
                var k=(n.arraydef.rowlines?n.arraydef.rowlines.split(/ /):[]);
                while(k.length<n.table.length){
                    k.push("none")
                }
                k[n.table.length-1]=m;
                n.arraydef.rowlines=k.join(" ")
            }
        },
        Begin:function(l){
            var m=this.GetArgument(l);
            if(m.match(/[^a-z*]/i)){
                d.Error('Invalid environment name "'+m+'"')
            }
            var n=this.envFindName(m);
            if(!n){
                d.Error('Unknown environment "'+m+'"')
            }
            if(++this.macroCount>d.config.MAXMACROS){
                d.Error("MathJax maximum substitution count exceeded; is there a recursive latex environment?")
            }
            if(!(n instanceof Array)){
                n=[n]
            }
            var k=b.begin().With({
                name:m,
                end:n[1],
                parse:this
            });
            if(n[0]&&this[n[0]]){
                k=this[n[0]].apply(this,[k].concat(n.slice(2)))
            }
            this.Push(k)
        },
        End:function(k){
            this.Push(b.end().With({
                name:this.GetArgument(k)
            }))
        },
        envFindName:function(k){
            return f.environment[k]
        },
        Equation:function(k,l){
            return l
        },
        ExtensionEnv:function(l,k){
            this.Extension(l.name,k,"environment")
        },
        Array:function(l,n,s,q,r,m,k,o){
            if(!q){
                q=this.GetArgument("\\begin{"+l.name+"}")
            }
            var t=("c"+q).replace(/[^clr|:]/g,"").replace(/[^|:]([|:])+/g,"$1");
            q=q.replace(/[^clr]/g,"").split("").join(" ");
            q=q.replace(/l/g,"left").replace(/r/g,"right").replace(/c/g,"center");
            var p=b.array().With({
                arraydef:{
                    columnalign:q,
                    columnspacing:(r||"1em"),
                    rowspacing:(m||"4pt")
                }
            });
            if(t.match(/[|:]/)){
                if(t.charAt(0).match(/[|:]/)){
                    p.frame.push("left");
                    p.frame.dashed=t.charAt(0)===":"
                }
                if(t.charAt(t.length-1).match(/[|:]/)){
                    p.frame.push("right")
                }
                t=t.substr(1,t.length-2);
                p.arraydef.columnlines=t.split("").join(" ").replace(/[^|: ]/g,"none").replace(/\|/g,"solid").replace(/:/g,"dashed")
            }
            if(n){
                p.open=this.convertDelimiter(n)
            }
            if(s){
                p.close=this.convertDelimiter(s)
            }
            if(k==="D"){
                p.arraydef.displaystyle=true
            }
            if(k==="S"){
                p.arraydef.scriptlevel=1
            }
            if(o){
                p.arraydef.useHeight=false
            }
            this.Push(l);
            return p
        },
        AlignedArray:function(k){
            var l=this.GetBrackets("\\begin{"+k.name+"}");
            return this.setArrayAlign(this.Array.apply(this,arguments),l)
        },
        setArrayAlign:function(l,k){
            k=this.trimSpaces(k||"");
            if(k==="t"){
                l.arraydef.align="baseline 1"
            }else{
                if(k==="b"){
                    l.arraydef.align="baseline -1"
                }else{
                    if(k==="c"){
                        l.arraydef.align="center"
                    }else{
                        if(k){
                            l.arraydef.align=k
                        }
                    }
                }
            }
            return l
        },
        convertDelimiter:function(k){
            if(k){
                k=f.delimiter[k]
            }
            if(k==null){
                return null
            }
            if(k instanceof Array){
                k=k[0]
            }
            if(k.length===4){
                k=String.fromCharCode(parseInt(k,16))
            }
            return k
        },
        trimSpaces:function(k){
            if(typeof(k)!="string"){
                return k
            }
            return k.replace(/^\s+|\s+$/g,"")
        },
        nextIsSpace:function(){
            return this.string.charAt(this.i).match(/[ \n\r\t]/)
        },
        GetNext:function(){
            while(this.nextIsSpace()){
                this.i++
            }
            return this.string.charAt(this.i)
        },
        GetCS:function(){
            var k=this.string.slice(this.i).match(/^([a-z]+|.) ?/i);
            if(k){
                this.i+=k[1].length;
                return k[1]
            }else{
                this.i++;
                return" "
            }
        },
        GetArgument:function(l,m){
            switch(this.GetNext()){
                case"":
                    if(!m){
                        d.Error("Missing argument for "+l)
                    }
                    return null;
                case"}":
                    if(!m){
                        d.Error("Extra close brace or missing open brace")
                    }
                    return null;
                case"\\":
                    this.i++;
                    return"\\"+this.GetCS();
                case"{":
                    var k=++this.i,n=1;
                    while(this.i<this.string.length){
                        switch(this.string.charAt(this.i++)){
                            case"\\":
                                this.i++;
                                break;
                            case"{":
                                n++;
                                break;
                            case"}":
                                if(n==0){
                                    d.Error("Extra close brace")
                                }
                                if(--n==0){
                                    return this.string.slice(k,this.i-1)
                                }
                                break
                        }
                    }
                    d.Error("Missing close brace");
                    break
            }
            return this.string.charAt(this.i++)
        },
        GetBrackets:function(l,n){
            if(this.GetNext()!="["){
                return n
            }
            var k=++this.i,m=0;
            while(this.i<this.string.length){
                switch(this.string.charAt(this.i++)){
                    case"{":
                        m++;
                        break;
                    case"\\":
                        this.i++;
                        break;
                    case"}":
                        if(m--<=0){
                            d.Error("Extra close brace while looking for ']'")
                        }
                        break;
                    case"]":
                        if(m==0){
                            return this.string.slice(k,this.i-1)
                        }
                        break
                }
            }
            d.Error("Couldn't find closing ']' for argument to "+l)
        },
        GetDelimiter:function(k){
            while(this.nextIsSpace()){
                this.i++
            }
            var l=this.string.charAt(this.i);
            if(this.i<this.string.length){
                this.i++;
                if(l=="\\"){
                    l+=this.GetCS(k)
                }
                if(f.delimiter[l]!=null){
                    return this.convertDelimiter(l)
                }
            }
            d.Error("Missing or unrecognized delimiter for "+k)
        },
        GetDimen:function(l){
            var m;
            if(this.nextIsSpace()){
                this.i++
            }
            if(this.string.charAt(this.i)=="{"){
                m=this.GetArgument(l);
                if(m.match(/^\s*([-+]?(\.\d+|\d+(\.\d*)?))\s*(pt|em|ex|mu|px|mm|cm|in|pc)\s*$/)){
                    return m.replace(/ /g,"")
                }
            }else{
                m=this.string.slice(this.i);
                var k=m.match(/^\s*(([-+]?(\.\d+|\d+(\.\d*)?))\s*(pt|em|ex|mu|px|mm|cm|in|pc)) ?/);
                if(k){
                    this.i+=k[0].length;
                    return k[1].replace(/ /g,"")
                }
            }
            d.Error("Missing dimension or its units for "+l)
        },
        GetUpTo:function(n,o){
            while(this.nextIsSpace()){
                this.i++
            }
            var m=this.i,l,q,p=0;
            while(this.i<this.string.length){
                l=this.i;
                q=this.string.charAt(this.i++);
                switch(q){
                    case"\\":
                        q+=this.GetCS();
                        break;
                    case"{":
                        p++;
                        break;
                    case"}":
                        if(p==0){
                            d.Error("Extra close brace while looking for "+o)
                        }
                        p--;
                        break
                }
                if(p==0&&q==o){
                    return this.string.slice(m,l)
                }
            }
            d.Error("Couldn't find "+o+" for "+n)
        },
        ParseArg:function(k){
            return d.Parse(this.GetArgument(k),this.stack.env).mml()
        },
        ParseUpTo:function(k,l){
            return d.Parse(this.GetUpTo(k,l),this.stack.env).mml()
        },
        InternalMath:function(q,s){
            var p={
                displaystyle:false
            };
    
            if(s!=null){
                p.scriptlevel=s
            }
            if(this.stack.env.font){
                p.mathvariant=this.stack.env.font
            }
            if(!q.match(/\$|\\\(|\\(eq)?ref\s*\{/)){
                return[this.InternalText(q,p)]
            }
            var o=0,l=0,r,n="";
            var m=[];
            while(o<q.length){
                r=q.charAt(o++);
                if(r==="$"){
                    if(n==="$"){
                        m.push(h.TeXAtom(d.Parse(q.slice(l,o-1),{}).mml().With(p)));
                        n="";
                        l=o
                    }else{
                        if(n===""){
                            if(l<o-1){
                                m.push(this.InternalText(q.slice(l,o-1),p))
                            }
                            n="$";
                            l=o
                        }
                    }
                }else{
                    if(r==="}"&&n==="}"){
                        m.push(h.TeXAtom(d.Parse(q.slice(l,o),{}).mml().With(p)));
                        n="";
                        l=o
                    }else{
                        if(r==="\\"){
                            if(n===""&&q.substr(o).match(/^(eq)?ref\s*\{/)){
                                if(l<o-1){
                                    m.push(this.InternalText(q.slice(l,o-1),p))
                                }
                                n="}";
                                l=o-1
                            }else{
                                r=q.charAt(o++);
                                if(r==="("&&n===""){
                                    if(l<o-2){
                                        m.push(this.InternalText(q.slice(l,o-2),p))
                                    }
                                    n=")";
                                    l=o
                                }else{
                                    if(r===")"&&n===")"){
                                        m.push(h.TeXAtom(d.Parse(q.slice(l,o-2),{}).mml().With(p)));
                                        n="";
                                        l=o
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if(n!==""){
                d.Error("Math not terminated in text box")
            }
            if(l<q.length){
                m.push(this.InternalText(q.slice(l),p))
            }
            return m
        },
        InternalText:function(l,k){
            l=l.replace(/^\s+/,g).replace(/\s+$/,g);
            return h.mtext(h.chars(l)).With(k)
        },
        SubstituteArgs:function(l,k){
            var o="";
            var n="";
            var p;
            var m=0;
            while(m<k.length){
                p=k.charAt(m++);
                if(p==="\\"){
                    o+=p+k.charAt(m++)
                }else{
                    if(p==="#"){
                        p=k.charAt(m++);
                        if(p==="#"){
                            o+=p
                        }else{
                            if(!p.match(/[1-9]/)||p>l.length){
                                d.Error("Illegal macro parameter reference")
                            }
                            n=this.AddArgs(this.AddArgs(n,o),l[p-1]);
                            o=""
                        }
                    }else{
                        o+=p
                    }
                }
            }
            return this.AddArgs(n,o)
        },
        AddArgs:function(l,k){
            if(k.match(/^[a-z]/i)&&l.match(/(^|[^\\])(\\\\)*\\[a-z]+$/i)){
                l+=" "
            }
            if(l.length+k.length>d.config.MAXBUFFER){
                d.Error("MathJax internal buffer size exceeded; is there a recursive macro call?")
            }
            return l+k
        }
    });
    d.Augment({
        Stack:e,
        Parse:a,
        Definitions:f,
        Startup:j,
        config:{
            MAXMACROS:10000,
            MAXBUFFER:5*1024
        },
        sourceMenuTitle:"TeX Commands",
        prefilterHooks:MathJax.Callback.Hooks(true),
        postfilterHooks:MathJax.Callback.Hooks(true),
        Config:function(){
            this.SUPER(arguments).Config.apply(this,arguments);
            if(this.config.equationNumbers.autoNumber!=="none"){
                if(!this.config.extensions){
                    this.config.extensions=[]
                }
                this.config.extensions.push("AMSmath.js")
            }
        },
        Translate:function(k){
            var l,m=false,o=MathJax.HTML.getScript(k);
            var q=(k.type.replace(/\n/g," ").match(/(;|\s|\n)mode\s*=\s*display(;|\s|\n|$)/)!=null);
            var p={
                math:o,
                display:q,
                script:k
            };
    
            this.prefilterHooks.Execute(p);
            o=p.math;
            try{
                l=d.Parse(o).mml()
            }catch(n){
                if(!n.texError){
                    throw n
                }
                l=this.formatError(n,o,q,k);
                m=true
            }
            if(l.inferred){
                l=h.apply(MathJax.ElementJax,l.data)
            }else{
                l=h(l)
            }
            if(q){
                l.root.display="block"
            }
            if(m){
                l.texError=true
            }
            p.math=l;
            this.postfilterHooks.Execute(p);
            return p.math
        },
        prefilterMath:function(l,m,k){
            return l
        },
        postfilterMath:function(l,m,k){
            this.combineRelations(l.root);
            return l
        },
        formatError:function(n,m,o,k){
            var l=n.message.replace(/\n.*/,"");
            c.signal.Post(["TeX Jax - parse error",l,m,o,k]);
            return h.merror(l)
        },
        Error:function(k){
            throw c.Insert(Error(k),{
                texError:true
            })
        },
        Macro:function(k,l,m){
            f.macros[k]=["Macro"].concat([].slice.call(arguments,1));
            f.macros[k].isUser=true
        },
        combineRelations:function(o){
            var p,k,n,l;
            for(p=0,k=o.data.length;p<k;p++){
                if(o.data[p]){
                    if(o.isa(h.mrow)){
                        while(p+1<k&&(n=o.data[p])&&(l=o.data[p+1])&&n.isa(h.mo)&&l.isa(h.mo)&&n.Get("texClass")===h.TEXCLASS.REL&&l.Get("texClass")===h.TEXCLASS.REL){
                            if(n.variantForm==l.variantForm&&n.Get("mathvariant")==l.Get("mathvariant")&&n.style==l.style&&n["class"]==l["class"]&&!n.id&&!l.id){
                                n.Append.apply(n,l.data);
                                o.data.splice(p+1,1);
                                k--
                            }else{
                                n.rspace=l.lspace="0pt";
                                p++
                            }
                        }
                    }
                    if(!o.data[p].isToken){
                        this.combineRelations(o.data[p])
                    }
                }
            }
        }
    });
    d.prefilterHooks.Add(function(k){
        k.math=d.prefilterMath(k.math,k.display,k.script)
    });
    d.postfilterHooks.Add(function(k){
        k.math=d.postfilterMath(k.math,k.display,k.script)
    });
    d.loadComplete("jax.js")
})(MathJax.InputJax.TeX,MathJax.Hub,MathJax.Ajax);

MathJax.Extension["TeX/AMSmath"]={
    version:"2.0",
    number:0,
    startNumber:0,
    labels:{},
    eqlabels:{},
    refs:[]
};

MathJax.Hub.Register.StartupHook("TeX Jax Ready",function(){
    var b=MathJax.ElementJax.mml,g=MathJax.InputJax.TeX,f=MathJax.Extension["TeX/AMSmath"];
    var d=g.Definitions,e=g.Stack.Item,a=g.config.equationNumbers;
    var c=function(h){
        return h.join("em ")+"em"
    };
        
    d.Add({
        macros:{
            mathring:["Accent","2DA"],
            nobreakspace:"Tilde",
            negmedspace:["Spacer",b.LENGTH.NEGATIVEMEDIUMMATHSPACE],
            negthickspace:["Spacer",b.LENGTH.NEGATIVETHICKMATHSPACE],
            intI:["Macro","\\mathchoice{\\!}{}{}{}\\!\\!\\int"],
            iiiint:["MultiIntegral","\\int\\intI\\intI\\intI"],
            idotsint:["MultiIntegral","\\int\\cdots\\int"],
            dddot:["Macro","\\mathop{#1}\\limits^{\\textstyle \\mathord{.}\\mathord{.}\\mathord{.}}",1],
            ddddot:["Macro","\\mathop{#1}\\limits^{\\textstyle \\mathord{.}\\mathord{.}\\mathord{.}\\mathord{.}}",1],
            sideset:["Macro","\\mathop{\\mathop{\\rlap{\\phantom{#3}}}\\nolimits#1\\!\\mathop{#3}\\nolimits#2}",3],
            boxed:["Macro","\\fbox{$\\displaystyle{#1}$}",1],
            tag:"HandleTag",
            notag:"HandleNoTag",
            label:"HandleLabel",
            ref:"HandleRef",
            eqref:["HandleRef",true],
            substack:["Macro","\\begin{subarray}{c}#1\\end{subarray}",1],
            injlim:["Macro","\\mathop{\\rm inj\\,lim}"],
            projlim:["Macro","\\mathop{\\rm proj\\,lim}"],
            varliminf:["Macro","\\mathop{\\underline{\\rm lim}}"],
            varlimsup:["Macro","\\mathop{\\overline{\\rm lim}}"],
            varinjlim:["Macro","\\mathop{\\underrightarrow{\\rm lim\\Rule{-1pt}{0pt}{1pt}}\\Rule{0pt}{0pt}{.45em}}"],
            varprojlim:["Macro","\\mathop{\\underleftarrow{\\rm lim\\Rule{-1pt}{0pt}{1pt}}\\Rule{0pt}{0pt}{.45em}}"],
            DeclareMathOperator:"HandleDeclareOp",
            operatorname:"HandleOperatorName",
            genfrac:"Genfrac",
            frac:["Genfrac","","","",""],
            tfrac:["Genfrac","","","",1],
            dfrac:["Genfrac","","","",0],
            binom:["Genfrac","(",")","0em",""],
            tbinom:["Genfrac","(",")","0em",1],
            dbinom:["Genfrac","(",")","0em",0],
            cfrac:"CFrac",
            shoveleft:["HandleShove",b.ALIGN.LEFT],
            shoveright:["HandleShove",b.ALIGN.RIGHT],
            xrightarrow:["xArrow",8594,5,6],
            xleftarrow:["xArrow",8592,7,3]
        },
        environment:{
            align:["AMSarray",null,true,true,"rlrlrlrlrlrl",c([5/18,2,5/18,2,5/18,2,5/18,2,5/18,2,5/18])],
            "align*":["AMSarray",null,false,true,"rlrlrlrlrlrl",c([5/18,2,5/18,2,5/18,2,5/18,2,5/18,2,5/18])],
            multline:["Multline",null,true],
            "multline*":["Multline",null,false],
            split:["AMSarray",null,false,false,"rl",c([5/18])],
            gather:["AMSarray",null,true,true,"c"],
            "gather*":["AMSarray",null,false,true,"c"],
            alignat:["AlignAt",null,true,true],
            "alignat*":["AlignAt",null,false,true],
            alignedat:["AlignAt",null,false,false],
            aligned:["AlignedArray",null,null,null,"rlrlrlrlrlrl",c([5/18,2,5/18,2,5/18,2,5/18,2,5/18,2,5/18]),".5em","D"],
            gathered:["AlignedArray",null,null,null,"c",null,".5em","D"],
            subarray:["Array",null,null,null,null,c([0,0,0,0]),"0.1em","S",1],
            smallmatrix:["Array",null,null,null,"c",c([1/3]),".2em","S",1],
            equation:["EquationBegin","Equation",true],
            "equation*":["EquationBegin","EquationStar",false]
        },
        delimiter:{
            "\\lvert":["2223",{
                texClass:b.TEXCLASS.OPEN
            }],
            "\\rvert":["2223",{
                texClass:b.TEXCLASS.CLOSE
            }],
            "\\lVert":["2225",{
                texClass:b.TEXCLASS.OPEN
            }],
            "\\rVert":["2225",{
                texClass:b.TEXCLASS.CLOSE
            }]
        }
    },null,true);
    g.Parse.Augment({
        HandleTag:function(j){
            var l=this.GetStar();
            var i=this.trimSpaces(this.GetArgument(j)),h=i;
            if(!l){
                i=a.formatTag(i)
            }
            var k=this.stack.global;
            k.tagID=h;
            if(k.notags){
                g.Error(j+" not allowed in "+k.notags+" environment")
            }
            if(k.tag){
                g.Error("Multiple "+j)
            }
            k.tag=b.mtd.apply(b,this.InternalMath(i)).With({
                id:a.formatID(h)
            })
        },
        HandleNoTag:function(h){
            if(this.stack.global.tag){
                delete this.stack.global.tag
            }
            this.stack.global.notag=true
        },
        HandleLabel:function(i){
            var j=this.stack.global,h=this.GetArgument(i);
            if(!f.refUpdate){
                if(j.label){
                    g.Error("Multiple "+i+"'s")
                }
                j.label=h;
                if(f.labels[h]||f.eqlabels[h]){
                    g.Error("Label '"+h+"' mutiply defined")
                }
                f.eqlabels[h]="???"
            }
        },
        HandleRef:function(j,l){
            var i=this.GetArgument(j);
            var k=f.labels[i]||f.eqlabels[i];
            if(!k){
                k="??";
                f.badref=!f.refUpdate
            }
            var h=k;
            if(l){
                h=a.formatTag(h)
            }
            if(a.useLabelIds){
                k=i
            }
            this.Push(b.mrow.apply(b,this.InternalMath(h)).With({
                href:a.formatURL(a.formatID(k)),
                "class":"MathJax_ref"
            }))
        },
        HandleDeclareOp:function(i){
            var h=(this.GetStar()?"\\limits":"");
            var j=this.trimSpaces(this.GetArgument(i));
            if(j.charAt(0)=="\\"){
                j=j.substr(1)
            }
            var k=this.GetArgument(i);
            k=k.replace(/\*/g,"\\text{*}").replace(/-/g,"\\text{-}");
            g.Definitions.macros[j]=["Macro","\\mathop{\\rm "+k+"}"+h]
        },
        HandleOperatorName:function(i){
            var h=(this.GetStar()?"\\limits":"\\nolimits");
            var j=this.trimSpaces(this.GetArgument(i));
            j=j.replace(/\*/g,"\\text{*}").replace(/-/g,"\\text{-}");
            this.string="\\mathop{\\rm "+j+"}"+h+" "+this.string.slice(this.i);
            this.i=0
        },
        HandleShove:function(i,h){
            var j=this.stack.Top();
            if(j.type!=="multline"||j.data.length){
                g.Error(i+" must come at the beginning of the line")
            }
            j.data.shove=h
        },
        CFrac:function(k){
            var h=this.trimSpaces(this.GetBrackets(k,"")),j=this.GetArgument(k),l=this.GetArgument(k);
            var i=b.mfrac(g.Parse("\\strut\\textstyle{"+j+"}",this.stack.env).mml(),g.Parse("\\strut\\textstyle{"+l+"}",this.stack.env).mml());
            h=({
                l:b.ALIGN.LEFT,
                r:b.ALIGN.RIGHT,
                "":""
            })[h];
            if(h==null){
                g.Error("Illegal alignment specified in "+k)
            }
            if(h){
                i.numalign=i.denomalign=h
            }
            this.Push(i)
        },
        Genfrac:function(i,k,p,m,h){
            if(k==null){
                k=this.GetDelimiterArg(i)
            }else{
                k=this.convertDelimiter(k)
            }
            if(p==null){
                p=this.GetDelimiterArg(i)
            }else{
                p=this.convertDelimiter(p)
            }
            if(m==null){
                m=this.GetArgument(i)
            }
            if(h==null){
                h=this.trimSpaces(this.GetArgument(i))
            }
            var l=this.ParseArg(i);
            var o=this.ParseArg(i);
            var j=b.mfrac(l,o);
            if(m!==""){
                j.linethickness=m
            }
            if(k||p){
                j=b.mfenced(j).With({
                    open:k,
                    close:p
                })
            }
            if(h!==""){
                var n=(["D","T","S","SS"])[h];
                if(n==null){
                    g.Error("Bad math style for "+i)
                }
                j=b.mstyle(j);
                if(n==="D"){
                    j.displaystyle=true;
                    j.scriptlevel=0
                }else{
                    j.displaystyle=false;
                    j.scriptlevel=h-1
                }
            }
            this.Push(j)
        },
        Multline:function(i,h){
            this.Push(i);
            this.checkEqnEnv();
            return e.multline(h,this.stack).With({
                arraydef:{
                    displaystyle:true,
                    rowspacing:".5em",
                    width:g.config.MultLineWidth,
                    columnwidth:"100%",
                    side:g.config.TagSide,
                    minlabelspacing:g.config.TagIndent
                }
            })
        },
        AMSarray:function(j,i,h,l,k){
            this.Push(j);
            if(h){
                this.checkEqnEnv()
            }
            l=l.replace(/[^clr]/g,"").split("").join(" ");
            l=l.replace(/l/g,"left").replace(/r/g,"right").replace(/c/g,"center");
            return e.AMSarray(j.name,i,h,this.stack).With({
                arraydef:{
                    displaystyle:true,
                    rowspacing:".5em",
                    columnalign:l,
                    columnspacing:(k||"1em"),
                    rowspacing:"3pt",
                    side:g.config.TagSide,
                    minlabelspacing:g.config.TagIndent
                }
            })
        },
        AlignAt:function(k,i,h){
            var p,j,o="",m=[];
            if(!h){
                j=this.GetBrackets("\\begin{"+k.name+"}")
            }
            p=this.GetArgument("\\begin{"+k.name+"}");
            if(p.match(/[^0-9]/)){
                g.Error("Argument to \\begin{"+k.name+"} must me a positive integer")
            }while(p>0){
                o+="rl";
                m.push("0em 0em");
                p--
            }
            m=m.join(" ");
            if(h){
                return this.AMSarray(k,i,h,o,m)
            }
            var l=this.Array.call(this,k,null,null,o,m,".5em","D");
            return this.setArrayAlign(l,j)
        },
        EquationBegin:function(h,i){
            this.checkEqnEnv();
            this.stack.global.forcetag=(i&&a.autoNumber!=="none");
            return h
        },
        EquationStar:function(h,i){
            this.stack.global.tagged=true;
            return i
        },
        checkEqnEnv:function(){
            if(this.stack.global.eqnenv){
                g.Error("Erroneous nesting of equation structures")
            }
            this.stack.global.eqnenv=true
        },
        MultiIntegral:function(h,l){
            var k=this.GetNext();
            if(k==="\\"){
                var j=this.i;
                k=this.GetArgument(h);
                this.i=j;
                if(k==="\\limits"){
                    if(h==="\\idotsint"){
                        l="\\!\\!\\mathop{\\,\\,"+l+"}"
                    }else{
                        l="\\!\\!\\!\\mathop{\\,\\,\\,"+l+"}"
                    }
                }
            }
            this.string=l+" "+this.string.slice(this.i);
            this.i=0
        },
        xArrow:function(j,n,m,h){
            var k={
                width:"+"+(m+h)+"mu",
                lspace:m+"mu"
            };
        
            var o=this.GetBrackets(j),p=this.ParseArg(j);
            var q=b.mo(b.chars(String.fromCharCode(n))).With({
                stretchy:true,
                texClass:b.TEXCLASS.REL
            });
            var i=b.munderover(q);
            i.SetData(i.over,b.mpadded(p).With(k).With({
                voffset:".15em"
            }));
            if(o){
                o=g.Parse(o,this.stack.env).mml();
                i.SetData(i.under,b.mpadded(o).With(k).With({
                    voffset:"-.24em"
                }))
            }
            this.Push(i)
        },
        GetDelimiterArg:function(h){
            var i=this.trimSpaces(this.GetArgument(h));
            if(i==""){
                return null
            }
            if(d.delimiter[i]==null){
                g.Error("Missing or unrecognized delimiter for "+h)
            }
            return this.convertDelimiter(i)
        },
        GetStar:function(){
            var h=(this.GetNext()==="*");
            if(h){
                this.i++
            }
            return h
        }
    });
    e.Augment({
        autoTag:function(){
            var i=this.global;
            if(!i.notag){
                f.number++;
                i.tagID=a.formatNumber(f.number.toString());
                var h=g.Parse("\\text{"+a.formatTag(i.tagID)+"}",{}).mml();
                i.tag=b.mtd(h.With({
                    id:a.formatID(i.tagID)
                }))
            }
        },
        getTag:function(){
            var i=this.global,h=i.tag;
            i.tagged=true;
            if(i.label){
                f.eqlabels[i.label]=i.tagID;
                if(a.useLabelIds){
                    h.id=a.formatID(i.label)
                }
            }
            delete i.tag;
            delete i.tagID;
            delete i.label;
            return h
        }
    });
    e.multline=e.array.Subclass({
        type:"multline",
        Init:function(i,h){
            this.SUPER(arguments).Init.apply(this);
            this.numbered=(i&&a.autoNumber!=="none");
            this.save={
                notag:h.global.notag
            };
            
            h.global.tagged=!i&&!h.global.forcetag
        },
        EndEntry:function(){
            var h=b.mtd.apply(b,this.data);
            if(this.data.shove){
                h.columnalign=this.data.shove
            }
            this.row.push(h);
            this.data=[]
        },
        EndRow:function(){
            if(this.row.length!=1){
                g.Error("multline rows must have exactly one column")
            }
            this.table.push(this.row);
            this.row=[]
        },
        EndTable:function(){
            this.SUPER(arguments).EndTable.call(this);
            if(this.table.length){
                var j=this.table.length-1,l,k=-1;
                if(!this.table[0][0].columnalign){
                    this.table[0][0].columnalign=b.ALIGN.LEFT
                }
                if(!this.table[j][0].columnalign){
                    this.table[j][0].columnalign=b.ALIGN.RIGHT
                }
                if(!this.global.tag&&this.numbered){
                    this.autoTag()
                }
                if(this.global.tag&&!this.global.notags){
                    k=(this.arraydef.side==="left"?0:this.table.length-1);
                    this.table[k]=[this.getTag()].concat(this.table[k])
                }
                for(l=0,j=this.table.length;l<j;l++){
                    var h=(l===k?b.mlabeledtr:b.mtr);
                    this.table[l]=h.apply(b,this.table[l])
                }
            }
            this.global.notag=this.save.notag
        }
    });
    e.AMSarray=e.array.Subclass({
        type:"AMSarray",
        Init:function(k,j,i,h){
            this.SUPER(arguments).Init.apply(this);
            this.numbered=(j&&a.autoNumber!=="none");
            this.save={
                notags:h.global.notags,
                notag:h.global.notag
            };
            
            h.global.notags=(i?null:k);
            h.global.tagged=!j&&!h.global.forcetag
        },
        EndRow:function(){
            var h=b.mtr;
            if(!this.global.tag&&this.numbered){
                this.autoTag()
            }
            if(this.global.tag&&!this.global.notags){
                this.row=[this.getTag()].concat(this.row);
                h=b.mlabeledtr
            }
            if(this.numbered){
                delete this.global.notag
            }
            this.table.push(h.apply(b,this.row));
            this.row=[]
        },
        EndTable:function(){
            this.SUPER(arguments).EndTable.call(this);
            this.global.notags=this.save.notags;
            this.global.notag=this.save.notag
        }
    });
    e.start.Augment({
        oldCheckItem:e.start.prototype.checkItem,
        checkItem:function(j){
            if(j.type==="stop"){
                var h=this.mmlData(),i=this.global;
                if(f.display&&!i.tag&&!i.tagged&&!i.isInner&&(a.autoNumber==="all"||i.forcetag)){
                    this.autoTag()
                }
                if(i.tag){
                    var l=[this.getTag(),b.mtd(h)];
                    var k={
                        side:g.config.TagSide,
                        minlabelspacing:g.config.TagIndent,
                        columnalign:h.displayAlign
                    };
                    
                    if(h.displayAlign===b.INDENTALIGN.LEFT){
                        k.width="100%";
                        if(h.displayIndent&&!String(h.displayIndent).match(/^0+(\.0*)?($|[a-z%])/)){
                            k.columnwidth=h.displayIndent+" fit";
                            k.columnspacing="0";
                            l=[l[0],b.mtd(),l[1]]
                        }
                    }else{
                        if(h.displayAlign===b.INDENTALIGN.RIGHT){
                            k.width="100%";
                            if(h.displayIndent&&!String(h.displayIndent).match(/^0+(\.0*)?($|[a-z%])/)){
                                k.columnwidth="fit "+h.displayIndent;
                                k.columnspacing="0";
                                l[2]=b.mtd()
                            }
                        }
                    }
                    h=b.mtable(b.mlabeledtr.apply(b,l)).With(k)
                }
                return e.mml(h)
            }
            return this.oldCheckItem.call(this,j)
        }
    });
    g.prefilterHooks.Add(function(h){
        f.display=h.display;
        f.number=f.startNumber;
        f.eqlabels={};
    
        f.badref=false;
        if(f.refUpdate){
            f.number=h.script.MathJax.startNumber
        }
    });
    g.postfilterHooks.Add(function(h){
        h.script.MathJax.startNumber=f.startNumber;
        f.startNumber=f.number;
        MathJax.Hub.Insert(f.labels,f.eqlabels);
        if(f.badref&&!h.math.texError){
            f.refs.push(h.script)
        }
    });
    MathJax.Hub.Register.MessageHook("Begin Math Input",function(){
        f.refs=[];
        f.refUpdate=false
    });
    MathJax.Hub.Register.MessageHook("End Math Input",function(k){
        if(f.refs.length){
            f.refUpdate=true;
            for(var j=0,h=f.refs.length;j<h;j++){
                f.refs[j].MathJax.state=MathJax.ElementJax.STATE.UPDATE
            }
            return MathJax.Hub.processInput({
                scripts:f.refs,
                start:new Date().getTime(),
                i:0,
                j:0,
                jax:{},
                jaxIDs:[]
            })
        }
        return null
    });
    g.resetEquationNumbers=function(i,h){
        f.startNumber=(i||0);
        if(!h){
            f.labels={}
        }
    };

    MathJax.Hub.Startup.signal.Post("TeX AMSmath Ready")
});
MathJax.Ajax.loadComplete("[MathJax]/extensions/TeX/AMSmath.js");

MathJax.Extension["TeX/AMSsymbols"]={
    version:"2.0"
};

MathJax.Hub.Register.StartupHook("TeX Jax Ready",function(){
    var a=MathJax.ElementJax.mml,b=MathJax.InputJax.TeX.Definitions;
    b.Add({
        mathchar0mi:{
            digamma:"03DD",
            varkappa:"03F0",
            varGamma:["0393",{
                mathvariant:a.VARIANT.ITALIC
            }],
            varDelta:["0394",{
                mathvariant:a.VARIANT.ITALIC
            }],
            varTheta:["0398",{
                mathvariant:a.VARIANT.ITALIC
            }],
            varLambda:["039B",{
                mathvariant:a.VARIANT.ITALIC
            }],
            varXi:["039E",{
                mathvariant:a.VARIANT.ITALIC
            }],
            varPi:["03A0",{
                mathvariant:a.VARIANT.ITALIC
            }],
            varSigma:["03A3",{
                mathvariant:a.VARIANT.ITALIC
            }],
            varUpsilon:["03A5",{
                mathvariant:a.VARIANT.ITALIC
            }],
            varPhi:["03A6",{
                mathvariant:a.VARIANT.ITALIC
            }],
            varPsi:["03A8",{
                mathvariant:a.VARIANT.ITALIC
            }],
            varOmega:["03A9",{
                mathvariant:a.VARIANT.ITALIC
            }],
            beth:"2136",
            gimel:"2137",
            daleth:"2138",
            backprime:["2035",{
                variantForm:true
            }],
            hslash:["210F",{
                variantForm:true
            }],
            varnothing:["2205",{
                variantForm:true
            }],
            blacktriangle:"25B2",
            triangledown:"25BD",
            blacktriangledown:"25BC",
            square:"25A1",
            Box:"25A1",
            blacksquare:"25A0",
            lozenge:"25CA",
            Diamond:"25CA",
            blacklozenge:"29EB",
            circledS:["24C8",{
                mathvariant:a.VARIANT.NORMAL
            }],
            bigstar:"2605",
            sphericalangle:"2222",
            measuredangle:"2221",
            nexists:"2204",
            complement:"2201",
            mho:"2127",
            eth:["00F0",{
                mathvariant:a.VARIANT.NORMAL
            }],
            Finv:"2132",
            diagup:"2571",
            Game:"2141",
            diagdown:"2572",
            Bbbk:["006B",{
                mathvariant:a.VARIANT.DOUBLESTRUCK
            }],
            yen:"00A5",
            circledR:"00AE",
            checkmark:"2713",
            maltese:"2720"
        },
        mathchar0mo:{
            dotplus:"2214",
            ltimes:"22C9",
            smallsetminus:["2216",{
                variantForm:true
            }],
            rtimes:"22CA",
            Cap:"22D2",
            doublecap:"22D2",
            leftthreetimes:"22CB",
            Cup:"22D3",
            doublecup:"22D3",
            rightthreetimes:"22CC",
            barwedge:"22BC",
            curlywedge:"22CF",
            veebar:"22BB",
            curlyvee:"22CE",
            doublebarwedge:"2A5E",
            boxminus:"229F",
            circleddash:"229D",
            boxtimes:"22A0",
            circledast:"229B",
            boxdot:"22A1",
            circledcirc:"229A",
            boxplus:"229E",
            centerdot:"22C5",
            divideontimes:"22C7",
            intercal:"22BA",
            leqq:"2266",
            geqq:"2267",
            leqslant:"2A7D",
            geqslant:"2A7E",
            eqslantless:"2A95",
            eqslantgtr:"2A96",
            lesssim:"2272",
            gtrsim:"2273",
            lessapprox:"2A85",
            gtrapprox:"2A86",
            approxeq:"224A",
            lessdot:"22D6",
            gtrdot:"22D7",
            lll:"22D8",
            llless:"22D8",
            ggg:"22D9",
            gggtr:"22D9",
            lessgtr:"2276",
            gtrless:"2277",
            lesseqgtr:"22DA",
            gtreqless:"22DB",
            lesseqqgtr:"2A8B",
            gtreqqless:"2A8C",
            doteqdot:"2251",
            Doteq:"2251",
            eqcirc:"2256",
            risingdotseq:"2253",
            circeq:"2257",
            fallingdotseq:"2252",
            triangleq:"225C",
            backsim:"223D",
            thicksim:["223C",{
                variantForm:true
            }],
            backsimeq:"22CD",
            thickapprox:["2248",{
                variantForm:true
            }],
            subseteqq:"2AC5",
            supseteqq:"2AC6",
            Subset:"22D0",
            Supset:"22D1",
            sqsubset:"228F",
            sqsupset:"2290",
            preccurlyeq:"227C",
            succcurlyeq:"227D",
            curlyeqprec:"22DE",
            curlyeqsucc:"22DF",
            precsim:"227E",
            succsim:"227F",
            precapprox:"2AB7",
            succapprox:"2AB8",
            vartriangleleft:"22B2",
            lhd:"22B2",
            vartriangleright:"22B3",
            rhd:"22B3",
            trianglelefteq:"22B4",
            unlhd:"22B4",
            trianglerighteq:"22B5",
            unrhd:"22B5",
            vDash:"22A8",
            Vdash:"22A9",
            Vvdash:"22AA",
            smallsmile:"2323",
            shortmid:["2223",{
                variantForm:true
            }],
            smallfrown:"2322",
            shortparallel:["2225",{
                variantForm:true
            }],
            bumpeq:"224F",
            between:"226C",
            Bumpeq:"224E",
            pitchfork:"22D4",
            varpropto:"221D",
            backepsilon:"220D",
            blacktriangleleft:"25C0",
            blacktriangleright:"25B6",
            therefore:"2234",
            because:"2235",
            eqsim:"2242",
            vartriangle:["25B3",{
                variantForm:true
            }],
            Join:"22C8",
            nless:"226E",
            ngtr:"226F",
            nleq:"2270",
            ngeq:"2271",
            nleqslant:["2A87",{
                variantForm:true
            }],
            ngeqslant:["2A88",{
                variantForm:true
            }],
            nleqq:["2270",{
                variantForm:true
            }],
            ngeqq:["2271",{
                variantForm:true
            }],
            lneq:"2A87",
            gneq:"2A88",
            lneqq:"2268",
            gneqq:"2269",
            lvertneqq:["2268",{
                variantForm:true
            }],
            gvertneqq:["2269",{
                variantForm:true
            }],
            lnsim:"22E6",
            gnsim:"22E7",
            lnapprox:"2A89",
            gnapprox:"2A8A",
            nprec:"2280",
            nsucc:"2281",
            npreceq:["22E0",{
                variantForm:true
            }],
            nsucceq:["22E1",{
                variantForm:true
            }],
            precneqq:"2AB5",
            succneqq:"2AB6",
            precnsim:"22E8",
            succnsim:"22E9",
            precnapprox:"2AB9",
            succnapprox:"2ABA",
            nsim:"2241",
            ncong:"2246",
            nshortmid:["2224",{
                variantForm:true
            }],
            nshortparallel:["2226",{
                variantForm:true
            }],
            nmid:"2224",
            nparallel:"2226",
            nvdash:"22AC",
            nvDash:"22AD",
            nVdash:"22AE",
            nVDash:"22AF",
            ntriangleleft:"22EA",
            ntriangleright:"22EB",
            ntrianglelefteq:"22EC",
            ntrianglerighteq:"22ED",
            nsubseteq:"2288",
            nsupseteq:"2289",
            nsubseteqq:["2288",{
                variantForm:true
            }],
            nsupseteqq:["2289",{
                variantForm:true
            }],
            subsetneq:"228A",
            supsetneq:"228B",
            varsubsetneq:["228A",{
                variantForm:true
            }],
            varsupsetneq:["228B",{
                variantForm:true
            }],
            subsetneqq:"2ACB",
            supsetneqq:"2ACC",
            varsubsetneqq:["2ACB",{
                variantForm:true
            }],
            varsupsetneqq:["2ACC",{
                variantForm:true
            }],
            leftleftarrows:"21C7",
            rightrightarrows:"21C9",
            leftrightarrows:"21C6",
            rightleftarrows:"21C4",
            Lleftarrow:"21DA",
            Rrightarrow:"21DB",
            twoheadleftarrow:"219E",
            twoheadrightarrow:"21A0",
            leftarrowtail:"21A2",
            rightarrowtail:"21A3",
            looparrowleft:"21AB",
            looparrowright:"21AC",
            leftrightharpoons:"21CB",
            rightleftharpoons:["21CC",{
                variantForm:true
            }],
            curvearrowleft:"21B6",
            curvearrowright:"21B7",
            circlearrowleft:"21BA",
            circlearrowright:"21BB",
            Lsh:"21B0",
            Rsh:"21B1",
            upuparrows:"21C8",
            downdownarrows:"21CA",
            upharpoonleft:"21BF",
            upharpoonright:"21BE",
            downharpoonleft:"21C3",
            restriction:"21BE",
            multimap:"22B8",
            downharpoonright:"21C2",
            leftrightsquigarrow:"21AD",
            rightsquigarrow:"21DD",
            leadsto:"21DD",
            dashrightarrow:"21E2",
            dashleftarrow:"21E0",
            nleftarrow:"219A",
            nrightarrow:"219B",
            nLeftarrow:"21CD",
            nRightarrow:"21CF",
            nleftrightarrow:"21AE",
            nLeftrightarrow:"21CE"
        },
        delimiter:{
            "\\ulcorner":"231C",
            "\\urcorner":"231D",
            "\\llcorner":"231E",
            "\\lrcorner":"231F"
        },
        macros:{
            implies:["Macro","\\;\\Longrightarrow\\;"],
            impliedby:["Macro","\\;\\Longleftarrow\\;"]
        }
    },null,true);
    var c=a.mo.OPTYPES.REL;
    MathJax.Hub.Insert(a.mo.prototype,{
        OPTABLE:{
            infix:{
                "\u2322":c,
                "\u2323":c,
                "\u25B3":c,
                "\uE006":c,
                "\uE007":c,
                "\uE00C":c,
                "\uE00D":c,
                "\uE00E":c,
                "\uE00F":c,
                "\uE010":c,
                "\uE011":c,
                "\uE016":c,
                "\uE017":c,
                "\uE018":c,
                "\uE019":c,
                "\uE01A":c,
                "\uE01B":c,
                "\uE04B":c,
                "\uE04F":c
            }
        }
    });
    MathJax.Hub.Startup.signal.Post("TeX AMSsymbols Ready")
});
MathJax.Hub.Register.StartupHook("HTML-CSS Jax Ready",function(){
    var a=MathJax.OutputJax["HTML-CSS"];
    var b=a.FONTDATA.VARIANT;
    if(a.fontInUse==="TeX"){
        b["-TeX-variant"]={
            fonts:["MathJax_AMS","MathJax_Main","MathJax_Size1"],
            remap:{
                8808:57356,
                8809:57357,
                8816:57361,
                8817:57358,
                10887:57360,
                10888:57359,
                8740:57350,
                8742:57351,
                8840:57366,
                8841:57368,
                8842:57370,
                8843:57371,
                10955:57367,
                10956:57369,
                988:57352,
                1008:57353
            }
        };
        
        if(a.msieIE6){
            MathJax.Hub.Insert(b["-TeX-variant"].remap,{
                8592:[58049,"-WinIE6"],
                8594:[58048,"-WinIE6"],
                8739:[58050,"-WinIE6"],
                8741:[58051,"-WinIE6"],
                8764:[58052,"-WinIE6"],
                9651:[58067,"-WinIE6"]
            })
        }
    }
    if(a.fontInUse==="STIX"){
        MathJax.Hub.Register.StartupHook("TeX Jax Ready",function(){
            var c=MathJax.InputJax.TeX.Definitions;
            c.mathchar0mi.varnothing="2205";
            c.mathchar0mi.hslash="210F";
            c.mathchar0mi.blacktriangle="25B4";
            c.mathchar0mi.blacktriangledown="25BE";
            c.mathchar0mi.square="25FB";
            c.mathchar0mi.blacksquare="25FC";
            c.mathchar0mi.vartriangle=["25B3",{
                mathsize:"71%"
            }];
            c.mathchar0mi.triangledown=["25BD",{
                mathsize:"71%"
            }];
            c.mathchar0mo.blacktriangleleft="25C2";
            c.mathchar0mo.blacktriangleright="25B8";
            c.mathchar0mo.smallsetminus="2216";
            MathJax.Hub.Insert(b["-STIX-variant"],{
                remap:{
                    10887:57360,
                    10888:57359,
                    8816:57361,
                    8817:57358,
                    8928:57419,
                    8929:57423,
                    8840:57366,
                    8841:57368
                }
            })
        })
    }
});
MathJax.Hub.Register.StartupHook("SVG Jax Ready",function(){
    var b=MathJax.OutputJax.SVG;
    var a=b.FONTDATA.VARIANT;
    a["-TeX-variant"]={
        fonts:["MathJax_AMS","MathJax_Main","MathJax_Size1"],
        remap:{
            8808:57356,
            8809:57357,
            8816:57361,
            8817:57358,
            10887:57360,
            10888:57359,
            8740:57350,
            8742:57351,
            8840:57366,
            8841:57368,
            8842:57370,
            8843:57371,
            10955:57367,
            10956:57369,
            988:57352,
            1008:57353
        }
    }
});
MathJax.Ajax.loadComplete("[MathJax]/extensions/TeX/AMSsymbols.js");

(function(b,c){
    var a;
    b.Parse=MathJax.Object.Subclass({
        Init:function(d){
            this.Parse(d)
        },
        Parse:function(g){
            var h;
            if(typeof g!=="string"){
                h=g.parentNode
            }else{
                if(g.match(/^<[a-z]+:/i)&&!g.match(/^<[^<>]* xmlns:/)){
                    g=g.replace(/^<([a-z]+)(:math)/i,'<$1$2 xmlns:$1="http://www.w3.org/1998/Math/MathML"')
                }
                var d=g.match(/^(<math( ('.*?'|".*?"|[^>])+)>)/i);
                if(d&&d[2].match(/ (?!xmlns=)[a-z]+=\"http:/i)){
                    g=d[1].replace(/ (?!xmlns=)([a-z]+=(['"])http:.*?\2)/ig," xmlns:$1 $1")+g.substr(d[0].length)
                }
                g=g.replace(/^\s*(?:\/\/)?<!(--)?\[CDATA\[((.|\n)*)(\/\/)?\]\]\1>\s*$/,"$2");
                g=g.replace(/&([a-z][a-z0-9]*);/ig,this.replaceEntity);
                h=b.ParseXML(g);
                if(h==null){
                    b.Error("Error parsing MathML")
                }
            }
            var f=h.getElementsByTagName("parsererror")[0];
            if(f){
                b.Error("Error parsing MathML: "+f.textContent.replace(/This page.*?errors:|XML Parsing Error: |Below is a rendering of the page.*/g,""))
            }
            if(h.childNodes.length!==1){
                b.Error("MathML must be formed by a single element")
            }
            if(h.firstChild.nodeName.toLowerCase()==="html"){
                var e=h.getElementsByTagName("h1")[0];
                if(e&&e.textContent==="XML parsing error"&&e.nextSibling){
                    b.Error("Error parsing MathML: "+String(e.nextSibling.nodeValue).replace(/fatal parsing error: /,""))
                }
            }
            if(h.firstChild.nodeName.toLowerCase().replace(/^[a-z]+:/,"")!=="math"){
                b.Error("MathML must be formed by a <math> element, not <"+h.firstChild.nodeName+">")
            }
            this.mml=this.MakeMML(h.firstChild)
        },
        MakeMML:function(g){
            var h=String(g.getAttribute("class")||"");
            var e,f=g.nodeName.toLowerCase().replace(/^[a-z]+:/,"");
            var d=(h.match(/(^| )MJX-TeXAtom-([^ ]*)/));
            if(d){
                e=this.TeXAtom(d[2])
            }else{
                if(!(a[f]&&a[f].isa&&a[f].isa(a.mbase))){
                    MathJax.Hub.signal.Post(["MathML Jax - unknown node type",f]);
                    return a.merror("Unknown node type: "+f)
                }else{
                    e=a[f]()
                }
            }
            this.AddAttributes(e,g);
            this.CheckClass(e,h);
            this.AddChildren(e,g);
            if(b.config.useMathMLspacing){
                e.useMMLspacing=8
            }
            return e
        },
        TeXAtom:function(e){
            var d=a.TeXAtom().With({
                texClass:a.TEXCLASS[e]
            });
            if(d.texClass===a.TEXCLASS.OP){
                d.movesupsub=d.movablelimits=true
            }
            return d
        },
        CheckClass:function(e,g){
            g=g.split(/ /);
            var h=[];
            for(var f=0,d=g.length;f<d;f++){
                if(g[f].substr(0,4)==="MJX-"){
                    if(g[f]==="MJX-arrow"){
                        e.arrow=true
                    }else{
                        if(g[f]==="MJX-variant"){
                            e.variantForm=true;
                            if(!MathJax.Extension["TeX/AMSsymbols"]){
                                MathJax.Hub.RestartAfter(MathJax.Ajax.Require("[MathJax]/extensions/TeX/AMSsymbols.js"))
                            }
                        }else{
                            if(g[f].substr(0,11)!=="MJX-TeXAtom"){
                                e.mathvariant=g[f].substr(3);
                                if(e.mathvariant==="-tex-caligraphic-bold"||e.mathvariant==="-tex-oldstyle-bold"){
                                    if(!MathJax.Extension["TeX/boldsymbol"]){
                                        MathJax.Hub.RestartAfter(MathJax.Ajax.Require("[MathJax]/extensions/TeX/boldsymbol.js"))
                                    }
                                }
                            }
                        }
                    }
                }else{
                    h.push(g[f])
                }
            }
            if(h.length){
                e["class"]=h.join(" ")
            }else{
                delete e["class"]
            }
        },
        AddAttributes:function(f,h){
            f.attr={};
    
            f.attrNames=[];
            for(var g=0,d=h.attributes.length;g<d;g++){
                var e=h.attributes[g].name;
                if(e=="xlink:href"){
                    e="href"
                }
                if(e.match(/:/)){
                    continue
                }
                var j=h.attributes[g].value;
                if(j.toLowerCase()==="true"){
                    j=true
                }else{
                    if(j.toLowerCase()==="false"){
                        j=false
                    }
                }
                if(f.defaults[e]!=null||a.copyAttributes[e]){
                    f[e]=j
                }else{
                    f.attr[e]=j
                }
                f.attrNames.push(e)
            }
        },
        AddChildren:function(f,h){
            for(var g=0,d=h.childNodes.length;g<d;g++){
                var k=h.childNodes[g];
                if(k.nodeName==="#comment"){
                    continue
                }
                if(k.nodeName==="#text"){
                    if(f.isToken&&!f.mmlSelfClosing){
                        var j=k.nodeValue.replace(/&([a-z][a-z0-9]*);/ig,this.replaceEntity);
                        f.Append(a.chars(this.trimSpace(j)))
                    }else{
                        if(k.nodeValue.match(/\S/)){
                            b.Error("Unexpected text node: '"+k.nodeValue+"'")
                        }
                    }
                }else{
                    if(f.type==="annotation-xml"){
                        f.Append(a.xml(k))
                    }else{
                        var e=this.MakeMML(k);
                        f.Append(e);
                        if(e.mmlSelfClosing&&e.data.length){
                            f.Append.apply(f,e.data);
                            e.data=[]
                        }
                    }
                }
            }
        },
        trimSpace:function(d){
            return d.replace(/[\t\n\r]/g," ").replace(/^ +/,"").replace(/ +$/,"").replace(/  +/g," ")
        },
        replaceEntity:function(f,e){
            if(e.match(/^(lt|amp|quot)$/)){
                return f
            }
            if(b.Parse.Entity[e]){
                return b.Parse.Entity[e]
            }
            var g=e.charAt(0).toLowerCase();
            var d=e.match(/^[a-zA-Z](fr|scr|opf)$/);
            if(d){
                g=d[1]
            }
            if(!b.Parse.loaded[g]){
                b.Parse.loaded[g]=true;
                MathJax.Hub.RestartAfter(MathJax.Ajax.Require(b.entityDir+"/"+g+".js"))
            }
            return f
        }
    },{
        loaded:[]
    });
    b.Augment({
        sourceMenuTitle:"Original MathML",
        prefilterHooks:MathJax.Callback.Hooks(true),
        postfilterHooks:MathJax.Callback.Hooks(true),
        Translate:function(d){
            if(!this.ParseXML){
                this.ParseXML=this.createParser()
            }
            var e,g,h={
                script:d
            };
        
            if(d.firstChild&&d.firstChild.nodeName.toLowerCase().replace(/^[a-z]+:/,"")==="math"){
                h.math=d.firstChild;
                this.prefilterHooks.Execute(h);
                g=h.math
            }else{
                g=MathJax.HTML.getScript(d);
                if(c.isMSIE){
                    g=g.replace(/(&nbsp;)+$/,"")
                }
                h.math=g;
                this.prefilterHooks.Execute(h);
                g=h.math
            }
            try{
                e=b.Parse(g).mml
            }catch(f){
                if(!f.mathmlError){
                    throw f
                }
                e=this.formatError(f,g,d)
            }
            h.math=a(e);
            this.postfilterHooks.Execute(h);
            return h.math
        },
        prefilterMath:function(e,d){
            return e
        },
        prefilterMathML:function(e,d){
            return e
        },
        formatError:function(g,f,d){
            var e=g.message.replace(/\n.*/,"");
            MathJax.Hub.signal.Post(["MathML Jax - parse error",e,f,d]);
            return a.merror(e)
        },
        Error:function(d){
            throw MathJax.Hub.Insert(Error(d),{
                mathmlError:true
            })
        },
        parseDOM:function(d){
            return this.parser.parseFromString(d,"text/xml")
        },
        parseMS:function(d){
            return(this.parser.loadXML(d)?this.parser:null)
        },
        parseDIV:function(d){
            this.div.innerHTML=d.replace(/<([a-z]+)([^>]*)\/>/g,"<$1$2></$1>");
            return this.div
        },
        parseError:function(d){
            return null
        },
        createParser:function(){
            if(window.DOMParser){
                this.parser=new DOMParser();
                return(this.parseDOM)
            }else{
                if(window.ActiveXObject){
                    var e=["MSXML2.DOMDocument.6.0","MSXML2.DOMDocument.5.0","MSXML2.DOMDocument.4.0","MSXML2.DOMDocument.3.0","MSXML2.DOMDocument.2.0","Microsoft.XMLDOM"];
                    for(var f=0,d=e.length;f<d&&!this.parser;f++){
                        try{
                            this.parser=new ActiveXObject(e[f])
                        }catch(g){}
                    }
                    if(!this.parser){
                        alert("MathJax can't create an XML parser for MathML.  Check that\nthe 'Script ActiveX controls marked safe for scripting' security\nsetting is enabled (use the Internet Options item in the Tools\nmenu, and select the Security panel, then press the Custom Level\nbutton to check this).\n\nMathML equations will not be able to be processed by MathJax.");
                        return(this.parseError)
                    }
                    this.parser.async=false;
                    return(this.parseMS)
                }
            }
            this.div=MathJax.Hub.Insert(document.createElement("div"),{
                style:{
                    visibility:"hidden",
                    overflow:"hidden",
                    height:"1px",
                    position:"absolute",
                    top:0
                }
            });
            if(!document.body.firstChild){
                document.body.appendChild(this.div)
            }else{
                document.body.insertBefore(this.div,document.body.firstChild)
            }
            return(this.parseDIV)
        },
        Startup:function(){
            a=MathJax.ElementJax.mml;
            a.mspace.Augment({
                mmlSelfClosing:true
            });
            a.none.Augment({
                mmlSelfClosing:true
            });
            a.mprescripts.Augment({
                mmlSelfClosing:true
            })
        }
    });
    b.prefilterHooks.Add(function(d){
        d.math=(typeof(d.math)==="string"?b.prefilterMath(d.math,d.script):b.prefilterMathML(d.math,d.script))
    });
    b.Parse.Entity={
        ApplyFunction:"\u2061",
        Backslash:"\u2216",
        Because:"\u2235",
        Breve:"\u02D8",
        Cap:"\u22D2",
        CenterDot:"\u00B7",
        CircleDot:"\u2299",
        CircleMinus:"\u2296",
        CirclePlus:"\u2295",
        CircleTimes:"\u2297",
        Congruent:"\u2261",
        ContourIntegral:"\u222E",
        Coproduct:"\u2210",
        Cross:"\u2A2F",
        Cup:"\u22D3",
        CupCap:"\u224D",
        Dagger:"\u2021",
        Del:"\u2207",
        Delta:"\u0394",
        Diamond:"\u22C4",
        DifferentialD:"\u2146",
        DotEqual:"\u2250",
        DoubleDot:"\u00A8",
        DoubleRightTee:"\u22A8",
        DoubleVerticalBar:"\u2225",
        DownArrow:"\u2193",
        DownLeftVector:"\u21BD",
        DownRightVector:"\u21C1",
        DownTee:"\u22A4",
        Downarrow:"\u21D3",
        Element:"\u2208",
        EqualTilde:"\u2242",
        Equilibrium:"\u21CC",
        Exists:"\u2203",
        ExponentialE:"\u2147",
        FilledVerySmallSquare:"\u25AA",
        ForAll:"\u2200",
        Gamma:"\u0393",
        Gg:"\u22D9",
        GreaterEqual:"\u2265",
        GreaterEqualLess:"\u22DB",
        GreaterFullEqual:"\u2267",
        GreaterLess:"\u2277",
        GreaterSlantEqual:"\u2A7E",
        GreaterTilde:"\u2273",
        Hacek:"\u02C7",
        Hat:"\u005E",
        HumpDownHump:"\u224E",
        HumpEqual:"\u224F",
        Im:"\u2111",
        ImaginaryI:"\u2148",
        Integral:"\u222B",
        Intersection:"\u22C2",
        InvisibleComma:"\u2063",
        InvisibleTimes:"\u2062",
        Lambda:"\u039B",
        Larr:"\u219E",
        LeftAngleBracket:"\u27E8",
        LeftArrow:"\u2190",
        LeftArrowRightArrow:"\u21C6",
        LeftCeiling:"\u2308",
        LeftDownVector:"\u21C3",
        LeftFloor:"\u230A",
        LeftRightArrow:"\u2194",
        LeftTee:"\u22A3",
        LeftTriangle:"\u22B2",
        LeftTriangleEqual:"\u22B4",
        LeftUpVector:"\u21BF",
        LeftVector:"\u21BC",
        Leftarrow:"\u21D0",
        Leftrightarrow:"\u21D4",
        LessEqualGreater:"\u22DA",
        LessFullEqual:"\u2266",
        LessGreater:"\u2276",
        LessSlantEqual:"\u2A7D",
        LessTilde:"\u2272",
        Ll:"\u22D8",
        Lleftarrow:"\u21DA",
        LongLeftArrow:"\u27F5",
        LongLeftRightArrow:"\u27F7",
        LongRightArrow:"\u27F6",
        Longleftarrow:"\u27F8",
        Longleftrightarrow:"\u27FA",
        Longrightarrow:"\u27F9",
        Lsh:"\u21B0",
        MinusPlus:"\u2213",
        NestedGreaterGreater:"\u226B",
        NestedLessLess:"\u226A",
        NotDoubleVerticalBar:"\u2226",
        NotElement:"\u2209",
        NotEqual:"\u2260",
        NotExists:"\u2204",
        NotGreater:"\u226F",
        NotGreaterEqual:"\u2271",
        NotLeftTriangle:"\u22EA",
        NotLeftTriangleEqual:"\u22EC",
        NotLess:"\u226E",
        NotLessEqual:"\u2270",
        NotPrecedes:"\u2280",
        NotPrecedesSlantEqual:"\u22E0",
        NotRightTriangle:"\u22EB",
        NotRightTriangleEqual:"\u22ED",
        NotSubsetEqual:"\u2288",
        NotSucceeds:"\u2281",
        NotSucceedsSlantEqual:"\u22E1",
        NotSupersetEqual:"\u2289",
        NotTilde:"\u2241",
        NotVerticalBar:"\u2224",
        Omega:"\u03A9",
        OverBar:"\u203E",
        OverBrace:"\u23DE",
        PartialD:"\u2202",
        Phi:"\u03A6",
        Pi:"\u03A0",
        PlusMinus:"\u00B1",
        Precedes:"\u227A",
        PrecedesEqual:"\u2AAF",
        PrecedesSlantEqual:"\u227C",
        PrecedesTilde:"\u227E",
        Product:"\u220F",
        Proportional:"\u221D",
        Psi:"\u03A8",
        Rarr:"\u21A0",
        Re:"\u211C",
        ReverseEquilibrium:"\u21CB",
        RightAngleBracket:"\u27E9",
        RightArrow:"\u2192",
        RightArrowLeftArrow:"\u21C4",
        RightCeiling:"\u2309",
        RightDownVector:"\u21C2",
        RightFloor:"\u230B",
        RightTee:"\u22A2",
        RightTeeArrow:"\u21A6",
        RightTriangle:"\u22B3",
        RightTriangleEqual:"\u22B5",
        RightUpVector:"\u21BE",
        RightVector:"\u21C0",
        Rightarrow:"\u21D2",
        Rrightarrow:"\u21DB",
        Rsh:"\u21B1",
        Sigma:"\u03A3",
        SmallCircle:"\u2218",
        Sqrt:"\u221A",
        Square:"\u25A1",
        SquareIntersection:"\u2293",
        SquareSubset:"\u228F",
        SquareSubsetEqual:"\u2291",
        SquareSuperset:"\u2290",
        SquareSupersetEqual:"\u2292",
        SquareUnion:"\u2294",
        Star:"\u22C6",
        Subset:"\u22D0",
        SubsetEqual:"\u2286",
        Succeeds:"\u227B",
        SucceedsEqual:"\u2AB0",
        SucceedsSlantEqual:"\u227D",
        SucceedsTilde:"\u227F",
        SuchThat:"\u220B",
        Sum:"\u2211",
        Superset:"\u2283",
        SupersetEqual:"\u2287",
        Supset:"\u22D1",
        Therefore:"\u2234",
        Theta:"\u0398",
        Tilde:"\u223C",
        TildeEqual:"\u2243",
        TildeFullEqual:"\u2245",
        TildeTilde:"\u2248",
        UnderBar:"\u005F",
        UnderBrace:"\u23DF",
        Union:"\u22C3",
        UnionPlus:"\u228E",
        UpArrow:"\u2191",
        UpDownArrow:"\u2195",
        UpTee:"\u22A5",
        Uparrow:"\u21D1",
        Updownarrow:"\u21D5",
        Upsilon:"\u03A5",
        Vdash:"\u22A9",
        Vee:"\u22C1",
        VerticalBar:"\u2223",
        VerticalTilde:"\u2240",
        Vvdash:"\u22AA",
        Wedge:"\u22C0",
        Xi:"\u039E",
        acute:"\u00B4",
        aleph:"\u2135",
        alpha:"\u03B1",
        amalg:"\u2A3F",
        and:"\u2227",
        ang:"\u2220",
        angmsd:"\u2221",
        angsph:"\u2222",
        ape:"\u224A",
        backprime:"\u2035",
        backsim:"\u223D",
        backsimeq:"\u22CD",
        beta:"\u03B2",
        beth:"\u2136",
        between:"\u226C",
        bigcirc:"\u25EF",
        bigodot:"\u2A00",
        bigoplus:"\u2A01",
        bigotimes:"\u2A02",
        bigsqcup:"\u2A06",
        bigstar:"\u2605",
        bigtriangledown:"\u25BD",
        bigtriangleup:"\u25B3",
        biguplus:"\u2A04",
        blacklozenge:"\u29EB",
        blacktriangle:"\u25B4",
        blacktriangledown:"\u25BE",
        blacktriangleleft:"\u25C2",
        bowtie:"\u22C8",
        boxdl:"\u2510",
        boxdr:"\u250C",
        boxminus:"\u229F",
        boxplus:"\u229E",
        boxtimes:"\u22A0",
        boxul:"\u2518",
        boxur:"\u2514",
        bsol:"\u005C",
        bull:"\u2022",
        cap:"\u2229",
        check:"\u2713",
        chi:"\u03C7",
        circ:"\u02C6",
        circeq:"\u2257",
        circlearrowleft:"\u21BA",
        circlearrowright:"\u21BB",
        circledR:"\u00AE",
        circledS:"\u24C8",
        circledast:"\u229B",
        circledcirc:"\u229A",
        circleddash:"\u229D",
        clubs:"\u2663",
        colon:"\u003A",
        comp:"\u2201",
        ctdot:"\u22EF",
        cuepr:"\u22DE",
        cuesc:"\u22DF",
        cularr:"\u21B6",
        cup:"\u222A",
        curarr:"\u21B7",
        curlyvee:"\u22CE",
        curlywedge:"\u22CF",
        dagger:"\u2020",
        daleth:"\u2138",
        ddarr:"\u21CA",
        deg:"\u00B0",
        delta:"\u03B4",
        digamma:"\u03DD",
        div:"\u00F7",
        divideontimes:"\u22C7",
        dot:"\u02D9",
        doteqdot:"\u2251",
        dotplus:"\u2214",
        dotsquare:"\u22A1",
        dtdot:"\u22F1",
        ecir:"\u2256",
        efDot:"\u2252",
        egs:"\u2A96",
        ell:"\u2113",
        els:"\u2A95",
        empty:"\u2205",
        epsi:"\u03B5",
        epsiv:"\u03F5",
        erDot:"\u2253",
        eta:"\u03B7",
        eth:"\u00F0",
        flat:"\u266D",
        fork:"\u22D4",
        frown:"\u2322",
        gEl:"\u2A8C",
        gamma:"\u03B3",
        gap:"\u2A86",
        gimel:"\u2137",
        gnE:"\u2269",
        gnap:"\u2A8A",
        gne:"\u2A88",
        gnsim:"\u22E7",
        gt:"\u003E",
        gtdot:"\u22D7",
        harrw:"\u21AD",
        hbar:"\u210F",
        hellip:"\u2026",
        hookleftarrow:"\u21A9",
        hookrightarrow:"\u21AA",
        imath:"\u0131",
        infin:"\u221E",
        intcal:"\u22BA",
        iota:"\u03B9",
        jmath:"\u0237",
        kappa:"\u03BA",
        kappav:"\u03F0",
        lEg:"\u2A8B",
        lambda:"\u03BB",
        lap:"\u2A85",
        larrlp:"\u21AB",
        larrtl:"\u21A2",
        lbrace:"\u007B",
        lbrack:"\u005B",
        le:"\u2264",
        leftleftarrows:"\u21C7",
        leftthreetimes:"\u22CB",
        lessdot:"\u22D6",
        lmoust:"\u23B0",
        lnE:"\u2268",
        lnap:"\u2A89",
        lne:"\u2A87",
        lnsim:"\u22E6",
        longmapsto:"\u27FC",
        looparrowright:"\u21AC",
        lowast:"\u2217",
        loz:"\u25CA",
        lt:"\u003C",
        ltimes:"\u22C9",
        ltri:"\u25C3",
        macr:"\u00AF",
        malt:"\u2720",
        mho:"\u2127",
        mu:"\u03BC",
        multimap:"\u22B8",
        nLeftarrow:"\u21CD",
        nLeftrightarrow:"\u21CE",
        nRightarrow:"\u21CF",
        nVDash:"\u22AF",
        nVdash:"\u22AE",
        natur:"\u266E",
        nearr:"\u2197",
        nharr:"\u21AE",
        nlarr:"\u219A",
        not:"\u00AC",
        nrarr:"\u219B",
        nu:"\u03BD",
        nvDash:"\u22AD",
        nvdash:"\u22AC",
        nwarr:"\u2196",
        omega:"\u03C9",
        omicron:"\u03BF",
        or:"\u2228",
        osol:"\u2298",
        period:"\u002E",
        phi:"\u03C6",
        phiv:"\u03D5",
        pi:"\u03C0",
        piv:"\u03D6",
        prap:"\u2AB7",
        precnapprox:"\u2AB9",
        precneqq:"\u2AB5",
        precnsim:"\u22E8",
        prime:"\u2032",
        psi:"\u03C8",
        rarrtl:"\u21A3",
        rbrace:"\u007D",
        rbrack:"\u005D",
        rho:"\u03C1",
        rhov:"\u03F1",
        rightrightarrows:"\u21C9",
        rightthreetimes:"\u22CC",
        ring:"\u02DA",
        rmoust:"\u23B1",
        rtimes:"\u22CA",
        rtri:"\u25B9",
        scap:"\u2AB8",
        scnE:"\u2AB6",
        scnap:"\u2ABA",
        scnsim:"\u22E9",
        sdot:"\u22C5",
        searr:"\u2198",
        sect:"\u00A7",
        sharp:"\u266F",
        sigma:"\u03C3",
        sigmav:"\u03C2",
        simne:"\u2246",
        smile:"\u2323",
        spades:"\u2660",
        sub:"\u2282",
        subE:"\u2AC5",
        subnE:"\u2ACB",
        subne:"\u228A",
        supE:"\u2AC6",
        supnE:"\u2ACC",
        supne:"\u228B",
        swarr:"\u2199",
        tau:"\u03C4",
        theta:"\u03B8",
        thetav:"\u03D1",
        tilde:"\u02DC",
        times:"\u00D7",
        triangle:"\u25B5",
        triangleq:"\u225C",
        upsi:"\u03C5",
        upuparrows:"\u21C8",
        veebar:"\u22BB",
        vellip:"\u22EE",
        weierp:"\u2118",
        xi:"\u03BE",
        yen:"\u00A5",
        zeta:"\u03B6",
        zigrarr:"\u21DD"
    };

    b.loadComplete("jax.js")
})(MathJax.InputJax.MathML,MathJax.Hub.Browser);

MathJax.Ajax.loadComplete("[MathJax]/config/TeX-AMS-MML_HTMLorMML.js");
