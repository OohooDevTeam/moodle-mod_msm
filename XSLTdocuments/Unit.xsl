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
                xmlns:bk="http://webmath.math.ualberta.ca/v1/Book"
                xmlns:xi="http://www.w3.org/2001/XInclude"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:unit="Unit"
                version="2.0">
    
    <xsl:import href="Intro.xsl"/>
   
    <xsl:output method="xml" indent="yes" version="1.0"
                encoding="UTF-8"/>
    
    <!-- processes book/bookpart and chapter-->
    <xsl:template match="/bk:book">
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
            <xsl:attribute name="standalone">
                <xsl:value-of>false</xsl:value-of>
            </xsl:attribute>
      
            <xsl:if test="node()[name()='texsupport' or name()='literature.db']">
                <xsl:element name="headers" namespace="Unit">
                    <xsl:apply-templates select="node()[name()='texsupport' or name()='literature.db']"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="node()[name()='title' or name()='html.title']">
                <xsl:element name='titles' namespace="Unit">
                    <xsl:apply-templates select="node()[name()='title']"/>
                    <xsl:element name="plain.title" namespace="Unit">
                        <xsl:value-of select="bk:html.title"/>
                    </xsl:element>
                </xsl:element>
            </xsl:if>
            <xsl:apply-templates select="node()[not(name()='texsupport' and name()='literature.db' and name()='title' and name()='html.title')]"/>
        </xsl:element>
    </xsl:template>
 
    <xsl:template match="bk:studymaterials">
        <xsl:element name="studymaterials">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    <xsl:template match="bk:exercise.pack.ref">
        <xsl:element name="exercise.pack.ref">
            <xsl:if test="./@exercisePackID != ''">
                <xsl:attribute name="exercisePackID">
                    <xsl:value-of select="./@exercisePackID"/>
                </xsl:attribute>
            </xsl:if>
        </xsl:element>      
    </xsl:template>
    <xsl:template match="bk:example.pack.ref">
        <xsl:element name="example.pack.ref">
            <xsl:if test="./@examplePackID != ''">
                <xsl:attribute name="examplePackID">
                    <xsl:value-of select="./@examplePackID"/>
                </xsl:attribute>
            </xsl:if>
        </xsl:element>      
    </xsl:template>
    <xsl:template match="bk:texsupport">
        <xsl:element name="texsupport" namespace="Unit">
            <xsl:attribute name="href">
                <xsl:value-of select="./@href"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:literature.db">
        <xsl:element name="literature.db" namespace="Unit">
            <xsl:attribute name="href">
                <xsl:value-of select="./@href"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>
  
    <xsl:template match="bk:acknowledgements">
        <xsl:element name="acknowledgements" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:preface">
        <xsl:element name="preface" namespace="Unit">
            <xsl:element name="block" namespace="Unit">
                <xsl:element name="block.body" namespace="Unit">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:title">
        <xsl:element name="title" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:headers">
        <xsl:element name="titles" namespace="Unit">
            <xsl:element name="title" namespace="Unit">
                <xsl:apply-templates select="bk:full"/>
            </xsl:element>
            
            <xsl:element name="plain.title" namespace="Unit">
                <xsl:value-of select="bk:short"/>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:cover">
    </xsl:template>
    
    <xsl:template match="bk:full">
        <xsl:apply-templates/>
    </xsl:template>
    
    <xsl:template match="bk:authors">
        <xsl:element name="authors" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:contributors">
        <xsl:element name="contributors" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:person">
        <xsl:element name="person" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:contactdata">
        <xsl:element name="contactdata" namespace="Unit">
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:e-mail">
        <xsl:element name="e-mail" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:webpage">
        <xsl:element name="webpage" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:phone">
        <xsl:element name="phone" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:address">
        <xsl:element name="address" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:date">
        <xsl:element name="dates" namespace="Unit">
            <xsl:element name="creation" namespace="Unit">
                <xsl:element name="date" namespace="Unit">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:element>
            <xsl:element name="last.revision" namespace="Unit">
                <xsl:element name="date" namespace="Unit">
                    <xsl:apply-templates/>
                </xsl:element>
            </xsl:element>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:month">
        <xsl:element name="month" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:day">
        <xsl:element name="day" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>    
    
    <xsl:template match="bk:year">
        <xsl:element name="year" namespace="Unit">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="bk:body">
        <xsl:element name="legitimate.children" namespace="Unit">
            <xsl:choose>
                <xsl:when test="count(bk:book.part) = 0">
                    <xsl:call-template name="forloop2">
                        <xsl:with-param name="i">1</xsl:with-param>
                        <xsl:with-param name="count">
                            <xsl:value-of select="count(bk:chapter)"/>
                        </xsl:with-param>
                    </xsl:call-template>    
                </xsl:when>
                <xsl:otherwise>
                    <xsl:call-template name="forloop">
                        <xsl:with-param name="i">1</xsl:with-param>
                        <xsl:with-param name="count">
                            <xsl:value-of select="count(bk:book.part)"/>
                        </xsl:with-param>
                    </xsl:call-template>    
                </xsl:otherwise>
            </xsl:choose>
           
                   
        </xsl:element>
    </xsl:template>
    
    <xsl:template name="forloop">
        <xsl:param name="i"/>
        <xsl:param name="count"/>
        
        <xsl:if test="$i &lt;= $count">
            <xsl:element name="unit.choice" namespace="Unit">
                <xsl:attribute name="unitId">
                    <xsl:value-of select="bk:book.part[position()=$i]/@id"/>
                </xsl:attribute>
            </xsl:element>
            <xsl:variable name="filename" select="concat(bk:book.part[position()=$i]/@id,'.xml')"/>
            
            <xsl:result-document href="{$filename}" method="xml">
                <xsl:element name="unit" namespace="Unit">
                    <xsl:attribute name="xsi:schemaLocation">Unit file:/C:/xampp/htdocs/moodle/mod/msm/NewSchemas/Unit.xsd</xsl:attribute>
                    <xsl:attribute name="unitid">
                        <xsl:value-of select="bk:book.part[position()=$i]/@id"/>
                    </xsl:attribute>
                    <xsl:attribute name="standalone">
                        <xsl:value-of>false</xsl:value-of>
                    </xsl:attribute>
               
                    <xsl:apply-templates select="bk:book.part[position()=$i]/bk:headers"/>
               
                    <xsl:if test="bk:book.part[position()=$i]/bk:chapter != ''">
                        <xsl:element name="legitimate.children" namespace="Unit">
                            <xsl:apply-templates select="bk:book.part[position()=$i]/node()[not(name()='headers')]"/>
                        </xsl:element>
                    </xsl:if>  
                </xsl:element>
            </xsl:result-document>
        </xsl:if>
        
        <xsl:if test="$i &lt;= $count"> 
            <xsl:call-template name="forloop"> 
                <xsl:with-param name="i"> 
                    <xsl:value-of select="$i + 1"/> 
                </xsl:with-param> 
                <xsl:with-param name="count"> 
                    <xsl:value-of select="$count"/> 
                </xsl:with-param> 
            </xsl:call-template> 
        </xsl:if>
    </xsl:template>
    
    <xsl:template name="forloop2">
        <xsl:param name="i"/>
        <xsl:param name="count"/>
        
        <xsl:if test="$i &lt;= $count">
            <xsl:element name="unit.choice" namespace="Unit">
                <xsl:attribute name="unitId">
                    <xsl:value-of select="bk:chapter[position()=$i]/@id"/>
                </xsl:attribute>
            </xsl:element>
            <xsl:variable name="filename" select="concat(bk:chapter[position()=$i]/@id,'.xml')"/>
            
            <xsl:result-document href="{$filename}" method="xml">
                <xsl:element name="unit" namespace="Unit">
                    <xsl:attribute name="xsi:schemaLocation">Unit file:/C:/xampp/htdocs/moodle/mod/msm/NewSchemas/Unit.xsd</xsl:attribute>
                    <xsl:attribute name="unitid">
                        <xsl:value-of select="bk:chapter[position()=$i]/@id"/>
                    </xsl:attribute>
                    <xsl:attribute name="standalone">
                        <xsl:value-of>false</xsl:value-of>
                    </xsl:attribute>
                    
                    <xsl:apply-templates select="bk:chapter[position()=$i]/bk:headers"/>
                    <xsl:element name="legitimate.children" namespace="Unit">
                        <xsl:apply-templates select="bk:chapter[position()=$i]/node()[not(name()='headers')]"/>
                    </xsl:element>
                    
                </xsl:element>
            </xsl:result-document>
        </xsl:if>
        
        <xsl:if test="$i &lt;= $count"> 
            <xsl:call-template name="forloop2"> 
                <xsl:with-param name="i"> 
                    <xsl:value-of select="$i + 1"/> 
                </xsl:with-param> 
                <xsl:with-param name="count"> 
                    <xsl:value-of select="$count"/> 
                </xsl:with-param> 
            </xsl:call-template> 
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="bk:chapter">
        <xsl:element name="unit.choice" namespace="Unit">
            <xsl:attribute name="unitId">
                <xsl:value-of select="./@id"/>
            </xsl:attribute>            
        </xsl:element>
        
        <xsl:variable name="unitID">
            <xsl:value-of select="./@id"/>
        </xsl:variable>
        
        <xsl:variable name="chapfile" select="concat($unitID,'.xml')"/>
        
        <xsl:result-document href="{$chapfile}" method="xml">
            <xsl:element name="unit" namespace="Unit">
                <xsl:attribute name="xsi:schemaLocation">Unit ../../NewSchemas/Unit.xsd</xsl:attribute>
                <xsl:attribute name="unitid">
                    <xsl:value-of select="$unitID"/>
                </xsl:attribute>
                <xsl:attribute name="standalone">
                    <xsl:value-of>false</xsl:value-of>
                </xsl:attribute>
                <xsl:apply-templates select="child::node()[name()='headers']"/>
                <xsl:element name="legitimate.children" namespace="Unit">
                    <xsl:apply-templates select="child::node()[not(name()='headers')]"/>
                </xsl:element>
            </xsl:element>            
        </xsl:result-document>
    </xsl:template>
   
</xsl:stylesheet>