<?
//####################################################################
// Active PHP Bookmarks - lbstone.com/apb/
//
// Filename: daily_browser.php
// Authors:  Nathanial P. Hendler (retards.org)
//
// 2002-03-02 03:35     Created
//
//####################################################################

include_once('apb.php');
$APB_SETTINGS['allow_edit_mode'] = 1;
$APB_SETTINGS['allow_search_box'] = 1;
apb_head();


?>

<p>

<table border='0' width='100%' cellpadding='0' cellspacing='0'>
<tr>
  <td valign='top'>

<?

if ($APB_SETTINGS['daily_browsing_public'] == 1 OR $APB_SETTINGS['auth_user_id']) {

	$today =  date("Y-m-d");

	if ($date) {
		$now       = $date;
		$weekday   = date("l",  strtotime($now));
		$tomorrow  = date("Y-m-d", strtotime("+1 day", strtotime($now)));
		$yesterday = date("Y-m-d", strtotime("-1 day", strtotime($now)));
	} else {
		$now       = date("Y-m-d");
		$weekday   = date("l");
		$tomorrow  = date("Y-m-d", strtotime("+1 day"));
		$yesterday = date("Y-m-d", strtotime("-1 day"));
	}

	$now_full = "<h2>$now</h2><b>$weekday</b>";

	?>

	<table align='center' width='100%'>
	<tr>
	<td align='left' valign='bottom'>&lt;-- <a href='?date=<?= $yesterday ?><? if ($edit_mode) print "&edit_mode=$edit_mode" ?>'><?= $yesterday ?></a></td>
	<td align='center' valign='center'><center><?= $now_full ?></center></td>
	<td align='right' valign='bottom'>


	<?
		if (strtotime($tomorrow) <= strtotime($today)) {
			print "<a href='?date=$today";
            if ($edit_mode) {
                print "&edit_mode=$edit_mode";
            }
            print "'>today</a> --&gt;<br>";
			print "<a href='?date=$tomorrow";
            if ($edit_mode) {
                print "&edit_mode=$edit_mode";
            }
            print "'>$tomorrow</a> --&gt;";
		} else {
			print "$tomorrow --&gt;";
		}
	?>

	</td>
	</tr>
	</table>
	<br><p>

  </td>
  </tr>

  <tr>
  <td valign='top'>

	<?

    if ($APB_SETTINGS['auth_user_id']) {
        $private_sql = "";
    } else {
        $private_sql = "AND b.bookmark_private = 0";
    }

	/* Display The Days Bookmarks */

	print "<b>Bookmarked Sites</b><p>\n\n";

	$query = "
		SELECT b.*, g.*, DATE_FORMAT(b.bookmark_creation_date, '%H:%i:%s') as creation_date
		  FROM apb_bookmarks b
		       NATURAL JOIN apb_groups g
		 WHERE DATE_FORMAT(b.bookmark_creation_date, '%Y-%m-%d') = '$now'
           AND b.user_id = $APB_SETTINGS[user_id]
           AND b.bookmark_deleted = 0
           $private_sql
	  ORDER BY b.bookmark_creation_date
	";
    #print "<p><pre>$query</pre><p>\n\n";
	$result = mysql_query($query);

    if (mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $b = apb_bookmark($row);
            $g = apb_group($row);
            print $row[creation_date] . " - " . $b->link() . " <font size='1'>(" . $g->link() . ")</font><br>\n";
        }
    } else {
        print "No sites bookmarked";
    }


	/* Display The Days Browsing */

	print "<p>\n";
	print "<b>Bookmarks Used by " . return_username($APB_SETTINGS['user_id']) . "</b><p>\n";

	$query = "
		SELECT b.*, g.*, DATE_FORMAT(h.hit_date, '%H:%i:%s') as hit_date
		FROM apb_hits h
		NATURAL JOIN apb_bookmarks b
		NATURAL JOIN apb_groups g
		WHERE DATE_FORMAT(h.hit_date, '%Y-%m-%d') = '$now'
        AND b.user_id = $APB_SETTINGS[user_id]
        AND b.bookmark_deleted = 0
        $private_sql
		ORDER BY h.hit_date
	";
    #print "<p><pre>$query</pre><p>\n\n";
	$result = mysql_query($query);

    if (mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $b = apb_bookmark($row);
            $g = apb_group($row);
            print $row[hit_date] . " - " . $b->link() . " <font size='1'>(" . $g->link() . ")</font><br>\n";
        }
    } else {
        print "No bookmarks used";
    }


	/* Display The Days Browsing by Visitors*/

	print "<p>\n";
	print "<b>Bookmarks Used by Visitors</b><p>\n";

	$query = "
		SELECT b.*, g.*, DATE_FORMAT(h.hit_date, '%H:%i:%s') as hit_date, h.hit_ip
		  FROM apb_hits h, apb_bookmarks b, apb_groups g
		 WHERE DATE_FORMAT(h.hit_date, '%Y-%m-%d') = '$now'
           AND h.user_id != $APB_SETTINGS[user_id]
           AND b.bookmark_id = h.bookmark_id
           AND b.group_id = g.group_id
           AND b.bookmark_deleted = 0
           $private_sql
	  ORDER BY h.hit_date
	";
    #print "<p><pre>$query</pre><p>\n\n";
	$result = mysql_query($query);

    if (mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $b = apb_bookmark($row);
            $g = apb_group($row);
            #print $row[hit_date] . " &nbsp;&nbsp; " . $row[hit_ip] . " &nbsp;&nbsp; " . $b->link() . " <font size='1'>(" . $g->link() . ")</font><br>\n";
            print $row[hit_date] . " - " . $b->link() . " <font size='1'>(" . $g->link() . ")</font> - <font color='#999999'>" . $row[hit_ip] . "</font><br>\n";
        }
    } else {
        print "No bookmarks used";
    }

    echo "<br>";

} else {

?>

<center><p>Daily bookmark browsing is not enabled for non-authenticated users.</center>

<?
}
?>

  </td>
</tr>
</table>

<?
apb_foot();
?>
