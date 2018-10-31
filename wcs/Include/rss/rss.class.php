<?php
if (defined('_CLASS_RSS_PHP')) return;
define('_CLASS_RSS_PHP',1);
/**
 *  Class name: RSS
 *  Author    : RedFox
 *  website   : <a href="http://www.foxbat.cn/" target="_blank">http://www.foxbat.cn/</a>
 *  blog      : <a href="http://redsoft.yculblog.com/" target="_blank">http://redsoft.yculblog.com/</a>
 *  CopyRight : RedFox (singlecat@163.com)
 *  说明 : 你可使用它到任意地方，但请保留此信息
 *  使用说明：
 *  $rss = new RSS('RedFox','http://redsoft.yculblog.com/',"RedFox's Blog");
 *  $rss->AddItem('RSS Class',"http://www.xxx.com","xxx",date());
 *  $rss->AddItem(...);
 *  $rss->SaveToFile(...);
 */


class RSS {
   //public
   public $rss_ver = "2.0";
   public $channel_title = '';
   public $channel_link = '';
   public $channel_description = '';
   public $language = 'zh_CN';
   public $copyright = '';
   public $webMaster = '';
   public $pubDate = '';
   public $lastBuildDate = '';
   public $generator = 'RedFox RSS Generator';

   public $content = '';
   public $items = array();

   function RSS($title, $link, $description) {
       $this->channel_title = $title;
       $this->channel_link = $link;
       $this->channel_description = $description;
       $this->pubDate = Date('Y-m-d H:i:s',time());
       $this->lastBuildDate = Date('Y-m-d H:i:s',time());
   }

   function AddItem($title, $link, $description ,$pubDate) {
       $this->items[] = array('titile' => $title ,
                        'link' => $link,
                        'description' => $description, 
                        'pubDate' => $pubDate);
   }

   function BuildRSS() {
       $s = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<rss version=\"2.0\"> \n";
       // start channel
       $s .= "<channel>\n";
       $s .= "<title>{$this->channel_title}</title>\n";
       $s .= "<link>{$this->channel_link}</link>\n";
       $s .= "<description>{$this->channel_description}</description>\n";
       $s .= "<language>{$this->language}</language>\n";
       if (!empty($this->copyright)) {
          $s .= "<copyright>{$this->copyright}</copyright>\n";
       }
       if (!empty($this->webMaster)) {
          $s .= "<webMaster>{$this->webMaster}</webMaster>\n";
       }
       if (!empty($this->pubDate)) {
          $s .= "<pubDate>{$this->pubDate}</pubDate>\n";
       }

       if (!empty($this->lastBuildDate)) {
          $s .= "<lastBuildDate>{$this->lastBuildDate}</lastBuildDate>\n";
       }

       if (!empty($this->generator)) {
          $s .= "<generator>{$this->generator}</generator>\n";
       }
       
       // start items
       for ($i=0;$i<count($this->items);$i++) {
           $s .= "<item>\n";
           $s .= "<title>{$this->items[$i]['title']}</title>\n";
           $s .= "<link>{$this->items[$i]['link']}</link>\n";
           $s .= "<description><![CDATA[{$this->items[$i]['description']}]]></description>\n";
           $s .= "<pubDate>{$this->items[$i]['pubDate']}</pubDate>\n";           
           $s .= "</item>\n";
       }
      
      // close channel
      $s .= "</channel>\n</rss>";
      $this->content = $s;
   }

   function Show() {
       if (empty($this->content)) $this->BuildRSS();
       header('Content-type:text/xml');
       echo($this->content);
   }

   function SaveToFile($fname) {
       if (empty($this->content)) $this->BuildRSS();
       $handle = fopen($fname, 'wb');
       if ($handle === false)  return false;
       fwrite($handle, $this->content);
       fclose($handle);
   }
}

?>