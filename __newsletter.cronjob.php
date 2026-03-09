#!/usr/local/bin/php
<?php
define('SUBHEADER_LENGTH', 150);
define('SECURITYHASH', md5("HANSSHAKE"));
error_reporting(E_ERROR);
ini_set('display_errors',1);

$basePath = dirname(__file__).'/';
$extPath = $basePath . 'typo3conf/ext/phi_newsletter/';
$mailerPath = $extPath . 'Resources/PMX/phpmailer/';
require $mailerPath . 'PHPMailerAutoload.php';

class cronJ {

    var $db;
    var $config;
    var $newsletters;
    var $savedToFile;
	var $itemFile;
	var $mainFile;
	var $stdMarkers;

	/**
	* init the database connection and load language labels and ids
	*
	* @return void
	*/
    public function init() {
		global $basePath,$extPath;
        //load db connection for typo3 version 6 or higher, from file "LocalConfiguration.php"
        $conf = require_once($basePath . "typo3conf/LocalConfiguration.php");

		if(isset($conf["DB"]["Connections"])){

			$host = $conf["DB"]["Connections"]['Default']["host"];
			$user = $conf["DB"]["Connections"]['Default']["user"];
			$pwd = $conf["DB"]["Connections"]['Default']["password"];
			$db = $conf["DB"]["Connections"]['Default']["dbname"];
		}else{
			$host = $conf["DB"]["host"];
			$user = $conf["DB"]["username"];
			$pwd = $conf["DB"]["password"];
			$db = $conf["DB"]["database"];
		}
        $this->db = new mysqli($host,$user, $pwd, $db);
        //load default Language keys and labels
        $this->getLL();
		//load website languages from database sys_language table
        $this->lang_keys = $this->getLangIds();

    }

	/**
	* load emails to send
	* todo: get Limit from the configuration
	*
	* @return void
	*/
    public function loadMails() {
		global $extPath,$basePath;

        $taskid = "phi".mt_rand();

 	 	$this->db->query("UPDATE tx_phinewsletter_domain_model_emails SET uniqueid = '".$taskid."' WHERE uid IN (SELECT uid FROM (SELECT ss.uid FROM tx_phinewsletter_domain_model_emails AS ss LEFT JOIN fe_users AS fe ON ss.userid = fe.uid WHERE uniqueid = '' AND senttime = 0 AND tosendtime < " . time() . "  LIMIT 0,20) tmp)");

        $res = $this->db->query("SELECT ss.*,fe.uid AS user,fe.last_name,fe.first_name,fe.language,fe.email,fe.gender,fe.usergroup FROM tx_phinewsletter_domain_model_emails AS ss LEFT JOIN fe_users AS fe ON ss.userid = fe.uid WHERE uniqueid = '".$taskid."' AND senttime = 0 AND tosendtime < " . time() . "  LIMIT 0,20");
        while ($row = $res->fetch_assoc()) {
			//old
            //$this->config = unserialize(($row['conf']));
      	 	$res_config = $this->db->query("SELECT * FROM tx_phinewsletter_domain_model_config AS config WHERE uid = ".$row['config']);
			if($conf_row = $res_config->fetch_assoc()){
				$this->config = $conf_row;
       			$this->config['url'] = $this->removeLastSlash($this->config['url']);
       			$this->config['url'] = $this->addProtocol($this->config['url'],"http");
				$conf = $this->config['configuration'];
				unset($this->config['configuration']);
				$confArray = explode(PHP_EOL,$conf);
				foreach($confArray as $line){
					$lineArray = explode("=",$line);
					$key = trim($lineArray[0]);
					$this->config['configuration'][$key] = trim($lineArray[1]);
				}

			}

			if(isset($this->config['configuration']["languageFile"])){
       			$this->getLL($this->config['configuration']["languageFile"]);
			}

			if(isset($this->config['configuration']["templatePath"])){
       			$this->config['configuration']["templatePath"] = $this->removeLastSlash($this->config['configuration']["templatePath"]);
				$this->itemFile = $basePath . $this->config['configuration']["templatePath"].'/item.html';
				$this->mainFile = $basePath . $this->config['configuration']["templatePath"].'/main.html';
			}else{
				$this->itemFile = $extPath . 'Resources/PMX/html/'. $this->config["url"].'/item.html';
				$this->mainFile = $extPath . 'Resources/PMX/html/'. $this->config["url"].'/main.html';
			}

			//load stdMarkers
			$this->initStdMarkers($row);


            $this->getItems($row['newsids'],$row["userid"]);

            //mail('philipp.holzmann@promacx.ch','sub',$this->getEmailContents(explode(",",$row['news_ids'])));
            //$personalize = strlen($row['title']) > 0?$row['title']." ".$row['last_name']:"";
            $personalize = '';
            if (strlen($row['gender'])) {
                $personalize = sprintf($this->getLocal("hello_" . $row["gender"],$row['language']),$row['last_name']);
            } else{
                $personalize = sprintf($this->getLocal("hello",$row['language']),$row['first_name'] . ' ' . $row['last_name']);
            }
            $subject = $this->renderSub();
            $contents = $this->getEmailContents(explode(",", $row['newsids']));


			$content = $this->wrapInTemplate($contents[$row['language']]["content"], $row['language'], ($personalize), $row['email'],$row['user']);

           // die();

            $s = array('</td>', '</tr>');
            $r = array('\t', '\n\r');
            /* $alt_content = preg_replace('\<style(.?*)\<\/style\>','',$content);
              $alt_content = str_replace($s,$r,$alt_content);
              $alt_content = strip_tags($content); */

            if (strlen($row['email']) && $this->sendEmail($row['email'], $personalize, $subject, 0, $content)) {
			 	$this->db->query("UPDATE  tx_phinewsletter_domain_model_emails SET senttime = " . time() . " WHERE uid = " . $row['uid']);
			}else{
			 	$this->db->query("UPDATE  tx_phinewsletter_domain_model_emails SET senttime = 1 WHERE uid = " . $row['uid']);
			}
        }
    }


