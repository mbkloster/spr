<?php

$starttime = microtime();
require("../include/include.php");

$output->subtitle = 'Spam Prevention';
$output->addl("<h2 class=\"section\">Spam Prevention</h2>",2);
$output->addl("<p>As you may know, spammers are a huge pain in the ass. SPR was once safe from these terror creatures of the internet, but from this point forward, no more. I've gotten many entries flooded with tons of junk comments already, and in order to better combat this growing menace, I've made a custom-built spam prevention technique. One that doesn't involve typing in the contents of any images, too!</p>",2);
$output->addl("<h3>How does this thing work?</h3>",2);
$output->addl("<p>It's simple: You're given a basic question, in English, and you have to answer it. Don't worry, there's never any strange wording to trip you up, and the question is never something you wouldn't know anything about. I've accounted for variations in spelling, phrasing, and interpretation in possible answers. If you can answer the question properly (punctuation and capitalization don't matter) you're assumed to be human and your comment will go through. Otherwise, your comment will be turned away. You have an upward maximum of $captcha_timeout_hours hours to type out your response and submit it before your question is erased, so don't leave your browser sitting for days on end before submitting a comment!</p>",2);
$output->addl("<h3>But what if I honestly don't know the answer to the question?</h3>",2);
$output->addl("<p>If this honestly happens, don't worry about it. You can quickly get a new question by simply refreshing the page in your browser. You can keep doing this until you get a question you feel very comfortable with. If there are any questions presented that you honestly think are beyond the capabilities if the average person to answer, <a href=\"mailto:genotoxin@gmail.com\">let me know</a>.</p>",2);
$output->addl("<h3>Could any complications or penalties arise if I enter the wrong answer?</h3>",2);
$output->addl("<p>Not at this moment, no. I'm working on blacklisting/whitelisting capabilities, so people that are obviously human are let through and bots that enter the wrong answer a bunch of times get blocked, but this hasn't been implemented yet. Don't worry, though, because when I do, it will take a lot of wrong answers before you get blacklisted.</p>",2);

$output->display();

?>