<?php

function summarize($paragraph, $limit){
  $tok = strtok($paragraph, " ");
  $text="";
  $words='0';
  while($tok){
   $text .= " ".$tok;
   $words++;
   if(($words >= $limit) && ((substr($tok, -1) == "!")||(substr($tok, -1) == ".")))
     break;
   $tok = strtok(" ");
  }
return ltrim($text);
}

?>