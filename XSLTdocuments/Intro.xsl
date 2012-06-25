<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:bk="http://webmath.math.ualberta.ca/v1/Book"
    xmlns:xi="http://www.w3.org/2001/XInclude"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:unit="Unit"
    version="2.0">
    
    <xsl:output method="xml" indent="yes" version="1.0"
                encoding="UTF-8"
                doctype-system="../Symbols.dtd"/>
    
    <xsl:template match="bk:intro" name="intro">
        <xsl:element name="intro" namespace="Unit">
            <xsl:if test="./@id != ''">
                <xsl:attribute name="unitid">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="./@xsi:schemaLocation">
                <xsl:attribute name="xsi:schemaLocation">Unit file:/C:/xampp/htdocs/moodle/mod/msm/NewSchemas/Unit.xsd</xsl:attribute>
            </xsl:if>
            <xsl:if test="./@xi != ''">
                <xsl:attribute name="xi">
                    <xsl:value-of select="./@xi"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="./@xsi != ''">
                <xsl:attribute name="xsi">
                    <xsl:value-of select="./@xsi"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:element name="block" namespace="Unit">
                <xsl:element name="block.body" namespace="Unit">
                    <xsl:for-each select=".">
                        <xsl:apply-templates select="node()[not(name()='')]"/>
                    </xsl:for-each>                   
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="xi:include">
        <xsl:if test="./@href != ''">
            <xsl:element name="xi:include" namespace="http://www.w3.org/2001/XInclude">
                <xsl:attribute name="href">
                    <xsl:value-of select="./@href"/>
                </xsl:attribute>
            </xsl:element>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="bk:figure">
        <xsl:element name="media" namespace="Theorem">
            <xsl:if test="./@id">
                <xsl:attribute name="id">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>  
            
            <xsl:attribute name="type">image</xsl:attribute>
            <xsl:choose>
                <xsl:when test="child::node()[name()='info']">
                    <xsl:attribute name="active">1</xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="active">0</xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
            
            <xsl:attribute name="inline">0</xsl:attribute>
            
            <xsl:apply-templates select="bk:img"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:img">
        <xsl:choose>     
            <xsl:when test="parent::node()[name()='figure']">
                <xsl:element name="img">
                    <xsl:attribute name="src">
                        <xsl:value-of select="./@src"/>
                    </xsl:attribute>
                    <xsl:if test="./@height">
                        <xsl:attribute name="height">
                            <xsl:value-of select="./@height"/>
                        </xsl:attribute>
                    </xsl:if>
                    <xsl:if test="./@width">
                        <xsl:attribute name="width">
                            <xsl:value-of select="./@width"/>
                        </xsl:attribute>
                    </xsl:if>
                    
                    <xsl:if test="child::node()[name()='info' or name()='caption']">
                        
                        <xsl:element name="extended.caption" namespace="Theorem">
                            
                            <xsl:if test="child::node()[name()='info']">
                                
                                <xsl:element name="image.mapping" namespace="Theorem">
                                    
                                    <xsl:element name="area" namespace="Theorem">
                                        <xsl:attribute name="shape">
                                            <xsl:text>rect</xsl:text>
                                        </xsl:attribute>
                                        
                                        <xsl:attribute name="coord">
                                            
                                            <xsl:choose>
                                                <xsl:when test="child::node()[name()='img'][attribute::width]">
                                                    <xsl:if test="child::node()[name()='img'][attribute::height]">                                       
                                                        <xsl:text>0&#44;0&#44;</xsl:text>
                                                        <xsl:value-of select="bk:img/@width"/>
                                                        <xsl:text>&#44;</xsl:text>
                                                        <xsl:value-of select="bk:img/@height"/>
                                                    </xsl:if>                                        
                                                </xsl:when>
                                                
                                                <xsl:otherwise>
                                                    <xsl:text>0&#44;0&#44;200&#44;100</xsl:text>
                                                </xsl:otherwise> 
                                                
                                            </xsl:choose>                                    
                                        </xsl:attribute>
                                    </xsl:element>
                                </xsl:element>
                                
                                <xsl:apply-templates select="bk:info"/>
                            </xsl:if>
                            <xsl:if test="child::node()[name()='caption']">
                                <xsl:apply-templates select="child::node()[name()='caption']"/>
                            </xsl:if>
                        </xsl:element>
                    </xsl:if>
                </xsl:element>         
            </xsl:when>
            <xsl:when test="parent::node()[name()='hot']">
                <xsl:element name="media">
                    <xsl:attribute name="type">image</xsl:attribute>
                    <xsl:attribute name="active">1</xsl:attribute>
                    <xsl:attribute name="inline">0</xsl:attribute>
                    <xsl:element name="img">
                        <xsl:attribute name="src">
                            <xsl:value-of select="./@src"/>
                        </xsl:attribute>
                        <xsl:if test="./@height">
                            <xsl:attribute name="height">
                                <xsl:value-of select="./@height"/>
                            </xsl:attribute>
                        </xsl:if>
                        <xsl:if test="./@width">
                            <xsl:attribute name="width">
                                <xsl:value-of select="./@width"/>
                            </xsl:attribute>
                        </xsl:if>
                    </xsl:element>        
                </xsl:element>            
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="media">
                    <xsl:if test="parent::node()[name()='figure'][attribute::id]">
                        <xsl:attribute name="id">
                            <xsl:value-of select="parent::node()/@id"/>
                        </xsl:attribute>
                    </xsl:if>  
                    
                    <xsl:attribute name="type">image</xsl:attribute>
                    <xsl:attribute name="active">0</xsl:attribute>
                    <xsl:attribute name="inline">0</xsl:attribute>
                    <xsl:element name="img">
                        <xsl:attribute name="src">
                            <xsl:value-of select="./@src"/>
                        </xsl:attribute>
                        <xsl:if test="./@height">
                            <xsl:attribute name="height">
                                <xsl:value-of select="./@height"/>
                            </xsl:attribute>
                        </xsl:if>
                        <xsl:if test="./@width">
                            <xsl:attribute name="width">
                                <xsl:value-of select="./@width"/>
                            </xsl:attribute>
                        </xsl:if>
                    </xsl:element>           
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="bk:image">
        <xsl:element name="media">
            <xsl:if test="parent::node()[name()='figure'][attribute::id]">
                <xsl:attribute name="id">
                    <xsl:value-of select="parent::node()/@id"/>
                </xsl:attribute>
            </xsl:if>  
            
            <xsl:attribute name="type">image</xsl:attribute>
            <xsl:choose>
                <xsl:when test="child::node()[name()='path']">
                    <xsl:attribute name="active">0</xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="active">1</xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:attribute name="inline">0</xsl:attribute>
            <xsl:element name="img">
                <xsl:attribute name="src">
                    <xsl:choose>
                        <xsl:when test="child::node()[name()='path']">
                            <xsl:value-of select="child::node()[name()='path']"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="./@src"/>
                        </xsl:otherwise>
                    </xsl:choose>                    
                </xsl:attribute>
                <xsl:if test="./@height">
                    <xsl:attribute name="height">
                        <xsl:value-of select="./@height"/>
                    </xsl:attribute>
                </xsl:if>
                <xsl:if test="./@width">
                    <xsl:attribute name="width">
                        <xsl:value-of select="./@width"/>
                    </xsl:attribute>
                </xsl:if>
                
                <xsl:if test="child::node()[not(name()='path')]">
                    <xsl:element name="image.mapping">
                        <xsl:apply-templates select="bk:area"/>
                    </xsl:element>
                </xsl:if>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    <xsl:template match="bk:area">
        <xsl:element name="area" namespace="Unit">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
        
        <xsl:for-each select=".">
            <xsl:apply-templates/>
        </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:math.display">
        <xsl:element name="math.display" namespace="Unit">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:computation">
        <xsl:element name="math.array" namespace="Unit">
            <xsl:attribute name="column">3</xsl:attribute>
            <xsl:for-each select="child::node()[name()='left']">
                <xsl:element name="row" namespace="Unit">
                    <xsl:attribute name="rowspan">
                        <xsl:value-of select="1"/>
                    </xsl:attribute>
                    <xsl:apply-templates select="."/>
                    <xsl:apply-templates select="following-sibling::node()[name()='center'][position()=1]"/>
                    <xsl:apply-templates select="following-sibling::node()[name()='right'][position()=1]"/>
                </xsl:element>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:left">
        <xsl:element name="cell" namespace="Unit">
            <xsl:attribute name="colspan">
                <xsl:value-of select="2"/>
            </xsl:attribute>
            <xsl:attribute name="halign">
                <xsl:text>center</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="valign">
                <xsl:text>middle</xsl:text>
            </xsl:attribute>
            
            <xsl:element name="math" namespace="Unit">
                <xsl:element name="latex" namespace="Unit">
                    <xsl:apply-templates select="child::node()[name()='']"/>
                </xsl:element>
            </xsl:element>
        </xsl:element>
            
        <xsl:if test="child::node()[name()='info']">
            <xsl:element name="companion" namespace="Unit">
                <xsl:apply-templates select="bk:info"/>
            </xsl:element>
        </xsl:if>
        <xsl:if test="child::node()[name()='companion']">
            <xsl:apply-templates select="child::node()"/>
        </xsl:if>
        <xsl:if test="child::node()[name()='crossref']">
            <xsl:element name="companion" namespace="Unit">
                <xsl:for-each select=".">
                        
                    <xsl:if test="child::node()[name() = 'comment.ref']">
                        <xsl:element name="comment.ref" namespace="Unit">
                            <xsl:attribute name="commentID">
                                <xsl:value-of select="bk:comment.ref/@commentID"/>
                            </xsl:attribute>
                        </xsl:element>
                    </xsl:if>
                        
                    <xsl:if test="child::node()[name() = 'definition.ref']">
                        <xsl:element name="definition.ref" namespace="Unit">
                            <xsl:attribute name="definitionID">
                                <xsl:value-of select="bk:definition.ref/@definitionID"/>
                            </xsl:attribute>
                        </xsl:element>
                    </xsl:if>
                        
                    <xsl:if test="child::node()[name() = 'theorem.ref']">
                        <xsl:element name="theorem.ref" namespace="Unit">
                            <xsl:attribute name="theoremID">
                                <xsl:value-of select="bk:theorem.ref/@theoremID"/>
                            </xsl:attribute>
                        </xsl:element>
                    </xsl:if>
                        
                    <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                        <xsl:element name="unit.ref" namespace="Unit">
                            <xsl:attribute name="unitId">
                                <xsl:choose>
                                    <xsl:when test="child::node()[name() = 'subpage.ref']">
                                        <xsl:value-of select="bk:subpage.ref/@subpageID"/>
                                    </xsl:when>
                                    <xsl:when test="child::node()[name() = 'chapter.ref']">
                                        <xsl:value-of select="bk:chapter.ref/@chapterID"/>
                                    </xsl:when>
                                    <xsl:when test="child::node()[name() = 'subsection.ref']">
                                        <xsl:value-of select="bk:subsection.ref/@subsectionID"/>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:value-of select="bk:section.ref/@sectionID"/>
                                    </xsl:otherwise>
                                        
                                </xsl:choose>
                                    
                            </xsl:attribute>
                        </xsl:element>
                    </xsl:if>
                </xsl:for-each>
            </xsl:element>
        </xsl:if>
           
    </xsl:template>
    
    <xsl:template match="bk:center">
        <xsl:element name="cell" namespace="Unit">
            <xsl:attribute name="colspan">
                <xsl:value-of select="1"/>
            </xsl:attribute>
            <xsl:attribute name="halign">
                <xsl:text>center</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="valign">
                <xsl:text>middle</xsl:text>
            </xsl:attribute>
            
            <xsl:element name="text" namespace="Unit">
                <xsl:apply-templates select="child::node()[name()='']"/>
            </xsl:element>
            
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion">
                    <xsl:apply-templates select="bk:info"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="child::node()[name()='companion']">
                <xsl:apply-templates select="child::node()"/>
            </xsl:if>
            <xsl:if test="child::node()[name()='crossref']">
                <xsl:element name="companion" namespace="Unit">
                    <xsl:for-each select=".">
                        
                        <xsl:if test="child::node()[name() = 'comment.ref']">
                            <xsl:element name="comment.ref" namespace="Unit">
                                <xsl:attribute name="commentID">
                                    <xsl:value-of select="bk:comment.ref/@commentID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'definition.ref']">
                            <xsl:element name="definition.ref" namespace="Unit">
                                <xsl:attribute name="definitionID">
                                    <xsl:value-of select="bk:definition.ref/@definitionID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'theorem.ref']">
                            <xsl:element name="theorem.ref" namespace="Unit">
                                <xsl:attribute name="theoremID">
                                    <xsl:value-of select="bk:theorem.ref/@theoremID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                            <xsl:element name="unit.ref" namespace="Unit">
                                <xsl:attribute name="unitId">
                                    <xsl:choose>
                                        <xsl:when test="child::node()[name() = 'subpage.ref']">
                                            <xsl:value-of select="bk:subpage.ref/@subpageID"/>
                                        </xsl:when>
                                        <xsl:when test="child::node()[name() = 'chapter.ref']">
                                            <xsl:value-of select="bk:chapter.ref/@chapterID"/>
                                        </xsl:when>
                                        <xsl:when test="child::node()[name() = 'subsection.ref']">
                                            <xsl:value-of select="bk:subsection.ref/@subsectionID"/>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:value-of select="bk:section.ref/@sectionID"/>
                                        </xsl:otherwise>
                                        
                                    </xsl:choose>
                                    
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                    </xsl:for-each>
                </xsl:element>
            </xsl:if>
          
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:right">
        <xsl:element name="cell" namespace="Unit">
            <xsl:attribute name="colspan">
                <xsl:value-of select="2"/>
            </xsl:attribute>
            <xsl:attribute name="halign">
                <xsl:text>center</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="valign">
                <xsl:text>middle</xsl:text>
            </xsl:attribute>
            
            <xsl:element name="math" namespace="Unit">
                <xsl:element name="latex" namespace="Unit">
                    <xsl:apply-templates select="child::node()[name()='']"/>
                </xsl:element>
            </xsl:element>
            
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Unit">
                    <xsl:apply-templates select="bk:info"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="child::node()[name()='companion']">
                <xsl:apply-templates select="child::node()"/>
            </xsl:if>
            <xsl:if test="child::node()[name()='crossref']">
                <xsl:element name="companion" namespace="Unit">
                    <xsl:for-each select=".">
                        
                        <xsl:if test="child::node()[name() = 'comment.ref']">
                            <xsl:element name="comment.ref" namespace="Unit">
                                <xsl:attribute name="commentID">
                                    <xsl:value-of select="bk:comment.ref/@commentID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'definition.ref']">
                            <xsl:element name="definition.ref" namespace="Unit">
                                <xsl:attribute name="definitionID">
                                    <xsl:value-of select="bk:definition.ref/@definitionID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'theorem.ref']">
                            <xsl:element name="theorem.ref" namespace="Unit">
                                <xsl:attribute name="theoremID">
                                    <xsl:value-of select="bk:theorem.ref/@theoremID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                            <xsl:element name="unit.ref" namespace="Unit">
                                <xsl:attribute name="unitId">
                                    <xsl:choose>
                                        <xsl:when test="child::node()[name() = 'subpage.ref']">
                                            <xsl:value-of select="bk:subpage.ref/@subpageID"/>
                                        </xsl:when>
                                        <xsl:when test="child::node()[name() = 'chapter.ref']">
                                            <xsl:value-of select="bk:chapter.ref/@chapterID"/>
                                        </xsl:when>
                                        <xsl:when test="child::node()[name() = 'subsection.ref']">
                                            <xsl:value-of select="bk:subsection.ref/@subsectionID"/>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:value-of select="bk:section.ref/@sectionID"/>
                                        </xsl:otherwise>
                                        
                                    </xsl:choose>
                                    
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                    </xsl:for-each>
                </xsl:element>
            </xsl:if>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:ul">
        <xsl:element name="ul" namespace="Unit">
            <xsl:if test="./@bullet">
                <xsl:copy-of select="./@bullet"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:ol">
        <xsl:element name="ol" namespace="Unit">
            <xsl:if test="./@type">
                <xsl:copy-of select="./@type"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:li">
        <xsl:element name="li" namespace="Unit">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:table">
        <xsl:element name="table" namespace="Unit">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:row">
        <xsl:element name="row" namespace="Unit">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:cell">
        <xsl:element name="cell" namespace="Unit">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
  
    <xsl:template match="bk:para">
        <xsl:element name="para" namespace="Unit">
            <xsl:if test="./@align != ''">
                <xsl:attribute name="align">
                    <xsl:value-of select="./@align"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="./@id != ''">
                <xsl:attribute name="id">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:element name="para.body" namespace="Unit">
                    <xsl:for-each select=".">
                        <xsl:if test="node()[name()='index.glossary']">
                            <xsl:value-of select="bk:term"/>
                        </xsl:if>
                        <xsl:if test="node()[name()='index.symbol']">
                            <xsl:value-of select="bk:symbol"/>
                        </xsl:if>
                        <xsl:apply-templates select="node()[not(name() = 'index.glossary' or name() = 'index.symbol' or name() = 'index.author')]"/>
                    </xsl:for-each>
                </xsl:element>
                <xsl:apply-templates select="bk:index.author"/>
                <xsl:apply-templates select="bk:index.symbol"/>
                <xsl:apply-templates select="bk:index.glossary"/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:index.author">
        <xsl:element name="index.author" namespace="Unit">
            <xsl:for-each select=".">
                    <xsl:apply-templates select="bk:name"/>                
            </xsl:for-each>
            <xsl:apply-templates select="bk:info"/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:index.symbol">
        <xsl:element name="index.symbol" namespace="Unit">
            <xsl:for-each select=".">
                <xsl:apply-templates select="bk:symbol"/>
            </xsl:for-each>
            <xsl:apply-templates select="bk:info"/>
            <xsl:if test="bk:sortstring != ''">
                <xsl:element name="sortstring" namespace="Unit">
                    <xsl:apply-templates select="bk:sortstring"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:index.glossary">
        <xsl:element name="index.glossary" namespace="Unit">
            <xsl:for-each select=".">
                <xsl:apply-templates select="bk:term"/>
            </xsl:for-each>
            <xsl:if test="bk:info != ''">
                <xsl:apply-templates select="bk:info"/>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:symbol">
        <xsl:element name="symbol" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:term">
        <xsl:element name="term" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    
    <xsl:template match="bk:name">
        <xsl:element name="name" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:first">
        <xsl:element name="first" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="bk:middle">
        <xsl:element name="middle" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="bk:last">
        <xsl:element name="last" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="bk:initials">
        <xsl:element name="initials" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:latex">
        <xsl:choose>
            <xsl:when test="parent::node()[name()='math.display']">
                <xsl:element name="latex" namespace="Unit">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="math" namespace="Unit">
                    <xsl:element name="latex" namespace="Unit">
                        <xsl:apply-templates/>
                    </xsl:element>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>       
    </xsl:template>
    
    <xsl:template match="bk:b">
        <xsl:element name="strong" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="bk:i">
        <xsl:element name="emphasis" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:subordinate">
        <xsl:choose>
            <xsl:when test="child::node()[child::node()[name()='img' or name()='image']]">
                <xsl:apply-templates select="child::node()[child::node()[name()='img' or name()='image']]"/>
                <xsl:if test="bk:info">
                    <xsl:apply-templates select="node()[name()='info']"/>
                </xsl:if>
                <xsl:if test="bk:companion">
                    <xsl:apply-templates select="node()[name()='companion']"/>
                </xsl:if>
                <xsl:if test="bk:crossref">
                    <xsl:apply-templates select="node()[name()='crossref']"/>
                </xsl:if>
                <xsl:if test="bk:cite">
                    <xsl:apply-templates select="node()[name()='cite']"/>
                </xsl:if>
                <xsl:if test="bk:link">
                    <xsl:apply-templates select="node()[name()='link']"/>
                </xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="subordinate" namespace="Unit">
                    <xsl:apply-templates select="node()[name()='hot']"/>
                    <xsl:if test="bk:info">
                        <xsl:apply-templates select="node()[name()='info']"/>
                    </xsl:if>
                    <xsl:if test="bk:companion">
                        <xsl:apply-templates select="node()[name()='companion']"/>
                    </xsl:if>
                    <xsl:if test="bk:crossref">
                        <xsl:apply-templates select="node()[name()='crossref']"/>
                    </xsl:if>
                    <xsl:if test="bk:cite">
                        <xsl:apply-templates select="node()[name()='cite']"/>
                    </xsl:if>
                    <xsl:if test="bk:link">
                        <xsl:apply-templates select="node()[name()='link']"/>
                    </xsl:if>
                    <!-- presentation may be the same processing as link, left unprocessed for now-->
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>      
    </xsl:template>
    
    <xsl:template match="bk:hot">
        <xsl:choose>
            <xsl:when test="child::node()[name()='img' or name()='image']">
                <xsl:apply-templates select="child::node()[name()='img' or name()='image']"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="hot" namespace="Unit">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>        
    </xsl:template>
    
    <xsl:template match="bk:info">
        <xsl:element name="info" namespace="Unit">
            <xsl:if test="bk:caption != ''">
                <xsl:element name="info.caption" namespace="Unit">
                    <xsl:apply-templates select="bk:caption"/>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="node()[not(name()='caption')]"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:crossref">
        <xsl:if test="child::node()[name() = 'external.ref']">
            <xsl:apply-templates select ="bk:external.ref"/>
        </xsl:if>
        <xsl:element name="crossref" namespace="Unit">
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'exercise.pack.ref']">
                    <xsl:element name="exercise.pack.ref" namespace="Unit">
                        <xsl:attribute name="exercisePackID">
                            <xsl:value-of select="bk:exercise.pack.ref/@exercisePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'example.pack.ref']">
                    <xsl:element name="example.pack.ref" namespace="Unit">
                        <xsl:attribute name="examplePackID">
                            <xsl:value-of select="bk:example.pack.ref/@examplePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Unit">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="bk:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Unit">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="bk:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Unit">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="bk:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                    <xsl:element name="unit.ref" namespace="Unit">
                        <xsl:attribute name="unitId">
                            <xsl:choose>
                                <xsl:when test="child::node()[name() = 'subpage.ref']">
                                    <xsl:value-of select="bk:subpage.ref/@subpageID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'chapter.ref']">
                                    <xsl:value-of select="bk:chapter.ref/@chapterID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'subsection.ref']">
                                    <xsl:value-of select="bk:subsection.ref/@subsectionID"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="bk:section.ref/@sectionID"/>
                                </xsl:otherwise>
                                
                            </xsl:choose>
                            
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
   
            <xsl:if test="child::node() != ''">
                <xsl:element name="info" namespace="Unit">
                    <xsl:if test="child::node()[name() = 'caption']">
                        <xsl:element name="info.caption" namespace="Unit">
                            <xsl:apply-templates select="bk:caption"/>
                        </xsl:element>
                    </xsl:if>
                    <xsl:apply-templates select="child::node()[not(name()='caption' or name()='subpage.ref' or name()='chapter.ref' or name()='subsection.ref' or name()='section.ref' or name()='theorem.ref' or name()='comment.ref' or name()='definition.ref' or name()='exercise.pack.ref' or name()='example.pack.ref' or name()='external.ref')]"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:external.ref">
        <xsl:element name="external.ref" namespace="Unit">
            <xsl:if test="./@bookID">
                <xsl:element name="compositionID" namespace="Unit">
                    <xsl:value-of select="./@bookID"/>
                </xsl:element>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'exercise.pack.ref']">
                    <xsl:element name="exercise.pack.ref" namespace="Unit">
                        <xsl:attribute name="exercisePackID">
                            <xsl:value-of select="bk:exercise.pack.ref/@exercisePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'example.pack.ref']">
                    <xsl:element name="example.pack.ref" namespace="Unit">
                        <xsl:attribute name="examplePackID">
                            <xsl:value-of select="bk:example.pack.ref/@examplePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Unit">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="bk:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Unit">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="bk:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Unit">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="bk:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                    <xsl:element name="unit.ref" namespace="Unit">
                        <xsl:attribute name="unitId">
                            <xsl:choose>
                                <xsl:when test="child::node()[name() = 'subpage.ref']">
                                    <xsl:value-of select="bk:subpage.ref/@subpageID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'chapter.ref']">
                                    <xsl:value-of select="bk:chapter.ref/@chapterID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'subsection.ref']">
                                    <xsl:value-of select="bk:subsection.ref/@subsectionID"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="bk:section.ref/@sectionID"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:cite">
        <xsl:if test="./@label != ''">
            <xsl:attribute name="label">
                <xsl:value-of select="./@label"/>
            </xsl:attribute>
        </xsl:if>
      
        <xsl:element name="cite" namespace="Unit">
            <xsl:if test="child::node()[name() = 'caption']">
                <xsl:element name="caption" namespace="Unit">
                    <xsl:apply-templates select="bk:caption"/>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="bk:item"/>
           
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:link">
        <xsl:element name="external.link" namespace="Unit">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:if test="child::node() != ''">
                <xsl:element name="info">
                    <xsl:if test="child::node()[name() = 'caption']">
                        <xsl:element name="info.caption" namespace="Unit">
                            <xsl:apply-templates select="bk:caption"/>
                        </xsl:element>
                    </xsl:if>
                    <xsl:apply-templates select="child::node()[not(name()='caption')]"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:item">
        <xsl:element name="item" namespace="Unit">
            <xsl:if test="child::node()[not(name() = 'citekey')]">
                <xsl:apply-templates select="child::node()[not(name() = 'citekey')]"/>
            </xsl:if>
            <xsl:if test="bk:citekey != ''">
                <xsl:apply-templates select="bk:citekey"/>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:citekey">
        <xsl:element name="citekey" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:companion">
        <xsl:element name="companion" namespace="Unit">
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'showme.pack.ref']">
                    <xsl:element name="showme.pack.ref" namespace="Unit">
                        <xsl:attribute name="showmePackID">
                            <xsl:value-of select="bk:showme.pack.ref/@showmePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'quiz.pack.ref']">
                    <xsl:element name="quiz.pack.ref" namespace="Unit">
                        <xsl:attribute name="quizPackID">
                            <xsl:value-of select="bk:quiz.pack.ref/@quizPackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Unit">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="bk:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Unit">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="bk:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Unit">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="bk:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref']">
                    <xsl:element name="unit.ref" namespace="Unit">
                        <xsl:attribute name="unitId">
                            <xsl:value-of select="bk:subpage.ref/@subpageID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[not(name()='subpage.ref' or name()='definition.ref' or name()='comment.ref' or name()='theorem.ref' or name()='quiz.pack.ref' or name()='showme.pack.ref')]">
                    <xsl:element name="info" namespace="Unit">
                        <xsl:if test="child::node()[name() = 'caption']">
                            <xsl:element name="info.caption" namespace="Unit">
                                <xsl:apply-templates select="bk:caption"/>
                            </xsl:element>
                        </xsl:if>
                        <xsl:apply-templates select="node()[not(name()='caption' or name()='subpage.ref' or name()='definition.ref' or name()='comment.ref' or name()='theorem.ref' or name()='quiz.pack.ref' or name()='showme.pack.ref')]"/>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
</xsl:stylesheet>