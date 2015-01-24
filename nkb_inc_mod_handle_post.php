<?php
/*
  Code By NKB Inc.
  For Support, please contact: "Nikko Benson" <nikko@nikkobenson.com>

  Wed Aug 13 12:30:13 CDT 2014 ... 0.9.0 ... Project Code started.

*/
// -----
try {
  if (file_exists('./nkb_inc_mod_lib_common.php'))
  {
    require_once("./nkb_inc_mod_lib_common.php");
  } else {
    throw new Exception('NKB_INC:Mod - nkb_inc_mod_handle_post.  Cannot load common library.  Cannot continue.');
  }
} catch (Exception $e) {
  echo $e->getMessage();
  exit;
}

// Figure out how we were called
if ( isset($_REQUEST['nkb_door_key']) )
{
  nkb_inc_mod_handle_post();
} else {
  echo "No." . $_REQUEST['nkb_door_key'] . " * \n";
}

// -----
function nkb_inc_mod_handle_post()
{
    try
    {
      $nkb_door_key = $_REQUEST['nkb_door_key'];
      $request = $_REQUEST['request'];
      $data = $_REQUEST['data'];

      $web_title_data = strtolower($request);
      $web_body_data  = "NKB_INC:nkb_inc_mod_handle_post - NoOp()";

      // check for the right 'door_key'
      if ($nkb_door_key != NKB_INC_KEYBLOCK) {
        $reason_code = 'NKB_INC:nkb_inc_mod_handle_post - NKB_INC_KEYBLOCK is bogus.  Cannot continue. (Got ' . $nkb_door_key . ')' ;
        throw new Exception( $reason_code );
      }

      switch ( $web_title_data ) {
        case 'get_did_qual_time_in_seconds':
          $web_body_data  = 'did: 3126839076|qual_time_in_seconds: 7|last_updated: 2014-08-22 10:40:50';
          break;

        default:
          $reason_code = 'NKB_INC:nkb_inc_mod_handle_post - REQUEST is bogus.  Cannot continue. (Got ' . $web_title_data . ')' ;
          throw new Exception( $reason_code );
          break;
      } // switch
    } // try
    catch (Exception $e)
    {
      $web_title_data = "ERROR TRAPPED";
      $web_body_data =  $e->getMessage();
    }

    // so publish some HTML as a reply now.
    // Generate headers if we've gotten this far
    header("Content-type: text/html; charset=utf-8");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache");
    header("Pragma: no-cache");

    echo "<!DOCTYPE html>\n";
    echo "<html>\n";
    echo " <head>\n";
    echo "  <title>";
    echo $web_title_data;
    echo "</title>\n";
    echo " </head>\n";
    echo " <body BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
    echo "  <p>";
    echo $web_body_data;
    echo "</p>\n";

    // echo "  <pre>";
    // print_r($_REQUEST);
    // echo "</pre> \n";

    echo " </body>\n";
    echo "</html>\n";

}

/* ---- end of file ---- */
?>
