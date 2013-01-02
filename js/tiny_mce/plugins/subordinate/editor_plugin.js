(function(){tinymce.PluginManager.requireLangPack('subordinate');tinymce.create('tinymce.plugins.SubordinatePlugin',{init:function(d,e){d.onNodeChange.add(function(a,b,n){if(a.selection.getContent()){b.setDisabled('subordinate',false)}else{b.setActive('subordinate',false);b.setDisabled('subordinate',true)}});d.addButton('subordinate',{title:'subordinate.desc',cmd:'mceSubordinate',image:e+'/img/subordinate.png',onclick:function(){var c=d.editorId.split("-");if(c>2){makeSubordinateDialog(c[1],c[2]);$('#msm_subordinate_container-'+c[1]+'-'+c[2]).dialog({open:function(a,b){$(".ui-dialog-titlebar-close").hide();$("#msm_subordinate_highlighted").val(d.selection.getContent({format:'text'}))},modal:true,autoOpen:false,height:500,width:750,closeOnEscape:false});$('#msm_subordinate_container-'+c[1]+'-'+c[2]).dialog('open').css('display','block')}else{makeSubordinateDialog(c[1],'');$('#msm_subordinate_container-'+c[1]).dialog({open:function(a,b){$(".ui-dialog-titlebar-close").hide();$("#msm_subordinate_highlighted").val(d.selection.getContent({format:'text'}))},modal:true,autoOpen:false,height:500,width:750,closeOnEscape:false});$('#msm_subordinate_container-'+c[1]).dialog('open').css('display','block')}}})},createControl:function(n,a){return null},getInfo:function(){return{longname:'Subordinate plugin',author:'Ga Young Kim',authorurl:'http://ualberta.ca ',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/subordinate',version:"1.0"}}});tinymce.PluginManager.add('subordinate',tinymce.plugins.SubordinatePlugin)})();function makeSubordinateDialog(a,b){var c;var d=document.createElement('div');var e=document.createElement('form');var f=document.createElement('div');var g=document.createElement('label');var h=document.createTextNode('Selected Text : ');var i=document.createElement('input');var j=document.createElement('label');var k=document.createTextNode('Subordinate Type : ');var l=document.createElement('select');var m=document.createElement('option');m.setAttribute("value","None");var n=document.createTextNode('None');var o=document.createElement('option');o.setAttribute("value","Information");var p=document.createTextNode('Information');var q=document.createElement('option');q.setAttribute("value","External Link");var r=document.createTextNode('External Link');var s=document.createElement('option');s.setAttribute("value","Internal Reference");var t=document.createTextNode('Internal Reference');var u=document.createElement('option');u.setAttribute("value","External Reference");var v=document.createTextNode('External Reference');m.appendChild(n);o.appendChild(p);q.appendChild(r);s.appendChild(t);u.appendChild(v);var w=document.createElement('div');var x=document.createElement('div');var y=document.createElement('input');var z=document.createElement('input');if(b!=''){c=document.getElementById('msm_subordinate_container-'+a+'-'+b);d.id='msm_subordinate-'+a+'-'+b;d.title="Create Subordinate";e.id='msm_subordinate_form-'+a+'-'+b;f.className="msm_subordinate_form_container";g.setAttribute("for",'msm_subordinate_highlighted-'+a+'-'+b);g.appendChild(h);i.id='msm_subordinate_highlighted-'+a+'-'+b;i.name='msm_subordinate_highlighted-'+a+'-'+b;i.setAttribute("disabled","disabled");j.setAttribute("for",'msm_subordinate_select-'+a+'-'+b);j.appendChild(k);l.id='msm_subordinate_select-'+a+'-'+b;l.name='msm_subordinate_select-'+a+'-'+b;l.onchange=function(){changeForm(event,a,b)};w.id='msm_subordinate_content_form_container-'+a+'-'+b;x.className='msm_subordinate_button_container';y.setAttribute("type","submit");y.id='msm_subordinate_submit-'+a+'-'+b;y.className='msm_subordinate_button';y.setAttribute("value","Save");z.setAttribute("type","button");z.id='msm_subordinate_cancel-'+a+'-'+b;z.className='msm_subordinate_button';z.setAttribute("value","Cancel");z.onclick=function(){closeSubFormDialog(a,b)}}else{c=document.getElementById('msm_subordinate_container-'+a);d.id='msm_subordinate-'+a;d.title="Create Subordinate";e.id='msm_subordinate_form-'+a;f.className="msm_subordinate_form_container";g.setAttribute("for",'msm_subordinate_highlighted-'+a);g.appendChild(h);i.id='msm_subordinate_highlighted-'+a;i.name='msm_subordinate_highlighted-'+a;i.setAttribute("disabled","disabled");j.setAttribute("for",'msm_subordinate_select-'+a);j.appendChild(k);l.id='msm_subordinate_select-'+a;l.name='msm_subordinate_select-'+a;l.onchange=function(){changeForm(event,a,'')};w.id='msm_subordinate_content_form_container-'+a;x.className='msm_subordinate_button_container';y.setAttribute("type","submit");y.id='msm_subordinate_submit-'+a;y.className='msm_subordinate_button';y.setAttribute("value","Save");z.setAttribute("type","button");z.id='msm_subordinate_cancel-'+a;z.className='msm_subordinate_button';z.setAttribute("value","Cancel");z.onclick=function(){closeSubFormDialog(a,'')}}l.appendChild(m);l.appendChild(o);l.appendChild(q);l.appendChild(s);l.appendChild(u);x.appendChild(y);x.appendChild(z);f.appendChild(g);f.appendChild(i);f.appendChild(document.createElement('br'));f.appendChild(document.createElement('br'));f.appendChild(j);f.appendChild(l);f.appendChild(w);e.appendChild(f);e.appendChild(document.createElement('br'));e.appendChild(document.createElement('br'));e.appendChild(x);d.appendChild(e);if(!c.hasChildNodes()){c.appendChild(e)}}