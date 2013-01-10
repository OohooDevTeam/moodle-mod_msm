(function(){tinymce.PluginManager.requireLangPack('subordinate');tinymce.create('tinymce.plugins.SubordinatePlugin',{init:function(k,l){k.addCommand('mceSubordinate',function(){var c;var d;var e=k.editorId.split("_");var f=e[2].split("-");c=k.editorId.split("-");if(c.length>2){d=e[1]+f[0]+c[1]+"-"+c[2]}else{d=e[1]+f[0]+c[1]}makeSubordinateDialog(k,d);init(k.selection.getContent(),d);loadValues(k,d);var g=$(window).width();var h=$(window).height();var i=g*0.6;var j=h*0.8;$('#msm_subordinate_container-'+d).dialog({open:function(a,b){$(".ui-dialog-titlebar-close").hide();$("#msm_subordinate_highlighted-"+d).val(k.selection.getContent({format:'text'}));initInfoEditor(d)},modal:true,autoOpen:false,height:j,width:i,closeOnEscape:false});$('#msm_subordinate_container-'+d).dialog('open').css('display','block')});k.onNodeChange.add(function(a,b,n){if(a.selection.getContent()){b.setDisabled('subordinate',false)}else{b.setActive('subordinate',false);b.setDisabled('subordinate',true)}});k.addButton('subordinate',{title:'subordinate.desc',cmd:'mceSubordinate',image:l+'/img/subordinate.png'})},createControl:function(n,a){return null},getInfo:function(){return{longname:'Subordinate plugin',author:'Ga Young Kim',authorurl:'http://ualberta.ca ',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/subordinate',version:"1.0"}}});tinymce.PluginManager.add('subordinate',tinymce.plugins.SubordinatePlugin)})();function makeSubordinateDialog(a,b){var c;var d=document.createElement('div');var e=document.createElement('form');var f=document.createElement('div');var g=document.createElement('label');var h=document.createTextNode('Selected Text : ');var i=document.createElement('input');var j=document.createElement('label');var k=document.createTextNode('Subordinate Type : ');var l=document.createElement('select');var m=document.createElement('option');m.value="Information";var n=document.createTextNode('Information');var o=document.createElement('option');o.setAttribute("value","External Link");var p=document.createTextNode('External Link');var q=document.createElement('option');q.setAttribute("value","Internal Reference");var r=document.createTextNode('Internal Reference');var s=document.createElement('option');s.setAttribute("value","External Reference");var t=document.createTextNode('External Reference');m.setAttribute("selected","selected");m.appendChild(n);o.appendChild(p);q.appendChild(r);s.appendChild(t);var u=document.createElement('div');var v=document.createElement('div');var w=document.createElement('input');var x=document.createElement('input');c=document.getElementById('msm_subordinate_container-'+b);d.id='msm_subordinate-'+b;c.setAttribute("title","Create Subordinate");e.id='msm_subordinate_form-'+b;f.className="msm_subordinate_form_container";g.setAttribute("for",'msm_subordinate_highlighted-'+b);g.appendChild(h);i.id='msm_subordinate_highlighted-'+b;i.name='msm_subordinate_highlighted-'+b;i.setAttribute("disabled","disabled");j.setAttribute("for",'msm_subordinate_select-'+b);j.appendChild(k);l.id='msm_subordinate_select-'+b;l.name='msm_subordinate_select-'+b;l.onchange=function(){changeForm(event,b)};u.id='msm_subordinate_content_form_container-'+b;u.className="msm_subordinate_content_form_containers";v.className='msm_subordinate_button_container';w.setAttribute("type","button");w.id='msm_subordinate_submit-'+b;w.className='msm_subordinate_button';w.setAttribute("value","Save");w.onclick=function(){submitSubForm(a,b)};x.setAttribute("type","button");x.id='msm_subordinate_cancel-'+b;x.className='msm_subordinate_button';x.setAttribute("value","Cancel");x.onclick=function(){closeSubFormDialog(b)};var y=makeInfoForm(b);u.appendChild(y);l.appendChild(m);l.appendChild(o);l.appendChild(q);l.appendChild(s);v.appendChild(w);v.appendChild(x);f.appendChild(g);f.appendChild(i);f.appendChild(document.createElement('br'));f.appendChild(document.createElement('br'));f.appendChild(j);f.appendChild(l);f.appendChild(u);e.appendChild(f);e.appendChild(document.createElement('br'));e.appendChild(document.createElement('br'));e.appendChild(v);d.appendChild(e);if(!c.hasChildNodes()){c.appendChild(d)}else{$('#msm_subordinate_container-'+b).empty();c.appendChild(d)}}