<?php

require 'db.php';

// variabili globali
$global = array();

//connessione al database;
$db = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD) or
		die ('Unable to connect. Check your connection parameters.');
mysql_select_db(MYSQL_DB, $db) or die(mysql_error($db));

// creazione oggetti DOM 
$dom = new DomDocument();
$domHTML = new DomDocument();

// variabili per il <br/>
$br = 4;


// pagina da cui estrarre il contenuto
echo "----> ATLAS <----<br/>";
extractFirstPage('atlas.html', $db, $dom, $domHTML);

require_once('common.php');
cleanDatabase($db);


//**************************************************************************************************************//



function extractFirstPage($iref, $db, $dom, $domHTML){
      
  // inserisco la prima pagina
      $dom = new DomDocument();
      // open if file exists
      if(!$domHTML->loadHTMLFile('./pandius.com/'.$iref)){
	  echo "Cannot open page ".$iref;
	  return;
      }

      $xpath = new DomXPath($domHTML);      
      $nodes = $xpath->query("//title", $domHTML->documentElement);
      $title = $nodes->item(0)->nodeValue;
      $sql = 'INSERT IGNORE INTO index_page 
		  (href, title, author, text, menu)
	      VALUES
		  ("'.$iref.'",
		  "'.mysql_real_escape_string($title, $db).'",
		  NULL,
		  NULL,"1")';	
      mysql_query($sql, $db) or die(mysql_error($db));
      $sql = 'SELECT id 
	  FROM 
	      index_page 
	  WHERE 
	      href="'.mysql_real_escape_string($iref, $db).'"';
      $result = mysql_query($sql, $db);
      if (mysql_num_rows($result) == 1) {
	  //ok
	  $row = mysql_fetch_array($result);
	  $id_index_page = $row['id'];
      }  else {
	  //errore    
      }
      mysql_free_result($result);
      
      
      extractIndex('maps.html', $db, $dom, $domHTML);
      $sql = 'SELECT id 
	  FROM 
	      index_page 
	  WHERE 
	      href="'.mysql_real_escape_string("maps.html", $db).'"';
      $result = mysql_query($sql, $db);
      if (mysql_num_rows($result) == 1) {
	  //ok
	  $row = mysql_fetch_array($result);
	  $id_target_index_page = $row['id'];
      }  else {
	  //errore    
      }
      mysql_free_result($result);
      $sql = 'INSERT IGNORE INTO index_2_content 
			    (id_start_index_page, id_target_index_page, link_name)
			VALUES
			    ("'.$id_index_page.'",
			    "'.$id_target_index_page.'",
			    "'.mysql_real_escape_string("Maps", $db).'")';	
	    mysql_query($sql, $db) or die(mysql_error($db));
      
      
      
      extractBlockquote('planejam.html', $db, $dom, $domHTML);
      $sql = 'SELECT id 
	  FROM 
	      index_page 
	  WHERE 
	      href="'.mysql_real_escape_string("planejam.html", $db).'"';
      $result = mysql_query($sql, $db);
      if (mysql_num_rows($result) == 1) {
	  //ok
	  $row = mysql_fetch_array($result);
	  $id_target_index_page = $row['id'];
      }  else {
	  //errore    
      }
      mysql_free_result($result);
      $sql = 'INSERT IGNORE INTO index_2_content 
			    (id_start_index_page, id_target_index_page, link_name)
			VALUES
			    ("'.$id_index_page.'",
			    "'.$id_target_index_page.'",
			    "'.mysql_real_escape_string("Mystaraspace", $db).'")';	
	    mysql_query($sql, $db) or die(mysql_error($db));
      
      
      extractBlockquoteFake($iref, $db, $dom, $domHTML,"worlds.html");
      $sql = 'SELECT id 
	  FROM 
	      index_page 
	  WHERE 
	      href="'.mysql_real_escape_string("worlds.html", $db).'"';
      $result = mysql_query($sql, $db);
      if (mysql_num_rows($result) == 1) {
	  //ok
	  $row = mysql_fetch_array($result);
	  $id_target_index_page = $row['id'];
      }  else {
	  //errore    
      }
      mysql_free_result($result);
      $sql = 'INSERT IGNORE INTO index_2_content 
			    (id_start_index_page, id_target_index_page, link_name)
			VALUES
			    ("'.$id_index_page.'",
			    "'.$id_target_index_page.'",
			    "'.mysql_real_escape_string("Worlds", $db).'")';	
	    mysql_query($sql, $db) or die(mysql_error($db));
      
      
      
      /*
      // SOTTOZEZIONE WORLDS 
            
      
      //estraggo le sotto-sezioni
      $nodes = $xpath->query("//h1[position()>1]", $domHTML->documentElement);
      //echo $nodes->length;      
      
      $subNodes = array();
      for ($j=0; $j<$nodes->length; $j++) {       	  
	  $subNodes[] = $xpath->query("//blockquote/blockquote[position()=".($j+2)."]/a[contains(@href,'html') and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
      }
      
            
      for ($j=0; $j<$nodes->length; $j++) {       
	  $singleNode = $nodes->item($j);
	  $sql = 'INSERT IGNORE INTO index_page 
		  (href, title, author, text)
	      VALUES
		  ("'.$iref.$j.'",
		  "'.mysql_real_escape_string($singleNode->nodeValue, $db).'",
		  NULL,
		  NULL)';
	   mysql_query($sql, $db) or die(mysql_error($db));	   
	   $sql = 'SELECT id 
	      FROM 
		  index_page 
	      WHERE 
		  href="'.mysql_real_escape_string($iref.$j, $db).'"';
	   $result = mysql_query($sql, $db);
	   if (mysql_num_rows($result) == 1) {
	      //ok
	      $row = mysql_fetch_array($result);
	      $id_target_index_page = $row['id'];
	   }  else {
	      //errore    
	   }  
	   mysql_free_result($result);
	   
	   $sql = 'INSERT IGNORE INTO index_2_content 
			    (id_start_index_page, id_target_index_page, link_name)
			VALUES
			    ("'.$id_index_page.'",
			    "'.$id_target_index_page.'",
			    "'.mysql_real_escape_string($name, $db).'")';	
	    mysql_query($sql, $db) or die(mysql_error($db));
	    
	    
	    
	 //   $subNodes[] = $xpath->query("//blockquote/blockquote[position()=".($j+3)."]/a[contains(@href,'html') and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
	 //   $subNodes[] = $xpath->query("//blockquote[position()=".($j+3)."]/a[contains(@href,'html') and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
	      
	    //inizio parte comune
	    echo "j: ".($j+2)."<br/>";
	   
	    echo "--- subnodes: ".($subNodes[$j]->length)."<br/>";
	    for ($z=0; $z<$subNodes[$j]->length; $z++) {        	    
	    $singleSubNode = $subNodes[$j]->item($z);  
	    $ref = $singleSubNode->attributes->getNamedItem('href')->nodeValue;
	    $name = $singleSubNode->nodeValue;
	    if (!(in_array($ref, $GLOBALS['global']))) {
		  //inserisco il ref nell'array'
		  $GLOBALS['global'][] = $ref;
		  
		  // METTO QUI LA CHIAMATA A extractContentPage.php //
		  if (extractContent($ref, $db, $dom, $domHTML)){
			    
		      // *** LA PAGINA ESAMINATA SI è RIVELATA EFFETTIVAMENTE UNA PAGINA CONTENT ***
		      // ottengo l'id della pagina content
		      $sql = 'SELECT id 
			  FROM 
			      content_page 
			  WHERE 
			      href="'.mysql_real_escape_string($ref, $db).'"';
		      $result = mysql_query($sql, $db);
		      if (mysql_num_rows($result) == 1) {
			  //ok
			  $row = mysql_fetch_array($result);
			  $id_target_content_page = $row['id'];
		      } else {
			  //errore    
		      }
		      mysql_free_result($result);


		      // ORA AGGIUNGO IL LINK TRA LE DUE PAGINE
		      $sql = 'INSERT IGNORE INTO index_2_content 
			      (id_start_index_page, id_target_content_page, link_name)
			  VALUES
			      ("'.$id_target_index_page.'",
			      "'.$id_target_content_page.'",
			      "'.mysql_real_escape_string($name, $db).'")';	
		      mysql_query($sql, $db) or die(mysql_error($db));
		  } else {
			    
		    // *** LA PAGINA ESAMINATA SI è RIVELATA INVECE UNA PAGINA INDEX ***
		    $sql = 'SELECT id 
			  FROM 
			      index_page 
			  WHERE 
			      href="'.mysql_real_escape_string($ref, $db).'"';
		      $result = mysql_query($sql, $db);
		      if (mysql_num_rows($result) == 1) {
			  //ok
			  $row = mysql_fetch_array($result);
			  $id_new_target_index_page = $row['id'];
		      } else {
			  //errore    
		      }
		      mysql_free_result($result);


		      // ORA AGGIUNGO IL LINK TRA LE DUE PAGINE
		      $sql = 'INSERT IGNORE INTO index_2_content 
			      (id_start_index_page, id_target_index_page, link_name)
			  VALUES
			      ("'.$id_target_index_page.'",
			      "'.$id_new_target_index_page.'",
			      "'.mysql_real_escape_string($name, $db).'")';	
		      mysql_query($sql, $db) or die(mysql_error($db));

		  }
	    
		}
	    }
	    //fine parte comune
      }
      */
}


