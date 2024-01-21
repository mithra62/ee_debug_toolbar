<?php

namespace DebugToolbar\Services;

class XmlService extends \XMLWriter
{

    /**
     * Name of the root element for this XMl. Defaults to root.
     * @var string
     */
    protected string $rootName = '';

    /**
     * XML version. Defaults to 1.0
     * @var string
     */
    protected string $xmlVersion = '1.0';

    /**
     * Character set. Defaukts to UTF-8.
     * @var string
     */
    protected string $charSet = 'UTF-8';

    /**
     * Indent for every new tag. Defaults to spaces.
     * @var string
     */
    protected string $indentString = '    ';

    /**
     *  Sets an xslt path for this XML. Defaults to ''.
     *  Xslt will only be included in the XML if $_xsltFilePath is not an
     *  empty string.
     * @var string
     */
    protected string $xsltFilePath = '';

    public function __construct()
    {
    }

    /**
     * @param string $rootName
     * @return $this
     */
    public function setRootName(string $rootName): XmlService
    {
        $this->rootName = $rootName;
        return $this;
    }

    /**
     * @param string $version
     * @return $this
     */
    public function setXmlVersion(string $version): XmlService
    {
        $this->xmlVersion = $version;
        return $this;
    }

    /**
     * @param string $charSet
     * @return $this
     */
    public function setCharSet(string $charSet): XmlService
    {
        $this->charSet = $charSet;
        return $this;
    }

    /**
     * @param $indentString
     * @return $this
     */
    public function setIndentStr($indentString): XmlService
    {
        $this->indentString = $indentString;
        return $this;
    }

    /**
     * @param string $xsltFilePath
     * @return $this
     */
    public function setXsltFilePath(string $xsltFilePath): XmlService
    {
        $this->xsltFilePath = $xsltFilePath;
        return $this;
    }

    /**
     * @return $this
     */
    public function initiate(): XmlService
    {
        // Create new xmlwriter using memory for string output.
        $this->openMemory();

        // Set indenting, if any.
        if ($this->indentString) {
            $this->setIndent(true);
            $this->setIndentString($this->indentString);
        }

        // Set DTD.
        $this->startDocument($this->xmlVersion, $this->charSet);

        // Set XSLT stylesheet path, if any.
        if ($this->xsltFilePath) {
            $this->writePi('xml-stylesheet', 'type="text/xsl" href="' . $this->xsltFilePath . '"');
        }

        // Set the root tag.
        $this->startElement($this->rootName);
        return $this;
    }

    /**
     * @param string $name
     * @param array $attributes
     * @return $this
     */
    public function startBranch(string $name, array $attributes = []): XmlService
    {
        $this->startElement($name);
        $this->addAttributes($attributes);
        return $this;
    }

    /**
     * End an open branch. A branch needs to be closed explicitely if the branch
     * is followed directly by another branch.
     */
    public function endBranch()
    {
        $this->endElement();
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @param bool $cdata
     * @return $this
     */
    public function addNode(string $name, string $value, array $attributes = [], bool $cdata = false): XmlService
    {
        $this->startElement($name);
        $this->addAttributes($attributes);

        if ($cdata) {
            $this->writeCdata($value);
        } else {
            $this->text($value);
        }

        $this->endElement();
        return $this;
    }

    /**
     * @param bool $echo
     * @return string
     */
    public function getXml(bool $echo = false): string
    {
        if ($echo === true) {
            header('Content-type: text/xml');
        }
        
        $this->endElement();
        $this->endDocument();

        $output = $this->outputMemory();
        if ($echo === true) {
            echo $output;
        }

        return $output;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    protected function addAttributes(array $attributes): XmlService
    {
        if (count($attributes) > 0) {
            // We have attributes, let's set them
            foreach ($attributes as $key => $value) {
                $this->writeAttribute($key, $value);
            }
        }

        return $this;
    }
}  