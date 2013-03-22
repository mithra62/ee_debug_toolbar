<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class used to write XMl document.
 * Extends XMLWriter (PHP5 only!)
 * 
 * Initialize the class:
 * $this->load->library('MY_Xml_writer');
 * $xml = new MY_Xml_writer;
 * $xml->initiate();
 * 
 * Start a branch with attributes:
 * $xml->startBranch('car', array('country' => 'usa', 'type' => 'racecar'));
 * 
 * End (close) a branch
 * $xml->endBranch();
 * 
 * Add a CDATA node with attributes:
 * $xml->addNode('model', 'Corolla', array('year' => '2002'), true);
 * 
 * Print the XMl directly to screen:
 * $xml->getXml(true);
 * 
 * Pass the XMl to a view file:
 * $data['xml'] = $xml->getXml();
 * $this->load->view('xml_template', $data);
 * 
 * @name /library/Accent/Xml/Accent_Xml_Writer.php
 * @category Accent_application
 * @version 1.0
 * @author Joost van Veen
 * @copyright Accent Webdesign
 * @created: 10 mrt 2009
 */
class Xml_writer extends XMLWriter
{
    
    /**
     * Name of the root element for this XMl. Defaults to root.
     */
    private $_rootName = '';
    
    /**
     * XML version. Defaults to 1.0
     */
    private $_xmlVersion = '1.0';
    
    /**
     * Character set. Defaukts to UTF-8.
     */
    private $_charSet = 'UTF-8';
    
    /**
     * Indent for every new tag. Defaults to spaces.  
     */
    private $_indentString = '    ';
    
    /**
     * Sets an xslt path for this XML. Defaults to ''. 
     * Xslt will only be included in the XML if $_xsltFilePath is not an 
     * empty string.
     */
    private $_xsltFilePath = '';

    public function __construct ()
    {}

    /**
     * Set the value of the root tag for this XML
     */
    public function setRootName ($rootName)
    {
        $this->_rootName = $rootName;
    }

    /**
     * Set the value of the XMl version for this XML.
     */
    public function setXmlVersion ($version)
    {
        $this->_xmlVersion = $version;
    }

    /**
     * Set the character set for this XML.
     */
    public function setCharSet ($charSet)
    {
        $this->_charSet = $charSet;
    }

    /**
     * Set indenting for every new node in this XML.
     */
    public function setIndentStr ($indentString)
    {
        $this->_indentString = $indentString;
    }

    /**
     * Set the XSLT filepath for this XML. This should be an absolute URL.
     */
    public function setXsltFilePath ($xsltFilePath)
    {
        $this->_xsltFilePath = $xsltFilePath;
    }

    public function initiate ()
    {
        // Create new xmlwriter using memory for string output.
        $this->openMemory();
        
        // Set indenting, if any.
        if ($this->_indentString) {
            $this->setIndent(true);
            $this->setIndentString($this->_indentString);
        }
        
        // Set DTD.
        $this->startDocument($this->_xmlVersion, $this->_charSet);
        
        // Set XSLT stylesheet path, if any.
        if ($this->_xsltFilePath) {
            $this->writePi('xml-stylesheet', 'type="text/xsl" href="' . $this->_xsltFilePath . '"');
        }
        
        // Set the root tag.
        $this->startElement($this->_rootName);
    }

    /**
     * Start a new branch that will contain nodes.
     */
    public function startBranch ($name, $attributes = array())
    {
        $this->startElement($name);
        $this->_addAttributes($attributes);
    }

    /**
     * End an open branch. A branch needs to be closed explicitely if the branch 
     * is followed directly by another branch.
     */
    public function endBranch ()
    {
        $this->endElement();
    }

    /**
     * Add a node, typically a child to a branch.
     * 
     * If you wish to create a simple text node, just set $name and $value.
     * If you wish to create a CDATA node, set $name, $value and $cdata.
     * You can set attributes for every node, passing a key=>value $attributes array
     * 
     * @param string $name
     * @param string $value
     * @param array attributes
     * @param boolean $cdata
     * @return void
     */
    public function addNode ($name, $value, $attributes = array(), $cdata = false)
    {
        /**
         * Set a CDATA element.
         */
        if ($cdata) {
            $this->startElement($name);
            $this->_addAttributes($attributes);
            $this->writeCdata($value);
            $this->endElement();
        }
        /**
         * Set a simple text element.
         */
        else {
            $this->startElement($name);
            $this->_addAttributes($attributes);
            $this->text($value);
            $this->endElement();
        }
    }

    /**
     * Close the XML document, print to screen if $echo == true, and return a 
     * string containing the full XML.
     * 
     * @param boolean echo - if true, print XML to screen. 
     * @return string - The full XML.
     */
    public function getXml ($echo = false)
    {
        
        /**
         * Set header.
         */
        if ($echo == true) {
            header('Content-type: text/xml');
        }
        
        /**
         * Close XMl document.
         */
        $this->endElement();
        $this->endDocument();
        
        /**
         * Return or echo output.
         */
        $output = $this->outputMemory();
        if ($echo == true) {
            // Print XML to screen.
            print $output;
        }
        
        return $output;
    }

    /**
     * Add attributes to an element.
     * 
     * @param array $attributes
     */
    private function _addAttributes ($attributes)
    {
        if (count($attributes) > 0) {
            // We have attributes, let's set them
            foreach ($attributes as $key => $value) {
                $this->writeAttribute($key, $value);
            }
        }
    }
}  