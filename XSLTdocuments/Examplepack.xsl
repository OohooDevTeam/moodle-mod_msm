<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:exa="http://webmath.math.ualberta.ca/v1/Example"
    xmlns:xi="http://www.w3.org/2001/XInclude"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:compositor="Compositor"
    version="2.0">
    
    <xsl:import href="Showmepack.xsl"/>
    
    <xsl:output method="xml" indent="yes" version="1.0"
        encoding="UTF-8"
        doctype-system="Symbols.dtd"/>
    
    <xsl:template match="exa:example.pack">
        <xsl:element name="example.pack" namespace="Compositor">
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
    
    <xsl:template match="exa:example">
        <xsl:element name="example" namespace="Compositor">
            <xsl:if test="./@id != ''">
                <xsl:attribute name="id">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:answer.ext">
        <xsl:element name="answer.ext" namespace="Compositor">
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
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exa:step">
        <xsl:choose>
            <xsl:when test="parent::node()[not(name()='pilot')]">
                <xsl:element name="step" namespace="Compositor">
                    <xsl:if test="./@partref != ''">
                        <xsl:attribute name="partref">
                            <xsl:value-of select="./@partref"/>
                        </xsl:attribute>
                    </xsl:if>
                    <xsl:if test="child::node()[name()='pilot']">
                        <xsl:apply-templates select="child::node()[name()='pilot']"/>
                    </xsl:if>
                    <xsl:element name="step.body" namespace="Compositor">
                        <xsl:apply-templates select="child::node()[not(name()='caption' or name()='pilot')]"/>
                    </xsl:element>
                </xsl:element> 
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates/>
            </xsl:otherwise>
        </xsl:choose>
        
    </xsl:template>
    
    <xsl:template match="exa:pilot">
        <xsl:element name="pilot" namespace="Compositor">
            <xsl:if test="child::node()[name()='step']">               
                    <xsl:element name="pilot.step" namespace="Compositor">
                        <xsl:apply-templates select="child::node()[(name()='step')]"/>
                    </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
</xsl:stylesheet>
    