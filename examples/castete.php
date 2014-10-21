<?php  /* ------ exemple de client CAS �crit en PHP --------*/
  //include_once('CAS.php');
  //print( "Content-type: text/html\n" );
  //print( "\n" );
  // localisation du serveur CAS
  define('CAS_BASE','http://authen.demo.net');

  // propre URL
  //$service = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  
  $service = 'http://' . $_SERVER['SERVER_NAME'] . '/castete.php';
  
  /** Cette simple fonction r�alise l?authentification CAS.
   * @return  le login de l?utilisateur authentifi�, ou FALSE.
   */
  function authenticate() {
      global $service ;

      // r�cup�ration du ticket (retour du serveur CAS)
      if (!isset($_GET['ticket'])) {
          // pas de ticket : on redirige le navigateur web vers le serveur CAS
          header('Location: ' . CAS_BASE . '/cas/login?service='  . $service);
          exit() ;
      }
      
      // un ticket a �t� transmis, on essaie de le valider aupr�s du serveur CAS
      $ticket  = $_GET['ticket'];
     // $service = $_GET['service'];
     
      print( 'service: '.$service.'<br>' );
      print( 'ticket: '.$ticket.'<br>' );
      //$ticket .= '1';
      $fpage = fopen (CAS_BASE . '/cas/serviceValidate?service='
                               . preg_replace('/&/','%26',$service) . '&ticket=' . $ticket,  'r');
      if ($fpage) {
          while (!feof ($fpage)) { $page .= fgets ($fpage, 1024); }
          // analyse de la r�ponse du serveur CAS
          print( 'la:  '.$page );
          if (preg_match('|<cas:authenticationSuccess>.*</cas:authenticationSuccess>|mis',$page)) {
              if(preg_match('|<cas:user>(.*)</cas:user>|',$page,$match)){
                  return($match[1]);
              }
          }
      }
      // probl�me de validation
      return FALSE;
  }

  //print( 'je passe ici' );
  $login = authenticate();

  if ($login == FALSE ) {
      echo 'Requ�te non authentifi�e (<a href="'.$service.'"><b>Recommencer</b></a>).';
      exit() ;
  }


  // � ce point, l?utilisateur est authentifi�
  echo 'Utilisateur connect� : ' . $login . '(<a href="' . CAS_BASE . '/cas/logout"><b>d�connexion</b></a>)';
?>
