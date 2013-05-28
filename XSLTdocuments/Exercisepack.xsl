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

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:compositor="Compositor"
    xmlns:exe="http://webmath.math.ualberta.ca/v1/Exercise"
    xmlns:xi="http://www.w3.org/2001/XInclude"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    version="2.0">
    
    <xsl:import href="Quizpack.xsl"/>
    
    <xsl:output method="xml" indent="yes" version="1.0"
                encoding="UTF-8"
                doctype-system="../Symbols.dtd"/>
    
    <xsl:template match="exe:exercise.pack">
        <xsl:element name="exercise.pack" namespace="Compositor">
        <xsl:if test="./@id != ''">
            <xsl:attribute name="id">
                <xsl:value-of select="./@id"/>
            </xsl:attribute>
        </xsl:if>
            <xsl:if test="./@xsi:schemaLocation">
                <xsl:attribute name="xsi:schemaLocation">Compositor <xsl:sequence select="resolve-uri('Compositor.xsd')"/></xsl:attribute>
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
    
    <xsl:template match="exe:exercise">
        <xsl:element name="exercise" namespace="Compositor">
            <xsl:if test="./@id != ''">
                <xsl:attribute name="id">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:problem">
        <xsl:element name="problem" namespace="Compositor">
            <xsl:if test="child::node()[name()='caption']">
                <xsl:apply-templates select="exe:caption"/>
            </xsl:if>
            <xsl:if test="child::node()[name()='tablabel']">
                <xsl:apply-templates select="exe:tablabel"/>
            </xsl:if>
            <xsl:element name="problem.body" namespace="Compositor">
                <xsl:apply-templates select="child::node()[not(name() = 'caption' or name()='tablabel')]"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:approach">
        <xsl:element name="approach" namespace="Compositor">
            <xsl:choose>
                <xsl:when test="./@version">
                    <xsl:attribute name="version">
                        <xsl:value-of select="./@version"/>
                    </xsl:attribute>
                </xsl:when>
                <xsl:otherwise> <!-- default value is 1-->
                    <xsl:attribute name="version">
                        <xsl:value-of select="1"/>
                    </xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
            
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:solution.hint">
        <xsl:element name="solution.hint" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:solution">
        <xsl:element name="solution" namespace="Compositor">
            <xsl:if test="child::node()[name()='caption']">
                <xsl:apply-templates select="child::node()[name()='caption']"/>
            </xsl:if>
            <xsl:element name="solution.body" namespace="Compositor">
                <xsl:apply-templates select="child::node()[not(name() ='caption')]"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:approach.ext">
        <xsl:element name="approach.ext" namespace="Compositor">
            <xsl:choose>
                <xsl:when test="./@version">
                    <xsl:attribute name="version">
                        <xsl:value-of select="./@version"/>
                    </xsl:attribute>
                </xsl:when>
                <xsl:otherwise> <!-- default value is 1-->
                    <xsl:attribute name="version">
                        <xsl:value-of select="1"/>
                    </xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:solution.ext">
        <xsl:element name="solution.ext" namespace="Compositor">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:step">
        <xsl:element name="step" namespace="Compositor">
            <xsl:if test="./@partref != ''">
                <xsl:attribute name="partref">
                    <xsl:value-of select="./@partref"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:if test="child::node()[name()='caption']">
                <xsl:apply-templates select="child::node()[name()='caption']"/>
            </xsl:if>
            <xsl:if test="child::node()[name()='pilot']">
                <xsl:apply-templates select="child::node()[name()='pilot']"/>
            </xsl:if>
            <xsl:element name="step.body" namespace="Compositor">
                <xsl:apply-templates select="child::node()[not(name()='caption' or name()='pilot')]"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="exe:pilot">
        <xsl:element name="pilot" namespace="Compositor">
            <xsl:if test="child::node()[not(name()='quiz')]">
                <xsl:element name="pilot.body" namespace="Compositor">
                    <xsl:apply-templates select="child::node()[not(name()='quiz')]"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="child::node()[name()='quiz']">
                <xsl:apply-templates select="child::node()[name()='quiz']"/>
            </xsl:if>
            
            <!--xsl:if test="child::node()[name()='quiz']">
                <xsl:for-each select="child::node()[name()='quiz']">
                    <xsl:apply-templates select="."/>
                    <xsl:element name="pilot.body" namespace="Compositor">
                        <xsl:apply-templates select="child::node()[not(name()='quiz')]"/>
                    </xsl:element>
                </xsl:for-each>
            </xsl:if-->
        </xsl:element>
    </xsl:template>
</xsl:stylesheet>