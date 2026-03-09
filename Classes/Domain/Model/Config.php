<?php
namespace Phi\PhiNewsletter\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Config
 */
class Config extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * filestorage
     *
     * @var string
     */
    protected $filestorage = '';

    /**
     * image0
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $image0 = NULL;

    /**
     * image1
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $image1 = NULL;

    /**
     * image2
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $image2 = NULL;

    /**
     * configuration
     *
     * @var string
     */
    protected $configuration = '';

    /**
     * emailfrom
     *
     * @var string
     */
    protected $emailfrom = '';

    /**
     * namefrom
     *
     * @var string
     */
    protected $namefrom = '';

    /**
     * subject0
     *
     * @var string
     */
    protected $subject0 = '';

    /**
     * subject1
     *
     * @var string
     */
    protected $subject1 = '';

    /**
     * subject2
     *
     * @var string
     */
    protected $subject2 = '';

    /**
     * replytoemail
     *
     * @var string
     */
    protected $replytoemail = '';

    /**
     * replytoname
     *
     * @var string
     */
    protected $replytoname = '';

    /**
     * statuspageid
     *
     * @var string
     */
    protected $statuspageid = '';

    /**
     * prefix0
     *
     * @var string
     */
    protected $prefix0 = '';

    /**
     * prefix1
     *
     * @var string
     */
    protected $prefix1 = '';

    /**
     * prefix2
     *
     * @var string
     */
    protected $prefix2 = '';

    /**
     * url
     *
     * @var string
     */
    protected $url = '';

    /**
     * selected
     *
     * @var bool
     */
    protected $selected = FALSE;

    /**
     * tosendtime
     *
     * @var string
     */
    protected $tosendtime = '';

    /**
     * issent
     *
     * @var int
     */
    protected $issent = 0;

    /**
     * Returns the emailfrom
     *
     * @return string $emailfrom
     */
    public function getEmailfrom()
    {
        return $this->emailfrom;
    }

    /**
     * Sets the emailfrom
     *
     * @param string $emailfrom
     * @return void
     */
    public function setEmailfrom($emailfrom)
    {
        $this->emailfrom = $emailfrom;
    }

    /**
     * Returns the namefrom
     *
     * @return string $namefrom
     */
    public function getNamefrom()
    {
        return $this->namefrom;
    }

    /**
     * Sets the namefrom
     *
     * @param string $namefrom
     * @return void
     */
    public function setNamefrom($namefrom)
    {
        $this->namefrom = $namefrom;
    }

    /**
     * Returns the subject0
     *
     * @return string $subject0
     */
    public function getSubject0()
    {
        return $this->subject0;
    }

    /**
     * Sets the subject0
     *
     * @param string $subject0
     * @return void
     */
    public function setSubject0($subject0)
    {
        $this->subject0 = $subject0;
    }

    /**
     * Returns the subject1
     *
     * @return string $subject1
     */
    public function getSubject1()
    {
        return $this->subject1;
    }

    /**
     * Sets the subject1
     *
     * @param string $subject1
     * @return void
     */
    public function setSubject1($subject1)
    {
        $this->subject1 = $subject1;
    }

    /**
     * Returns the subject2
     *
     * @return string $subject2
     */
    public function getSubject2()
    {
        return $this->subject2;
    }

    /**
     * Sets the subject2
     *
     * @param string $subject2
     * @return void
     */
    public function setSubject2($subject2)
    {
        $this->subject2 = $subject2;
    }

    /**
     * Returns the replytoemail
     *
     * @return string $replytoemail
     */
    public function getReplytoemail()
    {
        return $this->replytoemail;
    }

    /**
     * Sets the replytoemail
     *
     * @param string $replytoemail
     * @return void
     */
    public function setReplytoemail($replytoemail)
    {
        $this->replytoemail = $replytoemail;
    }

    /**
     * Returns the replytoname
     *
     * @return string $replytoname
     */
    public function getReplytoname()
    {
        return $this->replytoname;
    }

    /**
     * Sets the replytoname
     *
     * @param string $replytoname
     * @return void
     */
    public function setReplytoname($replytoname)
    {
        $this->replytoname = $replytoname;
    }

    /**
     * Returns the statuspageid
     *
     * @return string $statuspageid
     */
    public function getStatuspageid()
    {
        return $this->statuspageid;
    }

    /**
     * Sets the statuspageid
     *
     * @param string $statuspageid
     * @return void
     */
    public function setStatuspageid($statuspageid)
    {
        $this->statuspageid = $statuspageid;
    }

    /**
     * Returns the prefix0
     *
     * @return string $prefix0
     */
    public function getPrefix0()
    {
        return $this->prefix0;
    }

    /**
     * Sets the prefix0
     *
     * @param string $prefix0
     * @return void
     */
    public function setPrefix0($prefix0)
    {
        $this->prefix0 = $prefix0;
    }

    /**
     * Returns the prefix1
     *
     * @return string $prefix1
     */
    public function getPrefix1()
    {
        return $this->prefix1;
    }

    /**
     * Sets the prefix1
     *
     * @param string $prefix1
     * @return void
     */
    public function setPrefix1($prefix1)
    {
        $this->prefix1 = $prefix1;
    }

    /**
     * Returns the prefix2
     *
     * @return string $prefix2
     */
    public function getPrefix2()
    {
        return $this->prefix2;
    }

    /**
     * Sets the prefix2
     *
     * @param string $prefix2
     * @return void
     */
    public function setPrefix2($prefix2)
    {
        $this->prefix2 = $prefix2;
    }

    /**
     * Returns the url
     *
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the url
     *
     * @param string $url
     * @return void
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Returns the filestorage
     *
     * @return string $filestorage
     */
    public function getFilestorage()
    {
        return $this->filestorage;
    }

    /**
     * Sets the filestorage
     *
     * @param string $filestorage
     * @return void
     */
    public function setFilestorage($filestorage)
    {
        $this->filestorage = $filestorage;
    }

    /**
     * Returns the selected
     *
     * @return boolean $selected
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * Sets the selected
     *
     * @param boolean $selected
     * @return void
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

    /**
     * Returns the boolean state of selected
     *
     * @return boolean
     */
    public function isSelected()
    {
        return $this->selected;
    }

    /**
     * Returns the image0
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image0
     */
    public function getImage0()
    {
        return $this->image0;
    }

    /**
     * Sets the image0
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image0
     * @return void
     */
    public function setImage0(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image0 = NULL)
    {
        $this->image0 = $image0;
    }

    /**
     * Returns the image1
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image1
     */
    public function getImage1()
    {
        return $this->image1;
    }

    /**
     * Sets the image1
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image1
     * @return void
     */
    public function setImage1(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image1 = NULL)
    {
        $this->image1 = $image1;
    }

    /**
     * Returns the image2
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image2
     */
    public function getImage2()
    {
        return $this->image2;
    }

    /**
     * Sets the image2
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image2
     * @return void
     */
    public function setImage2(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image2 = NULL)
    {
        $this->image2 = $image2;
    }

    /**
     * Returns the configuration
     *
     * @return \string
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Sets the configuration
     *
     * @param \string $configuration
     * @return void
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

     /**
     * Returns the issent
     *
     * @return \string
     */
    public function getIssent()
    {
        return $this->issent;
    }

    /**
     * Sets the issent
     *
     * @param \string $issent
     * @return void
     */
    public function setIssent($issent)
    {
        $this->issent = $issent;
    }

    /**
     * Returns the tosendtime
     *
     * @return \string
     */
    public function getTosendtime()
    {

        return is_numeric($this->tosendtime)?date("H:i j.n.Y",$this->tosendtime):$this->tosendtime;
    }

    /**
     * Returns the tosendtimeNumeric
     *
     * @return \string
     */
    public function getTosendtimeNumeric()
    {

        return strlen($this->tosendtime) == 0?"0":$this->tosendtime;
    }

    /**
     * Sets the tosendtime
     *
     * @param \string $tosendtime
     * @return void
     */
    public function setTosendtime($tosendtime)
    {
        $this->tosendtime = $tosendtime;
    }

    /**
     * Gets the template
     *
     * @param \string $tosendtime
     * @return void
     */
    public function getTemplate()
    {
        $path = $this->getConfigurationValue("templatePath");
        $realName = explode("_",substr($path,strrpos($path,"/")+1));
        $realName = array_map(function($a){return ucfirst($a);},$realName);
        return trim(implode(" ",$realName));
    }

    /**
     * Returns the value of $name
     *
	 * @param \string $name
     * @return \string
     */
     public function getConfigurationValue($name)
    {
		$conf = $this->getConfiguration();
		$val = "";
		$confArray = explode(PHP_EOL,$conf);
		foreach($confArray as $line){
			$lineArray = explode("=",$line);
			if(trim(strtolower($lineArray[0])) == strtolower($name)){
				$val = $lineArray[1];
			}
		}
        return $val;
    }

    /**
     * Returns the value of $name
     *
     * @param \array $conf
     * @param \string $name
     * @return \string
     */
    static public function getConfigurationValueFromArray($conf,$name)
    {

		$val = "";
		$confArray = explode(PHP_EOL,$conf);
		foreach($confArray as $line){
			$lineArray = explode("=",$line);
			if(trim(strtolower($lineArray[0])) == strtolower($name)){
				$val = $lineArray[1];
			}
		}
        return $val;
    }

}
