/*************************************************************
 *
 *  MathJax/config/local/local.js
 *  
 *  Include changes and configuration local to your installation
 *  in this file.  For example, common macros can be defined here
 *  (see below).  To use this file, add "local/local.js" to the
 *  config array in MathJax.js or your MathJax.Hub.Config() call.
 *
 *  ---------------------------------------------------------------------
 *  
 *  Copyright (c) 2009 Design Science, Inc.
 * 
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */
MathJax.Hub.Config({
    jax: ["input/TeX", "output/HTML-CSS"], 
    
    MMLorHTML: {
        prefer: {
            MSIE: "MML",
            Firefox: "HTML",
            Safari: "HTML",
            Chrome: "HTML",
            Opera: "HTML",
            other: "HTML"
        }
    },
    showProcessingMessages: true,
    tex2jax: {
        inlineMath: [ ['$','$'], ["\\(","\\)"] ],
        displayMath: [ ['$$','$$'], ["\\[","\\]"] ],
        processEnvironments: true,
        processEscapes: true,
        balanceBraces: true
    },
    TeX: {
                extensions: ["color.js", "AMSmath.js", "AMSsymbols.js"],

//        extensions: ["autoload-all.js"],
  
        Macros: {
            st: '{\\, :\\!\\! |\\!\\! :\\, }',
            im: '{im}',
	
            AnglBr: ['{ \\left\\langle #1 \\right\\rangle }',1],		
            SqBr: ['{ \\left[ #1 \\right] }',1],	
		
            Set: ['{ \\left\\{ #1 \\right\\} }',1],	
            union: '{\\cup}',					
            Union: ['{ #1\\cup #2 }',2],		
            FamUnion: ['{ \\bigcup_{#1} #2 }',2],	
            intrsctn: '{\\cap}',				
            Intrsctn: ['{ #1\\cap #2 }',2],		
            FamIntrsctn: ['{ \\bigcap_{#1} #2 }',2],	
            stdffrnc: '{-}',					
            StDffrnc: ['{ #1 - #2 }',2],		
            prdct: '{\\! \\times \\!}',			
            Prdct: ['{ {#1}\\! \\times \\! {#2} }',2],		
            FamPrdct: ['{ \\prod_{#1} #2 }',2],	
            PwrSt: ['{ {\\cal{P}}\\left( #1 \\right) }',1],	
            crdnlty: '{ \\text{ Card} }',		
            Crdnlty: ['{ \\text{ Card }\\left(#1\\right) }',1],	
		
            mono: '{\\rightarrowtail}',			
            epi: '{\\twoheadrightarrow}',		
            from: '{\\colon}',					
            Comp: '{\\circ}',					
            Img: ['{ \\text{Im}\\left( #1 \\right) }',1],	
            Domain: ['{ \\text{Dom}\\left( #1 \\right) }',1],	
            Target: ['{ \\text{Tar}\\left( #1 \\right) }',1],	
            IdMap: ['{ \\text{Id}_{ #1 } }',1],		
            Prjctn: ['{ \\text{pr}_{ #1 } }',1],	
            Inclsn: ['{ \\text{inc}{ #1 } }',1],
            inc: '{\\text{inc}}',
            Dgnl: ['{ \\bigtriangleup_{ #1 } }',1],
            Fold: ['{ \\bigtriangledown_{ #1 } }',1],	
           
            RNr: ['{\\mathbb R^{#1}}',1],	
            CNr: ['{\\mathbb C^{#1}}',1],	
            NNr: ['{\\mathbb N^{#1}}',1],	
            QNr: ['{\\mathbb Q^{#1}}',1],	
            ZNr: ['{\\mathbb Z^{#1}}',1],	
            QuatNr: ['{\\mathbb H^{#1}}',1],	
            Zmod: ['{\\mathbb{Z}/{#1}}',1],	
            ZPoly: ['{\\mathbb{Z}\\left[ {#1} \\right]}',1],	
            QPoly: ['{\\mathbb{Q}\\left[ {#1} \\right]}',1],	
            RPoly: ['{\\mathbb{R}\\left[ {#1} \\right]}',1],	
            CPoly: ['{\\mathbb{C}\\left[ {#1} \\right]}',1],	
            PolyDegree: ['{\\text{deg}\\left( {#1} \\right)}',1],	
            SignNr: ['{\\text{sign}\\left( {#1} \\right)}',1],	
            Abs: ['{\\left| {#1} \\right|}',1],					
            Norm: ['{\\left\\| {#1} \\right\\|}',1],
            norm:['{\\left\\| {#1} \\right\\|}',1],
            RePrt: ['{\\text{Re}\\left( {#1} \\right)}',1],		
            ImPrt: ['{\\text{Im}\\left( {#1} \\right)}',1],		
		
            CCIntrvl: ['{\\left[#1,#2\\right]}',2],	
            COIntrvl: ['{\\left[#1,#2\\right[}',2],	
            OCIntrvl: ['{\\left]#1,#2\\right]}',2],	
            OOIntrvl: ['{\\left]#1,#2\\right[}',2],
            Min: ['{\\text{min}\\left\\{#1\\right\\}}',1],	
            Max: ['{\\text{max}\\left\\{#1\\right\\}}',1],
            Inf: ['{\\text{inf}\\left\\{#1\\right\\}}',1],	
            Sup: ['{\\text{sup}\\left\\{#1\\right\\}}',1],
            Dstnc: ['{\\text{dist}_{#1}\\left(#1,#2\\right)}',2],	
		
            Ker: ['{\\text{ker}\\left\\{#1\\right\\}}',1],		
            CoKer: ['{\\text{coker}\\left\\{#1\\right\\}}',1],	
		
            Dim: ['{\\text{dim}\\left(#1\\right)}',1],	
            RDim: ['{\\text{dim}_{\\mathbb{R}}\\left(#1\\right)}',1],	
            CDim: ['{\\text{dim}_{\\mathbb{C}}\\left(#1\\right)}',1],	
            span: ['{\\text{span}\\left\\{#1\\right\\}}',1],	
            Span: ['{\\text{span}_{#1}\\left\\{#2\\right\\}}',2],	
            RSpan: ['{\\text{span}_{\\mathbb{R}}\\left\\{#1\\right\\}}',1],	
            CSpan: ['{\\text{span}_{\\mathbb{C}}\\left\\{#1\\right\\}}',1],	
            Vect: ['{\\mathbf{#1}}',1],								
            Mtrx: ['{#1}',1],										
            dtrmnnt: '{\\text{det}}',								
            Dtrmnnt: ['{\\text{det}\\left\\(#1\\right\\)}',1],		
            DotPr: ['{{#1} \\bullet {#2}}',2], 			
            CrssPr: ['{{#1}\\! \\times \\!{#2}}',2], 
            StPrdct: ['{{#1}\\hskip -.2em\\times\\hskip -.2em {#2}}', 2],
            stprdct: '{\\hskip -.1em\\times\\hskip -.1em}',
            
            imes:'{\\hskip -.2mm\\times\\hskip -.2mm}',
            
		
            uInt: ['{\\mathbb{I}^{#1}}',1],		
            uBll: ['{\\mathbb{B}^{#1}}',1],		
            uDsk: ['{\\mathbb{D}^{#1}}',1],		
            uSphr: ['{\\mathbb{S}^{#1}}',1],	
            TopIntr: ['{\\text{Int}_{#1}\\left(#2\\right)}',2],		
            TopClsr: ['{\\text{Clsr}_{#1}\\left(#2\\right)}',2],	
            TopBndry: ['{\\partial{#1}}',1],	
		
            Arrw: ['{\\overset{\\longrightarrow}{#1}}',1],
            Arrow: ['{\\overset{\\longrightarrow}{#1}}',1],
            
            Abs: ['{\\left\\vert#1\\right\\vert}', 1],
            abs: ['{\\left\\vert#1\\right\\vert}', 1],
            Length:['{L(#1)}',1],
		
            IdMtrx: ['{I_{#1}}',1],					
            ZeroMtrx: ['{\\mathbf{0}_{#1}}',1],		
            ColSpc: ['{\\text{Col}(#1)}',1],		
            RowSpc: ['{\\text{Row}(#1)}',1],		
            NullSpc: ['{\\text{Null}(#1)}',1],		
            Rank: ['{\\text{Rank}(#1)}',1],			
			
            ZMtrx: ['{\\mathbf{0}_{#1}}',1],		
            ColSp: ['{\\text{Col}(#1)}',1],			
            RowSp: ['{\\text{Row}(#1)}',1],			
            NllSp: ['{\\text{Null}(#1)}',1],	
            
            pr: ['{\\text{proj}}'],
            StdBss: ['{\\mathbf{e}_{#1}}', 1],
            IdTrafo: ['{\\text{Id}_{#1}}', 1],            
            SltdBox: ['{\\text{Box}{#1}}', 1],
            
            Rnk: ['{\\text{Rank}{#1}', 1],
            
            SymGrp: ['{S_{#1}}', 1],
            
            Id: ['{\\text{Id}_{#1}}', 1],
            
            // temporary fix for the non-existing operators...
            sign:'{\\text{sign}}',
            OriVol: '{\\text{OVol}}',
            Vol: '\\text{Vol}',
		
            FakeString: '{\\text{fake}}'
        }      
    }   
});

MathJax.Hub.Register.StartupHook("TeX Jax Ready",function () {
    var TEX = MathJax.InputJax.TeX;
  
  

// place macros here.  E.g.:
//   TEX.Macro("R","{\\bf R}");
//   TEX.Macro("op","\\mathop{\\rm #1}",1); // a macro with 1 parameter
  
});
MathJax.Ajax.loadComplete("http://localhost/moodle/mod/msm/js/mathjax/config/local/local.js");
