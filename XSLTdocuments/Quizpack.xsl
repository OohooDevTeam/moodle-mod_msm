<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:compositor="Compositor"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:exe="http://webmath.math.ualberta.ca/v1/Exercise"
    xmlns:xi="http://www.w3.org/2001/XInclude"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    version="2.0">
    
    <xsl:output method="xml" indent="yes" version="1.0"
                encoding="UTF-8"
                doctype-system="../Symbols.dtd"/>
    
    <xsl:template match="exe:quiz.pack">
        <xsl:element name="quiz.pack" namespace="Compositor">
            <xsl:if test="./@id != ''">
                <xsl:attribute name="id">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="./@xsi:schemaLocation">
                <xsl:attribute name="xsi:schemaLocation">Compositor file:/C:/xampp/htdocs/moodle/mod/msm/NewSchemas/Compositor.xsd</xsl:attribute>
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
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:texsupport">
        <xsl:element name="texsupport" namespace="Compositor">
            <xsl:attribute name="href">
                <xsl:value-of select="./@href"/>
            </xsl:attribute>
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
    
    <xsl:template match="exe:title">
        <xsl:element name="title" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:caption">
        <xsl:element name="caption" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:doclabel">
        <xsl:element name="doclabel" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:quiz" name="quiz">
        <xsl:element name="quiz" namespace="Compositor">
            <xsl:if test="./@id != ''">
                <xsl:attribute name="id">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:tablabel">
        <xsl:element name="textcaption" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:hint">
        <xsl:element name="hint" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:choice">
        <xsl:element name="choice" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
   
    <xsl:template match="exe:answer">
        <xsl:choose>
            <xsl:when test="parent::node()[name()='approach' or name()='approach.ext']">
                <xsl:element name="answer.exercise" namespace="Compositor">
                    <xsl:element name="answer.exercise.block" namespace="Compositor">
                        <xsl:element name="answer.exercise.block.body" namespace="Compositor">
                            <xsl:apply-templates/>
                        </xsl:element>
                    </xsl:element>
                </xsl:element>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="answer" namespace="Compositor">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>                
    </xsl:template>
    
    <xsl:template match="exe:question">
        <xsl:element name="question" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:part">       
            <xsl:choose>
                <xsl:when test="parent::node()[name()='exercise']">
                    <xsl:element name="part.exercise" namespace="Compositor">
                        <xsl:apply-templates/>
                    </xsl:element>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:element name="part" namespace="Compositor">
                        <xsl:apply-templates/>
                    </xsl:element>
                </xsl:otherwise>
            </xsl:choose> 
    </xsl:template>
    
    <xsl:template match="exe:figure">
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
            
            <xsl:apply-templates select="exe:img"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:img">
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
                                                        <xsl:value-of select="exe:img/@width"/>
                                                        <xsl:text>&#44;</xsl:text>
                                                        <xsl:value-of select="exe:img/@height"/>
                                                    </xsl:if>                                        
                                                </xsl:when>
                                                
                                                <xsl:otherwise>
                                                    <xsl:text>0&#44;0&#44;200&#44;100</xsl:text>
                                                </xsl:otherwise> 
                                                
                                            </xsl:choose>                                    
                                        </xsl:attribute>
                                    </xsl:element>
                                </xsl:element>
                                
                                <xsl:apply-templates select="exe:info"/>
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
    
    <xsl:template match="exe:image">
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
                        <xsl:apply-templates select="exe:area"/>
                    </xsl:element>
                </xsl:if>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:area">
        <xsl:element name="area" namespace="Compositor">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
       
        <xsl:for-each select=".">
            <xsl:apply-templates/>
        </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:math.display">
        <xsl:element name="math.display" namespace="Compositor">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:computation">
        <xsl:element name="math.array" namespace="Compositor">
            <xsl:attribute name="column">3</xsl:attribute>
            <xsl:for-each select="child::node()[name()='left']">
                <xsl:element name="row" namespace="Compositor">
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
    
    <xsl:template match="exe:left">
        <xsl:element name="cell" namespace="Compositor">
            <xsl:attribute name="colspan">
                <xsl:value-of select="2"/>
            </xsl:attribute>
            <xsl:attribute name="halign">
                <xsl:text>center</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="valign">
                <xsl:text>middle</xsl:text>
            </xsl:attribute>
            
            <xsl:element name="math" namespace="Compositor">
                <xsl:element name="latex" namespace="Compositor">
                    <xsl:apply-templates select="child::node()[name()='']"/>
                </xsl:element>
            </xsl:element>
            
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Compositor">
                    <xsl:apply-templates select="exe:info"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Compositor">
                    <xsl:apply-templates select="exe:info"/>
                </xsl:element>
            </xsl:if>
        
            <xsl:if test="child::node()[name()='cite']">
                <xsl:element name="cite" namespace="Compositor">
                    <xsl:apply-templates select="exe:cite"/>
                </xsl:element>
            </xsl:if>
        
            <xsl:if test="child::node()[name()='link']">
                <xsl:element name="link" namespace="Compositor">
                    <xsl:apply-templates select="exe:link"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>           
    </xsl:template>
    
    <xsl:template match="exe:center">
        <xsl:element name="cell" namespace="Compositor">
            <xsl:attribute name="colspan">
                <xsl:value-of select="1"/>
            </xsl:attribute>
            <xsl:attribute name="halign">
                <xsl:text>center</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="valign">
                <xsl:text>middle</xsl:text>
            </xsl:attribute>
            
            <xsl:element name="text" namespace="Compositor">
                <xsl:apply-templates select="child::node()[name()='']"/>
            </xsl:element>
            
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Compositor">
                    <xsl:apply-templates select="exe:info"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Compositor">
                    <xsl:apply-templates select="exe:info"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='cite']">
                <xsl:element name="cite" namespace="Compositor">
                    <xsl:apply-templates select="exe:cite"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='link']">
                <xsl:element name="link" namespace="Compositor">
                    <xsl:apply-templates select="exe:link"/>
                </xsl:element>
            </xsl:if>          
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:right">
        <xsl:element name="cell" namespace="Compositor">
            <xsl:attribute name="colspan">
                <xsl:value-of select="2"/>
            </xsl:attribute>
            <xsl:attribute name="halign">
                <xsl:text>center</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="valign">
                <xsl:text>middle</xsl:text>
            </xsl:attribute>
            
            <xsl:element name="math" namespace="Compositor">
                <xsl:element name="latex" namespace="Compositor">
                    <xsl:apply-templates select="child::node()[name()='']"/>
                </xsl:element>
            </xsl:element>
            
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Compositor">
                    <xsl:apply-templates select="exe:info"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='cite']">
                <xsl:element name="cite" namespace="Compositor">
                    <xsl:apply-templates select="exe:cite"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='link']">
                <xsl:element name="link" namespace="Compositor">
                    <xsl:apply-templates select="exe:link"/>
                </xsl:element>
            </xsl:if>
           
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:ul">
        <xsl:element name="ul" namespace="Compositor">
            <xsl:if test="./@bullet">
                <xsl:copy-of select="./@bullet"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:ol">
        <xsl:element name="ol" namespace="Compositor">
            <xsl:if test="./@type">
                <xsl:copy-of select="./@type"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:li">
        <xsl:element name="li" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:table">
        <xsl:element name="table" namespace="Compositor">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:row">
        <xsl:element name="row" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:cell">
        <xsl:element name="cell" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
  
    <xsl:template match="exe:para">
        <xsl:element name="para" namespace="Compositor">
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
                <xsl:element name="para.body" namespace="Compositor">
                    <xsl:for-each select=".">
                        <xsl:if test="node()[name()='index.glossary']">
                            <xsl:value-of select="exe:term"/>
                        </xsl:if>
                        <xsl:if test="node()[name()='index.symbol']">
                            <xsl:value-of select="exe:symbol"/>
                        </xsl:if>
                        <xsl:apply-templates select="node()[not(name() = 'index.glossary' or name() = 'index.symbol' or name() = 'index.author')]"/>
                    </xsl:for-each>
                </xsl:element>
                <xsl:apply-templates select="exe:index.author"/>
                <xsl:apply-templates select="exe:index.symbol"/>
                <xsl:apply-templates select="exe:index.glossary"/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:index.author">
        <xsl:element name="index.author" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:element name="name" namespace="Compositor">
                    <xsl:apply-templates select="exe:name"/>
                </xsl:element>
            </xsl:for-each>
            <xsl:apply-templates select="exe:info"/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:index.symbol">
        <xsl:element name="index.symbol" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:apply-templates select="exe:symbol"/>
            </xsl:for-each>
            <xsl:apply-templates select="exe:info"/>
            <xsl:if test="exe:sortstring != ''">
                <xsl:element name="sortstring" namespace="Compositor">
                    <xsl:apply-templates select="exe:sortstring"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:index.glossary">
        <xsl:element name="index.glossary" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:apply-templates select="exe:term"/>
            </xsl:for-each>
            <xsl:if test="exe:info != ''">
                <xsl:apply-templates select="exe:info"/>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:symbol">
        <xsl:element name="symbol" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:term">
        <xsl:element name="term" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    
    <xsl:template match="exe:name">
        <xsl:element name="name" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:first">
        <xsl:element name="first" namespace="Compositor">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="exe:middle">
        <xsl:element name="middle" namespace="Compositor">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="exe:last">
        <xsl:element name="last" namespace="Compositor">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="exe:initials">
        <xsl:element name="initials" namespace="Compositor">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:latex">
        <xsl:choose>
            <xsl:when test="parent::node()[name()='math.display']">
                <xsl:element name="latex" namespace="Compositor">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="math" namespace="Compositor">
                    <xsl:element name="latex" namespace="Compositor">
                        <xsl:apply-templates/>
                    </xsl:element>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>       
    </xsl:template>
    
    <xsl:template match="exe:b">
        <xsl:element name="strong" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="exe:i">
        <xsl:element name="emphasis" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:subordinate">
        <xsl:choose>
            <xsl:when test="child::node()[child::node()[name()='img' or name()='image']]">
                <xsl:apply-templates select="child::node()[child::node()[name()='img' or name()='image']]"/>
                <xsl:if test="exe:info">
                    <xsl:apply-templates select="node()[name()='info']"/>
                </xsl:if>
                <xsl:if test="exe:companion">
                    <xsl:apply-templates select="node()[name()='companion']"/>
                </xsl:if>
                <xsl:if test="exe:crossref">
                    <xsl:apply-templates select="node()[name()='crossref']"/>
                </xsl:if>
                <xsl:if test="exe:cite">
                    <xsl:apply-templates select="node()[name()='cite']"/>
                </xsl:if>
                <xsl:if test="exe:link">
                    <xsl:apply-templates select="node()[name()='link']"/>
                </xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="subordinate" namespace="Compositor">
                    <xsl:apply-templates select="node()[name()='hot']"/>
                    <xsl:if test="exe:info">
                        <xsl:apply-templates select="node()[name()='info']"/>
                    </xsl:if>
                    <xsl:if test="exe:companion">
                        <xsl:apply-templates select="node()[name()='companion']"/>
                    </xsl:if>
                    <xsl:if test="exe:crossref">
                        <xsl:apply-templates select="node()[name()='crossref']"/>
                    </xsl:if>
                    <xsl:if test="exe:cite">
                        <xsl:apply-templates select="node()[name()='cite']"/>
                    </xsl:if>
                    <xsl:if test="exe:link">
                        <xsl:apply-templates select="node()[name()='link']"/>
                    </xsl:if>
                    <!-- presentation may be the same processing as link, left unprocessed for now-->
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>      
    </xsl:template>
    
    <xsl:template match="exe:hot">
        <xsl:choose>
            <xsl:when test="child::node()[name()='img' or name()='image']">
                <xsl:apply-templates select="child::node()[name()='img' or name()='image']"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="hot" namespace="Compositor">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>        
    </xsl:template>
    
    <xsl:template match="exe:info">
        <xsl:element name="info" namespace="Compositor">
            <xsl:if test="exe:caption != ''">
                <xsl:element name="info.caption" namespace="Compositor">
                    <xsl:apply-templates select="exe:caption"/>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="node()[not(name()='caption')]"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:crossref">
        <xsl:if test="child::node()[name() = 'external.ref']">
            <xsl:apply-templates select ="exe:external.ref"/>
        </xsl:if>
        <xsl:element name="crossref" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'exercise.pack.ref']">
                    <xsl:element name="exercise.pack.ref" namespace="Compositor">
                        <xsl:attribute name="exercisePackID">
                            <xsl:value-of select="exe:exercise.pack.ref/@exercisePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'example.pack.ref']">
                    <xsl:element name="example.pack.ref" namespace="Compositor">
                        <xsl:attribute name="examplePackID">
                            <xsl:value-of select="exe:example.pack.ref/@examplePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Compositor">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="exe:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Compositor">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="exe:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Compositor">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="exe:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                    <xsl:element name="unit.ref" namespace="Compositor">
                        <xsl:attribute name="unitId">
                            <xsl:choose>
                                <xsl:when test="child::node()[name() = 'subpage.ref']">
                                    <xsl:value-of select="exe:subpage.ref/@subpageID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'chapter.ref']">
                                    <xsl:value-of select="exe:chapter.ref/@chapterID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'subsection.ref']">
                                    <xsl:value-of select="exe:subsection.ref/@subsectionID"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="exe:section.ref/@sectionID"/>
                                </xsl:otherwise>
                                
                            </xsl:choose>
                            
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
            
            <xsl:if test="child::node() != ''">
                <xsl:element name="info" namespace="Compositor">
                    <xsl:if test="child::node()[name() = 'caption']">
                        <xsl:element name="info.caption" namespace="Compositor">
                            <xsl:apply-templates select="exe:caption"/>
                        </xsl:element>
                    </xsl:if>
                    <xsl:apply-templates select="child::node()[not(name()='caption' or name()='subpage.ref' or name()='chapter.ref' or name()='subsection.ref' or name()='section.ref' or name()='theorem.ref' or name()='comment.ref' or name()='definition.ref' or name()='exercise.pack.ref' or name()='example.pack.ref' or name()='external.ref')]"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:external.ref">
        <xsl:element name="external.ref" namespace="Compositor">
            <xsl:if test="./@bookID">
                <xsl:element name="compositionID" namespace="Compositor">
                    <xsl:value-of select="./@bookID"/>
                </xsl:element>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'exercise.pack.ref']">
                    <xsl:element name="exercise.pack.ref" namespace="Compositor">
                        <xsl:attribute name="exercisePackID">
                            <xsl:value-of select="exe:exercise.pack.ref/@exercisePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'example.pack.ref']">
                    <xsl:element name="example.pack.ref" namespace="Compositor">
                        <xsl:attribute name="examplePackID">
                            <xsl:value-of select="exe:example.pack.ref/@examplePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Compositor">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="exe:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Compositor">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="exe:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Compositor">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="exe:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                    <xsl:element name="unit.ref" namespace="Compositor">
                        <xsl:attribute name="unitId">
                            <xsl:choose>
                                <xsl:when test="child::node()[name() = 'subpage.ref']">
                                    <xsl:value-of select="exe:subpage.ref/@subpageID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'chapter.ref']">
                                    <xsl:value-of select="exe:chapter.ref/@chapterID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'subsection.ref']">
                                    <xsl:value-of select="exe:subsection.ref/@subsectionID"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="exe:section.ref/@sectionID"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:cite">
        <xsl:if test="./@label != ''">
            <xsl:attribute name="label" namespace="Compositor">
                <xsl:value-of select="./@label"/>
            </xsl:attribute>
        </xsl:if>
        
        <xsl:element name="cite" namespace="Compositor">
            <xsl:if test="child::node()[name() = 'caption']">
                <xsl:element name="caption" namespace="Compositor">
                    <xsl:apply-templates select="exe:caption"/>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="exe:item"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:link">
        <xsl:element name="external.link" namespace="Compositor">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:if test="child::node() != ''">
                <xsl:element name="info" namespace="Compositor">
                    <xsl:if test="child::node()[name() = 'caption']">
                        <xsl:element name="info.caption" namespace="Compositor">
                            <xsl:apply-templates select="exe:caption"/>
                        </xsl:element>
                    </xsl:if>
                    <xsl:apply-templates select="child::node()[not(name()='caption')]"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:item">
        <xsl:element name="item" namespace="Compositor">
            <xsl:if test="child::node()[not(name() = 'citekey')]">
                <xsl:apply-templates select="child::node()[not(name() = 'citekey')]"/>
            </xsl:if>
            <xsl:if test="exe:citekey != ''">
                <xsl:apply-templates select="exe:citekey"/>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:citekey">
        <xsl:element name="citekey" namespace="Compositor">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:companion">
        <xsl:element name="companion" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'showme.pack.ref']">
                    <xsl:element name="showme.pack.ref" namespace="Compositor">
                        <xsl:attribute name="showmePackID">
                            <xsl:value-of select="exe:showme.pack.ref/@showmePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'quiz.pack.ref']">
                    <xsl:element name="quiz.pack.ref" namespace="Compositor">
                        <xsl:attribute name="quizPackID">
                            <xsl:value-of select="exe:quiz.pack.ref/@quizPackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Compositor">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="exe:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Compositor">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="exe:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Compositor">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="exe:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref']">
                    <xsl:element name="unit.ref" namespace="Compositor">
                        <xsl:attribute name="unitId">
                            <xsl:value-of select="exe:subpage.ref/@subpageID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[not(name()='subpage.ref' or name()='definition.ref' or name()='comment.ref' or name()='theorem.ref' or name()='quiz.pack.ref' or name()='showme.pack.ref')]">
                    <xsl:element name="info" namespace="Compositor">
                        <xsl:if test="child::node()[name() = 'caption']">
                            <xsl:element name="info.caption" namespace="Compositor">
                                <xsl:apply-templates select="exe:caption"/>
                            </xsl:element>
                        </xsl:if>
                        <xsl:apply-templates select="node()[not(name()='caption' or name()='subpage.ref' or name()='definition.ref' or name()='comment.ref' or name()='theorem.ref' or name()='quiz.pack.ref' or name()='showme.pack.ref')]"/>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
</xsl:stylesheet>