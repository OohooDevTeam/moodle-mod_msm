/**
 **************************************************************************
 **                              MSM                                     **
 **************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 **************************************************************************
 **************************************************************************/

MathJax.Hub.Config({
    showProcessingMessages: false,
    tex2jax: {
        inlineMath: [ ['$','$'], ["\\(","\\)"] ],
        displayMath: [ ['$$','$$'], ['\\[','\\]'] ],
        processEscapes: true,
        balanceBraces: true
    },
    TeX: {
  
        Macros: {
            st: '{\\, :\\!\\! |\\!\\! :\\, }',
	
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
		
            uInt: ['{\\mathbb{I}^{#1}}',1],		
            uBll: ['{\\mathbb{B}^{#1}}',1],		
            uDsk: ['{\\mathbb{D}^{#1}}',1],		
            uSphr: ['{\\mathbb{S}^{#1}}',1],	
            TopIntr: ['{\\text{Int}_{#1}\\left(#2\\right)}',2],		
            TopClsr: ['{\\text{Clsr}_{#1}\\left(#2\\right)}',2],	
            TopBndry: ['{\\partial{#1}}',1],	
		
            Arrw: ['{\\overset{\\longrightarrow}{#1}}',1],
		
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
		
            FakeString: '{\\text{fake}}'
        }
    },
    
    jax: ["input/TeX", "output/HTML-CSS"]
//    "HTML-CSS": {
//        extensions: ["handle-floats.js"]
//    }
});

MathJax.Ajax.loadComplete("http://localhost/moodle/mod/msm/js/Mathjaxconfig.js");

 
       