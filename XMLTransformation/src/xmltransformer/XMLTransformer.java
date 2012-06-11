/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package xmltransformer;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.stream.StreamSource;
import net.sf.saxon.s9api.*;
import org.w3c.dom.Document;
import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

/**
 * The foundation of this code was taken from
 * http://blog.msbbc.co.uk/2007/06/simple-saxon-java-example.html
 *
 * @author User
 */
public class XMLTransformer {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {

        File XmlDir = new File("../xml");
        File newXmlDir = new File("../newXML");
        File XslDir = new File("../XSLTdocuments");

        if (!newXmlDir.exists()) {
            boolean success = newXmlDir.mkdir();
        }

        try {
            scandir(XmlDir, newXmlDir, XslDir);
        } catch (SaxonApiException ex) {
            handleException(ex);
        } catch (IOException e) {
            handleException(e);
        }
        System.out.println("done");
    }

    /**
     *
     * @param xml
     * @param newxml
     * @param xsl
     */
    public static void scandir(File xml, File newxml, File xsl) throws SaxonApiException, IOException {

        File[] listOfFiles = xml.listFiles();

        for (int i = 0; i < listOfFiles.length; i++) {

            String newXmlPath = newxml.getPath();
            File currentFile = listOfFiles[i];

            if (currentFile.isDirectory()) {
                
                if (newxml.getParent().equals("..")) {
                    newXmlPath = newXmlPath + "/" + currentFile.getName();
                } else {
                    //newXmlPath = newxml.getParent();

                    newXmlPath = newXmlPath + "/" + currentFile.getName();
                }
                
                File newXml = new File(newXmlPath);

                if (!newXml.exists()) {
                    boolean success = newXml.mkdir();

                    if (success) {
                        scandir(currentFile, newXml, xsl);
                    } else {
                        System.err.println("The specified directory could not be made.");
                    }
                } else {
                    scandir(currentFile, newXml, xsl);
                }
            } else if (currentFile.isFile()) {
                
                String fileExt = currentFile.getName();

                int position = fileExt.lastIndexOf(".");
                fileExt = fileExt.substring(position + 1);

                if (fileExt.equals("xml")) {
                    String currentnewXmlpath = newXmlPath + "/" + currentFile.getName();

                    newxml = new File(currentnewXmlpath);

                    String type = identifyXmlElement(currentFile.getPath());
                    String inputXsl = xsl.getPath();

                    if (type == null) {
                        System.err.println("unknown type of xml element!");
                        System.exit(0);
                    }

                    if (type.equals("book")) {

                        inputXsl = inputXsl + "/" + "Unit.xsl";

                    } else if (type.equals("intro")) {

                        inputXsl = xsl.getPath();
                        inputXsl = inputXsl + "/" + "Intro.xsl";

                    } else if (type.equals("theorem")) {

                        inputXsl = xsl.getPath();
                        inputXsl = inputXsl + "/" + "Theorem.xsl";

                    } else if (type.equals("subpage")) {

                        inputXsl = xsl.getPath();
                        inputXsl = inputXsl + "/" + "Subpage.xsl";

                    } else if (type.equals("simplepage")) {

                        inputXsl = xsl.getPath();
                        inputXsl = inputXsl + "/" + "Simplepage.xsl";

                    } else if (type.equals("exercisepack")) {

                        inputXsl = xsl.getPath();
                        inputXsl = inputXsl + "/" + "Exercisepack.xsl";

                    } else if (type.equals("showmepack")) {

                        inputXsl = xsl.getPath();
                        inputXsl = inputXsl + "/" + "Showmepack.xsl";

                    } else if (type.equals("quizpack")) {

                        inputXsl = xsl.getPath();
                        inputXsl = inputXsl + "/" + "Quizpack.xsl";

                    } else if (type.equals("examplepack")) {

                        inputXsl = xsl.getPath();
                        inputXsl = inputXsl + "/" + "Examplepack.xsl";

                    } else if (type.equals("section")) {

                        inputXsl = xsl.getPath();
                        inputXsl = inputXsl + "/" + "Section.xsl";

                    } else if (type.equals("unknown")) {
                        copyFile(currentFile, newxml);
                        newxml = new File(newXmlPath);
                    }

                    if (!type.equals("unknown")) {
                        myTransformer(currentFile.getPath(), inputXsl, newxml);
                        newxml = new File(newXmlPath);
                    } 
                    
                } else {
                    String currentnewXmlpath = newXmlPath + "/" + currentFile.getName();
                    newxml = new File(currentnewXmlpath);
                    copyFile(currentFile, newxml);
                    newxml = new File(newXmlPath);

                }
            }
        }
    }

