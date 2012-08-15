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
                xmlns:xi="http://www.w3.org/2001/XInclude"
                xmlns:thm="http://webmath.math.ualberta.ca/v1/Theorem"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:theorem="Theorem"
                version="2.0">

    <xsl:output method="xml" indent="yes" version="1.0"
                encoding="UTF-8"/>
    
    <xsl:strip-space elements="*"/>
    
    <xsl:template match="thm:theorem" name="theorem">
        <xsl:element name="theorem" namespace="Theorem">            
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
            <xsl:if test="./@xsi:schemaLocation">
                <xsl:attribute name="xsi:schemaLocation">Theorem file:/C:/xampp/htdocs/moodle/mod/msm/NewSchemas/Theorem.xsd</xsl:attribute>
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
    
    <!--<xsl:template match="thm:caption">
        <xsl:choose>
            <xsl:when test="parent::node()[name()='proof']">
                <xsl:apply-templates select="child::node()[name()= 'partref']"/>
                <xsl:element name="caption" namespace="Theorem">
                    <xsl:value-of select="child::node()[name()='partref']"/>
                </xsl:element>               
            </xsl:when>
            <xsl:when test="parent::node()[name()='info']">                
                <xsl:apply-templates/>                      
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="caption" namespace="Theorem">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>     
    </xsl:template>-->
    
    <xsl:template match="thm:caption">
        <xsl:apply-templates select="child::node()[name()='partref']"/>
        <xsl:element name="caption" namespace="Theorem">
            <xsl:apply-templates select="child::node()[not(name()='partref')]"/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:partref">
        <xsl:element name="logic" namespace="Theorem">
            <xsl:element name="part.ref" namespace="Theorem">
                <xsl:value-of select="."></xsl:value-of>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:tablabel">
        <xsl:element name="textcaption" namespace="Theorem">
            <xsl:value-of select="thm:tablable"/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:statement">
        <xsl:element name="statement.theorem" namespace="Theorem">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:part">
        <xsl:element name="part.theorem" namespace="Theorem">
            <xsl:if test="attribute::* != ''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:if test="child::node()[name()='caption']">               
                <xsl:apply-templates select="child::node()[name()='caption']"/>                
            </xsl:if>
            <xsl:element name="part.body" namespace="Theorem">
                <xsl:apply-templates select="child::node()[not(name()='caption')]"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    <xsl:template match="thm:proof">
        <xsl:element name="proof" namespace="Theorem">
            <xsl:if test="attribute::* != ''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            
            <xsl:element name="proof.block" namespace="Theorem">
               <xsl:for-each select=".">
                   <xsl:choose>
                       <xsl:when test="node()[name()='caption']">
                           <xsl:apply-templates/>
                       </xsl:when>
                       <xsl:otherwise>
                           <xsl:element name="proof.block.body" namespace="Theorem">
                               <xsl:apply-templates/>
                           </xsl:element>
                       </xsl:otherwise>
                   </xsl:choose>
               </xsl:for-each>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <!--<xsl:template match="thm:proof">
        <xsl:element name="proof" namespace="Theorem">
            <xsl:if test="attribute::* != ''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if> 
            <xsl:choose>
                <xsl:when test="count(child::node()[name()='caption']) = 0">
                    <xsl:element name="proof.block" namespace="Theorem">
                        <xsl:element name="proof.block.body" namespace="Theorem">
                            <xsl:apply-templates/>
                        </xsl:element>
                    </xsl:element>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:for-each select="child::node()[name()='caption']">
                        <xsl:element name="proof.block" namespace="Theorem">
                            <xsl:apply-templates select="."/>
                            <xsl:element name="proof.block.body" namespace="Theorem">
                                <xsl:apply-templates select="following-sibling::node()[position()=1]"/>
                            </xsl:element>
                        </xsl:element>
                    </xsl:for-each> 
                </xsl:otherwise>
            </xsl:choose>                       
        </xsl:element>
    </xsl:template>-->
    
    <!--xsl:template match="thm:proof.ext">
        <xsl:element name="proof.ext" namespace="Theorem">
            <xsl:if test="./@version != ''">
                <xsl:attribute name="version">
                    <xsl:value-of select="./@version"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template-->
    
    <xsl:template match="thm:step">
        <xsl:choose>
            <xsl:when test="parent::node()[name()='pilot']">
                <xsl:element name="pilot.step" namespace="Theorem">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="step" namespace="Theorem">
                    <xsl:choose>
                        <xsl:when test="./@partref != ''">
                            <xsl:attribute name="partref">
                                <xsl:value-of select="./@partref"/>
                            </xsl:attribute>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:attribute name="partref">1</xsl:attribute>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:for-each select=".">
                        <xsl:apply-templates select="following-sibling::node()[name()='caption'][position()=1]"/>
                        <xsl:apply-templates select="following-sibling::node()[name()='pilot'][position()=1]"/>
                        <xsl:element name="step.body" namespace="Theorem">
                            <xsl:apply-templates select="following-sibling::node()[position()=1]"/>
                        </xsl:element>                
                    </xsl:for-each>     
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>       
    </xsl:template>
    
    <xsl:template match="thm:pilot">
        <xsl:element name="pilot" namespace="Theorem">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:associate">
        <xsl:element name="associate" namespace="Theorem">
            <xsl:if test="./@Description != ''">
                <xsl:attribute name="type">
                    <xsl:value-of select="./@Description"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:comment.ref">
        <xsl:element name="comment.ref" namespace="Theorem">
            <xsl:attribute name="commentID">
                <xsl:value-of select="./@commentID"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:showme.pack.ref">
        <xsl:element name="showme.pack.ref" namespace="Theorem">
            <xsl:attribute name="showmePackID">
                <xsl:value-of select="./@showmePackID"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:quiz.pack.ref">
        <xsl:element name="quiz.pack.ref" namespace="Theorem">
            <xsl:attribute name="quizPackID">
                <xsl:value-of select="./@quizPackID"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:definition.ref">
        <xsl:element name="definition.ref" namespace="Theorem">
            <xsl:attribute name="definitionID">
                <xsl:value-of select="./@definitionID"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:theorem.ref">
        <xsl:element name="theorem.ref" namespace="Theorem">
            <xsl:attribute name="theoremID">
                <xsl:value-of select="./@theoremID"/>
            </xsl:attribute>
            
            <xsl:if test="./@theorempartID != ''">
                <xsl:attribute name="theorempartID">
                    <xsl:value-of select="./@theorempartID"/>
                </xsl:attribute>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:info">
        <xsl:element name="info" namespace="Theorem">
            <xsl:if test="thm:caption != ''">
                <xsl:element name="info.caption" namespace="Theorem">
                    <xsl:apply-templates select="thm:caption"/>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="node()[not(name()='caption')]"/>            
        </xsl:element>
    </xsl:template>

    <xsl:template match="thm:math.display">
        <xsl:element name="math.display" namespace="Theorem">
            <xsl:if test="./@id != ''">
                <xsl:attribute name="id">
                    <xsl:value-of select="./@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:para">
        <xsl:element name="para" namespace="Theorem">
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
                <xsl:element name="para.body" namespace="Theorem">
                    <xsl:for-each select=".">
                        <xsl:if test="node()[name()='index.glossary']">
                            <xsl:value-of select="thm:term"/>
                        </xsl:if>
                        <xsl:if test="node()[name()='index.symbol']">
                            <xsl:value-of select="thm:symbol"/>
                        </xsl:if>
                        <xsl:apply-templates select="node()[not(name() = 'index.glossary' or name() = 'index.symbol' or name() = 'index.author')]"/>
                    </xsl:for-each>
                </xsl:element>
                <xsl:apply-templates select="thm:index.author"/>
                <xsl:apply-templates select="thm:index.symbol"/>
                <xsl:apply-templates select="thm:index.glossary"/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:index.author">
        <xsl:element name="index.author" namespace="Theorem">
            <xsl:for-each select=".">
                <xsl:element name="name" namespace="Theorem">
                    <xsl:apply-templates select="thm:name"/>
                </xsl:element>
            </xsl:for-each>
            <xsl:apply-templates select="thm:info"/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:index.symbol">
        <xsl:element name="index.symbol" namespace="Theorem">
            <xsl:for-each select=".">
                <xsl:apply-templates select="thm:symbol"/>
            </xsl:for-each>
            <xsl:apply-templates select="thm:info"/>
            <xsl:if test="thm:sortstring != ''">
                <xsl:element name="sortstring" namespace="Theorem">
                    <xsl:apply-templates select="thm:sortstring"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:index.glossary">
        <xsl:element name="index.glossary" namespace="Theorem">
            <xsl:for-each select=".">
                <xsl:apply-templates select="thm:term"/>
            </xsl:for-each>
            <xsl:if test="thm:info != ''">
                <xsl:apply-templates select="thm:info"/>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:symbol">
        <xsl:element name="symbol" namespace="Theorem">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:term">
        <xsl:element name="term" namespace="Theorem">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
        
    <xsl:template match="thm:name">
        <xsl:element name="name" namespace="Theorem">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:first">
        <xsl:element name="first" namespace="Theorem">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="thm:middle">
        <xsl:element name="middle" namespace="Theorem">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="thm:last">
        <xsl:element name="last" namespace="Theorem">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="thm:initials">
        <xsl:element name="initials" namespace="Theorem">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:ol">
        <xsl:element name="ol" namespace="Theorem">
            <xsl:if test="./@type != ''">
                <xsl:attribute name="type">
                    <xsl:value-of select="./@type"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:ul">
        <xsl:element name="ul" namespace="Theorem">
            <xsl:if test="./@bullet != ''">
                <xsl:attribute name="bullet">
                    <xsl:value-of select="./@bullet"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:li">
        <xsl:element name="li" namespace="Theorem">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:b">
        <xsl:element name="strong" namespace="Theorem">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="thm:i">
        <xsl:element name="emphasis" namespace="Theorem">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:table">        
        <xsl:element name="table" namespace="Theorem">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>          
    </xsl:template>
    
    <xsl:template match="thm:row">
        <xsl:element name="row" namespace="Theorem">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:cell">
        <xsl:element name="cell" namespace="Theorem">
            <xsl:for-each select=".">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:latex">
        <xsl:choose>
            <xsl:when test="parent::node()[name()='math.display']">
                <xsl:element name="latex" namespace="Theorem">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="math" namespace="Theorem">
                    <xsl:element name="latex" namespace="Theorem">
                        <xsl:apply-templates/>
                    </xsl:element>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>       
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
    
    <xsl:template match="thm:computation">        
        <xsl:element name="math.array" namespace="Theorem">
            <xsl:attribute name="column">3</xsl:attribute>
            <xsl:for-each select="child::node()[name()='left']">
                <xsl:element name="row" namespace="Theorem">
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
    
    <xsl:template match="thm:left">
        <xsl:element name="cell" namespace="Theorem">
            <xsl:attribute name="colspan">2</xsl:attribute>
            <xsl:attribute name="halign">center</xsl:attribute>
            <xsl:attribute name="valign">middle</xsl:attribute>
          
            <xsl:if test="child::node()[name()=''] != ''">
                <xsl:element name="math" namespace="Theorem">
                    <xsl:element name="latex" namespace="Theorem">
                        <xsl:apply-templates select="child::node()[name() = '']"/>
                    </xsl:element>
                </xsl:element>
            </xsl:if>
          
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Theorem">
                    <xsl:apply-templates select="thm:info"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="child::node()[name()='companion']">
                <xsl:apply-templates select="child::node()"/>
            </xsl:if>
            <xsl:if test="child::node()[name()='crossref']">
                <xsl:element name="companion" namespace="Theorem">
                    <xsl:for-each select=".">
                      
                        <xsl:if test="child::node()[name() = 'comment.ref']">
                            <xsl:element name="comment.ref" namespace="Theorem">
                                <xsl:attribute name="commentID">
                                    <xsl:value-of select="thm:comment.ref/@commentID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                      
                        <xsl:if test="child::node()[name() = 'definition.ref']">
                            <xsl:element name="definition.ref" namespace="Theorem">
                                <xsl:attribute name="definitionID">
                                    <xsl:value-of select="thm:definition.ref/@definitionID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                      
                        <xsl:if test="child::node()[name() = 'theorem.ref']">
                            <xsl:element name="theorem.ref" namespace="Theorem">
                                <xsl:attribute name="theoremID">
                                    <xsl:value-of select="thm:theorem.ref/@theoremID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                      
                        <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                            <xsl:element name="unit.ref" namespace="Theorem">
                                <xsl:attribute name="unitId">
                                    <xsl:choose>
                                        <xsl:when test="child::node()[name() = 'subpage.ref']">
                                            <xsl:value-of select="thm:subpage.ref/@subpageID"/>
                                        </xsl:when>
                                        <xsl:when test="child::node()[name() = 'chapter.ref']">
                                            <xsl:value-of select="thm:chapter.ref/@chapterID"/>
                                        </xsl:when>
                                        <xsl:when test="child::node()[name() = 'subsection.ref']">
                                            <xsl:value-of select="thm:subsection.ref/@subsectionID"/>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:value-of select="thm:section.ref/@sectionID"/>
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
    
    <xsl:template match="thm:center">
        <xsl:element name="cell" namespace="Theorem">
            <xsl:attribute name="colspan">
                <xsl:value-of select="1"/>
            </xsl:attribute>
            <xsl:attribute name="halign">
                <xsl:text>center</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="valign">
                <xsl:text>middle</xsl:text>
            </xsl:attribute>
            
            <xsl:element name="math" namespace="Theorem">
                <xsl:element name="latex" namespace="Theorem">
                    <xsl:apply-templates select="child::node()[name()='']"/>
                </xsl:element>               
            </xsl:element>
            
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Theorem">
                    <xsl:apply-templates select="thm:info"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="child::node()[name()='companion']">
                <xsl:apply-templates select="child::node()"/>
            </xsl:if>
            <xsl:if test="child::node()[name()='crossref']">
                <xsl:element name="companion" namespace="Theorem">
                    <xsl:for-each select=".">
                        
                        <xsl:if test="child::node()[name() = 'comment.ref']">
                            <xsl:element name="comment.ref" namespace="Theorem">
                                <xsl:attribute name="commentID">
                                    <xsl:value-of select="thm:comment.ref/@commentID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'definition.ref']">
                            <xsl:element name="definition.ref" namespace="Theorem">
                                <xsl:attribute name="definitionID">
                                    <xsl:value-of select="thm:definition.ref/@definitionID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'theorem.ref']">
                            <xsl:element name="theorem.ref" namespace="Theorem">
                                <xsl:attribute name="theoremID">
                                    <xsl:value-of select="thm:theorem.ref/@theoremID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                            <xsl:element name="unit.ref" namespace="Theorem">
                                <xsl:attribute name="unitId">
                                    <xsl:choose>
                                        <xsl:when test="child::node()[name() = 'subpage.ref']">
                                            <xsl:value-of select="thm:subpage.ref/@subpageID"/>
                                        </xsl:when>
                                        <xsl:when test="child::node()[name() = 'chapter.ref']">
                                            <xsl:value-of select="thm:chapter.ref/@chapterID"/>
                                        </xsl:when>
                                        <xsl:when test="child::node()[name() = 'subsection.ref']">
                                            <xsl:value-of select="thm:subsection.ref/@subsectionID"/>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:value-of select="thm:section.ref/@sectionID"/>
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
    
    <xsl:template match="thm:right">
        <xsl:element name="cell" namespace="Theorem">
            <xsl:attribute name="colspan">2</xsl:attribute>
            <xsl:attribute name="halign">center</xsl:attribute>
            <xsl:attribute name="valign">middle</xsl:attribute>
            
            <xsl:if test="child::node()[name()=''] != ''">
                <xsl:element name="math" namespace="Theorem">
                    <xsl:element name="latex" namespace="Theorem">
                        <xsl:apply-templates select="child::node()[name() = '']"/>
                    </xsl:element>
                </xsl:element>
            </xsl:if>
            
            <xsl:if test="child::node()[name()='info']">
                <xsl:element name="companion" namespace="Theorem">
                    <xsl:apply-templates select="thm:info"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="child::node()[name()='companion']">
                <xsl:apply-templates select="child::node()"/>
            </xsl:if>
            <xsl:if test="child::node()[name()='crossref']">
                <xsl:element name="companion" namespace="Theorem">
                    <xsl:for-each select=".">
                        
                        <xsl:if test="child::node()[name() = 'comment.ref']">
                            <xsl:element name="comment.ref" namespace="Theorem">
                                <xsl:attribute name="commentID">
                                    <xsl:value-of select="thm:comment.ref/@commentID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'definition.ref']">
                            <xsl:element name="definition.ref" namespace="Theorem">
                                <xsl:attribute name="definitionID">
                                    <xsl:value-of select="thm:definition.ref/@definitionID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'theorem.ref']">
                            <xsl:element name="theorem.ref" namespace="Theorem">
                                <xsl:attribute name="theoremID">
                                    <xsl:value-of select="thm:theorem.ref/@theoremID"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:if>
                        
                        <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                            <xsl:element name="unit.ref" namespace="Theorem">
                                <xsl:attribute name="unitId">
                                    <xsl:choose>
                                        <xsl:when test="child::node()[name() = 'subpage.ref']">
                                            <xsl:value-of select="thm:subpage.ref/@subpageID"/>
                                        </xsl:when>
                                        <xsl:when test="child::node()[name() = 'chapter.ref']">
                                            <xsl:value-of select="thm:chapter.ref/@chapterID"/>
                                        </xsl:when>
                                        <xsl:when test="child::node()[name() = 'subsection.ref']">
                                            <xsl:value-of select="thm:subsection.ref/@subsectionID"/>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:value-of select="thm:section.ref/@sectionID"/>
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
    
    <xsl:template match="thm:figure">
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
            
            <xsl:apply-templates select="thm:img"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:img">
        <xsl:choose>     
            <xsl:when test="parent::node()[name()='figure']">
                <xsl:element name="img" namespace="Theorem">
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
                                                        <xsl:value-of select="thm:img/@width"/>
                                                        <xsl:text>&#44;</xsl:text>
                                                        <xsl:value-of select="thm:img/@height"/>
                                                    </xsl:if>                                        
                                                </xsl:when>
                                                
                                                <xsl:otherwise>
                                                    <xsl:text>0&#44;0&#44;200&#44;100</xsl:text>
                                                </xsl:otherwise> 
                                                
                                            </xsl:choose>                                    
                                        </xsl:attribute>
                                    </xsl:element>
                                </xsl:element>
                                
                                <xsl:apply-templates select="thm:info"/>
                            </xsl:if>
                            <xsl:if test="child::node()[name()='caption']">
                                <xsl:apply-templates select="child::node()[name()='caption']"/>
                            </xsl:if>
                        </xsl:element>
                    </xsl:if>
                </xsl:element>         
            </xsl:when>
            <xsl:when test="parent::node()[name()='hot']">
                <xsl:element name="media" namespace="Theorem">
                    <xsl:attribute name="type">image</xsl:attribute>
                    <xsl:attribute name="active">1</xsl:attribute>
                    <xsl:attribute name="inline">0</xsl:attribute>
                    <xsl:element name="img" namespace="Theorem">
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
                <xsl:element name="media" namespace="Theorem">
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
    
    <xsl:template match="thm:image">
        <xsl:element name="media" namespace="Theorem">
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
            <xsl:element name="img" namespace="Theorem">
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
                    <xsl:element name="image.mapping" namespace="Theorem">
                        <xsl:apply-templates select="thm:area"/>
                    </xsl:element>
                </xsl:if>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:area">
        <xsl:element name="area" namespace="Theorem">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
       
        <xsl:for-each select=".">
            <xsl:apply-templates/>
        </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:crossref">
        <xsl:if test="child::node()[name() = 'external.ref']">
            <xsl:apply-templates select ="thm:external.ref"/>
        </xsl:if>
        <xsl:element name="crossref" namespace="Theorem">
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'exercise.pack.ref']">
                    <xsl:element name="exercise.pack.ref" namespace="Theorem">
                        <xsl:attribute name="exercisePackID">
                            <xsl:value-of select="thm:exercise.pack.ref/@exercisePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'example.pack.ref']">
                    <xsl:element name="example.pack.ref" namespace="Theorem">
                        <xsl:attribute name="examplePackID">
                            <xsl:value-of select="thm:example.pack.ref/@examplePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Theorem">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="thm:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Theorem">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="thm:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Theorem">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="thm:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                    <xsl:element name="unit.ref" namespace="Theorem">
                        <xsl:attribute name="unitId">
                            <xsl:choose>
                                <xsl:when test="child::node()[name() = 'subpage.ref']">
                                    <xsl:value-of select="thm:subpage.ref/@subpageID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'chapter.ref']">
                                    <xsl:value-of select="thm:chapter.ref/@chapterID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'subsection.ref']">
                                    <xsl:value-of select="thm:subsection.ref/@subsectionID"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="thm:section.ref/@sectionID"/>
                                </xsl:otherwise>
                                
                            </xsl:choose>
                            
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
            
            <xsl:if test="child::node() != ''">
                <xsl:element name="info" namespace="Theorem">
                    <xsl:if test="child::node()[name() = 'caption']">
                        <xsl:element name="info.caption" namespace="Theorem">
                            <xsl:apply-templates select="thm:caption"/>
                        </xsl:element>
                    </xsl:if>
                    <xsl:apply-templates select="child::node()[not(name()='caption' or name()='subpage.ref' or name()='chapter.ref' or name()='subsection.ref' or name()='section.ref' or name()='theorem.ref' or name()='comment.ref' or name()='definition.ref' or name()='exercise.pack.ref' or name()='example.pack.ref' or name()='external.ref')]"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:external.ref">
        <xsl:element name="external.ref" namespace="Theorem">
            <xsl:if test="./@bookID">
                <xsl:element name="compositionID" namespace="Theorem">
                    <xsl:value-of select="./@bookID"/>
                </xsl:element>
            </xsl:if>
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'exercise.pack.ref']">
                    <xsl:element name="exercise.pack.ref" namespace="Theorem">
                        <xsl:attribute name="exercisePackID">
                            <xsl:value-of select="thm:exercise.pack.ref/@exercisePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'example.pack.ref']">
                    <xsl:element name="example.pack.ref" namespace="Theorem">
                        <xsl:attribute name="examplePackID">
                            <xsl:value-of select="thm:example.pack.ref/@examplePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Theorem">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="thm:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Theorem">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="thm:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Theorem">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="thm:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref' or name() = 'chapter.ref' or name() = 'subsection.ref'] or name() = 'section.ref'">
                    <xsl:element name="unit.ref" namespace="Theorem">
                        <xsl:attribute name="unitId">
                            <xsl:choose>
                                <xsl:when test="child::node()[name() = 'subpage.ref']">
                                    <xsl:value-of select="thm:subpage.ref/@subpageID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'chapter.ref']">
                                    <xsl:value-of select="thm:chapter.ref/@chapterID"/>
                                </xsl:when>
                                <xsl:when test="child::node()[name() = 'subsection.ref']">
                                    <xsl:value-of select="thm:subsection.ref/@subsectionID"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="thm:section.ref/@sectionID"/>
                                </xsl:otherwise>
                                
                            </xsl:choose>
                            
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:cite">
        <xsl:if test="./@label != ''">
            <xsl:attribute name="label" namespace="Theorem">
                <xsl:value-of select="./@label"/>
            </xsl:attribute>
        </xsl:if>
        
        <xsl:element name="cite" namespace="Theorem">
            <xsl:if test="child::node()[name() = 'caption']">
                <xsl:element name="caption" namespace="Theorem">
                    <xsl:apply-templates select="thm:caption"/>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="thm:item"/>
            
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:link">
        <xsl:element name="external.link" namespace="Theorem">
            <xsl:if test="attribute::* !=''">
                <xsl:copy-of select="attribute::*"/>
            </xsl:if>
            <xsl:if test="child::node() != ''">
                <xsl:element name="info" namespace="Theorem">
                    <xsl:if test="child::node()[name() = 'caption']">
                        <xsl:element name="info.caption" namespace="Theorem">
                            <xsl:apply-templates select="thm:caption"/>
                        </xsl:element>
                    </xsl:if>
                    <xsl:apply-templates select="child::node()[not(name()='caption')]"/>
                </xsl:element>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:item">
        <xsl:element name="item" namespace="Theorem">
            <xsl:if test="child::node()[not(name() = 'citekey')]">
                <xsl:apply-templates select="child::node()[not(name() = 'citekey')]"/>
            </xsl:if>
            <xsl:if test="thm:citekey != ''">
                <xsl:apply-templates select="thm:citekey"/>
            </xsl:if>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:citekey">
        <xsl:element name="citekey" namespace="Theorem">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:companion">
        <xsl:element name="companion" namespace="Theorem">
            <xsl:for-each select=".">
                <xsl:if test="child::node()[name() = 'showme.pack.ref']">
                    <xsl:element name="showme.pack.ref" namespace="Theorem">
                        <xsl:attribute name="showmePackID">
                            <xsl:value-of select="thm:showme.pack.ref/@showmePackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'quiz.pack.ref']">
                    <xsl:element name="quiz.pack.ref" namespace="Theorem">
                        <xsl:attribute name="quizPackID">
                            <xsl:value-of select="thm:quiz.pack.ref/@quizPackID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'theorem.ref']">
                    <xsl:element name="theorem.ref" namespace="Theorem">
                        <xsl:attribute name="theoremID">
                            <xsl:value-of select="thm:theorem.ref/@theoremID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'definition.ref']">
                    <xsl:element name="definition.ref" namespace="Theorem">
                        <xsl:attribute name="definitionID">
                            <xsl:value-of select="thm:definition.ref/@definitionID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'comment.ref']">
                    <xsl:element name="comment.ref" namespace="Theorem">
                        <xsl:attribute name="commentID">
                            <xsl:value-of select="thm:comment.ref/@commentID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[name() = 'subpage.ref']">
                    <xsl:element name="unit.ref" namespace="Theorem">
                        <xsl:attribute name="unitId">
                            <xsl:value-of select="thm:subpage.ref/@subpageID"/>
                        </xsl:attribute>
                    </xsl:element>
                </xsl:if>
                <xsl:if test="child::node()[not(name()='subpage.ref' or name()='definition.ref' or name()='comment.ref' or name()='theorem.ref' or name()='quiz.pack.ref' or name()='showme.pack.ref')]">
                    <xsl:element name="info" namespace="Theorem">
                        <xsl:if test="child::node()[name() = 'caption']">
                            <xsl:element name="info.caption" namespace="Theorem">
                                <xsl:apply-templates select="thm:caption"/>
                            </xsl:element>
                        </xsl:if>
                        <xsl:apply-templates select="node()[not(name()='caption' or name()='subpage.ref' or name()='definition.ref' or name()='comment.ref' or name()='theorem.ref' or name()='quiz.pack.ref' or name()='showme.pack.ref')]"/>
                    </xsl:element>
                </xsl:if>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="thm:subordinate">
        <xsl:choose>
            <xsl:when test="child::node()[child::node()[name()='img' or name()='image']]">
                <xsl:apply-templates select="child::node()[child::node()[name()='img' or name()='image']]"/>
                <xsl:if test="thm:info">
                    <xsl:apply-templates select="node()[name()='info']"/>
                </xsl:if>
                <xsl:if test="thm:companion">
                    <xsl:apply-templates select="node()[name()='companion']"/>
                </xsl:if>
                <xsl:if test="thm:crossref">
                    <xsl:apply-templates select="node()[name()='crossref']"/>
                </xsl:if>
                <xsl:if test="thm:cite">
                    <xsl:apply-templates select="node()[name()='cite']"/>
                </xsl:if>
                <xsl:if test="thm:link">
                    <xsl:apply-templates select="node()[name()='link']"/>
                </xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="subordinate" namespace="Theorem">
                    <xsl:apply-templates select="node()[name()='hot']"/>
                    <xsl:if test="thm:info">
                        <xsl:apply-templates select="node()[name()='info']"/>
                    </xsl:if>
                    <xsl:if test="thm:companion">
                        <xsl:apply-templates select="node()[name()='companion']"/>
                    </xsl:if>
                    <xsl:if test="thm:crossref">
                        <xsl:apply-templates select="node()[name()='crossref']"/>
                    </xsl:if>
                    <xsl:if test="thm:cite">
                        <xsl:apply-templates select="node()[name()='cite']"/>
                    </xsl:if>
                    <xsl:if test="thm:link">
                        <xsl:apply-templates select="node()[name()='link']"/>
                    </xsl:if>
                        <!-- presentation may be the same processing as link, left unprocessed for now-->
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>      
    </xsl:template>
    
    <xsl:template match="thm:hot">
        <xsl:choose>
            <xsl:when test="child::node()[name()='img' or name()='image']">
                <xsl:apply-templates select="child::node()[name()='img' or name()='image']"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:element name="hot" namespace="Theorem">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:otherwise>
        </xsl:choose>        
    </xsl:template>
      
</xsl:stylesheet>