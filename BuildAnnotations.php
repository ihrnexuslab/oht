<?php
  ini_set('error_reporting', E_ALL & ~E_NOTICE);
  ini_set('log_errors', '0');
  ini_set('error_log', './');

  require_once("OralHistoryDataConn.php");
  require_once("KeyFunctions.php");

  $str = "capitulation, enroller, magnification, amphidromia, crossbanding, Mercery, ungraceful, togolese, unsyringed, sufflated, Adductor, centering, hayloft, promisee, chimu, athematic, hitch, canarian, anoestrus, hosiery, Underrigged, item, cunningness, mtif, bumpier, Unharmonically, recallable, whirlicote, rto, nongeologic, innoxious, munga, spurry, hyperconservatism, flurried, Merino, eighties, signature, bertina, precommune, Unmowed, currajong, rebalance, nonargentiferous, beadwork, gombeen, faery, nonrefueling, quintan, levo, Nonsatiable, diaphragmatically, unhatchability, ethan, netball, Discord, melter, succinate, undocketed, glaciation, tlingit, hematoma, resinification, nonresidual, cymballike, Fanaticising, nonpractice, denims, gateau, ungored, Hellion, chamosite, masterwort, sylphic, unzoneddissimulative, olith, kiirun, unbraved, wow, Slinkingly, aftergrowth, anarchist, pithecanthropus, noncalumniating, Bergamot, gino, levantine, beeswing, pronaval, Sint, Vice, Tumblr, banjo, wolf, mixtape, keytar, Kitsch, Tumblr, plaid, readymade, aliqua, organic, irony, bicycle, rights, Mlkshk, est, Bushwick, chia, PBR, aute, cray, pickled, sapiente, voluptate, duis, meh, YOLO, small, batch, mustache, Skateboard, butcher, pug, squid, meggings, Tonx, nesciunt, vegan, pork, belly, nostrud, Williamsburg, cupidatat, DIY, fixie, chambray, lo-fi, Mollit, pork, belly, Echo, Park, flannel, quinoa, Typewriter, id, viral, Helvetica, excepteur, try-hard, labore, cillum, single-origin, coffee, Williamsburg, Laboris, jean, shorts, Vice, iPhone, Fixie, aliquip, hashtag, 8-bit, chillwave, church-key, banjo, Schlitz, Tonx, meh, ethical, nihil, literally, Pickled, lomo, blog, twee, Next, level, Cosby, sweater, assumenda, incididunt, Thundercats, Carles, swag, brunch, nesciunt, typewriter, you, probably, haven't, heard, of, them, commodo, irony, disrupt, Excepteur, aute, brunch, ut, ethical, voluptate, adipisicing, 90's, kale, chips, narwhal, Anim, vero, small, batch, yr, eiusmod, sapiente, fanny, pack, Banksy, McSweeney's, authentic, cillum, occaecat, ea, put, a, bird, on, it, whatever, Carles, excepteur, dolore, post-ironic, ut, leggings, butcher, sartorial, Truffaut, craft, beer, disrupt, try-hard, cupidatat, sapiente, selvage, Echo, Park, Neutra, mollit, cardigan, roof, party, bitters, fashion, axe, Shoreditch, tote, bag, tousled, quis, incididunt, DIY, tattooed, magna, irure, umami, Aliqua, viral, keffiyeh, occupy, pop-up, eu, Cardigan, quinoa, polaroid, umami, next, level, whatever, cupidatat, tempor, vero, exercitation, Flexitarian, placeat, normcore, nulla, exercitation, scenester, farm-to-table, salvia, Etsy, 3, wolf, moon, artisan, Selfies, Godard, labore, Bushwick, viral, 90's, accusamus, American, Apparel, craft, beer, cillum, minim, Banksy, Tumblr, Austin, pour-over, PBR&B, Intelligentsia, minim, labore, consequat, Schlitz, locavore, Nisi, accusamus, pork, belly, aliqua, vinyl, put, a, bird, on, it, consequat, mixtape, swag, scenester, Trust, fund, fingerstache, ennui, veniam, gastropub, crucifix, laborum, Brooklyn, craft, beer, plaid, Thundercats, Shoreditch, twee, gentrify, Carles, letterpress, incididunt, fingerstache, Banksy, post-ironic, Cray, Brooklyn, put, bird, adipisicing, Hoodie, dolor, plaid, master, cleanse, ethnic, et, mlkshk, Shoreditch, Meggings, literally, bicycle, rights, letterpress, Helvetica, chillwave, cray, DIY, retro, et, Tousled, 8-bit, master, cleanse, quis, fanny, pack, sartorial, PBR, duis, nisi, American, Apparel, letterpress, viral, Neutra, cardigan, Bespoke, Austin, mlkshk, selvage, messenger, bag, slow-carb, chambray, Tonx, squid, excepteur, sapiente, delectus, flannel, beard, Vegan, McSweeney's, letterpress, sartorial, pour-over, ugh, odio, before, they, sold, out, wayfarers, sunt, nostrud, lomo, aliquip, Sriracha, Kickstarter, salvia, tousled, ennui, freegan, cliche, dolore, Thundercats, mlkshk, Portland, lomo, pug, organic, eu, Swag, street, art, Wes, Anderson, deserunt, fixie, accusamus, before, they, sold, out, mumblecore, wayfarers, laborum, shabby, chic, ennui, single-origin, coffee, Intelligentsia, Literally, aesthetic, eiusmod, eu, chambray, pork, belly, fingerstache, Street, art, Marfa, crucifix, reprehenderit, nesciunt, farm-to-table, Pork, belly, enim, PBR&B, dreamcatcher, freegan, sriracha, ad, messenger, bag";

  $myWords = explode(", ",$str);
  //print_r($myWords);

  // Get the recordings...
  $bareQuery = "SELECT RecordingId,Title,Identifier,Seconds, UserId FROM recordings ORDER BY RecordingId";
  $resultall = mysqli_query($conn, $bareQuery);
  $numberall = mysqli_num_rows($resultall);
  $x = 0;
  while ($x<$numberall):
    // Retreive Data and put it in Local Variables for each Row...
    $row = mysqli_fetch_array($resultall, MYSQLI_ASSOC);
    $RecordingId = $row['RecordingId'];
    $Title = $row['Title'];
    $Identifier = $row['Identifier'];
    $Seconds = $row['Seconds'];
    $UserId = $row['UserId'];
    generateAnnotations($RecordingId, $Seconds, $UserId);
    $x++;
  endwhile;

  //-----------------------------------------------
  function getRandomDate($startDate, $endDate) {
    $datestart = strtotime('2009-12-10');//you can change it to your timestamp;
    $dateend = strtotime('2009-12-31');//you can change it to your timestamp;
    $daystep = 86400;
    $datebetween = abs(($endDate - $startDate) / $daystep);
    $randomday = rand(0, $datebetween);
    return date("Y-m-d", $startDate + ($randomday * $daystep));
  }

  //-----------------------------------------------
  function generateAnnotations($RecordingId, $Seconds, $myUser) {
    global $myWords;
    global $conn;
    $datestart = strtotime('2014-01-10');//you can change it to your timestamp;
    $dateend = strtotime('2014-06-30');//you can change it to your timestamp;

    $x = mt_rand(2, 5);  						// Number of annotations to create...
    for ($i=0; $i<=$x; $i++) :
         $AnnotationId = TheNextKeyValue("AnnotationId", "annotation");
         $AnnotationTypeId = mt_rand(1, 7);				// AnnotationType
         $SecondsIn = mt_rand (5, ($Seconds-5));			// Number of seconds into the audio...

         // create random description string...
         $d = mt_rand (10, 20);						// Number of words to use in description...
         $Description = "";
         for ($j=0; $j<=$d; $j++) :
              $w = mt_rand(0, 493);
              $Description .= $myWords[$w] . " ";
         endfor;
         $Description = rtrim($Description);

         // create random keyword string...
         $k = mt_rand (5, 10);						// Number of keywords...
         $Keywords = "";
         for ($j=0; $j<=$k; $j++) :
              $w = mt_rand(0, 493);
              $Keywords .= $myWords[$w] . ", ";
         endfor;
         $Keywords = rtrim($Keywords, ", ");

         // create random location...
         $Latitude = mt_rand (-600, 600) / 10;
         $Longitude = mt_rand (-1800, 1800) / 10;

         $DateAdded = getRandomDate($datestart, $dateend);
         $UserId = $myUser;

         $query="INSERT INTO annotation (AnnotationId,RecordingId,AnnotationTypeId,SecondsIn,Description,Keywords,Latitude,Longitude,DateAdded,UserId) VALUES ('$AnnotationId','$RecordingId','$AnnotationTypeId','$SecondsIn','$Description','$Keywords','$Latitude','$Longitude','$DateAdded','$UserId')";
         $result = mysqli_query($conn, $query);
         echo $query . "<br>\n";
    endfor;
    return true;
  }

?>