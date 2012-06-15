<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:sp="http://webmath.math.ualberta.ca/v1/Simplepage"
    xmlns:xi="http://www.w3.org/2001/XInclude"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:unit = "Unit"
    version="2.0">
    
    <xsl:output method="xml" indent="yes" version="1.0"
                encoding="UTF-8"
                doctype-system="../Symbols.dtd"/>
    
    <xsl:template match="sp:simplepage">
        <xsl:element name="unit" namespace="Unit">
            <xsl:if test="sp:texsupport|sp:literature.db != ''">
                <xsl:element name="header" namespace="Unit">
                    <xsl:copy-of select="sp:texsupport"/>
                    <xsl:if test="sp:literature.db != ''">
                        <xsl:element name="literature.db" namespace="Unit">
                            <xsl:value-of select="sp:literature.db"/>
                        </xsl:element>
                    </xsl:if>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="sp:header"/>
            <xsl:apply-templates select="sp:simple.body"/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:headers">
        <xsl:element name="titles" namespace="Unit">
            <xsl:element name="title" namespace="Unit">
                <xsl:apply-templates select="sp:full"/>
            </xsl:element>
            
            <xsl:element name="plain.title" namespace="Unit">
                <xsl:value-of select="sp:short"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:full">
        <xsl:apply-templates/>
    </xsl:template>
    
    <xsl:template match="sp:simple.body">
        <xsl:element name="body" namespace="Unit">
        <xsl:element name="block" namespace="Unit">
            <xsl:element name="block.body" namespace="Unit">
                <xsl:apply-templates/>
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
    
    <xsl:template match="sp:figure">
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
            
            <xsl:apply-templates select="sp:img"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:img">
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
                                                        <xsl:value-of select="sp:img/@width"/>
                                                        <xsl:text>&#44;</xsl:text>
                                                        <xsl:value-of select="sp:img/@height"/>
                                                    </xsl:if>                                        
                                                </xsl:when>
                                                
                                                <xsl:otherwise>
                                                    <xsl:text>0&#44;0&#44;200&#44;100</xsl:text>
                                                </xsl:otherwise> 
                                                
                                            </xsl:choose>                                    
                                        </xsl:attribute>
                                    </xsl:element>
                                </xsl:element>
                                
                                <xsl:apply-templates select="sp:info"/>
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
    
    <xsl:template match="sp:image">
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
                        <xsl:apply-templates select="sp:area"/>
                    </xsl:element>
                </xsl:if>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:area">
        <xsl:element name="area" namespace="Unit">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
        
        <xsl:for-each select=".">
            <xsl:apply-templates/>
        </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:math.display">
        <xsl:element name="math.display" namespace="Unit">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:computation">
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
    
    <xsl:template match="sp:left">
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
                    <xsl:apply-templates select="sp:info"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Unit">
                    <xsl:apply-templates select="sp:info"/>
                </xsl:element>
            </xsl:if>
        
            <xsl:if test="child::node()[name()='cite']">
                <xsl:element name="cite" namespace="Unit">
                    <xsl:apply-templates select="sp:cite"/>
                </xsl:element>
            </xsl:if>
        
            <xsl:if test="child::node()[name()='link']">
                <xsl:element name="link" namespace="Unit">
                    <xsl:apply-templates select="sp:link"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>           
    </xsl:template>
    
    <xsl:template match="sp:center">
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
                <xsl:element name="companion" namespace="Unit">
                    <xsl:apply-templates select="sp:info"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Unit">
                    <xsl:apply-templates select="sp:info"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='cite']">
                <xsl:element name="cite" namespace="Unit">
                    <xsl:apply-templates select="sp:cite"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='link']">
                <xsl:element name="link" namespace="Unit">
                    <xsl:apply-templates select="sp:link"/>
                </xsl:element>
            </xsl:if>          
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:right">
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
                    <xsl:apply-templates select="sp:info"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='cite']">
                <xsl:element name="cite" namespace="Unit">
                    <xsl:apply-templates select="sp:cite"/>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='link']">
                <xsl:element name="link" namespace="Unit">
                    <xsl:apply-templates select="sp:link"/>
                </xsl:element>
            </xsl:if>
           
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:ul">
        <xsl:element name="ul" namespace="Unit">
            <xsl:if test="./@bullet">
                <xsl:copy-of select="./@bullet"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:ol">
        <xsl:element name="ol" namespace="Unit">
            <xsl:if test="./@type">
                <xsl:copy-of select="./@type"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:li">
        <xsl:element name="li" namespace="Unit">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:table">
        <xsl:element name="table" namespace="Unit">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:row">
        <xsl:element name="row" namespace="Unit">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:cell">
        <xsl:element name="cell" namespace="Unit">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
  
    <xsl:template match="sp:para">
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
                        <xsl:apply-templates/>
                    </xsl:for-each>
                </xsl:element>               
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:latex">
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
    
    <xsl:template match="sp:b">
        <xsl:element name="strong" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="sp:i">
        <xsl:element name="emphasis" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:subordinate">
        <xsl:choose>
            <xsl:when test="child::node()[child::node()[name()='img' or name()='image']]">
                <xsl:apply-templates select="child::node()[child::node()[name()='img' or name()='image']]"/>
                <xsl:if test="sp:info">
                    <xsl:apply-templates select="node()[name()='info']"/>
                </xsl:if>
                <xsl:if test="sp:cite">
                    <xsl:apply-templates select="node()[name()='cite']"/>
                </xsl:if>
                <xsl:if test="sp:link">
                    <xsl:apply-templates select="node()[name()='link']"/>
                </xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="subordinate" namespace="Unit">
                    <xsl:apply-templates select="node()[name()='hot']"/>
                    <xsl:if test="sp:info">
                        <xsl:apply-templates select="node()[name()='info']"/>
                    </xsl:if>
                    <xsl:if test="sp:cite">
                        <xsl:apply-templates select="node()[name()='cite']"/>
                    </xsl:if>
                    <xsl:if test="sp:link">
                        <xsl:apply-templates select="node()[name()='link']"/>
                    </xsl:if>
                    <!-- presentation may be the same processing as link, left unprocessed for now-->
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>      
    </xsl:template>
    
    <xsl:template match="sp:hot">
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
    
    <xsl:template match="sp:info">
        <xsl:element name="info" namespace="Unit">
            <xsl:if test="sp:caption != ''">
                <xsl:element name="info.caption" namespace="Unit">
                    <xsl:apply-templates select="sp:caption"/>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="node()[not(name()='caption')]"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:cite">
        <xsl:if test="./@label != ''">
            <xsl:attribute name="label">
                <xsl:value-of select="./@label"/>
            </xsl:attribute>
        </xsl:if>
        
        <xsl:element name="cite" namespace="Unit">
            <xsl:if test="child::node()[name() = 'caption']">
                <xsl:element name="caption" namespace="Unit">
                    <xsl:apply-templates select="sp:caption"/>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="sp:item"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:link">
        <xsl:element name="external.link" namespace="Unit">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:if test="child::node() != ''">
                <xsl:element name="info" namespace="Unit">
                    <xsl:if test="child::node()[name() = 'caption']">
                        <xsl:element name="info.caption" namespace="Unit">
                            <xsl:apply-templates select="sp:caption"/>
                        </xsl:element>
                    </xsl:if>
                    <xsl:apply-templates select="child::node()[not(name()='caption')]"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:item">
        <xsl:element name="item" namespace="Unit">
            <xsl:if test="child::node()[not(name() = 'citekey')]">
                <xsl:apply-templates select="child::node()[not(name() = 'citekey')]"/>
            </xsl:if>
            <xsl:if test="sp:citekey != ''">
                <xsl:apply-templates select="sp:citekey"/>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="sp:citekey">
        <xsl:element name="citekey" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
</xsl:stylesheet>