	/**
	* addProtocol to url
	*
	* @param string $url
	* @param string $protocol
	* @return void
	*/
	function addProtocol($url,$protocol){
		return (strpos($url,$protocol) === false?$protocol."://" . $url:$url);
	}

	/**
	* removeLastSlash if exists
	*
	* @param string $link_url
	* @return string
	*/
	function removeLastSlash($link_url){
		return (strrpos($link_url,"/") == strlen($link_url) - 1)?substr($link_url, 0, strlen($link_url) - 1):$link_url;
	}

	/**
	* renderSub render the Subject
	*
	* @return string
	*/
    function renderSub() {
        $s = array('{datum}');
        $r = array(date("d.m.Y", time()));
        return str_replace($s, $r, ($this->config['subject']));
    }

	/**
	* getEmailContents
	*
	* @param array $id_order
	* @return void
	*/
    function getEmailContents($id_order) {
        $mailcontent = "";
        //mail('philipp.holzmann@promacx.ch',"personalize",print_r($id_order,true));
        $mailaltcontent = "";
        if (!is_array($id_order)) {
            return array();
        }

        //  mail('philipp.holzmann@promacx.ch','mail contet',print_r($id_order,true));

      	$contents = array();

        foreach ($id_order as $id) {
            $l18n_entries = $this->newsletters[$id];
			foreach ($l18n_entries as $lang => $entry) {
				$contents[$lang]["content"] .= str_replace('###CONTENT-PAGE###', 'left-content', ($entry['content']));
            }
        }
		unset($this->newsletters);
        return $contents;
    }

