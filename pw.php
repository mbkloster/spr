<?php

$str = md5("mahaD3va19");
$salt = chr(rand(1,255)) . chr(rand(1,255)) . chr(rand(1,255));

$final = md5($salt . $str);

echo "<p>Original md5: $str<br />Salt: $salt</p><p>Final salted password: $final<br />Reg date:" . gmdate("U") . "</p>";

?>