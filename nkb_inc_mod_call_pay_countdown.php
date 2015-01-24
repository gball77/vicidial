<?php
/*
  Code By NKB Inc.
  For Support, please contact: "Nikko Benson" <nikko@nikkobenson.com>

  Wed Aug 13 12:30:13 CDT 2014 ... 0.9.0 ... Project Code started.
  Fri Aug 22 10:45:56 CDT 2014 ... 0.9.1 ... Current revision re-written to avoid issues with Vicidial internals.
                                         ... moved some code into lib_common for greater reusability / encapsulation

  Code Includes:

  // Added By NKB Inc.: IF statement for development servers to use the same codebase without this function
  if (file_exists('custom/nkb_inc_mod_call_pay_countdown.php'))
  {
    require_once('custom/nkb_inc_mod_call_pay_countdown.php');
  }
  // End Added By NKB Inc.: IF statement for development servers to use the same codebase without this function


  <!-- NKB_INC:Mod - call_pay_countdown - begin -->
    <?PHP if (file_exists('custom/nkb_inc_mod_call_pay_countdown.php')) { nkb_inc_mod_call_pay_countdown_show_timer_html(); } ?>
  <!-- NKB_INC:Mod - call_pay_countdown - end -->
*/

// -----
try {
  if (file_exists('custom/nkb_inc_mod_lib_common.php'))
  {
    require_once("custom/nkb_inc_mod_lib_common.php");
  } else {
    throw new Exception('NKB_INC:Mod - nkb_inc_mod_call_pay_countdown.  Cannot load common library.  Cannot continue.');
  }
} catch (Exception $e) {
  echo $e->getMessage();
  exit;
}

// -----
function nkb_inc_mod_call_pay_countdown_show_timer_html()
{
  printf('<font class="body_text">&nbsp; <span id="nkb_inc_mod_call_pay_timer_show_here"></span></font>');
  printf('<font class="body_text">&nbsp; <span id="nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds"></span></font>');

} // end of nkb_inc_mod_call_pay_countdown_show_timer_html