	/**
	* copyResizedImage
	*
	* @param string $src
	* @return string
	*/
    function copyResizedImage($src) {
		global $basePath;

        $dstWidth = 500;

		if(strlen($this->config['configuration']['imageMaxWidth'])){
			$dstWidth = $this->config['configuration']['imageMaxWidth'];
		}

		$imagesize = getimagesize($basePath . $src);
		$sourceWidth = $imagesize[0];
        $sourceHeight = $imagesize[1];

        $scale = $sourceWidth /$dstWidth;
		$dstHeight = $sourceHeight / $scale;
        $srcName = $src;
        $dstName = "newsimage_" . substr($src,strrpos($src,"/") + 1); //new image name

		$dstPath = "uploads/phi_newsletter";


        if (!is_dir($basePath . $dstPath)) {
            //return $dstName;
            mkdir($basePath . $dstPath);
        }
        if (file_exists($basePath  . $dstPath . "/" . $dstName)) {
            //return $dstName;
            unlink($basePath . $dstPath . "/" . $dstName);

        }
        if (count(gd_info()) > 0) {
            switch (substr($src, strlen($src) - 3)) {
                case "JPG":
                case "PEG":
                case "jpg":
                case "peg":
                    $src = imagecreatefromjpeg($basePath . $src);
                    break;
                case "gif":
                    $src = imagecreatefromgif($basePath . $src);
                    break;
                case "png":
                    $src = imagecreatefrompng($basePath . $src);
                    break;
                default:
                    return "";
            }
            $dst = imagecreatetruecolor($dstWidth, $dstHeight);

            $rClip = $dstHeight / $dstWidth;


            $k = ($rClip < $rSource) ? $sourceWidth / $dstWidth : $sourceHeight / $dstHeight;

            $w = $k * $dstWidth;
            $h = $k * $dstHeight;

            $x = ($sourceWidth - $w) / 2;
            $y = ($sourceHeight - $h) / 2;

            if ($newImage = imagecopyresampled($dst, $src, 0, 0, $x, $y, $dstWidth, $dstHeight, $w, $h)) {// imagecopyresampled($dst,$src,0,0,$x,$y,$dstWidth,$dstHeight
                switch (substr($srcName, strlen($srcName) - 3)) {
                    case "JPG":
                    case "jpg":
                    case "PEG":
                    case "peg":
                        if (!imagejpeg($dst, $basePath . $dstPath . "/" . $dstName)) {
                            return "";
                        }
                        break;
                    case "gif":
                        if (!imagegif($dst, $basePath . $dstPath . "/" . $dstName)) {
                            return "";
                        }
                        break;
                    case "png":
                        if (!imagepng($dst, $basePath . $dstPath . "/" . $dstName)) {
                            return "";
                        }
                        break;
                    default:
                        return "";
                }
            }
        } else {
            return "";
        }
        imagedestroy($dst);
        imagedestroy($src);

        return  $dstPath . "/" . $dstName;
    }

