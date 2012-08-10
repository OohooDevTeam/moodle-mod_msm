<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xi="http://www.w3.org/2001/XInclude"
    xmlns:bk="http://webmath.math.ualberta.ca/v1/Book"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:unit="Unit"
    version="2.0">
    
    <xsl:import href="Unit.xsl"/>
    
    <xsl:output method="xml" indent="yes" version="1.0"
        encoding="UTF-8"/>
    
    <xsl:strip-space elements="*"/>
    
    <xsl:template match="bk:sub.page">
        <xsl:element name="unit" namespace="Unit">
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
            
            <xsl:element name="titles" namespace="Unit">              
               <xsl:apply-templates select="bk:title"/>                
            </xsl:element>
            
          
            <xsl:apply-templates select="bk:sub.page.body"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:sub.page.body">
        <xsl:element name="body" namespace="Unit">
            <xsl:choose>
                <xsl:when test="child::node()[name() = 'block']">
                    <xsl:apply-templates/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:element name="block" namespace="Unit">
                        <xsl:element name="block.body" namespace="Unit">
                            <xsl:apply-templates/>
                        </xsl:element>          
                    </xsl:element>  
                </xsl:otherwise>
            </xsl:choose>           
        </xsl:element>      
    </xsl:template>
    
    <xsl:template match="bk:block">
        <xsl:element name="block" namespace="Unit">
            <xsl:element name="block.body" namespace="Unit">
                <xsl:apply-templates/>
            </xsl:element>          
        </xsl:element>   
    </xsl:template>
</xsl:stylesheet>