    /**
     *
     * @param xmlpath
     * @return
     * @throws SaxonApiException
     */
    public static String identifyXmlElement(String xmlpath) throws SaxonApiException {
        DocumentBuilder builder;
        NodeList books = null;
        NodeList intros = null;
        NodeList sections = null;
        NodeList subpages = null;
        NodeList simplepages = null;
        NodeList exercisepack = null;
        NodeList showmepack = null;
        NodeList examplepack = null;
        NodeList quizpack = null;
        NodeList theorems = null;

        String identity = null;

        DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
        factory.setNamespaceAware(true);

        System.out.println("xml path");
        System.out.println(xmlpath);
        
        try {
            builder = factory.newDocumentBuilder();
            Document doc = builder.parse(new File(xmlpath));

            books = doc.getElementsByTagName("book");
            intros = doc.getElementsByTagName("intro");
            sections = doc.getElementsByTagName("section");
            subpages = doc.getElementsByTagName("sub.page");
            simplepages = doc.getElementsByTagName("simple.page");
            exercisepack = doc.getElementsByTagName("exercise.pack");
            showmepack = doc.getElementsByTagName("showme.pack");
            examplepack = doc.getElementsByTagName("example.pack");
            quizpack = doc.getElementsByTagName("quiz.pack");
            theorems = doc.getElementsByTagName("theorem");

            //intros can be in book and section documents so it will be default if none of the conditions below are met

            if (books.getLength() != 0) {
                identity = "book";
            } else if (sections.getLength() != 0) {
                identity = "section";
            } else if (theorems.getLength() != 0) {
                identity = "theorem";
            } else if (subpages.getLength() != 0) {
                identity = "subpage";
            } else if (simplepages.getLength() != 0) {
                identity = "simplepage";
            } else if (examplepack.getLength() != 0) {
                identity = "examplepack";
            } else if (exercisepack.getLength() != 0) {
                identity = "exercisepack";
            } else if (showmepack.getLength() != 0) {
                identity = "showmepack";
            } else if (quizpack.getLength() != 0) {
                identity = "quizpack";
            } else if (intros.getLength() != 0) {
                identity = "intro";
            } else {
                identity = "unknown";
            }

        } catch (ParserConfigurationException ex) {
            handleException(ex);
        } catch (SAXException ex) {
            handleException(ex);
        } catch (IOException ex) {
            handleException(ex);
        }

        return identity;
    }

    /**
     *
     * @param nonxmlFile
     * @param newnonxmlFile
     * @throws IOException
     */
    public static void copyFile(File nonxmlFile, File newnonxmlFile)
            throws IOException {
        // First make sure the source file exists, is a file, and is readable.
        if (!nonxmlFile.exists()) {
            System.err.println("no such source file: " + nonxmlFile);
        }
        if (!nonxmlFile.isFile()) {
            System.err.println("can't copy directory: " + nonxmlFile);
        }
        if (!nonxmlFile.canRead()) {
            System.err.println("source file is unreadable: " + nonxmlFile);
        }

        if (newnonxmlFile.exists()) {

            boolean success = newnonxmlFile.delete();
            if (!success) {
                throw new IllegalArgumentException("Delete: deletion failed");
            }

        }

        FileInputStream varFromSource = null;
        // Stream to write to destination
        FileOutputStream VarToDestination = null;
        try {
            // Create input stream
            varFromSource = new FileInputStream(nonxmlFile);
            // Create output stream
            VarToDestination = new FileOutputStream(newnonxmlFile);
            // To hold file contents
            byte[] buffer = new byte[4096];
            // How many bytes in buffer
            int bytes_read;
            // Read until EOF
            while ((bytes_read = varFromSource.read(buffer)) != -1) {
                VarToDestination.write(buffer, 0, bytes_read);
            }
        } catch (Exception e) {
            handleException(e);
        } finally {
            if (varFromSource != null) {
                try {
                    varFromSource.close();
                } catch (IOException e) {
                    handleException(e);
                }
            }

            if (VarToDestination != null) {
                try {
                    VarToDestination.close();
                } catch (IOException e) {
                    handleException(e);
                }
            }
        }

    }

    /**
     *
     * @param inputXmlPath
     * @param inputXslPath
     * @param outputFile
     * @throws SaxonApiException
     */
    public static void myTransformer(String inputXmlPath, String inputXslPath, File outputFile) throws SaxonApiException {

        Processor proc = new Processor(false);
        XsltCompiler comp = proc.newXsltCompiler();
        XsltExecutable exp = comp.compile(new StreamSource(new File(inputXslPath)));
        XdmNode source = proc.newDocumentBuilder().build(new StreamSource(new File(inputXmlPath)));

        if (outputFile != null) {
            Serializer out = proc.newSerializer(outputFile);
            out.setOutputProperty(Serializer.Property.METHOD, "xml");
            out.setOutputProperty(Serializer.Property.INDENT, "yes");

            XsltTransformer trans = exp.load();
            trans.setInitialContextNode(source);
            trans.setDestination(out);
            trans.transform();
        }

    }

    /**
     * This function is called in catch phrases to handle possible errors such
     * as IOException and/or SaxonApiException.
     *
     * @param ex
     */
    private static void handleException(Exception ex) {
        System.out.println("EXCEPTION: " + ex);
        ex.printStackTrace();
    }
}