	/**
	* getItems
	*
	* @param string $actualids
	* @param string $user
	* @return void
	*/
    public function getItems($actualids,$user) {

		$table = $this->config["configuration"]["databaseTable"];
		$pageFields = $this->config["configuration"]["pageFields"];

		$additionalFields = '';
		if(strlen($pageFields)){
			$additionalFields = "," . $pageFields;
		}

		$edition = $this->stdMarkers["###EDITION###"];

		$localizationField = "l10n_parent";
		if(strlen($this->config["configuration"]["databaseLocalizationField"])){
			$localizationField = $this->config["configuration"]["databaseLocalizationField"];
		}

		$sql = "SELECT ".$table .".*".$additionalFields." FROM ".$table ." LEFT JOIN pages ON ".$table .".pid = pages.uid WHERE ".$table .".uid IN (".$actualids.") OR ".$table ."." . $localizationField . " IN (".$actualids.") ORDER BY ".$table .".uid";

        //AND news.hidden =0
        $res = $this->db->query($sql);


        $item = file_get_contents($this->itemFile);
        $teaser = "";
        while ($row = $res->fetch_assoc()) {
            $item_html = "";
			$replace = array();
			foreach($row as $key=>$val){
				if($key == "datum"){
					$val = date("d.m.Y",$val);
				}
				$replace["###".strtoupper($key)."###"] = $val;
			}
			foreach($this->config["configuration"] as $key=>$val){
				if(strpos($key,"marker") !== false){
					$keyArray = explode(".",$key);
					if(count($keyArray) > 2){
						$condition = explode(":",$keyArray[2]);
						if($row[$condition[0]] == $condition[1]){
							$replace["###".strtoupper($keyArray[1])."###"] = $val;
						}
					}else{
						$replace["###".strtoupper($keyArray[1])."###"] = $val;
					}
				}
			}

			if(strlen($this->config["configuration"]["croppedTeaserField"])){
			    $croppedteaser = $row[$this->config["configuration"]["croppedTeaserField"]];
				$croppedteaser = str_replace(array("<br />","<br>"),array("<br/>","<br/>"),nl2br($croppedteaser));
				$croppedteaserArray = explode(" ",$croppedteaser);

				$index = 0;
				$croppedteaser = '';
				while(strlen($croppedteaser) < SUBHEADER_LENGTH && $index < count($croppedteaserArray)){
					$croppedteaser .= $croppedteaserArray[$index]  . " ";
					$index++;
				}
				/**
				* BARIS CUSTOM
				* No 3 dots if no content
				*
				*/
				if(strlen(trim($croppedteaser)) > 0) {
					$replace["###".strtoupper("croppedTeaserField")."###"] = trim($croppedteaser);
				} else {
					$replace["###".strtoupper("croppedTeaserField")."###"] = "";
				}
			}
			$backPid = $this->config["backpageid"];
            if($backPid == "{pid}"){
				$backPid = $row["pid"];
			}

			$itemid = $row['sys_language_uid'] == 0 ? $row['uid'] : $row['l10n_parent'];
 			$link_content = $this->config['url'] . '/index.php?eID=newsletter_tracker&amp;newsletter_tracker[crc]='.md5("HANSSHAKE").'&amp;newsletter_tracker[language]=' . $row['sys_language_uid'] . '&amp;no_cache=1&amp;newsletter_tracker[newsid]='.$itemid.'&amp;newsletter_tracker[user]='.$user.'&amp;newsletter_tracker[edition]='.$edition.'&amp;newsletter_tracker[config]='.$this->config['uid'].'&amp;newsletter_tracker[newspage]='.$backPid.'&amp;L='.$row['sys_language_uid'];

			if(strlen($this->config["configuration"]["additionalParams"])){
				$params = explode(",",$this->config["configuration"]["additionalParams"]);
				foreach($params as $p){

					$link_content .= (isset($row[$p]) && strlen($row[$p]))?'&amp;newsletter_tracker[additionalParams:'.$p.']='.$row[$p]:'';
				}
			}
			//print_r($link_content);exit;
            //translate images!
           /*if(intval($row["images"]) > 0 && $row['sys_language_uid'] > 0){
				  $itemid = $row['uid'];
			 }*/
			$imageSql =	"SELECT identifier,name FROM sys_file LEFT JOIN sys_file_reference ON sys_file.uid = sys_file_reference.uid_local WHERE sys_file_reference.tablenames = '".$table."' AND sys_file_reference.uid_foreign = " . $itemid ." AND sys_file_reference.hidden = 0 AND sys_file_reference.deleted = 0 ORDER BY sys_file_reference.sorting_foreign LIMIT 1";

        	$imageRes = $this->db->query($imageSql);
			$resizedImage = "";
			$replace["###IMAGE_TAG###"] = '';
			if($imageRow = $imageRes->num_rows == 0){
				if(strlen($row["image"])){
					$resizedImage = $this->copyResizedImage($this->config['filestorage'] . $row["image"]);
					$replace["###IMAGE_NAME###"] = $row["image"];
					$replace["###IMAGE_SRC###"] = $this->config['url'] . "/" . $resizedImage."?random=" . rand();
					$replace["###IMAGE_TAG###"] = '<img src="'.$replace["###IMAGE_SRC###"].'" alt="'.$replace["###IMAGE_NAME##"].'" class="fullwidth" width="580"/>';
				}
			} elseif($imageRow = $imageRes->fetch_assoc()){
				$resizedImage = $this->copyResizedImage($this->config['filestorage'] . $imageRow["identifier"]);
				$replace["###IMAGE_NAME###"] = $imageRow["name"];
				$replace["###IMAGE_SRC###"] = $this->config['url'] . "/" . $resizedImage."?random=" . rand();
				$replace["###IMAGE_TAG###"] = '<img src="'.$replace["###IMAGE_SRC###"].'" alt="'.$replace["###IMAGE_NAME##"].'" class="fullwidth" width="580"/>';
			}

            $replace["###MORE_LINK###"] = $link_content;
			$replace["###READ_MORE###"] = $this->getLocal("readmore",$row['sys_language_uid']);


            $content = str_replace(array_keys($replace), array_values($replace), $item);
			echo $content;
			//replace lang markers afterwards!
            $content = str_replace(array_keys($this->stdMarkers), array_values($this->stdMarkers), $content);

            $alt_content = $row['title'] . "\n\r\n\r"; //$this->config['prefix']."<br/><br/>";
            $alt_content .= $row['short'] . "\n\r\n\r";

            $alt_content = str_replace("<br>", "\n\r", $alt_content);
            $alt_content = str_replace("<br/>", "\n\r", $alt_content);
            $alt_content = str_replace("<p>", "\n\r\n\r", $alt_content);
            $alt_content .= $link_alt_content; //str_replace($this->config['morelinktext'],$link_alt_content,$alt_content);
            $alt_content = strip_tags($alt_content);
            $alt_content = addslashes($alt_content);
            //$content = addslashes($content);

            $orderid = in_array($row['sys_language_uid'],array(0,-1)) ? $row['uid'] : $row['l10n_parent'];
			$languageId = $row['sys_language_uid'] > -1?$row['sys_language_uid']:"ALL";
            $this->newsletters[$orderid][$languageId]['content'] = ($content);
            $this->newsletters[$orderid][$languageId]['altcontent'] = $alt_content;
        }   exit;
    }

