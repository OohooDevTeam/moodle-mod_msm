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

<!--
* This XSLT document is used to convert section elements as a child element of book elements to
* unit elements within another unit element. The new schema file to follow is Unit.xsd in NewSchemas directory
-->

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
    
    <xsl:template match="bk:section" name="section">
        <xsl:element name="unit" namespace="Unit">
            <xsl:if test="./@id != ''">
                <xsl:attribute name="unitid">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            
            <xsl:if test="./@xsi:schemaLocation">
                <xsl:attribute name="xsi:schemaLocation">Unit 
                    <xsl:sequence select="resolve-uri('Unit.xsd')"/>
                </xsl:attribute>
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
            <xsl:attribute name="standalone">
                <xsl:value-of>false</xsl:value-of>
            </xsl:attribute>
            
            <xsl:if test="bk:headers">
                <xsl:apply-templates select="bk:headers"/>
            </xsl:if>
            <xsl:if test="xi:include/@href != ''">
                <xsl:for-each select="xi:include">
                    <xsl:element name="xi:include" namespace="http://www.w3.org/2001/XInclude">
                        <xsl:attribute name="href">
                            <xsl:value-of select="./@href"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:for-each>
            </xsl:if>
            
            <xsl:if test="node()[name()='intro']">
                <xsl:apply-templates select="node()[name()='intro']"/>
            </xsl:if>
            
            <xsl:if test="node()[name()='section.body']">
                <xsl:apply-templates select="bk:section.body"/>
            </xsl:if>
            
            <xsl:if test="node()[name()='subsection']">
                <xsl:element name="legitimate.children" namespace="Unit">
                    <xsl:apply-templates select="bk:subsection"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="node()[name()='studymaterials']">                
                <xsl:apply-templates select="bk:studymaterials"/>                
            </xsl:if>
        </xsl:element>
    </xsl:template>
   
    <xsl:template match="bk:intro" name="intro">
        <xsl:element name="intro" namespace="Unit">
            <xsl:if test="./@id != ''">
                <xsl:attribute name="id">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:element name="block" namespace="Unit">
                <xsl:element name="block.body" namespace="Unit">
                    <xsl:for-each select=".">
                        <xsl:apply-templates select="node()[not(name()='')]"/>
                    </xsl:for-each>
                    <xsl:if test="xi:include/@href != ''">
                        <xsl:for-each select="xi:include">
                            <xsl:element name="xi:include">
                                <xsl:attribute name="href">
                                    <xsl:value-of select="./@href"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:for-each>
                    </xsl:if>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:section.body" name="sectionbody">
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
    
    <xsl:template match="bk:subsection" name="subsection">
     
        <xsl:element name="unit" namespace="Unit">
            <xsl:if test="./@id != ''">
                <xsl:attribute name="unitid">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:attribute name="standalone">
                <xsl:value-of>false</xsl:value-of>
            </xsl:attribute>
            
            <xsl:if test="bk:headers">
                <xsl:apply-templates select="bk:headers"/>
            </xsl:if>
            
            <xsl:if test="bk:subsection.body">
                <xsl:apply-templates select="bk:subsection.body"/>
            </xsl:if>
            
            <xsl:if test="node()[name()='studymaterials']">
                
                <xsl:apply-templates select="bk:studymaterials"/>
                
            </xsl:if>
        </xsl:element>
       
    </xsl:template>
    
    <xsl:template match="bk:subsection.body">       
        <xsl:element name="body" namespace="Unit">
            <xsl:element name="block" namespace="Unit">
                <xsl:element name="block.body" namespace="Unit">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:def">
        <xsl:element name="def" namespace="Unit">
            <xsl:if test="./@type != ''">
                <xsl:attribute name="type">
                    <xsl:value-of select="./@type"/>
                </xsl:attribute>
            </xsl:if>
            
            <xsl:if test="./@id != ''">
                <xsl:attribute name="id">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            
            <xsl:for-each select=".">
                <xsl:if test="node()[name()='caption']">
                    <xsl:element name="caption" namespace="Unit">
                        <xsl:apply-templates select="child::node()[name()='caption']"/>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="node()[name()='associate']">
                    <xsl:apply-templates select="bk:associate"/>
                </xsl:if>
                <xsl:element name="def.body" namespace="Unit">
                    <xsl:apply-templates select="node()[not(name()='caption' or name()='associate')]"/>
                </xsl:element>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:comment">
        <xsl:element name="comment" namespace="Unit">
            <xsl:if test="./@type != ''">
                <xsl:attribute name="type">
                    <xsl:value-of select="./@type"/>
                </xsl:attribute>
            </xsl:if>
            
            <xsl:if test="./@id != ''">
                <xsl:attribute name="id">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            
            <xsl:for-each select=".">
                <xsl:if test="node()[name()='caption']">
                    <xsl:element name="caption" namespace="Unit">
                        <xsl:apply-templates select="child::node()[name()='caption']"/>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="node()[name()='associate']">
                    <xsl:apply-templates select="bk:associate"/>
                </xsl:if>
                <xsl:element name="comment.body" namespace="Unit">
                    <xsl:apply-templates select="node()[not(name()='caption' or name()='associate')]"/>
                </xsl:element>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:associate">
        <xsl:element name="associate" namespace="Unit">
            <xsl:attribute name="type">
                <xsl:value-of select="./@Description"/>
            </xsl:attribute>
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
                    <xsl:element name="comment.ref">
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
                <xsl:if test="child::node()[name() = 'info']">
                    <xsl:apply-templates select="bk:info"/>
                </xsl:if>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
</xsl:stylesheet>