function extractBlockquote($iref, $db, $dom, $domHTML){
  
  // inserisco la prima pagina
      $dom = new DomDocument();
      // open if file exists
      if(!$domHTML->loadHTMLFile('./pandius.com/'.$iref)){
	  echo "Cannot open page ".$iref;
	  return;
      }

      $xpath = new DomXPath($domHTML);      
      $nodes = $xpath->query("//title", $domHTML->documentElement);
      $title = $nodes->item(0)->nodeValue;
      $sql = 'INSERT IGNORE INTO index_page 
		  (href, title, author, text)
	      VALUES
		  ("'.$iref.'",
		  "'.mysql_real_escape_string($title, $db).'",
		  NULL,
		  NULL)';	
      mysql_query($sql, $db) or die(mysql_error($db));
      $sql = 'SELECT id 
	  FROM 
	      index_page 
	  WHERE 
	      href="'.mysql_real_escape_string($iref, $db).'"';
      $result = mysql_query($sql, $db);
      if (mysql_num_rows($result) == 1) {
	  //ok
	  $row = mysql_fetch_array($result);
	  $id_index_page = $row['id'];
      }  else {
	  //errore    
      }
      mysql_free_result($result);
      
      
      
      
      //estraggo le sotto-sezioni
      $nodes = $xpath->query("//h2", $domHTML->documentElement);
      //echo $nodes->length;      
      $subNodes = array();
      $nodesText = array();
      
      for ($j=0; $j<$nodes->length; $j++) {
	//DEBUG: la seguente query sistemerebbe l'index di Aelos, ma salverebbe jpg e pdf come testo
	  if($j==0){
	  $subNodes[]= $xpath->query("//blockquote/blockquote[position()=1]/a[(contains(@href,'html') or(contains(@href,'pdf')) or(contains(@href,'jpg'))) and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
	  } else {
	  $subNodes[] = $xpath->query("//blockquote/blockquote[position()=".($j+1)."]/a[contains(@href,'html') and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
	  $nodesText[] = $xpath->query("//blockquote/blockquote[position()=".($j+1)."]", $domHTML->documentElement);	
      }
	  }
      
      $list = array();
      for ($j=0; $j<$nodes->length; $j++){
	for ($i=0; $i<$nodesText[$j]->length; $i++){
	  $list[$j] = explode(".",$nodesText[$j]->item($i)->nodeValue);
	}
      }
      
      for ($j=0; $j<$nodes->length; $j++) {       
	  $singleNode = $nodes->item($j);
	  
	  $dom = new DomDocument();
	  for ($i=0; $i<$nodesText[$j]->length; $i++) {        
	      $singleNodeText = $nodesText[$j]->item($i);
	      //$p = $dom->createElement($singleNode->nodeName);    	  
	      $p = $dom->createElement("xml");
	      $GLOBALS['br'] = 0;
	      exploreIndex($dom, $p, $singleNodeText);	  
	      $dom->appendChild($dom->createElement("br"));
	      $bq = $dom->createElement("blockquote");
	      $bq->appendChild($p);
	      $dom->appendChild($bq);
	  }
	  $text = $dom->saveHTML();
	  
	  
	  $sql = 'INSERT IGNORE INTO index_page 
		  (href, title, author, text)
	      VALUES
		  ("'.$iref.$j.'",
		  "'.mysql_real_escape_string($singleNode->nodeValue, $db).'",
		  NULL,
		  "'.mysql_real_escape_string($text, $db).'")';
		  
	   mysql_query($sql, $db) or die(mysql_error($db));	   
	   $sql = 'SELECT id 
	      FROM 
		  index_page 
	      WHERE 
		  href="'.mysql_real_escape_string($iref.$j, $db).'"';
	   $result = mysql_query($sql, $db);
	   if (mysql_num_rows($result) == 1) {
	      //ok
	      $row = mysql_fetch_array($result);
	      $id_target_index_page = $row['id'];
	   }  else {
	      //errore    
	   }  
	   mysql_free_result($result);
	   
	   $sql = 'INSERT IGNORE INTO index_2_content 
			    (id_start_index_page, id_target_index_page, link_name)
			VALUES
			    ("'.$id_index_page.'",
			    "'.$id_target_index_page.'",
			    "'.mysql_real_escape_string($singleNode->nodeValue, $db).'")';	
	    mysql_query($sql, $db) or die(mysql_error($db));
	    
	    
	    
	 //   $subNodes[] = $xpath->query("//blockquote/blockquote[position()=".($j+3)."]/a[contains(@href,'html') and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
	 //   $subNodes[] = $xpath->query("//blockquote[position()=".($j+3)."]/a[contains(@href,'html') and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
	      
	    //inizio parte comune
	    echo "j: ".($j+2)."<br/>";
	   
	    echo "--- subnodes: ".($subNodes[$j]->length)."<br/>";
	    for ($z=0; $z<$subNodes[$j]->length; $z++) {        	    
	    $singleSubNode = $subNodes[$j]->item($z);  
	    $ref = $singleSubNode->attributes->getNamedItem('href')->nodeValue;
	    $name = $singleSubNode->nodeValue;
		
		// estraggo autori e testo contenente from date per linkAtFile
		$tmpNode = $subNodes[$j]->item($z);
		$tmpNode = $tmpNode->nextSibling;
		$artAuthor = array();
		while(!strpos($tmpNode->nodeValue, ".")) {
			if($tmpNode->nodeName=="a") {
				$artAuthor[] = $tmpNode->nodeValue;
			}
			$tmpNode = $tmpNode->nextSibling;
		}
		$artText = $tmpNode->nodeValue;
		
	    $info = extractInfo($artText, $name);
	    
	    if (!(in_array($ref, $GLOBALS['global']))) {
		  //inserisco il ref nell'array'
		  $GLOBALS['global'][] = $ref;
		  
		  // L'ARTICOLO CHIAMA UN FILE E NON UNA PAGINA ALLORA LA CREO
		  if(!strpos($ref, ".html")){
			linkAtFile($name, $ref, $artAuthor, $info, $db, $id_target_index_page);
		  }
			
		  // METTO QUI LA CHIAMATA A extractContentPage.php //
		  else if (extractContent($ref, $db, $dom, $domHTML, $info)){
			    
		      // *** LA PAGINA ESAMINATA SI è RIVELATA EFFETTIVAMENTE UNA PAGINA CONTENT ***
		      // ottengo l'id della pagina content
		      $sql = 'SELECT id 
			  FROM 
			      content_page 
			  WHERE 
			      href="'.mysql_real_escape_string($ref, $db).'"';
		      $result = mysql_query($sql, $db);
		      if (mysql_num_rows($result) == 1) {
			  //ok
			  $row = mysql_fetch_array($result);
			  $id_target_content_page = $row['id'];
		      } else {
			  //errore    
		      }
		      mysql_free_result($result);


		      // ORA AGGIUNGO IL LINK TRA LE DUE PAGINE
		      $sql = 'INSERT IGNORE INTO index_2_content 
			      (id_start_index_page, id_target_content_page, link_name)
			  VALUES
			      ("'.$id_target_index_page.'",
			      "'.$id_target_content_page.'",
			      "'.mysql_real_escape_string($name, $db).'")';	
		      mysql_query($sql, $db) or die(mysql_error($db));
		  } else {
			    
		    // *** LA PAGINA ESAMINATA SI è RIVELATA INVECE UNA PAGINA INDEX ***
		    $sql = 'SELECT id 
			  FROM 
			      index_page 
			  WHERE 
			      href="'.mysql_real_escape_string($ref, $db).'"';
		      $result = mysql_query($sql, $db);
		      if (mysql_num_rows($result) == 1) {
			  //ok
			  $row = mysql_fetch_array($result);
			  $id_new_target_index_page = $row['id'];
		      } else {
			  //errore    
		      }
		      mysql_free_result($result);


		      // ORA AGGIUNGO IL LINK TRA LE DUE PAGINE
		      $sql = 'INSERT IGNORE INTO index_2_content 
			      (id_start_index_page, id_target_index_page, link_name)
			  VALUES
			      ("'.$id_target_index_page.'",
			      "'.$id_new_target_index_page.'",
			      "'.mysql_real_escape_string($name, $db).'")';	
		      mysql_query($sql, $db) or die(mysql_error($db));

		  }
	    
		}
	    }
	    //fine parte comune
      } 
}

function linkAtFile($title, $ref, $author, $info, $db, $id_target_index_page) {
    $text = '</br><a href="'.$ref.'">LINK FILE<a>';
    if($info['from'] != null && (strlen($info['from']) > 25) || strstr($info['from'],"by")) {
        echo "FROM FIELD DELETED<br/>";
        $info['from'] = null;
    }
    if($info['date'] != null && $info['from'] != null) {
        $sql = 'INSERT IGNORE INTO content_page
               (href, title, source, submit_date, publish_date, is_published, text)
               VALUES
               ("'.$ref.'",
               "'.mysql_real_escape_string($title, $db).'",
               "'.$info['from'].'",
               "'.date('Y-m-d').'",
               "'.$info['date'].'",
               TRUE,
               "'.mysql_real_escape_string($text, $db).'")';
    } else if($info['date'] != null && $info['from'] == null) {
        $sql = 'INSERT IGNORE INTO content_page
               (href, title, submit_date, publish_date, is_published, text)
               VALUES
               ("'.$ref.'",
               "'.mysql_real_escape_string($title, $db).'",
               "'.date('Y-m-d').'",
               "'.$info['date'].'",
               TRUE,
               "'.mysql_real_escape_string($text, $db).'")';
    } else {
        $sql = 'INSERT IGNORE INTO content_page
               (href, title, submit_date, is_published, text)
               VALUES
               ("'.$ref.'",
               "'.mysql_real_escape_string($title, $db).'",
               "'.date('Y-m-d').'",
               TRUE,
               "'.mysql_real_escape_string($text, $db).'")';
    }
    mysql_query($sql, $db) or die(mysql_error($db));
    $lastInseredContent = mysql_insert_id();
    foreach($author as $name) {
        //fix degli utenti che danno problemi
        require_once('common.php');
        $name = fixUser($name);
        $sql = 'SELECT id
               FROM
               users
               WHERE
               name="'.mysql_real_escape_string($name, $db).'"';
        $result = mysql_query($sql, $db);

        if (mysql_num_rows($result) == 1) {
            //ok
            $row = mysql_fetch_array($result);
            $author = $row['id'];
        }  else {
            //errore
        }
        mysql_free_result($result);

        $sql = 'INSERT IGNORE INTO content_page_author
               (contentPage, author)
               VALUES
               ("'.$lastInseredContent.'",
               "'.$author.'")';
        mysql_query($sql, $db) or die(mysql_error($db));
    }

	// ORA AGGIUNGO IL LINK TRA LE DUE PAGINE
	$sql = 'INSERT IGNORE INTO index_2_content 
	  (id_start_index_page, id_target_content_page, link_name)
	VALUES
	  ("'.$id_target_index_page.'",
	  "'.$lastInseredContent.'",
	  "'.mysql_real_escape_string($title, $db).'")';	
	mysql_query($sql, $db) or die(mysql_error($db));
}



function extractBlockquoteFake($iref, $db, $dom, $domHTML, $fakeref){
  
  // inserisco la prima pagina
      $dom = new DomDocument();
      // open if file exists
      if(!$domHTML->loadHTMLFile('./pandius.com/'.$iref)){
	  echo "Cannot open page ".$iref;
	  return;
      }

      $xpath = new DomXPath($domHTML);      
      $nodes = $xpath->query("//title", $domHTML->documentElement);
      $title = $nodes->item(0)->nodeValue;
      // la ecommerciale da' problemi, non viene caricato il nodo
      $title = str_replace("&","and",$title);
      
      $sql = 'INSERT IGNORE INTO index_page 
		  (href, title, author, text)
	      VALUES
		  ("'.$fakeref.'",
		  "'.mysql_real_escape_string($title, $db).'",
		  NULL,
		  NULL)';	
      mysql_query($sql, $db) or die(mysql_error($db));
      $sql = 'SELECT id 
	  FROM 
	      index_page 
	  WHERE 
	      href="'.mysql_real_escape_string($fakeref, $db).'"';
      $result = mysql_query($sql, $db);
      if (mysql_num_rows($result) == 1) {
	  //ok
	  $row = mysql_fetch_array($result);
	  $id_index_page = $row['id'];
      }  else {
	  //errore    
      }
      mysql_free_result($result);
      
      echo "<br/>-------------<br/>";
      echo "WORLD --> $iref<br/>"; 
      
      
      //estraggo le sotto-sezioni
      $nodes = $xpath->query("//h1", $domHTML->documentElement);
      //DEBUG: la seguente query estrae le 'regioni' di 'Outer World'
      $subCategory = $xpath->query("//body/blockquote/blockquote[position()=".($j+2)."]/blockquote/h2 | //body/blockquote/blockquote[position()=".($j+2)."]/h2", $domHTML->documentElement);
      echo "<b>number of world: ".($nodes->length-1)."</b>";      
      for($i=1;$i<$nodes->length;$i++){
	echo "<br/>world: ".$nodes->item($i)->nodeValue;
      }
      echo "<br/>";
      foreach($subCategory as $c){
	echo "<br/>region: ".$c->nodeValue;
      }
      echo "<br/>";
      
      $subNodes = array();
      $nodesText = array();
      for ($j=0; $j<$nodes->length-1; $j++) {       	  
	  $subNodes[] = $xpath->query("//body/blockquote/blockquote[position()=".($j+2)."]//a[contains(@href,'html') and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
	  $nodesText[] = $xpath->query("//body/blockquote/blockquote[position()=".($j+2)."]", $domHTML->documentElement);	
      }
            
      for ($j=0; $j<$nodes->length-1; $j++) {       
	  $singleNode = $nodes->item($j+1);
	  
	  $dom = new DomDocument();
	  for ($i=0; $i<$nodesText[$j]->length; $i++) {        
	      $singleNodeText = $nodesText[$j]->item($i);
	      //$p = $dom->createElement($singleNode->nodeName);    	  
	      $p = $dom->createElement("xml");
	      $GLOBALS['br'] = 0;
	      exploreIndexH2($dom, $p, $singleNodeText);	  
	      $dom->appendChild($dom->createElement("br"));
//	      $bq = $dom->createElement("blockquote");
//	      $bq->appendChild($p);
//	      $dom->appendChild($bq);
	       $dom->appendChild($p);
	  }
	  $text = $dom->saveHTML();
	  
	  
	  $sql = 'INSERT IGNORE INTO index_page 
		  (href, title, author, text)
	      VALUES
		  ("'.$fakeref.$j.'",
		  "'.mysql_real_escape_string($singleNode->nodeValue, $db).'",
		  NULL,
		  "'.mysql_real_escape_string($text, $db).'")';
		  
	   mysql_query($sql, $db) or die(mysql_error($db));	   
	   $sql = 'SELECT id 
	      FROM 
		  index_page 
	      WHERE 
		  href="'.mysql_real_escape_string($fakeref.$j, $db).'"';
	   $result = mysql_query($sql, $db);
	   if (mysql_num_rows($result) == 1) {
	      //ok
	      $row = mysql_fetch_array($result);
	      $id_target_index_page = $row['id'];
	   }  else {
	      //errore    
	   }  
	   mysql_free_result($result);
	   
	   $sql = 'INSERT IGNORE INTO index_2_content 
			    (id_start_index_page, id_target_index_page, link_name)
			VALUES
			    ("'.$id_index_page.'",
			    "'.$id_target_index_page.'",
			    "'.mysql_real_escape_string($singleNode->nodeValue, $db).'")';	
	    mysql_query($sql, $db) or die(mysql_error($db));
	    
	    
	    
	 //   $subNodes[] = $xpath->query("//blockquote/blockquote[position()=".($j+3)."]/a[contains(@href,'html') and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
	 //   $subNodes[] = $xpath->query("//blockquote[position()=".($j+3)."]/a[contains(@href,'html') and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
	      
	    //inizio parte comune
	    echo "j: ".($j+2)."<br/>";
	   
	    echo "--- subnodes: ".($subNodes[$j]->length)."<br/>";
	    for ($z=0; $z<$subNodes[$j]->length; $z++) {        	    
	    $singleSubNode = $subNodes[$j]->item($z);  
	    $ref = $singleSubNode->attributes->getNamedItem('href')->nodeValue;
	    $name = $singleSubNode->nodeValue;
	    if (!(in_array($ref, $GLOBALS['global']))) {
		  //inserisco il ref nell'array'
		  $GLOBALS['global'][] = $ref;
		  
		  // METTO QUI LA CHIAMATA A extractContentPage.php //
		  if (extractContent($ref, $db, $dom, $domHTML)){
			    
		      // *** LA PAGINA ESAMINATA SI è RIVELATA EFFETTIVAMENTE UNA PAGINA CONTENT ***
		      // ottengo l'id della pagina content
		      $sql = 'SELECT id 
			  FROM 
			      content_page 
			  WHERE 
			      href="'.mysql_real_escape_string($ref, $db).'"';
		      $result = mysql_query($sql, $db);
		      if (mysql_num_rows($result) == 1) {
			  //ok
			  $row = mysql_fetch_array($result);
			  $id_target_content_page = $row['id'];
		      } else {
			  //errore    
		      }
		      mysql_free_result($result);


		      // ORA AGGIUNGO IL LINK TRA LE DUE PAGINE
		      $sql = 'INSERT IGNORE INTO index_2_content 
			      (id_start_index_page, id_target_content_page, link_name)
			  VALUES
			      ("'.$id_target_index_page.'",
			      "'.$id_target_content_page.'",
			      "'.mysql_real_escape_string($name, $db).'")';	
		      mysql_query($sql, $db) or die(mysql_error($db));
		  } else {
			    
		    // *** LA PAGINA ESAMINATA SI è RIVELATA INVECE UNA PAGINA INDEX ***
		    $sql = 'SELECT id 
			  FROM 
			      index_page 
			  WHERE 
			      href="'.mysql_real_escape_string($ref, $db).'"';
		      $result = mysql_query($sql, $db);
		      if (mysql_num_rows($result) == 1) {
			  //ok
			  $row = mysql_fetch_array($result);
			  $id_new_target_index_page = $row['id'];
		      } else {
			  //errore    
		      }
		      mysql_free_result($result);


		      // ORA AGGIUNGO IL LINK TRA LE DUE PAGINE
		      $sql = 'INSERT IGNORE INTO index_2_content 
			      (id_start_index_page, id_target_index_page, link_name)
			  VALUES
			      ("'.$id_target_index_page.'",
			      "'.$id_new_target_index_page.'",
			      "'.mysql_real_escape_string($name, $db).'")';	
		      mysql_query($sql, $db) or die(mysql_error($db));

		  }
	    
		}
	    }
	    //fine parte comune
      }
            	  echo "-------------<br/><br/>";
      
      
      
}



function extractIndex($iref, $db, $dom, $domHTML){
      
      $dom = new DomDocument();
      // open if file exists
      if(!$domHTML->loadHTMLFile('./pandius.com/'.$iref)){
	  echo "Cannot open page ".$iref;
	  return;
      }

      $xpath = new DomXPath($domHTML);
      
      $nodesH2 = $xpath->query("//h2", $domHTML->documentElement);
      if ($nodesH2->length == 1){
	  $title = $nodesH2->item(0)->nodeValue;
      } else {
	  $nodes = $xpath->query("//title", $domHTML->documentElement);
	  $title = $nodes->item(0)->nodeValue;
      }
      // la ecommerciale da' problemi, non viene caricato il nodo
      $title = str_replace("&","and",$title);
      
      echo "<br/>-------------<br/>";
      echo "INDEX --> $iref<br/>"; 
      
      $subNodes = array();
      $nodesText = array();
      $nodesText = $xpath->query("//body/blockquote/blockquote", $domHTML->documentElement);
      
      $list = array();
      for ($i=0; $i<$nodesText->length; $i++){
	  $list[$i] = explode(".",$nodesText->item($i)->nodeValue);
      }
      
      $nodes = $xpath->query("//body", $domHTML->documentElement);
      for ($i=0; $i<$nodes->length; $i++) {        
	  $singleNode = $nodes->item($i);
	  //$p = $dom->createElement($singleNode->nodeName);    	  
	  $p = $dom->createElement("xml");
	  $GLOBALS['br'] = 4;
	  exploreIndex($dom, $p, $singleNode);	  
	  $dom->appendChild($p);
      }
      $text = $dom->saveHTML();
           
      $nodes = $xpath->query("//a[contains(@href,'authors')]", $domHTML->documentElement);
      if ($nodes->length > 0){
	    $name = trim($nodes->item(0)->nodeValue);
	    $sql = 'SELECT id 
		      FROM 
			  users 
		      WHERE 
			  name="'.mysql_real_escape_string($name, $db).'"';
	    $result = mysql_query($sql, $db);

	    if (mysql_num_rows($result) == 1) {
		//ok
		$row = mysql_fetch_array($result);
		$author = $row['id'];
		
		$sql = 'INSERT IGNORE INTO index_page 
			(href, title, author, text)
		    VALUES
			("'.$iref.'",
			"'.mysql_real_escape_string($title, $db).'",
			"'.$author.'",
			"'.mysql_real_escape_string($text, $db).'")';	
		mysql_query($sql, $db) or die(mysql_error($db));
	    }  else {
		//errore    
	    }
      } else {
	    $author = null;
	    $sql = 'INSERT IGNORE INTO index_page 
			(href, title, author, text)
		    VALUES
			("'.$iref.'",
			"'.mysql_real_escape_string($title, $db).'",
			NULL,
			"'.mysql_real_escape_string($text, $db).'")';	
		mysql_query($sql, $db) or die(mysql_error($db));
      }
                

      $sql = 'SELECT id 
	  FROM 
	      index_page 
	  WHERE 
	      href="'.mysql_real_escape_string($iref, $db).'"';
      $result = mysql_query($sql, $db);
      if (mysql_num_rows($result) == 1) {
	  //ok
	  $row = mysql_fetch_array($result);
	  $id_index_page = $row['id'];
      }  else {
	  //errore    
      }
      mysql_free_result($result);

            
      $nodes = $xpath->query("//a[(contains(@href,'html') or contains(@href,'jpg') or contains(@href,'gif') or contains(@href,'pdf')) and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
      
      if (($iref == 'stories.html')or($iref == 'atlas.html')or($iref == 'resource.html')or($iref == 'adv_camp.html')) 
	  {$start=8;} else {$start=9;}

      for ($i=$start; $i<$nodes->length; $i++) {        
	  $singleNode = $nodes->item($i);  
	  $ref = $singleNode->attributes->getNamedItem('href')->nodeValue;
	  $name = $singleNode->nodeValue;
	  
	  // estraggo autori e testo contenente from date per linkAtFile
	  $tmpNode = $nodes->item($i);
	  $tmpNode = $tmpNode->nextSibling;
	  $artAuthor = array();
	  while(!strpos($tmpNode->nodeValue, ".") && $tmpNode!=null) {
		if($tmpNode->nodeName=="a") {
			$artAuthor[] = $tmpNode->nodeValue;
		}
		$tmpNode = $tmpNode->nextSibling;
	  }
	  $artText = $tmpNode->nodeValue;
	  $info = extractInfo($artText, $name);
	  
	  if (!(in_array($ref, $GLOBALS['global']))) {
		
		//inserisco il ref nell'array'
		$GLOBALS['global'][] = $ref;
		
		// L'ARTICOLO CHIAMA UN FILE E NON UNA PAGINA ALLORA LA CREO
		if(!strpos($ref, ".html")){
			if($name=="map"){
				$name = $name.$nodes->item($i)->nextSibling->nodeValue;
			}
			linkAtFile($name, $ref, $artAuthor, $info, $db, $id_index_page);
		}
		
		// METTO QUI LA CHIAMATA A extractContentPage.php //
		else if (extractContent($ref, $db, $dom, $domHTML,$info)){
			  
		    // *** LA PAGINA ESAMINATA SI è RIVELATA EFFETTIVAMENTE UNA PAGINA CONTENT ***
		    // ottengo l'id della pagina content
		    $sql = 'SELECT id 
			FROM 
			    content_page 
			WHERE 
			    href="'.mysql_real_escape_string($ref, $db).'"';
		    $result = mysql_query($sql, $db);
		    if (mysql_num_rows($result) == 1) {
			//ok
			$row = mysql_fetch_array($result);
			$id_target_content_page = $row['id'];
		    } else {
			//errore    
		    }
		    mysql_free_result($result);


		    // ORA AGGIUNGO IL LINK TRA LE DUE PAGINE
		    $sql = 'INSERT IGNORE INTO index_2_content 
			    (id_start_index_page, id_target_content_page, link_name)
			VALUES
			    ("'.$id_index_page.'",
			    "'.$id_target_content_page.'",
			    "'.mysql_real_escape_string($name, $db).'")';	
		    mysql_query($sql, $db) or die(mysql_error($db));
		} else {
			  
		  // *** LA PAGINA ESAMINATA SI è RIVELATA INVECE UNA PAGINA INDEX ***
		  $sql = 'SELECT id 
			FROM 
			    index_page 
			WHERE 
			    href="'.mysql_real_escape_string($ref, $db).'"';
		    $result = mysql_query($sql, $db);
		    if (mysql_num_rows($result) == 1) {
			//ok
			$row = mysql_fetch_array($result);
			$id_target_index_page = $row['id'];
		    } else {
			//errore    
		    }
		    mysql_free_result($result);


		    // ORA AGGIUNGO IL LINK TRA LE DUE PAGINE
		    $sql = 'INSERT IGNORE INTO index_2_content 
			    (id_start_index_page, id_target_index_page, link_name)
			VALUES
			    ("'.$id_index_page.'",
			    "'.$id_target_index_page.'",
			    "'.mysql_real_escape_string($name, $db).'")';	
		    mysql_query($sql, $db) or die(mysql_error($db));

		}
	  
	  }
      }
      	  echo "-------------<br/><br/>";


  // max_allowed_packet = 1M */
  
}


function extractInfo($article,$name){
    $info = array( 'from' => null,'date' => null);
    if(strstr($article,'from') && strstr($article,'posted')){
	/*DEBUG*/$fullName = trim(substr($article,0,strpos($article,"from")));
	$info['from'] = trim(substr($article,strpos($article,"from")+4,(strpos($article,"posted")-strpos($article,"from")-4)));
	$info['date'] = date('Y-m-d',strtotime(trim(substr($article,strpos($article,"posted")+6))));
	if(strstr($info['from'],'from') || strstr($info['from'],'posted')){
	  if(strstr($info['from'],"the Mystara Message Board")){
	    $info['from'] = "the Mystara Message Board";
	  }else if(strstr($info['from'],"The Piazza")){
	    $info['from'] = "The Piazza";
	  }else if(strstr($info['from'],"the Mystara Mailing List")){
	    $info['from'] = "the Mystara Mailing List";
	  }
	}
    }else if(strstr($article,'from') && strstr($article,'current as of')){
	/*DEBUG*/$fullName = trim(substr($article,0,strpos($article,"from")));
	$info['from'] = trim(substr($article,strpos($article,"from")+4,(strpos($article,"current as of")-strpos($article,"from")-4)));
	$info['date'] = date('Y-m-d',strtotime(trim(substr($article,strpos($article,"current as of")+13))));
    }else if(strstr($article,'from')){
	/*DEBUG*/$fullName = trim(substr($article,0,strpos($article,"from")));
	$from = trim(substr($article,strpos($article,"from")+4));
	if(strstr($from,"the Mystara Message Board")){
	    $date = substr($from,25);
	}else if(strstr($from,"The Piazza")){
	    $date = substr($from,10);
	}else if(strstr($from,"the Mystara Mailing List")){
	    $date = substr($from,24);		
	}else if(strstr($from,"the Mystara News Server")){
	    $date = substr($from,23);		
	}
	$info['from'] = substr($from,0,(strlen($from)-strlen($date)));
	$info['date'] = date('Y-m-d',strtotime(trim($date)));
    }else if(strstr($article,'current as of')){
	/*DEBUG*/ $fullName = trim(substr($article,0,strpos($article,"current as of")));
	$info['date']= date('Y-m-d',strtotime(trim(substr($article,strpos($article,"current as of")+13))));
    }else if(strstr($article,'last section updated')){
	/*DEBUG*/ $fullName = trim(substr($article,0,strpos($article,"last section updated")));
	$info['date']= date('Y-m-d',strtotime(trim(substr($article,strpos($article,"last section updated")+20))));
    }
    
    /*DEBUG*/ echo "<b>name:</b> ".$name." <b>from:</b> ".$info['from']." <b>date:</b> ".$info['date']."<br/>";
    
    return $info;
}


function extractContent($ref, $db, $dom, $domHTML, $info){
      
      $dom = new DomDocument();
      // open if file exists
      if(!$domHTML->loadHTMLFile('./pandius.com/'.$ref)){
	echo "Cannot open page ".$ref;
	return;
      }
            
      $xpath = new DomXPath($domHTML);

      $nodes = $xpath->query("//p | //ul  | //ol | //table", $domHTML->documentElement);
      $nodesA = $xpath->query("//a[contains(@href,'html') and not(contains(@href,'authors')) and not(contains(@href,'#'))]", $domHTML->documentElement);
      
      
      //se non ci sono <p> allora mi trovo in una index page e quindi devo chiamare la extractIndex e terminare
      if ( (($nodes->length <= 1) and ($nodesA->length >= 11)) or ( ($nodes->length >= 2) and ((($nodesA->length) >= 5* ($nodes->length)))) ){
	  
//	  echo 'INDEX: P = '.$nodes->length.', A = '.$nodesA->length.' --> '.$ref.'<br/>';
	  
	  extractIndex($ref, $db, $dom, $domHTML);
	  return false;
      }
      
//      echo 'CONTENT: P = '.$nodes->length.', A = '.$nodesA->length.' --> '.$ref.'<br/>';

      for ($i=1; $i<$nodes->length; $i++) {        
	  $singleNode = $nodes->item($i);
	  if($singleNode->nodeName== "p"){
	    $p = $dom->createElement('p');    	  
	    explore($dom, $p, $singleNode);
	    $dom->appendChild($p);
	  }else if($singleNode->nodeName == "ul"){
	    $ul = $dom->createElement('ul');    	  
	    explore($dom, $ul, $singleNode);
	    $dom->appendChild($ul);
	  }else if($singleNode->nodeName == "ol"){
	    $ol = $dom->createElement('ol');    	  
	    explore($dom, $ol, $singleNode);
	    $dom->appendChild($ol);
	  }else if($singleNode->nodeName == "table"){
	    $table = $dom->createElement('table');    	  
	    explore($dom, $table, $singleNode);
	    $dom->appendChild($table);
	  }
      }
      $text = $dom->saveHTML();
      
      //$nodes = $xpath->query("//h2", $domHTML->documentElement);
      $nodes = $xpath->query("//title", $domHTML->documentElement);
      $title = $nodes->item(0)->nodeValue;
      // la ecommerciale da' problemi, non viene caricato il nodo
      $title = str_replace("&","and",$title);
           
      //aggiungo l'articolo
      //filtro il campo from per eliminare falsi positivi
      if($info['from'] != null && (strlen($info['from']) > 26) || strstr($info['from'],"by")){
	echo "FROM FIELD DELETED<br/>";
	$info['from'] = null;
      }
      if($info['date'] != null && $info['from'] != null){
	$sql = 'INSERT IGNORE INTO content_page 
	      (href, title, source, submit_date, publish_date, is_published, text)
	  VALUES
	      ("'.$ref.'",
	      "'.mysql_real_escape_string($title, $db).'",
	      "'.$info['from'].'",
	      "'.date('Y-m-d').'",
	      "'.$info['date'].'",
	      TRUE,
	      "'.mysql_real_escape_string($text, $db).'")';
      }else if($info['date'] != null && $info['from'] == null){
	$sql = 'INSERT IGNORE INTO content_page 
	      (href, title, submit_date, publish_date, is_published, text)
	  VALUES
	      ("'.$ref.'",
	      "'.mysql_real_escape_string($title, $db).'",
	      "'.date('Y-m-d').'",
	      "'.$info['date'].'",
	      TRUE,
	      "'.mysql_real_escape_string($text, $db).'")';
      }else{
	$sql = 'INSERT IGNORE INTO content_page 
	      (href, title, submit_date, is_published, text)
	  VALUES
	      ("'.$ref.'",
	      "'.mysql_real_escape_string($title, $db).'",
	      "'.date('Y-m-d').'",
	      TRUE,
	      "'.mysql_real_escape_string($text, $db).'")';
      }
      mysql_query($sql, $db) or die(mysql_error($db));
      $lastInseredContent = mysql_insert_id();

      //scorro tutti gli autori e li aggungo
      $nodes = $xpath->query("//a[contains(@href,'authors')]", $domHTML->documentElement);
      
      //controlla autori ripetuti e inserisce nel db
      require_once('common.php');
   	  addAuthors($nodes, $db, $lastInseredContent);
    
      return true;
}




function exploreIndex($dom, $fatherElement, $fatherNode){
            
      foreach ($fatherNode->childNodes as $childNode){
	  if ($childNode->nodeName != "center" && $childNode->nodeName != "h2" && $childNode->nodeName != "h1"){	    	  
	      if($childNode->hasChildNodes()){
		  if ($childNode->nodeName == "a" && strpos("dkaj".$childNode->attributes->getNamedItem("href")->nodeValue, "authors.html")){
		    exploreIndex($dom, $fatherElement, $childNode);
		    continue;
		  }
		  if ($childNode->nodeName == "a"){
		      $childElement = $dom->createElement($childNode->nodeName);		      
		      if($childNode->hasAttributes()){
			  foreach ($childNode->attributes as $attribute){
			      $childElement->setAttribute($attribute->name, "#");
			      $childElement->setAttribute("onclick", "linkTo('".$attribute->value."'); return false");
			  }
		      }
		      exploreIndex($dom, $childElement, $childNode);	      
		      $fatherElement->appendChild($childElement);    
		  } else {
		      $childElement = $dom->createElement($childNode->nodeName);
		      if($childNode->hasAttributes()){
			  foreach ($childNode->attributes as $attribute){
			      $childElement->setAttribute($attribute->name, $attribute->value);
			  }
		      }   	      
		      exploreIndex($dom, $childElement, $childNode);	      
		      $fatherElement->appendChild($childElement);
		  }   
	      } else {
		  //allora è un nodo testo	      
		  if ($childNode->nodeType == 3){ //costante del tipo testo
		    $childElement = $dom->createTextNode($childNode->nodeValue);
		    $fatherElement->appendChild($childElement);   
		  } else {
		  //o un nodo senza figli
		    if ($childNode->nodeType != 8){
		      //i commenti mi danno problemi quindi non li considero
		      if ($childNode->nodeName == "br") {
			$GLOBALS['br']--;
			if ($GLOBALS['br'] < 0){
			  $childElement = $dom->createElement($childNode->nodeName);
			  $fatherElement->appendChild($childElement);
			}
		      } else {
			$childElement = $dom->createElement($childNode->nodeName);
			$fatherElement->appendChild($childElement);
		      }      
		    }
		  }     
	      }
	  }
      }    
}


function exploreIndexH2($dom, $fatherElement, $fatherNode){
            
      foreach ($fatherNode->childNodes as $childNode){
	  if ($childNode->nodeName != "center" && $childNode->nodeName != "h1"){	    	  
	      if($childNode->hasChildNodes()){
		  if ($childNode->nodeName == "a" && strpos("dkaj".$childNode->attributes->getNamedItem("href")->nodeValue, "authors.html")){
		    exploreIndexH2($dom, $fatherElement, $childNode);
		    continue;
		  }
		  if ($childNode->nodeName == "a"){
		      $childElement = $dom->createElement($childNode->nodeName);		      
		      if($childNode->hasAttributes()){
			  foreach ($childNode->attributes as $attribute){
			      $childElement->setAttribute($attribute->name, "#");
			      $childElement->setAttribute("onclick", "linkTo('".$attribute->value."'); return false");
			  }
		      }
		      exploreIndexH2($dom, $childElement, $childNode);	      
		      $fatherElement->appendChild($childElement);    
		  } else {
		      $childElement = $dom->createElement($childNode->nodeName);
		      if($childNode->hasAttributes()){
			  foreach ($childNode->attributes as $attribute){
			      $childElement->setAttribute($attribute->name, $attribute->value);
			  }
		      }   	      
		      exploreIndexH2($dom, $childElement, $childNode);	      
		      $fatherElement->appendChild($childElement);
		  }   
	      } else {
		  //allora è un nodo testo	      
		  if ($childNode->nodeType == 3){ //costante del tipo testo
		    $childElement = $dom->createTextNode($childNode->nodeValue);
		    $fatherElement->appendChild($childElement);   
		  } else {
		  //o un nodo senza figli
		    if ($childNode->nodeType != 8){
		      //i commenti mi danno problemi quindi non li considero
		      if ($childNode->nodeName == "br") {
			$GLOBALS['br']--;
			if ($GLOBALS['br'] < 0){
			  $childElement = $dom->createElement($childNode->nodeName);
			  $fatherElement->appendChild($childElement);
			}
		      } else {
			$childElement = $dom->createElement($childNode->nodeName);
			$fatherElement->appendChild($childElement);
		      }      
		    }
		  }     
	      }
	  }
      }    
}


function explore($dom, $fatherElement, $fatherNode){
            
      foreach ($fatherNode->childNodes as $childNode){
	      if($childNode->hasChildNodes()){
		  $childElement = $dom->createElement($childNode->nodeName);
		  if($childNode->hasAttributes()){
		      foreach ($childNode->attributes as $attribute){
			  $childElement->setAttribute($attribute->name, $attribute->value);
		      }
		  }   	      
		  explore($dom, $childElement, $childNode);	      
		  $fatherElement->appendChild($childElement);
	      } else {
		  //allora è un nodo testo	      
		  if ($childNode->nodeType == 3){ //costante del tipo testo
		    $childElement = $dom->createTextNode($childNode->nodeValue);
		    $fatherElement->appendChild($childElement);   
		  } else {
		  //o un nodo senza figli
		    if ($childNode->nodeType != 8){
				//i commenti mi danno problemi quindi non li considero
				$childElement = $dom->createElement($childNode->nodeName);
				// aggiunta del campo src ai tag img
				if($childNode->nodeName == "img"){
					foreach ($childNode->attributes as $attribute){
						$childElement->setAttribute($attribute->name, "data/".$attribute->value);
					}
					// incapsulo in <a> le immagini articolo che non lo sono
					if($fatherElement->nodeName=="p" && $fatherNode->childNodes->length==1){
						$aHref = $dom->createElement("a");
						$aHref->setAttribute("href", $attribute->value);
						$aHref->setAttribute("target", "_blank");
						$childElement->setAttribute("style", "max-width:100%; max-height:100%;");
						$aHref->appendChild($childElement);
						$childElement = $aHref;
					}
					if($fatherElement->nodeName=="a"){
						$fatherElement->setAttribute("target", "_blank");
					}
				}
				$fatherElement->appendChild($childElement);
		    }
		  }
	      }
      }
}

echo 'success';

?>