	/**
	* getLangIds
	*
	* @return string
	*/
    function getLangIds() {
        $res = $this->db->query("SELECT * FROM sys_language ORDER BY uid");
        $lang_ids = array("default");
        while ($row = $res->fetch_assoc()) {
            $lang_ids[$row['uid']] = strtolower(substr($row['title'], 0, 2));
        }
        return $lang_ids;
    }

	/**
	* getHeadImage
	*
	* @return string
	*/
	function getHeadImage(){

		$imageSql =	"SELECT identifier,name FROM sys_file LEFT JOIN sys_file_reference ON sys_file.uid = sys_file_reference.uid_local WHERE sys_file_reference.tablenames = 'tx_phinewsletter_domain_model_config' AND sys_file_reference.uid_foreign = " . $this->config["uid"] ." AND sys_file_reference.hidden = 0 AND sys_file_reference.deleted = 0 ORDER BY sys_file_reference.uid DESC LIMIT 1";

        $imageRes = $this->db->query($imageSql);
		$resizedImage = "";
		$imageName = "";
		if($imageRow = $imageRes->fetch_assoc()){
			$imageName = $this->config['filestorage'] . $imageRow["identifier"];
		}
		return $imageName;
	}

	/**
	* wrapInTemplate
	*
	* @param string $content
	* @param string $lang
	* @param string $hello
	* @param string $mailTo
	* @param string $user
	* @return string
	*/
    function wrapInTemplate($content, $lang, $hello, $mailTo,$user) {
		 if (is_file($this->mainFile)) {
            $html = file_get_contents($this->mainFile);
        } else {
            return $content;
        }
        $link_url = $this->config['url'];

        $month = array(
            array("Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"),
            array("Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao&ucirc;t", "Septembre", "Octobre", "Novembre", "D&eacute;cembre")
        );

		$replace = $this->stdMarkers;

        $replace["###CONTENT###"] = $content;
        $replace["###URL###"] = $link_url;
        $replace["###MONTH###"] = date("m");
        $replace["###YEAR###"] = date("y");
        $replace["###UNSUBSCRIBE_LINK###"] = $this->config["unsubscribepageid"];

        $replace["###KEY_LANG###"] = $lang;
        $pref = "";
        //$replace = array();

       /*if($lang == 2){
			$pref = ((str_replace(array("<br>","<br/>"),array("BRTAG","BRTAG"),nl2br($this->config['prefixfr']))));
       }elseif($lang == 1){
			$pref = ((str_replace(array("<br>","<br/>"),array("BRTAG","BRTAG"),nl2br($this->config['prefixen']))));
        }else{
			$pref = ((str_replace(array("<br>","<br/>"),array("BRTAG","BRTAG"),nl2br($this->config['prefixdefault']))));
		}*/
		$pref = ((str_replace(array("<br>","<br/>"),array("BRTAG","BRTAG"),nl2br($this->config['prefix' . $lang]))));

        $timestamp = time();

        //encoding error from the mailserver because of encoding issues
        $pref = ($pref);
       // $pref = "Löärem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et bokimanm eewdewdedewdedewdew accusam et";


    	$replace["###HEAD_IMAGE###"] = '<img src="'.$this->config["url"] . "/" . $this->getHeadImage().'" alt="head" />';

    	$replace["###HEAD###"] = str_replace("BRTAG", "<br>", $pref);
		$replace["###EMPTY_HEAD###"] = strlen($pref) == 0?'display:none;':'';
		$replace["###URL_LANG###"] = $this->lang_keys[$lang];
        //$replace["###EDITION###"] = $edition;

		$replace["###UNSUBSCRIBE###"] = $this->config["url"] . "/index.php?eID=newsletter_unsubscribe&amp;email=".$mailTo . "&amp;L=".$lang."&amp;unsubscribe=" . $this->config["unsubscribepageid"];

		$replace["###LANG_UID###"] = $lang;
		$replace["###SUBJECT###"] = $this->config['subject'];


		$edition = $this->stdMarkers["###EDITION###"];

		//general markers with empty strings for the webview
		$replace["###ANREDE###"] = $this->getLocal("hello_noname", $lang);
        $replace["###USER_EMAIL###"] = "";
		$replace["###VIEW_HTML###"] = "";
		$replace["###VIEW_HTML_LINK###"] = "";
		$replace["###UNSUBSCRIBE###"] = "#";

        $file_html = str_replace(array_keys($replace), array_values($replace), $html);

      	$this->saveToFile($file_html,$lang,$mailTo,$edition);

		$viewHTMLLink = $this->config['url'] . '/index.php?eID=newsletter_tracker&amp;newsletter_tracker[crc]='.md5("HANSSHAKE").'&amp;newsletter_tracker[language]=' . $row['sys_language_uid'] . '&amp;no_cache=1&newsletter_tracker[newsid]=0&amp;newsletter_tracker[user]=0&amp;newsletter_tracker[edition]='.$edition.'&amp;newsletter_tracker[config]='.$this->config['uid'].'&amp;newsletter_tracker[newspage]=' . urlencode($this->savedToFile[$lang]);
		$html_link = '<a href="'.$viewHTMLLink.'" target="_blank" style="color:#706a60;font-size:14px;">'.$this->getLocal("viewHtml",$lang).'</a>';

		//personalized markers with empty strings for the webview
   		$replace["###ANREDE###"] = $hello;
        $replace["###USER_EMAIL###"] = $mailTo;
		$replace["###VIEW_HTML###"] = $html_link;
		$replace["###VIEW_HTML_LINK###"] = $viewHTMLLink;
		$replace["###UNSUBSCRIBE###"] = $this->config["url"] . "/index.php?eID=newsletter_unsubscribe&amp;email=".$mailTo . "&amp;L=".$lang."&amp;unsubscribe=" . $this->config["unsubscribepageid"];

        $mail_html = str_replace(array_keys($replace), array_values($replace), $html);

        return $mail_html;
    }