// -----
function nkb_inc_mod_call_pay_countdown_timer_script($timer_duration=90, $call_duration_so_far=0)
{
  $runs_for = $timer_duration - $call_duration_so_far;

  $check_per_sec = 10;
  //10 will run it every 10 times per second

  $check_interval_millisec = 1000/$check_per_sec;
  $counter_decrements = $check_interval_millisec/1000;
  $nkb_inc_keyblock = NKB_INC_KEYBLOCK;

  $codeblock = <<<ENDOFCODE
  <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
  <script language="javascript" type="text/javascript">

  console.log('NKB_INC:Mod - call_pay_countdown:  Start Loading' );

  /*
  function nkb_inc_mod_call_pay_timer_runs() ... when a pay_timer call is taking place, this runs the on-screen timer
  function nkb_inc_mod_call_pay_timer_monitor() ... watches for an incoming call, and then starts the timer if applicable
  function nkb_inc_mod_call_pay_timer_setup() ... establishes base configuration, and sets screen info
  function nkb_inc_mod_is_a_live_call() ... checks two internal VICIDIAL variables to determine if a call has been given to the agent

  html.span.id="nkb_inc_mod_call_pay_timer_show_here" ... contains the current agent-visible timer data

  var nkb_inc_mod_call_pay_timer_startval ... starting value for the timer, based on DB lookup
  var nkb_inc_mod_call_pay_timer_handling_call ... status flag used to indicate when a pay_timer call is taking place
  var nkb_inc_mod_call_pay_timer_time_left ... how many seconds are left on the call

  var nkb_inc_mod_call_pay_timer_run_canister ... setInterval canister
  var nkb_inc_mod_call_pay_timer_counter_canister ... setInterval canister
  */

  // -----
  var nkb_inc_mod_call_pay_timer_startval = 0;
  var nkb_inc_mod_call_pay_timer_handling_call = false;
  var nkb_inc_mod_call_pay_timer_raised_flag = false;
  var nkb_inc_mod_call_pay_timer_time_left = 0;

  var nkb_inc_mod_call_pay_timer_run_canister = 0;
  var nkb_inc_mod_call_pay_timer_counter_canister = 0;

  // -----
  function nkb_inc_mod_call_pay_timer_setup()
  {
    nkb_inc_mod_call_pay_timer_startval = -1;
    nkb_inc_mod_call_pay_timer_time_left = -1;
    nkb_inc_mod_call_pay_timer_handling_call = false;
    nkb_inc_mod_call_pay_timer_raised_flag = false;
    nkb_inc_mod_call_pay_timer_default_msg();
  }

  // -----
  function nkb_inc_mod_call_pay_timer_monitor() {
    if ((nkb_inc_mod_call_pay_timer_handling_call == false) && nkb_inc_mod_is_a_live_call() )
    {
      nkb_inc_mod_call_pay_timer_handling_call = true;
      nkb_inc_mod_call_pay_timer_startval = nkb_inc_mod_call_pay_timer_get_did_qual_time_in_seconds(did_extension);
      nkb_inc_mod_call_pay_timer_time_left = nkb_inc_mod_call_pay_timer_startval;

      console.log('NKB_INC:Mod - nkb_inc_mod_call_pay_timer_monitor:  DID:' + did_extension + ' | qual time:' + nkb_inc_mod_call_pay_timer_startval + 'seconds.' );
      alert_box('This Call Must Be Qualified in ' + nkb_inc_mod_call_pay_timer_startval + "seconds.");

      nkb_inc_mod_call_pay_timer_run_canister = setTimeout(nkb_inc_mod_call_pay_timer_runs, {$check_interval_millisec});
    } else {
      nkb_inc_mod_call_pay_timer_monitor_canister = setTimeout(nkb_inc_mod_call_pay_timer_monitor, {$check_interval_millisec});
    }
  } // function nkb_inc_mod_call_pay_timer_monitor ()

  // -----
  function nkb_inc_mod_call_pay_timer_runs()
  {
    if ( nkb_inc_mod_call_pay_timer_time_left > 0.0 ) {
      nkb_inc_mod_call_pay_timer_time_left = nkb_inc_mod_call_pay_timer_time_left - {$counter_decrements};
      document.getElementById("nkb_inc_mod_call_pay_timer_show_here").innerHTML="<br \> time to qual left: " + Math.round(nkb_inc_mod_call_pay_timer_time_left*10)/10 + " secs";
    }

    if ( nkb_inc_mod_call_pay_timer_handling_call && (nkb_inc_mod_is_a_live_call() == false) )
    {
      nkb_inc_mod_call_pay_timer_stop();
    };

    if ( nkb_inc_mod_call_pay_timer_time_left <= 0.0 && nkb_inc_mod_is_a_live_call() && ! nkb_inc_mod_call_pay_timer_raised_flag)
    {
      alert('This Call Must Be Qualified NOW');
      nkb_inc_mod_call_pay_timer_raised_flag = true;
    }

    if ( nkb_inc_mod_is_a_live_call() )
    {
      nkb_inc_mod_call_pay_timer_run_canister = setTimeout(nkb_inc_mod_call_pay_timer_runs, {$check_interval_millisec});
    }

  } // function nkb_inc_mod_call_pay_timer_runs()

  // -----
  function nkb_inc_mod_is_a_live_call() {
    if ((XD_live_customer_call == 1 || VD_live_customer_call == 1 )) {
      return true;
    } else{
      return false;
    };
  }

  // -----
  function nkb_inc_mod_call_pay_timer_stop() {
    nkb_inc_mod_call_pay_timer_setup();
    nkb_inc_mod_call_pay_timer_monitor_canister = setTimeout(nkb_inc_mod_call_pay_timer_monitor, {$check_interval_millisec});
    return;
    }

  // -----
  function nkb_inc_mod_call_pay_timer_default_msg() {
    document.getElementById("nkb_inc_mod_call_pay_timer_show_here").innerHTML="<br \>  time to qual left: no call ";
    return;
  }

  // -----
  function nkb_inc_mod_call_pay_timer_get_did_qual_time_in_seconds(get_data_for_did) {
    var nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds = -2;

    var xmlhttp=false;
    if (!xmlhttp && typeof XMLHttpRequest!='undefined')
      {
      xmlhttp = new XMLHttpRequest();
      }

    if (xmlhttp) {
      var nkb_inc_mod_call_pay_timer_server_query = "nkb_door_key={$nkb_inc_keyblock}&request=get_did_qual_time_in_seconds&data=did_" + get_data_for_did + "&format=text";
      xmlhttp.open('POST', 'custom/nkb_inc_mod_handle_post.php');
      xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
      xmlhttp.send(nkb_inc_mod_call_pay_timer_server_query);

      xmlhttp.onreadystatechange = function()
        {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
          {
          // alert( "xmlhttp" + xmlhttp.responseText );
          var nkb_inc_mod_call_pay_timer_server_reply = xmlhttp.responseText;
          var nkb_inc_mod_call_pay_timer_record_array = nkb_inc_mod_call_pay_timer_server_reply.split("|");
          var nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds_string = nkb_inc_mod_call_pay_timer_record_array[1].split(":");
          nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds = nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds_string[1];

          alert( "nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds_string[1]" + nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds_string[1] );
          alert( "nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds" + nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds );

          document.getElementById("nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds").innerHTML = nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds;

          }
        }
      delete xmlhttp;
    } else {
      console.log('NKB_INC:Mod - nkb_inc_mod_call_pay_timer_get_did_qual_time_in_seconds:  XMLHttpRequest unavailable?' );
      nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds = 45;
    };

    alert( "return nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds" + nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds );
    return nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds;

  } // function nkb_inc_mod_call_pay_timer_get_did_qual_time_in_seconds

  // -----
  // once page is "up", we kick off our base config and start monitoring for calls
  $( document ).ready( nkb_inc_mod_call_pay_timer_setup );
  $( document ).ready( function() {

    document.getElementById("nkb_inc_mod_call_pay_timer_show_here").style.display = 'block';
    document.getElementById("nkb_inc_mod_call_pay_timer_did_qual_time_in_seconds").style.display = 'block';

    nkb_inc_mod_call_pay_timer_monitor_canister = setTimeout(nkb_inc_mod_call_pay_timer_monitor, {$check_interval_millisec});
  } );

  console.log('NKB_INC:Mod - call_pay_countdown:  Done Loading' );

  </script>

ENDOFCODE;

  printf($codeblock);
} // end of nkb_inc_mod_call_pay_countdown_timer_script


/* ---- end of file ---- */
?>
