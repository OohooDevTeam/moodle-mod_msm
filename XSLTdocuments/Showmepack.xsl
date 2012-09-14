<?xml version="1.0" encoding="UTF-8"?>
<!--*
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
**************************************************************************-->

<xsl:stylesheet xmlns:compositor="Compositor"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:exa="http://webmath.math.ualberta.ca/v1/Example"
    xmlns:xi="http://www.w3.org/2001/XInclude"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    version="2.0">
    
    <xsl:output method="xml" indent="yes" version="1.0"
        encoding="UTF-8"
        doctype-system="../Symbols.dtd"/>
    
    <xsl:template match="exa:showme.pack">
        <xsl:element name="showme.pack" namespace="Compositor">
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
    
    <xsl:template match="exa:showme">
        <xsl:element name="showme" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:statement">
        <xsl:choose>
            <xsl:when test="parent::node()[name()='showme']">
                <xsl:element name="statement.showme" namespace="Compositor">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="statement.example" namespace="Compositor">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>
        
    </xsl:template>
    
    <xsl:template match="exa:answer">
        <xsl:choose>
            <xsl:when test="parent::node()[name()='example']">
                <xsl:element name="answer" namespace="Compositor">
                    <xsl:choose>
                        <xsl:when test="./@type != ''">
                            <xsl:attribute name="type">
                                <xsl:value-of select="./@type"/>
                            </xsl:attribute>                           
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:attribute name="type">
                                <xsl:text>solution</xsl:text>
                            </xsl:attribute>
                        </xsl:otherwise>
                    </xsl:choose>
                    
                    <xsl:choose>
                        <xsl:when test="./@version != ''">
                            <xsl:attribute name="version">
                                <xsl:value-of select="./@version"/>
                            </xsl:attribute>                           
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:attribute name="version">
                                <xsl:value-of select="1"/>
                            </xsl:attribute>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:element name="answer.block" namespace="Compositor">
                        <xsl:if test="child::node()[name()='caption']">
                            <xsl:apply-templates select="exa:caption"/>
                        </xsl:if>
                        <xsl:element name="answer.block.body" namespace="Compositor">                            
                            <xsl:apply-templates select="child::node()[not(name()='caption')]"/>
                        </xsl:element>
                    </xsl:element>
                </xsl:element>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="answer.showme" namespace="Compositor">
                    <xsl:if test="./@type != ''">
                        <xsl:attribute name="type">
                            <xsl:value-of select="./@type"/>
                        </xsl:attribute>                           
                    </xsl:if>
                    <xsl:element name="answer.showme.block" namespace="Compositor">
                        <xsl:if test="child::node()[name()='caption']">
                            <xsl:apply-templates select="exa:caption"/>
                        </xsl:if>
                        <xsl:element name="answer.showme.block.body" namespace="Compositor">                            
                            <xsl:apply-templates select="child::node()[not(name()='caption')]"/>
                        </xsl:element>
                    </xsl:element>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>      
    </xsl:template>
    
    <xsl:template match="exa:texsupport">
        <xsl:element name="texsupport" namespace="Compositor">
            <xsl:attribute name="href">
                <xsl:value-of select="./@href"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:title">
        <xsl:element name="title" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <!--xsl:template match="exa:caption">
        <xsl:element name="caption" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template-->
    
    <xsl:template match="exa:caption">
    <xsl:choose>
        <xsl:when test="parent::node()[name()='info']">
            <xsl:element name="info.caption" namespace="Compositor">
                <xsl:apply-templates/>
            </xsl:element>
        </xsl:when>
        <xsl:otherwise>
            <xsl:apply-templates select="child::node()[name()='partref']"/>
            <xsl:element name="caption" namespace="Compositor">
                <xsl:apply-templates select="child::node()[not(name()='partref')]"/>
            </xsl:element>
        </xsl:otherwise>
    </xsl:choose>      
    </xsl:template>
        
        <xsl:template match="exa:tablabel">
            <xsl:element name="textcaption" namespace="Compositor">
                <xsl:apply-templates/>
            </xsl:element>
        </xsl:template>
    
    <xsl:template match="exa:figure">
        <xsl:element name="media" namespace="Compositor">
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
            
            <xsl:apply-templates select="exa:img"/>
           
        </xsl:element>
    </xsl:template>
        
    <xsl:template match="exa:img">
        <xsl:choose>     
            <xsl:when test="parent::node()[name()='figure']">
                <xsl:element name="img" namespace="Compositor">
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
                        
                        <xsl:element name="extended.caption" namespace="Compositor">
                            
                            <xsl:if test="child::node()[name()='info']">
                                
                                <xsl:element name="image.mapping" namespace="Compositor">
                                    
                                    <xsl:element name="area" namespace="Compositor">
                                        <xsl:attribute name="shape">
                                            <xsl:text>rect</xsl:text>
                                        </xsl:attribute>
                                        
                                        <xsl:attribute name="coord">
                                            
                                            <xsl:choose>
                                                <xsl:when test="child::node()[name()='img'][attribute::width]">
                                                    <xsl:if test="child::node()[name()='img'][attribute::height]">                                       
                                                        <xsl:text>0&#44;0&#44;</xsl:text>
                                                        <xsl:value-of select="exa:img/@width"/>
                                                        <xsl:text>&#44;</xsl:text>
                                                        <xsl:value-of select="exa:img/@height"/>
                                                    </xsl:if>                                        
                                                </xsl:when>
                                                
                                                <xsl:otherwise>
                                                    <xsl:text>0&#44;0&#44;200&#44;100</xsl:text>
                                                </xsl:otherwise> 
                                                
                                            </xsl:choose>                                    
                                        </xsl:attribute>
                                    </xsl:element>
                                </xsl:element>
                                
                                <xsl:apply-templates select="exa:info"/>
                            </xsl:if>
                            <xsl:if test="child::node()[name()='caption']">
                                <xsl:apply-templates select="child::node()[name()='caption']"/>
                            </xsl:if>
                        </xsl:element>
                    </xsl:if>
                </xsl:element>         
            </xsl:when>
            <xsl:when test="parent::node()[name()='hot']">
                    <xsl:attribute name="type">image</xsl:attribute>
                    <xsl:attribute name="active">1</xsl:attribute>
                    <xsl:attribute name="inline">0</xsl:attribute>
                    <xsl:element name="img" namespace="Compositor">
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
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="media" namespace="Compositor">
                    <xsl:if test="parent::node()[name()='figure'][attribute::id]">
                        <xsl:attribute name="id">
                            <xsl:value-of select="parent::node()/@id"/>
                        </xsl:attribute>
                    </xsl:if>  
                    
                    <xsl:attribute name="type">image</xsl:attribute>
                    <xsl:attribute name="active">0</xsl:attribute>
                    <xsl:attribute name="inline">0</xsl:attribute>
                    <xsl:element name="img" namespace="Compositor">
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
    
    <xsl:template match="exa:image">
        <xsl:element name="media" namespace="Compositor">
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
            <xsl:element name="img" namespace="Compositor">
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
                <xsl:element name="image.mapping" namespace="Compositor">
                    <xsl:apply-templates select="exa:area"/>
                </xsl:element>
            </xsl:if>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:area">
        <xsl:element name="area" namespace="Compositor">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
        
        <xsl:for-each select=".">
            <xsl:apply-templates/>
        </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:math.display">
        <xsl:element name="math.display" namespace="Compositor">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:computation">
        <xsl:element name="math.array" namespace="Compositor">
            <xsl:attribute name="column">3</xsl:attribute>
            <xsl:for-each select="child::node()[name()='left']">
                <xsl:element name="row" namespace="Compositor">
                    <xsl:attribute name="rowspan" namespace="Compositor">
                        <xsl:value-of select="1"/>
                    </xsl:attribute>
                    <xsl:apply-templates select="."/>
                    <xsl:apply-templates select="following-sibling::node()[name()='center'][position()=1]"/>
                    <xsl:apply-templates select="following-sibling::node()[name()='right'][position()=1]"/>
                </xsl:element>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:left">
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
                    <xsl:apply-templates select="exa:info"/>
                </xsl:element>
            </xsl:if>
        
            <xsl:if test="child::node()[name()='cite']">
                <xsl:element name="cite" namespace="Compositor">
                    <xsl:apply-templates select="exa:cite"/>
                </xsl:element>
            </xsl:if>
        
            <xsl:if test="child::node()[name()='link']">
                <xsl:element name="link" namespace="Compositor">
                    <xsl:apply-templates select="exa:link"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>           
    </xsl:template>
    
    <xsl:template match="exa:center">
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
            
            <xsl:element name="math" namespace="Compositor">
                <xsl:element name="latex" namespace="Compositor">
                    <xsl:apply-templates select="child::node()[name()='']"/>
                </xsl:element>               
            </xsl:element>
            
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Compositor">
                    <xsl:apply-templates select="exa:info"/>
                </xsl:element>
            </xsl:if>
           
            <xsl:if test="child::node()[name()='cite']">
                <xsl:element name="cite" namespace="Compositor">
                    <xsl:apply-templates select="exa:cite"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='link']">
                <xsl:element name="link" namespace="Compositor">
                    <xsl:apply-templates select="exa:link"/>
                </xsl:element>
            </xsl:if>          
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:right">
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
                    <xsl:apply-templates select="exa:info"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='cite']">
                <xsl:element name="cite" namespace="Compositor">
                    <xsl:apply-templates select="exa:cite"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='link']">
                <xsl:element name="link" namespace="Compositor">
                    <xsl:apply-templates select="exa:link"/>
                </xsl:element>
            </xsl:if>
           
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:ul">
        <xsl:element name="ul" namespace="Compositor">
            <xsl:if test="./@bullet">
                <xsl:copy-of select="./@bullet"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:ol">
        <xsl:element name="ol" namespace="Compositor">
            <xsl:if test="./@type">
                <xsl:copy-of select="./@type"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:li">
        <xsl:element name="li" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:table">
        <xsl:element name="table" namespace="Compositor">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:row">
        <xsl:element name="row" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:cell">
        <xsl:element name="cell" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
  
    <xsl:template match="exa:para">
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
                            <xsl:value-of select="exa:term"/>
                        </xsl:if>
                        <xsl:if test="node()[name()='index.symbol']">
                            <xsl:value-of select="exa:symbol"/>
                        </xsl:if>
                        <xsl:apply-templates select="node()[not(name() = 'index.glossary' or name() = 'index.symbol' or name() = 'index.author')]"/>
                    </xsl:for-each>
                </xsl:element>
                <xsl:apply-templates select="exa:index.author"/>
                <xsl:apply-templates select="exa:index.symbol"/>
                <xsl:apply-templates select="exa:index.glossary"/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:index.author">
        <xsl:element name="index.author" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:element name="name" namespace="Compositor">
                    <xsl:apply-templates select="exa:name"/>
                </xsl:element>
            </xsl:for-each>
            <xsl:apply-templates select="exa:info"/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:index.symbol">
        <xsl:element name="index.symbol" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:apply-templates select="exa:symbol"/>
            </xsl:for-each>
            <xsl:apply-templates select="exa:info"/>
            <xsl:if test="exa:sortstring != ''">
                <xsl:element name="sortstring" namespace="Compositor">
                    <xsl:apply-templates select="exa:sortstring"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:index.glossary">
        <xsl:element name="index.glossary" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:apply-templates select="exa:term"/>
            </xsl:for-each>
            <xsl:if test="exa:info != ''">
                <xsl:apply-templates select="exa:info"/>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:symbol">
        <xsl:element name="symbol" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:term">
        <xsl:element name="term" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    
    <xsl:template match="exa:name">
        <xsl:element name="name" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:first">
        <xsl:element name="first" namespace="Compositor">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="exa:middle">
        <xsl:element name="middle" namespace="Compositor">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="exa:last">
        <xsl:element name="last" namespace="Compositor">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="exa:initials">
        <xsl:element name="initials" namespace="Compositor">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:latex">
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
    
    <xsl:template match="exa:b">
        <xsl:element name="strong" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="exa:i">
        <xsl:element name="emphasis" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:subordinate">
        <xsl:choose>
            <xsl:when test="child::node()[child::node()[name()='img' or name()='image']]">
                <xsl:element name="media" namespace="Compositor">
                    <xsl:apply-templates select="child::node()[child::node()[name()='img' or name()='image']]"/>
                    <xsl:if test="exa:info">
                        <xsl:apply-templates select="node()[name()='info']"/>
                    </xsl:if>
                    <xsl:if test="exa:companion">
                        <xsl:apply-templates select="node()[name()='companion']"/>
                    </xsl:if>
                    <xsl:if test="exa:crossref">
                        <xsl:apply-templates select="node()[name()='crossref']"/>
                    </xsl:if>
                    <xsl:if test="exa:cite">
                        <xsl:apply-templates select="node()[name()='cite']"/>
                    </xsl:if>
                    <xsl:if test="exa:link">
                        <xsl:apply-templates select="node()[name()='link']"/>
                    </xsl:if>
                </xsl:element>                
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="subordinate" namespace="Compositor">
                    <xsl:apply-templates select="node()[name()='hot']"/>
                    <xsl:if test="exa:info">
                        <xsl:apply-templates select="node()[name()='info']"/>
                    </xsl:if>
                    <xsl:if test="exa:companion">
                        <xsl:apply-templates select="node()[name()='companion']"/>
                    </xsl:if>
                    <xsl:if test="exa:crossref">
                        <xsl:apply-templates select="node()[name()='crossref']"/>
                    </xsl:if>
                    <xsl:if test="exa:cite">
                        <xsl:apply-templates select="node()[name()='cite']"/>
                    </xsl:if>
                    <xsl:if test="exa:link">
                        <xsl:apply-templates select="node()[name()='link']"/>
                    </xsl:if>
                    <!-- presentation may be the same processing as link, left unprocessed for now-->
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>      
    </xsl:template>
  
    <xsl:template match="exa:hot">
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
    
    <xsl:template match="exa:info">
        <xsl:element name="info" namespace="Compositor">
            <xsl:if test="exa:caption != ''">
               
                    <xsl:apply-templates select="exa:caption"/>
                
            </xsl:if>
            <xsl:apply-templates select="node()[not(name()='caption')]"/>            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:crossref">
        <xsl:if test="child::node()[name() = 'external.ref']">
            <xsl:apply-templates select ="exa:external.ref"/>
        </xsl:if>
        <xsl:element name="crossref" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'exercise.pack.ref']">
                    <xsl:element name="exercise.pack.ref" namespace="Compositor">
                        <xsl:attribute name="exercisePackID">
                            <xsl:value-of select="exa:exercise.pack.ref/@exercisePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'example.pack.ref']">
                    <xsl:element name="example.pack.ref" namespace="Compositor">
                        <xsl:attribute name="examplePackID">
                            <xsl:value-of select="exa:example.pack.ref/@examplePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Compositor">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="exa:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Compositor">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="exa:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Compositor">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="exa:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                    <xsl:element name="unit.ref" namespace="Compositor">
                        <xsl:attribute name="unitId">
                            <xsl:choose>
                                <xsl:when test="child::node()[name() = 'subpage.ref']">
                                    <xsl:value-of select="exa:subpage.ref/@subpageID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'chapter.ref']">
                                    <xsl:value-of select="exa:chapter.ref/@chapterID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'subsection.ref']">
                                    <xsl:value-of select="exa:subsection.ref/@subsectionID"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="exa:section.ref/@sectionID"/>
                                </xsl:otherwise>
                                
                            </xsl:choose>
                            
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
            
            <xsl:if test="child::node() != ''">
                <xsl:element name="info" namespace="Compositor">
                    <xsl:if test="child::node()[name() = 'caption']">
                        
                            <xsl:apply-templates select="exa:caption"/>
                        
                    </xsl:if>
                    <xsl:apply-templates select="child::node()[not(name()='caption' or name()='subpage.ref' or name()='chapter.ref' or name()='subsection.ref' or name()='section.ref' or name()='theorem.ref' or name()='comment.ref' or name()='definition.ref' or name()='exercise.pack.ref' or name()='example.pack.ref' or name()='external.ref')]"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:external.ref">
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
                            <xsl:value-of select="exa:exercise.pack.ref/@exercisePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'example.pack.ref']">
                    <xsl:element name="example.pack.ref" namespace="Compositor">
                        <xsl:attribute name="examplePackID">
                            <xsl:value-of select="exa:example.pack.ref/@examplePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Compositor">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="exa:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Compositor">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="exa:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Compositor">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="exa:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                    <xsl:element name="unit.ref" namespace="">
                        <xsl:attribute name="unitId">
                            <xsl:choose>
                                <xsl:when test="child::node()[name() = 'subpage.ref']">
                                    <xsl:value-of select="exa:subpage.ref/@subpageID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'chapter.ref']">
                                    <xsl:value-of select="exa:chapter.ref/@chapterID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'subsection.ref']">
                                    <xsl:value-of select="exa:subsection.ref/@subsectionID"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="exa:section.ref/@sectionID"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:cite">
        <xsl:if test="./@label != ''">
            <xsl:attribute name="label">
                <xsl:value-of select="./@label"/>
            </xsl:attribute>
        </xsl:if>
        
        <xsl:element name="cite">
            <xsl:if test="child::node()[name() = 'caption']">
                <xsl:element name="caption" namespace="Compositor">
                    <xsl:apply-templates select="exa:caption"/>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="exa:item"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:link">
        <xsl:element name="external.link" namespace="Compositor">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:if test="child::node() != ''">
                <xsl:element name="info" namespace="Compositor">
                    <xsl:if test="child::node()[name() = 'caption']">
                       
                            <xsl:apply-templates select="exa:caption"/>
                        
                    </xsl:if>
                    <xsl:apply-templates select="child::node()[not(name()='caption')]"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:item">
        <xsl:element name="item" namespace="Compositor">
            <xsl:if test="child::node()[not(name() = 'citekey')]">
                <xsl:apply-templates select="child::node()[not(name() = 'citekey')]"/>
            </xsl:if>
            <xsl:if test="exa:citekey != ''">
                <xsl:apply-templates select="exa:citekey"/>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:citekey">
        <xsl:element name="citekey" namespace="Compositor">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:companion">
        <xsl:element name="companion" namespace="Compositor">
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'showme.pack.ref']">
                    <xsl:element name="showme.pack.ref" namespace="Compositor">
                        <xsl:attribute name="showmePackID">
                            <xsl:value-of select="exa:showme.pack.ref/@showmePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'quiz.pack.ref']">
                    <xsl:element name="quiz.pack.ref" namespace="Compositor">
                        <xsl:attribute name="quizPackID">
                            <xsl:value-of select="exa:quiz.pack.ref/@quizPackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Compositor">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="exa:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Compositor">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="exa:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Compositor">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="exa:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref']">
                    <xsl:element name="unit.ref" namespace="Compositor">
                        <xsl:attribute name="unitId">
                            <xsl:value-of select="exa:subpage.ref/@subpageID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[not(name()='subpage.ref' or name()='definition.ref' or name()='comment.ref' or name()='theorem.ref' or name()='quiz.pack.ref' or name()='showme.pack.ref')]">
                    <xsl:element name="info" namespace="Compositor">
                        <xsl:if test="child::node()[name() = 'caption']">
                            
                                <xsl:apply-templates select="exa:caption"/>
                            
                        </xsl:if>
                        <xsl:apply-templates select="node()[not(name()='caption' or name()='subpage.ref' or name()='definition.ref' or name()='comment.ref' or name()='theorem.ref' or name()='quiz.pack.ref' or name()='showme.pack.ref')]"/>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
</xsl:stylesheet>