	/**
	* saveToFile
	*
	* @param string $content
	* @param string $lang
	* @param string $mailTo
	* @param string $edition
	* @return string
	*/
    function saveToFile($content, $lang, $mailTo,$edition) {
		global $basePath;

        $content = str_replace("charset=iso-8859-1", "charset=utf-8", $content);
        $content = str_replace("charset=ISO-8859-1", "charset=utf-8", $content);
        $content = str_replace('<meta http-equiv="X-UA-Compatible" content="IE=edge">', '<meta http-equiv="X-UA-Compatible" content="IE=edge"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />', $content);
        $content = str_replace("###VIEW_HTML###", '<script type="text/javascript">if(window.location.search.indexOf(\'print\') > -1){window.print();}</script>', $content);
		// newsletter_tracker[crc]

		$content = preg_replace("/newsletter_tracker\[crc\]\=(.*?)\&amp;/","newsletter_tracker[crc]=". SECURITYHASH ."&amp;",$content);
		$content = preg_replace("/newsletter_tracker\[user\]\=(.*?)\&amp;/","newsletter_tracker[user]=0&amp;",$content);


        //$lKey = $lang == 1 ? 'fr' : ($lang == 2 ? 'default' : 'de');

        if (!is_dir($basePath . "uploads/phi_newsletter/webview")) {
            mkdir($basePath . "uploads/phi_newsletter/webview", 0755);
        }
		$filename = "uploads/phi_newsletter/webview/newsletter-edition" . $edition . "-" . $lang . ".html";
        if (!isset($this->savedToFile[$lang]) && !is_file($filename)) {
            file_put_contents($basePath . $filename, utf8_encode($content));
	    }
        $this->savedToFile[$lang] = $this->config["url"] . "/" . $filename;
    }

	/**
	* getLocal
	*
	* @param string $key
	* @param string $lang
	* @return string
	*/
    function getLocal($key, $lang) {
        $str = isset($this->lang[$this->lang_keys[$lang]][$key]) ? $this->lang[$this->lang_keys[$lang]][$key] : $this->lang["default"][$key];
        return utf8_decode($str);
    }

	/**
	* sendEmail
	*
	* @param string $mailTo
	* @param string $hello
	* @param string $subject
	* @param string $lang
	* @param string $html
	* @param string $alt_content
	* @return string
	*/
    function sendEmail($mailTo, $hello, $subject, $lang = 0, $html = "") {

        //$file = '/home/richard/example.php'; to embed the file, first declare it like here
        $crlf = "\n";


		//Create a new PHPMailer instance
		$mail = new PHPMailer;
		// Set PHPMailer to use the sendmail transport
		$mail->isSendmail();
		//Set who the message is to be sent from
		$mail->setFrom($this->config['emailfrom'], $this->config['namefrom']);
		//Set an alternative reply-to address
		$mail->addReplyTo($this->config['emailfrom'], $this->config['namefrom']);
		//Set who the message is to be sent to
		$mail->addAddress($mailTo);
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($html);
		//Replace the plain text body with one created manually
		//$mail->AltBody = $alt_content;

		$ret = false;
		//send the message, check for errors
		if ($mail->send()) {
			$echo = "Message sent!";
			$ret = true;
		} else {
			$echo = "Mailer Error: " . $mail->ErrorInfo;
			$ret = false;
		}
        return $ret;
    }

	/**
	* getLL
	*
	* @param string $path
	* @return void
	*/
    function getLL($path = "") {
		global $basePath;
        $url = strlen($path)?realpath($basePath . '/' . $path):realpath($basePath . 'typo3conf/ext/phi_newsletter/Resources/Private/Language/locallang.php');

        if (is_file($url)) {
            include($url);
            $this->lang = $LOCAL_LANG;
        }
    }

	/**
	* initStdMarkers
	*
	* @param array $row
	* @param string $lang
	* @return void
	*/
    function initStdMarkers($row,$lang = 0) {
		if($lang == 0 && isset($row["language"])){
			$lang = $row["language"];
		}
		foreach($this->lang[$this->lang_keys[$lang]] as $key=>$val){
			$this->stdMarkers["###" . strtoupper($key)."###"] = utf8_decode($val);
		}
		foreach($this->config as $key=>$val){
			$this->stdMarkers["###" . strtoupper($key)."###"] = utf8_decode($val);
		}
		foreach($row as $key=>$val){
			$this->stdMarkers["###" . strtoupper($key)."###"] = utf8_decode($val);
		}
    }

}
//init the object and run the main actions
$cron = new cronJ();
$cron->init();
$cron->loadMails();
?>
